<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name="viewport" content="width=device-width" >

<title>EEB Greenhouse Main Query Page</title>

<?php

echo '</head><body>';
### Create Search Box
echo '<a name="top"></a>';
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">';
echo '<input type="text" name="instring" autofocus autocomplete="off">';
echo '<input type="submit" value="Search">';
echo ' <img src = "/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3 digit upper case);';
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>';
echo '</form>';
echo '<p>';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';
?>

</font>
</body>
</html> 
