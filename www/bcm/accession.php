<html>
<head>
<meta name="viewport" content="width=device-width" />
<link href="style.css" type="text/css">
<script language="javascript">
  function toggle_it(itemID){
      // Toggle visibility between none and inline
      if ((document.getElementById(itemID).style.display == 'none'))
      {
        document.getElementById(itemID).style.display = 'inline';
      } else {
        document.getElementById(itemID).style.display = 'none';
      }
  }
</script>
<?php
include '/var/www/bcm/credentials.php';
include '/var/www/bcm/evaluate.php';

$codeno = $_GET['codeno'];
$tab = $_GET['tab']; # tab mode
$url = urlencode(curPageURL());
### Update accession rankings.
### - Note that running this singly for each accession may result in slightly skewed rankings
### - best rankings results will occur when entire collection is reevaluated.
$result = evaluate($codeno);

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'select gh_inv.latin_name,classify.family,gh_inv.author,gh_inv.projnum from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	
### Generate Title ###
echo "<title>".$result['latin_name'].chr(10);
echo ' {'.$result['family'].'} #'.$codeno;
echo "</title>".chr(10);
echo '<head><body>'.chr(10);

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>'.chr(10);
echo '</form><p>'.chr(10);

# Generate Latin Name Title ###########################################
echo '<h3><i>'.$result['latin_name'].'</i> '.$result['author'].'</h3>';
echo '<b>Accession# </b>'.$codeno;
### Create Family Classification Link
echo '  <form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" style="display: inline-block;" method="post">';
echo '<input type="hidden" name="instring" value="'.$result['family'].'">';
echo '<input type="submit" style="color:green;" value="'.$result['family'].'">';
echo '</form>';
echo '<br>';
$family = $result['family']; # for later reference 

### Create projnum links
if ($result['projnum']<>"GEN_COLL"){
	echo 'Assigned to: <font color="red"><b>'.$result['projnum'].'</b></font> <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=projnum&url='.$url.'"> {Edit}</a><br>';
} else {
	echo 'Assigned to: <font color="green"><b>'.$result['projnum'].'</b></font> <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=projnum&url='.$url.'"> {Edit}</a><br>';
}

$sql = 'select * from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<br><a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=quant&url='.$url.'">';
echo $result['quant'].'</a> confirmed @ ';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=location&url='.$url.'">';
echo $result['location'].'</a> on ';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/confirm.php?codeno='.$codeno.'">'.date("m-d-y",strtotime($result['confirm'])).'</a>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=quant2&url='.$url.'">';
echo $result['quant2'].'</a> confirmed @ ';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=location2&url='.$url.'">';
echo $result['location2'].'</a>';
echo ' | <a href="http://florawww.eeb.uconn.edu/bcm/locswap.php?codeno='.$codeno.'">SWAP^^</a>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=surplus&url='.$url.'">';
echo $result['surplus'].'</a> surplus plants available ';

if ($result['redist']) echo '<p><font color="RED"><b>NOTE:</b> Accession has restrictions on redistribution - <br><b>DO NOT</b> give away, trade or share without manager consent.</font>';

echo '<p>';

### Check flowering status - disable and color button if recently checked
##### Check if already noted for the current week
$sql = 'select recno from history where codeno='.$codeno.' and class="FLOWERING"';
$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
$sql .= ' and week(history.date,3)=week(curdate(),3)';
$sth = $db->prepare($sql);
$sth->execute();
if ($sth->fetchColumn()) {
	echo '<b>Flowering</b> | ';
	} else {
	echo'<a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=&class=FLOWERING&url='.$url.'">Flowering</a> | ';
} # if flowering
echo '<b>Currently:</b> '.$result['currently'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=currently&url='.$url.'"> {Edit}</a> as of ';
echo date("m-d-y",strtotime($result['cdate']));

######################################################
### Plain Text Tabbing - KISS
######################################################
echo '<p>';
echo '<b>';
#echo '<a href="accession.php?codeno='.$codeno.'">NONE</a>';
echo '<a href="accession.php?codeno='.$codeno.'&tab=data">DATA</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=classify">CLASSIFY</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=bgci">BGCI</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=assess">ASSESS</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=pest">PEST</a>';
echo ' | <a href="/'.$codeno.'.html">WWW</a>';

echo '<p><a href="accession.php?codeno='.$codeno.'&tab=culture">CULTURE</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=history">HISTORY</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=tasks">TASKS</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=labels">LABELS</a>';
echo ' | <a href="accession.php?codeno='.$codeno.'&tab=misc">MISC</a>';
echo '</b><p>';

### Set up tabbed sections here, code in separate INCLude files for legibility
if ($tab=='data') {
	######################################################
	### DATA TAB
	######################################################	
	include '/var/www/bcm/accession.data.INCL.php';
} elseif ($tab=='classify') {
	######################################################
	### CLASSIFY TAB
	######################################################
	include '/var/www/bcm/accession.classify.INCL.php';
} elseif ($tab=='bgci') {
	######################################################
	### RESTRICTIONS / BGCI / IUCN TAB
	######################################################
	include '/var/www/bcm/accession.bgci.INCL.php';
} elseif ($tab=='assess') {
	######################################################
	### COLLECTION ASSESSMENT TAB
	######################################################
	include '/var/www/bcm/accession.assess.INCL.php';
} elseif ($tab=='pest') {
	######################################################
	### PEST CONTROL TAB
	######################################################
	echo '<b>PEST SCOUT / CONTROL TAB: </b><p>';
	include '/var/www/bcm/accession.pest.INCL.php';
} elseif ($tab=='culture') {
	######################################################
	### CULTURAL TAB
	######################################################
	echo '<b>CULTURE TAB: </b><p>';	
#	echo '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><br>';
	include '/var/www/bcm/accession.culture.INCL.php';
} elseif ($tab=='history') {
	######################################################
	### HISTORY / NOTES TAB
	######################################################
	include '/var/www/bcm/accession.history.INCL.php';
} elseif ($tab=='tasks') {
	######################################################
	### PROJECTS / TASKS TAB
	######################################################
	include '/var/www/bcm/accession.tasks.INCL.php';
} elseif ($tab=='labels') {
	######################################################
	### LABELS & SIGNAGE TAB
	######################################################
	include '/var/www/bcm/accession.labels.INCL.php';
} elseif ($tab=='misc') {
	######################################################
	### MISC TAB
	######################################################
	include '/var/www/bcm/accession.misc.INCL.php';
} # tabbed if/else section

echo '<p>';

##################################
$db = null; # close PDO connection
echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

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

