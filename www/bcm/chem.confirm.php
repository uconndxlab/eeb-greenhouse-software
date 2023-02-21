<html>
<head>

<?php
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

$recno = $_GET["recno"];

# Process Database Update Here ############################
$sql = 'update chemical set confirm=curdate() where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();

$db = null;
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/chem.view.php#'.$recno.'">';
?> 

</body>
</html> 
