<?php
	$sql = mysql_query("SELECT * FROM reviews r, reviews_text rt WHERE r.review_id = '$id' AND rt.review_id = r.review_id LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die("Review not found");
	$mapname = stripslashes($array['mapname']); $mapid = $array['map_id'];

	$maparray = mysql_fetch_array(mysql_query("SELECT user_id,map_url,thumbnails FROM maps WHERE map_id = '$mapid' LIMIT 1"));
	$gamename = mysql_result(mysql_query("SELECT name FROM games WHERE id = '$array[game]' LIMIT 1"),0);

	title("$t_features <a href=\"?page=reviews&game=$array[game]&mod=$array[mod]\" class=white>$gamename ($array[mod]) Map Reviews</a> » Review of &quot;$mapname&quot;",features);
	tracker('Reading review of '.$mapname,'features.php?page=reviews&id='.$id);
?>
	<fieldset>
	<legend>map info</legend>

<table width="100%" style="font-size:9pt" cellpadding=3 cellspacing=1 style="line-height:1.5em">
<tr><td width="33%" valign=top>
	<b>Author:</b> <?php if(!$array['author']) userdetails($maparray['user_id'],'','',''); ?><br>
	<b>Reviewer:</b> <?=userdetails($array['reviewer_id'],'','','')?><br>
	<b>Date:</b> <?=($array['date'])?date('l jS F Y',$array['date']):'?'?>
</td><td width="33%" valign=top><b>Pros:</b> <span class="abouttext"><?=stripslashes($array['pros'])?></span>
</td><td width="33%" valign=top><b>Cons:</b> <span class="abouttext"><?=stripslashes($array['cons'])?></span></td>

</tr>
<tr><td colspan=3>
<?php
 	if($maparray['map_url']) echo '<b>Download: <a href="maps.php?download='.$mapid.'">'.$maparray['map_url'].' <img src="images/gfx_download.gif" border=0 align=middle></a>';
	if($array['alt_download'] && $array['alt_download']!=$downloadurl) echo (($maparray['map_url'])?'<br>':'').'<b>Download: <a href="'.$array['alt_download'].'">'.$array['alt_download'].'</a><br>';
?>
	<span style="float:right">
		<b><a href="maps.php?map=<?=$mapid?>"><img src="images/gfx_info.gif" border=0 align=middle> more details and comments</a>
		<?php if($userdata) echo '&nbsp;&nbsp;<a href="cp.php?mode=reviews&map='.$mapid.'"><img src="images/gfx_edit.gif" border=0 align=middle> review this map</a>'; ?>
	</span>
</td></tr></table>
</fieldset>

<table width="99%" cellspacing=0 cellpadding=4 style="border-bottom:1px solid <?=$colors['lightbg']?>">
<tr>
	<td width="99%" valign=top><div class="forumtext"><?=stripslashes($array['text'])?></div></td>
	<td width="1%"></td>
	<td width="100" valign=top style="font-size:8pt" align=center>
		<?php $echo = ''; $numthumbs = 0;
			for($i=1;$i<6;$i++) { $imgtext = 'img_text'.$i; if($array[$imgtext]) $numthumbs++; }
			for($i=1;$i<6;$i++) { $imgtext = 'img_text'.$i;
				if($array[$imgtext]) {
					$bigimg = 'maps/'.$array['game'].'/images/'.$mapid.'_'.$i.'_review.jpg';
					$echo.='</p><a href="javascript:void(0)" onclick="popwin(\'screenshot.php?theme='.$theme.'&numthumbs='.$numthumbs.'&map='.$mapid.'&img='.$bigimg.'\')" onMouseOver="window.status=\'Click to view larger screenshot\'; return true" onMouseOut="window.status=\'\'" class=white><img src="maps/'.$array['game'].$modlink.'/images/'.$mapid.'_'.$i.'_review_thumb.jpg" border=0 class="thumb"></a><br>'.stripslashes($array[$imgtext]);
				}
				elseif($maparray['thumbnails']>0) {
					for($i==1;$i<=$maparray['thumbnails'];$i++) $echo.='</p><a href="javascript:void(0)" onclick="popwin(\'screenshot.php?theme='.$theme.'&numthumbs='.$numthumbs.'&map='.$mapid.'&img=maps/'.$array['game'].'/images/'.$mapid.'_'.$i.'.jpg\')" onMouseOver="window.status=\'Click to view larger screenshot\'; return true" onMouseOut="window.status=\'\'" class=white><img src="maps/'.$array['game'].'/images/'.$mapid.'_'.$i.'_thumb.jpg" border=0 class="thumb"></a><br>';
				}
			} echo $echo; 
		?>
	</td>
</tr>
</table>
<p>

<table width="100%" style="font-size:9pt" cellpadding=2 cellspacing=2>

<?php if($array['gameplay']||$array['design']) { ?>
<tr>
	<td width=50% valign=top><b>Gameplay:</b> <?=stripslashes($array['gameplay'])?></td>
	<td width=50% valign=top><b>Design:</b> <?=stripslashes($array['design'])?></td>
</tr>
<? } ?>

<tr><td colspan=2><b>Verdict:</b> <?=stripslashes($array['verdict'])?></td></tr>
</table>

<p><font size=4><b>Score: <?=$array['score']?>/10</b></font>
<p>

<?php //subtitle('map comments','',80); getcomments('map',$array[map_id]);
footer(); ?>
