<?php
include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10);	
}

### CREATE OUTPUT FILE
$file_spec = $webdir.'alphalist.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE
$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$status=fwrite($accfile,$strout);

$strout = '<TITLE>EEB Greenhouse Alpha Collections List</TITLE>';
$status=fwrite($accfile,$strout);

$strout = '<html><BODY><hr>EEB Greenhouse Alpha Collections List';
$status=fwrite($accfile,$strout);
$strout = '<p><i>Listing generated on '.date("r").'</i><hr>';
$status=fwrite($accfile,$strout);
$strout = '<font size=-1><table border=0><tr><th align="left">Accession#<th align="left">Family<th align="left">Name<th align="left">Location</tr>';
$status=fwrite($accfile,$strout);

### GENERATE TITLE HTML
$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.author,classify.family,gh_inv.location from gh_inv,classify where classify.genus=gh_inv.genus and projnum="GEN_COLL"';
$sql .= ' order by latin_name';

foreach($db->query($sql) as $row) {
	$strout = '<tr><td>'.$row['codeno'].'<td>'.$row['family'].'<td><a href="';
	$strout .= $row['codeno'].'.html"><i>';
	$strout .= $row['latin_name'].'</i></a> '.$row['author'].'<td>'.$row['location'].'</tr>'.chr(10);
	$status=fwrite($accfile,$strout);
} # foreach
$strout = '</table></font><hr><i>page generated on '.date("r").'</i></BODY></html>';
$status=fwrite($accfile,$strout);


# CLOSE THE OUTPUT FILE
fclose($accfile);
$db = null;;
?>
