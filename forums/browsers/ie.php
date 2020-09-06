<?php
	$formatting = '
<select name="selectFont" onChange="javascript:FormatText(\'fontname\',selectFont.options[selectFont.selectedIndex].value);document.frmAddMessage.selectFont.options[0].selected = true">
	<option selected id="cat_white">Font</option>
	<option value="Verdana">Verdana</option>
	<option value="Courier New">Courier New</option>
	<option value="Symbol">Symbol</option>
	<option value="Wingdings">Wingdings</option>
</select>

<select name="selectSize" onChange="javascript:FormatText(\'fontsize\',selectSize.options[selectSize.selectedIndex].value);document.frmAddMessage.selectSize.options[0].selected = true">
	<option selected id="cat_white">Font Size</option>
	<option value=1>1</option>
	<option value=2>2</option>
	<option value=3>3</option>
	<option value=4>4</option>
	<option value=5>5</option>
	<option value=6>6</option>
</select>

<select name="selectColour" onChange="javascript:FormatText(\'forecolor\',selectColour.options[selectColour.selectedIndex].value);document.frmAddMessage.selectColour.options[0].selected = true">
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

<img src="themes/'.$images['moddir'].'/post_button_remove.gif" align="top" onClick="FormatText(\'removeformat\')" style="cursor: hand" title="Remove formatting"><img src="themes/'.$images['moddir'].'/post_button_undo.gif" align="top" onClick="FormatText(\'undo\')" style="cursor: hand" title="Undo"><img src="themes/'.$images['moddir'].'/post_button_redo.gif" align="top" onClick="FormatText(\'redo\')" style="cursor: hand" title="Redo"><br>
';

	$mouseover = 'onmouseover="style.background=\''.$colors['trmouseover2'].'\'" onmouseout="style.background=\''.$colors['bg'].'\'" ';

	$s_s = '<span style="border:1px solid '.$colors['msg_warning_border'].';margin:2px">';
	$s_e = '</span>';
	$s_format = $s_s.'<img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_bold.gif" align="absmiddle" title="Bold" onClick="FormatText(\'bold\', \'\')" style="cursor: hand;"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_italic.gif" align="absmiddle" title="Italic" onClick="FormatText(\'italic\', \'\')" style="cursor: hand;"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_underline.gif" align="absmiddle" title="Underline" onClick="FormatText(\'underline\', \'\')" style="cursor: hand;"><img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_strike.gif" title="Strikethrough" onclick="FormatText(\'strikethrough\',\'\')" align=absmiddle style="cursor:hand">'.$s_e;
	$s_align = $s_s.'<img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_left_just.gif" align="absmiddle" onClick="FormatText(\'JustifyLeft\', \'\')" style="cursor: hand;" title="Left Justify"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_centre.gif" align="absmiddle" border="0" title="Centre Justify" onClick="FormatText(\'JustifyCenter\', \'\')" style="cursor: hand;"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_right_just.gif" align="absmiddle" onClick="FormatText(\'JustifyRight\', \'\')" style="cursor: hand;" title="Right Justify"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_full_just.gif" align="absmiddle" onClick="FormatText(\'justifyfull\', \'\')" style="cursor: hand;" title="Full Justify">'.$s_e;
	$s_list = $s_s.'<img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_list.gif" align="absmiddle" border="0" title="Unordered List" onClick="FormatText(\'InsertUnorderedList\',\'\')" style="cursor: hand"><img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_olist.gif" align="absmiddle" border="0" title="Ordered List" onClick="FormatText(\'InsertOrderedList\',\'\')" style="cursor: hand"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_outdent.gif" align="absmiddle" onClick="FormatText(\'Outdent\',\'\')" style="cursor: hand" title="Outdent"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_indent.gif" align="absmiddle" border="0" title="Indent" onClick="FormatText(\'indent\',\'\')" style="cursor: hand">'.$s_e;
	$s_gubbins = $s_s.'<img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_hyperlink.gif" align="absmiddle" border="0" title="Add hyperlink" onClick="FormatText(\'createLink\')" style="cursor: hand"><img '.$mouseover.' src="themes/'.$images['moddir'].'/post_button_image.gif" align="absmiddle" border="0" title="Add Image" onClick="AddImage()" style="cursor: hand"><img '.$mouseover.'src="themes/'.$images['moddir'].'/post_button_symbol.gif" align="absmiddle" border="0" title="Insert symbol" onClick="symbol_table()" style="cursor:hand">'.$s_e;

	$formatting .= '<div style="height:30px;padding-top:4px">'.$s_format.$s_align.$s_list.$s_gubbins.' <a href="forums.php?forum='.$forum.(($mode)?'&mode='.$mode:'').(($topic)?'&topic='.$topic:'').'&mode='.((!$mode)?'reply':$mode).((isset($_GET['edit']))?'&edit='.$_GET['edit']:'').((isset($_GET['quote']))?'&quote='.$_GET['quote']:'').((isset($_GET['auth']))?'&auth='.$_GET['auth']:'').'&java=1#box">Having trouble posting?</a></div>';

	if(isset($_GET['quote'])||isset($_GET['edit'])) $iframe = 'forums.php?textbody=1&r='.$now_time.((isset($_GET['quote']))?'&quote='.$_GET['quote']:'').(($_GET['edit'])?'&edit='.$_GET['edit']:'');
	else $iframe = 'themes/'.$theme.'/message.htm';

	$box = '<!-- browser: IE -->
		<div id="results" class="results"></div>
		<iframe src="'.$iframe.'" id="message" style="width:550px;height:300px;border:1px solid '.$colors['item'].'" border=0 frameborder=0 style="width:550px;height:300px" onfocus="setObjToCheck(\'message\'); resetAction()"></iframe>
		<script language="javscript" type="text/javascript">document.getElementById(\'message\').contentWindow.document.designMode = \'on\'</script>
		<input type="hidden" name="message" value="">';

	$boxmouseover = ' onmouseover="if(!openedWindow) document.getElementById(\'message\').contentWindow.focus()"';
?>
