<?php
/// VER SI SE PUEDE INTRODUCIR LIB.CONFIG_TIME.PHP
/************************************************
class.time.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
if (!defined('ROOT')){
	define('ROOT','./include/');
}
class ROLL {
	
	private  $db;
	private  $balance;
	private	 $trans;
	private	 $result;
	private	 $currency;
	private	 $tblTimer;
	private	 $tblDice1;
	private	 $tblDice2;
	private	 $tblDice3;
	private	 $tblHorse;
	private	 $tblRoulette;
	private	 $tblConfig;
	
	public function __construct() {
		require_once(ROOT."class/dbconfig.php");
		$this->db = new DB();		
		$this->result 		= array();
		$this->tblTimer		= "`gms_timer`";
		$this->tblDice1		= "`gms_won_dice.1`";
		$this->tblDice2		= "`gms_won_dice.2`";
		$this->tblDice3		= "`gms_won_dice.3`";
		$this->tblHorse		= "`gms_won_horse`";
		$this->tblRoulette	= "`gms_won_roulette`";
		$this->tblConfig	= "`gms_config`";
	}
	
	//------------------------------------------------
	// Calculos del tiempo
	//------------------------------------------------
	public function GenerateNextGameTime($game_mode){
		//$game_mode="dice.1"; // PATCH: no forzar el timer a dice.1
		$CICLO = 0;
		$data = $this->db->select('SELECT `ciclo` FROM  '.$this->tblTimer .'  WHERE `cd_game` = ?;',array($game_mode),array('%s'));
		if($data['count']==1){ $CICLO = $data[0]->ciclo; }		
		return date("Y-m-d H:i:s", $this->GetGameTime($CICLO)+5);
	}

	public function timeToRoll($game_mode){ 
		//$game_mode="dice.1"; // PATCH: no forzar el timer a dice.1	
		$data = $this->db->select('SELECT `play_time` FROM '.$this->tblTimer .' WHERE `cd_game` = ?;',array($game_mode),array('%s'));
		if($data['count']==1){return $data[0]->play_time;}else{return -1;}
	}

	public function lastGame($game_mode){
		$entry_date = -1;
		//$game_mode="dice.1"; // PATCH: no forzar el timer a dice.1
		switch ($game_mode){
			case "dice.1":
			case "dice.2":
			case "dice.3":
			case "horse":
			case "roulette":
				$data = $this->db->select('SELECT `entry_date` FROM `gms_won_'. $game_mode .'` WHERE `id_won` = (SELECT max(`id_won`) FROM `gms_won_'.$game_mode .'`);','','');
				if($data['count']==1){ $entry_date = $data[0]->entry_date; }				
			break;
			case "puntazo":
				$data = $this->db->select('SELECT `last_time` FROM  '.$this->tblTimer .' WHERE `cd_game` = "puntazo";','','');
				if($data['count']==1){ $entry_date = $data[0]->last_time; }				
			break;
		}
		return $entry_date;
	}
	
	public function nextPlay($game_mode){
		$NEXT_PLAY = -1;		
		switch ($game_mode){
			case "dice.1":
				$data = $this->db->select('SHOW TABLE STATUS LIKE "'. str_replace('`','',$this->tblDice1) .'"','','');
				if($data['count']==1){$NEXT_PLAY = (int)$data[0]->Auto_increment;}
			break;
			case "dice.2":
				$data = $this->db->select('SHOW TABLE STATUS LIKE "'. str_replace('`','',$this->tblDice2) .'"','','');
				if($data['count']==1){$NEXT_PLAY = (int)$data[0]->Auto_increment;}
			break;
			case "dice.3":
				$data = $this->db->select('SHOW TABLE STATUS LIKE "'.str_replace('`','',$this->tblDice3) .'"','','');
				if($data['count']==1){$NEXT_PLAY = (int)$data[0]->Auto_increment;}
			break;
			case "roulette":
				$data = $this->db->select('SHOW TABLE STATUS LIKE "'.str_replace('`','',$this->tblRoulette) .'"','','');
				if($data['count']==1){$NEXT_PLAY = (int)$data[0]->Auto_increment;}
			break;
			case "horse":
				$data = $this->db->select('SHOW TABLE STATUS LIKE "'.str_replace('`','',$this->tblHorse) .'"','','');
				if($data['count']==1){$NEXT_PLAY = (int)$data[0]->Auto_increment;}
			break;
			case "puntazo":
				$data = $this->db->select('SELECT `nm_play` FROM '.$this->tblConfig .'  WHERE `id_config` = ?;',array(1),array('%i'));
				if($data['count']==1){ $NEXT_PLAY = $data[0]->nm_play; }
			break;
			case "conoce":
				$NEXT_PLAY = 1;
			break;
			default:
				$NEXT_PLAY = "'Error #". __LINE__ ." | undefined GameMode [$game_mode]'";
			break;
		}
		return $NEXT_PLAY;
	}
	
	public function roll($game_mode){ 
		$NEXT = strtotime($this->timeToRoll($game_mode));
		$LAST = strtotime($this->lastGame($game_mode));
		//echo '<br/>LAST '.date("Y-m-d H:i:s",$LAST);
		$NOW  = $this->server_time();
		$difDate = $NEXT-$NOW;
		$difLastGame = $NOW-$LAST;		
		if($difLastGame<=6 and $difLastGame>= -5){
			return (object) array (   'roll' =>  true,
											'difDate' =>  $difDate,
											'difLastGame' =>  $difLastGame,
											'server_time' =>  $NOW
										);
		}else{
			return (object) array ( 'roll' =>  false,
									'difDate' =>  $difDate,
									'difLastGame' =>  $difLastGame,
									'server_time' =>  $NOW
									);
		}
	}
	
	public function game_next_roll(){		
		$data = $this->db->select('SELECT `cd_game` FROM `gms_timer` WHERE `cd_game` NOT IN ("horse","puntazo")  ORDER BY `play_time` LIMIT 1','','');
		if($data['count']==1){ return $data[0]->cd_game;}
	}
	
	private function server_time(){
		//$data = $this->db->select('SELECT now() as server_time FROM dual;','','');
		//return strtotime($data[0]->server_time);	
		return mktime(date("H",$_SERVER['REQUEST_TIME']),
					   date("i",$_SERVER['REQUEST_TIME']),
					   date("s",$_SERVER['REQUEST_TIME']),
					   date("m",$_SERVER['REQUEST_TIME']),
					   date("d",$_SERVER['REQUEST_TIME']),
					   date("Y",$_SERVER['REQUEST_TIME']));
	}
	
	private function nextRoundMinutes($n){
		$round_numerator = 60 * $n; // 60 seconds per minute * n minutes 
		$rounded_time = ( ceil ( $this->server_time() / $round_numerator ) * $round_numerator );
		return $rounded_time;
	}

	private function GetGameTime($ciclo){
		$server_time = $this->nextRoundMinutes($ciclo);
		 return mktime( date("H",$server_time),
						date("i",$server_time),
						date("s",$server_time),
						date("m",$server_time),
						date("d",$server_time),
						date("Y",$server_time));
	}
}
?>