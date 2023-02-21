<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name="viewport" content="width=device-width" >
<meta http-equiv="refresh" content="60">
<title>EEB/BCM: Inventory Status Map</title>

<?php
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}

echo '</head><body>';
### Create Clickable Inventory Status Map

echo '<map name="inv_status">'.chr(10);

### Global Variable Declarations
$offset_x=10; 
$offset_y=150;

echo '<area shape="rect" coords="0,0,1500,'.$offset_y.'" href="http://florawww.eeb.uconn.edu/bcm/query.php" alt="Query Page">'.chr(10);
echo '<area shape="rect" coords="0,1860,1500,2000" href="http://florawww.eeb.uconn.edu/bcm/admin.php" alt="Admin Page">'.chr(10);

$sql = 'select b_assign.location,b_assign.map_orig_x,b_assign.map_orig_y,b_assign.map_len_x,b_assign.map_len_y,';
$sql .= 'round(avg(to_days(gh_inv.confirm))) as d1, to_days(now()) as d2, count(gh_inv.codeno) as d3';
$sql .= ' from b_assign,gh_inv '; 
$sql .= 'where gh_inv.projnum="GEN_COLL" and gh_inv.location=b_assign.location ';
$sql .= ' and b_assign.map_include'; 
$sql .= ' group by b_assign.location order by b_assign.location';

foreach($db->query($sql) as $result) {
	#$avg_age=($result['d2']-$result['d1']);
	echo '<area shape="rect" coords="';
	echo ($result['map_orig_x']+$offset_x).',';
	echo ($result['map_orig_y']+$offset_y).',';
	echo ($result['map_orig_x']+$offset_x+$result['map_len_x']).',';
	echo ($result['map_orig_y']+$offset_y+$result['map_len_y']).'" ';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/scoutbyzonemini.php?zone=';
	echo $result['location'].'" alt="Bench '.$result['location'].'">'.chr(10);
} # foreach
echo '</map>';
echo '<img usemap="#inv_status" src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png"></img>';
$db = null;
?>

</font>
</body>
</html> 
