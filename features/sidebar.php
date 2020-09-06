<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">

	<div class="menuboxtitle"><a href="features.php" class=sidebar>features</a></div>
	<div class="menubox"><?php 
		$sql = mysql_query("SELECT id,name FROM games WHERE reviews > 0");
		while($array = mysql_fetch_array($sql)) echo '<a href="features.php?page=reviews&game='.$array['id'].'" class="msidebar">'.stripslashes($array['name']).' map reviews</a><br />';
	?>
	<a href="features.php?page=articles" class="msidebar">articles &amp; interviews</a>
	</div>

</div>

</td>
<td width="95%" valign=top height="100%"><?=$pmbar?>
<div class="content">
