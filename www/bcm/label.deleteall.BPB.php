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

### Delete all records in labels
$sql = 'delete from labels';
$sth = $db->prepare($sql);
$sth->execute();



/* Return number of rows that were deleted */
print("Return number of rows that were deleted:\n");
$count = $sth->rowCount();
print("Deleted $count rows.\n");

$db = null;

// Time out statement
echo '<meta HTTP-EQUIV="REFRESH" content="5; url=http://florawww.eeb.uconn.edu/bcm/admin.php">';
?> 
</font>
</body>
</html> 
