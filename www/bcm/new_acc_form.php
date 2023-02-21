<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

### generate input screen and insert new accession data ###

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}


$sql = 'select max(codeno) from gh_inv where codeno<210000000';
$sth = $db->prepare($sql);
$sth->execute();
$codeno = $sth->fetchColumn();
$codeno++;

echo '</head><body>';
echo '<h1>New Accession Data Entry ';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/new_acc_form.info.html" target="_blank"><img src = "/images/icons/question-20.png" title="Open Detailed Help Page in New Tab" /></a>';
echo '</h1>';

echo '<table><tr><form action="http://florawww.eeb.uconn.edu/bcm/insert_new_acc.php" method="post"></tr>';
echo '<tr><td>Codeno:</td><td><input name="codeno" value='.$codeno.'></td></tr>';
echo '<tr><td>Genus:</td><td><input name="genus" value=""></td></tr>';
echo '<tr><td>Species:</td><td><input name="species" value=""></td></tr>';
echo '<tr><td>Source:</td><td><input name="source" value=""></td></tr>';
echo '<tr><td>Location:</td><td><input name="location" value="1000"></td></tr>';
echo '<tr><td>Quantity:</td><td><input name="quant" value="1"></td></tr>';
echo '<tr><td>Project:</td><td><input name="projnum" value="GEN_COLL"></td></tr>';
echo '<tr><td></td><td><input type="submit" value="Create Accession"></td></tr>';
echo '</form></table>';

$db = null;
?> 
