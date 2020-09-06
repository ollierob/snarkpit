<?php

function get_last_post($id, $type) {
	switch($type) {
	case 'time_fix':
		$sql = "SELECT post_time FROM posts WHERE topic_id = '$id' ORDER BY post_time DESC LIMIT 1";   
	break;
	case 'forum':
		$sql = "SELECT p.post_time, p.poster_id, u.username FROM posts p, users u WHERE p.forum_id = '$id' AND p.poster_id = u.user_id ORDER BY post_time DESC LIMIT 1";
	break;
	case 'topic':
		$sql = "SELECT p.post_time, u.username FROM posts p, users u WHERE p.topic_id = '$id' AND p.poster_id = u.user_id ORDER BY post_time DESC LIMIT 1";
	break;
	case 'user':
		$sql = "SELECT post_time FROM posts WHERE poster_id = '$id' LIMIT 1";
	break;
	}
	if(!$result = mysql_query($sql)) error_die('Forums are down or just randomly b0rked, please press refresh and try again!');
	if(!$val = mysql_result($result,0)) return 0;
	return($val);
}

function reply_button() { global $topic,$forum,$userdata,$theme,$chap;
	if(isset($userdata['javaoff']) && $userdata['javaoff']==1) $java='&amp;java=1'; else $java = '';
	echo '<a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;mode=reply'.(($chap)?'&amp;chap='.$chap:'').$java.'"><img src="themes/'.$theme.'/reply.gif" border=0 alt="reply"></a>'; 
}

function newtopic_button() { global $forum,$userdata,$farray,$theme;
	if($farray['support']!=1) {
		if(isset($userdata['javaoff']) && $userdata['javaoff']==1) $java = '&amp;java=1'; else $java = '';
		echo '<a href="forums.php?mode=newtopic&amp;forum='.$forum.$java.'"><img src="themes/'.$theme.'/new_topic.gif" border=0 alt="new topic" title="Post a new topic"></a>';
		echo '<a href="forums.php?mode=newtopic&amp;forum='.$forum.$java.'&amp;act=poll"><img src="themes/'.$theme.'/new_poll.gif" border=0 alt="new poll" title="Post a new poll"></a>';
	} else { ?>
	<script language="javascript" type="text/javascript">
	var t_en = 'You cannot post a new topic in this forum without first searching the website for your problem, so you will now be taken to the search page.';
	var t_fr = 'Vous ne pouvez pas poser de question dans ce forum avant d\'en avoir bien cherché la réponse dans les archives du site; vous êtes donc emmené à la page de recherche';
	var t_de = 'Ohne vorher die Seite nach ihrem Problem durchsucht zu haben, können sie kein neues Thema erstellen. Daher werden sie jetzt zur Suchseite weitergeleitet.';
	var t_es = 'Usted no puede fijar un nuevo asunto en este foro sin primero buscando el Web site para su problema. Entonces, le ahora llevarán a la página de la búsqueda.';
	var t_17 = 'j00 c4nt p0st 1n h3ar wiv0ut s3rch1ng 1st!';
	</script>
	<a href="editing.php?game=<?=$farray['game']?>" onclick="alert(t_en+'\n\n'+t_fr+'\n\n'+t_de+'\n\n'+t_es+'\n\n'+t_17)"><img src="themes/<?=$theme?>/new_topic.gif" border=0 alt="new topic"></a>
	<?php }
}

function is_moderator($forum_id,$user_id) {
	$msql = mysql_query("SELECT user_id FROM forum_mods WHERE forum_id = '$forum_id' AND user_id = '$user_id'");
	if($marray = mysql_fetch_array($msql)) return('1');
}

function is_first_post($topic_id,$post_id) {
	global $tarray;
	if($tarray) $fp = $tarray['first_post_id'];
	else $fp = mysql_result(mysql_query("SELECT first_post_id FROM topics WHERE topic = '$topic_id' LIMIT 1"),0);

	if($post_id==$fp) return 1;
}

function assign_vars($array,$post) {
	$output = array_values($array); $input = array_keys($array);
	$post = str_replace($input,$output,$post);
	return $post;
}

function browser() { global $java,$userdata,$user_browser;
	$return = 'none';
	if(!$user_browser) {
	        $browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(substr_count($browser,'firefox')||substr_count($browser,'mozilla')) $return = 'moz';
		if(substr_count($browser,'msie')) $return = 'ie';
		if(substr_count($browser,'opera')||substr_count($browser,'safari')) $return = 'none';
		if($java||$userdata['javaoff']) $return = 'none';
		$user_browser = $return;
	} else $return = $user_browser;
	return $return;
}

function make_jumpbox($forum) {
        ?>
        <form name="forumselect" method="post">
	<select name="forum" onchange="if(document.forms['forumselect'].elements['forum'].value>0) location.href='forums.php?forum='+document.forms['forumselect'].elements['forum'].value;return true">
	<option value="" id="cat_white">Jump to forum:
	<?php
		if($forum) $forumsql = "WHERE forum_id != '$forum'"; else $forumsql = '';
		$sql = mysql_query('SELECT forum_id, forum_name FROM forums $forumsql ORDER BY cat,forum_id');
		while($row = mysql_fetch_array($sql)) {
			echo '<option value='.$row['forum_id'].'>'.$row['forum_name'].'</OPTION>';
		}
	?>
	</select></form>

	<?php
}

?>
