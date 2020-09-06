<?php
	title('Search','index');

	if($wut) {
		if(substr_count($wut,'articles')) $sa = ' CHECKED';
		if(substr_count($wut,'files')) $sf = ' CHECKED';
		if(substr_count($wut,'forums')) { $sfs = ' CHECKED'; $sfm = ' CHECKED'; }
		if(substr_count($wut,'maps')) $sm = ' CHECKED';
	}
?>

<table width="99%" cellspacing=0 cellpadding=2 style="font-size:11px" bgcolor="<?=$colors['darktext']?>">
<form name="searchform" action="index.php?page=query" method="post">

<tr bgcolor="<?=$colors['bg']?>"><td rowspan=2 valign=top><img src="themes/<?=$theme?>/q.gif"></td><td colspan=2></td></tr>
<tr bgcolor="<?=$colors['bg']?>">
	<td style="font-size:10pt" colspan=2>Enter some relevant words/phrases below, select the appropriate 
		sections of the website that you want to search, and click 'search'. If you have an editing problem use 
		<a href="editing.php"><b>this search page</b></a> instead as it can perform a wider search.</p>
	</td>
</tr>
<tr bgcolor="<?=$colors['bg']?>"><td></td>
	<td></p><b><font color="<?=$colors['item']?>">keywords: </b></font><input type="text" name="search" class="textinput" size=32 maxlength=48>
	</td>
	<td align=center><b><font color="<?=$colors['lighttext']?>">
		<input type="radio" name="which" value="all" CHECKED>all words
		<input type="radio" name="which" value="phrase">exact phrase
		<input type="radio" name="which" value="google">using Google
	</b></td>
</tr>
<tr bgcolor="<?=$colors['bg']?>"><td height=10 colspan=3></td></tr>

<tr bgcolor="<?=$colors['lighttext']?>"><td colspan=3></td></tr>

<tr>
	<td></td>
	<td><b><font color="<?=$colors['item']?>">site options:</td>
	<td rowspan=8 align=center>
		<b><font color="<?=$colors['item']?>">search by game
		<select name="searchgame">
		<option value="" id="cat_white">all/none
		<?php $sql = mysql_query("SELECT id,name FROM games");
		while($garray = mysql_fetch_array($sql)) { echo '<option value="'.$garray[id].'"'; if($_GET['game']==$garray[id]) echo ' SELECTED'; echo '>'.$garray[name]; }
		?>
		</select></p>
	</td>
</tr>
<tr><td></td>
	<td><blockquote><b>search: <font color="<?=$colors['lighttext']?>">
	<input type="checkbox" name="searcharticles" disabled>articles
	<input type="checkbox" name="searchtutorials">tutorials
	<input type="checkbox" name="searchreviews">reviews</font>
	<br>
	news search: <font color="<?=$colors['lighttext']?>">
	<input type="checkbox" name="searchnews">site news
	<input type="checkbox" name="searchplan">user news
	</td>
</tr>

<tr><td height=10></td></tr>
<tr><td></td><td><b><font color="<?=$colors['item']?>">forum options:</td></tr>
<tr><td></td>
	<td><blockquote><b>search: <font color="<?=$colors['lighttext']?>">
	<input type="checkbox" name="searchforumsubject"<?=$sfs?>>subject
	<input type="checkbox" name="searchforummessage"<?=$sfm?>>message</font>
	<br>in forum <select name="forumselect">
		<option value="all" id="cat_white">All forums
		<?php $sql = mysql_query("SELECT * FROM forums ORDER BY forum_id");
		while($farray = mysql_fetch_array($sql)) { echo '<option value="'.$farray[forum_id].'"'; if($forum==$farray[forum_id]) echo ' SELECTED'; echo '>'.stripslashes($farray[forum_name]); }
		?>
	</select>
	</td>
</tr>

<tr><td height=10></td></tr>
<tr><td></td><td><b><font color="<?=$colors['item']?>">file options:</td></tr>
<tr><td></td>
	<td><blockquote><b>search: <font color="<?=$colors['lighttext']?>">
	<input type="checkbox" name="searchfiles"<?=$sf?>>files
	<input type="checkbox" name="searchmaps"<?=$sm?>>maps
	</td>
</tr>

<tr bgcolor="<?=$colors['lighttext']?>"><td colspan=3 height=1> </td></tr>

<tr bgcolor="<?=$colors['bg']?>"><td></td><td colspan=3><b><font color="<?=$colors['item']?>">order results by <font color="<?=$colors['lighttext']?>">
	<input type="radio" name="sort" value="id" CHECKED>resource id
	<input type="radio" name="sort" value="date DESC">date (newest first)
	<input type="radio" name="sort" value="date ASC">date (oldest first)
	</td>
</tr>

<tr bgcolor="<?=$colors['bg']?>"><td height=10 colspan=3> </td></tr>

<tr bgcolor="<?=$colors['bg']?>"><td width=1></td><td width="50%">
		<input type="submit" name="submit" value="search" class="submit3" onclick="if(search.value=='') return false">
		<input type="hidden" name="game" value="<?=$game?>">
	</td><td width="50%"></td></tr></form>
</table>
