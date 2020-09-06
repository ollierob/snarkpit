<?php

function post_start($extrajava) { global $userdata,$mode,$topic,$forum,$edit,$quote,$java;
	?>

	<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 WIDTH="100%">
	<FORM ACTION="forums.php?submit=1" METHOD="POST" name="frmAddMessage" onsubmit="return checkform('<?=$mode?>')">
	<TR><TD width="15%" align=right><b>Username:<b></font></TD><TD width="85%">
	<INPUT type="text" name="username" size="24" maxlength="64" class="textinput"<?php
		if ($userdata) echo ' disabled VALUE="'.$userdata['username'].'"';
		echo '></TD></TR>'."\n";
		if(!$userdata) echo '	<TR><td align=right><b>Password:</b></td><TD><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25" class="textinput">';
		echo '	</TD></tr>';

		echo "\n<tr><td height=16></tr>";
		if($java==1 && $userdata && $userdata['javaoff']!=1) 
		echo "<tr><td></td><td colspan=2><b>You can set the forums not to use Java posting by <a href='cp.php?mode=preferences'>editing your preferences</a></b></td></tr>"; 
	
}

function post_subject($text) {
	echo "\n".'<tr><td colspan=2>'; subtitle('post details','maroon');  echo '</td></tr>';
	echo '<TR><TD align=right><b>Subject:</b></TD><TD><INPUT TYPE="TEXT" NAME="subject" SIZE="50" MAXLENGTH="100" class="textinput" value="'.$text.'"></TD></TR>';
}

function post_description($text) {
	echo '<TR><TD align=right><b>Description:</b></TD><TD><INPUT TYPE="TEXT" NAME="description" SIZE="50" MAXLENGTH="64" class="textinput" value="'.$text.'"></TD></TR>';
	echo '<TR><TD align=right></TD><TD><div class="help">Fill in this optional field if you want to give a quick description of this topic.</div></TR>'."\n";
}

function post_message($quote,$edit,$id) { global $browser,$java,$mode,$forum,$topic,$auth,$hidejavalink,$now_time,$theme,$colors,$images; //need $colors inside (browser).php

	echo '<tr><td height=10> </td></tr>';
	echo "\n".'<tr><td colspan=2>'; @subtitle('message body'); echo '</td></tr>';

		//$iframe = 'forums.php?textbody=1&r='.$now_time; if($quote) $iframe.='&quote='.$quote; if($edit) $iframe.= '&edit='.$edit;
		//if($quote||$edit) $iframe = 'forums.php?textbody=1&r='.$now_time.(($quote)?'&quote='.$quote:'').(($edit)?'&edit='.$edit:'');
		//else $iframe = 'themes/'.$theme.'/message.htm';
		
		echo '<tr><td valign=bottom align=center><b>insert a smiley:</b></td><td>';
		include('forums/browsers/'.browser().'.php');
		echo $formatting;
		
	?>

	</td></tr>
	<tr><td valign=top>
	<?php @include('forums/browsers/'.browser().'_smilies.php'); ?>
	<p>
	<div class="help"><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode','yes')">BBCode</a> enabled
		<br><a href="javascript:void(0)" onclick="popwin('popup.php?mode=bbcode','yes')">Images</a> enabled
		<br>HTML disabled
	<?php 
		if(browser()=='ie'||browser()=='moz') echo '<p><img src="themes/'.$images['moddir'].'/gfx_open.gif" alt="restore" align="left"><div style="position:relative;left:0"><a href="javascript:void(0)" onclick="if(toUpdate) document.getElementById(\'message\').contentWindow.document.body.innerHTML = toUpdate; else alert(\'No posts have been saved!\')">Restore lost post</a></div></p>';
	?>
	
		<p><img src="themes/<?=$images['moddir']?>/post_button_spelling.gif" align="middle"> <span class="action" id="action"><a href="javascript:void(0)" onmouseover="window.status='Check spelling';return true" onmouseout="window.status='';return true" onClick="setObjToCheck('message'); spellCheck();">Check spelling</a></span><br><span class="status" id="status"></span>
	</div>
	<div id="suggestions" class="suggestions">suggestions</div>
	</td><td valign=top<?=$boxmouseover?>>
	<?=$box?>
	</td></tr>

	<?php
}

function post_options() { global $java,$userdata,$forum,$mode,$colors,$parray;

	echo '<TR><TD></TD><TD><fieldset style="width:550px;border:1px solid '.$colors['item'].'"><legend style="color:'.$colors['item'].'">Options</legend>';

	if($userdata['addsig']==1||$parray['sig']==1) $checked = ' CHECKED'; else $checked = '';
	echo '<INPUT TYPE="checkbox" name="sig" id="addsig"'.$checked.'><label for="addsig">Add signature</label><br>';

	echo "\n".'<INPUT TYPE="CHECKBOX" NAME="notify" id="notify"><label for="notify">E-mail me when someone replies</label><BR>';
	echo '<INPUT TYPE="CHECKBOX" NAME="disablecode" id="bbcode"><label for="bbcode">Disable BBCode/smilies</label><BR>';
	if($userdata['user_level']>2 && $mode!='reply') echo '<input type="checkbox" name="sticky" id="sticky"><labe for="sticky">Make Sticky</label>';
	echo '</fieldset></TD></TR>';
}

function post_submit($type, $buttontext,$answer) { global $mode, $edit, $forum, $topic, $java; echo "\n";
	if($type=='editpost') echo '<INPUT TYPE="HIDDEN" NAME="post_id" VALUE="'.$edit.'">';
	//if($java==1) echo '<input type="hidden" name="java" value="no">'; else echo '<input type="hidden" name="message" value="">';
?>
	<TR><TD></td><td valign=bottom height=50>

	<?php if($answer) { ?>
	<div style="font-size:8pt;padding-bottom:4px">Please select what type of response this is. You will not be penalised for posting incorrect solutions! If you are posting a link to a solution, please summarise what it says (in case that website goes offline)</div>
	<select name="answer">
		<option value="2">This is a comment on the problem
		<option value="1"<?php if($answer=='q') echo ' SELECTED';?>>This is an answer to the problem
	</select>
	<? } ?>

	<input type="hidden" name="action" value="<?=$type?>">
	<input type="hidden" name="forum" value="<?=$forum?>">
	<input type="hidden" name="topic" value="<?=$topic?>">
	<input type="submit" name="submit" value="<?=$buttontext?>" class="submit3" onclick="style.cursor='wait'">

	</TD></TR></FORM>
	</TABLE>
	<?php
}

function topic_review($topic,$edit) { global $colors,$reply,$users,$chap;

	subtitle('topic review',''); //echo "\n\n".'<br><table width="95%" align=center cellspacing=0 cellpadding=2>';
	$sql = mysql_query("SELECT topic_replies FROM topics WHERE topic_id = '$topic' LIMIT 1");
	$tarray = mysql_fetch_array($sql); $numposts = $tarray['topic_replies']+1;

	if($chap) $chapsql = "AND chapter = '$chap'"; else $chapsql = '';

	$sql = mysql_query("SELECT p.post_id,p.poster_id,pt.post_text FROM posts p, posts_text pt WHERE p.topic_id = '$topic' AND pt.post_id = p.post_id $chapsql ORDER BY p.post_id DESC LIMIT 20");
	while($parray = mysql_fetch_array($sql)) { $count++;
		$text = $parray['post_text'];
		$text = (str_replace('[addsig]','',$text)); 
		//$text = str_replace('\"','"',str_replace("\'","'",$text));

		if($reply && $parray['post_id']==$reply) $text = '<div style="border:1px solid '.$colors['no'].';background-color:'.$colors['darktext'].'">'.$text.'</div>';

		echo '<div class="quote" style="width:100%;margin-bottom:8px"><div class="quotetitle">';
		if($count==$numposts) echo '• originally by '; else echo '• posted by '; echo userdetails($parray['poster_id'],'','return','');
		echo '</div><div class="quotetext">'.$text.'</div></div>';
		//if($edit==$parray['post_id']) echo '<tr><td bgcolor="'.$colors['red'].'"></td></tr>';

	if($count==20 && $count<$numposts) echo "<tr><td colspan=2 bgcolor=gray>More than 20 posts to this topic...</td></tr>";
	}
}

function post_answer($sel) { ?>
	<tr><td></td>
	<td><select name="answer">
		<option value="2">This is a comment on the problem
		<option value="1"<?php if($sel=='q') echo ' SELECTED';?>>This is an answer to the problem
	</select></td>
	</tr>
<?php }


?>
