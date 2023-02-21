<?php
### Generate HTML Page of plants indicated as 'flowering' in the past week ###

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10);	
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'inflower.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10).'<head>';
$status = fwrite($accfile,$strout);

$strout = '<title>UConn Biodiversity Conservatory & Research Greenhouses - Currently Blooming</title>';
$status = fwrite($accfile,$strout);

### CREATE META TAGS

$strout = '<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" >';
$status = fwrite($accfile,$strout);

$sql = 'set sql_mode=""';
$sth = $db->prepare($sql);
$sth->execute();

### RETRIEVE BOILERPLATE HTML
$sql = 'select head,body from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$strout = $result['head'].chr(10);
$status = fwrite($accfile,$strout);
$strout = $result['body'].chr(10);
$status = fwrite($accfile,$strout);

$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
$strout=$strout.'<div class="row" id="row1">'.chr(10);
#$strout=$strout.'<div class="row" id="row2">'.chr(10);

$strout=$strout.'<div id="home3" class="span4" role="complementary">'.chr(10);
$strout=$strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout=$strout.'<h2 class="widget-title">Currently Blooming in the Greenhouse:<br>'.date("l, F jS, Y").'</h2>'.chr(10);		
$strout=$strout.'<div id="col_holdings" class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

### GENERATE COMPLETE BLOOM LISTING 

$strout = '<div id="incl_fam" class="container">';
$status = fwrite($accfile,$strout);

### Count Families in bloom
$sql = 'select distinct classify.family';
$sql .= ' FROM history inner join gh_inv on history.codeno=gh_inv.codeno';
$sql .= ' inner join classify on gh_inv.genus=classify.genus';
$sql .= ' where history.class="FLOWERING" and gh_inv.projnum="GEN_COLL"';
$sql .= ' and history.date >= DATE_SUB(CURDATE(),INTERVAL 15 DAY)';
$i = 0;
foreach($db->query($sql) as $row) {
	$i++;
} # foreach

$sql = 'select distinct history.codeno, gh_inv.latin_name,classify.family';
$sql .= ' FROM history inner join gh_inv on history.codeno=gh_inv.codeno';
$sql .= ' inner join classify on gh_inv.genus=classify.genus';
$sql .= ' where history.class="FLOWERING" and gh_inv.projnum="GEN_COLL"';
$sql .= ' and history.date >= DATE_SUB(CURDATE(),INTERVAL 15 DAY) order by classify.family,gh_inv.latin_name';
$num = 0;
foreach($db->query($sql) as $row) {
	$num++;
} # foreach

$strout = '<h4>'.$num.' accessions in '.$i.' families</h4>'.chr(10);
$status = fwrite($accfile,$strout);

$tempfam="";

foreach($db->query($sql) as $row) {
	if ($tempfam<>$row['family']) {
		$strout='<h3>'.$row['family'].'</h3>'.chr(10);
		$status = fwrite($accfile,$strout);
	}
	$strout='<a href="'.$row['codeno'].'.html"><i>'.$row['latin_name'].'</i></a>';
	$status = fwrite($accfile,$strout);
	### check for images
	$imagemask=$imagedir.'byspecies/thumb/'.strtr($row['latin_name']," ","_").'*.jpg';
	$imagearray=glob($imagemask);
	if (count($imagearray)>0){
		$strout='<img src="/images/smallcamera.gif"></img> ';
		$status = fwrite($accfile,$strout);
	}	
	### check for TDWG map
	$imagemask=$imagedir.'maps/tdwg/'.$row['codeno'].'.jpg';
	$imagearray=glob($imagemask);
	if (count($imagearray)>0){
		$strout=' <img src="/images/globe-18.png"></img>';
		$status = fwrite($accfile,$strout);
	}	
	$strout='<br>'.chr(10);
	$status = fwrite($accfile,$strout);
	$tempfam = $row['family'];

} # foreach

$strout = '<p>&nbsp<p><img src="/images/smallcamera.gif"></img> = Images available';
$strout .= '<br><img src="/images/globe-18.png"></img> = Map available';
$status = fwrite($accfile,$strout);

$strout = '<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout .= '<div id="home3" class="span6" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">Blooming this Week:</h2>'.chr(10);			
$strout .= '<div class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '<center><div id="photo">'.chr(10); 
$status = fwrite($accfile,$strout);
$strout = '<div id="slider">'.chr(10); 
$status = fwrite($accfile,$strout);

### INSERT FLOWERING SLIDER HERE
### IMAGES should have a default size of 470x250px
###   FIND ALL IMAGES Xx00.jpg FOR IN FLOWER STATUS

$i = 0;
$i2 = 0;
foreach($db->query($sql) as $row) {
	$namestr = strtr($row['latin_name']," ","_");
	$imagemask = $imagedir.'byspecies/'.$namestr.'00.jpg';
	if (file_exists($imagemask)) {
		$slidearray[$i2][0]= $imagemask;
		$slidearray[$i2][1]= $row['codeno'];
		$slidearray[$i2][2]= $row['latin_name'];
		$i2++;
	}
} # foreach
$i=0;
shuffle($slidearray);
while ($i<$i2)	 {
		$strout = '<a href="http://florawww.eeb.uconn.edu/'.$slidearray[$i][1].'.html">';	
		$status = fwrite($accfile,$strout);
		$strout = preg_replace('#/var/www/images/byspecies/#','<img src="http://florawww.eeb.uconn.edu/images/byspecies/',$slidearray[$i][0]);
		$strout = $strout.'" alt="'.$slidearray[$i][2].' blooming this week" /> </a>'.chr(10);
		$status = fwrite($accfile,$strout);
		$i++;
		}
echo $i.' images in slideshow'.chr(10);			
	
$strout = '</div><div></center><hr>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</center></div></div>'.chr(10);
$status = fwrite($accfile,$strout);


$strout = '</div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select foot from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$strout = $result['foot'].chr(10);
$status = fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);
$db = null;
return true;

?> 

