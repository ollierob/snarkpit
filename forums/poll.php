<?php
if($tarray['topic_status']==1) { echo '<p><b><font color=red>Poll is disabled as the topic is locked.</font></b></p>'; } else {

if($HTTP_POST_VARS['submit']) {
	include('../config.php');
	if(!$userdata) error_die('You must be logged in to vote','forums');
	if(!$forum || !$topic || !$poll) error_die('Please vote properly...','forums');
	if(!$array = mysql_fetch_array(mysql_query("SELECT * FROM polls WHERE topic_id = '$topic' LIMIT 1"))) error_die('Poll does not exist','forums');
	$voteuserid = ','.$userdata[user_id].',';
	for($i=1;$i<=6;$i++) {
		$invote = 'voted'.$i;
		if(substr_count($array[$invote],$voteuserid)) $dontupdate = 1;
	} 
	if($dontupdate) $msg = '&msg=You+have+already+voted!'; else {
		$query = "UPDATE polls SET $poll = '".$array[$poll].$userdata[user_id].",', votes = votes + 1 WHERE topic_id = '$topic' LIMIT 1";
		if(!mysql_query($query)) $msg = '&msg=Error:+'.str_replace(' ','+',mysql_error());
	}
	header("Location: ../forums.php?forum=$forum&topic=$topic$msg");
	die;

} else {

	$sql = mysql_query("SELECT * FROM polls WHERE topic_id = '$topic' LIMIT 1");
	if(!$parray = mysql_fetch_array($sql)) { $message = 'Couldn\'t get poll information!'; } else {

		$message.= "\n".'<!--poll--><p>';

	if($msg) $message.= '<b><font color="red">'.$msg.'</font></b>';
	$message.= '	<table width="90%" align=center><tr><td><fieldset><legend><b>Poll:</b> '.$tarray['title'].'</legend>
		<table width="90%" align=center cellspacing=0 cellpadding=4>';

	$echo = ''; $vote = ''; $j=0;
	$polluserid = ','.$userdata['user_id'].','; 
	if($userdata) {
		$voting = 'y'; 
		for($i=1;$i<=6;$i++) { $option = 'option'.$i; $voted = 'voted'.$i; if($parray[$option]) {
			if(substr_count($parray[$voted],$polluserid)==1) $voting = 'n';
			$votesin[$i] = substr_count($parray[$voted],',') - 1;
		} }
	} else $voting = 'x';

	$pollcolor = array('','red','orange','yellow','green','blue','purple');
	$basewidth = 300; 
	for($i=1;$i<=6;$i++) { if($votesin[$i]>$maxvotes) $maxvotes=$votesin[$i]; }

	for($i=1;$i<=6;$i++) { $option = 'option'.$i; $voted = 'voted'.$i; if($parray[$option]) { $j++;

			if($maxvotes) $twidth = floor($basewidth*$votesin[$i]/$maxvotes); else $twidth = 10;

			$echo.= "\n	".'<tr onmouseover="style.background=\''.$colors['trmouseover'].'\'" onmouseout="style.background=\''.$colors['bg'].'\'"';
			$echo.= '><td width=1 valign=top><b>'.$j.'</b></td><td valign=top';
			if($voting=='y') $echo.= ' colspan=2 width="100%"><input type="radio" name="poll" value="voted'.$i.'" id="voted'.$i.'" onclick="javascript:voted=true"> '; else $echo.=' width="30%">';
			$echo.= '<label for="voted'.$i.'">'.$parray[$option].'</label></td>';
			if($voting=='n') $echo.= '<td width="70%" valign=top><table cellspacing=0 cellpadding=0 align=left bgcolor="'.$pollcolor[$j].'" width="'.$twidth.'"><tr><td><img src="images/null.gif" width="'.$twidth.'" height=16></td></tr></table> '.$votesin[$i].'</td>';
			$echo.= '</tr>';
	} } 

	if($voting=='y') {
		echo '<form action="forums/poll.php" method="post"><script>var voted = false</script>';
		$echo.= '<tr><td></td><td><input type="submit" name="submit" value="Vote!" class="submit3" onclick="if(voted!=true) return false"></td>';
		$echo.= '<input type="hidden" name="forum" value="'.$forum.'"><input type="hidden" name="topic" value="'.$topic.'"></form>';
	} else $echo.='<tr><td></td><td></td>';

	$message.= $echo; 
	$message.= '<td>'.$parray['votes'].' vote'; if($parray['votes']!=1) $message.= 's'; $message.= ' cast</td></tr></table></p>
	</fieldset></td></tr></table>';
	if(!$hidemsg) {
		$message.='<table style="font-size:10px" width="90%" cellspacing=0 cellpadding=0 align=center><tr><td align=right><font color="'.$colors[green].'">';
		if($voting=='y') $message.= 'you must vote to see the results'; 
			if(!$userdata) $message.= 'you must login to vote'; 
			if($voting=='n') $message.= 'you have already voted';
		$message.= "</td></tr></table>\n<!--endpoll-->\n";
	}

} } } ?>
