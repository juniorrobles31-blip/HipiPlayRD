<?php

/*$_game_modes["dice.1"]  = 1;
$_game_modes["dice.3"]  = 2;
$_game_modes["horse"]   = 3;
$_game_modes["roulette"]= 4;
$_game_modes["dice.2"]  = 5;
*/
$rows_per_page = 8;

$lang["next"] = ">";
$lang["last"] = ">>";
$lang["prev"] = "<";
$lang["first"] = "<<";

$numrows = 0;
$Result = "";

//Obtain the required page number
if (isset($_POST['pag'])){
	$pag = $_POST['pag'];
}else if (isset($_GET['pag'])){
	$pag = $_GET['pag'];
}else{
	$pag = 1;
} 
$limit  = ($pag - 1) * $rows_per_page;

require_once('./include/class/common.php');
$display = new DISPLAY();
if(isset($_POST['game_mode'],$_POST['value'])){
	$json = array("STATUS"=>"OK","INFO"=>"Game.results");
	$json["results"] = $display->bed_srch($_POST['game_mode'],$_POST['value'],'');
	die(json_encode($json));
}


return;
if(isset($_POST['game_mode'])){	

	if($stmt = $mysqli->prepare('SELECT `id_game`, `cd_game` FROM `tb_game`  WHERE `id_game` = ? ;')){
		if($stmt->bind_param('i',$_POST['game_mode'])){			
			if($stmt->execute()){
				if($stmt->bind_result($id,$cd)){ 	
					while($stmt->fetch()){
						$gameModeId = $id;
						$gameModeCd = $cd;
					}
				}
			}
		}$stmt->close();
	}
}

	if(isset($_SESSION['id'])){
		$logid = $_SESSION['id'];
	}

	//------------------------------------------------
	// Busquedas de resultados
	//------------------------------------------------
	if(isset($_POST['value'],$_POST['game_mode']) && !empty($_POST['value']) && !empty($_POST['game_mode'])){
		//------------------------------------------------
		// Busquedas por jugadas del jugador
		//------------------------------------------------
		if($_POST['type'] == 1){			
			if (!isset($logid)){ die("variable logid No definido");}
			//------------------------------------------------
			// dice.1
			//------------------------------------------------
			if($gameModeCd=='dice.1'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `game`, `nm_play`, `nm_one`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, IFNULL((SELECT `nm_one` FROM `tb_won_dice.1` C WHERE C.`id_won`= A.`nm_play`),0) as `won`  FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND `nm_play` = ? ')){
					if($stmt->bind_param('iii',$logid,$gameModeId,$_POST['value'])){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;					
								if($numrows>=1){
									if($stmt->bind_result($Game,$NumPlay,$nm_one,$Apuesta,$Day,$Time,$Won)){ 	
										$i = 0;
										while($stmt->fetch()){
											$i++;
											//  ($pag - 1) * $rows_per_page;
											if ($i > $limit && $i <= ($limit + $rows_per_page)){
												$Day = str_replace($eng,$esp,$Day);	
												$Result .=
														'<tr>'.
														'<th align="center">'.$Game.'</th>'.
														'<td align="center">'.$NumPlay.'</td>'.
														'<td align="center">'.$Day.' / '.$Time.'</td>'.
														'<td align="center"> $'.$Apuesta.'</td>'.
														'<td align="center">'.resultIcon($gameModeCd,$nm_one).'</td>'.
														'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
														'</tr>';
											}else{
												if ($i > ($limit + $rows_per_page)){
													break;	
												}
											}
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='roulette'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `game`, `nm_play`, `nm_one`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, IFNULL((SELECT `nm_one` FROM `tb_won_roulette` C WHERE C.`id_won`= A.`nm_play`),0) as `won`  FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND `nm_play` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iiiii',$logid,$gameModeId,$_POST['value'],$limit, $offset)){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){										
									if($stmt->bind_result($Game,$NumPlay,$nm_one,$Apuesta,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<th align="center">'.$Game.'</th>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center"> $'.$Apuesta.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$nm_one).'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='dice.3'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `game`, `nm_play`, `nm_one`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, IFNULL((SELECT `nm_one` FROM `tb_won_dice.3` C WHERE C.`id_won`= A.`nm_play`),0) as `nm_one` ,IFNULL((SELECT `nm_two` FROM `tb_won_dice.3` C WHERE C.`id_won`= A.`nm_play`),0) as `nm_two`, IFNULL((SELECT `nm_three` FROM `tb_won_dice.3` C WHERE C.`id_won`= A.`nm_play`),0) as `nm_three`  FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND `nm_play` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iiiii',$logid,$gameModeId,$_POST['value'],$limit, $offset)){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($Game,$NumPlay,$nm_one,$Apuesta,$Day,$Time,$NmOne,$NmTwo,$NmTree)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<th align="center">'.$Game.'</th>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center"> $'.$Apuesta.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$nm_one).'</td>';
													if(!empty($NmOne)&&!empty($NmTwo)&& !empty($NmTree)){
														$Result .='<td align="center">'.resultIcon($gameModeCd,$NmOne).' '.resultIcon($gameModeCd,$NmTwo).' '.resultIcon($gameModeCd,$NmTree).'</td>';
													}else{
														$Result .='<td align="center">'.resultIcon($gameModeCd,0).'</td>';
													}
													$Result .='</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='dice.2'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `game`, `nm_play`, `nm_one`,`nm_two`,`nm_three`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, IFNULL((SELECT `nm_one` FROM `tb_won_dice.2` C WHERE C.`id_won`= A.`nm_play`),0) as `won`  FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND `nm_play` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iiiii',$logid,$gameModeId,$_POST['value'],$limit, $offset)){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($Game,$NumPlay,$NmOne,$NmTwo,$NmTree,$Apuesta,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<th align="center">'.$Game.'</th>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center"> $'.$Apuesta.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$NmOne).' '.resultIcon($gameModeCd,$NmTwo).' '.resultIcon($gameModeCd,$NmTree).'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='horse'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `game`, `nm_play`, `nm_one`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, IFNULL((SELECT `nm_one` FROM `tb_won_dice.2` C WHERE C.`id_won`= A.`nm_play`),0) as `won`  FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND `id_user` = ? AND A.`id_game` = ? AND `nm_play` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iiiii',$logid,$gameModeId,$_POST['value'],$limit, $offset )){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($Game,$NumPlay,$nm_one,$Apuesta,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<th align="center">'.$Game.'</th>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center"> $'.$Apuesta.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$nm_one).'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}
		
		//------------------------------------------------
		// Busqueda de los resultados
		//------------------------------------------------
		}else{
			//------------------------------------------------
			// dice.1
			//------------------------------------------------
			if($gameModeCd=='dice.1'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `id_won`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, `nm_one` FROM `tb_won_dice.1` WHERE `id_won` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iii',$_POST['value'],$limit, $offset)){			
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($NumPlay,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='roulette'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `id_won`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, `nm_one` FROM `tb_won_roulette` WHERE `id_won` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iii',$_POST['value'],$limit, $offset)){					
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($NumPlay,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='dice.3'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `id_won`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, `nm_one`, `nm_two`,`nm_three` FROM `tb_won_dice.3` WHERE `id_won` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iii',$_POST['value'],$limit, $offset)){					
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($NumPlay,$Day,$Time,$NmOne,$NmTwo,$NmTree)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$NmOne).' '.resultIcon($gameModeCd,$NmTwo).' '.resultIcon($gameModeCd,$NmTree).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='dice.2'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `id_won`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, `nm_one` FROM `tb_won_dice.2` WHERE `id_won` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iii',$_POST['value'],$limit, $offset)){					
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($NumPlay,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}elseif($gameModeCd=='horse'){
				$Result = $LANG["game.result.header"] ;
				if($stmt = $mysqli->prepare('SELECT `id_won`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time`, `nm_one` FROM `tb_won_horse` WHERE `id_won` = ? LIMIT ?,?;')){
					if($stmt->bind_param('iii',$_POST['value'],$limit, $offset)){					
						if($stmt->execute()){
							if($stmt->store_result()){
								$numrows = $stmt->num_rows;
								if($numrows>=1){
									if($stmt->bind_result($NumPlay,$Day,$Time,$Won)){ 	
										while($stmt->fetch()){
											$Day = str_replace($eng,$esp,$Day);	
											$Result .=
													'<tr>'.
													'<td align="center">'.$NumPlay.'</td>'.
													'<td align="center">'.$Day.' / '.$Time.'</td>'.
													'<td align="center">'.resultIcon($gameModeCd,$Won).'</td>'.
													'</tr>';
										}
									}
								}
							}
						}
					}$stmt->close();
				}
			}
		}
	}else{
	//----------
	// TODAS LAS JUGADAS
	//----------
	if (!isset($_POST['type'])){$_POST['type'] = 1;}
	//----------
	// TODAS LAS JUGADAS del jugador
	//----------
	if($_POST['type'] == 1){
		if($stmtnum = $mysqli->prepare('SELECT COUNT(*) FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND A.`id_user` = ?')){
			if($stmtnum->bind_param('i',$logid)){		
				if($stmtnum->execute()){
					if($stmtnum->bind_result($total)){
						while($stmtnum->fetch()){
						 	$numrows = $total;
						}
					}
				}
			}
			$stmtnum->close();
		}
	}

	/*else{// todas
		if($stmtnum = $mysqli->prepare('SELECT COUNT( * ) FROM  `tb_trans` A,  `tb_game` B WHERE A.`id_game` = B.`id_game` ')){
			if($stmtnum->execute()){
				if($stmtnum->bind_result($total)){
					while($stmtnum->fetch()){
					 $numrows = $total;
					}
				}
			}
			$stmtnum->close();
		}
	}*/
		
		
		//Calculate number of $lastpage
		$lastpage      = ceil($numrows/$rows_per_page);
		
		//Ensure that $pag is within range
		$pag = (int)$pag;
		if ($pag > $lastpage){
			$pag = $lastpage;
		} 
		if ($pag < 1){
			$pag = 1;
		} 
		//Construct LIMIT clause
		$limit = ($pag - 1) * $rows_per_page;
		$offset = $rows_per_page;
		
		$mysqli2 = @new mysqli(HOST, USER, PASSWORD, DATABASE);
		if ($mysqli2->connect_error) {
			echo'ERRORCODE 6: [Connect Error] : ' . $mysqli2->connect_error ;
		}
		
		$Result = $LANG["game.result.header"] ;
		if($stmtAll = $mysqli->prepare('SELECT B.`id_game`,`game`,`cd_game`, `nm_play`, `nm_one`,`nm_two`,`nm_three`, `qty_bet`, DATE_FORMAT( time , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( `time` , "%h:%i %p" ) AS `time` FROM `tb_trans` A, `tb_game` B WHERE A.`id_game` = B.`id_game` AND A.`id_user` = ? ORDER BY  `id_trans` DESC LIMIT ?,?;')){		
			if($stmtAll->bind_param('iii',$logid,$limit, $offset)){		
				if($stmtAll->execute()){			
					if($stmtAll->bind_result($GameId,$Game,$CdGame,$NumPlay,$NmOne,$NmTwo,$NmTree,$Apuesta,$Day,$Time)){ 	
						while($stmtAll->fetch()){
							$Day = str_replace($eng,$esp,$Day);	
							$Result .=
									'<tr>'.
									'<th align="center">'.$Game.'</th>'.
									'<td align="center">'.$NumPlay.'</td>'.
									'<td align="center">'.$Day.' / '.$Time.'</td>'.
									'<td align="center"> $'.$Apuesta.'</td>';
									if($CdGame=='dice.2' || $CdGame=='horse'){
										$Result .='<td align="center">'.resultIcon($CdGame,$NmOne).' '.resultIcon($CdGame,$NmTwo).' '.resultIcon($CdGame,$NmTree).'</td>';
									}else{
										$Result .='<td align="center">'.resultIcon($CdGame,$NmOne).'</td>';
									}
									// TODO. separar horse?
									$Table='tb_won_'.$CdGame;
									if ($CdGame == "horse"){$Table='tb_won_dice.2';} 									
										
									if($CdGame=='dice.3'){
										if($stmt = $mysqli2->prepare('SELECT `nm_one`,`nm_two`,`nm_three` FROM `'.$Table.'` WHERE `id_won` = ? ;')){
											if($stmt->bind_param('i',$NumPlay)){					
												if($stmt->execute()){
													if($stmt->store_result()){
														$num = $stmt->num_rows;
														if($num==1){
															if($stmt->bind_result($WonOne,$WonTwo,$WonThree)){ 	
																while($stmt->fetch()){																
																	$Result .='<td align="center">'.resultIcon($CdGame,$WonOne).' '.resultIcon($CdGame,$WonTwo).' '.resultIcon($CdGame,$WonThree).'</td>'.'</tr>';
																}
															}
														}else{$Result .= '<td align="center">'.resultIcon($CdGame,0).'</td>'.'</tr>';}
													}
												}
											}$stmt->close();
										}
									}else{									
										if($stmt = $mysqli2->prepare('SELECT `nm_one` FROM `'.$Table.'` WHERE `id_won` = ? ;')){
											if($stmt->bind_param('i',$NumPlay)){					
												if($stmt->execute()){
													if($stmt->store_result()){
														$num = $stmt->num_rows;
														if($num==1){
															if($stmt->bind_result($Won)){ 	
																while($stmt->fetch()){																
																	$Result .=
																			'<td align="center">'.resultIcon($CdGame,$Won).'</td>'.'</tr>';
																}
															}
														}else{$Result .= '<td align="center">'.resultIcon($CdGame,0).'</td>'.'</tr>';}
													}
												}
											}$stmt->close();
										}
									}
						}$mysqli2->close();	
					}	
				}				
			}$stmtAll->close(); 
		}
	}
	
	//----------
	// Imprime la tabla
	//----------	
	$Result .= '</table>';	
	if ($numrows > 0){
		if(isset($Result)){echo $Result;}
	}else{
		echo $LANG["game.result.zero"];	
	}
	
	if (isset($gameModeCd)){
		if ($numrows > 0){
			$pag      = $_POST["pag"];
			$nextpage = $pag+1;
			$lastpage      = ceil($numrows/$rows_per_page);
			if ($lastpage == 0){
				$lastpage = 1;
				$nextpage = 1;
			}
			
			///previous pages
			if ($pag == 1){
				echo '<a data-role="button" class="ui-state-disabled" style="display:inline-block">'.$lang["first"].'</a>'.
				'<a data-role="button" class="ui-state-disabled" style="display:inline-block">'.$lang["prev"].'</a> ';
			}else{
				echo '<a onClick="search_result('."'".$gameModeId."'".','.$_POST['value'].','.$_POST['type'].', 1, '."'".$_POST['element']."'".')" data-role="button" style="display:inline-block">1</a> ';
				$prevpage = $pag-1;
				echo ' <a onClick="search_result('."'".$gameModeId."'".','.$_POST['value'].','.$_POST['type'].', '.$prevpage.', '."'".$_POST['element']."'".')" data-role="button" style="display:inline-block">'.$lang["prev"].'</a>';
			} 
			echo "<div style='height:48px; display:inline-block; vertical-align:bottom'> Página $pag de $lastpage&nbsp;</div>";

			/////following pages
			if ($pag == $lastpage){
				echo '<a data-role="button" class="ui-state-disabled" data-iconpos="right" style="display:inline-block">'.$lang["next"].'</a>
				<a data-role="button" class="ui-state-disabled" data-iconpos="right" style="display:inline-block">'.$lang["last"].'</a> ';
			}else{
				echo '<a onClick="search_result('."'".$gameModeId."'".','.$_POST['value'].','.$_POST['type'].', '.$nextpage.', '."'".$_POST['element']."'".')" data-role="button" data-iconpos="right" style="display:inline-block">'.$lang["next"].'</a> ';
				echo '<a onClick="search_result('."'".$gameModeId."'".','.$_POST['value'].','.$_POST['type'].', '.$lastpage.', '."'".$_POST['element']."'".')" data-role="button" data-iconpos="right" style="display:inline-block">'.$lang["last"].'</a>';
			}	
		}
	}else{
		if ($numrows > 0){
			$nextpage = $pag+1;
			if ($lastpage == 0){
				$lastpage = 1;
				$nextpage = 1;
			}
			///previous pages
			if ($pag == 1){
				echo '<a data-role="button" class="ui-state-disabled" style="display:inline-block">'.$lang["first"].'</a>'.
				'<a data-role="button" class="ui-state-disabled" style="display:inline-block">'.$lang["prev"].'</a> ';
			}else{
				echo '<a href="?page=profile&pag=1" data-role="button" style="display:inline-block">'.$lang["first"].'</a> ';
				$prevpage = $pag-1;
				echo ' <a href="?page=profile&pag='.$prevpage.'" data-role="button"  style="display:inline-block">'.$lang["prev"].'</a>';
			} 
			echo "<div style='height:48px; display:inline-block; vertical-align:bottom'>Página $pag de $lastpage&nbsp;</div>";

			/////following pages
			if ($pag == $lastpage){
				echo '<a data-role="button" class="ui-state-disabled" data-iconpos="right" style="display:inline-block">'.$lang["next"].'</a>
				<a data-role="button" class="ui-state-disabled" data-iconpos="right" style="display:inline-block">'.$lang["last"].'</a>';
			}else{
				echo '<a href="?page=profile&pag='.$nextpage.'" data-role="button" data-iconpos="right" style="display:inline-block">'.$lang["next"].'</a> ';
				echo '<a href="?page=profile&pag='.$lastpage.'" data-role="button" data-iconpos="right" style="display:inline-block">'.$lang["last"].'</a>';
			}	
		}
	}
$mysqli->close();
?> 