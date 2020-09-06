<?php
	if($action) {
		$id = $_GET['id']; if(substr_count($id,"'")) { errorlog('Hacking attempt in *watching.php*'); die; }
		if(!$id) { header('Location: cp.php'); die; }
		$sid = ','.$id.','; 
		if(!$uparray['mapwatch']) $uparray['mapwatch']=',';
		if(substr_count($uparray['mapwatch'],$sid)) $inarray = true; else $inarray = false; 
		if(substr_count($uparray['mapwatch'],',')>64) error_die('You can only watch up to 64 maps at a time.');
		$sql = ''; if($action=='addmap') $loc = 'maps.php?map='.$id; else $loc = 'cp.php';

		if($action=='addmap' && !$inarray) {
		        $sql = $uparray['mapwatch'].$id.',';
		        @mysql_query("UPDATE maps SET watching = watching + 1 WHERE map_id = '$id' LIMIT 1");
  		}

		if($action=='delmap' && $inarray) {
		        $sql = str_replace($sid,',',$uparray['mapwatch']);
			@mysql_query("UPDATE maps SET watching = watching - 1 WHERE map_id = '$id' LIMIT 1");
		}

		if($sql) @mysql_query("UPDATE users_profile SET mapwatch = '$sql' WHERE user_id = '$userdata[user_id]' LIMIT 1");

		if($return) $loc = 'forums.php?forum=2&topic='.$return;

		header('Location: '.$loc); die;
	}

	title('Watch List');
?>
One day this page will list all the maps and users you're watching. For now, use the menu on the left of the
main page and your control panel!
