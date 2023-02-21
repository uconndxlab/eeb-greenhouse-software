<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### CREATE OUTPUT FILE
$file_spec = $webdir.'chemical_list.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE
$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '<TITLE>EEB Greenhouse Chemical Inventory List</TITLE>';
$result=fwrite($accfile,$strout);

$strout = '<html><BODY><hr>EEB Greenhouse Chemical Inventory List';
$result=fwrite($accfile,$strout);
$strout = '<p><i>Listing generated on '.date("r").'</i><hr>';
$result=fwrite($accfile,$strout);
$strout = '<font size=-1><table border=0><tr><th align="left">Category<th align="left">Manufacturer<th align="left">Trade Name<th align="left">MSDS<th align="left">Label<th align="left">EPA Reg#<th align="left">Location</tr>';
$result=fwrite($accfile,$strout);
$i=0;
### GENERATE TITLE HTML
$sql = 'select * from chemical where active order by location,category,tradename';
foreach($db->query($sql) as $row) {
	$strout='<tr';
	if ($i % 2 == 0) $strout=$strout.' bgcolor="#bfbfbf"';
	$strout .= '><td>'.$row['category'].'<td>'.$row['manufacturer'].'<td>'.$row['tradename'].'<td align="center">';
	if ($row['msds']<>'none') $strout .= '<a href="/msds/'.$row['msds'].'"><img src="/images/pdf-icon.png" height="16px"></img></a>';
	$strout .= '<td align="center">';
	if ($row['label']<>'none') $strout .= '<a href="/msds/'.$row['label'].'"><img src="/images/pdf-icon.png" height="16px"></img></a>';
	$strout .= '<td>'.$row['epa_reg'].'<td>'.$row['location'].'</tr>'.chr(10);
	$result = fwrite($accfile,$strout);
	$i++;
} #foreach
$strout = '</table></font></BODY></html>';
$result=fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE
fclose($accfile);
$db = null;
?>
