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

$days =  $_GET['days'];

# Generate Title ################################################
echo '<title>Database History Chart</title>';
echo '</head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

# Set up Table
echo '<table bgcolor="#CC6699" width=400px>';

echo '<tr><th bgcolor="#996699" align="left">Class';
echo '<th>'.date("D").'<br>Today';
# This loops for yesterday to $daysago, note today listed above
for ($i=1; $i<$days; $i++) {
	echo '<th>'.date("D",time()-($i*86400)).'<br>';
	echo date("n/j",time()-($i*86400));
}
echo '</tr>';

### Fetch Unique Classes
$sql = 'select distinct class as classname from history order by class';
foreach($db->query($sql) as $row) {
	echo '<tr><td>'.$row['classname'];
	# Count instances for class & date
	for ($j=0; $j<$days; $j++) {
		$sql = 'select codeno from history where history.class="'.$row['classname'].'"';
		$sql .= ' and history.date=date_sub(curdate(),interval '.$j.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();	
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) {
			echo '<a href="http://florawww.eeb.uconn.edu/bcm/hist_stat_detail.php?class='.trim($row['classname']);
			echo '&daysago='.$j.'&numdays=0">'.$result.'</a>';
#			echo $result;
		}
	}
	echo '</tr>';
} # foreach

### Calculate New Additions
	echo '<tr><td colspan=16></td></tr><tr><td>NEW PLANTS';
	for ($j=0; $j<$days; $j++) {
		$sql = 'select codeno from gh_inv where gh_inv.acc_date=date_sub(curdate(),interval '.$j.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) {
#			echo '<center><form action=http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php method="post">';
#			echo '<input type="hidden" name="instring" value="'.date("Ymd",time()-($j*86400)).'">';
#			echo '<input type="hidden" name="qtype" value="acc_date">';
#			echo '<input type="submit" value="'.$result.'">';
#			echo '</form></center>';
#			#echo date("Ymd",time()-($j*86400));
			echo $result;
		}
	} # foreach
	echo '</tr>';

### Calculate confirmations
	echo '<tr><td>VERIFIED PLANTS';
	for ($j=0; $j<$days; $j++) {
		$sql = 'select codeno from gh_inv where gh_inv.confirm=date_sub(curdate(),interval '.$j.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) {
#			echo '<center><form action=http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php method="post">';
#			echo '<input type="hidden" name="instring" value="'.date("Ymd",time()-($j*86400)).'">';
#			echo '<input type="hidden" name="qtype" value="confirm">';
#			echo '<input type="submit" value="'.$result.'">';
#			echo '</form></center>';
			echo $result;
		}
	} # foreach

	echo '</tr>';

### Calculate culls and deletions
	echo '<tr><td>CULLS';
	for ($j=0; $j<$days; $j++) {
		$sql = 'select codeno from history where history.date=date_sub(curdate(),interval '.$j.' day)';
		$sql .= ' and (history.notes like "%culled%" or history.notes like "%deceased%")';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) echo $result;
	} # foreach
	echo '</tr>';
### Generate NEW Task Listings
	echo '<tr><td colspan=16></td></tr><tr><td>TASKS:NEW';
	for ($j=0; $j<$days; $j++) {
		$sql = 'select recno,dcreate from tasks where tasks.dcreate=date_sub(curdate(),interval '.$j.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) {
#			echo '<a href="http://florawww.eeb.uconn.edu/bcm/tmgr.reviewtask.php?dcreate=';
#			echo mysql_result($sql_result,0,1).'&dcomplete=ALL&status=ALL&assign=ALL&location=ALL">'.$result.'</a>';
		echo $result;
		}
	} # foreach

	echo '</tr>';
### Generate COMPLETE Task Listings
	echo '<tr><td>TASKS:COMPLETE';
	for ($j=0; $j<$days; $j++) {
		$sql = 'select recno,dcomplete from tasks where tasks.dcomplete=date_sub(curdate(),interval '.$j.' day)';
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->rowCount();
		echo '<td align="center"';
		if ((date("D",time()-($j*86400))=='Sat') or (date("D",time()-($j*86400))=='Sun')) {
			echo ' bgcolor="#FFCC66"';
		}
		else {
			echo ' bgcolor="#CCFFCC"';
		}
		echo '>';
		if ($result<>0) {
#			echo '<a href="http://florawww.eeb.uconn.edu/bcm/tmgr.reviewtask.php?dcomplete=';
#			echo mysql_result($sql_result,0,1).'&dcreate=ALL&status=ALL&assign=ALL&location=ALL">'.$result.'</a>';
			echo $result;
		}
	} # foreach

	echo '</tr>';
echo '</table>';
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
$db = null;
?> 

</body>
</html> 
