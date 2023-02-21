<?php
### Gather periodic collection statistics to stat_history

$user="ghstaff";
$password="argus";
$database="bcm";
date_default_timezone_set('America/New_York'); 

$rs = mysql_connect('localhost', $user, $password);
if (!$rs) {
    die('Could not connect: ' . mysql_error());
}
@mysql_select_db($database) or die( "Unable to select database");

### Collect GEN_COLL accessions
$sql = 'select codeno from gh_inv where projnum="GEN_COLL"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_acc=mysql_numrows($sql_result);

### Collect GEN_COLL taxa
$sql = 'select latin_name from gh_inv where projnum="GEN_COLL" group by latin_name';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_taxa=mysql_numrows($sql_result);

### Collect GEN_COLL genera
$sql = 'select genus from gh_inv where projnum="GEN_COLL" group by genus';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_genera=mysql_numrows($sql_result);

### Collect GEN_COLL families
$sql = 'select classify.family from gh_inv,classify where gh_inv.projnum="GEN_COLL" and gh_inv.genus=classify.genus group by classify.family';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_family=mysql_numrows($sql_result);

### Collect GEN_COLL sub family
$sql = 'select classify.subfamily from gh_inv,classify where gh_inv.projnum="GEN_COLL" and gh_inv.genus=classify.genus group by classify.subfamily';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_subfam=mysql_numrows($sql_result);

### Collect GEN_COLL tribe
$sql = 'select classify.tribe from gh_inv,classify where gh_inv.projnum="GEN_COLL" and gh_inv.genus=classify.genus group by classify.tribe';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_tribe=mysql_numrows($sql_result);

### Collect GEN_COLL subtribe
$sql = 'select classify.subtribe from gh_inv,classify where gh_inv.projnum="GEN_COLL" and gh_inv.genus=classify.genus group by classify.subtribe';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$gc_subtribe=mysql_numrows($sql_result);

### Collect WISHLIST taxa
$sql = 'select latin_name from gh_inv where projnum="WISHLIST" group by latin_name';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$wl_taxa=mysql_numrows($sql_result);

### Collect PROPAGATE taxa
$sql = 'select latin_name from gh_inv where projnum="PROPAGATE" group by latin_name';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$prop_taxa=mysql_numrows($sql_result);

### Collect AGGREGATE acc
$sql = 'select codeno from gh_inv where ';
$sql = $sql.'(gh_inv.projnum="GEN_COLL" or ';
$sql = $sql.'gh_inv.projnum="JONES" or ';
$sql = $sql.'gh_inv.projnum="OPEL" or ';
$sql = $sql.'gh_inv.projnum="PROPAGATE" or ';
$sql = $sql.'gh_inv.projnum="SECURE")';

$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$agg_acc=mysql_numrows($sql_result);

### Collect AGGREGATE taxa
$sql = 'select latin_name from gh_inv where ';
$sql = $sql.'(gh_inv.projnum="GEN_COLL" or ';
$sql = $sql.'gh_inv.projnum="JONES" or ';
$sql = $sql.'gh_inv.projnum="OPEL" or ';
$sql = $sql.'gh_inv.projnum="PROPAGATE" or ';
$sql = $sql.'gh_inv.projnum="SECURE") ';
$sql = $sql.'group by latin_name';

$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$agg_taxa=mysql_numrows($sql_result);

### Collect AGGREGATE genera
$sql = 'select genus from gh_inv where ';
$sql = $sql.'(gh_inv.projnum="GEN_COLL" or ';
$sql = $sql.'gh_inv.projnum="JONES" or ';
$sql = $sql.'gh_inv.projnum="OPEL" or ';
$sql = $sql.'gh_inv.projnum="PROPAGATE" or ';
$sql = $sql.'gh_inv.projnum="SECURE") ';
$sql = $sql.'group by genus';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$agg_genera=mysql_numrows($sql_result);

### Collect AGGREGATE families
$sql = 'select classify.family from gh_inv,classify where ';
$sql = $sql.'(gh_inv.projnum="GEN_COLL" or ';
$sql = $sql.'gh_inv.projnum="JONES" or ';
$sql = $sql.'gh_inv.projnum="OPEL" or ';
$sql = $sql.'gh_inv.projnum="PROPAGATE" or ';
$sql = $sql.'gh_inv.projnum="SECURE") ';
$sql = $sql.'and gh_inv.genus=classify.genus group by classify.family';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$agg_family=mysql_numrows($sql_result);

### Collect DELETE 16 accessions
$sql = 'select codeno from gh_inv where projnum="DELETE'.date("y").'"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$del15_acc=mysql_numrows($sql_result);

### Collect TODO 
$sql = 'select recno from tasks where ';
$sql = $sql.'status="TODO"';

$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$task_todo=mysql_numrows($sql_result);

### Collect TODO - Priority
$sql = 'select recno from tasks where ';
$sql = $sql.'status="TODO - Priority"';

$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$task_todo_priority=mysql_numrows($sql_result);

### Calculate average 'age' of last confirm
$sql='select round(avg(to_days(confirm))) as d1, to_days(now()) as d2, count(codeno) as d3 from gh_inv where projnum="GEN_COLL"';
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}
$avg_age=(mysql_result($sql_result,0,"d2")-mysql_result($sql_result,0,"d1"));


### Confirmations this week
$weeknum = (date('W',(time())))-1;
$sql = 'select confirm from gh_inv where';
$sql = $sql.' confirm > date_add(curdate(), interval -8 day)';
$sql = $sql.' and week(confirm,3) = '.$weeknum; 
$sql_result2=mysql_query($sql);
if (!$sql_result2) {
	echo mysql_error();
}
$confirms=mysql_numrows($sql_result2);

echo 'Collection Statistics:'.chr(10);
echo 'GEN_COLL ACC: '.$gc_acc.chr(10);
echo 'GEN_COLL TAXA: '.$gc_taxa.chr(10);
echo 'GEN_COLL GENERA: '.$gc_genera.chr(10);
echo 'GEN_COLL FAMILY: '.$gc_family.chr(10);
echo 'GEN_COLL SUB FAMILY: '.$gc_subfam.chr(10);
echo 'GEN_COLL TRIBE: '.$gc_tribe.chr(10);
echo 'GEN_COLL SUB TRIBE: '.$gc_subtribe.chr(10).chr(10);
echo 'WISHLIST TAXA: '.$wl_taxa.chr(10);
echo 'PROPAGATE TAXA: '.$prop_taxa.chr(10);
echo 'AGGREGATE ACC: '.$agg_acc.chr(10);
echo 'AGGREGATE TAXA: '.$agg_taxa.chr(10);
echo 'AGGREGATE GENERA: '.$agg_genera.chr(10);
echo 'AGGREGATE FAMILY: '.$agg_family.chr(10);
echo 'DELETE15 TAXA: '.$del15_acc.chr(10).chr(10);
echo 'TODO: '.$task_todo.chr(10);
echo 'TODO - Priority: '.$task_todo_priority.chr(10).chr(10);
echo 'AGING: '.$avg_age.chr(10);
echo 'CONFIRMS: '.$confirms.chr(10);


$sql='insert into stat_history (date,gc_acc,gc_taxa,gc_genera,gc_family,gc_subfam,';
$sql=$sql.'gc_tribe,gc_subtri,wl_taxa,prop_taxa,agg_acc,agg_taxa,agg_genera,agg_family,delete15_acc,task_todo,task_todo_priority,aging,confirms) values (curdate(),';
$sql=$sql.$gc_acc.','.$gc_taxa.','.$gc_genera.','.$gc_family.','.$gc_subfam.','.$gc_tribe.','.$gc_subtribe.',';
$sql=$sql.$wl_taxa.','.$prop_taxa.','.$agg_acc.','.$agg_taxa.','.$agg_genera.','.$agg_family.','.$del15_acc.','.$task_todo.','.$task_todo_priority.','.$avg_age.','.$confirms.')';
#echo $sql;
$sql_result=mysql_query($sql);
if (!$sql_result) {
	echo mysql_error();
}


mysql_close($rs);
?>
