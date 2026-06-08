<?php
//-----------------------------------------------------
// Codes
// info		:	Codes
// version 	:	1.0
// date		:	15-12-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
/**
* @info login
* @param apiuser [string] usuario
* @param apipass [string] clave
* @code URL:;;; http://juegosdeldinero.com/api/v1/login;;;;;;HEADER:;;; Content-Type: application/json;;;;;;POST:;;;{;;; "apiuser":"perezabreu",;;; "apipass":"123";;;}
* @return apikey [string] Token de acceso
*/
header("Content-Type: application/json; charset=UTF-8");

require_once("../include/class/codes.php");
$code = new CODE();
$list = get_object_vars($code);
foreach ($list as $row){
	$codes['List of codes'][] = ['code'=>$row['code'],'info'=>$row['info']];
}


die(json_encode($codes,JSON_NUMERIC_CHECK));

?>