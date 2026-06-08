<?php 
//include('../include/lib/ctrlsessionheader.php');
if(isset($_SESSION[$sid])){
redirigir("Location:?page=logout");
exit;
}

if(isset($CtrlPage)){ 

?>
<?php

require_once('include/lib/challenge.php');

$CHALLENGE_FIELD_PARAM_NAME = "verificationCode"; 

if(!isset($_POST[$lusername]) || !isset($_POST[$lpass]) || !isset($_POST[$CHALLENGE_FIELD_PARAM_NAME])){
redirigir("Location:?page=login.php&$wrong_empty=$true");
exit;
}
//$username = check_input($_POST[$lusername]);
//$password = check_input($_POST[$lpass]);
$username = $_POST[$lusername]; 
$password = $_POST[$lpass]; 

$username = preg_replace('/\s+/', '', $username);
if(isChallengeAccepted($_POST[$CHALLENGE_FIELD_PARAM_NAME]) === FALSE) {
redirigir("Location:?page=login&$lusername=$username&$wrong_code=$true");
exit;
}else{
 ////VERIFY LEGITIMACY OF TOKEN
if (verifyFormToken('login_form')) {

if(empty($username) || empty($password)){
redirigir("Location:?page=login&$lusername=$username&$wrong_empty=$true");
exit;
}
	
	$data = ConnectWs('Login','',$username,$password,'','','','','','','','');
	$userResult=$data['Result'];
	$userID=$data['ID'];	 
	$userMsg=$data['Msg'];	 
	$userRol = $data['Rol'];	 
	
	if(strcmp($cmpPass, $userResult) == 0){	
	////$userID=1;	 
	/////$userRol = 5;	
	sec_session_start(); // custom php session.	
	$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.	
	$userID = preg_replace("/[^0-9]+/", "", $userID); // XSS protection as we might print this value
	$_SESSION['id'] = $userID; 
	$userName = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
	$_SESSION['usuario'] = $userName;
	$_SESSION['role'] = $userRol;
	//$_SESSION[$slogin_string] = $password;
	$_SESSION['start'] = time(); // taking now logged in time
	/////* Session timeout	*/
	$_SESSION['expire'] = $_SESSION['start'] + ($timeout) ; // ending a session in 15 minutes from the starting time
	////Login successful.
	redirigir("Location:index.php?page=apuestas");
	exit;
	
	}else{
		redirigir("Location:?page=login&$lusername=$username&$getError=$userMsg");
		exit;	
	}
// /* END VERIFY LEGITIMACY OF TOKEN			*/
} else {
	if (!isset($_SESSION[$form.'_token'])) {			
	redirigir("Location:?page=login");
	exit;
	} else {
		writeLog('login_form');
		redirigir("Location:?page=login&$wrong_hack=$true");
		exit;
	}

}

}

?>
<?php 
}
//include('../include/lib/ctrlsessionfooter.php');
  ?>
 
 
 
