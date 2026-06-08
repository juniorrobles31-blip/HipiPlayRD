<?php

//die ("allow_url_fopen ".ini_get('allow_url_fopen'));
//------------------------------------------------
// FUNCTION SESSION
//------------------------------------------------
function sec_session_start() {
	if (defined('session')){
		return;	
	}
	define('session',true);

	$session_name = 'game_session'; // Set a custom session name
	$secure = false; // Set to true if using https.
	$httponly = true; // This stops javascript being able to access the session id.  
	ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
	//$cookieParams = session_get_cookie_params(); // Gets current cookies params.
	//session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
	session_name($session_name); // Sets the session name to the one set above.		 
	session_start(); // Start the php session
	//@session_start();
	session_regenerate_id(); // regenerated the session, delete the old one.  
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
}
  
      
//------------------------------------------------
// calcular edad
//------------------------------------------------
function age($fecha_de_nacimiento, $format = true) {
	//$fecha_de_nacimiento = "1983-01-22"; 
	$fecha_actual = date("Y-m-d"); 
	
	// separamos en partes las fechas 
	$form1 = explode ( "-", $fecha_de_nacimiento ); 
	$form2 = explode ( "-", $fecha_actual ); 
	
	$anos = $form2[0] - $form1[0]; // calculamos años 
	$meses = $form2[1] - $form1[1]; // calculamos meses 
	// calculamos los dias 
	$dias1 = mktime ( 0, 0, 0, date ("$form1[1]"), date ("$form1[2]"), date ("$form1[0]") );  
	$dias2 = mktime ( 0, 0, 0, date ("$form2[1]"), date ("$form2[2]"), date ("$form2[0]") );  
	$cuenta_dias = ( $dias2 - $dias1 ) / 86400; 
	
	if ($format){
		$R = $anos." a&ntilde;os";
		if ($meses > 0){
			$R .= " ".$meses." meses";	
		} 
	}else{
		$R = $anos + $meses/12;
	}
	return $R;
}
//------------------------------------------------
// FUNCTION Get the current page
//------------------------------------------------
function curPageName() {
 	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}


function genCodeUnique($size = 8) {
	   // generate a token from an unique value, took from microtime for 8 values
    	$dsCodeUnique = substr(md5(uniqid(rand(),true)),0,$size);		
    	return $dsCodeUnique;
}

//------------------------------------------------
// FUNCTION SendEmail to Administrator
//------------------------------------------------
function SendEmail($page,$subject,$msg){
      if(!empty($page)&&!empty($subject)&&!empty($msg)){
		//INSERTA EL ERROR EN LA BASE DE DATOS
		global $mysqli;	
		if($stmt = $mysqli->prepare('INSERT INTO `tb_error`(`error_desc`, `page`, `time`) VALUES (?,?,SYSDATE());')){
			if($stmt->bind_param('ss',$page,$msg)){$stmt->execute();}$stmt->close();
		}
		//ENVIA EL ERROR EN LA BASE DE DATOS
		$to  = 'p.santiago4@gmail.com' . ', '; 
		$to .= 'mamnarock@gmail.com';					
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
					</table>
					</body></html>';
		$from_mail = 'ruletadeldinero@gmail.com';
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
		$headers .= 'From:'.$from_mail.' '. "\r\n";

		if(mail($to,$subject,$message,$headers)){return true;}else{return false;}
	}else{return false;}	   
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
	

//------------------------------------------------
// FUNCTION WebSservice
//------------------------------------------------
function zzvmWs($metodo,$user,$pass,$iduser,$idcurrency,$amount,$idgame,$noplay,$idtrans){	
	
		//$idtrans=array();
		global $errormetodo;
		global $errorconexion;
		global $empty;
		global $zzvmUser;
		global $zzvmPass;
		
		//$zzvmUser = "jdd";
		//$zzvmPass = "4lph4M4x";
		
		$clave = sha1($zzvmUser.$zzvmPass);		
		$url='https://zuzuvama.com/api/';//'http://localhost/zuzuvama/api/'
		$out['status'] = 0;
		$out['msg'] = 'Error JD(f)# '. __LINE__ .' | '.$empty;
		
		switch($metodo){
			//-------------------------------------	
			case 'login':
			//-------------------------------------
				if(empty($user)||empty($pass)){
					return $out;
				}
				$url=$url.'signin.php';
				$fields = 'method=login&user='.$user.'&pass='.$pass;
				
				//$data 			= loginUser($user,$pass);
				//$out['status'] 	= $data['status'];
				//$out['msg'] 	= $data['msg'];
				
				
				//$out['user']   	= $user;
				//$out['rol']    	= $data['cd_role'];
				//$out['iduser'] 	= $data['id_user'];
				//$out['balance'] = $data['balance'];
				
				//return $out;
			break;
			//-------------------------------------	
			case 'login.token':
			//-------------------------------------
				if(empty($user)||empty($pass)){
					return $out;
				}
				$url=$url.'signin.php';
				$fields = 'method=login_token&iduser='.$user.'&token='.$pass;
			break;
			//-------------------------------------	
			case 'balance':
			//-------------------------------------
				if(empty($iduser)){
					return $out;
				}
				$url=$url.'common.php';
				$fields = 'method=balance&iduser='.$iduser;
				//$data 			= balance($iduser);
				//$out['status'] 	= 1;
				//$out['balance'] = $data['balance'];
				//return $out;
			break;
			//-------------------------------------	
			case 'bet':
			//-------------------------------------					
				if(empty($iduser)||empty($idgame)||empty($amount)){
					return $out;
				}
				$url=$url.'games.php';
				$fields = 'method=bet&iduser='.$iduser.'&idgame='.$idgame.'&noplay='.$noplay.'&amount='.$amount;
				//$out['status'] 	= 1;	
				//return $out;
			break;	
			//-------------------------------------	
			case 'won':  
			//-------------------------------------					
				if(empty($iduser)||empty($idtrans)||empty($amount)){
					return $out;
				}			
				$url=$url.'games.php';
				$fields = 'method=won&idtrans='.$idtrans.'&amount='.$amount;
			break;	
			//-------------------------------------	
			case 'sponsor':  
			//-------------------------------------					
				if(empty($iduser)||empty($idtrans)){
					return $out;
				}			
				$url=$url.'games.php';
				$fields = 'method=won&idsponsor='.$iduser.'&idtrans='.$idtrans;
			break;				
			//-------------------------------------
			default:
			//-------------------------------------
				$out['status'] = 0;
				$out['msg'] = $errormetodo;
				return $out;
			break;	
		}

		//$zzvmUser = "jdd";
		//$zzvmPass = "4lph4M4x";
		$fields .= "&apiuser=".$zzvmUser."&apipass=".$zzvmPass;
		//extract data from the post
		extract($_POST);
		
		$ch = curl_init(); 		
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
		curl_setopt($ch, CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_TIMEOUT,30);		
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
		// set URL and other appropriate options	
		$result=curl_exec($ch);

		curl_close($ch);
		
		if($result === false){
			$out['status'] = 0;
			$out['msg'] = $errorconexion;
			return $out;
		}

		$data = json_decode($result);
		$out['status'] = $data->{'status'};
		$out['msg'] = $data->{'msg'};
		
		if($out['status']==1){
			switch($metodo){
				//-------------------------------------	
				case 'login':
				case 'login.token':
				//-------------------------------------
					$out['user']   = $data->{'user'};
					$out['rol']    = $data->{'rol'};
					$out['iduser'] = $data->{'iduser'};
					$out['balance'] = $data->{'balance'};
				break;
				//-------------------------------------	
				case 'balance':
				//-------------------------------------
					$out['balance'] = $data->{'balance'};
				break;
				//-------------------------------------	
				case 'bet':
				//-------------------------------------
					$out['idtrans'] = $data->{'idtrans'};
				break;	
				//-------------------------------------	
				default:
				//-------------------------------------
				break;	
			}
		}elseif($out['status']==2){
			switch($metodo){
			//-------------------------------------	
			case 'login':
			case 'login.token':
			//-------------------------------------
				$out['iduser'] = $data->{'iduser'};
			break;
			}
		}
		return $out;
 }
 
 
function multiRequestZzvmWs($data,$options=array()){
 
  // array of curl handles
  $curly = array();
  // data to be returned
  $result = array();
 
  // multi handle
  $mh = curl_multi_init();
 
  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach ($data as $id => $d) {
 
    $curly[$id] = curl_init();
 
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL,            $url);
    curl_setopt($curly[$id], CURLOPT_HEADER,         false);
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, true);
 
    // post?
    if (is_array($d)) {
      if (!empty($d['post'])) {
        curl_setopt($curly[$id], CURLOPT_POST,       true);
        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
      }
    }
 
    // extra options?
    if (!empty($options)) {
      curl_setopt_array($curly[$id], $options);
    }
 
    curl_multi_add_handle($mh, $curly[$id]);
  }
 
  // execute the handles
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while($running > 0);
 
 
  // get content and remove handles
  foreach($curly as $id => $c) {
    $result[$id] = curl_multi_getcontent($c);
    curl_multi_remove_handle($mh, $c);
  }
 
  // all done
  curl_multi_close($mh);
 
  return $result;
}
//------------------------------------------------
// FUNCTION Nombre del juego 
//------------------------------------------------

function nameGame($cdGame){
	global $mysqli;
	$out['id'] = 0;
	$out['name'] = "";
 	if($stmt = $mysqli->prepare('SELECT `id_game`,`game` FROM `tb_game` WHERE `cd_game` = ? ;')){
		if($stmt->bind_param('s',$cdGame)){			
			if($stmt->execute()){
				if($stmt->bind_result($id, $game)){ 	
					while($stmt->fetch()){
						$out['id'] = $id;
						$out['name'] = $game;
					}
				}
			}
		}$stmt->close();
	}
	return $out;
}

//------------------------------------------------
// FUNCTION Nombre del juego 
//------------------------------------------------

function insertPromo($idUserSponsor,$idUserGamer){
	global $mysqli;
	global $onlynumber;
	global $todayis;
	global $goodws;
	global $errordb;
	global $existreg;
	global $empty;
	
	if(empty($idUserSponsor)||empty($idUserGamer)){
		$out['mensaje'] = $empty;
		return $out;
	}

	$idUserSponsor = check_input($idUserSponsor);
	$idUserGamer = check_input($idUserGamer);
	$out['codigo'] = 0;
	if(!is_numeric($idUserSponsor)||!is_numeric($idUserGamer)){
		$out['mensaje'] = $onlynumber;
		return $out;
	}
	$endDate = date('Y-m-d', strtotime("+3 months", strtotime($todayis)));
	
	if($stmt = $mysqli->prepare('SELECT `id_user_sponsor` FROM `tb_sponsor` WHERE  `id_user_sponsor`=? AND `id_user_gamer` =?;')){ 
		if($stmt->bind_param('ii',$idUserSponsor,$idUserGamer)){
			if($stmt->execute()){ 
				$stmt->store_result();
				if($stmt->num_rows>=1){
					$out['codigo'] = 0;
					$out['mensaje'] = $existreg;
				}			
			}
		}$stmt->close();
	}

 	if($stmt = $mysqli->prepare('INSERT INTO `tb_sponsor`(`id_user_sponsor`,`id_user_gamer`,`end_date`) VALUES(?,?,?);')){
		if($stmt->bind_param('iis',$idUserSponsor,$idUserGamer,$endDate)){			
			if($stmt->execute()){
				$out['codigo'] = 1;
				$out['mensaje'] = $goodws;
			}
		}$stmt->close();
	}
	$out['mensaje'] = $errordb;
	return $out;
}
//------------------------------------------------
// FUNCTION Porcejate de beneficio
//------------------------------------------------
function PercentageProfit($idTrans,$amount){
	$out=0;
	if($percentage>0&&$percentage<11&&$amount>0){//Por seguridad no permitimos mas de 
		$out = ($percentage / 100) * $amount;  
	}

}
 
//------------------------------------------------
// FUNCTION Resultados Apuestas pendiente
//------------------------------------------------
function pendingResultsBets($game_mode,$jugada) {	
	global $mysqli;	
	$results = array();			
	$json = array();
	
	if(empty($game_mode)){
		$json['STATUS']='ERROR';
		$json['INFO']='Tipo de juego esta vacio';
		return json_encode($json);
	}

	if(!isset($_SESSION['id'])){
		$json['STATUS']='ERROR';
		$json['INFO']='Debe estar logueado';
		return json_encode($json);		
	}
	$data = nameGame($game_mode);
	$gameModeId=$data['id'];

	switch ($game_mode){
		case "dice.1":
			if($stmt = $mysqli->prepare('SELECT `id_trans`, `cd_game`, `nm_play`, `nm_one`, `qty_bet` FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND A.`id_user` = ? AND A.`id_game` = ? AND A.`nm_play` = ? ;')){
				if($stmt->bind_param('iii',$_SESSION['id'],$gameModeId,$jugada)){			
					if($stmt->execute()){
						if($stmt->bind_result($Id,$Cdgame,$NumPlay,$nm_one,$Apuesta)){ 	
							while($stmt->fetch()){
							$results[] = array( "id"   => $NumPlay,
										"value" => $Apuesta,
										"bid" => $nm_one);
							}
						}				
					}
				}$stmt->close();
			}
		break;
		case "roulette":
			if($stmt = $mysqli->prepare('SELECT `id_trans`, `cd_game`, `nm_play`, `nm_one`, `qty_bet` FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND A.`nm_play` = ? ;')){
				if($stmt->bind_param('iii',$_SESSION['id'],$gameModeId,$jugada)){			
					if($stmt->execute()){
						if($stmt->bind_result($Id,$Cdgame,$NumPlay,$nm_one,$Apuesta)){ 		
							while($stmt->fetch()){
								$results[] = array( "id"   => $NumPlay,
										"value" => $Apuesta,
										"bid" => $nm_one);
							}
						}
					}
				}$stmt->close();
			}
		break;
		case "dice.3":
			if($stmt = $mysqli->prepare('SELECT `id_trans`, `cd_game`, `nm_play`, `nm_one`, `qty_bet` FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND A.`nm_play` = ? ;')){
				if($stmt->bind_param('iii',$_SESSION['id'],$gameModeId,$jugada)){			
					if($stmt->execute()){
						if($stmt->bind_result($Id,$Cdgame,$NumPlay,$nm_one,$Apuesta)){ 	
							while($stmt->fetch()){
								$results[] = array( "id"   => $NumPlay,
										"value" => $Apuesta,
										"bid" => $nm_one);
							}
						}
					}
				}$stmt->close();
			}
		break;
		case "dice.2":
		case "horse":
			if($stmt = $mysqli->prepare('SELECT `id_trans`, `cd_game`, `nm_play`, `nm_one`, `nm_two`,`nm_three`, `qty_bet` FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND A.`id_user` = ? AND A.`id_game` = ? AND A.`nm_play` = ? ;')){
				if($stmt->bind_param('iii',$_SESSION['id'],$gameModeId,$jugada)){				
					if($stmt->execute()){
						if($stmt->bind_result($Id,$Cdgame,$NumPlay,$NmOne,$NmTwo,$NmTree,$Apuesta)){ 	
							while($stmt->fetch()){
								$results[] = array( "id"   => $NumPlay,
										"value" => $Apuesta,
										"bid" => $NmOne,
										"bid2" => $NmTwo,
										"bid3" => $NmTree);
							}
						}
					}
				}$stmt->close();
			}
		break;
		case "puntazo":
			if ($stmt = $mysqli->prepare('SELECT `id`,`date` FROM `tb_puntazo` WHERE `owner` = "'.$_SESSION['usuario'].'";')){
				if($stmt->execute()){
					if($stmt->bind_result($id,$date)){
						while($stmt->fetch()){
							$results[] = array( 
									"id"   => $id, 
									"date" => $date);
						}
					}
				}$stmt->close();
			}
		break;
		}
		$json['STATUS']	=	'OK';
		$json['INFO']	=	'gamemode:'.$game_mode.' jugada: '.$jugada;
		$json['results']=	$results;
		return $json;//json_encode($json);
}

function registrar($username, $email, $pass, $idpromo, $name, $lastname, $bday, $idcountry, $phone) {	
	global $mysqli;
	global $goodws;
	global $onlynumber;
	global $errordb;
	global $empty;
	global $erroremail;
	global $existemail;
	global $existuser;

	$json['STATUS']='ERROR';
	
	if(empty($username)||empty($email)||empty($pass)||empty($name)||empty($lastname)||empty($bday)||empty($phone)){
		$json['INFO']= " Error [". __LINE__ . "]: ".$empty;
		return json_encode($json);
	}
	
	if(!is_numeric($phone)||!is_numeric($idcountry)){
		$json['INFO']= " Error [". __LINE__ . "]: ".$onlynumber;
		return json_encode($json);
	}

	$username 	= check_input($username);
	$idcountry 	= check_input($idcountry);
	$email 		= check_input($email);
	$pass 		= check_input(sha1($pass));
	$name 		= check_input(ucwords(strtolower($name)));
	$lastname 	= check_input(ucwords(strtolower($lastname)));
	$bday 		= check_input($bday);
	$phone 		= check_input($phone);
	$promo = NULL;
	if(!empty($idpromo)){$promo = check_input($idpromo);}
		
	// Remove all illegal characters from email
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	// Validate e-mail
	if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
		$json['INFO']= " Error [". __LINE__ . "]: ".$erroremail;
		return json_encode($json);
	}	
		
		if($stmt = $mysqli->prepare('SELECT `id_user` FROM `gms_user` WHERE `email`=?')){
			if($stmt->bind_param('s', $email)){
				if($stmt->execute()){
					if($stmt->store_result()){
						$num_rows = $stmt->num_rows;
						if($num_rows>0){
							$mysqli->close();
							$json['INFO']= " Error [". __LINE__ . "]: ".$existemail;
							return json_encode($json);
						}
					}
				}
			}$stmt->close();
		}
		
		if($stmt = $mysqli->prepare('SELECT `id_user` FROM `gms_user` WHERE `user`=?')){
			if($stmt->bind_param('s', $dsUsr)){
				if($stmt->execute()){
					if($stmt->store_result()){
						$num_rows = $stmt->num_rows;
						if($num_rows>0){
							$mysqli->close();
							$json['INFO']= " Error [". __LINE__ . "]: ".$existuser;
							return json_encode($json);							
						}
					}
				}
			}$stmt->close();
		}
		
		if($stmt = $mysqli->prepare('SELECT `id_user` FROM `gms_user` WHERE `phone`=?')){
			if($stmt->bind_param('i', $dsTel)){
				if($stmt->execute()){
					if($stmt->store_result()){
						$num_rows = $stmt->num_rows;
						if($num_rows>0){
							$mysqli->close();
							$json['INFO']= " Error [". __LINE__ . "]: ".$existtel;
							return json_encode($json);	
						}
					}
				}
			}$stmt->close();
		}
		
		$dsCodeUnique 	= genCodeUnique();
		$idrol			= 3; // Final User
		if($stmt = $mysqli->prepare('INSERT INTO `tb_user`(`id_rol`, `username`, `name`, `last_name`, `email`, `pass`, `bday`, `id_country`, `phone`, `status`,`id_promo`) VALUES (?,?,?,?,?,?,?,?,?,?,?);')){ 
			if($stmt->bind_param('issssssiss',$idrol,$username,$name,$lastname,$email,$pass,$bday,$idcountry,$phone,$dsCodeUnique)){
				if($stmt->execute()){
					$mysqli->close();
					// TODO enviar correo con la generacion del codigo
					$json['STATUS']		= 'OK';
					$json['INFO']		= $goodws;
					$json['cdactivate']	= $dsCodeUnique;
					return json_encode($json);	
				}
			}$stmt->close();
		}
	
		//*** Envio de error ***/
		$errorLine 	= 	'Error Juegos del dinero(f)# '. __LINE__ .'|';
		$msgError 	=	$mysqli->error;
		$page		=	'registrar';
		$subject	=	'Zuzuvama: Error en '.$page;		
		$msg 		= 	$errorLine.$msgError;
	//	SendEmail($page,$subject,$msg);
		
		
		$mysqli->close();
		$json=array('status'=>0,'msg'=>$errorLine.$errordb);
		$json['INFO']= $errorLine.$errordb;
		return json_encode($json);	
}

function balance($iduser){
	global $mysqli;
	global $empty;
	
	$out['balance'] = 0;
	
	if(empty($iduser)){
		$out['mensaje'] = $empty;
		return $out;
	}

	$iduser = check_input($iduser);

	if($stmt = $mysqli->prepare('SELECT IFNULL(`balance`,0) balance FROM `tb_balance` WHERE `id_user` = ?;')){ 
		if($stmt->bind_param('i',$iduser)){
			if($stmt->execute()){ 
				if($stmt->bind_result($balance)){
						while($stmt->fetch()){  $out['balance'] = $balance; }
					}			
			}
		}$stmt->close();
	}
	return $out;
}

function loginUser($user,$pass){
	global $mysqli;
	global $empty;
	global $errorlogin;
	
	$out['status'] = 0;
	
	if(empty($user)||empty($pass)){
		$out['msg'] = $empty;
		return $out;
	}

	$user = check_input($user);
	$pass = check_input(sha1($pass));

	if($stmt = $mysqli->prepare('SELECT `id_user`,`cd_role` FROM `gms_user`A ,`gms_role` B WHERE A.`id_role`=B.`id_role` AND `username` = ? AND `psw_user` = ?;')){ 
		if($stmt->bind_param('ss',$user,$pass)){
			if($stmt->execute()){ 
				if($stmt->bind_result($id_user,$cd_role)){
						if($stmt->fetch()){  
							$out['id_user'] = $id_user; 
							$out['cd_role'] = $cd_role;
							$out['status'] = 1; 
						}
					}			
			}
		}$stmt->close();
	}
	if($out['status']==0){
		$out['msg'] = $errorlogin;
	}
	
	$data = balance($id_user);
	$out['balance']=$data['balance'];
	return $out;
}

function insertBet($user,$pass){
	global $mysqli;
	global $empty;
	global $errorlogin;
	
	$out['status'] = 0;
	
	if(empty($user)||empty($pass)){
		$out['msg'] = $empty;
		return $out;
	}

	$user = check_input($user);
	$pass = check_input(sha1($pass));

	if($stmt = $mysqli->prepare('SELECT `id_user`,`cd_role` FROM `gms_user`A ,`gms_role` B WHERE A.`id_role`=B.`id_role` AND `username` = ? AND `psw_user` = ?;')){ 
		if($stmt->bind_param('ss',$user,$pass)){
			if($stmt->execute()){ 
				if($stmt->bind_result($id_user,$cd_role)){
						if($stmt->fetch()){  
							$out['id_user'] = $id_user; 
							$out['cd_role'] = $cd_role;
							$out['status'] = 1; 
						}
					}			
			}
		}$stmt->close();
	}
	if($out['status']==0){
		$out['msg'] = $errorlogin;
	}
	
	$data = balance($id_user);
	$out['balance']=$data['balance'];
	return $out;
}
?>
