<?php
	echo '<b>COLLECTION ASSESSMENT TAB: </b><p>';

	### Collection Assessment Information
	echo '<ul>';
	echo '<li><b>Importance:</b> ';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/editdropdown.php?codeno='.$codeno.'&v=importance&url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=assess">';
	echo $result['importance'].'</a>';
	echo '<li><b>Include:</b> ';
	echo '<a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno.'&v=eval_hyp_incl&url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=assess">';
	echo $result['eval_hyp_incl'].'</a> [<i>1=include, 0=exclude</i>]';
	echo '<li><b>Collection Rank:</b> '.$result['coll_rank'];
	echo '<li><b>Space Threshold:</b> '.$result['space_acc'].'sf';
	echo '<ul><li><b>Space Required:</b> <a href="http://florawww.eeb.uconn.edu/bcm/edittext.php?codeno='.$codeno;
	echo '&v=space_req&url=http://florawww.eeb.uconn.edu/bcm/accession.php?codeno='.$codeno.'&tab=assess">';
	echo $result['space_req'].'</a>sf</ul>';
	echo '<li><b>Assessment Scoring:</b> '.$result['eval_rank'];
	echo '<ul><li><b>Criteria:</b> <ul>'.$result['e_criteria'];
	### If Discretionary Points allocated, include a ? help image
	if (strpos($result['e_criteria'],'Discretionary')>0) {
		echo ' <img src="/images/icons/question-20.png" title="NOTE: Discretionary Points (gh_inv.ev_disc_pt) and Reason (gh_inv.ev_disc_re) modified from command line only " />'.chr(10);
	} # Discretionary Note
	echo '</ul></ul>';
	echo '<li><b><a href="http://florawww.eeb.uconn.edu/bcm/assess.zone.php?zone='.$result['location'].'">Assessments by Bench</a></b>';
	echo '</ul>';
	
	### Accession validation code segment
	echo '<b>COLLECTION VALIDATION:</b> (may be blank if up to date)';
	include '/var/www/bcm/accession.validate.INCL.php';
?>
