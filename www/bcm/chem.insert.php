<html>
<head>
<meta name="viewport" content="width=device-width" />
<?php
include '/var/www/bcm/credentials.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=bcm;charset=utf8mb4', $user, $password);
} catch(PDOException $ex) {
    echo "An Error occured connecting to BCM!".chr(10); ## user friendly message
    echo $ex->getMessage().chr(10); # Explicit Error Message	
}
echo 'got this far...';
$sql = 'set sql_mode=""'; # just in case, turn off strict field checking
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);

# Process Database Update Here ############################
#set values to posted data
$sql = 'insert into chemical (tradename,chemname,manufacturer,epa_reg,wps,rei,location,category,source,quant,size,label,msds,active,datercvd) ';
$sql = $sql.'values ("'.$_POST["tradename"].'","';
$sql = $sql.$_POST['chemname'].'","';
$sql = $sql.$_POST['manufacturer'].'","';
$sql = $sql.$_POST['epa_reg'].'","';
$sql = $sql.$_POST['wps'].'","';
$sql = $sql.$_POST['rei'].'","';
$sql = $sql.$_POST['location'].'","';
$sql = $sql.$_POST['category'].'","';
$sql = $sql.$_POST['source'].'","';
$sql = $sql.$_POST['quant'].'","';
$sql = $sql.$_POST['size'].'","';
$sql = $sql.$_POST['label'].'","';
$sql = $sql.$_POST['msds'].'",1,';
$sql = $sql.'curdate())';

echo $sql.'<hr>';
# process update SQL
$sth = $db->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
echo 'New record for '.$_POST["tradename"].' successfully created....';
}

### Add a chemical label to the label database
#$sql = 'insert into labels (latin_name,commonname,cntry_orig,habitat) values ("'.$_POST["tradename"].'","'.$_POST['chemname'].'","Rcvd from '.$_POST['source'].' on ","'.date("r").'","'.$_POST['category'].'")';
#echo $sql;
#$sql_result=mysql_query($sql);
#if (!$sql_result) echo mysql_error();
echo '<meta HTTP-EQUIV="REFRESH" content="1; url=http://florawww.eeb.uconn.edu/bcm/chem_view.php">';

?> 

</body>
</html> 
