<?php
### Misc mysql commands to flag individual pages for updates

{
$user="ghstaff";
$password="argus";
$database="bcm";
date_default_timezone_set('America/New_York'); 

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");


### set tempflag=1 if flowering in past 16 days
### flowering list is 15 days, 16th day will reset image captioning to non-flowering state

$sql = 'update gh_inv,history set gh_inv.tempflag=1 where gh_inv.codeno=history.codeno and (history.date >= DATE_SUB(CURDATE(),INTERVAL 16 DAY)';
$sql=$sql.' and history.class="FLOWERING") and gh_inv.projnum="GEN_COLL"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}

mysql_close($rs);
return true;
}
?>
