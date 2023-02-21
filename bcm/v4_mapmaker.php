<?php
function v4_mapmaker($codeno)
{
### takes codeno as command line argument (when standalone)
###   creates PNG origin map & 470px JPG thumbnail version
###   verifies TDWG overlays exist for all LEVEL 4 zones
###
### color reference RGB values
###   base map - blue 70 130 180
###   base map - orange 220 80 10 

echo chr(10);
include '/var/www/bcm/credentials.php';
# for standalone use
#parse_str(implode('&', array_slice($argv, 1)), $_GET);
#$codeno = $_GET['codeno']; 

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}

$sql = 'select gh_inv.latin_name,gh_inv.projnum,gh_inv.genus,gh_inv.species,gh_inv.commonname,gh_inv.cntry_orig,classify.family,gh_inv.tdwg from gh_inv,classify ';
$sql .= 'where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$i=0;
$name = $result['genus'].' '.$result['species'];
### parse tdwg data into array
$tdwg=str_word_count($result['tdwg'],1);
$numtdwg=count($tdwg);
if ($result['projnum']<>"GEN_COLL") $numtdwg=0; ### Force skip if not general collections
if ($numtdwg>0) { ### skip whole process if no valid TDWG data found
	echo 'Creating Base Image for '.$codeno.' - '.$name.chr(10);
	$overlay = imagecreatefrompng($imagedir."maps/tdwg_templates/level3base.png");
	$width = imagesx($overlay);
	$height = imagesy($overlay);

	$dest_image = imagecreatetruecolor($width, $height);
#	$trans_colour = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
	$trans_colour = imagecolorallocate($dest_image, 255, 255, 255);
	imagefill($dest_image, 0, 0, $trans_colour);
	imagecopyresampled($dest_image, $overlay, 0, 0, 0, 0, $width, $height, $width, $height);
#	imagesavealpha($dest_image, true); # disable alpha channel for testing (Google Chrome renders black)

###################################
	### Get overlays - LOOP HERE
###################################
	$i=0;
	### 5 digit TDWG > 3 DIGIT, then remove duplicates
	while ($i < $numtdwg) {
		$tdwg[$i]=substr($tdwg[$i],0,3);
		$i++;
	}
	$tdwg=array_unique($tdwg);
	$tdwg=array_values($tdwg);
	$numtdwg=count($tdwg); ### recount array
	$i=0;### reset counter
	$mapcomplete=true;
	#echo print_r($tdwg); ### debugging use
	while ($i < $numtdwg) {
		### ignore lower case entries
		if (strtoupper($tdwg[$i]) == $tdwg[$i]) {
			### does template file exist?
			$imagemask=$imagedir.'maps/tdwg_templates/'.$tdwg[$i].'.png';
			if (file_exists($imagemask)) {
				echo 'Adding overlay for '.$tdwg[$i].chr(10);
				$overlay = imagecreatefrompng($imagedir.'maps/tdwg_templates/'.$tdwg[$i].'.png');
				$width = imagesx($overlay);
				$height = imagesy($overlay);
				imagecopyresampled($dest_image, $overlay, 0, 0, 0, 0, $width, $height, $width, $height);
			} else {
				echo 'Overlay '.$tdwg[$i].' DOES NOT EXIST'.chr(10);
		#		$mapcomplete=false;
			}
		} # end upper case check
		$i++;
	} # end while loop



#################################

#################################
	### create 470px jpg
	$dest_thumb = imagecreatetruecolor(470, 260);
	$trans_colour = imagecolorallocatealpha($dest_thumb, 255, 255, 255, 0);
	imagefill($dest_thumb, 0, 0, $trans_colour);
	imagecopyresampled($dest_thumb, $dest_image, 0, 0, 250, 120, 470, 260, 2870, 1614);
#	$banner = imagecreatefrompng($imagedir.'maps/tdwg_templates/uconn-wordmark-stacked-blue.png');
#	imagecopyresampled($dest_thumb, $banner, 385, 235, 0, 0, 84, 25, 84, 25);
	if ($mapcomplete) { 
		echo 'Writing 470px x 260px JPG image...'.chr(10);
		imagejpeg($dest_thumb,$imagedir.'maps/tdwg/'.$codeno.'.jpg');
	} # mapcomplete check
#################################
	### Add information elements, header etc to main image
	echo 'Adding Banner Information to PNG image'.chr(10);
	### Add UConn Banner to image
	$blue = imagecolorallocate($dest_image, 0, 14, 47);
	imagefilledrectangle($dest_image, 0, 1659, 3199, 1799, $blue);
	imagefilledrectangle($dest_image, 0, 0, 3199, 140, $blue);
	$banner = imagecreatefrompng($imagedir.'maps/tdwg_templates/uconn_banner_blue.png');
	imagecopyresampled($dest_image, $banner, 25, 10, 0, 0, 976, 120, 488, 60);

#	### Draw Blue Box for Species Information
#	$ltblue = imagecolorallocate($dest_image, 70, 130, 180);
#	imagefilledrectangle($dest_image, 20,800, 699, 810, $ltblue);
#	imagefilledrectangle($dest_image, 20,800, 30, 1639, $ltblue);
#	imagefilledrectangle($dest_image, 20,1629, 699, 1639, $ltblue);	
#	imagefilledrectangle($dest_image, 689,800, 699, 1639, $ltblue);

#	### Add EEB Banner in box
#	$overlay = imagecreatefrompng($imagedir.'maps/tdwg_templates/eeb_banner.png');
#	imagecopy($dest_image, $overlay, 31, 811, 0, 0, 658, 60);

	### Place Thumbnail Image if available
	$namestr=strtr($name," ","_"); ### stripping out problem characters
	$namestr=str_replace('-', "_", $namestr);
	$namestr=str_replace('"', "", $namestr);
	$namestr=str_replace("'", "", $namestr);
	$imagemask=$imagedir.'byspecies/'.$namestr.'00.jpg';
	if (file_exists($imagemask)) {
		$overlay = imagecreatefromjpeg($imagemask);
		imagecopyresampled($dest_image, $overlay, 31, 1280, 0, 0, 658, 350, 470, 250);
		### Narrow Blue Border if image used
		imagefilledrectangle($dest_image, 31,1280, 689, 1270, $blue);
		imagefilledrectangle($dest_image, 31,1280, 41, 1629, $blue);
		imagefilledrectangle($dest_image, 31,1619, 689, 1629, $blue);	
		imagefilledrectangle($dest_image, 679,1270, 689, 1629, $blue);	
	}

	### Add Latin Name Text on Banner
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 36;
	$white = imagecolorallocate($dest_image, 255, 255, 255);
	$text = 'Native range of '.$name;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((2200 - $tb[2]) / 2)+1000; ## lower left X coordinate for text
	imagettftext($dest_image, $fontsize, 0, $x, 90, $white, $font, $text);

	### Add Reference URL Text on Footer
	$text = 'Please visit http://florawww.eeb.uconn.edu/'.$codeno.'.html for more information on data sources used to generate this map graphic.'; 
	$fontsize = 30;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$x = ceil((3200 - $tb[2]) / 2); ## lower left X coordinate for text
	imagettftext($dest_image, $fontsize, 0, $x, 1749, $white, $font, $text);

#################################
	### write finished image
	if ($mapcomplete) {
		echo 'Writing 3200px x 1800px PNG image...'.chr(10);
		imagepng($dest_image,$imagedir.'maps/tdwg/'.$codeno.'.png');
		echo '<hr>'.$imagedir.'maps/tdwg/'.$codeno.'.png<hr>';
	} # mapcomplete check
	### clean up
	imagedestroy($dest_image);
	imagedestroy($dest_thumb);
	imagedestroy($overlay);
	### set update flag so accession will get updated when map is created/changed
	$sql = 'update gh_inv set tempflag=1 where codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
	if ($mapcomplete) {
		echo 'Map Creation Process Complete'.chr(10);
		} else {
		echo 'Map Creation Process Incomplete - files not written'.chr(10);
	} # mapcomplete check
	} else {
	echo 'No TDWG data available for '.$name.' - aborting map creation process...'.chr(10);
} ### end tdwg not empty test
$db = null;
return true;
}
?>
