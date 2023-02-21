<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Label File Generation</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Delete first 10 records of database file
$sql = 'delete from labels_v2 order by recno limit 20';
$sth = $db->prepare($sql);
$sth->execute();

$db = null;

// Time out statement
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/label.update.php">';

?> 
</font>
</body>
</html> 
