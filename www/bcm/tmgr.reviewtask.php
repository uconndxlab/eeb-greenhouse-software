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

#$assign = $_GET['assign'];
#$status = $_GET['status'];
#$location = substr($_GET['location'],0,4);
#$dcreate = $_GET['dcreate'];
#$dcomplete = $_GET['dcomplete'];

# Generate Title ################################################
echo '<title>Completed Tasks for Review</title>';
echo '</head><body>';

### Create Search Box
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

$sql = 'select tasks.codeno,tasks.descrip,tasks.status,tasks.location,tasks.assignto,tasks.recno,gh_inv.latin_name from tasks,gh_inv';
$sql .= ' where tasks.status="DONE"';
$sql .= ' and tasks.codeno=gh_inv.codeno';
$sql .= ' order by tasks.location,gh_inv.location';

$sth = $db->prepare($sql);
$sth->execute();
echo '<h3>'.$sth->rowCount();
echo ' Completed Tasks for Approval</h3>';
echo '<ul>';
foreach($db->query($sql) as $row) {
	### Build Completion Button
	echo '<li><form action="http://florawww.eeb.uconn.edu/bcm/tmgr.taskcomplete.php" style="display: inline-block;" method="get">';
	echo '<input type="hidden" name="codeno" value="'.$row['codeno'].'">';
	echo '<input type="hidden" name="status" value="COMPLETE">';
	echo '<input type="hidden" name="recno" value="'.$row['recno'].'">';
	echo '<input type="hidden" name="assign" value="'.$row['assignto'].'">';
	echo '<input type="submit" name="btn" value="Approve"></form>';
	echo ' '.$row['location'];
	echo ' <a href="accession.php?codeno='.$row['codeno'].'&tab=tasks">'.$row['latin_name'].'</a>';
	echo ' - '.$row['descrip'].' ['.$row['assignto'].']<sup>'.$row['recno'].'</sup>';
} #foreach
echo '</ul>';
$db = null;
echo '<a href="admin.php">Admin Page</a>';

?> 
</body>
</html> 
