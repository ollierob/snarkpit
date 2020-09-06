<?php $echo_start = '<html>
<head>
<link rel="stylesheet" href="themes/'.$config['css'].'.css" type="text/css">
</head>
<body style="background-image:none;padding:2px">

';

	if($edit=$_GET['edit']) { 
		$sql = mysql_query("SELECT * FROM posts_text WHERE post_id = '$edit' LIMIT 1");
		$array = mysql_fetch_array($sql);
		$echo = str_replace('[addsig]','',$array['post_text']);
	} 

	if($quote=$_GET['quote']) { 
		$text = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$quote' LIMIT 1"),0);
		$posterid = mysql_result(mysql_query("SELECT poster_id FROM posts WHERE post_id = '$quote' LIMIT 1"),0);
		$postername = mysql_result(mysql_query("SELECT username FROM users WHERE user_id = '$posterid' LIMIT 1"),0);
		if(substr($text,0,3)=='<P>') $text = substr($text,3);
		$text = str_replace('[addsig]','',$text);
		if(substr($text,-4,4)=='</P>') $text = substr($text,0,-4);
		//$echo = '<div class="quote"><div class="quotetitle">• quoting <b><a href="users.php?name='.$postername.'">'.$postername.'</a></b></div><div class="quotetext">';
		//$echo .= $text.'</div></div><p>&nbsp;'."\n";
		$echo = '[quote='.$postername.']'.$text.'[/quote]';
	}

	$echo = str_replace("\'","'",$echo); $echo = str_replace('\"','"',$echo);
	if($echo) {
		$echo = str_replace('<FONT face=tahoma color=red>Some images in this post have been automatically down-sized, click on them to view the full sized versions:</FONT>','',$echo);
		$echo = str_replace('<p></p>','',$echo);
	}

	$echo_end = '</body></html>'; 

if($_GET['textbody']) {
	echo $echo_start.$echo.$echo_end; 
} else { 
	$echo = str_replace("\r",'',str_replace("\n",'',$echo));
	$echo = preg_replace('#="(.*?)images/smiles/(.*?).gif"#si','="http://www.snarkpit.net/images/smiles/\\2.gif"',$echo);
	$echo = '<font size=2 face=verdana>'.$echo.'</font>';
	echo addslashes($echo);
}

?>
