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

$sql = 'select latin_name,commonname,cntry_orig,redlist2010,signtext_alt,signtext_altA,signtext_altB,signtext_altC,signtext_altD,sign_icon,descrip from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	

#### Generate PNG Sign Image from database text

header('Content-type: image/png');

$color = RgbfromHex(672591);

$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
$fontsize = 30;

### create base image and background color 
$im = imagecreatetruecolor(800, 500);

### Default Background Color is Pale Green - 105, 150, 105
###    Can change this to create special use signage
###  !!!  Be sure to change text color (black/white) for proper contrast depending upon bg_color selected - near line #170
#$bg_color = imagecolorallocate($im, 105, 150, 105);  # Default green color
#$bg_color = imagecolorallocate($im, 230, 130, 25); #AntU - Pumpkin color
#$bg_color = imagecolorallocate($im,0,35,102); #EEB2244 Royal Blue
$bg_color = imagecolorallocate($im,255,239,0); #Yellow Kress Collection
#$bg_color = imagecolorallocate($im,255,0,100); #Pollination Syndrome Purple
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$font_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);

### Black Rectangle
imagefilledrectangle($im, 0, 0, 800, 500, $black);
### Fill with background color - leaving a border
imagefilledrectangle($im, 3, 3, 796, 496, $bg_color);
### Create Black Border for Informational Text
imagefilledrectangle($im, 20, 70, 779, 210, $black);
### Create White Rectangle for Informational Text
imagefilledrectangle($im, 22, 72, 777, 208, $white);
### Create Black Border for Photo
imagefilledrectangle($im, 20, 225, 493, 478, $black);
### Create Black Border for Icon Box
imagefilledrectangle($im, 513, 225, 780, 478, $black);
### Create White Rectangle for Icon Box
imagefilledrectangle($im, 515, 227, 778, 476, $white);

### INSERT PRIMARY IMAGE HERE
### IMAGE xx_00.jpg should have a default size of 470x250px
### IF NO xx.00.jpg exists use a default greenhouse image
### Strip out extra characters, replace spaces with underscores
$namestr = strtr($result['latin_name']," ","_");
$namestr = str_replace('-', "_", $namestr);
$namestr = str_replace('"', "", $namestr);
$namestr = str_replace("'", "", $namestr);
$imagemask = $imagedir.'byspecies/'.$namestr.'00.jpg';

if (file_exists($imagemask)) {
	$photo = imagecreatefromjpeg($imagemask);
	imagecopymerge($im,$photo,22,227,0,0,470,250,100);
} ### end file_exists

### Add QR Code Data
### Default=webpage - or use special URL in database (YouTube etc)

#if (strlen($result['signQR'])>0) {
#	$codeContents = $result['signQR'];
#	} else {
	$codeContents = "http://florawww.eeb.uconn.edu/".$codeno.".html#nfc";
#	}
ob_start();
QRCode::png($codeContents, null);
$QRString = ob_get_contents();
ob_end_clean();
$QRImage = imagecreatefromstring($QRString);
#	imagescale($QRImage,100,100);
	imagecopy($im,$QRImage,640,230,0,0,100,100);
	$fontsize = 10;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
#if (strlen($result['signQR'])>0) {
#	imagettftext($im, $fontsize, 0, 550,345, $black, $font, $result['signQRtag']);
#	} else {
	imagettftext($im, $fontsize, 0, 670,345, $black, $font, 'More Info');
#	}

### Add NFC Icon
	$logoImage = imagecreatefrompng("/var/www/images/icons/nfc-icon-100.png");
	imagecopy($im,$logoImage,530,240,0,0,100,107);
	#imagettftext($im, $fontsize, 0, 650,350, $black, $font, 'Tap Mobile Device');
	imageline($im,535,355,760,355,$black);
########################
########################  LOGO PLACEMENT TESTING
$logofile = '/var/www/images/icons/'.$result['sign_icon'];
$logoImage = imagecreatefrompng($logofile);
imagecopy($im,$logoImage,590,360,0,0,108,108);

########################
########################

### Add YouTube Icon if video link on webpage
	if (strpos($result['descrip'],"youtube.com") or strpos($result['descrip'],"vimeo.com")) {
		$logoImage = imagecreatefrompng("/var/www/images/icons/youtube-icon-100.png");
		imagecopy($im,$logoImage,650,370,0,0,100,70);
		imagettftext($im, $fontsize, 0, 652,470, $black, $font, 'Video Available');
	} # Check if video link on webpage

### Add IUCN Status Icon
	### Default to Not Evaluated - may indicate we have just not cross referenced it yet
#	$logoImage = imagecreatefromjpeg("/var/www/images/icons/NE-100.jpg");
#	imagecopy($im,$logoImage,525,360,0,0,100,100);
#	if ($result['redlist2010']=='Critically Endangered') $logoImage = imagecreatefromjpeg("/var/www/images/icons/CR-100.jpg");
#	if ($result['redlist2010']=='Extinct in the Wild') $logoImage = imagecreatefromjpeg("/var/www/images/icons/EW-100.jpg");
#	if ($result['redlist2010']=='Endangered') $logoImage = imagecreatefromjpeg("/var/www/images/icons/EN-100.jpg");
#	if ($result['redlist2010']=='Vulnerable') $logoImage = imagecreatefromjpeg("/var/www/images/icons/VU-100.jpg");
#	if ($result['redlist2010']=='Least Concern') $logoImage = imagecreatefromjpeg("/var/www/images/icons/LC-100.jpg");
#	if ($result['redlist2010']=='Lower Risk: Near Threatened') $logoImage = imagecreatefromjpeg("/var/www/images/icons/NT-100.jpg");
#	imagecopy($im,$logoImage,525,360,0,0,100,100);
#	imagettftext($im, $fontsize, 0, 540,470, $black, $font, 'IUCN Status');
### Add Poison Sign
#if ($result['poisonous']) {
#	$logoImage = imagecreatefrompng("/var/www/images/logos/poison.png");
#	imagecopy($im,$logoImage,525,365,0,0,123,102);
#	$fontsize = 12;
##### Standard Poison Text
#	$font = '/usr/share/fonts/truetype/msttcorefonts/ariali.ttf';
#	imagettftext($im, $fontsize, 0, 615,385, $black, $font, 'Some plant parts');
#	imagettftext($im, $fontsize, 0, 625,405, $black, $font, 'may contain toxins');
#	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';	
#	imagettftext($im, $fontsize, 0, 635,425, $black, $font, 'Please Do Not');
#	imagettftext($im, $fontsize, 0, 645,445, $black, $font, 'Sample Plants');
##### Special Stinging Hairs Text
#	$font = '/usr/share/fonts/truetype/msttcorefonts/ariali.ttf';
#	imagettftext($im, $fontsize, 0, 615,385, $black, $font, 'Plants contain');
#	imagettftext($im, $fontsize, 0, 625,405, $black, $font, 'stinging hairs');
#	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';	
#	imagettftext($im, $fontsize, 0, 635,425, $black, $font, 'Please Do Not');
#	imagettftext($im, $fontsize, 0, 645,445, $black, $font, 'Touch Plants');
##### Special EEB2244 Station Text
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';
	$text = $result['signtext_altA'];
	imagettftext($im, $fontsize, 0, 540,385, $black, $font, $text);
	$text = $result['signtext_altB'];
	imagettftext($im, $fontsize, 0, 540,405, $black, $font, $text);
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';	
	$text = $result['signtext_altC'];
	imagettftext($im, $fontsize, 0, 540,445, $black, $font, $text);
	$text = $result['signtext_altD'];
	imagettftext($im, $fontsize, 0, 540,465, $black, $font, $text);
#	}


### Write Common Name
$text = $result['commonname'];
	$fontsize = 36;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((800 - $tb[2]) / 2); // lower left X coordinate for text
### Select Text Color depending upon bg_color
	imagettftext($im, $fontsize, 0, $x, 55, $black, $font, $text);
#	imagettftext($im, $fontsize, 0, $x, 55, $white, $font, $text);
### Write Latin Name & Family
$text = 'Scientific Name: '.$result['latin_name'];
	$fontsize = 14;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbi.ttf';
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((800 - $tb[2]) / 2); // lower left X coordinate for text
imagettftext($im, $fontsize, 0, $x, 95, $black, $font, $text);

### Write Family
#$text = $result['family'];
#	$text = '{'.$text.'}';
#	### calculate bounding box for centering
#	$tb = imagettfbbox($fontsize, 0, $font, $text);
#	$x = ceil((800 - $tb[2]) / 2); // lower left X coordinate for text
#
#imagettftext($im, $fontsize, 0, $x, 155, $black, $font, $text);

### Write Country of Origin
$text = 'Origin: '.$result['cntry_orig'];
	$fontsize = 14;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((800 - $tb[2]) / 2); // lower left X coordinate for text
imageline($im,200,130,600,130,$black);
imagettftext($im, $fontsize, 0, $x, 120, $black, $font, $text);

### Write Description info
$text = $result['signtext_alt'];
	$fontsize = 12;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	### Break up multiline text
	$lines = explode('|', wordwrap($text, 93, '|'));
	## Starting Y position
	$y = 150;
	foreach ($lines as $line)
		{
		### calculate bounding box for centering
		$tb = imagettfbbox($fontsize, 0, $font, $line);
		$x = ceil((800 - $tb[2]) / 2); // lower left X coordinate for text
		imagettftext($im, $fontsize, 0, $x, $y, $black, $font, $line);
		    $y += 25;
		}

imagepng($im);

imagedestroy($im);

$db = null;

function RgbfromHex($hexValue) {
    if(strlen(trim($hexValue))==6) {
        return array(
                     hexdec(substr($hexValue,0,2)), // R
                     hexdec(substr($hexValue,2,2)), // G
                     hexdec(substr($hexValue,4,2))  // B
                    );
    }
    else return array(0, 0, 0);
}

?> 
