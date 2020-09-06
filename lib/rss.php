<?php
	$header = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>SnarkPit News</title>
    <link>http://www.snarkpit.net/</link>
    <description>Maps and mapping for Half-Life and Half-Life 2</description>
    <dc:language>en-GB</dc:language>
    <dc:creator>leperous@snarkpit.net</dc:creator>
    <dc:rights>Copyright 2005</dc:rights>
    <dc:date>'.date('y-m-d H:i:s').'</dc:date>
';

	$footer = '	</channel>
</rss>';

	$item = '';

	$sql = mysql_query("SELECT n.id,n.subject,n.date,n.text,n.plan,u.username FROM news n, users u WHERE u.user_id = n.user_id ORDER BY n.id DESC LIMIT 20");
	while($array = mysql_fetch_array($sql)) {
	                                        	
		$text = stripslashes(htmlspecialchars(strip_tags($array['text'])));

		$username = $array['username'];
		$item .= '
<item>
	<title>'.(($array['plan']==0)?'Site News: ':'').$array['subject'].'</title>
	<link>http://www.snarkpit.net/users.php?name='.$username.'</link>
	<description>'.$text.'</description>
	<dc:date>'.date("Y-m-d",$array['date']).'</dc:date>
	<dc:id>'.$array['id'].'</dc:id>
</item>

';

	}

	$text = $header.$item.$footer;
	if($item) {
		$handle = fopen('rss.xml','w');
		fwrite($handle,$text);
		fclose($handle);
	}

?>
