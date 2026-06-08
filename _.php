<?php
//------------------------------------------------
// Ejecución solo del Cron
//------------------------------------------------
	if (php_sapi_name() !='cli'){exit;}
	if(!empty($_SERVER['REMOTE_ADDR'])){exit;}
	$Won=0;
	$FirstRun=0;
	include_once('../system.php');
	include_once('../include/lib/lib.config_time.php');
//------------------------------------------------
// Calculo Dice.1
//------------------------------------------------
	$num_uno=0;
	$num_dos=0;
	$num_tres=0;
	$num_cuatro=0;
	$num_cinco=0;
	$num_seis=0;
	$NEXT_ROLL_DICE1=0;
	$WonDice1=0;
	$query = $mysqli->query('SHOW TABLE STATUS LIKE "tb_won_dice.1"');
	if($result = $query->fetch_assoc()){$NEXT_ROLL_DICE1 = $result["Auto_increment"];}

	if($NEXT_ROLL_DICE1>0){
		if($stmt = $mysqli->prepare('SELECT IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute()){																		
					if($stmt->bind_result($num_uno)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 2 AND `nm_play`= ? AND `id_game` = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute()){																		
					if($stmt->bind_result($num_dos)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 3 AND `nm_play`= ? AND `id_game` = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute()){	
					if($stmt->bind_result($num_tres)){
						$stmt->fetch();
					}									
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 4 AND `nm_play`= ? AND `id_game` = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute()){																		
					if($stmt->bind_result($num_cuatro)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 5 AND `nm_play`= ? AND `id_game` = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute()){																		
					if($stmt->bind_result($num_cinco)){
						$stmt->fetch();
					}									
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 6 AND `nm_play`= ? AND `id_game` = 1 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_DICE1)){
				if($stmt->execute())	{																		
					if($stmt->bind_result($num_seis)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}
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
		$WonDice1 = $winner[$indice];
	}
//------------------------------------------------
// Calculo Roulette
//------------------------------------------------	
	$rojo=0;
	$negro=0;
	$NEXT_ROLL_ROULETTE=0;
	$WonRoulette=0;
	$query = $mysqli->query('SHOW TABLE STATUS LIKE "tb_won_roulette"');
	if($result = $query->fetch_assoc()){$NEXT_ROLL_ROULETTE = $result["Auto_increment"];}
	if($NEXT_ROLL_ROULETTE>0){
		if($stmt = $mysqli->prepare('SELECT IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 4 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_ROULETTE)){
				if($stmt->execute()){																		
					if($stmt->bind_result($negro)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}
		if($stmt = $mysqli->prepare('SELECT  IFNULL(sum(qty_bet),0) as qty_bet FROM `tb_trans` WHERE `nm_one` = 2 AND `nm_play`= ? AND `id_game` = 4 LIMIT 1;')){		 
			if($stmt->bind_param('i',$NEXT_ROLL_ROULETTE)){
				if($stmt->execute()){																		
					if($stmt->bind_result($rojo)){
						$stmt->fetch();
					}									
			
				}
			}$stmt->close(); 
		}		
			
		if($negro==$rojo){
			$WonRoulette = rand(1,2);
		}elseif($negro > $rojo){
			$WonRoulette=2;
		}elseif($negro < $rojo){
			$WonRoulette=1;
		}
	}

	
	$NOW  = server_time();	
	$TimeOfServer = date("Y-m-d H:i:s",$NOW);		
	$LAST = strtotime(lastGame('dice.1'));
	$difLastGame = $NOW-$LAST;	
	//------------------------------------------------
	// Ejecución solo para la primera ganada
	//------------------------------------------------
	//if(empty($LAST) && $NEXT_ROLL_DICE1==1){$FirstRun=1;}
	//if($difLastGame>59 || $FirstRun==1){
	//------------------------------------------------		
	if($difLastGame>=59){
		//------------------------------------------------
		// Ganador Dice.1
		//------------------------------------------------			
		if($stmt = $mysqli->prepare('INSERT INTO `tb_won_dice.1` (`nm_one`, `time`) VALUES (?, ?);')){
			if($stmt->bind_param('is',$WonDice1,$TimeOfServer)){
				if($stmt->execute()){ $Won++; }
			}
			$stmt->close(); 
		}
		//------------------------------------------------
		// Ganador Roulette
		//------------------------------------------------	
		if($stmt = $mysqli->prepare('INSERT INTO `tb_won_roulette` (`nm_one`, `time`) VALUES (?, ?);')){
			if($stmt->bind_param('is',$WonRoulette,$TimeOfServer)){
				if($stmt->execute()){ $Won++; }
			}
			$stmt->close(); 
		}
		if($Won==2){				
		//------------------------------------------------
		// Actualiza el tiempo
		//------------------------------------------------	
			$next_play_time = GenerateNextGameTime('dice.1');			
			if($stmt = $mysqli->prepare('UPDATE `tb_config` SET `play_time`=? WHERE `id_config`= 1 LIMIT 1;')) {
				if($stmt->bind_param('s',$next_play_time)){$stmt->execute();}
				$stmt->close(); 
			}
		}
	}		
$mysqli->close();
?>