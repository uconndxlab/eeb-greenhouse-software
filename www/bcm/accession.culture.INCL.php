<?php

####################################
# Poll FERT status for past 360 days
####################################
$sql = 'set sql_mode=""'; ## disable sql_mode=only_full_group_by
$sth = $db->prepare($sql);
$sth->execute();
echo '<b>360 Day Fertility History</b><ul>';

if (substr($result['location'],2,2)<>"00") { 
	echo '<li><i>{refer to <a href=accession.php?codeno=99999'.substr($result['location'],0,2).'00&tab=culture>'.substr($result['location'],0,2).'00</a> for full zone fertilizer history}</i>';
} 
#$sql = 'select notes,class,max(unix_timestamp(date)) as lastdate from history where codeno='.$codeno.' and class="FERT"';
$sql = 'select notes,class,week(date) as week,unix_timestamp(date) as lastdate from history where codeno='.$codeno.' and class="FERT"';
$sql .= ' and history.date>date_sub(curdate(),interval 364 day)';
#$sql .= ' group by notes order by lastdate desc,notes';
$sql .= ' order by date desc,notes';
foreach($db->query($sql) as $fert_row) {
	$lastdate = $fert_row['lastdate'];
	$daysago=intval((time()-$lastdate)/86400);
#	if ($daysago<8) {
#		echo '<li><b>'.$fert_row['notes'].'</b><sup>'.$daysago.'</sup>';	
#	} elseif ($daysago > 13 and $daysago < 29) {
#		echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=';
#		echo $fert_row['notes'].'&class=';
#		echo $fert_row['class'].'&url='.$url.'"><font color="RED"><b>';
#		echo $fert_row['notes'].'</b></font></a><sup>'.$daysago.'</sup>';
#	} else {
		echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=';
		echo $fert_row['notes'].'&class=';
		echo $fert_row['class'].'&url='.$url.'">';
		echo $fert_row['notes'].'</a><sup>Week#'.$fert_row['week'].': '.$daysago.' days ago</sup>';
#	}

#	if (($i3 % 2) <> 0) {
#		echo '<p>';
#		} else {
#		echo ' | ';
#		}
} #foreach
echo '</ul><p>';


### Quick History Add for Common Fertilizer Treatments
echo '<b>Spot Fertilizer Application</b><ul>';
echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Sustane 16-4-8 120 day&class=FERT&url='.$url.'">Sustane 16-4-8 120 day</a>';
echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=lime&class=FERT&url='.$url.'">Lime</a>';
echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Pro-Tekt 0-0-3 @ 50ppm Si&class=FERT&url='.$url.'">Pro-Tekt</a>';
echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Iron Sulphate (Dr. Iron)&class=FERT&url='.$url.'">Iron Sulphate (Dr. Iron)</a>';
echo '<li><a href="quickhist.php?codeno='.$codeno.'&data=Iron Chelate&class=FERT&url='.$url.'">Iron Chelate</a>';
echo '</ul><p>';

### Cultural Data
echo '<b>Cultural Data</b><ul>';
echo '<li><b>Culture: </b>'.$result['culture'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=culture&url='.$url.'"> {Edit}</a>';
echo '<li><b>USDA Zone: </b>'.$result['usda_zone'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=usda_zone&url='.$url.'"> {Edit}</a>';

echo '<li><b>Light: </b> '.$result['cult_light'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_light&url='.$url.'"> {Edit}</a>';
echo '<li><b>Water: </b> '.$result['cult_water'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_water&url='.$url.'"> {Edit}</a>';
echo '<li><b>Temp: </b> '.$result['cult_temp'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_temp&url='.$url.'"> {Edit}</a>';
echo '<li><b>Dormancy: </b> '.$result['dormancy'].' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=dormancy&url='.$url.'"> {Edit}</a>';

echo '</ul><p>';




##################
#### Old Code Here
##################
#echo '<li><b>USDA Zone:</b> '.mysql_result($sql_result,$i,'usda_zone').' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=usda_zone&url='.$url.'"> {Edit}</a>';
### Temporary Edit Field for Minimum Temp (Sept2013)
#echo ' <b>MinF:</b> '.mysql_result($sql_result,$i,'mintempF').' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=mintempF&url='.$url.'"> {Edit}</a>';

#echo '<li><b>Culture:</b> '.mysql_result($sql_result,$i,'culture').' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=culture&url='.$url.'"> {Edit}</a>';
#### Cultural Drop-downs
#echo '<ul><li><b>Light: </b> '.mysql_result($sql_result,$i,'cult_light').' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_light&url='.$url.'"> {Edit}</a>';
#echo '<li><b>Water: </b> '.mysql_result($sql_result,$i,'cult_water').' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_water&url='.$url.'"> {Edit}</a>';
#echo '<li><b>Temp: </b> '.mysql_result($sql_result,$i,'cult_temp').' <a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=cult_temp&url='.$url.'"> {Edit}</a></ul>';

####################################
#echo '</ul><p><table border bgcolor="#cccc99">';
### Fertilizer Insert Checkbox Area
#$fieldlist = "Gardentone,Iron Chelate,Lime,Iron Sulphate (Dr. Iron),Magnesium sulfate,Miracid,Nutricote,Pro-tekt,Sustane 16-4-8 120d";
#$array = explode(",",$fieldlist);

#echo '<tr><td colspan=2 align="center"><b>Fertilizer Applications</b><br><img src="checkmark.gif"></img><i> = applied in past 90 days</i></center></td></tr><tr><form method=POST action="http://florawww.eeb.uconn.edu/bcm/cbox_insert.php">';
#echo '<input type="hidden" value='.$fieldlist.' name=fieldlist>';
#echo '<input type="hidden" value='.$codeno.' name=codeno>';
#echo '<input type="hidden" value="FERT" name=class>';
#$x=0;
#while ($x<count($array)) {
#	echo '<td><input type=checkbox name="update[]" value="'.$array[$x].'"';
#	echo '>'.ucfirst($array[$x]);
#	##### Check if applied in past 90 days
###	if ($array[$x]=='Nutricote') {
#		$sql = 'select * from history where codeno='.mysql_result($sql_result,$i,'codeno').' and class="FERT"';
#		$sql .= ' and history.date>date_sub(curdate(),interval 90 day)';
#		$sql .= ' and history.notes="'.$array[$x].'"';
#		$sql_result3=mysql_query($sql);
#		if (!$sql_result3) {
#			echo mysql_error();
#			}
#		$num3=mysql_numrows($sql_result3);
#		if ($num3>0) echo '<img src="checkmark.gif"></img>';	
###		}
#	echo '</input></td>';
#	if (($x % 2) <> 0) echo '</tr><tr>';	
#$x++;
#}
#echo '</tr>';
#echo '<tr><td colspan=2 align="center"><input type="SUBMIT" value="Insert Fertilizer Notations"></form></td></tr>';


?>
