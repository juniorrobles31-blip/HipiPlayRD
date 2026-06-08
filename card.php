<style>
.card{
	display:inline-block;
	text-decoration:none;
	text-align:center;
	color:#FFF !important;
	<?php if ($MOVIL){?>
		border-top-style:solid;
		border-top-width:3px;
	<?php }else{?>
	    border-style: solid;
		border-width:1px;
		background-color:#000;
	<?php }?>
}

.card > div {
	<?php if ($MOVIL){?>
		font-size:1.8em;
		margin-top: -50%;
		margin-left: 50%;
	<?php }else{?>
		background-color:rgba(0,0,0,0.42);
		font-size:1.8em;
		top:-1.2em;
		position:relative;
	<?php }?>
}
.card > button {
  display: inline-block;
  vertical-align:middle;
  background: linear-gradient(90deg, #ea0f0f, #ff0000)!important;
  border: none;
  text-align: center;
  font-size: 1em;
  padding: 8px;
  width: 160px;
  transition: all 0.5s;
  cursor: pointer;
  margin: 0px;
  <?php if ($MOVIL){?>
		margin-bottom: 2.4em;
		margin-left: 50%;
		margin-top: 1em;
  <?php }else{?>		
  		margin-top: -2em;
  <?php }?>
}

.card > button span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  transition: 0.5s;
}

.card > button span:after {
  content: '»';
  position: absolute;
  opacity: 0;
  top: 0;
  right: -20px;
  transition: 0.5s;
}

.card > button:hover span {
  padding-right: 25px;
}

.card >  button:hover span:after {
  opacity: 1;
  right: 0;
}
</style>

<?php
if (!function_exists("card")) {
	function card($game){
	global $LANG;
	global $MOVIL; 
	if ($MOVIL){
		$m     = "m";			
		$width = "100%";
		$height= "auto";
	}else{
		$m     = "";
		$width = "250px";
		$height= "270px";
	}
	switch($game){
	case "caballos":
		$title = $LANG["game.horse"];
		$info  = $LANG["game.horse.info"];
		$color = "#ff00ff";
		$image = "images/bg".$m."_horse.png";
	break;
	case "dado_directo":
		$title = $LANG["game.dice.1"];
		$info  = $LANG["game.dice.1.info"];
		$color = "#29ff00";
		$image = "images/bg".$m."_dice_1.png";
	break;
	case "super_dado":
		$title = $LANG["game.dice.2"];
		$info  = $LANG["game.dice.2.info"];
		$color = "#005aff";
		$image = "images/bg".$m."_dice_2.png";
	break;
	case "dado_tripleta":
		$title = $LANG["game.dice.3"];
		$info  = $LANG["game.dice.3.info"];
		$color = "#f28101";
		$image = "images/bg".$m."_dice_3.png";
	break;
	case "ruleta":
		$title = $LANG["game.roulette"];
		$info  = $LANG["game.roulette.info"];
		$color = "#db0000";
		$image = "images/bg".$m."_roulette.png";
	break;
	case "puntazo":
		$title = $LANG["game.puntazo"];
		$info  = $LANG["game.puntazo.info"];
		$color = "#ffff00";
		$image = "images/bg".$m."_puntazo.png";
	break;
	}
		
   if ($MOVIL){ ?>
       <a href="?page=<?=$game?>" class="card" style="width:<?=$width?>;height:<?=$height?>;border-color:<?=$color?>;">
            <img src="<?=$image?>" width="100%">
            <div style="color:<?=$color?>;" ><?=$title?></div>
            <div style="text-align:left;font-size:1em;position:relative;padding:8px;margin-top:0px;" ><?=$info?></div>
            <button class="button_play"><span><?=$LANG["play"]?></span></button>
        </a>
<?php }else{ ?>
        <a href="?page=<?=$game?>" class="card" style="width:<?=$width?>;height:<?=$height?>;border-color:<?=$color?>;">
            <img src="<?=$image?>" width="<?=$width;?>" >
            <div style="color:<?=$color?>;" ><?=$title?></div>
            <div style="text-align:left;font-size:1em;position:relative;padding:8px;top:-2em;" ><?=$info?></div>
            <button class="button_play"><span><?=$LANG["play"]?></span></button>
        </a>
<?php
	}
 } 
}
?>