<html>
<head>
<meta name="viewport" content="width=device-width" />
<link href="style.css" type="text/css">

<?php

# Generate Title ################################################
echo '<title>Tour Entry</title>';
echo '</head><body><h3>Enter Tour Description:</h3><p>';

echo '<form action="historyinsert.php" method=post>';
echo '<input type="hidden" name=codeno value="111111111">';
echo '<input type="hidden" name="zone" value="9999">';

echo '<select name=class size=1>';
echo '<option selected>TOUR</option>';
echo '<option>VISITOR</option>';
echo '</select>Select Type<br>';

echo '<input name="data">Group Name<br>';
echo '<input name="value">Number in Group<br>';
echo '<input type="submit" name="type" value="Make Notation"></form>';

?> 
</font>
</body>
</html> 
