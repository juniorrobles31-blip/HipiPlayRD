<?php 
//include('../include/lib/ctrlsessionheader.php');
//if(isset($_SESSION[$sid])){
//redirigir("Location:?page=logout");
//exit;
//}

//redirigir("Location:?page=login&$wrong_empty=$true");
//exit;


if(isset($CtrlPage)){ 
	if(!isset($_GET['clave']) || !isset($_GET['id']) || !isset($_GET['tel']) || empty($_GET['clave']) || empty($_GET['id'])|| empty($_GET['tel'])){
		redirigir("Location:?page=login&$wrong_empty=$true");
		exit;
	}

$pass = $_GET['clave'];
$id = $_GET['id'];
$phone = $_GET['tel'];
$access = 0;

if($stmtq = $mysqli->prepare('SELECT `id_access` FROM `tb_access` WHERE sha1(CONCAT(`user_access`,`pass_access`,DATE_FORMAT(sysdate(),"%m%d"))) = ? ;')){
	if($stmtq->bind_param('s', $pass)){
		if($stmtq->execute()){
			if($stmtq->store_result()){
				$access = $stmtq->num_rows;
			}
		}
	} 
	$stmtq->close();
}

if($access == 1){
	if($stmtq = $mysqli->prepare('SELECT `id_user` FROM `tb_user` WHERE `id_foreign_user` = ? AND `telefono` = ? ;')){
		if($stmtq->bind_param('ss',$id,$phone)){
			if($stmtq->execute()){
				if($stmtq->store_result()){
					$num_rows = $stmtq->num_rows;
					if($num_rows == 1){
						if($stmtq->bind_result($id_user)){
							while($stmtq->fetch()){	
								$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.	
								$userID = preg_replace("/[^0-9]+/", "", $id_user); // XSS protection as we might print this value
								$_SESSION['id'] = $userID; 
								$FuserID = preg_replace("/[^0-9]+/", "", $id); // XSS protection as we might print this value
								$_SESSION['fid'] = $FuserID; 
								//$userName = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $name); // XSS protection as we might print this value
								$_SESSION['usuario'] = $phone;
								$_SESSION['start'] = time(); // taking now logged in time
								////// Session timeout	//
								$_SESSION['role']=3;
								$_SESSION['expire'] = $_SESSION['start'] + ($timeout) ; // ending a session in 15 minutes from the starting time
								////Login successful.
								//$mysqli->close();
								redirigir("Location:?page=game");
								exit;
							}
						}
					}
				}	
			}				
		}
		$stmtq->close();
	}
}
redirigir("Location:?page=login&$wrong_login=$true");
exit;

}
//include('../include/lib/ctrlsessionfooter.php');
  ?>
 
 
 
