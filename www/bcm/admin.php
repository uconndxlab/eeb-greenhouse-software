<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name="viewport" content="width=device-width" >

<title>EEB-GH Admin</title>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

echo '</head><body>';
### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">'.chr(10);
echo '<img src = "/images/icons/question-20.png" title="SEARCH OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>'.chr(10);
echo '<p>';
echo '<h2>Greenhouse Administrative Functions ';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/admin.info.html" target="_blank"><img src = "/images/icons/question-20.png" title="Open Detailed Help Page in New Tab" /></a>';
echo '</h2>';

echo '<ul>';
echo '<li><b>Database Functions</b>';
echo '<ul>';
echo '<li><a href="new_acc_form.php">Add New Accession</a>';
echo '<ul><li><a href="http://florawww.eeb.uconn.edu/bcm/accession.validation.report.php?days2review=90">Review recent accessions</a><i> {curator use}</i></ul>';

echo '<li><a href="new_wish_form.php">Add New Wishlist</a>';
echo '<li><a href="deaccessioning.info.html">Deaccession procedural notes</a>';
echo '<li><a href="loan.entry.php">Class Borrowing</a> - working, needs extra testing to validate';
echo '<li>Current <a href="surplus.php">Surplus Plants</a>';
echo '<li><a href="keyword.cloud.php">Keyword Listing</a>';
echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/glossary.cloud.php">Botanical Glossary</a> - under construction, use with caution';
echo '</ul>'.chr(10);

echo '<li><b>Project & Task Management</b>';
echo '<ul>';
echo '<li>Accession Status Management</a>';
echo '<br><b>Active Zones: </b>';
### Check for Zones with active status
$zones = array (1000,1100,1200,1300,1400,2100,2200,2300,2400,3100,3200,3300,4100,5000,6100,6600,6900);
foreach ($zones as $zone) {
	### temporarily commenting out this section for force all zones to show
	#$sql = 'select gh_inv.location,gh_inv.currently,gh_inv.latin_name,gh_inv.codeno,gh_inv.confirm from gh_inv';
	#$sql .= ' where gh_inv.confirm>date_sub(curdate(),interval 350 day)';
	#$sql .= ' and week(gh_inv.confirm,3)<>week(curdate(),3)';
	#$sql .= ' and gh_inv.projnum not like "DELETE%"';
	#$sql .= ' and (gh_inv.currently = "active - pending bloom"';
	#$sql .= ' or gh_inv.currently = "active - needs attention"';
	#$sql .= ' or gh_inv.currently like "active - marginal%"';
	#$sql .= ' or gh_inv.currently like "breaking dormancy"';
	#$sql .= ' or gh_inv.currently like "going dormant")';
	#$sql .= ' and gh_inv.location >= '.($zone);
	#$sql .= ' and gh_inv.location < '.($zone+100);
	#$sql .= ' order by gh_inv.location,gh_inv.currently desc,gh_inv.latin_name,gh_inv.codeno';
	#$sth = $db->prepare($sql);
	#$sth->execute();
	#if ($sth->fetchColumn()) {
		echo '<a href="status.report.detail.php?zone='.$zone.'">'.$zone.'</a>  ';
	#} # endif check for data
} # foreach zones as zone

echo '<li>Pending Tasks:';
include '/var/www/bcm/admin.projectlist.INCL.php';
echo '</ul>'.chr(10);

echo '<li><b>Integrated Pest Management</b>';
echo '<ul>';
echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=ALL">Comprensive Pest Scouting Report</a>';
echo '<li><b>Target: </b><a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=mite">mite</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=mealybug"> - mealybug</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=scale"> - scale</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=thrip"> - thrip</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=aphid"> - aphid</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=whitefly"> - whitefly</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=ALL&pest=ant"> - ant</a>';
echo '<li><b>Zone (TLS): </b><a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=1100&pest=ALL">1100</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=1200&pest=ALL"> - 1200</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=1300&pest=ALL"> - 1300</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=2100&pest=ALL"> - 2100</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=2200&pest=ALL"> - 2200</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=2300&pest=ALL"> - 2300</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=3100&pest=ALL"> - 3100</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=3200&pest=ALL"> - 3200</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=3300&pest=ALL"> - 3300</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=4100&pest=ALL"> - 4100</a>';
echo '<li><b>Zone (BPB): </b><a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6000&pest=ALL">6000</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6100&pest=ALL"> - 6100</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6200&pest=ALL"> - 6200</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6300&pest=ALL"> - 6300</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6400&pest=ALL"> - 6400</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6500&pest=ALL"> - 6500</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6600&pest=ALL"> - 6600</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6700&pest=ALL"> - 6700</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6800&pest=ALL"> - 6800</a>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/pest.report.detail.php?zone=6900&pest=ALL"> - 6900</a>';
echo '</ul>'.chr(10);

echo '<li><b>Labels, Signage & Mapping</b>';
echo '<ul>';
### Large Format Labels - PDF / TLS Printer
$sql = 'select * from labels_v2';
$sth = $db->prepare($sql);
$sth->execute();
echo '<li><b><a href="http://florawww.eeb.uconn.edu/bcm/label.update.php">';
if ($sth->rowCount() >19) echo '<font color="RED">';
echo $sth->rowCount();
echo '</a></b> Labels pending for TLS Laser Printer';
if ($sth->rowCount() >19) echo '</font>';

### Small Format Labels - PDF / BPB Printer
$sql = 'select * from labels';
$sth = $db->prepare($sql);
$sth->execute();
echo '<li><b><a href="http://florawww.eeb.uconn.edu/bcm/label.update.BPB.php">';
#if ($sth->rowCount() >19) echo '<font color="RED">';
echo $sth->rowCount();
#if ($sth->rowCount() >19) echo '</font>';
echo '</a></b> Labels pending for BPB Laser Printer';
echo '</ul>'.chr(10);

echo '<li><b>Staff / User Functions</b>';
echo '<ul>';
### Check for staff training due
echo '<li><a href="training.staff.view.php">Staff Training Records</a>';

### Check for user WPS training due within 14 days
$sql = 'select recno from users where active and (wps < DATE_SUB(curdate(),INTERVAL 351 DAY) or wps is NULL)';
$sth = $db->prepare($sql);
$sth->execute();
echo '<li><a href="training.wps.user.view.php">User WPS Training</a>: ';
if ($sth->rowCount() >0) echo '<font color="RED">';
echo $sth->rowCount().' users due for training';
if ($sth->rowCount() >0) echo '</font>';

echo '<li><a href="https://www.youtube.com/watch?v=WK7XnWsIkwQ">Safety in the Greenhouse</a> - EPA Worker PST00026';
echo '<li><a href="https://www.youtube.com/watch?v=XtYfnV0mfOY">Safety in the Greenhouse</a> - EPA Handler PST00027';
echo '</ul>'.chr(10);

echo '<li><b>Supplies Needed</b>';
echo '<ul>';
echo '<li>Shopping List';
echo '</ul>';

echo '<li><b>Visitor Management</b>';
echo '<ul>';
echo '<li><a href="addtour.php">Add Tour / Visitors</a>';
echo '</ul>'.chr(10);

echo '<li><b>Statistics & History</b>';

echo '<ul>';
echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/hist_stat.php?days=15">History Chart</a>';
### Check for confirmations today
$sql = 'select codeno from gh_inv where confirm = curdate()';
$sth = $db->prepare($sql);
$sth->execute();
echo ': <font color="RED"><b>'.$sth->rowCount().' </b>confirmations today</font>';
echo '<li><a href="history.batch.entry.php">Batch History Insert</a>';
echo '<li><a href="assess.inventory.php">Collection Assessment</a> <i>- under (re)-development, use with caution</i>';
echo '</ul>'.chr(10);

echo '<li><b>Pesticide & Chemical Management</b>';
echo '<ul>';
echo '<li>Add New Chemical';
echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/chem.view.php">View/Edit</a> Active Chemical Inventory <b>(UNDER CONSTRUCTION)</b>';
echo '<li>Printer Friendly <a href="http://florawww.eeb.uconn.edu/chemical_list.html">Chemical Inventory</a>';
echo '<li><a href="http://florawww.eeb.uconn.edu/msds/">Online SDS & Label Archive</a>';
echo '<li>Pesticide Application (batch)';
echo '</ul>'.chr(10);

echo '</ul>';
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';

$db = null;
?>

</font>
</body>
</html> 
