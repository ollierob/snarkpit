<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">

<?php 
	$spacer = '<br><img src="images/null.gif" height=4 alt=""><br>'."\n";

	if(!$userdata) { $fplim = 4; ?>
	<table width="100%" cellpadding=0 cellspacing=0 onmouseover="style.cursor='hand'" onclick="self.location='?page=faq'">
	<tr bgcolor="<?=$colors['altbox1']?>" onmouseover="style.background='<?=$colors['altbox2']?>';return true" onmouseout="style.background='<?=$colors['altbox1']?>';return true"><td><div style="margin:3px"><b>New to the site?</b></div></td><td valign=bottom><img src="images/gfx_faq_top.gif" alt="faq"></td></tr>
	<tr bgcolor="<?=$colors['trmouseover']?>" style="font-size:11px"><td><div style="margin:3px"><a href="?page=faq"><b>Read the FAQ</b></a></div></td><td valign=top><img src="images/gfx_faq_bottom.gif" alt="faq"></td></tr>
	</table><br>
<?php } else $fplim = $userdata['fplim']; ?>

	<div class="menuboxtitle"><a href="editing.php?page=tutorials" class="sidebar">new tutorials</a></div>
	<div class="menubox">
<?php 	//if(!$userdata['game']) { $selgame = ''; $game = $default_game; } else { $selgame = 'AND game = \''.$userdata['game'].'\''; $game = $userdata['game']; }
	$sql = mysql_query("SELECT game,id,title,description FROM articles WHERE section = 'editing' ORDER BY id DESC LIMIT $fplim");
	while($array = mysql_fetch_array($sql)) {
		echo '<img src="themes/'.$images['moddir'].'/icon_'.$array['game'].'.gif" width=16 border=0 align=right alt="'.$array['game'].'"><a href="editing.php?page=tutorials&amp;game='.$array['game'].'&amp;id='.$array['id'].'" class=msidebar><b>'.stripslashes($array['title']).'</b></a><br>';
		echo '<font size=1>'.stripslashes($array['description']).'</font>'.$spacer;
	}
?>
	</div>

	<div class="menuboxtitle"><a href="maps.php?sort=date" class="sidebar">new maps</a></div>
	<div class="menubox">
<?php
	$sql = mysql_query("SELECT * FROM maps WHERE map_url!='' OR thumbnails>0 OR scr1!='' ORDER BY map_id DESC LIMIT $fplim");
	if(isset($colors['sidebarbox'])) $white = 'msidebar'; else $white = 'white';
	while($array = mysql_fetch_array($sql)) { echo $c;
		echo '<img src="themes/'.$images['moddir'].'/icon_'.$array['game'].'_'.$array['mod'].'.gif" border=0 alt="'.$array['game'].'-'.$array['mod'].'" title="'.$array['game'].'-'.$array['mod'].'" align="right"><a href="maps.php?map='.$array['map_id'].'" class="msidebar"><b> '.stripslashes(substr($array['name'],0,20)).'</b></a><br>by '.userdetails($array['user_id'],$white,'return','').$spacer;
	}
	
	echo '</div>';

if(isset($userdata['javaoff']) && $userdata['javaoff']==1) {

	echo '<div class="menuboxtitle"><a href="forums.php" class=sidebar>forums</a></div>'."\n".'<div class="menubox">';

	$sql = mysql_query("SELECT forum_id,forum_last_post_id,forum_posts,forum_name FROM forums ORDER BY cat,forum_id");
	while($farray = mysql_fetch_array($sql)) {
		$lastposttime = mysql_result(mysql_query("SELECT SQL_CACHE post_time FROM posts WHERE post_id = '$farray[forum_last_post_id]' LIMIT 1"),0);
		if($lastposttime > $last_visit) $img = '<img src="images/smallsnark.gif" align=right height=16 alt="new posts" title="new posts" border=0 style="clear:left">'; else $img = '<img src="images/smallsnark_old.gif" align=right height=16 alt="no new posts" title="no new posts" border=0 style="clear:left">';
		echo '<a href="forums.php?forum='.$farray['forum_id'].'&amp;'.$farray['forum_posts'].'" class="forummenu">';
		echo $img.$farray['forum_name'].'</a>'.$spacer;
	}
	echo '</div>';
} ?>


<?php if($userdata) { if(!function_exists(map_watch)) include('index/func_index.php'); map_watch(); } ?>

</div>

</td><td width="95%" valign=top><?=$pmbar?>
<div class="content">
