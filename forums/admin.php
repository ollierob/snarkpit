<?php
	if(!$userdata || $userdata['user_level']<2) error_die('You are not allowed to view this page');
	if($userdata['user_level']==2 && !is_moderator($forum,$userdata['user_id']) && $action!='viewip') error_die('You are not a moderator of this forum');
	$t_admin = 'The SnarkPit Forums » Admin »';

function forum_sync($forum) {
	$lp = @mysql_result(mysql_query("SELECT post_id FROM posts WHERE forum_id = '$forum' ORDER BY post_id DESC LIMIT 1"),0);
	$countposts = @mysql_result(mysql_query("SELECT count(post_id) FROM posts WHERE forum_id = '$forum'"),0);
	$counttopics = @mysql_result(mysql_query("SELECT count(topic_id) FROM topics WHERE forum_id = '$forum'"),0);
	@mysql_query("UPDATE forums SET forum_topics = '$counttopics', forum_posts = '$countposts', forum_last_post_id = '$lp' WHERE forum_id = '$forum' LIMIT 1");
}

switch($_GET['action']) {

case('viewip'):
	title($t_admin.' User Ip','none');
	$sql = mysql_query("SELECT * FROM posts WHERE post_id = '$post' LIMIT 1");
	$parray = mysql_fetch_array($sql); $ip = $parray['poster_ip'];
	echo 'Poster IP: '.$ip; if($host = gethostbyaddr($ip)) echo ' ('.$host.')';
	echo '<br>Posted by '; userdetails($parray['poster_id']);

	echo '<p>Other users with same IP: ';
	$sql = mysql_query("SELECT DISTINCT poster_id FROM posts WHERE poster_id!='$parray[poster_id]' AND poster_ip = '$parray[poster_ip]'");
	$countdistinct = mysql_num_rows($sql);
	while($darray = mysql_fetch_array($sql)) {
		userdetails($darray[poster_id]); $c++; if($c<$countdistinct) echo ', ';
	}

	while(substr($ip,-1)!='.') $ip = substr_replace($ip,'',-1); $ip = substr_replace($ip,'',-1);
	echo '<p>Other users with same IP range ('.$ip.'): ';
	$sql = mysql_query("SELECT DISTINCT poster_id FROM posts WHERE poster_id!='$parray[poster_id]' AND poster_ip LIKE '$ip%'");
	$countdistinct = mysql_num_rows($sql); $c = '';
	while($darray = mysql_fetch_array($sql)) {
		userdetails($darray[poster_id]); $c++; if($c<$countdistinct) echo ', ';
	}
	
	echo '<p>Other IPs used by this person:<blockquote>';
	$sql = mysql_query("SELECT DISTINCT(poster_ip) FROM posts WHERE poster_id = '$parray[poster_id]'");
	while($array = mysql_fetch_array($sql)) echo $array['poster_ip'].' ('.gethostbyaddr($array['poster_ip']).')<br>';
	echo '</blockquote>';

break;

case(lock):
	title($t_admin.' Lock Topic','none');
	$is_locked = @mysql_result(mysql_query("SELECT topic_status FROM topics WHERE topic_id = '$topic' LIMIT 1"),0);
	if($is_locked) {
		echo 'Topic is already locked; <b><a href="?mode=admin&action=unlock&topic='.$topic.'">click here</a></b> to unlock.';
	} else {
		@mysql_query("UPDATE topics SET topic_status = 1 WHERE topic_id = '$topic' LIMIT 1");
		echo 'Topic locked.';
	}
break;

case(unlock):
	title($t_admin.' Unlock Topic','none');
	$is_locked = @mysql_result(mysql_query("SELECT topic_status FROM topics WHERE topic_id = '$topic' LIMIT 1"),0);
	if($is_locked) {
		@mysql_query("UPDATE topics SET topic_status = 0 WHERE topic_id = '$topic' LIMIT 1");
		echo 'Topic unlocked.';
	} else {
		echo 'Topic is already unlocked; <b><a href="?mode=admin&action=lock&topic='.$topic.'">click here</a></b> to lock.';
	}
break;

case('del'):
	title($t_admin.' Delete Topic','none');
	if(!$confirm) echo 'Click to confirm: <input type="submit" value="delete" onclick="self.location.href=\'forums.php?mode=admin&action=del&forum='.$forum.'&topic='.$topic.'&confirm=1\'" class=submit3>'; else {
		$altusers = array();
		$tarray = mysql_fetch_array(mysql_query("SELECT subject,topic_poster,map FROM topics WHERE topic_id = '$topic' LIMIT 1"));
		@mysql_query("DELETE FROM topics WHERE topic_id = '$topic' LIMIT 1");
		$sql = mysql_query("SELECT poster_id,post_id FROM posts WHERE topic_id = '$topic'"); $sumposts=0;
		while($array = mysql_fetch_array($sql)) { 
			$altusers[$array['poster_id']]++; 
			if($array[poster_id]>$maxuserid) $maxuserid = $array['poster_id']; 
			@mysql_query("DELETE FROM posts_text WHERE post_id = '$array[post_id]' LIMIT 1");
			@mysql_query("DELETE FROM posts WHERE post_id = '$array[post_id]' LIMIT 1");
			$sumposts++;
		}

		for($i=1;$i<=$maxuserid;$i++) { if($altusers[$i]!=0) @mysql_query("UPDATE users_profile SET posts = posts - $altusers[$i] WHERE user_id = '$i' LIMIT 1"); }

		@mysql_query("DELETE FROM posts WHERE topic_id = '$topic'");
		@mysql_query("DELETE FROM polls WHERE topic_id = '$topic' LIMIT 1");
		if(file_exists('forums/chapters/'.$topic.'.php')) unlink('forums/chapters/'.$topic.'.php');
		if($tarray['map']) @mysql_query("UPDATE maps SET thread = '' WHERE map_id = '$tarray[map]' LIMIT 1");

		$message = addslashes('Your topic, '.stripslashes($tarray['subject']).', has been deleted, either because it was posted in the wrong forum, breached forum rules, was considered to be annoying spam, or just out of date or unnecessary. <b>Please read forum rules before posting in future!</b>');
		if($tarray['topic_poster']) @mysql_query("INSERT INTO messages (from_id,to_id,time,subject,text,status) VALUES ('-1','$tarray[topic_poster]','$now_time','Automated message- topic deleted','$message','1')");

		$lparray = mysql_fetch_array(mysql_query("SELECT * FROM posts WHERE forum_id = '$forum' ORDER BY post_id DESC LIMIT 1"));
		if($lparray) $sql = "forum_last_post_id = '$lparray[post_id]',"; else $sql = '';
		@mysql_query("UPDATE forums SET $sql forum_topics = forum_topics - 1, forum_posts = forum_posts - $sumposts WHERE forum_id = '$forum' LIMIT 1");
		echo 'Topic deleted.';
	}
break;

case(move):
	title($t_admin.' Move Topic','');
	$moveto = $_GET['moveto'];
	if(!$_POST['moveto']) { ?>
		<form action="forums.php?mode=admin&action=move" method="post">
		Select a forum to move this topic to:
		<select name="moveto">
			<?php $sql = mysql_query("SELECT forum_id,forum_name FROM forums WHERE forum_id != '$forum'");
			while($farray = mysql_fetch_array($sql)) {
				echo '<option value="'.$farray['forum_id'].'">'.stripslashes($farray['forum_name']);
			} ?>
		</select>
		<input type="hidden" name="from" value="<?=$forum?>"><input type="hidden" name="topic" value="<?=$topic?>">
		<input type="submit" value="move" class="submit3">
	<?php } else {

		if(!$topic = $_POST['topic']) error_die('No topic specified',''); $moveto = $_POST['moveto']; $from = $_POST['from'];
		@mysql_query("UPDATE topics SET forum_id = '$moveto', answered = '' WHERE topic_id = '$topic' LIMIT 1");
		@mysql_query("UPDATE posts SET forum_id = '$moveto' WHERE topic_id = '$topic' LIMIT 1");

		forum_sync($moveto); forum_sync($from);

		$tarray = mysql_fetch_array(mysql_query("SELECT topic_poster,title FROM topics WHERE topic_id = '$topic' LIMIT 1"));
		$message = addslashes('Your topic, '.$tarray['title'].', has been moved as it was posted in an inappropriate forum. You can now view it <b><a href="javascript:void(0)" onclick="opener.location=\'forums.php?forum='.$moveto.'&topic='.$topic.'\'">here</a></b>.');
		@mysql_query("INSERT INTO messages (from_id,to_id,time,subject,text,status) VALUES ('-1','$tarray[topic_poster]','$now_time','Automated message- topic moved','$message','1')");
		echo 'Topic moved. <a href="forums.php?forum='.$moveto.'&topic='.$topic.'"><b>Click here</b></a> to view it.';
	}
break;

case(sticky):
	title($t_admin.' Sticky','none');
	if($tarray[sticky]) { @mysql_query("UPDATE topics SET sticky = 0 WHERE topic_id = '$topic' LIMIT 1"); echo "Topic made un-sticky."; }
		else { @mysql_query("UPDATE topics SET sticky = 1 WHERE topic_id = '$topic' LIMIT 1"); echo "Topic made sticky."; }

break;

case('answer'):
	if(!$confirm) echo 'Are you sure you want to make this topic "answered", i.e. if the original post was\'t a question in the first place? <input type="submit" class="submit3" value="Yes" onclick="self.location=\'forums.php?mode=admin&action=answer&forum='.$forum.'&topic='.$topic.'&confirm=1\'">';
	else {
		@mysql_query("UPDATE topics SET answered = 'y' WHERE forum_id = '$forum' AND topic_id = '$topic' LIMIT 1");
		header("Location: forums.php?forum=$forum&topic=$topic");
	}
break;

case('fix_chapters'):

	if(!@include('forums/chapters/'.$topic.'.php')) {
		$chapters = array(); $i = 1;
		$sql = mysql_query("SELECT DISTINCT(chapter),post_id FROM posts WHERE topic_id = '$topic'");
		while($array = mysql_fetch_array($sql)) {
			$chapters['chapter '.$array['chapter']] = $array['post_id'];
		}
	}

	$c = 1; $rewrite = false;
	$fwrite = '<?php $chapters = array(';

	//for each chapter pull up the last post in the database to check if it's the one listed in the chapter data file
	while(list($chapname,$lpid)=each($chapters)) {
		$lp = @mysql_result(mysql_query("SELECT * FROM posts WHERE topid_id = '$topic' AND chapter = '$c' ORDER BY post_id DESC LIMIT 1"),0);
		if($lp) $fwrite .= '\''.$chapname.'\' => '.$lp.',';
		if($lp && $lp!=$lpid) $rewrite = true;
		$c++;
	}
	
		$fwrite .= '); ?>';

	if($rewrite) {
	        if(file_exists('forums/chapters/'.$topic.'.php')) chmod('forums/chapters/'.$topic.'.php',0766);
	        $fopen = fopen('forums/chapters/'.$topic.'.php','w');
		fwrite($fopen,$fwrite);
		fclose($fopen);
		chmod('forums/chapters/'.$topic.'.php',0766);
		echo 'Chapter data file errors fixed.';
	} else echo 'No chapter data errors found.';
break;

}

?>
