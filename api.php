<?php

	define("API-JDD", 'Control Api Juegos del dinero');

	if (!defined('ROOT')){
		define('ROOT','../../include/');
	}

	require_once(ROOT.'class/login.class.php');

	// Requests from the same server don't have a HTTP_ORIGIN header
	if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
		 $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
	}


	try {
		$API = new login($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);

		switch($API->getEndpoint()){
			case "recharge":
				$API->recharge();
			break;
			case "searchwithdraw":
				$API->SearchWithdraw();
			break;
			case "dowithdraw":
				$API->PayWithdraw();
			break;
		}
	} catch (Exception $e) {
		die(json_encode($e->getMessage()));
	}


?>
