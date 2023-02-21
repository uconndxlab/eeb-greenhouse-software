<html>
<head>

<meta name="viewport" content="width=device-width" />

<?php
include 'credentials.php';
include 'evaluate.php';

### Alpha List for Hypothetical Assessment
###  w/ Toggle for eval_hyp_incl

#$user="ghstaff";
#$password="argus";
#$database="bcm";
$mode = 'max';
#$mode = $_GET['mode'];

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");
echo '<title>Hypothetical Collection Makeup</title></head><body>';
### Evaluate all accession for zone

echo '<a href="query.php">NEW QUERY</a>';
#echo ' | Mode Options: Min/Max';

echo '<table border width=100%>';

$sql = 'select codeno,latin_name,eval_hyp_incl,location,location2,location3,confirm,quant,quant2,quant3,swingspace,importance,coll_rank,space_acc,e_criteria,p3_zone,p3_tdwg,classify.family,tblLevel2.l2region from gh_inv,classify,tblLevel2 where ';
$sql=$sql.' gh_inv.genus=classify.genus and projnum="GEN_COLL" and';
$sql=$sql.' gh_inv.p3_tdwg=tblLevel2.l2code order by family,gh_inv.genus,coll_rank';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$num=mysql_numrows($sql_result);

$i=0;

while ($i<$num) {
	$codeno = mysql_result($sql_result,$i,"codeno");
	$include=mysql_result($sql_result,$i,"eval_hyp_incl");
	echo '<tr><td';
	if($include) {
		echo ' bgcolor=#ECFFEC';
		} else {
		echo ' bgcolor=#FFE6CC';
	}

	echo '>';
	echo '<a name="'.$codeno.'">';
	echo ' <a href="accession.php?codeno='.$codeno.'" target="_blank">';
	echo trim(mysql_result($sql_result,$i,latin_name)).'</a> ';
	### Codeno = green for include, red for omitted based on eval_hyp_incl

	if ($include) {
	echo '<font color="green">'.$codeno.'</font>';
	} else {
	echo '<font color="RED">'.$codeno.'</font>';
	}
	### Include Family & Link
	echo ' <a href="http://florawww.eeb.uconn.edu/bcm/family_list.php?family='.mysql_result($sql_result,$i,"family").'" target="_blank">'.mysql_result($sql_result,$i,"family").'</a>';

	### Include evaluation criteria
	echo '<br><b>Rank:</b> '.mysql_result($sql_result,$i,"coll_rank");
	echo ' <b>Importance:</b> ';
	$url='http://florawww.eeb.uconn.edu/bcm/assessalpha.php#'.$codeno;
	#echo $url;
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=importance&url='.$url.'">';
	echo mysql_result($sql_result,$i,"importance").'</a>';
	echo ' <b>Include: </b>';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=eval_hyp_incl&url='.$url.'">';
	echo mysql_result($sql_result,$i,"eval_hyp_incl").'</a>';
	

	### This section only if mode=max
	if ($mode=='max') {
	echo '<br>'.chr(10);
	### Include Collection Location Data
	echo '<b>Zone:</b> <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=p3_zone&url='.$url.'">';
	echo mysql_result($sql_result,$i,"p3_zone").'</a>';
	echo ' <b>Region:</b> '.mysql_result($sql_result,$i,"p3_tdwg").':'.mysql_result($sql_result,$i,"l2region").'<br>';

	echo '<b>Threshold:</b> '.(floor(mysql_result($sql_result,$i,"space_acc")));
	echo ' <b>Footprint:</b> <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno;
	echo '&v=swingspace&url='.$url.'">'.mysql_result($sql_result,$i,"swingspace").'</a><br>';
	echo mysql_result($sql_result,$i,"e_criteria");

	} # mode = max
##############################################
### Add blank space on right for scrolling use
##############################################
	echo '<p></td><td valign="top"';
	if($include) {
		echo ' bgcolor=#ECFFEC';
		} else {
		echo ' bgcolor=#FFE6CC';
	}

echo '>';
### List Bench locations
echo mysql_result($sql_result,$i,"location").'(x'.mysql_result($sql_result,$i,"quant").')<br>';
if ($mode=='max') {
	echo mysql_result($sql_result,$i,"location2").'(x'.mysql_result($sql_result,$i,"quant2").')<br>';
	echo mysql_result($sql_result,$i,"location3").'(x'.mysql_result($sql_result,$i,"quant3").')<br>';
} # mode=max	
echo '</tr>'.chr(10);
$i++;
}

echo '</table>';
mysql_close($rs);


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

</font>
</body>
</html> 


