<?php
include('config.php');

if(isset($_POST['submit']) && $userdata && $_POST['id'] && $_POST['rate']) {
	$id = $_POST['id'];
	if($_POST['rate']=='' || $_POST['rate']>5 || $_POST['rate']<1) { header('Location: users.php?id='.$id); die; }
	$nummaps = mysql_result(mysql_query("SELECT count(*) AS total FROM maps WHERE user_id = '$id'"),0);
	if(!$nummaps) { header('Location: users.php?id='.$id); die; }

	$alreadyrated = mysql_result(mysql_query("SELECT rating FROM users_rating WHERE from_id = '$userdata[user_id]' AND to_id = '$id' LIMIT 1"),0);
	if(!$alreadyrated) @mysql_query("INSERT INTO users_rating (from_id,to_id,rating) VALUES ('$userdata[user_id]','$id','$_POST[rate]')");
		else @mysql_query("UPDATE users_rating SET rating = '$_POST[rate]' WHERE from_id = '$userdata[user_id]' AND to_id = '$id' LIMIT 1");

	$sql = mysql_query("SELECT rating FROM users_rating WHERE to_id = '$id'"); $numrates=''; $totalrate='';
	while($a = mysql_fetch_array($sql)) { $numrates++; $totalrate+=$a[rating]; } $newrating = floor(10*$totalrate/$numrates)/10;
	@mysql_query("UPDATE users_profile SET user_rating = '$newrating' WHERE user_id = '$id' LIMIT 1");
	calcsnarkpoints($id);

	header('Location: users.php?id='.$id); die;
}

if(isset($_GET['name'])) $name = $_GET['name']; else $name = '';
if(isset($_GET['id'])) $id = $_GET['id']; else $id = '';
if($id=='random') {
        $randomness = mysql_fetch_array(mysql_query("SELECT u.username,u.user_id FROM users u, users_profile p WHERE (p.profile!='' OR p.maps>0) AND u.user_id = p.user_id ORDER BY RAND() LIMIT 1"));
        $id = $randomness['user_id'];
        $name = $randomness['username'];
}

if($name) $pagetitle = 'Profile for '.$name;
elseif($page=='memberlist') $pagetitle = 'Memberlist';
else $pagetitle = 'Users';

include('header.php');
include('users/sidebar.php');

if($name OR $id) $page = 'profile'; if(!$page) $page = 'index';
$t_users = '<a href="users.php" class="white">People</a> »';

if(!@include("users/$page.php")) error_die('Page not found!');

footer();
?>
