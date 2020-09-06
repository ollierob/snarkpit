<?php
	tracker('Browsing '.$forumname.' board','');

	@mysql_query("UPDATE forums SET views = views + 1 WHERE forum_id = '$forum' LIMIT 1");

	if(isset($_COOKIE['forum'.$forum.'read'])) $last_visit = $_COOKIE['forum'.$forum.'read'];

	echo '<table width="99%"><tr><td width="80%">'.stripslashes($farray['forum_desc']);
	echo '<br><font color="'.$colors['medtext'].'">(moderated by '; 

		$sql = mysql_query("SELECT * FROM forum_mods WHERE forum_id = '$forum'");
		$num_mods = mysql_num_rows($sql); $countmods = 0;
		while($marray = mysql_fetch_array($sql)) {
			userdetails($marray['user_id'],'','','');
			$countmods++;
			if($countmods!=$num_mods) echo ', ';
		} if(!$countmods) echo 'no-one!';
		unset($marray);

	if(!$numpages = ceil($farray['forum_topics']/$ppf)) $numpages = 1;
	$gnum = array_fill(1,$numpages+1,0); $gnum[1] = 1;
	if(isset($_GET['page'])) $page = $_GET['page']; else $page = 1;
		if($page>$numpages) { header('Location: forums.php?forum='.$forum.'&amp;page='.$numpages); die; }
		if($page<1) { header('Location: forums.php?forum='.$forum); die; }

	$gotopage = 'Go to page: ';
	if($page!=1) $gotopage.=' <b><a href="forums.php?forum='.$forum.'&amp;page='.($page-1).'" class=white>«previous</a></b> ';
	$gotopage.= '[ ';
	if($numpages>1) {
		if($page) { $gnum[$page-2]='skip'; $gnum[$page+2]='skip'; }
		for($i=$numpages-2;$i<=$numpages;$i++) $gnum[$i]=1; if(!$gnum[$i]) $gnum[$numpages-3]='skip';
		if($numpages>3) { $gnum[1]=1;$gnum[2]=1;$gnum[3]=1; } else { for($i=1;$i<=$numpages;$i++) $gnum[$i]=1; }
		if($page) { for($i=($page-1);$i<=($page+1);$i++) $gnum[$i]=1; }
	}

		for($i=1;$i<=$numpages;$i++) { 
			if($gnum[$i]==1) {
				$justskipped='';
				if($page==$i) $gotopage.= '<b>'; else $gotopage.= '<a href="forums.php?forum='.$forum.'&amp;page='.$i.'">';
				$gotopage.= $i;
				if($page==$i) $gotopage.= '</b> '; else $gotopage.= '</a> '; }
			if($gnum[$i]=='skip' && !$justskipped) { $justskipped = 1; $gotopage.= '... '; }
		}

	$gotopage.=' ] ';
	if($page!=$numpages) $gotopage.=' <b><a href="forums.php?forum='.$forum.'&amp;page='.($page+1).'" class=white>next»</a></b> ';
	if($numpages==1) $gotopage = '';

	echo ')</font><br>'.$gotopage.'</td><td valign=top align=right>'; newtopic_button();
	echo '</td></tr></table>';

	//include custom forum header
	if($config['forumcustomheader']) {
		include('themes/'.$theme.'/forum_header.htm');
		if(!$template = file_get_contents('themes/'.$theme.'/forum.htm')) error_die('Error loading forum theme!');
	} else {
		include('forums/forum_header.htm');
		$template = file_get_contents('forums/forum.htm');
	}
	
	if(!$page) $start = 0; else $start = ($page-1)*$ppf;
	$fpslv = '';

$sql = mysql_query("SELECT * FROM topics WHERE forum_id = '$forum' ORDER BY sticky DESC, topic_time DESC LIMIT $start, $ppf");
while($tarray = mysql_fetch_array($sql)) {

	if($tarray['sticky']!=1 && isset($norow)) { unset($norow); echo '<tr><td height=12> </td></tr>'; }

	$mouseover = ' onmouseover="style.background=\''.$colors['trmouseover'].'\';return true" onmouseout="style.background=\''.$colors['bg'].'\';return true"';
	$replies = $tarray['topic_replies'];
	$last_post_time = $tarray['topic_time'];

	//images
	$image = ''; $alt = '';
	$newestpostlink = '<a href="forums.php?forum='.$forum.'&amp;topic='.$tarray['topic_id'].'&amp;findpost=newest#newposts">';
	if($last_post_time<$last_visit||($_COOKIE['readtopic'.$tarray['topic_id']] && $_COOKIE['readtopic'.$tarray['topic_id']]==$tarray['topic_time'])) {
		if($replies<50) $image = $forumimg['old']; else $image = $forumimg['pop_old'];
		if($tarray['answered']=='n') $image = $forumimg['help_old'];
		elseif($tarray['answered']=='p') $image = $forumimg['poll_old'];
		if($tarray['topic_status']==1||$tarray['topic_status']==3) $image = $forumimg['locked_old'];
	} else {
		if($replies<50) $image = $forumimg['new']; else $image = $forumimg['pop_new'];
		if($tarray['answered']=='n') $image = $forumimg['help_new'];
		elseif($tarray['answered']=='p') $image = $forumimg['poll_new'];
		if($tarray['topic_status']==1||$tarray['topic_status']==3) $image = $forumimg['locked_new'];
	} if($replies>=50) $alt = 'Hot topic- ';

	$img = $newestpostlink.'<img src="'.$image.'" border=0 alt="'.$alt.'go to newest post"></a>';

	//topic title & description
	$numpages = ceil(($replies+1)/$ppp);
	$topiclink = 'forums.php?forum='.$forum.'&amp;topic='.$tarray['topic_id'];
	$pagination = '';
	if(!$tarray['chapters']) {
		$start='';
		if($replies+1>$ppp) {
			$pagination = '(page ';
			$x = $numpages-1; if($x<2) $x=2;
			while($x<=$numpages) { 
				$start = '&amp;start='.(($x-1)*$ppp);
				$pagination .= '<a href="'.$topiclink.$start.'" class="forum">'.$x.'</a>';
				if($x!=$numpages) $pagination.=', ';
				$x++;
			}
		}
		$c = 0;
	} else {
	       	if($last_post_time<$last_visit) $new = false; else $new = true;
		if($new && !$fpslv) $fpslv = mysql_result(mysql_query("SELECT post_id FROM posts WHERE forum_id = '$forum' AND post_time > '$last_visit' ORDER BY post_time ASC LIMIT 1"),0);
		$pagination = '(chapters '; $c = 1;
		if(@include('forums/chapters/'.$tarray['topic_id'].'.php')) {
			while(list($chap,$lp)=each($chapters)) {
				$pagination.=(($c!=1)?', ':'').'<a href="'.$topiclink.'&amp;chap='.$c.'&amp;findpost=newest#newposts">'.(($fpslv<=$lp && $new)?'<b>':'').stripslashes($chap).(($fpslv<=$lp && $new)?'</b>':'').'</a>';
				$c++;
			}
 		} else $pagination = '';
	}
	
	if($pagination) $pagination = '<font color="'.$colors['medtext'].'" style="font-size:8pt">'.$pagination.')</font>';
	$topiclink .= '&amp;'.$replies;

	$title_pre = '';
	if(!$tarray['title']) $tarray['title'] = '<i>topic title</i>';
	if($tarray['sticky']==1) $title_pre = '<font style="color:'.$colors['subtitle'].'"><b>Sticky:</b></font> ';
	if($tarray['answered']=='p') $title_pre .= '<font style="color:'.$colors['info'].'"><b>Poll:</b></font> ';

	if($tarray['map']) {
		$marray = mysql_fetch_array(mysql_query("SELECT `game`,`mod`,`thread` FROM maps WHERE `map_id` = '$tarray[map]' LIMIT 1"));
		if(!$marray) $marray['game'] = '{X}';
		//$modimg = '<img src="themes/'.$images['moddir'].'/'.(($marray)?'icon_'.$marray['game'].'_'.$marray['mod'].'.gif" width="16"':'gfx_delete.gif" alt="deleted" title="Map has been deleted"').' align="top"> ';
		if($tarray['topic_id']!=$marray['thread']) @mysql_query("UPDATE maps SET thread = '$tarray[topic_id]' WHERE map_id = '$tarray[map]' LIMIT 1");
	} else $modimg = '';

	$title = $title_pre.'<a href="'.$topiclink.'" class="forum">';
	if($tarray['topic_status']==1 || $tarray['topic_status']==3) $i = true; else $i = false;
	$title .= (($i)?'<i>':'').stripslashes($tarray['title']).(($i)?'</i>':'').'</a></font> '.(($marray['game'])?modicon($marray['game'],$marray['mod']):'').$pagination;

	//description, author,views; replies done in $replies already
	if($tarray['description']) $desc = '<font size=1>'.stripslashes($tarray['description']).'</font>'; else $desc = '';
	$author = userdetails($tarray['topic_poster'],'','return','');
	$views = $tarray['views'];

	//last post
	$lptime = ((!$replies)?'posted ':'').(($last_post_time)?agotime($last_post_time,'return'):'?');

	if($replies>0) {
	        $lpauthor = mysql_result(mysql_query("SELECT u.username FROM users u, posts p WHERE p.post_id = '$tarray[last_post_id]' AND u.user_id = p.poster_id LIMIT 1"),0);
		if($lpauthor) $lpauthor = 'by <a href="users.php?name='.$lpauthor.'">'.$lpauthor.'</a>'; else $lpauthor = 'by ?';
	} else $lpauthor = '';

	$c++;
	if($tarray['sticky']==1) $norow = 1;

	$row = assign_vars(array(
		'{MOUSEOVER}'=>' onmouseover="this.style.background=\''.$colors['trmouseover'].'\';return true" onmouseout="this.style.background=\'\';return true"',
		'{IMG}'=>$img,
		'{TITLE}'=>$title,
		'{DESC}'=>$desc,
		'{REPLIES}'=>$replies,
		'{VIEWS}'=>$views,
		'{AUTHOR}'=>$author,
		'{LPTIME}'=>$lptime,
		'{LPAUTHOR}'=>$lpauthor,
	),$template);
	
	echo "\n".$row;

}	

	if(isset($c) && !$c) echo '<tr><td></td><td colspan=4>No posts in this forum<br><img src="images/null.gif" height=10 width=1 alt=""></td></tr>';

	if($config['forumcustomheader']) include('themes/'.$theme.'/forum_footer.htm'); else include('forums/forum_footer.htm');

	echo $gotopage;

	if($farray['support']==1) $l_ans = '</font><br>&nbsp;<font color="'.$colors['forum_off'].'">answered</font></b>'; else $l_ans = '</font>';

?>

	<p>
	<table width="100%" style="font-size:9pt" cellspacing=1 cellpadding=1><tr>
	<td width="35%"><?php if($last_visit) { ?>last visit as <?=$userdata?$userdata['username']:'guest';?> [<?php agotime($last_visit,''); ?>]<? } ?></td>
	<td width="30%" align=center><?php if($config['forumjumpbox']) make_jumpbox($forum); ?></td>
	<td width="35%"><form action="index.php?page=query" method="post" name="fsearch">
		<input type="hidden" name="searchforumsubject" value="on"><input type="hidden" name="searchforummessage" value="on"><input type="hidden" name="forumselect" value="<?=$forum?>">
		<b><font color="<?=$colors['item']?>">search this forum:</font></b><br><input type="text" name="search" class="textinput" size=24>
		<input type="submit" value="find" class="submit2" onclick="if(document.forms['fsearch'].elements['search'].value=='') return false">
	</form></td>
	</tr></table>

	<p>
	<table cellpadding=2 cellspacing=0 align=center><tr>
	<td><img src="<?=$forumimg['old']?>" align=left alt="no new posts"><b><font color="<?=$colors['forum_post']?>">No new posts<?=$l_ans?></b></td>
	<td><img src="<?=$forumimg['new']?>" align=left alt="new posts"><b><font color="<?=$colors['forum_post']?>">New posts<?=$l_ans?></b></td>
	<td><img src="<?=$forumimg['pop_new']?>" align=left alt="popular topic"><font color="<?=$colors['forum_hot']?>"><b>Popular topic</b></font></td>
	<td><img src="<?=$forumimg['locked_new']?>" align=left alt="locked topic"><font color="<?=$colors['forum_locked']?>"><b>Locked topic</b></font></td>
	</tr>
<?php if(substr_count($forumname,'editing')) { ?>
	<tr>
	<td><img src="<?=$forumimg['help_new']?>" align=left alt="no new posts, unanswered"><b><br>&nbsp;<font color="<?=$colors['forum_off']?>">unanswered</b></font></td>
	<td><img src="<?=$forumimg['help_old']?>" align=left alt="new posts, unanswered"><b><br>&nbsp;<font color="<?=$colors['forum_off']?>">unanswered</b></font></td>
	</td></td>
	</tr>
<?php } ?>
	<tr><td colspan=4 class="help" align=center>Click on the icon next to a topic to read posts made since your last visit!</td></tr>
	</table>
<p>
