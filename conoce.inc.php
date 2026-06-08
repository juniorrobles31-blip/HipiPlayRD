<?php 
	$GAME_MODE = "conoce";	
	require_once("include/gui/game.ui.php");
	
	$premios[0]["name"] = 'Premios';	
	$premios[0]["info"] = '<p><strong>3,000</strong> ganadores de una semana TODO INCLUIDO* en uno de los destinos vacacionales mas impresionantes del caribe, República Dominicana </p>
    <p style="font-size:0.8em">* Incluye estadia, transporte  aeropuerto-hotel-aeropuerto, boleto aéreo desde cualquier ciudad de USA o fraccion. La fecha del premio estara sujeta a disponibilidad.</p>';
	$premios[1]["name"] = 'Punta Cana';	
	$premios[1]["info"] = 'Reconocida mundialmente por sus espectaculares playas, hoteles de lujo, lugares románticos y campos de golf.<br>
        <div class="photo"> <img src="http://www.godominicanrepublic.com/wp-content/uploads/2013/10/playa-juanillo-punta-cana11-226x155.jpg" alt="Punta Cana">
        </div>';
	$premios[2]["name"] = 'Samaná';	
	$premios[2]["info"] = 'Salvaje y verde, y posee calas, bahías, cascadas, montañas y vistas impresionantes: hay mucho qué explorar.<br>
        <div class="photo">
            <img src="http://www.godominicanrepublic.com/wp-content/uploads/2013/09/los-haitises-saman-226x155.jpg" alt="Samaná">
        </div>';
	$premios[3]["name"] = 'Puerto Plata';	
	$premios[3]["info"] = 'Las nubes plateadas sobre la cima de la Loma Isabel de Torres fueron las que le dieron el nombre a la ciudad.<br>
        <div class="photo">
            <img src="http://www.godominicanrepublic.com/wp-content/uploads/2013/09/09-Puerto-Plata-CabareteKite-Surf-Cabarete-8-226x155.jpg" alt="Puerto Plata">
        </div>';
	$premios[4]["name"] = 'La Romana';	
	$premios[4]["info"] = 'Definida por la caña de azúcar, el golf, sus playas y la práctica de buceo, es uno de los destinos más visitados del país.<br>
        <div class="photo">
            <img src="http://www.godominicanrepublic.com/wp-content/uploads/2013/12/altos-de-chavon-la-romana-226x155.jpg" alt="La Romana">
        </div>';
	
	
	$project[0]["name"] = 'Causas';
	$project[0]["info"] = 'Una gran parte del dinero recaudado sera destinado a inversion social';
	$project[1]["name"] = 'Acción Callejera';
	$project[1]["info"] = '<p><a href="http://www.accioncallejera.org/index.php">www.accioncallejera.org/index.php</a><strong><a href="http://www.funglode.org/"><br>
  	  </a></strong></p>
    	<p><strong>Acción Callejera Fundación Educativa, INC.</strong></p>
    	<p>Nace como programa educativo en 1989, justo el año de la ratificación de la Convención de los Derechos del Niño. Surge por la necesidad de responder a una realidad social: una cantidad significativa de niños, niñas y adolescentes en busca de fuente de ingresos, que optaban por trabajar como limpiabotas, vendedores de frutas o periódicos, acarreos en los mercados y, en el peor de los casos, limpiar vidrios en las esquinas y semáforos.</p>
    	<br>
        <div class="photo">
            <img src="http://www.alianzaong.org.do/wp-content/uploads/2011/06/logo-accioncallejera-300x87.jpg" alt="Santo Domingo" width="226">
        </div></p>';
	$project[2]["name"] = 'Educa';
	$project[2]["info"] = '<p><strong>Acción por la Educación (EDUCA) </strong></p>
    	<p><strong><a href="http://www.educa.org.do/">http://www.educa.org.do/</a></strong></p>
    	<p>Es una organización no gubernamental, sin fines de lucro, con sede en Santo Domingo, República Dominicana, fundada en marzo de 1989 e incorporada bajo el Decreto No. 286-89 del 31 de julio de 1989, por un grupo de empresarios interesados en contribuir con el mejoramiento de la cobertura y la calidad de la educación básica del país.</p>
    	<br>
        <div class="photo">
            <img src="http://www.alianzaong.org.do/wp-content/uploads/2011/06/logo.png" alt="Samaná">
        </div>';
	$project[3]["name"] = 'Asoc. Rehabilitacion';
	$project[3]["info"] = '<p><a href="https://www.facebook.com/pages/ADR-Asociaci%C3%B3n-Dominicana-de-Rehabilitaci%C3%B3n/184719074904122?fref=ts">www.facebook.com/pages/ADR-Asociaci%C3%B3n-Dominicana-de-Rehabilitaci%C3%B3n/184719074904122?fref=ts</a></p>
    	<p>La <strong>Asociación Dominicana de Rehabilitación </strong>adquirió personería jurídica el 3 de abril de 1963, en Santo Domingo, República Dominicana. Esta Institución fue incorporada mediante decreto No. 126 del Poder Ejecutivo, bajo el nombre de Asociación Pro Rehabilitación de los Lisiados. El 15 de febrero de 1965, por decreto presidencial No. 2099, fue adoptado el nombre de Asociación Pro-Rehabilitación de Inválidos, luego cambiado por el actual, &ldquo;Asociación Dominicana de Rehabilitación&rdquo;, de fecha 10 de abril de 1977, por decreto No. 962.</p>
    	<br>
        <div class="photo">
            <img src="http://www.alianzaong.org.do/wp-content/uploads/2011/06/adr-150x113.jpg" alt="Puerto Plata">
        </div>';
	$project[4]["name"] = 'ADOPEN';
	$project[4]["info"] = 'Surge en 1982 fruto del esfuerzo de un conjunto de mujeres profesionales preocupadas por las condiciones de pobreza en que vivía una gran cantidad de mujeres de escasos recursos y con el objetivo de mejorar las condiciones de la mujer dominicana y su familia a través del crédito y la capacitación. Es una organización apolítica, de beneficio público y servicios a terceras personas en el área de fomento económico, cuyas actividades se encuentran orientadas a ofrecer servicios básicos en beneficio de toda la sociedad.<br>
        <div class="photo">
            <img src="http://www.alianzaong.org.do/wp-content/uploads/2011/06/adopem-150x80.gif" alt="La Romana">
        </div>';

    	
	
	function error(){
		if (mysqli_errno()) {
			printf("Connect failed: %s\n", mysqli_errno());
		}	
	}
	
	// INSERT
	if (isset($_GET["install"])){
		echo "Installing database<br>";
		$data = "DROP TABLE IF EXISTS `tb_conoce`;";
		if ($stmt = $mysqli->prepare($data)){			
			//if ($stmt->execute()){echo "drop previous table<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
		
		$data = "CREATE TABLE IF NOT EXISTS `tb_conoce` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',`sold` tinyint(4) DEFAULT NULL COMMENT 'vendido?',`project` tinyint(4) DEFAULT NULL COMMENT 'ID del proyecto a patrocinar?',`date` datetime DEFAULT NULL COMMENT 'fecha de venta',`owner` varchar(13) DEFAULT NULL COMMENT 'ID del comprador',PRIMARY KEY (`id`), KEY `id` (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Conoce & Disfruta mi Pais' AUTO_INCREMENT=1 ;";
		if ($stmt = $mysqli->prepare($data)){			
			if ($stmt->execute()){echo "create table<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
		$data = "INSERT INTO `tb_conoce` (`id`, `sold`,`project`, `date`, `owner`) VALUES (1, NULL,NULL, NULL, NULL),(2, NULL,NULL, NULL, NULL),(3, NULL,NULL, NULL, NULL),(4, NULL,NULL, NULL, NULL),(5, NULL,NULL, NULL, NULL),(6, NULL,NULL, NULL, NULL),(7, NULL,NULL, NULL, NULL),(8, NULL,NULL, NULL, NULL),(9, NULL,NULL, NULL, NULL),(10, NULL,NULL, NULL, NULL),(11, NULL,NULL, NULL, NULL),(12, NULL,NULL, NULL, NULL),(13, NULL,NULL, NULL, NULL),(14, NULL,NULL, NULL, NULL),(15, NULL, NULL,NULL, NULL),(16, NULL,NULL, NULL, NULL),(17,NULL, NULL, NULL, NULL),(18, NULL,NULL, NULL, NULL),(19, NULL,NULL, NULL, NULL),(20, NULL,NULL, NULL, NULL);	";
		if ($stmt = $mysqli->prepare($data)){			
			if ($stmt->execute()){echo "insert data<br>";}else{error();}
			if (!$stmt->close()){error();}								
		}else{
			error();
		}	
	}
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

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:16px">
  <tbody>
    <tr align="center" valign="top">
      <td width="300">
      <div <?php if ($MOVIL){ echo 'style="float:right; width:100%"';} ?>>
	
      <div class="help" data-role="collapsibleset" style="text-align:justify" data-mini="true">
	
    <div data-role="collapsible" data-collapsed="false"> <h3 ><?=$premios[0]["name"];?></h3> <?=$premios[0]["info"];?></div>
	<div data-role="collapsible"> <h3 ><?=$premios[1]["name"];?></h3> <?=$premios[1]["info"];?></div>
	<div data-role="collapsible"> <h3 ><?=$premios[2]["name"];?></h3> <?=$premios[2]["info"];?></div>
    <div data-role="collapsible"> <h3 ><?=$premios[3]["name"];?></h3> <?=$premios[3]["info"];?></div>
    <div data-role="collapsible"> <h3 ><?=$premios[4]["name"];?></h3> <?=$premios[4]["info"];?></div>
    
    </div>
      </td>
      <td>
      <div <?php if ($MOVIL){ echo 'style="float:right; width:100%"';} ?>>
	<a href="#bid" data-rel="popup" data-transition="pop" onClick="$('strong#cost').html('15 dolares');" style="text-decoration:none">
        <div class="sphere pulse" >
        <div class="sphere dollar" >
            <div id="conoce">Puntazo</div>
            <em></em>
         </div>
         </div>
	  </a>
      <br>
      <img src="./images/flag_us.jpg" width="32" height="20" alt=""/> US Dollar
</div>
	</td>
      <td width="300">
      <div <?php if ($MOVIL){ echo 'style="float:right; width:100%"';} ?>>
  <div class="help" data-role="collapsibleset" style="text-align:justify" data-mini="true">
	
     <div data-role="collapsible" data-collapsed="false"> <h3 ><?=$project[0]["name"];?></h3> <?=$project[0]["info"];?></div>
	<div data-role="collapsible"> <h3 ><?=$project[1]["name"];?></h3> <?=$project[1]["info"];?></div>
	<div data-role="collapsible"> <h3 ><?=$project[2]["name"];?></h3> <?=$project[2]["info"];?></div>
    <div data-role="collapsible"> <h3 ><?=$project[3]["name"];?></h3> <?=$project[3]["info"];?></div>
    <div data-role="collapsible"> <h3 ><?=$project[4]["name"];?></h3> <?=$project[4]["info"];?></div>

</div>
      </td>
    </tr>
  </tbody>
</table>


<div id="pop_message" style="float:left; padding-top: 40px; width:100%"></div>

</div>

<div id="bid" data-role="popup" data-position-to="window" class="ui-corner-all">
	<div style="text-align:center; margin:32px;">
    <?php if (!isset($_SESSION["usuario"])){ ?>
       Para jugar debes iniciar session <a href="#panel_user" style="display:inline-block;" class="ui-link"><img src="images/icon_user.png" width="32" style="padding-top:10px"></a>
    <?php }else{ ?>
    	Vas a jugar en el sorteo pro-recaudacion de fondos para proyectos sociales a un costo de $<strong id="cost"></strong>
      	<p >
      	  <label>
      	    <input type="radio" name="project" value="1" id="project_0">
      	    <?=$project[1]["name"];?></label>
      	  <label>
      	    <input type="radio" name="project" value="2" id="project_1">
      	   <?=$project[2]["name"];?></label>
      	  <label>
      	    <input type="radio" name="project" value="3" id="project_2">
      	    <?=$project[3]["name"];?></label>
      	  <label>
      	    <input type="radio" name="project" value="4" id="project_3">
      	    <?=$project[4]["name"];?></label>
   	  </p>
        <br>
    	<a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="alert" onClick="$('div#bid').popup('close');">Cancelar</a>
 	<a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="check" onClick="buyConoce();">Jugar</a>
    <?php } ?>
    <br>
    </div>

<div id="timer" style="display:none"></div>