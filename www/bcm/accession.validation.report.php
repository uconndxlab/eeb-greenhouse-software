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

$days2review = $_GET['days2review'];

$url = urlencode(curPageURL());
# Generate Title ################################################
echo '<title>Accession Validation Report</title>';
echo '</head><body>';
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

############# Generate Listing ###############
$sql = 'select * from gh_inv';
$sql .= ' where gh_inv.acc_date>date_sub(curdate(),interval '.$days2review.' day)';
$sql .= ' and gh_inv.projnum not like "DELETE%"';
$sql .= ' order by gh_inv.codeno';

###echo '<hr>'.$sql.'<hr>';
echo '<b>Recently Accessioned Plants (past '.$days2review.' days) with Missing Data</b><br>';
echo '<i>{Deleted accessions not displayed}</i>';

echo '<ul>';

		foreach($db->query($sql) as $result) {
			echo '<li>'.$result['projnum'].' <a href="accession.php?codeno='.$result['codeno'].'&tab=tasks"';
			echo ' target="_blank">'.$result['codeno'].': '.trim($result['latin_name']).'</a>';
			include '/var/www/bcm/accession.validate.INCL.php';


#			echo '<ol>';
########### The following statements will check various fields and report null values or probable questionable values

		### check MAIN Tab
		### check currently
#			if ($row['currently'] === NULL) echo '<li><mark><b>Currently</b> is set to NULL</mark> - Main Tab';

		### check DATA Tab
		### check for null value in commonname
#			if ($row['commonname'] === NULL) echo '<li><mark><b>Common name</b> is NULL</mark> - DATA Tab';
		### check for null value in country of origin
#			if ($row['cntry_orig'] === NULL) echo '<li><mark><b>Country of Origin</b> is NULL</mark> - DATA Tab';
		### check for null value in tdwg
#			if ($row['tdwg'] === NULL) echo '<li><mark><b>Origin Data</b> is NULL</mark> - DATA Tab';
#			if ($row['p3_tdwg'] === NULL) echo '<li><mark><b>Primary L2 Code</b> is NULL</mark> - DATA Tab';
		### check provenance & provenance 2 - flag if data present but no <pre> tags
#			if (strlen($row['provenance'])>0 and (strpos($row['provenance'],'pre>')=== false)) echo '<li><mark><b>Provenance</b> is missing &ltpre&gt&lt/pre&gt tags</mark> - DATA Tab';
#			if (strlen($row['provenance2'])>0 and (strpos($row['provenance2'],'pre>')=== false)) echo '<li><mark><b>Provenance 2</b> is missing &ltpre&gt&lt/pre&gt tags</mark> - DATA Tab';
			
		### check CLASSIFY Tab
		### check for blank author
#			if ($row['author'] === NULL) echo '<li><mark><b>Author</b> is NULL</mark> - CLASSIFY Tab';
		### is there a synonomous name in the literature?
#			if ($row['synonomy'] === NULL) echo '<li><mark>Are there any <b>Synonyms</b> in the literature?</mark> - CLASSIFY Tab';
		### check species, if more than one word (ie has missing infraspecific info)
#			if (str_word_count($row['species'],0)>1 and $row['infrarank'] === null) echo '<li><mark>Check <b>Infraspecific Rank</b></mark> - CLASSIFY Tab';
#			if (str_word_count($row['species'],0)>1 and $row['infraepithet'] === null) echo '<li><mark>Check <b>Infraspecific Epithet</b></mark> - CLASSIFY Tab';
#			if (str_word_count($row['species'],0)>1 and $row['cultivar'] === null) echo '<li><mark>Check <b>Cultivar</b></mark> - CLASSIFY Tab';

		### check BGCI Tab
		### bgci_status or bgci_exsitusites is null
#			if ($row['bgci_status'] === NULL) {
#				echo '<li><mark><b>BGCI Status</b> is NULL</mark> - BGCI Tab';
#				echo ' <a href="https://tools.bgci.org/plant_search.php?action=Find&ftrGenus='.$row['genus'];
#				echo '&ftrSpecies='.$row['species'].'" target="_blank">Link</a>';
#			} #bgci_status
#			if ($row['bgci_exsitusites'] === NULL) {
#				echo '<li><mark><b>Ex-Situ BGCI Sites</b> is NULL</mark> - BGCI Tab';
#				echo ' <a href="https://tools.bgci.org/plant_search.php?action=Find&ftrGenus='.$row['genus'];
#				echo '&ftrSpecies='.$row['species'].'" target="_blank">Link</a>';
#			} #bgci_status

		### check ASSESS Tab
		### check for missing space required
#			if ($row['space_req'] === NULL or $row['space_req']==0) echo '<li><mark><b>Space Required</b> is NULL or zero</mark> - ASSESS Tab';

		### check PEST Tab


		### check CULTURE Tab


		### check LABELS Tab


		### check MISC Tab
		### check for empty credits
#			if ($row['credits'] === NULL) echo '<li><mark><b>Credits</b> is NULL</mark> - MISC Tab';
#			if (strlen($row['credits'])>0 and (strpos($row['credits'],'ol>')=== false)) echo '<li><mark><b>Credits</b> are missing &ltol&gt&lt/ol&gt tags</mark> - DATA Tab';
		### check for NULL keywords
#			if ($row['keywords'] === NULL) echo '<li><mark><b>Keywords</b> is NULL</mark> - MISC Tab';
			
			
#			echo '</ol>';
		} #foreach

echo '</ul>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

$db = null;
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

</body>
</html> 
