<?php
	echo '<b>TAXONOMY TAB: </b><p>';
	echo '<ul>';
	echo '<li><b>Author: </b>';
	echo $result['author'].'<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=author&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Synonyms:</b> '.$result['synonomy'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=synonomy&url='.$url.'"> {Edit}</a>';
	echo '<br>Reminder: Please use HTML italics tags around scientific name.<p>';
#	echo '</ul>';
	echo '<p>';
	### Generate Classification Data #############################################
	$sql = 'select famcomm.division,famcomm.class as cls,famcomm.subclass,famcomm.order as ord,famcomm.order,famcomm.suborder,classify.family,classify.subfamily,classify.tribe,classify.subtribe,gh_inv.latin_name,gh_inv.genhybsymbol,gh_inv.genus,gh_inv.subgenus,gh_inv.section,gh_inv.subsect,gh_inv.series,gh_inv.sphybsymbol,gh_inv.species,gh_inv.infrarank,gh_inv.infraepithet,gh_inv.cultivar from gh_inv,classify,famcomm where classify.genus=gh_inv.genus and famcomm.family=classify.family and codeno='.$codeno;
	$sth = $db->prepare($sql);
	$sth->execute();
	$classify_result = $sth->fetch(PDO::FETCH_ASSOC);

	$genus = $classify_result['genus'];
	echo '<a name="classification">';
	echo '<li><b>Classification:</b><ul>';
	echo '<li><b>Division:</b> '.$classify_result['division'];
	echo '<li><b>Class:</b> '.$classify_result['cls'];
	echo '<li><b>Sub Class:</b> '.$classify_result['subclass'];
	echo '<li><b>Order:</b> '.$classify_result['ord'];
	echo '<li><b>Sub Order:</b> '.$classify_result['suborder'];
	echo '<li><b>Family:</b> '.$classify_result['family'].' <a href="http://florawww.eeb.uconn.edu/bcm/classify.edit.php?genus='.$genus.'&v=family&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Sub Family:</b> '.$classify_result['subfamily'].' <a href="http://florawww.eeb.uconn.edu/bcm/classify.edit.php?genus='.$genus.'&v=subfamily&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Tribe:</b> '.$classify_result['tribe'].' <a href="http://florawww.eeb.uconn.edu/bcm/classify.edit.php?genus='.$genus.'&v=tribe&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Sub Tribe:</b> '.$classify_result['subtribe'].' <a href="http://florawww.eeb.uconn.edu/bcm/classify.edit.php?genus='.$genus.'&v=subtribe&url='.$url.'"> {Edit}</a>';
	echo '<p>';
	echo '<li><b>Display Name:</b> '.$classify_result['latin_name'].'<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=latin_name&url='.$url.'"> {Edit}</a>';
	echo '<p>';
	echo '<li><b>Genus Hybrid Symbol:</b> '.$classify_result['genhybsymbol'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=genhybsymbol&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Generic Epithet:</b> '.$classify_result['genus'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=genus&url='.$url.'"> {Edit}</a> <i>*Use Caution!</i>';
	echo '<li><b>Subgenus:</b> '.$classify_result['subgenus'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=subgenus&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Section:</b> '.$classify_result['section'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=section&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Sub Section:</b> '.$classify_result['subsect'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=subsect&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Series:</b> '.$classify_result['series'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=series&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Species Hybrid Symbol:</b> '.$classify_result['sphybsymbol'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=sphybsymbol&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Specific Epithet:</b> '.$classify_result['species'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=species&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Infraspecific Rank:</b> '.$classify_result['infrarank'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=infrarank&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Infraspecific Epithet:</b> '.$classify_result['infraepithet'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=infraepithet&url='.$url.'"> {Edit}</a>';
	echo '<li><b>Cultivar:</b> '.$classify_result['cultivar'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=cultivar&url='.$url.'"> {Edit}</a>';
	echo '</ul></ul>';

?>
