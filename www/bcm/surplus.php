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

$url = urlencode(curPageURL());

echo '<title>Surplus Inventory</title>';
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

$sql = 'select codeno,latin_name,quant,quant2,location,location2,surplus,confirm,projnum';
$sql .= ' from gh_inv where surplus > 0';
$sql .= ' and projnum="GEN_COLL"';
$sql .= ' order by location,latin_name';
$sth = $db->prepare($sql);
$sth->execute();

echo '<b>'.$sth->rowCount().'</b> accessions with surplus plants<ul>';

foreach($db->query($sql) as $row) {
	echo '<li><a name="'.$row['codeno'].'">';
	echo ' <font color="green">'.$row['location'].'/'.$row['location2'].' </font>';
	echo '<a href="accession.php?codeno='.$row['codeno'].'" target="_blank"> ';
	echo $row['latin_name'].'</a>';
	echo ': <b><a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$row['codeno'].'&v=surplus&url='.$url.'#'.$row['codeno'].'">';
	echo $row['surplus'].'</a></b> Surplus Plants';
} # foreach
echo '</ul>';
$db = null;
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
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

</font>
</body>
</html> 


