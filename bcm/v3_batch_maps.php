<?php

include 'v3_mapmaker.php';
include '/var/www/bcm/credentials.php';

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

$sql='select codeno from gh_inv where tempflag and projnum="GEN_COLL" order by codeno';
$sql_result=mysql_query($sql);

if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$i=0;
while ($i<$num) {
	$result=v3_mapmaker(mysql_result($sql_result,$i,'codeno'));	
	$i++;
}
}
?>
