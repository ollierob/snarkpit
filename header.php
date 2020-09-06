<?php $headerloaded = true; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Language" content="en-gb">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="keywords" content="The Snarkpit, snarkpit, Half-Life, Half-Life 2, halflife, map reviews, Worldcraft, Hammer, editing, maps, Call of Duty">
<meta name="description" content="The SnarkPit: Map reviews and editing info for Half-Life and Half-Life 2">
<title>The SnarkPit <?php if(isset($pagetitle)) echo ' - '.$pagetitle; ?></title>
<base target="_top">
<link rel="stylesheet" href="<?=$url_site?>themes/<?=$config['css']?>.css" type="text/css">
<link rel="alternate" type="application/rss+xml" title="SnarkPit News Feed" href="rss.xml">

<script language="javascript" type="text/javascript">
var openedWindow = false;
function popwin(x,varscroll) {
	if(varscroll=="") varscroll = "no";
	child = open(x,'popup','width=400,height=400,top=50,left=50,status=no,scrollbars='+varscroll+',resizable=yes');
	child.opener=this; child.focus();
	openedWindow = true;
}
<?=$extrajava?>

</script>

<?php if(!$userdata['javaoff']) echo '<script src="scripts/ypSlideOutMenusC.js" language="JavaScript" type="text/javascript"></script>'; ?>
</head>

<body id="bodyid" <?=$colors['body']?> style="margin:0"<?=$onload?>>
<?php if(isset($t_span)) echo '<SPAN STYLE="filter: '.$t_span.'(Color=#ff0000,Strength=2); width:100%; height:100%";>';?>
<?=((isset($css))?$css:'')?>

<?php if($userdata && !$userdata['javaoff']) { ?>

<div id="editingContainer" style="z-index:1"><div id="editingContent" class="dropdown" style="width:124px;height:112px;background-color:<?=$colors['bg2']?>">
	<a href="editing.php" class="<?=$colors['class_dropdown']?>">troubleshooting</a><br>
	<a href="editing.php?page=entity" class="<?=$colors['class_dropdown']?>">entities</a><br>
	<a href="editing.php?page=glossary" class="<?=$colors['class_dropdown']?>">glossary</a><br>
	<a href="index.php?page=links" class="<?=$colors['class_dropdown']?>">links</a><br>
	<a href="editing.php?page=tutorials" class="<?=$colors['class_dropdown']?>">tutorials</a><br>
	<a href="editing.php?page=files" class="<?=$colors['class_dropdown']?>">downloads</a>
</div></div>
<div id="featureContainer"><div id="featureContent" class="dropdown" style="width:180px;height:38px;background-color:<?=$colors['bg2']?>">
	<a href="features.php?page=reviews&amp;game=HL" class="<?=$colors['class_dropdown']?>">Half-Life map reviews</a><br>
	<a href="features.php?page=reviews&amp;game=HL2" class="<?=$colors['class_dropdown']?>">Half-Life 2 map reviews</a>
</div></div>
<div id="userContainer"><div id="userContent" class="dropdown" style="width:112px;height:112px;background-color:<?=$colors['bg2']?>">
	<a href="users.php" class="<?=$colors['class_dropdown']?>">profile news</a><br>
	<a href="index.php?page=archive&amp;site=1" class="<?=$colors['class_dropdown']?>">news archive</a><br>
	<a href="users.php?page=memberlist" class="<?=$colors['class_dropdown']?>">memberlist</a><br>
	<a href="users.php?page=gallery" class="<?=$colors['class_dropdown']?>">gallery</a><br>
	<a href="users.php?page=stats" class="<?=$colors['class_dropdown']?>">stats</a>
</div></div>
<div id="forumContainer" style="z-index:1"><div id="forumContent" class="dropdown" style="width:165px;height:114px;background-color:<?=$colors['bg2']?>">
<?php $sql = mysql_query("SELECT forum_id,forum_posts,forum_last_post_id,forum_name FROM forums ORDER BY cat,forum_id");
	while($harray = mysql_fetch_array($sql)) {
		echo '<a href="forums.php?forum='.$harray['forum_id'].'&amp;'.$harray['forum_posts'].'" class="forummenu" style="font-variant:normal">';
		$lastposttime = @mysql_result(mysql_query("SELECT post_time FROM posts WHERE post_id = '$harray[forum_last_post_id]' LIMIT 1"),0);
		if($lastposttime > $last_visit) $img = '<img src="themes/'.$images['snark'].'/smallsnark.gif" height=16 title="new posts" alt="new posts" border=0 style="position:relative;float:right;left:-5px">'; else $img = '<img src="themes/'.$images['snark'].'/smallsnark_old.gif" height=16 title="no new posts" alt="no new posts" border=0 style="position:relative;float:right;left:-5px;top:2px">';
		echo $img.$harray['forum_name'].'</a><br>'."\n";
	}
?>
</div></div>

<?php } ?>

<table width="100%" height="100%" cellspacing=0 cellpadding=0>
<tr><td colspan=3 height=100>
	<table width="100%" cellpadding=0 cellspacing=0><tr>
	<td width="550"><a href="<?=$url_site?>index.php" class="header2"><img src="<?=$url_site.(($images['header1'])?$images['header1']:'themes/'.$theme.'/header1.jpg')?>" height=100 width=550 border=0 alt="The SnarkPit" title="The SnarkPit"></a></td>
	<td width="40%" style="font-size:8pt"><?php if($page=='index') { ?>
	<div style="padding-left:4px;padding-right:8px;border:1px solid white">
	<b style="font-size:10pt"><a href="forums.php?forum=1&amp;topic=4525" class="sidebar">The SnarkPit HL2DM server</a></b>
	<br><span class="help">current rotation:</span>
	<?php if(!include('server.htm')) echo '<i>none?!</i>'; ?>
	<div style="margin:4px"><a href="http://www.4u-servers.co.uk" target="_blank"><img src="images/4uservers.gif" align="right" border="0" alt="server"></a><b style="font-size:10pt">195.20.108.12:27025</b>
	</div></div><? } ?>
	</td>
	<td width="120" align="right"><a name="header"><img src="<?=$url_site?><?=(($images['header2'])?$images['header2']:'header2.jpg')?>" style="position:absolute;right:0;top:0" width=120 height=100 border=0 alt=""></a></td>
	</tr></table>
</td></tr>

<tr><td colspan=3 class="headerbar"><div style="position:relative;height:23px">
	<div class="headerbar_left">&nbsp;</div>
	<div class="headerbar_right">
	<table width="100%" cellpadding=2 cellspacing=0 style="font-size:9pt;color:<?=$colors['headerbar']?>"<?=((isset($colors['topmenu']))?' bgcolor="'.$colors['topmenu'].'"':'')?>><tr>
	<td width=400 align=center>
		<a href="<?=$url_site?>editing.php<?php echo isset($game)? '?game='.$game: ''; ?>"<?=((!$userdata['javaoff'])?' onmouseover="ypSlideOutMenu.showMenu(\'editing\')" onmouseout="ypSlideOutMenu.hideMenu(\'editing\')"':'')?> class="<?=$colors['class_headerbar']?>"><b>map editing</b></a> ::
		<a href="<?=$url_site?>maps.php<?php echo isset($game)? '?game='.$game: ''; ?>" class="<?=$colors['class_headerbar']?>"><b>maps</b></a> ::
		<a href="<?=$url_site?>features.php"<?=((!$userdata['javaoff'])?' onmouseover="ypSlideOutMenu.showMenu(\'feature\')" onmouseout="ypSlideOutMenu.hideMenu(\'feature\')"':'')?> class="<?=$colors['class_headerbar']?>"><b>features</b></a> ::
		<a href="<?=$url_site?>forums.php"<?=((!$userdata['javaoff'])?' onmouseover="ypSlideOutMenu.showMenu(\'forum\')" onmouseout="ypSlideOutMenu.hideMenu(\'forum\')"':'')?> class="<?=$colors['class_headerbar']?>"><b>forums</b></a> ::
		<a href="<?=$url_site?>users.php"<?=((!$userdata['javaoff'])?' onmouseover="ypSlideOutMenu.showMenu(\'user\')" onmouseout="ypSlideOutMenu.hideMenu(\'user\')"':'')?> class="<?=$colors['class_headerbar']?>"><b>people</b></a><img src="images/null.gif" width=400 height=1 align=left alt="">
	</td><td width="80%" align=right>
	<?php if(isset($userdata['user_level']) && $userdata['user_level']>2) $admin_link = ', <b><a href="'.$url_site.'admin.php" class="'.$colors['class_headerbar'].'">admin</a></b>'; else $admin_link = '';
		if($userdata) {
			$countnewpm = 0; $pmlink = '';
			echo 'Logged in as <b><a href="users.php?name='.$userdata['username'].'" class="'.$colors['class_headerbar2'].'">'.$userdata['username'].'</a></b> ';
				$sql = mysql_query("SELECT SQL_CACHE status FROM messages WHERE to_id = '$userdata[user_id]'"); $countpm=0;
				while($array = mysql_fetch_array($sql)) { if($array['status']==1) $countnewpm++; $countpm++; }
				if($countpm>0) { $pmlink = '<a href="cp.php?mode=inbox" class="'.$colors['class_headerbar'].'">'.$countpm.' PM'; if($countpm!=1) $pmlink.='s'; $pmlink = '<b>'.$pmlink.'</b></a>, ';  }
			if($countnewpm) {
				//$pmbar = '<table bgcolor="'.$colors['highlight'].'" cellspacing=0 cellpadding=1><tr><td>
				$pmbar = '<span class="pmbar">&nbsp;<a href="cp.php?mode=inbox" class=sidebar><img src="images/gfx_pm.gif" align=absmiddle border=0 alt="pm"><b> you have '.$countnewpm.' new private message'.(($countnewpm==1)?'':'s').'</a>';
			} else $pmbar = '';

			if($userdata['dailypostlimit']) {
			        $dppostlim = $now_time - (24*3600);
				$dppostmade = @mysql_result(mysql_query("SELECT COUNT(post_id) FROM posts WHERE poster_id = '$userdata[user_id]' AND post_time > '$dppostlim'"),0);
			        $pmbar = (($pmbar)?'- ':'<span class="pmbar">&nbsp;').'<b><a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=postwarning\')" class="white">You have '.($userdata['dailypostlimit']-$dppostmade).' posts left today</a></b>';
			}

			if($pmbar) $pmbar.='&nbsp;</b></span>';

			echo '[<b><a href="cp.php" class="'.$colors['class_headerbar'].'">control panel</a></b>, '.$pmlink.'<b><a href="'.$url_site.'logout.php" class="'.$colors['class_headerbar'].'">logout</a></b>'.$admin_link.'] ';

		} else { echo 'Not logged in [<a href="'.$url_site.'login.php?linkto='.$_SERVER['PHP_SELF'].'?'.urlencode($_SERVER['QUERY_STRING']).'" class="'.$colors['class_headerbar'].'"><b>login</b></a>, <b><a href="'.$url_register.'" class="'.$colors['class_headerbar'].'">register</a></b>] '; }

		$uonline = @mysql_result(mysql_query("SELECT COUNT(user_id) FROM sessions"),0);
		if($uonline>0) echo '<a href="javascript:void(0)" onclick="popwin(\'popup.php?mode=whosonline\''.(($uonline>25)?',\'yes\'':'').')" onMouseOver="window.status=\'Whos online list (popup window)\'; return true" onMouseOut="window.status=\'\'" class="'.$colors['class_headerbar2'].'">';
		echo $uonline ? $uonline: 0; echo ' user'.(($uonline!=1)?'s':'').' online</a>';
		$mostonline = mysql_result(mysql_query("SELECT SQL_CACHE hits FROM counter WHERE name = 'maxonline' LIMIT 1"),0);
		if($uonline>$mostonline) { $date = date("M jS, Y"); @mysql_query("UPDATE counter SET hits = '$uonline', date = '$date' WHERE name='maxonline' LIMIT 1"); }
	?>
	</td></tr></table>
	</div>
	</div></td>
</tr>

<tr>
<!--start-->
