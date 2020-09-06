<?php
	if(!$config) error_die('Dont h4x!');
	
	if(!$t = $_GET['t']) $t = $now_time;
	if($t>$now_time) $t = $now_time;

	if(!$forum) {
	     	setcookie($userdata['user_id'].'SPLastVisitTemp',$t,($now_time+$sesscookietime),$cookiepath,$cookiedomain,0);
		setcookie($userdata['user_id'].'SPLastVisit',$t,($now_time+(365*24*3600)),$cookiepath,$cookiedomain,0);
	} else {
		setcookie('forum'.$forum.'read',$t,($now_time+$sesscookietime),$cookiepath,$cookiedomain,0);
	}

	if($topic) $loc = '&topic='.$topic; else $loc = '';
	if($returnto=$_GET['returnto']) $forum = $returnto;
	header('Location: forums.php?forum='.$forum.$loc); die;

?>
