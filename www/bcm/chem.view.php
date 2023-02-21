<html>
<head>

<script language="javascript">
  function toggle_it(itemID){
      // Toggle visibility between none and inline
      if ((document.getElementById(itemID).style.display == 'none'))
      {
        document.getElementById(itemID).style.display = 'inline';
      } else {
        document.getElementById(itemID).style.display = 'none';
      }
  }
</script>

<meta name="viewport" content="width=device-width" />

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

echo '</head><body>';

echo '<h2>EEB Greenhouse Chemical Inventory</h2>';

### Create button bar

# Add New Chemical still needs work - appears to crash between chem.add & chem.insert
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/chem.add.php">{Add New Chemical}</a><p>';

echo '<form method=POST action="http://florawww.eeb.uconn.edu/bcm/chem_verify.php">';
$sql = 'select * from chemical where active order by location,category,tradename';

$location="";
$category="";

echo '<ul>';
foreach($db->query($sql) as $row) {
### Check & output section headers
	IF (($row['location'] <> $location) or ($row['category'] <> $category)) {
		$location = $row['location'];
		$category = $row['category'];	
		echo '</ul><b>'.$location.': '.$category.'</b></ul><p>'.chr(10);
		}
	echo '<div id="'.$row['recno'].'"></div>'.chr(10);
	echo '<li>';
	echo ' <a href="http://florawww.eeb.uconn.edu/bcm/chem.edit.php?recno=';
	echo $row['recno'].'">'.$row['tradename'].'</a><sup><font color="green">'.$row['recno'].'</font></sup>'.chr(10);
	echo '<ul><li>';
	echo $row['chemgroup'].'<li>';
	echo $row['manufacturer'].', ';
	### Quantity & Size Info
	echo $row['quant'].' x '.$row['size'].'<li>';

	### Check for valid MSDS & Label files
	$file = '/var/www/msds/'.$row['label'];
	if (file_exists($file)) {
		echo '<a href="http://florawww.eeb.uconn.edu/msds/'.$row['label'].'">Label</a>';
		} else {
		echo '<font color="Red">No Label</font>';		
		} # if label
	echo ' | ';
	$file = '/var/www/msds/'.$row['msds'];
	if (file_exists($file)) {
		echo '<a href="http://florawww.eeb.uconn.edu/msds/'.$row['msds'].'">MSDS</a>';
		} else {
		echo '<font color="Red">No MSDS</font>';		
	} # if msds
	echo ' | EPA Reg#: '.$row['epa_reg'];
	echo '<li>';
	echo 'Rcvd on '.$row['datercvd'].' from '.$row['source'];

	$daysago = floor((time()-MySQLtoTimestamp($row['confirm']))/86400);
	if ($daysago>60) echo '<font color="RED"><b>';
	echo '<li>Confirmed: ';
	echo '<a href="chem.confirm.php?recno='.$row['recno'].'">'.$row['confirm'].'</a>';
	if ($daysago>60) echo '</b></font>';
	echo '</ul>'.chr(10);
} #foreach
echo '</ul>'.chr(10);
echo '<a href="admin.php">Admin Page</a>';
$db = null;

function MySQLtoTimestamp($mysqlDate) {
    if (strlen($mysqlDate) > 10) {
        list($year, $month, $day_time) = explode('-', $mysqlDate);
        list($day, $time) = explode(" ", $day_time);
        list($hour, $minute, $second) = explode(":", $time);
        $ts = mktime($hour, $minute, $second, $month, $day, $year);
    } else {
        list($year, $month, $day) = explode('-', $mysqlDate);
        $ts = mktime(0, 0, 0, $month, $day, $year);
    }
    return $ts;
}
?> 

</font>
</body>
</html> 


