<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php
### input new chemical information ###
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

echo '</head><body>';
echo '<center><font size=+1>UConn EEB Greenhouse - Chemical Inventory<br>';

# Get Date/Time ####################################################
echo date("r").'<br>Week# '.date("W");
echo '</font>';
echo '</center><hr>';

echo '<h2>New Chemical Data Entry</h2>';

echo '<table><tr><form action="http://florawww.eeb.uconn.edu/bcm/chem.insert.php" method="post"></tr>';
echo '<tr><td>Manufacturer:</td><td><input name="manufacturer" value=""></td></tr>';
echo '<tr><td>Trade Name:</td><td><input name="tradename" value=""></td></tr>';
echo '<tr><td>Chemical Name:</td><td><input name="chemname" value=""></td></tr>';
echo '<tr><td>EPA Reg#:</td><td><input name="epa_reg" value=""></td></tr>';
echo '<tr><td>WPS Label (0 or 1):</td><td><input name="wps" value=""></td></tr>';
echo '<tr><td>REI (hours):</td><td><input name="rei" value=""></td></tr>';
### Build Location Dropdown Box
$sql = 'select distinct location from chemical order by location';
echo '<tr><td>Location: </td><td><select name="location" size=1>';
foreach($db->query($sql) as $row) {
	echo '<option';	
	echo'>'.$row['location'].'</option>'.chr(10);
	} # foreach
echo '</select></td></tr>';

### Build Category Dropdown Box
$sql = 'select distinct category from chemical order by category';
echo '<tr><td>Category: </td><td><select name="category" size=1>';
foreach($db->query($sql) as $row) {
	echo '<option';	
	echo'>'.$row['category'].'</option>'.chr(10);
	} # foreach category
echo '</select></td></tr>';
echo '<tr><td>Quant:</td><td><input name="quant" value=""></td></tr>';
echo '<tr><td>Size:</td><td><input name="size" value=""></td></tr>';
echo '<tr><td>Source:</td><td><input name="source" value=""></td></tr>';
echo '<tr><td>Label File:</td><td><input name="label" value="none"></td></tr>';
echo '<tr><td>MSDS File:</td><td><input name="msds" value="none"></td></tr>';

echo '<tr><td></td><td><input type="submit" value="Create Entry"></td></tr>';
echo '</form></table>';
$db = null;
?> 
</body></html>
