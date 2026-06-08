<?php 

if(isset($CtrlPage)){ 
	if($_GET['page']=='retiro'||$_GET['page']=='transferencias'){
		if(isset($_POST['srchusr'])&&!empty($_POST['srchusr'])){	
			$ResultTable = '<thead><tr><th>Usuario</th><th>Nombre</th><th>Apellido</th><th></th></tr></thead>';
			if($stmt = $mysqli->prepare('SELECT `id_user`,`user`,`name`,`last_name` FROM `tbl_user` WHERE (`id_user`=? AND `id_role`=5) OR (`phone`=? AND `id_role`=5);')) {
				if($stmt->bind_param('ii',$_POST['srchusr'],$_POST['srchusr'])){
					if($stmt->execute()){ 
						$stmt->store_result();
						if($stmt->num_rows==0){
							$ResultTable .= '<tr class=highlighter><td colspan="7">BUSQUEDA NO ENCONTRADA</td></tr>';
						}else{
							if($stmt->bind_result($id,$dsUser,$dsName,$dsLastname)){  
								while($stmt->fetch()){
									$edit="<a href=?page=".$_GET['page']."&".$key."=".$id." class=link_table >Seleccionar</a>";
									$ResultTable .=  
										'<tr class=highlighter >'.
										'<td>' . htmlentities($dsUser, ENT_QUOTES) . '</td>'.
										'<td>' . htmlentities($dsName, ENT_QUOTES) . '</td>'.					
										'<td>' . htmlentities($dsLastname, ENT_QUOTES) . '</td>' .					
										'<td>'.$edit.'</td>'.
										'</tr>';
								}
							}
						}
					}
				}$stmt->close();
			}
		
			$mysqli->close();
			echo 	'<table class="tablestyle"  width="100%">', 
					$ResultTable, 
					'</table>';
		}
	}		
}

?>
