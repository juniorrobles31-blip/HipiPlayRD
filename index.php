<?php 	ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<?php
define('WEB', true);
define("DEBUG",true);

if (!isset($_GET["page"])){
	//header("Location:index.php?page=login");
	//exit; 
}

//if (isset($_SESSION["id"])){
	//sec_session_start();
//}
require_once __DIR__ . '/include/lib/php8_compat.php';
require("system.php");

/* PHP 8 runtime defaults */
if (!isset($MOVIL)) {
    $MOVIL = false;
}

if (!isset($LANG) || !is_array($LANG)) {
    $langFallback = __DIR__ . '/include/lang/es.php';
    if (is_file($langFallback)) {
        require $langFallback;
    }
}

if (!isset($LANG) || !is_array($LANG)) {
    $LANG = array();
}

$LANG = array_merge(array(
    "menu" => "Menú",
    "session.start" => "Iniciar sesión",
    "session.end" => "Sesión finalizada",
    "balance" => "Balance",
    "lang" => "Idioma",
    "game.horse" => "Caballos",
    "game.horse.info" => "Juego de caballos",
    "game.dice.1" => "Dado directo",
    "game.dice.1.info" => "Juego de dado directo",
    "game.dice.2" => "Súper dado",
    "game.dice.2.info" => "Juego de súper dado",
    "game.dice.3" => "Dado tripleta",
    "game.dice.3.info" => "Juego de dado tripleta",
    "game.roulette" => "Ruleta",
    "game.roulette.info" => "Juego de ruleta",
    "game.puntazo" => "Puntazo",
    "game.puntazo.info" => "Juego puntazo",
    "play" => "Jugar",
    "bed" => "Apostar",
    "other" => "Otro",
    "html5.fail" => "Tu navegador no soporta HTML5"
), $LANG);

if (!isset($good)) {
    $good = "good";
}

if (!isset($error)) {
    $error = "error";
}

?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
    <meta name="viewport" content="width=device-width"/>
    <meta name="theme-color" content="<?=$theme_background?>">
    <meta name="msapplication-navbutton-color" content="<?=$theme_background?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="<?=$theme_background?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <link rel="icon" type="image/png" href="images/game_dice3.png">
    <link href="css/jquery.mobile-1.4.5.min.css" rel="stylesheet" ><!---->
	<link href="css/jquery.mobile.icons-1.4.5.min.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery.mobile.theme.css" rel="stylesheet" type="text/css" /><!---->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/dice.css"  rel="stylesheet" type="text/css">
    
	  
	<link href="css/table-responsive.css" rel="stylesheet">
	
	 <!-- Bootstrap  
	 <link href="css/bootstrap.css" rel="stylesheet">
	 <link href="css/style-responsive.css" rel="stylesheet"> 
   
    <link href="vendors/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

	<link href="build/css/table-responsive.css" rel="stylesheet">
   -->
    <link rel="manifest" href="manifest.json">
    
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/zuzuvama.js"></script>
    <script src="js/system.js"></script>
    <script src="js/pwa-db.js"></script>
    <script src="js/invitation-pool.js"></script>
    <script src="js/sw-register.js"></script>

	<script>	
    	var MOVIL   = <?php if ($MOVIL){echo 1;}else{echo 0;}?>;
		var SESSION = 0;
		<?php if(isset($_SESSION["id"])) { 
				echo 'SESSION = '.$_SESSION["id"];?>			
				setTimeout("alert('<?=$LANG["session.end"]?>');location.reload(false);", <?php echo $timeout * 1000;?>);
		<?php }?>
    </script>
<style>
<?php if ($MOVIL){ ?>
	.logo{
		background-image: url("images/logo-movil.png") ;
		width: 110px;
		height: 48px;
		display:inline-block;
	}
	#panel_menu{
		width: 0%;	
	}
	#panel_content{
		width: 100%;	
	}
	#timer{
		font-size: xx-large;
		font-family:"Impact";
		position: absolute;
		bottom:70px;
		left: 0px;
		width: 100%;
	}
	#play{
		font-size: small;
		font-family:"Impact";
		position: absolute;
		bottom:170px;
		left: 0px;
		width: 100%;
		color: rgba(255,255,0,1);
		text-decoration:blink;
	}
	#play_button{
		position: absolute;
		text-align:center;
		bottom:12px;
		left: 0px;
		width: 100%;
		/*top: 2px;*/
		/*right: 440px;	*/
		z-index: 100;
	}
	/*.card{
		width:160px;
		height:170px;
	}*/
<?php }else{ ?>
	.logo{
		background-image: url("images/logo.png") ;
		width: 300px;
		height: 48px;
		display:inline-block;
		background-repeat:no-repeat;
	}
	#panel_menu{
		width: 180px;
		padding-right: 1em;	
	}
	#panel_content{
		/*width: 100%;*/
	}
	.title{
		text-align:left;	
	}
	#timer{
		font-size: x-large;
		font-family: "Impact";
		position: absolute;
		right: 18px;
		bottom:12px;
	}
	#timer span{
		font-size: xx-large;
	}
	#play{
		font-size: xx-large;
		right: 0px;
		left: 0px;
		bottom:32px;		
		text-align:center;
	}
	#play_button{
		position: absolute;
		text-align: center;
		top: 2px;
		right: 440px;
		z-index: 100;
	}
	/*
	.card{
		width:160px;
		height:170px;
	}
	*/
<?php } ?>
/* Override */
.ui-bar-a,
.ui-page-theme-a .ui-bar-inherit,
html .ui-bar-a .ui-bar-inherit,
html .ui-body-a .ui-bar-inherit,
html body .ui-group-theme-a .ui-bar-inherit {
	background-color: <?=$theme_background ?>;
}
.ui-overlay-a,
.ui-page-theme-a,
.ui-page-theme-a .ui-panel-wrapper {
	background-color: <?=$theme_background ?>;
}
.ui-overlay-a,
.ui-page-theme-a,
.ui-page-theme-a .ui-panel-wrapper {
	background-color: <?=$theme_background ?>;
}
.ui-body-a,
.ui-page-theme-a .ui-body-inherit,
html .ui-bar-a .ui-body-inherit,
html .ui-body-a .ui-body-inherit,
html body .ui-group-theme-a .ui-body-inherit,
html .ui-panel-page-container-a {
	background-color: <?=$theme_background ?>;
	border-color: #0ED300;
}
</style>

<?php	
	$CtrlPage = true;
	$PAGE = "";
	if (!isset($_GET["page"])){
		$_GET["page"] = "home";
	}
	
	if(isset($SessionTimeOut)){
		$PAGE = "include/gui/login.inc.php";
	}else{
		$requestedPage = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET["page"]);
		$PAGE = "include/gui/".$requestedPage.".inc.php";
		if (!is_file($PAGE)) { $PAGE = "include/gui/home.inc.php"; }
	} 
	
?>
</head>
<body>

<?php include_once("include/gui/analyticstracking.php"); ?>
    <noscript>
    	<?php include_once("include/gui/noscript.php"); ?>
    </noscript>
    <?php
	// LOAD Registro Unico
	//require_once("registroUnico/api.php");
	//$RU = new RU();
	//$RU->login('?',false);

	?>          

<div id="page_main" data-role="page" style="overflow: hidden;">
    <div data-role="header">
        <?php if ($MOVIL){ ?>
        <div class="header_left">
            <a href="#panel_side" data-role="button" data-icon="bars" data-iconpos="notext" accesskey="M" style="background: #0F0 !important"><?=$LANG["menu"];?></a>
        </div>
        <?php } ?>
            
        <div class="header_right" style="height:48px;">        
            <?php if(isset($_SESSION["id"])) {?>
            <div id="balance_container" style='display:inline-block;'>
               <i id="balance_refresh" style="display: none">
               		<img class="icon" src="./images/icon_refresh.png" style="padding:4px;width:24px;height:24px" title="Balance" onclick="getBalance();">               	
               </i>
               <i id="balance">
					<span style="font-size:small"><?=$LANG["balance"];?></span><br>
					$<span id="balance"><?=$_SESSION['balance'];?></span>
				</i>
            </div>
            <?php
            //<a data-role="button" href="?page=profile&sec=account">Recargar</a>				
             } ?>
             
            <a href="#panel_user" style='display:inline-block;'><?php if (!$MOVIL && !isset($_SESSION["id"])){ ?><div style="display:inline-block"><?=$LANG["session.start"]?></div><?php } ?><img src="images/icon_user.png" width="32" style="padding-top:10px"/></a>
            <!--
           <a href="#" style='display:inline-block;' onClick="RU.show()"><img src="images/icon_user.png" width="32" style="padding-top:10px"/></a>-->
        </div>
        
        <div class="title">
           <div class="logo"></div>
            <div style="display:inline-block; font-size:0.6em">
            <a href="#" style='display:inline-block' onClick="$('div#lang_popup').toggle(500);"><?=$LANG["lang"];?></a> <br>
           <!-- <img src="include/lang/.jpg" style="padding-bottom:16px"/> -->                    
          <div id="lang_popup" style="display:none" class="ui-corner-all">
            <table width="100" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td> <a href="?lang=es" style='display:inline-block;'><img src="include/lang/es.jpg" width="32" style="padding-top:10px"/></a></td>
                  <td>Espa&ntilde;ol</td>
                </tr>
                <tr>
                  <td><a href="?lang=en" style='display:inline-block;'><img src="include/lang/en.jpg" width="32" style="padding-top:10px"/></a></td>
                  <td>English</td>
                </tr>
                <tr>
                  <td><a href="?lang=ch" style='display:inline-block;'><img src="include/lang/ch.jpg" width="32" style="padding-top:10px"/></a></td>
                  <td>Ã¤Â¸Â­Ã¦Â–Â‡</td>
                </tr>
              </tbody>
            </table>
            </div>
        </div>
        </div>  
    </div> 
    
    <div id="main" role="main" class="ui-content">
    	
    
    	<?php require_once 'include/lib/logerror.lib.php'; ?>
        
         <div id="panel_side" data-role="panel" data-position="left" data-display="overlay" >
            <?php if ($MOVIL){ include("include/menu/index.php");}?> 
        </div>
        
        <div id="panel_user" data-role="panel" data-position="right" data-display="overlay" >
            <?php include("include/menu/menu_user.php");?> 
        </div>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <?php if (!$MOVIL){ ?>
            <td valign="top" style="background: linear-gradient(90deg, #54a700, #66cb01 ) ">
                <div id="panel_menu" >
                	<?php include("include/menu/index.php"); ?>
                </div>
            </td>
            <?php } ?> 
            <td width="100%" valign="top" >           
                <div id="panel_content" >
                <?php 
                    if (file_exists($PAGE)){
                        include($PAGE);
                    }
                ?>                 
                </div>
        	</td>
          </tr>
        </table> 
    </div>

</div>

</body>
</html>
<?php ob_end_flush(); ?>
