/*************************************************************
 * AJAX Spell Checker - Version 1.7
 * (C) 2005 - Garrison Locke
 * 
 * This spell checker is built in the style of the Gmail spell
 * checker.  It uses AJAX to communicate with the backend without
 * requiring the page be reloaded.  If you use this code, please
 * give me credit and a link to my site would be nice.
 * http://www.broken-notebook.com.
 *
 * Copyright (c) 2005, Garrison Locke
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice, 
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice, 
 *     this list of conditions and the following disclaimer in the documentation 
 *     and/or other materials provided with the distribution.
 *   * Neither the name of the http://www.broken-notebook.com nor the names of its 
 *     contributors may be used to endorse or promote products derived from this 
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
 * OF SUCH DAMAGE.
 *
 *************************************************************/

//an event handler.  It's used to listen for clicks on the page.  checkClickLocation is called when the user clicks anywhere.
document.onclick = checkClickLocation;

var objToCheck; //the object you want to spell check.

/*************************************************************
 * setObjToCheck(idOfObject)
 *
 * This function sets a global variable that is the object that
 * needs to be spell checked.  This function is called from the
 * action div.  It also sets the width and height of the results
 * div to match the element that's being checked.
 *
 * idOfObject - The id of the object you want to check.
 *
 *************************************************************/
function setObjToCheck(id){
	objToCheck = document.getElementById(id);
	document.getElementById('results').style.width = objToCheck.style.width;
	document.getElementById('results').style.height = objToCheck.style.height;
}


/*************************************************************
 * spellCheck_cb(new_data)
 *
 * This is the callback function that the spellCheck php function
 * returns the spell checked data to.  It sets the results div
 * to contain the markedup misspelled data and changes the status
 * message.  If there are no misspellings then new_data is the 
 * empty string and the status is set to "No Misspellings Found".
 *
 * new_data - The marked up misspelled data returned from php.
 *
 *************************************************************/
function spellCheck_cb(new_data) {
	var isThereAMisspelling = new_data.charAt(0);
	new_data = new_data.substring(1);
	if(isThereAMisspelling == "1"){
		document.getElementById("results").innerHTML = new_data;
		objToCheck.style.display = "none";
		document.getElementById("results").style.display = "block";
		document.getElementById("status").innerHTML = "";
		document.getElementById("action").innerHTML = "<a href=\"javascript:void(0)\" onClick=\"resumeEditing();\">Resume editing</a>";
	}
	else {
		document.getElementById("status").innerHTML = "";
		document.getElementById("action").innerHTML = "No misspellings found";
		objToCheck.disabled = false;
	}
} //end spellCheck_cb function


/*************************************************************
 * spellCheck()
 *
 * The spellCheck javascript function sends the text entered by
 * the user in the text box to php to be spell checked.  It also
 * sets the status message to "Checking..." because it's currently
 * checking the spelling.
 *
 *************************************************************/
function spellCheck(type) {
	var query;
	document.getElementById("results").style.display = "none";
	document.getElementById("status").innerHTML = "Checking...";
	//query = objToCheck.value;
	if(type=='textarea') query = objToCheck.value; //textarea
	else if(type=='iframe') query = objToCheck.contentWindow.document.body.innerHTML; //iframe

	cpaint_call('SELF', 'POST', 'spellCheck', query, spellCheck_cb);
} //end spellcheck function


/*************************************************************
 * checkClickLocation(e)
 *
 * This function is called by the event listener when the user
 * clicks on anything.  It is used to close the suggestion div
 * if the user clicks anywhere that's not inside the suggestion
 * div.  It just checks to see if the name of what the user clicks
 * on is not "suggestions" then hides the div if it's not.
 *
 * e - the event, in this case the user clicking somewhere on
 *     the page.
 *
 *************************************************************/
function checkClickLocation(e){
	var theTarget = getTarget(e);
	if (theTarget.name != "suggestions"){
		document.getElementById("suggestions").style.display = "none";
	}
} //end checkClickLocation function


/*************************************************************
 * getTarget(e)
 *
 * The get target function gets the correct target of the event.
 * This function is required because IE handles the events in
 * a different (wrong) manner than the rest of the browsers.
 *
 * e - the target, in this case the user clicking somewhere on
 *     the page.
 *
 *************************************************************/
function getTarget(e){
	var value;
	if (checkBrowser() == "ie"){
		value = window.event.srcElement;
	}
	else{
		value = e.target;
	}
	return value;
} //end getTarget function


/*************************************************************
 * checkBrowser()
 *
 * The checkBrowser function simply checks to see what browser
 * the user is using and returns a string containing the browser
 * type.
 *
 *************************************************************/
function checkBrowser(){
	var theAgent = navigator.userAgent.toLowerCase();
	if(theAgent.indexOf("msie") != -1){
		if(theAgent.indexOf("opera") != -1){
			return "opera";
		}
		else{
			return "ie";
		}
	}
	else if(theAgent.indexOf("netscape") != -1){
		return "netscape";
	}
	else if(theAgent.indexOf("firefox") != -1){
		return "firefox";
	}
	else if(theAgent.indexOf("mozilla/5.0") != -1){
		return "mozilla";
	}
	else if(theAgent.indexOf("\/") != -1){
		if (theAgent.substr(0,theAgent.indexOf('\/')) != 'mozilla'){
			return navigator.userAgent.substr(0,theAgent.indexOf('\/'));
		}
		else{
			return "netscape";
		} 
	}
	else if(theAgent.indexOf(' ') != -1){
		return navigator.userAgent.substr(0,theAgent.indexOf(' '));
	}
	else{ 
		return navigator.userAgent;
	}
} //end checkBrowser function


/*************************************************************
 * showSuggestions_cb(new_data)
 *
 * The showSuggestions_cb function is a callback function that
 * php's showSuggestions function returns to.  It sets the 
 * suggestions table to contain the new data and then displays
 * the suggestions div.  It also clears the status message.
 *
 * new_data - The suggestions table returned from php.
 *
 *************************************************************/
function showSuggestions_cb(new_data) {
	document.getElementById("suggestions").innerHTML = new_data;
	document.getElementById("suggestions").style.display = "block";
	document.getElementById("status").innerHTML = "";
} //end showSuggestions_cb function


/*************************************************************
 * showSuggestions(word, id)
 *
 * The showSuggestions function calls the showSuggestions php
 * function to get suggestions for the misspelled word that the
 * user has clicked on.  It sets the status to "Searching...",
 * hides the suggestions div, finds the x and y position of the
 * span containing the misspelled word that user clicked on so 
 * the div can be displayed in the correct location, and then
 * calls the showSuggestions php function with the misspelled word
 * and the id of the span containing it.
 *
 * word - the misspelled word that the user clicked on
 * id - the id of the span that contains the misspelled word
 *
 *************************************************************/
function showSuggestions(word, id) {
	document.getElementById("status").innerHTML = "Searching...";
	document.getElementById("suggestions").style.display = "none";
	var x = findPosX(id);
	var y = findPosY(id);
	
	//normally here I'd get the scrollTop of the results div, but in my case
	//there are two divs, the main content_frame div that contains everything as
	//well as the div for the results.  we need to subtract the scrollTop of both
	//of them in order to have the suggestions div be in the right location all the time.
	//so if you only have the one div, just remove the other line.
	var scrollPos = document.getElementById("results").scrollTop;
	if(document.getElementById("content_frame")){
		var scrollPos2 = document.getElementById("content_frame").scrollTop;
	}
	else {
		scrollPos2 = 0;
	}
	
	document.getElementById("suggestions").style.left = x;
	document.getElementById("suggestions").style.top = y+16-scrollPos-scrollPos2;
	
	cpaint_call('SELF', 'POST', 'showSuggestions', word, id, showSuggestions_cb);
} //end showSuggestions function


/*************************************************************
 * replaceWord(id, newWord)
 *
 * The replaceWord function takes the id of the misspelled word
 * that the user clicked on and replaces the innerHTML of that
 * span with the new word that the user selects from the suggestion
 * div.  It hides the suggestions div and changes the color of
 * the previously misspelled word to green to let the user know
 * it has been changed.  It then calls the switchText php function
 * with the innerHTML of the div to update the text of the text box.
 *
 * id - the id of the span that contains the word to be replaced
 * newWord - the word the user selected from the suggestions div
 *           to replace the misspelled word.
 *
 *************************************************************/
function replaceWord(id, newWord){
	document.getElementById(id).innerHTML = trim(newWord);
	document.getElementById("suggestions").style.display = "none";
	document.getElementById(id).style.color = "#005500";
} //end replaceWord function


/*************************************************************
 * switchText()
 *
 * The switchText function is a funtion is called when the user
 * clicks on resume editing (or submits the form).  It calls the
 * php function to switchText and uncomments the html and replaces
 * breaks and everything.  Here all the breaks that the user has
 * typed are replaced with %u2026.  Firefox does this goofy thing
 * where it cleans up the display of your html, which adds in \n's
 * where you don't want them.  So I replace the user-entered returns
 * with something unique so that I can rip out all the breaks that
 * the browser might add and we don't want.
 *
 *************************************************************/
function switchText() {
	var text = document.getElementById("results").innerHTML;
	if(text != ""){
		text = text.replace(/<br \/>/gi, "%u2026");
		text = text.replace(/<br\/>/gi, "%u2026");
		text = text.replace(/<br>/gi, "%u2026");
		
		cpaint_call('SELF', 'POST', 'switchText', text, switchText_cb);
	}
} //end switchText function


/*************************************************************
 * switchText_cb(new_string)
 *
 * The switchText_cb function is a call back funtion that the
 * switchText php function returns to.  I replace all the %u2026's
 * with returns.  It then replaces the text in the text box with 
 * the corrected text fromt he div.
 *
 * new_string - The corrected text from the div.
 *
 *************************************************************/
function switchText_cb(new_string) {
	new_string = new_string.replace(/%u2026/g, "\n");
	objToCheck.value = new_string;
	objToCheck.disabled = false;
} //end switchText_cb function


/*************************************************************
 * resumeEditing()
 *
 * The resumeEditing function is called when the user is in the
 * correction mode and wants to return to the editing mode.  It
 * hides the results div and the suggestions div, then enables
 * the text box and unhides the text box.  It also calls
 * resetAction() to reset the status message.
 *
 *************************************************************/
function resumeEditing() {
	switchText();
	document.getElementById("suggestions").style.display = "none";
	objToCheck.disabled = false;
	objToCheck.style.display = "block";
	document.getElementById("results").style.display = "none";
	resetAction();
} //end resumeEditing function


/*************************************************************
 * resetAction()
 *
 * The resetAction function just resets the status message to
 * the default action of "Check Spelling".
 *
 *************************************************************/
function resetAction() {
	document.getElementById("action").innerHTML = "<a href=\"javascript:void(0)\" onClick=\"spellCheck();\">Check spelling</a>";
} //end resetAction function


/*************************************************************
 * resetSpellChecker()
 *
 * The resetSpellChecker function resets the entire spell checker
 * to the defaults.
 *
 *************************************************************/
function resetSpellChecker() {
	resetAction();
	objToCheck.value = "";
	objToCheck.style.display = "block";
	objToCheck.disabled = false;
	document.getElementById("results").innerHTML = "";
	document.getElementById("results").style.display = "none";
	document.getElementById("suggestions").style.display = "none";
	document.getElementById("status").style.display = "none";
	
} //end resetSpellChecker function


/*************************************************************
 * findPosX(object)
 *
 * The findPosX function just finds the X offset of the top left
 * corner of the object it's given.
 *
 * object - the object that you want to find the upper left X
 *          coordinate of.
 *
 *************************************************************/
function findPosX(object){
	var curleft = 0;
	var obj = document.getElementById(object);
	if (obj.offsetParent){
		while (obj.offsetParent){
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	}
	else if (obj.x){
		curleft += obj.x;
	}
	return curleft;
} //end findPosX function


/*************************************************************
 * findPosY(object)
 *
 * The findPosY function just finds the Y offset of the top left
 * corner of the object it's given.
 *
 * object - the object that you want to find the upper left Y
 *          coordinate of.
 *
 *************************************************************/
function findPosY(object){
	var curtop = 0;var curtop = 0;
	var obj = document.getElementById(object);
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	else if (obj.y){
		curtop += obj.y;
	}
	return curtop;
} //end findPosY function


/*************************************************************
 * trim(s)
 *
 * Trims white space from a string.
 *
 * s - the string you want to trim.
 *
 *************************************************************/
function trim(s) {
  while (s.substring(0,1) == ' ') {
    s = s.substring(1,s.length);
  }
  while (s.substring(s.length-1,s.length) == ' ') {
    s = s.substring(0,s.length-1);
  }
  return s;
}