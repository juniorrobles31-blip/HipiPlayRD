<?php
ob_start( );


require_once("include/lib/variables.lib.php");
require_once("include/lib/functions.lib.php");
//include("include/lib/setting.lib.php");




$page = ""; if(isset($_GET['page'])){$page =$_GET['page'];}
$page = str_replace(".","",$page);// remueve el punto para evitar que se muevan a carpetas superiores
////-----------------------------------------------------
//// check session login
////-----------------------------------------------------
sec_session_start();
	
//if(isset($_SESSION['ID'])&&isset($_SESSION['role'])){
					
////-----------------------------------------------------
//// session time
////-----------------------------------------------------
	/*
	if ($_SESSION['expireSession'] < time()-$timeout){	
		echo "<script>alert('La session ha expirado222');</script>";
		$urlLogout = true;		
	}else{
		$_SESSION['expireSession'] = time();
		echo "<script>alert('Actualizo: ".$_SESSION['expireSession']."');</script>";
	}*/	

	/*if(isset($urlLogout)){
		$file = "include/gui/logout.inc.php";
	}elseif(!empty($page)){
		if ( in_array($_SESSION['role'], array('ADM','ADS'), true ) ) {
			if($_GET['page']=='logout'){
				$file = "include/gui/logout.inc.php";
			}
		}else{
			$file = "include/gui/".$page.".inc.php";
		}
	}
}else{
  $file = "include/gui/login.inc.php";
}*/
$file = "include/gui/".$page.".inc.php";// Remover cuando este listo
require_once("include/menu/header.php");
$CtrlPage = true;

if(file_exists($file)){ include_once($file); }

require_once 'include/menu/footer.php'; 


ob_end_flush( );
?>