<?php
	tracker('Writing/editing a map review','');
	$hidden = '';

	if($id = $_GET['edit']) {
		$action = 'edit';
		if($_GET['auth']) {
		        if(!$array = mysql_fetch_array(mysql_query("SELECT * FROM authenticate WHERE id = '$id' LIMIT 1"))) { header('Location:cp.php?error=Review+doesn\'t+exist'); die; }
		        if($array['user_id']!=$userdata['user_id'] && $userdata['user_level']<3) { header('Location: cp.php?error=Review+isn\'t+yours'); die; }
			$map = $array['subtype'];
		        $array['pros'] = $array['ancillary1']; $array['cons'] = $array['ancillary2'];
		        $array['verdict'] = $array['description'];
		        $array['score'] = $array['editor'];
		        $hidden = '<input type="hidden" name="auth" value="true">';
		} else {
		       	//error_die('Sorry, you can\'t currently edit existing reviews.');
		       	$array = mysql_fetch_array(mysql_query("SELECT r.*,t.text FROM reviews r, reviews_text t WHERE r.review_id = '$id' AND t.review_id = r.review_id LIMIT 1"));
		       	if($array['reviewer_id']!=$userdata['user_id'] && $userdata['user_level']<3) { header('Location: cp.php?error=Review+isn\'t+yours'); die; }
			$map = $array['map_id'];
			$array['text'] = strip_tags($array['text']);
		}

		title($t_cp.' Edit review'.(($array['mapname'])?': &quot'.stripslashes($array['mapname']).'&quot;':''),'cp');

	} else {
		$action = 'new';
		title($t_cp.' Add review','cp');
		$map = $_GET['map']; if(!$map) { header('Location: cp.php'); die; }
	}

	if($map) {
		$sql = mysql_query("SELECT * FROM maps WHERE map_id = '$map' LIMIT 1");
		if(!$marray = mysql_fetch_array($sql)) { header('Location: cp.php?error=Map+doesn\'t+exist'); die; }
	}

msg('Before writing a review, <a href="features.php?page=reviews&game=HL">read some existing reviews</a> to see what sort of style of review we are after. We will only accept well written, informative reviews, preferably of average/good maps- this is not the place to post short comments about a map!','info','','','','div');

?>



<form action="cp.php" method="post">
<input type="hidden" name="action" value="<?=$action?>review">
<input type="hidden" name="id" value="<?=$map?>">
<?php if($id) echo '<input type="hidden" name="edit" value="'.$id.'">'; ?>
<?=$hidden?>
<table width="100%" cellspacing=2 cellpadding=2>
<tr>
	<td width="20%" align=right><b>Map name:</b></td>
	<td width="80%"><?=stripslashes($marray['name'])?></td>
</tr>
<tr>
	<td width="20%" align=right><b>Map author:</b></td>
	<td width="80%"><?php echo userdetails($marray['user_id'],'notbold','',''); ?></td>
</tr>

<tr>
	<td valign=top align=right><b>Pros:</b></td>
	<td><input type="text" name="pros" value="<?=$array['pros']?>" size=64 class="textinput" maxlength=128>
		<br><span class="help">The good things in this map</span></td>
</tr>
<tr>
	<td valign=top align=right><b>Cons:</b></td>
	<td><input type="text" name="cons" value="<?=$array['cons']?>" size=64 class="textinput" maxlength=128>
		<br><span class="help">The bad things about the map</span></td>
</tr>

<tr>
	<td valign=top align=right><b>Review:</b></td>
	<td><textarea name="text" rows=15 cols=70><?=str_replace('<br>',"\n",$array['text'])?></textarea>
		<br><span class="help">The review should ideally be over 200 words long and describe both the physical
		design of the map and the gameplay. Please spell correctly, use correct grammar and write a decent review or it might not
		be accepted!</span></td>
</tr>

<tr>
	<td align=right valign=top><b>Verdict:</b></td>
	<td><input type="text" name="verdict" value="<?=$array['verdict']?>" size=64 class="textinput" maxlength=128>
	<br><span class="help">A sentence or two to sum up</span></td>
</tr>
<tr>
	<td align=right><b>Score:</b></td>
	<td><select name="score"><?php for($i=0;$i<=10;$i++) echo '<option value='.$i.(($array['score']==$i)?' selected':'').'>'.$i.'</option>'; ?></select>/10</td>
</tr>
<tr>
<td>&nbsp;</td><td></td>
</tr>
<tr>
	<td></td>
	<td><div class="help">We reserve the right to alter your review for clarity, or to edit the score you suggest.
	This is to help maintain a standard of reviewing, rather than some evil editorial bias/plot towards certain
	people.</div>
	<p><input type="submit" name="submit" value="submit" class="submit3"></td>
</tr>

</table>
