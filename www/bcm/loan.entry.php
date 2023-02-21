<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name="viewport" content="width=device-width" >

<title>EEB Greenhouse Plant Borrowing</title>


<?php

echo '</head><body>';

### 
echo '<h3>Greenhouse Plant Borrowing Record</h3>';

echo '<form action="http://florawww.eeb.uconn.edu/bcm/loan.lookup.php" method="post">';

### Select Loan Date
echo '<b>Loan Date: </b>';
echo '<select name=usedate size=1>';
echo '<option selected>'.date("D, d M Y",time()-259200).'</option>';
echo '<option selected>'.date("D, d M Y",time()-172800).'</option>';
echo '<option selected>'.date("D, d M Y",time()-86400).'</option>';
echo '<option selected>'.date("D, d M Y").'</option>';
echo '<option>'.date("D, d M Y",time()+86400).'</option></select>';
echo '<br><i>  {NOTE: <a href="mailto:clinton.morse@uconn.edu?subject=Course Plant Use - Date Change Request">email manager</a> if you need to further backdate an entry}</i>';

echo '<p><b>Borrower Name: </b><br>';
echo '<select name=borrower size=1>';
include 'loan.borrowers.INCL.php';
echo '</select>';

echo '<p><b>Course#: </b><br>';
echo '<select name=course size=1>';
include 'loan.courses.INCL.php';
echo '</select>';

echo '<p><b>Enter Lab Number <i>{optional}</i>:</b><br>';
echo '<input name="labnum" value="0"><br>';

echo '<p><b>Enter Lab Name or Usage of Plants:</b><br>';
echo '<input name="labname"><br>';

echo '<p><input type="submit" name="type" value="Check Lab History">';
echo '<input type="reset" value="Reset" /></form><hr>';

?>

</font>
</body>
</html> 
