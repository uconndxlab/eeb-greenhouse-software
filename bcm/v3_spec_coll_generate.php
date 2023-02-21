<?php
function v3_spec_coll_generate($page)
{
$user="ghstaff";
$password="argus";
$database="bcm";
date_default_timezone_set('America/New_York'); 

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

$rootdir = '/var/www/';
$imagedir = '/var/www/images/';

### CREATE OUTPUT FILE

$file_spec = $rootdir.$page.'.html';
$accfile = fopen($file_spec,'w');
### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$result=fwrite($accfile,$strout);

### GENERATE TITLE HTML

$sql = 'select * from scoll where url="'.$page.'"';
$sql_scoll_result=mysql_query($sql);
if (!$sql_scoll_result) {
	echo mysql_error();
}
$i=0;

$strout = '<TITLE>'.mysql_result($sql_scoll_result,$i,'title').'</title>'.chr(10);
$result=fwrite($accfile,$strout);

### CREATE META TAGS

$strout = '<HEAD><META NAME="description" CONTENT="'.mysql_result($sql_scoll_result,$i,'title').'">';
$result=fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
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
$strout=$strout.'<h2 class="widget-title">Special Collection:<br><b>'.chr(10);		
$result=fwrite($accfile,$strout);
### WRITE COLLECTION INTRO

$strout=mysql_result($sql_scoll_result,0,'title').'</b></h2><p>';
$result=fwrite($accfile,$strout);

$strout='<div id="col_holdings" class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);

$strout=mysql_result($sql_scoll_result,0,'intro');
$result=fwrite($accfile,$strout);

$strout='<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '</div></div></div>'.chr(10);
$result=fwrite($accfile,$strout);

### END COLLECTION INTRO

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout = $strout.'<div id="home3" class="span6" role="complementary">'.chr(10);
$strout = $strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout = $strout.'<h2 class="widget-title">Special Collection Accessions:</h2>'.chr(10);			
$strout = $strout.'<div class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);


$strout='<div class="container">'.chr(10);
$result=fwrite($accfile,$strout);

### INSERT COLLECTION SLIDER HERE

$strout='<div id="photo" class="span-12 last">'.chr(10);
$result=fwrite($accfile,$strout);
### IMAGES should have a default size of 470x250px

### BUILD SLIDER GENERATOR
###   FIND ALL IMAGES Xx00.jpg FOR GIVEN COLLECTION

$sql='select gh_inv.codeno,gh_inv.latin_name from gh_inv';
$sql=$sql.' where gh_inv.projnum="GEN_COLL"';
### The following code looks for an '=' in the fieldname
### If found, process entire string, else process fieldname as boolean field

$pos = strrpos(mysql_result($sql_scoll_result,0,'fieldname'), "=");
if ($pos === false) { 
	$sql=$sql.' and gh_inv.'.mysql_result($sql_scoll_result,0,'fieldname');
	}else{
	$sql=$sql.' and '.mysql_result($sql_scoll_result,0,'fieldname');
}
$sql=$sql.' group by latin_name';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$i=0;
$strout = '<div id="slider" class="span-12 last">';
$result=fwrite($accfile,$strout);
while ($i<$num) {
	$namestr=strtr(mysql_result($sql_result,$i,'latin_name')," ","_");
	$imagemask=$imagedir.'byspecies/'.$namestr.'00.jpg';
	if (file_exists($imagemask)) {
		$strout='<a href="'.mysql_result($sql_result,$i,'codeno').'.html">';	
		$result=fwrite($accfile,$strout);
		$strout=preg_replace('#/var/www/images/byspecies/#','<img src="http://florawww.eeb.uconn.edu/images/byspecies/',$imagemask);
		$strout = $strout.'" alt="'.mysql_result($sql_result,$i,'latin_name').'" /></a>'.chr(10);
		$result=fwrite($accfile,$strout);
		}
		$i++;
	}
$strout = '</div>';
$result=fwrite($accfile,$strout);
}
$strout = '</div></div>'.chr(10);
$result=fwrite($accfile,$strout);

### INSERT SPECIAL COLLECTION SPECIES LIST


$strout='<p><hr><p>'.chr(10).'<ul>';
$result=fwrite($accfile,$strout);

$sql='select gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.projnum,gh_inv.wildcoll,classify.family from gh_inv,classify';
$sql=$sql.' where gh_inv.genus=classify.genus and (gh_inv.projnum="GEN_COLL" or gh_inv.projnum="WISHLIST")';
### The following code looks for an '=' in the fieldname
### If found, process entire string, else process fieldname as boolean field

$pos = strrpos(mysql_result($sql_scoll_result,0,'fieldname'), "=");
if ($pos === false) { 
	$sql=$sql.' and gh_inv.'.mysql_result($sql_scoll_result,0,'fieldname');
	}else{
	$sql=$sql.' and '.mysql_result($sql_scoll_result,0,'fieldname');
}

$sql=$sql.' order by projnum,latin_name';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$i=0;
while ($i<$num) {
	$projnum=mysql_result($sql_result,$i,'projnum');
	### check each accession for recent flowering (10 days)
		
	$sql='SELECT codeno FROM history where history.codeno='.$codeno=mysql_result($sql_result,$i,'codeno').' and history.class="FLOWERING" and history.date >= DATE_SUB(CURDATE(),INTERVAL 10 DAY)';
	$sql_bloom_result=mysql_query($sql);
	if (!$sql_bloom_result) echo mysql_error();
	$bloom=mysql_numrows($sql_bloom_result);

	if ($projnum<>"WISHLIST"){
		$strout = '<li>';
		### add icon if blooming
					
		if ($bloom <> 0) $strout=$strout.'<img src="/images/flower-rose.gif"></img> ';	
		$strout=$strout.'<a href = "'.mysql_result($sql_result,$i,'codeno').'.html"><i>'.mysql_result($sql_result,$i,'latin_name').'</i></a>';
		$result=fwrite($accfile,$strout);
		$temp = mysql_result($sql_result,$i,'commonname');
		if (!empty($temp)) {
			$strout = ' - <font color=purple>'.mysql_result($sql_result,$i,'commonname').'</font>';
			$result=fwrite($accfile,$strout);
		}
		$strout = ' - '.mysql_result($sql_result,$i,'family');
		$result=fwrite($accfile,$strout);
		$imagemask=$imagedir.'byspecies/thumb/'.strtr(mysql_result($sql_result,$i,'latin_name')," ","_").'*.jpg';
		$imagearray=glob($imagemask);
		$strout='';
		if (count($imagearray)>0) $strout='<img src="/images/smallcamera.gif"></img>';
		$strout=$strout.chr(10);		
		$result=fwrite($accfile,$strout);
		$temp = mysql_result($sql_result,$i,'wildcoll');	
		if ($temp) {
			$strout = ' <font color="GREEN">W/C</font>';
			$result=fwrite($accfile,$strout);
		}		
	} else {
		$strout = '<li><font color=gray><I>WISHLIST ITEM: <i>'.mysql_result($sql_result,$i,'latin_name').'</i>';
		$result=fwrite($accfile,$strout);
		$temp = mysql_result($sql_result,$i,'commonname');
		if (!empty($temp)) {
			$strout = ' - '.mysql_result($sql_result,$i,'commonname');
			$result=fwrite($accfile,$strout);
		}
		$strout = ' - '.mysql_result($sql_result,$i,'family').'</i></font>'.chr(10);
		$result=fwrite($accfile,$strout);
	}
	$i++;
}
}

$strout='</ul><br><font color="GREEN">W/C</font> = <i>Wild Collected</i><br> <img src="/images/flower-rose.gif"></img> = Currently Flowering';
$strout=$strout.'<br><img src="/images/smallcamera.gif"></img> = Image(s) Available'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '</div></div></div></div></div></div>'.chr(10);
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

### CLOSE OUTPUT FILE

mysql_close($rs);
return true;
}
?>