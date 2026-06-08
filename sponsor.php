<?php
/************************************************
class.userservice.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
//require_once('./include/class/class.system.php');

class SPONSOR {	
	private  $db;
	private  $id_values;
	private  $tblTransaction;
	private  $idJDD;
	//private  $fileName;

	public function __construct($server_time,$game_mode) {
		require_once('./include/class/dbconfig.php');
		$this->db = new DB();
		$this->tblTransaction = "gms_transaction";
		$this->id_values=25; //Id para los beneficios del promotor
		$this->idJDD=1; //Id juegos del dinero
		$this->dice1($server_time);		
	}
	
	private function dice1($server_time){
		$num_uno=0;
		$num_dos=0;
		$num_tres=0;
		$num_cuatro=0;
		$num_cinco=0;
		$num_seis=0;
		$NEXT_ROLL=0;
		$WonDice1=0;
		$data = $this->db->select("SELECT `id_game`,`amount`, FROM `gms_transaction` WHERE `id_trans` IN (
SELECT `pay` FROM `gms_transaction` WHERE `pay_sponsor` = 0 AND DATE(`entry_date`) = '2017-02-17' AND `pay` IS NOT NULL);",'','');
		if($data['count']==1){ 
			$NEXT_ROLL = (int)$data[0]->Auto_increment; 
		}
		if($NEXT_ROLL>0){ 
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_uno = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_dos = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 3 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_tres = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 4 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_cuatro = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 5 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_cinco = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 6 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_seis = $data[0]->amount*-1; }
					
			$nm_trans = array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);
			asort($nm_trans); 
			$count   = 1;
			$winner = array();

			foreach($nm_trans as $x => $x_value){   
				if($count==1){
					$value[$count] = $x_value;                          
					$winner[] = $x;
				}else{ 			
					$value[$count] = $x_value; 
					if ($value[$count]==$value[$count-1] and $value[$count]==$value[1] ){           
						$winner[] = $x;
					}				 
				}
				$count++;
			}	
			$indice = rand(0, count($winner)-1);
			$Won = $winner[$indice];
			$id = $this->db->insert('`gms_won_dice.1`',array("`nm_one`" => $Won,"`entry_date`" => $server_time),array('%i','%s'));
			if(empty($id)){
				echo $error = "Error # ". __LINE__ ." | No inserto el dice.1";						
				//SendEmailError('Ejecución','Importante Error',$error);
			}else{	
				$this->winners_dice1($id,$server_time);//Won records transactions
			}
		}
	}
	
	private function dice2($server_time){
		$num_uno=0;
		$num_dos=0;
		$num_tres=0;
		$num_cuatro=0;
		$num_cinco=0;
		$num_seis=0;
		$NEXT_ROLL=0;
		$Won=0;
		$data = $this->db->select('SHOW TABLE STATUS LIKE "gms_won_dice.2"','','');
		if($data['count']==1){ $NEXT_ROLL = (int)$data[0]->Auto_increment;  }
		
		$data = $this->db->select('SHOW TABLE STATUS LIKE "gms_won_horse"','','');
		if($data['count']==1){ $NEXT_ROLL_HORSE = (int)$data[0]->Auto_increment; }
		
		if($NEXT_ROLL>0 && $NEXT_ROLL_HORSE>0){ 
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 1 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 1 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 1 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 1 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 1 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 1 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_uno = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 2 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 2 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 2 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 2 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 2 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 2 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_dos = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 3 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 3 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 3 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 3 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 3 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 3 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_tres = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 4 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 4 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 4 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 4 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 4 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 4 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_cuatro = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 5 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 5 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 5 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 5 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 5 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 5 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_cinco = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 6 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 6 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 6 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 6 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 6 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 6 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE,$NEXT_ROLL_HORSE),array('%i','%i','%i','%i','%i','%i'));
			if($data['count']==1){ $num_seis = $data[0]->amount*-1; }
					
			$nmTransDice2 = array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);
			asort($nmTransDice2, SORT_NUMERIC);  
			$countDice2   = 1;
			$winDice2 = array();

			foreach($nmTransDice2 as $x => $x_value){
				$value[$countDice2] = $x_value;			
				if($countDice2==1){                         
					$winDice2[] = $x;
				}else{ 	
					if ($value[$countDice2]==$value[$countDice2-1] and $value[$countDice2]==$value[1]){    
						$winDice2[] = $x;
					}				 
				}
				$countDice2++;
			}	
			$indiceDice2 = rand(0, count($winDice2)-1);
			$Won = $winDice2[$indiceDice2];

			$id_dice2 = $this->db->insert('`gms_won_dice.2`',array("`nm_one`" => $Won,"`entry_date`" => $server_time),array('%i','%s'));
			if(empty($id_dice2)){
				echo $error = "Error # ". __LINE__ ." | No inserto el dice.1";						
				//SendEmailError('Ejecución','Importante Error',$error);
			}else{
				// Insert Horse
				$selected 	= array($Won);
				$numbers 	= array_keys($nmTransDice2);
				// nm_two
				$nums 		= array_diff($numbers,$selected);
				$rand 		= array_rand($nums);
				$nm_two		= $nums[$rand];
				array_push($selected,$nm_two);
				// nm_three
				$nums 		= array_diff($numbers,$selected);
				$rand 		= array_rand($nums);
				$nm_three	= $nums[$rand];
				array_push($selected,$nm_three);
				// nm_four
				$nums 		= array_diff($numbers,$selected);
				$rand 		= array_rand($nums);
				$nm_four	= $nums[$rand];
				array_push($selected,$nm_four);
				// nm_five
				$nums 		= array_diff($numbers,$selected);
				$rand 		= array_rand($nums);
				$nm_five	= $nums[$rand];
				array_push($selected,$nm_five);
				// nm_six
				$nums 		= array_diff($numbers,$selected);
				$rand 		= array_rand($nums);
				$nm_six		= $nums[$rand];
				
				$id_horse = $this->db->insert('`gms_won_horse`',array("`nm_one`" => $Won, "`nm_two`" => $nm_two,"`nm_three`" => $nm_three, "`nm_four`" => $nm_four, "`nm_five`" => $nm_five,"`nm_six`" => $nm_six,"`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id_horse)){
					echo $error = "Error # ". __LINE__ ." | No inserto caballo";						
					//SendEmailError('Ejecución','Importante Error',$error);
				}else{
					$this->winners_dice2($id_dice2,$server_time);//Won records transactions 
					$this->winners_horse($id_horse,$server_time);//Won records transactions
				}
			}
		}
	}
	
	private function dice3($server_time){
		$num_uno=0;
		$num_dos=0;
		$num_tres=0;
		$num_cuatro=0;
		$num_cinco=0;
		$num_seis=0;
		$NEXT_ROLL=0;
		$WonDice3=0;
		$WonDice3_1=0;
		$WonDice3_2=0;
		$WonDice3_3=0;
		$data = $this->db->select('SHOW TABLE STATUS LIKE "gms_won_dice.3"','','');
		if($data['count']==1){ 
			$NEXT_ROLL = (int)$data[0]->Auto_increment; 
		}
		if($NEXT_ROLL>0){ 
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_uno = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_dos = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 3 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_tres = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 4 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_cuatro = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 5 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_cinco = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 6 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $num_seis = $data[0]->amount*-1; }
					
			$nmTransDice3 = array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);
			asort($nmTransDice3, SORT_NUMERIC);  
			$countDice3 = 1;
			$n=0;
			$i=0;
			$winDice3 = array();
			$getThird = array();
		
			if($num_uno==0&&$num_dos==0&&$num_tres==0&&$num_cuatro==0&&$num_cinco==0&&$num_seis==0){
				$WonDice3_1= $winDice3 = rand(1,6);
				$WonDice3_2= $winDice3 = rand(1,6);
				$WonDice3_3= $winDice3 = rand(1,6);
			}elseif($num_uno==$num_dos&&$num_dos==$num_tres&&$num_tres==$num_cuatro&&$num_cuatro==$num_cinco&&$num_cinco==$num_seis){			
				$winDice3 = rand(1,6);
				$WonDice3_1= $winDice3;
				$WonDice3_2= $winDice3;
				$WonDice3_3= rand(1,6);
			}else{
				foreach($nmTransDice3 as $x => $x_value){
					$value[$countDice3] = $x_value;
					$valueNm[$countDice3] = $x;	
					if($countDice3==1){
						$value[$countDice3] = $x_value;                          
						$winDice3[] = $x;
					}else{ 			
						$value[$countDice3] = $x_value;
						//Toma un rango si hay 3 o mas iguales
						if($value[$countDice3]==$value[$countDice3-1] && $value[$countDice3]==$value[1]){           
							$winDice3[] = $x;
						}elseif($countDice3==2 && $value[$countDice3]<>$value[1]){
							$winDice3[] = $x; 
							$getThird[] = $x;					
						}elseif($countDice3==3){
							if($value[$countDice3]<>$value[2]){$winDice3[] = $x;
							}else{
							$getThird[] = $x;
							array_pop($winDice3);
							$i++;}
						}elseif($countDice3==4&&$value[$countDice3]==$value[3]){
							$getThird[] = $valueNm[3];
							$getThird[] = $x; 
							$n++;
							if($value[2]<>$value[3]){array_pop($winDice3);}
						}elseif($countDice3==5&&$value[$countDice3]==$value[3]){
							$getThird[] = $x;
						}elseif($countDice3==6&&$value[$countDice3]==$value[3]){
							$getThird[] = $x;
						}						
					}			
					$countDice3++;
				}
				
				if($value[1]==0&&$value[1]<>$value[2]){
					$WonDice3_1= $valueNm[1];
					$WonDice3_2= $valueNm[1];
					$WonDice3_3= $valueNm[1];
				}elseif($value[1]==$value[2]){
					$getOne = array();
					$getOne[]=$valueNm[1];
					$getOne[]=$valueNm[2];
					//Escoge al azar si repite 2 nm_ones
						$indGetOne = rand(0, count($getOne)-1);
						$indGetOne = $getOne[$indGetOne];
						$indGetTwo = rand(0, count($getOne)-1);
						$indGetTwo = $getOne[$indGetTwo];
						
					$WonDice3_1=$indGetOne;
					$WonDice3_2=$indGetTwo;
					
					if($n>0){
						array_push($getThird,$indGetOne);
						array_push($getThird,$indGetTwo);				
						$indGetThird = rand(0, count($getThird)-1);
						$inThird = $getThird[$indGetThird];			
						$WonDice3_3=$inThird;
					}else{
						$WonDice3_3=$valueNm[3];
					}
				}elseif($value[1]<$value[2]&&$value[2]<$value[3]&&$value[3]<$value[4]){
					$getOne = array();
					$getOne[]=$valueNm[1];
					$getOne[]=$valueNm[2];
					//Escoge al azar si repite 2 nm_ones
						$indGetOne = rand(0, count($getOne)-1);
						$indGetOne = $getOne[$indGetOne];
						
					$getTwo = array();
					$getTwo[]=$valueNm[1];
					$getTwo[]=$valueNm[2];
					$getTwo[]=$valueNm[3];
					//Escoge al azar si repite 2 nm_ones
						$indGetTwo = rand(0, count($getTwo)-1);
						$indGetTwo = $getTwo[$indGetTwo];
					
					$rand_keys = array_rand($winDice3,3);			
					$WonDice3_1=$valueNm[1];
					$WonDice3_2= $indGetOne;	
					$WonDice3_3= $indGetTwo;		
					
				}
				else{
					if($i>0){				
						$indGetThird = rand(0, count($getThird)-1);
						$inThird = $getThird[$indGetThird];	
						array_push($winDice3,$inThird);
						$indGetThird = rand(0, count($getThird)-1);
						$inThird = $getThird[$indGetThird];	
						array_push($winDice3,$inThird);				
					}else{
						$indGetThird = rand(0, count($getThird)-1);
						$inThird = $getThird[$indGetThird];	
						array_push($winDice3,$inThird);			
					}	
					$rand_keys = array_rand($winDice3,3);			
					$WonDice3_1= $winDice3[$rand_keys[0]];
					$WonDice3_2= $winDice3[$rand_keys[1]];	
					$WonDice3_3= $winDice3[$rand_keys[2]];	
				}
			}
			
			$id = $this->db->insert('`gms_won_dice.3`',array("`nm_one`" => $WonDice3_1, "`nm_two`" => $WonDice3_2, "`nm_three`" => $WonDice3_3,"`entry_date`" => $server_time),array('%i','%i','%i','%s'));
			if(empty($id)){
				echo $error = "Error # ". __LINE__ ." | No inserto el dice.1";						
				//SendEmailError('Ejecución','Importante Error',$error);
			}else{			
				$this->winners_dice3($id,$server_time);//Won records transactions
			}
		}
	}
		
	private function roulette($server_time){
		$rojo=0;
		$negro=0;
		$NEXT_ROLL=0;
		$Won=0;
		$data = $this->db->select('SHOW TABLE STATUS LIKE "gms_won_roulette"','','');
		if($data['count']==1){ 
			$NEXT_ROLL = (int)$data[0]->Auto_increment; 
		}
		if($NEXT_ROLL>0){ 
			$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 4 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $negro = $data[0]->amount*-1; }
			
			$data = $this->db->select('SELECT  IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND `id_game` = 4 AND `id_values`=13;',array($NEXT_ROLL),array('%i'));
			if($data['count']==1){ $rojo = $data[0]->amount*-1; }
			
			if($negro==$rojo){
				$Won = rand(1,2);
			}elseif($negro > $rojo){
				$Won=2;
			}elseif($negro < $rojo){
				$Won=1;
			}
		
			$r = rand(1,23);
			if ($this->isPar($Won) != $this->isPar($r)){ $r+=1;}
			
			$id = $this->db->insert('`gms_won_roulette`',array("`nm_one`" => $Won,"`entry_date`" => $server_time,"`place`" => $r),array('%i','%s','%i'));
			if(empty($id)){
				echo $error = "Error # ". __LINE__ ." | No inserto roulette";						
				//SendEmailError('Ejecución','Importante Error',$error);
			}else{
				$this->winners_roulette($id,$server_time);//Won records transactions
			}
		}
	}	
	
	private function winners_dice1($no_play,$server_time){
		$data = $this->db->select('SELECT A.`id_trans`, A.`id_user`, A.`id_game`, ((A.`amount` *-1) *5) AS `profit` FROM `gms_transaction` A, `gms_won_dice.1` B WHERE A.nm_play = B.id_won AND A.nm_one = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 1;',array($no_play),array('%i'));
		if($data['count']>0){ 
			for ($x = 0; $x < $data['count']; $x++) {	
				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" =>$this->idJDD,"`id_user`" => $data[$x]->id_user,"`id_game`" => $data[$x]->id_game,"`nm_play`" => $no_play, "`amount`" => $data[$x]->profit, "`id_values`" => $this->id_values, "`pay`" => $data[$x]->id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id)){
					echo $error = "Error # ". __LINE__ ." | No inserto el winners_dice1";						
					//SendEmailError('Ejecución','Importante Error',$error);
				}
			}
		}
		$this->updateConfig("dice.1");		
	}
	
	private function winners_dice2($no_play,$server_time){
		$data = $this->db->select('SELECT A.`id_trans`, A.`id_user`, A.`id_game`, ((A.`amount`*-1)*0.9) + (A.`amount`*-1) AS `profit` FROM `gms_transaction` A, `gms_won_dice.2` B WHERE (A.nm_play = B.id_won AND A.nm_one = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 5) OR  (A.nm_play = B.id_won AND A.nm_two = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 5) OR(A.nm_play = B.id_won AND A.nm_three = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 5);',array($no_play,$no_play,$no_play),array('%i','%i','%i'));
		if($data['count']>0){ 
			for ($x = 0; $x < $data['count']; $x++) {	
				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" =>$this->idJDD,"`id_user`" => $data[$x]->id_user,"`id_game`" => $data[$x]->id_game,"`nm_play`" => $no_play, "`amount`" => $data[$x]->profit, "`id_values`" => $this->id_values, "`pay`" => $data[$x]->id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id)){
					echo $error = "Error # ". __LINE__ ." | No inserto el winners_dice2";						
					//SendEmailError('Ejecución','Importante Error',$error);
				}
			}
		}
		$this->updateConfig("dice.2");		
	}
	
	private function winners_dice3($no_play,$server_time){
		$data = $this->db->select('SELECT A.`id_trans`, A.`id_user`, A.`id_game`, (A.`amount`*-1) AS `amount`, A.`nm_one` AS `no_bet`, B.`nm_one`, B.`nm_two`, B.`nm_three` FROM `gms_transaction` A, `gms_won_dice.3` B WHERE (A.nm_play = B.id_won AND A.`nm_one`=B.`nm_one` AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 2) OR  (A.nm_play = B.id_won AND A.nm_one = B.nm_two AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 2) OR(A.nm_play = B.id_won AND A.nm_one = B.nm_three AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 2);',array($no_play,$no_play,$no_play),array('%i','%i','%i'));
		if($data['count']>0){ 
			for ($x = 0; $x < $data['count']; $x++){
				$n=0;
				if($data[$x]->no_bet===$data[$x]->nm_one){$n++;}
				if($data[$x]->no_bet===$data[$x]->nm_two){$n++;}
				if($data[$x]->no_bet===$data[$x]->nm_three){$n++;}
				$profit=(($data[$x]->amount*0.9)*$n)+$data[$x]->amount;
				
				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" =>$this->idJDD,"`id_user`" => $data[$x]->id_user,"`id_game`" => $data[$x]->id_game,"`nm_play`" => $no_play, "`amount`" => $profit, "`id_values`" => $this->id_values, "`pay`" => $data[$x]->id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id)){
					echo $error = "Error # ". __LINE__ ." | No inserto el winners_dice3";						
					//SendEmailError('Ejecución','Importante Error',$error);
				}
			}
		}
		$this->updateConfig("dice.3");		
	}
	
	private function winners_roulette($no_play,$server_time){
		$data = $this->db->select('SELECT A.`id_trans`, A.`id_user`, A.`id_game`, ((A.`amount` *-1)*0.9) + (A.`amount`*-1) AS `profit` FROM `gms_transaction` A, `gms_won_roulette` B WHERE A.nm_play = B.id_won AND A.nm_one = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 4;',array($no_play),array('%i'));
		if($data['count']>0){ 
			for ($x = 0; $x < $data['count']; $x++) {		
				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" =>$this->idJDD,"`id_user`" => $data[$x]->id_user,"`id_game`" => $data[$x]->id_game,"`nm_play`" => $no_play, "`amount`" => $data[$x]->profit, "`id_values`" => $this->id_values, "`pay`" => $data[$x]->id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id)){
					echo $error = "Error # ". __LINE__ ." | No inserto el roulette";						
					//SendEmailError('Ejecución','Importante Error',$error);
				}
			}
		}
		$this->updateConfig("roulette");		
	}
	
	private function winners_horse($no_play,$server_time){
		$data = $this->db->select('SELECT A.`id_trans`, A.`id_user`, A.`id_game`, ((A.`amount`*-1)*0.9) + (A.`amount`*-1) AS `profit` FROM `gms_transaction` A, `gms_won_horse` B WHERE (A.nm_play = B.id_won AND A.nm_one = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 3) OR  (A.nm_play = B.id_won AND A.nm_two = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 3) OR(A.nm_play = B.id_won AND A.nm_three = B.nm_one AND A.entry_date < B.entry_date AND A.nm_play = ? AND A.`id_game` = 3);',array($no_play,$no_play,$no_play),array('%i','%i','%i'));
		if($data['count']>0){
			for ($x = 0; $x < $data['count']; $x++) {
				$id = $this->db->insert($this->tblTransaction,array("`id_user_reg`" =>$this->idJDD, "`id_user`" => $data[$x]->id_user,"`id_game`" => $data[$x]->id_game,"`nm_play`" => $no_play, "`amount`" => $data[$x]->profit, "`id_values`" => $this->id_values, "`pay`" => $data[$x]->id_trans, "`entry_date`" => $server_time),array('%i','%i','%i','%i','%i','%i','%i','%s'));
				if(empty($id)){
					echo $error = "Error # ". __LINE__ ." | No inserto el winners_horse";
					//SendEmailError('Ejecución','Importante Error',$error);
				}
			}
		}
		//$this->updateConfig("horse");
	}
	
	private	function isPar($input){ 
		if ($input % 2 == 0){ 
			return true; 
		}else{ 
			return false; 
		} 
	} 
	
	private function updateConfig($game_mode){
		require_once('./include/class/time.php');
		$exe = new ROLL();
		$next_play_time = $exe->GenerateNextGameTime($game_mode);
		$id = $this->db->update('`gms_timer`',array("`play_time`" => $next_play_time),array('%s'),array("`cd_game`" => $game_mode),array('%s'));
	}

}
?>