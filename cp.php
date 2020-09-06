<?php
	if($_GET['mode']=='newcomment') require_once('scripts/spell_checker.php');
	include('config.php');
	if(!$userdata) { header('Location: login.php?linkto='.urlencode($_SERVER['REQUEST_URI'])); die; }

	if($_POST) { include('cp/cpactions.php'); die; }
	if(!isset($mode)) $mode = 'index'; $page = '';

	$pagetitle = 'User Control Panel';
	if($_GET['mode']=='newcomment') init_spell_check('textarea');
	include('header.php');
	include('cp/sidebar.php');
	tracker('Control Panel','');

	$t_cp = '<a href="cp.php" class=white>Control Panel</a> »';
	if(isset($_GET['action'])) $action = $_GET['action'];

	if(!include('cp/'.$mode.'.php')) error_die('Page not found');

	footer();
?>
