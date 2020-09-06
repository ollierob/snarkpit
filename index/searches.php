<?php
	title('Search Phrases');

	if($userdata['user_level']==4) {
		@mysql_query("DELETE FROM search WHERE hits < 3");
		@mysql_query("OPTIMIZE TABLE search");
	}

	$sql = mysql_query("SELECT * FROM search ORDER BY hits DESC");
	while($a = mysql_fetch_array($sql)) echo $a['phrase'].' - '.$a['hits']."<br>\n";

?>
