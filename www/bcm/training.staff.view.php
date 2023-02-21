<html>
<head>

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

<meta name="viewport" content="width=device-width" />

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
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>'.chr(10);
echo '</form><p>'.chr(10);

echo '<h2>EEB Greenhouse Staff Training Chart';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/training.staff.view.info.html" target="_blank"><img src = "/images/icons/question-20.png" title="Open Detailed Help Page in New Tab" /></a>';
echo '</h2>';

$sql = 'select * from users where active and staff order by labgroup,lname,fname';
echo '<ul>';
foreach($db->query($sql) as $row) {
	echo '<li><b>'.$row['lname'].', '.$row['fname'].'</b>'.' : '.$row['labgroup'];	
	echo '<sup>'.$row['recno'].'-'.$row['netid'].'</sup>';
	
	### Check WHA
	echo '<ul>';
	echo '<li>Non-Lab Workplace Hazard Assessment: ';
	if ($row['wha']==0) {
		echo '<font color="RED"><b>missing</b></font>';
		} else {
		echo '<font color="GREEN">'.$row['wha'].'</font>';
	}

	### Check HazComm
	echo '<li><a href="http://ehsapps.uconn.edu/training/schedule/OccuTrainingSchedule.php#171">Hazard Communication: Right to Understand (HuskyCT)</a>: ';
	if ($row['hazcomm']==0) {
		echo '<font color="RED"><b>missing</b></font>';
		} else {
		echo '<font color="GREEN">'.$row['hazcomm'].'</font>';
	}

	### Check WPS
	echo '<li>EPA Worker Protection Standard';
	if ($row['labgroup']=='Staff, Permanent'){
		echo ' (Handler): ';
	} else {
		echo ' (Worker): ';
	}


	if (MySQLtoTimestamp($row['wps']) < time()-30326400) echo '<font color="RED"><b>';
	echo $row['wps'];
	if (MySQLtoTimestamp($row['wps']) < time()-30326400) echo '</b></font>';

	### Check Respirator Fit Test
	if ($row['labgroup']=='Staff, Permanent'){
		echo '<li>Respiratory Protection and Fit Testing (Medical required): ';
		if (MySQLtoTimestamp($row['respirator']) < time()-31536000) echo '<font color="RED"><b>';
		echo $row['respirator'];
		if (MySQLtoTimestamp($row['respirator']) < time()-31536000) echo '</b></font>';
	}

	### Check Fall Protection
	
	if ($row['labgroup']=='Staff, Permanent'){
		echo '<li>Fall Protection 2: ';
	} else {
		echo '<li><a href="http://ehsapps.uconn.edu/training/schedule/OccuTrainingSchedule.php#206">Fall Protection 1 (Husky CT)</a>: ';
	}
	if ($row['fallprot']==0) {
		echo '<font color="RED"><b>missing</b></font>';
		} else {
		echo '<font color="GREEN">'.$row['fallprot'].'</font>';
	}

	### Check Voluntary Use of Dust Masks
	echo '<li><a href="http://ehsapps.uconn.edu/training/schedule/OccuTrainingSchedule.php#161">Respiratory Protection - Voluntary Use of Dust Masks (HuskyCT)</a>: ';
	if ($row['dustmask']==0) {
		echo '<font color="RED"><b>missing</b></font>';
		} else {
		echo '<font color="GREEN">'.$row['dustmask'].'</font>';
	}

	### Check PPE
	echo '<li><a href="http://ehsapps.uconn.edu/training/schedule/OccuTrainingSchedule.php#170">Personal Protective Equipment (HuskyCT)</a>: ';
	if ($row['ppe']==0) {
		echo '<font color="RED"><b>missing</b></font>';
		} else {
		echo '<font color="GREEN">'.$row['ppe'].'</font>';
	}

	### Check Asbestos Awareness
	if ($row['labgroup']=='Staff, Permanent'){
		echo '<li>Asbestos Awareness Refresher (HuskyCT): ';
		if (MySQLtoTimestamp($row['asbestos']) < time()-31536000) echo '<font color="RED"><b>';
		echo $row['asbestos'];
		if (MySQLtoTimestamp($row['asbestos']) < time()-31536000) echo '</b></font>';
	}

	echo '</ul>';
} #foreach
echo '</ul>';

echo '<a href="admin.php">Admin Page</a>';
$db = null;

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


