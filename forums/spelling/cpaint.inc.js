// CPAINT (Cross-Platform Asynchronous INterface Toolkit) - Version 0.6
// Copyright (c) 2005 Boolean Systems, Inc. - www.booleansystems.com
// JavaScript Version 0.5

// Create the XMLHTTP Connection Object
var cpaint_httpobj;
try {
	cpaint_httpobj = new ActiveXObject('Msxml2.XMLHTTP');
} catch (e) {
	try {  
		cpaint_httpobj = new ActiveXObject('Microsoft.XMLHTTP');
	} catch (oc) {
		cpaint_httpobj = null;
	} 
}
if (!cpaint_httpobj && typeof XMLHttpRequest != 'undefined') 
	cpaint_httpobj = new XMLHttpRequest();
if (!cpaint_httpobj) alert('(CPAINT) Could not create connection object');

function cpaint_call() { 
	/* Parameters:  
		See documentation for parameters
	*/
	var cpaint_args, cpaint_url, cp_querystring, cp_i;
	cpaint_args = cpaint_call.arguments;
	
	if (cpaint_args[0] == 'SELF') {
		cpaint_url = document.location.href;
	} else {
		cpaint_url = cpaint_args[0];
	}
	cp_querystring = '';
	for (cp_i = 3; cp_i < cpaint_args.length - 1; cp_i++)
		cp_querystring = cp_querystring + '&cpaint_argument[]=' + escape(cpaint_args[cp_i]);
	
	if (cpaint_args[1] == 'GET') {
		cpaint_url = cpaint_url + '?cpaint_function=' + cpaint_args[2] + cp_querystring;
	} else {
		cp_querystring = 'cpaint_function=' + cpaint_args[2] + cp_querystring;
	}
	cpaint_httpobj.open(cpaint_args[1], cpaint_url, true);
	if (cpaint_args[1] == "POST") {
		try {
			cpaint_httpobj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		} catch(cp_err) {
			alert('Request cannot be completed due to incompatible browser (Opera).');
		}

	}

	cpaint_httpobj.onreadystatechange = function() {
		if (cpaint_httpobj.readyState != 4)
				return;
			var cpaint_status, cpaint_data;
			cpaint_status = cpaint_httpobj.responseText.charAt(0);
			cpaint_data = cpaint_httpobj.responseText;
			if (typeof(cpaint_debug) != 'undefined') {
				if (cpaint_debug == true) alert('[CPAINT Debug] ' + cpaint_data);
			}
			var data_pos = cpaint_data.indexOf('[cpaint_result]');
				if (cpaint_status == '-'){
					if(true){ alert('Error: ' + cpaint_data.substring(1, data_pos)); }}
		else
			cpaint_args[cpaint_args.length-1](cpaint_data.substring(1,data_pos)); 
		}
	if (cpaint_args[1] == 'GET') {
		cpaint_httpobj.send(null);
	} else {
		cpaint_httpobj.send(cp_querystring);
	}
} 				