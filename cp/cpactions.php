<?php
	if(!$_POST) { header('Location: ../cp.php'); die; }
	while(list($var,$val)=each($_POST)) $$var = $val;
	if($action) $mode = $action; 
	if(!$now_time) $now_time = time();
	if($userdata['activated']!=1 OR !$userdata['user_id']) { header('Location: login.php?linkto=cp.php'); die; }
	if(!$mode) die('No mode'); 
	if($rating) { if($rating<1 OR $rating>10) { errorlog('faking rating'); error_die('Please select a valid rating!'); } }

	if(!include('func_parse.php')) die('Error loading parsing page, please press refresh or contact the site admin!');

function url_check($url) { global $related;

	$url = trim($url);
	if(substr($url,0,4)=='www.') $url = 'http://'.$url;
	if(substr_count($url,'snarkpit.net/maps.php?download=') || substr($url,0,8)=='maps.php') $url = '';

	if(substr($url,0,7)=='http://' && substr_count($url,'/')==2 && !$related) { $url = ''; $related = $url; }
	$url = str_replace('http://www.snarkpit.net/','',$url);

	if(!substr_count($url,'/') && !substr_count($url,'.')) $url = '';

	return $url;
}

//start
	
if($mode=='feedback') {
	$sql = mysql_query("SELECT * FROM authenticate WHERE user_id = '$userdata[user_id]' AND text = '$text' LIMIT 1");
	if($array = mysql_fetch_array($sql)) die("<font face=verdana>You've already sent us this, probably because you pressed submit twice- don't worry, we got it the first time!</font>");
	if(!$sql = mysql_query("INSERT INTO authenticate (type,user_id,text,date) VALUES ('$relatedto','$userdata[user_id]','$text','$now_time')")) die('Error sending, please retry');
	header('Location: cp.php?msg=Message+sent,+thank+you'); die;
}

if($mode=='news') {
	$subject = htmlspecialchars(censor_string($subject,''));

	if($_POST['map']) {
	        $map = $_POST['map'];
		$maparray = mysql_fetch_array(mysql_query("SELECT game,thumbnails FROM maps WHERE map_id = '$map' LIMIT 1"));
		if($maparray['thumbnails']>0) {
			$message .= "\n\n";
		        for($i=1;$i<=$maparray['thumbnails'];$i++) $message .= '[thumb=maps/'.$maparray['game'].'/images/'.$map.'_'.$i.'_thumb.jpg]maps/'.$maparray['game'].'/images/'.$map.'_'.$i.'.jpg&map='.$map.'[/thumb] ';
		}
	}

	$message = htmlspecialchars(trim(censor_string($message,''))); $message = str_replace("\n",'<br>',bbencode(smile($message),''));
	$message = addslashes($message);
	
	if(!$message OR !$subject) { header('Location: cp.php?error=Your+news+needs+a+title+and+text'); die; }
	if($where=='profile') { if(!mysql_query("INSERT INTO news (user_id,subject,date,text,plan) VALUES ('$userdata[user_id]','$subject','$now_time','$message','2')")) error_die('Error adding news: '.mysql_error()); $msg = 'News+item+added'; }
		else { if(!mysql_query("INSERT INTO authenticate (type,user_id,title,text,date) VALUES ('news','$userdata[user_id]','$subject','$message','$now_time')")) error_die('Error adding news: '.mysql_error()); $msg = 'News+item+added,+please+wait+for+it+to+be+authed'; }
	writerss();
	header('Location: cp.php?msg='.$msg); die;
}

if($mode=='editnews') {
	if(!$edit) { header('Location: cp.php?mode=news'); die; }
	if($auth=='on') $sqltable = 'authenticate'; else $sqltable = 'news';
	$authorid = mysql_result(mysql_query("SELECT user_id FROM $sqltable WHERE id = '$edit' LIMIT 1"),0);
	if(!$authorid) { header('Location: cp.php?error=news+item+does+not+exist'); die; }
	if($authorid!=$userdata['user_id'] && $userdata['user_level']<3) { header('Location: cp.php?error=News+item+is+not+yours'); die; }

	$message = htmlspecialchars(trim(censor_string($message,''))); $message = str_replace("\n",'<br>',bbencode(smile($message))); if(!$message) header('Location: cp.php?error=Cannot+post+empty+news+item');
	$subject = trim(htmlspecialchars(censor_string($subject,'')));
	if(!$message OR !$subject) { header('Location: cp.php?mode=news&edit='.$edit); die; }

	if($auth=='on') @mysql_query("UPDATE authenticate SET title = '$subject',text='$message',status=0 WHERE id = '$edit' LIMIT 1");
		else @mysql_query("UPDATE news SET subject = '$subject', text = '$message' WHERE id = '$edit' LIMIT 1");
	writerss();
	header('Location: cp.php?msg=News+edited+successfully'); die;
}

if($mode=='newcomment') { 

	if($text) {
		$text = htmlspecialchars(trim(censor_string($text,'nobbcode')));
		$text = preg_replace("#http\:\/\/([^.]+)\.([^, \n\r]+)#", '<a href="http://\\1.\\2" target="_blank">http://\\1.\\2</a>', $text);
		$text = addslashes(ucfirst(str_replace("\n",'<br>',$text)));
	}

	if($rating) { $sql = mysql_query("SELECT * FROM comments WHERE article_id = '$id' AND rating!='' AND user_id = '$userdata[user_id]'");
		if($array = mysql_fetch_array($sql) AND $array[id]!=$edit) error_die("You've already rated this"); 
	}

	if(!$text && !$rating) { header('Location: '.$redirect); die; }
	if(!$type) { header('Location: cp.php?error=No+type+selected'); die; } $t=0; $subtype = '';

	if($type=='map') { $t++;
		$sql = mysql_query("SELECT user_id,status,name FROM maps WHERE map_id = '$id' LIMIT 1"); $zarray = mysql_fetch_array($sql);
		$commentlink = 'maps.php?map='.$id; $commentname = stripslashes($zarray['name']);
		if($zarray[status]<100) $subtype = 'beta';
	}
	elseif($type=='tutorial') { $t++;
		$sql = mysql_query("SELECT * FROM articles WHERE id = '$id' LIMIT 1"); $zarray = mysql_fetch_array($sql);
		$commentlink = 'editing.php?page=tutorials&id='.$id; $commentname = stripslashes($zarray['title']);
	}
	elseif($type=='prefabs'||$type=='models') { $t++;
		$sql = mysql_query("SELECT * FROM files WHERE file_id = '$id' LIMIT 1"); $zarray = mysql_fetch_array($sql);
		$commentlink = 'editing.php?page=files&comment='.$id; $commentname = stripslashes($zarray['filename']);
	}

	if(!$t) { header('Location: cp.php?error=Invalid+comment'); die; }

	if(!$edit) {
		@mysql_query("INSERT INTO comments (user_id,date,text,type,article_id,rating,subtype) VALUES ('$userdata[user_id]','$now_time','$text','$type','$id','$rating','$subtype')");
		@mysql_query("UPDATE users_profile SET comments = comments + 1 WHERE user_id = '$userdata[user_id]' LIMIT 1");

		$comment_id = mysql_insert_id();
		if(!$replyto = $_POST['replyto']) $replyto = $comment_id;
		@mysql_query("UPDATE comments SET `replyto` = '$replyto' WHERE `id` = '$comment_id' LIMIT 1");

		$currating = ''; $numratings = '';
		$sql = mysql_query("SELECT rating FROM comments WHERE article_id = '$id' AND rating!=''");
		while($ratingarray = mysql_fetch_array($sql)) { $currating = $currating + $ratingarray[rating]; $numratings++; } $newrating = $currating/$numratings;

		if($type=='map') @mysql_query("UPDATE maps SET comments = comments+1, rating = $newrating WHERE map_id = '$id' LIMIT 1"); 
		if($type=='tutorial') @mysql_query("UPDATE articles SET rating = $newrating WHERE id = '$id' LIMIT 1");
		if($type=='prefabs'||$type=='models') @mysql_query("UPDATE files SET comments = comments + 1 WHERE file_id = '$id' LIMIT 1");

	} else {
		$sql = mysql_query("SELECT user_id,id FROM comments WHERE id = '$edit' LIMIT 1");
		if(!$carray = mysql_fetch_array($sql)) error_die('Comment doesn\'t exist');
		if($carray['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) error_die('This isn\'t your comment so you can\'t edit it');
		if($carray['user_id']!=$userdata['user_id']) $altered = 1; else $altered = 0;
		@mysql_query("UPDATE comments SET text = '$text', rating = '$rating', altered = '$altered' WHERE id = '$edit' LIMIT 1");
	}

	calcsnarkpoints($userdata['user_id']);
	if($redirect) $commentlink = $redirect;
	
	if(!$edit && $zarray['user_id']>0 && $zarray['user_id']!=$userdata['user_id'] && !$zarray['nocommentpm']) {
		$pm_msg = addslashes('This is an automated message to inform you that '.$userdata['username'].' has commented on your '.$type.', '.$commentname.'. <a href="javascript:void(0)" onclick="opener.location=\''.$commentlink.'\'"><b>Click here</b></a> to view it.');
		@mysql_query("INSERT INTO messages (from_id,to_id,time,subject,text,status) VALUES ('-1','$zarray[user_id]','$now_time','Automated message','$pm_msg','1')");
	} 
	header('Location: '.$commentlink.'#comments'); die;
}

$mapdate = date("d/m/y",time());
$validext = array('jpg','jpeg','gif','png','tga'); $thumbnailed='';
//need to alter function to allow for valid extensions 				!***********************************!

	function uploadfile($name,$dir,$i) { global $edit,$article_id; if(!$article_id) $article_id = $edit;
		$dir = 'userimages/'.$dir.'/'; $filename = 'tut'.$article_id.'_'.$i; $maxfilesize = '150';
		$scr_name = strtolower($_FILES[$name]['name']);
		if(substr($scr_name,-4)=='.jpg') {
		$uscreen = $dir.$filename.'.jpg';
		if(!move_uploaded_file($_FILES[$name]['tmp_name'], $uscreen) ) error_die("Couldn't upload screenshot to <i>$dir</i>, please contact the site admin");
		if(!$sz = GetImageSize($uscreen)) die("Uploaded screenshot wasn't an image!"); $x = $sz[0]; $y = $sz[1];
		if($x>800) {
			$src_image = ImageCreateFromJPEG($uscreen); $resize = ImageCreateTrueColor(800,600);
			ImageCopyResampled($resize,$src_image,0,0,0,0,800,600,$x,$y);
			ImageJPEG($resize,$uscreen,85);
		}
		chmod($uscreen,0777);
	} }

if(($mode=='newtut' OR $mode=='edittut') && $submit=='preview') { //preview
	include('header.php'); include('cp/sidebar.php'); 

	title('Preview:','none');

	$tuttext = stripslashes(htmlspecialchars(trim(censor_string($tuttext,'')))); $tuttext = bbencode(str_replace("\n",'<br>',$tuttext),'');
	if($edit) { for($i=1;$i<9;$i++) $tuttext = str_replace('[image'.$i.']','<img src="userimages/auth/tut'.$edit.'_'.$i.'.jpg">',$tuttext); }

	include('lib/entify.php');
	$tuttext = entify($tuttext);

	echo $tuttext;

	echo '</p><hr color="'.$colours['bg'].'"></p>';
	include('cp/tutorials.php');
	footer();
}

if(($mode=='newrev' OR $mode=='editrev') && $submit=='preview') { //preview
	include('header.php'); include('cp/sidebar.php');
	title('Preview:','none');

	$tuttext = stripslashes(htmlspecialchars(trim(censor_string($text,'')))); $tuttext = bbencode(str_replace("\n","<br>",$tuttext),'');
	echo $tuttext;

	echo '</p><hr color="'.$colours['bg'].'"></p>';
	include('cp/reviews.php');
	footer();
}

if($mode=='newtut') {
	$title = htmlspecialchars(trim(censor_string($title,''))); $description = htmlspecialchars(trim(censor_string($description,'')));
	$tuttext = htmlspecialchars(trim(censor_string(stripslashes($tuttext),''))); $tuttext = addslashes(bbencode(str_replace("\n","<br>",$tuttext)));

	if(!$title || !$description || !$tuttext) @error_die('<p>You forgot something...</p>'.$tuttext);
	if($_POST['submit']=='save') $status = -2; else $status = 0;
	if(!mysql_query("INSERT INTO authenticate (type,subtype,game,user_id,title,description,editor,text,status,date) VALUES ('tutorial','$type','$game','$userdata[user_id]','$title','$description','$editor','$tuttext','$status','$now_time')"))
		die('There was a problem: '. mysql_error());
	$article_id = mysql_insert_id();

	if($tutimg1) uploadfile('tutimg1','auth','1'); if($tutimg2) uploadfile('tutimg2','auth','2'); if($tutimg3) uploadfile('tutimg3','auth','3'); if($tutimg4) uploadfile('tutimg4','auth','4');	
	if($tutimg5) uploadfile('tutimg5','auth','5'); if($tutimg6) uploadfile('tutimg6','auth','6'); if($tutimg7) uploadfile('tutimg7','auth','7'); if($tutimg8) uploadfile('tutimg8','auth','8');

	if($status=='-2') $msg = 'Tutorial+saved.'; else $msg = 'Tutorial+submitted.+Please+wait+for+an+admin+to+auth+it.';

	header('Location: cp.php?msg='.$msg); die;
}

if($mode=='edittut') {
	if($select) {
		$location = 'auth/'; $sqltable = 'authenticate';
	} else {
	       	$location = 'tutorials/'; $sqltable = 'articles';
	}
	$title = htmlspecialchars(trim(censor_string($title,''))); $description = htmlspecialchars(trim(censor_string($description,'')));
	$tuttext = htmlspecialchars(trim(censor_string(stripslashes($tuttext),''))); $tuttext = addslashes(bbencode(str_replace("\n","<br>",$tuttext),''));

	if(!$origuser = @mysql_result(mysql_query("SELECT user_id FROM $sqltable WHERE id = '$edit' LIMIT 1"),0)) error_die('Tutorial doesn\'t exist, stop h4x1ng! ('.$sqltable.')');
	if($userdata['user_id']!=$origuser && $userdata['user_level']<3) { header("Location: cp.php?error=You+cannot+edit+that+tutorial"); die; }
	if($submit=='delete') {
	        if($sqltable=='authenticate') @mysql_query("DELETE FROM authenticate WHERE id = '$edit' LIMIT 1");
	        else @mysql_query("UPDATE articles SET user_id = '-1' WHERE id = '$edit' LIMIT 1");
		//@mysql_query("DELETE FROM $sqltable WHERE id = '$edit' LIMIT 1");
		//if(!$select) @mysql_query("DELETE FROM articles_text WHERE id = '$edit' LIMIT 1");
		//for($i=1;$i<9;$i++) @unlink('userimages/'.$location.$edit.'_'.$i.'.jpg');
		header('Location: cp.php?msg=Tutorial+deleted'); die;
	} else {
		if($select) {
			if($_POST['submit']=='save') $status = '-2'; else $status = 0;
			if(!mysql_query("UPDATE authenticate SET subtype='$type',game='$game',title='$title',description='$description',editor='$editor',text='$tuttext',status='$status',date='$now_time' WHERE id = '$edit' LIMIT 1")) die('Error: '.mysql_error());
			if($tutimg1) uploadfile('tutimg1','auth','1'); if($tutimg2) uploadfile('tutimg2','auth','2'); if($tutimg3) uploadfile('tutimg3','auth','3'); if($tutimg4) uploadfile('tutimg4','auth','4');
			if($tutimg5) uploadfile('tutimg5','auth','5'); if($tutimg6) uploadfile('tutimg6','auth','6'); if($tutimg7) uploadfile('tutimg7','auth','7'); if($tutimg8) uploadfile('tutimg8','auth','8');
		} else {
		       	if(!$tuttext) @error_die('No tutorial text was submitted!'.print_r($_POST,'return'));
		       	if($_POST['nocommentpm']=='on') $nocommentsql = '1'; else $nocommentsql = '';
			@mysql_query("UPDATE articles SET type='$type', game='$game', title='$title', description='$description', editor='$editor', edited='$now_time', nocommentpm = '$nocommentsql' WHERE id = '$edit' LIMIT 1");
			if(!mysql_query("UPDATE articles_text SET text = '$tuttext', game='$game' WHERE id = '$edit' LIMIT 1")) die('Error: '.mysql_error());
			$article_id = $edit;
			if($tutimg1) uploadfile('tutimg1','tutorials','1'); if($tutimg2) uploadfile('tutimg2','tutorials','2');	if($tutimg3) uploadfile('tutimg3','tutorials','3'); if($tutimg4) uploadfile('tutimg4','tutorials','4');	
			if($tutimg5) uploadfile('tutimg5','tutorials','5'); if($tutimg6) uploadfile('tutimg6','tutorials','6'); if($tutimg7) uploadfile('tutimg7','tutorials','7'); if($tutimg8) uploadfile('tutimg8','tutorials','8');
		} header('Location: cp.php?msg=Tutorial+edited'); die;
} }

if($mode=='addmap') {

	$mapname = stripslashes($mapname);
	if($mapname{0}=='"' OR $mapname{0}=="'") $mapname = substr($mapname,1);
	if(substr($mapname,-1,1)=='"' OR substr($mapname,-1,1)=="'") $mapname = substr($mapname,0,-1);

	$text = bbencode(str_replace("\n",'<br>',htmlspecialchars(trim(censor_string($text,'')))),'');
	$text = str_replace('=========','',$text);
	$mapname = addslashes(htmlspecialchars(trim(censor_string($mapname,'nobbcode'))));

	if(!$status) $status = 0; 
	$mod = $_POST['game'.$selgame]; $size = $_POST['size'];
	if(!$mapname OR !$text) { header('Location: cp.php?mode=maps&msg=Please+name+your+map+and+write+something+about+it'); die; }
	if(!$selgame OR !$mod) { 
		$sql = mysql_query("SELECT `id` FROM games");
		while($garray = mysql_fetch_array($sql)) { if($_POST['game'.$garray['id']]) { $selgame = $garray['id']; $mod = $_POST['game'.$garray['id']]; } }
		if(!$selgame || !$mod) error_die('No game/mod selected for your map');
	}

	if($result = @mysql_result(mysql_query("SELECT map_id FROM maps WHERE name = '$mapname' AND mod = '$mod' AND game = '$selgame' LIMIT 1"),0)) { header('Location: cp.php?error=You+already+have+a+map+for+'.$mod.'+called+'.$mapname); die; }

	//primary & mirror downloads, related link
	$related = $_POST['related']; if($related && substr($related,0,4)=='www.') $related = 'http://'.$related;
	$url = url_check($_POST['url']);
	$mirror1 = url_check($_POST['mirror1']);

	//insert
	$query = "INSERT INTO maps (`name`,`map_url`,`mirror1`,`related`,`map_about`,`status`,`added`,`date`,`cdate`,`mod`,`game`,`user_id`,`size`) VALUES ('$mapname','$url','$mirror1','$related','$text','$status','$now_time','$now_time','$cdate','$mod','$selgame','$userdata[user_id]','$size')";
	if(!mysql_query($query)) { errorlog('error inserting new map',mysql_error()); die(mysql_error()); }
	$mapid = mysql_insert_id();

	$numthumbs = 0; $screen1 = $_FILES['screen1']; $screen2 = $_FILES['screen2']; $screen3 = $_FILES['screen3'];
	if($_FILES['screen1']['name']) { if(createthumb('screen1',$mapid.'_1','maps/'.$selgame.'/images/')) $numthumbs++; }
	if($_FILES['screen2']['name']) { if(createthumb('screen2',$mapid.'_2','maps/'.$selgame.'/images/')) $numthumbs++; }
	if($_FILES['screen3']['name']) { if(createthumb('screen3',$mapid.'_3','maps/'.$selgame.'/images/')) $numthumbs++; }

	if(!$numthumbs<3 && ($screen1 OR $screen2 OR $screen3)) {
		$fileloc = 'maps/'.$selgame.'/images/'.$mapid.'_';
		if(file_exists($fileloc.'1_thumb.jpg')) { $f1e++; }
		if(file_exists($fileloc.'2_thumb.jpg')) { if(!$f1e) { $f1e++; rename($fileloc.'2_thumb.jpg',$fileloc.'1_thumb.jpg'); rename($fileloc.'2.jpg',$fileloc.'1.jpg'); } else $f2e++; }
		if(file_exists($fileloc.'3_thumb.jpg')) { if(!$f1e) { $f1e++; rename($fileloc.'3_thumb.jpg',$fileloc.'1_thumb.jpg'); rename($fileloc.'3.jpg',$fileloc.'1.jpg'); }
									if(!$f2e && $f1e) { rename($fileloc.'3_thumb.jpg',$fileloc.'2_thumb.jpg'); rename($fileloc.'3.jpg',$fileloc.'2.jpg'); } }
	}

	if($numthumbs>0) { $editsql = "thumbnails = '$numthumbs'"; }
	else $editsql = "scr1 = '$extscreen1', scr2 = '$extscreen2', scr3 = '$extscreen3', scr4 = '$extscreen4', scr5 = '$extscreen5'";

@mysql_query("UPDATE maps SET $editsql WHERE map_id = '$mapid' LIMIT 1");

	if($favmap=='on') @mysql_query("UPDATE users_profile SET favmap ='$mapid' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	if($status==100) $sqlmods = 'finishedmaps = finishedmaps + 1'; else $sqlmods = 'betamaps = betamaps + 1';
@mysql_query("UPDATE mods SET $sqlmods WHERE name = '$mod' AND game = '$selgame' LIMIT 1");
@mysql_query("UPDATE games SET maps = maps + 1 WHERE id = '$selgame' LIMIT 1");

	if($altmodes = @mysql_result(mysql_query("SELECT altmodes FROM mods WHERE name = '$mod' AND game = '$selgame' LIMIT 1"),0)) {
		include('header.php');
		include('cp/sidebar.php');
		title('Control Panel » New Map');
		echo 'Your map has been submitted (please do not refresh this page or it will be submitted again!). The mod you have submitted it for supports different gameplay modes, so please select one from the list below and click "continue":';
		
		if(!include('lib/gameplay_'.$selgame.$mod.'.php')) {
		        errorlog('loading gameplay library',$selgame.$mod);
			msg('Error loading gameplay library!','error');
			echo '<a href="maps.php?map='.$mapid.'"><b>» Continue</b></a>';
		} else {
		        echo '<blockquote><form action="cp.php" method="post"><input type="hidden" name="mode" value="editgameplay"><input type="hidden" name="id" value="'.$mapid.'">';
			while(list($c,$name)=each($gamemodes)) echo '<input type="checkbox" name="gameplay_'.$c.'" id="'.$c.'"><label for="'.$c.'"> '.$name.'</label><br>';
			echo '<input type="checkbox" name="gameplay" value="[none]" id="[none]"><label for="[none]"> <i>none of the above</label></i>';
			echo '</blockquote>';
			echo '<input type="submit" name="submit" value="continue" class="submit3"></form>';
		}
		include('footer.php');
	}
	
	if($_POST['spmirror'] && $status==100 && $url) {
		$text = 'Requests a mirror for <a href="maps.php?map='.$mapid.'">map #'.$mapid.'</a> using this <a href="'.$url.'" target="_blank">URL</a>.';
		@mysql_query("INSERT INTO authenticate (`type`,`user_id`,`title`,`text`,`ancillary1`,`ancillary2`) VALUES ('mirror','$userdata[user_id]','Mirror','$text','$url','$selgame')");
	}

	header("Location: maps.php?map=$mapid"); die;
}

if($mode=='editmap') {

	if(!$edit) { header('Location: cp.php?error=No+edit+id'); die; }
	if(!$status) $status = 0; $size = $_POST['size'];

	$curarray = mysql_fetch_array(mysql_query("SELECT * FROM maps WHERE map_id = '$edit' LIMIT 1"));
	if(!$curarray['user_id']) { header('Location: cp.php?error=This+map+doesn\'t+exist'); die; }
	if($curarray['user_id']!=$userdata['user_id'] && $userdata['user_level']<4) { header('Location: cp.php?error=This+is+not+your+map+so+you+can\'t+edit+it'); die; }

	if($submit=='delete') {
		if($reviewed = @mysql_result(mysql_query("SELECT review_id FROM reviews WHERE map_id = '$edit' LIMIT 1"),0)) { header("Location: cp.php?error=This+map+has+been+reviewed,+so+you+can't+delete+it"); die; }
		$maparray = mysql_fetch_array(mysql_query("SELECT game,mod,status FROM maps WHERE map_id = '$edit' LIMIT 1"));
		$loc = 'maps/'.$maparray['game'].'/images/'.$edit.'_';

		@unlink($loc.'1.jpg'); @unlink($loc.'1_thumb.jpg'); @unlink($loc.'2.jpg'); @unlink($loc.'2_thumb.jpg'); @unlink($loc.'3.jpg'); @unlink($loc.'3_thumb.jpg');
		@mysql_query("DELETE FROM maps WHERE map_id = '$edit' LIMIT 1");
		@mysql_query("UPDATE users_profile SET maps = maps-1 WHERE user_id = '$curarray[user_id]' LIMIT 1");
		if($maparray['status']<100) @mysql_query("UPDATE mods SET betamaps = betamaps - 1 WHERE name = '$maparray[mod]' AND game = '$maparray[game]' LIMIT 1");
			else @mysql_query("UPDATE mods SET finishedmaps = finishedmaps - 1 WHERE name = '$maparray[mod]' AND game = '$maparray[game]' LIMIT 1");
		@mysql_query("UPDATE games SET maps = maps - 1 WHERE id = '$mararray[game]' LIMIT 1");
		$redirect = 'cp.php?msg=Map+deleted';
	} else {
		if(!$selgame) { //if javascript didn't work
			$sql = mysql_query('SELECT id FROM games'); $cc=0;
			while($garray = mysql_fetch_array($sql)) {
				$var = 'game'.$garray['id']; if($$var) { $cc++; $selgame = $garray['id']; }
			} if($cc!=1) { header('Location: cp.php?mode=maps&edit='.$edit); die; }
		}

		$mapname = stripslashes($mapname);
		if($mapname{0}=='"' OR $mapname{0}=="'") $mapname = substr($mapname,1);
		if(substr($mapname,-1,1)=='"' OR substr($mapname,-1,1)=="'") $mapname = substr($mapname,0,-1);

		$mapname = addslashes(htmlspecialchars(trim(censor_string($mapname,'nobbcode'))));
		$text = htmlspecialchars(trim(censor_string($text,''))); $text = bbencode(str_replace("\n",'<br>',$text),'noimg');
		$text = str_replace('==========','',$text);
		if(!$mapname) { header('Location: cp.php?mode=maps&edit='.$edit.'&error=Missing+map+name'); die; }

		$modfor = 'game'.$selgame; $mod = $_POST[$modfor];
		if(!$mod OR !$selgame) { header('Location: cp.php?mode=maps&edit='.$edit); die; }
		if($downloadreset=='on') $downloadreset = ", downloads = '0'"; else $downloadreset = '';


		$related = $_POST['related']; if($related && substr($related,0,4)=='www.') $related = 'http://'.$related;
		$url = url_check($_POST['url']);
		$mirror1 = url_check($_POST['mirror1']);

		$numthumbs = 0;
		$screen1 = $_FILES['screen1']; $screen2 = $_FILES['screen2']; $screen3 = $_FILES['screen3'];
		if($_FILES['screen1']['name']) { if(createthumb('screen1',$edit.'_1','maps/'.$selgame.'/images/','')) $numthumbs++; }
		if($_FILES['screen2']['name']) { if(createthumb('screen2',$edit.'_2','maps/'.$selgame.'/images/','')) $numthumbs++; }
		if($_FILES['screen3']['name']) { if(createthumb('screen3',$edit.'_3','maps/'.$selgame.'/images/','')) $numthumbs++; }

		if($numthumbs<3) { $numthumbs = 0;
			$fileloc = 'maps/'.$selgame.'/images/'.$edit.'_';
			if(file_exists($fileloc.'1_thumb.jpg') && file_exists($fileloc.'1.jpg')) { $numthumbs++; $f1e++; }
			if(file_exists($fileloc.'2_thumb.jpg') && file_exists($fileloc.'2.jpg')) { $numthumbs++; if(!$f1e) { $f1e++; rename($fileloc.'2_thumb.jpg',$fileloc.'1_thumb.jpg'); rename($fileloc.'2.jpg',$fileloc.'1.jpg'); } else $f2e++; }
			if(file_exists($fileloc.'3_thumb.jpg') && file_exists($fileloc.'3.jpg')) { $numthumbs++; if(!$f1e) { $f1e++; rename($fileloc.'3_thumb.jpg',$fileloc.'1_thumb.jpg'); rename($fileloc.'3.jpg',$fileloc.'1.jpg'); }
									if(!$f2e && $f1e) { rename($fileloc.'3_thumb.jpg',$fileloc.'2_thumb.jpg'); rename($fileloc.'3.jpg',$fileloc.'2.jpg'); } }
		}

		$gameplaymodes = ', gameplay = \'';
		if(include('lib/gameplay_'.$selgame.$mod.'.php')) { $x=0; $submit = $_POST;
			while(list($key,$val) = each($submit)) { if(substr($key,0,9)=='gameplay_' && $val=='on') {
				$gameplaymodes.='-'.str_replace('gameplay_','',$key); $x++;
		} } } if($x) $gameplaymodes.='-'; else $msg = ';+please+go+back+and+select+some+gameplay+modes';
		$gameplaymodes.='\''; 

		if(!mysql_query("UPDATE maps SET `name` = '$mapname', `map_url` = '$url', mirror1 = '$mirror1', related = '$related', map_about = '$text', status = '$status', `mod` = '$mod', `game` = '$selgame', `date` = '$now_time', cdate = '$cdate', size = '$size', thumbnails = '$numthumbs', scr1 = '$extscreen1', scr2 = '$extscreen2', scr3 = '$extscreen3', scr4 = '$extscreen4', scr5 = '$extscreen5' $downloadreset $gameplaymodes WHERE map_id = '$edit' LIMIT 1")) { errorlog('updating maps',mysql_error()); header('Location: cp.php?error=error+updating+map'); die; }

		if($favmap=='on') @mysql_query("UPDATE users_profile SET favmap ='$edit' WHERE user_id = '$userdata[user_id]' LIMIT 1");

		if($mod!=$curarray['mod']) {
		        if($curarray['status']<100) $oldstatus = 'betamaps'; else $oldstatus = 'finishedmaps';
		        if($mod<100) $curstatus = 'betamaps'; else $curstatus = 'finishedmaps';
			@mysql_query("UPDATE mods SET $oldstatus = $oldstatus - 1 WHERE name = '$curarray[mod]' LIMIT 1");
			@mysql_query("UPDATE mods SET $curstatus = $curstatus + 1 WHERE name = '$mod' LIMIT 1");
        	}
	}

	if($_POST['spmirror'] && $status==100 && $url) {
		$text = 'Requests a mirror for <a href="maps.php?map='.$edit.'">map #'.$edit.'</a> using this <a href="'.$url.'" target="_blank">URL</a>.';
		if(!mysql_query("INSERT INTO authenticate (type,user_id,title,text,ancillary1,ancillary2) VALUES ('mirror','$userdata[user_id]','Mirror','$text','$url','$selgame')")) error_die('Error:'.mysql_error());
	}

	if(!$redirect) $redirect = 'cp.php?msg=Map+updated'; 
	header('Location: '.$redirect); die;
}

if($mode=='editgameplay') {
        $map = $_POST['id'];
        
        if($_POST['gameplay']!='[none]') {

        	$maparray = mysql_fetch_array(mysql_query("SELECT user_id,game,mod FROM maps WHERE map_id = '$map' LIMIT 1"));
		if(!$maparray) { header('Location: cp.php?error=Map+does+not+exist?!'); die; }
        	if($userdata['user_id']!=$maparray['user_id'] && $userdata['user_level']<3) { header('Location: cp.php?error=Cannot+update+this+map\'s+info'); die; }

		include('lib/gameplay_'.$maparray['game'].$maparray['mod'].'.php');
		
		$submit = $_POST; $gameplay = ''; $c = 0;
		while(list($key,$val)=each($submit)) {
		        if(substr($key,0,9)=='gameplay_' && $val=='on') {
				$gameplay .= '-'.str_replace('gameplay_','',$key);
				$c++;
			}
		} if($c) $gameplay .= '-'; else $gameplay = '';

        	if($gameplay) @mysql_query("UPDATE maps SET gameplay = '$gameplay' WHERE map_id = '$map' LIMIT 1");
	}
       	header('Location: maps.php?map='.$map); die;

}

if($mode=='newreview') {
	$text = htmlspecialchars(trim(censor_string($text,''))); $text = bbencode(str_replace("\n","<br>",$text),'');
	if(!$text) error_die('Please write a review..?');
	if(!$pros = bbencode(htmlspecialchars(trim(censor_string($pros,''))),'')) @error_die('No "pros" written');
	if(!$cons = bbencode(htmlspecialchars(trim(censor_string($cons,''))),'')) @error_die('No "cons" written');
	if(!$verdict = bbencode(htmlspecialchars(trim(censor_string($verdict,''))),'')) @error_die('No verdict written');

	$map_exists = @mysql_result(mysql_query("SELECT status FROM maps WHERE map_id = '$id' LIMIT 1"),0);
	if($map_exists!=100) error_die('Map does not exist, or is still in beta<p>'.$text);

	if(!mysql_query("INSERT INTO authenticate (type,subtype,user_id,title,description,editor,text,ancillary1,ancillary2,date) VALUES ('review','$id','$userdata[user_id]','Review','$verdict','$score','$text','$pros','$cons','$now_time')")) error_die('Error inserting review<p>'.mysql_error());

	header('Location: cp.php?msg=Thanks+for+your+review!'); die;

}

if($mode=='editreview') { $edit = $_POST['edit'];

	$text = htmlspecialchars(trim(censor_string($text,''))); $text = bbencode(str_replace("\n",'<br>',$text),'');
	if(!$text) error_die('Please write a review..?');
	if(!$pros = bbencode(htmlspecialchars(trim(censor_string($pros,''))),'')) error_die('No "pros" written');
	if(!$cons = bbencode(htmlspecialchars(trim(censor_string($cons,''))),'')) error_die('No "cons" written');
	if(!$verdict = bbencode(htmlspecialchars(trim(censor_string($verdict,''))),'')) error_die('No verdict written');
	if($_POST['score']<0||$_POST['score']>10) error_die('Score must be between 0 and 10');
	
	if($_POST['auth']) {
		$curuser = @mysql_result(mysql_query("SELECT user_id FROM authenticate WHERE id = '$edit' LIMIT 1"),0);
		if($curuser!=$userdata['user_id'] && $userdata['user_level']<4) error_die('This is not your review, so you can\'t edit it!');
		if(!mysql_query("UPDATE authenticate SET description = '$verdict', editor = '$_POST[score]', text = '$text', ancillary1 = '$pros', ancillary2 = '$cons', status = 0 WHERE id = '$edit' LIMIT 1")) error_die('Error updating review: '.mysql_error());
		header('Location: cp.php?msg=Review+edited');
 	} else {
 	       	$curuser = @mysql_result(mysql_query("SELECT reviewer_id FROM reviews WHERE review_id = '$edit' LIMIT 1"),0);
		if($userdata['user_id']!=$curuser && $userdata['user_level']<4) error_die('This is not your review, so you can\'t edit it!');
		if(!mysql_query("UPDATE reviews SET pros = '$pros', cons = '$cons', verdict = '$verdict', score = '$_POST[score]' WHERE review_id = '$edit' LIMIT 1")) error_die('Error updating review: '.mysql_error());
		if(!mysql_query("UPDATE reviews_text SET text = '$text' WHERE review_id = '$edit' LIMIT 1")) error_die('Error updating review text: '.mysql_error());
		header('Location: features.php?page=reviews&id='.$edit);
	}
	die;
}


//user stuff below

	$uparray = mysql_fetch_array(mysql_query("SELECT * FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1"));	

if($mode=='editprofile') {
	$curpass = md5($curpass); if($userdata[password]!=$curpass) { header("Location: cp.php?mode=profile&msg=Incorrect+password"); die; }
	if(!$email OR !substr_count($email,'@')) { header('Location: cp.php?mode=profile&msg=New+e-mail+address+is+not+valid'); die; }
	$profile = str_replace("\n","<br>",bbencode(htmlspecialchars(trim(censor_string($profile,''))),''));
	$signature = str_replace("\n","<br>",bbencode(htmlspecialchars(trim(censor_string($signature,''))),'noimg'));
	$avatar = trim(censor_string($avatar,''));

	$birthday = $_POST['bday'].$_POST['bmonth'];
	if(strlen($birthday)==4) $birthdaysql = ", birthday = '$birthday'"; else $birthdaysql = '';

	$occupation = htmlspecialchars(trim(censor_string($occupation,''))); $location = htmlspecialchars(trim(censor_string($location,'')));

	if(!mysql_query("UPDATE users_profile SET icq='$icq', aim='$aim', yim='$yim', msnm='$msn', steam='$steam', website='$website', occupation='$occupation', location='$location', profile='$profile', user_sig = '$signature', avatar_text = '$avatar' $birthdaysql WHERE user_id = '$userdata[user_id]' LIMIT 1"))
		die(mysql_error());

	if($addsig=='on') $addsig = 1; else $addsig = ''; if($addsig != $userdata[addsig]) @mysql_query("UPDATE users SET addsig = '$addsig' WHERE user_id = '$userdata[user_id]' LIMIT 1");

	if($newpass1 OR $newpass2) {
		if($newpass1 != $newpass2) { header("Location: cp.php?mode=profile&msg=New+passwords+do+not+match"); die; }
		$newpass = md5($newpass1);
		@mysql_query("UPDATE users SET password = '$newpass' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	}

	$msg = 'Profile+updated+successfully';

	if($email != $uparray['user_email']) {
		$actkey = md5(rand().'change'.$userdata[user_id]);
		$sql = mysql_query("SELECT * FROM register WHERE userid = '$userdata[user_id]' AND email!='' LIMIT 1");
		if($carray = mysql_fetch_array($sql)) @mysql_query("UPDATE register SET email = '$email', actkey = '$actkey' WHERE userid = '$userdata[user_id]' LIMIT 1");
			else @mysql_query("INSERT INTO register (userid,actkey,email) VALUES ('$userdata[user_id]','$actkey','$email')");
		$message = 'To change your e-mail address, please follow this link: 
		http://www.snarkpit.net/?page=activate&key='.$actkey.'
If you have received this e-mail in error or changed your mind, ignore it and nothing will happen.';
		mail($email, 'The SnarkPit- your change of e-mail address', $message, "From: leperous@snarkpit.net");
		$msg = 'Profile+updated-+please+check+your+new+e-mail+address+to+activate+it';
	}

	header('Location: cp.php?msg='.$msg); die;
}

if($mode=='editprefs') {
	if(!file_exists('themes/'.$theme.'.php')) die('Invalid theme selected');

	if($hideemail=='on') $hideemail = 1; else $hideemail = '0'; $hidestuff = $hideemail.','.$showrating;
	if($hidehelp=='on') $hidehelp = 1; else $hidehelp = '';
	if($javaoff=='on') $javaoff = 1; else $javaoff = '';
	if($ppp<10 || $ppp>100) $ppp = 15;
	if($fplim<2 || $fplim > 15) $fplim = 4;

	if(!mysql_query("UPDATE users_profile SET hidestuff = '$hidestuff' WHERE user_id = '$userdata[user_id]' LIMIT 1")) errorlog('updating users_profile #1',mysql_error());
	if(!mysql_query("UPDATE users SET hidehelp = '$hidehelp', theme = '$theme', javaoff = '$javaoff', user_ppp = '$ppp', fplim = '$fplim', timezone = '$timezone' WHERE user_id = '$userdata[user_id]' LIMIT 1")) errorlog('updating users_profile #2',mysql_error());
	header('Location: cp.php?msg=Preferences+saved'); die;
}

	include('header.php'); include('cp/sidebar.php'); print_r($_POST);
	error_die('You shouldn\'t have got to this part of the page: mode '.$mode);

?>
