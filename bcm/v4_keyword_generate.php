<?php
function v4_keyword_generate($keyword)
{
include '/var/www/bcm/credentials.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'keyword_'.$keyword.'.html';
$accfile = fopen($file_spec,'w')or die("can't open file");

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$status = fwrite($accfile,$strout);

### GENERATE TITLE HTML

$sql = 'select * from keywords where keyword="'.$keyword.'"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$strout = '<TITLE>UConn Biodiversity Conservatory - '.$result['title'].'</title>'.chr(10);
$status = fwrite($accfile,$strout);

### CREATE META TAGS

$strout = '<HEAD><META NAME="description" CONTENT="'.$result['title'].'">';
$status = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$strout = $result['head'].chr(10);
$status = fwrite($accfile,$strout);
$strout = $result['body'].chr(10);
$status = fwrite($accfile,$strout);

$sql = 'select * from keywords where keyword="'.$keyword.'"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
$strout .= '<div class="row" id="row1">'.chr(10);
#$strout=$strout.'<div class="row" id="row2">'.chr(10);

$strout .= '<div id="home3" class="span4" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">SPECIAL COLLECTION:<br><b>'.chr(10);		
$status = fwrite($accfile,$strout);
### WRITE COLLECTION INTRO

$strout = $result['title'].'</b></h2><p>';
$status = fwrite($accfile,$strout);

$strout = '<div id="col_holdings" class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

#$strout = v4_glossary_scan($result['text']);
### glossary scan disabled due to glitch that finds glossary terms inside of URLS
$strout = $result['text'];
$status = fwrite($accfile,$strout);

		### check for Keyword Locator map
		$imagemask =$imagedir.'maps/keywords/keyword_'.$keyword.'_map.png';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = '<hr><b>Greenhouse Locator Map:</b><p><center><a href="http://florawww.eeb.uconn.edu/images/maps/keywords/keyword_'.$keyword.'_maplist.png">';
			$strout .= '<img src="http://florawww.eeb.uconn.edu/images/maps/keywords/keyword_'.$keyword.'_map.png" width="300px"></img>';
			$strout .= '</a></center>';
			$status = fwrite($accfile,$strout);
		}		
$strout = '<p><p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### END COLLECTION INTRO

### BEGIN SECOND COLUMN

### Collect names of plants 
$sql = 'select gh_inv.codeno,gh_inv.latin_name from gh_inv';
$sql .= ' where gh_inv.projnum="'.$project_default.'"';
#$sql .= ' where gh_inv.projnum="PENDING"';  ### temporary fix for one off file generator
$sql .= ' and gh_inv.keywords like "% '.$result['keyword'].' %"';
$sql .= ' order by latin_name';
echo $sql.chr(10);
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();

$strout = '<div class="row" id="row3">'.chr(10);	
$strout .= '<div id="home3" class="span6" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">'.$num.' Accessions:</h2>'.chr(10);			
$strout .= '<div class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);


$strout = '<div class="container">'.chr(10);
$status = fwrite($accfile,$strout);

### INSERT COLLECTION SLIDER HERE

$strout = '<div id="photo" class="span-12 last">'.chr(10);
$status = fwrite($accfile,$strout);
### IMAGES should have a default size of 470x250px

### BUILD SLIDER GENERATOR
###   FIND ALL IMAGES Xx00.jpg FOR GIVEN COLLECTION

$i=0;
$i2=0;
$strout = '<div id="slider" class="span-12 last">';
$status = fwrite($accfile,$strout);

foreach($db->query($sql) as $row) {
	$namestr = strtr($row['latin_name']," ","_");
	$imagemask = $imagedir.'byspecies/'.$namestr.'00.jpg';
	if (file_exists($imagemask)) {
		$strout = '<a href="'.$row['codeno'].'.html">';	
		$status = fwrite($accfile,$strout);
		$strout = preg_replace('#/var/www/images/byspecies/#','<img src="/images/byspecies/',$imagemask);
		$strout = $strout.'" alt="'.$row['latin_name'].'" /></a>'.chr(10);
		$status = fwrite($accfile,$strout);
		$i2++; #increment counter for found images
	}
} # foreach
$strout = '</div>';
$status = fwrite($accfile,$strout);
#}
$strout = '</div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### INSERT SPECIAL COLLECTION SPECIES LIST
$strout='<p><hr><p>'.chr(10);
$strout .= 'Number in parentheses references locator map icons<ul>';
$status = fwrite($accfile,$strout);

$sql = 'select gh_inv.source, gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.location,gh_inv.projnum,gh_inv.wildcoll,gh_inv.keywords,gh_inv.acc_date,classify.family,b_assign.map_index from gh_inv,classify,b_assign';
$sql .= ' where gh_inv.genus=classify.genus and (gh_inv.projnum="GEN_COLL" or gh_inv.projnum="WISHLIST" or gh_inv.projnum="PENDING")';
$sql .= ' and gh_inv.location = b_assign.location';
$sql .= ' and keywords like "% '.$result['keyword'].' %"';
$sql .= ' order by projnum,location,latin_name';

foreach($db->query($sql) as $row) {
	$projnum = $row['projnum'];
	### check each accession for recent flowering (14 days)		
	$sql = 'SELECT codeno FROM history where history.codeno='.$row['codeno'].' and history.class="FLOWERING" and history.date >= DATE_SUB(CURDATE(),INTERVAL 14 DAY)';
	$sth2 = $db->prepare($sql);
	$sth2->execute();
	$bloom = $sth2->rowCount();
	if ($projnum<>"WISHLIST"){
		$strout = '<li>';
		#$strout .= $row['location'].': ';
		$strout .= '{<b>'.$row['map_index'].'</b>} ';
		$strout .= '<a href = "'.$row['codeno'].'.html"><i>'.$row['latin_name'].'</i></a>';
		$status = fwrite($accfile,$strout);
		$temp = $row['commonname'];
		if (!empty($temp)) {
			$strout = ' - <font color=purple>'.$row['commonname'].'</font>';
			$status = fwrite($accfile,$strout);
		}
		$strout = ' - '.$row['family'];
	###	$strout .= ': <font color="green">'.$row['source'].'</font>';
		$status = fwrite($accfile,$strout);
		$strout = '';
		##### Check if NEW (past 90 days)
		$accdate = strtotime($row['acc_date']);
		if (time()-$accdate<7776000) $status = fwrite($accfile,' <img src="http://florawww.eeb.uconn.edu/images/new.jpg"></img> ');
		### add icon if blooming					
		if ($bloom > 0) $strout = ' <img src="/images/flower-rose.gif"></img> ';
		$status = fwrite($accfile,$strout);
		### check for images
		$imagemask = $imagedir.'byspecies/thumb/'.strtr($row['latin_name']," ","_").'*.jpg';
		$imagearray = glob($imagemask);
		$strout = '';
		if (count($imagearray)>0) $strout = '<img src="/images/smallcamera.gif"></img>';
		$strout = $strout.chr(10);		
		$status = fwrite($accfile,$strout);
		### check for TDWG map
		$imagemask = $imagedir.'maps/tdwg/'.$row['codeno'].'.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = ' <img src="/images/globe-18.png"></img>';
			$status = fwrite($accfile,$strout);
		}
		### check for voucher (keyword)
		if (strpos($row['keywords']," voucher ")) {
			$strout = ' <img src="/images/herbarium-book2-20px.png"></img>';
			$status = fwrite($accfile,$strout);
			}	
		$temp = $row['wildcoll'];	
		if ($temp) {
			$strout = ' <font color="GREEN">W/C</font>';
			$status = fwrite($accfile,$strout);
		}
	
	} else {
		$strout = '<li><font color=gray><I>WISHLIST ITEM: <i>'.$row['latin_name'].'</i>';
		$status = fwrite($accfile,$strout);
		$temp = $row['commonname'];
		if (!empty($temp)) {
			$strout = ' - '.$row['commonname'];
			$status = fwrite($accfile,$strout);
		}
		$strout = ' - '.$row['family'].'</i></font>'.chr(10);
		$status = fwrite($accfile,$strout);
	} # if !wishlist
} # foreach

$strout = '</ul><br><font color="GREEN">W/C</font> = <i>Wild Collected</i><br> <img src="/images/flower-rose.gif"></img> = Currently Flowering';
$strout .= '<br><img src="/images/smallcamera.gif"></img> = Image(s) Available';
$strout .= '<br><img src="/images/globe-18.png"></img> = map available for this accession'.chr(10);
$strout .= '<br><img src="/images/herbarium-book2-20px.png"></img> = voucher(s) on file at <a href="http://bgbaseserver.eeb.uconn.edu/aboutherb.html"><b>CONN</b></a> for this accession';
$strout .= '<br><img src="/images/new.jpg"></img> = accession added within past 90 days'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div></div></div></div></div>'.chr(10);
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
}
### glossary check routine
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
?>
