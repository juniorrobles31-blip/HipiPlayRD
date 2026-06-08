<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
    <meta name="viewport" content="width=device-width"/>
    <meta name="theme-color" content="#4d4d4d">
    <meta name="msapplication-navbutton-color" content="#4d4d4d">
    <meta name="apple-mobile-web-app-status-bar-style" content="#4d4d4d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <link rel="icon" type="image/png" href="images/game_dice3.png">
</head>

<body>
	<noscript>
		<?php include("include/gui/noscript.php"); ?>
	</noscript>

<?php 
$MOVIL=false; 
if (!isset($_SESSION["lang"])){
	$_SESSION["lang"] = "es";
}
if (isset($_GET["lang"])){
	$_SESSION["lang"] = $_GET["lang"];
}
require_once("./include/lang/".$_SESSION["lang"].".php");
?>   

<img id="logo" src="images/logo-movil.png" width="150px" onClick="FullScreen();">

<div id="overlay" onClick="FullScreen();">
	<br>
	<br>
	<img src="images/logo.png" height="100px">
	<br>
	<br>
	<br>
    <br>
    <br>
    <img src="images/touch.gif" height="150px">
    <br>
	<br>
    <br>
	Toque la pantalla para cambiar al modo de pantalla completa
</div>

<div id="tv">
    <iframe id="frame_horse" class="frame" src="iframe.php?iframe=caballos" frameborder="0" scrolling="no"></iframe>
    <iframe id="frame_roulette" class="frame" src="iframe.php?iframe=ruleta" frameborder="0" scrolling="no"></iframe>
    <iframe id="frame_dice1" class="frame" src="iframe.php?iframe=dado_directo" frameborder="0" scrolling="no"></iframe>
    <!--<iframe id="frame_dice2" class="frame" src="iframe.php?iframe=super_dado" frameborder="0" scrolling="no"></iframe>-->
    <iframe id="frame_dice3" class="frame" src="iframe.php?iframe=dado_tripleta" frameborder="0" scrolling="no"></iframe>
    <div id="publicidad" >
		<?php 
			$MOVIL = true; 
			include ("include/gui/card.php");
        ?>
    	<div id="slide_1" style="display:none"> <?=card("dado_directo");?> </div>
    	<div id="slide_2" style="display:none"> <?=card("super_dado");?> </div>
    	<div id="slide_3" style="display:none"> <?=card("dado_tripleta");?> </div>
    	<div id="slide_4" style="display:none"> <?=card("ruleta");?> </div>
    	<div id="slide_5" style="display:none"> <?=card("caballos");?> </div>
    	<div id="slide_6" style="display:none"> <?=card("puntazo");?> </div>
    </div>
</div>
<style>
body{
	margin: 0px;
	background-color: #4d4d4d;
}
#overlay {
    background: #4d4d4d;
	color: white;
	z-index: 10;
	position:fixed;
	top:0px;
	bottom: 0px;
	left:0px;
	right: 0px;
	display:block;
	text-align:center;
	vertical-align:middle;
	font-size: 2em;
}
#tv{
	width: 1280px;
	height: 720px;
	border: solid;
	position:fixed;
	margin: 0 auto;
	position:relative;
}
@media screen and (min-height: 1080px) {
    #tv{
        zoom:1.43;
    }
}

.frame{
	border: none;
	position:fixed;
}
#logo{
	position: fixed;
	left: 40px;	
	top: 12px;
	z-index : 2;
}
#publicidad{
	position:fixed;
	left: 0px;	
	top: 80px;
	z-index : 1;
	width: 600px;
	height:300px;
}
#frame_horse{
	top: 0px;
	left: 600px;
	width: 680px;
	height: 380px;
}
#frame_roulette{
	top: 390px;
	left: 360px;
	width: 340px;
	height: 340px;
}
#frame_dice1{
	top: 390px;
	left: 40px;
	width: 300px;
	height: 340px;
}
#frame_dice3{
	top: 380px;
	left: 722px;
	width: 550px;
	height: 332px;
	border: solid 4px #EDC807;
}
#frame_dice2{
	padding-left: 60px;
	top: 390px;
	left: 920px;
	width: 300px;
	height: 340px;
}
/*
#logo{
	position: fixed;
	right: 40px;	
	top: 12px;
	z-index : 1;
}
#publicidad{
	position:fixed;
	right: 20px;	
	top: 80px;
	z-index : 1;
}

#frame_horse{
	width: 680px;
	height: 380px;
}
#frame_roulette{
	left: 700px;
	width: 340px;
	height: 380px;
}
#frame_dice1{
	top: 390px;
	left: 40px;
	width: 300px;
	height: 340px;
}
#frame_dice3{
	top: 390px;
	left: 330px;
	width: 580px;
	height: 340px;
	border: solid 4px #EDC807;
}
#frame_dice2{
	padding-left: 60px;
	top: 390px;
	left: 920px;
	width: 300px;
	height: 340px;
}*/

</style>
<script src="js/jquery-2.1.4.min.js"></script>
<script>
var full = false;
function FullScreen() {
	var element = document.body;//document.getElementById("tv");
	if (full){
		if (document.exitFullscreen) {
			document.exitFullscreen();
		}else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		}else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		}else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		}
		//document.getElementById('overlay').style.display = 'block';
	}else{
		if (element.requestFullscreen) {
			element.requestFullscreen();
		}else if (element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		}else if (element.webkitRequestFullScreen) {
			element.webkitRequestFullScreen();
		}else if (element.msRequestFullscreen) {
			element.msRequestFullscreen();
		}
		//document.getElementById('overlay').style.display = 'none';
		$('div#overlay').hide(500);
	}
}

var fullscreenCount = 0;
var changeHandler = function() {                                           
    fullscreenCount ++;
    if(fullscreenCount % 2 === 0)    {
		full = false;
		$('div#overlay').show(500);
		//document.getElementById('overlay').style.display = 'block';
    }else {
		full = true;
		//document.getElementById('overlay').style.display = 'none';
    }
}                                                                     
document.addEventListener("fullscreenchange", changeHandler, false);      
document.addEventListener("webkitfullscreenchange", changeHandler, false);
document.addEventListener("mozfullscreenchange", changeHandler, false);
document.addEventListener("MSFullscreenChanges", changeHandler, false);

$(function() {
	"use strict";
	timer();
});

var ads = 1;
var ads_max = 5;

function timer(){
	"use strict";
	setTimeout(timer, 10*1000);
	for (var i=1; i<=ads_max; i++){
		$('div#slide_'+i).hide(0);
	}
	$('div#slide_'+ads).show(500);
	ads++;
	if (ads > ads_max){
		ads = 1;	
	}
}
</script>
</body>
</html>