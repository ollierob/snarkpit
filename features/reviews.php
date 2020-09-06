<?php
	if(!$game = $_GET['game']) $game = $userdata['game']; if(!$game) $game = $default_game;

	$garray = mysql_fetch_array(mysql_query("SELECT `name`,`reviews` FROM `games` WHERE `id` = '$game' LIMIT 1"));
	if(!$garray || !$garray['reviews']) { header("Location: features.php?page=reviews"); die; }

	if(!$mod = $_GET['mod']) $mod = 'DM';
	$sql = mysql_query("SELECT `fullname`,`reviews` FROM `mods` WHERE `name` = '$mod' LIMIT 1");
	if(!$marray = mysql_fetch_array($sql)) header("Location: features.php?page=reviews&game=$game"); //want array for #checking
	$tgamename = '<a href="?page=reviews&game='.$game.'" class=white>'.$garray['name'].' Map Reviews</a>';
	$modname = '» '.$marray['fullname'];

	tracker($garray['name'].' Map Reviews','');
	title("$t_features $tgamename $modname",'features');
?>

<p><table width="99%" cellpadding=1 cellspacing=0>
<tr>
<td width="49%" valign="top">
	<?php getonereview("SELECT r.*,m.user_id AS author FROM reviews r, maps m WHERE r.game = '$game' AND r.mod = '$mod' AND m.map_id = r.map_id ORDER BY r.review_id DESC LIMIT 1",'Recent review',''); ?>
</td>
<td width="2%"></td>
<?php if($numreviews>1) { ?>
<td width="49%" valign="top">
	<?php getonereview("SELECT r.*,m.user_id AS author FROM reviews r, maps m WHERE r.game = '$game' AND r.mod = '$mod' AND r.review_id != '$lastreview' AND m.map_id = r.map_id ORDER BY RAND() LIMIT 1",'Random review','check'); ?>
</td>
<?php } else echo '<td width="49%"></td>'; ?>
</tr></table>
</p>

<table width="100%">
<tr><td width="70%" valign=top><h1>Review Index:</h1>

<table width="100%" cellpadding=3 cellspacing=0 style="font-size:8pt" bgcolor="<?=$colors['bg']?>">
<tr style="font-weight:bold" bgcolor="<?=$colors['bg']?>">
	<td width=30%><a href="?page=reviews&game=<?=$game?>&mod=<?=$mod?>&amp;order=mapname" class=white>name</a></td>
	<td width=60%>verdict</td>
	<td width=10%><a href="?page=reviews&game=<?=$game?>&mod=<?=$mod?>&amp;order=score" class=white>score</a></td>
</tr>
<tr><td height=6></tr>

<?php
	$orderby = 'r.`mapname`'; if(isset($_GET['order'])) $order = $_GET['order'];
	if($order=='score') $orderby = 'r.`score` DESC';

	$c = 0;
	$sql = mysql_query("SELECT r.`map_id`,r.`review_id`,r.`mapname`,r.`user_id`,r.`verdict`,r.`score`,m.`user_id` AS `author` FROM `reviews` r, `maps` m WHERE r.`game` = '$game' AND r.`mod` = '$mod' AND m.`map_id` = r.`map_id` ORDER BY $orderby");
	while($array = mysql_fetch_array($sql)) {
		$c++;
		if(!$array['user_id']) $array['user_id'] = '?';
		echo "\n".'<tr onmouseover="style.background=\''.$colors['trmouseover'].'\'" onmouseout="style.background=\''.$colors['bg'].'\'">';
		//else { if($c%2) echo '<tr bgcolor="'.$colors['bg'].'">'; else echo '<tr bgcolor="'.$colors['dgray'].'">'; }
		echo '<td>';
		if($array['score']>=9) echo '<img src="images/goldensnark.gif" align=right>';
		if($array['score']<=2) echo '<img src="images/deadsnark.gif" align=right>';
		echo '<b><font size=2><a href="?page=reviews&id='.$array['review_id'].'">'.stripslashes($array['mapname']).'</a></font></b><br>by '.(($array['author'])?userdetails($array['author'],'white','return','',''):$array['user_id']).'</td>';
		echo '<td><font color="'.$colors['lgray'].'">'.stripslashes($array['verdict']).'</td>';
		echo '<td><font size=4>'.$array['score'].'/10</font></td></tr>';
		echo '<tr><td height=6></tr>';
	}
	
	if($c!=$marray['reviews']) {
		$query = "UPDATE `mods` SET `reviews` = '$c' WHERE `game` = '$game' AND `name` = '$mod' LIMIT 1";
		@mysql_query($query);
	}
?>
<tr><td colspan=3 bgcolor="<?=$colors['gray']?>"></td></tr>
</table>

</td>
<td width="2%"></td>
<td width="28%" valign=top>
<?php getreviews('HL'); ?>

</td></tr></table>
<p>
