<?php
if(isset($_GET['yes'])) $yes = $_GET['yes']; else $yes = '';
if(isset($_GET['no'])) $no = $_GET['no']; else $no = '';
if(isset($_GET['reset'])) $reset = $_GET['reset']; else $reset = '';
if($yes || $no || $reset) $topicstarter = mysql_result(mysql_query("SELECT topic_poster FROM topics WHERE topic_id = '$topic' LIMIT 1"),0);
if(($no||$yes||$reset) && ($userdata['user_level']>2 OR is_moderator($forum,$userdata['user_id']) OR $userdata['user_id']==$topicstarter)) {
	if($no) { $type = 'n'; $alter = '-1'; $pid = $no; }
	if($yes) { $type = 'y'; $alter = '+1'; $pid = $yes; @mysql_query("UPDATE topics SET answered = 'y' WHERE topic_id = '$topic' LIMIT 1"); }
	if($reset) { $type = 'q'; $pid = $reset; $alter = 0; }
	$post_array = mysql_fetch_array(mysql_query("SELECT type,poster_id FROM posts WHERE post_id = '$pid' LIMIT 1"));
	@mysql_query("UPDATE posts SET type = '$type' WHERE post_id = '$pid' LIMIT 1");
	if($no || $reset) { if(!$numanswers = mysql_result(mysql_query("SELECT post_id FROM posts WHERE topic_id = '$topic' AND type = 'y' LIMIT 1"),0)) @mysql_query("UPDATE topics SET answered = 'n' WHERE topic_id = '$topic' LIMIT 1"); }
	if($numcorrect = mysql_result(mysql_query("SELECT COUNT(post_id) FROM posts WHERE type = 'y' AND poster_id = '$post_array[poster_id]'"),0)) @mysql_query("UPDATE users_profile SET answered = '$numcorrect' WHERE user_id = '$post_array[poster_id]' LIMIT 1");
	header('Location: forums.php?forum='.$forum.'&topic='.$topic.'&findpost='.$pid.'#post'.$pid); die;
}
?>
