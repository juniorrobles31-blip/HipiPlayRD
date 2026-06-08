<?php
/************************************************
class.transaction.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
if (!defined('ROOT')){
	define('ROOT','./include/');
}
class TRANSACTION {

	private  $db;
	private  $balance;
	private	 $trans;
	private	 $result;
	private	 $currency;
	private	 $tblTransaction;
	private	 $tblBalance;
	private	 $tblGame;
	private	 $tblConfig;
	private	 $tblUser;
	private	 $tblTimer;
	private  $code;
	private  $type;
	private  $server_time;
	private  $yesterday;
	private  $day;


	public function __construct() {
		require_once(ROOT."class/dbconfig.php");
		require_once(ROOT."class/time.php");
		$this->db = new DB();
		$exe 				= new ROLL();
		$trans 				= $exe->roll("dice.1");
		$this->day 			= date("Y-m-d",$trans->server_time);
		$this->server_time 	= date("Y-m-d H:i:s",$trans->server_time);
		$this->yesterday    = date('Y-m-d', strtotime($this->server_time. ' - 1 day'));

		$this->balance 			= 0;
		$this->trans			= 0;
		$this->result 			= array();
		$this->tblConfig		= "`gms_config`";
		$this->tblTransaction	= "`gms_transaction`";
		$this->tblTimer			= "`gms_timer`";
		$this->tblUser			= "`gms_user`";
		$this->tblBalance		= "`gms_balance`";
		$this->tblGame			= "`gms_game`";

		//To return codes
		require_once(ROOT."class/codes.php");
		$this->code = new CODE();
		if (defined('API-JDD')){$this->type = "code";}else{$this->type="info";}

	}
	
	
	private function _chkbalance($userid){
		$data = $this->db->select('SELECT `balance`, `date`  FROM `gms_balance` WHERE `id_user` = ?;', array($userid),array('%i'));
		if ($data['count']==0){
			//Genera un nuevo balance
			$id = $this->db->insert("gms_balance",array("`id_user`" => $userid,"`balance`" => "0", "`date`"=>$this->yesterday), array('%i','%s','%s'));
			return true;
		}
	   $last_balance = $data[0]->balance;
	   $last_date 	 = $data[0]->date;

	   if($last_date==$this->yesterday){return true;}//Ya se hizo el proceso

	   $data = $this->db->select('SELECT IFNULL(SUM(`amount`),0) as trans FROM `gms_transaction` WHERE `entry_date` > ? AND `id_user` = ?;', array($last_date,$userid),array('%i','%i'));

	   $balance = $data[0]->trans+$last_balance;
	   $id = $this->db->update("gms_balance",array("`balance`" => $balance, "`date`"=>$this->yesterday), array('%s','%s'),array("`id_user`" => $userid),array('%i'));
	  return true;
   }

	public function getBalance($idUser,$format=true){
		$this->_chkbalance($idUser);//TODO: Eliminar cuando tenga las event		
		$data = $this->db->select('SELECT IFNULL(SUM(`amount`),0) as balance FROM '. $this->tblTransaction .' WHERE `id_user` = ?  AND DATE(`entry_date`) = ?;',array($idUser, $this->day),array('%i','%s'));
		if($data['count']==1){
			$this->trans = $data[0]->balance;
		}else{
			$this->trans = 0;
		}
		
		$data = $this->db->select('SELECT IFNULL(`balance`,0) as balance FROM '. $this->tblBalance .' WHERE `id_user` = ? AND `date` =  ?;',array($idUser, $this->yesterday),array('%i','%s'));

		if($data['count']==1){
			$this->balance = $data[0]->balance;
		}else{
			$this->balance = 0;
		}

		$this->balance = $this->balance+$this->trans;

		if($format==true){
			$this->balance=number_format(($this->balance),2,".",",");
		}
		return $this->balance;
	}


	public function doWithdraw($idUserReg,$idTpTrans,$idUser,$code){
	  if(empty($idUserReg)||empty($idTpTrans)||empty($idUser)||empty($code)){  throw new Exception($this->code->ERROR_EMPTY[$this->type]);}

	  if(!is_numeric($idUserReg)||!is_numeric($idTpTrans)||!is_numeric($idUser)){ throw new Exception($this->code->ERROR_ONLY_NUMBER[$this->type]); }


	  $data = $this->db->select('SELECT `id_withdraw`,`status`,`amount` FROM `tbl_withdraw` WHERE `id_user`=? AND `cd_withdraw`=?;',array($idUser,$code),array('%i','%s'));
	  if($data['count']!=1){ throw new Exception($this->code->ERROR_WITHDRAWAL_NOT_FOUND[$this->type]);}
	  switch($data[0]->status){
	    case 'B':
	     throw new Exception($this->code->ERROR_WITHDRAWAL_BLOCKED[$this->type]);
	    break;
	    case 'P':
	     throw new Exception($this->code->ERROR_WITHDRAWAL_PAYED[$this->type]);
	    break;
	  }
	  $idWithdraw	= $data[0]->id_withdraw;
	  $amount			= $data[0]->amount;
	  if($balance<$amount){ throw new Exception($this->code->ERROR_BALANCE[$this->type]); }

	  //$idTpTrans=22;//Retiro en establecimiento
	  $status    = 'P';
	  $id_game   = 0;

	  $id = $this->db->update(`tbl_withdraw`, array("`status`" => $status),array('%s'), array("`id_withdraw`" => $idWithdraw),array('%i'));
	  if(empty($id)){throw new Exception($this->code->ERROR_DB[$this->type]);}

	  $id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser, "`id_game`" => $id_game,"`withdraw`" => $idWithdraw,"`amount`" => $amount*-1, "`id_values`" => $idTpTrans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%s','%i','%s'));
	  if(empty($id)){ throw new Exception($this->code->ERROR_DB[$this->type]); }
	  return true;
	}

	 public function doBet($idUserReg,$idUser,$amount,$game,$number1,$number2,$number3){
	 	global $LANG;
		if(empty($idUserReg)||empty($idUser)||empty($amount)){  throw new Exception($this->code->ERROR_EMPTY[$this->type]);}		 

		if(!is_numeric($idUserReg)||!is_numeric($idUser)||!is_numeric($amount)||$amount<1){ throw new Exception($this->code->ERROR_ONLY_NUMBER[$this->type]); }

		$data = $this->db->select('SELECT `id_user` FROM '. $this->tblUser .' WHERE `id_user` = ?;',array($idUser),array('%i'));
		if($data['count']!=1){ throw new Exception( "[No. ". __LINE__ ."].- Usuario NO existe"); }
		require_once('./include/class/time.php');
		$exe 		= new ROLL();
		$NEXT_PLAY 	= $exe->nextPlay($game);
		$trans 		= $exe->roll($game);

		if($trans->difDate <=10){
			throw new Exception( "[No. ". __LINE__ ."].- ".$LANG["error.draw"]);
		}
		$balance = $this->getBalance($idUser,false);

		if($balance<$amount){
			throw new Exception( "[No. ". __LINE__ ."].- ".$LANG["error.balance"]);
		}
	
		$id_values 	 = 13;//Apuesta realizada
		//$amount     = $amount*-1;

		// Replace cd_game with id_game
		$data = $this->db->select('SELECT `id_game` FROM '. $this->tblGame .' WHERE `cd_game` = ?;',array($game),array('%s'));
		if($data['count']!=1){ throw new Exception($this->code->ERROR_GAME_TYPE[$this->type]); }
		$id_game = $data[0]->id_game;

		switch ($game){
			case "dice.1":
			case "dice.3":
				if($number1<=0 || $number1>6){
					throw new Exception( "[No. ". __LINE__ ."].-  Números de apuestas NO permitidos");
				}

				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser,"`id_game`" => $id_game,"`nm_play`" => $NEXT_PLAY, "`nm_one`" => $number1, "`amount`" => $amount*-1, "`id_values`" => $id_values,  "`entry_date`" => $this->server_time), array('%i','%i','%i','%i','%i','%s','%i','%s'));
				if(!empty($id)){
					$balance = $this->getBalance($idUser,true);
					if(isset($_SESSION['balance'])){$_SESSION['balance']=$balance;}

					$json = array("STATUS" => "OK", "INFO" => "Apostado: $ $amount Numero: $number1 Jugada: $NEXT_PLAY", "balance"=>$balance);
					return $json;
				}
				throw new Exception( "[No. ". __LINE__ ."].- Insertando la apuesta, intentelo de nuevo");
			break;
		case "roulette":
			if($number1<1 || $number1>2){
				throw new Exception( "[No. ". __LINE__ ."].- Números de apuestas NO permitidos");
			}
			$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser,"`id_game`" => $id_game,"`nm_play`" => $NEXT_PLAY,"`nm_one`" => $number1,"`amount`" => $amount*-1, "`id_values`" => $id_values, "`entry_date`" => $this->server_time), array('%i','%i','%i','%i','%i','%s','%i','%s'));
			if(!empty($id)){
				if ($number1==1){
					$color = "NEGRO";
				}else{
					$color = "ROJO";
				}
				$balance = $this->getBalance($idUser,true);
				if(isset($_SESSION['balance'])){$_SESSION['balance']=$balance;}
				$json = array("STATUS" => "OK", "INFO" => "Apostado: $ $amount Color: $color Jugada: $NEXT_PLAY", "balance"=>$balance);
				return $json;
			}
			throw new Exception($this->code->ERROR_DB[$this->type]);
		break;
		case "dice.2":
		case "horse":
			if($number1<=0 || $number2==0 || $number3==0 || $number1>6 || $number2>6 || $number3>6){
				throw new Exception($this->code->ERROR_NM_NOT_ALLOWED[$this->type]);
			}
			$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser,"`id_game`" => $id_game,"`nm_play`" => $NEXT_PLAY,"`nm_one`" => $number1,"`nm_two`" => $number2,"`nm_three`" => $number3,"`amount`" => $amount*-1, "`id_values`" => $id_values, "`entry_date`" => $this->server_time), array('%i','%i','%i','%i','%i','%i','%i','%s','%i','%s'));
			if(!empty($id)){
				$balance = $this->getBalance($idUser,true);
				if(isset($_SESSION['balance'])){$_SESSION['balance']=$balance;}
				$json = array("STATUS" => "OK", "INFO" => "Apostado: $ $amount numeros: $number1 , $number2 , $number3 Jugada: $NEXT_PLAY", "balance"=>$balance);
				return ($json);
			}
			throw new Exception($this->code->ERROR_DB[$this->type]);
		break;
		case "puntazo":
			$data = $this->db->select('SELECT `lowest_puntazo`, `highest_puntazo`, `percentage_super_cumulative`, `super_cumulative`, `price` FROM '. $this->tblConfig .' WHERE `id_config` = ?;',array(1),array('%i'));
			if($data['count']!=1){ throw new Exception( "[No. ". __LINE__ ."].- Puntazo invalido!!"); }
			$amount							= $data[0]->price;
			$percentage_super_cumulative 	= $data[0]->percentage_super_cumulative;
			$super_cumulative 				= $data[0]->super_cumulative;
			$range_numbers 					= range($data[0]->lowest_puntazo,$data[0]->highest_puntazo);
			$selected 						= array();

			$data = $this->db->select('SELECT `nm_puntazo` FROM '. $this->tblTransaction .' WHERE `nm_play` = ?;',array($NEXT_PLAY),array('%i'));
			if($data['count']>0){
				for ($x = 0; $x < $data['count']; $x++){
					$selected[] = $data[$x]->nm_puntazo;
				}
			}
			$nums 			  = array_diff($range_numbers,$selected);
			$rand 			  = array_rand($nums);
			$number			  = $nums[$rand];
			$sum_cumulative   = $amount * $percentage_super_cumulative;
			$super_cumulative = $super_cumulative + $sum_cumulative;

			$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser,"`id_game`" => $id_game,"`nm_play`" => $NEXT_PLAY, "`nm_puntazo`" => $number, "`amount`" => $amount*-1, "`id_values`" => $id_values, "`entry_date`" => $this->server_time), array('%i','%i','%i','%i','%i','%s','%i','%s'));
			if(!empty($id)){
				$this->db->update($this->tblConfig, array("`super_cumulative`" => $super_cumulative), array('%i'), array("`id_config`" =>1), array('%i'));
				$balance = $this->getBalance($idUser,true);
				if(isset($_SESSION['balance'])){$_SESSION['balance']=$balance;}
				$json = array("STATUS" => "OK", "INFO" => "Sorteo: $NEXT_PLAY<br>Ticket: ", "balance"=>$balance);
				$json["point"] = $number;

				return $json;

			}
			throw new Exception($this->code->ERROR_DB[$this->type]);
		break;
		default:
			throw new Exception($this->code->ERROR_GAME_TYPE[$this->type]);
		break;
		}
	}

	public function recharge($idUserReg,$idUser,$amount){

		if(empty($idUserReg)||empty($idUser)||empty($amount)){ throw new Exception($this->code->ERROR_EMPTY[$this->type]); }

		if(!is_numeric($idUserReg)||!is_numeric($idUser)||!is_numeric($amount) || $amount<1){throw new Exception($this->code->ERROR_ONLY_NUMBER[$this->type]);}

		$data = $this->db->select('SELECT `id_user` FROM '. $this->tblUser .' WHERE `id_user` = ?;',array($idUser),array('%i'));

		if($data['count']!=1){ throw new Exception($this->code->ERROR_USER_NOT_FOUND[$this->type]); }

		$id_values 	 = 9;//credito balance
		$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" => $idUserReg,"`id_user`" => $idUser,"`id_game`" => 0,"`nm_play`" => 0,"`nm_one`" => 0,"`amount`" => $amount, "`id_values`" => $id_values, "`entry_date`" => $this->server_time),array('%i','%i','%i','%i','%i','%s','%i','%s'));
		if(empty($id)){	throw new Exception($this->code->ERROR_DB[$this->type]);}

		$balance = $this->getBalance($idUser);

		if(isset($_SESSION['balance'])){$_SESSION['balance']=$balance;}

		return (object) array ( 'transaction' =>  $id,
								'balance' =>  $balance
							  );
	}
	


}
?>
