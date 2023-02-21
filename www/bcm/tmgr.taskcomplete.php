<html><head>

<?php
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$url = $_GET['url'];
$recno = $_GET['recno'];
$codeno = $_GET['codeno'];
$assign = $_GET['assign'];
$status = $_GET['status'];
$location = $_GET['location'];

$sql = 'select descrip from tasks where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$descrip = $result['descrip'];

### Check for valid assign field

### Process Tasks Database Update Here 
if ($status=='DONE'){
	$sql = 'update tasks set status="'.$status.'",dcomplete=curdate(),completeby="'.$assign.'" where recno='.$recno;
	} else {
	$sql = 'update tasks set status="'.$status.'",completeby="'.$assign.'" where recno='.$recno;
	}
$sth = $db->prepare($sql);
$sth->execute();
$sql = 'select dcomplete,location from tasks where recno='.$recno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$dcomplete = $result['dcomplete'];
$location = $result['location'];
### Insert History Code if status = COMPLETE
if ($status=='COMPLETE'){
	### update confirm
	$sql = 'update gh_inv set confirm=curdate(),tempflag=1 where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
	# Process Database Update Here ############################
	# Append assign to $descrip
	$descrip = $descrip.' ['.$assign.']';
	#set values to posted data
	$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
	$sql .= 'values ('.$codeno.',"';
	### Check for special CLASS
	if (substr_count($descrip,"Apply")>0) {  ### change class if spray Application
		$class='SPRAY';
		} elseif (substr_count($descrip,"Fertilize")>0) {  ### change class if fertilizer application
		$class='FERT';
		} else {
		$class='NOTE';
		}
	$sql .= $location.'","';
	$sql .= $class.'","';
	$sql .= $dcomplete.'",0,"';
	$sql .= $descrip.'"';
	$sql .= ',"",0,0)';
	# process update SQL
	$sth = $db->prepare($sql);
	$sth->execute();
}

$db = null;
#if (strlen($url)>0) {
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
#} else {
	if ($status=="COMPLETE") {
		echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/tmgr.reviewtask.php">';
	} else {
		echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=tasks">';
	}
#} # strlen(url)
?> 

</body>
</html> 
