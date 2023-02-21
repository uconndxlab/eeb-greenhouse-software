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

$codeno = $_POST['codeno'];
$fieldlist = explode(',',$_POST['fieldlist']);
$update = $_POST['update'];
$url = $_POST['url'];

# Generate Title ################################################
echo '<title>Accession Checkbox update page</title>';
echo '</head><body>';

for ($i = 0; $i < sizeof($fieldlist); $i++) { 
	### reset each field ###
	$sql = 'update gh_inv set '.$fieldlist[$i].'=0 where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
#	echo $sql.'<br>';
}
echo '<hr>';
for ($i = 0; $i < sizeof($update); $i++) { 
	### Set updated fields ###
	$sql = 'update gh_inv set '.$update[$i].'=1 where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
#	echo $sql.'<br>';
}


$db = null;;

echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
?> 

</body>
</html> 
