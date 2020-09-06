<?php
	title($t_users.' Memberlist','none');
	tracker('Member list');

	$letter = $_GET['letter'];

	if(strlen($letter)>1) $letter = substr($letter,0,1);
	if($letter) $letterlike = "AND username LIKE '$letter%'";
	if($letter=='$') {
		for($i=0;$i<10;$i++) { $numletter .="username LIKE '$i%' "; if($i!=9) $numletter.=" OR "; }
		$letterlike = "AND ($numletter)";
	}
	if($letter=='?') $letterlike = "AND (username LIKE '$%' OR username LIKE '-%' OR username LIKE '=%' OR username LIKE '[%' OR username LIKE '~%' OR username LIKE '|%')";
	if(!$letter) $letterlike = '';

	$sortby = $_GET['sort']; if(!$sort = $_GET['sort']) $sortby = 'user_id';
	if(!$start = $_GET['start']) $start=1;

	if($sort) $sortlink.='&sort='.$_GET['sort']; if($letter) $letlink.='&letter='.$letter;
	$reslink = $sortlink.$letlink;
?>
<table width="99%" cellpadding=3 style="font-size:8pt">

<tr><td></td><td style="font-size:9pt"><b><font color="<?=$colors['subtitle']?>">Search:</font></b>
	<?php if($letter) echo '<a href="?page=memberlist&letter='.$letter.'" class=white>Username begins with <b>'.$letter.'</b></a>, '; 
	$sortedby = 'sorted by <b>'.$sortby.'</b>';
	if(!$letter) echo ucfirst($sortedby); else echo $sortedby;
?>
<tr><td></td><td style="font-size:9pt">Search for name beginning with: 
<?php 	$alphabet = array('?','$','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	
	for($i=0;$i<29;$i++) {
		if($alphabet[$i]==$letter) echo '<b>'.$alphabet[$i].'</b> '; else
		echo '<a href="?page=memberlist'.$sortlink.'&letter='.$alphabet[$i].'">'.$alphabet[$i].'</a> '."\n";
	} ?> <b><a href="users.php?page=memberlist">all</a></b>
</td></tr>

<tr><td width="3%"></td><td width="97%" style="font-size:9pt"><?php

	$numusers = mysql_result(mysql_query("SELECT COUNT(user_id) FROM users WHERE user_id!=-1 $letterlike"),0);
	$upp = 25;
	$numpages = ceil($numusers/$upp); $gnum[1]=1; 

	if($numpages) echo 'Go to page [ ';

	if($start<$numpages) $next = '<a href="?page=memberlist&start='.($start+1).$reslink.'" class=white>next »</a> '; else $next='';
	if($start>1) $prev = '<a href="?page=memberlist&start='.($start-1).$reslink.'" class=white>« prev</a> '; else $prev='';

	echo $prev;

	if($numpages>1) {
		if($start) { $gnum[$start-2]='skip'; $gnum[$start+2]='skip'; }
		for($i=$numpages-2;$i<=$numpages;$i++) $gnum[$i]=1; if(!$gnum[$i]) $gnum[$numpages-3]='skip';
		if($numpages>3) { $gnum[1]=1;$gnum[2]=1;$gnum[3]=1; } else { for($i=1;$i<=$numpages;$i++) $gnum[$i]=1; }
		if($start) { for($i=($start-1);$i<=($start+1);$i++) $gnum[$i]=1; }
	}

	for($i=1;$i<=$numpages;$i++) { 
		if($gnum[$i]==1) { $justskipped=''; if($start==$i) echo '<b>'; else echo '<a href="users.php?page=memberlist'.$reslink.'&start='.$i.'">'; echo $i; if($start==$i) echo '</b> '; else echo '</a> '; } 
		if($gnum[$i]=='skip'&&!$justskipped) { $justskipped=1; echo '... '; } 
	}

	if($numpages) echo ' ] <font color="'.$colors['info'].'">'.$numusers.' users:</font>';

?></td>
</tr></table>

<table width="99%" cellpadding=3 cellspacing=0 style="font-size:8pt">
<tr>
	<td width=1><b><a href="?page=memberlist<? echo $start?"&start=$start":''; echo $letter?"&letter=$letter":''; ?>" class="white">id</a><img src="images/null.gif" width="25" align=right height=1></b></td>
	<td width="29%"><b><a href="?page=memberlist&<? echo $start?"start=$start&":''; echo $letter?"letter=$letter&":''; ?>sort=username" class=white>name</a></b></td>
	<td width=15></td><td width=14></td><td width=16></td>
	<td width="5%"><b><a href="?page=memberlist&<? echo $start?"start=$start&":''; echo $letter?"letter=$letter&":''; ?>sort=posts" class=white>posts</a></b></td>
	<td width="5%"><b><a href="?page=memberlist&<? echo $start?"start=$start&":''; echo $letter?"letter=$letter&":''; ?>sort=maps" class=white>maps</a></b></td>
	<td align=center width="5%"><a href="?page=memberlist&<? echo $start?"start=$start&":''; echo $letter?"letter=$letter&":''; ?>sort=snarkpoints" class=white><b>SM</b></a></td>
	<td width="10%"<nobr><b>registered<font color="<?=$colors['bg']?>">__</font></b></nobr></td>
	<td width="30%"><b>from</b></td>
	<td colspan=6><b>contact</b></td>

</tr>

<?php
	$pagelim = ($start-1)*$upp;
	if($sortby!='user_id' AND $sortby!='username') $sortby.=' DESC';

	$sql = "SELECT user_id,username,posts,maps,snarkpoints,user_regdate,location,hidestuff,icq,aim,yim,msnm,user_email,website,photo FROM users_profile WHERE user_id != -1 $letterlike"; 
	$sql.= "ORDER BY $sortby LIMIT $pagelim,$upp";
	
$sql = mysql_query($sql); $c = 0; $d = $pagelim;
while($array = mysql_fetch_array($sql)) { $c++; $d++;

		$ugame = mysql_result(mysql_query("SELECT game FROM users WHERE user_id = '$array[user_id]' LIMIT 1"),0);

		$username = stripslashes($array['username']);
		echo "\n".'<tr bgcolor="'.$colors['bg'].'" onmouseover="style.background=\''.$colors['trmouseover'].'\'" onmouseout="style.background=\''.$colors['bg'].'\'">';
		echo "\n".'	<td valign=top>'.(($_GET['sort'])?'<span style="float:left;position:relative;left:-40px;margin-right:-40px;color:'.$colors['medtext'].';">('.$d.')</span>':'').$array['user_id'].'</td>';
		
		echo '<td valign=top><b><a href="?name='.str_replace(' ','+',$username).'">'.$username.'</a></b></td>';

		echo '<td>'; if($array['photo']) echo '<a href="javascript:void(0)" onclick="popwin(\'screenshot.php?img=userimages/photo'.$array['user_id'].'.'.$array['photo'].'\')" onmouseover="window.status=\'Click here to view a photo of this user\';return true" onmouseout="window.status=\'\';return true"><img src="images/gfx_image.gif" border=0></a>';
		echo '</td><td>'; if($array['website']) echo '<a href="'.$array['website'].'" target="_blank"><img src="images/gfx_website.gif" border=0></a>';
		echo '</td><td><img src="themes/'.$images['moddir'].'/icon_'.$ugame.'.gif" align=right height=16></td>';
		echo '<td align=center>'.$array['posts'].'</td><td align=center>'.$array['maps'].'</td><td align=center>'.$array['snarkpoints'].'</td>';
		echo '<td>'.date("M jS 'y",$array['user_regdate']).'</td><td>'.$array['location']."</td>\n	";

		if($userdata) { $email='';
			list($email,$showrating) = split(',',$array[hidestuff]);
			if($email!=1) echo '<td><a href="mailto:'.$array[user_email].'" class=white><img src="images/email.gif" border=0></a></td>'; else echo '<td></td>';
			if($array['icq']) echo '<td><a href="http://wwp.icq.com/scripts/search.dll?to='.$array[icq].'"><img src="images/forum_icq.gif" border="0" align=texttop height=15></a></td>'; else echo '<td></td>';
			if($array['aim']) echo '<td><a href="aim:goim?screenname='.$array['aim'].'&message=Hi+'.$array['username'].'.+Are+you+there?"><img src="images/forum_aim.gif" border="0" align=texttop></a></td>'; else echo '<td></td>';
			if($array['yim']) echo '<td><a href="http://edit.yahoo.com/config/send_webmesg?.target='.$array['yim'].'&.src=pg"><img src="images/forum_yim.gif" border="0" align=texttop></a></td>'; else echo '<td></td>';
			if($array['msnm']) echo '<td><a href="http://members.msn.com/default.msnw?mem='.$array['msnm'].'" target="_blank"><img src="themes/'.$images['moddir'].'/forum_msn.gif" border="0" align=texttop></a></td>'; else echo '<td></td>';

		} else echo '<td colspan=6>'; echo '</td></tr>';
	} if(!$c) echo '<tr><td></td><td colspan=5><b><font color="'.$colors['no'].'">No users found</font></b></td></tr>';
?>
<tr><td colspan="7"><td width="2%"></td><td width=2%></td><td width=2%></td><td width=2%></td><td width=2%></td><td width=2%></td></tr>
<tr><td></td><td><?php if($prev OR $next) echo $prev; if($next&&$prev) echo ' : '; echo $next; ?>
</table>
</p>
<p>
<table width="100%" cellspacing=2 cellpadding=0><tr>
<td width="65%" valign=top>Note that e-mail addresses are only visible to other logged in users.</td>
<td width="35%">
<form action="index.php?page=query" method="post" name="searchform">
<input type="hidden" name="searchusers" value="on">
	<b><font color="<?=$colors['item']?>">user search:<br><input type="text" name="search" class="textinput" size=24>
	<input type="submit" value="[find]" class="submit2" onclick="if(!document.forms['searchform'].elements['search'].value) return false; if(document.forms['searchform'].elements['search'].value.length<3) { alert('Search string too short'); return false }">
</form>
</td>
</tr></table>
