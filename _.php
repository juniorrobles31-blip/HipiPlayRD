<?php 
//------------------------------------------------
// Ejecución solo del Cron
//------------------------------------------------
	//if (php_sapi_name() !='cli'){exit;}
	
	//if(!empty($_SERVER['REMOTE_ADDR'])){exit;}


	require_once('./include/class/time.php');
	$exe 			= new ROLL();
    //$game_mode	    = $exe->game_next_roll();
	$game_mode = "dice.1";
	$trans 			= $exe->roll($game_mode);
	$server_time 	= date("Y-m-d H:i:s",$trans->server_time);
	if($trans->difLastGame>=59){
		require('./include/class/winner.php');
		$exe = new WIN($server_time,$game_mode);
	}		

?>