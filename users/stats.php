<?php title('User Stats','users'); $mode = ''; tracker('User Stats'); ?>

<?=subtitle('Site stats')?><br><blockquote>
	<?php $days = round(($now_time - strtotime('30th August 2003'))/86400); ?>
	Since the 29th August 2003: (total/<font color=<?=$colors['item']?>>daily average</font>)<br>
	<b><?php $array = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'messages'")); echo $array[9].'/<font color='.$colors['item'].'>'.round($array[9]/$days); ?></font></b> private messages sent,
	<b><?php $num = mysql_result(mysql_query("SELECT hits FROM counter WHERE name = 'users' LIMIT 1"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> people have registered,
	and <b><?php $num = mysql_result(mysql_query("SELECT COUNT(map_id) FROM maps"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> maps,
	<b><?php $num = mysql_result(mysql_query("SELECT COUNT(id) FROM comments"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> comments,
	and <b><?php $num = mysql_result(mysql_query("SELECT COUNT(id) FROM articles"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> articles have been written.
	<b><?php $num = mysql_result(mysql_query("SELECT COUNT(post_id) FROM posts"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> posts have been made, containing about
	<b><?php $array = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'posts_text'")); echo ceil($array[5]*.99/5).'/<font color='.$colors['item'].'>'.ceil($array[5]*0.99/($days*5)); ?></font></b> words in total,
	in <b><?php $num = mysql_result(mysql_query("SELECT COUNT(topic_id) FROM topics"),0); echo $num.'/<font color='.$colors['item'].'>'.round($num/$days);?></font></b> topics.
	<p><?php
		$a = mysql_fetch_array(mysql_query("SELECT * FROM counter WHERE name = 'uhits' LIMIT 1"));
		$b = mysql_fetch_array(mysql_query("SELECT * FROM counter WHERE name = 'ghits' LIMIT 1"));
	echo $t = $a['hits']+$b['hits']; ?> pages in total since <?=date("jS F Y",$a['date'])?>- <?=round(($a['hits']/$t)*100)?>% from
	registered members, and about <?php $ppd = round($t/(($now_time-$a['date'])/(24*3600))); echo $ppd; ?> pages per day.
	We served 4.9 million pages in 2004, and are on course to do about <?=round(($ppd*365)/1000000,1)?> million this year.
</blockquote>

<?=subtitle('User rating')?><br><blockquote>
<?php	$sql = mysql_query("SELECT * FROM users_rating"); $sum = 0; $countr = 0; $userrated = array(); $ratings = array();
	while($rarray = mysql_fetch_array($sql)) {
		$userrated[$rarray['to_id']]++;
		$ratings[$rarray['to_id']] += $rarray[rating]; 
		$rateme = 'rate'.$rarray['rating'].'x'; $$rateme++; $sum = $sum+$rarray['rating']; $countr++;
		$from = $rarray['from_id'];
		if($rarray['rating']==1) $buserrating[$from]++;
		if($rarray['rating']==5) $guserrating[$from]++;
		if($maxtouserid<$rarray['to_id']) $maxtouserid = $rarray['to_id'];
	} arsort($buserrating); arsort($guserrating);
	$maxval = max($guserrating); $maxuser = array_search($maxval,$guserrating);
	$minval = max($buserrating); $minuser = array_search($minval,$buserrating);

	echo '<table width=100% height=16 cellspacing=1><tr>';
		echo '<td>-</td><td bgcolor="#FF0000"><img src="images/null.gif" width='.floor($rate1x*200/$countr).' height=1></td>
		<td bgcolor="#FFA500"><img src="images/null.gif" width='.floor($rate2x*200/$countr).' height=1></td>
		<td bgcolor="#FFFF00"><img src="images/null.gif" width='.floor($rate3x*200/$countr).' height=1></td>
		<td bgcolor="#CCFF99"><img src="images/null.gif" width='.floor($rate4x*200/$countr).' height=1></td>
		<td bgcolor="#32CD32"><img src="images/null.gif" width='.floor($rate5x*200/$countr).' height=1></td>';
		echo '<td width=100%>+ Average rating '.(floor($sum*10/$countr)/10).'/5</td></tr></table>';
		echo '';

	$maxratedfor = max($userrated); $maxuserratedfor = array_search($maxratedfor,$userrated);
	for($i=1;$i<=$maxtouserid;$i++) { if($ratings[$i]) { if($userrated[$i]<3) $ratings[$i]=0; else $ratings[$i] = $ratings[$i]/$userrated[$i]; }}
	$highestrating = max($ratings); $highestrated = array_search($highestrating,$ratings);

?>
<br>Highest rated: <?=userdetails($highestrated)?> with <?=floor(10*$highestrating)/10?> (rated by <?=$userrated[$highestrated]?> people)
<br>Nicest user: <?=userdetails($maxuser)?> (has rated <?=$maxval?> people 5/5)
<br>Bitter as a lemon: <?=userdetails($minuser)?> (has rated <?=$minval?> people 1/5)
<br>Everyone knows him: <?=userdetails($maxuserratedfor)?> (<?=$maxratedfor?> ratings)
</blockquote></p>

<?=subtitle('Most popular')?><br><blockquote>
Users:
<?php 	$c = 0;
	$sql = mysql_query("SELECT profile_count,username FROM users_profile ORDER BY profile_count DESC LIMIT 3");
	while($array = mysql_fetch_array($sql)) { if($c) echo ', '; $c++;
		echo '<b><a href="users.php?name='.$array['username'].'" class="white">'.$array['username'].'</a></b> ('.$array['profile_count'].' profile hits)';
	}
?>

<br>Maps:
<?php 	$c = 0;
	$sql = mysql_query("SELECT map_id,name,user_id,downloads FROM maps ORDER BY downloads DESC LIMIT 3");
	while($array = mysql_fetch_array($sql)) { if($c) echo ', '; $c++;
		echo '<b><a href="maps.php?map='.$array['map_id'].'">'.stripslashes($array['name']).'</a></b> by '.userdetails($array['user_id'],'white','return').' ('.$array['downloads'].' downloads)';
	}
?>

<br>Topic:</b> <?php $pop = mysql_fetch_array(mysql_query("SELECT forum_id,topic_id,title,topic_replies FROM topics ORDER BY topic_replies DESC LIMIT 1")); ?>
	<b><a href="forums.php?forum=<?=$pop['forum_id']?>&topic=<?=$pop['topic_id']?>"><?=stripslashes($pop['title'])?></a></b> (<?=$pop['topic_replies']?> replies)
<br>Poll:</b> <?php $pop = mysql_fetch_array(mysql_query("SELECT p.topic_id,p.votes,t.title,t.forum_id FROM polls p, topics t WHERE t.topic_id = p.topic_id ORDER BY p.votes DESC LIMIT 1")); ?>
	<b><a href="forums.php?forum=<?=$pop['forum_id']?>&topic=<?=$pop['topic_id']?>"><?=stripslashes($pop['title'])?></a></b> (<?=$pop[votes]?> votes)
<br>Tutorial:</b> <?php $pop = mysql_fetch_array(mysql_query("SELECT id,title,user_id,hits FROM articles WHERE section = 'editing' ORDER BY hits DESC LIMIT 1")); ?>
	<b><a href="editing.php?page=tutorials&id=<?=$pop['id']?>"><?=stripslashes($pop['title'])?></a></b> by <?=userdetails($pop['user_id'],'white')?> (<?=$pop['hits']?> views)

<p>Visit the <b><a href="?page=memberlist">memberlist</a></b> to see who has the most forum posts, maps, snarkmarks etc.
</blockquote>

<p><a href="cp.php?mode=feedback&select=feedback"><b>Click here</b></a> if you want to see some other stats on this page!
<p>
