
<html><head>

<?php
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

$term = $_POST['term'];
$def = $_POST['def'];

# Process Database Update Here ############################
$sql = 'update glossary set def ="'.$def.'" where term="'.$term.'"'; 
$sth = $db->prepare($sql);
$sth->execute();

$db = null;

echo '<meta HTTP-EQUIV="REFRESH" content="0; url=glossary.cloud.php">';
?> 

</body>
</html> 
