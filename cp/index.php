<?php
	title('<a href="cp.php" class=white>Control Panel</a>','cp');
	if(isset($_GET['msg'])) msg(htmlspecialchars(stripslashes(str_replace('+','',$_GET['msg']))),'info','','','','');
	if(isset($_GET['error'])) msg(htmlspecialchars(stripslashes(str_replace('+','',$_GET['error']))),'error','','','','');
?>
</p>

<table width="100%" bgcolor="<?=$colours['dgray']?>" background="themes/<?=$theme?>/bg_.gif" cellspacing=0 cellpadding=2>
	<tr><td valign=top width="10%" align=right>You have:</td>
	<td width="90%"><?php calcsnarkpoints($userdata['user_id'],'print'); ?>
</td></tr>
<tr><td></td><td>
<table width="100%" cellpadding=2 cellspacing=1 style="font-size:8pt; font-weight:bold"><tr>
	<td width="50%" valign=top>What you've been rated:
	<?php

	$rate1x = 0; $rate2x = 0; $rate3x = 0; $rate4x = 0; $rate5x = 0;
	$sum = 0; $numrate = 0; $r = 0; $countr = 0;
	$sql = mysql_query("SELECT * FROM users_rating WHERE to_id = '$userdata[user_id]'");
	while($rarray = mysql_fetch_array($sql)) {
		$rateme = 'rate'.$rarray['rating'].'x'; $$rateme++; $sum = $sum+$rarray['rating']; $numrate++;
		$r++; $countr++; 
	}

	if($r) { echo '<table width=220 height=16 cellspacing=1><tr>';
		echo '<td>-</td><td bgcolor=red width="'.floor($rate1x*200/$countr).'"><td bgcolor=orange width='.floor($rate2x*200/$countr).'><td bgcolor=yellow width='.floor($rate3x*200/$countr).'><td bgcolor="#CCFF99" width='.floor($rate4x*200/$countr).'><td bgcolor=limegreen width='.floor($rate5x*200/$countr).'>';
		echo '<td>+</td></tr><tr><td align=center colspan=7>^</td></tr></table>'; }
		else echo 'not yet rated';
	?>
	</td><td width=50% valign=top>What you've rated others:
<?php
	$rate1y = 0; $rate2y = 0; $rate3y = 0; $rate4y = 0; $rate5y = 0;
	$sql = mysql_query("SELECT * FROM users_rating WHERE from_id = '$userdata[user_id]'"); $sum=''; $numrate=''; $r=''; $countr='';
	while($rarray = mysql_fetch_array($sql)) {
		$rateme = 'rate'.$rarray['rating'].'y'; $$rateme++; $sum = $sum+$rarray['rating']; $numrate++;
		$r++; $countr++; 
	}

	if($r) { echo '<table width=220 height=16 cellspacing=1><tr>';
		echo '<td>-</td><td bgcolor=red width="'.floor($rate1y*200/$countr).'"><td bgcolor=orange width='.floor($rate2y*200/$countr).'><td bgcolor=yellow width='.floor($rate3y*200/$countr).'><td bgcolor="#CCFF99" width='.floor($rate4y*200/$countr).'><td bgcolor=limegreen width='.floor($rate5y*200/$countr).'>';
		echo '<td>+</td></tr><tr><td align=center colspan=7>^</td></tr></table>';
	} else echo 'not yet rated anyone';

?>

	</td>
</tr></table>
</td></tr>
</table>
</p>

<fieldset>
<legend>news</legend>
<table width="95%" align=center cellspacing=3 cellpadding=0 style="font-size:10pt">
<tr>
	<td align=right width="15%"><b>front page:</b></td>
	<td width="85%"><a href="?mode=news&action=newfp" class=white>+click here to add a news item to the front page and your profile</a></td>
</tr>
<tr>
	<td align=right><b>your profile:</b></td>
	<td><a href="?mode=news&action=newpr" class=white>+click here to add a news item to just your profile</a></td>
</tr>
<tr>
	<td align=right valign=top><b>edit news:</b></td>
	<td><?php $sql = mysql_query("SELECT id,subject FROM news WHERE user_id = '$userdata[user_id]' AND plan != 0"); $c='';
		while($array = mysql_fetch_array($sql)) {
			if(!$c) echo '<form action="redir.php" method="post" name="newsform" onsubmit="if(!document.forms[\'newsform\'].elements[\'editnews\'].value) return false"><select name="editnews"><option value="" id="cat_white">News:';
			echo '<option value="'.$array['id'].'">'.stripslashes($array['subject'])."</option>\n";
		$c++; } if($c) echo '</select><input name="submiteditnews" type="submit" value="edit" class="submit2">'; else echo '<font color="'.$colors['medtext'].'">no news</font>';
	?></td>
</tr>
</form>

<?php $sql = mysql_query("SELECT * FROM authenticate WHERE user_id = '$userdata[user_id]' AND type='news'");
	if($numauth = mysql_num_rows($sql)) { ?>
<tr>
	<td align=right valign=top><b>submitted:</b></td>
	<td valign=top>
	<?php while($array = mysql_fetch_array($sql)) {
		echo '<a href="?mode=news&edit='.$array['id'].'&select=auth"><b>'.stripslashes($array['title']).'</b></a> :: ';
		if($array['status']=='0') echo '<font color="'.$colors['lighttext'].'">has not been checked</font>';
		elseif($array['status']=='1') echo '<font color="'.$colors['maybe'].'"><b>please check your spelling/punctuation or text formatting, and resubmit</b></font>';
		elseif($array['status']=='2') echo '<font color="'.$colors['maybe'].'"><b>screenshots are too large or text is too long</b></font>';
		elseif($array['status']=='3') echo '<font color="'.$colors['maybe'].'"><b>news is probably inappropriate for the front page...</b></font>';
		elseif($array['status']=='-1') echo '<font color="'.$colors['no'].'"><b>rejected</b></font>';
	echo "<br>\n"; } ?></td>
</tr>
<?php } ?>
</table>
</fieldset>

<fieldset>
<legend>maps - <a href="?mode=maps&action=new">add new map</a></legend>
<div class="forumtext">You can show off your maps in development in the forums if you want feedback on it. <a href="forums.php?mode=newtopic&forum=2"><b>Click here</b></a> to start a new topic.
	<br>You can also post updates on the map's progress <a href="cp.php?mode=news"><b>here</b></a> instead of updating its profile.
</div>

<?php
	$sql = mysql_query("SELECT * FROM maps WHERE user_id = '$userdata[user_id]' ORDER BY game,name"); $c = 0;
	while($array = mysql_fetch_array($sql)) { $c++;
		if($c==1) { 
			echo '<div style="width:98%">';
			$curgame = $array['game']; 
			echo '<table width="100%" align=right cellpadding=2 cellspacing=0 style="font-size:8pt"><tr style="font-weight:bold"><td width="39%">';
			echo "\n".'</td><td width="1%">edit</td><td width="10%" align=center>status</td><td width="8%"><font color="'.$colors['bg'].'">______</font></td><td width="8%"><font color="'.$colors['bg'].'">____</font></td><td width="4%"></td><td width="4%"></td><td width="26%">messages:</td></tr>';
		}
		if($array['game']!=$curgame) { $curgame = $array['game']; echo '<tr><td height=8> </td></tr>'; }
		if($array['dldead']) $mouseover = 'bgcolor="'.$colors['warningbg'].'"'; else $mouseover = 'onmouseover="style.background=\''.$colors['trmouseover'].'\'" onmouseout="style.background=\''.$colors['bg'].'\'"';

		echo "\n".'<tr '.$mouseover.'>';
		echo '<td><a href="maps.php?map='.$array['map_id'].'"><img src="themes/'.$images['moddir'].'/icon_'.$array['game'].'_'.$array['mod'].'.gif" border=0 align=texttop> '.$array['name'].'</a></td>';
		echo '<td align=center><a href="cp.php?mode=maps&edit='.$array['map_id'].'"><img src="images/gfx_edit.gif" alt="edit map" border=0></a></td>';
		echo '<td>';
			if($array['status']==-1) echo '<i>abandoned</i>';
			elseif($array['status']<100) echo '<img src="themes/'.$theme.'/nullred.gif" width="'.($array['status']).'" height=12 alt="'.$array['status'].'% complete"><img src="themes/'.$theme.'/nullgrey.gif" width="'.(100-$array['status']).'" height=12 alt="'.$array['status'].'% complete">';
		echo '<td>'.(($array['map_url'])?'<a href="maps.php?download='.$array['map_id'].'"><img src="images/gfx_download.gif" alt="download" border=0 align=absmiddle></a> '.$array['downloads']:''); echo '</td>';
		echo '<td>'.(($array['comments'])?'<a href="maps.php?map='.$array['map_id'].'"><img src="images/gfx_info.gif" border=0 alt="comments" align=absmiddle></a> '.$array['comments']:'').'</td>';
		echo '<td>'.(($array['thumbnails'])?screenshot($array['map_id'],$array['thumbnails'],'maps/'.$array['game'].'/images/'.$array['map_id'].'_1.jpg').'<img src="images/gfx_image.gif" border=0></a>':'').'</td>';
		echo '<td>'.(($array['thread'])?'<a href="forums.php?topic='.$array['thread'].'"><img src="images/gfx_thread.gif" border=0 alt="forum thread" align=absmiddle></a>':'').'</td>';
		echo '<td>'; $err = '';
			if($array['status']<100 && $array['status']>-1 && ($now_time-$array['date'])>15552000) $err = 'please update map details';
			//if($array['report']==1) $err = 'download link not working';
			//elseif($array['report']==2) $err = 'screenshot links not working';
			echo $err.'</td>';
			if($err) $alert = true;
		echo '</tr>';
	} if($c!=$uparray['maps']) @mysql_query("UPDATE users_profile SET maps = '$c' WHERE user_id = '$userdata[user_id]' LIMIT 1"); 

	if($c) {
		if($alert) echo '&quot;please update map details&quot;- you have a map still in beta status that you haven\'t updated for a while<br><img src="images/null.gif" height=8><br>';
		echo '</table></div>';
	} else echo 'No maps in your profile yet!';
?>

</fieldset>


<fieldset>
<legend>files</legend>
<table width="95%" align=center cellspacing=3 cellpadding=0 style="font-size:10pt">
<tr>
	<td align=right width="15%"><b>new prefab:</b></td>
	<td width="85%"><a href="?mode=files&action=prefabs" class=white>+click here to add a new prefab</a></td>
</tr>
<tr>
	<td align=right><b>new model:</b></td>
	<td><a href="?mode=files&action=models" class=white>+click here to add a new map model</a></td>
</tr>
<tr>
	<td align=right valign=top><b>edit file:</b></td>
	<td><?php 
	$sql = mysql_query("SELECT * FROM files WHERE author = '$userdata[user_id]' ORDER BY file_id DESC"); $c='';
	while($array = mysql_fetch_array($sql)) {
		if(!$c) echo '<form action="redir.php" method="post" name="fileform" onsubmit="if(!document.forms[\'fileform\'].elements[\'editfile\'].value) return false"><select name="editfile">';
		$echogame = 'filegame'.$array['game']; if(!$$echogame) { echo '<option value="" id="cat_white">for '.$array[game].':</option>'; $$echogame++; }
		echo '<option value="'.$array['file_id'].'">'.stripslashes($array['filename']);
	$c++; } if(!$c) echo ' <font color="'.$colors['medtext'].'">no files</font>'; else echo '</select><input name="submiteditfile" type="submit" name="submit" value="edit" class="submit2">';
	?>
	</td>
</tr>
</form>
</table>
</fieldset>

<fieldset>
<legend>tutorials</legend>
<table width="95%" align=center cellspacing=3 cellpadding=0 style="font-size:10pt">
<form action="redir.php" method="post" name="tutform" onsubmit="if(!document.forms['tutform'].elements['edittut'].value) return false">
<tr>
	<td align=right valign=top><b>add tutorial:</b></td>
	<td><?php $c = 0;
		$sql = mysql_query("SELECT id,name FROM games WHERE editor != ''");
		while($array = mysql_fetch_array($sql)) {
			if($c) echo ', ';
			echo '<a href="?mode=tutorials&action=new&game='.$array['id'].'"><b>'.$array['name'].'</b></a>';
			$c++;
		}
	?>
	</td>
</tr>
<tr>
	<td align=right valign=top width="15%"><b>edit tutorial:</b></td>
	<td width="85%"><?php
	$games = array();
	$sql = mysql_query("SELECT * FROM articles WHERE user_id = '$userdata[user_id]' AND section = 'editing' ORDER BY game,id DESC"); $c = 0;
	while($array = mysql_fetch_array($sql)) {
		if(!$c) echo '<select name="edittut">';
		if(!$games[$array['game']]) { echo '<option value="" id="cat_white">for '.$array['game'].':</option>'; $games[$array['game']] = 1; }
		echo '<option value="'.$array['id'].'">'.stripslashes($array['title']).' ('.$array['game'].')';
	$c++; } if(!$c) echo ' <font color="'.$colors['medtext'].'">no tutorials</font>'; else echo '</select><input type="submit" name="submitedittut" value="edit" class="submit2"><input type="submit" name="submitviewtut" value="view" class="submit2">';
	?>
	</td>
</tr>
</form>

<?php 
	$sql = mysql_query("SELECT * FROM authenticate WHERE user_id = '$userdata[user_id]' AND type='tutorial'");
	if($numauth = mysql_num_rows($sql)) { ?>
<tr>
	<td align=right valign=top><b>submitted:</b></td>
	<td valign=top>
	<?php while($array = mysql_fetch_array($sql)) {
		echo '<a href="?mode=tutorials&edit='.$array['id'].'&select=auth"><img align=texttop src="themes/'.$images['moddir'].'/icon_'.$array['game'].'.gif" border=0> <b>'.stripslashes($array[title]).'</b></a> :: ';
		if($array['status']=='0') echo '<font color="'.$colors['lighttext'].'">has not been checked</font>';
		if($array['status']=='1') echo '<font color="'.$colors['maybe'].'"><b>please check your spelling/punctuation and resubmit</font>';
		if($array['status']=='2') echo '<font color="'.$colors['maybe'].'"><b>not enough detail or incorrect, please rewrite and resubmit</font>';
			if($array['status']=='3') echo '<font color="'.$colors['maybe'].'"><b>formatting error- screenshots don\'t work or too many paragraphs? Please fix and resubmit.</font>';
		if($array['status']=='-1') echo '<font color="'.$colors['no'].'"><b>bad tutorial, needs complete rewrite</b></font>';
		if($array['status']=='-2') echo '<font color="'.$colors['yes'].'"><b>tutorial saved for later</b></font>';
	echo "<br>\n"; } ?></td>
</tr>
<?php } ?>
</table>
</fieldset>

<fieldset>
<legend>reviews</legend>
<table width="95%" align=center cellspacing=3 cellpadding=0 style="font-size:10pt">
<tr>
	<td align=right width="15%"><b>new review:</b></td>
	<td width="85%">To review a map please click the 'new review' button on its profile page</td>
</tr>
<tr>
	<td align=right valign=top><b>edit review:</b></td>
	<td><?php 
	$sql = mysql_query("SELECT * FROM reviews WHERE reviewer_id = '$userdata[user_id]' ORDER BY game DESC,mapname ASC"); $c='';
	while($array = mysql_fetch_array($sql)) {
		if(!$c) echo '<form action="redir.php" method="post" name="revform" onsubmit="if(!document.forms[\'revform\'].elements[\'editrev\'].value) return false"><select name="editrev">';
		$echogame = 'revgame'.$array['game']; if(!$$echogame) { echo '<option value="" id="cat_white">for '.$array['game'].':</option>'; $$echogame++; }
		echo '<option value="'.$array['review_id'].'">'.stripslashes($array['mapname']).' ('.$array['mod'].')';
	$c++; } if(!$c) echo ' <font color="'.$colors['medtext'].'">no reviews</font>'; else echo '</select><input type="submit" name="submiteditrev" value="edit" class="submit2"><input type="submit" name="submitviewrev" value="view" class="submit2">';
	?>
	</td>
</tr>
<tr>
	<td align=right valign=top><b>submitted:</b></td>
	<td><?php $c=0; $sql = mysql_query("SELECT m.name,m.game,m.mod,a.id,a.status FROM maps m, authenticate a WHERE a.user_id = '$userdata[user_id]' AND m.map_id = a.subtype");
		while($array = mysql_fetch_array($sql)) { $c++;
			echo '<a href="cp.php?mode=reviews&edit='.$array['id'].'&auth=true"><img src="themes/'.$images['moddir'].'/icon_'.$array['game'].'_'.$array['mod'].'.gif" border=0 align=middle> '.$array['name'].'</a> :: ';
			if($array[status]=='0') echo '<font color="'.$colors['lighttext'].'">has not been checked</font>';
			if($array[status]=='1') echo '<font color="'.$colors['maybe'].'"><b>please check your spelling/punctuation and resubmit</font>';
			if($array[status]=='2') echo '<font color="'.$colors['no'].'"><b>not enough detail or too short, please re-write</font>';
			if($array[status]=='3') echo '<font color="'.$colors['no'].'"><b>formatting error- doesn\'t look like the rest of our reviews</font>';
			if($array[status]=='-1') echo '<font color="'.$colors['no'].'"><b>bad review, needs complete re-write!</b></font>';
			echo "<br>\n";
		} if(!$c) echo '<font color="'.$colors['medtext'].'">No reviews submitted</font>';
	?>
</form>
</table>
</fieldset>
</p>
