<?php
	tracker('Reading profile for '.$uarray['username'],'users.php?name='.$uarray['username']);

	if(!$uarray) error_die('User <b>'.$name.'</b> not found'); 
	title('Profile for '.$uarray['username'],none); 

	if(($id && $userdata['user_id']!=$id) OR ($name && $userdata['username']!=$name)) @mysql_query("UPDATE users_profile SET profile_count = profile_count + 1 WHERE username = '$name' LIMIT 1");

?>

<script language="JavaScript">
function showhide(id) {
<?php	if($userdata['javaoff']) echo '	return true;'; else { ?>
        divtoshow = document.getElementById(id);
        divtoupdate = document.getElementById(id+'text');
        if(divtoshow.style.display=='inline') {
        	divtoshow.style.visibily = 'invisible';
		divtoshow.style.display = 'none';
		divtoupdate.innerHTML = '+expand';

        } else {
        	divtoshow.style.visibily = 'visible';
		divtoshow.style.display = 'inline';
		divtoupdate.innerHTML = '-collapse';
        }
<? } ?>
}
</script>

<table width="99%"><tr>
<td width="65%" valign=top style="font-size:11px">
	<div class="abouttext" style="padding:2px">
	<?php
		echo $uparray['profile']?stripslashes($uparray['profile']):'<i>This member hasn\'t written anything about themself.</i>'; echo '<ul style="list-style-type:square">';
		if($userdata && $userdata['user_id']!=$uarray['user_id']) echo '<li><a href="cp.php?mode=compose&to='.$uname.'"><img src="images/gfx_pm.gif" border=0 align=top> send this user a private message</a>';
		if($userdata['user_level']>3) echo '<li><a href="admin.php?mode=edituser&id='.$uarray['user_id'].'"><img src="images/gfx_edit.gif" border=0 align=top"> edit user</a>';
		if($uparray['photo']) echo '<li><a href="javascript:void(0)" onclick="popwin(\'screenshot.php?img=userimages/photo'.$uparray['user_id'].'.'.$uparray['photo'].'\')" onmouseover="window.status=\'Click here to view a photo of this user\';return true" onmouseout="window.status=\'\';return true"><img src="images/gfx_image.gif" border=0 align=middle"> click here to view a photo of me</a>';
	?></ul>
	</div>
</td><td width="35%" valign=top>
	<fieldset>
	<legend>about</legend>
	<font color="<?=$colors['lighttext']?>">member #<?=$uarray['user_id']?>, <?php
		switch($uarray['user_level']) {
			case -1: echo '<font color='.$colors['no'].'>banned</font>'; break;
			case 0: echo 'n00b'; break; 
			case 1: echo 'member'; break;
			case 2: echo 'moderator'; break;
			case 3: echo 'admin'; break;
			case 4: echo 'überlord'; break;
		} ?>; <b><?=$uparray['profile_count']?></b> profile hits
	<br>registered <b><?=gmdate("M jS Y",$uparray['user_regdate'])?></b><br>
	last seen <b><?=($uarray['last_seen'])?gmdate('M jS Y',$uarray['last_seen']):(($uparray['last_seen'])?$uparray['last_seen']:'never')?></b>;
	<?php if($online = mysql_result(mysql_query("SELECT user_id FROM sessions WHERE user_id = '$uarray[user_id]' LIMIT 1"),0)) echo 'user <font color="'.$colors['yes'].'">online</font>'; else echo 'user offline';
		if($uparray['website']) echo '<br>website: <a href="'.$uparray['website'].'" target="_blank">'.str_replace('http://','',$uparray['website']).'</a>';
		if($hideemail!=1 && $userdata) echo '<br>email: <a href="mailto:'.$uparray['user_email'].'">'.$uparray['user_email'].'</a>';
		if($uparray['location']) echo '<br>location: <b>'.stripslashes($uparray['location']).'</b>'; 
		if($uparray['occupation']) echo '<br>occupation: <b>'.stripslashes($uparray['occupation']).'</b>'; 
	if($userdata) { $c = '';
		if($uparray['msnm']) $c.= '<a href="http://members.msn.com/default.msnw?mem='.$uparray['msnm'].'" target="_blank"><img src="themes/'.$images['moddir'].'/forum_msn.gif" border="0" align="texttop" title="'.$uparray['msnm'].'"></a> ';
		if($uparray['icq']) $c.= '<a href="http://wwp.icq.com/whitepages/about_me/1,,,00.html?Uin='.$uparray['icq'].'"><img src="http://web.icq.com/whitepages/online?icq='.$uparray['icq'].'&img=5" border=0 align="texttop" width=16 height=16 title="'.$uparray['icq'].'"></a> ';
		if($uparray['yim']) $c.= '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.$uparray['yim'].'&.src=pg"><img border=0 align=texttop src="images/forum_yim.gif"><img border=0 align=texttop src="http://opi.yahoo.com/online?u='.$uparray['yim'].'&m=g&t=1" title="'.$uparray['yim'].'"></a> ';
		if($uparray['steam']) $c.= '<img src="themes/'.$images['moddir'].'/forum_steam.gif" alt="Steam: '.$uparray['steam'].'" title="Steam: '.$uparray['steam'].'" align="top"> ';
		if($c) echo '<br>contact: '.$c;
	} ?>
	</fieldset>
</td></tr>
</table></p>

<?php if(!$lim) $lim = 4; $c = 0; $table = 0;
	$sql = mysql_query("SELECT * FROM news WHERE user_id = '$uarray[user_id]' AND plan!=0 ORDER BY id DESC LIMIT $lim");
	while($array = mysql_fetch_array($sql)) {
		if(!$c) {
			echo '<table width="100%" cellspacing=0 cellpadding=2>';
			$planbari = 'planbar_news';
		} else $planbari = 'planbar_left';
		echo "\n".'<tr><td style="background-image:url(\'themes/'.$theme.'/'.$planbari.'.gif\');background-repeat:no-repeat" width=30><img src="images/null.gif" width=26 height=1></td><td style="background-image:url(\'themes/'.$theme.'/planbar.gif\');background-repeat:x-repeat" height=24 width="99%"><span class="newstitle"><b>'.stripslashes($array['subject']).'</b></span> on '.gmdate("D jS M Y",$array['date']).'</td></tr>';
		echo "\n".'<tr><td></td><td style="padding-bottom:20px" class="news">'.stripslashes($array['text']).'</td></tr>';
	$c++; }

	if($c) { 
		if(!$_GET['lim'] && $c==4) echo '<tr><td></td><td><b><a href="?name='.$name.'&lim=100">Click here for more news from this author</a></b></td></tr>';
		echo '<tr><td><img src="images/null.gif" width=26 height=1></td></table>';
	}

	echo '<p>';
	title('<a href="maps.php" class=white name="maps">Maps</a> made by '.$uarray['username'],'none','no title');

	$sql = mysql_query("SELECT * FROM maps WHERE user_id = '$uarray[user_id]' ORDER BY map_id DESC LIMIT 1");
	if($iarray = mysql_fetch_array($sql)) $table++;

	if($uparray['favmap']>0 && $uparray['favmap']!=$iarray['map_id']) {
		$farray = mysql_fetch_array(mysql_query("SELECT * FROM maps WHERE map_id = '$uparray[favmap]' LIMIT 1"));
		$table++;
	}

if($table && $uparray['maps']>1) {

		echo '<table width="99%" cellspacing=0 cellpadding=2 style="font-size:8pt"><tr>';

	if($iarray) {
		echo "\n".'<td width="50%" valign=top><fieldset><legend>newest map</legend>';
		echo '<a href="maps.php?map='.$iarray['map_id'].'">';
		if($iarray['thumbnails']) echo '<img src="maps/'.$iarray['game'].'/images/'.$iarray['map_id'].'_1_thumb.jpg" border=0 align=right class="thumb">';
		echo '<b><font size=2>'.modicon($iarray['game'],$iarray['mod']).stripslashes($iarray['name']).'</a></font></b><br>';
		if($iarray['map_url']) echo ' <a href="maps.php?download='.$iarray['map_id'].'" onMouseOver="window.status=\''.$iarray['map_url'].'\'; return true" onMouseOut="window.status=\'\'"><img src="images/gfx_download.gif" align=absmiddle border=0></a>';
		if($iarray['rating']) '<table align=right height=50><tr><td valign=bottom><font size=4><b>'.$iarray['rating'].'/10</b></font></td</tr></table>';
		if(strlen(strip_tags($iarray['map_about']))>220) {
			$iarray['map_about'] = substr($iarray['map_about'],0,220);
			$lastgap = strrpos($iarray['map_about'],' ');
			$iarray['map_about'] = substr($iarray['map_about'],0,$lastgap).'...';
		}
		echo stripslashes($iarray['map_about']);
	echo '</fieldset></td>'."\n"; }

	if($iarray && $uparray['favmap']) echo '<td width="1"> </td>';

	if($farray) {
		echo "\n".'<td width="50%" valign=top><fieldset><legend>favourite map</legend>';
		echo '<a href="maps.php?map='.$farray['map_id'].'">';
		if($farray['thumbnails']) echo '<img src="maps/'.$farray['game'].'/images/'.$farray['map_id'].'_1_thumb.jpg" border=0 align=right>';
		echo '<b><font size=2>'.modicon($farray['game'],$farray['mod']).stripslashes($farray['name']).'</a></font></b><br>';
		if($farray['map_url']) echo ' <a href="maps.php?download='.$farray['map_id'].'" onMouseOver="window.status=\''.$farray['map_url'].'\'; return true" onMouseOut="window.status=\'\'"><img src="images/gfx_download.gif" align=absmiddle border=0></a>';
		if($farray['rating']) '<table align=right height=50><tr><td valign=bottom><font size=4><b>'.$farray['rating'].'/10</b></font></td</tr></table>';
		if(strlen(strip_tags($farray['map_about']))>220) {
			$farray['map_about'] = substr($farray['map_about'],0,220);
			$lastgap = strrpos($farray['map_about'],' ');
			$farray['map_about'] = substr($farray['map_about'],0,$lastgap).'...';
		}
		echo stripslashes($farray['map_about']);
	echo '</fieldset></td>'."\n"; }

	if($table==1) echo '<td width="50%"></td>';
	echo '</tr></table>';
}

echo "\n\n".'<p>';

	$c = 0;
	$gc = 0;

	$sql = mysql_query("SELECT m.*, g.sortid FROM maps m, games g WHERE m.user_id = '$uarray[user_id]' AND g.id = m.game ORDER BY g.sortid DESC,m.game,m.name");
	while($marray = mysql_fetch_array($sql)) {

		if(!$gamearray[$marray['game']]) {
		        $gamearray[$marray['game']]=1;
			if($c!=0) echo '</div>';
			$garray = mysql_fetch_array(mysql_query("SELECT name,colour FROM games WHERE id = '$marray[game]' LIMIT 1"));
			echo '<p><a href="#'.$garray['name'].'" onclick="showhide(\'game'.$marray['game'].'\')" class="white"><b><font color="'.$garray['colour'].'" size=2>'.$garray['name'].' maps:</font></b>';
			if(!$userdata['javaoff']) echo ' <span id="game'.$marray['game'].'text">'.(($userdata && $marray['game']!=$userdata['game'])?'+expand':'-collapse').'</span></a><a name="'.$garray['name'].'"></a>';
			echo '<div id="game'.$marray['game'].'"'.(($userdata && $marray['game']!=$userdata['game'] && !$userdata['javaoff'])?' style="visibility:invisibile;display:none"':' style="visibility:visible;display:inline"').'>';
		}

		echo "\n".'<div style="border:1px solid '.$colors['bg'].';margin:4px;width:99%" onmouseover="this.style.border=\'1px solid '.$garray['colour'].'\';return true" onmouseout="this.style.border=\'1px solid '.$colors['bg'].'\';return true">';
		echo '<table width="99%" cellpadding=3 cellspacing=0 style="font-size:8pt"><tr>';
		echo '<td valign=top width=16><img src="themes/'.$images['moddir'].'/icon_'.$marray['game'].'_'.$marray['mod'].'.gif" border=0 align=absmiddle>';
		echo '</td><td valign=top class="abouttext" width="70%"><a href="maps.php?map='.$marray['map_id'].'"><b><font size=2>'.stripslashes($marray['name']).'</font></b></a>';
		if($marray['status']==-1) echo ' <font color="'.$colors['no'].'"><b>map abandoned</b></font>';
		elseif($marray['status']!=100) echo ' <font color="'.$colors['info'].'">beta: <b>'.$marray['status'].'%</b></font>';
		if($userdata['user_id']==$uarray['user_id']) echo ' [<a href="cp.php?mode=maps&edit='.$marray['map_id'].'&returnto=users">edit map</a>]';

		if($marray['cdate']) { if($marray['status']<100) echo '<br><b>Expected release date:</b> '; else echo '<br><b>Released:</b> '; echo $marray['cdate'].'<br>'; }
		echo '<br>'.$marray['map_about'].'</p>';

		echo '</td><td valign=top width="100"><nobr>'; 
			if($marray['thumbnails']) echo screenshot($marray['map_id'],$marray['thumbnails'],'maps/'.$marray['game'].'/images/'.$marray['map_id'].'_1.jpg').'<img src="maps/'.$marray['game'].'/images/'.$marray['map_id'].'_1_thumb.jpg" border=0 align=left class="thumb"></a>';
			else {
				$numscreens=0; for($i=1;$i<=5;$i++) { $scr = 'scr'.$i; if($marray[$scr]) $numscreens++; }
				for($i=1;$i<=$numscreens;$i++) echo '<a href="javascript:void(0)" onclick="popwin(\'screenshot.php?img='.$marray['scr'.$i].'&theme='.$theme.'\')" onMouseOver="window.status=\'Screenshot '.$i.' (popup window)\'; return true" onMouseOut="window.status=\'\'"><img src="images/gfx_image.gif" align=absmiddle border=0></a>'; 
				if(!$numscreens) echo '<img src="images/null.gif" height=75 width=100 align=right>';
			}
		echo '</nobr></td><td valign=top width="28%">';
		if($marray['map_url']) {
			echo '<a href="maps.php?download='.$marray['map_id'].'" onMouseOver="window.status=\''.$marray['map_url'].'\'; return true" onMouseOut="window.status=\'\'"><b>download';
			if(!substr_count($marray['map_url'],'.zip') && !substr_count($marray['map_url'],'.rar')) echo '</b></a>'; else echo 's:</b></a> '.$marray['downloads'];
		}
		echo '<br><a href="maps.php?map='.$marray['map_id'].'" class=white>'.$marray['comments'].' comment'.(($marray['comments']!=1)?'s':'').'</a>'; 
		if($userdata && !$marray['thread']) echo ' (<a href="cp.php?mode=newcomment&type=map&id='.$marray['map_id'].(($marray['thread'])?'&thread='.$marray['thread']:'').'" class=white>+add</a>)';
		if($marray['rating']) echo '<br>rated '.$marray['rating'].'/10';
		if($marray['thread']) echo '<br><a href="forums.php?topic='.$marray['thread'].'" class=white>forum thread</a></b>';
		echo '</td></tr><tr><td height=4></td></tr></table></div>';

		$c++;
	}
	
	if($c) echo '</div>';

	if($c!=$uparray['maps']) @mysql_query("UPDATE users_profile SET maps = '$c' WHERE user_id = '$uarray[user_id]' LIMIT 1");
	if(!$c) echo '<font size=2>No maps</font>';

	?>

</p>
