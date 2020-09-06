<?php
	tracker('Map editing section'); 
	title("Map Editing for $garray[name]",editing); 
	if(!$garray['editor']) { echo 'Sorry, this game does not have any editing information for it yet.'; footer(); }
?>

<table width="99%" cellspacing=0 cellpadding=0 style="font-size:11px">
	<form action="editing.php?search" method="post" name="equery">
		<input type="hidden" name="from" value="editing">
		<input type="hidden" name="searchforumsubject" value="on">
		<input type="hidden" name="searchforummessage" value="on">
		<input type="hidden" name="forumselect" value="<?=$forum_id?>">
		<input type="hidden" name="searchgame" value="<?=$game?>">

	<tr bgcolor="<?=$colors['bg']?>">
		<td valign=top width=20><img src="themes/<?=$theme?>/q.gif" style="margin:2px" alt=""></td>
		<td colspan=2><font size=2>
		In order to post a new topic in the <?php echo $garray[name];?> editing forum you must first search
		for your problem here. Enter up to 5 relevant words or a phrase in the box below and click where
		you want to search. Or check out our <b><a href="?page=tutorials&game=<?=$game?>">tutorials</a></b>!
	</td></tr>
	<tr bgcolor="<?=$colors['bg']?>"><td height=16 colspan=3> </td></tr>

<tr><td colspan=3 height=1 bgcolor="<?=$colors['lightbg']?>"> </td></tr>
<tr bgcolor="<?=$colors['darkbg']?>" style="padding-top:5px; padding-bottom:20px" height=65>
	<td></td>
	<td width="50%"><b><font color="<?=$colors['item']?>">keywords: </b></font>
		<span style="position:absolute;left:290"><input type="text" name="search" class="textinput" size=32 maxlength=48 style="z-index:-1"></span>
		<p><b><font color="<?=$colors['item']?>">section: </b></font>
		<span style="position:absolute;left:290"><select name="section">
		<?php if(!include('lib/forums_'.$game.'.php')) echo '</select><i>error opening section lib</i>';
			else { while(list($var,$val)=each($helpsections)) echo '<option value="'.$var.'">'.$var; } ?>
		</select></span>
	</td>
	<td align=center><font color="<?=$colors['item']?>"><b>search in</font>
		<font color="<?=$colors['lighttext']?>">
		<input type="checkbox" name="searchglossary" id="fglos"><label for="fglos">glossary</label>
		<input type="checkbox" name="searchentities" id="fent"><label for="fent">entities</label>
		<input type="checkbox" name="searchtutorials" id="ftut" checked><label for="ftut">tutorials</label>
		<input type="checkbox" disabled checked>forums
	</td>
</tr>
<tr><td colspan=3 height=1 bgcolor="<?=$colors['lightbg']?>"> </td></tr>

<tr bgcolor="<?=$colors['bg']?>"><td height=10 colspan=3></td></tr>
<tr bgcolor="<?=$colors['bg']?>">
	<td width="50%" colspan=2 valign=top>
		<input type="submit" name="submit" value="search" class="submit3" onclick="if(document.forms['equery'].elements['search'].value=='') return false" style="margin-left:20px">
		<input type="hidden" name="game" value="<?=$game?>">
		<p><span class="subtitle">Latest <?=$game?> files:</span>
		<table width="100%" cellspacing=0 cellpadding=2 style="font-size:11px">
		<?php	$sql = mysql_query("SELECT file_id,type,filename,subcat FROM files WHERE game = '$game' ORDER BY file_id DESC LIMIT 5");
			while($array = mysql_fetch_array($sql)) { ?>
				<tr>
				<td width="10%" align=right><a href="editing.php?page=files&game=<?=$game?>&type=<?=$array['type'].(($array['subcat'])?'&subcat='.$array['subcat']:'')?>" class=white><?=str_replace('tie','ty',substr($array[type],0,-1))?>:</a></td>
				<td width="90%"><b><a href="editing.php?page=files&download=<?=$array['file_id']?>"><?=$array['filename']?></a></b></td>
			</tr>
		<?	} ?>
		</table>
	</td>
	<td width="50%" valign=top>
	Search is 'boolean', meaning you can use + and - signs in front of words, and enclose phrases in double quotes " "
	like many internet search engines- search looks for articles containing <b>any</b> of the words by default.
	Words shorter than 4 letters are ignored.

	<p><fieldset><legend>random editing tip - <a href="javascript:void(0)" onclick="popwin('popup.php?mode=quicktips&game=<?=$game?>','yes')" onmouseover="window.status='View all editing tips';return true" onmouseout="window.status='';return true" class="green">view all</a></legend>
		<?php include('lib/tip_'.$game.'.php'); $rand = rand(0,count($tips)-1); echo stripslashes($tips[$rand]); ?>
	</fieldset>
	</td>
</tr>
</form>
</table>
</p>

<p><div class="subtitle"><b>Unanswered <?=$game?> editing questions:</b></div>
<table width="99%" cellspacing=0 cellpadding=2 style="font-size:11px">

<?php	$ppp = 20; $qpp = 20; if(!$start) $start=0;
	//$numquestions = mysql_result(mysql_query("SELECT COUNT(topic_id) FROM topics WHERE answered = 'n' AND forum_id = '$forum_id'"),0);
	$sql = mysql_query("SELECT * FROM topics WHERE forum_id = '$forum_id' AND answered = 'n' ORDER BY topic_replies LIMIT $start,$qpp"); $c='';
	while($tarray = mysql_fetch_array($sql)) {
		if(!$c) echo '<tr><td></td><td><b>title</b></td><td><b>posted by</b></td><td align=center><b>replies</b></td><td align=center><b>last post</b></td></tr>';
		$numpages = ceil(($tarray['topic_replies']+1)/$ppp); $newestpostlink = '<a href="forums.php?forum='.$forum_id.'&topic='.$tarray['topic_id'].'&goto='.($numpages*$ppp).'#endofpage">';
		echo "\n".'<tr onmouseover="javascript:style.background=\''.$colors['trmouseover'].'\'" onmouseout="javascript:style.background=\''.$colors['bg'].'\'"><td bgcolor="'.$colors['bg'].'">'.$newestpostlink;
		if($tarray['topic_time']>$last_visit) echo '<img src="themes/'.$theme.'/topic_new_help.gif" border=0>'; else echo '<img src="themes/'.$theme.'/topic_old_help.gif" border=0>';
		echo '</a></td><td><font size=2><b><a href="forums.php?forum='.$forum_id.'&topic='.$tarray['topic_id'].'">'.stripslashes($tarray['title']).'</a></b></font>';
		echo '<br>'.stripslashes($tarray['topic_description']).'</td>';
		echo '<td><font size=2>'.userdetails($tarray['topic_poster'],'','return').'</font></td>';
		echo '<td align=center><font size=2>'.$tarray['topic_replies'].'</font></td>';
		echo '<td align=center>'.agotime($tarray['topic_time'],'1').'<br>by ';
			$lastposterid = mysql_result(mysql_query("SELECT poster_id FROM posts WHERE post_id = '$tarray[topic_last_post_id]' LIMIT 1"),0);
			userdetails($lastposterid);
	$c++; } if(!$c) echo '<tr><td></td><td><b>no problems need answering!</b></td></tr>';
		else echo '<tr><td height=10></tr><tr><td></td><td><b><a href="forums.php?forum='.$forum_id.'"><font size=2>more: visit the '.$game.' editing forums</font></a></b></td></tr>';
?>
<tr><td width=32></td><td width=50%></td><td width=20%></td><td width=10%></td><td width=20%></td></tr>
</table></p>
<?php if($c) { ?>
	<table cellpadding=2 align=center><tr>
	<td width=15></td><td><img src="themes/<?=$theme?>/topic_old_help.gif" align=left><font color='#cc6600'><b>No new posts<br>since last visit</font></b></td>
	<td width=15></td><td><img src="themes/<?=$theme?>/topic_new_help.gif" align=left><font color='#cc6600'><b>New posts<br>since last visit</b></font></td>
	</tr></table>
<? } ?>
