<?php
include_once 'v4_keyword_map.php';
include_once 'v4_keyword_maplist.php';
include_once 'v4_keyword_generate.php';

### Manual Keyword Page updater - set SQL to match
include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'select * from keywords where keyword="gsci1000e"';
foreach($db->query($sql) as $row) {
	echo 'Generating Locator Map Image for '.$row['keyword'].chr(10);
	$result=v4_keyword_map($row['keyword'],$row['title']);
	$result=v4_keyword_maplist($row['keyword'],$row['title']);
	$result=v4_keyword_generate($row['keyword']);
	echo $row['title'].' generated -> '.$row['keyword'].'.html<br>'.chr(10);
} # foreach


?>
