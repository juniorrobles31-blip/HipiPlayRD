<?php
	if (!isset($CtrlPage)){exit;}
	require_once('./include/class/common.php');
	$display = new DISPLAY();

	$USER = "";

	if(isset($_POST['srchusr'])){
		$USER = $_POST['srchusr'];
	}

	$display->formSearchUser($_GET['page'],$USER,'Busqueda de usuario');

	if(!empty($USER)){
		$display->tblUserSrch($_GET['page'],$USER);
	}


?>
