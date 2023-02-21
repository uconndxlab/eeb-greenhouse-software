<?php
include 'v3_spec_coll_generate.php';

### CURRENTLY UPDATING FOR INCLUDE = TRUE

$user="ghstaff";
$password="argus";
$database="bcm";
date_default_timezone_set('America/New_York'); 
$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

$sql='select * from scoll where include';
$sql_result=mysql_query($sql);

if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$i=0;

while ($i<$num) {
	$result=v3_spec_coll_generate(mysql_result($sql_result,$i,'url'));
	echo mysql_result($sql_result,$i,'title').' generated -> '.mysql_result($sql_result,$i,'url').chr(10);
	$i++;
}
}
?>
