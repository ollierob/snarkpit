<?php title("$t_editing $garray[name] Editing Glossary",editing); ?>

<table width="100%" cellspacing=1 cellpadding=2><tr><td width="10%"></td><td width="90%"></td></tr>

<?php
$sql = mysql_query("SELECT * FROM glossary WHERE game = '$game' OR game = '' ORDER BY word");
while($array = mysql_fetch_array($sql)) {

	$word = ucfirst($array[word]); $firstletter = substr($word,0,1);
	if(!$$firstletter) { echo '<tr><td height=20 valign=bottom><a name="'.$firstletter.'"><font color="'.$colors['item'].'" size=5>'.$firstletter.'</font></a></td></tr>'; $$firstletter++; }
?>

<tr>
	<td align=right valign=top><b><?=$word?>:</b></td>
	<td valign=top><?=stripslashes($array['text'])?></p></td>
</tr>

<?php } ?></table></p>
