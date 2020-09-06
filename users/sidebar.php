<td valign="top" bgcolor="<?=$colors['sidebar']?>">
<div class="menu">

	<div class="menuboxtitle"><a href="users.php" class=sidebar>people</a></b></div>
	<div class="menubox">
		<a href="users.php" class=msidebar>profile news</a><br>
		<a href="index.php?page=archive&date=<?=date("m/Y")?>&site=1" class=msidebar>profile news archive</a><br>
		<a href="?page=memberlist" class=msidebar>memberlist</a><br>
		<a href="?page=stats" class=msidebar>user stats</a><br>
		<a href="?page=gallery" class=msidebar>gallery of horror</a>
	</div>
	
	<p>

<?php if($name = $_GET['name'] OR $id = $_GET['id']) {

	$name = htmlspecialchars($name);
	if($name) { $query = "username = '$name'"; $userlink = 'name='.$name; }
	elseif($id) { $userlink = 'id='.$id; $query = "user_id = '$id'"; }

	if($id && $id=='you') {
		if($userdata) $id = $userdata['user_id']; 
		else { header('Location: users.php'); die; }
	}

	if($id && $name) $name = '';
	$sql = mysql_query("SELECT * FROM users WHERE $query LIMIT 1");
	if($uarray = mysql_fetch_array($sql)) { 
		$uname = stripslashes($name);
		if(!$name) { $uname = stripslashes($uarray[username]); $name = $uarray[username]; } //$name is slashed
		$psql = mysql_query("SELECT * FROM users_profile WHERE user_id = '$uarray[user_id]' LIMIT 1");
		$uparray = mysql_fetch_array($psql);

	list($hideemail,$showrating) = split(',',$uparray['hidestuff']);
	if($showrating!=2 && $userdata['user_id']!=$uparray['user_id'] && $userdata && $uparray['maps']>0) {
		echo '<table width="100%"><tr><td><form action="users.php" method="post" name="rateuser" onsubmit="javascript:if(rateuser.rate.value == \'\') return false;">'."\n";
		$sql = mysql_query("SELECT * FROM users_rating WHERE to_id = '$uparray[user_id]' AND from_id = '$userdata[user_id]' LIMIT 1");
		if($rarray = mysql_fetch_array($sql)) { $selectme = 'select'.$rarray[rating]; $$selectme=' SELECTED'; }
		echo '<select name="rate"><option value="" id="cat_white">rate user:</option><option value="5"'.$select5.'>5 (best)</option><option value="4"'.$select4.'>4<option value="3"'.$select3.'>3<option value="2"'.$select2.'>2</option><option value="1"'.$select1.'>1 (worst)</option>';
		echo '</select><input type=submit name=submit value="rate" class=submit2><input type="hidden" name="id" value="'.$uarray[user_id].'"></form></td></tr></table></p>';
	}
	
	if($uparray['avatar']) echo '<table width="130" cellpadding=2 cellspacing=0><tr><td align=center><img src="userimages/avatar'.$uarray['user_id'].'.'.$uparray['avatar'].'"></td></tr></table></p>';
?>
<font color="<?=$colors['medtext']?>" style="font-size:8pt">
	<b><?=$uparray['posts']?></b> <?=(($uparray['posts']>0)?'<a href="forums.php?mode=byuser&id='.$uarray['user_id'].'" class=msidebar>':'<a>')?>forum posts</a><br>
	+ <b><?=$uparray['maps']?></b> <?=(($uparray['maps']>0)?'<a href="#maps" class=msidebar>':'<a>')?>maps</a><br>
	+ <b><?=$tutorials = mysql_result(mysql_query("SELECT count(id) FROM articles WHERE user_id = '$uarray[user_id]' AND section='editing'"),0)?></b> <?=(($tutorials>0)?'<a href="editing.php?page=tutorials&byuser='.$uarray['user_id'].'" class=msidebar>':'<a>')?>tutorials</a><br>
	+ <b><?=$uparray['files']?></b> files<br>
	+ <b><?=$uparray['comments']?></b> comments<br>
	× <?=userrating($uparray['user_id'])?><br>
	» <b><?=$uparray['snarkpoints']?></b> <a href="index.php?page=faq#snarkmarks" class=msidebar>SnarkMarks</a><br>
<?
		$level = 'snarkatozoa';
		if($uparray['snarkpoints']>1) $level = 'snark zygote';
		if($uparray['snarkpoints']>10) $level = 'snark foetus';
		if($uparray['snarkpoints']>50) $level = 'neonatal snark';
		if($uparray['snarkpoints']>100) $level = 'snark spawn';
		if($uparray['snarkpoints']>250) $level = 'mini-snark';
		if($uparray['snarkpoints']>500) $level = 'scabby old HL1 snark';
		if($uparray['snarkpoints']>1000) $level = 'rabid snark';		
		if($uparray['snarkpoints']>2000) $level = 'spinkee HD pack snark';
		if($uparray['snarkpoints']>5000) $level = 'missing HL2 snark';
		if($uparray['snarkpoints']>10000) $level = 'mystical HL3 snark?';

		if($uparray['snarkpoints']==666) $level = '<font color="'.$colors['no'].'">omfg teh dev1l snark!!1</font>';
		echo "($level)";

		echo '<p>Other profiles:<br>';
		if($uparray['user_id']>1) echo '<a href="?id='.($uparray['user_id']-1).'">«prev</a> | ';
		echo '<a href="?id=random&'.($now_time+2).'">random</a> | <a href="?id='.($uparray['user_id']+1).'">next»</a>';

	} //if user exists
} ?>

</div>

</td>

<td width="95%" valign=top height="100%"><?=$pmbar?>
<div class="content">
