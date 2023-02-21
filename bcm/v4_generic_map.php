<?php
### generate blank benchmap
###   v4 updated to PDO


include_once '/var/bcm/v4_mapmaker_bench.php';
include_once '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}

### Global Variable Declarations
$offset_x=10; 
$offset_y=150;
$weeknum = date('W',(time()));

#$sql='select b_assign.location,b_assign.map_orig_x,b_assign.map_orig_y,b_assign.map_len_x,b_assign.map_len_y,';
#$sql .= 'round(avg(to_days(gh_inv.confirm))) as d1, to_days(now()) as d2, count(gh_inv.codeno) as d3';
#$sql .= ' from b_assign,gh_inv'; 
#$sql .= ' where gh_inv.projnum="GEN_COLL" and gh_inv.location=b_assign.location';
#$sql .= ' and b_assign.map_include'; 
#$sql .= ' group by b_assign.location order by b_assign.location';

$sum=0;
### Generate Base Image
### passed flags: zonelabels,showbenches,showbenchlabels
$dest_image=v4_mapmaker_bench(1,1,1);	
$white = imagecolorallocate($dest_image, 255, 255, 255);
$black = imagecolorallocate($dest_image, 0, 0, 0);

### Write Image Title and Date in lower header
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$text = 'EEB Greenhouse Bench Map';
	imagettftext($dest_image, 32, 0,60,1920, $white, $font, $text);
	$text='Map updated on: '.date('l, F jS Y, h:i A').' [bcm v4.0]';
	imagettftext($dest_image, 18, 0,120,1980, $white, $font, $text);

### Write Image formats and destroy image resource to free memory
	echo 'Writing '.$imagedir.'maps/bench_map.png'.chr(10);
	imagepng($dest_image,$imagedir.'maps/bench_map.png'); 
	imagedestroy($dest_image);

$db=null; ### close PDO object
?>
