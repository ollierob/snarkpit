<?php
	if(!$id) $id = $_GET['id']; $edit = $_GET['edit']; if(!$type) $type = $_GET['type'];
	$array = ''; $msg = '';

if($edit) {
	$sql = mysql_query("SELECT * FROM comments WHERE id = '$edit' LIMIT 1");
	if(!$carray = mysql_fetch_array($sql)) error_die('Comment doesn\'t exist');
	if($carray['user_id']!=$userdata['user_id'] && $userdata[user_level]<3) error_die('This isn\'t your comment so you can\'t edit it');
	$text = strip_tags(str_replace('<br>',"\n",stripslashes($carray['text'])));
	title('Edit comment','');
	
	if($carray['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) { $norate = true; $msg = 'You cannot alter the score other people have given your '.$type; }

}

if($type=='map') {
	$sql = mysql_query("SELECT * FROM maps WHERE map_id = '$id' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('Map doesn\'t exist');
	$sql = mysql_query("SELECT name FROM games WHERE id = '$array[game]' LIMIT 1"); $garray = mysql_fetch_array($sql); 
	$typelink = '<a href="maps.php?game='.$array[game].'&mod='.$array[mod].'" class=white>'.stripslashes($garray['name']).' ('.$array[mod].')</a>';
	$reslink = '<a href="maps.php?map='.$id.'" class=white>'.stripslashes($array[name]).'</a>';
	if(!$array['map_url']) { $norate = 1; $msg = 'You can\'t rate this map- there is no download link for it, so presumably you haven\'t played it!'; }
}

elseif($type=='tutorial') {
	$sql = mysql_query("SELECT * FROM articles WHERE id = '$id' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die("Tutorial doesn't exist");
	$sql = mysql_query("SELECT name FROM games WHERE id = '$array[game]' LIMIT 1"); $garray = mysql_fetch_array($sql); 
	$typelink = '<a href="editing.php?game='.$array[game].'&page=tutorials" class=white>'.stripslashes($garray[name]).' tutorials</a>';
	$reslink = '<a href="editing.php?page=tutorials&id='.$id.'" class=white>'.stripslashes($array[title]).'</a>';
}

elseif($type=='prefabs'||$type=='models') {
	$sql = mysql_query("SELECT * FROM files WHERE type = '$type' AND file_id = '$id' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('Prefab/model doesn\'t exist','');
	$sql = mysql_query("SELECT name FROM games WHERE id = '$array[game]' LIMIT 1"); $garray = mysql_fetch_array($sql); 
	$typelink = '<a href="editing.php?game='.$array['game'].'&amp;page=files&amp;type='.$type.'" class=white>'.stripslashes($garray['name']).' '.$type.'</a>';
	$reslink = '<a href="editing.php?game='.$array['game'].'&amp;page=files&amp;type='.$type.'&amp;id='.$id.'" class=white>'.stripslashes($array['filename']).'</a>';
	$array['user_id'] = $array['author'];
}

if(substr_count($_SERVER['PHP_SELF'],'cp.php') && !$edit) title('New Comment » '.ucfirst($type).': '.$typelink.' » '.$reslink,'');

?>

<b>Please only post helpful comments such as corrections or additions to the <?=$type?>!</b> Comments such as "post screenshots," "looks good" or "looks rubbish" 
are not particularly helpful to anyone; please say <i>why</i> you think what you do. We will delete useless comments, and bar you from commenting if you keep doing this.
If you want to simply rate it then leave the comment text blank. Note that your comment is just plain text and can't be formatted.

<?php if($_GET['thread']) msg('This map has a <b><a href="forums.php?forum=2&topic='.$_GET['thread'].'">forum thread</a></b> where you can post comments and critiques- please only post a comment if you want to rate/review it.','info','','90%'); ?>

<p>
<form action="cp.php" method="post" name="commentform">
<table width="100%" cellpadding=2 cellspacing=0 class="help">
<tr>
<?php
	if($commentboxwidth>300||!$commentboxwidth) echo '<td width="10%" valign=top align=right><b>comment:</b></td><td width="90%">';
	else echo '<td colspan=2>';
?>

<span id="action"><a href="javascript:void(0)" onclick="setObjToCheck('text'); spellCheck('textarea');">Check spelling</a></span>
<span id="status" class="status"></span><br>
<div id="results" class="results"></div>
<textarea style="height:100px;width:<?=(($commentboxwidth)?$commentboxwidth:350)?>px" name="text" id="text" onFocus="setObjToCheck('text'); resetAction()"><?=$text?></textarea>
<div id="suggestions" class="suggestions"></div>
</td></tr>

<?php if($array['user_id']!=$userdata['user_id']) { ?>
<tr><td align=right><b>rating:</b></td><td>

	<?php
		$sql = mysql_query("SELECT * FROM comments WHERE article_id = '$id' AND user_id = '$userdata[user_id]' AND type = '$type' AND rating!=''");
		if($xarray = mysql_fetch_array($sql) AND $xarray['id']!=$edit) {
			$norate=1; 
			echo 'You\'ve already rated this '.$type.', <a href="cp.php?mode=newcomment&type='.$type.'&id='.$id.'&edit='.$xarray[id].'">edit this comment</a> if you want to change it';
		}
	if(!$norate) {
		$rated = array(); $rated[$carray['rating']] = ' selected';
		$text = array(); $text[1] = ' - worst'; $text[10] = ' - best';
		echo '<select name="rating"><option value="">don\'t rate</option>';
		for($i=10;$i>=1;$i--) echo '<option value='.$i.$rated[$i].'>'.$i.$text[$i];
		echo '</select> '.(($type=='map')?'<br><b><font color="red">do not rate this map unless you have actually played it!</font></b>':'');

	} else echo $msg; ?>

</td></tr>
<?php } ?>

<tr><td></td><td><input type="submit" name="submit" value="Submit" id="submitbutton" class="submit" onclick="if(!document.forms['commentform'].elements['rating']){if(!document.forms['commentform'].elements['text'].value){alert('Please make a comment on this <?=$type?>!'); return false;}}else{if(!document.forms['commentform'].elements['text'].value && !document.forms['commentform'].elements['rating'].selectedIndex){alert('Please rate or make a comment on this <?=$type?>!');return false}}">
	<input type="hidden" name="type" value="<?=$type?>">
	<input type="hidden" name="id" value="<?=$id?>">
	<input type="hidden" name="edit" value="<?=$edit?>">
	<input type="hidden" name="mode" value="newcomment">
<?php
	if(!$refer) $refer = str_replace('#comments','',$_SERVER['HTTP_REFERER']);
	if($refer) echo '	<input type="hidden" name="redirect" value="'.$refer.'">';
?>

</td></tr>
</table></form>
</legend>
