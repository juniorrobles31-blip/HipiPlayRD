<?php 
if(isset($CtrlPage)){ 

	if(isset($IDs)&&isset($MDs)&&isset($FROMs)&&isset($TOs)&&isset($ROLs)&&isset($IDADMs)){	
	
	$numrows = 1;
	$sql = 'SELECT `id_transaction`,CONCAT(`user`," - ",`name`,",",`last_name`) as `usr`,`amount`, DATE_FORMAT( B.`entry_date` , "%e-%M-%Y" ) AS `day`, DATE_FORMAT( B.`entry_date` , "%h:%i %p" ) AS `time`, `ds_values`,B.`phone` FROM `tbl_user` A, `tbl_transaction` B, `tbl_values` C  WHERE A.`id_user`= B.`id_user_trans` AND B.`id_user_adm` = '.$IDADMs.' AND B.`id_user_trans` IN ('.$IDs.') AND B.`id_values` = C.`id_values` AND B.`id_module` IN ('.$MDs.') AND STR_TO_DATE(B.`entry_date`,"%Y-%m-%d") BETWEEN "'.$FROMs.'" AND "'.$TOs.'" ';	
	if($stmtProd = $mysqli->prepare($sql)){  
		if($stmtProd->execute()){
			$stmtProd->store_result();
			$numrows =$stmtProd->num_rows;
		}$stmtProd->close();
	}

		//Obtain the required page number		
	if (isset($_GET['pageno'])){
		$pageno = $_GET['pageno'];
	}else{
		$pageno = 1;
	} 

	$rows_per_page = 25;
	//End Calculate number of row
	$lastpage      = ceil($numrows/$rows_per_page);
	//Ensure that $pageno is within range
	$pageno = (int)$pageno;
	if ($pageno > $lastpage) {
	$pageno = $lastpage;
	} 
	if ($pageno < 1) {
	$pageno = 1;
	} 
	//Construct LIMIT clause
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	
	$actual_link = "$_SERVER[REQUEST_URI]";
	
	$posInic = strpos($actual_link, "?page");	
	$actual_link =  substr($actual_link,$posInic);
	
	$posFin = strpos($actual_link, "pageno");	
	
	if(!empty($posFin)){
	$actual_link =  substr($actual_link,0,$posFin-1);
	}
	
	///////************************** NAVIGATION *******************************/////////////////////////////////////////
	echo "<table width=50%><tr>";
	//previous pages				
	if ($pageno == 1){
		echo"<td><img src=images/dfrst.gif width=16 height=16/></td>";
		echo"<td><img src=images/dprv.gif width=16 height=16/></td>";
	}else{
		echo "<td><a href='".$actual_link."&pageno=1'><img src=images/bfrst.gif width=16 height=16/></a</td>";
		$prevpage = $pageno-1;
		echo " <td><a href='".$actual_link."&pageno=$prevpage'><img src=images/bprv.gif width=16 height=16/> </a></td> ";
	} 
	echo " <td> Página $pageno de $lastpage </td> ";
	
	//following pages
	if ($pageno == $lastpage){
	//echo " NEXT LAST ";
		echo"<td><img src=images/dnxt.gif width=16 height=16 /></td>";
		echo "<td><img src=images/dlst.gif width=16 height=16 /></td>";
	} else {
	$nextpage = $pageno+1;
	echo " <td><a href='".$actual_link."&pageno=$nextpage'><img src=images/bnxt.gif width=16 height=16 /></a></td> ";									
	echo " <td><a href='".$actual_link."&pageno=$lastpage'><img src=images/blst.gif width=16 height=16/></a></td> ";
	} // if	
	echo "</tr></table>";
	///////************************** END NAVIGATION *******************************/////////////////////////////////////////
	
	
	
		$total=0;		 
		$ResultTable = '<thead><tr><th>No. Transacción</th><th>Fecha</th><th>Usuario</th><th>Descripción</th><th>Otros</th><th>Balance</th></thead>';
		 $sql .= ' ORDER BY `id_transaction` DESC '. $limit.';';	
		if($stmt = $mysqli->prepare($sql)) {
				if($stmt->execute()){ 
					$stmt->store_result();
					if($stmt->num_rows==0){
						$ResultTable .= '<tr class=highlighter><td colspan="7">NO HAY RESULTADOS</td></tr>';
					}else{
						if($stmt->bind_result($id,$dsUser,$dsMonto,$day,$time,$dsValue,$dsTelefono)){  
							while($stmt->fetch()){
								$day = str_replace($eng,$esp,$day);
								if($ROLs=='POS'){$dsMonto=$dsMonto*-1;}								
								$total += $dsMonto;									
								$ResultTable .=  
												'<tr class=highlighter >'.
												'<td>' . htmlentities($id, ENT_QUOTES) . '</td>' .
												'<td>' . htmlentities($day, ENT_QUOTES) . ' ' . htmlentities($time, ENT_QUOTES) . ' </td>' .									
												'<td>' . htmlentities($dsUser, ENT_QUOTES) . '</td>' .
												'<td>' . htmlentities($dsValue, ENT_QUOTES) . '</td>' .	
												'<td>' . htmlentities($dsTelefono, ENT_QUOTES) . '</td>' .										
												'<td>' . htmlentities(number_format($dsMonto, 2), ENT_QUOTES) . '</td>' .						
												'</tr>';
							}
						}
				}
			}$stmt->close();
		}
		
		$ResultTable .=  
						'<tr bgcolor="#666666">'.
						'<td colspan="5"><strong><font color="#fff">Subtotal</font></strong></td>' .						
						'<td><strong><font color="#fff">' . htmlentities(number_format($total, 2), ENT_QUOTES) . '</font></strong></td>' .						
						'</tr>';

		$mysqli->close();

		echo 	'<table class="tablestyle"  width="100%">', 
				$ResultTable, 
				'</table>';
	}
	

}
//include('../include/lib/ctrlsessionfooter.php');
  ?>
