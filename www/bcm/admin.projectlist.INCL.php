<?php

### Main Loop Code for Calculating number of pending projects

$sql = 'set sql_mode = ""'; # disable for non-aggregated column
$sth = $db->prepare($sql);
$sth->execute();

$sql = 'select tasks.assignto,count(tasks.recno) as reccount,users.lname,users.fname';
$sql .= ' from tasks,taskstatus,users where tasks.status=taskstatus.status';
$sql .= ' and taskstatus.priority<5 and tasks.assignto=users.init group by assignto';
#echo '<p><b>Pending Tasks:</b> ';
echo '<ul>';
foreach($db->query($sql) as $row) {
	echo '<li><a href="tmgr.tasksbyuser.php?user='.$row['assignto'].'"><b>'.$row['reccount'].'</b></a> tasks pending for '.$row['fname'].' '.$row['lname'];
} #foreach
$sql = 'select codeno from tasks where status="DONE"';
$sth = $db->prepare($sql);
$sth->execute();
echo '<p><li><a href="tmgr.reviewtask.php">';
if ($sth->rowCount() > 0) echo '<font color="RED">';
echo '<b>'.$sth->rowCount().'</b>';
echo '</a> Completed tasks awaiting manager review.';
if ($sth->rowCount() > 0) echo '</font>';
echo '</ul>';

?>