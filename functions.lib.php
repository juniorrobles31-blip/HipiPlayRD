<?php
function protocol(){
	if (isset($_SERVER['HTTPS']) &&
		($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
		$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	  $protocol = 'https://';
	}else{
	  $protocol = 'http://';
	}
	return $protocol ;
}


//------------------------------------------------
// FUNCTION SESSION
//------------------------------------------------
function sec_session_start() {	
        $session_name = 'zuzuvama_session'; // Set a custom session name
		if (protocol()=="https"){
        	$secure = true; // Set to true if using https.
		}else{
			$secure = false;
		}
        $httponly = true; // This stops javascript being able to access the session id.  
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.		 
		session_start(); // Start the php session
		@session_start();
        session_regenerate_id(); // regenerated the session, delete the old one.  

}
//------------------------------------------------
// FUNCTION FormToken
//------------------------------------------------
function generateFormToken($form) {		    
        // generate a token from an unique value, took from microtime, you can also use salt-values, other crypting methods...
    	$token = md5(uniqid(microtime(), true));  
    	// Write the generated token to the session variable to check it against the hidden field when the form is sent
    	$_SESSION[$form.'_token'] = $token; 
    	return $token;
}


	
 function writeLog($where) {    
    	$ip = getRealIp(); // Get the IP from superglobal
    	$host = gethostbyaddr($ip);    // Try to locate the host of the attack
    	$date = date("d M Y");
    	// create a logging message with php heredoc syntax
    	$logging = <<<LOG
    		\n
    		<< Start of Message >>
    		There was a hacking attempt on your form. \n 
    		Date of Attack: {$date}
    		IP-Adress: {$ip} \n
    		Host of Attacker: {$host}
    		Point of Attack: {$where}
    		<< End of Message >>
LOG;
// Awkward but LOG must be flush left
    
            // open log file
    		if($handle = fopen('hacklog.log', 'a')) {
    		
    			fputs($handle, $logging);  // write the Data to file
    			fclose($handle);           // close the file
    			
    		} else {  // if first method is not working, for example because of wrong file permissions, email the data
    		
    			$to = 'p.santiago4@gmail.com';  
            	$subject = 'HACK ATTEMPT';
            	$header = 'From: p.santiago4@gmail.com';
            	mail($to, $subject, $logging, $header);
    
    		}
 }
  
	
function verifyFormToken($form) {        
        // check if a session is started and a token is transmitted, if not return an error
    	if(!isset($_SESSION[$form.'_token'])) { 
    		return false;
        }    	
    	// check if the form is sent with token in it
    	if(!isset($_POST['token'])) {
    		return false;
        }    	
    	// compare the tokens against each other if they are still the same
    	if ($_SESSION[$form.'_token'] !== $_POST['token']) {
    		return false;
        }    	
    	return true;
 }
 
 
function checkData($mydate) {        
    list($yyyy,$mm,$dd)=explode("-",$mydate); 
    if (is_numeric($yyyy) && is_numeric($mm) && is_numeric($dd)) { 
        return checkdate($mm,$dd,$yyyy); 
    } 
    return false;            
} 


 function genCodeUnique($size = 8) {		    
        // generate a token from an unique value, took from microtime for 8 values
    	$dsCodeUnique = substr(md5(uniqid(rand(),true)),0,$size);		
    	return $dsCodeUnique;
}


function isDefined($key,$empty=true){
	$out['status'] 	= 1;
	if(isset($_POST[$key])){
		if(($empty==true) && empty($_POST[$key])){
			$out['status'] 	= 0;
			$out['msg'] = " Error [". __LINE__ . "]: $key esta vacio";
			return $out;
		}		
		return $out;	
	}else{
		$out['status'] 	= 0;
		$out['msg'] = " Error [". __LINE__ . "]: $key no esta definido";
		return $out;	
	}
}
 
 // etiquetas segun el rol
 function badge_role($role){
	$role_name[0] = "disabled";
	$role_name[1] = "usuario";
	$role_name[2] = "gerente";
	$role_name[3] = "administrador";
	
	$role_color[0] = "#777";// disabled
	$role_color[1] = "#a9d86e";// usuario
	$role_color[2] = "#FCB322";// gerente
	$role_color[3] = "#d9534f";// administrador

	return '<span class="badge" style="background:'.$role_color[$role].'">'.$role_name[$role].'</span>';
}


 function SendEmail($to,$page,$subject,$msg){
      if(!empty($page)&&!empty($subject)&&!empty($msg)){
		$message = 	'<html><body>
					<table rules="all" style="border-color: #666666;" cellpadding="10" >
					<tr>
					<td>Page:</td>
					<td>'.$page.'</td>
					</tr>
					<tr>
					<td>Mensaje:</td>
					<td>'.$msg.'</td>
					</tr>
					<tr>
					<td>IP:</td>
					<td>'.$ip.'</td>
					</tr>
					<tr>
					<td>HOST:</td>
					<td>'.$host.'</td>
					</tr>
					</table>
					</body></html>';
		$from_mail = 'zuzuvama@gmail.com';
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
		$headers .= 'From:'.$from_mail.' '. "\r\n";
		//$to  = 'p.santiago4@gmail.com' ;
		$to  = 'p.santiago4@gmail.com' . ', '; 
		$to .= 'mamnarock@gmail.com';	
		

		if(mail($to,$subject,$message,$headers)){return true;}else{return false;}
	}else{return false;}	   
}
 /*

//------------------------------------------------
// get HTTP Header
//------------------------------------------------
function getHeader($header){
	foreach (getallheaders() as $name => $value) {
		if ($name == $header){
			return $value;	
		}
	}
	return "";
}


function server_time(){
	 return mktime(date("H",$_SERVER['REQUEST_TIME']),
				   date("i",$_SERVER['REQUEST_TIME']),
				   date("s",$_SERVER['REQUEST_TIME']),
				   date("m",$_SERVER['REQUEST_TIME']),
				   date("d",$_SERVER['REQUEST_TIME']),
				   date("Y",$_SERVER['REQUEST_TIME']));
}

//------------------------------------------------
// FUNCTION GetIp
//------------------------------------------------

function getRealIp() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
  
      
//------------------------------------------------
// FUNCTION Check before sql
//------------------------------------------------
 
 function check_input($value){
	 global $mysqli;
// Stripslashes
if(get_magic_quotes_gpc())  {
  $value = stripslashes($value);
  }
// Quote if not a number
if (!is_numeric($value)){
  $value = mysqli_real_escape_string($mysqli,$value);
  }
return $value;

 
 function genCodeUnique($size = 8) {
		    
        // generate a token from an unique value, took from microtime for 8 values
    	$dsCodeUnique = substr(md5(uniqid(rand(),true)),0,$size);		
    	return $dsCodeUnique;
}
 

 function SendEmail($page,$subject,$msg){
      if(!empty($page)&&!empty($subject)&&!empty($msg)){
	$ip = getRealIp(); // Get the IP from superglobal
	
    	$host = gethostbyaddr($ip);    // Try to locate the host of the attack
    	$date = date("d M Y");
    	
    	// create a logging message with php heredoc syntax
    	$logging = <<<LOG
    		\n
    		<< Start of Message >>
    		Error en la pagina:{$page} \n 
    		Date of Attack: {$date}
    		IP-Adress: {$ip} \n
    		Host of Attacker: {$host}
    		Mensaje: {$msg}
    		<< End of Message >>
LOG;
// Awkward but LOG must be flush left
    
            // open log file
		if($handle = fopen('Errorlog.log', 'a')) {		
			fputs($handle, $logging);  // write the Data to file
			fclose($handle);           // close the file	
		}   
		  
		$message = 	'<html><body>
					<table rules="all" style="border-color: #666666;" cellpadding="10" >
					<tr>
					<td>Page:</td>
					<td>'.$page.'</td>
					</tr>
					<tr>
					<td>Mensaje:</td>
					<td>'.$msg.'</td>
					</tr>
					<tr>
					<td>IP:</td>
					<td>'.$ip.'</td>
					</tr>
					<tr>
					<td>HOST:</td>
					<td>'.$host.'</td>
					</tr>
					</table>
					</body></html>';
		$from_mail = 'zuzuvama@gmail.com';
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
		$headers .= 'From:'.$from_mail.' '. "\r\n";
		//$to  = 'p.santiago4@gmail.com' ;
		$to  = 'p.santiago4@gmail.com' . ', '; 
		$to .= 'mamnarock@gmail.com';	
		

		if(mail($to,$subject,$message,$headers)){return true;}else{return false;}
	}else{return false;}	   
}


//------------------------------------------------
// FUNCTION Recarga proveedores
//------------------------------------------------
function IdeaDoWS($metodo, $monto, $telefono ,$telefonica,$IdReverso){		
	
	//$wsdl= 'http://70.38.30.68:8081/Solicitud?wsdl';	//WebSservice en la plataforma de recarga
	$wsdl= 'http://recargasfacil.com:8081/Solicitud?wsdl';
	//ID= 28
	$Usuario='ipga';//Usuario en la plataforma de recarga
	$Clave='1pg@';//Clave en la plataforma de recarga 
	$out['codigo'] = 100;
	
	if(empty($metodo)||empty($Usuario)||empty($Clave)){
		$out['mensaje'] = 'Campos vacios';
		return $out;
	}
	if($metodo=='Saldo'){
		try{			
			$requestParams = array(
				'usuario' => $Usuario,
				'contrasena' => $Clave	 
			);  
			$client = new SoapClient($wsdl, array('features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
			$response = $client->__soapCall('obtenerSaldo', array($requestParams));	
			$resultIdea = $response->return;
			
			$cd= before('-',$resultIdea);
			$out['codigo'] = $cd;
			$out['mensaje']  = '<strong>idea.do: </strong> '.after_last  ('-', $resultIdea);
			return $out;
			
		}catch (Exception $e){ 
			$out['mensaje'] = 'No se pudo conectar a la telefonica, contacte a su administrador!';
			//TODO: Envia correo a los administradores
			//$error = $e->getMessage();
		} 
		return $out;
	}
	if($metodo=='Recargar'){
		if(empty($monto)||empty($telefono)||empty($telefonica)){
			$out['mensaje'] = 'Campos vacios';
			return $out;
		}
		try{
			$requestParams = array(
				'usuario' => $Usuario,
				'contrasena' => $Clave,
				'monto' => $monto,
				'telefono' => $telefono,
				'telefonica' => $telefonica		 
			);  
			$client = new SoapClient($wsdl, array('features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
			$response = $client->__soapCall('recargar', array($requestParams));
			$resultIdea = $response->return;
			
			$cd= before('-',$resultIdea);
			$out['codigo'] = $cd;
			if($cd==0){
				$out['mensaje'] = between_last('-', '-', $resultIdea);
				$out['id']      = after_last('-', $resultIdea);
			}else{
				$out['mensaje']  = after_last  ('-', $resultIdea);
			}
			return $out;
			
		}catch (Exception $e){
			$out['mensaje'] = 'No se pudo conectar a la telefonica, contacte a su administrador!';
			//TODO: Envia correo a los administradores
			//$error = $e->getMessage();
		} 
		return $out;
	}	
	if($metodo=='Reversar'){
		if(empty($IdReverso)){
			$out['mensaje'] = 'Campos vacios';
			return $out;
		}
		try{			
			$requestParams = array(
				'usuario' => $Usuario,
				'contrasena' => $Clave,
				'id' => $IdReverso	 
			);  
			$client = new SoapClient($wsdl, array('features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
			$response = $client->__soapCall('reversar', array($requestParams));			
			$resultIdea = $response->return;
			
			$cd= before('-',$resultIdea);
			$out['codigo'] = $cd;
			$out['mensaje']  = after_last  ('-', $resultIdea);
			return $out;
			
		}catch (Exception $e){ 
			$out['mensaje'] = 'No se pudo conectar a la telefonica, contacte a su administrador!';
			//TODO: Envia correo a los administradores
			//$error = $e->getMessage();
		} 
		return $out;
	}		
        
 }

//------------------------------------------------
// FUNCTION controles zuzuvama 
//------------------------------------------------
function AccesoUsuario($Usuario, $idModulo){
	global $empty;
	global $goodws;
	global $errorwslogin;
	
	if(!isset($Usuario)||empty($idModulo)){
		$out['codigo'] = 0;
		$out['mensaje'] = $empty;
		return $out;
	}
	$idModulo = check_input($idModulo);
	$Usuario = check_input($Usuario);
	global $mysqli;	
	if($stmt = $mysqli->prepare('SELECT C.`cd_role` FROM `tbl_access`A,`tbl_user`B,`tbl_role`C WHERE  A.`id_module`=? AND B.`user` =? AND A.`id_user`=B.`id_user` AND B.`id_role`=C.`id_role`')) { 
		if($stmt->bind_param('is',$idModulo,$Usuario)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$count = $stmt->num_rows;
				if($stmt->bind_result($dsCdrole)){$stmt->fetch();}					
			}
		}$stmt->close();
	}

	if($count==1){
		$out['codigo'] 	= 1;
		$out['mensaje'] = $goodws;
		$out['cdrole'] 	= $dsCdrole;
		return $out;
	}else{
		$out['codigo'] = 0;
		$out['mensaje'] = $errorwslogin;
		return $out;		
	}

}	


//------------------------------------------------
// FUNCTION controles zuzuvama 
//------------------------------------------------
function AccesoUsuarioWs($usuario,$clave){
	global $empty;
	global $mysqli;	
	global $errorwslogin;	
	global $goodws;	
	$count=0;
	
	if(!isset($usuario)||empty($usuario)||!isset($clave)||empty($clave)){
		$out['codigo'] = 0;
		$out['mensaje'] = $empty;
		return $out;
	}
	$usuario = check_input($usuario);
	$clave = check_input(sha1($clave));
	
	if($stmt = $mysqli->prepare('SELECT B.`id_user` FROM `tbl_access`A,`tbl_user`B,`tbl_role`C WHERE B.`status`="A" AND A.`id_module`=7 AND `user`=? AND `psw_user`=? AND A.`id_user`=B.`id_user` AND B.`id_role`=C.`id_role` AND C.`cd_role`="AWS";')) { 
		if($stmt->bind_param('ss',$usuario,$clave)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$count = $stmt->num_rows;
				if($stmt->bind_result($idUser)){$stmt->fetch();}					
			}
		}$stmt->close();
	}
	
	if($count==1){
		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		$out['idusuario'] = $idUser;
		return $out;
	}else{
		$out['codigo'] = 0;
		$out['mensaje'] = $errorwslogin;
		return $out;		
	}
}	

function BalanceUsuario($idUsuario,$idCurrency){
	$count=0;
	$out['codigo'] = 0;
	$out['balance'] = 0;
	$out['mensaje'] = '';
	global $mysqli;	
	global $empty;
	if(empty($idUsuario)){		
		$out['mensaje'] = $empty;
		return $out;
	}
	
	$idUsrAdm = check_input($idUsuario);
	$idUsuario = check_input($idUsuario);
	if(!is_numeric($idUsuario)||!is_numeric($idUsrAdm)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}
	 
	if($stmt = $mysqli->prepare('SELECT B.`cd_role` FROM `tbl_user` A, `tbl_role` B WHERE A.`id_role`=B.`id_role` AND A.`id_user` = ?;')){ 
		if($stmt->bind_param('i',$idUsuario)){
			if($stmt->execute()){if($stmt->bind_result($dsCdrole)){$stmt->fetch();}				
			}
		}$stmt->close();
	}
	
	if($dsCdrole=='POS'){
		if($stmt = $mysqli->prepare('SELECT `id_user_adm` FROM `tbl_hierarchy` WHERE `id_user_pos` = ?;')){ 
			if($stmt->bind_param('i',$idUsuario)){
				if($stmt->execute()){if($stmt->bind_result($idUsrAdm)){$stmt->fetch();}				
				}
			}$stmt->close();
		}
	}

	if($stmt = $mysqli->prepare('SELECT `balance` FROM `tbl_balance` WHERE  `id_user_adm`=? AND `id_currency` =?;')){ 
		if($stmt->bind_param('ii',$idUsrAdm,$idCurrency)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$count = $stmt->num_rows;
				if($stmt->bind_result($balance)){$stmt->fetch();}				
			}
		}$stmt->close();
	}	
	if($count==1){
		$out['codigo'] = 1;
		$out['balance'] = $balance;
		$out['mensaje'] = 'Balance: $'. number_format($balance, 2);
		return $out;
	}else{
		$out['mensaje'] = 'NO se encontro balance';
		return $out;		
	}
}	

function RecargaTelefono($idUsuario,$idTelefonica,$telefono,$monto){
	
	global $empty;
	global $onlynumber;
	global $noaccess;
	global $mysqli;
	global $noaccessphone;	
	global $errorbalance;
	global $idUsrZZVM;
	global $goodws;
	global $errorrecarga;

	/// Los codigos se manejan diferente a otras funciones
	$out['codigo'] = 100;//codigo de error general
	$cdAccess=0;
	
	if(!isset($idUsuario)||!isset($idTelefonica)||!isset($telefono)||!isset($monto)||empty($idUsuario)||empty($idTelefonica)||empty($telefono)||empty($monto)){
		$out['mensaje'] = $empty;
		return $out;
	}
	
	$idUsuario = check_input($idUsuario);
	$idTelefonica = check_input($idTelefonica);
	$telefono = check_input($telefono);	
	$monto = check_input($monto);
	$user="";
	
	if(!is_numeric($idUsuario)||!is_numeric($idTelefonica)||!is_numeric($telefono)||!is_numeric($monto)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}
	////-----------------------------------------------------
	////  CONTROL DE ACCESO A ESTE MODULO
	////-----------------------------------------------------
	if($stmt = $mysqli->prepare("SELECT `user` FROM `tbl_user` WHERE `id_user` = ?")){ 
		if($stmt->bind_param('i',$idUsuario)){
			if($stmt->execute()){ 
				if($stmt->bind_result($user)){$stmt->fetch();}
			}
		}$stmt->close();
	}	
	$cdrolAccess='';
	$idModulo=1;
	$faccess =AccesoUsuario($user,$idModulo);
	$cdAccess=$faccess['codigo'];
	$cdrolAccess=$faccess['cdrole'];

	if($cdAccess==0){
		$out['mensaje'] = $noaccess;
		return $out;
	}

	$sql="";
	if($cdrolAccess=='POS'){
		$sql='SELECT A.`id_carrier_rate`,A.`rate`,C.`max_rate`,C.`max_rate`-A.`rate` AS `diferencia`,B.`id_user_adm`,D.`ds_carrier` FROM `tbl_carrier_rate`A, `tbl_hierarchy` B, `tbl_carrier_provider` C,`tbl_carrier` D WHERE  B.`id_user_pos`=? AND D.`id_carrier`=? AND A.`id_user`=B.`id_user_adm` AND C.`id_carrier_provider`=A.`id_carrier_provider` AND C.`id_carrier`=D.`id_carrier` LIMIT 1;';
	}elseif($cdrolAccess=='USR'){
		$sql='SELECT A.`id_carrier_rate`,A.`rate`,C.`max_rate`,C.`max_rate`-A.`rate` AS `diferencia`,A.`id_user`,D.`ds_carrier` FROM `tbl_carrier_rate`A, `tbl_carrier_provider` C,`tbl_carrier` D WHERE  A.`id_user`=? AND D.`id_carrier`=? AND C.`id_carrier_provider`=A.`id_carrier_provider` AND C.`id_carrier`=D.`id_carrier` LIMIT 1;';
	}	

	$count =0;
	if($stmt = $mysqli->prepare($sql)){ 
		if($stmt->bind_param('ii',$idUsuario,$idTelefonica)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$count = $stmt->num_rows;
				if($stmt->bind_result($idRate,$rate,$maxRate,$difRate,$idUsrAdm,$dsTelefonica)){$stmt->fetch();}
			}
		}$stmt->close();
	}

	if($count==0){
		$out['mensaje'] = $noaccessphone;
		return $out;
	}
	
	$idCurrency = 1;//Moneda pesos dominicanos
	
	$fbalance 	= BalanceUsuario($idUsuario,$idCurrency);
	$codBalance =$fbalance['codigo'];
	$balance	=$fbalance['balance'];
	$msgBalance	=$fbalance['mensaje'];
	
	if($codBalance==0){
		$out['mensaje'] = $msgBalance;
		return $out;
	}
	
	if($balance<$monto){
		$out['mensaje'] = $errorbalance;
		return $out;		
	}

	//Ejecuta el WebSservice del proveedor//
	///$fIdea 	= IdeaDoWS('Recargar',$monto,$telefono,$dsTelefonica,'');
	//$codIdea =$fIdea['codigo'];
	//$msgIdea	=$fIdea['mensaje'];
	//if($codIdea<>0){
	//	$out['mensaje'] = $msgIdea;
	//	return $out;	
	//}
//	$idIdea	=$fIdea['id'];
	
	$idIdea=151;///Parametro para pruebas - ID AU de las telefonicas 
	
	$idClasi=7;//Recarga realizada
	$idModule = 1;//Modulo de la recarga		
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	$monto = $monto*-1;
	if(isset($idUsuario)&&isset($idUsrAdm)&&isset($idClasi)&&isset($idCurrency)&&isset($idModule)&&isset($idRate)&&isset($idIdea)&&isset($monto)&&isset($telefono)){
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_carrier_rate`,`id_recarga`,`amount`,`phone`,`entry_date`) VALUES (?,?,?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiiiss',$idUsuario,$idUsrAdm,$idClasi,$idCurrency,$idModule,$idRate,$idIdea,$monto,$telefono,$TimeOfServer)){$stmt->execute();}$stmt->close();						
		}	
	}
	$IdTrans=$mysqli->insert_id;
	if($IdTrans>=1){
		$montoTrans=$monto*-1;
		$beneficio=$montoTrans*$rate;
		$idValue=11;//Referencia del beneficio de la recarga
		Beneficio($idUsrZZVM,$idUsrAdm,$idModule,$IdTrans,$idValue,$idCurrency,$beneficio,'Función RecargaTelefono');
		
		if($difRate<>0){			
			$beneficio=$montoTrans*$difRate;
			Beneficio($idUsrZZVM,$idUsrZZVM,$idModule,$IdTrans,$idValue,$idCurrency,$beneficio,'Función RecargaTelefono');
		}
	
		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		return $out;		
	}else{
		
		$out['mensaje'] = $errorrecarga;
		return $out;	
		
		//Realizar un reverso si no pudo insertar. Si no puede realizar el reverso tiene que enviar un correo para notificarnos
		$codIdea=0;//Parametro para pruebas
		if($codIdea==0){
			//Ejecuta el WebSservice del proveedor//
			//$fIdeareverso = IdeaDoWS('Reversar','','','',$idIdea);
			//$codIdeareverso =$fIdeareverso['codigo'];
			$codIdeareverso=0;//Parametro para pruebas
			if($codIdeareverso==0){
				$out['mensaje'] = $errorrecarga;
				return $out;				
			}else{
				$errorLine 	= 	"Error Z(f)# ". __LINE__ ;
				$page='Function recargatelefonica';
				$subject='Zuzuvama: No se pudo reversar la recarga';		
				$msg = 'No se inserto el registro, pero se realizo la recarga No.'.$idIdea.' | compania: '.$dsTelefonica.' | tel: '.$telefono.' | monto: '.$monto." | ".$errorLine;
				SendEmail($page,$subject,$msg);
				$out['codigo'] = 1;
				$out['mensaje'] = $goodws;
				return $out;
			}
		}
		
	}
 }
 
 function ReversoTelefono($idUsuario,$idTrans){
	/// Los codigos se manejan diferente a otras funciones
	$out['codigo'] = 100;//codigo de error general
	global $empty;
	global $onlynumber;
	global $noaccess;
	global $mysqli;
	global $errorreverso;
	global $goodws;
	global $errordb;
	global $idUsrZZVM;	
	
	if(!isset($idUsuario)||!isset($idTrans)||empty($idUsuario)||empty($idTrans)){
		$out['mensaje'] = $empty;
		return $out;
	}
	
	if(!is_numeric($idUsuario)||!is_numeric($idTrans)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}	

	$idUsuario = check_input($idUsuario);
	$idTrans = check_input($idTrans);

	////-----------------------------------------------------
	////  CONTROL DE ACCESO A ESTE MODULO
	////-----------------------------------------------------	
	$cdrolAccess='';
	$idModulo=1;
	$faccess =AccesoUsuario($idUsuario,$idModulo);
	$cdAccess=$faccess['codigo'];
	
	if($cdAccess==0){
		$out['mensaje'] = $noaccess;
		return $out;
	}
	if(isset($faccess['cdrole'])){
		if($faccess['cdrole']<>'POS'){
			$out['mensaje'] = $noaccess;
			return $out;
		}
	}

	$cntTrans=0;
	$dtfrom = date("Y-m-d H:i:s",strtotime("-5 minutes",server_time()));
	if($stmt = $mysqli->prepare('SELECT A.`id_user_adm` , A.`id_currency` , A.`id_module` , A.`id_carrier_rate` , A.`id_recarga` , A.`phone` , A.`amount`  FROM `tbl_transaction`A,`tbl_carrier_rate` B, `tbl_carrier_provider` C WHERE A.`id_module` = 1 AND `entry_date` >= "'.$dtfrom.'" AND  A.`id_values` = 7 AND A.`id_user_trans` = ? AND A.`id_carrier_rate`=B.`id_carrier_rate` AND B.`id_carrier_provider`=C.`id_carrier_provider` AND C.`id_carrier` = 1 AND `id_transaction`=? AND A.`id_transaction` NOT IN ( SELECT `id_last_transaction` FROM `tbl_transaction` WHERE `id_values` = 8 AND `id_user_trans` = ? AND `id_last_transaction`=?  AND `entry_date` >= "'.$dtfrom.'")  ORDER BY A.`id_transaction` DESC;')) { 
		if($stmt->bind_param('iiii',$idUsuario,$idTrans,$idUsuario,$idTrans)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$cntTrans = $stmt->num_rows;
				if($stmt->bind_result($idUsrAdm,$idCurrency,$idModulo,$idRate,$idRecarga,$telefono,$monto)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	
	if($cntTrans==0){
		$out['mensaje'] = $errorreverso;
		return $out;
	}
	
	$fIdea 	= IdeaDoWS('Reversar','','','',$idRecarga);
	$codIdea =$fIdea['codigo'];
	$msgIdea	=$fIdea['mensaje'];
	if($codIdea<>0){
		$out['mensaje'] = $msgIdea;
		return $out;	
	}
	
	$idValue=8;//Recarga reversada
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	$monto=$monto*-1;
	
	if(isset($idUsuario)&&isset($idUsrAdm)&&isset($idValue)&&isset($idCurrency)&&isset($idModulo)&&isset($idRate)&&isset($idTrans)&&isset($monto)&&isset($telefono)){
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_carrier_rate`,`id_last_transaction`,`amount`,`phone`,`entry_date`) VALUES (?,?,?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiiiss',$idUsuario,$idUsrAdm,$idValue,$idCurrency,$idModulo,$idRate,$idTrans,$monto,$telefono,$TimeOfServer)){$stmt->execute();}$stmt->close();						
		}	
	}
	$error=$mysqli->error;

	if($mysqli->insert_id>=1){
		$idValueLast=11;//Beneficio recarga
		$idValue=18;//Reverso beneficio recarga
		ReversoBeneficio($idUsrZZVM,$idUsrAdm,$idTrans,$idValueLast,$idValue,'Función ReversoTelefono');

		//Si ZZVM recibio beneficio
		ReversoBeneficio($idUsrZZVM,$idUsrZZVM,$idTrans,$idValueLast,$idValue,'Función ReversoTelefono');

		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		return $out;		
	}else{
		//Realizar un reverso si no pudo insertar. Si no puede realizar el reverso tiene que enviar un correo para notificarnos
		//$codIdea=0;//Parametro para pruebas
		if($codIdea==0){
			$errorLine 	= 	"Error ZR(f)# ". __LINE__ ;
			$page		=	'Function ReversoTelefono';
			$subject	=	'Zuzuvama: No se inserto reversar la recarga';
			$msg 		= 	$errorLine.'| No se inserto el registro, pero se realizo en el proveedor Transaccion No.'.$idTrans;
			SendEmail($page,$subject,$msg);
			$out['codigo'] = 1;
			$out['mensaje'] = $goodws;
			return $out;
			
		}else{
			//---- Envio de error----//
			$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
			$msgError 	=	$mysqli->error;		
			$page		=	'Function ReversoTelefono';
			$subject	=	'Zuzuvama: No se pudo Reversar la recarga con el proveedor';
			$msg 		= 	$errorLine.$msgError.'|No se realizo el reverso de la trans No.'.$idTrans.' | Monto: '.$monto.'| ID para el proveedor'.$idRecarga;
			SendEmail($page,$subject,$msg);

			$out['codigo'] 	= 0;
			$out['mensaje'] = $errorLine.$errordb;
			return $out;	
		}
		
	}
	
 }
 
 //------------------------------------------------
// FUNCTION para las apuestas del juego del dinero
//------------------------------------------------
 
 function JuegoApuestas($idUserTrans,$idUser,$idGame,$noPlay,$amount){
	
	global $empty;
	global $onlynumber;
	global $noaccess;
	global $mysqli;
	global $errorbalance;
	global $idUsrZZVM;
	global $idUsrJDD;
	global $goodws;
	global $errordb;
	
	$idCurrency = 1;//Moneda pesos dominicanos
	$idModule	= 2;//Modulo de juegos del dinero
	$idTrans	= 0;
	$idClasi	= 13;//Recarga realizada	
	$USER 		= "0";

	$out['codigo'] = 0;//codigo de error general

	if(!isset($idUserTrans)||!isset($idUser)||!isset($idGame)||!isset($noPlay)||!isset($amount)||empty($idUserTrans)||empty($idUser)||empty($idGame)||empty($noPlay)||empty($amount)){
		$out['mensaje'] = 'Error Z(f)# '. __LINE__ .'|'.$empty;
		return $out;
	}
	
	$idUserTrans 	= check_input($idUserTrans);
	$idUser 		= check_input($idUser);
	$amount 		= check_input($amount);	
	$idGame		 	= check_input($idGame);
	$noPlay 		= check_input($noPlay);	

	if(!is_numeric($idUserTrans)||!is_numeric($idUser)||!is_numeric($idGame)||!is_numeric($noPlay)||!is_numeric($amount)){
		$out['mensaje'] = 'Error Z(f)# '. __LINE__ .'|'.$onlynumber;
		return $out;
	}
	////-----------------------------------------------------
	////  CONTROL DE ACCESO A ESTE MODULO
	////-----------------------------------------------------	
	if($stmt = $mysqli->prepare('SELECT `user` FROM `tbl_user` WHERE `id_user` = ?')) { 
		if($stmt->bind_param('i',$idUser)){
			if($stmt->execute()){ 
				if($stmt->bind_result($USER)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	

	$cdrolAccess='';
		
	$faccess =AccesoUsuario($USER,$idModule);
	$cdAccess=$faccess['codigo'];
	if($cdAccess==0){
		$out['mensaje'] = $noaccess;
		return $out;
	}

	$fbalance 	= BalanceUsuario($idUser,$idCurrency);
	$codBalance =$fbalance['codigo'];
	$balance	=$fbalance['balance'];
	$msgBalance	=$fbalance['mensaje'];
	
	if($codBalance==0){		
		$out['mensaje'] = $msgBalance;
		return $out;
	}

	if($balance<$amount){
		$out['mensaje'] = $errorbalance;
		return $out;		
	}

	$amount=$amount*-1;	
	
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_game`,`no_play`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?,?);')) {
		if($stmt->bind_param('iiiiiiiss',$idUserTrans,$idUser,$idClasi,$idCurrency,$idModule,$idGame,$noPlay,$amount,$TimeOfServer)){$stmt->execute();}$stmt->close();						
	}	

	$idTrans=$mysqli->insert_id;
	
	if($idTrans>=1){
		$amount=$amount*-1;
		$idValue=24;//Referencia del beneficio por servicio
			
		$beneficio= $amount*0.01;//(1% de la transaccion)
		Beneficio($idUserTrans,$idUsrZZVM,$idModule,$idTrans,$idValue,$idCurrency,$beneficio,'Beneficio de ZZVM');
		
		Beneficio($idUserTrans,$idUsrJDD,$idModule,$idTrans,$idValue,$idCurrency,$beneficio,'Beneficio de JDD');
		//-- Beneficio del POS
		$beneficio= $amount*0.02;//(2% de la transaccion)
			if($stmt = $mysqli->prepare('SELECT `id_user_pos` FROM `tbl_last_debit` WHERE `id_user_adm`=?;')){ 
				if($stmt->bind_param('i',$idUser)){
					if($stmt->execute()){			
						$stmt->store_result();
						$cntTrans = $stmt->num_rows;
						if($stmt->bind_result($idUserPos)){$stmt->fetch();}
					}
				}$stmt->close();
			}
		if($cntTrans==0){
			$idUserPos=1;
		}
		$idValue=23;//Referencia del beneficio por punto de venta
		Beneficio($idUserTrans,$idUserPos,$idModule,$idTrans,$idValue,$idCurrency,$beneficio,'Beneficio de POS');

		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		$out['idtrans'] = $idTrans;
		return $out;		
	}else{
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;		
	$page		=	'Function JuegoApuestas';
	$subject	=	'Zuzuvama: No se pudo ingresar la apuesta en el juego';
	$msg 		= 	$errorLine.$msgError;
	SendEmail($page,$subject,$msg);
	
	$out['mensaje'] = $errorLine.$errordb;
	return $out;

	}
}
 function JuegoGanado($idUserTrans,$idTrans,$amount){
	 
	global $empty;
	global $onlynumber;
	global $noaccess;
	global $mysqli;
	global $goodws;
	global $errordb;
	global $erroridtrans;
	global $errorTime;
	$out['codigo'] = 0;//codigo de error general
	$idClasiBet=13;//Recarga realizada
	$idClasiWon = 14;//Apuesta ganada
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	
	if(!isset($idUserTrans)||!isset($idTrans)||!isset($amount)||empty($idUserTrans)||empty($idTrans)||empty($amount)){
		$out['mensaje'] = $empty;
		return $out;
	}
	
	$idUserTrans 	= check_input($idUserTrans);
	$idTrans 		= check_input($idTrans);
	$amount 		= check_input($amount);

	
	if(!is_numeric($idUserTrans)||!is_numeric($idTrans)||!is_numeric($amount)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_user_adm`,`id_currency`,`id_module`,`id_game`, `no_play`,`entry_date` FROM `tbl_transaction` WHERE `id_transaction`=? AND `id_values` =? AND `id_user_trans`=? ;')){
		if($stmt->bind_param('iii',$idTrans,$idClasiBet,$idUserTrans)){
			if($stmt->execute()){
				if($stmt->store_result()){
					$idtransrow = $stmt->num_rows;
					if($idtransrow==1){
						if($stmt->bind_result($idUser,$idCurrency,$idModule,$idGame,$noPlay,$time)){$stmt->fetch();}
					}else{
						$out['mensaje'] = "Error Z(f)# ". __LINE__ . " | ".$erroridtrans. " | ".$idTrans. " | ".$idClasiBet. " | ".$idUserTrans;
						return $out;
					}
				}
			}
		}$stmt->close();
	}
	
	$time = strtotime($time);
	$difference = server_time()-$time;
	if($difference>300){//Tiempo limite para ingresar al ganador (60segundos*5minutos=300segundos)
		//Envio de error 
		$errorLine 	= 	"Error Z(f)# ". __LINE__ ;
		$page		=	'Function JuegoGanado';
		$subject='Zuzuvama: No se pudo ingresar el juego ganado por limite de tiempo';
		$msg 		= 	'No se inserto la ganancia de la trans No.'.$idTrans.' | Limite de tiempo |'.$errorLine;
		SendEmail($page,$subject,$msg);
	
		$out['mensaje'] = $errorLine.$errorTime;
		return $out;
	}
	
	if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_game`, `no_play`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?,?,?);')){
		if($stmt->bind_param('iiiiiiiiss',$idUserTrans,$idUser,$idClasiWon,$idCurrency,$idModule,$idGame,$noPlay,$idTrans,$amount,$TimeOfServer)){
			if($stmt->execute()){
				$out['codigo'] = 1;
				$out['mensaje'] = $goodws;
				$out['idtrans'] = $idTrans;
				return $out;
			}
		}$stmt->close();
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;	
	$page		=	'Function JuegoGanado';
	$msg 		= 	$errorLine.$msgError.'|No se inserto la ganancia de la trans No.'.$idTrans;
	SendEmail($page,$subject,$msg);
	
	$out['mensaje'] = $errorLine.$errordb;
	return $out;

}


 function JuegoReverso($idUserTrans,$idTrans){
	 
	global $empty;
	global $onlynumber;
	global $noaccess;
	global $mysqli;
	global $goodws;
	global $errordb;
	global $erroridtrans;
	global $errorTime;
	$out['codigo'] = 0;//codigo de error general
	$idClasiOld=13;//Recarga realizada
	$idClasiNew = 15;//Apuesta reversada
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	
	if(!isset($idUserTrans)||!isset($idTrans)||empty($idUserTrans)||empty($idTrans)){
		$out['mensaje'] = $empty;
		return $out;
	}
	
	$idUserTrans 	= check_input($idUserTrans);
	$idTrans 		= check_input($idTrans);
	
	if(!is_numeric($idUserTrans)||!is_numeric($idTrans)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_user_adm`,`id_currency`,`id_module`,`id_game`, `no_play`,`entry_date` FROM `tbl_transaction` WHERE `id_transaction`=? AND `id_values` =? AND `id_user_trans`=? ;')){
		if($stmt->bind_param('iii',$idTrans,$idClasiOld,$idUserTrans)){
			if($stmt->execute()){
				if($stmt->store_result()){
					$idtransrow = $stmt->num_rows;
					if($idtransrow==1){
						if($stmt->bind_result($idUser,$idCurrency,$idModule,$idGame,$noPlay,$time)){$stmt->fetch();}
					}else{
						$out['mensaje'] = $out['mensaje'] = "Error Z(f)# ". __LINE__ . " | ".$erroridtrans;
						return $out;
					}
				}
			}
		}$stmt->close();
	}
	
	$time = strtotime($time);
	$difference = server_time()-$time;
	if($difference>300){//Tiempo limite para ingresar al ganador (60segundos*5minutos=300segundos)
		//Envio de error 
		$errorLine 	= 	"Error Z(f)# ". __LINE__ ;
		$page		=	'Function JuegoGanado';
		$subject='Zuzuvama: No se pudo ingresar el juego ganado por limite de tiempo';
		$msg 		= 	'No se inserto la ganancia de la trans No.'.$idTrans.' | Limite de tiempo |'.$errorLine;
		SendEmail($page,$subject,$msg);
	
		$out['mensaje'] = $errorLine.$errorTime;
		return $out;
	}
	
	
	if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_game`, `no_play`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?,?,?);')){
		if($stmt->bind_param('iiiiiiiiss',$idUserTrans,$idUser,$idClasiNew,$idCurrency,$idModule,$idGame,$noPlay,$idTrans,$amount,$TimeOfServer)){
			if($stmt->execute()){
				$out['codigo'] = 1;
				$out['mensaje'] = $goodws;
				return $out;
			}
		}$stmt->close();
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;	
	$page		=	'Function JuegoGanado';
	$msg 		= 	$errorLine.$msgError.'|No se inserto la ganancia de la trans No.'.$idTrans;
	SendEmail($page,$subject,$msg);
	
	$out['mensaje'] = $errorLine.$errordb;
	return $out;

}
 
 function JuegoPromotor($idUserTrans,$idUser,$idTrans){
	
	global $empty;
	global $onlynumber;
	global $mysqli;
	global $goodws;
	global $errordb;
	global $errorTime;
	
	$idCurrency = 1;//Moneda pesos dominicanos
	$idModule=2;//Modulo de juegos del dinero
	$idClasiBet=13;//Recarga realizada
	$idClasiSpondor = 25;//Beneficio del promotor

	$out['codigo'] = 0;//codigo de error general
	
	if(!isset($idUserTrans)||!isset($idUser)||!isset($idTrans)||empty($idUserTrans)||empty($idUser)||empty($idTrans)){
		$out['mensaje'] = $empty;
		return $out;
	}
	
	$idUserTrans 	= check_input($idUserTrans);
	$idUser 		= check_input($idUser);
	$idTrans 		= check_input($idTrans);	

	if(!is_numeric($idUserTrans)||!is_numeric($idUser)||!is_numeric($idTrans)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}

	if($stmt = $mysqli->prepare('SELECT `id_user_adm`,`id_currency`,`id_module`,`id_game`, `no_play`,`amount`,`entry_date` FROM `tbl_transaction` WHERE `id_transaction`=? AND `id_values` =? AND `id_user_trans`=? ;')){
		if($stmt->bind_param('iii',$idTrans,$idClasiBet,$idUserTrans)){
			if($stmt->execute()){
				if($stmt->store_result()){
					$idtransrow = $stmt->num_rows;
					if($idtransrow==1){
						if($stmt->bind_result($idUser,$idCurrency,$idModule,$idGame,$noPlay,$amount,$time)){$stmt->fetch();}
					}else{
						$out['mensaje'] = $out['mensaje'] = "Error Z(f)# ". __LINE__ . " | ".$erroridtrans;
						return $out;
					}
				}
			}
		}$stmt->close();
	}
	
	$time = strtotime($time);
	$difference = server_time()-$time;
	if($difference>90000){//Tiempo limite para ingresar al ganador (60segundos*1500minutos(25 horas)=90000segundos)
	//---- Envio de error----//
		$errorLine 	= 	"Error Z(f)# ". __LINE__ ."|" ;
		$page		=	'Function JuegoPromotor';
		$subject	=	'Zuzuvama: No se pudo ingresar el beneficio por limite de tiempo';
		$msg 		= 	$errorLine.'No se inserto la ganancia de la trans No.'.$idTrans.' | Limite de tiempo ';
		SendEmail($page,$subject,$msg);
	
		$out['mensaje'] = $errorLine.$errorTime;
		return $out;
	}
	
	$beneficio	= 	($amount*0.01)*-1;//(1% de la transaccion)
	$function 	=	Beneficio($idUserTrans,$idUser,$idModule,$idTrans,$idClasiSpondor,$idCurrency,$beneficio,'Beneficio de Promotor');
	
	$out['codigo'] 	= $function['codigo'];
	$out['mensaje'] = $function['mensaje'];
	return $out;
	
}
 
//------------------------------------------------
// FUNCTION para las ganancias
//------------------------------------------------
 function Beneficio($idUserTrans,$idUser,$idModulo,$idTrans,$idValue,$idMoneda,$monto,$page){
	// idUserTrans - > Responsable de crear la transaccion
	// idUser - > Quien recibe el beneficio
	$dsProfit='N';
	global $mysqli;
	global $idUsrZZVM;	
	global $goodws;	
	global $errordb;
	global $empty;	
	global $onlynumber;	
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	$out['codigo'] = 0;//codigo de error general
	
	if(!isset($idUserTrans)||!isset($idUser)||!isset($idModulo)||!isset($idTrans)||!isset($idValue)||!isset($idMoneda)||!isset($monto)||!isset($page)||empty($idUserTrans)||empty($idUser)||empty($idModulo)||empty($idTrans)||empty($idValue)||empty($idMoneda)||empty($monto)||empty($page)){
		$out['mensaje'] = $empty;
		return $out;
	}
	if(!is_numeric($idUserTrans)||!is_numeric($idUser)||!is_numeric($idModulo)||!is_numeric($idTrans)||!is_numeric($idValue)||!is_numeric($idMoneda)||!is_numeric($monto)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}	
	$fOption = GetOptionUser($idUser);
	if($fOption['codigo']==1){
		$dsProfit=$fOption['profit'];
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_transaction` FROM `tbl_transaction` WHERE `id_transaction`=?;')){ 
		if($stmt->bind_param('i',$idTrans)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$cntTrans = $stmt->num_rows;
			}
		}$stmt->close();
	}
		
	if($cntTrans==0){
		$out['mensaje'] = $out['mensaje'] = "Error Z(f)# ". __LINE__ . " | ".$erroridtrans;
		return $out;		
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_user` FROM `tbl_user` WHERE `id_user`=?;')){ 
		if($stmt->bind_param('i',$idUser)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$cntTrans = $stmt->num_rows;
			}
		}$stmt->close();
	}
		
	if($cntTrans==0){
		$out['mensaje'] = $noexistuser;
		return $out;		
	}
	
	if($dsProfit=='N'){ 
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserTrans,$idUser,$idValue,$idMoneda,$idModulo,$idTrans,$monto,$TimeOfServer)){
				if($stmt->execute()){
					$stmt->close();
					$out['codigo'] = 1;
					$out['mensaje'] = $goodws;
					return $out;
				}$msgError 	=	$mysqli->error;	
			}$stmt->close();						
		}	
	}elseif($dsProfit=='Y'){
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_profit` (`id_user_trans`,`id_user`,`id_values`,`id_currency`,`id_module`,`id_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserTrans,$idUser,$idValue,$idMoneda,$idModulo,$idTrans,$monto,$TimeOfServer)){
				if($stmt->execute()){
					$stmt->close();
					$out['codigo'] = 1;
					$out['mensaje'] = $goodws;
					return $out;
				}
			}$stmt->close();						
		}
	}	
			
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
		
	$page		=	'Function Beneficio';
	$subject	=	'Zuzuvama: No se pudo ingresar el beneficio';
	$msg 		= 	$errorLine.$msgError.'| No se inserto el beneficio de la trans No.'.$idTrans.' | Monto: '.$monto;
	SendEmail($page,$subject,$msg);
	
	$out['codigo'] 	= 0;
	$out['mensaje'] = $errorLine.$errordb;
	return $out;
 }
 
function ReversoBeneficio($idUserTrans,$idUser,$idTransLast,$idValueLast,$idValue,$page){
	// idUserTrans - > Solo el usuario que realizo la transaccion puede reversarla
	// idUser - > De quien recibe el beneficio
	$dsProfit='N';
	global $mysqli;
	global $idUsrZZVM;	
	global $goodws;	
	global $errordb;
	global $empty;	
	global $onlynumber;	
	global $erroridtrans;
	//$idUsuarioTrans=$idUsrZZVM;//Usuario del sistema
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	$out['codigo'] = 0;//codigo de error general	
	
	if(!isset($idUserTrans)||!isset($idUser)||!isset($idTransLast)||!isset($idValueLast)||!isset($idValue)||!isset($page)||empty($idUserTrans)||empty($idUser)||empty($idTransLast)||empty($idValueLast)||empty($idValue)||empty($page)){
		$out['mensaje'] = $empty;
		return $out;
	}
	if(!is_numeric($idUserTrans)||!is_numeric($idUser)||!is_numeric($idTransLast)||!is_numeric($idValueLast)||!is_numeric($idValue)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}	

	$cntTrans=0;

	$fOption = GetOptionUser($idUser);
	if($fOption['codigo']==1){
		$dsProfit=$fOption['profit'];
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_transaction`,`id_currency`,`id_module`,`amount` FROM `tbl_transaction` WHERE `id_last_transaction`=? AND `id_values`= ? AND `id_user_adm`=? AND `id_user_trans` =?;')) { 
		if($stmt->bind_param('iiii',$idTransLast,$idValueLast,$idUser,$idUserTrans)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$cntTrans = $stmt->num_rows;
				if($stmt->bind_result($idTrans,$idMoneda,$idModulo,$monto)){$stmt->fetch();}
			}
		}$stmt->close();
	}
		
	if($cntTrans==0){
		$out['mensaje'] = $erroridtrans;
		return $out;		
	}
			
	if($dsProfit=='N'){ 
		$monto=$monto*-1;
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserTrans,$idUser,$idValue,$idMoneda,$idModulo,$idTrans,$monto,$TimeOfServer)){
				if($stmt->execute()){
						$stmt->close();
						$out['codigo'] = 1;
						$out['mensaje'] = $goodws;
						return $out;
					}
			}$stmt->close();						
		}
	}elseif($dsProfit=='Y'){	
		$monto=$monto*-1;
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_profit` (`id_user_trans`,`id_user`,`id_values`,`id_currency`,`id_module`,`id_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserTrans,$idUser,$idValue,$idMoneda,$idModulo,$idTrans,$monto,$TimeOfServer)){
				if($stmt->execute()){
					$stmt->close();
					$out['codigo'] = 1;
					$out['mensaje'] = $goodws;
					return $out;
				}
			}$stmt->close();						
		}
	}	
	
	
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;		
	$page		=	'Function ReversoBeneficio';
	$subject	=	'Zuzuvama: No se pudo ingresar el reverso beneficio';
	$msg 		= 	$errorLine.$msgError.'|No se inserto el beneficio correspondiente a la transacción No.'.$idTrans.' | Monto: '.$monto;
	SendEmail($page,$subject,$msg);
	
	$out['codigo'] 	= 0;
	$out['mensaje'] = $errorLine.$errordb;
	return $out;
 }
 
 
  function UltimoRecargo($idUserTrans,$idUser){
	// idUserTrans - > Responsable de crear la transaccion
	// idUser - > Quien recibe el balance
	global $mysqli;
	global $goodws;	
	global $errordb;
	global $empty;	
	global $onlynumber;	

	$out['codigo'] = 0;//codigo de error general
	
	if(!isset($idUserTrans)||!isset($idUser)||empty($idUserTrans)||empty($idUser)){
		$out['mensaje'] = $empty;
		return $out;
	}
	if(!is_numeric($idUserTrans)||!is_numeric($idUser)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}	
	
	if($stmt = $mysqli->prepare('SELECT `id_user_pos` FROM `tbl_last_debit` WHERE `id_user_adm`=?;')){ 
		if($stmt->bind_param('i',$idUser)){
			if($stmt->execute()){			
				$stmt->store_result();
				$cntTrans = $stmt->num_rows;
				if($stmt->bind_result($idUserPos)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	if($idUserPos===$idUserTrans){//Si es el mismo punto de venta sal, para una ejecucion mas rapida
		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		return $out;
	}


	if($cntTrans<>0){
		if($stmt = $mysqli->prepare('UPDATE `tbl_last_debit` SET `id_user_pos`= ? WHERE `id_user_adm` = ?;')) {
			if($stmt->bind_param('ii',$idUserTrans,$idUser)){
				if($stmt->execute()){
					$stmt->close();
					$out['codigo'] = 1;
					$out['mensaje'] = $goodws;
					return $out;
				}
			}$stmt->close();						
		}
	}else{
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_last_debit` (`id_user_adm`, `id_user_pos`) VALUES (?,?);')) {
			if($stmt->bind_param('ii',$idUser,$idUserTrans)){
				if($stmt->execute()){
					$stmt->close();
					$out['codigo'] = 1;
					$out['mensaje'] = $goodws;
					return $out;
				}
			}$stmt->close();						
		}
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;		
	$page		=	'Function UltimoRecargo';
	$subject	=	'Zuzuvama: No se pudo ingresar el beneficio';
	$msg 		= 	$errorLine.$msgError.'|No relaciono la ultima recarga con el punto de venta: Idpunto Venta'.$idUserTrans.' | IDUsuario: '.$idUser;
	SendEmail($page,$subject,$msg);
	
	$out['codigo'] 	= 0;
	$out['mensaje'] = $errorLine.$errordb;
	return $out;

 }
 
//------------------------------------------------
// FUNCTION para saber si las ganancias van APARTE
//------------------------------------------------
function GetOptionUser($idUsuario){
	global $empty;
	global $goodws;	
	global $nooption;
	global $onlynumber;
	global $mysqli;
	$dsProfit='N';
	$dsPromotor='N';
	$out['codigo'] = 0;//codigo de error general	
	if(!isset($idUsuario)||empty($idUsuario)){
		$out['mensaje'] = $empty;
		return $out;
	}
	if(!is_numeric($idUsuario)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}	
	$idUsuario = check_input($idUsuario);
	$countOption=0;
	if($stmt = $mysqli->prepare('SELECT `profit`,`token`,`send_email` FROM `tbl_option` WHERE `id_user` = ?;')){  
		if($stmt->bind_param('i',$idUsuario)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$countOption = $stmt->num_rows;
				if($stmt->bind_result($profit,$token,$sendEmail)){$stmt->fetch();}
			}			
		}$stmt->close();
	}	
	if($countOption==0){
		$out['mensaje'] = $nooption;
		return $out;
	}else{
		$out['codigo'] = 1;
		$out['mensaje'] = $goodws;
		$out['profit'] = $profit;
		$out['token'] = $token;
		$out['sendEmail'] = $sendEmail;
		return $out;
	}
}


function SearchUser($search){
	global $empty;
	global $mysqli;
	global $goodws;
	global $noresults;
	if(!isset($search)||empty($search)){
		$json=array('status'=>0,'msg'=>$empty);
		return($json);
	}
	$search = check_input($search);

	$usuarios=array();
	if($stmt = $mysqli->prepare('SELECT `id_user`,`user`,`name`,`last_name`,`phone` FROM `tbl_user` WHERE (`id_user`=? AND `id_role`=5) OR (`phone`=? AND `id_role`=5);')) {
		if($stmt->bind_param('ii',$search,$search)){
			if($stmt->execute()){
				if($stmt->store_result()){
					$Numrows = $stmt->num_rows;
					if($stmt->bind_result($idUser,$dsUser,$dsName,$dsLastname,$phone)){
						while($stmt->fetch()){
							$usuarios[]=array(
											'id'=>$idUser,
											'usuario'=>$dsUser,
											'nombre'=>$dsName,
											'apellido'=>$dsLastname,
											'telefono'=>$phone
											);
						
						}
					}
				}
			}
		}$stmt->close();
	}
	
	if(isset($Numrows)){
		if($Numrows==1){
			$json=array('status'=>1,'msg'=>$goodws,'usuario'=>$usuarios);
			return($json);
		}
	}
	$json=array('status'=>0,'msg'=>$noresults);
	return($json);
}

//------------------------------------------------
// FUNCTION transferencia de balance
//------------------------------------------------
function TransAmount($idUserFrom,$idUserTo,$amount){
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $noexistuser;
	global $errorbalance;
	global $errordb;
	global $noaccess;
	
	if(!isset($idUserFrom)||empty($idUserFrom)||!isset($idUserTo)||empty($idUserTo)||!isset($amount)||empty($amount)){
		$json=array('status'=>0,'msg'=>$empty);
		return($json);
	}
	$idUserFrom = check_input($idUserFrom);
	$idUserTo = check_input($idUserTo);
	$amount = check_input($amount);
	
	if(!is_numeric($idUserFrom)||!is_numeric($idUserTo)||!is_numeric($amount)){
		$json=array('status'=>0,'msg'=>$onlynumber);
		return($json);
	}
	
	if($stmt = $mysqli->prepare('SELECT `id_user` FROM `tbl_user` WHERE `id_user`=? ;')){ 
		if($stmt->bind_param('i',$idUserTo)){
			if($stmt->execute()){ 
				$stmt->store_result();
				if($stmt->num_rows<>1){
					$json=array('status'=>0,'msg'=>$noexistuser);
					return($json);
				}
			}
		}$stmt->close();
	}
	
	$idMoneda=1;
	$fbalance 	= BalanceUsuario($idUserFrom,$idMoneda);
	$codBalance =$fbalance['codigo'];
	$balance	=$fbalance['balance'];
	$msgBalance	=$fbalance['mensaje'];		
	if($codBalance==0){
		$json=array('status'=>0,'msg'=>$msgBalance);
		return($json);
	}
	
	if($balance<$amount){
		$json=array('status'=>0,'msg'=>$errorbalance);
		return($json);
	}
	
	////-----------------------------------------------------
	////  CONTROL DE ACCESO A ESTE MODULO
	////-----------------------------------------------------	
	$cdrolAccess='';
	$idModulo=5;//Modulo de transferencias
	$faccess =AccesoUsuario($idUserFrom,$idModulo);
	$cdrolAccess=$faccess['cdrole'];

	if($cdrolAccess=='ADM'||$cdrolAccess=='ADS'){
		$out['mensaje'] = $noaccess;
		return $out;
	}
	
	if($cdrolAccess=='POS'){
		if($stmt = $mysqli->prepare('SELECT `id_user_adm` FROM `tbl_hierarchy` WHERE `id_user_pos`=? ;')){ 
			if($stmt->bind_param('i',$idUserFrom)){
				if($stmt->execute()){ 
					$stmt->store_result();
					if($stmt->num_rows<>1){
						$json=array('status'=>0,'msg'=>$noexistuser);
						return($json);
					}else{
						if($stmt->bind_result($idUserAdm)){$stmt->fetch();}
					}
				}
			}$stmt->close();
		}
	}else{
		$idUserAdm=$idUserFrom;
	}
	
	$idMoneda=1;
	$idModulo=5;
	$idTpTrans=19;
	$amount=$amount*-1;
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	
	if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?);')) {
		if($stmt->bind_param('iiiiiss',$idUserFrom,$idUserAdm,$idTpTrans,$idMoneda,$idModulo,$amount,$TimeOfServer)){$stmt->execute();}			
	}
	$idLastTrans=$mysqli->insert_id;
	if($idLastTrans>=1){
		$amount=$amount*-1;
		$idTpTrans=20;
		$newBalance = $balance + $amount;// + because the amount is negative
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserFrom,$idUserTo,$idTpTrans,$idMoneda,$idModulo,$idLastTrans,$amount,$TimeOfServer)){
				if($stmt->execute()){
					UltimoRecargo($idUserFrom,$idUserTo);
					$json=array('status'=>1,'msg'=>$goodws,'balance'=>$newBalance);
					return($json);
				}
			}			
		}
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;
	$page		=	'Function TransAmount ';
	$subject	=	'Zuzuvama: Error no pudo realizar una transaccion';		
	$msg 		= 	$errorLine.$msgError;
	SendEmail($page,$subject,$msg);
	
	$json=array('status'=>0,'msg'=>$errorLine.$errordb);
	return($json);
	
}

//------------------------------------------------
// Funcion para solicitar el retiro de dinero
//------------------------------------------------
function WithdrawAmount($idUser,$idMethod,$amount){
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errorbalance;
	global $errordb;
	global $noaccess;
	global $nofrmpag;
	$cdUser='';
	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	
	if(!isset($idUser)||empty($idUser)||!isset($idMethod)||empty($idMethod)||!isset($amount)||empty($amount)){
		$json=array('status'=>0,'msg'=>$empty);
		return($json);
	}
	
	if(!is_numeric($idUser)||!is_numeric($idMethod)||!is_numeric($amount)){
		$json=array('status'=>0,'msg'=>$onlynumber);
		return($json);
	}
	$idUser = check_input($idUser);
	$idMethod = check_input($idMethod);
	$amount = check_input($amount);
	

	if($stmt = $mysqli->prepare('SELECT `cd_role`  FROM `tbl_user`A,`tbl_role` B WHERE A.`id_user`=? AND A.`id_role`=B.`id_role`;')){ 
		if($stmt->bind_param('i',$idUser)){
			if($stmt->execute()){ 
				if($stmt->bind_result($cdUser)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	if(empty($cdUser)||$cdUser=='ADM'||$cdUser=='ADS'||$cdUser=='POS'){
		$json=array('status'=>0,'msg'=>$noaccess);
		return($json);
	}
	
	$idMoneda=1;
	$fbalance 	= BalanceUsuario($idUser,$idMoneda);
	$codBalance =$fbalance['codigo'];
	$balance	=$fbalance['balance'];
	$msgBalance	=$fbalance['mensaje'];		
	if($codBalance==0){
		$json=array('status'=>0,'msg'=>$msgBalance);
		return($json);
	}
	
	if($balance<$amount){
		$json=array('status'=>0,'msg'=>$errorbalance);
		return($json);
	}
	
	switch($idMethod){
		//-------------------------------------
		case 22://Retiro en establecimiento
		//-------------------------------------
		$dsCodeUnique = genCodeUnique();
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_withdraw`(`id_user`,`cd_withdraw`,`amount`,`date`) VALUES (?,?,?,?);')) {
			if($stmt->bind_param('isss',$idUser,$dsCodeUnique,$amount,$TimeOfServer)){
				if($stmt->execute()){
					//Enviar correo al usuario con el codigo
					$json=array('status'=>1,'msg'=>$goodws,'codigo'=>$dsCodeUnique);
					return($json);
				}
			}			
		}
		$mysqli->error;
		break;		
		//-------------------------------------
		default:  //Si no encuentra el tipo de retiro
		//-------------------------------------
			$json=array('status'=>0,'msg'=>$nofrmpag);
			echo json_encode($json);
			die;
		break;
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;
	$page		=	'Function WithdrawAmount ';
	$subject	=	'Zuzuvama: Error no pudo realizar una transaccion';		
	$msg 		= 	$errorLine.$msgError;
	SendEmail($page,$subject,$msg);
	
	$json=array('status'=>0,'msg'=>$errorLine.$errordb);
	return($json);	
}

//------------------------------------------------
// Formas de pagos 
//------------------------------------------------
function PaymentMethods(){
	global $mysqli;
	global $goodws;
	global $errordb;
	global $noresults;
	$frm=array();
	
	if($stmt = $mysqli->prepare('SELECT `id_values`, `ds_values` FROM `tbl_values` WHERE `id_tp_values`=8;')){ 
		if($stmt->execute()){ 
			$stmt->store_result();
			$numRows=$stmt->num_rows;
			if($stmt->bind_result($id,$ds)){
				while($stmt->fetch()){
				$frm[]=array(
							'id'=>$id,
							'forma'=>$ds
							);
				}
			}
		}$stmt->close();
	}
	
	
	if(isset($numRows)){
		if($numRows>=1){
			$json=array('status'=>1,'msg'=>$goodws,'formas'=>$frm);
			return($json);
		}
	}
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;
	$page		=	'Function PaymentMethods ';
	$subject	=	'Zuzuvama: Error en la funcion PaymentMethods';		
	$msg 		= 	$errorLine.$msgError;
	SendEmail($page,$subject,$msg);
	
	$json=array('status'=>0,'msg'=>$errorLine.$errordb);
	return($json);	
}


//------------------------------------------------
// Funcion para realizar el retiro en establecimiento
//------------------------------------------------
function SearchWithdraw($idUser,$code){
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $retirobloqueado;
	global $retiropagado;
	global $retironofnd;
	
	if(!isset($idUser)||empty($idUser)||!isset($code)||empty($code)){
		$json=array('status'=>0,'msg'=>$empty);
		return($json);
	}
	
	if(!is_numeric($idUser)){
		$json=array('status'=>0,'msg'=>$onlynumber);
		return($json);
	}
	
	$idUser = check_input($idUser);
	$code = check_input($code);
	$numRows=0;
	if($stmt = $mysqli->prepare('SELECT `id_withdraw`,`status`,`amount` FROM `tbl_withdraw` WHERE `id_user`=? AND `cd_withdraw`=?')){ 
		if($stmt->bind_param('is',$idUser,$code)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$numRows=$stmt->num_rows;
				if($stmt->bind_result($idWithdraw,$dsStatus,$amount)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	if($numRows<>1){		
		$json=array('status'=>0,'msg'=>$retironofnd);
		return($json);
	}
	if($dsStatus=='B'){
		$json=array('status'=>0,'msg'=>$retirobloqueado);
		return($json);
	}
	if($dsStatus=='P'){
		$json=array('status'=>0,'msg'=>$retiropagado);
		return($json);
	}
	

	//Enviar correo al usuario con el codigo
	$json=array('status'=>1,'msg'=>$goodws,'amount'=>$amount);
	return($json);

}

//------------------------------------------------
// Funcion para realizar el retiro en establecimiento
//------------------------------------------------
function PayWithdraw($idUserTrans,$idUser,$code){
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errordb;
	global $noaccess;
	global $retirobloqueado;
	global $retiropagado;
	global $retironofnd;


	$TimeOfServer = date("Y-m-d H:i:s",server_time());
	
	if(!isset($idUserTrans)||empty($idUserTrans)||!isset($idUser)||empty($idUser)||!isset($code)||empty($code)){
		$json=array('status'=>0,'msg'=>$empty);
		return($json);
	}
	
	if(!is_numeric($idUserTrans)||!is_numeric($idUser)){
		$json=array('status'=>0,'msg'=>$onlynumber);
		return($json);
	}
	$idUserTrans = check_input($idUserTrans);
	$idUser = check_input($idUser);
	$code = check_input($code);
	
	////-----------------------------------------------------
	////  CONTROL DE ACCESO A ESTE MODULO
	////-----------------------------------------------------	
	$cdrolAccess='';
	$cdAccess=0;
	$idModulo=6;
	$faccess =AccesoUsuario($idUserTrans,$idModulo);
	$cdAccess=$faccess['codigo'];
	$cdrolAccess=$faccess['cdrole'];

	if($cdrolAccess<>'POS'||$cdAccess==0){
		$out['mensaje'] = $noaccess;
		return $out;
	}
	
	
	if($stmt = $mysqli->prepare('SELECT `id_withdraw`,`status`,`amount` FROM `tbl_withdraw` WHERE `id_user`=? AND `cd_withdraw`=?')){ 
		if($stmt->bind_param('is',$idUser,$code)){
			if($stmt->execute()){ 
				$stmt->store_result();
				$numRows=$stmt->num_rows;
				if($stmt->bind_result($idWithdraw,$dsStatus,$amount)){$stmt->fetch();}
			}
		}$stmt->close();
	}
	if($numRows<>1){		
		$json=array('status'=>0,'msg'=>$retironofnd);
		return($json);
	}
	if($dsStatus=='B'){
		$json=array('status'=>0,'msg'=>$retirobloqueado);
		return($json);
	}
	if($dsStatus=='P'){
		$json=array('status'=>0,'msg'=>$retiropagado);
		return($json);
	}
	
		
	$amount=$amount*-1;
	$idTpTrans=22;//Retiro en establecimiento
	$idMoneda=1;//Pesos dominicanos
	
	if($stmt = $mysqli->prepare('UPDATE `tbl_withdraw` SET `status`="P" WHERE `id_withdraw`=? ;')){
		if($stmt->bind_param('i',$idWithdraw)){$stmt->execute();}			
	}
	
	
	

	if($mysqli->affected_rows==1){

		$idMoneda=1;
		$balance=0;
		$fbalance 	= BalanceUsuario($idUserTrans,$idMoneda);
		$balance	=$fbalance['balance'];	
	
		if($stmt = $mysqli->prepare('INSERT INTO `tbl_transaction` (`id_user_trans`,`id_user_adm`,`id_values`,`id_currency`,`id_module`,`id_last_transaction`,`amount`,`entry_date`) VALUES (?,?,?,?,?,?,?,?);')) {
			if($stmt->bind_param('iiiiiiss',$idUserTrans,$idUser,$idTpTrans,$idMoneda,$idModulo,$idLastTrans,$amount,$TimeOfServer)){
				if($stmt->execute()){
					$stmt->close();	
					//Enviar correo al usuario con el codigo
					$json=array('status'=>1,'msg'=>$goodws,'balance'=>$balance);
					return($json);
				}
			}
		}$stmt->close();		
	}
	
	//---- Envio de error----//
	$errorLine 	= 	'Error Z(f)# '. __LINE__ .'|';
	$msgError 	=	$mysqli->error;
	$page		=	'Function PayWithdraw ';
	$subject	=	'Zuzuvama: Error no pudo realizar una transaccion';		
	$msg 		= 	$errorLine.$msgError;
	SendEmail($page,$subject,$msg);
	
	$json=array('status'=>0,'msg'=>$errorLine.$errordb);
	return($json);
	
}
 
//------------------------------------------------
// FUNCTION para buscar dentro de un string
//------------------------------------------------
 
 function after ($this, $inthat){
        if (!is_bool(strpos($inthat, $this)))
        return substr($inthat, strpos($inthat,$this)+strlen($this));
    };

    function after_last ($this, $inthat){
        if (!is_bool(strrevpos($inthat, $this)))
        return substr($inthat, strrevpos($inthat, $this)+strlen($this));
    };

    function before ($this, $inthat){
        return substr($inthat, 0, strpos($inthat, $this));
    };

    function before_last ($this, $inthat){
        return substr($inthat, 0, strrevpos($inthat, $this));
    };

    function between ($this, $that, $inthat){
        return before ($that, after($this, $inthat));
    };

    function between_last ($this, $that, $inthat){
     return after_last($this, before_last($that, $inthat));
    };

// use strrevpos function in case your php version does not include it
function strrevpos($instr, $needle){
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
};



//------------------------------------------------
// Agregar Compania
//------------------------------------------------
function companyADD($company, $url, $address, $id_country, $work_phone){	
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errordb;

	$out['status'] 	= 0;
	$id_company		= 0;
	
	if(empty($company)||empty($url)||empty($address)||empty($id_country)||empty($work_phone)){
		$out['msg'] = " Error [". __LINE__ . "]: ".$empty;
		return $out;
	}
	
	if(!is_numeric($id_country)||!is_numeric($work_phone)){
		$out['msg'] = " Error [". __LINE__ . "]: ".$onlynumber;
		return $out;
	}
	$lastId     = 0;
	$company	= check_input($company);
	$url		= check_input($url);
	$address	= check_input($address);
	$id_country	= check_input($id_country);
	$work_phone	= check_input($work_phone);
	$token 		= check_input(genCodeUnique());

	if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)) {
		$out['msg'] = " Error [". __LINE__ . "]: ".$errorurl;
		return $out;
	}

	if($stmt = $mysqli->prepare('INSERT INTO `tbl_company`(`company`, `url`, `address`, `id_country`, `work_phone`, `token`) VALUES (?,?,?,?,?,?);')) {
		if($stmt->bind_param('sssiis',$company,$url,$address,$id_country,$work_phone,$token)){
			$stmt->execute();
		}$stmt->close();						
	}
	
	$id_company = $mysqli->insert_id;
	if($id_company>0){
		$out['status'] 	= 1;
		$out['id'] 		= $id_company;
		$out['token'] 	= $token;
		$out['msg'] 	= $goodws;
		return $out;
	}else{
		$out['msg'] = " Error [". __LINE__ . "]: ".$errordb;
		return $out;
	}
}



//------------------------------------------------
// Agregar Usuarios
//------------------------------------------------
function userADD($id_role, $id_company, $id_country, $address, $username, $name, $lastname, $email, $phone, $password){	
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errordb;

	$out['status'] 	= 0;
	$lastId			= 0;
	
	if(empty($id_role)||empty($id_company)||empty($id_country)||empty($address)||empty($username)||empty($name)||empty($lastname)||empty($email)||empty($phone)||empty($password)){
		$out['msg'] = " Error [". __LINE__ . "]: ".$empty;
		return $out;
	}
	
	if(!is_numeric($id_role)||!is_numeric($id_country)||!is_numeric($phone)){
		$out['msg'] = " Error [". __LINE__ . "]: ".$onlynumber;
		return $out;
	}

	$id_company	= check_input($id_company);
	$id_country	= check_input($id_country);
	$address	= check_input($address);
	$phone		= check_input($phone);
	$password	= check_input(sha1($password));
	$email		= check_input($email);
	$username	= check_input(strtolower(trim($username)));
	$name		= check_input(ucwords(strtolower(trim($name))));
	$lastname	= check_input(ucwords(strtolower(trim($lastname))));
	$token 		= check_input(genCodeUnique());
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$out['msg'] = " Error [". __LINE__ . "]: ".$erroremail;
		return $out;
	}
	
	if($stmt = $mysqli->prepare('INSERT INTO `tbl_user`( `id_role`, `id_country`, `id_company`, `user`, `name`, `last_name`, `email`, `phone`, `address`, `psw_user`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?,?);')) {
		if($stmt->bind_param('iiissssssss',$id_role,$id_country,$id_company,$username,$name,$lastname,$email,$phone,$address,$password,$token)){$stmt->execute();}$stmt->close();						
	}
	$lastId = $mysqli->insert_id;
	if($lastId>0){
		$out['status'] 	= 1;
		$out['id'] 		= $lastId;
		$out['token'] 	= $token;
		$out['msg'] 	= $goodws;
		return $out;
	}else{
		$out['msg'] = " Error [". __LINE__ . "]: ".$errordb;
		return $out;
	}
}

function registrationADD($company, $url, $address, $id_country, $work_phone, $username, $name, $lastname, $email, $phone, $password){	
	global $empty;
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errordb;
	
	$keyC	= companyADD($company, $url, $address, $id_country, $work_phone);
	
	if($keyC['status']==0){
		$out['status'] 	= $keyC['status'];
		$out['msg'] 	= $keyC['msg'];
		return $out;
	}
	$id_company	= $keyC['id'];
	$tokenC		= $keyC['token'];

	$roleid = 8; //Administrador de la cuenta

	$keyU = userADD($roleid, $id_company, $id_country, $address, $username, $name, $lastname, $email, $phone, $password);
	if($keyU['status']==0){
		if($stmt = $mysqli->prepare('DELETE FROM `tbl_company` WHERE `id_company` = ? ;')) {
			if($stmt->bind_param('i',$id_company)){$stmt->execute();}$stmt->close();						
		}
		$out['status'] 	= $keyU['status'];
		$out['msg'] 	= $keyU['msg'];
		return $out;
	}
	
	$tokenU	= $keyU['token'];
	
	//Enviar correo al usuario para validar la informacion
	$emailmsg 	= "https://zuzuvama.com/valcompany.php?email=".$email."&val=".$tokenU."&token=".$tokenC;
	
	$out['status'] 	= 1;
	$out['msg'] 	= "Registro completado. Verifique su email ('.$email.') para validar su información. Gracias! [".$emailmsg."]";
	return $out;
		
}


function getBalance($userid){
	$balance = 0;
	$p_balance = 0;
	global $DATABASE;
	global $CONFIG;
	//$TimeOfServer = date("Y-m-d H:i:s",server_time());
	//$date 	= new DateTime($CONFIG["SERVER_TIME"]);
	$today 	= $date->format('Y-m-d');
	$date->sub(new DateInterval('P1D'));
	$yesterday = $date->format('Y-m-d');	

	$sql = "SELECT `balance` FROM `t_balance` WHERE `user_id` = $userid AND `date` = '$yesterday'";
	$result = SQL($sql);
	if ($row = $result->fetch_array(MYSQLI_ASSOC)){
		$p_balance = $row["balance"];
	}
	$result->free();
	
	$sql = "SELECT sum(`amount`) as amount FROM `t_payment` WHERE `user_id` = $userid AND DATE_FORMAT(`created_date`,'%Y-%m-%d') = '$today';";
	$result = SQL($sql);
	if ($row = $result->fetch_array(MYSQLI_ASSOC)){
		$trans = $row["amount"];
	}
	$result->free();
	$balance = $p_balance+$trans;
	return number_format($balance,2,".",","); 
}
		
		
//------------------------------------------------
// EJEMPLOS
//------------------------------------------------
//after ('@', 'biohazard@online.ge');
//returns 'online.ge'
//from the first occurrence of '@'

//before ('@', 'biohazard@online.ge');
//returns 'biohazard'
//from the first occurrence of '@'

//between ('@', '.', 'biohazard@online.ge');
//returns 'online'
//from the first occurrence of '@'

//after_last ('[', 'sin[90]*cos[180]');
//returns '180]'
//from the last occurrence of '['

//before_last ('[', 'sin[90]*cos[180]');
//returns 'sin[90]*cos['
//from the last occurrence of '['

//between_last ('[', ']', 'sin[90]*cos[180]');
//returns '180'
//from the last occurrence of '[' 		
*/
 
 ?>
