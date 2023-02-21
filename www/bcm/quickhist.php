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

$codeno = $_GET['codeno'];
$class = $_GET['class'];
$url = $_GET['url'];
# Process Database Update Here ############################
$sql = 'select gh_inv.latin_name,gh_inv.location from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$latin_name = $result['latin_name'];
$location = $result['location'];
#set values to posted data
$sql = 'insert into history ';
$sql .= '(codeno,zone,class,date,value,notes,extra,recno,ipmtag) ';
$sql .= 'values ('.$codeno.',';
$sql .= $location.',"';
$sql .= $class.'",curdate(),0,"';
$sql .= $_GET["data"].'"';
$sql .= ',"",0,0)';

$sth = $db->prepare($sql);
$sth->execute();

# Process Confirm Here ############################
$sql = 'update gh_inv set confirm=curdate(),tempflag=1 where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

$db = null;
#echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'">';
echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$url.'">';

?> 

</body>
</html> 
