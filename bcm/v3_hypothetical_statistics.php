<?php
### Generate Statistics and Recent 25 Data ###

$user="ghstaff";
$password="argus";
$database="bcm";
date_default_timezone_set('America/New_York'); 

echo 'Generating Collection Statistics Page'.chr(10);

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

$rootdir = '/var/www/clint/';
$imagedir = '/var/www/images/';
$collrank = 1001;
$space_threshold= 5000;

### CREATE OUTPUT FILE

$file_spec = $rootdir.'statistics.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$result=fwrite($accfile,$strout);

$strout='<title>Hypothetical EEB Greenhouse Collection Statistics and Recent Accessions</title><head>';
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
$strout=$strout.'<h2 class="widget-title">Hypothetical Collection Statistics as of '.date("l, F jS, Y");
$strout=$strout.'<br>Space Threshold: '.$space_threshold.' square feet</h2>'.chr(10);		
$result=fwrite($accfile,$strout);
$strout='<div class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);

# Calculate various collections statistics here

$sql='select codeno,quant,quant2,quant3 from gh_inv where gh_inv.projnum="GEN_COLL" and space_acc <='.$space_threshold;
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$colltotal=$num;
$strout='<p><B>'.$num.'</B> accessions in the General Collections<ul><li>'.chr(10);
$result=fwrite($accfile,$strout);
$i=0;
$result=0;
while ($i<$num) {
	$result=$result+mysql_result($sql_result,$i,1)+mysql_result($sql_result,$i,2)+mysql_result($sql_result,$i,3);
	echo $i.'-'.mysql_result($sql_result,$i,"codeno").': '.mysql_result($sql_result,$i,1).'-'.mysql_result($sql_result,$i,2).'-'.mysql_result($sql_result,$i,3).': ';
	echo $result.chr(10);
	$i++;
}
$strout='<b>'.$result.' </B><i>individual plants under cultivation</I>'.chr(10);
$result=fwrite($accfile,$strout);
}
### Count distinct families
$sql='select distinct classify.family from gh_inv,classify where gh_inv.projnum="GEN_COLL" and space_acc <='.$space_threshold;
$sql=$sql.' and gh_inv.genus=classify.genus';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$strout='<li><B>'.$num.'</B><i> families are represented</i>'.chr(10);
$result=fwrite($accfile,$strout);
}
### Count distinct ANGIOSPERM families
$sql='select distinct classify.family from gh_inv,classify,famcomm where gh_inv.projnum="GEN_COLL" and space_acc <='.$space_threshold;
$sql=$sql.' and gh_inv.genus=classify.genus and classify.family=famcomm.family and famcomm.division="Magnoliophyta"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$strout='<ul><li>'.$num.'<i> angiosperm families are represented</i></ul>'.chr(10);
$result=fwrite($accfile,$strout);
}
### Count distinct genera
$sql='select distinct classify.genus from gh_inv,classify where gh_inv.projnum="GEN_COLL" and space_acc <='.$space_threshold;
$sql=$sql.' and gh_inv.genus=classify.genus';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$strout='<li><B>'.$num.'</B><i> genera are represented</i>'.chr(10);
$result=fwrite($accfile,$strout);
}
### Count distinct ANGIOSPERM genera
$sql='select distinct classify.genus from gh_inv,classify,famcomm where gh_inv.projnum="GEN_COLL" and space_acc <='.$space_threshold;
$sql=$sql.' and gh_inv.genus=classify.genus and classify.family=famcomm.family and famcomm.division="Magnoliophyta"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$strout='<ul><li>'.$num.'<i> angiosperm genera are represented</i></ul>'.chr(10);
$result=fwrite($accfile,$strout);
}
### Wild collected

$sql='select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and wildcoll and space_acc <='.$space_threshold;
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$num=mysql_numrows($sql_result);
$strout='<li><B>'.$num;
$strout=$strout.'</B><i> are documented wild collected specimens</i></ul>'.chr(10);
$result=fwrite($accfile,$strout);
}
### IUCN Red List 2013 (variable still says 2010)

$strout='<a href="http://www.iucnredlist.org/" target="_blank"><font color="RED">IUCN Red List Status (2013):</font></a><ul>';
$result=fwrite($accfile,$strout);
$sql='select distinct redlist2010 from gh_inv where redlist2010>"" order by redlist2010';
$sql_result1=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$j=mysql_numrows($sql_result1);
$i=0;
while ($i<$j) {
	$sql='select codeno from gh_inv where projnum="GEN_COLL" and redlist2010="'.mysql_result($sql_result1,$i,'redlist2010').'" and space_acc <='.$space_threshold;
	$sql_result=mysql_query($sql);
	if (!$sql_result) {
		echo mysql_error();
	} else {
		$num=mysql_numrows($sql_result);
		$strout='<li>';
		### Link to KEYWORD files
		if (mysql_result($sql_result1,$i,'redlist2010')=='Critically Endangered') {
			$strout=$strout.'<a href="http://florawww.eeb.uconn.edu/keyword_endangered-critical.html">'.mysql_result($sql_result1,$i,'redlist2010').'</a>: <b>'.$num.'</b>'.chr(10); 		
		} elseif (mysql_result($sql_result1,$i,'redlist2010')=='Endangered'){
			$strout=$strout.'<a href="http://florawww.eeb.uconn.edu/keyword_endangered.html">'.mysql_result($sql_result1,$i,'redlist2010').'</a>: <b>'.$num.'</b>'.chr(10); 
		} elseif (mysql_result($sql_result1,$i,'redlist2010')=='Extinct in the Wild'){
			$strout=$strout.'<a href="http://florawww.eeb.uconn.edu/keyword_endangered-extinct-in-wild.html">'.mysql_result($sql_result1,$i,'redlist2010').'</a>: <b>'.$num.'</b>'.chr(10); 
		} else {
		$strout=$strout.mysql_result($sql_result1,$i,'redlist2010').': <b>'.$num.'</b>'.chr(10);
		}
		$result=fwrite($accfile,$strout);
	$i++;
	}
	}
}
$strout='</ul>';
$result=fwrite($accfile,$strout);

### CITES

$strout='<a href="http://www.cites.org/" target="_blank"><font color="green">CITES Status:</font></a>'.chr(10).'<ul>';
$result=fwrite($accfile,$strout);
$sql='select distinct cites from gh_inv where cites>"" order by cites';
$sql_result1=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
} else {
$j=mysql_numrows($sql_result1);
$i=0;
while ($i<$j) {
	$sql='select codeno from gh_inv where cites="'.mysql_result($sql_result1,$i,'cites').'" and space_acc <='.$space_threshold;
	$sql_result=mysql_query($sql);
	if (!$sql_result) {
		echo mysql_error();
	} else {
		$num=mysql_numrows($sql_result);
		$strout='<li>'.mysql_result($sql_result1,$i,'cites').': <b>'.$num.'</b>'.chr(10);
		$result=fwrite($accfile,$strout);
	$i++;
	}
	}
}
$strout='</ul><hr>';
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
$strout = $strout.'<h2 class="widget-title">Column Title</h2>'.chr(10);			
$strout = $strout.'<div class="textwidget">'.chr(10);
$result=fwrite($accfile,$strout);

### Start First Block

$strout='<div class="container">'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '<p><h4>First Block Title</h4><ul>';
$result=fwrite($accfile,$strout);

$strout = '<ul><li>First Block Text</ul>';
$result=fwrite($accfile,$strout);

$strout = '</div>';
$result=fwrite($accfile,$strout);
### End First Block

### Start Second Block
$strout='<div class="container">'.chr(10);
$result=fwrite($accfile,$strout);

$strout = '<p><h4>Second Block Title</h4><ul>';
$result=fwrite($accfile,$strout);

$strout = '<ul><li>Second Block Text</ul>';
$result=fwrite($accfile,$strout);

$strout = '</div>';
$result=fwrite($accfile,$strout);

### End Second Block

$strout = '</div></div></div></div></div>';
$result=fwrite($accfile,$strout);

$strout = '</div>'.chr(10);
$result=fwrite($accfile,$strout);
### Main Body


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
