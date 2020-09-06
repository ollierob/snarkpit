</div>
</td></tr>
<tr><td colspan=2 valign="bottom">
<table width="100%" cellpadding=2 cellspacing=0 <?if($colors['sidebar']) echo ' bgcolor="'.$colors['sidebar'].'"';?>>
<tr>
	<td width="50%" valign=bottom height=30>
	<font size="1" color="<?=$colors['lighttext']?>"><a href="index.php?page=faq#snarkpower" class="sidebar">SnarkPower</a> made this page in <?php
		$mtime = explode(' ',microtime());
		$mtime = $mtime[1] + $mtime[0];
		$totaltime = ($mtime - $starttime);
		echo (floor($totaltime*1000)/1000);
?> seconds<br>All site content © <a href="index.php?page=about" class="sidebar">Leperous</a> &amp; respective owners, 2002-2005</font>
	</td>
	<td width="50%" align=center style="font-size:8pt;color:<?=$colors['headerbar']?>"><b>
	[ <a href="index.php?page=about" class="sidebar">about</a> :
	<a href="index.php?page=faq" class="sidebar">faq</a> :
	<a href="editing.php?page=files" class="sidebar">files</a> :
	<a href="irc://irc.quakenet.org/snarkpit" class="sidebar">IRC</a> :
	<a href="index.php?page=links" class="sidebar">links</a> :
	<a href="index.php?page=search" class="sidebar">search</a> ]
	<a href="#header" class="msidebar">top</a></b>
	</td>
</tr>
</table>

</td></tr></table>
<?php if(isset($t_span)) echo '</span>'; ?>

</body>
</html>
<?
	if($userdata) $table = 'uhits'; else $table = 'ghits';
	@mysql_query("UPDATE counter SET hits = hits+1 WHERE name = '$table' LIMIT 1");

die;
?>
