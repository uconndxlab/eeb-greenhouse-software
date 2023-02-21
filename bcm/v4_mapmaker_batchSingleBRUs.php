<?php

### modified version of v4_mapmaker
### creates a map of just each unique level 3 code for use in indexes
###   creates PNG origin map & 470px JPG thumbnail version
###   verifies TDWG overlays exist for all LEVEL 4 zones
###
### color reference RGB values
###   base map - blue 70 130 180
###   base map - orange 220 80 10 

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}

### Collect list of TDWG L3 Codes
$sql = 'select l3code,l3name from tblLevel3';
foreach($db->query($sql) as $row) {

	echo 'Creating Base Image for '.$row['l3code'].': '.$row['l3name'].chr(10);
	$overlay = imagecreatefrompng($imagedir."maps/tdwg_templates/level3base.png");
	$width = imagesx($overlay);
	$height = imagesy($overlay);

	$dest_image = imagecreatetruecolor($width, $height);
#	$trans_colour = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
	$trans_colour = imagecolorallocate($dest_image, 255, 255, 255);
	imagefill($dest_image, 0, 0, $trans_colour);
	imagecopyresampled($dest_image, $overlay, 0, 0, 0, 0, $width, $height, $width, $height);
#	imagesavealpha($dest_image, true); # disable alpha channel for testing (Google Chrome renders black)
	### does template file exist?
	$imagemask=$imagedir.'maps/tdwg_templates/'.$row['l3code'].'.png';
	if (file_exists($imagemask)) {
		echo 'Adding overlay for '.$row['l3code'].chr(10);
		$overlay = imagecreatefrompng($imagedir.'maps/tdwg_templates/'.$row['l3code'].'.png');
		$width = imagesx($overlay);
		$height = imagesy($overlay);
		imagecopyresampled($dest_image, $overlay, 0, 0, 0, 0, $width, $height, $width, $height);
	} else {
		echo 'Overlay '.$row['l3_code'].' DOES NOT EXIST'.chr(10);
	}

	### create 470px jpg
	$dest_thumb = imagecreatetruecolor(470, 260);
	$trans_colour = imagecolorallocatealpha($dest_thumb, 255, 255, 255, 0);
	imagefill($dest_thumb, 0, 0, $trans_colour);
	imagecopyresampled($dest_thumb, $dest_image, 0, 0, 250, 120, 470, 260, 2870, 1614);
#	$banner = imagecreatefrompng($imagedir.'maps/tdwg_templates/uconn-wordmark-stacked-blue.png');
#	imagecopyresampled($dest_thumb, $banner, 385, 235, 0, 0, 84, 25, 84, 25); 
	echo 'Writing 470px x 260px JPG image...'.chr(10);
	imagejpeg($dest_thumb,$imagedir.'maps/tdwg/'.$row['l3code'].'.jpg');
#################################
	### Add UConn Banner to image
	$blue = imagecolorallocate($dest_image, 0, 14, 47);
	imagefilledrectangle($dest_image, 0, 1659, 3199, 1799, $blue);
	imagefilledrectangle($dest_image, 0, 0, 3199, 140, $blue);
	$banner = imagecreatefrompng($imagedir.'maps/tdwg_templates/uconn_banner_blue.png');
	imagecopyresampled($dest_image, $banner, 25, 10, 0, 0, 976, 120, 488, 60);

	### Add Latin Name Text on Banner
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 36;
	$white = imagecolorallocate($dest_image, 255, 255, 255);
	$text = 'TDWG Range of '.$row['l3code'].': '.$row['l3name'];
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((2200 - $tb[2]) / 2)+1000; ## lower left X coordinate for text
	imagettftext($dest_image, $fontsize, 0, $x, 90, $white, $font, $text);

	### write finished image
	echo 'Writing 3200px x 1800px PNG image...'.chr(10).chr(10);
	imagepng($dest_image,$imagedir.'maps/tdwg/'.$row['l3code'].'.png');
	### clean up
	imagedestroy($dest_image);
	imagedestroy($dest_thumb);
	imagedestroy($overlay);
} ### foreach
$db = null;
return true;

?>
