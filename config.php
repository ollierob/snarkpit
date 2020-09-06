<?php
if(substr_count($_SERVER['PHP_SELF'],'config.php')) die('Er, no...');
if($_SERVER['HTTP_HOST']=='snarkpit.com') { header('Location: http://www.snarkpit.com'); die; }

$mtime = explode(' ',microtime());
$mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
ini_set('display_errors', 1);

ob_start("ob_gzhandler");

require_once('functions.php');

$dbhost = 'localhost';
$dbname = 'leperous_snarkpit';
$dbuser = '';
$dbpasswd = '';

$url_site = '/snarkpit/'; $url_images = $url_site.'images/'; $url_smiles = $url_images.'smiles/';
$url_register = $url_site.'index.php?page=register';
$default_game = 'HL';
$extrajava = ''; $onload = '';

$cookiename = 'spit2offcookie';
$cookiepath = $url_site; $cookiedomain = '';
$sesscookiename = 'spit2offsession';
$sesscookietime = 1200;
$now_time = time();

if(!$db = mysql_connect($dbhost,$dbuser,$dbpasswd)) { include('error.php?type=connect'); die; }
if(!mysql_select_db($dbname,$db)) { include('error.php?type=select'); die; }

$username = ''; $user_logged_in = 0; $userdata = array(); $users = array();

if(isset($_COOKIE[$sesscookiename])) {
	$sessid = $_COOKIE[$sesscookiename];
	$mintime = $now_time - $sesscookietime;
	$user_id = mysql_result(mysql_query("SELECT user_id FROM sessions WHERE sess_id = '$sessid' AND start_time > '$mintime' AND remote_ip = '$_SERVER[REMOTE_ADDR]' LIMIT 1"),0);
	if($user_id>0) {
		$user_logged_in = 1;
		update_session_time($sessid);
		$userdata = get_userdata_from_id($user_id);
		$username = addslashes($userdata['username']);
	}
}

if(!$user_logged_in) {
	if(isset($_COOKIE['sp2autologinuser']) && isset($_COOKIE['sp2autologinpass'])) {
		$sp2autologinuser = addslashes($_COOKIE['sp2autologinuser']);
		if(check_autologin($sp2autologinuser,$_COOKIE['sp2autologinpass'])) {
			$userdata = get_userdata($sp2autologinuser);
			$user_logged_in = 1;
			if(!$userdata['user_id']) error_die('Error logging in');
			$sessid = new_session($userdata['user_id'],$_SERVER['REMOTE_ADDR'],$sesscookietime);
			set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath,$cookiedomain);
		}
  	}
}

if(isset($userdata['user_id'])) $user_id = $userdata['user_id']; else $user_id = '';

if(!$_COOKIE[$user_id.'SPLastVisitTemp']) {
        $temptime = $_COOKIE[$userdata['user_id'].'SPLastVisit'];
	if($userdata && $temptime<$userdata['last_visit']) $temptime = $userdata['last_visit'];
} else {
       	$temptime = $_COOKIE[$user_id.'SPLastVisitTemp'];
} if(!$temptime && $userdata) $temptime = $userdata['last_visit'];

//echo 'temptime:'.date("H:i:s",$temptime); echo '<br>last seen '.date("H:i:s",$userdata['last_seen']); die;

setcookie($user_id.'SPLastVisit',$now_time,($now_time+(3600*24*365)),$cookiepath,$cookiedomain,0);
setcookie($user_id.'SPLastVisitTemp',$temptime,($now_time+$sesscookietime),$cookiepath,$cookiedomain,0);
$last_visit = $temptime;

//if($userdata['user_id']==1) echo 'lastvisit will expire at '.gmdate("H:i jS M Y",$now_time+(3600*24*365)).' and lastvisittemp at '.gmdate("H:i jS M Y",$now_time+$sesscookietime);

if($userdata) {
	@mysql_query("UPDATE users SET user_ip = '$_SERVER[REMOTE_ADDR]', last_seen = '$now_time' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	if(isset($userdata['theme'])) $theme = $userdata['theme'];
	if($_GET['theme']) $theme = $_GET['theme'];
	if($userdata['user_level']==-1) die('Your account has been closed, as you have either breached website rules or been deemed annoying. Please go away and don\'t come back until you\'ve grown up. THAT DOESN\'T MEAN REREGISTER, OR WE\'LL JUST BAN YOU AGAIN!');
} else $pmbar = '';

if(!isset($theme)) $theme = 'standard'; if(!include("themes/$theme.php")) msg('ERROR LOADING THEME!','error'); if(isset($alttheme)) $theme = $alttheme;

	if(isset($_GET['page'])) $page = $_GET['page']; else $page = 'index';
	if(isset($_GET['mode'])) $mode = $_GET['mode'];

?>
