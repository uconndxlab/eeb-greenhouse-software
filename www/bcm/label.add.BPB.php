<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Label File Generation - BPB Printer</title>
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

$codeno = $_GET['codeno'];

### Add one instance of label
$sql = 'select codeno,latin_name,commonname,cntry_orig,habitat,feature,family,source from gh_inv,classify where gh_inv.genus=classify.genus and gh_inv.codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$latin_name = $result['latin_name'];
$latin_name = str_replace('"',"",$latin_name);
$latin_name = str_replace("'","",$latin_name);
#echo $latin_name;
$commonname = $result['commonname'];
$cntry_orig = $result['cntry_orig'];
$habitat = $result['habitat'];
$source = $result['source'];
$family = $result['family'];

$sql = 'set sql_mode=""'; # disable default value checking
$sth = $db->prepare($sql);
$sth->execute();

$sql = 'insert into labels (codeno,latin_name,commonname,cntry_orig,habitat,feature,family,source) values ('.$codeno.',"'.$latin_name.'","'.$commonname.'","'.$cntry_orig.'","'.$habitat.'","'.$feature.'","'.$family.'","'.$source.'")';
$sth = $db->prepare($sql);
$sth->execute();

#echo $sql.'<hr>';

$db = null;

// Time out statement
echo '<meta HTTP-EQUIV="REFRESH" content="0; url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=labels">';

?> 
</font>
</body>
</html> 
