<?php
//------------------------------------------------
// Controles de seguridad
//------------------------------------------------
	if (php_sapi_name() !='cli'){exit;}
	if(!empty($_SERVER['REMOTE_ADDR'])){exit;}
	if(!isset($CtrlInPage)){exit;}

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
		asort($nm_trans, SORT_NUMERIC);  
		$count   = 1;
		$winner = array();

		foreach($nm_trans as $x => $x_value){
			$value[$count] = $x_value; 
			if($count==1){                         
				$winner[] = $x;
			}else{			
				if($value[$count]==$value[$count-1] and $value[$count]==$value[1] ){           
					$winner[] = $x;
				}				 
			}
			$count++;
		}	
		$indice = rand(0, count($winner)-1);
		$WonDice1 = $winner[$indice];
	}

?>