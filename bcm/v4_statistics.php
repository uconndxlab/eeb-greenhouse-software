<?php
### Generate Statistics and Recent 25 Data ###

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'statistics.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$status = fwrite($accfile,$strout);

$strout='<title>EEB Greenhouse Collection Statistics and Recent Accessions</title><head>';
$status = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$strout = $result['head'].chr(10);
$status = fwrite($accfile,$strout);
$strout = $result['body'].chr(10);
$status = fwrite($accfile,$strout);

$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
$strout=$strout.'<div class="row" id="row1">'.chr(10);

$strout=$strout.'<div id="home3" class="span4" role="complementary">'.chr(10);
$strout=$strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout=$strout.'<h2 class="widget-title">Collection Statistics as of '.date("l, F jS, Y").':</h2>'.chr(10);		
$status = fwrite($accfile,$strout);
$strout='<div class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

### Calculate various collections statistics here

$sql='select count(codeno) as cnt,sum(quant + quant2) as totalplants from gh_inv where gh_inv.projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);	

$strout = '<p><b>'.$result['cnt'].'</b> accessions in the General Collections';
$strout .= '<ul><li>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '<b>'.$result['totalplants'].' </b><i>individual plants under cultivation</i>'.chr(10);
$status = fwrite($accfile,$strout);

### Count distinct families
$sql = 'select distinct classify.family from gh_inv,classify where gh_inv.projnum="GEN_COLL"';
$sql .= ' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<li><b>'.$sth->rowCount().'</b><i> families are represented</i>'.chr(10);
$status = fwrite($accfile,$strout);

### Count distinct ANGIOSPERM families
$sql = 'select distinct classify.family from gh_inv,classify,famcomm where gh_inv.projnum="GEN_COLL"';
$sql .= ' and gh_inv.genus=classify.genus and classify.family=famcomm.family and famcomm.division="Magnoliophyta"';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<ul><li>'.$sth->rowCount().'<i> angiosperm families are represented</i></ul>'.chr(10);
$status = fwrite($accfile,$strout);

### Count distinct genera
$sql = 'select distinct classify.genus from gh_inv,classify where gh_inv.projnum="GEN_COLL"';
$sql .= ' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<li><b>'.$sth->rowCount().'</b><i> genera are represented</i>'.chr(10);
$status = fwrite($accfile,$strout);

### Count distinct ANGIOSPERM genera
$sql = 'select distinct classify.genus from gh_inv,classify,famcomm where gh_inv.projnum="GEN_COLL"';
$sql .= ' and gh_inv.genus=classify.genus and classify.family=famcomm.family and famcomm.division="Magnoliophyta"';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<ul><li>'.$sth->rowCount().'<i> angiosperm genera are represented</i></ul>'.chr(10);
$status = fwrite($accfile,$strout);

### Wild collected
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and wildcoll';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<li><b>'.$sth->rowCount();
$strout .= '</b><i> are documented wild collected specimens</i>'.chr(10);
$status = fwrite($accfile,$strout);

### Vouchered
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and gh_inv.keywords like "% voucher %"';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<li><b>'.$sth->rowCount();
$strout .= '</b><i> have <a href="http://florawww.eeb.uconn.edu/keyword_voucher.html">voucher specimens</a> deposited in <a href="http://bgbaseserver.eeb.uconn.edu/aboutherb.html"><b>CONN</b></a></i></ul>'.chr(10);
$status = fwrite($accfile,$strout);

### IUCN Red List 2013 (variable still says 2010)

$strout = '<a href="http://www.iucnredlist.org/" target="_blank"><font color="RED">IUCN Red List Status (2013):</font></a><ul>';
$status = fwrite($accfile,$strout);
$sql = 'select count(codeno) as cnt,redlist2010 from gh_inv where redlist2010>"" and projnum="GEN_COLL" group by redlist2010';
foreach($db->query($sql) as $row) {
	$strout = '<li>';
	### Link to KEYWORD files
	if ($row['redlist2010']=='Critically Endangered') {
		$strout .= '<a href="http://florawww.eeb.uconn.edu/keyword_endangered-critical.html">'.$row['redlist2010'].'</a>: <b>'.$row['cnt'].'</b>'.chr(10); 		
	} elseif ($row['redlist2010']=='Endangered'){
		$strout .= '<a href="http://florawww.eeb.uconn.edu/keyword_endangered.html">'.$row['redlist2010'].'</a>: <b>'.$row['cnt'].'</b>'.chr(10); 
	} elseif ($row['redlist2010']=='Extinct in the Wild'){
		$strout .= '<a href="http://florawww.eeb.uconn.edu/keyword_endangered-extinct-in-wild.html">'.$row['redlist2010'].'</a>: <b>'.$row['cnt'].'</b>'.chr(10); 
	} else {
	$strout .= $row['redlist2010'].'</a>: <b>'.$row['cnt'].'</b>'.chr(10);
	} #endif
	$status = fwrite($accfile,$strout);
} #foreach
$strout = '</ul>';
$status = fwrite($accfile,$strout);

### CITES

$strout = '<a href="http://www.cites.org/" target="_blank"><font color="green">CITES Status:</font></a>'.chr(10).'<ul>';
$status = fwrite($accfile,$strout);
#$sql = 'select distinct cites from gh_inv where cites>"" order by cites';
$sql = 'select count(codeno) as cnt,cites from gh_inv where cites>"" and projnum="GEN_COLL" group by cites';
foreach($db->query($sql) as $row) {
	$strout = '<li>'.$row['cites'].': <b>'.$row['cnt'].'</b>'.chr(10);
	$status = fwrite($accfile,$strout);
} #foreach
$strout = '</ul>';
$status = fwrite($accfile,$strout);

### BGCI Rarity
#$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and bgci_exsitusites < 5 and bgci_status="Accepted"';
#$sth = $db->prepare($sql);
#$sth->execute();
#$strout = '<b>'.$sth->rowCount();
#$strout .= '</b> accessions are held in fewer than 5 <a href="https://www.bgci.org/">BGCI</a> affiliated Botanic Gardens.<hr>'.chr(10);
#$status = fwrite($accfile,$strout);

### New Accessions Section
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and acc_date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<b>'.$sth->rowCount();
$strout .= '</b> added in the past year<br>'.chr(10);
$status = fwrite($accfile,$strout);

### Confirmations
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and confirm> date_sub(curdate(),interval 60 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<b>'.$sth->rowCount();
$strout .= '</b> verified in the past 60 days<br>'.chr(10);
$status = fwrite($accfile,$strout);

### Class Use
$sql = 'select codeno from history where history.class="CLASS" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<hr><b>'.$sth->rowCount();
$strout .= '</b> used in UConn classes in the past 12 months<br>'.chr(10);
$status = fwrite($accfile,$strout);

### Outreach Use
$sql = 'select codeno from history where history.class="OUTREACH" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<b>'.$sth->rowCount();
$strout .= '</b> used in outreach activities in the past 12 months<br>'.chr(10);
$status = fwrite($accfile,$strout);

### Trades
$sql = 'select codeno from history where history.class="TRADE" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<b>'.$sth->rowCount();
$strout .= '</b> sent to other institutions in the past 12 months<br>'.chr(10);
$status = fwrite($accfile,$strout);

### Spray
$sql = 'select history.codeno from history where zone < 6000 and history.class="SPRAY" and date> date_sub(curdate(),interval 30 day)';
$sth = $db->prepare($sql);
$sth->execute();
$strout = '<b>'.$sth->rowCount();
$strout .= '</b> <a href="ipm.html">treated for pests</a> in the past 30 days {<i>Public Collections only</i>}<br>'.chr(10);
$status = fwrite($accfile,$strout);

### count images in byspecies
$imagemask = '/var/www/images/byspecies/*.jpg';
$imagearray = glob($imagemask);
$num = count($imagearray);
$strout = '<b>'.$num;
$strout .= '</b> accession images are in the database<br>'.chr(10);
$status = fwrite($accfile,$strout);

### count distribution maps in database
$imagemask = '/var/www/images/maps/tdwg/*.jpg';
$imagearray = glob($imagemask);
$num = count($imagearray);
$strout = '<b>'.$num;
$strout .= '</b> distribution maps are in the database<br>'.chr(10);
$status = fwrite($accfile,$strout);

### count interpretive signage (not currently in use)
#$sql = 'select codeno from gh_inv where gh_inv.signage and gh_inv.projnum="GEN_COLL"';
#$sth = $db->prepare($sql);
#$sth->execute();
#$strout = '<b>'.$sth->rowCount();
#$strout .= '</b> interpretive signs in the teaching collections<br>'.chr(10);
#$status = fwrite($accfile,$strout);

$strout = '<hr><p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '</div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### END COLLECTION INTRO

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout .= '<div id="home3" class="span6" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title"></h2>'.chr(10);			
$strout .= '<div class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

### INSERT COLLECTION SLIDER HERE

$strout = '<div id="photo">'.chr(10);
$status = fwrite($accfile,$strout);
### IMAGES should have a default size of 470x250px

$strout = '<div id="slider">'.chr(10); 
$status = fwrite($accfile,$strout);

### Get Random Banner Images

$images = glob($imagedir."byspecies/*00.jpg");
$images = array_rand(array_flip($images),20);
shuffle($images);

foreach ($images as $filename) {
	# Fetch codeno for URL
	
	$name = strtr($filename,"_"," ");
	echo $name.chr(10);
	$name = preg_replace('#/var/www/images/byspecies/#','',$name);
	$name = preg_replace('#00.jpg#','',$name);	
	$sql = 'select codeno,latin_name from gh_inv where latin_name="'.$name.'"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);			
	$strout = '<a href="'.$result['codeno'].'.html">';
	$status = fwrite($accfile,$strout);	
	$strout = preg_replace('#/var/www/#','<img src="http://florawww.eeb.uconn.edu/',$filename);
	$strout .= '" alt="<i>Random Accession: </i>'.$name.'" /></a>'.chr(10);
	$status = fwrite($accfile,$strout);	
} #foreach

$strout = '</div></div>';
$status = fwrite($accfile,$strout);

$strout = '<div class="container">'.chr(10);
$status = fwrite($accfile,$strout);

### Most Recent Accessions

$strout = '<p><hr><p><h4>25 Latest Accessions:</h4><ul>';
$status = fwrite($accfile,$strout);

$sql = 'select gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.acc_date,classify.family,gh_inv.wildcoll,gh_inv.keywords from gh_inv,classify';
$sql .= ' where gh_inv.genus=classify.genus and gh_inv.projnum="GEN_COLL"';
$sql .= ' order by acc_date desc, codeno desc limit 25';
foreach($db->query($sql) as $row) {
	$strout = '<li>'.date("m-d-Y",strtotime($row['acc_date'])).' - ';
	$status = fwrite($accfile,$strout);
	$strout = '<A HREF = "'.$row['codeno'].'.html"><i>'.$row['latin_name'].'</i></a>';
	$status = fwrite($accfile,$strout);
	$strout = ' - '.$row['family'];
	$status = fwrite($accfile,$strout);
	$temp = $row['wildcoll'];	
	if ($temp) {
		$strout = ' <font color="GREEN">W/C</font>';
		$status = fwrite($accfile,$strout);
	}
	$imagemask = $imagedir.'byspecies/thumb/'.strtr($row['latin_name']," ","_").'*.jpg';
	$imagearray = glob($imagemask);
	if (count($imagearray)>0){
		$strout='<img src="/images/smallcamera.gif"></img>';
		$status = fwrite($accfile,$strout);
	}
	### check for TDWG map
	$imagemask = $imagedir.'maps/tdwg/'.$row['codeno'].'.jpg';
	$imagearray = glob($imagemask);
	if (count($imagearray)>0){
		$strout=' <img src="/images/globe-18.png"></img>';
		$status = fwrite($accfile,$strout);
	}
	### check for voucher (keyword)
	if (strpos($row['keywords']," voucher ")) {
		$strout=' <img src="/images/herbarium-book2-20px.png"></img>';
		$status = fwrite($accfile,$strout);
	}	
	$strout = chr(10);
	$status = fwrite($accfile,$strout);
} #foreach

$strout = '</ul><font color="GREEN">W/C</font> = <i>Wild Collected</i>';
$strout .= '<br><img src="/images/smallcamera.gif"></img> = Images available';
$strout .= '<br><img src="/images/globe-18.png"></img> = Map available';
$strout .= '<br><img src="/images/herbarium-book2-20px.png"></img> = voucher(s) on file at <a href="http://bgbaseserver.eeb.uconn.edu/aboutherb.html"><b>CONN</b></a> for this accession';
$status = fwrite($accfile,$strout);

$strout = '</div></div></div></div></div></div></div>'.chr(10);
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
