<html>
<head>
<meta name="viewport" content="width=device-width" />

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}
$zone = $_POST['zone'];

echo '<title>Inventory</title>';
echo '</head><body>';
### Create button bar
echo '<a href="http://florawww.eeb.uconn.edu/bcm/query.php">{HOME}</a>';
echo '<br><b>INVENTORY ASSESSMENT ONLY</b>';
echo '<p>';

$sql = 'select distinct gh_inv.location,b_assign.descrip from gh_inv,b_assign where gh_inv.location=b_assign.location and gh_inv.location > 0 order by gh_inv.location';

echo '<ul>';
$i=0;
foreach($db->query($sql) as $row) {
	echo '<li><a href="assess.zone.php?zone='.$row['location'].'">'.$row['location'].'</a> - '.$row['descrip'];
}

echo '</ul>';

$db = null;
echo '<p><a href="admin.php">Admin Page</a>';
?> 

</font>
</body>
</html> 
