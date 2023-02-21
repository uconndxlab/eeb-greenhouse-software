<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Keyword Editor</title>
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

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

$keyword = $_GET["keyword"];
$url = 'keyword.search.php?keyword='.$keyword;
$sql = 'select * from keywords where keyword=:keyword';
$sth = $db->prepare($sql);
$sth->bindParam(':keyword',$keyword);
$sth->execute();
if ($sth->fetchColumn()) {
	# do nothing if record exists
} else {
	### create blank entry
#	$sql = 'insert into keywords (keyword,title,text,page_gen) values ("'.$keyword.'","","",1)';
#	$sth = $db->prepare($sql);
#	$sth->execute();
#	echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
} #if keyword exists

$sth->closeCursor();
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<hr><form action="keyword.update.php" method="post">';
echo 'Keyword:<input type="text" size=80 name="keyword" value="'.$result['keyword'].'"><br>';
echo 'Title Text:<input type="text" size=80 name="title" value="'.$result['title'].'"><br>';
echo 'Page Text:<textarea name="text" cols=80 rows=20>'.$result['text'].'</textarea><br>';

echo '<br>Hint: Use HTML tags for formatting';
echo '<input type="hidden" name="url" value="'.$url.'">';
echo '<p><input type="submit" name="submit" value="Update Record"></form>';

echo '<p><a href="'.$weburl.'keyword_'.$keyword.'.html">View Webpage</a>';
echo '<hr>';

$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.author,gh_inv.location,gh_inv.projnum, gh_inv.source';
$sql .= ' from gh_inv where substr(projnum,1,6)<>"DELETE" and';
$sql .= ' keywords like "% '.$keyword.' %" order by location';

echo 'Accessions containing the keyword <b>'.$keyword.'</b>:<p><ul>';
$deleted=0;
foreach($db->query($sql) as $row) {
	### skip entries with 'DELETE' in project number, but tally total for end
	if (substr($row['projnum'],0,6) == "DELETE") {
		$deleted++;
	} else {
	$out = '<li>';
	$out .= str_pad((int) strval($row['location']),4,"0",STR_PAD_LEFT).' - ';
	$out .= '<a href="http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$row['codeno'].'&tab=misc"  TARGET="_blank">';
	$out .= $row['latin_name'].' <i>'.$row['author'].'</i></a> - ';
	$out .= $row['codeno'].' : ';
	if ($row['projnum']<>"GEN_COLL"){
		$out .= '<font color="red">'.$row['projnum'].': '.$row['source'].'</font>';
	}else{
		$out .= $row['projnum'];
	}
	} # endif not DELETE
echo $out;

} # foreach
echo '</ul>';
if ($deleted > 0) echo $deleted.' deleted accessions supressed from results list';

$db = null;

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a><br><a href="admin.php">Admin Page</a>'; 
?> 
</font>
</body>
</html> 
