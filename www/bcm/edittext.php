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
$codeno = $_GET['codeno'];
$url = $_GET['url'];
$sql = 'select gh_inv.latin_name,classify.family,gh_inv.author from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
foreach($db->query($sql) as $result) {
	# Generate Title ################################################
	echo '<title>Text Edit for '.$result['latin_name'];
	echo ' {'.$result['family'].'} #'.$codeno;
	echo '</title>';
	echo '</head><body>';
	echo "<hr></center><h3>";
	# Generate Latin Name Title ###########################################
	echo '<i>'.$result['latin_name'].'</i> '.$result['author'].'</h3><hr>';
} # foreach

# Generate General Information #######################################
$sql = 'select gh_inv.'.$_GET["v"];
$sql .= ' from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$value = $sth->fetchColumn();

echo '<form action="http://florawww.eeb.uconn.edu/bcm/updatetext.php" method="post">';
echo '<input type="hidden" name="codeno" value="'.$codeno.'">';
echo '<input type="hidden" name="field" value="'.$_GET["v"].'">';
echo '<input type="hidden" name="url" value="'.$url.'">';
echo '<b>'.$_GET["v"].': </b><input type="text" size=80 name="text" value="'.$value.'">';

echo '<p><input type="submit" name="submit" value="Update Record"></form>';
####### Build Cancel Button ################
echo '<form action="http://florawww.eeb.uconn.edu/bcm/accession.php" method="get">';
echo '<input type="hidden" name="codeno" value="'.$codeno.'">';
echo '<input type="submit" value="Cancel"></form><hr>'; 

$db = null;
?> 
</body>
</html> 
