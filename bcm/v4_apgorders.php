<?php
### AP Web Generator ###

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'SELECT distinct famcomm.order from famcomm order by famcomm.order';

foreach($db->query($sql) as $row) {
	### CREATE OUTPUT FILE
	$file_spec = $rootdir.strtr(strtolower($row['order']),' ','_').'.html';
	echo 'Generating '.$file_spec.chr(10);
	$accfile = fopen($file_spec,'w');

	### BEGIN OUTPUTTING HTML CODE
	$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10).'<head>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<TITLE>UConn Biodiversity Conservatory Collections by Family</TITLE>'.chr(10);
	$status = fwrite($accfile,$strout);

	### CREATE META TAGS

	$strout = '<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" >'.chr(10);
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

	$strout = '<div id="uc-main" class="container"><p></p>'.chr(10);	
	$strout .= '<div class="row" id="row1">'.chr(10);

	$strout .= '<div id="home3" class="span4" role="complementary">'.chr(10);
	$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
	$strout .= '<h2 class="widget-title">Collection Holdings:</h2>'.chr(10);		
	$strout .= '<div id="col_holdings" class="textwidget">'.chr(10);
	$status = fwrite($accfile,$strout);

	$sql = 'SELECT DISTINCT famcomm.family, famcomm.subclass, famcomm.apgweblink FROM famcomm WHERE famcomm.order = "'.$row['order'].'" ORDER BY family';
	$sth = $db->prepare($sql);
	$sth->execute();
	$count = $sth->rowCount();
	$strout = '<h1>';
	$status = fwrite($accfile,$strout);

	$strout = $row['order'].'</h1><ul>';
	$status = fwrite($accfile,$strout);

	$family = "";
	$subfamily = "";
	$tribe = "";

	#### Current Accessions ####
	$sql = 'select gh_inv.codeno, gh_inv.latin_name,gh_inv.wildcoll,gh_inv.keywords,gh_inv.acc_date,classify.family,classify.subfamily,classify.tribe,classify.subtribe ';
	$sql .= 'from gh_inv,classify,famcomm ';
	$sql .= 'where classify.family = famcomm.family and gh_inv.genus = classify.genus ';
	$sql .= 'and famcomm.order = "'.$row['order'];
	$sql .= '" and projnum="GEN_COLL" order by classify.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.latin_name';
	foreach($db->query($sql) as $row2) {
		if (trim($row2['family'])<>$family) {
			$family = trim($row2['family']);
			$strout = '</ul>'.chr(10);
			$strout .= '<h2 name="'.$family.'" id="'.$family.'">'.	$family.'</h2><ul>';	
			$status = fwrite($accfile,$strout);
			};
		#####Check if subfamily or tribe has changed		
		IF (($row2['subfamily'] <> $subfamily) or ($row2['tribe'] <> $tribe)){
			$tempstr = $row2['subfamily'];
			$tempstr2 = $row2['tribe'];
			IF ((!empty($tempstr)) or (!empty($tempstr2))) {
				$strout = '</ul><h4>Subfamily '.$tempstr.chr(10);
				$tempstr = $row2['tribe'];
				IF (($tempstr <> $tribe)and(!empty($tempstr))) $strout .= '<br>Tribe '.$row2['tribe'].chr(10);
				$strout .= '</h4><ul>';
				$status = fwrite($accfile,$strout);
			}
			$tribe = trim($row2['tribe']);		
			$subfamily = trim($row2['subfamily']);
		}
		$strout = '<li><a href="';
		$strout .= $row2['codeno'].'.html">'.$row2['latin_name'].'</a>'.chr(10);
		$status = fwrite($accfile,$strout);
		### wild collected?
		$temp = $row2['wildcoll'];	
		if ($temp) {
			$strout = ' <font color="GREEN">W/C</font>';
			$status = fwrite($accfile,$strout);
		}
		### check if flowering
		$sql = 'select * from history where codeno='.$row2['codeno'].' and class="FLOWERING"';
		$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
		$sql .= ' and week(history.date,3)=week(curdate(),3)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->rowCount()>0) {
			$strout = '<img src="http://florawww.eeb.uconn.edu/images/flower-rose.gif"></img>';
			$status = fwrite($accfile,$strout);
			}
		### check for images
		$imagemask = $imagedir.'byspecies/thumb/'.strtr($row2['latin_name']," ","_").'*.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
 			$strout = '<img src="/images/smallcamera.gif"></img>';
			$status = fwrite($accfile,$strout);
		}
		### check for TDWG map
		$imagemask = $imagedir.'maps/tdwg/'.$row2['codeno'].'.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = ' <img src="/images/globe-18.png"></img>';
			$status = fwrite($accfile,$strout);
		}
		### check for voucher (keyword)
		if (strpos($row2['keywords']," voucher ")) {
			$strout = ' <img src="/images/herbarium-book2-20px.png"></img>';
			$status = fwrite($accfile,$strout);
			}	
		##### Check if NEW (past 90 days)
		$accdate = strtotime($row2['acc_date']);
		if (time()-$accdate<7776000) {
			$strout = ' <img src="http://florawww.eeb.uconn.edu/images/new.jpg"></img> ';	
			$status = fwrite($accfile,$strout);	
		}	
		$strout = chr(10);
		$status = fwrite($accfile,$strout);	
	} # foreach row2

	$strout = '</ul>';
	$status = fwrite($accfile,$strout);

	$strout = '</ul><br><font color="GREEN">W/C</font> = <i>Wild Collected</i><br> <img src="/images/flower-rose.gif"></img> = Currently Flowering';
	$strout .= '<br><img src="/images/smallcamera.gif"></img> = Image(s) Available';
	$strout .= '<br><img src="/images/globe-18.png"></img> = map available for this accession'.chr(10);
	$strout .= '<br><img src="/images/herbarium-book2-20px.png"></img> = voucher(s) on file at <a href="http://bgbaseserver.eeb.uconn.edu/aboutherb.html"><b>CONN</b></a> for this accession';
	$strout .= '<br><img src="/images/new.jpg"></img> = accession added within past 90 days'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<p><p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '</div></div></div>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<div class="row" id="row3">'.chr(10);	
	$strout = $strout.'<div id="home3" class="span6" role="complementary">'.chr(10);
	$strout = $strout.'<div id="text-3" class="widget widget_text">'.chr(10);
	$strout = $strout.'<h2 class="widget-title">Summary of EEB Greenhouse Accessions:</h2>'.chr(10);			
	$strout = $strout.'<div class="textwidget">'.chr(10);
	$status = fwrite($accfile,$strout);

	### BEGIN SECOND COLUMN
#	### Random Banner Image Slider
#
#	$strout ='<div id="imageblock" class="container">'.chr(10); 
#	$status = fwrite($accfile,$strout);
#	$strout ='<div id="slider" class="span-12 last"><center>'.chr(10); 
#	$status = fwrite($accfile,$strout);
#
#	### Get Random Banner Images
#
#	$images = glob($imagedir."byspecies/*00.jpg");
#	$images = array_rand(array_flip($images),20);
#	shuffle($images);
#
#	foreach ($images as $filename) {
#		 Fetch codeno for URL
#
#		$name=strtr($filename,"_"," ");
#		$name = preg_replace('#/var/www/images/byspecies/#','',$name);
#		$name = preg_replace('#00.jpg#','',$name);	
#		$sql='select codeno,latin_name from gh_inv where latin_name="'.$name.'"';
#		$sql_result=mysql_query($sql);
#		if (!$sql_result) {
#			echo mysql_error(); } else {			
#		$strout='<a href="'.mysql_result($sql_result,0,'codeno').'.html">';
#		$status = fwrite($accfile,$strout);	
#		$strout=preg_replace('#/var/www/#','<img src="http://florawww.eeb.uconn.edu/',$filename);
#		$strout = $strout.'" alt="<i>Random Accession: </i>'.$name.'" /></a>'.chr(10);
#		$status = fwrite($accfile,$strout);	
#		}
#	}

#	$strout='</center></div></div>'.chr(10);
#	$status = fwrite($accfile,$strout);

	### Collection Statistics

	$strout = '<div id="incl_fam" class="container">';
	$status = fwrite($accfile,$strout);

	#$strout ='<p><p><h4>Collection Holdings:</h4> <ul>';
	#$status = fwrite($accfile,$strout);

	$sql = 'SELECT DISTINCT famcomm.family, famcomm.subclass, famcomm.apgweblink FROM famcomm WHERE famcomm.order = "'.$row['order'].'" ORDER BY family';
	foreach($db->query($sql) as $row3) {
		$strout = chr(10).'<li>';
		if (strlen($row3['apgweblink'])>0) {
			$strout .= '<a href="'.$row3['apgweblink'].'" target="blank"><img src="/images/mobot.gif" border="0" width=16px"></img></a> '.chr(10);
		}
		$status = fwrite($accfile,$strout);
		$sql = 'SELECT gh_inv.codeno from gh_inv,classify where classify.genus=gh_inv.genus ';
		$sql .= 'and classify.family="'.$row3['family'].'" ';
		$sql .= 'and gh_inv.projnum="GEN_COLL"';
		$sth = $db->prepare($sql);
		$sth->execute();
		$tally = $sth->rowCount();
		$strout = '';
		if ($tally>0) $strout .= '<a href="#'.$row3['family'].'">';	
		$strout .= $row3['family'];
		if ($tally>0) $strout .= '</a>';
		$status = fwrite($accfile,$strout);		

		if ($tally>0) {
			$strout = ' - <b>'.$tally.'</b>';
			$status = fwrite($accfile,$strout);
		}
	} # foreach row3

	$strout =chr(10).'</ul>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout =chr(10).'</div></div></div>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout =chr(10).'</div></div>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout =chr(10).'</div></div>'.chr(10);
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
} # foreach order

### CLOSE OUTPUT FILE
$db = null;
return true;

?>

