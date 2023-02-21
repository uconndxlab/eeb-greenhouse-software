<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # user friendly message
	echo $ex->getMessage().chr(10); # Explicit Error Message
}

#echo '</head><body><h3>';
$field = $_GET['v'];
$url = $_GET['url'];
$codeno = $_GET['codeno'];

$sql = 'select gh_inv.latin_name,gh_inv.genus,gh_inv.species,classify.family,gh_inv.author from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

# Generate Title ###################################################
echo '<title>Edit Memo for '.$result['latin_name'];
echo ' {'.$result['family'].'} #'.$codeno;
echo '</title>';
echo '</head><body><h3>';

# Generate Latin Name Title ###########################################
echo '<i>'.$result['latin_name'].'</i> '.$result['author'].'</h3><p>';

# Generate General Information #######################################
$sql = 'select gh_inv.'.$field.' as field';
$sql .= ' from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

echo '<b>'.$field.': </b>';
echo '<form action="http://florawww.eeb.uconn.edu/bcm/updatetext.php" method="post">';
echo '<input type="hidden" name="codeno" value="'.$codeno.'">';
echo '<input type="hidden" name="field" value="'.$field.'">';
echo '<input type="hidden" name="url" value="'.$url.'">';
echo '<textarea name="text" cols=60 rows=12>'.$result['field'].'</textarea>';

echo '<p><input type="submit" name="submit" value="Update Record"></form>';

### include helper links for credits section
if ($field=="credits") {
	echo '<hr><b>Quick Links for Credits (new browser tab)</b><br><i>Hint: copy latin name before jumping to new tab, paste into search box</i>';
	echo '<br><i>Note: AutoKey must be running on local computer for macros to work</i>';
	echo '<ul>';
	echo '<li>ctrl+super+o - Insert Ordered List Tags for New List</li>';
	echo '<li>ctrl+super+w - <a href="https://wcsp.science.kew.org/home.do" target="_blank">WCSP</a></li>';
	echo '<li>ctrl+super+p - <a href="http://www.theplantlist.org/" target="_blank">PlantList</a></li>';
	echo '<li>ctrl+super+i - <a href="http://www.iucnredlist.org/" target="_blank">IUCN RedList</a></li>';
	echo '<li>ctrl+super+b - <a href="https://tools.bgci.org/plant_search.php" target="_blank">BGCI PlantSearch</a> - <i>Note: will need to split genus & species in tab</i></li>';
	echo '<li>ctrl+super+a - <a href="https://npgsweb.ars-grin.gov/gringlobal/taxon/taxonomysearch.aspx" target="_blankl">ARS-GRIN Taxonomy</a></li>';
	echo '<li>no key macro - <a href="http://www.google.com/" target="_blank">Google</a></li>';	
	
	echo '</ul>';
} # end credit helper
$db = null;

?> 
</body>
</html> 
