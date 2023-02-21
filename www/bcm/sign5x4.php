<?php

include '/var/www/bcm/credentials.php';
include '/var/www/bcm/phpqrcode/qrlib.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$codeno = $_GET['codeno'];

$sql = 'select latin_name,commonname,cntry_orig,redlist2010,signtext,signQR,signQRtag,poisonous,descrip from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	

#### Generate PNG Sign Image from database text

header('Content-type: image/png');

$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
$fontsize = 30;

### create base image and background color 
$im = imagecreatetruecolor(432, 336); # 4.5"x3.5" @ 96 dpi


### Default Background Color is Pale Green - 105, 150, 105
###    Can change this to create special use signage
#$bg_color = imagecolorallocate($im, 105, 150, 105);
$bg_color = imagecolorallocate($im,255,239,0); #Yellow Kress Collection
#$bg_color = imagecolorallocate($im, 230, 130, 25); #AntU - Pumpkin color
#$bg_color = imagecolorallocate($im,0,35,102); #EEB2244 Royal Blue
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);

### Black Rectangle
imagefilledrectangle($im, 0, 0, 432, 336, $black);
### Fill with background color - leaving a border
imagefilledrectangle($im, 2, 2, 429, 333, $bg_color);
### Create Black Border for Informational Text
imagefilledrectangle($im, 20, 60, 411, 116, $black);
### Create White Rectangle for Informational Text
imagefilledrectangle($im, 22, 62, 409, 114, $white);
### Create Black Border for Photo
imagefilledrectangle($im, 20, 129, 411, 316, $black);

### INSERT PRIMARY IMAGE HERE
### IMAGE xx_00.jpg should have a default size of 470x250px
### Strip out extra characters, replace spaces with underscores
$namestr = strtr($result['latin_name']," ","_");
$namestr = str_replace('-', "_", $namestr);
$namestr = str_replace('"', "", $namestr);
$namestr = str_replace("'", "", $namestr);
$imagemask = $imagedir.'byspecies/'.$namestr.'00.jpg';

if (file_exists($imagemask)) {
	$photo = imagecreatefromjpeg($imagemask);
	imagecopyresampled($im,$photo,22,131,0,0,388,184,470,250);
#	imagecopymerge($im,$photo,22,131,0,0,470,250,100);
} ### end file_exists

### Add NFC Icon
	$logoImage = imagecreatefrompng("/var/www/images/icons/nfc-icon-100.png");
	imagecopyresampled($im,$logoImage,361,63,0,0,48,51,100,107);

### Write Common Name
$text = $result['commonname'];
	if (strlen($text)==0) $text = 'Gingermania';
	$fontsize = 30;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((432 - $tb[2]) / 2); // lower left X coordinate for text
	imagettftext($im, $fontsize, 0, $x, 48, $black, $font, $text);
### Write Latin Name & Family
$text = $result['latin_name'];
	$fontsize = 12;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((408 - $tb[2]) / 2); // lower left X coordinate for text
	imagettftext($im, $fontsize, 0, $x, 85, $black, $font, $text);

### Write Country of Origin
$text = substr($result['cntry_orig'],0,40);
	$fontsize = 12;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((408 - $tb[2]) / 2); // lower left X coordinate for text
#	imageline($im,200,130,600,130,$black);
	imagettftext($im, $fontsize, 0, $x, 105, $black, $font, $text);

imagepng($im);
imagedestroy($im);
$db = null;

?> 
