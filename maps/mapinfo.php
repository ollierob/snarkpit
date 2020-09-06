<?php
	$msql = mysql_query("SELECT fullname FROM mods WHERE name = '$maparray[mod]' AND game = '$maparray[game]' LIMIT 1");
	if(!$marray = mysql_fetch_array($msql)) { header('Location: maps.php'); die; }
	$marray['fullname'] = stripslashes($marray['fullname']);
	
	if($maparray['status']!=100) $map_complete = false; else $map_complete = true;
	$imgpath = 'maps/'.$maparray['game'].'/images/';
	$titleinsert = '<a href="maps.php?game='.$maparray['game'].'" class=white>'.$garray['name'].'</a> » <a href="maps.php?game='.$maparray['game'].'&mod='.$array['mod'].'" class=white>'.$marray['fullname'].'</a>';

	if($userdata['user_id']!=$maparray['user_id']) @mysql_query("UPDATE maps SET views = views + 1 WHERE map_id = '$map' LIMIT 1");

	tracker('Reading about map &quot;'.addslashes($maparray['name']).'&quot;','maps.php?map='.$map);
	title('<a href="maps.php" class="white">Maps</a> » '.$titleinsert.': &quot;'.$maparray['name'].'&quot;','maps');

function download_link($url,$type) { global $map;
	if(substr($url,0,4)=='www.') $url = 'http://'.$url;
	if(!substr_count($url,'.zip') AND !substr_count($url,'.rar')) $window = '" target="_blank'; else $window = '';
	if(substr_count($url,'angelfire.com')) $warning = ' onclick="alert(\'This host does not allow direct map downloads; please right click on this link and `save target` instead\');return false"'; else $warning = '';
	$return = '<a href="?download='.$map.'&amp;type='.$type.$window.'"'.$warning.'>'.str_replace('http://','',$url).'</a>';
	if(substr($url,0,5)=='maps/') $return .= ' ('.((file_exists($url))?round(filesize($url)/1048576,2).' Mb':'<font color="'.$colors['no'].'">d/l broken?</font>').')';
	return $return;
}

	?>

<table width="99%" cellspacing=2 cellpadding=2>

<tr>
<td width="60%" valign=top class="forumtext">

<?php echo($maparray['map_about'])?$maparray['map_about']:'no description available'; ?>

</td>
<td width="380" valign="top">
<fieldset style="padding-bottom:15px">
	<legend><b>map details</b></legend>
	<div class="abouttext" style="font-weight:bold"><?php
	echo 'author: '.(($maparray['user_id']>0)?userdetails($maparray['user_id'],'','1',''):'<i>community map</i>');
	echo '<div style="width:100%">status: ';
	if(!$map_complete) {
		if($maparray['status']=='-1') echo '<i>abandoned</i>';
		else echo '<img src="themes/'.$theme.'/nullred.gif" width='.(2*$maparray['status']).' height=12 align=absbottom><img src="themes/'.$theme.'/nullgrey.gif" width='.(2*(100-$maparray['status'])).' height=12 align=absbottom> '.$maparray['status'].'%';
		//if($userdata && $maparray['user_id']!=$userdata['user_id']) echo ' - <a href="cp.php?mode=watching&action=addmap&id='.$map.'">watch this map </a>'; //<img src="images/gfx_watch.gif" border=0 align=texttop>
	} else echo '<font color="'.$colors['info'].'">complete</font>';

	echo '</div>map size: <font color="'.$colors['info'].'">'; $sizes=array('tiny','small','medium','large','huge','unknown'); echo $sizes[$maparray['size']].'</font>';
	if($maparray['added']) echo '<br>added: <font color="'.$colors['info'].'">'.gmdate("l jS M Y",$maparray['added']).'</font>';
	echo '<br>views: <font color="'.$colors['info'].'">'.$maparray['views'].((!$map_complete)?' ('.$maparray['watching'].' watching)':'').'</font>';
	echo '<br>last updated: <font color="'.$colors['info'].'">'; if($maparray['date']) echo gmdate("l jS M Y",$maparray['date']); else echo '?'; echo '</font>';

	if($maparray['cdate']) { if(!$map_complete) echo '<br>expected release date: '; else echo '<br>released: '; echo '<font color="'.$colors['info'].'">'.$maparray['cdate'].'</font>'; }

	if($maparray['gameplay']) { if(include('lib/gameplay_'.$maparray['game'].$maparray['mod'].'.php')) {
		echo '<br>supported gameplay modes: <font color="'.$colors['info'].'"><b>'; $c=0;
		while(list($key,$val) = each($gamemodes)) {
			if(substr_count($maparray['gameplay'],'-'.$key.'-')) { $c++; if($c!=1) echo ', '; echo $val; }
		} echo '</font>';
	} }
	
	if($maparray['map_url']) echo '<br>downloads: <font color="'.$colors['info'].'">'.$maparray['downloads'].'</font>';

	echo '</div></fieldset>';

	$b_s = '<div style="text-align:center;position:relative;top:-20px"><span class="legendbox" style="font-size:11px;text-align:center">';
	$b_e = '</span></div>';
	$b_t = '';
	if($maparray['status']<100) $b_t = '&nbsp;<a href="cp.php?mode=watching&action=addmap&id='.$map.'"><img src="images/gfx_watch.gif" align=top border=0> watch</a>&nbsp;';
	if($userdata['user_id']==$maparray['user_id'] OR $userdata['user_level']>2) $b_t .= '&nbsp;<a href="cp.php?mode=maps&amp;edit='.$map.'&amp;returnto=maps"><img src="images/gfx_edit.gif" border=0 align=middle> <b>edit this map</b></a>&nbsp;';
	if($b_t) echo $b_s.$b_t.$b_e;


	echo '<div align=center style="width:380px">';
	if($maparray['thumbnails']) {
		for($t=1;$t<=$maparray['thumbnails'];$t++) echo screenshot($map,$maparray['thumbnails'],'maps/'.$maparray['game'].'/images/'.$maparray['map_id'].'_'.$t.'.jpg').'<img src="'.$imgpath.$map.'_'.$t.'_thumb.jpg" border=0></a> ';
	} else { for($i=1;$i<6;$i++) {
		if($maparray['scr'.$i]) $scrshot .= screenshot($map.'&amp;scr='.$i,'',$maparray['scr'.$i]).'<img src="images/gfx_image.gif" border=0 align=texttop></a> ';
	}

	if($scrshot) echo '<b>screenshots:</b> '.$scrshot; }
	echo '</div>';

?>

</td>
</tr>

<tr><td colspan="2" height="50">
<b>
	<img src="images/gfx_download.gif" align="middle"> download
<?php
 	if($maparray['map_url']) {
	        echo ' from</b> '.download_link($maparray['map_url'],'primary');
	        if($maparray['mirror1']) echo '<br><b>or from mirror</b> '.download_link($maparray['mirror1'],'mirror1').'</span>';
	} else echo ' not available</b>';

	if($maparray['related']) echo '<br><img src="images/gfx_html.gif" align="top"><b> read more at </b><a href="'.$maparray['related'].'" target="_blank">'.str_replace('http://','',$maparray['related']).'</a>';
?>
</td></tr>

<tr>
<td valign=top>

<?php if($maparray['comments']>2) echo '<a href="#commentbox"><img src="themes/'.$theme.'/newcomment.jpg" align=right border=0></a>'; ?>
<a name="comments"><h1>Map Comments:</h1></a>

<?php
	if($maparray['thread']) {
		if($maparray['status']!=100) $nonewcomments = 'Please comment on this map by posting in its <a href="forums.php?topic='.$maparray['thread'].'"><b>forum thread</b></a>!';
	}

	$commentbox = true; $commentboxwidth = 310;
	getcomments('map',$map,'maps.php?map='.$map,'',95);

	if($numrated==1) $averagerating = '';
	if($averagerating!=$maparray['rating']) @mysql_query("UPDATE maps SET rating = '$averagerating' WHERE map_id = '$map' LIMIT 1") OR errorlog('updating map rating',mysql_error());
	if($numcomments!=$maparray['comments']) @mysql_query("UPDATE maps SET comments = '$numcomments' WHERE map_id = '$map' LIMIT 1");
?>

</td>
<td valign=top>

<?php
	if($maparray['status']==100) {
	                             	
	echo '<h1>'.(($userdata)?'<a href="cp.php?mode=reviews&map='.$map.'"><img src="themes/'.$theme.'/newreview.jpg" border="0" align=right></a>':'').'Reviews:</h1>';

	$c = 0;
	$sql = mysql_query("SELECT * FROM reviews WHERE map_id = '$map'");
	while($rarray = mysql_fetch_array($sql)) { $c++; ?>
	<p><fieldset><legend><b>Review by <?php userdetails($rarray['reviewer_id'],'','',''); ?></legend>
	<table width="100%" cellpadding=2 cellspacing=2 style="font-size:8pt">
	<tr>
		<td colspan=2 valign=top width="50%"><b>Pros:</b> <?=stripslashes($rarray['pros'])?></td>
		<td colspan=2 valign=top width="50%"><b>Cons:</b> <?=stripslashes($rarray['cons'])?></td>
	</tr>
	<tr>
		<td colspan=3 valign=top width="75%"><b>Verdict:</b> <?=stripslashes($rarray['verdict'])?></td>
		<td width="25%"><font size=5><b><?=$rarray['score']?>/10</b></font></td>
	</tr>
	</table>
	<font size=2><b><a href="features.php?page=reviews&id=<?=$rarray['review_id']?>">Read the full review</a>
	</fieldset>

<?php
	} if(!$c) echo '<p class="abouttext">No reviews of this map have been written yet. Register and <a href="login.php?linkto='.urlencode('cp.php?mode=reviews&map='.$map).'">login</a> to write one!</div>';
}
?>

</td>
</tr>

</table>
