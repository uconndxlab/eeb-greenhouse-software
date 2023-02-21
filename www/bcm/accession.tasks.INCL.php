<?php
	echo '<b>PROJECT & TASKS TAB: </b><p>';
	echo '<b><a href="http://florawww.eeb.uconn.edu/bcm/tmgr.acctask.php?codeno='.$codeno.'">Add New Task</a></b><p>';

	### Search Tasks for pending tasks
	$sql = 'select tasks.recno,tasks.codeno,tasks.descrip,tasks.status,tasks.dcreate,tasks.dfuture,tasks.assignto,tasks.location,tasks.recurweek,tasks.recur_every,tasks.assigned,tasks.priority,gh_inv.latin_name,taskstatus.priority';
	$sql .= ' from tasks,gh_inv,taskstatus where tasks.codeno=gh_inv.codeno and tasks.status=taskstatus.status and gh_inv.codeno='.$codeno;
	$sql .= ' and taskstatus.priority>0 and taskstatus.priority<10';
	$sql=$sql.' order by taskstatus.priority,recurweek,assignto,location,dcreate';

	foreach($db->query($sql) as $row) {
		### Build Completion Button
		if ($row['status']=='TODO' or $row['status']=='TODO - Priority' or $row['status']=='REDO') {
			echo ' <form action="http://florawww.eeb.uconn.edu/bcm/tmgr.taskcomplete.php" style="display: inline-block;" method="get">';
			echo '<input type="hidden" name="codeno" value="'.$row['codeno'].'">';
			echo '<input type="hidden" name="status" value="DONE">';
			echo '<input type="hidden" name="recno" value="'.$row['recno'].'">';
			echo '<input type="hidden" name="assign" value="'.$row['assignto'].'">';
			echo '<input type="submit" name="btn" value="Complete"></form>';
		}
		echo ' <a href="http://florawww.eeb.uconn.edu/bcm/tmgr.dropdown.php?recno='.$row['recno'];
		echo '&v=assignto&assign='.$row['assignto'].'&location=ALL&status=';
		echo $row['assignto'].'">'.$row['assignto'].'</a> : ';
		echo '<a href="http://florawww.eeb.uconn.edu/bcm/tmgr.dropdown.php?recno='.$row['recno'];
		echo '&v=status&assign='.$row['assignto'].'&location=ALL&status=';
		echo $row['status'].'">'.$row['status'].'</a> : ';

########### placeholder until above is fixed...
#		echo ' <b>'.$row['assignto'].'</b> : <font color="RED">'.$row['status'].'</font>';


#		echo '<br>';
		if ($row['status']=='DONE') echo '<img src="http://florawww.eeb.uconn.edu/images/checkmark.gif"></img>';	
		echo ' '.$row['descrip'];
#		if (mysql_result($sql_result,$i,'status')=='RECURRING') {
#			if (mysql_result($sql_result,$i,'recur_every')>0) {
#				echo ' every <a href="/bcm/tmgr.edittask2.php?v=recur_every';
#				echo '&recno='.mysql_result($sql_result,$i,'recno');
#				echo '&assign='.mysql_result($sql_result,$i,'assignto');
#				echo '&location='.mysql_result($sql_result,$i,'location');
#				echo '&status='.mysql_result($sql_result,$i,'status');				
#				echo '">';
#				echo mysql_result($sql_result,$i,'recur_every').'</a> weeks';
#			} #recur_every
#			if (mysql_result($sql_result,$i,'recurweek')>0) {
#				echo ' during week# '.mysql_result($sql_result,$i,'recurweek');
#				echo ' during week# <a href="/bcm/tmgr.edittask2.php?v=recurweek';
#				echo '&recno='.mysql_result($sql_result,$i,'recno');
#				echo '&assign='.mysql_result($sql_result,$i,'assignto');
#				echo '&location='.mysql_result($sql_result,$i,'location');
#				echo '&status='.mysql_result($sql_result,$i,'status');				
#				echo '">';
#				echo mysql_result($sql_result,$i,'recurweek').'</a>';
#			} # recurweek	
#			} # RECURRING
#		if (mysql_result($sql_result,$i,'status')=='FUTURE') echo ' <font color="red"> scheduled for <b>'.mysql_result($sql_result,$i,'dfuture').'</b>';
		echo ' <sup>{R#'.$row['recno'].'}</sup>';	

	
		echo '<br>';
	} # foreach
#	echo '<hr>';


?>