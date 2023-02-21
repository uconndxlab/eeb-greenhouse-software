<?php

include '/var/www/bcm/credentials.php';

try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo 'An Error occurred connecting to BCM!'.chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}

$codeno = $_GET['codeno'];
$location = substr($_GET['location'],0,4);

$text = $_GET['text'];
$assign = $_GET['assign'];
$status = $_GET['status'];
$dfuture = $_GET['dfuture'];
#$page_ref = $_GET['page_ref']; # passed through from assign button
#$recno_ref = $_GET['recno_ref']; # passed through from assign button


if ($dfuture<>'0000-00-00') $status = 'FUTURE';
# Process Database Update Here ############################
$sql = 'set sql_mode=""';
$sth = $db->prepare($sql);
$sth->execute();

#set values to posted data
$sql = 'insert into tasks (codeno,descrip,status,dcreate,dfuture,assignto,location,priority) ';
$sql .= 'values ('.$codeno.',"';
$sql .= $text.'","'.$status.'",';
$sql .= 'curdate(),"'.$dfuture.'","'.$assign.'",'.$location.',"NONE")';

$sth = $db->prepare($sql);
$sth->execute();

### set tasks.assigned=1 if referred from weekly list
#if ($page_ref=="weekly") {
#	$sql = 'update tasks set assigned=1 where recno='.$recno_ref;
#	# process update SQL
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) echo mysql_error();
#	echo $sql;
#} # page_ref=weekly > reset assigned=1

#if ($page_ref=="weekly") {
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/weekly.php?init=CEM">';
#	} else {
	if ($codeno == 111111111) {
		echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/query.php">';
	} else {
		echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=tasks">';
	}
#}
$db = null;
?> 
