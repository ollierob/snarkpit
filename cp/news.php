<?php	$title=''; if(!$action = $_GET['action']) $action = 'newpr';
	$select = $_GET['select']; $edit = $_GET['edit'];

	if($delete=$_GET['delete']) { $authorid = mysql_result(mysql_query("SELECT user_id FROM news WHERE id = '$delete' LIMIT 1"),0);
		if(!$authorid OR $authorid!=$userdata[user_id]) { header("Location: cp.php?msg=news+isn't+yours+or+doesn't+exist"); die; }
		@mysql_query("DELETE FROM news WHERE id = '$delete' LIMIT 1");
		header("Location: cp.php?msg=news+deleted");
	}

	if($edit && !$select) {
		$sql = mysql_query("SELECT * FROM news WHERE id = '$edit' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) { header('Location: cp.php?error=news+item+does+not+exist!'); die; }
		if($array['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) error_die("You can't edit this news item as it's not yours");
		$title = 'Edit News';
		$hidden = '<input type="hidden" name="action" value="editnews"><input type="hidden" name="edit" value="'.$edit.'">';
	}

	if($edit && $select) {
		$title = 'Edit News (waiting to be authed)';
		$sql = mysql_query("SELECT * FROM authenticate WHERE id = '$edit' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) { header('Location: cp.php?error=news+does+not+exist'); die; }
		$array['subject'] = $array['title']; $action = 'newfp';
		$hidden = '<input type="hidden" name="action" value="editnews"><input type="hidden" name="edit" value="'.$edit.'"><input type="hidden" name="auth" value="1">';
	}

	if(!$auth=$_GET['auth'] AND !$edit) {
		$title = 'New News Item';
		$hidden = '<input type="hidden" name="action" value="news">';
	}

	if(!$title) { header("Location: cp.php"); die; }

	title("$t_cp $title",cp); include('func_parse.php');
	$array['text'] = trim(stripslashes(str_replace("<br>","\n",bbdecode(desmile($array['text'])))));

?>

<p>This page lets you add news items to your profile, and also the front page of this site if you want to. Post anything you want
in your profile, but the front page is for telling us about map releases and other important things- <b>do not</b> use it 
to ask for editing help or to talk about random topics, and please keep it fairly short. Large news items that take up the 
whole page (unless they're important) or irrelevant posts will probably be confined to your profile.
Also, if you are posting about a map, please include a link to info or screenshots instead of just saying 'check my profile' (otherwise
we won't put it up on the front page!). If you are submitting front page news, don't repost it as profile news.
<p>Check out our <a href="rss.xml"><b>RSS news feed</b></a>!
<p>

<script language="javascript">
	function checkform() {
		alertmsg="";
		if(formname.subject.value=="") { alertmsg = "Please enter a subject/title for your news"; }
		if(formname.message.value=="") { alertmsg = "Please enter some text!"; }
		if(alertmsg) { alert(alertmsg); return false; }
	}

</script>

<p><table width="100%">
<form action="cp.php" method="post" onsubmit="return checkform()" name="formname">

<?=$hidden?>

<tr>
	<td align=right><b>subject:</b></td>
	<td><input type="text" name="subject" value="<?=$array[subject]?>" class=textinput size=48 maxlength=64></td>
</tr>

<tr>
	<td align=right valign=top><b>map:</b></td>
	<td><select name="map"><option value="" id="cat_white">not map news</option>
	<?php
		$gsql = mysql_query("SELECT map_id,name,game FROM maps WHERE user_id = '$userdata[user_id]' AND thumbnails > 0 ORDER BY name");
		while($garray = mysql_fetch_array($gsql)) echo '<option value="'.$garray['map_id'].'">'.stripslashes($garray['name']).' ('.$garray['game'].')</option>';
	?></select>
	<div class="help">If you are posting map news, please select it from the list above and it will automatically
	insert thumbnailed images into your news post (if there are any). Maps not listed above don't have any screenshots
	uploaded to this website; use [img]*[/img] or [thumb=*]*[/thumb] tags instead.</div>
	</td>
</tr>

<tr>
	<td align=right valign=top><b>text:</b></td>
	<td><textarea name="message" rows=8 cols=64><?=$array['text']?></textarea>
	<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode')">BBCode</a> and <a href="javascript:void(0)" onclick="popwin('popup.php?mode=smilies&bbsmilies=1','1')">smilies</a> are enabled, HTML is disabled</td>
</tr>

<tr>
	<td></td>
	<td><input type="radio" name="where" value="profile" <?php if($action=='newpr' OR $array['plan']==2) echo 'checked="checked"'; if($edit) echo ' DISABLED';?>> Post this just in my profile<br>
	<input type="radio" name="where" value="frontpage" <?php if($action=='newfp' OR $array['plan']==1) echo 'checked="checked"'; if($edit) echo ' DISABLED';?>> Post this in my profile and on the front page</p>
</tr>

<tr>

	<td></td>
	<td><input type="submit" name="submit" value="Submit" class=submit3>
	<?php if($edit) echo '[<b><a href="?mode=news&delete='.$edit.'" onclick="return confirm(\'Are you sure you want to delete this news post?\')">delete</a></b>]'; ?>
	</td>
</tr>

<tr>
	<td width=15%></td>
	<td width=85%></td>
</tr>
</form>
</table></p>
