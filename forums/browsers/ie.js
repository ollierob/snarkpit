function FormatText(command, option) {
	document.getElementById('message').contentWindow.focus();
	document.getElementById('message').contentWindow.document.execCommand(command, false, option);
	document.getElementById('message').contentWindow.focus();
}

function AddImage() {
	imagePath = prompt('Enter the web address of the image', 'http://');				
	if((imagePath!=null) && (imagePath!='')) {	
		document.getElementById('message').contentWindow.focus();				
		document.getElementById('message').contentWindow.document.execCommand('InsertImage', false, imagePath);
	}
	document.getElementById('message').contentWindow.focus();			
}

function Emoticon(url) {
	document.getElementById('message').contentWindow.document.execCommand('InsertImage', false, url);
	document.getElementById('message').contentWindow.focus();
}

function symbol_table() {
	var ins_symbol = showModalDialog('forums/symbol_table.htm','Symbol table','resizable:no;help:no;status:no;scroll:no;dialogWidth:360px;dialogHeight:300px');
	if(ins_symbol) document.getElementById('message').contentWindow.document.body.innerHTML += ins_symbol;	
}

function checkform(mode) { 
	var alertmsg = '';
	document.frmAddMessage.message.value = document.getElementById('message').contentWindow.document.body.innerHTML;
	if(document.frmAddMessage.section) { if(document.frmAddMessage.section.value=='ERR') alertmsg += '\n- Please select a section for your editing question to go under'; }
	if(document.frmAddMessage.message.value=='') alertmsg += '\n- You need to write a message!'
	if(mode=='newtopic') { if(document.frmAddMessage.subject.value=='') alertmsg += '\n- Your topic need a title'; }
	if(frmAddMessage.message.value.length>55000) alertmsg += '\n- Your message (including HTML code) is too long!'
	if(alertmsg) alertmsg = 'There was a problem submitting your post:' + alertmsg;

	if(alertmsg) { 
		alert(alertmsg);
		return false
	} else {
		notSubmitted = false;
		document.getElementById('submit').disabled = true;
		document.forms('frmAddMessage').submit();
		return true;
	}
}

<!-- Setcookie scripts originally from http://javascript.internet.com but cookieForms() heavily modified by Leperous -->

var expDays = 100;
var exp = new Date(); 
exp.setTime(exp.getTime() + (expDays*24*60*60*1000));
var notSubmitted = true;
var toUpdate = '';

function getCookieVal(offset) {  
	var endstr = document.cookie.indexOf (";", offset);  
	if (endstr == -1) { endstr = document.cookie.length; }
	return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie(name) {  
	var arg = name + "=";  
	var alen = arg.length;  
	var clen = document.cookie.length;  
	var i = 0;  
	while (i < clen) {    
		var j = i + alen;    
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);    
		i = document.cookie.indexOf(" ", i) + 1;    
		if (i == 0) break;   
	}  
	return null;
}

function SetCookie(name, value) {
	var argv = SetCookie.arguments;  
	var argc = SetCookie.arguments.length;  
	var expires = (argc > 2) ? argv[2] : null;  
	var path = (argc > 3) ? argv[3] : null;  
	var domain = (argc > 4) ? argv[4] : null;  
	var secure = (argc > 5) ? argv[5] : false;  
	document.cookie = name + "=" + escape (value) + 
	((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + 
	((path == null) ? "" : ("; path=" + path)) +  
	((domain == null) ? "" : ("; domain=" + domain)) +    
	((secure == true) ? "; secure" : "");
}

function cookieForms(mode,value) {

	if(mode == 'open') {
		cookieValue = GetCookie('saved_message');
		if(cookieValue != null && cookieValue!='<p>&nbsp;</p>') {
			if(document.getElementById('dbox')) {
				showhide('dbox');
				showhide('hbox');
			}
			toUpdate = cookieValue;
		}
	}

	else if(mode == 'save') {
		toUpdate = document.getElementById(value).contentWindow.document.body.innerHTML;
		if(notSubmitted==true) {
			if(toUpdate && toUpdate.length>32) SetCookie('saved_message',toUpdate,exp);
		} else SetCookie('saved_message','',exp);
	}
}

var loadedtext = false;