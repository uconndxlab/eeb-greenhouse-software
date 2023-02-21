<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name="viewport" content="width=device-width" >

<title>Pending Tasks</title>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$user = $_GET['user'];

echo '</head><body>';
### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src = "/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number"></img>';
echo '</form>';
echo '<p>';

$sql = 'select lname,fname from users where init="'.$user.'"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<h3>Pending Tasks for '.$result['fname'].' '.$result['lname'].'</h3>';
$sql = 'select tasks.codeno,tasks.descrip,unix_timestamp(tasks.dcreate) as createdate,tasks.status,tasks.location,tasks.recno,gh_inv.latin_name from tasks,taskstatus,gh_inv';
$sql .= ' where tasks.status=taskstatus.status and taskstatus.priority<5';
$sql .= ' and tasks.codeno=gh_inv.codeno';
$sql .= ' and tasks.assignto="'.$user.'" order by tasks.location,taskstatus.priority';

echo '<ul>';
foreach($db->query($sql) as $row) {
	echo '<li>'.$row['location'];
	echo ': <a href="accession.php?codeno='.$row['codeno'].'&tab=tasks" target="_blank">'.$row['latin_name'].'</a> ';
#	echo '<br>';
	if ($row['status']=="TODO - Priority" or $row['status']=="REDO") {
		echo '<font color="RED">'.$row['status'].'</font>';
	} else {
		echo $row['status'];
	}

echo ': '.$row['descrip'];	
### green if task less than 7 days, red if over 45 days
	$daysago=intval((time()-$row['createdate'])/86400);
	if ($daysago < 8) echo '<b><font color="green">';
	if ($daysago > 45) echo '<b><font color="red">';	
	
	echo '<sup>'.$row['recno'].'</sup>';
	
	if (($daysago < 8) or ($daysago > 45)) echo '</font></b>';
	
} #foreach
echo '</ul>';

echo 'Rec# in <b><font color="green">green</font></b> are new in past 7 days<br>';
echo 'Those in <b><font color="red">red</font></b> are older than 45 days<p>';
echo '<a href="admin.php">Admin Page</a>';
$db = null;
?>

</font>
</body>
</html> 
