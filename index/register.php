<?php title('Register','index');

	$cur_ip = str_replace('.', '', $REMOTE_ADDR);
	$sql = mysql_query("SELECT ban_ip FROM banlist WHERE ban_ip!=''");
	while($array = mysql_fetch_array($sql)) {
		$ban_ip = str_replace('.', '', $array['ban_ip']);
		if($ban_ip==$cur_ip OR substr_count($cur_ip,$ban_ip)) error_die("Your IP (or it's 'range') has been banned from logging into this website (and hence also from registering)");
	}

	$host = gethostbyaddr($REMOTE_ADDR);
	if(substr_count($host,'bnc.ox.ac.uk')) echo '<b>Up the Brasenose! :D</b>';

if($_POST) {

	$registername = trim(htmlspecialchars(strip_tags($_POST['registername'])));
	$registername = str_replace("'",'',$registername);
	$registername = str_replace("\"",'',$registername);

	if(!$password=$_POST['password'] OR !$registername OR !$email=$_POST['email']) $error = 'You didn\'t enter a username/password/e-mail address';
	if(substr_count($registername,'Lepero')) error_die('You may not use that username!');

	$sql = mysql_query("SELECT word FROM words");
	while($array = mysql_fetch_array($sql)) {
		if(substr_count(strtolower($registername),strtolower($array[word]))) error_die('You cannot use that username!');
	}

	if(!$error) {
		$sql = mysql_query("SELECT username FROM users WHERE username = '$registername' LIMIT 1");
		if($a = mysql_fetch_array($sql)) { $error = 'Username has already been taken'; $registername=''; }
		$sql = mysql_query("SELECT username FROM users_profile WHERE user_email = '$email' LIMIT 1");
		if($a = mysql_fetch_array($sql)) { $error = 'E-mail address has already been used for an account'; $email=''; }
	}

	if($password!=$rpassword && !$error) $error = 'Passwords don\'t match!';
	if($email && (!substr_count($email,'@') || !substr_count($email,'.'))) { $email = ''; $error = 'Invalid e-mail address'; } 
	if($email && substr_count($email,'@dodgeit.com')) { $email = ''; $error = 'You cannot use a dodgeit.com e-mail address!'; }

	if(!$error) { 

		$occ = addslashes($_POST['occ']);
		$from = addslashes($_POST['from']);
		$passwd = md5($password);
		$email = addslashes($email);
   		$website = trim($_POST['website']);
			if(substr(strtolower($website), 0, 7) != "http://") $website = "http://" . $website;
			if($website == "http://") $website = "";
			if(strtolower($website)=="http://n/a") $website = "";
		$website = addslashes($website);
		$icq = (ereg("^[0-9]+$", $_POST['icq'])) ? $icq : '';
		$aim = addslashes($_POST['aim']);
		$yim = addslashes($_POST['yim']);
		$msnm = addslashes($_POST['msnm']);
		$viewemail = $_POST['viewemail'];
  
		if($viewemail == 1) $sqlviewemail = '1,0'; else $sqlviewemail = '0,0';

		$total = mysql_result(mysql_query("SELECT max(user_id) AS total FROM users"),0);
		$total += 1;

		if(!$sql = mysql_query("INSERT INTO users (user_id, username, password, user_ip) VALUES ('$total', '$registername', '$passwd', '$REMOTE_ADDR')")) error_die("Error inserting new user, please contact the site admin");
		if(!$usql = mysql_query("INSERT INTO users_profile (user_id, username, user_regdate, user_email, icq, occupation, location, website, aim, hidestuff, yim, msnm) VALUES ('$total', '$registername', '$now_time', '$email', '$icq', '$occ', '$from', '$website', '$aim', '$sqlviewemail', '$yim', '$msnm')")) error_die("Problem inserting user into database");

		if($_POST['cookie_username']) {
			$time = (time() + (3600 * 24 * 30 * 12));
			setcookie($cookiename, $total, $time, $cookiepath, $cookiedomain, $cookiesecure);
		}

	$activatekey = md5('rX50'.microtime());
	if(!$sql = mysql_query("INSERT INTO register (userid, actkey) VALUES ('$total', '$activatekey')")) echo mysql_error();
	
	$message = "Welcome to the SnarkPit! Please keep this email for your records.
		Your account information is as follows:
		----------------------------
		Username: $registername
		Password: $password
		----------------------------
		You must activate your account by visiting:
		http://www.snarkpit.net/?page=activate&key=$activatekey \r\n
		Please do not forget your password as it has been encrypted in our database and we cannot retrieve it for you.
		However, should you forget your password we provide an easy to use script to generate and email a new, random, password.\nThank you for registering.";
		 
	if(!mail($email,'Welcome to the SnarkPit',$message,'From: leperous@snarkpit.net')) errorlog('Sending activation mail to '.$email);

	@mysql_query("UPDATE counter SET hits=hits+1,date='$registername' WHERE name = 'users'");

	echo '<p align=center>You have registered successfully.</p><p align=center>Please check your e-mail, as you have been sent an activation link you need to follow before you can login.';
	echo '<p align=center>If you do not receive an activation e-mail within the next few hours or have made a mistake, please <a href="mailto:leperous@snarkpit.net">e-mail the site admin</a> from the e-mail address you registered with, and quote your username.';
	
footer();

	} if($error) msg($error,'error');

} ?>
	<div class="content">
	If you've lost your password or haven't yet received your activation e-mail, <b><a href="?page=sendpassword">visit this page</a></b> instead of signing up again.
	<span style="background-color:<?=$colors['medtext']?>">Grey fields</span> are required.

<script language="javascript">
function checkform() { alertmsg = "";
	if(!document.forms['rform'].elements['registername'].value) alertmsg = "\n- Please enter a username";
	if(!document.forms['rform'].elements['password'].value) alertmsg += "\n- Please type in a password";
	if(document.forms['rform'].elements['password'].value != document.forms['rform'].elements['rpassword'].value) alertmsg += "\n- Passwords don't match";
	if(!document.forms['rform'].elements['email'].value || document.forms['rform'].elements['email'].value.indexOf("@")==-1 || document.forms['rform'].elements['email'].value.indexOf(".")==-1) alertmsg += "\n- Please enter a valid e-mail address";
	if(document.forms['rform'].elements['email'].value != document.forms['rform'].elements['remail'].value) alertmsg += "\n- E-mail addresses don't match";

	if(document.forms['rform'].elements['email'].value.indexOf("@dodgeit.com")>0 || document.forms['rform'].elements['email'].value.indexOf("@spamgourmet.com")>0) alertmsg+= "\n- You cannot use a 'fowarding' e-mail address!";
	if(document.forms['rform'].elements['registername'].value.indexOf("@")>0) alertmsg+= "\n- Please don't use symbols in your username";

	if(document.forms['rform'].elements['website'].value.indexOf("[img]")>0) alertmsg += "\n- You cannot use BBCode in your website URL";

	if(alertmsg) { alert('Error:'+alertmsg); return false; } else return true;
}
</script>

<p>
<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
<FORM ACTION="?page=register&submit" METHOD="POST" name="rform" onsubmit="return checkform()">
<TR bgcolor="<?=$colors['darkbg']?>">
	<TD align=right width="25%"><FONT FACE="verdana" SIZE="2"><b>Username:</b></FONT></TD>
	<TD width="75%"><input type="text" name="registername" SIZE="25" maxlength="20" class="textinput" value="<?=$registername?>"> <span class="help">don't use strange symbols in your name</span></TD>
</TR>
<TR bgcolor="<?=$colors['darkbg']?>">
	<TD align=right><FONT FACE="verdana" SIZE="2"><b>Password:</b></TD>
	<TD><input type="password" name="password" size="25" maxlength="25" class="textinput"></TD>
</TR>
<TR bgcolor="<?=$colors['darkbg']?>">
	<TD align=right><FONT FACE="verdana" SIZE="2"><b>Re-enter password:</b></TD>
	<TD><input type="password" name="rpassword" size="25" MAXLENGTH="25" class="textinput"></TD>
</TR>
<TR bgcolor="<?=$colors['darkbg']?>">
	<TD align=right><b>E-mail address:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="email" SIZE="25" MAXLENGTH="80" class="textinput" value="<?=$email?>"></TD>
</TR>
<TR bgcolor="<?=$colors['darkbg']?>">
	<TD align=right><b>Re-enter e-mail address:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="remail" SIZE="25" MAXLENGTH="80" class="textinput" value="<?=$remail?>"></TD>
</TR>
<tr>
	<td></td>
	<td><span class="help">Please enter a proper e-mail address so we can send you an activation e-mail
	or in case you forget your password.</span></td>
</tr>
<TR>
	<TD align=right><img src="images/forum_icq.gif" align=texttop> <b>ICQ:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="icq" SIZE="20" MAXLENGTH="10" class="textinput" value="<?=$_POST['icq']?>"></TD>
</TR>
<TR>
	<TD align=right><img src="images/forum_aim.gif" align=texttop> <b>AIM: <b></TD>
	<TD><INPUT TYPE="TEXT" NAME="aim" SIZE="20" MAXLENGTH="96" class="textinput" value="<?=$_POST['aim']?>"></TD>
</TR>
<TR>
	<TD align=right><img src="images/forum_yim.gif" align=texttop> <b>YIM:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="yim" SIZE="25" MAXLENGTH="96" class="textinput" value="<?=$_POST['yim']?>"></TD>
</TR>
<TR>
	<TD align=right><img src="images/forum_msn.gif" align=texttop> <b>MSN:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="msnm" SIZE="25" MAXLENGTH="96" class="textinput" value="<?=$_POST['msnm']?>"></TD>
</TR>
<TR>
	<TD align=right><img src="images/gfx_website.gif" align=texttop> <b>Website:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="website" SIZE="25" MAXLENGTH="120" VALUE="<?=(($_POST['website'])?$_POST['website']:'http://')?>" class="textinput"> <span class="help">leave field blank if you don't have one</span></TD>
</TR>
<TR>
	<TD align=right><b>Country:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="from" SIZE="25" MAXLENGTH="40" class="textinput" value="<?=$_POST['from']?>"></TD>
</TR>
<TR>
	<TD align=right><b>Occupation:<b></TD>
	<TD><INPUT TYPE="TEXT" NAME="occ" SIZE="25" MAXLENGTH="255" class="textinput" value="<?=$_POST['occ']?>"></TD>
</TR>
<TR ALIGN="LEFT">
	<TD align=right><b>Options:</b></TD>
	<TD><INPUT TYPE="CHECKBOX" NAME="cookie_username" checked>Auto-login<BR>
	<input type=checkbox name=viewemail checked>Show my e-mail address
	</TD>
</TR>

<tr>
	<td align=right valign=top><b>Legal:</b></td>
	<td><div class="help">By providing your e-mail address, you are allowing us at the SnarkPit to contact you for
	important reasons, e.g. those pertaining to your account or to inform you about a change of website address.
	Other people are also able to view your e-mail address and use it at their discretion, unless you tick the
	"show my e-mail address" button above (you can enable/disable this later if you wish.)
	Your contact information, such as e-mail address, MSN username, etc. is only available to other logged in
	users, but all other data you provide can be viewed by the public.
	We will <b>never</b> pass on or disclose your e-mail address to any outside parties or use them for any kind
	of commercial purpose.
	<p>We do not take any responsibility for any bad language or offensive material on this website, or any other
	harm that may come to you through usage of this website unless directly caused by a staff member,
	although we do our best to moderate the content here.
	<p>We will never contact you to ask for any additional personal information, or to verify your password or
	other information about yourself. We also reserve the right to stop you or restrict your usage of this website
	if we feel that you are being abusive or an annoyance to other members.
	<b style="color:red">If you are here to be abusive, please don't bother- it takes us less time to delete
	an account, and all traces of it, than it does for you to register!</b>
	</div></td>
</tr>

<TR>
	<TD></td>
	<td height=40 valign=bottom><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit" class="submit3"></td>
</TR>
</FORM>
</TABLE></p>
