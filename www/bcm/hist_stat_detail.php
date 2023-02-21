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

$daysago = $_GET['daysago'];
$class = $_GET['class'];
$num_days = $_GET['numdays'];
#$recno = $_GET['recno'];
$eventtally = 0;
$valuetally = 0;

# Generate Title ################################################
echo '<title>History Report for '.$class.' - '.$daysago.' days ago</title>';
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

echo '<b>History Detail<br>Class = '.$class;
echo '</b>';
#<br><i>add &recno=1 to include table record numbers</i>
echo '<hr><ul>';

$sql = 'select history.zone,history.date,history.class,history.notes,gh_inv.latin_name,history.codeno,history.value,gh_inv.author,history.recno from history,gh_inv';
$sql .= ' where history.codeno=gh_inv.codeno and history.class="'.$class.'"';
$sql .= ' and history.date<=date_sub(curdate(),interval '.$daysago.' day) and history.date>=date_sub(curdate(),interval '.($daysago+$num_days).' day)';
$sql .= ' order by history.date,history.zone,history.recno';

foreach($db->query($sql) as $row) {
	echo '<li>';
	echo date("m-d-Y",strtotime($row['date'])).' - ';
	if ($row['zone']<>"9999"){
		echo $row['zone'].' - ';
	}
#	if ($recno) echo $row['recno'].' - ';
	if ($row['codeno']<>"111111111"){	
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$row['codeno'].'" TARGET="_blank">'.$row['latin_name'].'</a> - ';
	}
	echo $row['notes'];
	echo' <sup>R#'.$row['recno'].'</sup>';
	if ($row['value']>"0") echo ' ['.$row['value'].']';
	$eventtally++;
	$valuetally = $valuetally + $row['value'];

} #foreach
echo '</ul>';

echo '<hr>Total Items: '.$eventtally;
echo '<br>Value Sum: '.$valuetally;


$db = null;
echo '<p><a href="admin.php">Admin Page</a>';
?> 

</font>
</body>
</html> 
