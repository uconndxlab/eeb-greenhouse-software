<?php
function v4_glossary_scan($subjstr)
{
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}	

### collect list of current glossary terms.
$count = 1; #only replace first instance
$sql = 'select term,def from glossary';
foreach($db->query($sql) as $row) {
	$searchstr = $row['term'];
	if (preg_match('/\b'.ucfirst($searchstr).'\b/',$subjstr)) {
			### build replacement string if uppercase
			$replstr = '<dfn><abbr title="'.$row['def'].'">'.ucfirst($searchstr).'</abbr></dfn>';
			$subjstr = preg_replace('/\b'.ucfirst($searchstr).'\b/',$replstr,$subjstr,$count);
	} elseif (preg_match('/\b'.$searchstr.'\b/',$subjstr)) {
			### build replacement string if lowercase
			$replstr = '<dfn><abbr title="'.$row['def'].'">'.$searchstr.'</abbr></dfn>';
			$subjstr = preg_replace('/\b'.$searchstr.'\b/',$replstr,$subjstr,$count);
	} # if stripos
} #foreach
### CLOSE OUTPUT FILE
$db=null; ### close PDO object
return $subjstr;
} #end v4_glossary_scan definition
?>
