<?php
	######################################################
	### SPECIAL BIOCONTROL RELEASE BOX
	######################################################
	$showbiocontrol=0;  #TOGGLE THIS SECTION ON/OFF - EDIT DISPLAY USING COMMENTS
	if ($showbiocontrol){
		echo '<hr><b>Biocontrol Releases</b><p><ul>';

		echo '<li>Spider Mite';
		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=P. persimilis released&class=BIOCONTROL">P. persimilis released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=M. longipes released&class=BIOCONTROL">M. longipes released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=N. califonicus released&class=BIOCONTROL">N. califonicus released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Amblyseius andersoni released&class=BIOCONTROL">Amblyseius andersoni released</a>';

		echo '<li>Thrips';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=N. cucumeris released&class=BIOCONTROL">N. cucumeris released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Orius released&class=BIOCONTROL">Orius released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Dalotia released&class=BIOCONTROL">Dalotia released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Stratiolaelaps released&class=BIOCONTROL">Stratiolaelaps released</a>';
		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Chrysoperla released&class=BIOCONTROL">Chrysoperla released</a>';

#		echo '<li>Scale';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Aphytis melinus released&class=BIOCONTROL">Aphytis melinus released</a>';

		echo '<li>Whitefly';
		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Eretmocerus released&class=BIOCONTROL">Eretmocerus released</a>';
		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Encarsia released&class=BIOCONTROL">Encarsia released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Amblyseius swirskii released&class=BIOCONTROL">Amblyseius swirskii released</a>';

#		echo '<li>Aphid';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=ACE Mix released&class=BIOCONTROL">ACE Mix released</a>';

		echo '<li>Mealybug';
		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Crypt adults released&class=BIOCONTROL">Crypt adults released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Crypt larva released&class=BIOCONTROL">Crypt larva released</a>';
#		echo '<li><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Anagyrus released&class=BIOCONTROL">Anagyrus released</a>';

		echo '</ul><hr>';
	} #showbiocontrol

####################################
# Poll pest status for past 360 days
####################################
$sql = 'set sql_mode=""'; ## disable sql_mode=only_full_group_by
$sth = $db->prepare($sql);
$sth->execute();
echo '<b>360 Day Pest History</b><ul>';

$sql = 'select notes,class,max(unix_timestamp(date)) as lastdate from history where codeno='.$codeno.' and (class="SCOUT" or class="BIOCONTROL")';
$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
$sql .= ' group by notes order by class desc,notes';
foreach($db->query($sql) as $pest_row) {
	$lastdate = $pest_row['lastdate'];
	$daysago=intval((time()-$lastdate)/86400);
	if ($daysago<14) {
		echo '<li><b>'.$pest_row['notes'].'</b><sup>'.$daysago.'</sup>';	
	} elseif ($daysago > 13 and $daysago < 29) {
		echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=';
		echo $pest_row['notes'].'&class=';
		echo $pest_row['class'].'&url='.$url.'"><font color="RED"><b>';
		echo $pest_row['notes'].'</b></font></a><sup>'.$daysago.'</sup>';
	} else {
		echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=';
		echo $pest_row['notes'].'&class=';
		echo $pest_row['class'].'&url='.$url.'">';
		echo $pest_row['notes'].'</a><sup>'.$daysago.'</sup>';
	}

#	if (($i3 % 2) <> 0) {
#		echo '<p>';
#		} else {
#		echo ' | ';
#		}
} #foreach
echo '</ul><p>';

####################################
# End New Poll Section
####################################

### Quick History Add for Syringing
echo '<b>Quick Spot Spray</b><ul>';

### Greenshield Quick History for Zone Locations only
if (($result['location'] % 100) == 0) {
	echo '<li>Floors: <a href="quickhist.php?codeno='.$codeno.'&data=Green-Shield II&class=SPRAY&url='.$url.'">Greenshield</a>'; 
	echo '<li>Floors: <a href="quickhist.php?codeno='.$codeno.'&data=Zerotol 2.0&class=SPRAY&url='.$url.'">Zerotol</a>'; 
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Advion Ant Gel&class=SPRAY&url='.$url.'">Advion Ant Gel</a> (ants)';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Advance 375A&class=SPRAY&url='.$url.'">Advance Granular</a> (ants)';
} else {
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Water (syringe)&class=SPRAY&url='.$url.'">Syringe</a>';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Swab/Manual Removal&class=SPRAY&url='.$url.'">Swab/Manual</a>';
#	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Ultra-Fine Oil&class=SPRAY&url='.$url.'">Ultra-Fine Oil</a>';
#	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Abamectin 0.15EC&class=SPRAY&url='.$url.'">Abamectin</a>';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=M-Pede&class=SPRAY&url='.$url.'">M-Pede</a>';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Maxforce FC Ant Gel&class=SPRAY&url='.$url.'">Fipronil Gel</a> (ants)';
#	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Invict AB Insect Paste&class=SPRAY&url='.$url.'">Invict AB Gel</a> (ants)';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Advion Ant Gel&class=SPRAY&url='.$url.'">Advion Ant Gel</a> (ants)';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Advance 375A&class=SPRAY&url='.$url.'">Advance Granular</a> (ants)';
	echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Sluggo Plus&class=SPRAY&url='.$url.'">Sluggo Plus</a> (slugs/pillbugs/earwigs)';
	} # zone vs accession test

##################################
### Check Recent Spray activity
##################################
$sql = 'select notes,date from history where codeno='.$codeno.' and class="SPRAY"';
$sql .= ' and date > date_sub(curdate(),interval 14 day)';
$sql .= ' order by date DESC';
foreach($db->query($sql) as $spray_row) {
#	if (substr($spray_row['notes'],0,5)=="Apply") {
#		echo '<li><img src="http://florawww.eeb.uconn.edu/images/spray_trans.png"></img> Applied <b><font color="RED">'.$spray_row['notes'].'</font> on '.$spray_row['date'].'</b><br>';
#	} else {
		echo '<li><img src="http://florawww.eeb.uconn.edu/images/spray_trans.png"></img> Applied <b><font color="RED">'.$spray_row['notes'].'</font> on '.$spray_row['date'].'</b><br>';
#	}
} #foreach
echo '</ul><p>';

##################################
### Check Syringe status - color button if recently syringed
##################################
#echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/quickhist.php?codeno='.$codeno.'&data=Water (syringe)&class=SPRAY">Syringe</a>';
##### Check if already noted for the current week
#$sql = 'select recno from history where codeno='.$codeno.' and class="SPRAY" and notes="Water (syringe)"';
#$sql .= ' and history.date>date_sub(curdate(),interval 350 day)';
#$sql .= ' and week(history.date,3)=week(curdate(),3)';
#$sql_result3=mysql_query($sql);
#if (!$sql_result3) {
#	echo mysql_error();
#}
#$num3=mysql_numrows($sql_result3);
#if ($num3>0) echo '('.$num3.')';
#echo ' | ';

##################################
### Full Scouting Options
##################################
echo '<b>Extended Scouting List</b>';

echo '<form action="http://florawww.eeb.uconn.edu/bcm/insertpest.php" method="post">'.chr(10);
echo '<input type="hidden" name="codeno" value="'.$codeno.'">'.chr(10);
echo '<input type="hidden" name="class" value="SCOUT">';
echo '<input type="hidden" name="location" value="'.$result['location'].'">';
#echo '<b>Pest: </b>'.chr(10);
echo '<select name=notes size=1>';
echo '<option value="Ants" selected>Ants</option>';
echo '<option value="Aphid">Aphid</option>';
echo '<option value="Whitefly">Whitefly</option>';
echo '<option value="Mealybug">Citrus Mealybug</option>';
echo '<option value="Mealybug LT">Longtail Mealybug</option>';
echo '<option value="Root Mealybug">Root Mealybug</option>';
echo '<option value="Spider Mite">Spider Mite</option>';
echo '<option value="Broad Mite">Broad Mite</option>';
echo '<option value="Echinothrips">Echinothrips</option>';
echo '<option value="WF Thrips">Western Flower Thrips</option>';
echo '<option value="Greenhouse Thrips">Greenhouse Thrips</option>';
echo '<option value="California Red Scale">California Red Scale</option>';
echo '<option value="Brown Soft Scale">Brown Soft Scale</option>';
echo '<option value="Boisduval Scale">Boisduval Scale</option>';
echo '<option value="Fern Scale">Fern Scale</option>';
echo '<option value="Florida Wax Scale">Florida Wax Scale</option>';
echo '<option value="Hemispherical Scale">Hemispherical Scale</option>';
echo '<option value="unspecified Scale">unspecified Scale</option>';
echo '<option value="Fungus Gnat">Fungus Gnat</option>';
echo '<option value="Shore Fly">Shore Fly</option>';
echo '<option value="Slugs">Slugs</option>';
echo '<option value="Sowbug/Pillbug">Sowbug/Pillbug</option>';
echo '</select><br><input type="submit" name="submit" value="PEST SCOUT"></form>'.chr(10);

echo '<p>';

echo '<form action="http://florawww.eeb.uconn.edu/bcm/insertpest.php" method="post">'.chr(10);
echo '<input type="hidden" name="codeno" value="'.$codeno.'">'.chr(10);
echo '<input type="hidden" name="class" value="BIOCONTROL">';
echo '<input type="hidden" name="location" value="'.$result['location'].'">';
#echo '<b>Biocontrol: </b>'.chr(10);
echo '<select name=notes size=1>';
echo '<option value="Amblyseius andersoni" selected>Amblyseius andersoni</option>';
echo '<option value="Amblyseius swirskii">Amblyseius swirskii</option>';
echo '<option value="Anagyrus">Anagyrus</option>';
echo '<option value="Aphidius">Aphidius</option>';
echo '<option value="Aphidoletes">Aphidoletes</option>';
echo '<option value="Aphytis">Aphytis melinus</option>';
echo '<option value="Chrysoperla">Chrysoperla</option>';
echo '<option value="Crypt adults">Crypt adults</option>';
echo '<option value="Crypt larva">Crypt larva</option>';
echo '<option value="Delphastus">Delphastus pusillus</option>';
echo '<option value="Encarsia">Encarsia</option>';
echo '<option value="Eretmocerus">Eretmocerus</option>';
echo '<option value="Feltiella">Feltiella</option>';
echo '<option value="Leptomastix">Leptomastix</option>';
echo '<option value="Lindorus">Lindorus</option>';
echo '<option value="Neoseiulus californicus">Neoseiulus californicus</option>';
echo '<option value="Neoseiulus cucumeris">Neoseiulus cucumeris</option>';
echo '<option value="Neoseiulus fallacis">Neoseiulus fallacis</option>';
echo '<option value="Orius">Orius insidiosus</option>';
echo '<option value="Phytoseiulus persimilis">Phytoseiulus persimilis</option>';
echo '<option value="Scymnus">Scymnus</option>';
echo '<option value="Stethorus">Stethorus</option>';
echo '<option value="Syrphids">Syrphids</option>';
echo '<option value="unidentified predatory mite">unidentified predatory mite</option>';
echo '<option value="unidentified parasitoid">unidentified parasitoid</option>';
echo '</select><br><input type="submit" name="submit" value="BIOCONTROL SCOUT"></form>'.chr(10);

echo '<p>';

echo '<form action="http://florawww.eeb.uconn.edu/bcm/insertpest.php" method="post">'.chr(10);
echo '<input type="hidden" name="codeno" value="'.$codeno.'">'.chr(10);
echo '<input type="hidden" name="class" value="BIOCONTROL">';
echo '<input type="hidden" name="location" value="'.$result['location'].'">';
#echo '<b>Biocontrol: </b>'.chr(10);
echo '<select name=notes size=1>';
echo '<option value="Amblyseius andersoni released" selected>Amblyseius andersoni</option>';
echo '<option value="Amblyseius swirskii released">Amblyseius swirskii</option>';
echo '<option value="Anagyrus released">Anagyrus</option>';
echo '<option value="Aphidius released">Aphidius</option>';
echo '<option value="ACE Mix released">ACE Mix</option>';
echo '<option value="Aphidoletes released">Aphidoletes</option>';
echo '<option value="Aphytis released">Aphytis melinus</option>';
echo '<option value="Chrysoperla released">Chrysoperla</option>';
echo '<option value="Crypt adults released">Crypt adults</option>';
echo '<option value="Crypt larva released">Crypt larva</option>';
echo '<option value="Delphastus released">Delphastus pusillus</option>';
echo '<option value="Encarsia released">Encarsia</option>';
echo '<option value="Eretmocerus released">Eretmocerus</option>';
echo '<option value="Feltiella released">Feltiella</option>';
echo '<option value="Leptomastix released">Leptomastix</option>';
echo '<option value="Lindorus released">Lindorus</option>';
echo '<option value="Neoseiulus californicus released">Neoseiulus californicus</option>';
echo '<option value="Neoseiulus cucumeris released">Neoseiulus cucumeris</option>';
echo '<option value="Neoseiulus fallacis released">Neoseiulus fallacis</option>';
echo '<option value="Orius released">Orius insidiosus</option>';
echo '<option value="Phytoseiulus persimilis released">Phytoseiulus persimilis</option>';
echo '<option value="Scymnus released">Scymnus</option>';
echo '<option value="Stethorus released">Stethorus</option>';
echo '</select><br><input type="submit" name="submit" value="BIOCONTROL RELEASE"></form>'.chr(10);

echo '<p>';


?>
