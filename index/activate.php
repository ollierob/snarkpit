<?php
	if(!$key = $_GET['key']) { header('Location: index.php'); die; }

	$sql = mysql_query("SELECT * FROM register WHERE actkey = '$key' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) error_die('Wrong/invalid activation code!');

	$uid = $array['userid'];

if($array['email']) @mysql_query("UPDATE users_profile SET user_email = '$array[email]' WHERE user_id = '$uid' LIMIT 1");
	else @mysql_query("UPDATE users SET activated = 1 WHERE user_id = '$uid' LIMIT 1");
	@mysql_query("DELETE FROM register WHERE userid = '$uid' LIMIT 1");

if($array['email']) echo 'Your e-mail address has been changed.';
	else echo 'Your account has been successfully activated! Please <b><a href="login.php">click here</a></b> to login';


?>
