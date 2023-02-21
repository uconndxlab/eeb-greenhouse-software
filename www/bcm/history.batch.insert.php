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

$update = $_POST["update"];
$class = $_POST['class'];
$note = $_POST['note'];

# Generate Title ################################################
echo '<title>Batch History Insert</title>';
echo '</head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

for ($i = 0; $i < sizeof($update); $i++) { 
	echo '<br>'.$update[$i].' - <b>'.$class.': </b>'.$note;
	$sql = 'select location from gh_inv where codeno='.$update[$i];
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$zone = $result['location'];
	# first insert event into history file
	$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
	$sql .= 'values ('.$update[$i].',';
	$sql .= $zone.',"';
	$sql .= $class.'",curdate(),0,"'.$note.'","",0,0)';
	#echo $sql.'<br>';
	$sth = $db->prepare($sql);
	$sth->execute();
	### Now update tempflag in gh_inv ###
	$sql = 'update gh_inv set tempflag=1 where codeno='.$update[$i];
	$sth = $db->prepare($sql);
	$sth->execute();
}
echo '<p>'.$i.' entries entered';
echo '<p>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<p>';
echo '<a href="admin.php">Admin Page</a>';


$db = null;;

?> 

</body>
</html> 
