<html>
<head>
<meta name="viewport" content="width=device-width" />
<link href="style.css" type="text/css">

<?php
include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}
$codeno = $_GET['codeno'];
#$zone = $_POST['zone'];  ### Artifact?

$sql = 'select gh_inv.latin_name,classify.family,gh_inv.author from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

# Generate Title ################################################
echo '<title>Notes for '.$result['latin_name'];
echo ' {'.$result['family'].'} #'.$codeno;
echo '</title>';
echo '</head><body>';

echo '<H3>';

# Generate Latin Name Title ###########################################
echo $result['latin_name'].' <i>'.$result['author'].'</i></h3>';
echo '<b>Accession# </b>'.$codeno;
echo '<hr><b>Note:</b> Do not use quotation marks in text field<p>';

# Generate Pest Scout Dropdown
echo '<form action="historyinsert.php" method=post>';
echo '<input type="hidden" name=codeno value="'.$codeno.'">';
echo '<input type="hidden" name="class" value="SCOUT">';
echo '<input name="data" style="width:240px;">Data<br>';
echo '<select name="class" style="width:240px;">';
echo '<option>FLOWERING</option>';
echo '<option selected>NOTE</option>';
echo '<option>SPRAY</option>';
echo '<option>SCOUT</option>';
echo '<option value="ZNOTE">ZONE NOTATION</option>';
echo '<option>FERT</option>';
echo '<option>PROPAGATE</option>';
echo '<option>BIOCONTROL</option>';
echo '<option>CLASS</option>';
echo '<option>RESEARCH</option>';
echo '<option>OUTREACH</option>';
echo '<option>TRADE</option>';
echo '<option>WEEKLY</option>';
echo '<option>WATER</option>';
echo '<option>TASK</option>';
echo '<option>MECH</option>';
echo '</select>Class<br>';
echo '<input name="value" style="width:240px;">Value<br>';
echo '<input type="submit" name="type" value="Notation"></form>';

$db = null;

include('/var/www/bcm/footer.php');
?> 
</font>
</body>
</html> 
