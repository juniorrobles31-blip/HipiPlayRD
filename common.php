<?php

class DISPLAY {

	private  $db;
	private  $balance;
	private	 $trans;
	private	 $result;
	private	 $currency;
	private	 $tblTimer;
	private	 $tblDice1;
	private	 $tblGame;
	private  $tblUser;
	private	 $json;
	private  $rows_per_page;
	private  $numrows;
	private  $result_header;
	private  $format_day;
	private  $format_time;

	public function __construct() {
		require_once('./include/class/dbconfig.php');
		$this->db = new DB();
		$this->result 			= array();
		$this->json 			= array();
		$this->rows_per_page 	= 8;
		$this->numrows 			= 0;
		//formato de fechas
		$this->format_day = "d-M-y";
		$this->format_time = "h:i A";
	}
	//------------------------------------------------
	// FUNCTION DropDown
	//------------------------------------------------

	public function ddGames($id){
		$result='<select id="cboGame" name="cboGame" class="form-control"> <option value="0">Tipo de Juego</option>';
		$data = $this->db->select('SELECT `id_game`, `game` FROM `gms_game` WHERE `id_game` > 0 ORDER BY `game`;','','');

		if ($data['count']>0){
			for ($x = 0; $x < $data['count']; $x++) {
				$selected= '';
				if(!empty($id)){if($id==$data[$x]->id_game){$selected = ' selected ';}}
				  $result .='<option value="'.htmlentities($data[$x]->id_game, ENT_QUOTES).'" '.$selected.'>'.htmlentities($data[$x]->game, ENT_QUOTES).'</option>';
			}
		}
		$result .= '</select>';
		echo $result;
	}

	public function ddValues($id){
		$result='<select id="cboType" name="cboType" class="form-control"> <option value="0">Tipo  de Transacion</option>';
		$data = $this->db->select('SELECT `id_values`, `id_tp_values`, `ds_values` FROM `gms_values` WHERE `id_tp_values` IN (5,7) AND `id_values` NOT IN (19,20) ORDER BY `id_tp_values`, `ds_values`;','','');

		if ($data['count']>0){
			for ($x = 0; $x < $data['count']; $x++) {
				$selected= '';
				if(!empty($id)){if($id==$data[$x]->id_values){$selected = ' selected ';}}
				  $result .='<option value="'.htmlentities($data[$x]->id_values, ENT_QUOTES).'" '.$selected.'>'.htmlentities($data[$x]->ds_values, ENT_QUOTES).'</option>';
			}
		}
		$result .= '</select>';
		echo $result;
	}



	public function getWon($game){
		$json = array();
		$json["STATUS"] = "ERROR";
		$json["INFO"]   = "";
		switch ($game){
			case "puntazo":
				$data = $this->db->select('SELECT `nm_play`, `super_cumulative`, `lowest_puntazo`, `price` FROM `gms_config`;','','');
				if ($data['count']==1){
					$json["price"] = $data[0]->price;
					$json["next_play"] = $data[0]->nm_play;
					$json["super_pool"] = $data[0]->super_cumulative;
				$pool = 0 ;
				$data = $this->db->select('SELECT SUM(`amount`)*-1 as total FROM `gms_transaction` WHERE `nm_play` = ? AND `id_game` = 6 ;',array($json["next_play"]),array('%i'));
				if($data['count']==1){
					$pool = $data[0]->total;
				}

					$json["pool"] = $pool;
					$json["STATUS"] = "OK";
					$json["INFO"] = "game.play";

					if ($json["pool"] < 10000){
						$json["pool"] = '10,000';
					}
					if ($json["super_pool"] < 100000){
						$json["super_pool"] = '100,000';
					}
					$data = $this->db->select('SELECT `id_puntazo`, `nm_play`, `number`, DATE_FORMAT( `entry_date` , "%e-%m-%Y" ) AS `entry_day` FROM `gms_won_puntazo` WHERE `nm_play` = (SELECT max(`nm_play`) FROM `gms_won_puntazo`);','','');
					if($data['count'] > 0){
						$json["date_last"] = $data[0]->entry_day;
						for ($x = 0; $x < $data['count']; $x++) {
							$json["nwom"][] = $data[$x]->number;
						}
					}
				}else{
					$json["INFO"] = "cant find config";
				}
			break;
			case "conoce":
				$json["next_play"] = 1;
			break;
			case "dice.1":
			case "dice.2":
				$data = $this->db->select('SELECT `nm_one` FROM `gms_won_'.$game.'` WHERE `id_won` = (SELECT max(`id_won`) FROM `gms_won_'.$game.'`);','','');
				if($data['count']==1){
					$json["nwon1"] = $data[0]->nm_one;
					$json["STATUS"] = "OK";
				}
			break;
			case "horse":
				$data = $this->db->select('SELECT `nm_one`, `nm_two`, `nm_three`, `nm_four`, `nm_five`, `nm_six` FROM `gms_won_'.$game.'` WHERE `id_won` = (SELECT max(`id_won`) FROM `gms_won_'.$game.'`);','','');
				if($data['count']==1){
					$json["nwon1"] = $data[0]->nm_one;
					$json["nwon2"] = $data[0]->nm_two;
					$json["nwon3"] = $data[0]->nm_three;
					$json["nwon4"] = $data[0]->nm_four;
					$json["nwon5"] = $data[0]->nm_five;
					$json["nwon6"] = $data[0]->nm_six;
					$json["STATUS"] = "OK";
				}
			break;
			case "dice.3":
				$data = $this->db->select('SELECT `nm_one`, `nm_two`, `nm_three` FROM `gms_won_'.$game.'` WHERE `id_won` = (SELECT max(`id_won`) FROM `gms_won_'.$game.'`);','','');
				if($data['count']==1){
					$json["nwon1"] = $data[0]->nm_one;
					$json["nwon2"] = $data[0]->nm_two;
					$json["nwon3"] = $data[0]->nm_three;
					$json["STATUS"] = "OK";
				}
			break;
			case "roulette":
				$data = $this->db->select('SELECT `nm_one`, `place` FROM `gms_won_'.$game.'` WHERE `id_won` = (SELECT max(`id_won`) FROM `gms_won_'.$game.'`);','','');
				if($data['count']==1){
					$json["place"] = $data[0]->place;
					$json["win"] = $data[0]->nm_one;
					$json["STATUS"] = "OK";
				}
			break;
		}

		return $json;
	}


	//------------------------------------------------
	//  function Form
	//------------------------------------------------

	public function formSearchUser($page,$srchusr,$title){
		echo '<div class="row mt">
          		<div class="col-lg-12">
                  <div class="form-panel">
                      <form class="form-horizontal style-form"  id="usrsrch_form" name="usrsrch_form" method="post" action="?page='.$page.'">
                          <h3><i class="fa fa-angle-right"></i> '.$title.'</h3>
	                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Usuario</label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" required="required"   name="srchusr" id="srchusr" value="'.$srchusr.'">
                              </div>
                          </div>
                        <button type="submit" class="btn btn-theme"> Buscar </button>
					  </form>
                  </div>
          		</div>
          	</div>';

	}


	//------------------------------------------------
	//  function tables
	//------------------------------------------------

	public function tblUserSrch($page,$srchusr){

			if(empty($srchusr)){ die('Busqueda esta vacia, verique!');}
			$ResultTable =
				'<div class="row mt">
							 <div class="col-md-12">
							 <div class="content-panel"> <h4><i class="fa fa-angle-right"></i> Resultado</h4>
					<section id="no-more-tables">
																<table class="table table-striped table-advance table-hover">
																		<thead>
																		<tr>
																				<th>Alias</th>
																				<th></th>
																		</tr>
																		</thead>
																		<tbody>';
			$sql = "SELECT `id_user`, `id_zzvm`, `alias`  FROM `gms_user`  WHERE `cd_user` like '%".$srchusr."%'";
			$data = $this->db->select($sql,'','');
			if($data['count']>0){
				for ($x = 0; $x < $data['count']; $x++) {
					$ResultTable .=
					"<tr>" .
					'<td data-title="Alias">' . htmlentities($data[$x]->alias, ENT_QUOTES) . "</td>" .
					'<td data-title="">'.
					 	' <a href="?page=showtransusr&'.KEY.'='.$data[$x]->id_user.'" ><button class="btn btn-success  btn-xs"><i class="fa fa-check"></i></button></a>'.
						' <a href="?page=users&'.KEY.'='.$data[$x]->id_user.'"><button class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></button></a>'.
						' <a href="?page=chguser&'.KEY.'='.$data[$x]->id_user.'" onclick="return confirmDelete()"><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o "></i></button></a>'.
					'</td>'.
					"</tr>";
				}
			}else{
				$ResultTable .= '<tr><td data-title="Usuario">NO hay resultados</td></tr>';
			}

			$ResultTable .='</tbody>
																</table>
														</section>
												</div>
										</div>
								</div>';

			echo 	$ResultTable;
		}



		public function tblUserTrans($IDTRANs,$IDGAMEs,$FROMs,$TOs,$IDUSERs,$IDVALUEs,$NOPLAYs){

				$sqlIDTRANs	 	= '';
				$sqlGAMEs	 	= '';
				$sqlIDUSERs	 	= '';
				$sqlIDVALUEs	= '';
				$sqlNOPLAYs		= '';

				// Filtro  del usuario que registro la transaccion
				if(!empty($IDTRANs)){ $sqlIDTRANs = ' AND A.`id_trans` = '.$IDTRANs.' '; }

				// Filtro del tipo de juego
				if(!empty($IDGAMEs)){ $sqlGAMEs = ' AND A.`id_game` = "'.$IDGAMEs.'" '; }

				// Filtro por usuario
				if(!empty($IDUSERs)){ $sqlIDUSERs = ' AND A.`id_user` = '.$IDUSERs.' '; }

				// Filtro por tipo de transaccion
				if(!empty($IDVALUEs)){ $sqlIDVALUEs = ' AND A.`id_values` = '.$IDVALUEs.' '; }

				// Filtro por tipo de transaccion
				if(!empty($NOPLAYs)){ $sqlNOPLAYs = ' AND A.`nm_play` = '.$NOPLAYs.' '; }


				$sqlOrder = " ORDER BY A.`entry_date` DESC";
				if(empty($FROMs)){ $FROMs = FIRSTDAY;}
				if(empty($TOs)){$TOs = date('Y-m-d', strtotime(TODAY. ' + 1 day'));}

				$sql = 'SELECT  A.`id_trans`, D.`cd_game`, A.`nm_play`, A.`nm_one`, A.`nm_two`, A.`nm_three`, A.`nm_puntazo`, A.`amount`, B.`alias`,C.`ds_values`, D.`game`, A.`entry_date` FROM `gms_transaction` A, `gms_user` B, `gms_values`C, `gms_game`D WHERE A.`id_user`=B.`id_user`AND A.`id_values`=C.`id_values` AND A.`id_game`=D.`id_game` AND STR_TO_DATE(A.`entry_date`,"%Y-%m-%d") BETWEEN "'.$FROMs.'" AND "'.$TOs.'" '.$sqlIDTRANs.' '.$sqlGAMEs.' '.$sqlIDUSERs.' '.$sqlIDVALUEs.' '.$sqlNOPLAYs.' '.$sqlOrder ;

				$data = $this->db->select($sql,'','');

				if (isset($_GET['pageno'])){
					$pageno = ! empty( $_GET['pageno'] ) ? (int) $_GET['pageno'] : 1;
				}else{$pageno = 1;}

				$rows_per_page 	= 25;

				$lastpage      	= ceil($data['count']/$rows_per_page);	//Ensure that $pageno is within range
				$pageno 		= (int)$pageno;
				if ($pageno > $lastpage){$pageno = $lastpage;}
				if ($pageno < 1) {$pageno = 1;}	//Construct LIMIT clause
				$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
				$actual_link = "$_SERVER[REQUEST_URI]";
				$posInic = strpos($actual_link, "?page");
				$actual_link =  substr($actual_link,$posInic);
				$posFin = strpos($actual_link, "pageno");
				if(!empty($posFin)){$actual_link =  substr($actual_link,0,$posFin-1);}
				unset($data['count']);
				$data = array_slice( $data, ($pageno-1)*$rows_per_page, $rows_per_page );
				$datalength = count($data);
					/////// NAVIGATION ///////////////
					$tablePagi= '<table width=50% bgcolor="#000000"><tr>';
					//previous pages
					if ($pageno == 1){
						$tablePagi.="<td>&laquo;</td>";
						$tablePagi.="<td>&lt;</td>";
					}else{
						$tablePagi.= "<td><a href='".$actual_link."&pageno=1'><img src=images/bfrst.gif width=16 height=16/></a</td>";
						$prevpage = $pageno-1;
					// $tablePagi.= ' <td><div class="text-center"><a href="'.$actual_link.'&pageno='.$prevpage.'" class="pagination" ><i class="fa fa-angle-left"></i></a></div></td> ';
						$tablePagi.= " <td><a href='".$actual_link."&pageno=$prevpage'><img src=images/bprv.gif width=16 height=16/> </a></td> ";
					}
					$tablePagi.= " <td> P�g $pageno de $lastpage </td> ";

					//following pages
					if ($pageno == $lastpage){
					//echo " NEXT LAST ";
						$tablePagi.="<td>&gt;</td>";
						$tablePagi.= "<td>&raquo;</td>";
					} else {
					$nextpage = $pageno+1;
					 // $tablePagi.= ' <td><div class="text-center"><a href="'.$actual_link.'&pageno='.$nextpage.'" class="pagination" ><i class="fa fa-angle-right"></i></a></div></td> ';
					$tablePagi.= " <td><a href='".$actual_link."&pageno=$nextpage'><img src=images/bnxt.gif width=16 height=16 /></a></td> ";
					$tablePagi.= " <td><a href='".$actual_link."&pageno=$lastpage'><img src=images/blst.gif width=16 height=16/></a></td> ";
					} // if
					$tablePagi.= "</tr></table>";
					/////// END NAVIGATION //////////////////////////


				$ResultTable = ' <div class="row mt">
		                  <div class="col-md-12">
		                      <div class="content-panel"> '.$tablePagi.'
													<section id="no-more-tables">
		                          <table class="table table-striped table-advance table-hover">
			                  	  	  <hr>
		                              <thead>
		                              <tr>
																		<th>No.</th>
																		<th>Fecha</th>
																		<th>Hora</th>
																		<th>Juego</th>
																		<th>Jugada</th>
																		<th>Usuario</th>
																		<th>Descripcion</th>
																		<th>Monto</th>
		                              </tr>
		                              </thead>
		                              <tbody>';
				if($datalength>0){
					for ($x = 0; $x < $datalength; $x++) {
						$amount=number_format(($data[$x]->amount),2,".",",");
						if($data[$x]->cd_game=="puntazo"){
								$nmPlay = $data[$x]->nm_puntazo;
						}else{
								$nmPlay = $data[$x]->nm_play;
						}
						//formato de fechas
					 $date = date_create($data[$x]->entry_date);
					 $day = date_format($date,$this->format_day);
					 $time = date_format($date,$this->format_time);

						$ResultTable .=
						'<tr>' .
						'<td data-title="No.">' . htmlentities($data[$x]->id_trans, ENT_QUOTES) .'</td>'.
						'<td data-title="Fecha">' . htmlentities($day, ENT_QUOTES) .'</td>'.
						'<td data-title="Hora">' .  htmlentities($time, ENT_QUOTES) .'</td>'.
						'<td data-title="Juego">' . htmlentities($data[$x]->game, ENT_QUOTES) .'</td>'.
						'<td data-title="Jugada">' . htmlentities($nmPlay, ENT_QUOTES) .'</td>'.
						'<td data-title="Usuario">' . htmlentities($data[$x]->alias, ENT_QUOTES) .'</td>'.
						'<td data-title="Descripcion">' . htmlentities($data[$x]->ds_values, ENT_QUOTES) .'</td>'.
						'<td data-title="Monto"> ' . htmlentities($amount, ENT_QUOTES) .'</td>'.
						'</tr>';
					}
				}else{
					$ResultTable .= "<tr><td>NO hay resultados</td></tr>";
				}

				$ResultTable .='</tbody>
		                          </table>
															</section>
		                      </div>
		                  </div>
		              </div>';

				echo 	$ResultTable;
			}


		public function tblGamesResults($IDGAMEs,$FROMs,$TOs,$NOPLAYs){
				$sqlGAMEs	 	= '';
				$sqlNOPLAYs		= '';

				// Filtro del tipo de juego
				if(!empty($IDGAMEs)){ $sqlGAMEs = ' AND  t1.`id_game` = "'.$IDGAMEs.'" '; }

				// Filtro por tipo de transaccion
				if(!empty($NOPLAYs)){ $sqlNOPLAYs = ' AND t1.`nm_play` = '.$NOPLAYs.' '; }


				$sqlOrder = " ORDER BY t1.`entry_date` DESC, t1.`game` ASC";

				if(empty($FROMs)){ $FROMs = FIRSTDAY;}
				if(empty($TOs)){$TOs = date('Y-m-d', strtotime(TODAY. ' + 1 day'));}

			 	$sql ='SELECT t1.* FROM ( SELECT 1 AS `id_game`, "Dado directo" as `game`, `id_won`, `nm_one`, 0 AS `nm_two`, 0 AS `nm_three`, `entry_date` FROM `gms_won_dice.1` t1 WHERE STR_TO_DATE(`entry_date`,"%Y-%m-%d")  BETWEEN "'.$FROMs.'" AND "'.$TOs.'"  UNION ALL SELECT 2 AS `id_game`, "Dado tripleta" as `game`, `id_won`, `nm_one`, `nm_two`, `nm_three`, `entry_date` FROM `gms_won_dice.3` WHERE STR_TO_DATE(`entry_date`,"%Y-%m-%d")  BETWEEN "'.$FROMs.'" AND "'.$TOs.'" UNION ALL  SELECT 5 AS `id_game`, "Super dado" as `game`, `id_won`, `nm_one`, 0 AS `nm_two`, 0 AS `nm_three`, `entry_date` FROM `gms_won_dice.2` WHERE STR_TO_DATE(`entry_date`,"%Y-%m-%d")  BETWEEN "'.$FROMs.'" AND "'.$TOs.'" UNION ALL SELECT 4 AS `id_game`, "Ruleta" as `game`, `id_won`, `nm_one`, 0 AS `nm_two`, 0 AS `nm_three`, `entry_date` FROM `gms_won_roulette` WHERE STR_TO_DATE(`entry_date`,"%Y-%m-%d")  BETWEEN "'.$FROMs.'" AND "'.$TOs.'" ) t1  WHERE (t1.`id_game`, t1.`id_won`) IN (SELECT `id_game`, `nm_play` FROM `gms_transaction` WHERE STR_TO_DATE(`entry_date`,"%Y-%m-%d")  BETWEEN "'.$FROMs.'" AND "'.$TOs.'" GROUP BY `id_game`, `nm_play`)  '.$sqlGAMEs.' '.$sqlGAMEs.' '.$sqlNOPLAYs.' '.$sqlOrder;

				$data = $this->db->select($sql,'','');

				if (isset($_GET['pageno'])){
					$pageno = ! empty( $_GET['pageno'] ) ? (int) $_GET['pageno'] : 1;
				}else{$pageno = 1;}

				$rows_per_page 	= 25;

				$lastpage      	= ceil($data['count']/$rows_per_page);	//Ensure that $pageno is within range
				$pageno 		= (int)$pageno;
				if ($pageno > $lastpage){$pageno = $lastpage;}
				if ($pageno < 1) {$pageno = 1;}	//Construct LIMIT clause
				$limit = ' LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
				$actual_link = "$_SERVER[REQUEST_URI]";
				$posInic = strpos($actual_link, "?page");
				$actual_link =  substr($actual_link,$posInic);
				$posFin = strpos($actual_link, "pageno");
				if(!empty($posFin)){$actual_link =  substr($actual_link,0,$posFin-1);}
				unset($data['count']);
				$data = array_slice( $data, ($pageno-1)*$rows_per_page, $rows_per_page );
				$datalength = count($data);
					/////// NAVIGATION ///////////////
					$tablePagi= '<table width=50% bgcolor="#000000"><tr>';
					//previous pages
					if ($pageno == 1){
						$tablePagi.="<td>&laquo;</td>";
						$tablePagi.="<td>&lt;</td>";
					}else{
						$tablePagi.= "<td><a href='".$actual_link."&pageno=1'><img src=images/bfrst.gif width=16 height=16/></a</td>";
						$prevpage = $pageno-1;
					// $tablePagi.= ' <td><div class="text-center"><a href="'.$actual_link.'&pageno='.$prevpage.'" class="pagination" ><i class="fa fa-angle-left"></i></a></div></td> ';
						$tablePagi.= " <td><a href='".$actual_link."&pageno=$prevpage'><img src=images/bprv.gif width=16 height=16/> </a></td> ";
					}
					$tablePagi.= " <td> P�g $pageno de $lastpage </td> ";

					//following pages
					if ($pageno == $lastpage){
					//echo " NEXT LAST ";
						$tablePagi.="<td>&gt;</td>";
						$tablePagi.= "<td>&raquo;</td>";
					} else {
					$nextpage = $pageno+1;
					 // $tablePagi.= ' <td><div class="text-center"><a href="'.$actual_link.'&pageno='.$nextpage.'" class="pagination" ><i class="fa fa-angle-right"></i></a></div></td> ';
					$tablePagi.= " <td><a href='".$actual_link."&pageno=$nextpage'><img src=images/bnxt.gif width=16 height=16 /></a></td> ";
					$tablePagi.= " <td><a href='".$actual_link."&pageno=$lastpage'><img src=images/blst.gif width=16 height=16/></a></td> ";
					} // if
					$tablePagi.= "</tr></table>";
					/////// END NAVIGATION //////////////////////////


				$ResultTable = ' <div class="row mt">
											<div class="col-md-12">
													<div class="content-panel"> '.$tablePagi.'
													<section id="no-more-tables">
															<table class="table table-striped table-advance table-hover">
																<hr>
																	<thead>
																	<tr>
																		<th>Fecha</th>
																		<th>Hora</th>
																		<th>Juego</th>
																		<th>Jugada</th>
																		<th>No 1</th>
																		<th>No 2</th>
																		<th>No 3</th>
																		<th>No 4</th>
																		<th>No 5</th>
																		<th>No 6</th>
																		<th>Negro</th>
																		<th>Rojo</th>
																		<th>Total apostado</th>
																		<th>Total pagado</th>
																		<th>Beneficio</th>
																	</tr>
																	</thead>
																	<tbody>';
					if($datalength>0){
						for ($x = 0; $x < $datalength; $x++) {
							//calculo de los juegos
							$one 				= 0;
							$two 				= 0;
							$three 			= 0;
							$four 			= 0;
							$five 			= 0;
							$six 				= 0;
							$black 			= 0;
							$red 				= 0;
							$total_pay	= 0;

						//--- Search bets
						 switch ($data[$x]->id_game){
						 	case 1://Dado directo
										$dataRslt 	= $this->_dice1($data[$x]->id_won);
										$one 				= (int)$dataRslt[1];
										$two 				= (int)$dataRslt[2];
										$three 			= (int)$dataRslt[3];
										$four 			= (int)$dataRslt[4];
										$five 			= (int)$dataRslt[5];
										$six 				= (int)$dataRslt[6];
										$total_pay	= number_format($this->_dice1_winners($data[$x]->id_won),2,".",",");
						 		break;
							case 2://Dado tripleta
									  $dataRslt 	= $this->_dice3($data[$x]->id_won);
										$one 				= (int)$dataRslt[1];
										$two 				= (int)$dataRslt[2];
										$three 			= (int)$dataRslt[3];
										$four 			= (int)$dataRslt[4];
										$five 			= (int)$dataRslt[5];
										$six 				= (int)$dataRslt[6];
										$total_pay	= number_format($this->_dice3_winners($data[$x]->id_won),2,".",",");
								 break;
							case 3://caballos
							case 5://Super dado
										$dataRslt 	= $this->_dice2($data[$x]->id_won);
										$one 				= (int)$dataRslt[1];
										$two 				= (int)$dataRslt[2];
										$three 			= (int)$dataRslt[3];
										$four 			= (int)$dataRslt[4];
										$five 			= (int)$dataRslt[5];
										$six 				= (int)$dataRslt[6];
										$total_pay	= number_format($this->_dice2_winners($data[$x]->id_won),2,".",",");
								break;
							case 4://Ruleta
										$dataRslt 	= $this->_roulette($data[$x]->id_won);
										$black 			= (int)$dataRslt[1];
										$red 				= (int)$dataRslt[2];
										$total_pay	= number_format($this->_roulette_winners($data[$x]->id_won),2,".",",");
							 break;
						 }
						$total_bet 	= number_format($one+$two+$three+$four+$five+$six+$black+$red,2,".",",");
						$profit			= number_format($total_bet-$total_pay,2,".",",");

						 //formato de fechas
						$date = date_create($data[$x]->entry_date);
						$day = date_format($date,$this->format_day);
						$time = date_format($date,$this->format_time);

						$ResultTable .=
						'<tr>' .
						'<td data-title="Fecha">' . htmlentities($day, ENT_QUOTES) .'</td>'.
						'<td data-title="Hora">' . htmlentities($time, ENT_QUOTES) .'</td>'.
						'<td data-title="Juego">' . htmlentities($data[$x]->game, ENT_QUOTES) .'</td>'.
						'<td data-title="Jugada">' . htmlentities($data[$x]->id_won, ENT_QUOTES) .'</td>'.
						'<td data-title="No.1">' . htmlentities($one, ENT_QUOTES) .'</td>'.
						'<td data-title="No.2">' . htmlentities($two, ENT_QUOTES) .'</td>'.
						'<td data-title="No.3">' . htmlentities($three, ENT_QUOTES) .'</td>'.
						'<td data-title="No.4">' . htmlentities($four, ENT_QUOTES) .'</td>'.
						'<td data-title="No.5">' . htmlentities($five, ENT_QUOTES) .'</td>'.
						'<td data-title="No.6">' . htmlentities($six, ENT_QUOTES) .'</td>'.
						'<td data-title="Negro">' . htmlentities($black, ENT_QUOTES) .'</td>'.
						'<td data-title="Rojo">' . htmlentities($red, ENT_QUOTES) .'</td>'.
						'<td data-title="Total apostado">' . htmlentities($total_bet, ENT_QUOTES) .'</td>'.
						'<td data-title="Total pagado">' . htmlentities($total_pay, ENT_QUOTES) .'</td>'.
						'<td data-title="Beneficio">' . htmlentities($profit, ENT_QUOTES) .'</td>'.
							'<td data-title="Detalle">'.
								' <a href="?page=showtransusr&game='.$data[$x]->id_game.'&play='.$data[$x]->id_won.'" ><button class="btn btn-success  btn-xs"><i class="fa fa-reorder"></i></button></a>'.
							'</td>'.
					 '</tr>';
					}
				}else{
					$ResultTable .= "<tr><td>NO hay resultados</td></tr>";
				}


				$ResultTable .='</tbody>
															</table>
															</section>
													</div>
											</div>
									</div>';

				echo 	$ResultTable;
			}


			private function _dice1($nm_play){
				$num_uno=0;
				$num_dos=0;
				$num_tres=0;
				$num_cuatro=0;
				$num_cinco=0;
				$num_seis=0;

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_uno = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_dos = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 3 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_tres = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 4 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_cuatro = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 5 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $tnum_cinco = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 6 AND `nm_play`= ? AND id_game = 1 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_seis = $data[0]->amount; }

				return array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);

			}

			private function _dice2($nm_play){
				$num_uno=0;
				$num_dos=0;
				$num_tres=0;
				$num_cuatro=0;
				$num_cinco=0;
				$num_seis=0;

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 1 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 1 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 1 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 1 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 1 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 1 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_uno = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 2 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 2 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 2 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 2 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 2 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 2 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_dos = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 3 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 3 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 3 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 3 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 3 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 3 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_tres = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 4 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 4 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 4 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 4 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 4 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 4 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_cuatro = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 5 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 5 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 5 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 5 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 5 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 5 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_cinco = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE (`nm_one` = 6 AND `nm_play`= ?  AND id_game = 5  AND `id_values`=13) OR (`nm_two` = 6 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_three` = 6 AND `nm_play`= ? AND id_game = 5 AND `id_values`=13) OR (`nm_one` = 6 AND `nm_play`= ?  AND id_game = 3  AND `id_values`=13) OR (`nm_two` = 6 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13) OR (`nm_three` = 6 AND `nm_play`= ? AND id_game = 3 AND `id_values`=13);',array($nm_play,$nm_play,$nm_play,$nm_play,$nm_play,$nm_play),array('%i','%i','%i','%i','%i','%i'));
				if($data['count']==1){ $num_seis = $data[0]->amount*-1; }

				return array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);

			}

			private function _dice3($nm_play){
				$num_uno=0;
				$num_dos=0;
				$num_tres=0;
				$num_cuatro=0;
				$num_cinco=0;
				$num_seis=0;

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_uno = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_dos = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 3 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_tres = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 4 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_cuatro = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 5 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_cinco = $data[0]->amount; }

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 6 AND `nm_play`= ? AND id_game = 2 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $num_seis = $data[0]->amount; }

				return array(1=>$num_uno, 2=>$num_dos, 3=>$num_tres, 4=>$num_cuatro, 5=>$num_cinco, 6=>$num_seis);

			}


			private function _roulette($nm_play){
				$black=0;
				$red=0;

				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 1 AND `nm_play`= ? AND id_game = 4 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $black = $data[0]->amount*-1; }

				$data = $this->db->select('SELECT  IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_one` = 2 AND `nm_play`= ? AND `id_game` = 4 AND `id_values`=13;',array($nm_play),array('%i'));
				if($data['count']==1){ $red = $data[0]->amount*-1; }

				return array(1=>$black, 2=>$red);

			}


			//---------------------------
			// Winners
			//---------------------------
			private function _dice1_winners($nm_play){//Dado directo
				$amount=0;
				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_play`= ? AND id_game = 1 AND `id_values`=14;',array($nm_play),array('%i'));
				if($data['count']==1){ $amount = $data[0]->amount;}
				return $amount;
			}

			private function _dice2_winners($nm_play){ //Caballos y Superdado
				$amount=0;
				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_play`= ? AND id_game IN (5,3) AND `id_values`=14;',array($nm_play),array('%i'));
				if($data['count']==1){ $amount = $data[0]->amount;}
				return $amount;
			}

			private function _dice3_winners($nm_play){ //Dado Tripleta
				$amount=0;
				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_play`= ? AND id_game = 2 AND `id_values`=14;',array($nm_play),array('%i'));
				if($data['count']==1){ $amount = $data[0]->amount;}
				return $amount;
			}

			private function _roulette_winners($nm_play){ //Ruleta
				$amount=0;
				$data = $this->db->select('SELECT IFNULL(sum(amount),0) as amount FROM `gms_transaction` WHERE `nm_play`= ? AND id_game = 4 AND `id_values`=14;',array($nm_play),array('%i'));
				if($data['count']==1){ $amount = $data[0]->amount;}
				return $amount;
			}





}
?>
