<html><head>

<?php
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

$codeno = $_POST['codeno'];
$field = $_POST["field"];
$text = $_POST["text"];
### keyword field must begin and end with a space to make keyword searching function correctly.
if ($field=="keywords") {
	preg_replace('/\s/s+/', ' ', $text); # replace multiple spaces with single space
	if (substr($text,0) <> ' ') $text = ' '.$text; # prepend with a space if missing
	if (substr($text,-1) <> ' ') $text .= ' '; # append space at end of string if missing 
} # endif keyword

$url = $_POST["url"];

$sql = 'select gh_inv.latin_name,gh_inv.location,gh_inv.currently,classify.family,gh_inv.author from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

# Process Database Update Here ############################
$text = $db->quote($text);
$tempstatus = $result['currently'];
$zone = $result['location'];

$sql = 'update gh_inv set '.$field.'='.$text; 
$sql .= ' where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

### Add History Notation for certain field types
if ($field == "propstatus") {
	$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
	$sql .= 'values ('.$codeno.',';
	$sql .= $zone.',"';
	$sql .= 'PROPAGATE",';
	$sql .= 'curdate(),0,"';
	$sql .= $text;
	$sql .= '","",0,0)';
	$sql = 'update gh_inv set propstatusdate =curdate() where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
} # if propstatus

if ($field == "currently") {
	$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
	$sql .= 'values ('.$codeno.',';
	$sql .= $zone.',"';
	$sql .= 'STATUS",';
	$sql .= 'curdate(),0,"';
	$sql .= $tempstatus.' > '.$_POST['text']; # using original passed value rather than $text to avoid concatenating a quoted value
	$sql .= '","",0,0)';
	$sth = $db->prepare($sql);
	$sth->execute();
	$sql = 'update gh_inv set cdate =curdate(),confirm=curdate(),tempflag=1 where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
	$url = $url.'#'.$codeno;
} # if currently

$db = null;

echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';
?> 

</body>
</html> 
