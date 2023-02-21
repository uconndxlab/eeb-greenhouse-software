<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Borrowing Lookup Routine</title>
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
echo '<tr><th colspan=2 align="left">EEB Greenhouse Plant Loan Summary:<p></th></tr>';
echo '<tr><th colspan=2>&nbsp</th></tr>';
echo '<tr><td><b>Use Date:</b></td><td>'.$_POST["usedate"].'</td></tr>';
echo chr(10);

$usedate = $_POST["usedate"];
$borrower = $_POST["borrower"];

echo '<tr><td><b>Borrower:</b></td><td>'.substr($borrower,strpos($borrower," ")+3).'</td></tr>';
echo '<tr><td><b>Course #:</b></td><td>'.$_POST["course"].'</td></tr>';
echo '<tr><td><b>Lab Number <i>{opt}</i>:</b></td><td>'.$_POST["labnum"].'</td></tr>';
echo '<tr><td><b>Lab Name/Usage:</b></td><td>'.$_POST["labname"].'</td></tr>';
echo '<tr><th colspan=2>&nbsp</th></tr></table>';
echo chr(10);
echo '<table><form action="http://florawww.eeb.uconn.edu/bcm/loan.validate.php" method="post">';

### Do lookup here ####### Past Lab Lookup Suppressed - may revive it a later date.
#echo '<input type=hidden name="acc_check[0]" value="">';
#if ($_POST["labnum"]>0) {
#	$sql = 'select distinct history.codeno,gh_inv.latin_name,gh_inv.location,gh_inv.projnum,classify.family from history,gh_inv,classify where gh_inv.genus=classify.genus and history.codeno=gh_inv.codeno and history.class="CLASS" and history.value='.$_POST["labnum"].' and "'.substr($_POST["course"],0,7).'"= substr(history.notes,1,7) order by gh_inv.location';
#	foreach($db->query($sql) as $row) {
#		$outstr = '<tr><td><input type=checkbox name="acc_check[]" value="'.$row['codeno'].'"></input>';
#		if (substr($row['projnum],0,6)=="DELETE") $outstr .= '<strike>';
#		$outstr .= ' - '.$row['location'].'</font>';	
#		$outstr .= ' - <font color="blue">'.$row['latin_name'].'</font>';
#		$outstr .= ' - '.$row['family'].'<br>';
#		$outstr .= ' <font color="green">'.$row['codeno'].' </font>';
#		$outstr .= ' - <font color="red">'.$row['projnum'].'</font>';
#		if (substr($row['projnum'],0,6)=="DELETE") $outstr .= '</strike>';
#		$outstr .= '</td></tr>';
#		echo $outstr.chr(10);
#	} #foreach
#}

#echo '<tr><td></td></tr><tr><td><center><b>AND/OR</b></center><p></td></tr><tr><td></td></tr>';

### Enter additional accessions here
echo chr(10);
echo '<tr><td><b>Enter up to 30 accession numbers (9-digits):</b><br>';
echo '<input name="acc[1]">  ';
echo '<input name="acc[2]"><br>';
echo '<input name="acc[3]"> ';
echo '<input name="acc[4]"><br>';
echo '<input name="acc[5]">  ';
echo '<input name="acc[6]"><br>';
echo '<input name="acc[7]">  ';
echo '<input name="acc[8]"><br>';
echo '<input name="acc[9]">  ';
echo '<input name="acc[10]"><br>';
echo '<input name="acc[11]">  ';
echo '<input name="acc[12]"><br>';
echo '<input name="acc[13]">  ';
echo '<input name="acc[14]"><br>';
echo '<input name="acc[15]">  ';
echo '<input name="acc[16]"><br>';
echo '<input name="acc[17]">  ';
echo '<input name="acc[18]"><br>';
echo '<input name="acc[19]">  ';
echo '<input name="acc[20]"><br>';
echo '<input name="acc[21]">  ';
echo '<input name="acc[22]"><br>';
echo '<input name="acc[23]">  ';
echo '<input name="acc[24]"><br>';
echo '<input name="acc[25]">  ';
echo '<input name="acc[26]"><br>';
echo '<input name="acc[27]">  ';
echo '<input name="acc[28]"><br>';
echo '<input name="acc[29]">  ';
echo '<input name="acc[30]"><br>';
echo '</td></tr>';
### Hidden Form Elements
echo chr(10);
echo '<tr><td><input name="labname" type="hidden" value="'.$_POST["labname"].'">';
echo chr(10);
echo '<input name="labnum" type="hidden" value="'.$_POST["labnum"].'">';
echo chr(10);
echo '<input name="usedate" type="hidden" value="'.$_POST["usedate"].'">';
echo chr(10);
echo '<input name="borrower" type="hidden" value="'.$_POST["borrower"].'">';
echo chr(10);
echo '<input name="course" type="hidden" value="'.$_POST["course"].'">'.'</td></tr>';
echo chr(10);
echo '<tr><td><input type="submit" name="type" value="Validate Entries">';
echo '</td></tr></form>';

echo '</table>';
$db = null;

?> 
</font>
</body>
</html> 
