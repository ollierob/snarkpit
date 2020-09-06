<?
// CPAINT (Cross-Platform Asynchronous INterface Toolkit) - Version 0.6
// Copyright (c) 2005 Boolean Systems, Inc. - www.booleansystems.com
// PHP Version 0.4

$method = "";
$function_name = "";

if ($_GET['cpaint_function'] != "") {
	$method = "GET";
	$function_name = $_GET['cpaint_function'];
	$return = call_user_func_array($function_name, $_GET['cpaint_argument']);
	print("+" . $return . "[cpaint_result]");
	exit();
} elseif ($_POST['cpaint_function']) {
	$method = "POST";
	$function_name = $_POST['cpaint_function'];
	$return = call_user_func_array($function_name, $_POST['cpaint_argument']);
	print("+" . $return . "[cpaint_result]");
	exit();
}


?>
