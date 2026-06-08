<?php
//-----------------------------------------------------
// CODES
// info		:	codigos de errores y mensajes
// version 	:	1.0
// date		:	15-12-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
class _code{
	public $code;
	public $info;
	
	public function __construct($code,$info) {
		$this->code = $code;
		$this->info = $info;
	}
}


class CODE{
	public function __construct() {
		//// HTTP Status Codes //////
		$this->OK 															= ['code'=>200,'info'=>"The request has succeeded"];
		$this->CREATED 													= ['code'=>201,'info'=>"The request has been fulfilled and resulted in a new resource being created"];
		$this->BAD_REQUEST 											= ['code'=>400,'info'=>"The request could not be understood by the server due to malformed syntax"];
		$this->UNAUTHORIZED 										= ['code'=>401,'info'=>"The request requires user authentication"];
		$this->FORBIDDEN 												= ['code'=>403,'info'=>"The server understood the request, but is refusing to fulfill it"];
		$this->NOT_FOUND 												= ['code'=>404,'info'=>"The server has not found anything matching the Request"];
		$this->METHOD_NOT_ALLOWED 							= ['code'=>405,'info'=>"The method specified in the Request-Line is not allowed for the resource identified by the Request"];
		$this->NOT_ACCEPTABLE 									= ['code'=>406,'info'=>"The resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headers sent in the request"];
		$this->REQUEST_TIMEOUT 									= ['code'=>408,'info'=>"The client did not produce a request within the time that the server was prepared to wait"];
		$this->NOT_IMPLEMENTED 									= ['code'=>501,'info'=>"The server does not support the functionality required to fulfill the request."];
		$this->SERVICE_UNAVAILABLE 								= ['code'=>503,'info'=>"The server is currently unable to handle the request due to a temporary overloading or maintenance of the server"];
		//// Application MESSAGES //////
		//processing 
		$this->ERROR_DB 												= ['code'=>1000,'info'=>"Error ejecutado el proceso, intentelo de nuevo o contacte al administrador"];
		$this->ERROR_RECHARGE 									= ['code'=>1001,'info'=>"Recarga NO pudo ser realizada"];
		$this->ERROR_REVERSE 										= ['code'=>1002,'info'=>"El reverso NO puede ser realizado por limite de tiempo o la recarga no existe"];
		$this->ERROR_TIME 												= ['code'=>1003,'info'=>"NO se puede registrar la transaccion por limite de tiempo"];
		$this->ERROR_NOT_RESULT 								= ['code'=>1004,'info'=>"NO hay resultados"];
		$this->ERROR_SESSION_EXPIRE 							= ['code'=>1005,'info'=>"La session expiro"];
		//validation 
		$this->ERROR_EMPTY 											= ['code'=>1101,'info'=>"Hay campos vacios"];
		$this->ERROR_ONLY_NUMBER 								= ['code'=>1102,'info'=>"Hay campos que son solo numericos"];
		$this->ERROR_ONLY_TWO_CURRENCY 				= ['code'=>1103,'info'=>"Solo se permite tener dos (2) tipos de monedas"];
		$this->ERROR_EMAIL_FOUND 								= ['code'=>1104,'info'=>"Este email ya existe"];
		$this->ERROR_USER_FOUND 								= ['code'=>1105,'info'=>"Este usuario ya existe"];
		$this->ERROR_COMPANY_FOUND							= ['code'=>1106,'info'=>"Este compania ya existe"];
		$this->ERROR_PHONE_FOUND								= ['code'=>1107,'info'=>"Este telefono ya existe"];
		$this->ERROR_VAL_EMAIL									= ['code'=>1108,'info'=>"Validacion de email invalida, verifique de nuevo"];
		$this->ERROR_LOGIN											= ['code'=>1109,'info'=>"Usuario o clave incorrecta (Si la cuenta fue validada)"];
		$this->ERROR_URL												= ['code'=>1110,'info'=>"URL invalido"];
		$this->ERROR_CODE											= ['code'=>1111,'info'=>"Codigo invalido"];
		$this->ERROR_GAME_TYPE									= ['code'=>1111,'info'=>"Tipo de juego invalido"];
		//validation  - not found
		$this->ERROR_NOT_ACCESS_FUNCTIONS			= ['code'=>1151,'info'=>"NO tiene acceso a esta funcion"];
		$this->ERROR_METHOD_NOT_FOUND 					= ['code'=>1152,'info'=>"El metodo no existe"];
		$this->ERROR_BALANCE 										= ['code'=>1153,'info'=>"NO tiene balance suficiente para realizar esta transacción"];
		$this->ERROR_TRANS_NOT_FOUND 						= ['code'=>1154,'info'=>"Tipo de transacción NO existe"];
		$this->ERROR_NO_OPTION 									= ['code'=>1155,'info'=>"NO tiene opciones agregadas"];
		$this->ERROR_USER_NOT_FOUND 						= ['code'=>1156,'info'=>"NO se ha encontrado el usuario"];
		$this->ERROR_SUBTP_COMPANY_NOT_FOUND 	= ['code'=>1157,'info'=>"NO existe este subtipo de compania"];
		$this->ERROR_PROMO_NOT_FOUND 					= ['code'=>1158,'info'=>"NO existe el promotor"];
		$this->ERROR_NOT_ACCESS_CARRIER 				= ['code'=>1159,'info'=>"NO tiene acceso a esta telefónica"];
		$this->ERROR_NOT_FRM_PAY 								= ['code'=>1160,'info'=>"Forma de pago NO relacionada"];
		$this->ERROR_ROL_NOT_FOUND 							= ['code'=>1161,'info'=>"NO existe el rol"];
		$this->ERROR_AMOUNT_HIGHER 							= ['code'=>1162,'info'=>"El número NO puede ser mas grande que el permitido"];
		$this->ERROR_LAST_PASSWORD 							= ['code'=>1163,'info'=>"La clave anterior NO coincide"];
		$this->ERROR_CODE_IMG 									= ['code'=>1164,'info'=>"El valor de la imagen NO coincide con el digitado"];
		$this->ERROR_NM_NOT_ALLOWED						= ['code'=>1165,'info'=>"Números de apuestas NO permitidos"];
		//Registration validation message
		$this->ERROR_VAL_USER 									= ['code'=>1201,'info'=>"Error validando el registro, intentelo de nuevo!"];
		$this->SIGNIN_EXECUTED 									= ['code'=>1202,'info'=>"Su cuenta ha sido registrada y un enlace ha sido enviado a email. Tenga en cuenta que debe activar la cuenta haciendo click en el enlace de activacion"];
		//Withdrawal messages
		$this->ERROR_WITHDRAWAL_BLOCKED 				= ['code'=>1501,'info'=>"Este retiro fue bloqueado"];
		$this->ERROR_WITHDRAWAL_PAYED 					= ['code'=>1502,'info'=>"Este retiro ya fue pagado"];
		$this->ERROR_WITHDRAWAL_NOT_FOUND 			= ['code'=>1503,'info'=>"Este retiro NO existe, verifique el usuario y/o el codigo"];
		//Hack message
		$this->ERROR_HACK 											= ['code'=>1900,'info'=>"Control de ataques"];
	}
	
	public static function getList(){
		return get_object_vars($this);
	}
	
}

//$code = new CODE();
//echo "Hello: ";
//echo $code->ERROR_HACK["info"];
//echo json_encode(get_object_vars($code));


?>