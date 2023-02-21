<?php
### Modified code to generate a family/subfamily/tribe/subtribe
### classify_full_generate modified to include wishlist items...

{
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'wishlist.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$outstr=fwrite($accfile,$strout);

$strout = '<TITLE>EEB Greenhouse Wishlist by Family</TITLE>'.chr(10);
$outstr=fwrite($accfile,$strout);

### CREATE META TAGS

$strout = '<head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" >'.chr(10);
$outstr=fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	
$strout = $result['head'].chr(10);
$outstr = fwrite($accfile,$strout);
$strout = $result['body'].chr(10);
$outstr = fwrite($accfile,$strout);

$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
$strout .= '<div class="row" id="row1">'.chr(10);
$strout .= '<div id="home3" class="span4" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">Wishlist by Family as of:<br>'.date("l, F jS, Y").'</h2>'.chr(10);		
$strout .='<div id="col_holdings" class="textwidget">'.chr(10);
$outstr = fwrite($accfile,$strout);

$strout = 'The following plants are species we would like to add to our collections.  Some are very rare and likely not even in cultivation, others are fairly ';
$strout .= 'common but would make useful additions. Parentheses indicate potential or previously identified sources and/or internal notes but are not fully vetted. ';  
$strout .= '  <p>Note: Hypothetical Composition is a combination of existing accessions plus all items on wishlist and is intended for internal use. ';  

$strout .= ' <p>We also maintain a list of <a href="http://florawww.eeb.uconn.edu/surplus.html">surplus plants</a> that we will occasionally ';
$strout .= 'trade with other institutional and hobbyist collections.  Please contact the facility manager to discuss potential trades<p>';
$outstr = fwrite($accfile,$strout);
$strout = '<div id="incl_fam" class="container">'.chr(10);
$outstr = fwrite($accfile,$strout);

### GENERATE FAMILY LISTING CODE

$sql = 'SELECT gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.author,gh_inv.wildcoll,acc_date,gh_inv.projnum,gh_inv.source,gh_inv.coll_rank,';
$sql .= 'gh_inv.e_cl_rank,gh_inv.e_tx_rank,';
$sql .= 'classify.subfamily,classify.tribe,classify.subtribe,classify.scheme, famcomm.family,'; 
$sql .= 'famcomm.commonname,famcomm.fam_filen, famcomm.lump_fam ';
$sql .= 'FROM gh_inv,classify,famcomm ';
$sql .= 'WHERE classify.genus = gh_inv.genus ';
$sql .= 'and (gh_inv.projnum="WISHLIST" ';
$sql .= 'or gh_inv.projnum="REPLACE" ';
$sql .= 'or gh_inv.projnum="PENDING") ';
$sql .= 'and famcomm.family = classify.family ';
$sql .= 'and gh_inv.codeno <> 0 ';
$sql .= 'ORDER BY famcomm.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.projnum,gh_inv.genus,gh_inv.species';

$family = "";
$subfamily = "";
$tribe = "";
$linkpos="A"; ## used for placing internal links

$addlink=FALSE;

$strout = '<ul>';
$outstr = fwrite($accfile,$strout);

foreach($db->query($sql) as $row) {
	##### Check & output family name
	IF ($row['family'] <> $family) {
		$strout = '</ul>';
		$family = $row['family'];
		$strout .= '<h2>'.$family.'</h2>';
		$strout .= '<ul>';			
		$outstr = fwrite($accfile,$strout);
		echo $strout.chr(10);		
	}
	#####Check if subfamily or tribe has changed	

	IF (($row['subfamily'] <> $subfamily) or ($row['tribe'] <> $tribe)){
		$tempstr = $row['subfamily'];
		$tempstr2 = $row['tribe'];
		IF ((!empty($tempstr)) or (!empty($tempstr2))) {
			$strout = '</ul><h4>Subfamily '.$tempstr.chr(10);
			$tempstr = $row['tribe'];
			IF (($tempstr <> $tribe)and(!empty($tempstr))) $strout .= '<br>Tribe '.$row['tribe'].chr(10);
			$strout .= '</h4><ul>';
			$outstr = fwrite($accfile,$strout);
		}
		$tribe = trim($row['tribe']);		
		$subfamily=trim($row['subfamily']);
	}

	$strout = chr(10).'<li>';
	$subtribe = trim($row['subtribe']);
	if (!empty($subtribe)) $strout .= $row['subtribe'].': ';
	$strout .= '<i>'.$row['latin_name'].'</i>';
	$strout .= '</a>: '.$row['projnum'].' {'.$row['source'].'}';
	
	$outstr = fwrite($accfile,$strout);
} # foreach

$strout = '</ul>'.chr(10);
$outstr = fwrite($accfile,$strout);


$strout = '<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$outstr = fwrite($accfile,$strout);
$strout = '</div></div></div></div>'.chr(10);
$outstr = fwrite($accfile,$strout);

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout .= '<div id="home3" class="span6" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">Hypothetical Collections Composition:</h2>'.chr(10);			
$strout .= '<div class="textwidget">'.chr(10);
$outstr = fwrite($accfile,$strout);

# Calculate various collections statistics here

$sql = 'select codeno,quant,swingquant from gh_inv where gh_inv.projnum="GEN_COLL" or gh_inv.projnum="WISHLIST" or gh_inv.projnum="SECURE" or gh_inv.projnum="JONES" or gh_inv.projnum="OPEL"or gh_inv.projnum="YUAN"';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$colltotal = $num;
$strout = '<p><B>'.$num.'</B> accessions in the General Collections'.chr(10);
$outstr = fwrite($accfile,$strout);

### Count distinct families

$sql = 'select distinct classify.family from gh_inv,classify where (gh_inv.projnum="GEN_COLL" or gh_inv.projnum="WISHLIST" or gh_inv.projnum="SECURE" or gh_inv.projnum="JONES" or gh_inv.projnum="OPEL"or gh_inv.projnum="YUAN")';
$sql .= ' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<br><B>'.$num.'</B><i> families are represented</i>'.chr(10);
$outstr = fwrite($accfile,$strout);

### IUCN Red List 2010

#$strout = '<p><a href="http://www.iucnredlist.org/" target="_blank"><font color="RED">IUCN Red List Status (2010):</font></a><ul>';
#$outstr = fwrite($accfile,$strout);
#$sql = 'select distinct redlist2010 from gh_inv where redlist2010>"" order by redlist2010';
#$sth = $db->prepare($sql);
#$sth->execute();
#$j = $sth->rowCount();
#$i=0;
#while ($i<$j) {
#	$sql = 'select codeno from gh_inv where redlist2010="'.mysql_result($sql_result1,$i,'redlist2010').'"';
#	$sth1 = $db->prepare($sql);
#	$sth1->execute();
#	if ($sth1->fetchColumn())
#		$num = mysql_numrows($sql_result);
#		$strout='<li>'.mysql_result($sql_result1,$i,'redlist2010').': <b>'.$num.'</b>'.chr(10);
#		$outstr=fwrite($accfile,$strout);
#	$i++;
#	}
#	}
#}
#$strout='</ul>';
#$outstr=fwrite($accfile,$strout);

### CITES

#$strout='<a href="http://www.cites.org/" target="_blank"><font color="green">CITES Status:</font></a>'.chr(10).'<ul>';
#$outstr=fwrite($accfile,$strout);
#$sql='select distinct cites from gh_inv where cites>"" order by cites';
#$sql_result1=mysql_query($sql);
#if (!$sql_result) {
#	echo mysql_error();
#} else {
#$j=mysql_numrows($sql_result1);
#$i=0;
#while ($i<$j) {
#	$sql='select codeno from gh_inv where cites="'.mysql_result($sql_result1,$i,'cites').'"';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	} else {
#		$num=mysql_numrows($sql_result);
#		$strout='<li>'.mysql_result($sql_result1,$i,'cites').': <b>'.$num.'</b>'.chr(10);
#		$outstr=fwrite($accfile,$strout);
#	$i++;
#	}
#	}
#}
#$strout='</ul><hr>';
#$outstr=fwrite($accfile,$strout);

$strout = chr(10).'</div></div></div>'.chr(10);
$outstr = fwrite($accfile,$strout);

$strout = chr(10).'</div></div>'.chr(10);
$outstr = fwrite($accfile,$strout);

$strout = chr(10).'</div></div>'.chr(10);
$outstr = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select foot from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	
$strout = $result['foot'].chr(10);
$outstr = fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);
$db = null;
return true;
}
?>

