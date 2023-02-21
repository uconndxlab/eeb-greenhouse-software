<?php
### This section will generate validation text for data in the bcm.gh_inv table stored in $result (all fields included)

echo '<ol>';
########### The following statements will check various fields and report null values or probable questionable values

### check MAIN Tab
### check currently
			if ($result['currently'] === NULL) echo '<li><mark><b>Currently</b> is set to NULL</mark> - Main Tab';

		### check DATA Tab
		### check for null value in commonname
			if ($result['commonname'] === NULL) echo '<li><mark><b>Common name</b> is NULL</mark> - DATA Tab';
		### check for null value in country of origin
			if ($result['cntry_orig'] === NULL) echo '<li><mark><b>Country of Origin</b> is NULL</mark> - DATA Tab';
		### check for null value in tdwg
			if ($result['tdwg'] === NULL) echo '<li><mark><b>Origin Data</b> is NULL</mark> - DATA Tab';
			if ($result['p3_tdwg'] === NULL) echo '<li><mark><b>Primary L2 Code</b> is NULL</mark> - DATA Tab';
		### check provenance & provenance 2 - flag if data present but no <pre> tags
			if (strlen($result['provenance'])>0 and (strpos($result['provenance'],'pre>')=== false)) echo '<li><mark><b>Provenance</b> is missing &ltpre&gt&lt/pre&gt tags</mark> - DATA Tab';
			if (strlen($result['provenance2'])>0 and (strpos($result['provenance2'],'pre>')=== false)) echo '<li><mark><b>Provenance 2</b> is missing &ltpre&gt&lt/pre&gt tags</mark> - DATA Tab';
			
		### check CLASSIFY Tab
		### check for blank author
			if ($result['author'] === NULL) echo '<li><mark><b>Author</b> is NULL</mark> - CLASSIFY Tab';
		### is there a synonomous name in the literature?
			if ($result['synonomy'] === NULL) echo '<li><mark>Are there any <b>Synonyms</b> in the literature?</mark> - CLASSIFY Tab';
		### check species, if more than one word (ie has missing infraspecific info)
			if (str_word_count($result['species'],0)>1 and $result['infrarank'] === null) echo '<li><mark>Check <b>Infraspecific Rank</b></mark> - CLASSIFY Tab';
			if (str_word_count($result['species'],0)>1 and $result['infraepithet'] === null) echo '<li><mark>Check <b>Infraspecific Epithet</b></mark> - CLASSIFY Tab';
			if (str_word_count($result['species'],0)>1 and $result['cultivar'] === null) echo '<li><mark>Check <b>Cultivar</b></mark> - CLASSIFY Tab';

		### check BGCI Tab
		### bgci_hide is null
			if ($result['bgci_hide'] === NULL) echo '<li><mark><b>Bgci_hide</b> checkbox is null</mark> - BGCI Tab';
		### bgci_status or bgci_exsitusites is null
			if ($result['bgci_status'] === NULL) {
				echo '<li><mark><b>BGCI Status</b> is NULL</mark> - BGCI Tab';
				echo ' <a href="https://tools.bgci.org/plant_search.php?action=Find&ftrGenus='.$result['genus'];
				echo '&ftrSpecies='.$result['species'].'" target="_blank">Link</a>';
			} #bgci_status
			if ($result['bgci_exsitusites'] === NULL) {
				echo '<li><mark><b>Ex-Situ BGCI Sites</b> is NULL</mark> - BGCI Tab';
				echo ' <a href="https://tools.bgci.org/plant_search.php?action=Find&ftrGenus='.$result['genus'];
				echo '&ftrSpecies='.$result['species'].'" target="_blank">Link</a>';
			} #bgci_status

		### check ASSESS Tab
		### check for missing space required
			if ($result['space_req'] === NULL or $result['space_req']==0) echo '<li><mark><b>Space Required</b> is NULL or zero</mark> - ASSESS Tab';

		### check PEST Tab


		### check CULTURE Tab


		### check LABELS Tab


		### check MISC Tab
		### check for empty credits
			if ($result['credits'] === NULL) echo '<li><mark><b>Credits</b> is NULL</mark> - MISC Tab';
			if (strlen($result['credits'])>0 and (strpos($result['credits'],'ol>')=== false)) echo '<li><mark><b>Credits</b> are missing &ltol&gt&lt/ol&gt tags</mark> - DATA Tab';
		### check for NULL keywords
			if ($result['keywords'] === NULL) echo '<li><mark><b>Keywords</b> is NULL</mark> - MISC Tab';
			
			
			echo '</ol>';

?> 

