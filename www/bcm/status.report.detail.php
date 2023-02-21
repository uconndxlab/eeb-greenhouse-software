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

$zone = $_GET['zone'];
$currently = $_GET['currently'];
$url = urlencode(curPageURL());
# Generate Title ################################################
echo '<title>Accession Status Detail</title>';
echo '</head><body>';
### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src = "/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>';
echo '<p>';

############# Generate Listing ###############
$sql = 'select gh_inv.location,gh_inv.currently,gh_inv.latin_name,gh_inv.codeno,gh_inv.confirm from gh_inv';
$sql .= ' where gh_inv.confirm>date_sub(curdate(),interval 350 day)';
$sql .= ' and week(gh_inv.confirm,3)<>week(curdate(),3)';
$sql .= ' and gh_inv.projnum not like "DELETE%"';
$sql .= ' and (gh_inv.currently = "active - pending bloom"';
$sql .= ' or gh_inv.currently = "active - needs attention"';
$sql .= ' or gh_inv.currently like "active - marginal%"';
$sql .= ' or gh_inv.currently like "breaking dormancy"';
$sql .= ' or gh_inv.currently like "going dormant"';
$sql .= ' or gh_inv.currently like "%propagating%"';
$sql .= ' or gh_inv.currently like "plants missing")';
$sql .= ' and gh_inv.location >= '.($zone);
$sql .= ' and gh_inv.location < '.($zone+100);
$sql .= ' order by gh_inv.location,gh_inv.currently desc,gh_inv.latin_name,gh_inv.codeno';
#echo '<hr>'.$sql.'<hr>';
echo '<b>Plants Needing Review</b> - Weekly Checklist<br>';

echo '<ul>';
##	$sth = $db->prepare($sql);
##	$sth->execute();
##	if (!$sth->fetchColumn()) {
		foreach($db->query($sql) as $row) {
			echo '<li>'.$row['location'].' '.str_replace("active - ","",$row['currently']).' <a href="accession.php?codeno='.$row['codeno'].'&tab=tasks"';
			echo ' target="_blank">'.trim($row['latin_name']).'</a>';
			echo ' <a href="status.report.confirm.php?codeno='.$row['codeno'].'&zone='.$row['location'].'">'.$row['codeno'].'</a>';
		} #foreach
##	} elseif {
##		echo '<li>All up to date';
##	}
echo '</ul>';

### Check flowering from previous week
echo '<b>Plants Flowering Last Week</b><ul>';
### Check flowering status
### Allow a flowering button if flowering last week but not confirmed yet this week
$sql = 'select history.recno,history.codeno,gh_inv.location,gh_inv.latin_name from history,gh_inv where history.codeno=gh_inv.codeno';
$sql .= ' and history.class="FLOWERING"';
$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
$sql .= ' and week(history.date,3)=week(curdate(),3)-1'; # a flowering note from last week
#$sql .= ' and week(history.date,3)=52'; # hack for week 1 of the new year
$sql .= ' and gh_inv.confirm>date_sub(curdate(),interval 350 day)';
$sql .= ' and week(gh_inv.confirm,3)<>week(curdate(),3)'; # not yet confirmed this week
$sql .= ' and gh_inv.location >= '.($zone);
$sql .= ' and gh_inv.location < '.($zone+100);
$sql .= ' order by gh_inv.location,gh_inv.latin_name';

foreach($db->query($sql) as $row) {
	$sql = 'select recno from history where codeno='.$row['codeno'].' and class="FLOWERING"';
	$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
	$sql .= ' and week(history.date,3)=(week(curdate(),3))';
	$sth = $db->prepare($sql);
	$sth->execute();
	if (!$sth->fetchColumn()) {
		echo '<li>'.$row['location'].' <a href="accession.php?codeno='.$row['codeno'].'" target="_blank">'.$row['latin_name'].'</a> ---';
		echo ' <a href="zonehistmini.php?codeno='.$row['codeno'].'&data=&class=FLOWERING&zone='.$zone.'&url='.$url.'">';
		echo '<font color="red"><b>Flowering</b></font></a> ---';
		echo ' <a href="status.report.confirm.php?codeno='.$row['codeno'].'&zone='.($row['location']-($row['location'] % 100)).'">'.$row['codeno'].'</a>';
	} 
} # foreach flowering last week
echo '</ul>';

### Return # plants over 28 day confirm
$sql = 'select codeno,latin_name,location from gh_inv where projnum = "GEN_COLL"';
$sql .= ' and gh_inv.location >= '.($zone);
$sql .= ' and gh_inv.location < '.($zone+100);
$sql .= ' and gh_inv.confirm < date_sub(curdate(),interval '.($inventory_interval-1).' day)';

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->rowCount();
echo '<p><b>Oldest Plants Requiring Confirmation</b> ('.$result.' total)<p><ul>';
### Display quick confirm list of 10 oldest
$sql = 'select codeno,latin_name,location from gh_inv where projnum = "GEN_COLL"';
$sql .= ' and gh_inv.location >= '.($zone);
$sql .= ' and gh_inv.location < '.($zone+100);
$sql .= ' and gh_inv.confirm < date_sub(curdate(),interval '.($inventory_interval-1).' day)';
$sql .= ' order by gh_inv.confirm limit 10'; ## only return the 10 oldest
foreach($db->query($sql) as $row) {
	echo '<li>'.$row['location'].' <a href="accession.php?codeno='.$row['codeno'].'"';
	echo ' target="_blank">'.trim($row['latin_name']).'</a>';
	echo ' <a href="status.report.confirm.php?codeno='.$row['codeno'].'&zone='.$row['location'].'">'.$row['codeno'].'</a>';
	} # foreach
echo '</ul>';

### List of plants syringed last week for followup
echo '<b>Plants Syringed Last Week</b> - beta testing, use with caution<ul>';
$sql = 'select history.recno,history.codeno,gh_inv.location,gh_inv.latin_name from history,gh_inv where history.codeno=gh_inv.codeno';
$sql .= ' and history.class="SPRAY"';
$sql .= ' and history.notes = "Water (syringe)"';
$sql .= ' and gh_inv.confirm < curdate()'; # hide for one day if confirmed
$sql .= ' and history.date>date_sub(curdate(),interval 350 day)'; # limit to past 12 month period
$sql .= ' and week(history.date,3)=week(curdate(),3)-1'; # a syringe note from last week
#$sql .= ' and week(history.date,3)=52'; # hack for week 1 of the new year
#$sql .= ' and gh_inv.confirm>date_sub(curdate(),interval 350 day)';
$sql .= ' and gh_inv.location >= '.($zone);
$sql .= ' and gh_inv.location < '.($zone+100);
$sql .= ' order by gh_inv.location,gh_inv.latin_name';

#echo '<hr><li>'.$sql.'<hr>';

foreach($db->query($sql) as $row) {
	$sql = 'select recno from history where codeno='.$row['codeno'].' and history.class="SPRAY"';
	$sql .= ' and  history.notes = "Water (syringe)"';
	$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
	$sql .= ' and week(history.date,3)=(week(curdate(),3))';
	$sth = $db->prepare($sql);
	$sth->execute();
	if (!$sth->fetchColumn()) {
		echo '<li>'.$row['location'].' <a href="accession.php?codeno='.$row['codeno'].'&tab=pest"';
		echo ' target="_blank">'.trim($row['latin_name']).'</a>';
		echo ' <a href="status.report.confirm.php?codeno='.$row['codeno'].'&zone='.$row['location'].'">'.$row['codeno'].'</a>';
		echo ' - <a href="quickhist.php?codeno='.$row['codeno'].'&data=Water (syringe)&class=SPRAY&url='.$url.'">Syringe</a>';
	} # if not syringed this week
} #foreach syringed
echo '</ul>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

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
