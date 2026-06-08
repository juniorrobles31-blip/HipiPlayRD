<?php
//-----------------------------------------------------
// SECURITY
// info		:	Seguridad
// version 	:	1.2
// date		:	15-12-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
require_once("../include/class/codes.php");

class SECURITY{
	/**
	* @info Clase que gestiona la seguridad del servidor.
	* @code <?php require_once("security.php");; ?>
	*/
	public $Crypt;
	public $key;

	public function __construct(){
		//require_once("./AES.php");
		//$this->Crypt = new AES();
		$this->check();
	}

	public function set_header($type){
		/**
		* @info Establece el tipo de respuesta del servidor y regresa el mensaje asociado a el.
  		* @param type[int] header type
	  	* @return [string] header message info
		* @code <?php require_once("security.php");; SECURITY::set_header(200);;?>
		*/
		$header = "HTTP/1.0";
		switch($type){
			//HTTP Status Codes and messages
			case 200: header($header.' '.$type.' OK'); $m = "The request has succeeded"; break;
			case 201: header($header.' '.$type.' Created')  ; $m = "The request has been fulfilled and resulted in a new resource being created"; break;
			case 400: header($header.' '.$type.' Bad Request'); $m = "The request could not be understood by the server due to malformed syntax";break;
			case 401: header($header.' '.$type.' Unauthorized');$m = "The request requires user authentication"; break;
			case 403: header($header.' '.$type.' Forbidden')  ; $m = "The server understood the request, but is refusing to fulfill it"; break;
			case 404: header($header.' '.$type.' Not Found')  ; $m = "The server has not found anything matching the Request"; break;
			case 405: header($header.' '.$type.' Method Not Allowed')  ; $m = "The method specified in the Request-Line is not allowed for the resource identified by the Request"; break;
			case 406: header($header.' '.$type.' Not Acceptable')  ; $m = "The resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headers sent in the request"; break;
			case 408: header($header.' '.$type.' Request Timeout') ; $m = "The client did not produce a request within the time that the server was prepared to wait"; break;
			case 501: header('HTTP/1.0 501 Not Implemented') ; $m = "The server does not support the functionality required to fulfill the request."; break;
			case 503: header($header.' '.$type.' Service Unavailable')  ; $m = "The server is currently unable to handle the request due to a temporary overloading or maintenance of the server"; break;

			//// application ERRORS //////

			//processing messages
			case 1000: header($header.' '.$type.' Internal Server Error')  ; $m = "Error ejecutado el proceso, intentelo de nuevo o contacte al administrador"; break;
			case 1001: header($header.' '.$type.' Internal Server Error')  ; $m = "Recarga NO pudo ser realizada"; break;
			case 1002: header($header.' '.$type.' Internal Server Error')  ; $m = "El reverso NO puede ser realizado por limite de tiempo o la recarga no existe"; break;
			case 1010: header($header.' '.$type.' Internal Server Error')  ; $m = "NO tiene acceso a esta funcion"; break;
			case 1023: header($header.' '.$type.' Internal Server Error')  ; $m = "NO hay resultados"; break;
			//validation messages
			case 1101: header($header.' '.$type.' Internal Server Error')  ; $m = "Hay campos vacios"; break;
			case 1102: header($header.' '.$type.' Internal Server Error')  ; $m = "Hay campos que son solo numericos"; break;
			case 1103: header($header.' '.$type.' Internal Server Error')  ; $m = "La clave anterior NO coincide"; break;
			case 1104: header($header.' '.$type.' Internal Server Error')  ; $m = "El valor de la imagen no coincide con el digitado"; break;
			case 1105: header($header.' '.$type.' Internal Server Error')  ; $m = "Solo se permite tener dos (2) tipos de monedas"; break;
			case 1106: header($header.' '.$type.' Internal Server Error')  ; $m = "Este email ya existe"; break;
			case 1107: header($header.' '.$type.' Internal Server Error')  ; $m = "Este usuario ya existe"; break;
			case 1108: header($header.' '.$type.' Internal Server Error')  ; $m = "Esta compania ya existe"; break;
			case 1109: header($header.' '.$type.' Internal Server Error')  ; $m = "Este telefono ya existe"; break;
			case 1110: header($header.' '.$type.' Internal Server Error')  ; $m = "Validacion de email invalida, verifique de nuevo"; break;
			case 1111: header($header.' '.$type.' Internal Server Error')  ; $m = "Usuario o clave incorrecta (Si la cuenta fue validada)"; break;
			case 1112: header($header.' '.$type.' Internal Server Error')  ; $m = "El metodo NO existe"; break;
			case 1113: header($header.' '.$type.' Internal Server Error')  ; $m = "URL invalido"; break;
			case 1114: header($header.' '.$type.' Internal Server Error')  ; $m = "Codigo invalido"; break;
			case 1115: header($header.' '.$type.' Internal Server Error')  ; $m = "Tipo de transacción NO existe"; break;
			case 1116: header($header.' '.$type.' Internal Server Error')  ; $m = "NO se puede registrar la transaccion por limite de tiempo"; break;
			case 1150: header($header.' '.$type.' Internal Server Error')  ; $m = "NO tiene opciones agregadas"; break;
			case 1151: header($header.' '.$type.' Internal Server Error')  ; $m = "NO existe este usuario"; break;
			case 1152: header($header.' '.$type.' Internal Server Error')  ; $m = "NO existe este subtipo de compania"; break;
			case 1153: header($header.' '.$type.' Internal Server Error')  ; $m = "NO existe el promotor"; break;
			case 1154: header($header.' '.$type.' Internal Server Error')  ; $m = "No tiene acceso a esta telefónica"; break;
			case 1155: header($header.' '.$type.' Internal Server Error')  ; $m = "Forma de pago NO relacionada"; break;
			case 1156: header($header.' '.$type.' Internal Server Error')  ; $m = "NO existe el rol"; break;
			case 1157: header($header.' '.$type.' Internal Server Error')  ; $m = "NO tiene balance suficiente para realizar esta transacción"; break;
			case 1158: header($header.' '.$type.' Internal Server Error')  ; $m = "El número NO puede ser mas grande que el permitido"; break;
			//Registration validation message
			case 1201: header($header.' '.$type.' Internal Server Error')  ; $m = "Error validando el registro, intentelo de nuevo!"; break;
			case 1202: header($header.' '.$type.' Internal Server Error')  ; $m = "Su cuenta ha sido registrada y un enlace ha sido enviado a email. Tenga en cuenta que debe activar la cuenta haciendo click en el enlace de activacion"; break;
			//Withdrawal messages
			case 1501: header($header.' '.$type.' Internal Server Error')  ; $m = "Este retiro fue bloqueado"; break;
			case 1502: header($header.' '.$type.' Internal Server Error')  ; $m = "Este retiro ya fue pagado"; break;
			case 1503: header($header.' '.$type.' Internal Server Error')  ; $m = "Este retiro NO existe, verifique el usuario o el codigo"; break;
			//Hack message
			case 1900: header($header.' '.$type.' Internal Server Error')  ; $m = "Control de ataques"; break;
			//default international message
			default : header($header.' 500 Internal Server Error'); $m = "Internal server error, sorry for the inconvenience may caused you, try to contact us."; break;
	  }
	  return $m;
	}

	public function check(){
		/**
		* @info Ejecuta la revision de seguridad.
		* @code <?php require_once("./security.php");; $Security = new SECURITY();; $Security->check();;?>
		*/
		if (!defined("ORIGIN")){ define("ORIGIN","*"); }

		header("Content-Type: application/json; charset=UTF-8");
		header("Cache-Control: no-cache, must-revalidate");
		//header("Expires: Sat, 30 Jan 1982 00:00:00 GMT");
		header('Access-Control-Allow-Origin: '.ORIGIN, false);// TODO : Restringir acceso a origenes reconocidos
		header('Access-Control-Allow-Methods: POST');

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

		if ($protocol !== "https://"){
			//$this->send("ErrorCode.".__LINE__.": SSL Required ");
		}
		// no acceso directamente al archivo
		if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
			//$this->send("Direct access Unauthorized", "F.".__LINE__);
		}
		// user agent
		if (!isset($_SERVER['HTTP_USER_AGENT'])){
			//$this->send("ErrorCode.".__LINE__.": Invalid User");
		}else{
			if ($_SERVER['HTTP_USER_AGENT'] != "JD.service"){
				//$this->send("ErrorCode.".__LINE__.": Invalid User");
			}
		}
		// raw
		if ($this->Crypt != NULL){
			if (!isset($_SERVER['HTTP_SHA'])){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Undefined Hacth");
			}
			$this->key = $_SERVER['HTTP_KEY'];//$_SESSION["id"];//
			$sha = $_SERVER['HTTP_SHA'];

			$data = file_get_contents('php://input');
			$dec  = $this->Crypt->decrypt($data, $this->key);
			if ($dec == NULL){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Data");
			}
			if (sha1($dec) != $sha){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Integrity");
			}
			//$this->send("ErrorCode.".__LINE__.": ".$dec);
			$_POST = json_decode($dec,true);
			unset($data);
			unset($dec);
		}else{
			$data = file_get_contents('php://input');
			if ($data == NULL){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Data");
			}
			$_POST = json_decode($data,true);
			unset($data);
		}
		if ($_POST == NULL){
			$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Data");
		}
	}


	public function send($data){
		/**
		* @info envia los datos al cliente y termina la ejecucion del script con un die
		* @param data[array / string] datos a enviar o un string cuando hay un error
		* @code <?php require_once("./security.php");; $Security = new SECURITY();; $data = new array("hello"=>"world");; $Security->send($data);;?>
		*/
		global $CODE;
		
		if (is_int($data)){
			$m 	  = $this->set_header($data);
			$json = array();
			$json["STATUS"] = $data;
			die(json_encode($json,JSON_NUMERIC_CHECK));
		}else{
			$m = $this->set_header($CODES->OK);
			if (is_array($data)){
				$json = $data;
			}else{
				$json = array();
			}
			$json["STATUS"] = CODES::OK;
			if ($this->Crypt != NULL){
				if ($this->key != -1){// TODO : remover el bypass para ignorar la encryptacion en produccion
					$this->key 	= rand(0,19);
				}
				header("KEY: ".$this->$key);
				header("SHA: ".sha1($json));
				$data = $this->Crypt->encrypt(json_encode($json,JSON_NUMERIC_CHECK), $this->key);
			}else{
				$data = json_encode($json,JSON_NUMERIC_CHECK);
			}
			die($data);
		}
	}

	public function send_OLD($data){
		/**
		* @info envia los datos al cliente y termina la ejecucion del script con un die
		* @param data[array / string] datos a enviar o un string cuando hay un error
		* @code <?php require_once("./security.php");; $Security = new SECURITY();; $data = new array("hello"=>"world");; $Security->send($data);;?>
		*/
		if (is_string($data)){// hubo un error
			$m 	  = $this->set_header(400);
			$json = array();
			$json["STATUS"] = "ERROR";
			$json["INFO"] 	= $data;
			die(json_encode($json,JSON_NUMERIC_CHECK));
		}else{
			$m = $this->set_header(200);
			$json = $data;
			$json["STATUS"] = "OK";
			if (!isset($json["INFO"])){
				$json["INFO"] 	= "Proceso ejecutado";
			}
			if ($this->Crypt != NULL){
				if ($this->key != -1){// TODO : remover el bypass para ignorar la encryptacion en produccion
					$this->key 	= rand(0,19);
				}
				header("KEY: ".$this->$key);
				header("SHA: ".sha1($json));
				$data = $this->Crypt->encrypt(json_encode($json,JSON_NUMERIC_CHECK), $this->key);
			}else{
				$data = json_encode($json,JSON_NUMERIC_CHECK);
			}
			die($data);
		}
	}

}
?>
