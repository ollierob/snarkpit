<?php tracker('Links','');

	$show = $_GET['show'];

if($show!='homepages') {
	title('Links','none','','','','');

	echo '<a href="http://www.snarkpit.net" target="_blank" title="Link to us!"><img src="images/snarkpit3.gif" align=right border=0></a>';
	echo 'Have a link you want to see up here? Send it to us via <a href="cp.php?mode=feedback">this form</a>, or e-mail us. ';
	echo 'If you\'re interested in working in the games industry, check out <a href="http://www.gamasutra.com/php-bin/jobs_display.php" target="_blank"><b>Gamasutra</b></a> for job listings.';

	echo "\n".'<p><table width="100%" class="forumtext">';

	$sql = mysql_query("SELECT * FROM links ORDER BY type,name"); $c='';
	while($array = mysql_fetch_array($sql)) {
		$sitetype = 'type'.$array[type];
		if(!$$sitetype) { $$sitetype++; if($c) echo '<tr><td height=10></td></tr>'; echo '<tr><td colspan=2>'.subtitle($array['type'],'return').'</div></td></tr>'; }

		echo '<tr><td valign=top align=right>';
			if($array['language']) echo '<img src="images/flag_'.$array['language'].'.gif" align=middle>';
			if($array['game']) echo '<img src="themes/'.$images['moddir'].'/icon_'.$array['game'].'.gif" align=middle>';
		echo '</td><td>';
		if($array['icon']) echo '<img src="images/'.$array['icon'].'" align=right>';
		echo '<b><a href="'.$array['url'].'" target="_blank">'.$array['name'].'</a></b><br>'.stripslashes($array['description'])."</td></tr>\n";
	$c++; }
?>

<tr><td height=10></td></tr>
<tr><td colspan=2><?=@subtitle('mapper homepages')?></font></td></tr>
<tr><td></td><td><b><a href="?page=links&show=homepages">click here</a></b> for the directory (<?php echo mysql_result(mysql_query("SELECT count(user_id) FROM users_profile WHERE website != ''"),0);?> sites)</td></tr>
<tr><td></td><td><b><a href="http://www.hlgaming.com/" target="_blank">Half-Life Gaming.com</a></b><br>Free Half-Life website hosting</td></tr>

<tr><td width="5%"></td><td width="95%"></td></tr>
</table>

<?php } else {
	title("<a href=\"?page=links\" class=white>Links</a> » Mapper Websites",index);
	$sql = mysql_query("SELECT username,website FROM users_profile WHERE website != '' ORDER BY username");
	while($array = mysql_fetch_array($sql))
		echo "\n".'<a href="users.php?name='.$array[username].'" class=white>'.$array[username].'</a>- <a href="'.$array[website].'" target="_blank">'.$array[website].'</a><br>';

} ?>
