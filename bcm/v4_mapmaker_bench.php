<?php
function v4_mapmaker_bench($zonelabels,$showbenches,$showbenchlabels)
{
### test bed for greenhouse bench map generator
###
### color reference RGB values
###   base map - blue 70 130 180
###   base map - orange 220 80 10 
###   v4 updates to mysql PDO

include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}

#$sql_result=mysql_query($sql);

#if (!$sql_result) {
#	echo mysql_error();
#}
#$numbenches=mysql_numrows($sql_result);

### GENERATE TORREY BASEMAP TEMPLATE
#	echo 'Generating Greenhouse Map Basemap image'.chr(10);
	$overlay = imagecreatefrompng($imagedir."maps/bench/Torrey_Greenhouse_Base.png");
	$width = imagesx($overlay);
	$height = imagesy($overlay);

	$dest_image = imagecreatetruecolor($width, $height);
	$white = imagecolorallocate($dest_image, 255, 255, 255);
	$black = imagecolorallocate($dest_image, 0, 0, 0);
	$lightgray = imagecolorallocate($dest_image, 195, 195, 195);
	$textgray = imagecolorallocate($dest_image,140,140,140);
	$trans_colour = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);
	imagefill($dest_image, 0, 0, $trans_colour);
	imagecopyresampled($dest_image, $overlay, 0, 0, 0, 0, $width, $height, $width, $height);
	imagesavealpha($dest_image, true);

#	echo 'Adding Banner Information to PNG image'.chr(10);
	### Add UConn Banner to image
	$blue = imagecolorallocate($dest_image, 0, 14, 47);
	imagefilledrectangle($dest_image, 0, 1859, 1999, 1999, $blue);
	imagefilledrectangle($dest_image, 0, 0, 1999, 140, $blue);
	$banner = imagecreatefrompng($imagedir.'maps/tdwg_templates/uconn_banner_blue.png');
	imagecopyresampled($dest_image, $banner, 25, 10, 0, 0, 976, 120, 488, 60);

	### Draw Greenhouse Footprint
#	echo 'Generating Facility Footprint'.chr(10);
	imagefilledrectangle($dest_image, 10, 150, 489, 1349, $black); # Greenhouse #1 background
	imagefilledrectangle($dest_image, 610, 150, 969, 1349, $black); # Greenhouse #2
	imagefilledrectangle($dest_image, 1090, 150, 1449, 1349, $black); # Greenhouse #3
	imagefilledrectangle($dest_image, 10, 1347, 1449, 1494, $black); # Headhouse
	imagefilledrectangle($dest_image, 10, 1495, 261, 1782, $black); # Greenhouse #4

	### Fill in each room

	imagefilledrectangle($dest_image, 13, 153, 486, 547, $lightgray); # Greenhouse #1300 interior
	imagefilledrectangle($dest_image, 13, 550, 486, 947, $lightgray); # Greenhouse #1200
	imagefilledrectangle($dest_image, 13, 950, 486, 1347, $lightgray); # Greenhouse #1100 
	imagefilledrectangle($dest_image, 613, 153, 966, 547, $lightgray); # Greenhouse #2300
	imagefilledrectangle($dest_image, 613, 550, 966, 947, $lightgray); # Greenhouse #2200
	imagefilledrectangle($dest_image, 613, 950, 966, 1347, $lightgray); # Greenhouse #2100
	imagefilledrectangle($dest_image, 1093, 153, 1446, 547, $lightgray); # Greenhouse #3300
	imagefilledrectangle($dest_image, 1276, 550, 1446, 947, $lightgray); # Greenhouse #3200
	imagefilledrectangle($dest_image, 1093, 550, 1273, 647, $lightgray); # Greenhouse #32D
	imagefilledrectangle($dest_image, 1093, 650, 1273, 747, $lightgray); # Greenhouse #32C
	imagefilledrectangle($dest_image, 1093, 750, 1273, 847, $lightgray); # Greenhouse #32B
	imagefilledrectangle($dest_image, 1093, 850, 1273, 947, $lightgray); # Greenhouse #32A
	imagefilledrectangle($dest_image, 1093, 950, 1446, 1347, $lightgray); # Greenhouse #3100

	imagefilledrectangle($dest_image, 13, 1350, 1446, 1491, $lightgray); # Headhouse
	imagefilledrectangle($dest_image, 13, 1495, 258, 1779, $lightgray); # Greenhouse #4

	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 18;
	$text = 'To TLS Bldg';
	imagettftext($dest_image, $fontsize, 0,50,1808, $black, $font, $text);

###############################################################################
	### Bench Outline generation code
###############################################################################
	### Bench data is relative to southeast corner of 1300 - upper left
	### Offset is to adjust for margins, banners etc.
	$offset_x=10; 
	$offset_y=150;
	if ($showbenches==1) {
	$sql = 'select * from b_assign where map_include order by location';
	foreach($db->query($sql) as $row) {
		$orig_x=$row['map_orig_x'];
		$orig_y=$row['map_orig_y'];
		$len_x=$row['map_len_x'];
		$len_y=$row['map_len_y'];
		$line_wt=2;
		$line_color=$black;
		$fill_color=$white;	
		### Draw bench
		imagefilledrectangle($dest_image, $orig_x+$offset_x, $orig_y+$offset_y, $orig_x+$len_x+$offset_x, $orig_y+$len_y+$offset_y, $line_color); 
		imagefilledrectangle($dest_image, $orig_x+$offset_x+$line_wt, $orig_y+$offset_y+$line_wt, $orig_x+$len_x+$offset_x-$line_wt, $orig_y+$len_y+$offset_y-$line_wt, $fill_color); 
	} # foreach loop
	} # showbenches if

###############################################################################
	### Bench Numbering generation code
###############################################################################
	if ($showbenchlabels==1) {	
	$i=0; 
	foreach($db->query($sql) as $row) {
		$text = substr($row['location'],2,2);
		### calculate middle of bench polygon
		$orig_x=($row['map_orig_x']+($row['map_len_x']/2))+$offset_x;
		$orig_y=($row['map_orig_y']+($row['map_len_y']/2))+$offset_y;
		$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
		$fontsize = 12;
		### calculate bounding box for centering
		$tb = imagettfbbox($fontsize, 0, $font, $text);
		$orig_x -= (abs($tb[4] - $tb[0])/2);
		$orig_y += (abs($tb[5] - $tb[1])/2);
		imagettftext($dest_image, $fontsize, 0,$orig_x,$orig_y, $black, $font, $text);
	} # foreach loop
	} # showbenchlabels if


###############################################################################
	### Zone only code
###############################################################################
if ($zonelabels==1) {
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 48;
	imagettftext($dest_image, $fontsize, 0,180,325, $textgray, $font, '1300');
	imagettftext($dest_image, $fontsize-12, 0,100,400, $textgray, $font, 'Neotropic A');
	imagettftext($dest_image, $fontsize, 0,180,725, $textgray, $font, '1200');
	imagettftext($dest_image, $fontsize-12, 0,100,800, $textgray, $font, 'Asian Tropic');
	imagettftext($dest_image, $fontsize, 0,180,1125, $textgray, $font, '1100');
	imagettftext($dest_image, $fontsize-12, 0,100,1200, $textgray, $font, 'African Tropic');

	imagettftext($dest_image, $fontsize, 0,720,325, $textgray, $font, '2300');
	imagettftext($dest_image, $fontsize-12, 0,670,400, $textgray, $font, 'Temperate');
	imagettftext($dest_image, $fontsize, 0,720,725, $textgray, $font, '2200');
	imagettftext($dest_image, $fontsize-12, 0,725,800, $textgray, $font, 'Desert');
	imagettftext($dest_image, $fontsize, 0,720,1125, $textgray, $font, '2100');
	imagettftext($dest_image, $fontsize-12, 0,625,1200, $textgray, $font, 'Mediterranean');

	imagettftext($dest_image, $fontsize, 0,1200,325, $textgray, $font, '3300');
	imagettftext($dest_image, $fontsize-12, 0,1130,400, $textgray, $font, 'Neotropic B');
	imagettftext($dest_image, $fontsize, 0,1200,725, $textgray, $font, '3200');
	imagettftext($dest_image, $fontsize-12, 0,1200,800, $textgray, $font, 'Orchid');
	imagettftext($dest_image, $fontsize, 0,1200,1125, $textgray, $font, '3100');
	imagettftext($dest_image, $fontsize-12, 0,1150,1200, $textgray, $font, 'Fern Room');

	imagettftext($dest_image, $fontsize, 0,50,1600, $textgray, $font, '4100');
	imagettftext($dest_image, $fontsize-12, 0,30,1675, $textgray, $font, 'Epiphyte');

	imagettftext($dest_image, $fontsize, 0,525,1450, $textgray, $font, '1000: Headhouse');
	} # zonelabels
###############################################################################
return $dest_image;
}
?>
