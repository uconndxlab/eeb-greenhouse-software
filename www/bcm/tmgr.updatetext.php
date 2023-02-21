<html><head>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$recno = $_GET['recno'];
$field = $_GET["field"];
$text = $_GET["text"];
$status = $_GET['status'];
$location = $_GET['location'];
$assign = $_GET['assign'];
$codeno = $_GET['codeno'];

# Process Database Update Here ############################
$sql = 'update tasks set '.$field.'="'.$text.'" where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();

### SET UP CONDITIONALS
if ($text='REDO') {
	$sql = 'update tasks set dcomplete=00000000, completeby="" where recno='.$recno;
	$sth = $db->prepare($sql);
	$sth->execute();
}

$db = null;
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$_GET['codeno'].'&tab=tasks">';
?> 

</body>
</html> 
