<?php tracker('Entities');

if($mod=$_GET['mod']) {

	$sql = mysql_query("SELECT fullname FROM mods WHERE name = '$mod' AND game = '$game' LIMIT 1");
	$array = mysql_fetch_array($sql); $modname = stripslashes($array['fullname']);

}

function entlink($name,$type) { global $game,$mod;
	if(substr($name,0,3)=='[s]') { $type = substr($name,1,1); $name = substr($name,3); }
	return '<a href="?page=entity&game='.$game.(($mod)?'&mod='.$mod:'').(($type)?'&type='.$type:'').'&name='.$name.'">'.$name.'</a>';
}

if($name=$_GET['name']) {
                        	
        $name = str_replace(' ','_',$name);

	if($mod) $xmod = ' » <a href="?page=entity&game='.$game.'&mod='.$mod.'" class=white>'.$modname.'</a>';
	title($t_editing.' <a href="?page=entity&game='.$game.'" class=white>'.$garray['name'].' Entities </a>'.$xmod.' » '.$name,'editing');
	$type = $_GET['type'];

	echo '<form name="entform" method="post" action="index.php?page=query">
		<input type="hidden" name="search" value="'.$name.'">
		<input type="hidden" name="searchtutorials" value="on">
		<input type="hidden" name="searchforumsubject" value="on"><input type="hidden" name="searchforummessage" value="on">
		<input type="hidden" name="searchgame" value="'.$game.'">
		<input type="hidden" name="forumselect" value="'.$forum_id.'">
	</form>';
	echo '<blockquote><b><a href="javascript:void(entform.submit())">Search tutorials/forums for more info on this entity</a></b></blockquote><p>';

	if(!include('lib/entify.php')) error_die('Couldn\'t get entity parsing library');

	$sql = mysql_query("SELECT * FROM entities WHERE `game` = '$game' AND `mod` = '$mod' AND `name` = '$name' AND `type` = '$type' LIMIT 1");
	if(!$array = mysql_fetch_array($sql)) echo 'No entity information found'; else {

		if($array['intro']) echo $array['intro'];
		if($array['properties']) echo '<p><b><font color="'.$colors['info'].'">Properties:</font></b><ul style="font-size:10px;line-height:1.5em;color:'.$colors['medtext'].'">'.entify($array['properties']).'</b></ul>';
		if($array['outputs']) echo '<p><b><font color="'.$colors['info'].'">Outputs:</font></b><ul style="font-size:10px;line-height:1.5em;color:'.$colors['medtext'].'">'.entify($array['outputs']).'</b></ul>';
		if($array['inputs']) echo '<p><b><font color="'.$colors['info'].'">Inputs:</font></b><ul style="font-size:10px;line-height:1.5em;color:'.$colors['medtext'].'">'.entify($array['inputs']).'</b></ul>';
		if($array['flags']) echo '<p><b><font color="'.$colors['info'].'">Flags:</font></b><ul style="font-size:10px;line-height:1.5em;color:'.$colors['medtext'].'">'.entify($array['flags']).'</b></ul>';
		if($array['notes']) echo '<p><b><font color="'.$colors['info'].'">Notes:</font></b><blockquote style="font-size:9pt;line-height:1.5em">'.entify($array['notes']).'</blockquote>';

		if($array['alsosee']) { echo '<b><font color="'.$colors['subtitle'].'">Also see:</font></b> ';
		        $count = substr_count($array['alsosee'],',')+1;
		        $i = 0; $block = $array['alsosee']; $e = array();
		        while($i<$count) {
		        	$x = ''; $y = '';
				list($e[$i+1],$e[$i]) = split(', ',$block);
				$block = str_replace($e[$i+1].', '.$e[$i],'',$block);
				$i = $i+2;
          		}
          		while(list($var,$val)=each($e)) { if($val) echo entlink(trim($val)).(($var!=$count)?', ':''); }
  		}
  		
  		if($array['usercontrib']) echo '<p><i>Entity info contributed by '.userdetails($array['usercontrib'],'white','return').'<p>';

	}

footer(); } //entity selected

title($t_editing.' '.$garray['name'].' Entities','editing');

	$c = 0; echo '<table width="100%" cellspacing=2 cellpadding=2>';

	$sql = mysql_query("SELECT name,intro,type FROM entities WHERE `game` = '$game' AND `mod` = '$mod' ORDER BY `type`, `name`");
	while($array = mysql_fetch_array($sql)) { $c++;

		if($array['type'] && !$typeblock[$array['type']]) {
		        $typeblock[$array['type']] = 1;
		        if($array['type']=='s') $type = 'Brush based ';
			echo '<tr><td colspan=2 height=40 valign=bottom><font size=4>'.$type.' entities:</font></td></tr>';
  		} elseif($c==1) echo '<tr><td colspan=2><font size=4>Point entities:</font></td></tr>';
  		
  		echo '<tr><td width="15%">'.entlink($array['name'],$array['type']).'</td>';
  		echo '<td width="85%" style="font-size:11px">'.$array['intro'].'</td></tr>';

	} if(!$c) echo '<tr><td colspan=2><b><font color="'.$colors['red'].'">No entities found</font></b></td></tr>';

	echo '</table>';

footer(); ?>
