<?php
	include('forums/post.php');
	title('Reply to &quot;<a href="forums.php?forum='.$forum.'&topic='.$topic.'">'.stripslashes($tarray['title']).'</a>&quot;</p>','forums');
	if($tarray['topic_status']==1) error_die('This topic is locked, so you can\'t reply to it.');

	if($error==1) msg('You can\'t post an empty message, please try again','error');
	if($error==2) msg('Error message doesn\'t exist!','error');
	if($error==3) msg('No, really, it doesn\'t. I won\'t tell you again :P','error');
	
	if($userdata['dailypostlimit']>0 && $userdata['dailypostlimit']-$dppostmade<1) error_die('You have made all the posts you can today, try again later, and click the red box at the top of the screen if you\'re confused.');

	if($farray['forum_text']) echo '<div style="width:95%;background-color:'.$colors['msg_info_bg'].';border:1px solid '.$colors['msg_info_border'].';padding:3px">'.stripslashes($farray['forum_text']).'</div><p>';

	post_start('');

		if($reply) $replyto = $reply; elseif($quote) $replyto = $quote; else $replyto = '';
		if($replyto) echo '<input type="hidden" name="reply" value="'.$replyto.'">';

		if($act=='chapter' && $tarray['chapters']) {
			echo "\n".'<tr><td colspan=2>'; subtitle('post details','');
			echo '</td></tr><tr><td align=right><b>chapter title:</b></td><td><input type="text" name="addchapter" class="textinput" size=32 maxlength=32></td></tr><tr><td height=5> </td></tr>'."\n";
		}

		if($chap=$_GET['chap']) {
		        $addchap = true;
			if(!$tarray['chapters']) $addchap = false;
			include('forums/chapters/'.$topic.'.php');
			if($chap>count($chapters)) $addchap = false;
			if($addchap) echo '<input type="hidden" name="chapid" value="'.$chap.'">';
		}

	if(substr_count(strtolower($forum_name),'maps') && $userdata['user_id']==$tarray['topic_poster']) echo '<tr><td></td><td>If you are posting an update about your map, please start a new chapter instead! Click the \'add chapter\' link back on the topic page.</td></tr>';

	post_message($quote,'','');
	post_options();
	post_submit('reply','Submit',(($tarray['answered']=='n')?'n':''));
	topic_review($topic,'');
?></p>
