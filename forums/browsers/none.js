function checkform(mode) { 
	//var alertmsg = '';
	//document.frmAddMessage.message.value = document.getElementById('message').innerHTML;
	//if(document.frmAddMessage.section) { if(document.frmAddMessage.section.value=='ERR') alertmsg += '\n- Please select a section for your editing question to go under'; }
	//if(document.frmAddMessage.message.value=='') alertmsg += '\n- You need to write a message!'
	//if(mode=='newtopic') { if(document.frmAddMessage.subject.value=='') alertmsg += '\n- Your topic need a title'; }
	//if(document.frmAddMessage.message.value.length>55000) alertmsg += '\n- Your message (including HTML code) is too long!'
	//if(alertmsg) alertmsg = 'There was a problem submitting your post:' + alertmsg;
	//if(alertmsg) { alert(alertmsg); return false } else return true;
	return true;
}

function storeCaret (textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function insertAtCaret (textEl, text) {
	if (textEl.createTextRange && textEl.caretPos) {
		var caretPos = textEl.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
	} else textEl.value  = text;
	textEl.focus();
}