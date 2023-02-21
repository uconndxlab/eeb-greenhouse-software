<?php
function v4_acc_page_generate($codeno)

### Version 3 page generator - March 2014 Huskypress:Hale theme updates
### Version 4 - change to mysql-PDO libraries
{

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'/'.$codeno.'.html';
$accfile = fopen($file_spec,'w');
### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><head>'.chr(10);
$status=fwrite($accfile,$strout);

### GENERATE TITLE HTML

$sql = 'select gh_inv.latin_name,gh_inv.commonname,gh_inv.cntry_orig,classify.family,gh_inv.location,gh_inv.quant,gh_inv.keywords,';
$sql .= 'b_assign.descrip from gh_inv,classify,b_assign where classify.genus=gh_inv.genus and gh_inv.location=b_assign.location and codeno='.$codeno;
foreach($db->query($sql) as $result1) {

	$name = $result1['latin_name'];
	$family = $result1['family'];
	$location = $result1['location'];

	echo 'creating V4 accession file: '.$codeno.'.html - '.$name.chr(10);
	$strout = '<TITLE>'.$result1['latin_name'].' {'.$result1['family'].'}';
	$strout .= ' '.$result1['commonname'].'</TITLE>'.chr(10);
	$status=fwrite($accfile,$strout);
	### CREATE META TAGS
	$strout = '<meta name="description" CONTENT="EEB Greenhouse Accession Data for '.$name;
	$strout .= $strout.'">'.chr(10);
	$status=fwrite($accfile,$strout);
	### ADD KEYWORDS
	$strout = '<META NAME ="keywords" CONTENT="';
	$strout .= trim($result1['keywords']).' '.$result1['latin_name'];
	$strout .= ' '.$result1['commonname'];
	$strout .= ' '.$result1['cntry_orig'];
	$strout .= ' '.$result1['family'].'">'.chr(10);
	$status=fwrite($accfile,$strout);
	### ADD Open Graphics Propietary META Tags - social media page scraping
	$strout = '<meta property="og:image" content="';
	## IMAGE xx_00.jpg should have a default size of 470x250px
	## IF NO xx.00.jpg exists use a default greenhouse image
	## Strip out extra characters, replace spaces with underscores
	$namestr=strtr($name," ","_");
	$namestr=str_replace('-', "_", $namestr);
	$namestr=str_replace('"', "", $namestr);
	$namestr=str_replace("'", "", $namestr);
	$imagemask=$imagedir.'byspecies/'.$namestr.'00.jpg';
	if (file_exists($imagemask)) {
		$strout .= preg_replace('#/var/www/images/byspecies/#','http://florawww.eeb.uconn.edu/images/byspecies/',$imagemask).'">'.chr(10);
	} else {
		$strout .= 'http://florawww.eeb.uconn.edu/images/location/default02.jpg">'.chr(10);
	}
	$status=fwrite($accfile,$strout);
} # foreach

### RETRIEVE BOILERPLATE HTML
$sql = 'select head,body from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
foreach($db->query($sql) as $result1) {

	$strout = $result1['head'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout = $result1['body'].chr(10);
	$status=fwrite($accfile,$strout);

	$strout = '<div id="bodytext" class="container"><p></p>'.chr(10);	
	$strout .= '<div class="row" id="row1">';
	#$strout .= '<div class="row" id="row2">'.chr(10);

	$strout .= '<div id="home3" class="span6" role="complementary">'.chr(10);
	$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
	$strout .= '<h2 class="widget-title">Accession Data:</h2>';		
	$strout .= '<div class="textwidget">'.chr(10);
	$status=fwrite($accfile,$strout);
} # foreach

### GENERATE PRIMARY ACCESSION DATA SEGMENT

$sql = 'select gh_inv.genus,gh_inv.species,gh_inv.author,gh_inv.commonname,';
$sql .= 'gh_inv.subgenus,gh_inv.section,gh_inv.subsect,gh_inv.series,';
$sql .= 'famcomm.famauthor,gh_inv.cntry_orig,gh_inv.habitat,gh_inv.synonomy ';
$sql .= 'from gh_inv,classify,famcomm ';
$sql .= 'where codeno='.$codeno;
$sql .= ' and gh_inv.genus=classify.genus and classify.family=famcomm.family';
foreach($db->query($sql) as $result1) {
	$strout = '<h1><i>'.$name.'</i> '.$result1['author'].'</h1><P>';
	$status=fwrite($accfile,$strout);
	$strout = '<ul>';
	### check for infrageneric classification
	if (($result1['subgenus']<>'') or ($result1['section']<>'') or ($result1['subsect']<>'')or($result1['series']<>'')) $strout .= '<li><h3>';
	if ($result1['subgenus']<>'') $strout .= 'subg. '.$result1['subgenus'].' ';
	if ($result1['section']<>'') $strout .= 'sect. '.$result1['section'].' ';	
	if ($result1['subsect']<>'') $strout .= 'subsect. '.$result1['subsect'].' ';	
	if ($result1['series']<>'') $strout .= 'series '.$result1['series'].' ';
	if (($result1['subgenus']<>'')or($result1['section']<>'')or($result1['subsect']<>'')or($result1['series']<>'')) $strout .= '</h3>';
	$status=fwrite($accfile,$strout);
	$strout = '<li><b>Common Name: </b>'.$result1['commonname'].'<li><b>Family: </b>'.$family;
	$status=fwrite($accfile,$strout);
	$tempstr = $result1['famauthor']; ##### FAMAUTHOR
	if (!empty($tempstr)){
		$strout=' <I>'.$result1['famauthor'].'</I>';
		$status=fwrite($accfile,$strout);	
	} # endif

	$tempstr = $result1['synonomy']; ##### SYNONOMY
	if (!empty($tempstr)){
		$strout=' <li><b>Synonym(s):</b> '.$result1['synonomy'];
		$status=fwrite($accfile,$strout);	
	} # endif
	$strout='</ul><p>'.chr(10).'<ul>';
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Country of Origin: </b>'.$result1['cntry_orig'].chr(10); ##### COUNTRY OF ORIGIN
	$status=fwrite($accfile,$strout);
	$imagemask = $imagedir.'maps/tdwg/'.$codeno.'.jpg'; ##### TDWG MAP FILE CHECK
	if (file_exists($imagemask)) {
		$strout=preg_replace('#/var/www/images/maps/tdwg/#','<a href="http://florawww.eeb.uconn.edu/images/maps/tdwg/',$imagemask);
		$strout=preg_replace('#.jpg#','.png',$strout).'">';
		$status=fwrite($accfile,$strout);	
		$strout=preg_replace('#/var/www/images/maps/tdwg/#','<img src="http://florawww.eeb.uconn.edu/images/maps/tdwg/',$imagemask).'"></img></a>';
		$status=fwrite($accfile,$strout);
	} # endif
	$tempstr = $result1['habitat']; ##### HABITAT
	if (!empty($tempstr)){
		$strout='<li><b>Habitat: </b>'.$result1['habitat'].chr(10);
		$status=fwrite($accfile,$strout);
	} # endif
	$strout='</ul><p>';
	$status=fwrite($accfile,$strout);
} # foreach

$sql = 'select gh_inv.descrip,gh_inv.usedfor,gh_inv.culture,gh_inv.usda_zone,gh_inv.keywords from gh_inv where gh_inv.codeno='.$codeno;
foreach($db->query($sql) as $result1) {
	$tempstr=$result1['descrip']; ##### DESCRIPTION
	if (!empty($tempstr)){
		### Scan tempstring for glossary words - insert dfn/abbr tagging
		$strout = '<ul><li><b>Description: </b>'.v4_glossary_scan($tempstr).'</ul>'.chr(10);
		$status=fwrite($accfile,$strout);	
	} # endif
	$tempstr=$result1['usedfor']; ##### USAGE
	if (!empty($tempstr)){
		### Scan tempstr for glossary words - insert dfn/abbr tagging
		$strout = '<UL><LI><B>Uses: </B>'.v4_glossary_scan($tempstr);
		### Add disclaimer if medicinal usage noted - keyword includes medicinal
		if (strpos($result1['keywords'],"medicinal")) {
			$strout .= '<p></p><ul><li><font color="RED"><b>IMPORTANT NOTE:</b></font> <i>';
			$strout .= 'Plant Uses are for informational purposes only. EEB Greenhouses assume ';
			$strout .= 'no responsibility for adverse effects from the use of any plants referred';
			$strout .= ' to on this site. Always seek advice from a professional before using any plant medicinally.</i></ul>';	
		} # medicinal
		$strout .= '</UL>'.chr(10);
		$status=fwrite($accfile,$strout);	
	} # endif
	$tempstr=$result1['culture']; ##### CULTURE
	if (!empty($tempstr)){
		$strout = '<UL><LI><B>Culture: </B>'.$result1['culture'].'</UL>'.chr(10);
		$status=fwrite($accfile,$strout);	
	} # endif
	$tempstr=$result1['usda_zone']; ##### USDA ZONE
	if (!empty($tempstr)){
		$strout = '<UL><LI><B>USDA Zone: </B>'.$result1['usda_zone'].'</UL>'.chr(10);
		$status=fwrite($accfile,$strout);	
	} # endif
} # foreach


###############################################################
### REWORK FLOWERING ROUTINE HERE #############################
###   EXTERNAL FUNCTION TO RETURN GRAPHIC #####################
###   NOT HTML TABLE CODE HACK ################################
###############################################################

$sql = 'select gh_inv.source,gh_inv.provenance,gh_inv.provenance2,gh_inv.voucher,gh_inv.acc_date,gh_inv.location,gh_inv.quant,b_assign.descrip,gh_inv.confirm,gh_inv.currently from gh_inv,b_assign where codeno='.$codeno.' and gh_inv.location=b_assign.location';
foreach($db->query($sql) as $result1) {
$strout = '<h4>Accession Data:</h4><p><ul><li><b>Accession # </b>'.$codeno.chr(10);
$status=fwrite($accfile,$strout);
$strout = '<li><b>Source: </b>'.$result1['source'].chr(10); ##### SOURCE
$status=fwrite($accfile,$strout);
$tempstr=$result1['provenance'];
IF (!EMPTY($tempstr)){
	$strout='<li><b>Provenance: </b>'; ##### PROVENANCE
	$status=fwrite($accfile,$strout);
	$strout=$result1['provenance'].chr(10);
	$status=fwrite($accfile,$strout);
}
$tempstr=$result1['provenance2'];
IF (!EMPTY($tempstr)){
	$strout='<p>Additional provenance data on file but suppressed from public display.'; ##### SUPPRESSED PROVENANCE
	$status=fwrite($accfile,$strout);
}
$tempstr=$result1['voucher'];
IF (!EMPTY($tempstr)){
	$strout='<li><b>Recorded Vouchers: </b>'; ##### VOUCHERS
	$status=fwrite($accfile,$strout);	
	$strout=$result1['voucher'].chr(10);
	$status=fwrite($accfile,$strout);	
}
$strout='<li><b>Accession Date:     </b>'.date("m-d-Y",strtotime($result1['acc_date'])).chr(10);
$status=fwrite($accfile,$strout);
$strout='<li><b>Bench: </b>'.$result1['location'].' - '.$result1['descrip'].chr(10); ##### BENCH/STATUS/QUANT
$strout .= '<ul><li><b>Currently:</b> '.$result1['currently'];
$status=fwrite($accfile,$strout);
$strout='<li><b>Qty: </b>'.$result1['quant'].' confirmed on '.date("m-d-Y",strtotime($result1['confirm'])).'</ul>'.chr(10);
$status=fwrite($accfile,$strout);
} # foreach

### OUTPUT RESTRICTIONS LIST
$sql = 'select poisonous,redlist2010,cites,fed_weed,invasive,redist,engineered,toxinnotes,ppaf,bgci_exsitusites from gh_inv where codeno='.$codeno;
foreach($db->query($sql) as $result1) {
IF($result1['poisonous'] or $result1['redlist2010']>"" or $result1['cites']>"" or $result1['fed_weed'] or $result1['invasive'] or $result1['redist']){ 
	$strout = '<li><b>Restrictions:</b><ul>';
	$status=fwrite($accfile,$strout);
	IF ($result1['poisonous']){
		$strout = '<li><b>Poisonous Plant Parts - <font color=RED>Not for Human Consumption</font></b><br>';
		$status=fwrite($accfile,$strout);		
		$strout = $result1['toxinnotes'];
		$status=fwrite($accfile,$strout);		
	}
	$redlist = $result1['redlist2010'];
	IF ($redlist == 'Critically Endangered'){
		$strout = '<li><b>IUCN Red List: </b>Critically Endangered Species<br>';
		$status=fwrite($accfile,$strout);	
	}
	IF ($redlist == 'Endangered'){
		$strout = '<li><b>IUCN Red List: </b>Endangered Species<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($redlist == 'Extinct in the Wild'){
		$strout = '<li><b>IUCN Red List:</b> Endangered Species - Extinct in the Wild<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($redlist == 'Vulnerable'){
		$strout = '<li><b>IUCN Red List: </b>Species Vulnerable or Habitat Critically Threatened<br>';
		$status=fwrite($accfile,$strout);		
	}
	$cites = $result1['cites'];
	IF ($cites == 'CITES I'){
		$strout = '<li><b>CITES Appendix I Listed Plant</b> - Plants are not to leave Greenhouse!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($cites == 'CITES II'){
		$strout = '<li><b>CITES Appendix II Listed Plant</b><br>';
		$status=fwrite($accfile,$strout);		
	}
	$bgci_sites = $result1['bgci_exsitusites'];
	IF (($bgci_sites < 6) and ($bgci_sites > 0)){
		$strout = '<li><b>BGCI Limited Ex Situ Distribution</b> - Plants do not leave building without Manager approval!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($result1['fed_weed']){
		$strout = '<li><b>Federally Listed Noxious Weed</b> - Plants are not to leave Greenhouse!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($result1['invasive']){
		$strout = '<li><b>Potentially Invasive Plant</b> - Plants are not to leave Greenhouse!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($result1['redist']){
		$strout = '<li><b>Distribution Agreement (CBD or similar)</b> - Plants do not leave Greenhouse/TLS w/o Manager approval!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($result1['engineered']){
		$strout = '<li><b>GMO Material</b> - Plants kept in BPB Facility & do not leave Greenhouse w/o Manager approval!<br>';
		$status=fwrite($accfile,$strout);		
	}
	IF ($result1['ppaf']){
		$strout = '<li><b>PPAF</b> - Plant Patent Applied For - no propagation/redistribution of plants permitted<br>';
		$status=fwrite($accfile,$strout);		
	}
$strout = '</ul>';
$status=fwrite($accfile,$strout);	
} # end if
} # foreach

### CLASSIFICATION SECTION
$sql = 'select famcomm.division,famcomm.class,famcomm.subclass,';
$sql = $sql.'famcomm.order,famcomm.suborder,classify.family, ';
$sql = $sql.'classify.subfamily,classify.tribe,classify.subtribe ';
$sql = $sql.'from gh_inv,classify,famcomm ';
$sql = $sql.'where codeno='.$codeno;
$sql = $sql.' and gh_inv.genus=classify.genus and classify.family=famcomm.family';
foreach($db->query($sql) as $result1) {
	$strout='</ul><h4>Classification:</h4><p><ul>';
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Division: </b>'.$result1['division'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Class: </b>'.$result1['class'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>SubClass: </b>'.$result1['subclass'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Order: </b>';
	### Check for APG Orders page, link if present
	$filename = $rootdir.strtolower($result1['order']).'.html';
	if (file_exists($filename)) $strout = $strout.'<a href="'.strtolower($result1['order']).'.html">';
	$strout = $strout.$result1['order'];
	if (file_exists($filename)) $strout = $strout.'</a>';
	$strout = $strout.chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>SubOrder: </b>'.$result1['suborder'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Family: </b>'.$result1['family'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>SubFamily: </b>'.$result1['subfamily'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>Tribe: </b>'.$result1['tribe'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='<li><b>SubTribe: </b>'.$result1['subtribe'].chr(10);
	$status=fwrite($accfile,$strout);
	$strout='</ul>'.chr(10);
	$status=fwrite($accfile,$strout);
} # foreach



### FLOWERING DATA
$sql = 'select date,year(date) as year from history where class="FLOWERING" and codeno='.$codeno.' order by date';

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

### Check for any bloom records in history, and determine year of first occurence
if ($result) {
	$firstyear = $result['year']; 
	$year = date("Y");
	$strout = '<h4>Flowering Data:</h4><i>This accession has been observed in bloom on:</i>'.chr(10);
	$status=fwrite($accfile,$strout);
	### Set up table and column headers
	$strout = '<table><tr>';
	$strout .= '<th>Year</th><th colspan=4 align="center">Jan</th>';
	$strout .= '<th colspan=4 align="center">Feb</th>';
	$strout .= '<th colspan=5 align="center">Mar</th>';
	$strout .= '<th colspan=4>Apr</th>';
	$strout .= '<th colspan=4>May</th>';
	$strout .= '<th colspan=5>Jun</th>';
	$strout .= '<th colspan=4>Jul</th>';
	$strout .= '<th colspan=4>Aug</th>';
	$strout .= '<th colspan=5>Sep</th>';
	$strout .= '<th colspan=4>Oct</th>';
	$strout .= '<th colspan=4>Nov</th>';
	$strout .= '<th colspan=5>Dec</th></tr>'.chr(10);
	$status=fwrite($accfile,$strout);
	### iterate rows (years)
	while ($year > ($firstyear-1)) { 
		$week = 1;
		$strout = '<tr><td>'.$year.'</td>'.chr(10);
		$status=fwrite($accfile,$strout);
		### iterate columns (weeks)
		while ($week < 53) {
			### query history file to see if any flowering records made for each given week/year combo from beginning of current year to last week of first year.
			$sql = 'select date,year(date),week(date) from history where year(date)="'.$year.'" and week(date)="'.$week.'" and  class="FLOWERING" and codeno='.$codeno.' order by date';
			$sth = $db->prepare($sql);
			$sth->execute();
			$result = $sth->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				### output appropriate color to chart
				###   #4682b4 = RGB 70 130 180
				$strout = '<td style="background-color:#4682b4; width:2px; padding:0px;"></td>'.chr(10);
				} else {
					$strout = '<td style="background-color:#ffffff; width:2px; padding:0px;"></td>'.chr(10);
				}	
			$status=fwrite($accfile,$strout);
			$week++;
			} # weekly loop
			$strout = '</tr>'.chr(10);
			$status=fwrite($accfile,$strout);
			$year--;
	} # yearly loop
	$strout = '</table><p>'.chr(10);
	$status=fwrite($accfile,$strout);
} # if $result

### KEYWORD LINKS
$strout='<h4>References (internal):</h4>';
$status=fwrite($accfile,$strout);
$sql = 'select keywords from gh_inv ';
$sql .= 'where codeno='.$codeno;

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$keywords=str_word_count($result['keywords'],1,'0..9');
$numkeywords=count($keywords);
if ($numkeywords > 0) $strout = '<ul>';
$i=0;
while ($i < $numkeywords) {
	### cross reference to keywords file
	$sql = 'select * from keywords ';
	$sql .= 'where keyword="'.$keywords[$i].'"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	if ($result) {  # make sure keyword is in keyword table
		if (str_word_count($result['text'],0)>0) { # make sure there is valid text for keyword page
			if ($result['page_hide']<>1) {
				$strout .= '<li><a href="keyword_'.strtolower($keywords[$i]).'.html">'.$result['title'].'</a>'.chr(10);	
			} # page hide
		} # valid keyword text title
	} # valid keyword in keyword table
	$i++;
} # while loop
if ($numkeywords > 0) {
	$strout .= '</ul>';
	$status=fwrite($accfile,$strout);
}

### TDWG References:
$sql = 'select tdwg from gh_inv ';
$sql .= 'where codeno='.$codeno;

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$tdwg=str_word_count($result['tdwg'],1);
$numtdwg=count($tdwg);
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

if ($numtdwg > 0) $strout = '<ul><li><b>EEB Greenhouse Holdings native to:</b> ';
while ($i < $numtdwg) {
	### cross reference to keywords file
	$sql = 'select * from tblLevel3 ';
	$sql = $sql.'where upper(l3code) = upper("'.$tdwg[$i].'")';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	if ($result) {  # make sure keyword is in keyword table
		if (ctype_upper($tdwg[$i])) { # print for upper case only
			$strout .= '<a href="BRU_'.$tdwg[$i].'.html">'.$result['l3name'].'</a>';
			if ($i+1 < $numtdwg) $strout .= ' / ';	
		} # upper case only check
	} # code in table
	$i++;
} # while loop
if ($numtdwg > 0) {
	$strout = $strout.'</ul>';
	$status=fwrite($accfile,$strout);
}

### CREDITS
$strout='<h4>References (external):</h4>';
$status=fwrite($accfile,$strout);
$sql = 'select credits from gh_inv ';
$sql .= 'where codeno='.$codeno;

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$tempstr=$result['credits'];
IF (!EMPTY($tempstr)){
	$strout='<p>'.$result['credits'].chr(10);
	$status=fwrite($accfile,$strout);
}
$strout='<p style="text-align: left;"><i>data regenerated on '.date("r").' [bcm v4.0]</i>'.chr(10);
$status=fwrite($accfile,$strout);

$strout = '</div></div></div>';
$status=fwrite($accfile,$strout);

$strout = '<div class="row" id="row3">'.chr(10);	
$strout = $strout.'<div id="home3" class="span5" role="complementary">'.chr(10);
$strout = $strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout = $strout.'<h2 class="widget-title">Images:</h2>';			
$strout = $strout.'<div class="textwidget">'.chr(10);
$status=fwrite($accfile,$strout);

### END OF PRIMARY DATA SECTION

### BEGIN SECOND COLUMN

### INSERT PRIMARY IMAGE HERE
#$strout='<div id="photo" class="span-12 last">'.chr(10);
#$status=fwrite($accfile,$strout);
### IMAGE xx_00.jpg should have a default size of 470x250px
### IF NO xx.00.jpg exists use a default greenhouse image
### Strip out extra characters, replace spaces with underscores
$namestr=strtr($name," ","_");
$namestr=str_replace('-', "_", $namestr);
$namestr=str_replace('"', "", $namestr);
$namestr=str_replace("'", "", $namestr);
$imagemask=$imagedir.'byspecies/'.$namestr.'00.jpg';
if (file_exists($imagemask)) {
	$strout=preg_replace('#/var/www/images/byspecies/#','<img src="http://florawww.eeb.uconn.edu/images/byspecies/',$imagemask).'" alt="'.$name;
#	### CHECK IF BLOOMING
#	$sql='SELECT history.codeno, gh_inv.latin_name,classify.family';
#	$sql .= ' FROM history inner join gh_inv on history.codeno=gh_inv.codeno';
#	$sql .= ' inner join classify on gh_inv.genus=classify.genus';
#	$sql .= ' where history.codeno='.$codeno.' and history.class="FLOWERING" and gh_inv.projnum="GEN_COLL"';
#	$sql .= ' and history.date >= DATE_SUB(CURDATE(),INTERVAL 15 DAY)';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	}
#	$num=mysql_numrows($sql_result);
#	if ($num>0) $strout=$strout.' blooming this week'; 

	$strout .= '" />'.chr(10);
	$status = fwrite($accfile,$strout);	
	} else {
	### INSERT LOCATION IMAGE DEFAULT SLIDER		
	$strout ='<img src="http://florawww.eeb.uconn.edu/images/location/default02.jpg" alt="No default accession image assigned" />'.chr(10);
	$status = fwrite($accfile,$strout);
} # file exists imagemask

### ACCESSION IMAGES SECTION
$strout = '<h4>Additional images for this accession:</h4><i>Click on thumbnails to enlarge</i><p>';
$status = fwrite($accfile,$strout);

### Strip out extra characters, replace spaces with underscores
$namestr=strtr($name," ","_");
$namestr=str_replace('-', "_", $namestr);
$namestr=str_replace('"', "", $namestr);
$namestr=str_replace("'", "", $namestr);
$imagemask=$imagedir.'byspecies/'.$namestr.'*.jpg';
$imagearray=glob($imagemask);
foreach (glob($imagemask) as $filename) {
	### regenerate only if thumbnail is older than source image

	if (!file_exists($imagedir.'byspecies/thumb/'.basename($filename)) or (filemtime($imagedir.'byspecies/'.basename($filename)) > filemtime($imagedir.'byspecies/thumb/'.basename($filename)))) {
		$width = 100;
		$height = 75;
		list($width_orig, $height_orig) = getimagesize($filename);
		$reduction_ratio = $height_orig/75;
		$width = intval($width_orig/$reduction_ratio);
		$height=$height_orig/$reduction_ratio;
		echo 'resampling '.$filename.' from '.$width_orig.'x'.$height_orig.' to '.$width.'x'.$height.chr(10);
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		imagejpeg($image_p,preg_replace('#/byspecies/#','/byspecies/thumb/',$filename), 100);
		imagedestroy($image);
	}
	### output file info - ignore *00.jpg

	if (substr($filename,-6)<>"00.jpg"){
		$strout=preg_replace('#/var/www/#','<a href="http://florawww.eeb.uconn.edu/',$filename);
		$strout = $strout.'">';
		$status=fwrite($accfile,$strout);	
		$strout=preg_replace('#/var/www/images/byspecies/#','<img style="height:75px;padding:5px;border:0" src="http://florawww.eeb.uconn.edu/images/byspecies/thumb/',$filename);
		$strout = $strout.'">';
		$status=fwrite($accfile,$strout);	
		$strout = '</img></a>';
		$status=fwrite($accfile,$strout);
	}
}

### Check for images by accession number

$imagemask=$imagedir.'byspecies/'.$codeno.'*.jpg';
$imagearray=glob($imagemask);
foreach (glob($imagemask) as $filename) {
	### regenerate only if thumbnail is older than source image

	if (!file_exists($imagedir.'byspecies/thumb/'.basename($filename)) or (filemtime($imagedir.'byspecies/'.basename($filename)) > filemtime($imagedir.'byspecies/thumb/'.basename($filename)))) {
		$width = 100;
		$height = 75;
		list($width_orig, $height_orig) = getimagesize($filename);
		$reduction_ratio = $height_orig/75;
		$width = intval($width_orig/$reduction_ratio);
		$height=$height_orig/$reduction_ratio;
		echo 'resampling '.$filename.' from '.$width_orig.'x'.$height_orig.' to '.$width.'x'.$height.chr(10);
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		imagejpeg($image_p,preg_replace('#/byspecies/#','/byspecies/thumb/',$filename), 100);
		imagedestroy($image);
	}
	### output file info
	
	$strout=preg_replace('#/var/www/#','<a href="http://florawww.eeb.uconn.edu/',$filename);
	$strout = $strout.'">';
	$status=fwrite($accfile,$strout);	
	$strout=preg_replace('#/var/www/images/byspecies/#','<img style="height:75px;padding:5px;border:0" src="http://florawww.eeb.uconn.edu/images/byspecies/thumb/',$filename);
	$strout = $strout.'">';
	$status=fwrite($accfile,$strout);	
	$strout = '</img></a>';
	$status=fwrite($accfile,$strout);
} # foreach glob

### PLACE FAMILY LISTING CODE 
$strout = '<h4>Current Accessions in the '.$family.'</h4>';
$status=fwrite($accfile,$strout);

$sql = 'SELECT gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.author,gh_inv.wildcoll,acc_date,';
$sql .= 'classify.subfamily,classify.tribe,classify.subtribe,classify.scheme, famcomm.family,'; 
$sql .= 'famcomm.commonname,famcomm.fam_filen, famcomm.lump_fam ';
$sql .= 'FROM gh_inv,classify,famcomm ';
$sql .= 'WHERE classify.genus = gh_inv.genus ';
$sql .= 'and gh_inv.projnum="GEN_COLL" ';
$sql .= 'and famcomm.family = classify.family ';
$sql .= 'and classify.family = "'.$family.'" ';
$sql .= 'and gh_inv.codeno <> 0 ';
$sql .= 'ORDER BY famcomm.family, classify.subfamily,classify.tribe,classify.subtribe,gh_inv.genus,gh_inv.species,gh_inv.infraepithet';

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$subfamily = trim($result['subfamily']);
$tribe = trim($result['tribe']);

IF (!empty($subfamily)or(!empty($tribe))){
	$strout = '<h3>Subfamily '.$result['subfamily'].chr(10);
	$status=fwrite($accfile,$strout);	
	IF (!empty($tribe)){
		$strout = '<br>Tribe '.$result['tribe'].chr(10);
		$status=fwrite($accfile,$strout);	
	}
	$strout = '</h3>'.chr(10);
	$status=fwrite($accfile,$strout);	
	}
$strout = '<ul>';
$status=fwrite($accfile,$strout);

foreach($db->query($sql) as $row) {
	if (($row['subfamily'] <> $subfamily) or ($row['tribe'] <> $tribe)){
		$strout = '</ul><h3>Subfamily '.$row['subfamily'].chr(10);
		$tempstr = $row['tribe'];
		IF (($tempstr <> $tribe)and(!empty($tempstr))){
			$strout .= '<br>Tribe '.$row['tribe'].chr(10);
			
		}
		$tribe = trim($row['tribe']);
		$strout .= '</h3><ul>';			
		$output = fwrite($accfile,$strout);		
		$subfamily = $row['subfamily'];
	}
	$strout = chr(10).'<li>';
	$subtribe = trim($row['subtribe']);
	if (!empty($subtribe)) $strout .= $row['subtribe'].': ';
	$strout .= '<a href="'.$row['codeno'].'.html"><i>';
	$strout .= $row['latin_name'].'</i></a>';
	$output = fwrite($accfile,$strout);
	$temp = $row['wildcoll'];	
	if ($temp) {
		$strout = ' <font color="GREEN">W/C</font>';
		$output = fwrite($accfile,$strout);
	}
	$imagemask = $imagedir.'byspecies/thumb/'.strtr($row['latin_name']," ","_").'*.jpg';
	$imagearray = glob($imagemask);
	if (count($imagearray)>0){
		$strout = '<img src="/images/smallcamera.gif"></img>';
		$output = fwrite($accfile,$strout);
	}
	### check for TDWG map
	$imagemask = $imagedir.'maps/tdwg/'.$row['codeno'].'.jpg';
	$imagearray = glob($imagemask);
	if (count($imagearray)>0){
		$strout = ' <img src="/images/globe-18.png"></img>';
		$output = fwrite($accfile,$strout);
	}
	##### Check if NEW (past 90 days)

	$accdate = strtotime($row['acc_date']);
	if (time()-$accdate<7776000) $status=fwrite($accfile,' <img src="http://florawww.eeb.uconn.edu/images/new.jpg"></img> ');		
	##### Check if noted flowering in past 14 days
	$sql = 'select * from history where codeno='.$row['codeno'].' and class="FLOWERING"';
	$sql = $sql.' and history.date>date_sub(curdate(),interval 350 day)';
	$sql = $sql.' and history.date>date_sub(curdate(),interval 14 day)';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result2 = $sth->fetchColumn();
	if ($result2) {
		$strout = '<img src="http://florawww.eeb.uconn.edu/images/flower-rose.gif"></img>';
		$output = fwrite($accfile,$strout);
	}
} # foreach

$strout = '</ul><font color="GREEN">W/C</font> = <i>Wild Collected</i>'.chr(10);
$strout .= '<br><img src="/images/flower-rose.gif"></img> = indicates flowering in past 14 days'.chr(10);
$strout .= '<br><img src="/images/smallcamera.gif"></img> = images available for this accession'.chr(10);
$strout .= '<br><img src="/images/globe-18.png"></img> = map available for this accession'.chr(10);
$strout .= '<br><img src="/images/new.jpg"></img> = accession added within past 90 days'.chr(10);

$output = fwrite($accfile,$strout);
$strout = '</div></div></div></div>'.chr(10);
$output = fwrite($accfile,$strout);

$strout = '</div></div>'.chr(10);
$output = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select foot from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$strout = $result['foot'].chr(10);
$output = fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);

### If we get this far, reset tempflag

$sql = 'update gh_inv set tempflag=0 where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();

### CLOSE OUTPUT FILE
$db=null; ### close PDO object
return true;
}
### glossary search routine
function v4_glossary_scan($subjstr)
{
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!"; ## user friendly message
}	

### collect list of current glossary terms.
$count = 1; #only replace first instance
$sql = 'select term,def from glossary';
foreach($db->query($sql) as $row) {
	$searchstr = $row['term'];
	if (preg_match('/\b'.ucfirst($searchstr).'\b/',$subjstr)) {
			### build replacement string if uppercase
			$replstr = '<dfn><abbr title="'.$row['def'].'">'.ucfirst($searchstr).'</abbr></dfn>';
			$subjstr = preg_replace('/\b'.ucfirst($searchstr).'\b/',$replstr,$subjstr,$count);
	} elseif (preg_match('/\b'.$searchstr.'\b/',$subjstr)) {
			### build replacement string if lowercase
			$replstr = '<dfn><abbr title="'.$row['def'].'">'.$searchstr.'</abbr></dfn>';
			$subjstr = preg_replace('/\b'.$searchstr.'\b/',$replstr,$subjstr,$count);
	} # if stripos
} #foreach
### CLOSE OUTPUT FILE
$db=null; ### close PDO object
return $subjstr;
} #end v4_glossary_scan definition
###############################################################
### EXIT CODE #################################################
### Drop a time stamp for debugging, then close files and exit...
#$strout = '<p style="text-align: left;"><i>data regenerated on '.date("r").' [bcm v4.0]</i>'.chr(10);
#$output = fwrite($accfile,$strout);
#fclose($accfile);
#$db=null; ### close PDO object
#exit('Exiting for debugging...'.chr(10));
###############################################################
?>

