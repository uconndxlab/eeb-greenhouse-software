<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Glossary Listing</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
    echo $ex->getMessage(); # Explicit Error Message	
}

### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src = "/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>'.chr(10);
echo '<p>';
echo '<h3>Current Botanical & Medicinal Terms in Glossary</h3>';
echo '<ul>';  
$sql = 'select term,def from glossary order by term';
$divid = '';
foreach($db->query($sql) as $row) {
	if (substr($row['term'],0,1)<>$divid) {
		$divid = substr($row['term'],0,1);
		### Place page jumps here
		echo '<p>Jump To: ';
		echo '<a href="#A">A</a>-';
		echo '<a href="#B">B</a>-';
		echo '<a href="#C">C</a>-';
		echo '<a href="#D">D</a>-';
		echo '<a href="#E">E</a>-';
		echo '<a href="#F">F</a>-';
		echo '<a href="#G">G</a>-';
		echo '<a href="#H">H</a>-';
		echo '<a href="#I">I</a>-';
		echo '<a href="#J">J</a>-';
		echo '<a href="#K">K</a>-';
		echo '<a href="#L">L</a>-';
		echo '<a href="#M">M</a>-';
		echo '<a href="#N">N</a>-';
		echo '<a href="#O">O</a>-';
		echo '<a href="#P">P</a>-';
		echo '<a href="#Q">Q</a>-';
		echo '<a href="#R">R</a>-';
		echo '<a href="#S">S</a>-';
		echo '<a href="#T">T</a>-';
		echo '<a href="#U">U</a>-';
		echo '<a href="#V">V</a>-';
		echo '<a href="#W">W</a>-';
		echo '<a href="#X">X</a>-';
		echo '<a href="#Y">Y</a>-';
		echo '<a href="#Z">Z</a><p></p>';
		echo '<div id="'.strtoupper(substr($row['term'],0,1)).'"></div>'.chr(10);
	} # divid
	echo '<li><a href="glossary.edittext.php?term='.$row['term'].'">'.$row['term'].'</a>'.chr(10);
} #foreach

echo '</ul>';
echo '<p>Glossary Terms derived from a variety of sources including:<ol>';
echo '<li><a href="https://en.wikipedia.org/wiki/Glossary_of_botanical_terms">Glossary of Botanical Terms</a> at Wikipedia.';
echo '<li><a href="https://gobotany.newenglandwild.org/glossary/a/">GoBotany Glossary</a> at New England Wildflower Society.';
echo '<li><a href="http://abc.herbalgram.org/site/PageServer?pagename=Terminology">Terminology</a> at American Botanical Council.'; 
echo '</ol>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
$db = null;
### scan glossary
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
</body>
</html> 
