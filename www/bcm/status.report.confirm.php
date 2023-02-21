<html>
<head>

<?php
##### Same as regular with different redirect to status.report.detail
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}


$codeno = $_GET["codeno"];
$zone = $_GET["zone"];
$zone = ($zone-($zone % 100)); # strip bench number & return zone only

$sql = 'select gh_inv.latin_name,classify.family from gh_inv,classify where gh_inv.codeno='.$codeno.' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$family=$result['family'];

# Process Database Update Here ############################
$sql = 'update gh_inv set confirm=curdate() where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
### Flag all members of family for updating
$sql='update gh_inv,classify set gh_inv.tempflag=1 where gh_inv.genus=classify.genus and classify.family="'.$family.'" and gh_inv.projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();

$db = null;
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=status.report.detail.php?zone='.$zone.'">';
?> 

</body>
</html> 
