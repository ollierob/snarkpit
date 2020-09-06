<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">

	<fieldset>
	<legend>games</legend>
	<div style="font-size:13px;margin-bottom:5px">
	<?php $sql = mysql_query("SELECT * FROM games");
		while($sarray = mysql_fetch_array($sql)) {
			if($game!=$sarray['id']) echo '<a href="?game='.$sarray['id'].'"'.((isset($colors['class_gamemenu']))?' class="'.$colors['class_gamemenu'].'"':'').'>';
			else echo '<b><a href="?game='.$sarray['id'].'" '.(($colors['gamemenu'])?$colors['gamemenu']:'class=white').'>';
			echo '<img src="themes/'.$images['moddir'].'/icon_'.$sarray['id'].'.gif" border=0 align=texttop width=16> '.$sarray['name'].'</b></a><br>';
		} ?>
	<img src="images/null.gif" height=4></div>
	</fieldset>

	<?php if($userdata && $_GET['game'] && $_GET['game']!=$userdata['game']) { ?>
	<div class="legendbox" style="position:relative;top:-20px;left:6px;width:92%;font-size:10px;text-align:center;"><a href="?page=<?=$page?>&setdefault=<?=$game?>" onmouseover="window.status='Click to set <?=$garray['name']?> as your default game';return true" onmouseout="window.status='';return true" class="msidebar">+set <?=$game?> as default game</a></div>
	<? } ?>

	<div style="font-size:8pt;position:absolute;left:4px">
	<p align=center>
	<?php
		if(isset($_GET['show'])) $show = $_GET['show']; else $show = '';
		if(!$show || $show=='all') echo '<b>'; else echo '<a href="?game='.$game.'&mod='.$mod.'" class="msidebar">'; echo 'all</a></b> : ';
		if($show=='beta') echo '<b>'; else echo '<a href="?game='.$game.'&mod='.$mod.'&show=beta" class="msidebar">'; echo 'beta</a></b> : ';
		if($show=='completed') echo '<b>'; else echo '<a href="?game='.$game.'&mod='.$mod.'&show=completed" class="msidebar">'; echo 'completed</a></b>';
	echo '</p><p><font color="'.$colors['medtext'].'">';

	if($map) $mod = mysql_result(mysql_query("SELECT `mod` FROM maps WHERE `map_id` = '$map' LIMIT 1"),0);

	$sql = mysql_query("SELECT * FROM mods WHERE game = '$game' ORDER BY sortindex DESC,name");
	while($xarray = mysql_fetch_array($sql)) {
		if(!$show OR $show=='all') $v=$xarray['betamaps']+$xarray['finishedmaps'];
		if($show=='beta') $v=$xarray['betamaps']; if($show=='completed') $v=$xarray['finishedmaps'];

		if($v>0 OR $xarray['name']==$mod) { 
			//if($colors['bg2']) $s = '<font color="'.$colors['bg2'].'">'; else $s = '<font color="'.$colors['text'].'">';
			$s = '<font color="'.$colors['medtext'].'">';
			if($xarray['name']!=$mod) { $s .= '<a href="?game='.$game.'&mod='.$xarray['name'].'&show='.$show.'" class=sidebar>'; $e = "</a> ($v)"; }
			else { $s .= '<span style="background-color: '.$colors['trmouseover2'].';padding:1px"><b><a href="?game='.$game.'&mod='.$xarray['name'].'&show='.$show.'" class=sidebar>'; $e = '</a> <font color=white>('.$v.')</font> </b></span>'; } 
			$e = $e.'</font>';
		} else { $s = ''; $e = " ($v)"; }

		echo $s.$xarray['fullname'].$e."<br>\n";
	
	}

?>
	</div>

</div>
</td>

<td width="95%" valign=top height="100%"><?=$pmbar?>
<div class="content">
