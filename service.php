<?php 
//------------------ -----------------------------------
// SERVICE
// info		:	servicios
// version 	:	1.3
// date		:	15-12-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
require_once("../include/class/codes.php");
require_once("./security.php");
$Security = new SECURITY();

if (!defined('ROOT')){
	define('ROOT','../include/');
}

//require_once(ROOT.'lib/variables.php');
//require_once(ROOT.'lib/functions.php');

if(!isset($_POST['apikey'])||empty($_POST['apikey']) ){
	$Security->send(CODES::Unauthorized);//"ErrorCode.".__LINE__.": Unauthorized");
}

require_once(ROOT.'class/userservice.php');
try{
	$exec = new USERSERVICE();
	$idApiuser = $exec->loginUserApiKey($_POST['apikey']);
	//$json = array();
	//$json['idApiuser'] = $idApiuser;
	//$Security->send($json);
}catch(Exception $e){
	$Security->send(CODES::Unauthorized);//"ErrorCode.".__LINE__.": Unauthorized - $apiuser ");
}

// metodo		
if(!isset($_POST['method'])||empty($_POST['method'])){
	//var_dump($data);
	$this->send(CODES::Method_Not_Allowed);//"ErrorCode.".__LINE__.": Invalid Method");
}

switch($_POST['method']){
//=============================================================
case "test":
//=============================================================
	/**
	* @info TEST connection 
	* @param method [string]- test
	* @return json.STATUS
	* @return json.INFO 
	*/
	$json = array();
	$json["INFO"] 	= "TEST connection OK";
	$Security->send($json);
break;
//=============================================================
case "codes":
//=============================================================
	/**
	* @info get codes and messages 
	* @param method [string]- codes
	* @return json.STATUS
	* @return json.codes 
	*/
	$json = array();
	$json["codes"] = CODES::getList();
	$Security->send($json);
break; 
//=============================================================
case "recharge":
//=============================================================
/**
* @info recarga balance de usuario 
* @param method [string]- recharge
* @param user [string]- usuario a recargar
* @param amount [int]- monto a recargar
* @return json.STATUS
* @return json.INFO
* @return json.transaction ID de la transacion
* @return json.balance Nuevo Balance del usuario
*/
	if( !isset($_POST['user'])||empty($_POST['user'])||
		!isset($_POST['amount'])||empty($_POST['amount'])){
		$Security->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.":".'Incomplete data');
	}
	
	require_once(ROOT.'class/common.php');	
	try{
		$exec    = new DISPLAY();
		$data    = $exec->getIdUser($_POST['user']);
	    $id_user = $data->id_user;
	}catch(Exception $e){
		$Security->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.":".$e->getMessage());
	}

	require_once(ROOT.'class/transaction.php');	
	try{
		$exec = new TRANSACTION();
		$data =$exec->recharge($idApiuser,$id_user,$_POST['amount']);
		$json = array();
		$json["transaction"] = $data->transaction;
		$json["balance"] 	 = $data->balance;
		$Security->send($json);
	}catch(Exception $e){
		$Security->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.":".$e->getMessage());
	}
	
break;
}
$Security->send(CODES::Not_Implemented);//"ErrorCode.".__LINE__.": API.Overflow");
?>