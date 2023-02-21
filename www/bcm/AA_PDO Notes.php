##### For Easy Copy/Paste of PDO functions

include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}

##### Return data into array $result

$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

##### Loop over returned data

foreach($db->query($sql) as $row) {

} #foreach

##### Check if any data returned

$sth = $db->prepare($sql);
$sth->execute();
if ($sth->fetchColumn())

### Create Search Box
echo '<a name="top"></a>'.chr(10);
# partial string in latin_name
echo '<form action="http://florawww.eeb.uconn.edu/bcm/gh_inv_query.php" method="post">'.chr(10);
echo '<input type="text" name="instring" autofocus autocomplete="off">'.chr(10);
echo '<input type="submit" value="Search">'.chr(10);
echo ' <img src="/images/icons/question-20.png" title="OPTIONS: Partial binomial text search (incl synonym); Full family name; TWDG (3-digit upper case);'.chr(10);
echo ' 9-digit accession number; 4-digit bench number, #keyword"></img>'.chr(10);
echo '</form><p>'.chr(10);

##### Standardize web page output
$result=fwrite  -->> $status = fwrite 

echo '<p><a href="http://florawww.eeb.uconn.edu/bcm/map_status.php"><img src="http://florawww.eeb.uconn.edu/images/maps/ipm_map.png" width="200px"></img></a>';
echo '<br><a href="admin.php">Admin Page</a>';

##### UNDER CONSTRUCTION BANNER
echo '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><br>';

### Glossary Definition HTML code
<dfn><abbr title="HyperText Markup Language">HTML</abbr></dfn>
