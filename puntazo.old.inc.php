<?php 
	$GAME_MODE = "puntazo";
	require_once("include/gui/game.ui.php");
	
	function error(){
		if (mysqli_errno()) {
			printf("Connect failed: %s\n", mysqli_errno());
		}	
	}
	
	// INSET
	if (isset($_GET["install"])){
		echo "Installing database<br>";
		$data = "DROP TABLE IF EXISTS `tb_puntazo`;";
		if ($stmt = $mysqli->prepare($data)){			
			//if ($stmt->execute()){echo "drop previous table<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
		
		$data = "CREATE TABLE IF NOT EXISTS `tb_puntazo` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',`sold` tinyint(4) DEFAULT NULL COMMENT 'vendido?',`date` datetime DEFAULT NULL COMMENT 'fecha de venta',`owner` varchar(13) DEFAULT NULL COMMENT 'ID del comprador',PRIMARY KEY (`id`), KEY `id` (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Puntazo Millonario' AUTO_INCREMENT=1 ;";
		if ($stmt = $mysqli->prepare($data)){			
			if ($stmt->execute()){echo "create table<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
		$data = "INSERT INTO `tb_puntazo` (`id`, `sold`, `date`, `owner`) VALUES (1, NULL, NULL, NULL),(2, NULL, NULL, NULL),(3, NULL, NULL, NULL),(4, NULL, NULL, NULL),(5, NULL, NULL, NULL),(6, NULL, NULL, NULL),(7, NULL, NULL, NULL),(8, NULL, NULL, NULL),(9, NULL, NULL, NULL),(10, NULL, NULL, NULL),(11, NULL, NULL, NULL),(12, NULL, NULL, NULL),(13, NULL, NULL, NULL),(14, NULL, NULL, NULL),(15, NULL, NULL, NULL),(16, NULL, NULL, NULL),(17, NULL, NULL, NULL),(18, NULL, NULL, NULL),(19, NULL, NULL, NULL),(20, NULL, NULL, NULL);	";
		if ($stmt = $mysqli->prepare($data)){			
			if ($stmt->execute()){echo "insert data<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
	}
	
	//include("include/gui/game.ui.php");	
	
	//echo $_SESSION['usuario'];
	
	// Agregar registros
	/*
	$SQL_vaciar = "TRUNCATE `tb_puntazo`;";
	$SQL_insert = "INSERT INTO `tb_puntazo` (`id`, `sold`, `date`, `owner`) VALUES (NULL, NULL, NULL, NULL)";
	$SQL_clear  = "UPDATE `tb_puntazo` SET `sold` = NULL,`date`=NULL,`owner`=NULL WHERE true;";
	
	$data = "INSERT INTO `tb_puntazo` (`id`) VALUES (NULL)";
	for ($i=1; $i < 1000000; $i++){
		$data .= ",(NULL)";
	}
	die($data);
	*/
	/*
	// INSERT
	$data = $SQL_insert;
	for ($i=1; $i < 1000000; $i++){
		$data .= ",(NULL,NULL,NULL,NULL)";
	}
	if ($stmt = $mysqli->prepare($data)){			
		$stmt->execute();
		$stmt->close(); 									
	}	
	
	// Clear registros
	if ($stmt = $mysqli->prepare('UPDATE `tb_puntazo` SET `sold` = NULL,`date`=NULL,`owner`=NULL WHERE true;')){			
		$stmt->execute();
		$stmt->close(); 									
	}
	*/
	
?>
<script> 
	GAME_MODE        = "<?=$GAME_MODE;?>";
	GLOBAL.NEXT_PLAY = <?php echo $time->nextPlay($GAME_MODE);?>;	
</script>

<style>
.dollar{
	box-shadow: inset 0 0 80px 0px #006FCB !important;
	-webkit-box-shadow: inset 0 0 80px 0px #006FCB !important;
}
.dollar:hover{
	box-shadow: inset 0 0 80px 0px #FC3 !important;
	-webkit-box-shadow: inset 0 0 80px 0px #FC3 !important;
}
.sphere {
	width: 200px;
	height: 200px;
	line-height: 200px;
	display:inline-block;
	text-align: center;
	color: #FFF;
	margin: auto auto;
	border-radius: 100%;
	box-shadow: inset 0 0 80px 0px #66CB01;
	-webkit-box-shadow: inset 0 0 80px 0px #66CB01;
   } 
.sphere:hover {
	box-shadow: inset 0 0 80px #FC3;
	cursor: pointer;
	-webkit-box-shadow: inset 0 0 80px #FC3;
 }  
.sphere em {
	display: block;
	-webkit-animation-name: dazzle;
	-webkit-animation-duration: 1s;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-direction: alternate;
	-webkit-animation-timing-function: ease-in-out;
	-webkit-border-radius: 25px;
	margin: auto auto;
	margin-top: -120px;
	width: 27px;
	height: 27px;
} 
.puntazo_icon{
	width:32px;
	height:32px;
	display:inline-block;
	text-align: center;
	line-height:32px;
	border-radius: 100%; 
	box-shadow: inset 0 0 20px 0px #66CB01;
	-webkit-box-shadow: inset 0 0 20px 0px #66CB01;
}
.puntazo_icon_us{
	width:32px;
	height:32px;
	display:inline-block;
	text-align: center;
	line-height:32px;
	border-radius: 100%; 
	box-shadow: inset 0 0 20px 0px #006FCB;
	-webkit-box-shadow: inset 0 0 20px 0px #006FCB;
}

@-webkit-keyframes dazzle {
	0% {
		-webkit-box-shadow: 
			-50px  40px  2px -13px rgba(255,255,255,0), 
			-60px  60px  2px -13px rgba(255,255,255,1), 
			-35px -20px  2px -12px rgba(255,255,255,0), 
			 40px  60px  3px -13px rgba(235,255,255,1), 
			 30px -50px  2px -13px rgba(255,255,255,0), 
			-50px  35px  3px -11px rgba(255,255,235,1), 
			-25px -20px  3px -13px rgba(235,255,235,0), 
			-40px  10px  4px -11px rgba(255,255,255,1), 
			-40px -65px  1px -13px rgba(255,245,255,0), 
			-10px  50px  3px -12px rgba(255,255,255,1), 
			 10px -30px  3px -12px rgba(235,255,255,0), 
			-30px -20px  2px -12px rgba(255,245,255,1), 
			 70px  40px  3px -13px rgba(255,255,255,0), 
			 20px  50px  2px -13px rgba(205,255,255,1), 
			-40px  45px  6px -11px rgba(255,255,255,0), 
			-35px -60px  3px -13px rgba(255,255,225,1), 
			-30px  40px  4px -11px rgba(255,255,255,0), 
			-70px -55px  1px -13px rgba(255,255,255,1), 
			-10px  50px  3px -12px rgba(235,255,255,0), 
			 20px -40px  3px -12px rgba(225,255,255,1), 
			-20px -10px  2px -13px rgba(255,255,255,0), 
			-50px -20px  5px -12px rgba(245,255,255,1), 
			 70px  30px  3px -13px rgba(215,255,245,0), 
			 40px  90px  2px -13px rgba(255,255,255,1), 
			-40px  35px  3px -11px rgba(165,235,215,0), 
			 35px -40px  3px -13px rgba(245,255,255,1), 
			-70px  20px  4px -11px rgba(215,215,235,0), 
			-40px -25px  1px -13px rgba(255,245,255,1), 
			 10px -60px  3px -12px rgba(255,225,245,0), 
			-20px  70px  3px -12px rgba(255,255,255,1), 
			-25px  80px  2px -13px rgba(245,255,255,0), 
			-40px  90px  2px -12px rgba(170,235,255,1), 
			-35px  20px  3px -13px rgba(225,215,255,0), 
			 60px -20px  3px -12px rgba(255,255,255,1), 
			 20px -80px  2px -13px rgba(255,255,255,0), 
			 10px  40px  3px -13px rgba(225,255,255,1), 
			-45px -40px  2px -13px rgba(255,245,255,0), 
			 40px -10px  3px -12px rgba(255,255,255,1), 
			 15px  30px  2px -13px rgba(225,235,255,0), 
			 10px -30px  2px -12px rgba(255,245,245,1), 
			 40px  50px  2px -13px rgba(255,255,255,0), 
			-55px  40px  4px -13px rgba(225,245,255,1), 
			-40px -10px  2px -12px rgba(195,255,175,0), 
			 30px -20px  2px -13px rgba(255,255,255,1), 
			 50px  20px  3px -13px rgba(255,255,255,0), 
			-55px -10px  2px -13px rgba(255,205,255,1), 
			 10px -30px  7px -12px rgba(255,255,255,0), 
			 10px  60px  2px -13px rgba(255,255,255,1), 
			 20px -30px  2px -12px rgba(225,225,225,0), 
			 40px  50px  2px -13px rgba(195,255,255,1), 
			 30px  70px  3px -13px rgba(235,255,235,0), 
			-60px  60px  2px -13px rgba(255,255,255,1), 
			 10px  70px  3px -12px rgba(225,205,255,0), 
			 20px  90px  2px -13px rgba(235,255,235,1), 
			 30px  70px  5px -12px rgba(225,255,225,0), 
			 10px -40px  2px -12px rgba(195,255,175,1), 
			 20px -60px  2px -13px rgba(255,255,255,0), 
			 30px  60px  3px -13px rgba(255,255,255,1), 
			-75px -30px  2px -13px rgba(255,205,255,0), 
			 30px -30px  7px -12px rgba(255,255,255,1), 
			 40px  60px  2px -13px rgba(255,255,255,0), 
			 20px -30px  2px -12px rgba(225,225,225,1), 
			 20px  50px  2px -13px rgba(195,255,255,0), 
			 40px  20px  3px -13px rgba(235,255,235,1), 
			-40px  30px  2px -13px rgba(255,255,255,0); 
	}
	100% {
		-webkit-box-shadow: 
			-50px  40px  2px -13px rgba(255,255,255,1), 
			-60px  60px  2px -13px rgba(255,255,255,0), 
			-35px -20px  2px -12px rgba(255,255,255,1), 
			 40px  60px  3px -13px rgba(235,255,255,0), 
			 30px -50px  2px -13px rgba(255,255,255,1), 
			-50px  35px  3px -11px rgba(255,255,235,0), 
			-25px -20px  3px -13px rgba(235,255,235,1), 
			-40px  10px  4px -11px rgba(255,255,255,0), 
			-40px -65px  1px -13px rgba(255,245,255,1), 
			-10px  50px  3px -12px rgba(255,255,255,0), 
			 10px -30px  3px -12px rgba(235,255,255,1), 
			-30px -20px  2px -12px rgba(255,245,255,0), 
			 70px  40px  3px -13px rgba(255,255,255,1), 
			 20px  50px  2px -13px rgba(205,255,255,0), 
			-40px  45px  6px -11px rgba(255,255,255,1), 
			-35px -60px  3px -13px rgba(255,255,225,0), 
			-30px  40px  4px -11px rgba(255,255,255,1), 
			-70px -55px  1px -13px rgba(255,255,255,0), 
			-10px  50px  3px -12px rgba(235,255,255,1), 
			 20px -40px  3px -12px rgba(225,255,255,0), 
			-20px -10px  2px -13px rgba(255,255,255,1), 
			-50px -20px  5px -12px rgba(245,255,255,0), 
			 70px  30px  3px -13px rgba(215,255,245,1), 
			 40px  90px  2px -13px rgba(255,255,255,0), 
			-40px  35px  3px -11px rgba(165,235,215,1), 
			 35px -40px  3px -13px rgba(245,255,255,0), 
			-70px  20px  4px -11px rgba(215,215,235,1), 
			-40px -25px  1px -13px rgba(255,245,255,0), 
			 10px -60px  3px -12px rgba(255,225,245,1), 
			-20px  70px  3px -12px rgba(255,255,255,0), 
			-25px  80px  2px -13px rgba(245,255,255,1), 
			-40px  90px  2px -12px rgba(170,235,255,0), 
			-35px  20px  3px -13px rgba(225,215,255,1), 
			 60px -20px  3px -12px rgba(255,255,255,0), 
			 20px -80px  2px -13px rgba(255,255,255,1), 
			 10px  40px  3px -13px rgba(225,255,255,0), 
			-45px -40px  2px -13px rgba(255,245,255,1), 
			 40px -10px  3px -12px rgba(255,255,255,0), 
			 15px  30px  2px -13px rgba(225,235,255,1), 
			 10px -30px  2px -12px rgba(255,245,245,0), 
			 40px  50px  2px -13px rgba(255,255,255,1), 
			-55px  40px  4px -13px rgba(225,245,255,0), 
			-40px -10px  2px -12px rgba(195,255,175,1), 
			 30px -20px  2px -13px rgba(255,255,255,0), 
			 50px  20px  3px -13px rgba(255,255,255,1), 
			-55px -10px  2px -13px rgba(255,205,255,0), 
			 10px -30px  7px -12px rgba(255,255,255,1), 
			 10px  60px  2px -13px rgba(255,255,255,0), 
			 20px -30px  2px -12px rgba(225,225,225,1), 
			 40px  50px  2px -13px rgba(195,255,255,0), 
			 30px  70px  3px -13px rgba(235,255,235,1), 
			-60px  60px  2px -13px rgba(255,255,255,0), 
			 10px  70px  3px -12px rgba(225,205,255,1), 
			 20px  90px  2px -13px rgba(235,255,235,0), 
			 30px  70px  5px -12px rgba(225,255,225,1), 
			 10px -40px  2px -12px rgba(195,255,175,0), 
			 20px -60px  2px -13px rgba(255,255,255,1), 
			 30px  60px  3px -13px rgba(255,255,255,0), 
			-75px -30px  2px -13px rgba(255,205,255,1), 
			 30px -30px  7px -12px rgba(255,255,255,0), 
			 40px  60px  2px -13px rgba(255,255,255,1), 
			 20px -30px  2px -12px rgba(225,225,225,0), 
			 20px  50px  2px -13px rgba(195,255,255,1), 
			 40px  20px  3px -13px rgba(235,255,235,0), 
			-40px  30px  2px -13px rgba(255,255,255,1); 
	}
}

@-webkit-keyframes pulse {
	0% {
		-webkit-box-shadow: 
			0 0 5px 0 rgba(51,255,51,0),
			0 0 0px 0 rgba(140,255,180,.9);
	}
	50% {
		-webkit-box-shadow: 
			0 0 5px 10px rgba(51,255,51,0.1),
			0 0 20px 0 rgba(140,255,180,.9);
	}
	100% {
		-webkit-box-shadow: 
			0 0 5px 0 rgba(51,255,51,0),
			0 0 0px 0 rgba(140,255,180,.9);
	}
	
}
					
.pulse {
	-webkit-animation-name: pulse;
	-webkit-animation-duration: .5s;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-timing-function: ease-out;
}
							
</style>

<br>
<br>
<br>

<div style="margin: auto auto;text-align:center; max-width:500px">

<div style="float:left">
	<a href="#bid" data-rel="popup" data-transition="pop" onClick="window.DOLLAR = 0; $('strong#cost').html('120 pesos');" style="text-decoration:none">
        <div class="sphere pulse" >
        <div class="sphere" >
            <div id="puntazo_do">Puntazo</div>
            <em></em>
        </div>
        </div>
	  </a>
      <br>
     <img src="./images/flag_do.jpg" width="30" height="20" alt=""/> Pesos dominicanos
</div>      

<div style="float:right">
	<a href="#bid" data-rel="popup" data-transition="pop" onClick="window.DOLLAR = 1; $('strong#cost').html('120 dolares');" style="text-decoration:none">
        <div class="sphere pulse" >
        <div class="sphere dollar" >
            <div id="puntazo_us">Puntazo</div>
            <em></em>
         </div>
         </div>
	  </a>
      <br>
      <img src="./images/flag_us.jpg" width="32" height="20" alt=""/> US Dollar
</div>

<div id="pop_message" style="float:left; padding-top: 40px; width:100%"></div>

</div>

<div id="bid" data-role="popup" data-position-to="window" class="ui-corner-all">
	<div style="text-align:center; margin:32px;">
    <?php if (!isset($_SESSION["usuario"])){ ?>
       Para jugar debes iniciar session <a href="#panel_user" style="display:inline-block;" class="ui-link"><img src="images/icon_user.png" width="32" style="padding-top:10px"></a>
    <?php }else{ ?>
    	Vas a jugar al puntazo millonario, tiene un costo de $<strong id="cost"></strong>
      	<br>
        <br>
    	<a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="alert" onClick="$('div#bid').popup('close');">Cancelar</a>
 	<a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="check" onClick="buyPuntazo(DOLLAR);">Jugar</a>
    <?php } ?>
    <br>
    </div>
</div> 

<div id="timer" style="display:none"></div>