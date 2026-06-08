<?php 
//------------------------------------------------
// Ejecución solo del Cron
//------------------------------------------------
	//if (php_sapi_name() !='cli'){exit;}
	
	//if(!empty($_SERVER['REMOTE_ADDR'])){exit;}


	require_once('./include/class/balance.php');
	$exe 			= new DAILY();



?>