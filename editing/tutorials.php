<?php
	$editor = $_GET['editor']; $sort = $_GET['sort'];
	referals('tuts');

if($id = $_GET['id']) {
	$sql = mysql_query("SELECT SQL_CACHE * FROM articles WHERE id = '$id' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('Tutorial not found');

	$game = $array['game']; $gamename = mysql_result(mysql_query("SELECT name FROM games WHERE id = '$game' LIMIT 1"),0);

	tracker('Reading tutorial &quot;'.stripslashes($array['title']).'&quot;','editing.php?page=tutorials&id='.$id);
	title($t_editing.' <a href="?page=tutorials&game='.$array['game'].'" class=white>'.$gamename.' Tutorials</a>: <a href="?page=tutorials&game='.$array['game'].'&type='.$array['type'].'" class=white>'.ucfirst($array['type']).'</a> » '.stripslashes($array['title']),'editing');

	$msg = stripslashes($array[description]).'- written by '.userdetails($array['user_id'],'','return').' on '.date("jS M Y",$array[date]).'<br>';
	$msg .= '<a href="printable.php?section=articles&id='.$id.'" class=white><img src="images/gfx_print.gif" border=0 align=texttop> Click here for a printable version</a>';
	if($userdata['user_level']>2 || $userdata['user_id']==$array['user_id']) $msg .= ' - <a href="cp.php?mode=tutorials&edit='.$id.'"><img src="images/gfx_edit.gif" border=0 align=absmiddle> Edit tutorial</a>';
	msg($msg,'warning','','','','div');

	if($array['example_map']) echo '<p><a href="editing.php?download='.$array['example_map'].'"><img src="images/gfx_download.gif" border=0> Download an example level, detailing some of the things shown in this tutorial</a></p>';

	if($array['pinclude']) $text = file_get_contents('editing/content/'.$array['pinclude']); else $text = mysql_result(mysql_query("SELECT SQL_CACHE text FROM articles_text WHERE id = '$id' LIMIT 1"),0);
	if(!$text) error_die('There was a problem retrieving the article from the database, please try again. <br>'.mysql_error());

	if(!$hideglossary AND !$array['pinclude']) { ?>

		<script language="javascript">
		function glossary(word,game) {
			child = open('popup.php?mode=glossary&word='+word+'&game='+game,'popup','width=400,height=200,status=no,scrollbars=no,resizable=yes');
			child.opener = this; child.focus();
		}
		</script>

	<?php
		$highlight = trim(htmlspecialchars($_GET['highlight']));
		$sql = mysql_query("SELECT * FROM glossary WHERE game = '$game' OR game = '' AND word != '$highlight' ORDER BY word DESC");
		while($garray = mysql_fetch_array($sql)) $text = preg_replace('|\b('.quotemeta($garray['word']).')\b|U', '<a href="?page=glossary#'.substr($garray['word'],0,1).'" title="'.$garray['word'].': '.strip_tags(str_replace('"','\'',$garray['text'])).'" class=green onmouseover="style.cursor=\'help\'" style="border-bottom-style:dotted;border-bottom-width:1px">\\1</a>',$text,1);
	}

	if($highlight) {
		$highlightarray = split(',',$highlight); $sizeof = sizeof($highlightarray);
		for($i=0;$i<$sizeof;$i++) {
		        $text = preg_replace('|\b('.quotemeta($highlightarray[$i]).')\b|iU', '<b style="background-color:'.$colors['highlight'].'"><font color="'.$colors['text'].'">\\1</font></b>',$text);
		}
	}

	$text = preg_replace('#\[image([1-8]+)\]#i','<img src="userimages/tutorials/tut'.$id.'_\\1.jpg">',$text);
	include('lib/entify.php'); $text = entify($text);
	
	//need to fix if highlighted entity names
	if($highlightarray) {
	        while(list($key,$word)=each($highlightarray)) $text = str_replace('<a href="editing.php?game='.$array['game'].'&page=entity&name=<b style="background-color:'.$colors['highlight'].'"><font color="'.$colors['text'].'">'.$word.'</font></b>" class=green>','',$text);
	}
	
	echo '<p>'.$text.'</p>';

	subtitle('<a href="#addcomment"><img src="themes/'.$theme.'/newcomment.gif" border=0 align=right style="position:relative;top:30px"></a><a name="comments">tutorial comments</a>','');
	$commentbox = true; $commentboxwidth = 550;
	getcomments('tutorial',$id,'editing.php?page=tutorials&id='.$id);

	if($averagerating!=$array['rating']) @mysql_query("UPDATE articles SET rating = '$averagerating' WHERE id = '$id' LIMIT 1");

	@mysql_query("UPDATE articles SET hits = hits + 1 WHERE id = '$id' LIMIT 1");

footer(); }

function gettuts($type,$lim) { global $_GET,$game,$sort,$colors,$editor,$images;
	$selgame = "AND game = '$game'";
	if($lim) $lim = "LIMIT $lim";
	if($sort) $sortby = 'ORDER BY '.$sort; else $sortby = 'ORDER BY rating DESC,title';
	if($sort=='rating') $sortby.= ' DESC';
	if($editor) { $seleditor = "AND editor='$editor'"; $selgame = ''; }

	$sql = mysql_query("SELECT * FROM articles WHERE type = '$type' AND section = 'editing' $selgame $seleditor $sortby $lim");
	while($tarray = mysql_fetch_array($sql)) {
		echo "\n".'<tr onmouseover="style.background=\''.$colors['trmouseover'].'\';style.cursor=\'hand\'" onmouseout="style.background=\''.$colors['bg'].'\'" onclick="location.href=\'editing.php?page=tutorials&id='.$tarray['id'].'\'"><td valign=top>';
			if($tarray['editor']) echo '<img src="images/icon_'.$tarray['editor'].'.gif" alt="relevant to '.$tarray['editor'].'" align=texttop height=16 width=16>'; 
		echo '</td><td valign=top>';
			if($byuser || $editor) echo '<img src="themes/'.$images['moddir'].'/icon_'.$tarray['game'].'.gif">';
			elseif($tarray['mod']) echo ' <img src="themes/'.$images['moddir'].'/icon_'.$tarray['game'].'_'.$tarray['mod'].'.gif" align=texttop height=16 width=16>';
		echo '</td><td><b><a href="?page=tutorials&game='.$tarray['game'].'&id='.$tarray['id'].'">'.stripslashes($tarray['title']).'</a></b>';
		echo ' written by '.userdetails($tarray['user_id'],'white','return').'<br>';
		echo '<font color="'.$colors['lighttext'].'">'.stripslashes($tarray['description']).'</td><td align=center>';
		if($tarray[rating]>0) echo '<font size=5>'.$tarray['rating'].'</font>';
		echo '</td></tr>';
	$c++; } if(!$c) echo '<tr><td colspan=2></td><td><i>no tutorials'.$bythisuser.' found</i></tr>'; else { if(!$_GET['type'] && $c>4) echo '<tr><td colspan=2></td><td><a href="?page=tutorials&game='.$game.'&type='.$type.'" class=white><b>more '.$type.' tutorials...</b></a></tr>'; }
}

	include('lib/editors.php');
	tracker('Tutorials Section');

if($type=$_GET['type']) {
	if(!$sort) $sort = 'title';
	$t_tut = $t_editing.' <a href="?page=tutorials&game='.$game.'" class=white>'.$garray['name'].' Tutorials</a>:';
	for($i=0;$i<$lib_seclength;$i++) { if($lib_sections[$i][0]==$type) { $t_id = $i; $t_type = $lib_sections[$i][1]; } }
	title("$t_tut $t_type",'editing'); ?>

	<p>
	<span style="padding:2px;border:1px solid <?=$colors['msg_info_border']?>"><b>show by editor:</b> <a href="?page=tutorials&game=<?php echo $game; if($type) echo '&type='.$type; ?>" class=white>all</a>
	<?php for($i=0;$i<$edlength;$i++) echo '<a href="?page=tutorials&game='.$game.'&type='.$type.'&editor='.$editors[$i].'"><img src="images/icon_'.$editors[$i].'.gif" height=16 width=16 align=texttop border=0 alt="'.$editors[$i].'"></a> '; ?>
	</span>

<p><b><?=ucfirst($lib_sections[$t_id][2])?>:</b>

	<table width="99%" cellpadding=3 cellspacing=0 style="line-height:1.3em">
	<tr><td width=16></td><td width=16></td><td width="90%"></td><td width="5%" style="font-size:8pt"><b><a href="?page=tutorials&amp;game=<?=$game?>&amp;type=<?=$type?>&amp;sort=rating" class=white>rated /10</a></b></td></tr>

<?php
	gettuts($type);
	echo '</table></p>';

footer(); }

if($byuser=$_GET['byuser']) {
        title($t_died.' Tutorials by '.mysql_result(mysql_query("SELECT username FROM users WHERE user_id = '$byuser' LIMIT 1"),0));
	$sections = array(); $c = 0; ?>

	<table width="99%" cellpadding=3 cellspacing=0>
	<tr><td width=16></td><td width=16></td><td width="90%"></td><td width="5%" style="font-size:8pt"><b><a href="?page=tutorials&game=<?=$game?>&type=<?=$type?>&sort=rating" class=white>rated /10</a></b></td></tr>

	<?php
	$sql = mysql_query("SELECT * FROM articles WHERE section = 'editing' AND user_id = '$byuser' ORDER BY type DESC");
	while($uarray = mysql_fetch_array($sql)) { $c++;
	        
	        if(!$sections[$uarray['type']]) {
	        	include('lib/tutsections_'.$uarray['game'].'.php');
	        	for($i=0;$i<$lib_seclength;$i++) {
	        	        if($lib_sections[$i][0]==$uarray['type']) $this_type = $lib_sections[$i][1];
        		}
			$sections[$uarray['type']] = $this_type;
			echo '<tr><td colspan=3><font size=4>'.$this_type.'</font></td></tr>';
         	}

		echo "\n".'<tr onmouseover="style.background=\''.$colors['trmouseover'].'\';style.cursor=\'hand\'" onmouseout="style.background=\''.$colors['bg'].'\'" onclick="location.href=\'editing.php?page=tutorials&id='.$uarray['id'].'\'"><td valign=top>';
			if($uarray['editor']) echo '<img src="images/icon_'.$uarray['editor'].'.gif" alt="relevant to '.$uarray['editor'].'" align=texttop height=16 width=16>';
		echo '</td><td valign=top>';
			if($byuser || $editor) echo '<img src="images/icon_'.$uarray['game'].'.gif">';
			elseif($uarray['mod']) echo ' <img src="images/icon_'.$uarray['game'].'_'.$uarray['mod'].'.gif" align=texttop height=16 width=16>';
		echo '</td><td><b><a href="?page=tutorials&game='.$uarray[game].'&id='.$uarray['id'].'">'.stripslashes($uarray['title']).'</a></b>';
		echo '<br>';
		echo '<font color="'.$colors['lighttext'].'">'.stripslashes($uarray['description']).'</td><td align=center>';
		if($uarray['rating']>0) echo '<font size=5>'.$uarray['rating'].'</font>';
		echo '</td></tr>';
	} if(!$c) echo '<tr><td></td><td colspan=2>No tutorials by this user found. <a href="editing.php?page=tutorials"><b>Click here</b></a> to return to the tutorials menu.</td></tr>';
	
	echo '</table>';

footer(); }

	title($t_editing.' '.$garray['name'].' Tutorials'.$title,'editing'); ?>

	<p>

	<?php if($userdata) echo '<span style="float:right"><a href="cp.php?mode=tutorials&game='.$game.'"><b>Click here</b></a> to submit a tutorial!</span>'; ?>

	<span style="padding:2px;border:1px solid <?=$colors['msg_info_border']?>"><b>show by editor:</b> <a href="?page=tutorials&game=<?=$game?>" class=white>all</a>
	<?php for($i=0;$i<$edlength;$i++) echo '<a href="?page=tutorials&game='.$game.'&editor='.$editors[$i].'"><img src="images/icon_'.$editors[$i].'.gif" height=16 width=16 align=texttop border=0 alt="'.$editors[$i].'"></a> '; ?>
	</span>

	<p>
	<table width="99%" cellpadding=3 cellspacing=0>
	<tr><td width=16></td><td width=16></td><td width="90%"></td><td width="5%"></td></tr>

	<?php if(!include('lib/tutsections_'.$game.'.php')) error_die('<i>Could not load tut library...</i>'); $j=0;
		for($i=0;$i<$lib_seclength;$i++) { if($j!=0) echo '<tr><td height=10></tr>'; ?>
		<tr><td colspan=<?=(($j>0)?'4':'3')?>><h1><a href="?page=tutorials&game=<?=$game?>&type=<?=$lib_sections[$i][0]?>" class=white><?=strtolower($lib_sections[$i][1])?>:</a></font> <span class="help" style="font-weight:normal">(<?=$lib_sections[$i][2]?>)</span></h1></td>
		<?php if($j==0) echo '<td style="font-size:8pt"><b>rated /10</b></td>'; echo '</tr>';
		gettuts($lib_sections[$i][0],5); $j++;
	} ?>

	</table></p>

<?php footer(); ?>
