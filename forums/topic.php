<?php
	$trackertitle = addslashes($topictitle);
	tracker("Reading thread \'$trackertitle\'",'');

	if($tarray['topic_time']>$last_visit) setcookie('readtopic'.$topic,$tarray['topic_time'],($now_time+3600),$cookiepath,$cookiedomain,0); //i think arrayed cookies expire at the same time?

	if(isset($_GET['start'])) $start = $_GET['start']; else $start = 0;
	if(isset($_GET['findpost'])) $findpost = $_GET['findpost']; else $findpost = '';
	if(isset($_GET['chap'])) $chap = $_GET['chap']; else $chap = ''; $chapsql = '';

	if(isset($userdata['javaoff'])) $java = '&amp;java='.$userdata['javaoff']; else $java = '';
	if($start % $ppp) $start = round($start/$ppp)*$ppp;

	$sql = mysql_query("SELECT * FROM topics WHERE topic_id = '$topic' LIMIT 1");
	if(!$tarray = @mysql_fetch_array($sql)) error_die('The topic you selected does not exist. Please go back and try again.','');

	if($findpost) { $sql = '';
		if($findpost=='lastpost') $sql = "SELECT post_id,chapter FROM posts WHERE topic_id = '$topic' ORDER BY post_id DESC LIMIT 1";
		elseif($findpost=='newest' && !$chap) $sql = "SELECT post_id,chapter FROM posts WHERE post_time > '$last_visit' AND topic_id = '$topic' ORDER BY post_id ASC LIMIT 1";
		elseif($findpost=='newest' && $chap) $sql = "SELECT post_id,chapter FROM posts WHERE post_time > '$last_visit' AND topic_id = '$topic' AND chapter = '$chap' ORDER BY post_id ASC LIMIT 1";
		else $sql = "SELECT post_id,chapter FROM posts WHERE post_id = '$findpost' LIMIT 1";
		$a = @mysql_fetch_array(mysql_query($sql));
		if(!$a) $a = mysql_fetch_array(mysql_query("SELECT post_id,chapter FROM posts WHERE topic_id = '$topic' ORDER BY post_id DESC LIMIT 1"));

		$findpost = $a['post_id'];
		if($tarray['chapters']=='y' && !$chap) $chap = $a['chapter']; //else $chap = $_GET['chap'];
		if($chap) $chapsql = "AND chapter = '$chap'"; else $chapsql = '';
		$priorposts = @mysql_result(mysql_query("SELECT count(post_id) FROM posts WHERE post_id < '$findpost' AND topic_id = '$topic' $chapsql"),0);
		$start = floor($priorposts/$ppp)*$ppp; //not priorposts-1 since $sql has post_id < $findpost, not <=
		if($start<0) $start = 0;
	}

	if($tarray['map']) include('forums/plugins/map.php');

	if(!isset($chap)) $chap = '';
	if(!$chap && isset($_GET['chap'])) $chap = $_GET['chap'];
	if(!$chap && $tarray['chapters']=='y') $chap = 1;
	
	$fl = substr($tarray['section'],0,1);
	if($fl=='A'||$fl=='E'||$fl=='I'||$fl=='O'||$fl=='U') $n = 'n'; else $n = '';

	echo "\n".'<table width="100%" cellpadding=1 cellspacing=1>';
	echo '<tr><td width="60%" valign=top>'.stripslashes($tarray['description']).(($tarray['section'])?'<div class="abouttext">This is a'.$n.' &quot;<i>'.$tarray['section'].'</i>&quot; problem.</div></i>':'').'</td>';
	echo '<td width="40%" rowspan=2 align=right style="padding:2px"><nobr>';
		newtopic_button();
	
		if(($now_time-$tarray['topic_time'])>10368000 && $userdata['user_id']!=$tarray['topic_poster'] && $userdata['user_level']<4 && !$farray['support']) $tarray['topic_status'] = 2;
		if($tarray['topic_status']==1) echo '<img src="themes/'.$theme.'/reply_locked.gif" alt="locked topic" title="This topic is locked">';
		elseif($tarray['topic_status']>1) echo '';
		else reply_button();

	echo '&nbsp;</nobr></td></tr>'."\n".'<tr><td>';

	$total = $tarray['topic_replies']+1;
	$numpages = ceil($total/$ppp);

	if($tarray['chapters']) include('forums/plugins/chapters.php');

	$gotopage = '';
	if($numpages<15) {
		function gotopage() { global $total,$ppp,$start,$forum,$topic,$gotopage,$clink; //note $clink defined elsewhere
			if(!$gotopage) {
				$gotopage = 'Go to page: [ ';
				if($total>$ppp) { $times = 1; $last_page = $start - $ppp;
					if($start>0) $gotopage.= ' « <i><a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$last_page.$clink.'#endofpage">previous</a></i> ';
					if(($start+$ppp)<$total) {
						$next_page = $start + $ppp;
						$gotopage.= '<i><a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$next_page.$clink.'">next</a></i> » ';
					}
					for($x=0;$x<$total;$x+=$ppp) {
						if($times!=1) $gotopage.= ' ';
						if($start&&$start==$x) $gotopage.= '<b>'.$times.'</b>'; else if($start == 0 && $x == 0) $gotopage.= '<b>1</b>'; else { $gotopage.= '<a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$x.$clink.'">'.$times.'</a>'; }
						$times++;
					}
				} else { $gotopage.= '<b>1</b>'; } $gotopage.= ' ]';
			} return $gotopage;
		}
	} else {
		function gotopage($c) { global $numpages,$ppp,$start,$forum,$topic,$chap,$clink;
			$curpage = ceil(($start+1)/$ppp);
			$gotopage = '<form name="pageselect'.$c.'" action="redir.php" method="post" style="border:0"><input type="hidden" name="topic" value="'.$topic.'"><input type="hidden" name="forum" value="'.$forum.'">'.(($chap)?'<input type="hidden" name="chapter" value="'.$chap.'">':'');
			$gotopage.= 'Go to page <select name="gotopage" onchange="if(document.forms[\'pageselect'.$c.'\'].elements[\'gotopage\'].value>-1) location.href=\'forums.php?forum='.$forum.'&topic='.$topic.'&start=\'+document.forms[\'pageselect'.$c.'\'].elements[\'gotopage\'].value;return true">';
			for($i=1;$i<=$numpages;$i++) $gotopage.= '<option value="'.(($i-1)*$ppp).'"'.(($curpage==$i)?' selected':'').'>'.$i.'</option>';
			$gotopage.= '</select> <input type="submit" name="pagesubmit" value="go" class="submit2">';
			if($curpage!=1) $gotopage.= ' <a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.($ppp*($curpage-2)).$clink.'#endofpage"><b>« prev</b></a>';
			if($curpage!=$numpages) $gotopage.= ' <a href="forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.($ppp*$curpage).$clink.'"><b>next »</b></a>';
			$gotopage.= '</form>';
			return $gotopage;
		}
	}


	echo gotopage(1).'</td><td></td><td></td></tr></table>'."\n\n";
	if(!$start) $start = 0;
	//if(!is_int($start)) $start = 0; if(!is_int($ppp)) $ppp = 20;

	//if($userdata['user_level']==4) {
	//	$numposts = @mysql_result(mysql_query("SELECT COUNT(*) FROM posts WHERE topic_id = '$topic'"),0);
	//	$replies = $numposts - 1;
	//	if($replies!=$tarray['topic_replies']) {
	//		echo 'UPDATING: '.$replies.' replies found, '.$tarray['topic_replies'].' replies for topic listed.';
	//		@mysql_query("UPDATE topics SET topic_replies = '$replies' WHERE topic_id = '$topic' LIMIT 1") or die('Error checking topic replies: '.mysql_error());
	//	}
	//	if(!$numposts) error_die('Topic is empty'.(($userdata['user_level']>2||is_moderator($forum,$userdata['user_id']))?'<br>(<a href="?mode=admin&amp;action=del&amp;forum='.$forum.'&amp;topic='.$topic.'">delete topic</a>?)':''),'');
	//}

	//echo $numposts.' - '.$tarray['topic_replies'];

	$sql = "SELECT p.*, pt.post_text FROM posts p, posts_text pt WHERE p.topic_id = '$topic' $chapsql AND pt.post_id = p.post_id ORDER BY p.post_id LIMIT $start, $ppp";
	if(!$result = mysql_query($sql)) { errorlog(mysql_error(),'loading topic.php with SQL query:'.$sql); error_die('Error querying database, please press refresh',''); }

	@mysql_query("UPDATE topics SET views = views + 1 WHERE topic_id = '$topic' LIMIT 1");

	if(!$curtheme = $userdata['theme']) $curtheme = 'standard';
	if(!$template = file_get_contents('themes/'.$curtheme.'/topic.htm')) { msg('No topic theme file present!','error'); $template = file_get_contents('forums/topic.htm'); }

	$userdetails = array(); $usersession = array(); $newpostmsg = false; $c = 0;

	echo '<P><TABLE width="100%" cellspacing=0 cellpadding=1>'."\n";

while($array = mysql_fetch_array($result)) { $c++;

	if($array['post_time']>$last_visit && !$newpostmsg) { $newpostmsg = true; echo '<tr><td colspan=2>&nbsp;<a name="newposts"><b>New posts since your last visit:</b></a></td></tr>'; }

	//post sorting
	$tcolor = 'blue'; $avatar = ''; $usercontact = ''; $edit = ''; $sig = '';
	$posttime = agotime($array['post_time'],1);

	$message = $array['post_text'];	$message = str_replace("\'","'",$message); $message = str_replace('\"','"',$message);
	if(substr($message,0,3)=='<P>') $message = substr($message,3); if(substr($message,-4,4)=='</P>') $message = substr($message,0,-4);

	if($farray['support']) {
		echo '<tr bgcolor="'.$colors['bg'].'"><td colspan=3><table width="99%" cellpadding=1 cellspacing=1><tr><td width="50%">';
		if($array['type']=='q' && $tarray['answered']=='n') { echo '<b>suggested answer:</b>'; $tcolor = 'yellow'; }
		if($array['type']=='y') { echo '<b>correct/accepted answer:</b>'; $tcolor = 'green'; }
		if($array['type']=='n') { echo '<b>incorrect/rejected answer:</b>'; $tcolor = 'red'; }
		echo '</td><td width="50%" align=right>';
		if(($userdata['user_id']==$tarray['topic_poster'] && $array['type']=='q') OR (($userdata['user_level']>2 OR is_moderator($forum,$userdata['user_id'])) && $array['type'])) {
			echo 'Does this answer the question? <input type="submit" value="yes" class="submit3" onclick="self.location=\'?forum='.$forum.'&amp;topic='.$topic.'&amp;yes='.$array['post_id'].'\'">';
			echo '<input type="submit" value="no" class="submit3" onclick="self.location=\'?forum='.$forum.'&amp;topic='.$topic.'&amp;no='.$array['post_id'].'\'">';
			echo '<input type="submit" value="reset" class="submit3" onclick="self.location=\'?forum='.$forum.'&amp;topic='.$topic.'&amp;reset='.$array['post_id'].'\'">';
		}
		echo '</td></tr></table></TD></TR>';
	}

	//user info; check line below isset if problems
	if(!isset($userdetails[$array['poster_id']])) $userdetails[$array['poster_id']] = mysql_fetch_array(mysql_query("SELECT p.user_sig,p.snarkpoints,p.steam,p.aim,p.yim,p.msnm,p.icq,p.website,p.hidestuff,p.username,p.avatar,p.avatar_text,p.location,p.user_email,p.user_rating,u.user_level FROM users_profile p, users u WHERE p.user_id = '$array[poster_id]' AND u.user_id = p.user_id LIMIT 1"));
	if(!$userdetails[$array['poster_id']]) { $username = '<i>Deleted user</i>'; } else {
		list($hideemail,$showrating) = split(',',$userdetails[$array['poster_id']]['hidestuff']);
		if($userdetails[$array['poster_id']]['user_level']>1) $class = $colors['class_moduser']; else $class = $colors['class_user'];
		$username = '<a href="users.php?name='.$userdetails[$array['poster_id']]['username'].'"'.$class.'>'.$userdetails[$array['poster_id']]['username'].'</a>';
			if($array['poster_id']==1) $username = '<span style="filter:glow(color=#669900, strength=3); height=12px; color: #00FF00">'.$username.'</span>';
		if($userdetails[$array['poster_id']]['avatar']) $avatar = '<a href="users.php?name='.$userdetails[$array['poster_id']]['username'].'"><img src="userimages/avatar'.$array['poster_id'].'.'.$userdetails[$array['poster_id']]['avatar'].'" border=0></a>';
		if($userdetails[$array['poster_id']]['avatar_text']) { if($avatar) $avatar.='<br>'; $avatar .= '<font color="'.$colors['text'].'"><b>'.$userdetails[$array['poster_id']]['avatar_text'].'</b></font>'; }
		if($userdetails[$array['poster_id']]['location']) $from = '<br><b>from</b> '.$userdetails[$array['poster_id']]['location'].'<br>'; else $from = '<br>';

		//if($userdetails[$array['poster_id']]['user_rating']>0 && $userdetails[$array['poster_id']]['user_rating']<2) $avatar = '<img src="images/moron.gif"><br><b>n00b</b>';

		if($userdata) {
			$usercontact = '<a href="cp.php?mode=compose&amp;to='.$userdetails[$array['poster_id']]['username'].'&amp;topic='.$topic.'&amp;post='.$array['post_id'].'"><img src="images/gfx_pm.gif" alt="Send this user a private message" align=absmiddle border=0></a> ';
			if(!$hideemail) $usercontact.= '<a href="mailto:'.$userdetails[$array['poster_id']]['user_email'].'"><img src="images/email.gif" title="E-mail this user" align=middle border=0></a> ';
			if($userdetails[$array['poster_id']]['icq']) $usercontact.= '<a href="http://wwp.icq.com/whitepages/about_me/1,,,00.html?Uin='.$userdetails[$array['poster_id']]['icq'].'"><img src="images/forum_icq.gif" border=0 align="texttop" width=16 height=16></a> '; //http://web.icq.com/whitepages/online?icq='.$userdetails[$array['poster_id']]['icq'].'&img=5
			if($userdetails[$array['poster_id']]['msnm']) $usercontact.= '<a href="http://members.msn.com/default.msnw?mem='.$userdetails[$array['poster_id']]['msnm'].'" target="_blank"><img src="themes/'.$images['moddir'].'/forum_msn.gif" border="0" align="texttop" height=15></a> ';
			if($userdetails[$array['poster_id']]['yim']) $usercontact.= '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.$userdetails[$array['poster_id']]['yim'].'&.src=pg"><img src="images/forum_yim.gif" border="0" align="texttop" height=15></a> ';
			if($userdetails[$array['poster_id']]['aim']) $usercontact.= '<a href="aim:goim?screenname='.$userdetails[$array['poster_id']]['aim'].'&amp;message=Hi+'.$userdetails[$array['poster_id']]['username'].'.+Are+you+there?"><img src="images/forum_aim.gif" border="0" align="texttop" height=16></a> ';
			if($userdetails[$array['poster_id']]['steam']) $usercontact.= '<img src="themes/'.$images['moddir'].'/forum_steam.gif" align="texttop" alt="Steam: '.$userdetails[$array['poster_id']]['steam'].'" title="Steam: '.$userdetails[$array['poster_id']]['steam'].'" height=16> ';

		} if($userdetails[$array['poster_id']]['website']) $usercontact.='<a href="'.$userdetails[$array['poster_id']]['website'].'" target="_blank"><img src="images/gfx_website.gif" align=texttop border=0></a>';

		if(!isset($usersession[$array['poster_id']])) { if(@mysql_result(mysql_query("SELECT user_id FROM sessions WHERE user_id = '$array[poster_id]' LIMIT 1"),0)) $usersession[$array['poster_id']] = 'user <font color="'.$colors['yes'].'">online</font>'; else $usersession[$array['poster_id']] = 'user offline'; }
	}

	$reply = '<a href="forums.php?mode=reply&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;reply='.$array['post_id'].(($chap)?'&amp;chap='.$chap:'').$java.'"'.$colors['class_forumbutton'].'>'.$images['forum_reply'].'</a><a href="forums.php?mode=reply&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;quote='.$array['post_id'].(($chap)?'&amp;chap='.$chap:'').$java.'"'.$colors['class_forumbutton'].'>'.$images['forum_quote'].'</a>&nbsp;';
	if($userdata['user_level']>2 || is_moderator($forum,$userdata['user_id']) || $userdata['user_id']==$array['poster_id']) $edit = '<a href="forums.php?mode=editpost&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;edit='.$array['post_id'].$java.'"'.$colors['class_forumbutton'].'>'.$images['forum_edit'].'</a>';
	if($tarray['topic_status']>0) { if($userdata['user_level']<4) { $edit = ''; $reply = ''; } }
	if($userdata['user_level']>2 || is_moderator($forum,$userdata['user_id'])) $ip = '<a href="forums.php?mode=admin&amp;action=viewip&amp;post='.$array['post_id'].'">'.$images['forum_ip'].'</a>';

	echo '<!post>';

	if(isset($_GET['highlight'])) {
	        $highlight = trim(htmlspecialchars($_GET['highlight']));
		$highlightarray = split(',',$highlight); $sizeof = sizeof($highlightarray);
		for($i=0;$i<$sizeof;$i++) $message = preg_replace('|\b('.quotemeta($highlightarray[$i]).')\b|iU', '<b style="background-color:'.$colors['highlight'].'"><font color="'.$colors['text'].'">\\1</font></b>', $message);
	}

	if($c==1 && !$start && $tarray['answered']=='p') {
		$message = str_replace('[addsig]','',$message);
		include('forums/poll.php'); 
	}

	if($array['sig']) {
		if($sig = $userdetails[$array['poster_id']]['user_sig']) {
			$sig = stripslashes($sig);
			$sig = '<p><hr size=0>'.$sig.'</a>';
		}
	}
	//legacy, remove in snarkforums
	$message = str_replace('[addsig]','',$message);

	if($array['post_time']<$last_visit) { 
		$visible = 'visibility:visible;display:inline';
		$invisible = 'visibility:invisible;display:none';
	} else {
		$visible = 'visibility:invisible;display:none';
		$invisible = 'visibility:visible;display:inline';
	}

	if($array['edited']>0) $message .= '<div class="abouttext" style="text-align:right;margin-top:4px"><i>post last edited '.agotime($array['edited'],'return').'</i></div>';

	if($array['post_id']==$tarray['last_post_id'] || $c==$ppp) { $username = $username.'<a name="endofpage"></a>'; if(!$newpostmsg) $username = $username.='<a name="newposts"></a>'; }

	$post = assign_vars(array(
		'{THEME}'=>$curtheme,
		'{C_LIGHT}'=>$colors['lighttext'],'{BGCOLOR}'=>$colors['bg'],
		'{IMG}'=>'themes/'.$img_path.'/table_'.$tcolor,
		'{USERNAME}'=>$username,
		'{USERCONTACT}'=>$usercontact,
		'{USERID}'=>$array['poster_id'],'{SNARKMARKS}'=>$userdetails[$array['poster_id']]['snarkpoints'],'{FROM}'=>$from,
		'{AVATAR}'=>$avatar,'{USER_ONLINE}'=>$usersession[$array['poster_id']],
		'{TOPIC}'=>'forum='.$forum.'&amp;topic='.$topic,
		'{POST_TEXT}'=>$message,'{POST_SIG}'=>$sig,
		'{POST_REPLY}'=>$reply,	'{POST_EDIT}'=>$edit,'{POST_IP}'=>$ip,
		'{POST_TIME}'=>$posttime,
		'{POST_ID}'=>$array['post_id'],
	),$template);

	echo $post;

	if($tarray['answered']=='y' && $c==1 && !$start) {
		$fpid = @mysql_result(mysql_query("SELECT * FROM posts WHERE topic_id = '$topic' AND type = 'y' ORDER BY post_id ASC LIMIT 1"),0);
		if($fpid) {
			if(($fpid-$ppp)>$array['post_id']) {
				$fpprevposts = @mysql_result(mysql_query("SELECT COUNT(post_id) AS total FROM posts WHERE topic_id = '$topic' AND post_id < '$fpid'"),0);
				if($fpprevposts>$ppp) $link = 'forums.php?forum='.$forum.'&amp;topic='.$topic.'&amp;start='.(floor($fpprevposts/$ppp)*$ppp);
			}
		echo '<tr><td colspan=2>&nbsp;<b><a href="'.$link.'#post'.$fpid.'">jump to first solution to this problem »</a></b></td></tr>';
		} else echo '<tr><td colspan=2>&nbsp;<b>This topic has been marked as answered or \'case closed\' by the moderators.</b></td></tr>';
	}

	echo '<tr><td height=20>&nbsp;</td></tr>'."\n\n";

} 	if(!$c) { errorlog('loading topic ('.$_SERVER['QUERY_STRING'].')',$sql); header('Location: forums.php?forum='.$forum.'&amp;error=No+posts+found!'); die; }

?>

</table>

<table width="99%" align=center cellspacing=0 cellpadding=2><tr>
	<td width="<?=(($config['topicjumpbox'])?'43':'66')?>%" valign=top><?=gotopage(2)?></td>

	<?php if($config['topicjumpbox']) { echo '<td width="23%" align=center>'; make_jumpbox(''); echo '</td>'; } ?>

	<td align=center width="33%" valign=top><? if($userdata) {
	        list($t1,$t2,$t3) = explode(',',$simthemes);
	        if($t1) echo '&nbsp;<a href="forums.php?changemode='.$t1.'&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$start.'"><img src="themes/topic_'.$t1.'.gif" align=texttop border=0> '.$t1.'</a>&nbsp;';
	        if($t2) echo '&nbsp;<a href="forums.php?changemode='.$t2.'&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$start.'"><img src="themes/topic_'.$t2.'.gif" align=texttop border=0> '.$t2.'</a>&nbsp;';
	        if($t3) echo '&nbsp;<a href="forums.php?changemode='.$t3.'&amp;forum='.$forum.'&amp;topic='.$topic.'&amp;start='.$start.'"><img src="themes/topic_'.$t3.'.gif" align=texttop border=0> '.$t3.'</a>&nbsp;';
		if($t1||$t2||$t3) echo '<br>';
	} ?><a href="printable.php?section=forums&amp;id=<?=$topic?>"><img src="images/gfx_print.gif" border=0 align=texttop> print topic</a></td>
	</tr>

<?php if($tarray['topic_status']<1) { if($userdata) { ?>

<?php if(browser()!='none') { $showhide = ' - <a href="javascript:void(0)" onclick="showhide(\'dbox\');showhide(\'hbox\')">show/hide</a>'; ?>
<script language="javascript" type="text/javascript">
function showhide(id) {
	divs = document.getElementsByTagName('div');
	if(divs[id].style.visibility == 'hidden' || !divs[id].style.visibility) {
		divs[id].style.visibility = 'visible';
		divs[id].style.display = 'inline';
	} else {
		divs[id].style.visibility = 'hidden';
		divs[id].style.display = 'none';
	}

	if(id=='hbox') document.getElementById('message').contentWindow.document.designMode = "on";
}
</script>
<?php } 

	if($userdata['dailypostlimit']>0 && $userdata['dailypostlimit']-$dppostmade<1) $showhide = ' - <a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=postwarning\')"><font color="'.$colors['no'].'">You can\'t make any more posts today!</font></a>';

?>

	<tr><td colspan=3>
		<form action="forums.php" method="post" name="frmAddMessage" onsubmit="return checkform('reply')">
		<table width="90%" align=center><tr><td>
		<fieldset>
		<legend><a name="box">quick reply</a><?=$showhide?></legend>
<? if(browser()!='none') { ?>
			<div id="dbox" style="visibility:hidden; display:none">
<? } include('forums/browsers/'.browser().'.php'); ?>
			<table width="100%" cellpadding=1 cellspacing=1 style="font-size:8pt"<?=$boxmouseover?>><tr>
			<td width="20%" valign=top>
				<p><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode&amp;theme=<?=$theme?>','yes')">BBCode</a> enabled&nbsp;&nbsp;
				<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode')">Images</a> enabled
				<br>HTML disabled
				<?php   $e = '';
					if($userdata['addsig']) echo '<br>Add signature<input type="hidden" name="sig" value="add">';
					if($java) echo '<p><img src="images/gfx_save.gif" alt="save" align=left><div style="position:relative;left:0"><a href="cp.php?mode=preferences">Save posting preference</a></div>';
					if(browser()=='ie') {
					        echo '<p><img src="themes/'.$images['moddir'].'/gfx_open.gif" alt="restore" align=left><div style="position:relative;left:0"><a href="javascript:void(0)" onclick="if(toUpdate) document.getElementById(\'message\').contentWindow.document.body.innerHTML = toUpdate; else alert(\'No posts have been saved!\')">Click to restore lost post</a></div>';
						$e = '<p>ENTER starts a new paragraph, SHIFT+ENTER starts a new line.';
					}
					if(browser()=='ie'||browser()=='moz') {
					        echo '<p><img src="themes/'.$images['moddir'].'/post_button_spelling.gif" alt="undo" align=left><div id="action" style="position:relative;left:0"><a href="javascript:void(0)" onClick="if(confirm(\'This is still in beta and may delete your message or some formatting, ok to proceed?\')){setObjToCheck(\'message\');spellCheck();}">Check spelling</a></div><br><div id="status">&nbsp;</div>(still in beta, beware!)';
						$e .= '<p>CTRL+SHIFT+Z removes formatting of the selected text.';
					}
				?>
				<p>Press CTRL+Z to undo changes, and CTRL+Y to redo.<?=$e?>
			</td><td width="80%">
				<?=$formatting?>
				<div id="suggestions" class="suggestions">suggestions</div>
				<?=$box?>
				<?php 
					if($tarray['answered']=='n') echo '<div align=left style="padding:2px">Please select what type of response this is. You will not be penalised for posting incorrect solutions! If you are posting a link to a solution, please summarise what it says (in case that website goes offline)</div><select name="answer"><option value="2">This is a comment on the problem<option value="1">This is a possible solution to the problem</select> '."\n";
					if($is_editing_forum && $userdata['user_id']==$tarray['topic_poster']) echo '<div align=left style="padding:2px"><b><font color="'.$colors['no'].'">Please read before posting:</font></b><br>'.$farray['forum_text'].'</div>';
				?>
					<div><input type="submit" name="submit" value="submit" class="submit"></div>
				</div>
			</td></tr>
		<?php if($tarray['answered']=='p' && $userdata) {
			$array = @mysql_fetch_array(mysql_query("SELECT * FROM polls WHERE topic_id = '$topic' LIMIT 1")); $voted = false; $maxvotes = 0;
			for($i=1;$i<=6;$i++) { if(substr_count($array['voted'.$i],','.$userdata['user_id'].',')) $voted = true; $numvotes[$i] = substr_count($array['voted'.$i],',')-1; if($numvotes[$i]>$maxvotes) $maxvotes = $numvotes[$i]; }
			if($voted) { 
				echo "\n".'<tr><td></td><td>';
				echo '<fieldset><legend>Poll results</legend><table width="100%" cellspacing=0 cellpadding=0>';
				$pollcolor = array('','red','orange','yellow','green','blue','purple'); $basewidth = 300;
				for($i=1;$i<=6;$i++) { if($array['option'.$i]) {
					echo "\n".'<tr><td width="30%">'.$array['option'.$i].':</td><td width="70%">';
					echo '<table width="'.floor($basewidth*($numvotes[$i]/$maxvotes)).'" bgcolor="'.$pollcolor[$i].'" align=left><tr><td><img src="images/null.gif" height=8 width=1></td></tr></table> '.$numvotes[$i];
					echo '</td></tr>';
				} }
				echo '</table></fieldset></td></tr>';
			} else echo 'You have not yet voted in this poll';
		} ?>
			</tr></table>
<? if(browser()!='none') { ?>
			</div>
			<div id="hbox" style="visibility:visible; display:inline"><table width="700"><tr><td><a href="#box" onclick="showhide('dbox');showhide('hbox')">Click here to reply to this message</a></td></tr></table></div>
<? } ?>
		</fieldset>
		</td></tr></table>
		<input type="hidden" name="action" value="reply"><input type="hidden" name="forum" value="<?=$forum?>"><input type="hidden" name="topic" value="<?=$topic?>"><?=(($chap)?'<input type="hidden" name="chapid" value="'.$chap.'">':'')?>
		</form>

<?php	} } 

	if($tarray['topic_status']==2) echo '	<tr><td colspan=3 align=center height=50><b><font color="'.$colors['no'].'">You cannot reply to this topic as it has not been replied to for over 4 months</font></b></td></tr>';
	if($tarray['topic_status']==3) echo '	<tr><td colspan=3 align=center height=50><b><font color="'.$colors['no'].'">This map has been finished so this thread is automatically locked. Please leave your comments in its <a href="maps.php?map='.$tarray['map'].'">profile!</font></b></td></tr>';

	echo '</td></tr>';

	if ($userdata['user_level']>2 OR $ismod) {
	        echo '<tr><td colspan=3 align=center>';
		if($tarray['topic_status'] != 1) echo '<a href="?mode=admin&amp;action=lock&amp;forum='.$forum.'&amp;topic='.$topic.'"><IMG SRC="images/topic_lock.gif" BORDER=0></a> '; else echo '<a href="?mode=admin&amp;action=unlock&amp;forum='.$forum.'&amp;topic='.$topic.'"><IMG SRC="images/topic_unlock.gif" ALT="Unlock topic" BORDER=0></a> ';
		echo '<a href="?mode=admin&amp;action=sticky&amp;topic='.$topic.'&amp;forum='.$forum.'"><img src="images/topic_sticky.gif" alt="Make sticky/unsticky" border=0></a> ';
		echo '<a href="?mode=admin&amp;action=move&amp;forum='.$forum.'&amp;topic='.$topic.'"><IMG SRC="images/topic_move.gif" alt="Move topic" BORDER=0></a> ';
		echo '<a href="?mode=admin&amp;action=del&amp;forum='.$forum.'&amp;topic='.$topic.'"><IMG SRC="images/topic_del.gif" alt="Delete topic" BORDER=0></a>';
		if($tarray['answered']=='n') echo ' <a href="?mode=admin&amp;action=answer&amp;topic='.$topic.'&amp;forum='.$forum.'"><img src="images/topic_tick.gif" alt="Make this topic answered" border=0></a>';
		if($tarray['chapters']) echo ' <a href="?mode=admin&amp;action=fix_chapters&amp;topic='.$topic.'&amp;forum='.$forum.'"><img src="images/admin_chapters.gif" title="Check and fix chapters" border=0></a>';
		echo '</td></tr>';
	} ?>
	
	</td></tr>
</table>
