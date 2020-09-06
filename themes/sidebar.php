<?php
	include('standard.php');

	$config = array(
		'forumsidebar'=>true,
		'topicsidebar'=>true,
		'css'=>'standard',
		'forumjumpbox'=>false,
	);

	$img_path = 'sidebar';

	$images['forum_edit'] = '<span onmouseover="style.background=\'#0F3700\'" onmouseout="style.background=\'\'">&nbsp;edit post&nbsp;</span>';
	$images['forum_reply'] = ' reply ';
	$images['forum_quote'] = '&nbsp;quote ';
	$images['forum_ip'] = '&nbsp;ip address';

	$alttheme = 'standard';
	
	$simthemes = 'standard,sidebar,hires';

?>
