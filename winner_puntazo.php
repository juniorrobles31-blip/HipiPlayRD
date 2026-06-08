<?php
/************************************************
class.userservice.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
//require_once('./include/class/class.system.php');

class WIN {	
	private  $db;
	private  $id_values;
	private  $tblTransaction;

	public function __construct($server_time) {
		require_once('./include/class/dbconfig.php');
		$this->db = new DB();
		$this->tblWon 		= "gms_won_puntazo";
		$this->tblTrans 	= "gms_transaction";
		$this->tblConfig 	= "gms_config";
		$this->puntazo($server_time);	
	}
	
	private function puntazo($server_time){
		$total 	= 0;
		$ticket = 0;
		$numbers = array();
		$data = $this->db->select('SELECT `nm_play`, `lowest_amount`, `count_winner` FROM '.$this->tblConfig.' WHERE `id_config` = 1;','','');
		if ($data['count']==1){	
			$amount_winner = $data[0]->lowest_amount;
			$count_winner = $data[0]->count_winner;
			$nm_play = $data[0]->nm_play;
			// All bets
			$data = $this->db->select('SELECT count(*) as count_play, SUM(`amount`) as total FROM '.$this->tblTrans.' WHERE `nm_play` = ?;',array($nm_play),array('%i'));
			if($data['count']>0){
				if($data[0]->count_play==0){ $this->updateConfig(true); return;}
				
				$count_play 	= $data[0]->count_play;				
				$total 			= $data[0]->total;
				$ticket 		= $total/$data[0]->count_play;
				$played_winner 	= $total/$amount_winner;
				
				if($played_winner<$count_winner){$count_winner=$played_winner;}
				//if total higher than count winner(config) pay more money to count winner
				if($played_winner>$count_winner){ $amount_winner = $total/$count_winner;}
				
				//if($played_winner>$count_play){return;}//Return because theres not have to be more winner than transaction
	
				//if($total<$amount_winner){return;}//Higher than the lowest_amount for winners
					
				//Get all numbers played_winner
				$data = $this->db->select('SELECT `nm_puntazo` FROM '.$this->tblTrans.' WHERE `nm_play` = ?;',array($nm_play),array('%i'));
				for ($x = 0; $x < $data['count']; $x++){
					$numbers[] = $data[$x]->nm_puntazo;
				}
				$first_winner	= array_rand($numbers,1);
				$first_winner	= $numbers[$first_winner];				

				$data = $this->db->select('SELECT `nm_puntazo`,`id_user` FROM '.$this->tblTrans.' WHERE `nm_play` = ? AND `nm_puntazo` >= ? AND `nm_puntazo` NOT IN (SELECT `nm_puntazo` FROM `gms_won_puntazo` WHERE `nm_play` = ? )  ORDER BY `nm_play`;',array($nm_play,$nm_play,$first_winner),array('%i','%i','%i'));
				$count = $data['count'];
				for ($x = 0; $x < $data['count']; $x++){
					$this->winners_puntazo($nm_play,$data[$x]->nm_puntazo,$amount_winner,$server_time);
				}
				if($count<$count_winner){
					$limit = $count_winner-$count;
					$data = $this->db->select('SELECT `id_trans`, `nm_puntazo`,`id_user` FROM '.$this->tblTrans.' WHERE `nm_play` = ? AND `nm_puntazo` NOT IN (SELECT `nm_puntazo` FROM `gms_won_puntazo` WHERE `nm_play` = ? ) ORDER BY `nm_play` LIMIT ?;',array($nm_play,$nm_play,$limit),array('%i','%i','%i'));
					for ($x = 0; $x < $data['count']; $x++){
						$this->winners_puntazo($nm_play,$data[$x]->nm_puntazo,$server_time);
						$this->insert_trans($data[$x]->id_user, $nm_play, $amount_winner, $data[$x]->id_trans,$server_time);
					}
				}
				//Update time and number of play
				$this->updateConfig();	
			}
		}
	}
	
	private function winners_puntazo($nm_play,$number,$server_time){
		$id = $this->db->insert($this->tblWon, array("`nm_play`" => $nm_play, "`number`" => $number, "`entry_date`" => $server_time), array('%i','%i','%s'));
		if(empty($id)){
			echo $error = "Error # ". __LINE__ ." | No inserto el winners_puntazo";
			//SendEmailError('Ejecución','Importante Error',$error);
		}	
	}

	private function insert_trans($id_user,$nm_play,$amount,$id_trans,$server_time){
		$id = $this->db->insert($this->tblTransaction,array("`id_user`" => $id_user,"`id_game`" => 6,"`nm_play`" => $nm_play, "`amount`" => $amount, "`id_values`" => 14, "`pay`"=> $id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%s'));
			if(empty($id)){
				echo $error = "Error # ". __LINE__ ." | No inserto el winners_dice1";						
				//SendEmailError('Ejecución','Importante Error',$error);
			}
		
	}
	
	private function updateConfig($only_time=false){
		require_once('./include/class/time.php');
		$exe = new ROLL();
		$next_play_time = $exe->GenerateNextGameTime('puntazo');
		if($only_time==false){
			$data = $this->db->select('SELECT `nm_play` FROM '.$this->tblConfig.' WHERE `id_config` = 1;','','');
			if ($data['count']==1){
			
				$id = $this->db->update('`gms_config`',array("`nm_play`" => $data[0]->nm_play+1),array('%i'),array("`id_config`" => '1'),array('%i'));
			}
		}
	}
	

}
?>