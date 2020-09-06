<?php
	$title = $t_cp.' <a href="?mode=tutorials" class=white>Tutorials</a> »';
	$edittext = ''; $title = 'New Tutorial';

	include('lib/editors.php');

	if($_POST) {
		$edittext = stripslashes($_POST['tuttext']);
		$array['title'] = stripslashes($_POST['title']);
		$array['description'] = stripslashes($_POST['description']);
		$array['game'] = $_POST['game'];
		$array['type'] = $_POST['type'];
		if($mode=='edittut') {
		        $edit = $_POST['editid'];
		        $title = 'Edit tutorial';
		} else $edit = '';
	}

if($_GET['edit']) { 
	$edit = $_GET['edit'];
	$title = 'Edit Tutorial'; if(!$edittext) {
	if(!function_exists(bbencode)) include('func_parse.php');
	if($_GET['select']=='auth') {
		$sql = mysql_query("SELECT * FROM authenticate WHERE id = '$edit' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) error_die('Article waiting to be authed not found');
		if($array['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) error_die("This isn't your tutorial so you can't edit it");
		$edittext = bbdecode(($array['text'])); $edittext = str_replace("<br>","\n",$edittext);
		$hidden = '<input type="hidden" name="select" value="auth">';
		$array['type'] = $array['subtype']; $game = $array['game'];
	} else {
		$sql = mysql_query("SELECT * FROM articles WHERE id = '$edit' LIMIT 1");
		if(!$array = mysql_fetch_array($sql)) error_die('Article not found');
		if($array['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) error_die('This isn\'t your tutorial so you can\'t edit it');
		$edittext = bbdecode((mysql_result(mysql_query("SELECT text FROM articles_text WHERE id = '$edit' LIMIT 1"),0))); $edittext = str_replace("<br>","\n",$edittext);
		$game = $array['game'];
	}
} }

	if($array['game']) $game = $array['game']; else $game = $_GET['game'];
	if(!$game) { header('Location: cp.php?msg=Please+select+a+game+to+write+a+tutorial+for'); die; }

title("$t_cp $title",'cp'); 
if($action=='new') echo '<p class="forumtext">Your tutorial will not appear on the site until it has been checked over by an admin for details and spelling/grammar.<br>
	If you have a short and handy editing tip, submit it via <a href="?mode=feedback&select=quicktip"><b>this page</b></a> instead of using a tutorial.
	<br>If you want to write a tutorial but aren\'t sure if we\'ll acccept it, please contact a site admin and ask him if it will be appropriate.
	We want tutorials that offer all the information someone would want or need to know about a particular topic, and not just tidbits of information.</p>';
?>

<script language="javascript">
	function checkform() {
		alertmsg = "";
		if(tutform.title.value=="") { alertmsg = "Your tutorial needs a title"; }
		if(tutform.description.value=="") { alertmsg = "Your tutorial needs a quick description"; }
		if(tutform.tuttext.value=="") { alertmsg = "You need to write a tutorial..?"; }
		if(alertmsg) { alert(alertmsg); return false; }	
	}
</script>

<p>
<table width="100%" cellspacing=1 cellpadding=2 class="forumtext">
<form action="cp.php" method="post" onsubmit="return checkform()" name="tutform" enctype="multipart/form-data">
<?php
	if($edit) echo '<input type="hidden" name="action" value="edittut"><input type="hidden" name="editid" value="'.$edit.'">';
	else echo '<input type="hidden" name="action" value="newtut">'; 
	if($_GET['select']=='auth') echo '<input type="hidden" name="select" value="auth">';
?>
<tr><td width=15%></td><td width=85%></td></tr>

<tr>
	<td align=right><b>title:</b></td>
	<td><input type="text" name="title" value="<?=stripslashes($array['title'])?>" class=textinput size=32></td>
</tr>
<tr>
	<td align=right><b>description:</b></td>
	<td><input type="text" name="description" value="<?=stripslashes($array['description'])?>" class=textinput size=32></td>
</tr>
<tr>
	<td align=right><b>for game:</b></td>
	<td><?=mysql_result(mysql_query("SELECT name FROM games WHERE id = '$game' LIMIT 1"),0)?></td>
	<input type="hidden" name="game" value="<?=$game?>">
</tr>
<tr>
	<td align=right valign=top><b>relevant to:</b></td>
	<td><select name="type">
	<?php if(!include('lib/tutsections_'.$game.'.php')) error_die('<i>Could not load tut library...</i>'); $j=0; $lib_secabout = '';
		for($i=0;$i<$lib_seclength;$i++) {
			echo '<option value="'.$lib_sections[$i][0].'"';
			if($array && $array[type]==$lib_sections[$i][0]) echo ' SELECTED';
			echo '>'.$lib_sections[$i][1].'</option>'."\n";
			$lib_secabout.='<b><a href="editing.php?page=tutorials&type='.$lib_sections[$i][0].'" class=white target="_blank">'.$lib_sections[$i][1].'</a>:</b> '.$lib_sections[$i][2].'<br>';
		}
	?>
	</select><table width="100%" style="font-size:8pt"><tr>
	<td><?=$lib_secabout?></td></tr></table>
	</td>
</tr>
<tr>
	<td align=right><b>editor:</b></td>
	<td><select name="editor">
		<option value="">n/a
		<?php for($i=0;$i<$edlength;$i++) { echo '<option value="'.$editors[$i].'"'; if($array[editor]==$editors[$i]) echo ' SELECTED'; echo '>'.$editors[$i]; } ?>
	</select> <span class="help">if tutorial is relevant to any editor, just leave as 'n/a'</span></td>
</tr>

<tr><td height=10></td></tr>
<tr>
	<td align=right valign=top><b>text:</b></td>
	<td><textarea name="tuttext" rows=25 cols=80><?=($edittext)?></textarea>
	<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode','yes')">BBCode</a> and <a href="javascript:void(0)" onclick="popwin('popup.php?mode=smilies')">smilies</a> are enabled, HTML is disabled<br>
	<p><?php msg('Please use tutorial BBCode, or else we <b>WILL</b> return this to you to edit. This means use the [e] [pr] [pv] [title] tags in the right places- see <a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=bbcode\',\'yes\')">this page</a> for how to use them.','warning','','100%','Please read before submitting:','div'); ?>
</tr>

<tr>
	<td colspan=2><?php @subtitle('screenshots'); ?></td>
</tr>
<tr>
	<td colspan=2>You can upload up to 8 screenshots to use in your tutorial- they must be JPG format.
		If above 800 pixels in any dimension, they will be scaled down. 
		To use them in your tutorial, type <b>[image1]</b>, <b>[image2]</b> etc. in the text above where you want them.</td>
</tr>

<?php for($i=1;$i<9;$i++) { ?>
<tr>
	<td align=right><b>[image<?=$i?>]</b></td>
	<td><input type="file" name="tutimg<?=$i?>" class=textinput size=32>
	<?php if($edit) { if(file_exists('userimages/tutorials/tut'.$edit.'_'.$i.'.jpg') OR file_exists('userimages/auth/tut'.$edit.'_'.$i.'.jpg')) echo '(image already uploaded)'; } ?>
	</td>
</tr>
<? } 

	if($edit) echo '<tr><td></td><td>If you have already uploaded images you can leave the above fields blank, unless you want to replace them. Note that you can\'t preview uploaded images.</td></tr>'; 
?>

<tr>
	<td colspan=2><?php @subtitle('example map'); ?></td>
</tr>
<tr>
	<td colspan=2>There are two ways to attach an example level to your tutorial. You can either place a link to it in your tutorial and one of our admins will download, check and upload it here for you,
	or you can add it yourself as a new "example map" prefab- <a href="cp.php?mode=files&action=prefabs" target="_blank"><b>do this now in a new window</b></a> before submitting this tutorial- and it will be automatically
	added.</td>
</tr>

<?php if($edit) { ?>

<tr>
	<td></td>
	<td><input type="checkbox" name="nocommentpm"<?=(($array['nocommentpm']==1)?' checked':'')?> id="comment"><label for="comment"> Tick if you <b>don't</b> want to be sent PMs when someone makes a comment on this tutorial</label></td>
</tr>

<? } ?>

<tr><td height=10> </td></tr>
<tr>
	<td></td><td>
	<?php if($edit) { echo '<input type="hidden" name="action" value="edittut"><input type="hidden" name="edit" value="'.$edit.'">'; } ?>
	<input type="submit" name="submit" value="submit" class="submit3">
	<input type="submit" name="submit" value="preview" class="submit3" onclick="if(tutimg1.value || tutimg2.value || tutimg3.value || tutimg4.value || tutimg5.value || tutimg6.value || tutimg7.value || tutimg8.value) return confirm('If you continue, your images will not be uploaded!')">
	<input type="submit" name="submit" value="delete" class="submit3" onclick="return confirm('Are you sure you want to delete this tutorial?')">
	<?php if(($edit && $_GET['select']=='auth') || $action=='new' || $_POST['action']=='newtut') { ?><p><input type="submit" name="submit" value="save" class="submit3"> Click to finish this tutorial another time<? } ?>
	</p></td>
</tr>
</form>
</table>
