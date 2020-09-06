<?php
	$type = $_GET['type']; $subcat = $_GET['subcat']; $comments = $_GET['comments']; $sort = $_GET['sort'];

	$gtitle = $t_editing.' <a href="?game='.$game.'&page=files" class=white>'.$garray[name].' Files</a>';
	$ttitle = ' » <a href="?game='.$game.'&page=files&type='.$type.'" class=white>'.ucfirst($type).'</a>';

if($comments) {
	$sql = mysql_query("SELECT * FROM files WHERE file_id = '$comments' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('File doesn\'t exist');
	if($array['type']!='prefabs' && $array['type']!='models') error_die('File isn\'t a prefab or model ('.$array['type'].') so you can\'t comment on it');

	@title($gtitle.': <a href="editing.php?game='.$array['game'].'&amp;page=files&amp;type='.$array['type'].'&amp;subcat='.$array['subcat'].'" class=white>File Comments</a>','');
	echo '<table width="99%" cellpadding=1 cellspacing=1><tr>';
		echo '<td valign=top width=100 rowspan=2 valign=top>'; if($array['screenshot']) echo '<img src="files/'.$array['game'].'/images/'.$array['screenshot'].'">';
		echo '</td><td style="font-size:10pt" width="100%" valign=top>';
		echo '<a href="?page=files&download='.$array['file_id'].'">'.$fileimg.' <b>'.stripslashes($array['filename']).'</b></font></a>';
		if($array['author']) { echo ' <font color="'.$colors['medtext'].'">made by <b>'.((is_numeric($array['author']))?userdetails($array['author'],'white','return'):stripslashes($array['author'])).'</b>'; }
		echo ', <font color="'.$colors['medtext'].'">'.$array['downloads'].' downloads</td></tr>';
		echo '<tr><td valign=top>'.$array['description'];
	echo '</td></tr></table></p>';

	subtitle('<a name="comments">file comments</a>');
	getcomments($array['type'],$comments,'editing.php?page=files&comments='.$comments);

	if($d!=$array['comments']) @mysql_query("UPDATE files SET comments = '$d' WHERE file_id = '$comments' LIMIT 1");

footer(); }

$title = $t_editing.' '.$garray['name'].' Files';

if($type) $title = $gtitle.' » '.ucfirst($type);
if($subcat) $title = $gtitle.$ttitle.' » '.ucfirst($subcat);

	title($title,'editing'); echo 'Select a different game from the menu on the left, or <a href="index.php?page=search&wut=files&game='.$game.'"><b>search for a '.$garray[name].' file</b></a>.</p>';
	tracker('Files Section','editing.php?page=files&amp;game='.$game);

	//msg('Sorry, some files are currently unavailable due to our recent server move. They should be back within a few days.','warning');

if($subcat) {
	if(!$sort) $sort = 'downloads';
	echo '<p>Order by: ';
	$sarray = array('name','date','downloads');
	for($i=0;$i<=2;$i++) { if($sort==$sarray[$i]) echo '<b>'.$sarray[$i].'</b>'; else echo '<a href="editing.php?page=files&game='.$game.'&type='.$type.'&subcat='.$subcat.'&sort='.$sarray[$i].'">'.$sarray[$i].'</a>'; if($i!=2) echo ' - '; }
	echo '<br>';
}


$orderby = 'type,file_id DESC'; $seltoplevel = 'AND top = 1';

	$orderby = 'filename';

if($type) {
	$seltype = "AND type = '$type'";
	$$type = 1; $seltoplevel = '';
	$showfiles = 0;
	$topborder = ' style="border-top:1px solid '.$colors['lightbg'].';border-bottom:1px solid '.$colors['lightbg'].'"';
} else {
	$seldistinct = 'DISTINCT(type),'; 
	$showfiles = 1;
	$topborder = '';
}

if($subcat) {
	$selsubcat = "AND subcat = '$subcat'"; 
	$numsubcatrows = mysql_result(mysql_query("SELECT files FROM files_cats WHERE game = '$game' AND name = '$subcat' AND under = '$type' LIMIT 1"),0); 
	$showfiles = 1;
}

	if($sort=='name') $orderby = 'filename';
	if($sort=='downloads') $orderby = 'downloads DESC';
	if($sort=='date') $orderby = 'date DESC';

	$countsubcat = 0; $cf = 0;

	echo "\n\n".'<TABLE width="99%" cellpadding=2 cellspacing=0'.$topborder.'>';

	$sql = mysql_query("SELECT * FROM files WHERE game = '$game' $seltoplevel $seltype $selsubcat ORDER BY $orderby");
	while($array = mysql_fetch_array($sql)) { $cf++; $cs = 0; if(!$type) $starttr = 0;

		if($type) $array['type'] = $type;
		if(!$array['description']) $array['description'] = '<i>no description available</i>';
		//if($type && $cf==1) echo "\n\n".'<TR bgcolor="'.$colors['lightbg'].'"><td colspan=2 height=1> </td></TR>';

		if(!$subcat) { //index/type
			$psql = mysql_query("SELECT * FROM files_cats WHERE under = '$array[type]' AND game = '$game' ORDER BY name");
			$subcats = mysql_num_rows($psql); if(!$subcats) $showfiles = 1;
		}

		if(!$type) { //index
			if($cf!=1) echo '<tr><td height=10></td></tr>';
			echo "\n".'<TR><td colspan=2><a href="?page=files&game='.$game.'&type='.$array['type'].'" class=white><font size=4>'.ucfirst($array['type']).':</font></a></TR>'."\n".'<TR bgcolor="'.$colors['lightbg'].'"><td colspan=2></td></TR>';
		}

		//if showing files!
		if($showfiles) {
			if(!$starttr) { echo "\n".'<TR bgcolor="'.$colors['darkbg'].'">'; $starttr = 1; }
			echo '<td width="50%" valign=top onmouseover="document.getElementById(\''.$cf.'\').style.border=\'1px solid '.$colors['msg_info_border'].'\';return true" onmouseout="document.getElementById(\''.$cf.'\').style.border=\'1px solid '.$colors['darkbg'].'\';return true">';
			if($array['icon']) $fileimg = $array['icon']; else {
			        $fileext = substr($array['url'],-3);
				$fileimg = 'gfx_'.$fileext.'.gif';
				if(substr_count($array['url'],'http://')) { $newwin = true; $fileimg = 'gfx_website.gif'; } else $newwin = false;
			}
			echo '<img src="images/'.$fileimg.'" style="position:relative;top:4px;float:left;">';
			echo '<div id="'.$cf.'" class="forumtext" style="font-size:8pt;position:relative;margin-left:22px;clear:right;margin-bottom:10px;border:1px solid '.$colors['darkbg'].';padding:2px;">';
			if($array['screenshot']) {
				if(substr($array['screenshot'],-10)=='_thumb.jpg') $thumb = true; else $thumb = false;
				if($thumb) echo '<a href="javascript:void(0)" onclick="popwin(\'screenshot.php?img=files/'.$array['game'].'/images/'.(substr($array['screenshot'],0,-10)).'.jpg&theme='.$theme.'\')" onMouseOver="window.status=\'Click to view full sized screenshot (popup window)\'; return true" onMouseOut="window.status=\'\'">';
				echo '<img align=right src="files/'.$array['game'].'/images/'.$array['screenshot'].'" border=0 class="thumb">';
				if($thumb) echo '</a>';
			}
			echo '<font size=2><a href="?page=files&download='.$array['file_id'].(($newwin)?'" target="_blank':'').'"><b>'.ucfirst(stripslashes($array['filename'])).'</b></font></a><br>';
			echo ucfirst(stripslashes($array['description'])).'</font><br><font color="'.$colors['medtext'].'">'.$array['downloads'].' downloads';
			if(!$newwin) { if($size = floor(filesize('files/'.$game.'/'.$array['url'])/1024)) echo ', '.$size.'kb'; }
			if($array['subcat'] && !$subcat) echo '<br>'.$array['type'].' » <a href="?page=files&game='.$game.'&type='.$array['type'].'&subcat='.$array['subcat'].'">'.$array['subcat'].'</a>';
			if($type=='prefabs'||$type=='models') { echo '<br><a href="editing.php?page=files&comments='.$array['file_id'].'">'.$array['comments'].' comments</a>'; if($userdata) echo ' (<a href="cp.php?mode=newcomment&amp;type='.$type.'&amp;id='.$array['file_id'].'">+add</a>)'; }
			if($array['author']) { echo '<br>'; if(substr_count($array['author'],'http://')) echo '<a href="'.$array['author'].'" target="_blank">'.$array['author'].'</a>'; else { echo 'made by <b>'.((is_numeric($array['author']))?userdetails($array['author'],'white','return'):stripslashes($array['author'])).'</b>'; } }
			if(($array['author'] && $userdata['user_id']==$array['author'])||$userdata['user_level']==4) echo ' <a href="cp.php?mode=files&amp;action='.$type.'&amp;edit='.$array['file_id'].'"><img src="images/gfx_edit.gif" border=0 align="middle"></a>';
			echo '</div></td>';

			if($starttr % 2) {} else echo "</TR>\n<TR bgcolor=\"".$colors['darkbg']."\">";
			if(!$type) { 
				echo '<td align=center>';
				if(!$subcats) echo '<b><a href="?page=files&game='.$game.'&type='.$array[type].'">all '.$array[type].'...</a></b>';
				echo '</td></TR>'."\n".'<TR bgcolor="'.$colors['lightbg'].'"><td colspan=2 height=1> </td></TR>';
			}
			$starttr++;
		}

		//if subcat listing
		if(!$subcat && $subcats && !$subcatarray[$array['type']]) { while($parray = mysql_fetch_array($psql)) { $cs++;
			if($cs==1) echo "\n".'<TR><td colspan=2><table width="100%" cellpadding=2 cellspacing=1>';
			echo '<tr><td></td><td><a href="?page=files&game='.$game.'&type='.$array['type'].'&subcat='.$parray['name'].'" class=white><img align=texttop src="images/gfx_folder.gif" border=0>';
			echo ' <b>'.stripslashes($parray['name']).'</b></a> :: '.ucfirst(stripslashes($parray['description'])).' <font color="'.$colors['item'].'">'.$parray['files'].' file'.(($parray['files']!=1)?'s':'').'</font>';
		} echo '<tr><td width=16><img src="images/null.gif" width=14 height=1></td></tr></table></td></TR>'; $subcatarray[$type]=1; } 

	} 

	if(!$cf) echo '<tr><td colspan=2><i>No files found</i></td></tr>';
	if($subcat) { if($cf!=$numsubcatrows) @mysql_query("UPDATE files_cats SET files = '$cf' WHERE game = '$game' AND name = '$subcat' AND under = '$type' LIMIT 1"); }

	if($starttr % 2) {} else echo '<td></td></tr>';
?>

</TABLE>

<p>Got a file you want to see here? <a href="mailto:leperous.at.snarkpit.net?subject=<?=$game?> file">E-mail it to us</a>, or if it's a prefab or model <a href="cp.php"><b>add it yourself</b></a>!
<?php $sql = mysql_query("SELECT * FROM links WHERE type = 'files' AND game = '$game'"); $c = 0;
	while($array = mysql_fetch_array($sql)) {
	        $c++;
	        if($c==1) echo '<p>Also check out these websites for more '.$game.' files: ';
	        echo (($c!=1)?', ':'').'<a href="'.$array['url'].'" target="_blank"><b>'.$array['name'].'</b></a>';
	}
?><p>
