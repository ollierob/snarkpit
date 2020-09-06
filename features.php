<?php include('config.php');

	$pagetitle = 'Features';
	include('header.php');
	if(isset($_GET['id'])) $id = $_GET['id']; else $id = '';

	$t_features = '<a href="features.php" class="white">Features</a> »';
	include('features/sidebar.php');

function getonereview($sql,$title,$imgcheck) { global $lastreview,$colors,$noreviews,$images;
	if($rarray = mysql_fetch_array(mysql_query($sql))) { 
	if($rarray['author']) $author = userdetails($rarray['author'],'white','return',''); else $author = $array['user_id']; if(!$author) $author = '?';
	?>
	<fieldset style="min-height:100px">
	<legend><?=$title?>: <a href="?page=reviews&id=<?=$rarray['review_id']?>"><?=stripslashes($rarray['mapname'])?> <img src="themes/<?=$images['moddir']?>/icon_<?=$rarray['game'].'_'.$rarray['mod']?>.gif" align=texttop border=0></a></legend>
	<table align=right style="font-size:8pt" height=30 cellspacing=1 cellpadding=1><tr><td align=right><b><font size=4><?=$rarray['score']?>/10</font></b></td></tr></table>
	<?php
		//$img = '<table width="100" cellpadding=1 cellspacing=1 align=left><tr><td><img border=0 src="maps/'.$rarray['game'].'/images/'.$rarray['map_id'].'_1'.((!$rarray['unofficial'])?'_review':'').'_thumb.jpg" class="thumb"></td></tr></table>';
		//if(file_exists('maps/'.$rarray['game'].'/images/'.$rarray['map_id'].'_1'.((!$rarray['unofficial']||file_exists)?'_review_thumb':'').'.jpg')) echo $img;

		$img = 'maps/'.$rarray['game'].'/images/'.$rarray['map_id'].'_1';
		if(file_exists($img.'_review_thumb.jpg')) $e = '_review_thumb';
		elseif(file_exists($img.'_thumb.jpg')) $e = '_thumb';
		else $e = '';
		if($e) echo '<table width="100" cellpadding=1 cellspacing=1 align=left><tr><td><img border=0 src="'.$img.$e.'.jpg"></td></tr></table>';
	?>
	<font color="<?=$colors['lgray']?>"><?=stripslashes($rarray['verdict'])?></font><br><img src="images/null.gif" height=4><br clear="right">
	<span style="float:right;margin-top:4px"><a href="maps.php?download=<?=$rarray['map_id']?>"><b>download</b> <img src="images/gfx_download.gif" border=0 align=texttop></a></span>
	Map by <?php echo $author; if($rarray['user_website']) echo ' <a href="'.$rarray['user_website'].'" target="_blank"><img src="images/homepage.gif" border=0 align=texttop></a>'; ?>
	</fieldset>
	<?php $lastreview = $rarray['review_id']; return $lastreview;
	} else $noreviews++;
	if($noreviews==2) echo 'No reviews are available for this game.';
}

function getreviews() { global $images;
	$sql = mysql_query("SELECT * FROM `games` WHERE `reviews` != ''");
	echo '<table width=100% cellpadding=2 cellspacing=0><tr>';
	while($garray = mysql_fetch_array($sql)) { $modreviews='';
		echo "\n".'<td colspan=2><a href="?page=reviews&game='.$garray['id'].'"><img src="themes/'.$images['moddir'].'/icon_'.$garray['id'].'.gif" align=texttop border=0> <b>'.stripslashes($garray['name']).' reviews</b></a></td></tr>';
		echo '<tr><td><img src="images/null.gif" height=1 width=16></td><td width=100% style="font-size:8pt"><b>';
		$msql = mysql_query("SELECT `name`,`fullname`,`reviews` FROM `mods` WHERE `game` = '$garray[id]' AND `reviews` != '0'");
		while($marray = mysql_fetch_array($msql)) { echo '<a href="?page=reviews&game='.$garray['id'].'&mod='.$marray['name'].'" class=white>'.$marray['fullname'].' ('.$marray['reviews'].')</a><br>'; }
		echo '</b></td></tr><tr><td height=6></td></tr>'."\n";
	} echo '</table>';
}

	if(!$page) $page = 'index'; 
	if(!isset($game)) { $game = $userdata['game']; if(!$game) $game = $default_game; }
	if($page=='reviews' && $id) $page = 'showreview';
	if(!include("features/$page.php")) error_die('Page not found');

footer(); ?>
