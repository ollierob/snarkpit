<?php title('Gallery Of Horror','users'); tracker('Gallery Of Horror');

	if($userdata) echo '<p><a href="cp.php?mode=profile"><b>Click here</b></a> to upload a photo of yourself';
	if($userdata['user_level']>3) echo ', or <a href="users.php?page=gallery&'.(($p)?'p='.$p.'&':'').'check=true">check photos</a> (admin)';

	if(!$p = $_GET['p']) $p = 1;
	$numrows = mysql_result(mysql_query("SELECT COUNT(user_id) FROM users_profile WHERE photo != '' "),0);
	$ppp = 10; $start = ($p-1) * $ppp; $numpages = ceil($numrows/$ppp);

	if($numrows>$ppp) {
		$goto = '<p>Go to page [ ';
		if($p) { $gnum[$p-2]='skip'; $gnum[$p+2]='skip'; }
		for($i=$numpages-2;$i<=$numpages;$i++) $gnum[$i]=1; if(!$gnum[$i]) $gnum[$numpages-3]='skip';
		if($numpages>3) { $gnum[1]=1;$gnum[2]=1;$gnum[3]=1; } else { for($i=1;$i<=$numpages;$i++) $gnum[$i]=1; }
		if($p) { for($i=($p-1);$i<=($p+1);$i++) $gnum[$i]=1; }

		for($i=1;$i<=$numpages;$i++) {
			if($gnum[$i]==1) { $justskipped=''; if($p==$i) $goto.= '<b>'; else $goto.= '<a href="?page=gallery&p='.$i.'">'; $goto.= $i; if($p==$i) $goto.= '</b> '; else $goto.= '</a> '; }
			if($gnum[$i]=='skip'&&!$justskipped) { $justskipped=1; $goto.= '... '; }
		}

		$goto.= ']';
	} echo $goto;

	echo '<p>';
	$sql = mysql_query("SELECT user_id,username,photo FROM users_profile WHERE photo != '' LIMIT $start,$ppp");
	while($array = mysql_fetch_array($sql)) {
	        if($check=='true') {
	                if(!file_exists('userimages/photo'.$array['user_id'].'.'.$array['photo'])) @mysql_query("UPDATE users_profile SET photo = '' WHERE user_id = '$array[user_id]' LIMIT 1");
        	}
	        echo '<p><a href="users.php?name='.$array['username'].'"><img border=0 src="userimages/photo'.$array['user_id'].'.'.$array['photo'].'" alt="'.$array['user_id'].'"><br><b>'.$array[username].'</b></a></p>'."\n";
	}
	
	echo $goto.'<p>&nbsp;';

?>
