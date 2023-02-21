<?php

echo '<b>LABELS & SIGNAGE TAB: </b><p>';

### Count pending labels for Laser Printer TLS
$sql = 'select * from labels_v2 where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
echo '<a href="http://florawww.eeb.uconn.edu/bcm/label.add_v2.php?codeno='.$codeno.'">LG Label TLS</a><b> '.$sth->rowCount().'</b>';
echo '<br>';
### Count pending labels for Laser Printer BPB
$sql = 'select * from labels where codeno='.$codeno;
$sth = $db->prepare($sql);
$sth->execute();
echo '<a href="http://florawww.eeb.uconn.edu/bcm/label.add.BPB.php?codeno='.$codeno.'">SM Label BPB</a><b> '.$sth->rowCount().'</b>';
echo '<p>';

#echo '<img src="http://florawww.eeb.uconn.edu/images/icons/Under_construction_icon-yellow.144.png"></img><br>';


echo '<b><a href="http://florawww.eeb.uconn.edu/bcm/sign5x7.php?codeno='.$codeno.'">Standard Signage</a></b>';
echo '<ul><b><li>Text:</b> '.$result['signtext'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=signtext&url='.$url.'"> {Edit}</a>';
##echo '<li><b>QR Code: DO NOT USE/TESTING</b> '.$result['signQR'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signQR&url='.$url.'"> {Edit}</a>';
##echo '<li><b>QR Tag: DO NOT USE/TESTING</b> '.$result['signQRtag'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signQRtag&url='.$url.'"> {Edit}</a>';
echo '</ul>';

echo '<b><a href="http://florawww.eeb.uconn.edu/bcm/sign5x7_custom.php?codeno='.$codeno.'">Alternate Signage</a></b>';
echo '<ul><b><li>Text:</b> '.$result['signtext_alt'].' <a href="http://florawww.eeb.uconn.edu/bcm/editmemo.php?codeno='.$codeno.'&v=signtext_alt&url='.$url.'"> {Edit}</a>';
echo '<li><b>LineA:</b> '.$result['signtext_altA'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signtext_altA&url='.$url.'"> {Edit}</a>';
echo '<li><b>LineB:</b> '.$result['signtext_altB'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signtext_altB&url='.$url.'"> {Edit}</a>';
echo '<li><b>LineC:</b> '.$result['signtext_altC'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signtext_altC&url='.$url.'"> {Edit}</a>';
echo '<li><b>LineD:</b> '.$result['signtext_altD'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=signtext_altD&url='.$url.'"> {Edit}</a>';
echo '<li><b>Icon:</b> '.$result['sign_icon'].' <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=sign_icon&url='.$url.'"> {Edit}</a>';
echo '</ul>';

echo '<b><a href="http://florawww.eeb.uconn.edu/bcm/sign5x4.php?codeno='.$codeno.'">Kress Mini Signage</a></b> - experimental';
?>
