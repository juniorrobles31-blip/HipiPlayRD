<?php
/************************************************
class.balance.php
version	:  	1.0.1
date	:	19-JAN-2017
*************************************************/
if (!defined('ROOT')){
	define('ROOT','./include/');
}
class DAILY {
	
	private  $db;
	private  $balance;
	private  $server_time;
	private  $yesterday;


	public function __construct() {
		require_once(ROOT."class/dbconfig.php");
		require_once(ROOT."class/time.php");
		
		$this->db 			= new DB();		
		$exe 				= new ROLL();
		$trans 				= $exe->roll("dice.1");
	 	$this->server_time 	= date('Y-m-d H:i:s', $trans->server_time); 
	 	$this->yesterday    = date('Y-m-d', strtotime($this->server_time. ' - 1 day'));
		$this->b_yesterday  = date('Y-m-d', strtotime($this->server_time. ' - 2 day'));
		$this->tblUser		= "`gms_user`";
		$this->tblBalance	= "`gms_balance`";
		$this->tblTrans		= "`gms_transaction`";
		
		//execute
		$this->daily_balance();

	}
	
	//Actualizar la tabla de balance
	private function daily_balance(){ 
		$data = $this->db->select('SELECT `id_user` FROM '.$this->tblUser.' where id_user = 1;','','');
		if ($data['count']>0){
			for ($x = 0; $x < $data['count']; $x++) {
				$balanceAnterior 	= 0;
				$transAnterior 		= 0;
				$data_balance = $this->db->select('SELECT `balance` FROM '.$this->tblBalance.' WHERE `id_user` = ? AND `date` = ?;',array($data[$x]->id_user,$this->b_yesterday),array('%i','%s'));
				if ($data_balance['count']==1){ $balanceAnterior = $data_balance[0]->balance;}
				
				$data_balance = $this->db->select('SELECT IFNULL(SUM(`amount`),0) as trans FROM '.$this->tblTrans.' WHERE `id_user` = ? AND DATE(`entry_date`) = ?;',array($data[$x]->id_user,$this->yesterday),array('%i','%s'));
				if ($data_balance['count']==1){  $transAnterior = $data_balance[0]->trans;}
				
			 	$balance=$balanceAnterior+$transAnterior;
				//return;
				$data_srch = $this->db->select('SELECT `id_user`, `date` FROM '.$this->tblBalance.' WHERE `id_user` = ?;',array($data[$x]->id_user),array('%i'));
				if ($data_srch['count']==1){
					if($data_srch[0]->date!=$this->yesterday){
						$this->db->update($this->tblBalance,array("`balance`"=>$balance, "`date`"=>$this->yesterday, "`update_time`"=>$this->server_time), array('%i','%s','%s'), array("`id_user`"=>$data[$x]->id_user), array('%i'));
					}
				}else{ 
					$this->db->insert($this->tblBalance,array("`id_user`" => $data[$x]->id_user, "`balance`" => $balance, "`date`" => $this->yesterday, "`update_time`" => $this->server_time), array('%i','%i','%s','%s'));
				}
			}
		}		
	}
	
	
}
?>