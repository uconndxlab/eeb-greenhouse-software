<?php
### Generate Slideshow of all images in /var/www/images/byspecies/ ###
include '/var/www/bcm/credentials.php';

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

### CREATE OUTPUT FILE

$file_spec = $rootdir.'imageshow.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$result=fwrite($accfile,$strout);

$strout='<title>EEB Greenhouse Collection Statistics and Recent Accessions</title><head>';
$result=fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$i=0;
$strout = mysql_result($sql_result,$i,'head').chr(10);
$result=fwrite($accfile,$strout);
$strout = mysql_result($sql_result,$i,'body').chr(10);
$result=fwrite($accfile,$strout);

$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
$strout=$strout.'<div class="row" id="row1">'.chr(10);
#$strout=$strout.'<div class="row" id="row2">'.chr(10);

$strout=$strout.'<div id="home3" class="span4" role="complementary">'.chr(10);
$strout=$strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout=$strout.'<h2 class="widget-title">IMAGE SHOW FOR CINDI:</h2>'.chr(10);		
$result=fwrite($accfile,$strout);
$strout='<div class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);

$strout='<hr><p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '</div></div></div>'.chr(10);
$result=fwrite($accfile,$strout);

### END COLLECTION INTRO

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout = $strout.'<div id="home3" class="span6" role="complementary">'.chr(10);
$strout = $strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout = $strout.'<h2 class="widget-title"></h2>'.chr(10);			
$strout = $strout.'<div class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);

### INSERT COLLECTION SLIDER HERE

$strout='<div id="photo">'.chr(10);
$result=fwrite($accfile,$strout);
### IMAGES should have a default size of 470x250px

$strout ='<div id="slider">'.chr(10); 
$result=fwrite($accfile,$strout);

### Get Random Banner Images

$images = glob($imagedir."byspecies/*00.jpg");
#$images = array_rand(array_flip($images),20);
#shuffle($images);

foreach ($images as $filename) {
	# Fetch codeno for URL
	
	$name=strtr($filename,"_"," ");
	echo $name.chr(10);
	$name = preg_replace('#/var/www/images/byspecies/#','',$name);
	$name = preg_replace('#00.jpg#','',$name);	
	$sql='select codeno,latin_name from gh_inv where latin_name="'.$name.'"';
	$sql_result=mysql_query($sql);
	if (!$sql_result) {
		echo mysql_error(); } else {			
	$strout='<a href="'.mysql_result($sql_result,0,'codeno').'.html">';
	$result=fwrite($accfile,$strout);	
	$strout=preg_replace('#/var/www/#','<img src="http://florawww.eeb.uconn.edu/',$filename);
	$strout = $strout.'" alt="'.$name.'" /></a>'.chr(10);
	$result=fwrite($accfile,$strout);	
	}
}

$strout='</div></div>';
$result=fwrite($accfile,$strout);

$strout='<div class="container">'.chr(10);
$result=fwrite($accfile,$strout);



$strout = '</div></div></div></div></div></div></div>'.chr(10);
$result=fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select foot from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$i=0;
$strout = mysql_result($sql_result,$i,'foot').chr(10);
$result=fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);
mysql_close($rs);
return true;
#}
?>
