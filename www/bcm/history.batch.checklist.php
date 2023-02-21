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


$start = $_POST['start'];
$end = $_POST['end'];
$note = $_POST['note'];
$class = $_POST['class'];

echo $start.' '.$end.'<hr>';
echo '<title>Batch History Checklist</title></head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

echo '<table border><tr><td colspan=3 bgcolor="#CCFF99"><b>Class: </b>'.$class.'<br><b>Entry: </b>'.$note.'<br><form method=POST name="checklist" action="http://florawww.eeb.uconn.edu/bcm/history.batch.insert.php"></tr>';
$codeno=111111111;
$count = 0;

#### Create List
$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.location from gh_inv';
$sql .= ' where gh_inv.location>='.$start.' and gh_inv.location<='.$end;
$sql .= ' order by location,latin_name';

foreach($db->query($sql) as $row) {
	$out = '<tr><td><input type=checkbox name="update[]" value="'.$row['codeno'].'" checked></input>';
	$out .= $row['location'];
	$out .= ' <font color="green">'.$row['codeno'].' </font>';

	$out .= '<td><a href="http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$row['codeno'].'" target="_blank"> ';
	$out .= $row['latin_name'].'</a>';

##### Check if already noted for the current week
#$sql = 'select * from history where codeno='.mysql_result($sql_result,$i,'codeno').' and class="FLOWERING"';
#$sql = $sql.' and history.date>date_sub(curdate(),interval 350 day)';
#$sql = $sql.' and week(history.date,3)=week(curdate(),3)';
#$sql_result3=mysql_query($sql);
#if (!$sql_result3) {
#	echo mysql_error();
#}
#$num3=mysql_numrows($sql_result3);
#if ($num3>0) $out=$out.'<img src="http://florawww.eeb.uconn.edu/images/flower-rose.gif"></img>';
##### end bloom check

	$out .= '</tr>';
	echo $out;
	$codeno = $row['codeno'];
	$count = $count+1;

} #foreach

echo '<tr><td colspan=3 align="center">';
echo '<input type="hidden" value='.$class.' name=class>';
echo '<input type="hidden" value="'.$note.'" name=note>';
echo '<input type="SUBMIT" value="Confirm Data Entry"></form></td></tr>';
echo '<tr><td colspan=3 bgcolor="#CCFF99"><b>Class: </b>'.$class.'<br><b>Entry: </b>'.$note.'</td></tr>';

echo '</table>';
echo '<p>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<p>';
echo '<a href="admin.php">Admin Page</a>';

$db = null;;



#function MySQLtoTimestamp($mysqlDate) {
#    if (strlen($mysqlDate) > 10) {
#        list($year, $month, $day_time) = explode('-', $mysqlDate);
#        list($day, $time) = explode(" ", $day_time);
#        list($hour, $minute, $second) = explode(":", $time);
#        $ts = mktime($hour, $minute, $second, $month, $day, $year);
#    } else {
#        list($year, $month, $day) = explode('-', $mysqlDate);
#        $ts = mktime(0, 0, 0, $month, $day, $year);
#    }
#    return $ts;
#}

?> 
</font>
</body>
</html> 


