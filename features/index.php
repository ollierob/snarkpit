<?php tracker('Features',''); ?>
<table width="99%" cellspacing=0 cellpadding=0>
<tr><td width="50%" valign=top>

<div class="title"><a href="features.php?page=reviews" class=white>Map Reviews</a></div>
<p>

<?php
 	$rowname = $game.'MOTM';
	$motm = @mysql_result(mysql_query("SELECT hits FROM counter WHERE name = '$rowname' LIMIT 1"),0);

	getonereview("SELECT r.*,m.user_id AS author FROM reviews r, maps m WHERE r.review_id = '$motm' AND m.map_id = r.map_id LIMIT 1",$game.' map of the moment',''); 

 	if($game) $gamesql = "r.game = '$game' AND";
	getonereview("SELECT r.*,m.user_id AS author FROM reviews r, maps m WHERE $gamesql m.map_id = r.map_id AND r.review_id != '$motm' ORDER BY r.review_id DESC LIMIT 1",'Newest '.$game.' review','');

	echo '<p>';
	
	$sql = mysql_query("SELECT * FROM games WHERE reviews != ''");
	$num = mysql_num_rows($sql);
	$rowwidth = floor(100/$num);
	subtitle('more reviews:','',($num*50));
	echo '<table width=100% cellpadding=2 cellspacing=0><tr>';

	while($garray = mysql_fetch_array($sql)) {
		echo "\n".'<td width="'.$rowwidth.'%" valign=top><table width=100% cellpadding=2 cellspacing=0>';
		echo '<tr><td colspan=2 valign=top><a href="?page=reviews&game='.$garray['id'].'"><img src="themes/'.$images['moddir'].'/icon_'.$garray['id'].'.gif" title="'.$garray['id'].'" align=texttop border=0> <b>'.stripslashes($garray['name']).'</b></a></td></tr>';
		echo '<tr><td><img src="images/null.gif" height=1 width=16></td><td width=100% style="font-size:8pt"><b>';
		$msql = mysql_query("SELECT * FROM mods WHERE game = '$garray[id]' AND reviews!='0' ");
		while($marray = mysql_fetch_array($msql)) {
			echo '<a href="?page=reviews&game='.$garray['id'].'&mod='.$marray['name'].'" class=white>'.$marray['fullname'].' ('.$marray['reviews'].')</a><br>';
		}
		echo "</b></td></tr></table>\n</td>\n";
	} echo '</tr></table>'; 
?>
</td>
<td width="5%"></td>

<td width="45%" valign=top>
<div class="title"><a href="?page=articles" class=white>Articles &amp; Interviews</a></div>
<p>
<table width="100%" cellpadding=2 cellspacing=2 style="font-size:8pt">
<?php $sql = mysql_query("SELECT * FROM articles WHERE section = 'articles' ORDER BY id DESC LIMIT 5");
while($array = mysql_fetch_array($sql)) {
	//if($array['image']) $imgurl = $array['image']; else
	$imgurl = 'images/gfx_'.$array['type'].'.gif';
	echo "\n<tr><td>";
	echo '<a href="?page=articles&id='.$array['id'].'"><img src="'.$imgurl.'" align=left border=0 height=32 width=32><b>'.stripslashes($array['title']).'</b></a>';
	echo '<br>'.stripslashes($array['description']);

	echo '</td></tr>';
}
?></table>

</td></tr></table>
