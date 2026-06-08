<?php  
require_once ("./include/class/userservice.php");
$user = new USERSERVICE();
$user->logout();

redirigir('Location: ?page=home');
exit; 	

?> 