<?php
//------------------------------------------------
// Controles de seguridad
//------------------------------------------------
	if (php_sapi_name() !='cli'){exit;}
	if(!empty($_SERVER['REMOTE_ADDR'])){exit;}
	if(!isset($CtrlInPage)){exit;}
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
	
?>