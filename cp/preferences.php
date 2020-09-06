<?php
	title($t_cp.' Edit Preferences','cp');
	if(isset($_REQUEST['msg'])) echo '<font color=red><b>'.stripslashes(str_replace('+','',$_REQUEST['msg'])).'</b></font></p>'; 
	list($hideemail,$showrating) = split(',',$uparray['hidestuff']);
	$sel = false;
?>

<script>
function checkform() {
	var alertmsg = '';
	if(document.forms['prefs'].elements['ppp'].value>100 || document.forms['prefs'].elements['ppp'].value<10) alertmsg = 'Topic posts/page must be between 10 and 100';

	if(alertmsg) { alert(alertmsg); return false; } else return true;
}
</script>

<table width="100%">
<form action="cp.php" method="post" name="prefs" onsubmit="return checkform()">
<input type="hidden" name="action" value="editprefs">

<tr><td colspan=2><?php @subtitle('user options'); ?></td></tr>

<tr>
	<td align=right><b>hide e-mail:</b></td>
	<td><input type="checkbox" name="hideemail"<?php if($hideemail==1) echo ' CHECKED'; ?>>
	<span class="help">your e-mail address is only visible to other logged in users</span></td>
</tr>
<tr>
	<td align=right><b>time display:</b></td>
	<td><select name="timezone">
		<option value="ago"<?php if($userdata['timezone']=='ago') { echo ' selected'; $sel = true; } ?>>Days/hours/minutes/seconds ago
		<?php for($i=-12;$i<=12;$i++) echo '<option value="'.$i.'"'.(($i==$userdata['timezone'] && !$sel)?' selected':'').'>'.(($i>0)?'+':'').$i.' GMT'; ?>
		</select>
</tr>

<tr><td height=10> </td></tr>
<tr><td colspan=2><?php @subtitle('site options'); ?></td></tr>

<tr>
	<td align=right><b>show rating:</b></td>
	<td><select name="showrating">
		<option value="0">rating is visible
		<option value="1"<?php if($showrating==1) echo ' SELECTED';?>>rating is hidden
		<option value="2"<?php if($showrating==2) echo ' SELECTED';?>>can't be rated
	</select></td>
</tr>
<tr>
	<td align=right><b>hide help:</b></td>
	<td><input type="checkbox" name="hidehelp"<?php if($userdata['hidehelp']==1) echo ' CHECKED'; ?>> <span class="help">hide help icon in the top right of this page</span></td>
</tr>
<tr>
	<td align=right><b>theme:</b></td>
	<td><select name="theme">
		<option value="standard"<?php if($userdata['theme']=='standard') echo ' SELECTED';?>>standard
		<option value="sidebar"<?php if($userdata['theme']=='sidebar') echo ' SELECTED';?>>standard (condensed topic)
		<option value="hires"<?php if($userdata['theme']=='hires') echo ' SELECTED';?>>standard (hi-res version)
		<option value="basic"<?php if($userdata['theme']=='basic') echo ' SELECTED';?>>basic
		<option value="condensate"<?=(($userdata['theme']=='condensate')?' SELECTED':'')?>>condensate
		<option value="classic"<?php if($userdata['theme']=='classic') echo ' SELECTED';?>>classic*
		<option value="xray"<?php if($userdata['theme']=='xray') echo ' SELECTED'; ?>>xray*
		<option value="invert"<?php if($userdata['theme']=='invert') echo ' SELECTED'; ?>>invert*
	</select> <span class="help">* may not work correctly or are still being worked on</span></font></td>
</tr>
<tr>
	<td align=right><b>menu limit:</b></td>
	<td><input type="text" name="fplim" value="<?=$userdata['fplim']?>" size=3 maxlength=2 class="textinput">
	<span class="help">new tutorial/map list limit on front page</span></td>
</tr>

<tr>
	<td align=right><b>javascript off:</b></td>
	<td><input type="checkbox" name="javaoff"<?php if(isset($userdata['javaoff']) && $userdata['javaoff']==1) echo ' CHECKED';?>> <span class="help">tick to disable as much javascript as possible- menus, forum posting, etc.</span>
</tr>

<tr><td height=10></td></tr>
<tr><td colspan=2><?php @subtitle('forum options'); ?></td></tr>
<tr>
	<td align=right><b>topic posts/page:</b></td>
	<td><input type="text" size=3 maxlength=3 name="ppp" value="<?=$userdata['user_ppp']?>" class="textinput"> <span class="help">max 100, min 10</span></td>
</tr>

<tr><td height=10></td></tr>
<tr><td width="20%"></td><td width="80%"><input type="submit" name="submit" value="save preferences" class=submit3>
</table>
