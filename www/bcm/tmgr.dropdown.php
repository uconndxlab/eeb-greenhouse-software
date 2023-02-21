<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

include '/var/www/bcm/credentials.php';

$field=$_GET["v"];
$recno = $_GET['recno'];
$status = $_GET['status'];
$location = $_GET['location'];
$assign = $_GET['assign'];
$url = $_GET['url'];

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'set sql_mode=""'; # turn off default
$sth = $db->prepare($sql);
$sth->execute();

$sql = 'select tasks.codeno,tasks.location,tasks.descrip,gh_inv.latin_name from tasks,gh_inv where tasks.codeno=gh_inv.codeno and recno='.$recno;

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	

$codeno=$result['codeno'];
# Generate Title ################################################
if ($codeno<>111111111) {
	echo '<title>'.$codeno.' - '.$result['latin_name'].'</title>';
	} else {
	echo '<title>Generic Task</title>';
	}
echo '</head><body><H3>';

# Generate Task Title ###########################################
if ($codeno<>111111111) echo $codeno.' - '.$result['latin_name'];

echo '<br>'.$result['location'].': '.$result['descrip'].'</h3>'.chr(10);

# Generate General Information #######################################
$sql = 'select tasks.'.$field;
$sql .= ' from tasks where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();
$default = $sth->fetchColumn();

### Build Specific Dropdowns here
if ($field=='assignto') $sql = 'select distinct init as keyval,lname,fname from users where active and staff order by title,lname';
if ($field=='status') $sql = 'select distinct status as keyval from taskstatus order by priority';

echo '<form action="http://florawww.eeb.uconn.edu/bcm/tmgr.updatetext.php" method="get">'.chr(10);
echo '<input type="hidden" name="recno" value="'.$recno.'">'.chr(10);
echo '<input type="hidden" name="field" value="'.$field.'">';
echo '<input type="hidden" name="status" value="'.$status.'">';
echo '<input type="hidden" name="assign" value="'.$assign.'">';
echo '<input type="hidden" name="codeno" value="'.$codeno.'">';
echo '<input type="hidden" name="location" value="'.$location.'">';
echo '<b>'.$field.': </b>'.chr(10);
echo '<select name=text size=1>';
foreach($db->query($sql) as $row) {
	echo '<option';
#	if ($field=='assignto') {
#		echo ' value="'.$row['init'].'"';	
#	} else {
		echo ' value="'.$row['keyval'].'"';
#	}
	if ($row['keyval']==$default) {
		echo ' selected';
	} 
	if ($field=='assignto') {
		echo '>'.$row['lname'].', '.$row['fname'];	
	} else {
		echo '>'.$row['keyval'];
	}
	echo '</option>'.chr(10);
} # foreach

echo '</select><input type="submit" name="submit" value="Update"></form>'.chr(10);

$db = null;

#include('/var/www/bcm/footer.php');

?> 
</body>
</html> 
