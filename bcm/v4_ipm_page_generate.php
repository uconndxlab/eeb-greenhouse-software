<?php
include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}

### CREATE OUTPUT FILE

$file_spec = $webdir.'ipm.html';
$accfile = fopen($file_spec,'w');

### BEGIN OUTPUTTING HTML CODE

$strout = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><head>'.chr(10);
$output=fwrite($accfile,$strout);

### GENERATE TITLE HTML
echo 'creating V3 ipm file: '.chr(10);

$strout = '<title>UConn Biodiversity Conservatory and Research Greenhouses - IPM Page</title>'.chr(10);
$output=fwrite($accfile,$strout);

### CREATE META TAGS

### ADD KEYWORDS

### RETRIEVE BOILERPLATE HTML

$sql = 'select head from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$strout = $sth->fetchColumn();
$output=fwrite($accfile,$strout);

$sql = 'select body from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$strout = $sth->fetchColumn();
$output=fwrite($accfile,$strout);

### Banner Graphic
$strout = '<div id="uc-main" class="container">';
$strout .= '<p></p>';	
$strout .= '<div id="row1">';					
$strout .= '<div id="images">';
$strout .= '<div id="banner" class="container">';

$strout .= '<div id="slider" class="span-18">'; 
$strout .= '<!-- images should be 710x300px-->';
$strout .= '<img src="http://florawww.eeb.uconn.edu/images/banners/visiting1_710x300.jpg" alt="Now Open Saturdays 10am - 2pm!!" />';	
$strout .= '<img src="http://florawww.eeb.uconn.edu/images/banners/3100_02_710x300px.jpg" alt="Now Open Saturdays 10am - 2pm!!" />';
$strout .= '<img src="http://florawww.eeb.uconn.edu/images/banners/3305_01-710x300.jpg" alt="Now Open Saturdays 10am - 2pm!!" />';
$strout .= '</div>';
$strout .= '</div>';				
$strout .= '</div></div><p>';		
$output=fwrite($accfile,$strout);
### End Banner Output

$strout = '<div class="row" id="row1">';
$result = fwrite($accfile,$strout);

$strout = '<div id="home3" class="span6" role="complementary">'.chr(10);
$strout .= '<div id="text-3" class="widget widget_text">'.chr(10);
$strout .= '<h2 class="widget-title">Pest Management Updates (Past 30 days):</h2>';		
$strout .= '<div class="textwidget">'.chr(10);
$result = fwrite($accfile,$strout);

### GENERATE FIRST COLUMN SEGMENT

$strout = '<h3>Note:</h3>';
$strout .= '<ul><li>This page is generated automatically every 60 minutes and serves as the official pesticide application record.';
$strout .= '<li>It may not reflect pesticide applications currently in progress or made within the past half hour.';
$strout .= '<li>"Pesticide Applications" may include biocontrol products and non-WPS regulated chemical products for which formal posting requirements are not required by law, but we ';
$strout .= 'include them here in order to provide a complete record of our pest control procedures';
$strout .= '<li>REI durations are listed for general information only.  Where applicable, REIs will always remain in effect until greenhouse staff have taken down any signage at the conclusion';
$strout .= ' of the REI.  Contact greenhouse staff if you feel signage may be mistakenly up past a valid REI expiration.  <font color="RED"><b>DO NOT ENTER</b></font> any signed areas unless you are properly';
$strout .= ' trained and are wearing the appropriate PPE.  Worker Protection Standard (worker) training is NOT sufficient for Early Entry activity.';
$strout .= '<li>Additional information about pest control measures is available by contacting <a href="http://florawww.eeb.uconn.edu/staff.html">greenhouse staff</a>.</ul><p>';

$output=fwrite($accfile,$strout);

### Parse Previous 30 days History

### This loops for today to 30 days ago, note today listed above
for ($i=0; $i<31; $i++) {
	### Parse for  Sprays $i days ago - summary only - product & number apps
	### Output Date Header
	$strout = date("D",time()-($i*86400)).' - ';
	$strout .= date("M jS",time()-($i*86400));
	echo 'Generating Data for '.$strout.chr(10);
	$strout = '<h3>'.$strout.'</h3>'.chr(10);
	$output=fwrite($accfile,$strout);
	$strout='<ol>';
	$sql = 'SET sql_mode=""'; ### Disables MySQL ONLY_FULL_GROUP_BY mode
	$sth = $db->prepare($sql);
	$sth->execute();
	$sql = 'select date_format(history.date,"%Y%m%d") as datestr,count(history.codeno) as appcount,';
	$sql .= 'history.notes,chemical.tradename,chemical.chemname,chemical.epa_reg,chemical.wps,';
	$sql .= 'chemical.rei,chemical.label,chemical.msds';
	$sql .= ' from history,chemical where class="SPRAY"';
	$sql .= ' and history.notes=chemical.tradename';
#	$sql .= ' and chemical.active';
	$sql .= ' and date=date_sub(curdate(),interval '.$i.' day)';
	$sql .= ' group by history.notes order by history.notes DESC';
	foreach($db->query($sql) as $row) {
		echo $row['appcount'].' '.$row['notes'].chr(10);
		$strout .= '<li><b>'.$row['notes'].'</b>';
		$strout .= ' {<a href="http://florawww.eeb.uconn.edu/recentspraying.html#';
		$strout .= $row['datestr'];	
		$strout .= '">x'.$row['appcount'].' applications</a>}'.chr(10);
		$strout .= '<ul><li><b>Active Ingredient:</b> '.$row['chemname'].chr(10);
		$strout .= '<li><b>EPA Reg#:</b> '.$row['epa_reg'].chr(10);
		if ($row['wps']==0) $strout .= '<li>No WPS Restrictions or REI'.chr(10);
		if ($row['wps']==1) $strout .= '<li><b>REI:</b> '.$row['rei'].' hours'.chr(10);
		$strout .= '<li><a href="http://florawww.eeb.uconn.edu/msds/'.$row['label'].'">Label</a> / '.chr(10);
		$strout .= '<a href="http://florawww.eeb.uconn.edu/msds/'.$row['msds'].'">MSDS</a>';
		### Collect Zones affected
		$sql = 'select substr(zone,1,2) as affzone from history where class="SPRAY" and date=date_sub(curdate(),interval '.$i.' day)';
		$sql .= ' and notes="'.$row['notes'].'" group by affzone order by affzone DESC';

		$strout .= '<li><b>Zone(s) Affected: </b>';
		foreach($db->query($sql) as $row2) {
			$strout .= $row2['affzone'].'00 ';
		} # foreach row2
		$strout .= '</ul>'.chr(10);
#		$output=fwrite($accfile,$strout);
	} # foreach

### Parse for Biocontrol Notations $i days ago - summary only - notation & number occurences
#	$sql = 'select history.date,count(history.codeno) as appcount,history.notes from history where class="BIOCONTROL"';
#	$sql .= ' and date=date_sub(curdate(),interval '.$i.' day)';
#	$sql .= ' group by notes order by notes DESC';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	}
#	$num=mysql_numrows($sql_result);
#	$strout = '';
#	while ($num > 0) {
#		$strout = '<li><b>Biocontrol Scouting/Release Notations:</b> '.mysql_result($sql_result,$num-1,"notes");
#		$strout .= ' {x'.mysql_result($sql_result,$num-1,"appcount").' notations}'.chr(10);
#		$output=fwrite($accfile,$strout);
#	$num--;
#	}
### Parse for Pest Notations $i days ago - summary only - notation & number occurences
#	$sql = 'select codeno as appcount from history where class="SCOUT"';
#	$sql .= ' and date=date_sub(curdate(),interval '.$i.' day)';
#	$sql_result=mysql_query($sql);
#	if (!$sql_result) {
#		echo mysql_error();
#	}
#	$num=mysql_numrows($sql_result);
#	$strout = '<li><b>Pest Scouting Notations:</b> '.$num;
#	if ($num > 0) $output=fwrite($accfile,$strout);
#	while ($num > 0) {
#		$strout = '<li><b>Pest Notation:</b> '.mysql_result($sql_result,$num-1,"notes");
#		$strout .= ' {x'.mysql_result($sql_result,$num-1,"appcount").' notations}'.chr(10);
#		$output=fwrite($accfile,$strout);
#	$num--;
#	}


	$strout .= '</ol>'.chr(10);
	$output=fwrite($accfile,$strout);
} ### $i loop for dates

### GENERATE TIME STAMP
$strout='<p style="text-align: left;"><i>data regenerated on '.date("r").'</i>'.chr(10);
$output=fwrite($accfile,$strout);


$strout = '</div></div></div>';
$output=fwrite($accfile,$strout);

### END OF PRIMARY DATA SECTION

### BEGIN SECOND COLUMN

$strout = '<div class="span3" id="home2" role="complementary">';													
$strout .= '<div id="text-4"  class="widget widget_text">';
$strout .= '<h2 class="widget-title">General Links:</h2>';
$strout .= '<div class="textwidget">';
$strout .= '<h4>Favorite Pages:</h4>';
$strout .= '<ul>';
$strout .= '<li><a href="inflower.html">Currently Blooming</a></li>';
$strout .= '<li><a href="statistics.html">New Arrivals</a></li>';
$strout .= '<li><a href="collections.html">Special Collections</a></li>';
$strout .= '<li><a href="http://florawww.eeb.uconn.edu/msds/">MSDS & Label Archive</a></li>';
$strout .= '<li><a href="http://florawww.eeb.uconn.edu/chemical_list.html">Current Chemical Inventory</a></li';			
$strout .= '</ul>';	
$output=fwrite($accfile,$strout);		

$strout = '<h4><a href="http://biodiversity.uconn.edu/">Biodiversity Research Collection</a></h4>';
$strout .= '<ul>';
$strout .= '<li><a href="http://biodiversity.uconn.edu/herbarium/">Herbarium (CONN)</a></li>';
$strout .= '<li><a href="http://biodiversity.uconn.edu/fossil-plants/">Fossil Plant Collections</a></li>';
$strout .= '</ul>';	
$strout .= '<h4>';
$strout .= '<a href="https://www.facebook.com/pages/UCONN-Ecology-Evolutionary-Biology-Plant-Growth-Facilities/121769795587">';
$strout .= '<img src="/images/icons/icon_facebook.png" width=33px /></a>';
$strout .= '<a href="https://www.instagram.com/eeb.greenhouse/">';
$strout .= '<img src="/images/icons/icon_instagram.png" width=33px /></a>';
$strout .= '<a href="http://uconneebgreenhouse.tumblr.com/">';
$strout .= '<img src="/images/icons/icon_tumblr.png" width=33px /></a>';
$strout .= '</h4>';
$output=fwrite($accfile,$strout);
	
$strout = '<table class="uc-table">';
$strout .= '<h4>Contact Us:</h4>';
$strout .= 'Meghan Moriarty<br>';
$strout .= '860-486-8941<br>';
$strout .= '<a href="mailto:meghan.moriarty@uconn.edu">meghan.moriarty@uconn.edu</a><br>';
$strout .= '75 NorthEagleville Road, Unit 3043<br>';
$strout .= 'Storrs, CT <br>';
$strout .= '06269-3043, U.S.A.<br>';
$strout .= '</table>';
$strout .= '</div>';	
$strout .= '</div>';						
$strout .= '</div>';
					
$strout .= '</div>';				
$strout .= '</div>';			
$strout .= '</div></div>';
$output=fwrite($accfile,$strout);

### RETRIEVE BOILERPLATE HTML

$sql = 'select foot from ghmaster where facility="Ecology & Evolutionary Biology Greenhouse"';
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

$strout = $result['foot'].chr(10);
$output=fwrite($accfile,$strout);

# CLOSE THE OUTPUT FILE

fclose($accfile);

### CLOSE OUTPUT FILE

$db = null;
return true;
?>

