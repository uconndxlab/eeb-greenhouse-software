<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}
$term = $_GET['term'];

$sql = 'select term,def from glossary where term="'.$term.'"';
foreach($db->query($sql) as $result) {
	# Generate Title ################################################
	echo '<title>Glossary Edit for: '.$result['term'];
	echo '</title>';
	echo '</head><body>';
	echo "<hr></center><h3>";
	# Generate Latin Name Title ###########################################
	echo 'Update glossary definition</h3><i>Limit 254 characters</i><hr>';
} # foreach

echo '<form action="http://florawww.eeb.uconn.edu/bcm/glossary.updatetext.php" method="post">';
echo '<input type="hidden" name="term" value="'.$term.'">';
echo '<b>'.$term.': </b><input type="text" size=80 name="def" value="'.$result['def'].'">';
echo '<p><input type="submit" name="submit" value="Update Definition"></form>';
####### Build Cancel Button ################
echo '<form action="http://florawww.eeb.uconn.edu/bcm/glossary.cloud.php" method="get">';
echo '<input type="submit" value="Cancel"></form><hr>'; 

echo '<b><a href="glossary.query.php?term='.$term.'">Find accessions</a></b> with the term <b>'.$term.'</b> in Description or Usage fields.';
$db = null;
?> 
</body>
</html> 
