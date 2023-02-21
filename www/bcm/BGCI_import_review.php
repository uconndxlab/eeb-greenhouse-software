<html>
<head>
<meta name="viewport" content="width=device-width" />
<link href="style.css" type="text/css">

<?php

#########################################
### Preparation Steps
### Delete old bgci_import data
###   delete from bgci_import
### Import BGCI csv data and review
###   Current data in csv form to be copied to /var/lib/mysql-files/BGCI.csv
###   Copy requires superuser rights
###   set sql_mode='' {to avoid error 1261 - Row 1 doesn't contain data for all fields}
###   load data infile '/var/lib/mysql-files/BGCI.csv' into table bgci_import fields terminated by ',' lines terminated by '\n' ignore 1 rows
#########################################

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

# Generate Title ################################################
echo '<title>BGCI Review Routine</title>';
echo '</head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>'.chr(10);
echo '</form><p>'.chr(10);

### generate latin_name
$sql = 'update bgci_import set latin_name=concat(genus," " ,species," ",infrarank," ",infraepithet," ",cultivar)'; 
$sth = $db->prepare($sql);
$sth->execute();

$sql = 'select * from bgci_import';
foreach($db->query($sql) as $row) {
	### now check data in bgci_import table against current gh_inv data
	$latin_name = $row['latin_name'];
	$namestatus = $row['namestatus'];
	$bgcisites = $row['bgcisites'];
	$iucn2015 = $row['iucn2015'];
	$iucn1997 = $row['iucn1997'];
	$cites = $row['cites'];

	echo '<div id="'.$latin_name.'"></div>'.$latin_name.' - ';
	if ($namestatus=="") echo '<font color="RED">undefined</font>';
	echo $namestatus;	
	echo ' ['.$bgcisites.'] {';
	echo $iucn2015.' | '.$iucn1997.' | '.$cites.'}';
	echo '<br>';
	$sql = 'select codeno,latin_name,bgci_status,bgci_exsitusites,redlist1997,redlist2010,cites from gh_inv where latin_name="'.$latin_name.'" and projnum="GEN_COLL"';
	foreach($db->query($sql) as $row2) {

		$codeno = $row2['codeno'];
		echo $codeno.' - ';
		echo '<a href="/bcm/accession.php?codeno='.$row2['codeno'].'" target="_blank">';
		echo $row2['latin_name'];
		echo '</a> - ';
		### simply highlight bgci name status if different from previous - allows manual updating.
		if ($row2['bgci_status']<>$namestatus) {
			echo '[<font color="RED">**<a href="/bcm/editdropdown.php?codeno='.$codeno.'&v=bgci_status';
			echo '&url=http://florawww.eeb.uconn.edu/bcm/BGCI_import_review.php%23'.$latin_name.'">';    
			echo $row2['bgci_status'].'</a></font>]';
		} else {
			echo $namestatus;
		} # if bgcistatus
		echo ' ';
		### update bgci sites
        if ($row2['bgci_exsitusites']<>$bgcisites) {
			$sql = 'update gh_inv set bgci_exsitusites = '.$bgcisites.' where codeno='.$codeno;
			$sth = $db->prepare($sql);
			$sth->execute();
		} # if bgcisites


		### update redlist status
#		echo ' {';
#              if (mysql_result($sql_result2,$i2,"redlist2010")<>$redlist2015) {
#                        echo '<font color="RED"><a href="/bcm/editdropdown.php?codeno='.$codeno.'&v=redlist2010';
#			echo '&url=http://florawww.eeb.uconn.edu/bcm/BGCI_import_review.php%23'.$latin_name.'">';
#			echo mysql_result($sql_result2,$i2,"redlist2010").'</a></font>';
#                        } else {
#                        echo $redlist2015;
#              } # if bgcisites
#		echo ' | ';
#              if (mysql_result($sql_result2,$i2,"redlist1997")<>$redlist1997) {
#                        echo '[<font color="RED"><a href="/bcm/editdropdown.php?codeno='.$codeno.'&v=redlist1997';
#			echo '&url=http://florawww.eeb.uconn.edu/bcm/BGCI_import_review.php%23'.$latin_name.'">';
#			echo mysql_result($sql_result2,$i2,"redlist1997").'</a></font>]';
#                        } else {
#                        echo $redlist1997;
#              } # if bgcisites
#		echo ' | ';
#              if (mysql_result($sql_result2,$i2,"cites")<>$cites) {
#                        echo '[<font color="RED"><a href="/bcm/editdropdown.php?codeno='.$codeno.'&v=cites';
#			echo '&url=http://florawww.eeb.uconn.edu/bcm/BGCI_import_review.php%23'.$latin_name.'">';
#			echo mysql_result($sql_result2,$i2,"cites").'</a></font>]';
#                        } else {
#                        echo $cites;
#              } # if bgcisites
#		echo '}';
		echo '<br>';

	} # foreach row2
	echo '<hr>'.chr(10).chr(10);
} #foreach


$db = null;
echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
?> 
</font>
</body>
</html> 
