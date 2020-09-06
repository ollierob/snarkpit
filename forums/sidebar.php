<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">

<?php
	$forumsql = mysql_query("SELECT forum_id,forum_posts,forum_name,forum_last_post_id FROM forums ORDER BY cat,forum_id");
	while($forumarray = mysql_fetch_array($forumsql)) {
		
		$sql = mysql_query("SELECT SQL_CACHE post_time,poster_id,topic_id FROM posts WHERE post_id = '$forumarray[forum_last_post_id]' LIMIT 1");
		$parray = mysql_fetch_array($sql); $last_post_time = $parray['post_time'];

		if(isset($_COOKIE['forum'.$forumarray['forum_id'].'read'])) $lastread = $_COOKIE['forum'.$forumarray['forum_id'].'read']; else $lastread = '';

		echo '<div class="forumbox"><table cellpadding=2 cellspacing=0 style="font-size:8pt" bgcolor="'.$colors['bg'].'">';
		echo '<tr><td width="32"><a href="forums.php?forum='.$forumarray['forum_id'].'&amp;topic='.$parray['topic_id'].'&amp;findpost=newest#newposts">';
		if($last_post_time > $last_visit && $lastread < $last_post_time) { echo '<img src="'.$forumimg['new'].'" align="top" border=0 alt="new posts">'; $bgcolor = $colors['forum_newposts']; $b = '<b>'; } else { echo '<img src="'.$forumimg['old'].'" align="top" border=0 alt="no new posts">'; $bgcolor = $colors['forum_oldposts']; $b = ''; }
		echo '</a></td><td width="100%" bgcolor="'.$colors['sidebarinner'].'">';
		echo '<a href="forums.php?forum='.$forumarray['forum_id'].'&amp;'.$forumarray['forum_posts'].'"'.$colors['class_forummenu'].' target="_top" class="forummenu">'.$forumarray['forum_name'].'</a><br><font size=1>';
		if($forumarray['forum_last_post_id']!=0) {
			agotime($last_post_time,'');
			echo '<br>by '.userdetails($parray['poster_id'],'msidebar',1,'','');
		} else echo '<i>No posts</i>';
		echo '</font></td></tr>';
		//if(!isset($colors['sidebarcat'])) echo '<tr><td colspan="2" height="1"><table width="100%" cellpadding=0 cellspacing=0 bgcolor="'.$bgcolor.'" height=1><tr><td></td></tr></table></td></tr>';
		if($colors['forum_newposts']) echo '<tr><td colspan=2><table width="100%" height="1" cellspacing=0 cellpadding=0 style="margin:0;padding:0;height:1px;border-bottom:1px solid '.$bgcolor.'"><tr><td> </td></tr></table></td></tr>';
		echo '</table></div>'."\n";
	} 
?>
	<div align="center" style="font-size:8pt"><b>
		<a href="index.php?page=search&amp;wut=forums" class="sidebar">search the forums</a><br>
		<a href="forums.php?mode=markread&amp;returnto=<?=$forum?>&amp;t=<?=$now_time?>" class="sidebar">mark all forums read</a>
		<?php if($forum) echo '<br><a href="forums.php?mode=markread&amp;forum='.$forum.'&amp;t='.$now_time.'" class="sidebar">mark this forum read</a>'; ?>
	</b></div>

</div>

</td>
<td width="95%" valign="top" height="100%"><?=$pmbar?>
<div class="content">
