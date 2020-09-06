<table>
<?php
	if(!$smiley_cols) $smiley_cols = 4;
	$sql = mysql_query("SELECT code,url FROM smiles LIMIT 20");
	while($sarray=mysql_fetch_array($sql)) {
		if($count % $smiley_cols) {} else echo '<tr>';
		echo '<td width=1>';
		echo '<img src="images/smiles/'.$sarray['url'].'" onclick="insertAtCaret(document.getElementById(\'message\'),\''.$sarray['code'].'\')">'; if($count % 2) echo '<br>';
		echo '</td>'.$trend;
		$count++;
	}
?>
</table>
<div align=center><a href="javascript:void(0)" onClick="popwin('popup.php?mode=smilies&bbsmilies=1','yes'); return false;" onMouseOver"=window.status='Smiley guide'; return true" onMouseOut="window.status=''; return true"><b>show all</b></a>


