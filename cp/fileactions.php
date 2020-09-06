<?php include('../config.php');

	if(!$userdata || !$userdata['user_id']) { header('Location: login.php?linkto=cp.php'); die; }
	if(!$_POST['submit']) { header('Location: ../cp.php'); die; }
	while(list($var,$val)=each($_POST)) $$var = $val;

if($refer = $_SERVER['HTTP_REFERER']) {
	if(!substr_count($refer,'cp.php?mode=files')) die('invalid submission page, please do this via the website');
}

if(!$mode) $mode = $_POST['action'];
if(!$mode) { header('Location: ../cp.php?error=stop+h4x1ng!'); die; }

function uploadfile($name,$rename,$type) { global $fileid;

	$die = '';
	if(!$filename = strtolower($_FILES[$name]['name'])) $die = 'Error uploading file, please do it properly'; 
	$fileext = substr($filename,-3);
	if($fileext!=$type) { errorlog(print_r($_FILES,'return'),'uploading '.$_POST['type']); $die = 'Invalid filetype- only '.$type.' files are allowed'; }
	if(!move_uploaded_file($_FILES[$name]['tmp_name'],$rename)) $die = 'Couldn\'t upload file! Please contact the site admin';

	if($die) {
		if($fileid) @mysql_query("DELETE FROM files WHERE file_id = '$fileid' LIMIT 1");
		die('Error: '.$die);
	}

	if($type=='jpg') {
		if(!$sz = GetImageSize($rename)) die('Uploaded screenshot wasn\'t an image!'); 
		$x = $sz[0]; $y = $sz[1];
		if($x>120) {
			$src_image = ImageCreateFromJPEG($rename);
			$resize = ImageCreateTrueColor(120,90);
			ImageCopyResampled($resize,$src_image,0,0,0,0,120,90,$x,$y);
			ImageJPEG($resize,$rename,85);
		}
	}

	chmod($rename,0777);
}

if($text) { 
	include('../func_parse.php');
	$text = bbencode(htmlspecialchars(trim($text)),'noimg');
	$text = str_replace("\n",'<br>',$text);
}

if($mode=='newfile') {

	$sql = mysql_query("SELECT id FROM games"); $i=0;
	while($garray = mysql_fetch_array($sql)) {
		$var = 'game'.$garray['id'];
		if($_POST[$var]) { $i++; $game = $garray['id']; $section = $_POST[$var]; }
	}

	$error = '';
	if(!$filename = $_POST['filename']) $error = 'Missing file name';
	$screenshot = $_FILES['screenshot']['name'];
	if(!$type = $_POST['type']) $error = 'Error submitting file, no type selected (model, prefab, ..?)';
	if(!$game) $error = 'Missing game';
	if(!$_FILES) $error = 'No file submitted!';
	if(!$text) $error = 'Please write a description of your file';
	if(!$section) $error = 'Missing prefab/model section';
	if($i!=1) $error = 'Please submit for only one game.';

	if($error) { header('Location: cp.php?mode=files&action='.$type.'&msg='.str_replace(' ','+',$error)); die; }

	if(!mysql_result(mysql_query("SELECT description FROM files_cats WHERE name = '$section' AND under = '$_POST[type]' AND game = '$game' LIMIT 1"),0)) die('Invalid section, please stop h4x1ng');
	if(!mysql_query("INSERT INTO files (filename,type,subcat,game,date,description,author) VALUES ('$filename','$type','$section','$game','$now_time','$text','$userdata[user_id]')")) { errorlog(mysql_error(),'file insert'); die('There was a problem adding the new file: '.mysql_error()); }
	$fileid = mysql_insert_id(); $filename = stripslashes($filename);

	$sqlloc = $type.'/'.$fileid.'-'.strtolower(str_replace(' ','_',$filename)).'.zip';
	uploadfile('file','../files/'.$game.'/'.$sqlloc,'zip');

	if(!$sqlloc || !file_exists('../files/'.$game.'/'.$sqlloc)) {
		@mysql_query("DELETE FROM files WHERE file_id = '$fileid' LIMIMT 1");
		errorlog('Error uploading file',print_r($_FILES,'return')); echo 'files/'.$game.'/'.$sqlloc;
		error_die('There was a problem uploading your file, pleasetry again or contact the site admin.');
	}

	if($screenshot) {
		$screenshotname = $type.$fileid;
		createthumb('screenshot',$screenshotname,'../files/'.$game.'/images/');
		$screenshotname.='_thumb.jpg';
	}

	$sqlloc = addslashes($sqlloc); if(!$sqlloc) { errorlog('no sqlloc','updating files #3'); die('Error adding file, please contact the site admin'); }
	if(!$sql = mysql_query("UPDATE files SET url = '$sqlloc', screenshot = '$screenshotname' WHERE file_id = '$fileid' LIMIT 1") OR !$sqlloc) { errorlog(mysql_error(),'file updating #2'); die('There was an error updating the file info with the URL, please contact the site admin'); }
	@mysql_query("UPDATE files_cats SET files = files + 1 WHERE name = '$section' AND under = '$type' AND game = '$game' LIMIT 1");
	@mysql_query("UPDATE users_profile SET files = files+1 WHERE user_id = '$userdata[user_id]' LIMIT 1");
	header('Location: ../cp.php?msg=File+added'); die;
}

if($mode=='editfile') {
	if(!$edit) { header('Location: ../cp.php&msg=missing+file+id'); die; }
	$oldarray = mysql_fetch_array(mysql_query("SELECT * FROM files WHERE file_id = '$edit' LIMIT 1"));
	if($userdata['user_id']!=$oldarray['author'] && $userdata['user_level']<4) { header('Location: ../cp.php?error=You+cannot+edit+that+file!'); die; }
	$oldfilename = stripslashes(strtolower(str_replace(' ','_',$oldarray['filename']))); 

	$sql = mysql_query("SELECT id FROM games"); $i=0;
	while($garray = mysql_fetch_array($sql)) {
		$var = 'game'.$garray[id];
		if($$var) { $i++; $game = $garray[id]; $section = $$var; }
	} if($i!=1 OR !$section OR !$game) { header('Location: ../cp.php?mode=files&edit=$edit&msg=Error+updating+file'); die; }
	$type = $_POST['type'];

	if($_POST['submit']=='delete') {
		@mysql_query("DELETE FROM files WHERE file_id = '$edit' LIMIT 1");
		@mysql_query("UPDATE users_profile SET files = files-1 WHERE user_id = '$oldarray[author]' LIMIT 1");
		unlink('files/'.$game.'/'.$oldarray['url']);
		unlink('files/'.$game.'/images/'.$oldarray['screenshot']);
		header('Location: ../cp.php?msg=File+deleted'); die;
	}

	if(!$text || !$filename) { header('Location: ../cp.php?mode=files&edit='.$edit); die; }
	if(!mysql_result(mysql_query("SELECT description FROM files_cats WHERE name = '$section' AND under = '$type' AND game = '$game' LIMIT 1"),0)) die('Invalid section, please stop h4x1ng');

	if(!mysql_query("UPDATE files SET filename = '$filename', subcat = '$section', game = '$game', date = '$now_time', description = '$text' WHERE file_id = '$edit' LIMIT 1")) { errorlog('files: '.mysql_error()); header("Location: ../cp.php?msg=error+updating+file+details!"); die; }

	$filename = stripslashes(strtolower(str_replace(' ','_',$filename))); 
	if($filename!=$oldfilename) rename('../files/'.$game.'/'.$type.'/'.$edit.'-'.$oldfilename.'.zip','../files/'.$game.'/'.$type.'/'.$edit.'-'.$filename.'.zip');

	if($file) {
		$sqlloc = $type.'/'.$edit.'-'.$filename.'.zip';
		if(!uploadfile('file','../files/'.$game.'/'.$sqlloc,'zip')) die('could not upload file!');
		$sqlloc = addslashes($sqlloc); if(!$sqlloc) error_die('Error uploading file, please try again or contact the site admin');
		if(!$sql = mysql_query("UPDATE files SET url = '$sqlloc' WHERE file_id = '$edit' LIMIT 1") OR !$sqlloc) die('There was an error, please contact the site admin: '.mysql_error());
	}

	if($screenshot) {
		$screenshotname = $type.$edit.'.jpg';
		uploadfile('screenshot','../files/'.$game.'/images/'.$screenshotname,'jpg');
	}

	header('Location: ../cp.php?msg=File+edited'); die;
}

die('You should not have got here: '.$mode); ?>
