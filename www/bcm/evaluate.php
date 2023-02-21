<?php
function evaluate($codeno)
{
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

#$codeno=201900242;  ##################  TEMPORARY TESTING

### Recalculate Collection Evaluation
$points = 0;
$evalcriteria = '';
$tx_temp = 0;
$cl_temp = 0;

$sql = 'select * from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

// Score 1-3 points in first 3 years as a new plant boost
$x = strtotime($result['acc_date']); //time stamp of acc_date
#$y = time()-31536000; // timestamp of 1 year ago
if ($x>(time()-94608000)) $points = $points + 1; // 1 point for 3 years
if ($x>(time()-63072000)) $points = $points + 1; // 2 points for 2 years
if ($x>(time()-31536000)) $points = $points + 1; // 3 points for 1 year
if ($points > 0) $evalcriteria .= '<li>New Plant Boost = '.$points.'<br>';

$genus = $result['genus'];
$section = $result['section'];
$series = $result['series'];

#### IMPORTANCE RANKING
#	*** Score 10 points for Importance = 'Feature'
$x = $result['importance']; 
if ($x=='Feature') {
	$points = $points + 10;
	$evalcriteria = $evalcriteria.'<li>Feature Specimen = 10';
}
#	*** Score 10 points for Importance = 'Research-Active'
$x = $result['importance']; 
if ($x=='Research-Active') {
	$points = $points + 10;
	$evalcriteria = $evalcriteria.'<li>Active Research = 10';
}
#	*** Score 5 Points for Importance = 'Essential' incl sole member of family or subfamily
$x = $result['importance']; 
if ($x=='Essential') {
	$points = $points + 5;
	$evalcriteria = $evalcriteria.'<li>Essential Specimen = 5<br>';
}
#	*** Score 2 points for Importance = 'Desirable' - incl sole member of genus or tribe
$x = $result['importance']; 
if ($x=='Desirable') {
	$points = $points + 2;
	$evalcriteria = $evalcriteria.'<li>Desirable Specimen = 2';
}
#	*** DEDUCT 10 points for Importance = 'Redundant'
$x = $result['importance']; 
if ($x=='Redundant') {
	$points = $points - 10;
	$evalcriteria = $evalcriteria.'<li>Redundant Specimen = (-10)';
}
#	*** DEDUCT 5 points for Importance = 'Unresolved Name'
$x = $result['importance']; 
if ($x=='Unresolved Name') {
	$points = $points - 5;
	$evalcriteria = $evalcriteria.'<li>Unresolved Name = (-5)';
}
#	*** DEDUCT 20 points for Importance = 'Oversize: cull'
$x = $result['importance']; 
if ($x=='Oversize:cull') {
	$points = $points - 20;
	$evalcriteria = $evalcriteria.'<li>Oversize:cull Specimen = (-20)';
}

### BGCI Status - 1pt for 'Accepted' status, 1pt each for exsitu sites fewer than 6
$x = $result['bgci_status']; 
if ($x=='Accepted') {
	$points = $points + 1;
	$evalcriteria = $evalcriteria.'<li>BGCI:Accepted = 1';
}
$y = $result['bgci_exsitusites']; 
if (($y < 6) and ($y>0)) {
	$points = $points + (6-$y);
	$evalcriteria = $evalcriteria.'<li>BGCI: '.$y.' ExSitu Sites = '.(6-$y).'';
}


### FLORIFEROUSNESS - in flower more than 10 weeks in past year
$sql='select codeno from history where class="FLOWERING" and codeno='.$codeno.' and history.date>date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($sth->rowcount($tempresult)>9) {
	$points=$points+3;
	$evalcriteria=$evalcriteria.'<li>Reliable Bloomer = 3';
	}

### PEST MAGNET - Deduct one point for every 5 pest scouting notations over 10
$sql='select codeno from history where class="SCOUT" and codeno='.$codeno.' and history.date>date_sub(curdate(),interval 365 day)';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$num=(round($sth->rowcount($tempresult)/5))-2;
if ($num>0) {
	$points=$points-$num;
	$evalcriteria=$evalcriteria.'<li>Pest Prone Plant = -'.$num;
}

#	*** Score 5 points if accession of known wild collected provenance
if ($result['wildcoll']) {
	$points = $points + 5;
	$evalcriteria = $evalcriteria.'<li>Wild Collected Specimen = 5';
}
#	*** Score for Red List Status 2010
if ($result['redlist2010']=="Extinct in the Wild") {
	$points = $points + 5;
	$evalcriteria = $evalcriteria.'<li>IUCN Extinct in the Wild = 5';
}
if ($result['redlist2010']=="Critically Endangered") {
	$points = $points + 5;
	$evalcriteria = $evalcriteria.'<li>IUCN Critically Endangered = 5';
}
if ($result['redlist2010']=="Endangered") {
	$points = $points + 5;
	$evalcriteria = $evalcriteria.'<li>IUCN Endangered = 5';
}
if ($result['redlist2010']=="Vulnerable") {
	$points = $points + 3;
	$evalcriteria = $evalcriteria.'<li>IUCN Vulnerable = 2';
}
if ($result['redlist2010']=="Lower Risk: Near Threatened") {
	$points = $points + 2;
	$evalcriteria = $evalcriteria.'<li>IUCN Near Threatened = 1';
}
#if ($result['redlist2010']=="Least Concern") {
#	$points = $points + 1;
#	$evalcriteria = $evalcriteria.'<li>IUCN Least Concern = 1';
#}
#if ($result['redlist2010']=="Lower Risk: Conservation Dependent") {
#	$points = $points + 2;
#	$evalcriteria = $evalcriteria.'<li>IUCN Conservation Dependent = 2';
#}
### Score for Red List Status 1997
#if ($result['redlist1997']=="Endangered") {
#	$points = $points + 4;
#	$evalcriteria = $evalcriteria.'<li>IUCN Endangered (1997) = 4';
#}
#if ($result['redlist1997']=="Extinct") {
#	$points = $points + 4;
#	$evalcriteria = $evalcriteria.'<li>IUCN Extinct (1997) = 4';
#}
#if ($result['redlist1997']=="Rare") {
#	$points = $points + 2;
#	$evalcriteria = $evalcriteria.'<li>IUCN Rare (1997) = 2';
#}
#if ($row['redlist1997']=="Threatened") {
#	$points = $points + 3;
#	$evalcriteria = $evalcriteria.'<li>IUCN Threatened (1997) = 3';
#}
### Score for CITES status
if ($result['cites']=="CITES I") {
	$points = $points + 3;
	$evalcriteria = $evalcriteria.'<li>CITES App I = 3';
}
if ($result['cites']=="CITES II") {
	$points = $points + 3;
	$evalcriteria = $evalcriteria.'<li>CITES App II = 1';
}

### *** Score one point for each keyword
###
$x = $result['keywords'];
$y = str_word_count($x);
	if ($y>0) {
		$points = $points + $y;
		$evalcriteria = $evalcriteria.'<li>Keywords = '.$y;	
	}

#	*** Score 3 points if exhibit not empty
#$x = mysql_result($sql_result,0,exhibit); 
#if ($x<>'') {
#	$points = $points + 3;
#	$evalcriteria = $evalcriteria.'<li>Exhibit Use ('.$x.') = 3';
#}
#	*** Score 4 points if only representative of family
$sql = 'select classify.family,classify.subfamily,classify.tribe from gh_inv,classify where classify.genus=gh_inv.genus and codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$fam=$tempresult['family'];
$sfam=$tempresult['subfamily'];
$tribe=$tempresult['tribe'];

$sql = 'select gh_inv.codeno from gh_inv,classify where classify.genus = gh_inv.genus and classify.family="'.$fam.'" and projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($sth->rowCount()==1) {
	$points = $points + 10;
	$tx_temp = $tx_temp + 10;
	$evalcriteria = $evalcriteria.'<li>Sole member of Family = 10';
}
### Add 5 pts each if only 2 representatives of family in collection
if ($sth->rowCount()==2) {
	$points = $points + 5;
	$tx_temp = $tx_temp + 5;
	$evalcriteria = $evalcriteria.'<li>One of two members of Family = 5ea';
}


#	*** Score 4 points if only representative of subfamily but not if sole member of family
#if ($num > 1) {
#	$sql = 'select gh_inv.codeno from gh_inv,classify where classify.genus = gh_inv.genus and classify.subfamily="'.$sfam.'" and projnum="GEN_COLL"';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	}
#	$num2=mysql_numrows($sql_result);
#	if ($num2==1) {
#		$points = $points + 4;
#		$tx_temp = $tx_temp + 4;
#		$evalcriteria = $evalcriteria.'<li>Sole member of Subfamily = 4';
#	}
#	}
#	*** Score 2 points if only representative of tribe but not if sole member of subfamily
#if ($num > 1) {
#	$sql = 'select gh_inv.codeno from gh_inv,classify where classify.genus = gh_inv.genus and classify.tribe="'.$tribe.'" and projnum="GEN_COLL"';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	}
#	$num2=mysql_numrows($sql_result);
#	if ($num2==1) {
#		$points = $points + 2;
#		$tx_temp = $tx_temp + 2;
#		$evalcriteria = $evalcriteria.'<li>Sole member of Tribe = 2';
#	}
#	}

#	*** Score 3 point if only member of genus in collection
$sql = 'select gh_inv.codeno from gh_inv where genus="'.$genus.'" and projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($sth->rowCount()==1) {
	$points = $points + 3;
	$tx_temp = $tx_temp + 3;
	$evalcriteria = $evalcriteria.'<li>Sole member of Genus = 3';
}

### Score one point if only member of section
$sql = 'select gh_inv.codeno from gh_inv where section="'.$section.'" and projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($sth->rowCount()==1) {
	$points = $points + 1;
	$tx_temp = $tx_temp + 1;
	$evalcriteria = $evalcriteria.'<li>Sole member of Section = 1';
}

### Score one point if only member of series
$sql = 'select gh_inv.codeno from gh_inv where series="'.$series.'" and projnum="GEN_COLL"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($sth->rowCount()==1) {
	$points = $points + 1;
	$tx_temp = $tx_temp + 1;
	$evalcriteria = $evalcriteria.'<li>Sole member of Series = 1';
}

#	*** Score 2 Points for each class usage
$sql = 'select date from history where codeno='.$codeno.' and class="CLASS"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$num=$sth->rowCount();
$num = $num*2;
$cl_temp = $num;

$points = $points + $num;
if ($num > 0 ) $evalcriteria = $evalcriteria.'<li>Class Use: (2pts ea) = '.$num;

#	*** Score 1 point for each trade
#	*** Limit to 5 points??
$sql = 'select date from history where codeno='.$codeno.' and class="TRADE"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$num=$sth->rowCount();
### LIMIT if used
if ($num>5) $num = 5;
$points = $points + $num;
if ($num > 0 ) $evalcriteria = $evalcriteria.'<li>Other Institutions: (1pt ea, max 5) = '.$num;

#	*** Score 1 point for each research use
#	*** Limit to 5 points??
$sql = 'select date from history where codeno='.$codeno.' and class="RESEARCH"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$num=$sth->rowCount();
### LIMIT if used
if ($num>5) $num = 5;
$points = $points + $num;
if ($num > 0 ) $evalcriteria = $evalcriteria.'<li>Research Use: (1pt ea, max 5) = '.$num;

#	*** Score 1 point for each outreach entry
#	*** Limit to 5 points??
$sql = 'select date from history where codeno='.$codeno.' and class="OUTREACH"';
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
$num=$sth->rowCount();
### LIMIT if used
if ($num>5) $num = 5;
$points = $points + $num;
if ($num > 0 ) $evalcriteria = $evalcriteria.'<li>Outreach Use: (1pt ea, max 5) = '.$num;

$sql = 'select ev_disc_pt,ev_disc_re from gh_inv where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$tempresult = $sth->fetch(PDO::FETCH_ASSOC);
if ($tempresult['ev_disc_pt'] <> 0 ) $evalcriteria = $evalcriteria.'<li>Discretionary Points: = '.$tempresult['ev_disc_pt'].' = '.$tempresult['ev_disc_re'];

### Update database record with new criteria
$sql = 'update gh_inv set eval_rank='.($points+$tempresult["ev_disc_pt"]).',e_criteria="'.$evalcriteria.'",e_tx_rank='.$tx_temp.',e_cl_rank='.$cl_temp.' where codeno='.$codeno; 
$sth = $db->prepare($sql);
$sth->execute();
# Calculate new ranking
$sql = 'select codeno,eval_rank,space_req,eval_hyp_incl from gh_inv where projnum="GEN_COLL" order by eval_rank desc';
$i=0;
$coll_rank=9999;
$space_acc = 0;
$space_req = 0;
foreach($db->query($sql) as $row) {
	if ($row['eval_hyp_incl']) {
		$space_acc = $space_acc + $row['space_req'];
		$i++;
		if ($row["codeno"]==$codeno) {
			$coll_rank = $i;
			$space_req = $space_acc;
		}
	}
}

$sql = 'update gh_inv set coll_rank='.$coll_rank.',space_acc='.$space_req.' where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$db = null;
return true;
}
?>
