<?php
	if($marray = mysql_fetch_array(mysql_query("SELECT * FROM maps WHERE map_id = '$tarray[map]' LIMIT 1"))) {
		echo "\n".'<table width="100%" cellpadding=3 cellspacing=0 style="font-size:8pt"><tr><td width="60%" valign=top>';
		echo "\n".'	<fieldset><legend>description of <a href="maps.php?map='.$tarray['map'].'">'.$marray['name'].'</a> <img src="themes/'.$images['moddir'].'/icon_'.$marray['game'].'_'.$marray['mod'].'.gif" align=texttop></legend>';

		if(strlen(strip_tags($marray['map_about']))>400) {
			if(!$pos = strpos($marray['map_about'],'.',400)) $pos = strpos($marray['map_about'],' ',400); if(!$pos) $pos = 400;
			$marray['map_about'] = substr($marray['map_about'],0,$pos+1).'.. [<a href="maps.php?map='.$tarray['map'].'" class="white">read more</a>]';
		}

		echo stripslashes($marray['map_about']).'<br><img src="images/null.gif" height=4 width=100><br>';
		echo '<table cellspacing=0 cellpadding=0 align=right style="font-size:8pt"><tr><td><b><a href="maps.php?map='.$tarray['map'].'">more info <img src="images/gfx_info.gif" border=0 align=texttop></a>';
	
		if($marray['map_url']) {
		        if(substr_count($marray['map_url'],'angelfire.com')) $warning = ' onclick="alert(\'This host does not allow direct map downloads; please right click on this link and `save target` instead\');return false"'; else $warning = '';
			echo '&nbsp;&nbsp;<a href="maps.php?download='.$tarray['map'].'"'.$warning.'>download <img src="images/gfx_download.gif" border=0 align=texttop></a>';
		}
	
		if($userdata && $userdata['user_id']!=$marray['user_id']) echo '&nbsp;&nbsp;<a href="cp.php?mode=watching&action=addmap&id='.$tarray['map'].'&return='.$topic.'">watch <img src="images/gfx_watch.gif" border=0 align=texttop></a>';
		echo '</b></td></tr></table>';
		echo "\n".'<b>Map ';
		if($marray['status']<100) echo $marray['status'].'% '; echo 'complete</b>';
		echo '</fieldset>';
		echo '</td><td width="40%" valign=top align=right><nobr><br>'; $c = 0;

		if($marray['thumbnails']) {
			for($i=1;$i<=$marray['thumbnails'];$i++) { $c++; echo screenshot($tarray['map'],$marray['thumbnails'],'maps/'.$marray['game'].'/images/'.$tarray['map'].'_'.$i.'.jpg').'<img src="maps/'.$marray['game'].'/images/'.$tarray['map'].'_'.$i.'_thumb.jpg" border=0 align=texttop></a> '; }
		} else {
			for($i=1;$i<=5;$i++) { $c++; if($marray['scr'.$i]) { if($c==1) echo 'Screenshots: '; echo '<a href="javascript:void(0)" onclick="popwin(\'screenshot.php?map=&theme='.$theme.'&img='.$marray['scr'.$i].'\')" onMouseOver="window.status=\'Click to view full sized screenshot (popup window)\';return true" onMouseOut="window.status=\'\';return true"><img src="images/gfx_image.gif" border=0 align=texttop></a> '; } }
		}
		echo '&nbsp;</nobr></td></tr></table>'; if(!$c) echo '</p>';
		$tarray['description']='';

		if($marray['status']==100) $tarray['topic_status'] = 3;

	} else $tarray['description'] = '<b><font color="red">Map has been deleted!</font></b>';
?>
