<?php
//------------------------------------------------
// Controles de seguridad
//------------------------------------------------
	if (php_sapi_name() !='cli'){exit;}
	if(!empty($_SERVER['REMOTE_ADDR'])){exit;}
	if(!isset($CtrlInPage)){exit;}
//------------------------------------------------
// Calculo Dice.3
//------------------------------------------------
	$num_uno=1;
	$num_dos=2;
	$num_tres=10;
	$num_cuatro=10;
	$num_cinco=10;
	$num_seis=10;
	$nmTransDice3 = array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);
	asort($nmTransDice3, SORT_NUMERIC);  
	$countDice3   = 1;
	$i=1;
	$winDice3 = array();
	$getThird = array();

	if($num_uno==$num_dos&&$num_dos==$num_tres&&$num_tres==$num_cuatro&&$num_cuatro==$num_cinco&&$num_cinco==$num_seis){			
		$winDice3 = array_keys($nmTransDice3);	
	}else{
		foreach($nmTransDice3 as $x => $x_value){
			$value[$countDice3] = $x_value;
			$valueNm[$i] = $x;				
			if($countDice3<=2){                         
				$winDice3[] = $x;				
			}elseif($countDice3==3){ 			
				if($value[$countDice3]==$value[$countDice3-1] and $value[$countDice3] <> $value[$countDice3-2] ){  				
					$getThird[] = $x;							
				}elseif($value[$countDice3]==$value[$countDice3-1] and $value[$countDice3] == $value[$countDice3-2]){
					$winDice3[] = $x; 
				}else{$winDice3[] = $x;}
			}elseif($countDice3==4){
				if($value[$countDice3]==$value[$countDice3-1]){ 
					if($value[$countDice3-1]<>$value[$countDice3-2]){
						array_pop($winDice3);
						$getThird[] = $valueNm[$i-1];			
					}			
					$getThird[] = $x;									
				}	
			}elseif($countDice3==5){
				if($value[$countDice3]==$value[$countDice3-1]){					
					$getThird[] = $x;						
				}	
			}elseif($countDice3==6){
				if($value[$countDice3]==$value[$countDice3-1]){					
					$getThird[] = $x;						
				}	
			}
			$countDice3++;
			$i++;
		}
		
		if(($value[1]==$value[2] and $value[2]==$value[3]) or ($value[1]<>$value[2] and $value[2]<>$value[3] and $value[3]<>$value[4])){
		}else{
			$indGetThird = rand(0, count($getThird)-1);
			$getThird = $getThird[$indGetThird];	
			array_push($winDice3,$getThird);		
		}
	}

	$rand_keys = array_rand($winDice3, 3);		
	$WonDice3_1= $winDice3[$rand_keys[0]];
	$WonDice3_2= $winDice3[$rand_keys[1]];	
	$WonDice3_3= $winDice3[$rand_keys[2]];	

?>