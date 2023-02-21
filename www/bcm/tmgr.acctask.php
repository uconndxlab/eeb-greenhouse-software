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

$task="";
$codeno = $_GET['codeno'];
$task = $_GET['task'];
$url = $_GET['url'];
$sql = 'select latin_name,location from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

# Generate Title ################################################
echo '<title>Create Task for '.$result['latin_name'].' ['.$codeno.']</title>';
echo '</head><body><h3>';

# Generate Latin Name Title ###########################################
echo 'Create New Task for '.$result['latin_name'].'</h3><hr>'.chr(10);

echo '<form action="http://florawww.eeb.uconn.edu/bcm/tmgr.inserttask.php" method="get">'.chr(10);
echo '<input type="hidden" name="codeno" value="'.$codeno.'">'.chr(10);
echo '<input type="hidden" name="url" value="'.$url.'">'.chr(10);
echo '<input type="hidden" name="location" value="'.$result['location'].'">'.chr(10);
echo '<b>Enter Task: </b><input type="text" size=40 name="text" value="'.$task.'"><br>'.chr(10);
### Need to set sql_mode="" in order for queries to run properly
$sql = 'set sql_mode=""';
$sth = $db->prepare($sql);
$sth->execute();
### Build Assignto Dropdown Box
echo '<b>Assign to: </b><select name="assign" size=1>'.chr(10);
$sql = 'select distinct init,lname,fname from users where staff and active order by title,lname';

foreach($db->query($sql) as $row) {
	echo '<option';
	if ($row['init']=="NONE") echo ' selected';
	echo ' value="'.$row['init'].'">'.$row['lname'].', '.$row['fname'].'</option>'.chr(10);	
}

echo '</select>'.chr(10);
### Build Status Dropdown Box
echo '<br><b>Status: </b><select name="status" size=1>';
$sql = 'select distinct status from taskstatus where priority<10 order by priority';

foreach($db->query($sql) as $row) {
	echo '<option';
	if ($row['status']=="TODO") echo ' selected';
	echo ' value="'.$row['status'].'">'.$row['status'].'</option>'.chr(10);	
} # foreach
echo '</select>'.chr(10);
echo '<br><b>Future Task: </b><input type="text" size=10 name="dfuture" value="0000-00-00">'.chr(10);
echo '<br><b>Recurring Task (recur every X weeks): </b><input type="text" size=10 name="recur_every" value="">'.chr(10);
echo '<br><b>Recurring Task (recur during specific week#): </b><input type="text" size=10 name="recurweek" value="">'.chr(10);
echo '<p><input type="submit" name="submit" value="Create Task"></form>';

$db = null;
?> 
</body>
</html> 
