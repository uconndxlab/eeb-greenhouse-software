<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>gh_inv Query Output</title>
</head>
<body>

<?php

include '/var/www/bcm/credentials.php';

$instring = $_POST["instring"];

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

### Jump direct if 9 digit numeric is entered in partial text search field 
if ((is_numeric($instring)) and (strlen($instring)==9)) {
	### check for valid codeno
	$sql = 'select codeno from gh_inv where codeno='.$instring;
	$sth = $db->prepare($sql);
	$sth->execute();
	if ($sth->fetchColumn()) echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$instring.'">';	
	}

### Create Search Box
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>';

### Form SQL statement
$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.author,gh_inv.location,gh_inv.location2,gh_inv.location3,classify.family,gh_inv.projnum, gh_inv.source,gh_inv.poisonous,gh_inv.keywords';

### ADD FIELDS FOR CUSTOM DATA ON LINE
$sql .= ',gh_inv.bgci_exsitusites,bgci_status';

$sql .= ' from gh_inv,classify where classify.genus=gh_inv.genus and';
$searchtype = '';
if (substr($instring,-4)=='ceae') {
		### Check if family name
		$searchtype = 'Family = ';
		$sql .= ' classify.family="'.$instring.'"';
	} elseif ((strtoupper($instring)==$instring) and (strlen($instring)==3)) {
		### check for 3 digit uppercase - ie TDWG code
		$searchtype = 'TDWG Code = ';
		$sql .= ' gh_inv.tdwg like "%'.$instring.'%"';
	} elseif ((is_numeric($instring)) and (strlen($instring)==4)) {
		### check for 4 digit numeric - ie bench location
		$searchtype = 'Bench Location = ';
		$sql .= ' (gh_inv.location='.$instring.' or gh_inv.location2='.$instring.')';
	} elseif (substr($instring,0,1) == '#') {
		### check for # - keyword search
		$searchtype = 'Keyword = ';
		$instring = substr($instring,1);
		$sql .= ' gh_inv.keywords like "%'.$instring.'%"';
	} else {
		### check latin name and synonomy for partial string matches
		$sql .= ' (gh_inv.latin_name like "%'.$instring.'%"';
		$sql .= ' or gh_inv.synonomy like "%'.$instring.'%")';
	}
$sql .= ' order by projnum, latin_name';
#echo $sql.'<hr>';
#echo 'Search String = '.$instring.'<br>';
#echo 'String Length = '.strlen($instring).'<hr>';

$result = $db->query($sql);
$rowcount = $result->rowCount();

echo '<b><font color="green">'.$rowcount.' Results found for '.$searchtype.'</font>'.$instring.'</b><p>';
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

######### CUSTOM (TEMPORARY) DATA SECTION
		echo $out;
		$out = ' [BGCI:'.$row['bgci_exsitusites'].', '.$row['bgci_status'].']';

######### END OF CUSTOM (TEMPORARY) DATA SECTION


		$out .= '<p>'.chr(10);
		echo $out;
	} # if deleted
} #foreach

if ($deleted > 0) echo $deleted.' deleted accessions supressed from results list';

$db = null;
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

?> 
</font>
</body>
</html> 
