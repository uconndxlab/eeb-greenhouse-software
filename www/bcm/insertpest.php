<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # explicit error message
}

$codeno = $_POST['codeno'];
$class = $_POST['class'];
$notes = $_POST['notes'];
$location = $_POST['location'];

#set values to posted data
$sql = 'insert into history ';
$sql .= '(codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
$sql .= 'values ('.$codeno.',';
$sql .= $location.',"';
$sql .= $class.'",curdate(),0,"';
$sql .= $notes.'"';
$sql .= ',"",0,0)';

$sth = $db->prepare($sql);
$sth->execute();

# Process Confirm Here ############################
$sql = 'update gh_inv set confirm=curdate(),tempflag=1 where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

$db = null;
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=pest">';
?> 

</body>
</html> 
