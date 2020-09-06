<?php
umask(0000);
$usercp = $_GET['usercp']; $upload = $_GET['upload'];

if(!$db) include('../config.php');
if(!$userdata) die('Cannot access this page');
$username = strtolower(str_replace(' ','_',$userdata['username']));
$location = '../pits/'.$username.'/';

$header = '<html>
	<head>
	<base target="_self">
	<link rel="stylesheet" href="../themes/'.$theme.'.css" type="text/css">
	<script language="javascript" type="text/javascript">
		function popwin(x,varscroll) {
			if(varscroll=="") varscroll = "no";
			child = open(x,\'popup\',\'width=400,height=400,top=50,left=50,status=no,scrollbars=\'+varscroll+\',resizable=yes\');
			child.opener=this; child.focus();
	}</script>
	</head>
	<body id="minibodyid" style="background-image:none;border:1px solid '.$colors['lighttext'].';padding:4px">
	';

if($usercp) {

	if(!$username) die('Cannot access this page');

	$sql = mysql_query("SELECT * FROM homepage WHERE user_id = '$userdata[user_id]'");
	if(!$myrow = mysql_fetch_array($sql)) die('You don\'t have webspace');
	if($myrow['status']==2) die("Your website is offline, probably because you have been temporarily banned. Please contact the site admin for reasons.");

	if(!$location) die('Access denied!');
	if($opendir = $_GET['opendir']) { $opendir = str_replace('..','',$opendir); $location = $location . $opendir.'/'; $refresh_link = '&opendir='.$opendir; }
	$location = str_replace('//','/',$location);

	$scabby_location = str_replace('../','',$location);
	$extval = array('txt', 'zip', 'jpeg', 'gif', 'jpg', 'tga', 'png', 'rar', 'htm', 'html', 'js', 'swf', 'ico', 'css','log');
	
	echo $header;
?>
	<table height="100%" width="99%" cellspacing=0 cellpadding=0><tr><td valign=top><font size=2 face=verdana>

	<form method="POST" action="homepage.php?usercp=submit<?=$refresh_link?>" enctype="multipart/form-data" target="_self">
	<table width="99%">
	<tr><td align=right valign=top width="18%"><b>Valid file types:</b></td><td width="82%">
	<?php for ($i = 0; $i < count($extval); $i++) { echo $extval[$i]; if ($i<count($extval)-1) echo ', '; } ?>
	</td></tr>
	<tr><td align=right valign=top style="padding-top:5px"><b>Upload a file:</b></td>
	<td align=left><INPUT TYPE="file" NAME="file" class="textinput">
	<input type="checkbox" name="thumbnail" id="thumb"> <label for="thumb">tick to generate a thumbnail (JPG images only)</label><br>
	<input type="submit" class="submit3" name="submitfile" value="Upload" onClick="if(!file.value) return false; minibodyid.style.cursor='wait'">
	<b>OR</b> <a href="?upload=multiple&amp;to=<?=$location?>" target="_self">click here to upload several files at once to this directory</a>
	</td></tr>
	</table></form>

	<table width="99%"><tr><td height=20><p align=center>

<?php

	if($_POST['submitfile'] && $_FILES['file'] && $_FILES['file']!='none') {
		set_time_limit(300);
		$orig_name = $_FILES['file']['name'];
		$filename = ereg_replace("[^a-zA-Z0-9._]",'', str_replace(' ', '_', ereg_replace("%20",'_',$orig_name)));
		$extget = substr(strrchr(strtolower($filename),'.'),1);

		if($_POST['thumbnail']=='on' && $extget=='jpg') {
			if(!function_exists('createthumb')) include('../func_parse.php');
			$name = substr($filename,0,-strlen($extget)-1);
			if(!createthumb('file',$name,$location,'orig_size')) echo '<b><font color="'.$colors['no'].'">Error creating thumbnail!</font></b>';
		} else {
			$extfound = in_array($extget,$extval);
			if(!$extfound || substr_count($filename,'.php')) { echo '<b><font color="'.$colors['no'].'">You may not upload files of that type!</b></font> (.'.strtoupper($extget).')'; } else {
				if(!move_uploaded_file($_FILES['file']['tmp_name'], $location.$filename) ) { echo '<b><font color="'.$colors['no'].'">Couldn\'t upload file</font></b> '.$location.$filename; } else {
					echo '<b>Uploaded to <a href="http://www.snarkpit.net/'.$scabby_location.$filename.'" target="_blank">http://www.snarkpit.net/'.$scabby_location.$filename.'</a></b>';
					if(!chmod($location.$filename,0666)) echo 'Couldn\'t CHMOD file '.$location.$filename;
				}
			}
		}
	}
	elseif($_POST['submitnewdir'] && $_POST['newdirname']) {
		$nloc = $location.$_POST['newdirname'];
		if(@mkdir($nloc,0777)) {
		        echo 'Created new directory &quot;'.$_POST['newdirname'].'&quot;';
			if(!chmod($nloc,0777)) echo '<b><font color="'.$colors['no'].'">Couldn\'t CHMOD dir!</font></b>';
		} else {
		       	echo '<font color="'.$colors['no'].'"><b>Couldn\'t create new directory </b></font>';
		}
	}
	elseif($_POST['submitaction'] && $_POST['fromfile'] && $_POST['tofile']) {
		if($_POST['submitnewdir']) echo '<br>';
		if(!$opendir) { $fromfile = str_replace('..', '', $_POST['fromfile']); $tofile = str_replace("..", "", $_POST['tofile']); }
		if($_POST['submitaction']=='rename') {
			list($fromfile_name, $fromfile_ext) = explode('.', $_POST['fromfile']);
			list($tofile_name, $tofile_ext) = explode('.', $_POST['tofile']);
			if(substr_count($tofile_name,'.') || substr_count($tofile_ext,'.')) echo '<font color="'.$colors['no'].'">Invalid filename</font>'; else {
				$tofile_ext = $fromfile_ext;
				$tofile = $tofile_name.'.'.$tofile_ext;
				if(substr($tofile,-4)=='.php' || substr($tofile,-5)=='.php3') die('You cannot rename to PHP files! Nice try though...');
				if(rename($location.$fromfile, $location.$tofile)) { echo "Renamed $fromfile to $tofile"; } else { echo '<font color="'.$colors['no'].'"><b>Can\'t rename '.$fromfile.'!</b></font>'; }
			}
		} elseif($_POST['submitaction']=='move') {
			list($fromfile_name, $fromfile_ext) = explode('.', $fromfile);
			if(!$fromfile_ext) { echo '<b><font color="'.$colors['no'].'">You may not move folders</b></font>'; } else {
				if(copy($location.$fromfile, $location.$tofile."/".$fromfile) && unlink($location.$fromfile)) { echo "Moved file"; } else { echo '<b><font color="'.$colors['no'].'">Can\'t move '.$fromfile.'!</b></font>'; }
				if(!chmod($location.$tofile.'/'.$fromfile,0777)) echo 'Can\'t CHMOD file';
			}
		}
	}
	
	elseif($_POST['submitmultiple']) {
		for($i=1;$i<=10;$i++) { $f = 'file'.$i; if($_FILES['file'.$i]['name']) {
		        $orig_name = $_FILES['file'.$i]['name'];
			$filename = ereg_replace("[^a-zA-Z0-9._]",'', str_replace(' ', '_', ereg_replace("%20",'_',$orig_name)));
			$extget = substr(strrchr(strtolower($filename),'.'),1);
			$extfound = in_array($extget,$extval);
			if(!$extfound || substr_count($filename,'.php')) { echo '<b><font color="'.$colors['no'].'">You may not upload files of type '.$extget.'!</b></font><br>'.$extget; } else {
				if(!move_uploaded_file($_FILES['file'.$i]['tmp_name'], $location.$filename) ) { echo '<b><font color="'.$colors['no'].'">Can\'t upload file</font></b> '.$location.$filename.'<br>'; } else {
					echo '<b>Uploaded '.$filename.' to /'.$scabby_location.'</b><br>';
					if(!chmod($location.$filename,0666)) echo 'Can\'t CHMOD file '.$location.$filename.'<br>';
				}
			}
		} }
	}

	if($delete=$_GET['delete']) {
		$delete = str_replace('..','',$delete);
		if(is_dir($location.$delete)) { if(force_rmdirs($location.$delete)) { echo "Deleted directory &quot;$delete&quot;"; } else { echo '<b><font color="'.$colors['no'].'">Can\'t delete directory</font> &quot;'.$location.$delete.'&quot;!</b></font>'; } } else {
		if(unlink($location.$delete)) { echo "Deleted &quot;$delete&quot;"; } else { echo '<b><font color="'.$colors['no'].'">Can\'t delete directory</font> &quot;'.$location.$delete.'&quot;!</b>'; }
	} }

	echo '</td></tr></table>';
	echo "\n".'<table width="99%" cellspacing=1 cellpadding=2><tr>';
	echo '<td colspan=3 background="../themes/'.$theme.'/table_blue.gif" height="19" style="font-size:9pt"><a href="?usercp=upload'.$refresh_link.'" target="_self"><img src="../images/gfx_refresh.gif" align=right border=0 alt="Refresh Page"></a><font color="'.$colors['item'].'"><b>Browsing:</b></font> http://www.snarkpit.net/pits/';
	echo '<a href="homepage.php?usercp=upload" target="_self">'.$username.'</a>/';

		if($opendir = $_GET['opendir']) {
		        $dir_loc = explode('/', $opendir);
			$count = count($dir_loc);
			for($i=0;$i<$count;$i++) { if($dir_loc[$i]) {
				$grow_dir=$grow_dir.'/'.$dir_loc[$i];
				echo '<a href="?usercp=upload&amp;opendir='.$grow_dir.'" target="_self">'.$dir_loc[$i].'</a>/';
			} }
		}

	echo '</td></tr>';

	if($opendir) echo "\n".'<tr><td align=center width=50><a href="?usercp=upload" class="white" target="_self"><b><img src="../images/gfx_updir.gif" border=0></b></a></td></tr>';

		$dir = opendir($location);
		if(!$dir) die('Error opening <i>'.$location.'</i>');
		while(($file=readdir($dir))!== false) { $dirlist[] = $file; }	
		closedir($dir); sort($dirlist);
		while(list($key,$file) = each($dirlist)) {
			if($file=='.' || $file=='..') { } else { 
				list($filename, $filetype) = explode('.', $file); 
				$filesize = floor(filesize($location.$file)/1024);
				if(!$filetype) {
					echo "\n".'<tr><td width=1><font size=1><a href="?usercp=upload&amp;delete='.$file.$refresh_link.'" onclick="return confirm(\'Are you sure you want to delete this directory and everything in it?\')" target="_self">delete</a>&nbsp;</font></td>';
					echo '<td width=1><font size=1><a href="#actions" onclick="document.forms[\'homepageactions\'].elements[\'fromfile\'].value = \''.$file.'\'" target="_self">select&nbsp;</a></td>';
					echo '<td width="99%"><a href="homepage.php?usercp=upload&amp;opendir='.$opendir.'/'.$file.'" class="white" target="_self"><img src="../images/gfx_folder.gif" align="texttop" border=0> <b>'.$file.'</a></b>';
					echo '<font size=1> '; get_folder_size($location.$file); echo '</font></td></tr>';
				} $num_files++;
			} 
		}

		$adir = opendir($location);
		while (($file = readdir($adir)) !== false) { $adirlist[] = $file; }	
		closedir($adir); 
		sort($adirlist);
		while (list($key, $file) = each($adirlist)) {
		if ($file == "." || $file == '..') { } else { 
		list($filename, $filetype) = explode('.',$file); $filesize = floor(filesize($location.$file)/1024);
		if($filetype!="") {
			$total_file_size = $total_file_size + $filesize;
			echo "\n".'<tr><td width=1><font size=1><a href="?usercp=upload&delete='.$file.$refresh_link.'" target="_self">delete</a>&nbsp;</font></td>';
			echo '<td width=1><font size=1><a href="#actions" onclick="document.forms[\'homepageactions\'].elements[\'fromfile\'].value = \''.$file.'\'" target="_self">select&nbsp;</a></td>';

				$imgsize = '';
				$img = '<a href="'.$location.$file.'" class="white" target="_blank"><img src="../themes/'.$images['moddir'].'/icon_HL2.gif" border=0 align=texttop alt="misc file">';
				if($filetype=='jpg') { $img='<a href="javascript:void(0)" onClick="popwin(\'../screenshot.php?img='.str_replace('../','',$location.$file).'\'); return false;" class="white"><img src="../images/gfx_jpg.gif" alt="JPG image" align=texttop border=0>'; $imgsize = getimagesize($location.$file); }
				elseif($filetype=='gif') { $img='<a href="'.$location.$file.'" class="white" target="_blank"><img src="../images/gfx_gif.gif" alt="GIF image" align="texttop" border=0>'; $imgsize = getimagesize($location.$file); }
				elseif($filetype=='htm' OR $filetype=="html") { $img="<a href='".$location.$file."' target='_blank' class=white><img border=0 src='../images/gfx_html.gif' align=texttop>"; }
				elseif($filetype=='zip') { $img='<a href="'.$location.$file.'" class="white"><img src="../images/gfx_zip.gif" alt="ZIP file" align="texttop" border=0>'; }
				elseif($filetype=='txt') { $img="<a href='".$location.$file."' class='white' target='_blank'><img src='../images/gfx_txt.gif' alt='TXT file' align=texttop border=0>"; }
				elseif($filetype=='js') $img = '<a href="'.$location.$file.'" class="white"><img src="../images/gfx_js.gif" align=texttop border=0>';
				elseif($filetype=='swf') $img = '<a href="'.$location.$file.'" class="white"><img src="../images/gfx_swf.gif" align=texttop border=0>';

			echo '<td width="100%">'.$img.' '.$file.'</a></b> <font size=1 color="'.$colors['lighttext'].'">'.$filesize.'kb'.(($imgsize)?'; '.$imgsize[0].'x'.$imgsize[1].' pixels':'').'</font></td></tr>';
		} $num_files++; 
		} } 

		if(!$num_files) echo '<tr><td width=50></td><td width=100%>Folder is empty</td></tr></table>'; else echo '</table><p>Folder size: '.$total_file_size.'kb';
?>

		<form action="homepage.php?usercp=submit&selectaction=<?=$submitaction.$refresh_link?>" method="post" name="homepageactions">
		<p><table width="99%" border=0>
		<tr>
			<td width="22%" valign=top align=right><b>Create new directory:<b></td>
			<td width="80%" valign=top><input type="text" class="textinput" name="newdirname" border=0></td>
		</tr>
		<tr>
			<td valign=top align=right><a name="actions"><b>File action:</b></a></td>
			<td><SELECT NAME="submitaction"><option value="move">Move</option><option value="rename"<?php if($submitaction=='rename') echo ' SELECTED'; ?>>Re-name</option></select>
			<input type="text" class="textinput" name="fromfile" border=0> to <input type="text" class="textinput" name="tofile" border=0></td>
		</tr>
		<tr>
			<td align=right><input type="submit" name="submitnewdir" value="Do It!" class="submit3" onClick="minibodyid.style.cursor='wait'"></form></td>
		</tr>
		</table>

<?php die; }

elseif($upload=='multiple') {
	echo $header;
	subtitle('Upload multiple files','');
	if(!$to = $_GET['to']) die('Sorry, you need to specify where you want to mass-upload to.');
	echo 'Uploading to directory <b>'.$to.'</b>: (<a href="javascript:history.back(-1)" target="_self">go back</a> to upload to another location)<p>';
	$dir = str_replace($location,'',$to);
	echo '<form method="post" action="homepage.php?usercp=submit&opendir='.$dir.'" enctype="multipart/form-data" target="_self"><table width="100%"><tr>';
	echo '<input type="hidden" name="dir" value="'.$dir.'">';
	for($i=1;$i<=10;$i++) echo '<td width="10%" align=right><b>File '.$i.':</b><td width="40%"><input type="file" name="file'.$i.'" class="textinput"></td>'.(($i%2)?'':"\n".'</tr><tr>');
	echo '</tr></table>';
	echo '<blockquote><input type="submit" name="submitmultiple" value="submit" class="submit3" onclick="style.cursor=\'wait\'">';
die; }

if(!$userdata) { header('Location: login.php?linkto=cp.php'); die; }

if($request) {
	title($t_cp.' Request A Website');
	$result = mysql_result(mysql_query("SELECT user_id FROM authenticate WHERE type = 'homepage' LIMIT 1"),0);
	if($result == $userdata['user_id']) echo 'You have already requested a homepage, please wait for an admin to see to it.';
	else { @mysql_query("INSERT INTO authenticate (type,user_id) VALUES ('homepage','$userdata[user_id]')");
		echo 'Your request has been sent to an admin to ponder over, you\'ll be informed by private message about what happens.';
	}
footer(); }

function get_folder_size($dir) {
 	$dh = opendir($dir);
	while(false !== ($ffile = readdir($dh))) {
		if($ffile != '.' && $ffile != '..') {
			$path = $dir."/".$ffile;
			if(is_dir($path)) { get_folder_size($path); }
			else { $filesize = floor(filesize($path)/1024);
			$folder_file_size = $folder_file_size + $filesize; }
 		}
		$total_folder_size = $total_folder_size + $folder_file_size;
	} closedir($dh);
}

function force_rmdirs($dir) {
       $dh = opendir($dir);
       while(false !== ($file = readdir($dh))) {
           if($file != '.' && $file != '..') {
               $path = $dir.'/'.$file;
               if(is_dir($path)) force_rmdirs($path);
               else unlink($path);
           }
       }
       return rmdir($dir);
}

tracker('Managing webspace','');

$sql = mysql_query("SELECT * FROM homepage WHERE user_id = '$userdata[user_id]'");
$myrow = mysql_fetch_array($sql);
if (!$myrow) { ?>

	Sorry, we're not taking any new people on right now, check out
	<a href="http://www.hlgaming.com/hlgaming/hosting.php" target="_blank">HLGaming's</a> free hosting.

<?php } else {

	title($t_cp.' Homepage','cp');

	if($myrow['status']==0) { echo 'Your website has not yet been activated. Please wait for a few days.'; footer(); }
	if($myrow['status']==2) { echo 'Your website is offline, probably because you have been temporarily banned. Please contact the site admin for reasons.'; footer(); }

?>
	<b style="color:red">Some pits have not been restored in our move, please PM Lep if you want yours back.</b>
	<br>
	Please note that it may take several minutes to upload your maps on a slower connection, so only press 'upload' once.<br>
	Do not attempt to upload files larger than 2mb on a 56k connection.<br>
	<iframe height=500 width="99%" border=0 frameborder=0 src="cp/homepage.php?usercp=upload">Your browser is not iframe compatible- you must upgrade it to use this feature!</iframe>

<? } ?>
