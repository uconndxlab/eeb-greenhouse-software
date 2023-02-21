<?php
### BRU Country Listing Generator ###

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'SELECT * from tblLevel3';
foreach($db->query($sql) as $row) {

	### CREATE OUTPUT FILE
	$file_spec = $webdir.'BRU_'.$row['l3code'].'.html';
	echo 'Generating '.$file_spec.chr(10);
	$accfile = fopen($file_spec,'w+');

	### BEGIN OUTPUTTING HTML CODE
	$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10).'<head>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<TITLE>UConn Biodiversity Conservatory Collections  native to '.$row['l3name'].'</TITLE>'.chr(10);
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
	$strout .= '<div id="home3" class="span8" role="complementary">'.chr(10);
	$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
	$strout .= '<h2 class="widget-title">Collection Holdings native to '.$row['l3name'].':</h2>'.chr(10);
	$status = fwrite($accfile,$strout);

	$imagemask = $imagedir.'maps/tdwg/'.$row['l3code'].'.jpg'; ##### TDWG MAP FILE CHECK
	if (file_exists($imagemask)) {
		$strout=preg_replace('#/var/www/images/maps/tdwg/#','<a href="http://florawww.eeb.uconn.edu/images/maps/tdwg/',$imagemask);
		$strout=preg_replace('#.jpg#','.png',$strout).'">';
		$status=fwrite($accfile,$strout);	
		$strout=preg_replace('#/var/www/images/maps/tdwg/#','<img src="http://florawww.eeb.uconn.edu/images/maps/tdwg/',$imagemask).'"></img></a>';
		$status=fwrite($accfile,$strout);
	} # endif
			
	$strout = '<div id="col_holdings" class="textwidget"><ul>'.chr(10);
	$status = fwrite($accfile,$strout);

	$bru = $row['l3code'];;
	$family = "";
	$subfamily = "";
	$tribe = "";
	$i = 0; ## Counter for number of accessions
	#### Current Accessions ####
	$sql = 'select gh_inv.codeno, gh_inv.latin_name,gh_inv.cntry_orig,gh_inv.wildcoll,gh_inv.acc_date,classify.family,classify.subfamily,classify.tribe,classify.subtribe ';
	$sql .= 'from gh_inv,classify ';
	$sql .= 'where gh_inv.genus = classify.genus ';
	$sql .= 'and gh_inv.tdwg like BINARY "%'.$row['l3code'].'%"';
	$sql .= ' and projnum="GEN_COLL" order by classify.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.latin_name';
	foreach($db->query($sql) as $row2) {
		if (trim($row2['family'])<>$family) {
			$family = trim($row2['family']);
			$strout = '</ul><h2 name="'.$family.'" id="'.$family.'">'.	$family.'</h2><ul>';	
			$status = fwrite($accfile,$strout);
		}; # endif
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
			$subfamily=trim($row2['subfamily']);
		}
		$strout = '<li><a href="';
		$strout .= $row2['codeno'].'.html">'.$row2['latin_name'].'</a>'.chr(10);
		$status = fwrite($accfile,$strout);
		$strout = ' - '.$row2['cntry_orig'].chr(10);
		$status = fwrite($accfile,$strout);

		### wild collected?
		$temp = $row2['wildcoll'];	
		if ($temp) {
			$strout = ' <font color="GREEN">W/C</font>';
			$status = fwrite($accfile,$strout);
		} # if temp

		### check for TDWG map
		$imagemask = $imagedir.'maps/tdwg/'.$row2['codeno'].'.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = ' <img src="/images/globe-18.png" />';
			$status = fwrite($accfile,$strout);
		}
		##### Check if NEW (past 90 days)
		$accdate = strtotime($row2['acc_date']);
		if (time()-$accdate<7776000){
			$strout = ' <img src="http://florawww.eeb.uconn.edu/images/new.jpg" /> ';
			$status = fwrite($accfile,$strout);
		}
		### check if flowering
		$sql = 'select * from history where codeno='.$row2['codeno'].' and class="FLOWERING"';
		$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
		$sql .= ' and week(history.date,3)=week(curdate(),3)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->fetchColumn()) {
			$strout = '<img src="http://florawww.eeb.uconn.edu/images/flower-rose.gif" />';
			$status = fwrite($accfile,$strout);
		}
		##### end bloom check

		### check for images	
		$imagemask = $imagedir.'byspecies/thumb/'.strtr($row2['latin_name']," ","_").'*.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout='<img src="/images/smallcamera.gif" />';
			$status = fwrite($accfile,$strout);
		}	
		$strout = chr(10); 
		$status = fwrite($accfile,$strout);
		$i++; # increment counter	
	} # foreach row2



	$strout = '</ul>';
	$strout .= 'The EEB Greenhouses are currently cultivating <b>'.$i.'</b> accessions native to <b>'.$row['l3name'].'</b><p>';
	
	$status = fwrite($accfile,$strout);

	$strout = '<p><font color="GREEN">W/C</font> = <i>Wild Collected</i><br>'.chr(10);
	$strout .= '<img src="/images/flower-rose.gif"></img> = indicates flowering in past 14 days<br>'.chr(10);
	$strout .= '<img src="/images/smallcamera.gif"></img> = images available for this accession<br>'.chr(10);
	$strout .= '<img src="/images/globe-18.png"></img> = map available for this accession<br>'.chr(10);
	$strout .= '<img src="/images/new.jpg"></img> = accession added within past 90 days<p>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout='<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '</div></div></div>'.chr(10);
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
} #foreach row

$db = null;
return true;

?>

