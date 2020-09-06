<table>
<?php
	if(!isset($smiley_cols)) $smiley_cols = 4; $rows = 5; $lim = $rows * $smiley_cols; $count = 0;
	$sql = mysql_query("SELECT DISTINCT(url) FROM smiles LIMIT $lim");
	while($sarray=mysql_fetch_array($sql)) {
		if($count % $smiley_cols) { } else echo '<tr>';
		echo '<td width=1>';
		echo '<img src="images/smiles/'.$sarray['url'].'" onClick="Emoticon(\'/images/smiles/'.$sarray['url'].'\')">'; if($count % 2) echo '<br>';
		echo '</td>';
		$count++;
	} 
?>
</table>

<a href="javascript:void(0)" onClick="popwin('popup.php?mode=smilies','1'); return false;" onMouseOver"window.status='Smiley guide'; return true" onMouseOut="window.status=''; return true"><b>show all</b></a>
