<?php
### this version prints a map keyed to a list of species
function v4_keyword_maplist($keyword,$title)
{
include_once '/var/bcm/v4_mapmaker_bench.php';
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Global Variable Declarations
$offset_x=10; 
$offset_y=150;

$sql = 'select gh_inv.latin_name,gh_inv.location,b_assign.map_index,b_assign.map_orig_x,b_assign.map_orig_y,b_assign.map_len_x,b_assign.map_len_y from gh_inv,b_assign';
$sql .= ' where gh_inv.projnum="GEN_COLL" and gh_inv.location=b_assign.location';
$sql .= ' and gh_inv.keywords like "% '.$keyword.' %"';
$sql .= ' and gh_inv.location<5000'; # exclude garden & BPB
$sql .= ' order by location, latin_name';

$listindex = 0;
$location = 0;
$sum = 0;
### parse through and assign a location index
	### clear map_index first
	$sqlb = 'update b_assign set map_index = 0';
	$sthb = $db->prepare($sqlb);
	$sthb->execute();
	foreach($db->query($sql) as $row) {
	if ($row['location']<>$location) $listindex++;
	$location = $row['location'];
	### store listindex in b_assign for later use in generating the html plant list
	$sqlb = 'update b_assign set map_index = '.$listindex.' where location = '.$location;
	#echo $sqlb.chr(10);
	$sthb = $db->prepare($sqlb);
	$sthb->execute();
	#echo 'maplist:'.$row['latin_name'].' - '.$location.' : '.$row['map_index'].chr(10);
}  
### Generate Base Image
### passed flags: zonelabels,showbenches,showbenchlabels
$dest_image = v4_mapmaker_bench(1,1,0);	
$white = imagecolorallocate($dest_image, 255, 255, 255);
foreach($db->query($sql) as $row) {
	### calculate middle of bench polygon
	$orig_x = ($row['map_orig_x']+($row['map_len_x']/2))+$offset_x;
	$orig_y=($row['map_orig_y']+($row['map_len_y']/2))+$offset_y;
	$logoImage = imagecreatefrompng("/var/www/images/leaf-40.png");
	imagecopy($dest_image,$logoImage,$orig_x-20,$orig_y-20,0,0,40,40);
	### Overlay listindex
	$text = $row['map_index'];
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 12;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$orig_x -= (abs($tb[4] - $tb[0])/2);
	$orig_y += (abs($tb[5] - $tb[1])/2);
	imagettftext($dest_image, $fontsize, 0,$orig_x,$orig_y-4, $white, $font, $text);
	$sum++;
	#$listindex++;
	#print_r($row);
	echo $row['latin_name'].' - '.$row['map_index'].' : '.$row['location'].chr(10);
} # foreach



### Write Image Title and Date in lower header
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$sql = 'select title from keywords where keyword like "%'.$keyword.'%"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$text = $sum.' accession locations for '.$result['title'];
	imagettftext($dest_image, 32, 0,60,1920, $white, $font, $text);
#	$text = 'Number correlates to species listing on associated web page';
#	imagettftext($dest_image, 18, 0,120,1955, $white, $font, $text);
	$text = 'Map updated on: '.date('l, F jS Y, h:i A');
	imagettftext($dest_image, 18, 0,120,1980, $white, $font, $text);
### Write Image formats and destroy image resource to free memory
	echo 'Writing '.$imagedir.'maps/bench/keyword_'.$keyword.'_maplist.png'.chr(10);
	imagepng($dest_image,$imagedir.'maps/keywords/keyword_'.$keyword.'_maplist.png');
	imagedestroy($dest_image);
#}

$db = null;
return true;
}
?>
