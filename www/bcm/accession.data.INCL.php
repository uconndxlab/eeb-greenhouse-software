<?php
	echo '<b>DESCRIPTIVE DATA TAB: </b><p>';
#	echo '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><br>';

	echo '<ul><li><b>Common Name: </b>'.$result['commonname'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=commonname&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Family:</b> '.$family;
	echo '<p>';
	echo '<li><b>Origin:</b> ';
	echo '<ul><li><b>Country of Origin:</b> '.$result['cntry_orig'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=cntry_orig&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Primary L2 Code:</b> '.$result['p3_tdwg'].'<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=p3_tdwg&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Data:</b> '.$result['tdwg'].'<a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=tdwg&url='.$url.'"> {Edit}</a>';
	echo '</ul><p>';

	echo '<li><b>Habitat:</b> '.$result['habitat'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=habitat&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Feature:</b> '.$result['feature'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=feature&url='.$url.'"> {Edit}</a>';
	echo '<p>';

	echo '<li><b>Web Description:</b> '.$result['descrip'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=descrip&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Usage:</b> '.$result['usedfor'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=usedfor&url='.$url.'"> {Edit}</a>';
	echo '<p>';

	echo '<li><b>Source:</b> '.$result['source'];
	echo '<li><b>Accession Date:</b> '.date("m-d-Y",strtotime($result['acc_date']));
#	echo '<li><b>Wild Collected?:</b> '.$result['wildcoll'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=wildcoll&url='.$url.'"> {Edit - 0/1 only}</a>';
	echo '<li><b>Provenance: </b>'.$result['provenance'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=provenance&url='.$url.'"> {Edit}</a>';
		echo '<li><b>Provenance 2</b> <i>(suppressed data)</i><b>: </b>'.$result['provenance2'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=provenance2&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Voucher(s): </b>'.$result['voucher'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=voucher&url='.$url.'"> {Edit}</a>';
	
	echo '</ul>';
?>
