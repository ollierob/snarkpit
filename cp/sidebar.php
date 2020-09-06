<td valign="top" bgcolor="<?=$colors['sidebar']?>">

<div class="menu">

	<div class="menuboxtitle"><a href="cp.php" class=sidebar>control panel</a></div>
	<div class="menubox"><b><a href="cp.php?mode=profile" class=msidebar>edit profile</a></b><br>Add/change details about yourself</div>
	<div class="menubox"><b><a href="cp.php?mode=preferences" class=msidebar>edit preferences</a></b><br>Change how the website and forums work</div>
	<div class="menubox"><b><a href="cp.php?mode=inbox" class=msidebar>private messages <img src="images/gfx_pm.gif" border=0 align=texttop></a></b><br><?=$countpm?>/50 messages</div>
	<div class="menubox"><b><a href="cp.php?mode=feedback" class=msidebar>contact us</a></b><br>Send us links, quick tips, feedback, complaints etc.</div>
	<div class="menubox"><b><a href="cp.php?mode=homepage" class=msidebar>homepage</a></b><br>Manage your 'pit', if you have one</div>

<?php
	$sql = mysql_query("SELECT * FROM users_profile WHERE user_id = '$userdata[user_id]' LIMIT 1");
	$uparray = mysql_fetch_array($sql);

	if($mode=='index') { include('index/func_index.php'); map_watch(); }

?>

</div>

</td>
<td width="90%" valign=top height="100%">
<?php if($mode!='inbox' AND $mode!='compose') echo $pmbar; ?>

<div class="content">
