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

$genus = $_GET['genus'];
$url=$_GET['url'];
$param = $_GET["v"];


# Generate Title ################################################
echo '<title>Edit '.$param.' for '.$genus.'</title>';
echo '</head><body>';

# Generate Latin Name Title ###########################################
echo '<h3>Edit '.$param.' for '.$genus.'</h3><hr>';

#echo $genus.'<br>';
#echo $param.'<br>';
#echo $url.'<br>';

# Generate General Information #######################################
$sql = 'select '.$_GET["v"].' as value';
$sql .= ' from classify where genus="'.$genus.'"';

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<form action="classify.update.php" method="post">';
echo '<input type="hidden" name="genus" value="'.$genus.'">';
echo '<input type="hidden" name="field" value="'.$param.'">';
echo '<input type="hidden" name="url" value="'.$url.'">';
echo '<b>'.$param.': </b><input type="text" size=80 name="text" value="'.$result['value'].'">';

echo '<p><input type="submit" name="submit" value="Update Classify"></form>'; 

$db = null;

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

?> 
</body>
</html> 
