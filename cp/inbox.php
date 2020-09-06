<?php

if($delete = $_GET['delete']) {
	$deleteuser = mysql_result(mysql_query("SELECT to_id FROM messages WHERE msg_id = '$delete' LIMIT 1"),0);
	if($deleteuser!=$userdata['user_id'] && $userdata['user_level']<4) { header('Location: cp.php?mode=inbox'); die; }
	@mysql_query("DELETE FROM messages WHERE msg_id = '$delete' LIMIT 1");
	header('Location: cp.php?mode=inbox&msg=Message+deleted'); die;
}

	$pmblock = mysql_result(mysql_query("SELECT pm_block FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1"),0);
	$msg = htmlspecialchars($_GET['msg']);
	$maxpms = 50;

if($pmblock!=',') {
	$countblocks = substr_count($pmblock,','); $c = 0; $d = 0;
	while($c<$countblocks) { $x=''; $y='';
		list($x,$y) = split(',',$pmblock);
		$pmblock = str_replace("$x,$y,","",$pmblock);
		$c++; $c++;
		if($x) { if($sql = mysql_query("DELETE FROM messages WHERE to_id = '$userdata[user_id]' AND from_id = '$x'")) $d++; }
		if($y) { if($sql = mysql_query("DELETE FROM messages WHERE to_id = '$userdata[user_id]' AND from_id = '$y'")) $d++; }
	}
	if($d && !$msg) $msg = 'Some e-mails from blocked senders have just been deleted.';
	$countpm = mysql_result(mysql_query("SELECT COUNT(*) FROM messages WHERE to_id = '$userdata[user_id]'"),0);
} //block people

title($t_cp.' Private Messages: Inbox','cp'); tracker('Checking private messages','');

if($countpm>1) {
?>
<SCRIPT LANGUAGE="JavaScript">
var checkflag = "false";
function check(field) {
if (checkflag == "false") {
  for (i = 0; i < field.length; i++) {
  field[i].checked = true;}
  checkflag = "true";
  return "Uncheck all"; }
else {
  for (i = 0; i < field.length; i++) {
  field[i].checked = false; }
  checkflag = "false";
  return "Check all"; }
}

</script>
<?php $selectbox = '<input type=checkbox value="Check all" onClick="this.value=check(this.form)">'; } else $selectbox = '<img src="images/null.gif" width=20 height=1>'; ?>

Your inbox can hold up to <?=$maxpms?> messages- you currently have <?=$countpm?>. If this limit is exceeded, older messages will be deleted.</p>

<form action="cp/pmactions.php" method="post">
<table width="100%" cellspacing=0 cellpadding=2 style='font-size:8pt'>

<tr>
	<td colspan=4>
	<table cellpadding=0 cellspacing=0 style="font-size:8pt" width="100%"><tr>
		<td width="20%"><a href="cp.php?mode=compose"><img src="themes/<?=$theme?>/button_pm.gif" border=0 alt="compose" title="Send a PM to someone"></a></td>
		<td width="60%" align=right>
		<input type=submit name=delete value=delete class=submit3>
		<input type=submit name=block value=block class=submit3>
		<input type=submit name=unread value="mark unread" class=submit3> 
		<b>selected messages/senders&nbsp;</b></td></tr>
	</table>

	<?php if($msg) msg('<img src="images/gfx_pm.gif" align=absmiddle> '.$msg,'info','','','',''); ?></p>
</td></tr>

<tr style="font-weight:bold">
	<td width="1%"><?=$selectbox?></td>
	<td width="45%">subject</td>
	<td width="30%">from</td>
	<td width="24%">received</td>
</tr>
<tr><td colspan=5 height=1 bgcolor="<?=$colors['lightbg']?>"> </td></tr>

<?php $c=0;
$sql = mysql_query("SELECT * FROM messages WHERE to_id = '$userdata[user_id]' ORDER BY conversation DESC, msg_id DESC");
while($array = mysql_fetch_array($sql)) { if($c<$maxpms) { if(!$array['subject']) $array['subject'] = '<i>no subject</i>'; 
	if($result = @mysql_result(mysql_query("SELECT user_id FROM sessions WHERE user_id = '$array[from_id]' LIMIT 1"),0)) $useronline = ' - user <font color="'.$colors['yes'].'">online</font>'; else $useronline = ' - user offline';
	if($array['from_id']==-1) $useronline = '';
	if($array['conversation'] && $array['conversation']==$curconversation) $spaces .= '&nbsp;&nbsp;&nbsp;';
		else { $spaces = ''; $curconversation = $array['conversation']; }
?>
	<tr bgcolor="<?=$colors['darkbg']?>">
		<td><input type="checkbox" name="list<?=$c?>" value="<?=$array['msg_id']?>"></td>
		<td><?php if($array['status']==1) { echo '<b>'; $i='<img src="images/gfx_pm.gif" align=absbottom border=0> '; } else $i=''; ?>
			<?php if($array['from_id']>0) { ?><table align=right cellspacing=0 cellpadding=0 style="font-size:8pt"><tr><td>[<a href="cp.php?mode=compose&replyto=<?=$array['msg_id']?>" class=white>reply</a>, <a href="cp.php?mode=compose&foward=<?=$array['msg_id']?>" class=white>foward</a>]&nbsp;</td></tr></table><? } ?>
			<a href="javascript:void(0)" onClick="popwin('popup.php?mode=viewpm&msg=<?=$array['msg_id']?>','yes'); return false;"<?=(($array['status']==1)?'':' style="font-weight:normal"')?>><?=$i.$spaces.stripslashes($array['subject'])?></a></b>
			</td>
		<td><nobr><?php echo userdetails($array['from_id'],'','return','').$useronline?></nobr></td>
		<td><nobr><?=agotime($array['time'],'')?></nobr></td>
	</tr>

<?php 	} else { //delete extra than 50
		@mysql_query("DELETE FROM messages WHERE msg_id = '$array[msg_id]' AND status = 1 LIMIT 1");
		$deletedmessage=1;
	} $c++;
} if($deletedmessage) echo '<tr><td></td><td colspan=3><b><font color="'.$colors['no'].'">Your inbox is full so some messages that you have already read have been deleted.</font></b></td></tr>';
	
	if(!$countpm) echo '<tr><td></td><td>No PMs</td></tr>';
	else echo '<tr><td colspan=5 bgcolor="'.$colors['lightbg'].'" height=1> </td></tr>';
?>

</form>
</table>
<?php
	if($countpm) echo '<span style="float:right; height:16px" class="help"><a href="javascript:void(0)" onclick="popwin(\'popups/pmdump.php\',\'yes\')">Download all messages in inbox</a>&nbsp;</span>';

	if($uparray['pm_block'] && $uparray['pm_block']!=',') {
		$pmblock = $uparray['pm_block']; echo '</p>Ignore list: ';
		echo "\n".'<table><form action="cp/pmactions.php" method="post"><tr><td><select name="unblocklist[]" multiple="multiple">'; $c='';
		while($c<$countblocks) {
			$x=''; $y='';
			list($x,$y) = split(',',$pmblock);
			$pmblock = str_replace("$x,$y,","",$pmblock);
			$c=$c+2;
			if($x) echo '<option value="'.$x.'">'.userdetails($x,'','return','');
			if($y) echo '<option value="'.$y.'">'.userdetails($y,'','return','');
		}
		echo '</select></td><td width="100%"><input type="submit" name="unblock" value="Unblock" class=submit3><br>select multiple users<br>by holding CTRL or SHIFT</td></tr></form></table>';
	}
?>

</p>&nbsp;</p>

<?php subtitle('Sent Messages',''); ?>
<p>
<table width="100%" cellspacing=0 cellpadding=2 style='font-size:11px'>
<tr style='font-weight:bold'>
	<td width="1%"><img src="images/null.gif" width=20 height=1></td>
	<td width="45%">subject</td>
	<td width="30%">to</td>
	<td width="24%">sent</td>
</tr>
<tr>
	<td colspan=5 bgcolor="<?=$colors['lightbg']?>" height=1> </td>
</tr>
<?php $s=0; if($_GET['show']=='all') $lim = ''; else $lim = 'LIMIT 50';
$sql = mysql_query("SELECT * FROM messages WHERE from_id = '$userdata[user_id]' ORDER BY msg_id DESC $lim");
while($array = mysql_fetch_array($sql)) { if(!$array['subject']) $array['subject'] = '<i>no subject</i>'; ?>

	<tr bgcolor="<?=$colors['darkbg']?>">
		<td colspan=2 height=22><?php 
			if($array['status']==1) echo '<b><font color="'.$colors['no'].'">unread:</b></font>';
			if($array['status']==0) echo '<b><font color="'.$colors['yes'].'">read:</b></font>';
			?> <b><a href="javascript:void(0)" onClick="popwin('popup.php?mode=viewpm&msg=<?=$array[msg_id]?>','yes'); return false;"><?=stripslashes($array['subject'])?></a></b></td>
		<td><?php userdetails($array['to_id'],'','',''); ?></td>
		<td><?=agotime($array['time'],'')?></td>

<?php $s++; } 

	if(!$s) echo '<tr><td></td><td colspan=2>No sent messages are still stored</td></tr>'; else {
		if($s==50 && $lim) echo '<tr bgcolor="'.$colors['warningbg'].'"><td></td><td colspan=3>More messages are still stored, <b><a href="cp.php?mode=inbox&show=all">click here to view them</a></b></td></tr>';
		echo '<tr><td colspan=5 bgcolor="'.$colors['lightbg'].'" height=1> </td></tr>'; 
	} ?>

</table></p>
