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
$week = $_GET['week'];
$zone = $_GET['zone'];
$sprayfilter = $_GET['sprayfilter'];  # number of days since last spray application
$pest = str_replace("_"," ",$_GET['pest']);
$url = urlencode(curPageURL());

# Generate Title ################################################
echo '<title>'.$scout_interval.' Day Pest Detail</title>';
echo '</head><body>';

############# Do Physical Scouting Listing ###############
$sql = 'select gh_inv.location,history.notes,unix_timestamp(history.date) as lastdate,gh_inv.latin_name,gh_inv.codeno,history.class as cls,history.value from history, gh_inv';
$sql .= ' where (history.class="SCOUT" or history.class="BIOCONTROL")';
#$sql .= ' and week(history.date,3)='.$week;
$sql .= ' and history.date>date_add(curdate(),interval-'.$scout_interval.' day)';
if ($zone<>"ALL") $sql .= ' and history.zone >='.$zone.' and history.zone <'.$zone.'+100';
if ($pest<>"ALL") $sql .= ' and history.notes like "%'.$pest.'%"';
$sql .= ' and history.codeno=gh_inv.codeno';
$sql .= ' order by history.zone,gh_inv.latin_name,gh_inv.codeno,history.date';

echo '<b>'.$scout_interval.' Day Scouting Report</b><br>';
echo '<img src="/images/checkmark.gif" /> indicates pest control notations have taken place today';
echo '<br><img src="/images/spray_trans.png" /></img> indicates spray activities have taken place in past 10 days';
echo '<br><img src="/images/ladybug.png" /> indicates biocontrol notes have been made in past '.$scout_interval.' days';
echo '<br>superscript = number of days ago notation made';
echo '<br>scout interval can be manually adjusted in global file - credentials.php (manager only)';
echo '<ul>';

$lastcode=0;
foreach($db->query($sql) as $row) {
#	### Check for last spray application
#	$sprayed = 0; # spray filter reset
#	$sql = 'select max(date) as daysago from history where codeno='.$row['codeno'].' and class="SPRAY"';
#	$sth = $db->prepare($sql);
#	$sth->execute();
#	if ($sth->rowCount()>0) $sprayed = ;
#	echo $sprayed;
	if ($lastcode<>$row['codeno']){
		if ($lastcode<>0) {
		#	echo ' | <a href="quickhist.php?codeno='.$row['codeno'];
		#	echo '&data=Water (syringe)&class=SPRAY&url=';
		#	echo'">Syringe</a>';
		} # if loop
		echo '<li>'.$row['location'];
		### Check for pest history notes today (ie already addressed, at least in part)
		$sql = 'select * from history where codeno='.$row['codeno'].' and (class="SCOUT" or class="BIOCONTROL" or class="SPRAY") and history.date=curdate()';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->rowCount()>0) echo ' <img src="/images/checkmark.gif" />';
		### Check for spray history notes last 10 days (ie already addressed, at least in part)
		$sql = 'select * from history where codeno='.$row['codeno'].' and class="SPRAY" and history.date>date_add(curdate(),interval-10 day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->rowCount()>0) echo ' <img src="/images/spray_trans.png" />';
		### Check for biocontrol notes last $scoutinterval days (ie already addressed, at least in part)
		$sql = 'select * from history where codeno='.$row['codeno'].' and class="BIOCONTROL" and history.date>date_add(curdate(),interval-'.$scout_interval.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->rowCount()>0) echo ' <img src="/images/spray_trans.png" />';
		echo ' <a href="accession.php?codeno='.$row['codeno'];
		echo '&tab=pest" target="_blank">'.trim($row['latin_name']).'</a> '.$row['codeno'];
	}
	$daysago=intval((time()-$row['lastdate'])/86400);
	echo ' | <font color="GREEN">'.$row['notes'].'</font><sup>'.$daysago.'</sup>';

### pest specific quickhist

if ($pest == 'whitefly') {
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Botanigard 22WP&class=SPRAY&url='.$url.'">Botanigard</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif whitefly

if ($pest == 'aphid') {
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Botanigard 22WP&class=SPRAY&url='.$url.'">Botanigard</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif aphid

if ($pest == 'mite') {
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Abamectin 0.15EC&class=SPRAY&url='.$url.'">Abamectin</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif mite

if ($pest == 'thrip') {
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Enstar AQ&class=SPRAY&url='.$url.'">Enstar</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Conserve SC&class=SPRAY&url='.$url.'">Conserve</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif thrip

if ($pest == 'mealybug') {
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Botanigard 22WP&class=SPRAY&url='.$url.'">Botanigard</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=M-Pede&class=SPRAY&url='.$url.'">M-Pede</a>}';
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Distance&class=SPRAY&url='.$url.'">Distance</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif mealybug

if ($pest == 'scale') {
	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Enstar AQ&class=SPRAY&url='.$url.'">Enstar</a>}';
#	echo ' - {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>}';
} # endif scale
############# This section included for full zone spot sprays, comment out when not in use
if ($pest == 'ALL') {
	echo ' {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Enstar AQ&class=SPRAY&url='.$url.'">Enstar AQ</a>}';
	#echo ' {<a href="quickhist.php?codeno='.$row['codeno'].'&data=Abamectin 0.15EC&class=SPRAY&url='.$url.'">Abamectin</a>}';
} # endif ALL

	$lastcode = $row['codeno'];
} #foreach

#if ($lastcode>0) {
#	echo ' | '.'Syringe';
#} # if lastcode>0
echo '</ul>';
echo '<a href="admin.php">Admin Page</a>';
### Check for zone releases


$db = null;
### function to grab current URL
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
?> 

</body>
</html> 
