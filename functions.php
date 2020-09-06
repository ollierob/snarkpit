<?php

function footer() { global $starttime,$hidebar,$colors,$userdata; include('footer.php'); }

function new_session($userid, $remote_ip, $lifespan) { global $now_time,$userdata;

	if(!$_SERVER['REMOTE_ADDR']) { include('header.php'); error_die('Your IP is hidden- we need to know what it is in order to moderate this site and for cookies to work properly.'); }
	if($userid<1) error_die('Cannot login (invalid user_id)');

	$cur_ip = str_replace('.','',$_SERVER['REMOTE_ADDR']);
	$sql = mysql_query("SELECT ban_ip FROM banlist WHERE ban_ip!=''");
	while($array = mysql_fetch_array($sql)) {
		$ban_ip = str_replace('.', '', $array['ban_ip']);
		if($ban_ip == $cur_ip OR substr_count($cur_ip, $ban_ip)) error_die('Your IP (or it\'s \'range\') has been banned from logging into this website.');
	}

	mt_srand((double)microtime()*1000000); $sessid = mt_rand();      
	$expirytime = (string) ($now_time - $lifespan);

	@mysql_query("DELETE FROM sessions WHERE (start_time < $expirytime)");
	$ssql = mysql_query("SELECT * FROM sessions WHERE user_id = '$userid' LIMIT 1");
	if(!$q = @mysql_result($ssql,0)) {
		$newsession = 1;
		$sql = "INSERT INTO sessions (sess_id,user_id,start_time,remote_ip,user_browsing) VALUES ('$sessid','$userid','$now_time','$remote_ip','Just logged on')";
	} else {
		$newsession = 0;
		$sql = "UPDATE sessions SET sess_id = '$sessid', start_time = '$now_time', remote_ip = '$remote_ip' WHERE user_id = '$userid'";
	} if(!$r = mysql_query($sql)) errorlog(mysql_error(),'session management ('.$newsession.')');

	if($newsession) {
	        @mysql_query("UPDATE users SET user_ip = '$_SERVER[REMOTE_ADDR]', last_seen = '$now_time' WHERE user_id = '$userdata[user_id]' LIMIT 1");
		calcsnarkpoints($userdata['user_id'],false);
	}

	return $sessid;

}

function update_session_time($sessid) { global $now_time;
	if(!mysql_query("UPDATE sessions SET start_time = '$now_time' WHERE sess_id = '$sessid' LIMIT 1")) { errorlog(mysql_error(),'updating session'); error_die('Error updating session DB, please refresh the page and try again'); }
	return 1;
}

function set_session_cookie($sessid, $cookietime, $cookiename, $cookiepath, $cookiedomain) { global $now_time;
	setcookie($cookiename,$sessid,($now_time+$cookietime),$cookiepath,$cookiedomain);
}

function check_autologin($username, $password) { global $cookiepath,$cookiedomain;
	$username = addslashes($username);
	$result = mysql_result(mysql_query("SELECT user_id FROM users WHERE username = '$username' AND password = '$password' LIMIT 1"),0);
	if(!$result) {
		while(list($key,$val)=each($_COOKIE)) setcookie($key,'',time()-10000,$cookiepath,$cookiedomain);
		die('There was a problem logging you in, cookies have been deleted- please try again. If the problem persists, clear your SnarkPit cookies, and don\'t try to log in with an incorrect username/password.');
	} else return true;
}

function get_userdata_from_id($userid) {
	$myrow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id = '$userid' LIMIT 1"));
	return($myrow);
}

function get_userdata($username) {
	$username = addslashes($username);
	$sql = mysql_query("SELECT * FROM users WHERE username = '$username' AND user_level != -1 LIMIT 1");
	if(!$myrow = mysql_fetch_array($sql)) die('Error getting user details');
	return($myrow);
}

function error_die($msg,$sidebar) { global $userdata,$starttime,$url_site,$headerloaded,$last_visit,$forumimg;
	if(!$headerloaded) {
		if(!$theme = $userdata['theme']) $theme = 'standard';
		include('themes/'.$theme.'.php');
		include('header.php');
		if($config['forumsidebar']) include($sidebar.'/sidebar.php');
	}
	echo '<p><table border=0 cellpadding=1 cellspacing=0 align=center width="100%"><tr><td align=center><font size=4 face=verdana>There was a problem</font>';
	if(!$msg) $f = '<i>Unspecified error- please contact the site admin immediately</i>';
	echo '<p><font face=Verdana size=2>'.$msg.'</font></p></td></tr></table><p align=center><img src="'.$url_site.'images/deadsnark.gif">'; 

	footer();
}

function subtitle($text,$return) { global $colors;
	if(!$text) $text = ' ';
	$t = '<h1>'.$text.'</h1>';
	if($return) return $t; else echo $t;
}

function title($text,$help) { global $page,$mode,$userdata,$forum,$topic,$theme;
	$localmode = $mode; $localpage = $page; if($localpage) $localmode=$localpage;
	if(!$localmode) { if($forum) $localmode = 'forum'; if($topic) $localmode = 'topic'; }

	if(isset($userdata['hidehelp']) && $userdata['hidehelp']!=1 AND $help!='none') echo '<a onclick="popwin(\'popup.php?mode=help_'.$help.'&amp;act='.$localmode.'\')"><img src="themes/'.$theme.'/help.gif" align=right style="cursor: hand" alt="help" title="Click here for help"></a>';
	echo '<span class="title">'.$text.'</span>'."\n<p>";
}

function userdetails($userid,$class,$return,$nolink) { global $users;
	if($class && $class!='notbold') $classl = ' class='.$class; else $classl = '';
	if(isset($users[$userid])) $fname = $users[$userid]; else {
		$username = @mysql_result(mysql_query("SELECT `username` FROM `users` WHERE `user_id` = '$userid' LIMIT 1"),0);
		$users[$userid] = stripslashes($username);
	} 
	if($nolink) { if($return) return $users[$userid]; else echo $users[$userid]; } else {
		if($users[$userid]) $muff = '<a href="users.php?name='.$users[$userid].'"'.$classl.'>'.$users[$userid].'</a>'; else { if($userid/1==0 && $userid) $muff = $userid; else $muff = '?'; }
		if($class!='notbold') $muff = '<b>'.$muff.'</b>'; 
		if($return) return $muff; else echo $muff;
	}
}

function calcsnarkpoints($userid,$echo) {
                                        	
        $mapdownloads = 0; $maptotal = 0; $totalmaps = 0;
	$totaltuts = 0; $tuttotal = 0;

	$sql = mysql_query("SELECT answered,posts,comments,user_rating,maps,profile_count,snarkpoints FROM users_profile WHERE user_id = '$userid' LIMIT 1"); $uparray = mysql_fetch_array($sql);
	$ratings = mysql_num_rows(mysql_query("SELECT rating FROM users_rating WHERE to_id = '$userid'"));
	$usertopics = mysql_result(mysql_query("SELECT COUNT(topic_id) FROM topics WHERE topic_poster = '$userid'"),0);
	$sql = mysql_query("SELECT rating FROM articles WHERE user_id = '$userid' AND section = 'editing' AND rating!=''");
		while($array = mysql_fetch_array($sql)) {
			$tuttotal = $tuttotal + $array['rating'];
			$totaltuts++;
		}
		if($totaltuts) $tutaverage = ((floor($tuttotal*10/$totaltuts))/10); else $tutaverage = 0;

	$sql = mysql_query("SELECT rating,downloads FROM maps WHERE user_id = '$userid' AND status > -1");
		while($array = mysql_fetch_array($sql)) {
			if($array['rating']) {
				$maptotal = $maptotal + $array['rating'];
				$ratedmaps++;
			}
			if($array['downloads']) $mapdownloads+=$array['downloads'];
		}
		if($ratedmaps) $mapaverage = ((floor($maptotal*10/$ratedmaps))/10); else $mapaverage = 3;

	$rating = round((40*$tutaverage*$totaltuts) + (10*$uparray['answered']) + (($uparray['posts']/2 +(2*$usertopics)+(2*$uparray['comments']))*($uparray['user_rating']/10)) + ((($mapaverage*$mapaverage)+3)*$uparray['maps']) + ($mapdownloads/10) + floor($uparray['profile_count']/50));
	if($rating!=$uparray['snarkpoints']) @mysql_query("UPDATE users_profile SET snarkpoints = $rating WHERE user_id = '$userid' LIMIT 1");

	if($echo) { ?>
		listed <?=$uparray['maps']?> map<?=($uparray['maps']==1)?'':'s'; if($ratedmaps) echo ' (average '.$mapaverage.'/10)'; ?>, made <?=$uparray['comments']?> comment<?=($uparray['comments']==1)?'':'s';?> and written <?=$totaltuts?> tutorial<?=($totaltuts==1)?'':'s'; if($tutaverage) echo ' (average '.$tutaverage.'/10)'; ?><br>
		<span style="position:relative;float:right;font-size:24pt;margin-right:10px;margin-top:4px"><a href="index.php?page=faq#snarkmarks" class="white"><span style="font-size:10pt">SnarkMarks:</span><br><?=$uparray['snarkpoints']?></a></span>
		started <?=$usertopics?> new topics, made <?=$uparray['posts']?> forum posts and solved <?=$uparray['answered']?> editing problems<br>
		had <?=$uparray['profile_count']?> profile hits and been rated <?=$uparray['user_rating']?>/5 by <?=$ratings?> other users
	<?php } return $uparray;
}

function userrating($user_id) { global $uparray,$showrating;
	list($email,$showrating) = split(',',$uparray['hidestuff']);
	$numratings = mysql_result(mysql_query("SELECT COUNT(rating) FROM users_rating WHERE to_id = '$uparray[user_id]'"),0);
	if($showrating==0) { if($numratings>0) return 'rated <b>'.$uparray['user_rating'].'/5</b> by '.$numratings.' users'; else return 'not yet rated'; }
	if($showrating==1) return 'rating is hidden';
	if($showrating==2) return 'user can\'t be rated';
}

function agotime($otime,$return) { global $now_time,$userdata;

	if(!$userdata || $userdata['timezone']=='ago') {
		$time = $now_time - $otime;
		if($time<60) $echo = 'within the last minute';
		elseif($time<3600) $echo = floor($time/60).' mins ago';
		elseif($time<86400) $echo = floor($time/3600).' hrs '.floor(($time-(floor($time/3600)*3600))/60).' mins ago';
		elseif($time<2678400) { $days = floor($time/86400); $hours = floor(($time-($days*86400))/3600); $echo = $days.' day'; if($days!=1) $echo .= 's'; $echo .= ' '.$hours.' hr'; if($hours!=1) $echo .= 's'; $echo .= ' ago'; }
		else $echo = 'on '.gmdate("jS M Y",$otime);
		if(!$otime) $echo = '';
	} else {
		$echo = date("H:i, jS M Y",$otime+(3600*$userdata['timezone']));       	
	}
	if($return) return $echo; else echo $echo;
}

function tracker($location,$url) { global $userdata, $page, $mode, $name, $forum, $topic;
	if($userdata) {
	if(!$url) {
		$xtra = '';
		if($mode) $xtra = '?mode='.$mode;
		if($page) $xtra = '?page='.$page;
		if($forum) $xtra = '?forum='.$forum;
		if($topic) $xtra.='&amp;topic='.$topic;
		$url = $_SERVER['PHP_SELF'].$xtra;
	} $pagelocation = addslashes('<a href="'.$url.'">'.$location.'</a>');
	if(!$sql = mysql_query("UPDATE sessions SET user_browsing = '$pagelocation' WHERE user_id = '$userdata[user_id]' LIMIT 1")) errorlog(mysql_error(),'tracker');
} }

function getcomments($type,$id,$url,$subtype,$width) {
	global $userdata,$array,$averagerating,$page,$numcomments,$theme,$thread,$colors,$images,$commentbox,$commentboxwidth,$maparray,$nonewcomments,$numrated,$now_time,$d;

	$averagerating = ''; if($subtype) $subtype = "AND subtype = '$subtype'"; $c = 0; $numrated = 0;

	if($deletecomment = $_GET['deletecomment']) {
		if(mysql_result(mysql_query("SELECT id FROM comments WHERE id = '$deletecomment' LIMIT 1"),0)) {
		$origuserid = mysql_result(mysql_query("SELECT user_id FROM comments WHERE id = '$deletecomment' LIMIT 1"),0);
		if($origuserid==$userdata['user_id'] OR $userdata['user_level']>2) { 
			@mysql_query("DELETE FROM comments WHERE id = '$deletecomment' LIMIT 1");
			@mysql_query("UPDATE users_profile SET comments = comments-1 WHERE user_id = '$origuserid' LIMIT 1");
			if($page=='files') @mysql_query("UPDATE files SET comments = comments - 1 WHERE file_id = '$id' LIMIT 1");
			$msg = addslashes('This is an automated message to let you know that one of your comments for <a href="javascript:void(0)" onclick="opener.location=\''.$url.'\'">this '.$type.'</a> has been deleted, probably because it was an inappropriate comment to make (thought it might have just been out of date!). Please read the rules next time before commenting, especially the part where it says <b>Please only post helpful comments such as corrections or additions</b>.');
			@mysql_query("INSERT INTO messages (from_id,to_id,time,subject,text,status) VALUES ('-1','$origuserid','$now_time','Automated message- comment deleted','$msg','1')");
		}
	} }
	
	$oldcomment = '<tr><td colspan=2><span style="color:'.$colors['info'].'"><b>This tutorial has been updated since the above comments were made.</b></span></td></tr>';

	$comments = "\n".'<!--comments--><table width="'.(($width)?$width:'80').'%" cellpadding=2 cellspacing=0><tr><td><img src="images/null.gif" width="10" height=1></td><td width="100%"></td></tr>';

	$sql = mysql_query("SELECT * FROM comments WHERE type = '$type' AND article_id = '$id' $subtype ORDER BY id");
	while($carray = mysql_fetch_array($sql)) { $numcomments++; $c++;
		if($array['edited']>0 && $carray['date']>$array['edited']) { $comments .= $oldcomment; $oldcomment = ''; }
		$rateme = 'rated'.$carray['rating'];
		if($userdata['user_id']==$carray['user_id'] OR $userdata['user_level']>2) $editlink = ' <a href="cp.php?mode=newcomment&amp;type='.$type.'&amp;id='.$id.'&amp;edit='.$carray['id'].'"><img src="images/gfx_edit.gif" border=0 align=absmiddle alt="Edit comment" title="Edit comment"></a> <a href="'.$url.'&amp;deletecomment='.$carray['id'].'#comments"><img src="themes/'.$images['moddir'].'/gfx_delete.gif" border=0 align=absmiddle alt="Delete comment" title="Delete comment" onclick="return confirm(\'Are you sure you want to delete this comment?\')"></a>'; else $editlink = '';
		$comments .= '<tr><td colspan=2>'.userdetails($carray['user_id'],'','return','').' on '.gmdate("jS M Y",$carray['date']).$editlink.'</tr>';
		$comments .= "\n".'<tr><td></td><td class=abouttext>'.stripslashes($carray['text']);
		if($carray['altered']==1) $comments .= '<div style="color:'.$colors['no'].'"><i><b>Note:</b> This comment has been edited by the author/moderators</i></div>';
		if($carray['rating']) { $comments .= '<br><div align=right><font color="'.$colors['info'].'"><b>rated '.$carray['rating'].'/10</b></font></div>'; $numrated++; $sum=$sum+$carray['rating']; }
		$comments .= '</tr><tr><td height=10></tr>';
	}

	if(!$c) $comments .=  '<tr><td colspan=2>No comments posted yet</td></tr>';
	if($c && $oldcomment && $array['edited']>0) $comments .=  $oldcomment;
	if($c && $sum) {
		$averagerating = floor($sum*10/$numrated)/10;
		echo '<b>Average rating: '.$averagerating.'/10</b>'; //<tr><td colspan=2>
	}

	if($nonewcomments) {
		//echo $nonewcomments.$comments;
		@msg($nonewcomments,'warning');
		echo $comments;
	} else {

		echo $comments;

		if($userdata && $commentbox==true) {
			$refer = str_replace('deletecomment=','',str_replace('#comments','',$_SERVER['REQUEST_URI']));
			echo '<tr><td colspan=2><a name="commentbox"></a><fieldset><legend><a name="addcomment">new comment</a></legend>';
			include('cp/newcomment.php');
			echo '</fieldset></td></tr>';
		}

		if($userdata && $commentbox!=true) {
			echo '<tr><td colspan=2 align=right>';
			if($type=='map' && $maparray['status']==100 && $maparray['user_id']!=$userdata['user_id']) echo '<a href="cp.php?mode=reviews&amp;map='.$id.'"><img src="themes/'.$theme.'/newreview.jpg" border=0></a> ';
			echo '<a href="cp.php?mode=newcomment&amp;type='.$type.'&amp;id='.$id.(($thread)?'&amp;thread='.$thread:'').'"><img src="themes/'.$theme.'/newcomment.gif" border=0></a>';
			echo '</td></tr>';
		}
	}

	echo '</table>';

}

function screenshot($map,$numthumbs,$img) { global $theme;
	return '<a href="javascript:void(0)" onclick="popwin(\'screenshot.php?map='.$map.'&amp;theme='.$theme.'&amp;numthumbs='.$numthumbs.'&amp;img='.$img.'\')" onMouseOver="window.status=\'Click to view full sized screenshot (popup window)\'; return true" onMouseOut="window.status=\'\'">';
}

function errorlog($event,$location,$loc) { global $userdata; if(!$username = $userdata['username']) $username = '(Unknown)';
	if($location) $event.= ' ('.$location.')';
	$write = $username.' on '.gmdate("d/m/y").': '.$event."\r\n";
	if(!$loc) $loc = 'errorlog.txt'; else $loc = 'admin/log.txt';
	$fopen = fopen($loc,'a'); if(!$fopen) echo 'Could not open error file';
	$filewrite = fwrite($fopen,$write);
	fclose($fopen);
}

function msg($message,$type,$nop,$width,$title,$element) { global $colors;

	$message = stripslashes($message);

	$s = ''; $t = '';
	if($type=='error') { $c = 'warning'; $t = '<b>Error:</b> '; }
	if($type=='warning') { $c = 'warning'; } //no $t as we use this in admin
	if($type=='info') { $c = 'info'; }
	if($type=='small') { $c = 'info'; $s = 'font-size:8pt;'; }
	if(!$nop) echo '<p>'; if($width) $width = 'width:'.$width.'%;';
	if(!$element) $element = 'span';
	if($title) echo '<div style="color:'.$colors['msg_'.$c.'_border'].';font-size:8pt;font-weight:bold;margin-left:4px">'.$title.'</div>';
	echo '<'.$element.' style="padding:3px;margin:2px;margin-top:0;border:1px solid '.$colors['msg_'.$c.'_border'].';background-color:'.$colors['msg_'.$c.'_bg'].';'.$width.$s.';list-style-type:square" onmouseover="window.status=\''.ucfirst($type).': '.addslashes(strip_tags($message)).'\';return true" onmouseout="window.status=\'\';return true">'.$t.$message.'</'.$element.'>';
	if(!$nop) echo '<p>'; else echo '<br>';
}

function referals($where) {

	if(isset($_SERVER['HTTP_REFERER'])) $refer = strtolower($_SERVER['HTTP_REFERER']); else $refer = '';
	if($refer && !substr_count($refer,'snarkpit.net')) {
		if(substr_count($refer, 'google.')) $refer = 'http://www.google.com';
		if(substr_count($refer,'search')) {
			$slash = strpos(substr($refer,7),'/')+7;
			$refer = substr($var,0,$slash);
		}
		if(substr_count($refer,'chatbear.com')) $refer = 'http://www.chatbear.com';
		if(substr_count($refer,'++++++')) $refer = '';
		if($refer) {
		$refer = preg_replace('/\&s=(.*?)&/si','&',$refer); $refer = preg_replace('/\?s=(.*?)&/si','?',$refer);
		$sql = mysql_query("SELECT * FROM refer WHERE url = '$refer' LIMIT 1");
		if($array = mysql_fetch_array($sql)) @mysql_query("UPDATE refer SET hits=hits+1 WHERE url = '$refer' AND loc = '$where' LIMIT 1"); else @mysql_query("INSERT INTO refer (hits,url,loc) VALUES ('1','$refer','$where')");
	} }
}

function writerss() {
        include('lib/rss.php');
}

function modicon($game,$mod) { global $images;
	if(!$images['moddir']) $images['moddir'] = 'black';
	if($game=='{X}') {
		$img = 'gfx_delete';
		$alt = 'map has been deleted!';
	} else {
	       	$img = 'icon_'.$game.'_'.$mod;
		$alt = $game.'-'.$mod;
	}
	return '<img src="themes/'.$images['moddir'].'/'.$img.'.gif" border="0" align="absmiddle" alt="'.$alt.'"> ';
}

function field($text,$type,$notes) { global $width,$ts;

	if(!$ts) { echo '<table width="100%">'; $ts = true; }

	echo '<tr>';
	echo '<td width="'.$width[0].'">'.$text.(($notes)?'<div class="help">'.$notes.'</div>':'').'</td>';
	echo '<td width="'.$width[1].'">'.$input.'</td>';
	if(isset($width[2])) echo '<td width="'.$width[2].'">';

	if($lastrow) echo '</table>';

}

function init_spell_check($type) {
	global $extrajava;
	if(!$type) $type = 'textarea';
	//require_once('scripts/spell_checker.php'); //this has to be the first thing loaded, before config!?
	$extrajava .= 'var Etype = \''.$type.'\';
</script><script src="scripts/spell_checker.js" type="text/javascript"></script><script src="scripts/cpaint.inc.js" type="text/javascript">';
}

?>
