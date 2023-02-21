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

$recno = $_POST['recno'];

# Generate Title ################################################
#echo '<title>Chemical Update Page</title>';
echo '</head><body>';

$sql = 'update chemical set ';
$sql .= 'manufacturer="'.$_POST['manufacturer'].'",';
$sql .= 'tradename="'.$_POST['tradename'].'",';
$sql .= 'chemname="'.$_POST['chemname'].'",';
$sql .= 'epa_reg="'.$_POST['epa_reg'].'",';
$sql .= 'wps="'.$_POST['wps'].'",';
$sql .= 'rei="'.$_POST['rei'].'",';
$sql .= 'location="'.$_POST['location'].'",';
$sql .= 'category="'.$_POST['category'].'",';
$sql .= 'source="'.$_POST['source'].'",';
$sql .= 'label="'.$_POST['label'].'",';
$sql .= 'msds="'.$_POST['msds'].'",';
$sql .= 'quant="'.$_POST['quant'].'",';
$sql .= 'size="'.$_POST['size'].'"';
#$sql .= 'active="'.$_POST['active'].'"';
$sql .= ' where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();
$db = null;
echo '<META HTTP-EQUIV=REFRESH CONTENT="0; URL=http://florawww.eeb.uconn.edu/bcm/chem.view.php#'.$recno.'">';

?> 

</body>
</html> 
