<?php
	title($t_cp.' <a href="cp.php?mode=inbox" class="white">Private Messages</a>: Compose','cp');

	$error = ''; $subject = $_GET['subject'];

	if($to=$_GET['to']) {
		$userid = mysql_result(mysql_query("SELECT user_id FROM users WHERE username = '$to' LIMIT 1"),0);
		if($userid<1) { $error = 'Er, you can\'t PM the website :P'; $to = ''; }
		if(!$userid) { $error = 'That user does not exist.'; $to = ''; }
	}

	if($replyto=$_GET['replyto']) { 
		$sql = mysql_query("SELECT * FROM messages WHERE msg_id = '$replyto' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) { header('Location: cp.php?mode=inbox&msg=Can\'t+reply+to+this+message'); die; }
		if($array['to_id']!=$userdata['user_id'] && $userdata['user_level']<4) { header('Location: cp.php?mode=inbox&msg=Can\'t+reply+to+this+message'); die; }
		$subject = 're.: '.stripslashes(str_replace('re.:','',$array['subject']));
		include('func_parse.php');
		if(!$to) $to = mysql_result(mysql_query("SELECT username FROM users WHERE user_id = '$array[from_id]' LIMIT 1"),0);
		if($to=='The SnarkPit') { header('Location: cp.php?msg=You+can\'t+PM+the+website!'); die; }
	}

	elseif($foward=$_GET['foward']) {
		$sql = mysql_query("SELECT * FROM messages WHERE msg_id = '$foward' AND to_id = '$userdata[user_id]' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) header("Location: cp.php?mode=inbox&msg=can't+foward+this+message");
		$subject = 'fw.: '.stripslashes(str_replace('fw.: ','',$array['subject']));
		include('func_parse.php');
		$message = "\n\n\n[b]Message received from ".stripslashes(mysql_result(mysql_query("SELECT username FROM users WHERE user_id = '$array[from_id]' LIMIT 1"),0)).":[/b]\n[box]".str_replace("<br>","\n",stripslashes(bbdecode(desmile(trim($array[text])))))."\n[/box]";
	}

	if($error) msg($error,'error');

?>

<script language="javascript">
	function checkform() { 
		var alertmsg = "";
		if(document.getElementById('tofield').value=="") alertmsg = "The message must be sent to someone";
		if(document.getElementById('message').value=="") alertmsg = "You can't send an empty message";

		if(alertmsg!="") { 
			alert(alertmsg);
			return false;
		} else {
			document.body.style.cursor = 'wait';
			//document.getElementById('submit').disabled = true;
			//document.forms['pmform'].submit();
			return true;
		}
	}
</script>

<p>
<form action="cp/pmactions.php" method="post" name="pmform" onsubmit="return checkform()">
<?php
	if($replyto) echo '<input type="hidden" name="reply" value="'.$replyto.'"><input type="hidden" name="conversation" value="'.$array['conversation'].'">';
	if($_GET['topic'] && $_GET['post']) echo '<input type="hidden" name="topic" value="'.$_GET['topic'].'"><input type="hidden" name="post" value="'.$_GET['post'].'">';
?>

<table width="100%">
<tr>
	<td align=right><b>from:</b></td>
	<td><?=$userdata['username']?></td>
</tr>
<tr>
	<td align=right><b>to:</b></td>
	<td><input type="text" name="tofield" value="<?=$to?>" class=textinput size=32 maxlength=32>
	<b><a href="javascript:void(0)" onclick="popwin('popup.php?mode=memberlist','yes')" onMouseOver="window.status='Memberlist (popup window)'; return true" onMouseOut="window.status=''">memberlist</a></b></td>
</tr>

<tr><td height=10></td></tr>

<tr>
	<td align=right><b>subject:</b></td>
	<td><input type="text" name="subject" value="<?=stripslashes($subject)?>" size=32 maxlength=96 class=textinput></td>
</tr>
<tr>
	<td align=right valign=top><b>message:</b></td>
	<td><textarea name="message" rows=10 cols=64><?=$message?></textarea>
	<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode')">BBCode</a> and <a href="javascript:void(0)" onclick="popwin('popup.php?mode=smilies&bbsmilies=1','yes')">smilies</a> are enabled, HTML is disabled</td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" name="submit" value="Send" class="submit3"></td>

<tr><td width="15%"></td><td width="85%"></td></tr>
</table>
</form>
</p>
<p>
<?php if($replyto && $array['conversation']>0) { 

	echo '<p>';
	subtitle('Post conversation:','');
	echo '<blockquote>';

	$sql = mysql_query("SELECT * FROM messages WHERE conversation = '$array[conversation]' ORDER BY msg_id DESC");
	while($array = mysql_fetch_array($sql)) {
	        echo '<p><font size=2 color="'.$colors['text'].'">Message from '.userdetails($array['from_id'],'white','return','','','').':</font>';
		echo "\n".'<div class="abouttext" style="margin-left:20px;padding:2px">'.bbencode(stripslashes($array['text']),'').'</div>';
	}
	echo '</blockquote>';
} ?>
