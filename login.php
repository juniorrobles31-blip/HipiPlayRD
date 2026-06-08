<?php
//-----------------------------------------------------
// LOGIN
// info		:	Login
// version 	:	1.1
// date		:	15-12-2016
// autor	: 	Pedro Santiago
//-----------------------------------------------------
require_once("../include/class/codes.php");

class Login{
	public function __construct(){
		/**
		* @info login
		* @param apiuser [string] usuario
		* @param apipass [string] clave
		* @code URL:;;; http://juegosdeldinero.com/api/v1/login;;;;;;HEADER:;;; Content-Type: application/json;;;;;;POST:;;;{;;; "apiuser":"perezabreu",;;; "apipass":"123";;;}
		* @return apikey [string] Token de acceso
		*/
		require_once("./security.php");
		$Security = new SECURITY();
		
		if (!defined('ROOT')){
			define('ROOT','../include/');
		}
		
		require_once(ROOT.'lib/variables.php');
		//require_once(ROOT.'lib/functions.php');
		
		if(!isset($_POST['apiuser'])||empty($_POST['apiuser'])||
		   !isset($_POST['apipass'])||empty($_POST['apipass'])){
			$Security->send(CODES::Unauthorized);//"ErrorCode.".__LINE__.": Unauthorized");
		}
		
		require_once(ROOT.'class/userservice.php');
		try{
			$exec = new USERSERVICE();
			$token = $exec->loginUserApi($_POST['apiuser'],$_POST['apipass']);
			$json = array();
			$json["apikey"] = $token;
			$Security->send($json);
		}catch(Exception $e){
			//echo "Error [AJ #". __LINE__ ."].-".$e->getMessage();
			$Security->send(CODES::Unauthorized);//"ErrorCode.".__LINE__.":".$e->getMessage());
		}
	}
}

$login = new Login();
?>