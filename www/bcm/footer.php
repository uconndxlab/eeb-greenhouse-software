<!FOOTER BUTTONS ETC>
<?php

#echo '<hr>';
echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
#echo '<hr><table width=100%>';
#echo '<tr><td bgcolor="#cccc99">';

include '/var/www/bcm/credentials.php';
try {
	$db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4',$user,$password);
} catch(PDOException $ex) {
	echo "An Error occurred connecting to BCM!".chr(10); # User friendly message
	echo $ex-getMessage().chr(10); # Explicit Error Message
}



#echo '<b>NOTE:</b> Footer Page Links will be added back in as they are tested and operational';
#echo '<p>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/bloomlist.php?days=14">FLOWERING</a>';|
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/hist_stat.php?days=15">HISTORY</a> | '; 
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/inventorymini.php">INVENTORY</a> | ';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/inventoryspeed.php">AGING</a>';

#echo '<br>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/scout_list_14-28.php">PEST REPORT</a> | '; 
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/scoutingchart.php">PEST CHART</a> | '; 
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/keyword_cloud.php">KEYWORDS</a>';
#echo '<br>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/addtour.php">ADD TOUR</a> | ';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/tmgr.student.selecttask.php">PROJECTS</a> | ';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/ghuser/loan_entry.php">BORROW</a> |';

#echo '<p>';

#echo '<p><b>Permanent Staff Only:</b><p>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/new_wish_form.php">ADD WISHLIST</a> | ';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/new_acc_form.php">ADD ACCESSION</a>';
#echo '<p>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/tmgr.stat.php?days=15">TASK MATRIX</a> | ';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/batch_history.php">BATCH HISTORY</a>';
#echo '<p>';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/batch_pesticide.php">PESTICIDE APP</a> |';
#echo '<a href="http://florawww.eeb.uconn.edu/bcm/chem_view.php">CHEM INVENTORY</a>';
#echo '<p>';
#echo '<a href="propagate.php?sort=location3">PROPAGATION</a> | ';
#echo '<a href="orchid_culture.php?location=3204">ORCHIDS</a> |';
#echo '<a href="currentstatus.php">STATUS</a>';
#echo '<p>';
#echo '<a href="scoutsurplusmini.php">SURPLUS</a> | ';
#echo '<p>';



### Large Format Labels - PDF / TLS Printer
#$sql = 'select * from labels_v2';
#$sql_result=mysql_query($sql);
#if (!$sql_result) echo mysql_error();
#$num=mysql_numrows($sql_result);
#echo '<p>Labels pending for <a href="http://florawww.eeb.uconn.edu/bcm/labelupdate.php">TLS Laser Printer:</a><b> '.$num.'</b>';

### Small Format Labels - BPB Printer
#$sql = 'select * from labels';
#$sql_result=mysql_query($sql);
#if (!$sql_result) echo mysql_error();
#$num=mysql_numrows($sql_result);
#echo '<p>Labels pending for <a href="http://florawww.eeb.uconn.edu/bcm/labelupdateBPB.php">BPB Label Printer:</a><b> '.$num.'</b>';

$db = null;
?> 




</td></tr></table><hr>



