<?php
	if($_GET['map']) require_once('scripts/spell_checker.php');
	require_once('config.php');

	//while(list($var,$val)=each($_GET)) $$var = $val;
	//$game = strtoupper($game);
	$prev = ''; $next = '';

if(isset($_GET['download'])) { $download = $_GET['download']; include('maps/download.php'); die; }

if(isset($_GET['game'])) $game = $_GET['game'];
elseif(!isset($game)) { if($userdata['game']) $game = $userdata['game']; else $game = $default_game; $mod = ''; }
$game = strtoupper($game);

if(isset($_GET['setdefault'])) {
        $setdefault = $_GET['setdefault'];
	if($userdata) @mysql_query("UPDATE users SET game = '$setdefault' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	header('Location: maps.php?game='.$setdefault); die;
}

	referals('maps');

if(isset($_GET['map'])) {
	if($userdata) init_spell_check('textarea');
        $map = $_GET['map'];
	$maparray = mysql_fetch_array(mysql_query("SELECT * FROM maps WHERE map_id = '$map' LIMIT 1")); 
	if(!$maparray) { header('Location: maps.php'); die; }
	$game = $maparray['game']; $mod = $maparray['mod']; $mapname = stripslashes($maparray['name']);
} else {
       	$map = '';
       	if(isset($_GET['mod'])) $mod = $_GET['mod']; else $mod = '';

}

$sql = mysql_query("SELECT * FROM games WHERE id = '$game' LIMIT 1");
if(!$garray = mysql_fetch_array($sql)) { header('Location: maps.php'); die; }
$modname = '» <a href="?game='.$game.'" class=white>'.$garray['name'].'</a> ';

if($mod && $game) {
	$msql = mysql_query("SELECT `fullname`,`website`,`betamaps`,`finishedmaps` FROM mods WHERE `name` = '$mod' AND `game` = '$game' LIMIT 1");
	if(!$marray = mysql_fetch_array($msql)) { header('Location: maps.php?game='.$game); die; }
	$modname .= '» <a href="?game='.$game.'&mod='.$mod.'" class=white>'.stripslashes($marray['fullname']).'</a>';
}

if($mod) $pagetitle = $garray['name'].' - '.stripslashes($marray['fullname']).' Maps'; else $pagetitle = $garray['name'].' Maps';
if($map) $pagetitle.=': '.$maparray['name'];
$page = '';
require_once('header.php');

	$modlink = ''; $sortlink = ''; $letterlink = ''; $showlink = ''; $modelink = '';
	if($mod) $modlink = '&mod='.$mod;
	if(isset($_GET['sort'])) { $sort = $_GET['sort']; $sortlink = '&sort='.$sort; } else $sort = '';
	if(isset($_GET['letter'])) { $letter = $_GET['letter']; $letterlink = '&letter='.$letter; } else $letter = '';
	if(isset($_GET['show'])) { $show = $_GET['show']; $showlink = '&show='.$show; } else $show = '';
	if(isset($_GET['mode'])) { $mode = $_GET['mode']; $modelink = '&mode='.$mode; } else $mode = '';
	$reslink = $modlink.$sortlink.$letterlink.$showlink.$modelink; 

require_once('maps/sidebar.php');
if($map) { include('maps/mapinfo.php'); footer(); }
tracker('Maps section','');

if($show AND $show!='all' AND $show!='beta' AND $show!='completed') error_die('Stop trying to be l337'); 
if(!$show) $show='all';

	$gamemodshow = '?game='.$game.'&mod='.$mod; if($show!='all') $gamemodshow.='&show='.$show;
	if(!$sort) $sort = 'name';
	if($sort=='size') $sizes = array('tiny','small','medium','large','huge');

title('<a href="maps.php" class="white">Maps</a> '.$modname,'maps');

if(file_exists('lib/gameplay_'.$game.$mod.'.php')) $modeincluded = include('lib/gameplay_'.$game.$mod.'.php');
	else $modeincluded = '';

?>

<p><table width="99%" cellspacing=1 cellpadding=1><tr>
<td width="65%" valign=top>

	<img src="themes/<?=$images['moddir']?>/snark.gif" align=left><a href="<?=$gamemodshow?>" class=white>Currently showing <b><?=$show?> maps</b> for selected
		<?php echo $mod ? 'mod' : 'game'; echo '</a>'; 
		if($letter) echo ', <a href="'.$gamemodshow.'&letter='.$letter.'" class=white>starting with <b>'.$letter.'</b></a>';
		if($mode) echo ', <b>'.$gamemodes[$mode].'</b> specific';
		echo ', <a href="'.$gamemodshow.'&sort='.$sort.'" class=white>sorted by <b>'.$sort.'</b></a>';
		?>

	</p>
	<p>
	<table cellpadding=2>
	<tr>
		<td><b>sort:</b></td>
		<td><?php
			$sorting = array('name','downloads','date','size','rating','views');
			for($i=0;$i<6;$i++) { if($sort==$sorting[$i]) echo "<b>$sort</b> ";
				else echo '<a href="'.$gamemodshow.$modelink.$letterlink.'&sort='.$sorting[$i].'">'.$sorting[$i].'</a> '; 
				if($i<5) echo ' : ';
			} ?>
		</td>
	</tr>
	<tr>
		<td><b>letter:</b></td><td>
		<?php $alphabet = array('$','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			for($i=0;$i<27;$i++) {
				if($alphabet[$i]==$letter) echo '<b>'.$alphabet[$i].'</b> '; else
				echo '<a href="'.$gamemodshow.$modelink.'&sort='.$sort.'&letter='.$alphabet[$i].'">'.$alphabet[$i].'</a> '."\n";
			} ?> (<a href="<?=$gamemodshow.$sortlink.$modelink?>">all</a>)
		</td>
	</tr>
<?php 	if($modeincluded) {
		echo "	<tr>\n		<td><b>mode:</b></td>\n		<td>"; $c=0;
		while(list($key,$val) = each($gamemodes)) { $c++; if($c!=1) echo ' : ';
			if($key==$mode) echo '<b>'.$val.'</b>'; else echo '<a href="maps.php?game='.$game.$reslink.'&mode='.$key.'">'.$val.'</a>';
		}
	echo ' (<a href="'.$gamemodshow.$sortlink.$letterlink.'">all</a>)'; 
} ?>

	<tr>
		<td colspan=4><?php

	$mpp = 25; $showsql = '';
	if($mod) $msql = "AND `mod` = '$mod'"; else $msql = '';
	if(!isset($_GET['page'])) $page = 1; else $page = $_GET['page'];
	$start = ($page-1)*$mpp;
	if($letter) $letterlike = "AND `name` LIKE '$letter%'"; else $letterlike = '';
	if($letter=='$') { 
		for($i=0;$i<10;$i++) { $numletter .="`name` LIKE '$i%' "; if($i!=9) $numletter.=" OR "; }
		$letterlike = "AND ($numletter)";
	}
	if($show=='beta') $showsql = 'AND `status` != 100'; elseif($show=='completed') $showsql = 'AND `status` = 100';
	if($mode) $modesql = 'AND `gameplay` LIKE \'%'.$mode.'%\''; else $modesql = '';

	if($sort=='downloads' OR $sort=='size' OR $sort=='rating' OR $sort=='date' OR $sort=='views') $sortsql = $sort.' DESC';
		else $sortsql = $sort;

	$wheresql = "game = '$game' $showsql $msql $letterlike $modesql";
	$nummaps = mysql_result(mysql_query("SELECT COUNT(map_id) FROM maps WHERE $wheresql"),0);
	
	if($mod && $show && !$letter) {
		if($show=='beta') { if($nummaps!=$marray['betamaps']) @mysql_query("UPDATE mods SET betamaps = '$nummaps' WHERE name = '$mod' LIMIT 1"); }
		elseif($show=='completed') { if($nummaps!=$marray['finishedmaps']) @mysql_query("UPDATE mods SET finishedmaps = '$nummaps' WHERE name = '$mod' LIMIT 1"); }
	}


	if(!$nummaps) echo '<font color="'.$colors['no'].'"><b>No maps found</b></font>'; else {
		$numpages = ceil($nummaps/$mpp); 
		$gnum = array_fill(2,$numpages+1,0); $gnum[1] = 1;

		if($page>$numpages || $page<1) { header('Location: maps.php?game='.$game.$reslink.'&page='.$numpages); die; }

		echo '<b>'.$nummaps.'</b> map'.(($nummaps!=1)?'s':'').' found:';

		if($numpages>1) {
			echo ' go to page [ ';
			if($page) { $gnum[$page-2]='skip'; $gnum[$page+2]='skip'; }
			for($i=$numpages-2;$i<=$numpages;$i++) $gnum[$i]=1;
			if(!$gnum[$i]) $gnum[$numpages-3]='skip';
			if($numpages>3) { $gnum[1]=1;$gnum[2]=1;$gnum[3]=1; } else { for($i=1;$i<=$numpages;$i++) $gnum[$i]=1; }
			if($page) { for($i=($page-1);$i<=($page+1);$i++) $gnum[$i]=1; }

			for($i=1;$i<=$numpages;$i++) { 
				if($gnum[$i]==1) { $justskipped=''; if($page==$i) echo '<b>'; else echo '<a href="?game='.$game.$reslink.'&page='.$i.'">';
				echo $i;
				if($page==$i) echo '</b> '; else echo '</a> '; }
				if($gnum[$i]=='skip'&&!$justskipped) { $justskipped=1; echo '... '; } 
			}

			echo ']';

			if($page<$numpages) $next = '<a href="?game='.$game.$reslink.'&page='.($page+1).'#maptop" class=white>next »</a> ';
			if($page>1) $prev = '<a href="?game='.$game.$reslink.'&page='.($page-1).'#maptop" class=white>« previous</a> ';
			if($prev&&$next) $next = ' : '.$next; 
		}
	}

?>

	</td></tr>
	</table>
</td>
<form action="index.php?page=query" method="post" name="searchform"><input type="hidden" name="searchgame" value="<?=$game?>"><input type="hidden" name="searchmaps" value="on">
<td width="35%" valign="top" style="font-size:11px">

	<?php $sql = mysql_query("SELECT `map_id`,`name`,`game`,`mod`,`user_id`,`thumbnails`,`status`,`rating`,`views` FROM maps WHERE game = '$game' AND thumbnails>0 ORDER BY RAND() LIMIT 1");
		if($array = mysql_fetch_array($sql)) {
		        echo '<fieldset style="position:relative;top:-15px;padding-left:4px;padding-bottom:2px;margin:2px"><legend style="padding:1px">random '.$game.' map</legend>';
			echo screenshot($array['map_id'],$array['thumbnails'],'maps/'.$array['game'].'/images/'.$array['map_id'].'_1.jpg').'<img src="maps/'.$array['game'].'/images/'.$array['map_id'].'_1_thumb.jpg" class="thumb" style="position:relative;float:right;margin-right:-2px;margin-bottom:4px" border=0></a>';
			echo '<font size=2><b><a href="maps.php?map='.$array['map_id'].'">'.modicon($array['game'],$array['mod']).$array['name'].'</a></b></font>';
			echo '<br>by '.userdetails($array['user_id'],'white','return','');
			if($array['rating']) echo '<br><img src="images/null.gif" height=5 width=1><br>rated '.$array['rating'].'/10';
			echo '</fieldset>';
		}
	?>

	<div>
	<b><font color="<?=$colors['item']?>"><?=$garray['name']?> map search:<br><input type="text" name="search" class="textinput" size=24>
	<input type="submit" value="[find]" class="submit2" onclick="if(!document.forms['searchform'].elements['search'].value) return false; if(document.forms['searchform'].elements['search'].value.length<3) { alert('Search string too short'); return false }">
	</div>
</td></form>
</tr>

</table>

<?php if($nummaps) { ?>

<table width="99%" cellspacing=0 cellpadding=3>
<tr><td width="65%"><a name="maptop"><font size=2><b><?=$prev.$next?></b></font></a></td><td width="35%"><?php if(isset($marray['website'])) echo '<a href="'.$marray['website'].'" target="_blank"><img src="images/gfx_website.gif" border=0 align=texttop> <b>Visit this mod\'s website</b></a>&nbsp;';?></td></tr>
</table>
<table width="99%" cellspacing=0 cellpadding=3 style="font-size:8pt;border-top:1px solid <?=$colors['lightbg']?>;border-bottom:1px solid <?=$colors['lightbg']?>">
<?php
	$query = mysql_query("SELECT * FROM maps WHERE $wheresql ORDER BY $sortsql LIMIT $start,$mpp");
	while($array = mysql_fetch_array($query)) {
		echo "\n".'<tr bgcolor="'.$colors['darkbg'].'"><td align=right width="5%">';
		if($array['map_url']) echo '<a href="maps.php?download='.$array['map_id'].'" onMouseOver="window.status=\''.$array['map_url'].'\'; return true" onMouseOut="window.status=\'\'"><img src="images/gfx_download.gif" border=0></a>';
		echo '</td><td width="70%"><a href="maps.php?map='.$array['map_id'].'">'.modicon($array['game'],$array['mod']).'<b>'.stripslashes($array['name']).'</b></a>';
		echo ' by '.userdetails($array['user_id'],'white','return','').' ';
		if($array['status']<0) echo '[<i>abandoned</i>] ';
		elseif($array['status']<100) echo '[beta: '.$array['status'].'%] ';
		if($array['thumbnails']) echo screenshot($array['map_id'],$array['thumbnails'],'maps/'.$array['game'].'/images/'.$array['map_id'].'_1.jpg').'<img src="images/gfx_image.gif" align=absmiddle border=0></a>';
		echo '</td><td align=right width="25%">';

		if($sort=='rating' && isset($array['rating'])) echo '<font color="'.$colors['info'].'"><b>rated '.$array['rating'].'/10</b></font>';
		if($sort=='downloads') { echo '<font color="'.$colors['info'].'"><b>'.$array['downloads'].' download'.(($array['downloads']!=1)?'s':'').'</b></font>'; }
		if($sort=='date' && $array['date']>0) echo '<font color="'.$colors['info'].'"><b>'.(($array['date']>=$array['added']+10000)?'edited':'added').' on '.date("D jS M Y",$array['date']).'</b></font>';
		if($sort=='size' && isset($array['size'])) echo '<font color="'.$colors['info'].'"><b>size: '.$sizes[$array['size']].'</b></font>';
		if($sort=='views' && $array['views']) echo '<font color="'.$colors['info'].'"><b>'.$array['views'].' view'.(($array['views']!=1)?'s':'').'</b></font>';

		echo '</td></tr>';
	}
?>
</table>
<table width="99%" cellspacing=0 cellpadding=3>
<tr><td colspan=2 valign=top><font size=2><b><?=$prev.$next?></b></font></td>
<?php if($numpages>5) { ?>
	<form method="post" action="redir.php">
	<td align=right><b><font color="<?=$colors['item']?>" size=2>page
		<select name="page" onchange="if(this.options[selectedIndex].value!='') location.href='maps.php?game=<?=$game.$reslink?>&page='+this.options[selectedIndex].value+'#maptop'">
		<?php for($i=1;$i<=$numpages;$i++) { echo '<option value='.$i; if($i==$page) echo ' SELECTED'; echo '>'.$i.'</option>'; } ?> 
		</select><input type="submit" value=" [go] " class="submit2"><input type="hidden" name="mapredir" value="<?=$game.$reslink?>">
	</td></form>
<? 	} else echo '<td></td>'; ?>

</tr></table>
<? } //no maps

echo '</p>';
footer(); ?>
