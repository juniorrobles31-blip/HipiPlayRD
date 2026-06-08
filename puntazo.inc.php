<?php 
	$GAME_MODE = "puntazo";
	//require_once("include/gui/game.ui.php");
	require_once("./include/class/time.php");
	$time = new ROLL();
		
	//Resultados de la ultima jugada --- ENGELLLL	
	require_once('./include/class/common.php');
	$exec = new DISPLAY();	
	$data = $exec->getWon('puntazo');
	//var_dump($data);
	$win = $data['nwom'];
	//var_dump($win);
	$price = $data["price"];
?>
<script> 
	GAME_MODE        = "<?=$GAME_MODE;?>";
	GLOBAL.NEXT_PLAY = <?php echo $time->nextPlay($GAME_MODE);?>;
	
function showResults(){
	$( ".show" ).each(function( index ) {
	  var element = this;
	  setTimeout(function() {
			 $(element).attr('class','show now');
		  }, 300*index);
	});
}
</script>
<style>
.scroll{
	position:absolute;
	right:4px;
	top:56px;
	width:240px;
	overflow-y: scroll; 
	height:560px;
}
div::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	border-radius: 10px;
	background-color: #F5F5F5;
}
.dollar {
	box-shadow: inset 0 0 80px 0px #006FCB !important;
	-webkit-box-shadow: inset 0 0 80px 0px #006FCB !important;
}
.dollar:hover {
	box-shadow: inset 0 0 80px 0px #FC3 !important;
	-webkit-box-shadow: inset 0 0 80px 0px #FC3 !important;
}
.sphere {
	width: 200px;
	height: 200px;
	line-height: 200px;
	display: inline-block;
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
.puntazo_icon {
	width: 32px;
	height: 32px;
	display: inline-block;
	text-align: center;
	line-height: 32px;
	border-radius: 100%;
	box-shadow: inset 0 0 20px 0px #66CB01;
	-webkit-box-shadow: inset 0 0 20px 0px #66CB01;
}
.puntazo_icon_us {
	width: 32px;
	height: 32px;
	display: inline-block;
	text-align: center;
	line-height: 32px;
	border-radius: 100%;
	box-shadow: inset 0 0 20px 0px #006FCB;
	-webkit-box-shadow: inset 0 0 20px 0px #006FCB;
}

li > .puntazo_icon{
    width: 48px !important;
    height: 48px !important;
    line-height: 48px !important;	
}
 @-webkit-keyframes dazzle {
 0% {
 -webkit-box-shadow: 
 -50px 40px 2px -13px rgba(255,255,255,0),  -60px 60px 2px -13px rgba(255,255,255,1),  -35px -20px 2px -12px rgba(255,255,255,0),  40px 60px 3px -13px rgba(235,255,255,1),  30px -50px 2px -13px rgba(255,255,255,0),  -50px 35px 3px -11px rgba(255,255,235,1),  -25px -20px 3px -13px rgba(235,255,235,0),  -40px 10px 4px -11px rgba(255,255,255,1),  -40px -65px 1px -13px rgba(255,245,255,0),  -10px 50px 3px -12px rgba(255,255,255,1),  10px -30px 3px -12px rgba(235,255,255,0),  -30px -20px 2px -12px rgba(255,245,255,1),  70px 40px 3px -13px rgba(255,255,255,0),  20px 50px 2px -13px rgba(205,255,255,1),  -40px 45px 6px -11px rgba(255,255,255,0),  -35px -60px 3px -13px rgba(255,255,225,1),  -30px 40px 4px -11px rgba(255,255,255,0),  -70px -55px 1px -13px rgba(255,255,255,1),  -10px 50px 3px -12px rgba(235,255,255,0),  20px -40px 3px -12px rgba(225,255,255,1),  -20px -10px 2px -13px rgba(255,255,255,0),  -50px -20px 5px -12px rgba(245,255,255,1),  70px 30px 3px -13px rgba(215,255,245,0),  40px 90px 2px -13px rgba(255,255,255,1),  -40px 35px 3px -11px rgba(165,235,215,0),  35px -40px 3px -13px rgba(245,255,255,1),  -70px 20px 4px -11px rgba(215,215,235,0),  -40px -25px 1px -13px rgba(255,245,255,1),  10px -60px 3px -12px rgba(255,225,245,0),  -20px 70px 3px -12px rgba(255,255,255,1),  -25px 80px 2px -13px rgba(245,255,255,0),  -40px 90px 2px -12px rgba(170,235,255,1),  -35px 20px 3px -13px rgba(225,215,255,0),  60px -20px 3px -12px rgba(255,255,255,1),  20px -80px 2px -13px rgba(255,255,255,0),  10px 40px 3px -13px rgba(225,255,255,1),  -45px -40px 2px -13px rgba(255,245,255,0),  40px -10px 3px -12px rgba(255,255,255,1),  15px 30px 2px -13px rgba(225,235,255,0),  10px -30px 2px -12px rgba(255,245,245,1),  40px 50px 2px -13px rgba(255,255,255,0),  -55px 40px 4px -13px rgba(225,245,255,1),  -40px -10px 2px -12px rgba(195,255,175,0),  30px -20px 2px -13px rgba(255,255,255,1),  50px 20px 3px -13px rgba(255,255,255,0),  -55px -10px 2px -13px rgba(255,205,255,1),  10px -30px 7px -12px rgba(255,255,255,0),  10px 60px 2px -13px rgba(255,255,255,1),  20px -30px 2px -12px rgba(225,225,225,0),  40px 50px 2px -13px rgba(195,255,255,1),  30px 70px 3px -13px rgba(235,255,235,0),  -60px 60px 2px -13px rgba(255,255,255,1),  10px 70px 3px -12px rgba(225,205,255,0),  20px 90px 2px -13px rgba(235,255,235,1),  30px 70px 5px -12px rgba(225,255,225,0),  10px -40px 2px -12px rgba(195,255,175,1),  20px -60px 2px -13px rgba(255,255,255,0),  30px 60px 3px -13px rgba(255,255,255,1),  -75px -30px 2px -13px rgba(255,205,255,0),  30px -30px 7px -12px rgba(255,255,255,1),  40px 60px 2px -13px rgba(255,255,255,0),  20px -30px 2px -12px rgba(225,225,225,1),  20px 50px 2px -13px rgba(195,255,255,0),  40px 20px 3px -13px rgba(235,255,235,1),  -40px 30px 2px -13px rgba(255,255,255,0);
}
 100% {
 -webkit-box-shadow: 
 -50px 40px 2px -13px rgba(255,255,255,1),  -60px 60px 2px -13px rgba(255,255,255,0),  -35px -20px 2px -12px rgba(255,255,255,1),  40px 60px 3px -13px rgba(235,255,255,0),  30px -50px 2px -13px rgba(255,255,255,1),  -50px 35px 3px -11px rgba(255,255,235,0),  -25px -20px 3px -13px rgba(235,255,235,1),  -40px 10px 4px -11px rgba(255,255,255,0),  -40px -65px 1px -13px rgba(255,245,255,1),  -10px 50px 3px -12px rgba(255,255,255,0),  10px -30px 3px -12px rgba(235,255,255,1),  -30px -20px 2px -12px rgba(255,245,255,0),  70px 40px 3px -13px rgba(255,255,255,1),  20px 50px 2px -13px rgba(205,255,255,0),  -40px 45px 6px -11px rgba(255,255,255,1),  -35px -60px 3px -13px rgba(255,255,225,0),  -30px 40px 4px -11px rgba(255,255,255,1),  -70px -55px 1px -13px rgba(255,255,255,0),  -10px 50px 3px -12px rgba(235,255,255,1),  20px -40px 3px -12px rgba(225,255,255,0),  -20px -10px 2px -13px rgba(255,255,255,1),  -50px -20px 5px -12px rgba(245,255,255,0),  70px 30px 3px -13px rgba(215,255,245,1),  40px 90px 2px -13px rgba(255,255,255,0),  -40px 35px 3px -11px rgba(165,235,215,1),  35px -40px 3px -13px rgba(245,255,255,0),  -70px 20px 4px -11px rgba(215,215,235,1),  -40px -25px 1px -13px rgba(255,245,255,0),  10px -60px 3px -12px rgba(255,225,245,1),  -20px 70px 3px -12px rgba(255,255,255,0),  -25px 80px 2px -13px rgba(245,255,255,1),  -40px 90px 2px -12px rgba(170,235,255,0),  -35px 20px 3px -13px rgba(225,215,255,1),  60px -20px 3px -12px rgba(255,255,255,0),  20px -80px 2px -13px rgba(255,255,255,1),  10px 40px 3px -13px rgba(225,255,255,0),  -45px -40px 2px -13px rgba(255,245,255,1),  40px -10px 3px -12px rgba(255,255,255,0),  15px 30px 2px -13px rgba(225,235,255,1),  10px -30px 2px -12px rgba(255,245,245,0),  40px 50px 2px -13px rgba(255,255,255,1),  -55px 40px 4px -13px rgba(225,245,255,0),  -40px -10px 2px -12px rgba(195,255,175,1),  30px -20px 2px -13px rgba(255,255,255,0),  50px 20px 3px -13px rgba(255,255,255,1),  -55px -10px 2px -13px rgba(255,205,255,0),  10px -30px 7px -12px rgba(255,255,255,1),  10px 60px 2px -13px rgba(255,255,255,0),  20px -30px 2px -12px rgba(225,225,225,1),  40px 50px 2px -13px rgba(195,255,255,0),  30px 70px 3px -13px rgba(235,255,235,1),  -60px 60px 2px -13px rgba(255,255,255,0),  10px 70px 3px -12px rgba(225,205,255,1),  20px 90px 2px -13px rgba(235,255,235,0),  30px 70px 5px -12px rgba(225,255,225,1),  10px -40px 2px -12px rgba(195,255,175,0),  20px -60px 2px -13px rgba(255,255,255,1),  30px 60px 3px -13px rgba(255,255,255,0),  -75px -30px 2px -13px rgba(255,205,255,1),  30px -30px 7px -12px rgba(255,255,255,0),  40px 60px 2px -13px rgba(255,255,255,1),  20px -30px 2px -12px rgba(225,225,225,0),  20px 50px 2px -13px rgba(195,255,255,1),  40px 20px 3px -13px rgba(235,255,235,0),  -40px 30px 2px -13px rgba(255,255,255,1);
}
}
 @-webkit-keyframes pulse {
 0% {
 -webkit-box-shadow: 
 0 0 5px 0 rgba(51,255,51,0),  0 0 0px 0 rgba(140,255,180,.9);
}
 50% {
 -webkit-box-shadow: 
 0 0 5px 10px rgba(51,255,51,0.1),  0 0 20px 0 rgba(140,255,180,.9);
}
 100% {
 -webkit-box-shadow: 
 0 0 5px 0 rgba(51,255,51,0),  0 0 0px 0 rgba(140,255,180,.9);
}
}
.pulse {
	-webkit-animation-name: pulse;
	-webkit-animation-duration: .5s;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-timing-function: ease-out;
}
.show  {
	float:left;
	width: 60px;
	font-size: 0.6em;
 	opacity: 0;
	transform:translate(-100px,-100px);
}
.show.now{
	opacity: 1;
	transform: none;
	transition: all 1.5s cubic-bezier(.36,-0.64,.34,1.76);
}
.results{
	max-width:560px;
	width: 100%;	
}

ul {list-style-type: none; }

</style>

<br>
<br>
<br>
<div style="margin: 0 auto;text-align:center; max-width:500px"> 
	<a href="#bid" data-rel="popup" data-transition="pop" onClick="window.DOLLAR = 0; $('strong#cost').html('<?=$price?>');" style="text-decoration:none">
  <div class="sphere pulse" >
    <div class="sphere" >
      <div id="puntazo_do"></div>
      <em></em> 
     </div>
  </div>
  </a>   
    <br>  
    <br>
    <span style="font-size:1.5em;">
    	<?=$LANG["game.puntazo"]?><br>
    	<?=$LANG["game.puntazo.game"]?> #<span id="sorteo_next" style="font-size:1.5em;">?</span><br>
        <span id="sorteo_date" style="font-size:1.0em;">?</span>
    </span>
    
    <!--
    $<strong align="right" id="pool" >?</strong> <span style="font-size:0.5em;">Acumulado</span>
    <br>
    $<strong align="right" id="super_pool" >?</strong> <span style="font-size:0.5em">Acumulado Navidad</span>
    -->
  <div id="pop_message" style="float:left; padding-top: 40px; width:100%"></div>
</div>

<div id="bid" data-role="popup" data-position-to="window" class="ui-corner-all">
  <div style="text-align:center; margin:32px;">
    <?php if (!isset($_SESSION["id"])){ 
		echo $LANG["login.required"]; ?>
    	<a href="#panel_user" style="display:inline-block;" class="ui-link"><img src="images/icon_user.png" width="32" style="padding-top:10px"></a>
    <?php }else{ ?>
    <?=$LANG["game.puntazo.play"]?> $<strong id="cost"></strong> <br>
    <br>
    <a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="alert" onClick="$('div#bid').popup('close');"><?=$LANG['cancel']?></a> <a data-role="button" style="margin:0px; width:100px; display:inline-block" data-icon="check" onClick="buyPuntazo();"><?=$LANG['play']?></a>
    <?php } ?>
    <br>
  </div>
</div>
<div id="timer" style="display:none"></div>

<div id="search_popup" data-role="popup" data-position-to="window" data-theme="a" class="ui-corner-all">
	<table class="results" border="0" cellspacing="4" cellpadding="0">
  <tr>
  <td align="center">
    <h3 onClick="start();"><?=$LANG['game.puntazo']?></h3>
    <div ><?=$LANG['game']?> : <span id="sorteo">?</span> @ <span id="date">?</span></div>
    <div style="font-size:18px"><?=$LANG['game.puntazo.pool']?> $<strong id="pool">?</strong></div>
     <div id="list">
       
       <div style="zoom: 1.3;"><?=$LANG['game.result.winner']?> : <?php 
			echo resultIcon('puntazo', $win[0]);
			?>
		</div>
      	<?php
	  	$i = 0;
      	foreach ($win as $w){
			$i++; 
			if ($i == 1){continue;}
			?>
          <div class="show" ><?=$i ." :  ".resultIcon('puntazo', $w)?></div>
      	<?php	} ?>
      </div>
      
    </td>
    </tr>
	</table>
</div>

<div id="help_popup" data-role="popup" data-position-to="window" class="ui-corner-all">
	<?php include("include/gui/info.puntazo.inc.php");?>
</div>

<table id="play_button" width="0%" border="0" align="right" cellpadding="2" cellspacing="0">
	<tr>
    <td align="center">
		<?php if (isset($_SESSION["id"])){?>
        <a href="#bid" data-role="button" data-inline="true" style="margin:0px; width:80px; display:inline-block" data-icon="bars" data-rel="popup" data-transition="pop" onClick="window.DOLLAR = 0; $('strong#cost').html('<?=$price?>');"><?=$LANG['play']?></a>
        <?php } ?>
    </td>
    <td align="center">
     <a href="#search_popup" data-role="button" style="margin:0px; display:inline-block" data-icon="search" data-rel="popup" data-transition="pop" onClick="showResults()">&nbsp;</a>
    </td>
    <td align="center">
     <a href="#help_popup" data-role="button" style="margin:0px; display:inline-block" data-icon="info" data-rel="popup" data-transition="pop">&nbsp;</a>
    </td>
  </tr>
</table>
<div id="timer"></div>
<div id="play"></div>