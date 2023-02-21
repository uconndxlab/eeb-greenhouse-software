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
$field=$_GET["v"];
$url=$_GET["url"];
$codeno = $_GET['codeno'];

$sql = 'select gh_inv.latin_name,classify.family,gh_inv.author from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
# Generate Title ################################################
echo "<title>EEB Conservatory Page for ".$result['latin_name'];
echo ' {'.$result['family'].'} #'.$codeno;
echo "</title>";
echo "</head><body><h3>";

# Generate Latin Name Title ###########################################
echo '<i>'.$result['latin_name'].'</i> '.$result['author'].'</h3><hr>'.chr(10);

# Generate General Information #######################################
$sql = 'select gh_inv.'.$field;
$sql .= ' from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$default = $sth->fetchColumn();

echo '<form action="http://florawww.eeb.uconn.edu/bcm/updatetext.php" method="post">'.chr(10);
echo '<input type="hidden" name="codeno" value="'.$codeno.'">'.chr(10);
echo '<input type="hidden" name="field" value="'.$field.'">';
echo '<input type="hidden" name="url" value="'.$url.'">';
echo '<b>'.$field.': </b>'.chr(10);
echo '<select name=text size=1>';

###################################### SPECIAL CASES INITIALLY OMITTED
#if ($field=="propstatus") {
#	$sql = 'select propstatus from tblPropStatus order by listorder';
#	} elseif ($field=="currently") {
#	$sql = 'select currently from tblCurrentStatus order by listorder';
#	} else {
	$sql = 'select distinct gh_inv.'.$field.' as field from gh_inv order by '.$field;
#	}	

foreach($db->query($sql) as $result) {
	echo '<option';
	if ($result['field']==$default) {
		echo ' selected';
	} 
	echo '>'.$result['field'].'</option>'.chr(10);
} # foreach 
echo '</select><input type="submit" name="submit" value="Update Record"></form>'.chr(10);

####### Build Cancel Button ################
### Disabled - redirect different when using from something other than accession
#echo '<form action="http://florawww.eeb.uconn.edu/bcm/accession.php" method="get">';
#echo '<input type="hidden" name="codeno" value="'.$codeno.'">';
#echo '<input type="submit" value="Cancel"></form><hr>'; 

$db = null;

?> 
</body>
</html> 
