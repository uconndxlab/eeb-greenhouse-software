<?php
require_once('PDF_label.php'); # 3rd party library
include '/var/www/bcm/credentials.php';

$image='/var/www/images/logos/uconn-wordmark-stacked-black.png';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}
$sql = 'select * from labels_v2 order by recno limit 20';

$sth = $db->prepare($sql);
$sth->execute();	
$num=$sth->rowCount();
#if ($num>10) $num=10;

/*------------------------------------------------
To create the object, 2 possibilities:
either pass a custom format via an array
or use a built-in AVERY name
------------------------------------------------*/

// Example of custom format
// $pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99, 'height'=>38, 'font-size'=>14));

// Standard format
$pdf = new PDF_Label('5161CM');

$pdf->AddPage();
// Brute force placement of UConn Logo
$pdf->Image($image,72,7,25);
$pdf->Image($image,178,7,25);

$pdf->Image($image,72,32,25);
$pdf->Image($image,178,32,25);

$pdf->Image($image,72,58,25);
$pdf->Image($image,178,58,25);

$pdf->Image($image,72,83,25);
$pdf->Image($image,178,83,25);

$pdf->Image($image,72,109,25);
$pdf->Image($image,178,109,25);

### The following locations are for bottom 10 results on a 20 label sheet
$pdf->Image($image,72,134,25);
$pdf->Image($image,178,134,25);

$pdf->Image($image,72,160,25);
$pdf->Image($image,178,160,25);

$pdf->Image($image,72,185,25);
$pdf->Image($image,178,185,25);

$pdf->Image($image,72,211,25);
$pdf->Image($image,178,211,25);

$pdf->Image($image,72,235,25);
$pdf->Image($image,178,235,25);

// Print labels
for($i=0;$i<=$num-1;$i++) {
	$result = $sth->fetch(PDO::FETCH_ASSOC);
   	$pdf->Set_Font_Size(10); 
	$codeno = $result['codeno'];
	$latin_name = (substr($result['latin_name'],0,30));
	$commonname = $result['commonname'];
	$family = $result['family'];
	$cntry_orig = $result['cntry_orig'];
	$habitat = $result['habitat'];
	$text = sprintf("%s\n%s%s\n%s%s\n%s%s%s%s", $latin_name, "  ",$commonname, "  ",$cntry_orig, "","UConn# $codeno","  |  ", $family);
#   	$pdf->Add_Label($text); // default same size text outputting
	$pdf->Add_FLabel($codeno,$latin_name,$commonname,$family,$cntry_orig,$habitat); //Line by line formatted version

}

$pdf->Output();

$db = null;
?> 
