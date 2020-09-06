<?php
	$formatting = '
<select id="FontName" onchange="select(this.id);">
	<option value="Font">Font</option>
	<option value="Verdana">Verdana</option>
	<option value="Courier New">Courier New</option>
</select>

<select id="FontSize" onchange="select(this.id);">
	<option value="Size">Size</option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>  
</select>

<select id="ForeColor" onchange="select(this.id);">
	<option selected id="cat_white">Font Colour</option>
	<option value=black>Black</option>
	<option value=gray>Dark Gray</option>
	<option value=silver>Light Gray</option>
	<option value=white>White</option>
	<option value=red>Red</option>
	<option value=orange>Orange</option>
	<option value=gold>Gold</option>
	<option value=yellow>Yellow</option>
	<option value=limegreen>Green</option>
	<option value=darkgreen>Dark Green</option>
	<option value=cyan>Cyan</option>
	<option value=blue>Blue</option>
	<option value="#AF38FE">Purple</option>
	<option value=magenta>Magenta</option>
	<option value=violet>Violet</option>
	<option value=lightblue>*Sarcasm</option>
</select>

<img src="themes/'.$images['moddir'].'/post_button_remove.gif" align="top" onClick="FormatText(\'removeformat\')" style="cursor: hand" title="Remove formatting"><img src="themes/'.$images['moddir'].'/post_button_undo.gif" align="top" onClick="FormatText(\'undo\')" style="cursor: hand" title="Undo"><img src="themes/'.$images['moddir'].'/post_button_redo.gif" align="top" onClick="FormatText(\'redo\')" style="cursor: hand" title="Redo">

<br>
<table><tr>
<td>
	<div class="box">
	<img src="themes/'.$images['moddir'].'/post_button_bold.gif" title="bold" onClick="FormatText(\'bold\',\'\')"><img src="themes/'.$images['moddir'].'/post_button_italic.gif" title="Italic" onClick="FormatText(\'italic\',\'\')"><img src="themes/'.$images['moddir'].'/post_button_underline.gif" title="Underline" onClick="FormatText(\'underline\',\'\')"><img src="themes/'.$images['moddir'].'/post_button_strike.gif" title="Strikethrough" onClick="FormatText(\'strikethrough\',\'\')">
	</div>
</td>
<td>
	<div class="box">
	<img src="themes/'.$images['moddir'].'/post_button_left_just.gif" title="justify left" onClick="FormatText(\'JustifyLeft\', \'\')" title="left justify"><img src="themes/'.$images['moddir'].'/post_button_centre.gif" title="Centre Justify" onClick="FormatText(\'JustifyCenter\', \'\')"><img src="themes/'.$images['moddir'].'/post_button_right_just.gif" onClick="FormatText(\'JustifyRight\', \'\')" title="Right Justify"><img src="themes/'.$images['moddir'].'/post_button_full_just.gif" onClick="FormatText(\'justifyfull\', \'\')" title="Full Justify">
	</div>
</td>
<td>
	<div class="box">
	<img src="themes/'.$images['moddir'].'/post_button_list.gif" align="absmiddle" border="0" title="Unordered List" onClick="FormatText(\'InsertUnorderedList\',\'\')"><img src="themes/'.$images['moddir'].'/post_button_olist.gif" align="absmiddle" border="0" title="Ordered List" onClick="FormatText(\'InsertOrderedList\',\'\')"><img src="themes/'.$images['moddir'].'/post_button_outdent.gif" align="absmiddle" onClick="FormatText(\'Outdent\',\'\')" title="Outdent"><img src="themes/'.$images['moddir'].'/post_button_indent.gif" align="absmiddle" border="0" title="Indent" onClick="FormatText(\'indent\',\'\')">
	</div>
</td>
<td>
	<div class="box">
	<img src="themes/'.$images['moddir'].'/post_button_image.gif" align="absmiddle" border="0" title="Add Image" onClick="AddImage()">
	</div>
</td>

<td><a href="forums.php?'.(($mode)?'mode='.$mode.'&':'').'forum='.$forum.(($topic)?'&topic='.$topic:'').'&mode='.((!$mode)?'reply':$mode).(($_GET['edit'])?'&edit='.$_GET['edit']:'').'&java=1#box">Can\'t see the box below?</a></td>
</tr></table>
';

	$box = '<!-- browser: mozilla -->
	<div id="results" class="results"></div>
	<iframe id="message" border=0 frameborder=0 style="width:550px;height:300px;border:1px solid '.$colors['item'].';padding:2px"></iframe>
	<textarea id="editbox" style="display:none;visibility:hidden" onfocus="setObjToCheck(\'message\'); resetAction()">';
	$box .= htmlspecialchars('<html><head><link rel="stylesheet" href="themes/'.$theme.'.css" type="text/css"></head><body topmargin=2 leftmargin=2>');

	if($mode=='editpost') {
		$edit = $_GET['edit'];
		$edittext = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$edit' LIMIT 1"),0);
		$box .= htmlspecialchars(str_replace('[addsig]','',str_replace("\r",'',$edittext)));
	}

	if($mode=='reply' && $_GET['quote']) { 
		$quote = $_GET['quote'];
		$edittext = mysql_result(mysql_query("SELECT post_text FROM posts_text WHERE post_id = '$quote' LIMIT 1"),0);
		if(substr($edittext,0,3)=='<P>') $edittext = substr($edittext,3);
		$edittext = str_replace('[addsig]','',str_replace("\r",'',$edittext));
		//if(substr($edittext,-4,4)=='</P>') $edittext = substr($edittext,0,(strlen($edittext)-4));
		//$box .= htmlspecialchars('[quote]'.$edittext.'[/quote]';
		$box .= htmlspecialchars('[quote]'.$edittext.'[/quote]');
	}

	$box .= '</textarea>';
	$box .= '
	<script language="javscript" type="text/javascript">
		// mozilla
		var xwin = document.getElementById(\'message\').contentWindow.document;
		var frameHTML = document.getElementById(\'editbox\').innerHTML;
		xwin.open(); xwin.write(frameHTML); xwin.close();
	</script>
	<input type="hidden" name="message" value=""><input type="hidden" name="browser" value="mozilla">
';

	//$boxmouseover = ' onmouseover="if(!openedWindow) document.getElementById(\'message\').contentWindow.focus()"';

?>
