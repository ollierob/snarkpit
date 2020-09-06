<?php
require_once('config.php');
require_once('index/func_index.php');

include('header.php');
include('index/sidebar.php');

if(!$page) $page = 'index';

if(!include("index/$page.php")) error_die('Page not found!');

footer(); 
?>
