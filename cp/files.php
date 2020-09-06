<?php 	$title = ''; $hidden = '';

	if($edit) {
		if(!$array = mysql_fetch_array(mysql_query("SELECT * FROM files WHERE file_id = '$edit' LIMIT 1"))) header("Location: cp.php?msg=file+does+not+exist");
		if($array['author']!=$userdata['user_id'] && $userdata['user_level']<4) { header("Location: cp.php?msg=file+does+not+belong+to+you"); die; }
		$action = $array[type];
		include('func_parse.php');
		$array[description] = str_replace("<br>","\n",bbdecode(stripslashes($array[description])));
	}

	if($action=='prefabs') {
		if($edit) { $hidden = 'editfile'; $title = 'Edit Prefab'; } else { $hidden = 'newfile'; $title = 'New Prefab'; }
		$name = 'prefab'; $under = 'prefabs';
	}
	
	elseif($action=='models') {
		if($edit) { $hidden = 'editfile'; $title = 'Edit Model'; } else { $hidden = 'newfile'; $title = 'New Model'; }
		$name = 'model'; $under = 'models';
	}

	if(!$title OR !$hidden) { header('Location: cp.php?error=Invalid+file+category'); die; }
	title("$t_cp Files: $title",'');
	
	if($_GET['msg']) msg($_GET['msg'],'error','','','','');

?>

<script>
function checkform() { var alertmsg = ''; i = 0;
	<?php if(!$edit) echo 'if(fileform.file.value==\'\') alertmsg = \'You need to upload a file\';'; ?>

	if(fileform.text.value=='') alertmsg = 'You need to write a description for your file';
<?php
	$gsql = mysql_query("SELECT DISTINCT(f.game),g.id FROM games g, files_cats f WHERE g.id = f.game AND f.under = '$under'");
	while($garray = mysql_fetch_array($gsql)) echo '	if(fileform.game'.$garray['id'].'.value!=\'\') i++;'."\n";
?>
	
	if(i != 1) alertmsg = 'Your file needs to be for a single category';
	if(fileform.filename.value=='') alertmsg = 'Your file needs a name/title';

	if(alertmsg) { alert(alertmsg); return false; }
}
</script>

<table width="95%" cellspacing=2 cellpadding=1>
<form action="cp/fileactions.php" method="post" enctype="multipart/form-data" name="fileform" onsubmit="return checkform()">
<input type="hidden" name="action" value="<?=$hidden?>">
<input type="hidden" name="type" value="<?=$action?>">
<?php if($edit) echo '<input type="hidden" name="edit" value="'.$edit.'">'; ?>

<tr><td colspan=2><?php subtitle('file details','');?></td></tr>

<tr>
	<td align=right><b><?=$name?> name:</b></td>
	<td><input type="text" name="filename" value="<?=stripslashes($array[filename])?>" size=32 maxlength=32 class=textinput></td>
</tr>
<tr>
	<td align=right valign=top><b>game/category:</b></td>
	<td>
	<?php  	$gsql = mysql_query("SELECT DISTINCT(f.game),g.id,g.name FROM games g, files_cats f WHERE g.id = f.game AND f.under = '$under'");
		while($garray = mysql_fetch_array($gsql)) {
			echo '<select name="game'.$garray['id'].'" onchange="document.getElementById(\'desctext\').innerHTML = game'.$garray['id'].'.options[game'.$garray['id'].'.selectedIndex].label">';
			echo '<option value="" id="cat_white">'.$garray['name'].' categories:</option>';
			$csql = mysql_query("SELECT * FROM files_cats WHERE under = '$under' AND game = '$garray[id]' ORDER BY name");
			while($carray = mysql_fetch_array($csql)) { 
				echo '<option label="'.ucfirst($carray['description']).'" value="'.$carray['name'].'"'; if($array['subcat']==$carray['name'] && $array['game']==$garray['id']) echo ' SELECTED';
				echo'>'.$carray['name'].'</option>';
			}
			echo '</select>';
		} ?><div id="desctext" class="help">Select a file category from above.</div></td>
</tr>

<tr><td height=10></td></tr>
<tr>
	<td align=right valign=top><b>description:</b><font size=1></p>bbcode enabled<br>smilies disabled</td>
	<td><textarea name="text" rows=5 cols=64><?=$array[description]?></textarea>
	<br> <span class="help">A few lines describing your file will suffice, you don't need to write an essay!</span></td>
</tr>

<tr><td colspan=2><?php subtitle('upload files','');?></td></tr>

<tr>
	<td align=right valign=top><b>screenshot:</b></td>
	<td><input type="file" name="screenshot" class="textinput" size=32><font size=1><?php if($array && file_exists('files/'.$array['game'].'/images/'.$name.$edit.'.jpg')) echo ' <font color=red>screenshot already uploaded</font>'; ?>
	<br><span class="help">uploaded screenshots must be .JPG format and will be automatically resized to 120x90 pixels</span></td>
</tr>

<tr>
	<td align=right valign=top><b>file:</b></td>
	<td><input type="file" name="file" class="textinput" size=32>
	<br><span class="help">file must be a .ZIP archive containing a valid <?=$name?> file and a readme</span></td>
</tr>

<tr>
	<td width="15%"></td>
	<td width="85%"><input type="submit" name="submit" value="submit" class="submit3">
	<?php if($edit) echo '<input type="submit" name="submit" value="delete" class="submit3" onclick="return confirm(\'Are you sure you want to delete this file?\')">';?>
</form>
</table>
