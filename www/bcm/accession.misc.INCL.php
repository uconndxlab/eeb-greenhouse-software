<?php
	echo '<b>MISC FIELDS TAB: </b><p>';
	echo '<ul>';

	### Create individual linkable keywords
	$keywords = explode(" ",trim($result['keywords']));
	echo '<li><b>Keywords:</b> ';
	foreach ($keywords as $key) {
		echo '<a href="keyword.search.php?keyword='.$key.'">'.$key.'</a> ';
	} # foreach keys	
	echo ' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=keywords&url='.$url.'#keywords"> {Edit}</a>';


	echo '<li><b>Compounds:</b> '.$result['compounds'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=compounds&url='.$url.'#keywords"> {Edit}</a>';
	echo '<li><b>Credits:</b> <i>(use html ordered list tags)</i>'.$result['credits'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=credits&url='.$url.'#keywords"> {Edit}</a>';
	echo '</ul>';

?>
