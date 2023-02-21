<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### CREATE OUTPUT FILE
$file_spec = $webdir.'tdwglist.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE
$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '<title>EEB Greenhouse TDWG Summary</title>';
$status = fwrite($accfile,$strout);

$strout = '<html><body><hr><b>EEB Greenhouse TDWG Summary</b>';
$strout .= '<p>{<i>Biodiversity Information Standards (TDWG), also known as the Taxonomic Databases Working Group,<br>';
$strout .= 'is a not for profit scientific and educational association that is affiliated with<br>';
$strout .= 'the International Union of Biological Sciences.</i>}'; 
$status = fwrite($accfile,$strout);
$strout = '<hr><i>Listing generated on '.date("r").'</i><hr>';
$status = fwrite($accfile,$strout);
$strout = '<font size=-1><table border=0><tr><th align="left">Region<th align="left">L3 Code<th align="left">L3 Name<th align="left">Quantity</tr>';
$status = fwrite($accfile,$strout);

### Collect GEN_COLL Accessions
$sql = 'select gh_inv.tdwg from gh_inv where';
$sql .= ' projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetchAll();
$num1 = $sth->rowCount();

### Collect list of TDWG L3 Codes
$sql = 'select tblLevel2.l2region,tblLevel3.l3code,tblLevel3.l3name from tblLevel2,tblLevel3 where tblLevel2.l2code=tblLevel3.l2code order by tblLevel3.l2code,tblLevel3.l3code';
foreach($db->query($sql) as $row) {
	$strout = '<tr><td>'.$row['l2region'];
	$strout .= '</td><td><a href="BRU_'.$row['l3code'].'.html">'.$row['l3code'].'</a>';
	$strout .= '</td><td>'.$row['l3name'];
	echo $row['l3name'].chr(10);
	### count matching accessions
	$count=0;
	foreach ($result as $element) {
		if (strpos($element['tdwg'],$row['l3code'])) $count++;
	} # foreach
	$strout .= '</td><td>'.$count.'</td></tr>'.chr(10);
	$status = fwrite($accfile,$strout);
} # foreach
$strout = '</table></font><hr><i>page generated on '.date("r").'</i></BODY></html>';
$status = fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE
fclose($accfile);
$db = null;
?>
