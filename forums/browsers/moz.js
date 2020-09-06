function FormatText(command, option) { //format text options
	document.getElementById('message').contentWindow.focus();
	document.getElementById('message').contentWindow.document.execCommand(command, false, null);
	document.getElementById('message').contentWindow.focus();
}

function select(selectname) { //used with <select elements to format text
	document.getElementById('message').contentWindow.focus();
	var cursel = document.getElementById(selectname).selectedIndex;
	if (cursel != 0) {
		var selected = document.getElementById(selectname).options[cursel].value;
		document.getElementById('message').contentWindow.document.execCommand(selectname, false, selected);
		document.getElementById(selectname).selectedIndex = 0;
	}
	document.getElementById('message').contentWindow.focus();
}

function AddImage() { //add image
	imagePath = prompt('Enter the web address of the image', 'http://');				
	if((imagePath!=null) && (imagePath!='')) {	
		document.getElementById('message').contentWindow.focus();				
		document.getElementById('message').contentWindow.document.execCommand('InsertImage', false, imagePath);
	} document.getElementById('message').contentWindow.focus();			
}

function Emoticon(url) { //add emoticon by clicking on link
	document.getElementById('message').contentWindow.document.execCommand('InsertImage', false, url);
	document.getElementById('message').contentWindow.focus();
}

function checkform(mode) { //checks form before submission
	var alertmsg = '';
	document.frmAddMessage.message.value = document.getElementById('message').contentWindow.document.body.innerHTML;

	if(document.getElementById('section')) { if(document.getElementById('section').value=='ERR') alertmsg += '\n- Please select a section for your editing question to go under'; }
	if(document.frmAddMessage.message.value=='') alertmsg += '\n- You need to write a message!'
	if(mode=='newtopic') { if(document.getElementById('subject').value=='') alertmsg += '\n- Your topic need a title'; }
	if(document.frmAddMessage.message.value.length>55000) alertmsg += '\n- Your message (including HTML code) is too long!'

	if(alertmsg) { 
		alert('There was a problem submitting your post:'+alertmsg); return false;
	} else return true;
}