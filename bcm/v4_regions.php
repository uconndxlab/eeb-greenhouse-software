<?php
### BRU Regional Listing Generator ###

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'SELECT * from tblLevel2';
foreach($db->query($sql) as $row) {
	### CREATE OUTPUT FILE

	$file_spec = $webdir.'region'.strval($row['l2code']).'.html';
	$mapname = '/images/maps/tdwg/region'.strval($row['l2code']).'.jpg';

	echo 'Generating '.$file_spec.chr(10);
	$accfile = fopen($file_spec,'w');

	### BEGIN OUTPUTTING HTML CODE

	$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10).'<head>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<TITLE>UConn Biodiversity Conservatory Collections  from '.$row['l2region'].'</TITLE>'.chr(10);
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
	$strout .= '<h2 class="widget-title">Collection Holdings from '.$row['l2region'].':</h2>'.chr(10);

	if (file_exists('/var/www'.$mapname)) {
		$strout .= '<a href="/images/maps/tdwg/region'.$row['l2code'].'.png">';
		$strout .= '<img src="'.$mapname.'"></img></a>';
		$status = fwrite($accfile,$strout);
	}

	### Output links to BRU level files
	$sql = 'SELECT * from tblLevel3 where l2code='.$row['l2code'];
	$strout = '<p><b>Includes:</b><br><ol>';
	foreach($db->query($sql) as $bru) {	
		$strout .= '<li><a href="BRU_'.$bru['l3code'].'.html">';
		$strout .= $bru['l3code'].'</a>: '.$bru['l3name'];
	} # foreach
	$strout .= '</ol>'.chr(10);
	$status = fwrite($accfile,$strout);	
	### END BRU LEVEL OUTPUT			

	$strout='<div id="col_holdings" class="textwidget"><ul>'.chr(10);
	$status = fwrite($accfile,$strout);

	$region = $row['l2region'];
	$family = "";
	$subfamily = "";
	$tribe = "";
	$i = 0; # total tally

	#### Current Accessions ####
	$sql = 'select gh_inv.codeno, gh_inv.latin_name,gh_inv.cntry_orig,gh_inv.wildcoll,classify.family,classify.subfamily,classify.tribe,classify.subtribe ';
	$sql .= 'from gh_inv,classify ';
	$sql .= 'where gh_inv.genus = classify.genus ';
	$sql .= 'and gh_inv.tdwg like "%'.strval($row['l2code']).'%"';
	$sql .= 'and gh_inv.tdwg not like "%('.strval($row['l2code']).')%"'; ## check for code in parentheses - ie not native
	$sql .= ' and projnum="GEN_COLL" order by classify.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.latin_name';
	foreach($db->query($sql) as $acc) {
		if (trim($acc['family'])<>$family) {
			$family = trim($acc['family']);
			$strout = '</ul><h2 name="'.$family.'" id="'.$family.'">'.	$family.'</h2><ul>';	
			$status = fwrite($accfile,$strout);
		};
		#####Check if subfamily or tribe has changed
			
		IF (($acc['subfamily'] <> $subfamily) or ($acc['tribe'] <> $tribe)){
			$tempstr = $acc['subfamily'];
			$tempstr2 = $acc['tribe'];
			IF ((!empty($tempstr)) or (!empty($tempstr2))) {
				$strout = '</ul><h4>Subfamily '.$tempstr.chr(10);
				$tempstr = $acc['tribe'];
				IF (($tempstr <> $tribe)and(!empty($tempstr))) $strout .= '<br>Tribe '.$acc['tribe'].chr(10);
				$strout .= '</h4><ul>';
				$status = fwrite($accfile,$strout);
			}
			$tribe = trim($acc['tribe']);		
			$subfamily = trim($acc['subfamily']);
		}
		$strout ='<li><a href="';
		$strout .= $acc['codeno'].'.html">'.$acc['latin_name'].'</a>'.chr(10);
		$status = fwrite($accfile,$strout);
		$strout = ' - '.$acc['cntry_orig'].chr(10);
		$status = fwrite($accfile,$strout);
	
		### wild collected?
		$temp = $acc['wildcoll'];	
		if ($temp) {
			$strout = ' <font color="GREEN">W/C</font>';
			$status = fwrite($accfile,$strout);
		}

		### check if flowering		
		$sql = 'select * from history where codeno='.$acc['codeno'].' and class="FLOWERING"';
		$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
		$sql .= ' and week(history.date,3)=week(curdate(),3)';
		$sth = $db->prepare($sql);
		$sth->execute();
		if ($sth->fetchColumn()) {
			$strout = '<img src="/images/flower-rose.gif"></img>';
			$status = fwrite($accfile,$strout);
		}
		##### end bloom check

		### check for images		
		$imagemask = $imagedir.'byspecies/thumb/'.strtr($acc['latin_name']," ","_").'*.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = '<img src="/images/smallcamera.gif"></img>';
			$status = fwrite($accfile,$strout);
		}	
		$strout = chr(10);
		$status = fwrite($accfile,$strout);

		### check for TDWG map
		$imagemask = $imagedir.'maps/tdwg/'.$acc['codeno'].'.jpg';
		$imagearray = glob($imagemask);
		if (count($imagearray)>0){
			$strout = ' <img src="/images/globe-18.png"></img>';
			$status = fwrite($accfile,$strout);
		}
	$i++; #total count
	} # foreach acc
	$strout = '</ul>';
	$strout .= 'The EEB Greenhouses are currently cultivating <b>'.$i.'</b> accessions native to <b>'.$row['l2region'].'</b><p>';	
	$result = fwrite($accfile,$strout);

	$strout = '<p><font color="GREEN">W/C</font> = <i>Wild Collected</i>';
	$strout .= '<br> <img src="/images/flower-rose.gif"></img> = Currently Flowering';
	$strout .= '<br> <img src="/images/smallcamera.gif"></img> = Image(s) Available';
	$strout  .= '<br><img src="/images/globe-18.png"></img> = map available for this accession'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = '</div></div></div>'.chr(10);
	$status = fwrite($accfile,$strout);

	$strout = chr(10).'</div></div>'.chr(10);
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
} # foreach row
### CLOSE OUTPUT FILE

$db = null;
return true;

?>

