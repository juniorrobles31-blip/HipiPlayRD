<?php   
if(isset($_GET[GOOD])){ 
	echo '<div class="alert alert-success"> <b> '.PROCESS_EXECUTED.'</b></div>'; 
}elseif(isset($_GET[ERROR])){ 
	echo '<div class="alert alert-danger"><b>** '.$_GET[ERROR].'</b></div>';
}elseif(isset($_GET[GOODMSG])){ 
	echo '<div class="alert alert-success"> '.$_GET[GOODMSG].'</div>'; 
}
?>