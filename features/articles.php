<?php
if($id) {
	$sql = mysql_query("SELECT * FROM articles WHERE id = '$id' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('Article not found'); 
	$title = stripslashes($array['title']); $desc = stripslashes($array['description']);

	@mysql_query("UPDATE articles SET hits = hits + 1 WHERE id = '$id' LIMIT 1");

	title("$t_features Articles » $title",'');
	echo '<p>('.$desc.'- by '.@userdetails($array['user_id'],'','return').')</p>'."\n";
	if($array['pinclude']) { if(!include('content/'.$array['pinclude'])) error_die('Article could not be found!',''); }
	else {
		$sql = mysql_query("SELECT * FROM articles_text WHERE id = '$id' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) error_die('Article text not found!');
		include('lib/entify.php');
		echo entify(stripslashes($array['text']));
	}

footer(); }

?>
