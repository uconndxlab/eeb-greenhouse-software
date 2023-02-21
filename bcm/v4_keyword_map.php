<?php
function v4_keyword_map($keyword,$title)
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
#$keyword='endangered-critical'; ## temporary for testing

$sql='select b_assign.location,b_assign.map_orig_x,b_assign.map_orig_y,b_assign.map_len_x,b_assign.map_len_y,count(gh_inv.codeno) as count from b_assign,gh_inv ';
$sql .= 'where gh_inv.projnum="GEN_COLL" and gh_inv.location=b_assign.location ';
$sql .= 'and gh_inv.keywords like "% '.$keyword.' %"';
#$sql .= 'and gh_inv.signage > 00000000';

$sql .= ' and gh_inv.location<5000'; # exclude garden & BPB
$sql .= ' group by location order by location';

$sum=0;
### Generate Base Image
### passed flags: zonelabels,showbenches,showbenchlabels
$dest_image = v4_mapmaker_bench(0,1,1);	
$white = imagecolorallocate($dest_image, 255, 255, 255);
foreach($db->query($sql) as $row) {
	### calculate middle of bench polygon
	$orig_x = ($row['map_orig_x']+($row['map_len_x']/2))+$offset_x;
	$orig_y=($row['map_orig_y']+($row['map_len_y']/2))+$offset_y;
	$logoImage = imagecreatefrompng("/var/www/images/leaf-40.png");
	imagecopy($dest_image,$logoImage,$orig_x-20,$orig_y-20,0,0,40,40);
	### Overlay count
	$acc_count = $row['count'];
	$text = $acc_count;
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$fontsize = 12;
	### calculate bounding box for centering
	$tb = imagettfbbox($fontsize, 0, $font, $text);
	$orig_x -= (abs($tb[4] - $tb[0])/2);
	$orig_y += (abs($tb[5] - $tb[1])/2);
	imagettftext($dest_image, $fontsize, 0,$orig_x,$orig_y-4, $white, $font, $text);
	$sum = $sum + $acc_count;
} # foreach

### Write Image Title and Date in lower header
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$sql = 'select title from keywords where keyword like "%'.$keyword.'%"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$text = $sum.' accession locations for '.$result['title'];
	imagettftext($dest_image, 32, 0,60,1920, $white, $font, $text);
	$text = 'Number represents total accessions in that location';
	imagettftext($dest_image, 18, 0,120,1955, $white, $font, $text);
	$text = 'Map updated on: '.date('l, F jS Y, h:i A');
	imagettftext($dest_image, 18, 0,120,1980, $white, $font, $text);

### Write Image formats and destroy image resource to free memory
	echo 'Writing '.$imagedir.'maps/bench/keyword_'.$keyword.'_map.png'.chr(10);
	imagepng($dest_image,$imagedir.'maps/keywords/keyword_'.$keyword.'_map.png');
	imagedestroy($dest_image);
#}

$db = null;
return true;
}
?>
