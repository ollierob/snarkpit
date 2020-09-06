<?php
	if(!$refer = $_SERVER['HTTP_REFERER']) $refer = 'maps.php?error=Map+not+found!';
	$sql = mysql_query("SELECT user_id,map_url,mirror1 FROM maps WHERE map_id = '$download' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) { header('Location: '.$refer); die; }

	if($_GET['type']=='mirror1') $url = $array['mirror1']; else $url = $array['map_url'];

	if($url) {
		if(substr($url,0,4)=='www.') $url = 'http://'.$url;
		//if($userdata && $userdata['user_id']!=$array['user_id'])
		@mysql_query("UPDATE maps SET downloads = downloads + 1 WHERE map_id = '$download' LIMIT 1");
		//if($userdata && $userdata['user_id']!=$array['user_id'] && !$_COOKIE['mapdownload'.$download]) @mysql_query("UPDATE maps SET downloads = downloads + 1 WHERE map_id = '$download' LIMIT 1");
		//setcookie('mapdownload'.$download,'1',($now_time+(3600*12)),$cookiepath,$cookiedomain,0);
		header('Location: '.$url);
	} else {
		header('Location: '.$refer); die;
	}
?>
