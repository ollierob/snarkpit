<?php
	if($_GET['id']&&$_GET['page']=='tutorials') { $ptut = true; require_once('scripts/spell_checker.php'); } else $ptut = false;
	include('config.php');

if(isset($_POST['search'])) {
	$game = $_POST['searchgame'];
	include('header.php');
	include('editing/sidebar.php');
	include('index/query.php');
	footer(); 
}

if(isset($_GET['setdefault'])) {
        $setdefault = $_GET['setdefault'];
	if(!$select = mysql_result(mysql_query("SELECT id FROM games WHERE id = '$setdefault' LIMIT 1"),0)) error_die('Game does not exist, stop h4x1ng!');
	if($userdata) @mysql_query("UPDATE users SET game = '$setdefault' WHERE user_id = '$userdata[user_id]' LIMIT 1");
	header("Location: editing.php?game=$setdefault"); die;
}

if(isset($_GET['download'])) {
        $download = $_GET['download'];
	if($_GET['game']) $download = mysql_result(mysql_query("SELECT file_id FROM files WHERE filename = '$download' LIMIT 1"),0);
	$sql = mysql_query("SELECT game,url FROM files WHERE file_id = '$download' LIMIT 1");
	if($result = mysql_fetch_array($sql)) {
		if(substr_count($result['url'],'http://')) $downloadurl = $result[url]; else $downloadurl = 'files/'.$result[game].'/'.stripslashes($result[url]);
		@mysql_query("UPDATE files SET downloads = downloads + 1 WHERE file_id = '$download'");
		header("Location: $downloadurl"); die;
	}
}

if(isset($_GET['game'])) $game = $_GET['game']; else { if(!$game = $userdata['game']) $game = $default_game; }
$sql = mysql_query("SELECT * FROM games WHERE id = '$game' LIMIT 1");
if(!$garray = mysql_fetch_array($sql)) { header('Location: editing.php'); die; }

if($page) $pagetitle = $garray['name'].' '.ucfirst($page); else $pagetitle = $garray['name'].' Editing section ';
	if($page=='files' &&$type) $pagetitle.=': '.ucfirst($type);

if(isset($_POST['search'])) { include('index/query.php'); footer(); }

if($ptut) init_spell_check('textarea');

include('header.php');
include('editing/sidebar.php');

$t_editing = '<a href="editing.php?game='.$game.'" class=white>Editing</a> »';

if(!$page) $page = 'index';
if(!@include("editing/$page.php")) error_die('Page not found!');

footer(); ?>
