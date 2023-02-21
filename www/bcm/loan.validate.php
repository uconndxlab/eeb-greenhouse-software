<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Borrowing Test Routine</title>
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

echo '<table>';
echo '<tr><th colspan=2 align="left">EEB Greenhouse Plant Loan Summary:</th></tr>';
echo '<tr><th colspan=2>&nbsp</th></tr>';
echo '<tr><td><b>Use Date:</b></td><td>'.$_POST["usedate"].'</td></tr>';

$usedate = $_POST["usedate"];
$borrower = $_POST["borrower"];

echo '<tr><td><b>Borrower:</b></td><td>'.substr($borrower,strpos($borrower," ")+3).'</td></tr>';
echo '<tr><td><b>Course #:</b></td><td>'.$_POST["course"].'</td></tr>';
echo '<tr><td><b>Lab Number:</b></td><td>'.$_POST["labnum"].'</td></tr>';
echo '<tr><td><b>Lab Name/Usage:</b></td><td>'.$_POST["labname"].'</td></tr>';
echo '<tr><th colspan=2>&nbsp</th></tr>';
echo '<tr><th colspan=2>Plants Used:</th></tr>';

#$array = array_merge($_POST["acc_check"],$_POST["acc"]); ### version for checkbox list

$array = $_POST['acc']; ### version for manual entry only
### reverse sort merged array
rsort($array);
### remove null and zero values
$array = array_diff($array, array(''));
#echo print_r($array);
$y = count($array);

if ($y==0) echo '<tr><td>No Plants Recorded</td></tr>';

for ($x=0;$x<$y;$x++) {
##	if (is_numeric($array[$x]) and ($array[$x] >= 198500001 && $array[$x] <= 205000000)) {
	if (is_numeric($array[$x])) {
		$sql = 'select gh_inv.codeno,gh_inv.latin_name from gh_inv where codeno='.$array[$x];		
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->fetchColumn()) {
			$sth->execute();
			$result = $sth->fetch(PDO::FETCH_ASSOC);	
			echo '<tr><td><b>'.($x+1).' - '.$array[$x].'</b></td><td>'.$result['latin_name'].'</td></tr>';
			$array2[]=$array[$x];
		} else {
			echo '<tr><td><b>'.($x+1).' - '.$array[$x].'</b></td><td><b>*** INVALID NUMBER ***<b></td></tr>';	
		} # if existing accession number (valid)
	} else {
		echo '<tr><td><b>'.($x+1).' - '.$array[$x].'</b></td><td><b>*** NON-NUMERIC DATA ***<b></td></tr>';	
	} # if correct format and in range
} #for loop

echo '</table>';

### echo form information
echo '<form action="http://florawww.eeb.uconn.edu/bcm/loan.process.php" method="post">';
echo '<input type="hidden" name="usedate" value="'.$usedate.'">';
echo '<input type="hidden" name="borrower" value="'.$_POST["borrower"].'">';
echo '<input type="hidden" name="course" value="'.$_POST["course"].'">';
echo '<input type="hidden" name="labname" value="'.$_POST["labname"].'">';
echo '<input type="hidden" name="labnum" value="'.$_POST["labnum"].'">';
$array3=implode(",",$array2);
echo '<input type="hidden" name="accessions" value='.$array3.'>';
echo '<p><input type="submit" name="type" value="Is this correct?"></form>';
echo '<br><i>Please use browser BACK button to make corrections</i><b> - not working properly, needs debugging</b>';

$db = null;

?> 
</font>
</body>
</html> 
