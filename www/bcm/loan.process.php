<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Processing Greenhouse Loans</title>
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

$usedate = $_POST["usedate"];
$usedate = date("Y-m-d",strtotime($usedate));

$borrower = $_POST["borrower"];
$borrower = substr($borrower,0,strpos($borrower," "));

$course = $_POST["course"];
$course = substr($course,0,strpos($course," "));

$labname = $_POST["labname"];
$labnum = $_POST["labnum"];
$outstr = $course.' - '.$labname.' ['.$borrower.']';

$array = explode(",",$_POST["accessions"]);
$y = count($array);
for ($x=0;$x<$y;$x++) {
	if (is_numeric($array[$x]) and ($array[$x] >= 198500001 && $array[$x] <= 205000000)) {
		### Generate insert sql
		$codeno=$array[$x];
		$sql = 'select gh_inv.latin_name,gh_inv.location from gh_inv where codeno='.$codeno;
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		$zone = $result['location'];

		# Process Database Update Here ############################
		$sql = 'insert into history (codeno,zone,class,date,value,notes,extra,ipmtag) ';
		$sql .= 'values ('.$codeno.',';
		$sql .= $zone.',"';
		if (substr($course,0,8)=='Outreach') {
			$sql .= 'OUTREACH","'; 
		} elseif (substr($course,0,5)=='Trade') {
			$sql .= 'TRADE","';
		} else {
			$sql .= 'CLASS","';
		}
		$sql .= $usedate.'",'.$labnum.',"';
		$sql .= $outstr.'"';
		$sql .= ',"",0)';
		echo 'Processing '.($x+1).' - '.$codeno.'<br>';
		# process update SQL
		$sth = $db->prepare($sql);
		$sth->execute();
		# Process Confirm Here ############################
		$sql = 'update gh_inv set confirm="'.$usedate.'" where codeno='.$codeno;
		$sth = $db->prepare($sql);
		$sth->execute();
	} #if valid codeno
} #foreach
echo 'Processing complete......Thank You....';
$db = null;

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/admin.php">Return to Admin Screen</a>';

?> 
</font>
</body>
</html> 
