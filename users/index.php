<?php
	title($t_users.' Profile News','users');
	tracker('User news');
	include('index/func_index.php');
?>

</p>
<div align=right><a href="index.php?page=archive&date=<?=date("m/Y")?>&site=1" class=white><b>user news archive</b></a> :: <a href="rss.xml" class=white><b>RSS news feed</b></a></div>
<table width="100%" cellspacing=0 cellpadding=2 style="font-size:10pt">
<?php 
	$sql = mysql_query("SELECT * FROM news WHERE plan > 0 ORDER BY id DESC LIMIT 8");
	while($narray = mysql_fetch_array($sql)) news_item($narray);
?>
<tr><td width=24><img src="images/null.gif" width=26 height=0></td><td width=100%></td></tr>
</table>
