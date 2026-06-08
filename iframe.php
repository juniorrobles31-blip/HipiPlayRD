<?php 
if (!isset($_GET["iframe"])){ return; } 
$MOVIL = false;
?>
<script>	
   var MOVIL   = <?php if ($MOVIL){echo 1;}else{echo 0;}?>;   
</script>
<script src="js/jquery-2.1.4.min.js"></script>
<script src="js/system.js"></script>

<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="css/dice.css"  rel="stylesheet" type="text/css">
<style>
.title{
	position: fixed;
	left: 10px;
	bottom: 20px;
	text-decoration: blink;
	font-size: 1em;
	color: #EDC807;
	text-shadow: 1px 1px 3px #C09500;
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
#roulette{
    position: absolute;
    left: -140px;
}
@media screen and (max-height: 1079px) {
#area1{
	margin-left: 0px !important;	
}
}
@media screen and (min-height: 1080px) {
#area1{
	margin-left: 80px !important;	
}
}
#timer,
#play{
	color : white;	
}
</style>
<?php
define('WEB', true);
require_once("system.php");
require_once("include/class/time.php");
require_once("include/class/common.php");

$display = new DISPLAY();
$time = new ROLL();
	
include("include/gui/".$_GET["iframe"].".inc.php");

$game = $display->nameGame($GAME_MODE);
?>
<div id="timer"></div>
<div id="play"></div>
<div class="title"><?=$game["name"]?></div>