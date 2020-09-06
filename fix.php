<?php
	include('config.php');

	$sql = mysql_query("SELECT topic_id FROM topics");
	while($array = mysql_fetch_array($sql)) {

		$tid = $array['topic_id'];
		$fp = mysql_result(mysql_query("SELECT post_id FROM posts WHERE topic_id = '$tid' ORDER BY post_id ASC LIMIT 1"),0);
		if(!mysql_query("UPDATE topics SET first_post_id = '$fp' WHERE topic_id = '$tid' LIMIT 1")) echo '<br>ERROR: '.mysql_error();

	}
?>
