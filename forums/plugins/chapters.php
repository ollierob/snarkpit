<?php if(!@include('forums/chapters/'.$topic.'.php')) @msg('Error loading chapter data!','error'); else {

	if(!$chap) { errorlog('Chapters',$topic); error_die('Error loading chapter data! Admins have been informed.'); }
	$c = 1;
	while(list($key,$val)=@each($chapters)) {

		if($c==1) {
			echo '<fieldset><legend>chapter list';
			if($userdata['user_level']>2 || is_moderator($forum,$userdata['user_id']) || $userdata['user_id']==$tarray['topic_poster']) echo ' - <a href="forums.php?forum='.$forum.'&topic='.$topic.'&mode=reply&act=chapter" class=green><b>add chapter</b></a>';
			echo '</legend><table width="100%" cellspacing=0 cellpadding=2 class="abouttext">';
		}

		echo "\n".'<tr><td>'.(($c==$chap)?'<font size=2>»</font>':'').'</td><td><a href="forums.php?forum='.$forum.'&topic='.$topic.'&chap='.$c.'&findpost=newest#newposts"';
		if($c!=$chap) echo ' class="white">'; else echo '>';

		$lp = mysql_fetch_array(mysql_query("SELECT poster_id,post_time FROM posts WHERE post_id = '$val' LIMIT 1"));
		if($lp['post_time']>$last_visit) echo '<img src="themes/'.$images['moddir'].'/smallsnark.gif" border=0 align=texttop> <b>'; else echo '<img src="themes/'.$images['moddir'].'/smallsnark_old.gif" border=0 align=texttop> ';

		$goto = '';
		$numposts = mysql_result(mysql_query("SELECT count(post_id) FROM posts WHERE topic_id = '$topic' AND chapter = '$c'"),0);
		if($numposts>$ppp) {
			$numpages = ceil($numposts/$ppp); if($numpages==2) $s = 2; else $s = $numpages-2;
			for($i=$s;$i<=$numpages;$i++) { if($i!=$s) $goto.=', '; $goto.= '<a href="?forum='.$forum.'&topic='.$topic.'&chap='.$c.'&start='.(($i-1)*$ppp).'"><b>'.$i.'</b></a>'; }
		} if($c==$chap) { $total = $numposts; $clink = '&chap='.$c; } 

		echo '<font size=2>'.stripslashes($key).'</font></b></a>&nbsp;&nbsp;['.$numposts.' post'.(($numposts!=1)?'s':'').'- '.(($goto)?'page '.$goto.'- ':'').agotime($lp['post_time'],true).' by '.userdetails($lp['poster_id'],'white',true,'').']</td></tr>';
	$c++; }

	if($c>1) { echo '<tr><td width="1%"></td><td width="99%"></td></tr></table></fieldset>'; $chapsql = "AND p.chapter = '$chap'"; }
} ?>
