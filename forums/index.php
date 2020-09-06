<?php
	title('The SnarkPit Forums','none'); 
	tracker('Forums','');
	if(isset($_GET['msg'])) msg($_GET['msg'],'error');
?>
<p>Please select a forum from the list on the left. Only registered users may post.</p>

<table width="80%" align=center><tr><td>
<fieldset>
	<legend>forum stats</legend>
	<table width="100%" cellspacing=2 cellpadding=1 class="abouttext">
	<?php $sql = mysql_query("SELECT * FROM forums ORDER BY cat, forum_id");
		while($f = mysql_fetch_array($sql)) {
			echo '<tr><td width="20%"><nobr><b><a href="forums.php?forum='.$f['forum_id'].'">'.$f['forum_name']."</a></b></td>\n";
			echo '<td width="100%">'.$f['forum_topics'].' topics, '.$f['forum_posts'].' posts, '.$f['views'].' visits; last post ';
			if($f['forum_last_post_id']) $pt = mysql_result(mysql_query("SELECT post_time FROM posts WHERE post_id = '$f[forum_last_post_id]' LIMIT 1"),0);
			echo agotime($pt,'return');
			echo '</td>';
		}
	?>
	</table>
</fieldset>
</td></tr></table>

<p>
<table cellpadding="2" align="center">
<tr>
	<td><img src="<?=$oldpost_img?>" align="left"><b><font color="<?=$colors['forum_post']?>">No new posts<br>since last visit</b></font></td>
	<td width=15></td><td><img src="<?=$newpost_img?>" align=left><font color="<?=$colors['forum_post']?>"><b>New posts<br>since last visit</font></b></td>
</tr></table>

</td></tr>
