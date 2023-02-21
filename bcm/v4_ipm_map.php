<?php
### generate ipm heatmap
###   bench coloration to denote review status
###   icons to denote pest notations
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

$sql='select b_assign.location,b_assign.map_orig_x,b_assign.map_orig_y,b_assign.map_len_x,b_assign.map_len_y,';
$sql .= 'round(avg(to_days(gh_inv.confirm))) as d1, to_days(now()) as d2, count(gh_inv.codeno) as d3';
$sql .= ' from b_assign,gh_inv'; 
$sql .= ' where gh_inv.projnum="GEN_COLL" and gh_inv.location=b_assign.location';
$sql .= ' and b_assign.map_include'; 
$sql .= ' group by b_assign.location order by b_assign.location';

$sum=0;
### Generate Base Image
### passed flags: zonelabels,showbenches,showbenchlabels
$dest_image=v4_mapmaker_bench(0,0,0);	
$white = imagecolorallocate($dest_image, 255, 255, 255);
$black = imagecolorallocate($dest_image, 0, 0, 0);
$blue = imagecolorallocate($dest_image, 0, 0, 255);
$bugImage = imagecreatefrompng("/var/www/images/bug20.png");
$checkImage = imagecreatefromgif("/var/www/images/checkmark.gif");
$sprayImage = imagecreatefrompng("/var/www/images/spray_trans.png");
$signImage = imagecreatefrompng("/var/www/images/icons/signs-20_ON.png");

foreach($db->query($sql) as $row) {
	$avg_age=$row['d2']-$row['d1'];
	###############################################################################
	### Bench Outline generation code
	###############################################################################
	### Bench data is relative to southeast corner of 1300 - upper left
	### Offset is to adjust for margins, banners etc.
	$offset_x=10; 
	$offset_y=150;
	echo 'Drawing bench '.$row['location'].', Average age: '.$avg_age;
	$orig_x=$row['map_orig_x'];
	$orig_y=$row['map_orig_y'];
	$len_x=$row['map_len_x'];
	$len_y=$row['map_len_y'];
	$line_wt=2;
	$line_color=$black;
	### calculate R & G values on sliding scale between 0 & 28
	$r=255;
	$g=255;
	if ($avg_age < 14){ 
		$r=round(($avg_age*18.21));
		$g=255;
	}
	if ($avg_age > 14) {
		$g=round($g-(($avg_age-15)*18.21));
		$r = 255;
	}
	if ($r>255) $r=255;
	if ($g>255) $g=255;
	if ($r<0) $r=0;
	if ($g<0) $g=0;
	$fill_color = imagecolorallocate($dest_image,$r,$g,0);
	### Draw bench
	imagefilledrectangle($dest_image, $orig_x+$offset_x, $orig_y+$offset_y, $orig_x+$len_x+$offset_x, $orig_y+$len_y+$offset_y, $line_color); 
	imagefilledrectangle($dest_image, $orig_x+$offset_x+$line_wt, $orig_y+$offset_y+$line_wt, $orig_x+$len_x+$offset_x-$line_wt, $orig_y+$len_y+$offset_y-$line_wt, $fill_color); 

	###############################################################################
	### Bench Numbering generation code
	###############################################################################
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


	###############################################################################
	### Current Week Notations - Scouting/Inventory/Flowering
	###############################################################################
	$orig_x=$row['map_orig_x']+$offset_x;
	$orig_y=$row['map_orig_y']+$offset_y;
	$len_x=$row['map_len_x'];
	$len_y=$row['map_len_y'];
	### check for history notations this week

#	### CONFIRMATIONS TODAY
#	$sql = 'select count(codeno) as count_confirm from gh_inv where projnum="GEN_COLL"';
#	$sql .= ' and location='.$row['location'];
#	$sql .= ' and confirm = curdate()';
#	$sql .= ' and week(confirm,3) = '.$weeknum; 
#	foreach($db->query($sql) as $test) {
#		if ($test['count_confirm'] > 0) imagecopy($dest_image,$checkImage,$orig_x+3,$orig_y+3,0,0,15,15);
#	} # foreach conf

#	### SPRAYED TODAY
#	$sql = 'select count(codeno) as count_spray from history where history.class="SPRAY"';
#	$sql .= ' and zone='.$row['location'];
#	$sql .= ' and date = curdate()';
#	foreach($db->query($sql) as $spray) {
#		if ($spray['count_spray'] > 0) imagecopy($dest_image,$sprayImage,$orig_x+$len_x-22,$orig_y+$len_y-22,0,0,20,20);
#	} # foreach conf

	### Pending Scout (14-28 day) and flowering
### NOT FUNCTIONAL - PARTIAL PROGRAMMING
	$sql = 'set sql_mode=""'; # turn off only full group by
	$sth = $db->prepare($sql);
	$sth->execute();
	### tally last weeks flowering notations yet to be checked this week
	$sql = 'select count(recno),week(date,3),gh_inv.confirm from history,gh_inv where class="FLOWERING"';
	$sql .= ' and history.codeno=gh_inv.codeno';
	$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';  # within the past year
	$sql .= ' and week(history.date,3)=(week(curdate(),3)-1)';
	$sql .= ' and week(gh_inv.confirm,3) = (week(curdate(),3)-1)';
	$sql .= ' and history.zone='.$row['location'];
	$sql .= ' group by history.codeno';
	$sth = $db->prepare($sql);
	$sth->execute();
	$benchtally = $sth->rowCount();
	### tally pest or biocontrol notations between 14 & 28 days old with no current week confirmation
	$sql = 'select count(recno),week(date,3),gh_inv.confirm from history,gh_inv where (class="SCOUT" or class="BIOCONTROL")';
	$sql .= ' and history.codeno=gh_inv.codeno';
	$sql .= ' and history.date>date_sub(curdate(),interval 29 day)';  
	$sql .= ' and history.date<date_sub(curdate(),interval 13 day)';  
	$sql .= ' and week(gh_inv.confirm,3) <> week(curdate(),3)';
	$sql .= ' and history.zone='.$row['location'];
	$sql .= ' group by history.codeno';
	$sth = $db->prepare($sql);
	$sth->execute();
	$benchtally = $benchtally + $sth->rowCount();

	if ($benchtally>0) {
		$text=$benchtally;
		$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
		$fontsize = 9;
		imagettftext($dest_image, $fontsize, 0,$orig_x+$len_x-12,$orig_y+$len_y-6, $blue, $font, $text);
	} # if rowCount


#	### SIGNAGE COUNT
#	$sql = 'select count(codeno) as count_sign from gh_inv where gh_inv.signage';
#	$sql .= ' and gh_inv.location='.$row['location'];
#	foreach($db->query($sql) as $sign) {
#		if ($sign['count_sign'] > 0) {
#			#imagecopy($dest_image,$signImage,$orig_x+$len_x-22,$orig_y+$len_y-22,0,0,20,20);
#			$text=$sign['count_sign'];
#			$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
#			$fontsize = 9;
#			imagettftext($dest_image, $fontsize, 0,$orig_x+$len_x-12,$orig_y+$len_y-6, $blue, $font, $text);
#		} # if signcount
#	} # foreach sign

#	### PEST STATUS ICONS
#	### Pest older than 13 day and less than 29 days
#	$sql = 'select codeno,notes,class,max(unix_timestamp(date)) as lastdate from history where class="SCOUT"';
#	$sql = $sql.' and history.zone='.$row['location'];
#	$sql = $sql.' and history.date>date_sub(curdate(),interval 30 day)';
#	$sql = $sql.' group by notes';
#	$rescoutcount=0; 
#	foreach($db->query($sql) as $rescout) {
#		$daysago=intval((time()-$rescout['lastdate'])/86400);
#		if ($daysago < 14 ) {
#			$rescoutcount++;
#			# echo ' -- '.$rescout['codeno'].' '.$rescout['notes'].chr(10); ### DEBUGGING
#		}
#	} # foreach rescout
#	if ($rescoutcount > 3) imagecopy($dest_image,$bugImage,$orig_x+$len_x-18,$orig_y+3,0,0,15,20);
#	echo ' Pest Recounts:'.$rescoutcount;
#	### END PEST STATUS ICONS ###
	echo chr(10);
	} # foreach loop

### Write LEGEND
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$text = 'Map Legend';
	imagettftext($dest_image, 18, 0,300,1530, $black, $font, $text);
#	imagecopy($dest_image,$checkImage,300,1545,0,0,15,15);
#	$text='Database Activity Today';
#	imagettftext($dest_image, 12, 0,320,1560, $black, $font, $text);
#	imagecopy($dest_image,$bugImage,300,1575,0,0,15,20);
#	$text='More than 3 pest notations in past 14 days';
#	imagettftext($dest_image, 12, 0,320,1590, $black, $font, $text);
	$text='Flowering or Scouting Notations to be checked this week';
	imagettftext($dest_image, 9, 0,300,1620, $blue, $font, $text);
#	imagecopy($dest_image,$sprayImage,300,1605,0,0,20,20);
#	$text='Sprayed Today - signed only if required by WPS';
#	imagettftext($dest_image, 12, 0,320,1620, $black, $font, $text);

### Write Image Title and Date in lower header
	$font = '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf';
	$text = 'Inventory Status Map as of '.date('l, F jS Y, h:i A');
	imagettftext($dest_image, 24, 0,60,1920, $white, $font, $text);
#	$text='Map updated on: '.date('l, F jS Y, h:i A').' [bcm v4.0]';
#	imagettftext($dest_image, 18, 0,120,1980, $white, $font, $text);

### Write Image formats and destroy image resource to free memory
	echo 'Writing '.$imagedir.' '.chr(10);
	imagepng($dest_image,$imagedir.'maps/ipm_map.png'); 
	imagedestroy($dest_image);

$db=null; ### close PDO object
?>
