<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Adams Tasks</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

$instring = $_POST["instring"];

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Set sql_mode='' to turn off explicit checking - temporary??
$sql = 'set sql_mode=""';
$sth = $db->prepare($sql);
$sth->execute();

### Jump direct if 9 digit numeric is entered in partial text search field 
if ((is_numeric($instring)) and (strlen($instring)==9)) {
	### check for valid codeno
	$sql = 'select codeno from gh_inv where codeno='.$instring;
	$sth = $db->prepare($sql);
	$sth->execute();
	if ($sth->fetchColumn()) echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$instring.'">';	
	}

### Create Search Box
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);';
echo ' 9-digit accession number; 4-digit bench number"></img>';
echo '</form>';

### Form SQL statement
$sql = 'select tasks.location,tasks.codeno,gh_inv.latin_name,tasks.descrip,tasks.status from tasks,gh_inv where tasks.codeno=gh_inv.codeno and assignto="ALH" and tasks.status like "%TODO%" order by tasks.status DESC, tasks.location';
echo '<p><ul>';
foreach($db->query($sql) as $row) {
	echo '<li>'.$row['location'].' - '.$row['codeno'].' - '.$row['latin_name'].' - '.$row['descrip'].' - <b>'.$row['status'].'</b>'.chr(10);
} #foreach
echo '</ul>';

$db = null;
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
#include('/var/www/bcm/footer.php');

?> 
</font>
</body>
</html> 
