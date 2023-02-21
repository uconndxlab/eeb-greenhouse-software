<html>
<head>
<meta name="viewport" content="width=device-width" />
<link href="style.css" type="text/css">

<?php

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

# Generate Title ################################################
echo '<title>Batch History Checklist</title>';
echo '</head><body>';

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

# Generate Pest Scout Dropdown
echo '<form action="history.batch.checklist.php" method=post>';
echo '<input name="start" style="width:240px;">Start Location<br>';
echo '<input name="end" style="width:240px;">End Location<br>';
echo '<select name="class" style="width:240px;">';
echo '<option>FLOWERING</option>';
echo '<option selected>NOTE</option>';
echo '<option>SCOUT</option>';
echo '<option value="ZNOTE">ZONE NOTATION</option>';
echo '<option>SPRAY</option>';
echo '<option>FERT</option>';
echo '<option>BIOCONTROL</option>';
echo '<option>CLASS</option>';
echo '<option>RESEARCH</option>';
echo '<option>OUTREACH</option>';
echo '<option>TRADE</option>';
echo '<option>WEEKLY</option>';
echo '<option>TASK</option>';
echo '</select>Class<br>';
echo '<input name="note" style="width:240px;">Notation<br>';
echo '<input type="submit" name="type" value="Generate Checklist"></form>';

echo '<a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<p>';
echo '<a href="admin.php">Admin Page</a>';
?> 
</font>
</body>
</html> 
