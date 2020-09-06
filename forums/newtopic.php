<?php
	include('forums/post.php');
	title('Post A New Topic in &quot;'.$forum_name.'&quot;','forums');
	
	if($userdata['dailypostlimit']>0 && $userdata['dailypostlimit']-$dppostmade<1) @error_die('You have made all the posts you can today, try again later, and click the red box at the top of the screen if you\'re confused.');


if($farray['support']) {
	$auth = $_GET['auth'];
	if(!$auth) @error_die('You must submit a question via the <a href="editing.php"><b>editing page</a></b>. Please use it to search for your problem, and then click the "post in forums" link it provides you underneath your results <b>if</b> nothing relevant is found.');
	$code = 'Mky'.$forum.$userdata['user_id'];
	$code1 = $code.date("h/d/m"); $code2 = $code.date("h/d/m",time()-3600); $code3 = $code.date("h/d/m",time()+3600);
	$code1 = md5($code1); $code2 = md5($code2); $code3 = md5($code3);
	if($auth!=$code1 AND $auth!=$code2 AND $auth!=$code3) @error_die('You must submit a question via the <a href="editing.php"><b>editing page</a></b>');

	$pft = mysql_result(mysql_query("SELECT topic_id FROM topics WHERE topic_poster = '$userdata[user_id]' LIMIT 1"),0);
	if(!$error && !$pft) {
		$readmetext = mysql_result(mysql_query("SELECT p.post_text FROM topics t, posts_text p WHERE t.forum_id = '$forum' AND t.sticky = 1 AND p.post_id = t.last_post_id ORDER BY topic_id ASC LIMIT 1"),0);
		if($readmetext) echo '<font size=4>This is your first time posting in this forum, so please read this first:</font><p>'.$readmetext;
 	}

	$array = @mysql_fetch_array(mysql_query("SELECT COUNT(*) AS stillopen, topic_id FROM topics WHERE forum_id = '$forum' AND topic_poster = '$userdata[user_id]' AND answered = 'n'"));
	if($array['stillopen']>8) @error_die('Sorry, you can\'t post in this forum again until you sort out some of your other unsolved problems (please read the forum guide and learn to mark people\'s answers). For example, try getting <a href="forums.php?forum='.$forum.'&amp;topic='.$array['topic_id'].'"><b>this problem</b></a> solved.');

}

	if(isset($_REQUEST['error'])) {
		if($_REQUEST['error']==1) msg('You can\'t post an empty message, please try again','error','','','','');
		if($_REQUEST['error']==2) msg('Your topic needs a title, please try again','error','','','','');
	}

	echo '<p>'.$forum_desc.'</p>';

	//if($farray['forum_text']) echo "\n".'<blockquote><b><font color="'.$colors['green'].'" style="font-size:11px">read me before posting:</font></b><br><div style="background-color:'.$colors['dgreen'].';border:1px solid '.$colors['green'].';list-style-type:square;padding:3px">'.stripslashes($farray['forum_text']).'</div></blockquote>';
	if($farray['forum_text']) msg($farray['forum_text'],'info','','90','Read me before posting','div');

	post_start('if(frmAddMessage.map.value==\'\') alertmsg=\'You need to choose a map; this is necessary for the Maps forum. Add the map to your profile first if it isnt listed.\''); 

if(substr_count(strtolower($forum_name),'maps')) { //map forum check ?>
	<tr><td colspan=2><?subtitle('post details','')?></td></tr>
	<tr>
		<td align=right><b>For map:</b></td>
		<td><?php if(!$userdata) { 
			echo '<input type="text" size=4 maxlength=5 name="map" class="textinput"> <font size=1>Enter the id# of your map</font>'; 
		} else {
			echo '<select name="map"><option value="" id="cat_white">select an unfinished map:</option>'; $gamearray = array();
			$sql = mysql_query("SELECT `map_id`,`name`,`mod`,`game` FROM maps WHERE user_id = '$userdata[user_id]' AND thread = '' AND status != 100 ORDER BY game,name");
			while($array = mysql_fetch_array($sql)) {
				if(!$gamearray[$array['game']]) echo '</optgroup><optgroup label="'.$array['game'].' maps">';
				$gamearray[$array['game']]++;
				echo "\n".'		<option value="'.$array['map_id'].'">'.stripslashes($array['name']).' ('.$array['mod'].')';
			}
		echo '		</select>'; } ?></td>
	</tr>
	<tr><td></td><td><div class="help">Please select your map from the list above- note you can only have one different thread per map, <b>and it must still be in beta stages</b>. A link to this thread will appear in yours and the maps 'profile',
		and it also helps prevent forum n00bism. <a href="cp.php?mode=maps&action=new">Click here</a> to add a new map to your profile.</div>
	</td></tr>

<? } else { post_subject(((isset($_GET['subject']))?$_GET['subject']:'')); }

	post_description('');

	if($farray['support']) {
		$game = $farray['game'];
		if(include('lib/forums_'.$game.'.php')) {
			echo "\n".'<tr><td align=right><b>Related to:</b></td><td><select name="section"><option id="cat_white" value="ERR">Select an option:</option>';
			while(list($var,$val)=each($helpsections)) echo '<option value="'.$var.'">'.$var.'</option>';
			echo '</select>';
		}
	}

	post_message('','','');
	post_options();

	if(isset($_GET['act']) && $_GET['act']=='poll') { include('forums/plugins/post_poll.php'); post_poll(''); }

	post_submit('newtopic','Submit','');

?>
