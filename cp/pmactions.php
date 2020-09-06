<?php
	include('../config.php');
	if(!$userdata) { header('Location: ../login.php?linkto=cp.php'); die; }

if($_POST['delete']) {
	for($i=0; $i<50; $i++) { $lname = 'list'.$i; if($_POST[$lname]) {
		$msgid = $_POST[$lname];
		@mysql_query("DELETE FROM messages WHERE msg_id = '$msgid' AND to_id = '$userdata[user_id]' LIMIT 1");
	$cd++; } } if($cd) $msg = '&msg=Messages+deleted';
	header('Location: ../cp.php?mode=inbox'.$msg);
}

if($_POST['block']) {
	for($i=0; $i<50; $i++) { $lname = 'list'.$i; if($_POST[$lname]) { $blockuserid='';
		$msgid = $_POST[$lname];
		$blockuserid = mysql_result(mysql_query("SELECT from_id FROM messages WHERE msg_id = '$msgid' AND to_id = '$userdata[user_id]' LIMIT 1"),0);
		if($blockuserid!='-1' AND $blockuserid!='1') { 
		$blocklist = mysql_result(mysql_query("SELECT pm_block FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1"),0);
			if(!$blocklist) $blocklist = ',';
			$newblocklist=$blocklist.$blockuserid.',';
		if(!substr_count($blocklist,','.$blockuserid.',')) @mysql_query("UPDATE users_profile SET pm_block = '$newblocklist' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	} else { $msg = '&msg=Cannot+block+this+user'; } $cb++; } } if($cb && !$msg) $msg = '&msg=Users+blocked';
	header("Location: ../cp.php?mode=inbox$msg"); die;
}

if($_POST['unread']) {
	for($i=0; $i<50; $i++) { $lname = 'list'.$i; if($_POST[$lname]) { $blockuserid='';
		$msgid = $_POST[$lname];
		@mysql_query("UPDATE messages SET status = 1 WHERE msg_id = '$msgid' AND to_id = '$userdata[user_id]' LIMIT 1");
	$cu++; } } if($cu) $msg = '&msg=Messages+marked+as+unread';
	header("Location: ../cp.php?mode=inbox$msg"); die;
}

if($_POST['unblock']) {
	$pmblock = mysql_result(mysql_query("SELECT pm_block FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1"),0); $cb='';
	while(list($key,$val)=each($unblocklist)) { 
		$pmblock = str_replace(",$unblocklist[$key],",",",$pmblock);
	$cb++; } 
	if($cb) {
		@mysql_query("UPDATE users_profile SET pm_block = '$pmblock' WHERE user_id = '$userdata[user_id]' LIMIT 1");
		$msg = '&msg=Users+unblocked';
	}
	header('Location: ../cp.php?mode=inbox'.$msg); die;
}


if($_POST['submit']) {
	if($userdata['user_id']<1) { errorlog('PM error'); header("Location: ../cp.php?error=user_id+not+recognised?!"); die; }

	include('../func_parse.php'); 
	$text = stripslashes($_POST['message']); $text = trim(htmlspecialchars($text)); $text = str_replace("\n","<br>",$text); $text = addslashes(bbencode($text));
	$tofield = $_POST['tofield']; $subject = $_POST['subject'];
	if(!$tofield OR !$text) { header('Location: ../cp.php?mode=compose'); die; }

	if(!$to_id = mysql_result(mysql_query("SELECT user_id FROM users WHERE username = '$tofield' LIMIT 1"),0)) { include('header.php'); include('cp/sidebar.php'); error_die("That user doesn't exist</p>".str_replace("\n","<br>",$message)); }
	if($to_id<1) { header('Location: cp.php?error=You+can\'t+PM+the+website!!'); die; }
	$subject = trim(htmlspecialchars($subject));
	if(!$conversation) { 
		$x = mysql_result(mysql_query("SELECT conversation FROM messages ORDER BY conversation DESC LIMIT 1"),0);
		$conversation = $x + 1;
	}
	if(!$into = mysql_query("INSERT INTO messages (from_id,to_id,time,subject,text,from_ip,reply,conversation) VALUES ('$userdata[user_id]','$to_id','$now_time','$subject','$text','$_SERVER[REMOTE_ADDR]','$reply','$conversation')")) { include('../header.php'); include('sidebar.php'); error_die('Failed to send message: '.mysql_error().'</p>'.str_replace("\n","<br>",$message)); }

	if($_POST['topic'] && $_POST['post']) header('Location: ../forums.php?topic='.$_POST['topic'].'&findpost='.$_POST['post'].'#post'.$_POST['post']);
	else header("Location: ../cp.php?mode=inbox&msg=Message+sent");

	die;
}

?>
