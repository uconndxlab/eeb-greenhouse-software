<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

$codeno = $_POST['codeno'];

#set values to posted data
$sql = 'insert ignore into gh_inv (codeno,genus,species,latin_name,source,projnum,location,location2,quant,quant2,acc_date,date_rcvd,confirm) ';
$sql .= 'values ('.$codeno.',"';
$sql .= $_POST['genus'].'","';
$sql .= $_POST['species'].'","';
$sql .= $_POST['genus'].' '.$_POST["species"].'","';
$sql .= $_POST['source'].'","';
$sql .= $_POST['projnum'].'",';
$sql .= $_POST['location'].',0,';
$sql .= $_POST['quant'].',0,';
if ($codeno > 100000000) {
	$sql .= 'curdate(),curdate(),curdate()';
	} else {
	$sql .='00000000,00000000,00000000';
	}
$sql .= ')';
$sth = $db->prepare($sql);
$sth->execute();

### update tempflag for family
$sql = 'select classify.family from gh_inv,classify where gh_inv.codeno='.$codeno.' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();

$result = $sth->fetch(PDO::FETCH_ASSOC);
$family = $result['family'];

$sql = 'update gh_inv,classify set gh_inv.tempflag=1 where gh_inv.genus=classify.genus and classify.family="'.$family.'"';
$sth = $db->prepare($sql);
$sth->execute();

echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/query.php">';
$db = null;
?> 

</body>
</html> 
