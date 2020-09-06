<?php
	if(!$edit = $_GET['edit']) { header("Location: forums.php?forum=$forum&topic=$topic"); die; }

	include('forums/post.php');

	$sql = mysql_query("SELECT * FROM posts WHERE post_id = '$edit' LIMIT 1");
	if(!$parray = mysql_fetch_array($sql)) { header("Location: forums.php?forum=$forum&topic=$topic"); die; }

	if($parray['forum_id']!=$forum) { $farray = mysql_fetch_array(mysql_query("SELECT * FROM forums WHERE forum_id = '$parray[forum_id]' LIMIT 1")); $forum = $parray['forum_id']; }
	if($parray['topic_id']!=$topic) { $tarray = mysql_fetch_array(mysql_query("SELECT * FROM topics WHERE topic_id = '$parray[topic_id]' LIMIT 1")); $topic = $parray['topic_id']; }

	if($userdata) {
		if ($userdata['user_level']==1 && $userdata['user_id']!=$parray['poster_id']) error_die('You are not allowed to edit this message','forums');
		if ($userdata['user_level']==2 && $userdata['user_id']!=$parray['poster_id'] && !is_moderator($forum, $userdata['user_id'])) error_die('You are not allowed to edit this message','forums');
	} else {
	       	header('Location: login.php?linkto='.$_SERVER['PHP_SELF'].urlencode($_SERVER['REQUEST_URI']));
		die;
	}

	if($tarray['topic_status']==1 && $userdata['user_level']<3) error_die('This topic has been locked- you can\'t edit messages in it');

	title('Edit Post','forums');

	if($error==1) msg('You can\'t post an empty message, please try again','error');

	post_start('');

	if($first_post = is_first_post($topic,$edit)) {
		$topictitle = stripslashes(str_replace("'","&#39;",$tarray['title']));
		$topicdesc = stripslashes(str_replace("'","&#39;",$tarray['description']));
		post_subject(stripslashes($topictitle));
		post_description(stripslashes($topicdesc));
		
		if($tarray['section']) {
			if(include('lib/forums_'.$farray['game'].'.php')) {
				echo "\n".'<tr><td align=right><b>Related to:</b></td><td><select name="section"><option id="cat_white" value="ERR">Select an option:</option>';
				while(list($var,$val)=each($helpsections)) echo '<option value="'.$var.'"'.(($tarray['section']==$var)?' selected':'').'>'.$var.'</option>';
				echo '</select>';
			}
		}

	}

	post_message('',$edit,'');

	echo '<tr><td></td><td><fieldset style="border: 1px solid '.$colors['item'].';width:550px"><legend style="color:'.$colors['item'].'">Options</legend>';

	//for polls etc.:
	if(!$message) $message = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$edit' LIMIT 1"),0);

	echo '<input type="checkbox" name="sig"'.(($parray['sig'])?' checked':'').'>Add signature';

	if($first_post) { 
		if($tarray['topic_notify'] == 1) $chk = ' CHECKED'; echo '<br><INPUT TYPE="CHECKBOX" NAME="notify"'.$chk.'>Notify me by e-mail when someone replies'; 
	} else {
		if($now_time-$tarray['topic_time']<360) echo '<BR><INPUT TYPE="CHECKBOX" NAME="delete">Delete this post';
	}

	echo "\n".'<BR><INPUT TYPE="CHECKBOX" NAME="disablecode">Disable BBCode/smilies';
	if($first_post) {
		if($tarray['sticky']==1 && $userdata['user_level']>2) echo "\n".'<br><input type=checkbox name=unstick>Tick to make unsticky';
		if($tarray['sticky']!=1 && $userdata['user_level']>2) echo '<br><input type="checkbox" name="sticky">Make Sticky';
	}
	
	echo '</fieldset></td></tr>';

	//if(!$first_post && $tarray['answered']=='n') post_answer($parray[type]);
	if($first_post && $tarray['answered']=='p') {
		if(!$polldata = mysql_fetch_array(mysql_query("SELECT * FROM polls WHERE topic_id = '$topic' LIMIT 1"))) error_die('Error getting poll data!');
		include('forums/plugins/post_poll.php'); post_poll($polldata);
	}

	if(!$parray['type']) $parray['type'] = 'n';
	post_submit('editpost','Submit',((!$first_post && $tarray['answered']=='n')?$parray['type']:''));
	topic_review($topic,$edit);


?>
