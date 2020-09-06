<?php
	if(!function_exists('bbdecode')) include('func_parse.php');
	$formatting = '';
	
	function editparse($text) {
	        $text = str_replace('<BR>',"\n",str_replace('[addsig]','',$text));
	        $text = str_replace('<p>',"\n\n",$text);
	        $text = str_replace('<br>',"\n",$text);
	        $text = strip_tags(desmile(bbdecode($text)));
	        return $text;
	}

	if($quote = $_GET['quote']) {
		$msg = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$quote' LIMIT 1"),0);
		$msg = '[quote]'.editparse($msg).'[/quote]';
		$msg = str_replace("\n\n","\n",$msg);
		$msg = str_replace("\n[/quote]",'[/quote]',$msg);
		$msg = str_replace("\r[/quote]",'[/quote]',$msg);
	}

	if($edit = $_GET['edit']) {
		$msg = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$edit' LIMIT 1"),0);
		$msg = editparse($msg);
	}

	$box = '<a name="box"></a><div id="results" class="results"></div><textarea id="message" name="message" style="height:300px;width:550px" onfocus="setObjToCheck(\'message\'); resetAction()" onselect="storeCaret(this)" onclick="storeCaret(this)" onkeyup="storeCaret(this)">'.$msg.'</textarea><input type="hidden" name="java" value="no">'; 
	$boxmouseover = '';

?>
