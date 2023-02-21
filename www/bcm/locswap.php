<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php
include '/var/www/bcm/credentials.php';

try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occured connecting to BCM!".chr(10); ## User friendly message
	echo $ex->getMessage().chr(10); ## Explicit Error Message
}

$codeno = $_GET['codeno'];
### get current location & quant
$sql = 'select location,quant,location2,quant2 from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

### Update database
$sql = 'update gh_inv set location='.$result['location2'].',location2='.$result['location'].',quant='.$result['quant2'].',quant2='.$result['quant'].' where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

$db = null; ### close connection
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'">';
?> 
</body>
</html> 
