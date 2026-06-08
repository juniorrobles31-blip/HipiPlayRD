<?php
//die('{"STATUS":"ERROR", "INFO":"'.sha1($_POST['apipass']).'"}');
require_once("index.php");

if(!isset($_POST['method'])||empty($_POST['method'])){
	$json = array('STATUS'=>'ERROR','INFO'=>ERROR_EMPTY);
	die(json_encode($json));
}

// TODO: cambiar por base de datos
$db = array();

$Recarga = array("name"=>"Recarga", "info"=> "Recargar balances de Zuzuvama", "method" => "POS.recharge", "params"=>array("id","amount"));

$db[] = array("name"=>"Recarga", "info"=> "Recargar balances de Zuzuvama",	"menu"=> $Recarga);
$db[] = array("name"=>"Pago", "info"=> "Pagar a Clientes",	"menu"=> NULL);
$db[] = array("name"=>"Remesa", "info"=> "Recargar balances de Zuzuvama",	"menu"=> NULL);
$db[] = array("name"=>"Juegos", "info"=> "Juegos del Dinero",	"menu"=> NULL);


switch($_POST['method']){
//=============================================================
case "POS":
//=============================================================
/**
* @info Optiene el Menu a desplegar en las POS (Point Of Sale) 
* @param method* [string]- POS
* @return json.STATUS
* @return json.INFO
* @return json.menu [array] Lista de menu 
*/
	$json = array();	
	$json["STATUS"] = "OK";
	$json["INFO"] = "POS";
	$json["menu"] = $db;
	
	die(json_encode($json,JSON_NUMERIC_CHECK));
break;
//=============================================================
case "POS.recharge":
//=============================================================
/**
* @info POS (Point Of Sale) 
* @param method* [string]- POS.recharge
* @param id* [string]- id del usuario a recargarle la cuenta
* @param amount* [string]- balance a recargar
* @return json.STATUS
* @return json.INFO
* @return json.menu [array] Lista de menu 
*/
	$json = array();	
	$json["STATUS"] = "OK";
	$json["INFO"]   = "Recarga completa";
	$json["print"]  = "Recarga completa <br> ticket #9214985<br> Una recarga de $[".$_POST['amount']."] se ha acreditado a su cuenta, su nuevo balance es de [$10,000.00]";
	
	die(json_encode($json,JSON_NUMERIC_CHECK));
break;
//=============================================================
case "POS.menu":
//=============================================================
/**
* @info POS (Point Of Sale) sub-menu
* @param method* [string]- POS.menu
* @param menu* [int]- id menu
* @return json.STATUS
* @return json.INFO
* @return json.menu [array] Lista de menu 
*/
	$json = array();	
	$json["STATUS"] = "OK";
	$json["INFO"] = "POS.menu";

	if ($_POST["menu"] == 0 || !isset($db[$_POST["menu"]])){
		$json["STATUS"] = "ERROR";
		$json["INFO"]   = "Menu Invalido";
		$json["menu"] 	= $db;
	}else{
		$menu = $db[$_POST["menu"]];
	}
	
	die(json_encode($json,JSON_NUMERIC_CHECK));
break;
//-------------------------------------
default:
//-------------------------------------
	$json=array('STATUS'=>'ERROR','INFO'=>ERROR_METHOD_NOT_FOUND);
	die(json_encode($json,JSON_NUMERIC_CHECK));
break;	
}
?>