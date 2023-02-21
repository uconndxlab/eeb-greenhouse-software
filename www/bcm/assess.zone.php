<html>
<head>

<meta name="viewport" content="width=device-width" />

<?php
include 'credentials.php';
include 'evaluate.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$zone = $_GET['zone'];
$currweek = (int)date('W');

echo '<title>Report for Week# '.date("W").', Zone# '.$_POST['zone'].'</title></head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>'.chr(10);
echo '</form><p>'.chr(10);

### Evaluate all accession for zone
$sql = 'select codeno,latin_name,coll_rank from gh_inv where location='.$zone.' and projnum="GEN_COLL" order by coll_rank';

foreach($db->query($sql) as $row) {
	#echo 're-evaluating...'.$row['codeno'].' - '.$row['latin_name'].'<br>';
	$codeno = $row['codeno'];
	$result=evaluate($codeno);
}
echo '<hr>';

echo '<a href="assess.zone.php?zone='.($zone-1).'">PREV ZONE</a>';
echo ' | <a href="assess.inventory.php">ZONE LIST</a>';
echo ' | <a href="assess.zone.php?zone='.($zone+1).'">NEXT ZONE</a>';

echo '<table border width=100%>';

$sql = 'select codeno,latin_name,location,location2,location3,confirm,quant,quant2,quant3,eval_hyp_incl,space_req,importance,coll_rank,eval_rank,space_acc,e_criteria,p3_zone,p3_tdwg,classify.family,tblLevel2.l2region from gh_inv,classify,tblLevel2 where ';
$sql .= ' gh_inv.genus=classify.genus and ';
$sql .= ' gh_inv.p3_tdwg=tblLevel2.l2code and ';
### check zone vs bench
#if (($zone%100) == 0) {
#	$sql=$sql.'location>='.$zone.' and location <='.($zone+99);
#	} else {
	$sql .= 'location='.$zone;
#}
$sql .= ' order by coll_rank';
foreach($db->query($sql) as $row) {
	$codeno = $row['codeno'];
	$include = $row['eval_hyp_incl'];
	echo '<tr><td';
	if($include) {
		echo ' bgcolor=#ECFFEC';
		} else {
		echo ' bgcolor=#FFE6CC';
	}

	echo '>';
	echo '<a name="'.$codeno.'">';
	echo ' <a href="accession.php?codeno='.$codeno.'&tab=assess" target="_blank">';
	echo trim($row['latin_name']).'</a> ';
	### BOLD LINK if over inventory interval days
	$daysago = floor((time()-MySQLtoTimestamp($row['confirm']))/86400);
	if ($daysago < $inventory_interval) {
	echo '<font color="green">'.$codeno.'</font>';
	} else {
	echo '<font color="RED">'.$codeno.'</font>';
	}
	### Include Family & Link
#	echo ' <a href="http://florawww.eeb.uconn.edu/bcm/family_list.php?family='.$row['family'].'" target="_blank">'.$row['family'].'</a>';
	echo ' '.$row['family'];
	echo '<br>'.chr(10);
	### Include Collection Location Data
	echo '<b>Zone:</b> <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=p3_zone&url=http://florawww.eeb.uconn.edu/bcm/assess.zone.php?zone='.$zone.'">';
	echo $row['p3_zone'].'</a>';
	echo ' <b>Region:</b> '.$row['p3_tdwg'].':'.$row['l2region'].'<br>';
	### Include evaluation criteria
	echo '<b>Rank:</b> '.$row['coll_rank'];
	echo ' <b>Importance:</b> ';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=importance&url=http://florawww.eeb.uconn.edu/bcm/assess.zone.php?zone='.$zone.'">';
	echo $row['importance'].'</a><br>';

	echo '<b>Include:</b> ';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=eval_hyp_incl&url=http://florawww.eeb.uconn.edu/bcm/assess.zone.php?zone='.$zone.'">';
	echo $row['eval_hyp_incl'].'</a><br>';
	echo '<b>Threshold:</b> '.(floor($row['space_acc']));
	echo ' <b>Footprint:</b> <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno;
	echo '&v=space_req&url=http://florawww.eeb.uconn.edu/bcm/assess.zone.php?zone='.$zone.'">'.$row['space_req'].'</a><br>';

	echo '<b>Scoring: </b>'.$row['eval_rank'].'';
	echo $row['e_criteria'];
	echo '';

##############################################
### Add blank space on right for scrolling use
##############################################
	echo '<p></td><td valign="top"';
	if($include) {
		echo ' bgcolor=#ECFFEC';
		} else {
		echo ' bgcolor=#FFE6CC';
	}

echo '>';
### List Bench locations
echo $row['location'].'(x'.$row['quant'].')<br>';
echo $row['location2'].'(x'.$row['quant2'].')<br>';
echo $row['location3'].'(x'.$row['quant3'].')<br>';

echo '</tr>'.chr(10);
$i++;
}

#### Location Density Calculation
#$i=0; #reset $i
#$q=0;
#$s=0;
#while ($i<$num) {
#	if (mysql_result($sql_result,$i,quant)<>0) {
#		$q=$q+mysql_result($sql_result,$i,quant);
#		$s=$s+(mysql_result($sql_result,$i,quant) * mysql_result($sql_result,$i,space_req));
#		}
#	if (mysql_result($sql_result,$i,quant2)<>0) {
#		$q=$q+mysql_result($sql_result,$i,quant2);
#		$s=$s+(mysql_result($sql_result,$i,quant2) * mysql_result($sql_result,$i,space_req));
#		}
#	if (mysql_result($sql_result,$i,quant3)<>0) {
#		$q=$q+mysql_result($sql_result,$i,quant3);
#		$s=$s+(mysql_result($sql_result,$i,quant3) * mysql_result($sql_result,$i,space_req));
#		}
#	$i++;
#	}
#echo ' '.$q.' plants, '.$s.' nsf utilized, ('.$benchsize.' sf bench)';
#echo ' <a href="inv2b.php?zone='.$zone.'&daysago=-1" target="_blank">{EDIT}';
echo '</table>';

echo '<hr>';
echo '<a href="assess.zone.php?zone='.($zone-1).'">PREV ZONE</a>';
echo ' | <a href="assess.inventory.php">ZONE LIST</a>';
echo ' | <a href="assess.zone.php?zone='.($zone+1).'">NEXT ZONE</a>';
echo '<hr>';

$db = null;
echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

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
?> 

</font>
</body>
</html> 


