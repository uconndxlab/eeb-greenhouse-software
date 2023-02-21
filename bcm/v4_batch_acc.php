<?php
### Ver 4 - add mysql-PDO compatibility

include 'v4_acc_page_generate.php';
include 'v4_mapmaker.php';
#include '/var/www/bcm/evaluate.php';
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10);	
}
$sql='select codeno,tdwg from gh_inv where tempflag and projnum="GEN_COLL" order by codeno';

foreach($db->query($sql) as $row) {
	$result=v4_acc_page_generate($row['codeno']);	
	### Check for distribution map & TDWG data - update map if more than 3 months old
	if ($row['tdwg']>"") {
		$mapname=$imagedir.'maps/tdwg/'.$row['codeno'].'.png';
		if (file_exists($mapname)) {
			### recreate map if more than 2 months old
			# echo $mapname.' is '.round(((time()-filemtime($mapname))/86400)).' days old'.chr(10);
			if (round(((time()-filemtime($mapname))/86400))>60) $result=v4_mapmaker($row['codeno']);
		} else {
			### create map if does not exist
			$result=v4_mapmaker($row['codeno']);
		} ### existing map check
	} ### distribution map check
} # foreach

?>
