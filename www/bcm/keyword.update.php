<html><head>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);	
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$keyword = $_POST["keyword"];
$title = $_POST["title"];
$text = $_POST["text"];
$url = $_POST["url"];

# Process Database Update Here ############################
$sql = 'update keywords set title=:title,text=:text where keyword=:keyword';
echo $sql.'<hr>';
$sth = $db->prepare($sql);
$sth->bindParam(':title',$title);
$sth->bindParam(':text',$text);
$sth->bindParam(':keyword',$keyword);
$sth->execute();

print_r($sth->errorInfo());
$db = null;

echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
?> 

</body>
</html> 
