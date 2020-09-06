<?php 

	echo 'Voting has finished, stay tuned for the results.';
	footer();
	die;

	if($HTTP_POST_VARS['submit'] && $userdata) { $v='';

	$userid = '-'.$userdata['user_id'].'-';

	if($winner) {
		$sql = mysql_query("SELECT * FROM competition WHERE map_id = '$winner' LIMIT 1"); 
		if(!$warray = mysql_fetch_array($sql)) error_die("Map doesn't exist");

		$wsql = mysql_query("SELECT * FROM competition WHERE winner LIKE '%$userid%'");
		while($varray = mysql_fetch_array($wsql)) {
			$update = str_replace($userid,'-',$varray[winner]);
			@mysql_query("UPDATE competition SET winner = '$update' WHERE map_id = '$varray[map_id]' LIMIT 1");
		}

		$update = str_replace($userid,'-',$warray[winner]).$userdata[user_id].'-';
		@mysql_query("UPDATE competition SET winner = '$update' WHERE map_id = '$winner' LIMIT 1");
	}

	if($runnerup && $runnerup!=$winner) {
		$sql = mysql_query("SELECT * FROM competition WHERE map_id = '$runnerup' LIMIT 1"); 
		if(!$rarray = mysql_fetch_array($sql)) error_die("Map doesn't exist");

		$sql = mysql_query("SELECT * FROM competition WHERE map_id = '$runnerup' AND winner LIKE '%$userid%' LIMIT 1"); 
		if($warray = mysql_fetch_array($sql)) $error = 1; else {

		$wsql = mysql_query("SELECT * FROM competition WHERE runnerup LIKE '%$userid%'");
		while($varray = mysql_fetch_array($wsql)) {
			$update = str_replace($userid,'-',$varray[runnerup]);
			@mysql_query("UPDATE competition SET runnerup = '$update' WHERE map_id = '$varray[map_id]' LIMIT 1");
		}

		$update = str_replace($userid,'-',$rarray[runnerup]).$userdata[user_id].'-';
		@mysql_query("UPDATE competition SET runnerup = '$update' WHERE map_id = '$runnerup' LIMIT 1");
	} }

	$sql = mysql_query("SELECT * FROM competition WHERE winner LIKE '%$userid%' AND runnerup LIKE '%$userid%' LIMIT 1");
	if($array = mysql_fetch_array($sql)) {
		$update = str_replace($userid,'-',$array[runnerup]);
		@mysql_query("UPDATE competition SET runnerup = '$update' WHERE map_id = '$array[map_id]' LIMIT 1");
		$error = 1;
	}

	echo 'Thanks for your vote. You can go back and change it if you want at any time before the end date.'; 
	if(($runnerup && $runnerup==$winner) OR ($error)) echo '<br>Your runnerup has been ignored as it\'s the same as the map you chose to win!';
	footer();
} ?>

Well, it's that time of the year, or something- better have a mapping competition!</p>

<B>The aim:</B> Find a (single) piece of concept art, and build a Half-Life deathmatch level around it. 
	Credit will be given for faithful reproduction of the scene and staying true to that theme, having an interesting 
	location, and obviously if it's a decent DM level. Extra kudos for those with l337 drawing sk1lz who draw their own 
	picture!
</p>

Vote below for your favourite map- i.e. the one which you think is the best overall map, and not which most closely fits
its concept art.</p>

<font size=4>Entries:</font>

<table width="100%">
<form action="features.php?page=articles&id=6&vote" method="post" name="compform">
<?php 	$sql = mysql_query("SELECT * FROM competition");
	while($array = mysql_fetch_array($sql)) { echo '<tr><td></td><td>';
		$msql = mysql_query("SELECT * FROM maps WHERE map_id = '$array[map_id]' LIMIT 1");
		$marray = mysql_fetch_array($msql);
		echo '<a href="maps.php?map='.$array[map_id].'"><b>'.$marray[name].'</b></a> by '.userdetails($marray[user_id],'white','return').'</td>';
	if($userdata) {
		$userid = '-'.$userdata[user_id].'-';

		echo '<td><input type="radio" name="winner" value="'.$array[map_id].'"'; if(substr_count($array[winner],$userid)) echo ' CHECKED'; echo '> vote winner</td>';
		echo '<td><input type="radio" name="runnerup" value="'.$array[map_id].'"'; if(substr_count($array[runnerup],$userid)) echo ' CHECKED'; echo '> vote runner-up</td>';

	} echo '</tr>'; } 

if(!$userdata) echo '<tr><td colspan=2></td><td colspan=2><b><font color=red>Please login to vote for your favourite map</font></b></td></tr>';

?>

<tr>
	<td width=1><img src="images/null.gif" width=10 height=1></td>
	<td width="60%"><?php if($userdata) { ?><input type="submit" name="submit" value="vote!" class="submit3"><font size=1> (you can change your descision as many times as you want)</font><? } ?></td>
	<td width="20%"></td><td width="20%"></td>
</tr>

</form>
</table>