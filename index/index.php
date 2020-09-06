<?php
	tracker('Front Page','');
	//referals('','');

	$numusers = mysql_result(mysql_query("SELECT SQL_CACHE hits FROM counter WHERE name = 'users' LIMIT 1"),0);
	$forumposts = mysql_result(mysql_query("SELECT COUNT(*) FROM posts"),0);
	$nummaps = mysql_result(mysql_query("SELECT COUNT(*) FROM maps"),0);
	$numtuts = mysql_result(mysql_query("SELECT COUNT(*) FROM articles"),0);
	$newestuser = mysql_result(mysql_query("SELECT SQL_CACHE username FROM users WHERE activated = '1' ORDER BY user_id DESC LIMIT 1"),0);

	//$fopen = fopen('hits.txt','a');
	//if(!$fw = fwrite($fopen,$_SERVER['REMOTE_ADDR'].' - '.$_SERVER['HTTP_REFERER']."\n") && $userdata['user_id']==1) echo 'couldnt fwrite';
	//fclose($fopen);
?>
<font color="<?=$colors['lighttext']?>"><b><?=$numusers?></b> active users have made <b><?=$forumposts?></b> forum posts, created <b><?=$nummaps?></b> maps and written <b><?=$numtuts?></b> articles. This is SnarkPit v4.5.
<br>Welcome to our newest member, <b><a href="users.php?name=<?=$newestuser?>"><?=$newestuser?></a></b>!</font>

<?php
	$bday = date('dm');
	//$sql = mysql_query("SELECT username FROM users_profile WHERE birthday = '$bday'");

?>

<?php if(!$userdata) { ?>

<p><div style="margin:2px;width:99%;border:1px solid <?=$colors['msg_info_border']?>;padding:2px" class="news">
<b>Welcome to the SnarkPit!</b> This site brings together both maps and mapping into one handy place.
Not only do we have a tonne of editing tutorials, but a huge list of maps and mappers- simply
<a href="?page=register">register</a> and login, and you instantly have your own <a href="users.php?name=Leperous">profile</a>
which you can update with your <a href="maps.php">maps</a> and news. You can also submit your own tutorials and prefabs, and
post away in our custom <a href="forums.php">forums</a>! Read the <a href="?page=faq">FAQ</a> for more details.
</div></p>

<?php } ?>

<p>
<div style="text-align:right;padding-bottom:2px"><a href="users.php" class="white"><b>more user news</b></a> :: <a href="index.php?page=archive&amp;date=<?=gmdate("m/Y")?>" class="white"><b>news archive</b></a> :: <a href="rss.xml" class="white"><b>RSS news feed</b></a>&nbsp;</div>
<table width="100%" cellspacing=0 cellpadding=2 style="font-size:10pt">
<?php
	$sql = mysql_query("SELECT * FROM news WHERE plan < 2 ORDER BY id DESC LIMIT 6");
	while($narray = mysql_fetch_array($sql)) news_item($narray);
?>
<tr><td width=24><img src="images/null.gif" width=24 height=0 alt=""></td><td width="100%"></td></tr>
</table>
</p>
