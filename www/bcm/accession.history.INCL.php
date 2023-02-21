<?php
	echo'<b>HISTORY TAB:<p><a href="http://florawww.eeb.uconn.edu/bcm/notation.php?codeno='.$result['codeno'].'">Add New Note</a></b><p>';
	$sql = 'select zone,date,week(date) as week,class,notes,recno,value from history where history.codeno='.$codeno;
	$sql .= ' order by date desc';
	foreach($db->query($sql) as $row) {	
		echo '<li>'.$row['date'].' {'.($row['week']+1).'} ';	
		echo $row['zone'].' - ';
		echo $row['class'].' - ';
		echo $row['notes'];
		if ($row['value']>0) echo ' ['.$row['value'].']';
		echo '<sup>'.$row['recno'].'</sup>';
	} # foreach
	echo '</ul>';
?>
