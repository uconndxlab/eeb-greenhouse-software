<html>
<head>
<meta name="viewport" content="width=device-width" />
<title>Label File Generation</title>
</head>
<body>

<?php
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search; Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number"></img>'.chr(10);
echo '</form><p>'.chr(10);

### Count records of current database file = labels is BPB file
$sql = 'select * from labels';
$sth = $db->prepare($sql);
$sth->execute();
echo '<p>This page for clearing BPB label queue only';
echo '<p>There are currently <b>'.$sth->rowCount().'</b> labels to be printed';

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/label.deleteall.BPB.php">Delete ALL label records??</a> - Are you sure??';

// Time out statement
#echo '<meta HTTP-EQUIV="REFRESH" content="10; url=admin.php">';
echo '<p></p><a href="admin.php">Admin Page</a>';
$db = null;
?> 
</font>
</body>
</html> 
