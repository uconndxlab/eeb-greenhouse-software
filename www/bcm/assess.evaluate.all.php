<?php
### This routine, although sitting in www/bcm/, is generally run from command line or automatically from crontab
### running from root crontab every 30 minutes as of 02JAN2020

include 'credentials.php';
include 'evaluate.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Evaluate all accession for zone
$sql = 'select codeno,latin_name,coll_rank from gh_inv where projnum="GEN_COLL" order by coll_rank';

foreach($db->query($sql) as $row) {
	echo 'evaluating..'.$row['coll_rank'].': '.$row['codeno'].'-'.$row['latin_name'].chr(10);
	$codeno = $row['codeno'];
	$result=evaluate($codeno);
}

echo 'Evaluation Complete';

$db = null;
?> 

