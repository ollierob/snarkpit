<?php
if($_GET['topic']||$_GET['mode']) require_once('scripts/spell_checker.php');
require('config.php');
include('forums/func_forums.php');

if(isset($_GET['textbody'])) { include('forums/textbody.php'); die; }

if(isset($_GET['changemode']) && $userdata) {
	$changemode=$_GET['changemode'];
	if(!file_exists('themes/'.$changemode.'.php')) { header("Location: forums.php?forum=$forum&topic=$topic"); die; }
	@mysql_query("UPDATE users SET theme = '$changemode' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	header("Location: forums.php?forum=$forum&topic=$topic&start=$start#endofpage"); die;
}

if(isset($_GET['forum'])) $forum = $_GET['forum']; else $forum = '';
if(isset($_GET['topic'])) $topic = $_GET['topic']; else $topic = '';
if(isset($_GET['java'])) $java = $_GET['java']; else $java = '';
if(isset($_GET['mode'])) $mode = $_GET['mode']; else $mode = '';
if(isset($_GET['act'])) $act = $_GET['act']; else $act = '';
if(isset($_GET['page'])) $page = $_GET['page']; else $page = '';

if(!$ppp = $userdata['user_ppp']) $ppp = 15;
$ppf = 20;

if($mode=='markread') include('forums/markread.php');
if(isset($_POST['message']) && !$mode) { include('forums/submit.php'); die; }
@include('forums/run_support.php'); //if using support forums; delete the file run_support.php if you're not

//general forum-wide setup
$t_forums = '<a href="forums.php" class="white">The SnarkPit Forums</a> »';

//image setup
$imgdir = 'themes/'.$theme.'/';
$forumimg['new'] = $imgdir.'topic_new.gif'; $forumimg['old'] = $imgdir.'topic_old.gif';
$forumimg['pop_new'] = $imgdir.'topic_new_hot.gif'; $forumimg['pop_old'] = $imgdir.'topic_old_hot.gif';
$forumimg['locked_new'] = $imgdir.'topic_locked.gif'; $forumimg['locked_old'] = $imgdir.'topic_locked.gif';
$forumimg['poll_new'] = $imgdir.'topic_new_poll.gif'; $forumimg['poll_old'] = $imgdir.'topic_old_poll.gif';
$forumimg['help_new'] = $imgdir.'topic_new_help.gif'; $forumimg['help_old'] = $imgdir.'topic_old_help.gif';

//browser-dependent stuff
if(browser()=='moz' && ($mode=='newtopic'||$topic)) {
	init_spell_check('iframe');
	$onload = ' onload="if(document.getElementById(\'message\')) document.getElementById(\'message\').contentWindow.document.designMode = \'on\'"';
} elseif(browser()=='ie' && ($mode=='newtopic'||$topic)) {
	init_spell_check('iframe');
	$onload = ' onload="cookieForms(\'open\',\'message\')" onunload="cookieForms(\'save\',\'message\')"';
} elseif($mode=='newtopic'||$topic) init_spell_check('textarea');
$extrajava .= '</script><script src="forums/browsers/'.browser().'.js" type="text/javascript">';

if(!$mode) {
	if($topic && !$forum) {
		$forum = mysql_result(mysql_query("SELECT forum_id FROM topics WHERE topic_id = '$topic' LIMIT 1"),0); 
		if(!$forum) { header('Location: forums.php?msg=topic+not+found'); die; }
	} 

	if($forum) {
		$sql = mysql_query("SELECT * FROM forums WHERE forum_id = '$forum' LIMIT 1");
		if(!$farray = mysql_fetch_array($sql)) { header('Location: forums.php'); die; }
		$forum_topics = $farray['forum_topics'];
		$forumname = stripslashes($farray['forum_name']);

		if($topic) {
			if($topic=='newesttopic') {
				$topic = mysql_result(mysql_query("SELECT topic_id FROM topics WHERE forum_id = '$forum' ORDER BY topic_id DESC LIMIT 1"),0);
				if(!$topic) error_die('Forum error, please refresh the page');
			}
			$tsql = mysql_query("SELECT * FROM topics WHERE topic_id = '$topic' LIMIT 1");
			if(!$tarray = mysql_fetch_array($tsql)) { header('Location: forums.php?forum='.$forum); die; }
			$topictitle = stripslashes($tarray['title']);
			$htext = '<a href="?forum='.$forum.'&'.$farray['forum_posts'].'" class="white">'.$forumname.'</a> » <a name="top">'.$topictitle.'</a>';

		} else { $htext = $forumname; } 

		$pagetitle = 'Forums';
		if($forum) $pagetitle = 'Viewing forum: '.$forumname;
		if($topic) $pagetitle = 'Viewing topic: '.$topictitle;
		include('header.php');

		if($topic) { 
			if($config['topicsidebar']) include('forums/sidebar.php'); else echo '<td valign=top colspan=2><table width="100%" cellpadding=2><tr><td>'; 
		} else {
			if($config['forumsidebar']) include('forums/sidebar.php'); else echo '<td valign=top colspan=2><table width="90%" align=center cellpadding=2><tr><td>'; 
		}

		title($t_forums.' '.$htext,'forums');
		
		if(!$topic) include('forums/forum.php'); else include('forums/topic.php');

		footer();
	}

	$pagetitle = 'Forums';
	include('header.php');
	include('forums/sidebar.php');
	include('forums/index.php');

} else {

	if($mode=='newtopic') $pagetitle = 'Post a new topic';
	if($mode=='reply') $pagetitle = 'Reply';
	if($mode=='editpost') $pagetitle = 'Edit post';

	include('header.php');
	include('forums/sidebar.php');

	if($forum) {

		$ismod = is_moderator($forum, $userdata['user_id']);

		$sql = mysql_query("SELECT * FROM forums WHERE forum_id = '$forum' LIMIT 1");
		if(!$farray = mysql_fetch_array($sql)) error_die('Forum does not exist');
		
		$forum_name = $farray['forum_name'];
		$forum_desc = stripslashes($farray['forum_desc']);

		if($topic) { $sql = mysql_query("SELECT * FROM topics WHERE topic_id = '$topic' LIMIT 1");
			if(!$tarray = mysql_fetch_array($sql)) error_die('Topic does not exist');
		} 
	}

	if($mode=='reply' && !$topic) error_die('You must select a topic to reply to.');
	if($mode) { if(!$i = include("forums/$mode.php")) error_die('Page/function not found'); }

} 

echo '</td></tr>';

footer();
?>
