<?php

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'recentspraying.html';
$accfile = fopen($file_spec,'w');

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
$result = fwrite($accfile,$strout);

$strout = '<TITLE>EEB Greenhouse 30 Day Pesticide Application Listing</TITLE>';
$result = fwrite($accfile,$strout);

$strout = '<html><BODY><hr>EEB Greenhouse 30 Day Pesticide Application List';
$result = fwrite($accfile,$strout);
$strout = '<p><i>Listing generated on '.date("r").'</i><hr>';
$status=fwrite($accfile,$strout);

$strout = '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><hr>';  ### Temporary
$status=fwrite($accfile,$strout);

### Pesticide Product Summary
$strout = '<font size=-1><table border=1><tr><th colspan=2>Pesticide Application Summary - 31 Day</tr><tr><th align="left">Product<th align="center">Applications</tr>';
$status=fwrite($accfile,$strout);

$sql = 'select notes from history where class="SPRAY" and date>date_sub(curdate(),interval 31 day)';
$sql .= ' order by notes';
$sth = $db->prepare($sql);
$sth->execute();
$product = $sth->fetchColumn();
$counter=0; #initialize counters
foreach($db->query($sql) as $row) {
	if ($product <> $row['notes']){
		$strout = '<tr><td>'.$product.'<td align="center">'.$counter.'</tr>';
		$status=fwrite($accfile,$strout);
		$counter = 0; # reset counters
	}
	$counter++;
	$product = $row['notes']; 	
} # foreach

$strout = '<tr><td>'.$product.'<td align="center">'.$counter.'</tr>';
$status=fwrite($accfile,$strout);
$strout = '</table><p><hr>';
$status=fwrite($accfile,$strout);

############# Do Physical Scouting History Chart ###############

$startweek=date('W',(time()-2116800));
$zone = 'ALL';
$count=0;

# create list of pests & biocontrols

$sql = 'select distinct notes from history where (class="SCOUT" or class="BIOCONTROL") and date>date_sub(curdate(),interval 28 day)';
$sql .= ' group by notes';
#$sth = $db->prepare($sql);
#$sth->execute();
#$result = $sth->fetch(PDO::FETCH_ASSOC);
#print_r($result);

$numpests = 0; # initialize counter
foreach($db->query($sql) as $pests) {
	$numpests++;	
} # foreach
### Set up Scouting Table Chart

$strout = '<table border><tr><th colspan=6>Pest & Beneficial Scouting - 28 Day</tr>';
$status=fwrite($accfile,$strout);
$strout = '<tr><th align="left" colspan=1>Pest</th><th align="center" colspan=5>Week#</th></tr>'.chr(10);
$status=fwrite($accfile,$strout);



$pestindex=0;
$weekindex=0;
# create weeknumber table headers

$strout = '<tr><th>';
while ($weekindex<5) {
	if (($startweek+$weekindex)< 53) {
		$out=($startweek+$weekindex);
		}else{ 
		$out=($startweek+$weekindex)-52;
		}
	$strout .= '<th>'.$out.'</th>';
	$weekindex++;
} 
$strout .= '</tr>'.chr(10);
$status=fwrite($accfile,$strout);

$strout = '<tr><td colspan=6>This table under construction</td></tr>'.chr(10);
$status=fwrite($accfile,$strout);

# begin collecting table data

foreach($db->query($sql) as $pests) {
	# get data for current pest
	
	$sql = 'select week(date,3),value,notes,date,class from history where notes="';
	$sql .= $pests['notes'].'" and date>date_sub(curdate(),interval 56 day)';
	$sql .= ' order by date';
#	$sql_result_data=mysql_query($sql);
#	if (!$sql_result_data) {
#		echo mysql_error();
#	}
#	$datapoints=mysql_numrows($sql_result_data);
#	$dataindex=0;
#	$pest=mysql_result($sql_result_pests,$pestindex,0);
#	$strout = '<tr><td>'.$pest.'</td>';
#	$status=fwrite($accfile,$strout);
#	$weekindex=0;;
#	while ($weekindex<5) {
#		$strout = '<td>';
#		$status=fwrite($accfile,$strout);
#		if (($startweek+$weekindex)< 53) {
#			$week=($startweek+$weekindex);
#			}else{ 
#			$week=($startweek+$weekindex)-52;
#		}
		### Begin tallying weekly pest counts	
			
#		while ($dataindex<$datapoints) {
#			$weekval=mysql_result($sql_result_data,$dataindex,0);
#			if ($weekval==$week) {
#				$count=$count+1;		
#				}
#
#			$dataindex++;
#			}
#		if ($count>0) {		
#			$strout = $count;
#			$status=fwrite($accfile,$strout);
#			$count=0;
#		}else{
#			$strout = '&nbsp&nbsp&nbsp&nbsp&nbsp';
#			$status=fwrite($accfile,$strout);
#		}	
#			
#		$dataindex=0;	
#		$weekindex++;
#		$strout='</td>';
#		$status=fwrite($accfile,$strout);
#		}
#	$strout = '</tr>'.chr(10);
#	$status=fwrite($accfile,$strout);
} # foreach pests

$strout = '</table><p><hr>';
$status=fwrite($accfile,$strout);

### Create Accession Table

$strout = '<font size=-1><table border=1><tr><th colspan=5>Pesticide Application Detail - 31 Day</tr><tr><th align="left">Date<th align="left">Location<th align="left">Project<th align="left">Accession# & Name<th align="left">Application</tr>';
$status=fwrite($accfile,$strout);

$sql = 'select history.codeno,history.date,date_format(history.date,"%Y%m%d") as datestr,gh_inv.latin_name,gh_inv.projnum,history.zone,history.notes from history,gh_inv where history.codeno=gh_inv.codeno and history.class="SPRAY"';
$sql .= ' and history.date>DATE_SUB(CURDATE(),INTERVAL 31 DAY) order by history.date DESC,history.notes,history.zone,gh_inv.latin_name';
$prevdate= '19850101';
foreach($db->query($sql) as $row) {




	$strout='<tr>';
	if ($row['datestr']<>$prevdate) {
		$strout .= '<a id="'.$row['datestr'].'">';
		$prevdate = $row['datestr'];
	}
	$strout .= '<td>'.$row['date'];
	$strout .= '<td>'.$row['zone'].'<td>'.$row['projnum'].'<td>';
	$strout .= $row['codeno'].': '.$row['latin_name'].'<td>'.$row['notes'].'</tr>'.chr(10);
	$status=fwrite($accfile,$strout);
} #foreach


$strout = '</table></font><hr><i>page generated on '.date("r").'</i></BODY></html>';
$status=fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);
$db = null;

function MySQLtoTimestamp($mysqlDate) {
    if (strlen($mysqlDate) > 10) {
        list($year, $month, $day_time) = explode('-', $mysqlDate);
        list($day, $time) = explode(" ", $day_time);
        list($hour, $minute, $second) = explode(":", $time);
        $ts = mktime($hour, $minute, $second, $month, $day, $year);
    } else {
        list($year, $month, $day) = explode('-', $mysqlDate);
        $ts = mktime(0, 0, 0, $month, $day, $year);
    }
    return $ts;
}
?>
