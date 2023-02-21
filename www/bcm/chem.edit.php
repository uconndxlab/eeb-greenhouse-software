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

$recno = $_GET['recno'];

echo '</head><body>';

echo '<h2>Edit Chemical Inventory</h2>';
$sql = 'select * from chemical where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<form action="http://florawww.eeb.uconn.edu/bcm/chem.update.php" method="post">'.chr(10);

echo 'Record#:<b> '.$result['recno'].'</b><br>'.chr(10);
echo '<input name="recno" type="hidden" value= '.$recno.'>';
echo 'Manufacturer:<input name="manufacturer" value="'.$result['manufacturer'].'"><br>'.chr(10);
echo 'Trade Name:<input name="tradename" value="'.$result['tradename'].'"><br>'.chr(10);
echo 'Chemical Name:<input name="chemname" value="'.$result['chemname'].'"><br>'.chr(10);
echo 'EPA Reg#:<input name="epa_reg" value="'.$result['epa_reg'].'"><br>'.chr(10);
if ($result['wps']<>1) $result['wps'] = 0; #set null or bogus values to zero
echo 'WPS Label (0 or 1):<input name="wps" value="'.$result['wps'].'"><br>'.chr(10);
echo 'REI (hours):<input name="rei" value="'.$result['rei'].'"><br>'.chr(10);
echo 'Quantity:<input name="quant" value="'.$result['quant'].'"><br>'.chr(10);
echo 'Size:<input name="size" value="'.$result['size'].'"><br>'.chr(10);
echo 'Source:<input name="source" value="'.$result['source'].'"><br>'.chr(10);
echo 'Label File:<input name="label" value="'.$result['label'].'"><br>'.chr(10);
echo 'MSDS File:<input name="msds" value="'.$result['msds'].'"><br>'.chr(10);

### Build Location Dropdown Box
echo 'Location: <select name="location" size=1>';
$sql = 'select distinct location from chemical order by location';
foreach($db->query($sql) as $row) {
	echo '<option value="'.$row['location'].'"';	
	if ($row['location'] == $result['location']) echo ' selected';
	echo'>'.$row['location'];
	echo '</option>'.chr(10);
} #foreach
echo '</select><br>';

### Build Category Dropdown Box
echo 'Category: <select name="category" size=1>';
$sql = 'select distinct category from chemical order by category';
foreach($db->query($sql) as $row) {
	echo '<option value="'.$row['category'].'"';	
	if ($row['category'] == $result['category']) echo ' selected';
	echo'>'.$row['category'];
	echo '</option>'.chr(10);
} #foreach
echo '</select><p>';
#echo '<b>Active?</b> <input name="active" value="'.$result['active'].'"><br><b>NOTE:</b> Setting this to zero will remove entry from display, set quantity etc accordingly<p>';

echo '<input type="submit" value="Update Entry">';
echo '</form>';
?> 

</body>
</html> 