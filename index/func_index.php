<?php

function news_item($array) { global $theme,$userdata,$images,$colors;
	if($array['plan']>0) $type = 'plan'; else $type = 'news';
?>
	<tr>
	<?php if($array['plan']>0) echo '<td rowspan=2 style="background-image:url(\'themes/'.$theme.'/planbar_left.gif\');background-repeat:no-repeat;"></td>'; else echo '<td rowspan=2 style="background-image:url(\'themes/'.$theme.'/newsbar_'.$array['user_id'].'.gif\');background-repeat:no-repeat;"></td>'; ?>
		<td height=24 style="color:<?=$colors['offtext']?>; background-image:url('themes/<?=$theme?>/<?=$type?>bar.gif'); background-repeat:x-repeat">
		<span style="float:right;text-align:right;width:50px">
	<?php
		if($userdata) echo '<a href="cp.php?mode=compose&amp;to='.userdetails($array['user_id'],'','return','1').'&amp;subject=Re:+'.str_replace(' ','+',trim($array['subject'])).'"><img src="images/gfx_pm.gif" border=0 alt="pm this person"></a> ';
		if(isset($userdata['user_level']) && $userdata['user_level']>2) echo '<a href="admin.php?mode=news&amp;edit='.$array['id'].'"><img src="images/gfx_edit.gif" border=0 alt="edit"></a>';
	?>
		&nbsp;</span>
		<?=$images['dot']?><b><span class="newstitle"><?=stripslashes($array['subject'])?></span></b> posted by <?=userdetails($array['user_id'],'','','')?> on <?=gmdate("l jS F",$array['date'])?></td>
	</tr>
	<tr>
	<td class="news"><?=stripslashes($array['text'])?></td>
	</tr>
	<tr><td height=16> </td></tr>

	<?php
}

function map_watch() { global $userdata,$uparray,$images,$spacer,$last_visit,$now_time; $user_id = $userdata['user_id'];
	if(!$uparray) $mapwatch = $mapwatch = mysql_result(mysql_query("SELECT SQL_CACHE `mapwatch` FROM users_profile WHERE user_id = '$user_id' LIMIT 1"),0);
		else $mapwatch = $uparray['mapwatch'];

	if(!$spacer) $spacer = '<br><img src="images/null.gif" height=4 alt=""><br>'."\n";

	?>

	<div class="menuboxtitle">map watch</div>
	<div class="menubox">
	<?php
		$i = 0;
		if($mapwatch) {
			$sql = str_replace(',','\' OR `map_id` = \'',substr(substr($mapwatch,1),0,-1));
			$query = mysql_query("SELECT `map_id`,`name`,`date`,`game`,`mod`,`status` FROM maps WHERE `map_id` = '$sql' ORDER BY date DESC");
			while($array = mysql_fetch_array($query)) {
				$i++;
				echo '<a href="maps.php?map='.$array['map_id'].'" class="msidebar"><b>'.$array['name'].'</b></a><br>';
				echo '<a href="cp.php?mode=watching&amp;action=delmap&amp;id='.$array['map_id'].'"><img src="themes/'.$images['moddir'].'/gfx_delete.gif" align=right alt="Stop watching" title="stop watching" border=0></a>';
				if($array['status']==100) echo '<b>complete: ';
				elseif($array['date']>$last_visit || $array['date']>($now_time-345600)) echo '<b>recent: ';
				echo ''.date("jS M",$array['date']).'</b>'.$spacer;
			}
		}
		if(!$i) echo 'not watching any maps';

	echo '</div>';
}

?>
