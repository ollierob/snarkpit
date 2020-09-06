<?php
	//$cur_ip = str_replace('.','',$_SERVER['REMOTE_ADDR']);
	//$sql = mysql_query("SELECT ban_ip FROM banlist WHERE ban_ip!=''");
	//while($array = mysql_fetch_array($sql)) {
	//	$ban_ip = str_replace('.','',$array['ban_ip']);
	//	if($ban_ip==$cur_ip OR substr_count($cur_ip,$ban_ip)) die("Your IP (or it's 'range') has been banned from logging into this website.");
	//}

	$subject = strip_tags($_POST['subject']); $description = strip_tags($_POST['description']);
	$poster_ip = $_SERVER['REMOTE_ADDR']; $forum = $_POST['forum']; $topic = $_POST['topic'];
	$map = $_POST['map']; $answer = $_POST['answer']; $post_id = $_POST['post_id'];
	$message = stripslashes($_POST['message']); $action = $_POST['action']; $java = $_POST['java'];
	$chapid = $_POST['chapid']; $addchapter = addslashes($_POST['addchapter']);

	if(!$message) error_die('You can\'t post an empty message!','forums');
	if(!$action) { errorlog('h4xing forums',$_SERVER['REMOTE_ADDR']); error_die('Don\'t h4x us please.'); }
	if(!$forum) error_die('Please select a forum to post in.','forums');
	if(!isset($_POST['chapid'])) $_POST['chapid'] = 0;

if(!$userdata) {
	$username = $_POST['username']; $password = $_POST['password'];
	if(!$username OR !$password) error_die('You must enter your username & password</p><p><b>Your post:</b></p><p>'.$message,'forums');

	$sql = mysql_query("SELECT * FROM users WHERE username = '$username' LIMIT 1");
	$array = mysql_fetch_array($sql);
	if(!$array OR md5($password)!=$array['password']) error_die('Invalid username/password!</p><p><b>Your post:</b></p><p>'.$message,'forums');

	$userdata = get_userdata($username);
	if($userdata['activated']!=1) error_die('You have been temporarily banned for abuse (or your account hasn\'t yet been activated), please come back later.','forums');

	$sessid = new_session($userdata['user_id'],$_SERVER['REMOTE_ADDR'],$sesscookietime);
	set_session_cookie($sessid,$sesscookietime,$sesscookiename,$cookiepath,$cookiedomain,$cookiesecure);
}
	if(!$userdata) error_die('<p>You need to be logged in to post</p><p><b>Your post:</b></p><p>'.$message,'forums'); 

	include('func_parse.php');

if($java=='no') {
	$java='&java=1';
	$message = htmlspecialchars(trim($message));
	$message = str_replace("\n", '<BR>', $message);
} else {
	$java='';
	$message = str_replace('<EM>','<I>',$message); $message = str_replace('</EM>','</I>',$message);
	$message = str_replace('<STRONG>','<B>',$message); $message = str_replace('</STRONG>','</B>',$message);
	$message = str_replace('<FONT face=verdana size=2></FONT>','',$message);
	$message = str_replace('<FONT face=verdana color=white size=2>','',$message);
	$message = str_replace('<FONT face=verdana color=#ffffff size=2>','',$message);
	$message = preg_replace('/<FONT style="BACKGROUND-COLOR: ([^\"]*?)">/si','',$message);
	$message = preg_replace("/\<a href=(.*?)\>\<font color=(.*?)\>(.*?)\<\/font\>\<\/a\>/si",'<a href=\\1>\\3</a>',$message);
	$message = preg_replace('/<img ([^>]*?)src=\"([^>]*?)images\/smiles\/([^>]*?).gif\"([^>]*?)>/si','<img class="smiley" src="images/smiles/\\3.gif">',$message);
}

	$message = preg_replace("/<script([^>]*?)>(.*?)<\/script>/si",'',$message);
	$message = preg_replace("/<iframe([^>]*?)>(.*?)<\/iframe\>/si",'(IFrames are not allowed!)',$message);

	if($sticky=='on') { if($userdata['user_level']<3) $sticky = ''; else $sticky = 1; }
	$message = censor_string($message,''); $subject = censor_string($subject,''); $description = censor_string($description,'');
	if(!$_POST['disablecode']) { $message = bbencode($message,''); $message = smile($message); }

	//image resizing
	if(preg_match_all("#<IMG SRC=\"http:\/\/(.*?)\"(.*?)>#si",$message,$matches,PREG_SET_ORDER)) {
	for($i=0;$i<15;$i++) { if($matches[$i][0] && !substr_count($matches[$i][2],'width=800')) {
		if($imageurl = preg_replace("#<IMG SRC=\"(.*?)\"(.*?)>#si","\\1",$matches[$i][0])) {
			if(substr($imageurl,-4,4)=='.bmp') $message = preg_replace("#<IMG SRC=\"$imageurl\"(.*?)>#si","<a href=\"$imageurl\" target=\"_blank\"><img src=\"images/mrt.jpg\" border=0></a>",$message);
			else {
				$imgsize = getimagesize($imageurl);
				//$filesize = 1024*filesize($imageurl);
				$resized = false;
				if($imgsize[0]>800) {
					$resized = true;
					$message = preg_replace("#<IMG SRC=\"$imageurl\"(.*?)>#si","<a href=\"$imageurl\" target=\"_blank\"><img src=\"$imageurl\" width=800 border=0></a>",$message);
				}
				if($resized) $message = '<p><FONT face="tahoma" color="red">Some images in this post have been automatically down-sized, click on them to view the full sized versions:</FONT></p>'.$message;
			} //normal or .bmp
		}
	} } }
	
	$message = strip_tags($message,'<br><p><b><i><u><div><span><table><tr><td><blockquote><a><hr><pre><img><font><strike><sup><sub><ol><ul><li>');
	$message = addslashes($message);

	if($message=='' OR !$message) {
		if($action=='newtopic') { header("Location: forums.php?mode=newtopic&forum=$forum&error=1$java"); die; }
		if($action=='editpost') { header("Location: forums.php?mode=editpost&forum=$forum&topic=$topic&post_id=$post_id&error=1$java"); die; }
		if($action=='reply') { header("Location: forums.php?mode=reply&forum=$forum&topic=$topic&error=1$java"); die; }
		error_die('You can\'t post an empty message','forums');
	}

	if($action=='reply'||$action=='editpost') {
		//preload topic data since we might need to check stuff
		$sql = mysql_query("SELECT forum_id,topic_status,chapters,first_post_id FROM topics WHERE topic_id = '$topic' LIMIT 1");
		if(!$tarray = mysql_fetch_array($sql)) error_die('Topic does not exist');
	}

	if($action=='editpost') {
		$first_post = is_first_post($topic,$post_id);
		if($first_post && trim($subject)=='') { header("Location: forums.php?mode=editpost&forum=$forum&topic=$topic&post_id=$post_id&error=2$java"); die; }
	}

if($action!='editpost' && !$_POST['addchapter']) {
	$lastpost = mysql_fetch_array(mysql_query("SELECT t.post_text,p.post_time,p.topic_id,p.post_id,p.chapter FROM posts p, posts_text t WHERE p.poster_id = '$userdata[user_id]' AND t.post_id = p.post_id ORDER BY p.post_id DESC LIMIT 1"));
	if($message==$lastpost['post_text']) error_die('<b>Double posting detected</b><br>If you pressed the \'submit\' button twice your post was still entered, go back a few pages or <a href="?forum='.$forum.'&topic='.$topic.'">view the thread</a>.','forums');
	if(($now_time-$lastpost['post_time'])<31 && $userdata['user_level']<3) error_die('<b>Flood control</b><br>You cannot post twice within 30 seconds- press refresh in '.(30-$now_time+$lastpost['post_time']).' seconds to automatically resend your post','forums');

	$agotime = $now_time - $lastpost['post_time'];
	//reply within an hour to same chapter; was chapid!=chapter but why?!
	if($topic==$lastpost['topic_id'] && $agotime<3600 && $_POST['chapid']==$lastpost['chapter'] && $lastpost['post_text']) {
		$actual_last_poster = mysql_result(mysql_query("SELECT poster_id FROM posts WHERE topic_id = '$topic' ORDER BY post_id DESC LIMIT 1"),0);
		if($userdata['user_id']==$actual_last_poster) {
			$action = 'editpost';
			$post_id = $lastpost['post_id'];
			$message = $lastpost['post_text'].'<p><div class="abouttext"><i>Message submitted '.floor($agotime/60).' minutes after original post:</i></b></div><br>'.$message;
			if(is_first_post($topic,$post_id)) {
				$tarray = mysql_fetch_array(mysql_query("SELECT title,description FROM topics WHERE topic_id = '$topic' LIMIT 1"));
				$subject = $tarray['title'];
				$description = $tarray['description'];
			}
		}
	}
}

//if($userdata['user_id']==1) die('mode: '.$action.'; last post '.$agotime.'; chapters '.$_POST['chapid'].'-'.$lastpost['chapter']);

	if($_POST['sig']) $sig = 1; else $sig = 0;

//echo stripslashes(htmlspecialchars($message)); die;

	//post limitations
	if($userdata['dailypostlimit'] && ($action=='reply' OR $action=='newtopic')) {
		$dppostlim = $now_time - (24*3600);
		$dppostmade = mysql_result(mysql_query("SELECT COUNT(post_id) FROM posts WHERE poster_id = '$userdata[user_id]' AND post_time > '$dppostlim'"),0);
		if($dppostmade>$userdata['dailypostlimit']) error_die('You cannot make any more posts today. Check the red box at the top of the screen if you are confused.');
	}

	$answered = '';
	if($action=='newtopic') {

		if($support = mysql_result(mysql_query("SELECT support FROM forums WHERE forum_id = '$forum' LIMIT 1"),0)) $answered = 'n';

		if($map) {
			$maparray = mysql_fetch_array(mysql_query("SELECT user_id,name FROM maps WHERE map_id = '$map' LIMIT 1"));
			if(!$maparray) error_die('Map doesn\'t exist!</p><p>'.$message,'forums');
			if($maparray['user_id']!=$userdata['user_id']) error_die('Map is not yours or doesn\'t exist.</p><p>'.$message,'forums');
			$updatemapsql = ', map = \''.$map.'\''; $chapid = 1; $chapters = 'y'; $chapter = 'Intro';
			$subject = addslashes($maparray['name']); $maparray['map_id'] = $map;
		} else { 
			if($forum==2) error_die('You cannot post a new topic in this forum without referring to a map, go back and try again.<p>'.$message,'forums');
			$updatemapsql = '';
		}

		if(!$subject) { header('Location: forums.php?mode=newtopic&forum='.$forum.'&error=2'.$java); die; }
		if($section=='ERR') $section = '';

		$sql = "INSERT INTO topics (title,topic_poster,forum_id,topic_time,topic_notify,description,section,sticky,answered,chapters) VALUES ('$subject', '$userdata[user_id]', '$forum', '$now_time'";
			if($notify=$_POST['notify'] && $userdata['user_id']!=-1) $sql .= ", '1'"; else $sql .= ", '0'";
		$sql .= ", '$description','$section','$sticky','$answered','$chapters')";
		if(!$result = mysql_query($sql)) { errorlog(mysql_error(),'creating new topic'); error_die('Couldn\'t enter topic into database. Please try again.','forums'); }
		$topic_id = mysql_insert_id();
		
		$updatepollsql = '';
		if($poll==1) {
		        if($polloption1 || $polloption2 || $polloption3 || $polloption4 || $polloption5 || $polloption6) {
			        @mysql_query("INSERT INTO polls (topic_id,option1,option2,option3,option4,option5,option6,voted1,voted2,voted3,voted4,voted5,voted6) VALUES ('$topic_id','$polloption1','$polloption2','$polloption3','$polloption4','$polloption5','$polloption6', ',', ',', ',', ',', ',', ',')");
		               	$updatepollsql = ", answered = 'p'";
			}
		}

		if(!@mysql_query("INSERT INTO posts (topic_id,forum_id,poster_id,sig,post_time,poster_ip,chapter) VALUES ('$topic_id','$forum','$userdata[user_id]','$sig','$now_time','$poster_ip','$chapid')")) { errorlog(mysql_error(),'entering post data for new topic'); error_die('Couldn\'t enter post into datbase. Please try again.','forums'); }
		$post_id = mysql_insert_id();
   		if(!@mysql_query("INSERT INTO posts_text (post_id, post_text) values ('$post_id','$message')")) { @errorlog(mysql_error(),'inserting post (topic)'); error_die('Couldn\'t enter post into database. Please try again.</p><p>'.mysql_error(),'forums'); }

   		if(!@mysql_query("UPDATE topics SET first_post_id = '$post_id', last_post_id = '$post_id' $updatemapsql $updatepollsql WHERE topic_id = '$topic_id' LIMIT 1")) { errorlog(mysql_error(),'updating new topic with post/map/poll info'); @mysql_query("DELETE FROM topics WHERE topic_id = '$topic_id' LIMIT 1"); error_die('Couldn\'t enter post into database. Please try again.','forums'); }
		if($map) @mysql_query("UPDATE maps SET thread = '$topic_id' WHERE map_id = '$maparray[map_id]' LIMIT 1");

		if($chapter) {
			$fwrite = '<?php $chapters = array(\''.str_replace("'",'´',stripslashes($chapter)).'\'=>'.$post_id.',); ?>';
			$fopen = fopen('forums/chapters/'.$topic_id.'.php','w'); fwrite($fopen,$fwrite); fclose($fopen);
			umask(000); chmod('forums/chapters/'.$topic_id.'.php',0777);
		}

		@mysql_query("UPDATE users_profile SET posts=posts+1 WHERE (user_id = $userdata[user_id])");
		@mysql_query("UPDATE forums SET forum_posts = forum_posts+1, forum_topics = forum_topics+1, forum_last_post_id = $post_id WHERE forum_id = '$forum'");

		header('Location: forums.php?forum='.$forum.'&topic='.$topic_id); die;
	}

	elseif($action=='reply') {

		if(!$topic) error_die('Please select a topic to reply to.');

		if($tarray['topic_status']==1 OR $tarray['topic_status']==3) error_die('Topic is locked');
		$forum = $tarray['forum_id'];

		if($reply = $_POST['reply']) {
			if(!$replyto = mysql_result(mysql_query("SELECT topic_id FROM posts WHERE post_id = '$reply' LIMIT 1"),0)) $reply = '';
			if($replyto!=$topic) $reply = '';
		}
	
		if($answer==1) $type = 'q'; else $type = '';

		if($chapid) { include('forums/chapters/'.$topic.'.php'); if($chapid>count($chapters) || !$chapters) $chapid = 0; }
		if($addchapter) { if(substr_count($addchapter,',);')) error_die('Invalid chapter name!'); include('forums/chapters/'.$topic.'.php'); $chapid = count($chapters)+1; }
		if($tarray['chapters'] && !$chapid) $chapid = 1;

		if(!$result = mysql_query("INSERT INTO posts (topic_id,forum_id,poster_id,sig,post_time,poster_ip,type,reply,chapter) VALUES ('$topic','$forum','$userdata[user_id]','$sig','$now_time','$poster_ip','$type','$reply','$chapid')")) error_die('Error - Could not enter post into database. Please go back and try again:<br>'.mysql_error(),'forums');
		$this_post = mysql_insert_id();
		if(!$this_post || !mysql_query("INSERT INTO posts_text (post_id, post_text) VALUES ($this_post, '$message')")) {
			include('header.php'); errorlog(mysql_error(),'inserting into posts_text');
			error_die('Could not enter post text! Please try again.'); 
		}

		if(!mysql_query("UPDATE topics SET topic_replies = topic_replies+1, last_post_id = '$this_post', topic_time = '$now_time' WHERE topic_id = '$topic' LIMIT 1")) errorlog(mysql_error(),'updating topic/post info');
		if(!mysql_query("UPDATE users_profile SET posts=posts+1 WHERE user_id = '$userdata[user_id]'")) errorlog(mysql_error(),'updating user post count');
		if(!mysql_query("UPDATE forums SET forum_posts = forum_posts+1, forum_last_post_id = '$this_post' WHERE forum_id = '$forum'")) errorlog(mysql_error(),'updating forum post count');

		if($addchapter) {
			$addchapter = str_replace("'",'´',$addchapter);
			$fwrite = file_get_contents('forums/chapters/'.$topic.'.php');
			$endpos = strpos($fwrite,',);');
			$fwrite = substr($fwrite,0,$endpos+1).'\''.$addchapter.'\'=>'.$this_post.',); ?>';
			$fopen = fopen('forums/chapters/'.$topic.'.php','w');
			fwrite($fopen,$fwrite);	fclose($fopen); $addchapter = '';
		}		

		if($chapid && !$addchapter) {
			$fwrite = '<?php $chapters = array(';
			include('forums/chapters/'.$topic.'.php'); $c = 1;
			while(list($key,$val)=each($chapters)) { if($c==$chapid) $val = $this_post; $fwrite.= '\''.$key.'\' => '.$val.','; $c++; }
			$fwrite.='); ?>'; 
			$fopen = fopen('forums/chapters/'.$topic.'.php','w'); fwrite($fopen,$fwrite); fclose($fopen);
		}

		$sql = mysql_query("SELECT t.topic_notify, t.last_post_id, u.username, u.user_id FROM topics t, users u WHERE t.topic_id = '$topic' AND t.topic_poster = u.user_id LIMIT 1");
		$m = mysql_fetch_array($sql);
		if($m['topic_notify']==1 && $m['user_id']!=$userdata['user_id']) {
			$email = mysql_result(mysql_query("SELECT user_email FROM users_profile WHERE user_id = '$m[user_id]' LIMIT 1"),0);
			$message = 'This is a message notifying you about a reply to your topic "'.$subject.'". Read it here:
		http://www.snarkpit.net/forums.php?forum='.$forum.'&topic='.$topic;
			$subject = 'Snarkpit forums: Reply to "'.$subject.'"';
			mail($email,$subject,$message, "From: SnarkPit <leperous@snarkpit.net>\r\nX-Mailer: The SnarkPit");
		}
	}

	elseif($action=='editpost') {
		$sql = mysql_query("SELECT * FROM posts WHERE post_id = '$post_id' LIMIT 1");
		$myrow = mysql_fetch_array($sql);
		$forum = $myrow['forum_id']; $chapid = $myrow['chapter'];

		if($userdata['user_id']!=$myrow['poster_id']) {
			if($userdata['user_level']==1) error_die('You are not allowed to edit this message');
			if($userdata['user_level']<3 && !is_moderator($forum, $userdata['user_id'])) error_die('You are not allowed to edit this message');
			errorlog('edited forum post',$post_id,'admin');
		}

		if(!$delete) {
			if($answer==1) $typesql = "type = 'q'";
			elseif($answer==2) $typesql = "type = ''";
			else $typesql = '';
			
			if($now_time-$myrow['post_time']>300) $editsql = "edited = '$now_time'";
			else $editsql = '';

		        $sqlquery = $editsql.(($editsql&&$typesql)?', ':'').$typesql;
			if($sqlquery) $sqlquery = ', '.$sqlquery;

			if(!@mysql_query("UPDATE posts SET sig = '$sig' $sqlquery WHERE post_id = '$post_id' LIMIT 1")) { errorlog(mysql_error(),'updating post (edit)'); error_die('There was an error updating your post, please try again.','forums'); }
			if(!@mysql_query("UPDATE posts_text SET post_text = '$message' WHERE post_id = '$post_id' LIMIT 1")) { errorlog(mysql_error(),'updating post text (edit)'); error_die('There was an error updating your post, please try again.','forums'); }
			$first_post = is_first_post($topic,$post_id);

			if($first_post) {
				$sarray = mysql_result(mysql_query("SELECT sticky FROM topics WHERE topic_id = '$topic' LIMIT 1"),0);
				if($sarray==1) $sticky=1; if($unstick=='on' && $userdata['user_level']>2) $sticky = 0;
				$sql = "UPDATE topics SET title = '$subject', description = '$description'";
					if($_POST['notify']) $sql .= ", topic_notify = '1'";
					if($sticky) $sql .= ", sticky = '$sticky'";
					if($section) $sql .= ", section = '$section'";
				$sql .= " WHERE topic_id = '$topic'";

				if(!$result = mysql_query($sql)) { errorlog(mysql_error(),'topic update'); error_die('Unable to update the topic subject in the database'); }
				if($poll==1) @mysql_query("UPDATE polls SET option1 = '$polloption1', option2 = '$polloption2', option3 = '$polloption3', option4 = '$polloption4', option5 = '$polloption5', option6 = '$polloption6' WHERE topic_id = '$topic' LIMIT 1");
			}

		} else {

			if($now_time-$myrow['post_time']>1200 && $userdata['user_level']<3 && !is_moderator($forum, $userdata[user_id])) error_die('You are not allowed to delete this post');

			if($first_post) error_die('You can\'t delete the first post in a topic, please delete the entire topic.');

			if(!mysql_query("DELETE FROM posts WHERE post_id = '$post_id' LIMIT 1")) { errorlog(mysql_error(),'deleting post from DB'); error_die('Couldn\'t delete post from database'); }
			if(!mysql_query("DELETE FROM posts_text WHERE post_id = '$post_id' LIMIT 1")) { errorlog(mysql_error(),'deleting post text from DB'); error_die('Couldn\t delete post from database'); }
			if(!mysql_query("UPDATE users_profile SET posts = posts - 1 WHERE user_id = '$myrow[poster_id]' LIMIT 1")) errorlog(mysql_error(),'updating user post count after post delete');

			$lastforumpost = mysql_result(mysql_query("SELECT post_id FROM posts WHERE forum_id = '$forum' ORDER BY post_id DESC LIMIT 1"),0);
			if(!mysql_query("UPDATE forums SET forum_posts = forum_posts - 1, forum_last_post_id = '$lastforumpost' WHERE forum_id = '$forum' LIMIT 1")) errorlog(mysql_error(),'updating forum after post delete');

			$ltarray = mysql_fetch_array(mysql_query("SELECT post_id,post_time FROM posts WHERE topic_id = '$topic' ORDER BY post_id DESC LIMIT 1"));
			if(!mysql_query("UPDATE topics SET topic_time = '$ltarray[post_time]', topic_replies = topic_replies - 1, last_post_id = '$ltarray[post_id]' WHERE topic_id = '$topic'")) errorlog(mysql_error(),'updating topic after post delete');

		}
	}

	if($action!='editpost') {
		if($chapid) {
			$replys = mysql_result(mysql_query("SELECT count(post_id) FROM posts WHERE topic_id = '$topic' AND chapter = '$chapid'"),0);
			$replys = $replys - 1;
			$last_post_id = $this_post;
		} else {
			$row = mysql_fetch_array(mysql_query("SELECT topic_replies,last_post_id FROM topics WHERE topic_id = '$topic'"));
			$replys = $row['topic_replies']; $last_post_id = $row['last_post_id'];
		}
		$pagestart = (floor($replys/$ppp))*$ppp;
	}

	if($action=='reply') $bookmark = '#post'.$last_post_id;

	if($action=='editpost' && !$delete) {
		$bookmark = '#post'.$post_id; if($chapid) $chapsql = "AND chapter = '$chapid'"; else $chapsql = '';
		$priorposts = mysql_result(mysql_query("SELECT count(post_id) FROM posts WHERE topic_id = '$topic' AND post_id < '$post_id' $chapsql"),0);
		$pagestart = floor($priorposts/$ppp)*$ppp;
	}

	calcsnarkpoints($userdata['user_id']);

	//echo 'replies:'.$replys.'<br>chapter:'.$chapid.'<br>http://localhost/snarkpit/forums.php?forum='.$forum.'&topic='.$topic.(($pagestart)?'?start='.$pagestart:'').(($chapid)?'&chap='.$chapid:'').$bookmark; die;

	if(!$deleted_topic) { header('Location: forums.php?forum='.$forum.'&topic='.$topic.(($pagestart)?'&start='.$pagestart:'').(($chapid)?'&chap='.$chapid:'').$bookmark); die; }
		else { header('Location: forums.php?forum='.$forum); die; }

	include('header.php');
	error_die('You shouldn\'t be here! :)');
?>
