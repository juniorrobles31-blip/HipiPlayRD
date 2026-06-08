<?php
	if (defined("TODAY")){return;}
	//------------------------------------------------
	//  Variables de mensajes
	//------------------------------------------------
	define("TODAY", date("Y-m-d"));
	define("FIRSTDAY", '2016-01-01');
	//------------------------------------------------
	//  Variables Web Service - JSON
	//------------------------------------------------
	define("ERROR_EMPTY", 'Hay campos vacios!');
	define("ERROR_ONLY_NUMBER", 'Hay campos que son solo numericos');
	define("ERROR_ONLY_TWO_CURRENCY", 'Solo se permite tener dos (2) tipos de monedas');
	define("ERROR_EMAIL_FOUND", 'Este email ya existe'); 
	define("ERROR_USER_FOUND", 'Este usuario ya existe');
	define("ERROR_RNC_FOUND", 'Este usuario ya existe');
	define("ERROR_COMPANY_FOUND", 'Esta compania ya existe');
	define("ERROR_PHONE_FOUND", 'Este telefono ya existe');
	define("ERROR_DB", 'Error ejecutado el proceso, intentelo de nuevo o contacte al administrador');
	define("ERROR_VAL_EMAIL", 'Validacion de email invalida, verifique de nuevo'); 
	define("ERROR_LOGIN", 'Usuario o clave incorrecta (Si la cuenta fue validada)');
	define("ERROR_NOT_ACCESS_FUNCTIONS", 'NO tiene acceso a esta funcion');
	define("ERROR_METHOD_NOT_FOUND", 'El metodo no existe');
	define("ERROR_URL", 'URL invalido');
	define("ERROR_BALANCE", 'NO tiene balance suficiente para realizar esta transacción'); 
	define("ERROR_TRANS_NOT_FOUND", 'Tipo de transacción NO existe'); 
	define("ERROR_RECHARGE", 'Recarga NO pudo ser realizada'); 
	define("ERROR_REVERSE", 'El reverso NO puede ser realizado por limite de tiempo o la recarga no existe');
	define("ERROR_TIME", 'NO se puede registrar la transaccion por limite de tiempo'); 
	define("ERROR_NO_OPTION", 'NO tiene opciones agregadas');
	define("ERROR_USER_NOT_FOUND", 'NO existe este usuario');
	define("ERROR_SUBTP_COMPANY_NOT_FOUND", 'NO existe este subtipo de compania');
	define("ERROR_PROMO_NOT_FOUND", 'NO existe el promotor');
	define("ERROR_NOT_ACCESS_CARRIER", 'No tiene acceso a esta telefónica');
	define("ERROR_NOT_RESULT", 'NO hay resultados');
	define("ERROR_NOT_FRM_PAY", 'Forma de pago NO relacionada');
	define("ERROR_ROL_NOT_FOUND", 'NO existe el rol');
	define("ERROR_WITHDRAWAL_BLOCKED", 'Este retiro fue bloqueado'); 
	define("ERROR_WITHDRAWAL_PAYED", 'Este retiro ya fue pagado');
	define("ERROR_WITHDRAWAL_NOT_FOUND", 'Este retiro NO existe, verifique el usuario o el codigo');
	define("ERROR_AMOUNT_HIGHER", 'El número NO puede ser mas grande que el permitido'); 
	define("ERROR_HACK", 'Control de ataques');
	define("ERROR_LAST_PASSWORD", 'La clave anterior NO coincide'); 
	define("ERROR_CODE_IMG", 'El valor de la imagen no coincide con el digitado');
	define("ERROR_CODE", 'Codigo invalido');
	define("ERROR_VAL_USER", 'Error validando el registro, intentelo de nuevo!');
	define("PROCESS_EXECUTED", 'Proceso ejecutado exitosamente!');
	define("SIGNIN_EXECUTED", 'Su cuenta ha sido registrada y un enlace ha sido enviado a email. Tenga en cuenta que debe activar la cuenta haciendo click en el enlace de activacion.');

	//------------------------------------------------
	//  Variables de campos
	//------------------------------------------------
	$zzvmUser = "jdd";
	$zzvmPass = "123";//4lph4M4x
	
 	$todayis = date("d-M-Y");
	$error = md5('error');
	$good=md5('good');
	$perPromo=0.25;//Base del porcentaje de los promotores
	
	//------------------------------------------------
	//  Session timeout
	//------------------------------------------------
	$D = 0;
	$H = 0;
	$M = 30;
	$timeout = $D*60*60*24 + $H*60*60 + $M*60;
	
	//------------------------------------------------
	//  Los que deben recibir los correos automaticos
	//------------------------------------------------
	
	$to_mail  = 'ruletadeldinero@gmail.com' . ', '; 
	$to_mail .= 'pedro.santiago.flores@gmail.com';
	
	//------------------------------------------------
	//  Variables de WebService TransWs
	//------------------------------------------------
	
	$TransApuesta 	= 1;
	$TransGanada 	= 2;
	$TransPerdida 	= 3;
	$TransNoValida 	= 4;


	
	
?>
