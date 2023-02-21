<html><head>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}


$genus = $_POST['genus'];
$field = $_POST["field"];
$text = $_POST["text"];
$url = $_POST["url"];
$sql = 'select genus from classify where genus="'.$genus.'"';
#echo $sql.'<hr>';
$sth = $db->prepare($sql);
$sth->execute();
if ($sth->fetchColumn()) {
	# Process Database Update Here ############################
	$text = $db->quote($text);
#	echo $text.'<hr>';
	$sql = 'update classify set '.$field.'='.$text.' where genus="'.$genus.'"';	
#	echo $sql.'<hr>';
	$sth = $db->prepare($sql);
	$sth->execute();
} #endif
$db = null;

echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
?> 

</body>
</html> 
