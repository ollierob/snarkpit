<?php
	$id = $_GET['id'];

	if(!$username = mysql_result(mysql_query("SELECT username FROM users WHERE user_id = '$id' LIMIT 1"),0)) { header('Location: users.php?page=memberlist'); die; }
	title("$t_forums Posts by <a href=\"users.php?name=$username\" class=white>$username</a>",'forums');
	echo '<table width="95%" align=center cellpadding=2>';

	$sql = mysql_query("SELECT * FROM posts WHERE poster_id = '$id' ORDER BY topic_id DESC,post_id ASC LIMIT 200"); $r = '';
	while($array = mysql_fetch_array($sql)) {
		$topicid = 'topic'.$array['topic_id'];
		$tlink = '?forum='.$array['forum_id'].'&topic='.$array['topic_id'];
		if(!$$topicid) { $$topicid++; if($r) echo '<tr><td height=10></td></tr>';
			echo '<tr><td colspan=2>';
			$tarray = mysql_fetch_array(mysql_query("SELECT answered,topic_poster,title FROM topics WHERE topic_id = '$array[topic_id]' LIMIT 1"));
			if($tarray['answered']=='p') echo '<b><font color="'.$colors['info'].'">poll:</font></b> ';
				else echo '<b><font color="'.$colors['subtitle'].'">topic:</font></b> ';
			if($tarray['topic_poster']==$id) echo '<b>'; echo '<a href="'.$tlink.'">'.stripslashes($tarray['title']).'</a></b>';
			echo ' started by '.@userdetails($tarray['topic_poster'],'white','return','','','');
		}	

		echo '<tr><td></td><td><a href="'.$tlink.'&findpost='.$array['post_id'].'#post'.$array['post_id'].'" class=white><b>post</b> at '.date("H:i",$array[post_time]).' on '.date("jS M Y",$array[post_time]).'</a>';
		
		echo '</td></tr>';
	$r++; }

	if($r==200) echo '<tr><td colspan=2><b>search has hit maximum of 200 results</b></td></tr>';
	else {
	     	if($r<1) $r = 'No';
		echo '<tr><td colspan=2><b>'.$r.' posts made by this user.</td></tr>';
		//@mysql_query("UPDATE users_profile SET posts = '$r' WHERE user_id = '$id' LIMIT 1");
	}

?>

<tr><td width=16><img src="images/null.gif" width=16 height=1></td><td width="100%"></td></tr>
</table>
