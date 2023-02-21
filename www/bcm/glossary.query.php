<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Glossary Query Output</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

$instring = $_GET["term"];

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Set sql_mode='' to turn off explicit checking - temporary??
$sql = 'set sql_mode=""';
$sth = $db->prepare($sql);
$sth->execute();

### Create Search Box
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>';

### Form SQL statement
$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.author,gh_inv.location,gh_inv.location2,gh_inv.location3,classify.family,';
$sql .= 'gh_inv.projnum, gh_inv.source,gh_inv.poisonous,gh_inv.keywords';
$sql .= ' from gh_inv,classify where classify.genus=gh_inv.genus and';
$sql .= ' (gh_inv.descrip like "%'.$instring.'%" or gh_inv.usedfor like "%'.$instring.'%")';
$sql .= ' order by projnum, latin_name';
$result = $db->query($sql);
$rowcount = $result->rowCount();

echo '<b><font color="green">'.$rowcount.' Results found for '.$searchtype.'</font>'.$instring.'</b><font color="green"><b> in Description or Usage fields</b></font><p>';
$deleted = 0;
foreach($db->query($sql) as $row) {
	### skip entries with 'DELETE' in project number, but tally total for end
	if (substr($row['projnum'],0,6) == "DELETE") {
		$deleted++;
	} else {
		$out = '';
		$out .= '<b>['.substr($row['family'],0,5).']</b> ';
		$out .= $row['location'];
		$out .= ' <a href="http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$row['codeno'];
		if (substr($searchtype,0,7) == 'Keyword') $out .= '&tab=misc';
		$out .= '" target="_blank">';
		$out .= $row['latin_name'].' <i>'.$row['author'].'</i></a> - ';
		$out .= $row['codeno'].' : ';
		if ($row['projnum']<>"GEN_COLL"){
			$out = $out.'<font color="red">'.$row['projnum'].': '.$row['source'].'</font>';
		} else {
			$out .= $row['projnum'];
		}


		$out .= '<p>'.chr(10);
		echo $out;
	} # if deleted
} #foreach

if ($deleted > 0) echo $deleted.' deleted accessions supressed from results list';

$db = null;
echo '<p>Return to <a href="glossary.cloud.php">Glossary</a>';
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

?> 
</font>
</body>
</html> 
