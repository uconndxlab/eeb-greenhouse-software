<html>
<head>

<meta name="viewport" content="width=device-width" />

<?php
#### Pared down version - Flowering & Confirm Only
include '/var/www/bcm/credentials.php';

try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}

$url = urlencode(curPageURL());
$zone = $_GET['zone'];
$currweek = (int)date('W');

echo '<title>Inventory Report for Week# '.date("W").', Zone# '.$_POST['zone'].'</title></head><body>';

#echo '<a href="query.php">QUERY</a>';
#echo ' | ZONE LIST'; #<a href="inventorymini.php">
#echo ' | COMP'; #<a href="weekly.php?init=ALL&comprehensive=1">
#echo ' | <a href="http://florawww.eeb.uconn.edu/bcm/map_status.php">MAP</a>';
#echo ' | PEST CHECK'; #<a href="http://florawww.eeb.uconn.edu/bcm/scout_list_14-28.php">
### Create Search Box
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autocomplete="off">'; ## Autofocus attribute removed to allow refresh/anchor links to work properly
echo '<input type="submit" value="Search">';
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TDWG code (3-digit upper case);';
echo ' 9-digit accession number; 4-digit bench number"></img>';
echo '</form>';



echo '<table border width=100%>';
$sql = 'select descrip,length,width from b_assign where location='.$zone;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<tr><td><b>'.$result['descrip'].'</b></td>';
$benchsize=($result['width']*$result['length']);
echo '<td><a href="scoutbyzonemini.php?zone='.($zone-1).'">PREV<br>ZONE</a></td></tr>';

$sql = 'select codeno,latin_name,location,location2,location3,confirm,signage,quant,quant2,quant3,space_req,propstatus,propstatusdate,currently from gh_inv where ';
$sql .= '(location='.$zone;
$sql .= ' or location2='.$zone.')';
$sql .= ' order by latin_name';
$i = 0; # used for color toggle
foreach($db->query($sql) as $result) {
	$codeno = $result['codeno'];
	echo '<tr><td';
	if($i&1) {
		echo ' bgcolor=#FFE6CC';
		} else {
		echo ' bgcolor=#ECFFEC';
	}


#	$age = floor((time() - strtotime($result['confirm']))/86400);
#	### calculate R & G values on sliding scale between 0 & 28
#	$r=255;
#	$g=255;
#	
#	echo $result['confirm'].'--'.$age.chr(10);
#	if ($age < 14){ 
#		$r=round(($age*18.21));
#		$g=255;
#	}
#	if ($age > 13) {
#		$g=round($g-(($age-15)*18.21));
#		$r = 255;
#	}
#	if ($r>255) $r=255;
#	if ($g>255) $g=255;
#	if ($r<0) $r=0;
#	if ($g<0) $g=0;
#	echo ' bgcolor=rgb('.$r.','.$g.',0)';



	echo '>';
	echo '<div id="'.$codeno.'"></div>';
	echo ' <a href="accession.php?codeno='.$codeno.'&tab=pest" target="_blank">';
	echo trim($result['latin_name']).'</a> ';

	### TASK STATUS ICONS
	### If active tasks, place icon
	$sql = 'select recno from tasks,taskstatus where tasks.codeno='.$codeno.' and tasks.status=taskstatus.status';
	$sql .= ' and taskstatus.priority < 5';
	$sth = $db->prepare($sql);
	$sth->execute();
	if ($sth->fetchColumn()) echo ' <img src="http://florawww.eeb.uconn.edu/images/clipboard-20px.png"></img> ';

	### RED LINK if over inventory interval days
	$daysago = floor((time()-MySQLtoTimestamp($result['confirm']))/86400);
	if ($daysago == 0) {
		echo '<b>'.$codeno.'</b>';
	} elseif ($daysago < $inventory_interval) {
		echo '<a href="zoneconfirmmini.php?codeno='.$codeno;
		echo '&zone='.$zone.'"><font color="green">'.$codeno.'</font></a>';
	} else {
		echo '<a href="zoneconfirmmini.php?codeno='.$codeno;
		echo '&zone='.$zone.'"><b><font color="RED">'.$codeno.'</font></b></a>';
	}

#	### SIGN STATUS ICONS
#	### If active signs out, place icon
#	if ($result['signage']) {
#		echo ' <a href="binary_toggle.php?codeno='.$codeno.'&field=signage&url='.$url.'&codeno='.$codeno.'"><img src="http://florawww.eeb.uconn.edu/images/icons/signs-20_ON.png"></img></a> ';
#		} else {
#		echo ' <a href="binary_toggle.php?codeno='.$codeno.'&field=signage&url='.$url.'&codeno='.$codeno.'"><img src="http://florawww.eeb.uconn.edu/images/icons/signs-20_OFF.png"></img></a> ';
#	} #signage
#	echo '  ';

#	### PEST STATUS ICONS
#	### Pest older than 13 day and less than inventory interval
#	$sql = 'select recno from history where history.codeno='.$codeno.' and history.class="SCOUT"';
#	$sql .= ' and history.date < date_sub(curdate(),interval 13 day)';
#	$sql .= ' and history.date > date_sub(curdate(),interval '.$inventory_interval.' day)';
#	$sth = $db->prepare($sql);
#	$sth->execute();
#	if ($sth->fetchColumn()) echo ' <img src="http://florawww.eeb.uconn.edu/images/bug20.png"></img> ';
#	### Check 14 day biocontrol scouting
#	$sql = 'select recno from history where history.codeno='.$codeno.' and history.class="BIOCONTROL"';
#	$sql .= ' and history.date < date_sub(curdate(),interval 13 day)';
#	$sql .= ' and history.date > date_sub(curdate(),interval '.$inventory_interval.' day)';
#	$sth = $db->prepare($sql);
#	$sth->execute();
#	if ($sth->fetchColumn()) echo ' <img src="http://florawww.eeb.uconn.edu/images/ladybug.png"></img> ';

##### END PEST STATUS ICONS #####

### Current status
	echo ' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=currently&url='.$url.'">';
	if (($result['currently']=="going dormant") or ($result['currently']=="breaking dormancy") or ($result['currently']=="active - pending bloom")) {
		echo '<font color="RED"><b>'.$result['currently'].'</b></font></a> ';
		} else {
		echo $result['currently'].'</a> ';
	}

### Check flowering status - disable and color button if recently checked
##### Check if already noted for the current week
	$sql = 'select recno from history where codeno='.$codeno.' and class="FLOWERING"';
	$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
	$sql .= ' and week(history.date,3)=week(curdate(),3)';
	$sth = $db->prepare($sql);
	$sth->execute();
	if ($sth->fetchColumn()) {
		echo '<b>Flowering</b>';
		} else {
		echo '<a href="zonehistmini.php?codeno='.$codeno.'&data=&class=FLOWERING&zone='.$zone.'&url='.$url.'#'.$codeno.'">';
		$sql = 'select recno from history where codeno='.$codeno.' and class="FLOWERING"';
		$sql = $sql.' and history.date>date_sub(curdate(),interval 350 day)';
		$sql = $sql.' and week(history.date,3)=(week(curdate(),3)-1)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->fetchColumn()) {
			echo '<font color="red"><b>Flowering</b></font>';
		} else {
			echo 'Flowering';
		} 
		echo '</a>';
	}

####################################
# Poll pest status for past 360 days
####################################
	$sql = 'set sql_mode=""'; # disable ;only_full_group_by
	$sth = $db->prepare($sql);
	$sth->execute();
	echo '<p>';
	$sql = 'select notes,class,max(unix_timestamp(date)) as lastdate from history where codeno='.$codeno.' and (class="SCOUT" or class="BIOCONTROL")';
	$sql .= ' and history.date>date_sub(curdate(),interval 35 day)';
	$sql .= ' group by notes'; #  order by class desc,notes
	foreach($db->query($sql) as $result2) {
		$lastdate = $result2['lastdate'];
		$daysago=intval((time()-$lastdate)/86400);
		if ($daysago <14 ) {
			echo '<b>'.$result2['notes'].'</b><sup>'.$daysago.'</sup>  <br>';
		} elseif ($daysago > 13 and $daysago < 29) {
			echo '<a href="zonehistmini.php?codeno='.$codeno.'&data=';
			echo $result2['notes'].'&class=';
			echo $result2['class'].'&zone='.$zone.'&url='.$url.'"><font color="RED"><b>';
			echo $result2['notes'].'</b></font></a><sup>'.$daysago.'</sup>  ';
		} #if
	} # foreach

###############################################
### Add blank space on right for scrolling use
##############################################
	echo '<p></td><td valign="top"';
	if($i&1) {
		echo ' bgcolor=#FFE6CC';
		} else {
		echo ' bgcolor=#ECFFEC';
	}

	echo '>';

### List Bench locations
	echo $result['location'].' (x'.$result['quant'].')<br>';
	echo $result['location2'].' (x'.$result['quant2'].')<br>';
	echo '</tr>'.chr(10);
	$i++; # color toggle
} #foreach

### Quick confirmation count
$sql='select codeno from gh_inv where confirm=curdate()';
$confirmations = 0;
foreach($db->query($sql) as $result) {
	$confirmations++;
} # foreach

echo '<tr><td><b>Total Confirmations Today: '.$confirmations.'</b>';

echo '<td><a href="scoutbyzonemini.php?zone='.($zone+1).'">NEXT<br>ZONE</a></td></tr>';
echo '</table>';
$db = null;

include ("/var/www/bcm/footer.php");

function MySQLtoTimestamp($mysqlDate) {
    if (strlen($mysqlDate) > 10) {
        list($year, $month, $day_time) = explode('-', $mysqlDate);
        list($day, $time) = explode(" ", $day_time);
        list($hour, $minute, $second) = explode(":", $time);
        $ts = mktime($hour, $minute, $second, $month, $day, $year);
    } else {
        list($year, $month, $day) = explode('-', $mysqlDate);
        $ts = mktime(0, 0, 0, $month, $day, $year);
    }
    return $ts;
}

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

</font>
</body>
</html> 


