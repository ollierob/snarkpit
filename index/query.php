<?php
	while(list($var,$val)=each($_POST)) $$var = $val;
	if(!$search) { header('Location: index.php'); die; }
	if(strlen($search)<4) { title("<a href=\"index.php?page=search\" class=white>Search:</a> Results",'none'); echo 'Search string too short, please try again'; footer(); }

	if($sql = @mysql_result(mysql_query("SELECT hits FROM search WHERE phrase = '$search' LIMIT 1"),0)) @mysql_query("UPDATE search SET hits = hits + 1 WHERE phrase = '$search' LIMIT 1");
		else @mysql_query("INSERT INTO search (phrase,hits) VALUES ('$search','1')");

	$search = ' '.trim($search);
	$search = stripslashes(ereg_replace (' +', ' ', $search));
	$words = array('$and$',' if ',' was ',' a ',' an ',' in ',' with ',' i ',' for ');
	$search = str_replace('  ',' ',str_replace($words,' ',$search));
	$terms = split(' ',str_replace('"','',$search)); $search = trim($search); $numterms = sizeof($terms);	
	while($terms[0]=='') { if($terms[0]=='') {
		$numterms--;
		for($i=0;$i<$numterms;$i++) $terms[$i] = $terms[($i+1)];
	} }

	if($sort=='id') $sort = ''; if($sort && $sort!='date ASC' && $sort!='date DESC') error_die('Invalid sort choice');
	if(!$which) $which = 'all';
	if($which=='google') { header('Location: http://www.google.com/search?q='.$search.'+site:www.snarkpit.net&l=en'); die; }

	if($searchgame) {
		if(!$searchgamename = mysql_result(mysql_query("SELECT name FROM games WHERE id = '$searchgame' LIMIT 1"),0)) error_die('Invalid game selected');
		$sqlgame = "AND game = '$searchgame'";
	}

	$boolean = ' IN BOOLEAN MODE';
	$minscore = round(3*$numterms/8); if(substr_count($search,'"')>1) $minscore=0;
	$highlight = str_replace(' ',',',str_replace('"','',$search));
	if(!$start) $start = 0;
	$ctotal = 0; $lim = 15;

function likeall($wut,$bool) { global $numterms,$terms; if(!$bool) $bool = 'AND';
	for($i=0;$i<$numterms;$i++) { $likeall.="`$wut` LIKE '%$terms[$i]%'"; if($i!=($numterms-1)) $likeall.=" $bool "; }
	return $likeall;
}

function invalid($wut,$huh) { 
	$return = '<tr><td></td><td><b><font color="'.$colors['no'].'">no '.$wut.' found';
	if($huh) $return.='<br>'.$huh;
	$return.='</b></font></td></tr>'; 
	return $return;
}


	title('<a href="index.php?page=search" class=white>Search:</a> Results','none');
	echo 'Searching for <b>'.$search.'</b>';
	if($numterms>5) { $numterms=5; echo '- cut to 5 words for some searches'; }
	if($searchgame) echo '<br>Search limited to <b>'.$searchgamename.'</b>';
	if($section) echo ', in <b>'.$section.'</b> related pages';
	if($sort) { if($searchgame) echo ', sorted '; else echo 'Sorted '; echo 'by <b>'.$sort.'</b>'; }
	echo '<p><table width="90%" cellspacing=1 cellpadding=1 style="font-size:8pt">';


if($searchglossary=='on') { $cg=''; ?>
	<tr><td colspan=2><div class="subtitle"><a href="?game=<?=$game?>&page=glossary"><font size=2>Glossary</font></a></td></tr>
	<?php
	for($i=0;$i<$numterms;$i++) { if($terms[$i]) { $cg='';
		echo "\n".'<tr><td align=right valign=top><font color="'.$colors['medtext'].'"><b>'.$terms[$i].':</b></td><td>';
		$gsql = mysql_query("SELECT DISTINCT * FROM glossary WHERE word LIKE '%$terms[$i]%' AND (game = '$game' OR game = '') LIMIT 1");
		if($garray = mysql_fetch_array($gsql)) { echo $garray['text'].'<br>'; $cg++; $glosdist.=" AND word!='$garray[word]'"; }
		else {	$gsql = mysql_query("SELECT DISTINCT * FROM glossary WHERE text LIKE '%$terms[$i]%' AND (game = '$game' OR game = '') $glosdist LIMIT 2");
			while($garray = mysql_fetch_array($gsql)) { echo '<b>'.ucfirst($garray['word']).'</b>: '.$garray[text].'<br>'; $cg++; }
		} if(!$cg) echo '<b><font color="'.$colors['no'].'">no words found</b>';
	} }
	echo '<tr><td height=10></td></tr>';
	$ctotal+=$cg;
}

if($searchentities=='on') {

	echo '<tr><td colspan=2><div class="subtitle"><a href="?page=entity&game='.$game.'" class=white>Entity Search</a></div></td></tr>';
	echo "\n".'<tr><td align=right valign=top><font color="'.$colors['medtext'].'"><b>entities:</b></td><td>';

	$e = 0;
	$query = "SELECT type,name,intro FROM entities WHERE game = '$game' AND (".likeall('name','OR').") OR (".likeall('intro','OR').") LIMIT $lim";
	$sql = mysql_query($query);

	while($array = mysql_fetch_array($sql)) {
		$e++;
		echo "\n".'	<a href="editing.php?page=entity&amp;game='.$game.'&amp;type='.$array['type'].'&amp;name='.$array['name'].'"><b>'.$array['name'].'</b></a>: <span style="color:'.$colors['lighttext'].'">'.$array['intro'].'</span><br>';
	}

	if(!$e) echo '<font color="'.$colors['no'].'"><b>no entities found:</b></font> <i>search doesn\'t include mod entities, so try browsing manually</i>'; echo '</td></tr>';
	echo '<tr><td height=10> </td></tr>';
}

if($searcharticles=='on') { $ca=0;
	echo '<tr><td colspan=2><div class="subtitle">Article Search:</div></td></tr>';

	$query = "SELECT id,title,description,user_id, MATCH(title,description) AGAINST ('$search'$boolean) AS score FROM articles
		WHERE MATCH(title,description) AGAINST ('$search'$boolean) AND section = 'articles' $sqlgame ORDER BY score DESC LIMIT $lim";
	$sql = mysql_query($query); while($array = mysql_fetch_array($sql)) {
		$resarray[$array['id']] = $array[score]*2;
		if($array['id']>$maxid) $maxid = $array['id'];
		$aarray[$array[id]][title] = $array[title]; $aarray[$array[id]][description] = $array[description]; $aarray[$array[id]][user_id] = $array[user_id];
	}

	$sql = mysql_query($query);
	while($array = mysql_fetch_array($sql)) { $ca++;
		echo '<tr><td';
		if($ca==1) echo ' valign=top align=right><font color="'.$colors['medtext'].'"><b>all:</b></font></td';
		echo '><td><font size=2><b><a href="features.php?page=articles&id='.$array['id'].'&highlight='.$highlight.'">'.$array[title].'</a></b>';
		echo '</font> by '.userdetails($array['user_id'],'white','return','');
		echo '<br>'.$array['description'].'</td></tr>'; 
	} if($ca==0) echo invalid('articles','not all articles are searched due to the way they are stored, try using <a href="http://www.google.com" target="_blank">Google</a>');

	if($ca==$lim) echo '<tr><td></td><td>limit of '.$lim.' articles found, please narrow your search</td></tr>';
	echo '<tr><td height=10> </td></tr>';
	$ctotal += $ca;
}

if($searchtutorials=='on') { $ct=0;
	echo '<tr><td colspan=2><div class="subtitle">Tutorial Search:</div></td></tr>';
	if($searchgame) $sqlgame = "AND a.game = '$searchgame'";

	if($section && $game) {
		if(include('lib/forums_'.$game.'.php')) {
			$sections = $helpsections[$section]; $c = 0; $sectionsql = '';
			if($sections) {
				while(list($var,$val)=each($sections)) { if($c) $sectionsql.= ' OR '; $sectionsql.= "a.type = '$val'"; $c++;  }
 				if($sectionsql) $sectionsql = 'AND ('.$sectionsql.')';
			}
		}
	}

	//leave a. parts in here, because $sqlgame etc. use it to be compatible with articles_text search
	$query = "SELECT a.id,a.title,a.description,a.user_id, MATCH(a.title,a.description) AGAINST ('$search'$boolean) AS score FROM articles a
		WHERE MATCH(a.title,a.description) AGAINST ('$search'$boolean) AND a.section = 'editing' $sqlgame $sectionsql LIMIT $lim";
	$sql = mysql_query($query); while($array = mysql_fetch_array($sql)) {
		$resarray[$array['id']] = $array['score']*2;
		if($array['id']>$maxid) $maxid = $array['id'];
		$aarray[$array['id']]['title'] = $array['title']; $aarray[$array['id']]['description'] = $array['description'];
		$aarray[$array['id']]['user_id'] = $array['user_id'];
	}

	$query = "SELECT a.id,a.title,a.description,a.user_id, MATCH(t.text) AGAINST ('$search'$boolean) AS score FROM articles a, articles_text t
		WHERE MATCH(t.text) AGAINST ('$search'$boolean)	AND a.id = t.id $sqlgame $sectionsql LIMIT $lim";
	$sql = mysql_query($query); while($array = mysql_fetch_array($sql)) { $c++;
		$resarray[$array['id']] = $resarray[$array['id']] + $array['score'];
		if($array[id]>$maxid) $maxid = $array['id'];
		$aarray[$array['id']]['title'] = $array['title']; $aarray[$array['id']]['description'] = $array['description'];
		$aarray[$array['id']]['user_id'] = $array['user_id'];
	}

	if($resarray) { arsort($resarray); reset($resarray); }
	while(list($key,$val)=@each($resarray)) { if($ct<$lim && $resarray[$key]>$minscore) { $ct++;
		echo '<tr><td';
		if($ct==1) echo ' valign=top align=right><font color='.$colors['medtext'].'><b>all:</b></font></td';
		echo '><td><font size=2><b><a href="editing.php?page=tutorials&id='.$key.'&highlight='.$highlight.'">'.$aarray[$key][title].'</a></b>';
		echo '</font> by '.userdetails($aarray[$key]['user_id'],'white','return','');
		echo ' <font color="'.$colors['medtext'].'">(relevance:'.$resarray[$key].')</font>';
		echo '<br>'.$aarray[$key]['description'].'</td></tr>';
	} } if($ct==0) echo invalid('tutorials','');

	if($ct==$lim) echo '<tr><td></td><td>limit of '.$lim.' tutorials found, please narrow your search</td></tr>';
	echo '<tr><td height=10> </td></tr>';
	$ctotal += $ct;
	unset($resarray);
}

if($searchnews=='on' || $searchplan=='on') {
	$wheresql = ''; $sortsql = ''; $limsql = ' LIMIT '.$lim;
	echo '<tr><td colspan=2><div class="subtitle">News Search:</div>';
	if($searchnews=='on' && $searchplan=='on') {} else {
		echo ' <font color="'.$colors['medtext'].'">(searching ';
		if($searchnews=='on') { echo 'site'; $wheresql = 'AND plan = 0'; } else { echo 'user'; $wheresql = 'AND (plan = 1 OR plan = 2)'; }
		echo ' news)</font>';
	} echo '</td></tr>';

	$query = 'SELECT id,user_id,subject,date,plan FROM news WHERE ';
	if($which=='all') $query.= '(('.likeall('subject','').') OR ('.likeall('text','').')) ';
	if($which=='any') $query.= '(('.likeall('subject','OR').') OR ('.likeall('text','OR').')) ';
	if($which=='phrase') $query.= "(subject LIKE '%$search%' OR text LIKE '%$search%') ";
	$query.= $wheresql.$sortsql.$limsql;
	//echo $query;

	$sql = mysql_query($query);
	while($array = mysql_fetch_array($sql)) {
		if($array[plan]>0) $plan = 1; else $plan = 0;
		echo '<tr><td></td><td><b><a href="index.php?page=archive&date='.date("m/Y",$array[date]).'&site='.$plan.'#news'.$array[id].'">'.stripslashes($array[subject]).'</a></b>';

	}
}

if($searchreviews=='on') { $cr=0;
	echo '<tr><td colspan=2><div class="subtitle">Review Search:</div></td></tr>';

	if($which=='all') { $query = 'SELECT DISTINCT r.review_id, r.* FROM reviews r, reviews_text t WHERE '; if($searchgame) $query.= "r.game = '$searchgame' AND";
		$query.= "((".likeall('r.mapname','').") OR (".likeall('r.verdict','').") OR (".likeall('t.text','')." AND t.review_id = r.review_id))"; }
	//if($which=='phrase') $query = "SELECT DISTINCT a.id, a.* FROM articles a, articles_text t WHERE a.section = 'editing' AND (a.title LIKE '%$search%' OR a.description LIKE '%$search%' OR (t.text LIKE '%$search%' AND t.id = a.id))";
	if($sort) $query .= ' ORDER BY '.$sort; else $query .= ' ORDER BY review_id DESC';
	$query.= ' LIMIT '.$lim; $sql = mysql_query($query);

	while($array = mysql_fetch_array($sql)) { $cr++;
		echo '<tr><td></td><td><b><a href="features.php?page=reviews&id='.$array[review_id].'">'.stripslashes($array[mapname]).'</a></b>';
		echo ' by '.userdetails($array['user_id'],'white','return','');
		echo "</td></tr>\n";
	} if(!$cr) echo invalid('reviews','');

	if($cr==$lim) echo '<tr><td></td><td>limit of '.$lim.' reviews found, please narrow your search</td></tr>';
	echo '<tr><td height=10> </td></tr>';
	$ctotal += $cr;
}

if($searchforumsubject=='on' OR $searchforummessage=='on') { $cf=0;
	echo '<tr><td colspan=2><div class="subtitle">Forum Search:</div></b>';
	if($forumselect!='all') echo ' <font color="'.$colors['medtext'].'">(searching <b>'.mysql_result(mysql_query("SELECT forum_name FROM forums WHERE forum_id = '$forumselect' LIMIT 1"),0).'</b> board)</font>';

	$resarray = array();

	if($searchforumsubject=='on') {
		if($forumselect!='all') $sqlforum = "AND forum_id = '$forumselect'";
		$query = "SELECT DISTINCT(topic_id),title,description,topic_time,forum_id,answered, MATCH(title,description) AGAINST ('$search'$boolean) AS score FROM topics
			WHERE MATCH(title,description) AGAINST ('$search'$boolean) $sqlforum LIMIT $lim";
		$sql = mysql_query($query); while($array = mysql_fetch_array($sql)) {
			$resarray[$array[topic_id]] = $array[score]*3;
			$farray[$array['topic_id']]['title'] = stripslashes($array['title']); $farray[$array['topic_id']]['description'] = stripslashes($array['description']);
			$farray[$array['topic_id']]['topic_time'] = $array['topic_time']; $farray[$array['topic_id']]['forum_id'] = $array['forum_id']; $farray[$array['topic_id']]['answered'] = $array['answered'];
		}
	}

	if($searchforummessage=='on') {
		if($forumselect!='all') $sqlforum = "AND p.forum_id = '$forumselect' AND t.forum_id = '$forumselect'";
		$query = "SELECT DISTINCT(t.topic_id),t.title,t.description,t.topic_time,t.forum_id,t.answered,pt.post_id, MATCH(pt.post_text) AGAINST ('$search'$boolean) AS score FROM posts_text pt, posts p, topics t
			WHERE MATCH(pt.post_text) AGAINST ('$search'$boolean) AND p.post_id = pt.post_id AND t.topic_id = p.topic_id $sqlforum LIMIT $lim";
		$sql = mysql_query($query); while($array = mysql_fetch_array($sql)) {
			$resarray[$array['topic_id']] = $resarray[$array['topic_id']] + $array['score'];
			$farray[$array['topic_id']]['title'] = stripslashes($array['title']); $farray[$array['topic_id']]['description'] = stripslashes($array['description']);
			$farray[$array['topic_id']]['topic_time'] = $array['topic_time']; $farray[$array['topic_id']]['forum_id'] = $array['forum_id']; $farray[$array['topic_id']]['post_id'] = $array['post_id']; $farray[$array[topic_id]][answered] = $array[answered];
		}
	}

	if($resarray) { arsort($resarray); reset($resarray); }
	while(list($key,$val)=each($resarray)) { if($cf<$lim && $resarray[$key]>$minscore) { $cf++;
		echo '<tr><td align=right><a href="forums.php?forum='.$farray[$key][forum_id].'&topic='.$key.'&findpost=lastpost#endofpage"><img src="themes/'.$theme.'/';
			if($farray[$key]['topic_time']>$last_visit) {
				if($farray[$key]['answered']=='p') $img = 'topic_new_poll.gif';
				elseif($farray[$key]=='n') $img = 'topic_new_help.gif';
				else $img = 'topic_new.gif';
			} else {
				if($farray[$key]['answered']=='p') $img = 'topic_old_poll.gif';
				elseif($farray[$key]['answered']=='n') $img = 'topic_old_help.gif';
				else $img = 'topic_old.gif';
			} echo $img;
		if($farray[$key]['post_id']) $gotopost = '&findpost='.$farray[$key]['post_id'].'#post'.$farray[$key][post_id]; else $gotopost = '';
		echo '" border=0></a></td><td><font size=2><b><a href="forums.php?forum='.$farray[$key]['forum_id'].'&topic='.$key.'&highlight='.$highlight.$gotopost.'">'.$farray[$key]['title'].'</a></b></font>';
		if($farray[$key]['description']) echo '<br>'.$farray[$key]['description'].'</td></tr>';
	} } if($cf==0) echo invalid('topics/posts','');

	if($cf==$lim) echo '<tr><td></td><td>limit of '.$lim.' topics found, please narrow your search</td></tr>';
	$ctotal += $cf;

	if($from=='editing') {
		$auth = md5('Mky'.$forum_id.$userdata['user_id'].date("h/d/m")); ?>
		<tr><td height=10><img src="images/null.gif" height=10 width=1 border=0></td></tr>
		<tr><td colspan=2>
		<table bgcolor="<?=$colors['item']?>" cellspacing=1 cellpadding=2 width="50%"><tr><td bgcolor="<?=$colors['bg']?>" style="font-size:11px">
		<img src="themes/<?=$theme?>/snark.gif" align=left border=0><font size=2><b>Nothing helpful?</b></font></a><br>
		<a href="forums.php?mode=newtopic&forum=<?=$forum_id?>&subject=<?=$search?>&auth=<?=$auth?>">Click here to post your problem in the forums</a>
		</td></tr></table></td></tr>
	<?php }

}


if($searchfiles=='on') { $cf=0;
	echo '<tr><td colspan=2><div class="subtitle">File Search:</div></td></tr>';
//filename,author,url,date,icon,description,game
	if($which=='all') $query = 'SELECT DISTINCT(file_id),filename,author,url,date,icon,description,game,screenshot FROM files WHERE (('.likeall('filename','').') OR ('.likeall('description','').'))';
	if($which=='phrase') $query = "SELECT DISTINCT file_id, * FROM files WHERE (filename LIKE '%$search%' OR description LIKE '%$search%')";

	if($searchgame) $query.= " AND game = '$searchgame'"; if($sort) $query .= ' ORDER BY '.$sort; else $query .= ' ORDER BY file_id DESC';
	$query.= ' LIMIT '.$lim; $sql = mysql_query($query);

	while($array = mysql_fetch_array($sql)) { $cf++;
		echo '<tr><td align=right valign=top>'; 
		if($array[icon]) echo '<img src="images/'.$array['icon'].'" align=texttop>'; else if(!$searchgame) echo '<img src="images/icon_'.$array[game].'.gif">';
		echo '</td><td>';
		if($array[screenshot]) echo '<img src="files/'.$array['game'].'/images/'.$array[screenshot].'" align=right>';
		echo '<b><font color="'.$colors['item'].'">'.stripslashes($array['filename']).'</font></b>';
		if($array[author]) echo ' by '.userdetails($array['author'],'white',1,'');
		if($array[url]) echo ' <a href="editing.php?page=files&download='.$array['file_id'].'"><img src="images/gfx_folder.gif" onmouseover="window.status=\''.$array[url].'\'; return true" onmouseout="window.status=\'\'; return true" align=texttop border=0></a>';
		if(substr_count($sort,'date')) echo ' <font color="'.$colors['info'].'">edited on '.date("jS M Y",$array[date]);
		echo '<br><font color="'.$colors['medtext'].'">'.stripslashes($array['description']).'</font>';
		echo "</td></tr>\n";
	} if(!$cf) echo invalid('files','');

	if($cm==$lim) echo '<tr><td></td><td>limit of '.$lim.' files found, please narrow your search</td></tr>';
	echo '<tr><td height=10> </td></tr>';
	$ctotal += $cf;
}

if($searchmaps=='on') {	$cm=0;
	echo '<tr><td colspan=2><div class="subtitle">Map Search:</div></td></tr>';

	if($which=='all') $query = 'SELECT DISTINCT `map_id`, `name`,`game`,`mod`,`user_id`,`map_url`,`date` FROM maps WHERE (('.likeall('name','').') OR ('.likeall('map_about','').'))';
	elseif($which=='phrase') $query = "SELECT DISTINCT `map_id`, `name`,`game`,`mod`,`user_id`,`map_url`,`dat`e FROM maps WHERE (`name` LIKE '%$search%' OR `map_about` LIKE '%$search%')";

	if($searchgame) $query.= " AND game = '$searchgame'"; if($sort) $query .= ' ORDER BY '.$sort; else $query.= ' ORDER BY map_id DESC';
	$query.= ' LIMIT '.$start.', '.$lim; $sql = mysql_query($query);

	while($array = mysql_fetch_array($sql)) { $cm++;
		echo '<tr><td></td><td><b><a href="maps.php?map='.$array['map_id'].'"><img src="images/icon_'.$array[game].'_'.$array[mod].'.gif" border=0 align=texttop> '.stripslashes($array[name]).'</a></b>';
		echo ' by '.userdetails($array['user_id'],'white','return','');
		if($array['map_url']) echo ' <a href="maps.php?download='.$array['map_id'].'"><img src="images/gfx_folder.gif" onmouseover="window.status=\''.$array['map_url'].'\'; return true" onmouseout="window.status=\'\'; return true" align=texttop border=0></a>';
		if(substr_count($sort,'date')) echo ' <font color="'.$colors['info'].'">edited on '.date("jS M Y",$array[date]);
		echo "</td></tr>\n";
	} if(!$cm) echo invalid('maps','');

	if($cm==$lim) echo '<tr><td></td><td>limit of '.$lim.' maps found, please narrow your search</td></tr>';
	echo '<tr><td height=10> </td></tr>';
	$ctotal += $cm;
}

if($searchusers=='on') {
        $cu = 0;
	echo '<tr><td colspan=2><b><font size=2>User Search:</font></b></td></tr>';
	$sql = mysql_query("SELECT username,last_seen FROM users WHERE username LIKE '%$search%' LIMIT $lim");
	while($array = mysql_fetch_array($sql)) {
	        $cu++;
		echo '<tr><td></td><td><a href="users.php?name='.$array['username'].'"><b>'.$array['username'].'</b></a>'.(($array['last_seen'])?' <font color="'.$colors['medtext'].'">(last seen '.date("l jS M Y",$array['last_seen']).')</font>':'').'</td></tr>';
	}
	if(!$cu) echo invalid('users','');
	$ctotal += $cu;
}


if(!$ctotal) echo '<tr><td height=10> </td></tr><tr><td colspan=2>No matches for your search found.</td></tr>'; //else echo $ctotal.' results found';
?>
<tr><td width="5%"></td><td width="95%"></td></tr>
</table>
