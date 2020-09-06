<?php
	title("$t_cp Submit Stuff",cp);
	if($_REQUEST['sent']) { echo 'Thank you for your submission, someone should take a look at it fairly soon.'; footer(); }
	if(isset($_GET['select'])) $select = $_GET['select'];
?>

Use this form to send us suggestions or small nuggets of info.
<br>If you send us something we know it's from you, so don't misuse it!
<ul>
	<li>Please only send us useful resource websites, we won't link to clan or personal mapping sites.
	<li>Quick Tips are useful little things, such as a shortcut or key combination, that are too
		short to write a whole tutorial on.
</ul></p>

<table width="99%" cellspacing=1 cellpadding=1>
<form action="cp.php?mode=feedback&sent=1" method=post>
<tr><td align=right><b>Related to:</b></td>
	<td><select name=relatedto>
		<option value="link">New Link</option>
		<option value="quicktip"<?php if($select=='quicktip') echo ' SELECTED';?>>Quick Tip</option>
		<option value="complaint">Complaint</option>
		<option value="feedback"<?php if($select=='feedback') echo ' SELECTED'; ?>>Site Feedback/Bug</option>
	</select>
</tr>

<tr><td></td><td>
	<textarea cols=48 rows=6 name=text></textarea>
</tr>

<tr><td></td><td><input type=submit name=submit value='Submit' class=submit>
	

<tr><td width=15%><td width=80%></tr>
</table>
