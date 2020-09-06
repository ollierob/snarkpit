<?php function entify($line) { global $game,$colors;

	//$line = htmlspecialchars($line); NO! DONT DO THIS!

	$line = str_replace($name,"<b>$name</b>",$line);
	$line = str_replace('[*]','</b></font><li style="padding-bottom:10px"><font size=2 color="'.$colors['text'].'"><b>',$line);
	$line = str_replace('[desc]','</b></font><div>',$line);
	$line = str_replace('[pr]','<font color="'.$colors['item'].'">',$line); $line = str_replace("[/pr]","</font>",$line);
	$line = str_replace('[pv]','<font color="'.$colors['info'].'">',$line); $line = str_replace("[/pv]","</font>",$line);
	$line = preg_replace('/\[v\](.*?)\[\/v\]/si','</b><div style="color:'.$colors['info'].';font-family:\'courier new\'">'.str_replace('<','&lt;','\1').'</div>',$line);

	$line = preg_replace('/\[title\](.*?)\[\/title\]/si','<h1>\1</h1>',$line);
	$line = preg_replace("#\[e\](.*?)\[/e\]#si","<a href=\"editing.php?game=$game&page=entity&name=\\1\" class=green>\\1</a>",$line);
	$line = preg_replace("#\[e:(.*?)\](.*?)\[/e\]#si","<a href=\"editing.php?game=$game&page=entity&name=\\2&type=\\1\" class=green>\\2</a>",$line);

	$line = str_replace("</h1>\r<br>\r<br>",'</h1>',$line);

	return $line;
}
?>
