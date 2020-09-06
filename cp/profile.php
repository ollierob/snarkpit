<?php title("$t_cp Edit Profile",cp);

	include('func_parse.php');
	$uparray['profile'] = stripslashes(str_replace("<br>","\n",$uparray['profile']));

?>

Please leave fields blank if you are unsure or don't have anything to enter in it.
<p>

<?php if($msg) msg(htmlspecialchars(stripslashes(str_replace('+','',$msg))),'error','','','','');?>

<script language="Javascript">
	function checkform() {
		alertmsg = "";
		if(document.forms['editprofile'].elements['curpass'].value=="") { alertmsg = "Please re-enter your password"; }
		if(document.forms['editprofile'].elements['newpass1'].value != document.forms['editprofile'].elements['newpass2'].value) { alertmsg = "Your new passwords don't match"; }
		if(document.forms['editprofile'].elements['email'].value=="") { alertmsg = "You must specify an e-mail address"; }

		if(alertmsg != "") { alert(alertmsg); return false; }

	}
</script>

<form action="cp.php" method="post" name="editprofile" onsubmit="return checkform()">
<table width="99%" cellspacing=1 cellpadding=1 class="forumtext">
<input type="hidden" name="action" value="editprofile">

<tr>
	<td align=right><b>username:</b></td>
	<td><?=$userdata['username']?></td>
</tr>
<tr>
	<td align=right><b>password:</b></td>
	<td><input type=password name=curpass size=24 maxsize=24 class=textinput> <font color=red><b>*required</b></font></td>
</tr>
<tr>
	<td align=right><b>user level:</b></b></td><td>
	<?php echo $level = $userdata['user_level'].' (';
		if($level==4) echo 'überlord';
		if($level==3) echo 'webmonkee';
		if($level==2) echo 'moderator';
		if($level==1) echo 'regular';
	?>)</td>
</tr>

<tr><td height=10> </td></tr>
<tr><td colspan=2><?php @subtitle('change password'); ?></td></tr>
<tr><td colspan=2>Only fill this in if you want to change your password- remember to enter your current password above.</td></tr>
<tr>
	<td align=right><b>new password:</b></td>
	<td><input type=password name=newpass1 size=24 maxsize=24 class=textinput></td>
</tr>
<tr>
	<td align=right><b>re-enter:</b></td>
	<td><input type=password name=newpass2 size=24 maxsize=24 class=textinput></td>
</tr>

<tr><td height=10></tr>
<tr><td colspan=2><?php @subtitle('contact details'); ?></td></tr>
<tr><td colspan=2>You must enter a valid e-mail address if you are changing it, as your current e-mail address will not
	be changed until you follow the 'activation' link e-mailed to the new address specified. Also note that your
	e-mail address is only visible to other logged in users (assuming it's not hidden), so you will not be spammed
	as a result.
<tr>
	<td align=right valign=top><b>e-mail:</b></td>
	<td><input type="text" name="email" value="<?=$uparray['user_email']?>" size="32" class="textinput"> <font color=red><b>*required</b></font>
		<br><input type=checkbox id="hideemail"<?php if($uparray['hideemail']==1) echo 'CHECKED';?>> <label for="hideemail">hide e-mail address</label></td>
</tr>

<tr>
	<td align=right><b>steam:</b></td>
	<td><input type=text name="steam" value="<?=$uparray['steam']?>" size=32 maxlength=64 class=textinput> <a href="http://www.steampowered.com" target="_blank"><img src="images/steam.gif" align=texttop border=0></a>
	<span class="help">e-mail address you registered with Steam</span>
</tr>
<tr>
	<td align=right><b>msn:</b></td>
	<td><input type=text name=msn value="<?=$uparray['msnm']?>" size=32 maxlength=96 class=textinput> <a href="http://www.msn.com" target="_blank"><img src="images/forum_msn.gif" align=texttop border=0></a>
</tr>
<tr>
	<td align=right><b>aim:</b></td>
	<td><input type=text name=aim value="<?=$uparray['aim']?>" size=32 maxlength=96 class=textinput> <a href="http://www.aol.com" target="_blank"><img src="images/forum_aim.gif" align=texttop border=0></a>
</tr>
<tr>
	<td align=right><b>yim:</b></td>
	<td><input type=text name=yim value="<?=$uparray['yim']?>" size=32 maxlength=96 class=textinput> <a href="http://www.yahoo.com" target="_blank"><img src="images/forum_yim.gif" align=texttop border=0></a>
</tr>
<tr>
	<td align=right><b>icq:</b></td>
	<td><input type=text name=icq value="<?=$uparray['icq']?>" size=10 maxlength=10 class=textinput> <a href="http://www.icq.com" target="_blank"><img src="images/forum_icq.gif" align=texttop border=0></a>
</tr>

<tr><td height=10></tr>
<tr><td colspan=2><?php @subtitle('personal details'); ?></td></tr>
<tr><td align=right><b>website:</b></td><td><input type=text name=website value="<?=$uparray['website']?>" size=32 class=textinput> <span class="help">please enter a fully qualified URL beginning with http://</span></td></tr>
<tr><td align=right><b>country:</b></td><td><input type=text name=location value="<?=$uparray['location']?>" size=32 maxlength=32 class=textinput></td></tr>
<tr><td align=right><b>occupation:</b></td><td><input type=text name=occupation value="<?=$uparray['occupation']?>" size=32 maxlength=32 class=textinput></td></tr>
<tr>
	<td align=right valign=top><b>about yourself:</b><p><span class="help">BBCode enabled</span></td>
	<td><textarea name="profile" cols=48 rows=8><?=bbdecode(stripslashes($uparray['profile']))?></textarea>
	<br><b><a href="javascript:void(0)" onclick="popwin('popup.php?mode=upload&type=photo')" onmouseover="window.status='Upload (popup window)'; return true" onmouseout="window.status=''; return true">Click to upload a photo of yourself</a></b></td>
</tr>

<?php
	if(strlen($uparray['birthday'])==3) $uparray['birthday'] = "0".$uparray['birthday'];
	$bday = substr($uparray['birthday'],0,2);
	$bmonth = substr($uparray['birthday'],2,2);
	//if($bday && $bmonth) $disabled = ' disabled'; else $disabled = '';
?>
<tr>
	<td align=right><b>birthday:</b></td>
	<td><select name="bday"<?=$disabled?>><option value="" id="cat_white">Day:</option><?php for($i=1;$i<=31;$i++) echo '<option value="'.(($i<10)?'0':'').$i.'"'.(($i==$bday)?' selected':'').'>'.$i; ?></select>
	<select name="bmonth"<?=$disabled?>><option value="" id="cat_white">Month:</option><?php
		$months = array('','January','February','March','April','May','June','July','August','September','October','November','December');
		while(list($i,$val)=each($months)) { if($val) echo '<option value="'.(($i<10)?'0':'').$i.'"'.(($i==$bmonth)?' selected':'').'>'.$val; }
	?>
	</select>
	<span class="help">you can only set your birthday once (in order to prevent abuse!)</span>
	</td>
</tr>

<tr><td height=10></tr>
<tr><td colspan=2><?php @subtitle("forums"); ?></td></tr>
<tr>
	<td align=right valign=top><b>avatar text:</b></td>
	<td><input type="text" name="avatar" class="textinput" size=48 maxlength=32 value="<?=stripslashes($uparray[avatar_text])?>">
	<br><b><a href="javascript:void(0)" onclick="popwin('popup.php?mode=upload&type=avatar')" onmouseover="window.status='Upload (popup window)'; return true" onmouseout="window.status=''; return true">Click to upload a forum avatar</a></b></td>
</tr>
<tr>
	<td align=right valign=top><b>signature:</b><p><span class=help>max length <b>255</b> chars<br>BBCode enabled<br>images disabled</span></td>
	<td><textarea name="signature" cols=48 rows=4><?=htmlspecialchars(str_replace('<br>',"\n",bbdecode(stripslashes($uparray['user_sig']))))?></textarea>
	<br><input type="checkbox" name="addsig" id="addsig"<?php if($userdata['addsig']==1) echo ' CHECKED'; ?>> <label for="addsig">automatically add my signature to posts</label></td>
</tr>
<tr><td height=10></tr>
<tr><td width="20%"></td><td width="80%"><input type=submit name=submit value="update profile" class="submit3" size=32 maxlength=100></td></tr>
</table>
</form>
