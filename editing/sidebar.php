<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">
	<fieldset>
	<legend>games</legend>
	<div style="font-size:13px;line-height:20px;margin-bottom:5px">
	<?php $sql = mysql_query("SELECT * FROM games WHERE editor!=''");
		while($sarray = mysql_fetch_array($sql)) {
			if($game!=$sarray['id']) echo '<a href="?game='.$sarray['id'].'"'.((isset($colors['class_gamemenu']))?' class="'.$colors['class_gamemenu'].'"':'').'>';
			else echo '<b><a href="?game='.$sarray['id'].'" '.(($colors['gamemenu'])?$colors['gamemenu']:'class=white').'>';
			echo '<img src="themes/'.$images['moddir'].'/icon_'.$sarray['id'].'.gif" border="0" align="absmiddle"> '.$sarray['name'].'</b></a><br>';
		} ?>
	</div>
	</fieldset>

	<?php if($userdata && $_GET['game'] && $_GET['game']!=$userdata['game']) { ?>
	<div class="legendbox" style="position:relative;top:-20px;left:6px;width:92%;font-size:10px;text-align:center"><a href="?page=<?=$page?>&setdefault=<?=$game?>" onmouseover="window.status='Click to set <?=$garray['name']?> as your default game';return true" onmouseout="window.status='';return true" class="msidebar">+set <?=$game?> as default game</a></div>
	<? } ?>

	<div class="menuboxtitle"><a href="?game=<?=$game?>" class="sidebar">editing info</a></div>
	<div class="menubox">
		<a href="editing.php?game=<?=$game?>" class="msidebar">troubleshooting</a><br>
		<a href="editing.php?page=entity&game=<?=$game?>" class="msidebar">entities</a><br>
		<a href="editing.php?page=glossary&game=<?=$game?>" class="msidebar">glossary</a><br>
		<a href="index.php?page=links" class="msidebar">links</a><br>
		<?php 
			$forum_id = mysql_result(mysql_query("SELECT forum_id FROM forums WHERE game = '$game' LIMIT 1"),0);
			if($forum_id) echo '<a href="forums.php?forum='.$forum_id.'" class="msidebar"><b>editing forums</b></a>';
		?>
	</div>

	<div class="menuboxtitle"><a href="editing.php?page=tutorials<?=($_GET[game])?'&game='.$game:'';?>" class=sidebar>tutorials</a></div>
	<div class="menubox">
	<?php if(!include('lib/tutsections_'.$game.'.php')) echo '<i>Could not load tut library...</i>'; 
		for($i=0;$i<$lib_seclength;$i++) echo '<a href="editing.php?page=tutorials&game='.$game.'&type='.$lib_sections[$i][0].'" class="msidebar">'.strtolower($lib_sections[$i][1]).'</a><br>'; ?>
	</div>

	<div class="menuboxtitle"><a href="?page=files&game=<?=$game?>" class="sidebar">downloads</a></div>
	<div class="menubox">
		<a href="editing.php?page=files&game=<?=$game?>&type=editors" class=msidebar>editors</a><br>
		<a href="editing.php?page=files&game=<?=$game?>&type=prefabs" class=msidebar>prefabs</a><br>
		<a href="editing.php?page=files&game=<?=$game?>&type=models" class=msidebar>models</a><br>
		<a href="editing.php?page=files&game=<?=$game?>&type=textures" class=msidebar>textures</a><br>
		<a href="editing.php?page=files&game=<?=$game?>&type=utilities" class=msidebar>utilities</a><br>
		<a href="editing.php?page=files&game=<?=$game?>" class=msidebar>more...</a>
	</div>

</div>

</td>

<td width="95%" valign=top height="100%"><?=$pmbar?>
<div class="content">
