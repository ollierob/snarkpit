<?php include('config.php');

$linkto = str_replace($url_site,'',$_REQUEST['linkto']);

if(isset($_POST['submit'])) {
	$un = $_POST['user'];
	$passwd = $_POST['passwd'];
	if(!$un || !$passwd) $error = 'Enter your username/password'; 
	if(!$error) { 
		$sql = mysql_query("SELECT * FROM users WHERE username = '$un' LIMIT 1"); 
		if(!$array = mysql_fetch_array($sql)) $error = 'User does not exist';
		if($_POST['encrypt']=='on') {
		        if($passwd!=$array['password'] && !$error) $error = 'Wrong username/password (try disabling encryption)';
		} else {
			if(md5($passwd)!=$array['password'] && !$error) $error = 'Wrong username/password';
		}
	}

	if($un == 'The SnarkPit') $error = 'Er, no...';
	if($array['activated']!=1) $error = 'You have not yet activated your account, please follow the instructions e-mailed to you. If you have not received an activation e-mail (check your junk mail), then please see <a href="index.php?page=sendpassword">this</a> page.';
	if($array && $array['user_level']<1) $error = 'Your account has either been deactivated or there has been an error logging in.';
	if(!$error) {
		if($_POST['autologin']) {
			$cookieusername = $un;
			$cookiepassword = md5($passwd);
			setcookie('sp2autologinuser',$cookieusername,($now_time+3600*24*365),$cookiepath,$cookiedomain,0);
			setcookie('sp2autologinpass',$cookiepassword,($now_time+3600*24*365),$cookiepath,$cookiedomain,0);
		}
		$userdata = get_userdata($un);
		$sessid = new_session($userdata['user_id'],$_SERVER['REMOTE_ADDR'],$sesscookietime);
		set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
		if(!$linkto) $linkto = 'index.php'; header('Location: '.$linkto); die;
	}
	if(!$array) $error = 'Wrong username/password';
}

	$extrajava = '</script><script src="scripts/encrypt.js" language="JavaScript" type="text/javascript">';

	$pagetitle = 'Login';
	include('header.php'); include('index/sidebar.php');
	title('Login','index');  
	if(isset($error)) echo '<font color=red><b>Error:</b></font> '.$error;

	if(isset($userdata['username'])) $un = $userdata['username']; else $un = '';
?>

<script>
function checkform() {
	if(!document.forms['loginform'].elements['user'].value || !document.forms['loginform'].elements['passwd'].value) { alert('Please enter a username and password'); return false; }
	if(document.forms['loginform'].elements['encrypt'].checked==true) {
	        document.forms['loginform'].elements['passwd'].value = hex_md5(document.forms['loginform'].elements['passwd'].value);
	}
	return true;
}
</script>

<FORM ACTION="login.php" METHOD="POST" onsubmit="return checkform()" name="loginform">
<table width=80% align=center cellpadding=2 cellspacing=0 style="font-size:12px">
<TR>
	<TD><b>Username:</b></TD>
	<TD width="100%"><INPUT TYPE="TEXT" NAME="user" SIZE="25" MAXLENGTH="40" VALUE="<?=$un?>" class="textinput"></TD>
</TR>
<TR>
	<TD><b>Password:</b></TD>
	<TD><INPUT TYPE="PASSWORD" NAME="passwd" SIZE="25" MAXLENGTH="25" class="textinput"></TD>
</TR>
<TR>
	<TD></td>
	<td><INPUT TYPE="CHECKBOX" NAME="autologin" VALUE="1" id="autologin"><label for="autologin"><B>keep me logged in</B></label>
	<br><input type="checkbox" name="encrypt" id="encrypt"><label for="encrypt"><b>encrypt password</b></label>
	</TD>
</TR>
<TR>
	<TD></td><td><br><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Login" class="submit"></TD>
	<?php if(isset($xpage)) $xtra = '?page='.$xpage; else $xtra = ''; ?>
	<input type="hidden" name="linkto" value="<?=$linkto.$xtra?>">
</TR>
</FORM>
</TABLE>

</p>

<p><b><a href="index.php?page=sendpassword">Click here</a></b> if you've lost your password (or your activation e-mail)

<p>Ticking the 'encrypt password' box means your password is encrypted before being sent over the internet. You can use
it if you are worried about your network security, and disable it if it is not compatible with your PC/browser.
However, it may intefere with your browser's password management system.

<p>If you are having problems staying logged in, please check your cookie settings and relax them (for just this
site, if you are worried).

<?php footer(); ?>
