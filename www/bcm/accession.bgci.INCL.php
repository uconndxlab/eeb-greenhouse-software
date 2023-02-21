<?php
	echo '<b>BGCI / IUCN / CITES TAB: </b><p>';
	echo '<ul>';
#	echo '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><br>';
	### Restrictions Checkbox Area
	$fieldlist = "wildcoll,poisonous,redist,ppaf,fed_weed,invasive,banned_CT,engineered,bgci_hide";
	$scoll_arr = explode(",",$fieldlist);
	echo '<li><b>Special Restrictions:</b><form method=POST action="http://florawww.eeb.uconn.edu/bcm/cbox_update.php">';
	echo '<input type="hidden" value='.$fieldlist.' name=fieldlist>';
	echo '<input type="hidden" value='.$url.' name=url>';  ### currently not redirecting properly
	echo '<input type="hidden" value='.$codeno.' name=codeno>';
	$x=0;
	while ($x<count($scoll_arr)) {
		echo '<input type=checkbox name="update[]" value="'.$scoll_arr[$x].'"';
		if ($result[$scoll_arr[$x]]==1) echo ' checked';
		echo '>'.ucfirst($scoll_arr[$x]).'</input><br>';
		$x++;
	 } #while
	echo '<input type="SUBMIT" value="Broken redirect - use BACK button after entry"></form>';
	echo '<p>';

	### include text box for toxin notes
	echo '<br><b>Toxin Notes: </b>'.$result['toxinnotes'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=toxinnotes&url='.$url.'"> {Edit}</a>';
	echo '<p>';
	### CITES & IUCN Dropdowns
	echo '<li><b>1997 IUCN Red List Status:</b> '.$result['redlist1997'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=redlist1997&url='.$url.'"> {Edit}</a>';
	echo '<li><b>2015 IUCN Red List Status:</b> '.$result['redlist2010'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=redlist2010&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Cites Status:</b> '.$result['cites'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cites&url='.$url.'"> {Edit}</a>';	
	echo '<p><li><b>BGCI Status:</b> '.$result['bgci_status'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=bgci_status&url='.$url.'"> {Edit}</a>';	
	echo '<br><b>Ex-Situ BGCI Sites:</b> '.$result['bgci_exsitusites'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=bgci_exsitusites&url='.$url.'"> {Edit}</a>';	
	echo '</ul>';

#	# Generate Restriction Information #################################### SUPERSEDED???
#
#	echo '<p><b>Restrictions:</b>';
#	echo '<ul>';
#	if ($result['cites']) echo '<li>'.$result['cites'].' - not to leave greenhouse';
#	if ($result['redlist2010']) echo '<li>IUCN RedList (2015) -'.$result['redlist2010'];
#	if ($result[''redlist1997'])echo '<li>IUCN RedList (1997) - '.$result['redlist1997'];
#	if ($result['fed_weed']) echo '<li>Federally Listed Noxious Weed - not to leave greenhouse';
#	if ($result['redist']) echo '<li>Distribution Agreement in place (CBD or similar)';
#	if ($result['invasive']) echo '<li>Invasive Species - not to leave greenhouse';
#	if ($result['banned_CT']) echo '<li>Banned in CT - restricted to Research Greenhouse';
#	if ($result['poisonous'){
#		echo '<li><font color="red">Potentially Poisonous:</font> ';
#		### include text box for toxin notes
#		echo '<br><b>Toxin Notes: </b>'.$result['toxinnotes'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=toxinnotes&url='.$url.'"> {Edit}</a>';
#	}
#	if ($result['engineered']) echo '<li>GMO - restricted to BPB Facility';
#	if ($result['ppaf']) echo '<li>Plant Patent Applied For - no propagation/redistribution';
#	echo '</ul><p>';



?>
