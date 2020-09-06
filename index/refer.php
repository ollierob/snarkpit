<?php title('Top referring websites:'); ?>
<?php
	@mysql_query("DELETE FROM refer WHERE hits < 5");

 	$sql = mysql_query("SELECT * FROM refer ORDER BY hits DESC");
	while($array = mysql_fetch_array($sql)) {
		echo "\n".'	<a href="'.$array[url].'" target="_blank">'.$array[url].'</a> ('.$array[hits].')';
		if($array[loc]) echo ' <b>'.$array[loc].'</b>';
		echo '<br>';

	}
?>
