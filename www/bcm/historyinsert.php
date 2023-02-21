<html>
<head>

<?php

include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$value = $_POST['value'];
$i=0;
if ($value==""){
	$value=0;
}
$codeno = $_POST['codeno'];
if ($codeno=="111111111"){
	$zone=$_POST['zone'];
}else{ 
$sql = 'select gh_inv.latin_name,gh_inv.location from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	
$zone = $result['location'];
}

# Process Database Update Here ############################

#set values to posted data
$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
$sql .= 'values ('.$codeno.',';
$sql .= $zone.',"';
$sql .= $_POST["class"].'",';
$sql .= 'curdate(),'.$value.',"';
$sql .= $_POST["data"].'"';
$sql .= ',"",0,0)';

# process update SQL
$sth = $db->prepare($sql);
$sth->execute();

# Process Confirm Here ############################
$sql = 'update gh_inv set confirm=curdate() where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

#if ($_POST["class"]=="ZNOTE"){
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/hist_stat_detail.php?class=ZNOTE&daysago=0&numdays=0">';
#
#} elseif ($_POST["class"]=="FERT") {
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/hist_stat_detail.php?class=FERT&daysago=0&numdays=0">';
#} elseif ($_POST["class"]=="SCOUT" and $codeno>999990000) {
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/i.vselect.php">';
if ($_POST["class"]=="TOUR") {
	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/admin.php">';
} elseif ($_POST["class"]=="VISITOR") {
	echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/admin.php">';
} else {
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=history">';
}

$db = null;
?> 

</body>
</html> 
