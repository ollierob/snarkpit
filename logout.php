<?php
include('config.php'); 
if($userdata) {
	$expire = $now_time - 10000000;
	$sessid = $_COOKIE[$sesscookiename];

	while(list($name,$value) = each($_COOKIE)) { if($name!=$userdata['user_id'].'SPLastVisit') {
		setcookie($name,'',$expire,$cookiepath,$cookiedomain,0);
		//setcookie($name,'',$expire,$cookiepath);
	} }
	@mysql_query("DELETE FROM sessions WHERE user_id = '$userdata[user_id]'");
	@mysql_query("DELETE FROM sessions WHERE sess_id = '$sessid'");
	setcookie($userdata['user_id'].'SPLastVisit',$now_time,($now_time+31536000),$cookiepath,$cookiedomain,0);
	unset($userdata);
}

if($redir) $redir = '?page='.$redir;
header('Location: index.php'.$redir); die;
?>
