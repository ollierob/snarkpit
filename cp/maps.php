<?php
	$action = $_GET['action']; $edit = $_GET['edit']; $returnto = $_GET['returnto'];

	if($action!='new' AND !$edit) { header('Location: ../cp.php'); die; }
	if($action=='new' && $edit) $action='';

	if($action=='new') title($t_cp.' New Map','cp');

if($edit) {

	$sql = mysql_query("SELECT * FROM maps WHERE map_id = '$edit' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) { header('Location:cp.php?error=Map+not+found'); die; }
	if($userdata['user_id']!=$array['user_id'] && $userdata['user_level']<3) { header('Location:cp.php?error=Not+your+map+so+you+can\'t+edit+it'); die; }
	$mapname = stripslashes($array['name']);
	title("$t_cp Modify Map: &quot;$mapname&quot;",cp);

	include('func_parse.php');
	$array['map_about'] = bbdecode($array['map_about']);
	$array['map_about'] = str_replace("<br>","\n",$array['map_about']); $array['map_about'] = str_replace("<BR>","\n",$array[map_about]);
	$array['map_about'] = stripslashes($array['map_about']);

} ?>

<script language="javascript">
function checkform() {
	i = 0; submitOK = "True"; selgame = ""; alertmsg = "";
	<?php $sql = mysql_query("SELECT id FROM games"); while($garray = mysql_fetch_array($sql)) { echo "\n".'	if(document.forms[\'mapform\'].elements[\'game'.$garray[id].'\'].value != "") { i++; document.forms[\'mapform\'].elements[\'selgame\'].value = "'.$garray[id].'"; } '; } ?>
	if(i != 1) alertmsg = "Your map can only be for one mod";

	if(!document.forms['mapform'].elements['mapname'].value) alertmsg = 'Your map needs a name\n'; 
	if(document.forms['mapform'].elements['status'].value >= -1 && document.forms['mapform'].elements['status'].value < 101 ) { } else { alertmsg += 'Status must be between -1 and 100%\n'; }
	if(!document.forms['mapform'].elements['text'].value) alertmsg += 'You need to write something about your map\n';
	if(alertmsg) { alert(alertmsg); return false; }
	
	urllength = document.forms['mapform'].elements['url'].value.length;
	urlext = document.forms['mapform'].elements['url'].value.substring(urllength-3,999);
	if(urlext=='bsp'||urlext=='BSP') return confirm('You should distribute your map in a .ZIP or .RAR file; are you sure you want to set the download URL to be the .BSP?');

	if(document.forms['mapform'].elements['screen1'].value || document.forms['mapform'].elements['screen2'].value || document.forms['mapform'].elements['screen3'].value) alert('Please note uploading screenshots will take some time so be patient. DO NOT press submit again!');
	if(document.forms['mapform'].elements['url'].value=='none' || document.forms['mapform'].elements['url'].value=='n/a' || document.forms['mapform'].elements['url'].value=='na') document.forms['mapform'].elements['url'].value = '';
}

function enable_mirror(val) {
	if(val==100) {
	        document.forms['mapform'].elements['spmirror'].disabled = false;
	       	document.getElementById('spmirrortext').style.textDecoration = '';
	} else {
	       	document.forms['mapform'].elements['spmirror'].disabled = true;
	        document.getElementById('spmirrortext').style.textDecoration = 'line-through';
	}
}
</script>

<?php 
	if($edit) echo '<b><a href="maps.php?map='.$edit.'">Click here</a></b> to view this map\'s details</p>'; 
	msg('<li>Do not submit "test" maps! We can and will delete these.<li>Do not submit maps which are not yours for "caretaking" purposes','warning','','','submission rules','div');
?>

<p>
<table width="99%" style="font-size:9pt">

<tr><td colspan=3><?php @subtitle('map details',''); ?></td></tr>

<form action="cp.php" method="post" name="mapform" enctype="multipart/form-data">
<?php 
	if($returnto=='maps') echo '<input type="hidden" name="redirect" value="maps.php?map='.$edit.'">';
	elseif($returnto=='users') echo '<input type="hidden" name="redirect" value="users.php?name='.$userdata['username'].'">'; 
?>
<tr><td width="15%"></td><td width="40%"></td><td width="45%"></td></tr>

<tr>
	<td align=right><b>map name:</b></td>
	<td colspan=2><input type="text" name="mapname" value="<?=$array['name']?>" class=textinput size=32 maxlength=32></td>
</tr>
<tr>
	<td align=right><b>game/mod:</b></td>
	<td colspan=2>
	<?php 	$sql = mysql_query("SELECT * FROM games ORDER BY name");
		while($garray = mysql_fetch_array($sql)) { echo "\n";
			echo '<select name="game'.$garray['id'].'"><option value="" id="cat_white">Select a'.(($garray['name']{0}=='A'||$garray['name']{0}=='E'||$garray['name']{0}=='I'||$garray['name']{0}=='O'||$garray['name']{0}=='U')?'n':'').' '.$garray['name'].' mod:</option>';
			$msql = mysql_query("SELECT name,fullname,game,altmodes FROM mods WHERE game = '$garray[id]' ORDER BY sortindex DESC,fullname"); 
			while($a = mysql_fetch_array($msql)) {
				echo "\n".'<option value="'.$a['name'].'"'; if($array['mod']==$a['name'] && $array['game']==$a['game']) { $selmodname = $a['fullname']; echo ' selected'; }
				echo '>'.$a['fullname']; if($a['altmodes']) echo '*'; 
			} echo '</select>';
		}
	?></td>
</tr>
<tr>	<td height=20></td><td colspan=2 valign=top>Mods with an asterisk * after mean they have different gameplay modes. 
	<?php if(!$edit) echo 'You can specify these by editing your map later on.'; else {
		if(@include('lib/gameplay_'.$array['game'].$array['mod'].'.php')) { ?>

</tr>
<tr>
	<td></td><td colspan=2>Your map is currently listed for <?=$selmodname?>, please tick the gameplay modes it supports:<br>
	<?php	while(list($key,$val) = each($gamemodes)) {
			echo '<input type="checkbox" name="gameplay_'.$key.'"';
			if(substr_count($array['gameplay'],'-'.$key.'-')) echo ' CHECKED';
			echo '>'.$val.' ';
		}
	} } ?>
</tr>
<tr>
	<td align=right valign=top style="padding-top:5px"><b>status:</b></td>
	<td><input type="text" id="status" name="status" value="<?=$array['status']?>" size=3 maxlength=3 class=textinput onfocus="enable_mirror(this.value)" onkeyup="enable_mirror(this.value)">%
	<span class="help">how complete the map is</span>
	<br><input type="checkbox" name="abandoned" onclick="if(this.checked==true) document.getElementById('status').value=-1"<?=(($array['status']=='-1')?' checked':'')?>> map has been abandoned
	</td>
	<td align=middle>
		<span style="height:18px;border:1px solid <?=$colors['msg_warning_border']?>;padding:2px"><a href="cp.php?mode=batch" onclick="self.location='login.php'" class="white"><b><img src="images/gfx_bat.gif" align=top border=0> Create a compile batch file</b></a></div>
	</td>
</tr>
<tr>
	<td align=right><b>release date:</b></td>
	<td colspan=2><input type="text" name="cdate" value="<?=$array['cdate']?>" size=32 maxlength=32 class=textinput> <span class="help">if map is incomplete, when you aim to release it- e.g. <b>12th October 2004</b></span></td>
</tr>
<tr>
	<td align=right><b>size:</b></td>
	<td colspan=2><select name="size"><?php
		$sizes = array('tiny','small','medium','large','huge');
		for($i=0;$i<5;$i++) { echo '<option value="'.$i.'"'; if($array['size']==$i || ($i==2 && !$array)) echo ' SELECTED'; echo '>'.$sizes[$i]; }
	?></select></td>
</tr>
<tr>
	<td align=right valign=top style="padding-top:5px"><b>download URL:</b></td>
	<td colspan=2><input type="text" name="url" value="<?=$array['map_url']?>" size=64 maxlength=96 class=textinput>
	<br><span class="help">New to mapping, or confused? <a href="javascript:void(0)" onclick="popwin('popup.php?mode=mapsubmitting','yes')">Click here</a> for map submission/distribution guidelines.</span></td>
</tr>
<tr>
	<td align=right valign=top style="padding-top:5px"><b>mirror 1:</b></td>
	<td colspan=2><input type="text" name="mirror1" value="<?=$array['mirror1']?>" size=64 maxlength=96 class=textinput>
	<br><input type="checkbox" name="spmirror"<?=(($array['status']==100 && substr($array['mirror1'],0,5)!='maps/')?'':' disabled')?>><span id="spmirrortext" style="<?=(($array['status']==100 && substr($array['mirror1'],0,5)!='maps/')?'':'text-decoration:line-through')?>"> tick to request a mirror here on the SnarkPit</span> (finished maps only)
	</td>
</tr>

<?php if($edit && $array['map_url']) echo '<tr><td></td><td colspan=2><input type="checkbox" name="downloadreset"> tick to reset download counter to 0<br><div class="help">If you would like to count how many people have downloaded your map, please link to
	<br><b>http://www.snarkpit.net/maps.php?download='.$edit.'</b> instead of the URL above</td></tr>';
?>

<tr><td height=10></td></tr>
<tr>
	<td align=right valign=top><b>related link:</b></td>
	<td colspan=2><input type="text" name="related" value="<?=$array['related']?>" size=64 maxlength=96 class=textinput>
	<br><span class="help">A webpage with some more info about the map, if you have one. If, for example, your map is part
	of a mod please link to the mod website here instead of in the <i>download URL</i> field above.</span></td>
</tr>
<tr>
	<td align=right valign=top><b>about:</b></td>
	<td colspan=2><textarea name="text" rows=8 cols=64><?=$array['map_about']?></textarea>
	<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode')">BBCode</a> is enabled; HTML, smilies and images are disabled
	</td>
</tr>

<tr>
	<td></td>
	<td colspan=2><input type="checkbox" name="favmap"<?php $favmap = mysql_result(mysql_query("SELECT favmap FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1"),0); if($edit && $edit==$favmap) echo ' CHECKED'; ?> id="favmap"> <label for="favmap">make this my 'favourite' map (more prominent in your profile)</label>
<tr>
	<td></td><td colspan=2>
	<div class="help"><li>If your map is now complete or you've added some new screenshots, why not
	<a href="cp.php?mode=news&action=newfp" target="_blank"><b>post a news item on the front page</b></a> about it?
	<li>There's no need to submit pages and pages of information, and PLEASE check your spelling.
	<li>It's still up to you to generate interest in your map, so don't just expect hundreds of downloads from
	posting it here. The best way to get your levels played is with lots of publicity, and by helping other
	mappers out too.
	</div><p>
	<input type="submit" name="submit" value="submit" class="submit3" onclick="return checkform()" onmouseover="window.status='This submits everything on the page, including any screenshot data below';return false;" onmouseout="window.status='';return false;">
	<input type="submit" name="submit" value="delete" class="submit3" onclick="javascript:return confirm('Are you sure you want to delete this map?')">
</tr>
<tr><td height=10></td></tr>

<tr><td colspan=3><?php @subtitle('screenshots',''); ?></td></tr>
<tr><td colspan=3>You can upload up to 3 .JPG screenshots of your map to this website- they will automatically be
	downsized to 800x600 pixels if they're too big, and have thumbnails generated for them.
	If you upload some below, they'll 'override' any external images specified.
	Fully compiled in-game shots only please, we don't care for what your map looks like in the editor.</td>
</tr>
<tr><td height=10> </td></tr>

<tr><td colspan=2><font color="<?=$colors['info']?>" size=2><b>-EITHER- upload:</b></font></td></tr>
<tr>
	<td align=right><b>screenshot 1:</b></td>
	<td><input type="file" name="screen1" class=textinput></td>
	<td rowspan=3><table width="100%" cellpadding=1 cellspacing=0 style="font-size:8pt"><tr>

	<?php	if($edit) {
		$curloc = 'maps/'.$array[game].'/images/'.$edit.'_';
		if(file_exists($curloc.'1_thumb.jpg')) 
			echo '<td width=33% align=center><b>scr1:</b><br><img src="'.$curloc.'1_thumb.jpg"><br>(<a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=deletescreenshot&map='.$edit.'&scr=1\')">delete</a>)</td>'."\n";
		if(file_exists($curloc.'2_thumb.jpg')) 
			echo '<td width=33% align=center><b>scr2:</b><br><img src="'.$curloc.'2_thumb.jpg"><br>(<a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=deletescreenshot&map='.$edit.'&scr=2\')">delete</a>)</td>'."\n";
		if(file_exists($curloc.'3_thumb.jpg')) 
			echo '<td width=33% align=center><b>scr3:</b><br><img src="'.$curloc.'3_thumb.jpg"><br>(<a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=deletescreenshot&map='.$edit.'&scr=3\')">delete</a>)</td>'."\n";
		}
	?></tr></table>
	</td>
</tr>
<tr>
	<td align=right><b>screenshot 2:</b></td>
	<td><input type="file" name="screen2" class=textinput></td>
</tr>
<tr>
	<td align=right><b>screenshot 3:</b></td>
	<td><input type="file" name="screen3" class=textinput></td>
</tr>

<tr><td colspan=2><font color="<?=$colors['info']?>" size=2><b>-OR- enter image URLs:</b></font></td></tr>
<?php for($i=1;$i<6;$i++) { ?>
<tr>
	<td align=right><b>screenshot <?=$i?>:</b></td>
	<td colspan=3><input type="text" name="extscreen<?=$i?>" class=textinput size=48 value="<?=$array['scr'.$i]?>"></td>
</tr>
<?php } ?>
<tr><td></td><td class="help" colspan=2>Please try to make screenshot size 800x600 pixels so everyone can see them!</td></tr>

<tr><td height=10></tr>
<tr>
	<td></td><td>
	<?php if($edit) { echo '<input type="hidden" name="action" value="editmap"><input type="hidden" name="edit" value="'.$edit.'">'; } else echo '<input type="hidden" name="action" value="addmap">'; ?>
	<input type="hidden" name="selgame" value="">
	<input type="submit" name="submit" value="submit" class="submit3" onmouseover="window.status='This submits everything on the page';return false;" onmouseout="window.status='';return false;" onclick="return checkform();">
</tr>
</form>
</table>

<?php footer(); ?>
