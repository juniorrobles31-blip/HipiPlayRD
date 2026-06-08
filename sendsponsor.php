<?php
//PAGAR A LOS PROMOTORES DIARIAMENTE
//------------------------------------------------
// Ejecución solo del Cron
//------------------------------------------------
//if (php_sapi_name() !='cli'){exit;}
//if(!empty($_SERVER['REMOTE_ADDR'])){exit;}
$url='http://localhost/zuzuvama/api/games.php';
$data = array(array(),array());
$method='sponsor';
$i=0;
$fileName=basename(__FILE__, '.php'); 
include_once('../system.php');
include_once('../include/lib/lib.config_time.php');
include_once('../include/lib/functions.php');


$server_time = date("Y-m-d H:i:s",server_time());	
$get_date = new DateTime($server_time);
$get_date->format('Y-m-d H:i:s'); 
$get_date->modify('-1 day');
$get_date=$get_date->format('Y-m-d');

if($stmt = $mysqli->prepare('SELECT `num_trans`, IFNULL((SELECT `id_user_sponsor` FROM `tb_sponsor` WHERE `id_user_gamer` = `id_user`),3) as`id_user_sponsor` FROM `tb_trans` WHERE date(`time`)= ?;')){
	if($stmt->bind_param('s',$get_date)){
		if($stmt->execute()){
			if($stmt->bind_result($numTrans,$idSponsor)){
				while($stmt->fetch()){
					$data[$i]['url']  = $url;
					$data[$i]['post'] = array();
					$data[$i]['post']['method']  = $method;
					$data[$i]['post']['idsponsor'] = $idSponsor;
					$data[$i]['post']['idtrans'] = $numTrans;
					$i++;					
				}
			}
		}
	}$stmt->close();
}

if($i==1){ 
	$result = zzvmWs('sponsor','','',$idSponsor,'','','','',$numTrans);
	$status=$result['status'];
	$msg=$result['msg'];
	$idtrans=$numTrans;
	if($status==0){
		//Envia un correo que no se pudo realizar el pago al cliente
		$error = "Error # ". __LINE__ ." |idtrans:".$numTrans." | Msg:".$msg." | Nota: Pago Promotor NO se realizo en ZUZUVAMA";
		SendEmail($fileName,'Importante Error',$error);	
	}
}elseif($i>=2){
	$result = multiRequestZzvmWs($data);//Webservice para enviar a los ganadores	
	foreach ($result as $key => $value){
		$fdata = json_decode($value);
		$status=$fdata->{'status'};
		$msg=$fdata->{'msg'};
		$idtrans=$fdata->{'idtrans'};
		if($status==0){
			//Envia un correo que no se pudo realizar el pago al cliente
			$error = "Error # ". __LINE__ ." |idtrans:".$numTrans." | Msg:".$msg." | Nota: Pago Promotor NO se realizo en ZUZUVAMA";				
			SendEmail($fileName,'Importante Error',$error);	
		}
	}
}	



?>