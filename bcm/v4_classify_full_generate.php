<?php
### Modified code to generate a family/subfamily/tribe/subtribe

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

$sql = 'set sql_mode = ""';
$sth = $db->prepare($sql);
$sth->execute();

$result = $db->query($sql);
if ($result !== false) {
    echo 'Query OK'.chr(10);
} else {
    echo 'The SQL query failed with error '.$db->errorCode().chr(10);
}

### CREATE OUTPUT FILE

$file_spec = $rootdir.'classify.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '<TITLE>UConn Biodiversity Conservatory Collections by Family</TITLE>'.chr(10);
$status = fwrite($accfile,$strout);

### CREATE META TAGS

$strout = '<head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" >'.chr(10);
$status = fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select head,body from ghmaster';

$result = $db->query($sql);
if ($result !== false) {
    echo 'Query OK'.chr(10);
} else {
    echo 'The SQL query failed with error '.$db->errorCode().chr(10);
}

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
$strout .= '<h2 class="widget-title"> General Collection by Family as of:<br> '.date("l, F jS, Y").'</h2>'.chr(10);		
$strout .= '<div id="col_holdings" class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

### Place page jumps here

$strout = '<center>Jump To: '.chr(10);
$strout .= '<a href="classify.html#A">A</a>'.chr(10);
$strout .= '<a href="classify.html#B">B</a>'.chr(10);
$strout .= '<a href="classify.html#C">C</a>'.chr(10);
$strout .= '<a href="classify.html#D">D</a>'.chr(10);
$strout .= '<a href="classify.html#E">E</a>'.chr(10);
$strout .= '<a href="classify.html#F">F</a>'.chr(10);
$strout .= '<a href="classify.html#G">G</a>'.chr(10);
$strout .= '<a href="classify.html#H">H</a>'.chr(10);
$strout .= '<a href="classify.html#I">I</a>'.chr(10);
$strout .= '<a href="classify.html#J">J</a>'.chr(10);
$strout .= '<a href="classify.html#K">K</a><br>'.chr(10);
$status = fwrite($accfile,$strout);
$strout = '<a href="classify.html#L">L</a>'.chr(10);
$strout .= '<a href="classify.html#M">M</a>'.chr(10);
$strout .= '<a href="classify.html#O">O</a>'.chr(10);
$strout .= '<a href="classify.html#P">P</a>'.chr(10);
$strout .= '<a href="classify.html#Q">Q</a>'.chr(10);
$strout .= '<a href="classify.html#R">R</a>'.chr(10);
$strout .= '<a href="classify.html#S">S</a>'.chr(10);
$strout .= '<a href="classify.html#T">T</a>'.chr(10);
$strout .= '<a href="classify.html#U">U</a>'.chr(10);
$strout .= '<a href="classify.html#V">V</a>'.chr(10);
$strout .= '<a href="classify.html#W">W</a>'.chr(10);
$strout .= '<a href="classify.html#X">X</a>'.chr(10);
$strout .= 'Y'.chr(10);
$strout .= '<a href="classify.html#Z">Z</a>'.chr(10);
$status = fwrite($accfile,$strout);
$strout = '</center>';
$status = fwrite($accfile,$strout);

$strout = '<div id="incl_fam" class="container">'.chr(10);
$status = fwrite($accfile,$strout);

### GENERATE FAMILY LISTING CODE

$sql = 'SELECT gh_inv.codeno,gh_inv.latin_name,gh_inv.commonname,gh_inv.author,gh_inv.wildcoll,acc_date,gh_inv.projnum,gh_inv.source,gh_inv.coll_rank,';
$sql .= 'gh_inv.e_cl_rank,gh_inv.e_tx_rank,';
$sql .= 'classify.subfamily,classify.tribe,classify.subtribe,classify.scheme, famcomm.family,'; 
$sql .= 'famcomm.commonname,famcomm.fam_filen,famcomm.lump_fam ';
$sql .= 'FROM gh_inv,classify,famcomm ';
$sql .= 'WHERE classify.genus = gh_inv.genus ';
$sql .= 'and gh_inv.projnum="GEN_COLL" ';
$sql .= 'and famcomm.family = classify.family ';
$sql .= 'and gh_inv.codeno <> 0 ';
$sql .= 'ORDER BY famcomm.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.projnum,gh_inv.genus,gh_inv.species';

$i=0;
$family = "";
$subfamily = "";
$tribe = "";
$linkpos = "A"; ## used for placing internal links

$addlink = FALSE;

$strout = '<ul>';
$status = fwrite($accfile,$strout);

### check errors
$result = $db->query($sql);
if ($result !== false) {
    echo 'Query OK'.chr(10);
} else {
    echo 'The SQL query failed with error '.$db->errorCode().chr(10);
}

foreach($db->query($sql) as $row) {
	##### Check & output family name

	IF ($row['family'] <> $family) {
		$strout = '</ul>';
		
		$family = $row['family'];
		IF (substr($family,0,1) == $linkpos) {
			$addlink = TRUE;
			$strout .= '<div id="'.$linkpos.'">';
			$linkpos++;	
			IF ($linkpos=="Y") $linkpos++;
		}		
		$strout .= '<h2>'.$family.'</h2>';
		IF ($addlink){
			$strout .= '</div>';
			$addlink = FALSE;
			}		
		$strout .= '<ul>';			
		$status = fwrite($accfile,$strout);
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
			$status = fwrite($accfile,$strout);
		}
		$tribe = trim($row['tribe']);		
		$subfamily = trim($row['subfamily']);
	}

	$strout = chr(10).'<li>';
	$subtribe = trim($row['subtribe']);
	if (!empty($subtribe)) $strout .= $row['subtribe'].': ';
	$strout .= '<a href="'.$row['codeno'].'.html">';
	$strout .= '<i>'.$row['latin_name'].'</i>';
	if ($row['projnum']=="GEN_COLL") {
		$strout .= '</a>';
		} else {
		$strout .= '</a>: '.$row['projnum'].' {'.$row['source'].'}';
		}	
	$status = fwrite($accfile,$strout);

	##### Check if wild-collected
	
	$temp = $row['wildcoll'];	
	if ($temp) {
		$strout = ' <font color="GREEN">W/C</font>';
		$status = fwrite($accfile,$strout);
	}
	##### Check if images exist
	
	$imagemask = $imagedir.'byspecies/thumb/'.strtr($row['latin_name']," ","_").'*.jpg';
	$imagearray = glob($imagemask);
	if (count($imagearray)>0){
		$strout = '<img src="/images/smallcamera.gif"></img>';
		$status = fwrite($accfile,$strout);
	}
	##### Check if NEW (past 90 days)
	
	$accdate = strtotime($row['acc_date']);
	if (time()-$accdate<7776000) $status = fwrite($accfile,' <img src="http://florawww.eeb.uconn.edu/images/new.jpg"></img> ');

} #foreach

$strout = '</ul></td><p><font color="GREEN">W/C</font> = <i>Wild Collected</i>'.chr(10);
$status = fwrite($accfile,$strout);


$strout = '<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$status = fwrite($accfile,$strout);
$strout = '</div></div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

### BEGIN SECOND COLUMN

$strout = '<div class="row" id="row3">'.chr(10);	
$strout = $strout.'<div id="home3" class="span6" role="complementary">'.chr(10);
$strout = $strout.'<div id="text-3" class="widget widget_text">'.chr(10);
$strout = $strout.'<h2 class="widget-title">Image Gallery:</h2>'.chr(10);			
$strout = $strout.'<div class="textwidget">'.chr(10);
$status = fwrite($accfile,$strout);

$strout = '<center><div id="photo">'.chr(10); 
$status = fwrite($accfile,$strout);
$strout = '<div id="slider">'.chr(10); 
$status = fwrite($accfile,$strout);

### Get Random Banner Images

$images = glob($imagedir."byspecies/*00.jpg");
$images = array_rand(array_flip($images),20);
shuffle($images);

foreach ($images as $filename) {
	# Fetch codeno for URL
	
	$name = strtr($filename,"_"," ");
	$name = preg_replace('#/var/www/images/byspecies/#','',$name);
	$name = preg_replace('#00.jpg#','',$name);	
	$sql = 'select codeno,latin_name from gh_inv where latin_name="'.$name.'"';

	### check errors
	$result = $db->query($sql);
	if ($result !== false) {
    		echo 'Query OK'.chr(10);
		$sth = $db->prepare($sql);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		$strout = '<a href="'.$result['codeno'].'.html">';
		$status = fwrite($accfile,$strout);	
		$strout = preg_replace('#/var/www/#','<img src="http://florawww.eeb.uconn.edu/',$filename);
		$strout .= '" alt="<i>Random Accession: </i>'.$name.'" /></a>'.chr(10);
		$status = fwrite($accfile,$strout);	
		} else {
    		echo 'The SQL query failed with error '.$db->errorCode().chr(10);
	}# if result OK
} #foreach images

$strout = '</div><div></center><hr>'.chr(10);
$status = fwrite($accfile,$strout);

### Collection Statistics

$strout = '<h4>Collection Statistics:</h4>'.chr(10);
$status = fwrite($accfile,$strout);

# Calculate various collections statistics here

$sql = 'select codeno,quant,quant2,quant3 from gh_inv where gh_inv.projnum="GEN_COLL" or gh_inv.projnum="SECURE" or gh_inv.projnum="JONES" or gh_inv.projnum="OPEL"or gh_inv.projnum="YUAN"';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$colltotal = $num;
$strout = '<p><B>'.$num.'</B> accessions in the General Collections<ul><li>'.chr(10);
$status = fwrite($accfile,$strout);
$i=0;
$sum=0;
foreach($db->query($sql) as $row) {
	$sum = $sum+$row['quant']+$row['quant2']+$row['quant3'];
} #foreach quant
$strout = '<b>'.$sum.' </B><i>individual plants under cultivation</I>'.chr(10);
$status = fwrite($accfile,$strout);

### Count distinct families
$sql = 'select distinct classify.family from gh_inv,classify where (gh_inv.projnum="GEN_COLL" or projnum="SECURE")';
$sql .= ' and gh_inv.genus=classify.genus';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<li><B>'.$num.'</B><i> families are represented</i>'.chr(10);
$status = fwrite($accfile,$strout);

### Wild collected
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and wildcoll';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<li><B>'.$num;
$strout .= '</B><i> are documented wild collected specimens</i></ul>'.chr(10);
$status = fwrite($accfile,$strout);

### IUCN Red List 2010
$strout = '<a href="http://www.iucnredlist.org/" target="_blank"><font color="RED">IUCN Red List Status (2010):</font></a><ul>';
$status = fwrite($accfile,$strout);
$sql = 'select distinct redlist2010 from gh_inv where redlist2010>"" order by redlist2010';
foreach($db->query($sql) as $row2) {
	$sql='select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and redlist2010="'.$row2['redlist2010'].'"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$num = $sth->rowCount();
	$strout = '<li>'.$row2['redlist2010'].': <b>'.$num.'</b>'.chr(10);
	$status = fwrite($accfile,$strout);
} # foreach row2
$strout = '</ul>';
$status = fwrite($accfile,$strout);

### CITES

$strout = '<a href="http://www.cites.org/" target="_blank"><font color="green">CITES Status:</font></a>'.chr(10).'<ul>';
$status = fwrite($accfile,$strout);
$sql = 'select distinct cites from gh_inv where cites>"" order by cites';
foreach($db->query($sql) as $row2) {
	$sql='select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and cites="'.$row2['cites'].'"';
	$sth = $db->prepare($sql);
	$sth->execute();
	$num = $sth->rowCount();
	$strout = '<li>'.$row2['cites'].': <b>'.$num.'</b>'.chr(10);
	$status = fwrite($accfile,$strout);
} # foreach row2

#while ($i<$j) {
#	$sql='select codeno from gh_inv where cites="'.mysql_result($sql_result1,$i,'cites').'"';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	} else {
#		$num=mysql_numrows($sql_result);
#		$strout='<li>'.mysql_result($sql_result1,$i,'cites').': <b>'.$num.'</b>'.chr(10);
#		$status = fwrite($accfile,$strout);
#	$i++;
#	}
#	}
#}
$strout = '</ul><hr>';
$status = fwrite($accfile,$strout);

### New Accessions Section
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and acc_date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<B>'.$num;
$strout .= '</B> added in the past year<br>'.chr(10);
$status = fwrite($accfile,$strout);


### Confirmations
$sql = 'select codeno from gh_inv where gh_inv.projnum="GEN_COLL" and confirm> date_sub(curdate(),interval 60 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<B>'.$num;
$strout .= '</B> verified in the past 60 days<BR>'.chr(10);
$status = fwrite($accfile,$strout);

### Class Use
$sql = 'select codeno from history where history.class="CLASS" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<hr><B>'.$num;
$strout .= '</B> used in UConn classes in the past 12 months<BR>'.chr(10);
$status = fwrite($accfile,$strout);

### Outreach Use
$sql = 'select codeno from history where history.class="OUTREACH" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<B>'.$num;
$strout .= '</B> used in outreach activities in the past 12 months<BR>'.chr(10);
$status = fwrite($accfile,$strout);

### Trades
$sql = 'select codeno from history where history.class="TRADE" and date> date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<B>'.$num;
$strout .= '</B> sent to other institutions in the past 12 months<BR>'.chr(10);
$status = fwrite($accfile,$strout);

### Spray
$sql = 'select history.codeno from history where zone < 6000 and history.class="SPRAY" and date> date_sub(curdate(),interval 30 day)';
$sth = $db->prepare($sql);
$sth->execute();
$num = $sth->rowCount();
$strout = '<B>'.$num;
$strout .= '</B> <a href="ipm.html">treated for pests</a> in the past 30 days<BR>'.chr(10);
$status = fwrite($accfile,$strout);

### count images in byspecies
$imagemask = '/var/www/images/byspecies/*.jpg';
$imagearray = glob($imagemask);
$num = count($imagearray);
$strout = '<B>'.$num;
$strout .= '</B> accession images are in the database<BR>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = chr(10).'</div></div></div>'.chr(10);
$status = fwrite($accfile,$strout);

$strout = chr(10).'</div></div>'.chr(10);
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
$db = null;
return true;

?>

