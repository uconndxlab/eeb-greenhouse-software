<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Keyword Cloud</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$terms = array(); // create empty array for parsed terms
$cterms =array(); // array for term/count pairs
$sql = 'select gh_inv.codeno,gh_inv.keywords from gh_inv where projnum not like "%DELETE%"';

foreach($db->query($sql) as $row) {
	#collect keyword strings from database
	$keywords = explode(" ",$row['keywords']);
	$keycount = count($keywords);
	for ($j=0;$j<$keycount;$j++){
		$keyword = trim(strtolower($keywords[$j]));
		if (strlen($keyword)>0) $terms[]=$keyword;	
	}
} #foreach

asort($terms); // sorted full listing of terms including duplicates
$count = array_count_values($terms); // array of term counts, key=term
$index = range(0,count($count)-1,1); // sequential array of numbers to use as keys
$uterms = array_unique($terms); // array of unique terms
$count = array_combine($index,$count);
$uterms = array_combine($index,$uterms);
$terms = array(); // reset initial array for repopulation
for ($j=0;$j<count($index);$j++) {
	$terms[] = array('term'=> $uterms[$j], 'count'=> $count[$j]);	
}

### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src = "/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>';
echo '<p>';
echo '<h3>Current Keywords in Database</h3><ul>';  
foreach ($terms as $k) {
	echo '<li><a href="keyword.search.php?keyword='.urlencode($k['term']).'">'.$k['term'].'</a> - '.$k['count'].' accessions';
} #foreach

echo '</ul>';
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
$db = null;
?> 
</body>
</html> 
