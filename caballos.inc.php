<?php 
	$GAME_MODE = "horse";
	if (!isset($_GET["iframe"])){
		include("./include/gui/game.ui.php");
	}
?>
<script> 
	GAME_MODE        = "<?=$GAME_MODE;?>";
	GLOBAL.NEXT_PLAY = <?php echo $time->nextPlay($GAME_MODE);?>;
</script>
<script type="text/javascript" src="js/horseRace.js"></script>
<script type="text/javascript" src="js/pwa-db.js"></script>
<script type="text/javascript" src="js/horse-blockchain.js"></script>
<style>
#canvas {
	width: 100%;
	top: 0;
	left: 0;
	border: none;
	z-index: 1;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
</style>


<div class="game" id="game">
    <canvas id="canvas" width="1280" height="720">
       <?=$LANG["html5.fail"]?>
    </canvas>
</div>

<?php if (isset($_GET["iframe"])){ return; }?>

<div id="bid" data-role="popup" data-position-to="window" data-theme="a" class="ui-corner-all">
  <table width="100px" border="0" cellspacing="0" cellpadding="4">
  <tr valign="top">
     <td align="center" colspan="2">
     <?=$LANG["game.horse"]?>
     </td>
   </tr>
  <tr valign="top">
     <td align="center" colspan="2">
      <fieldset data-role="controlgroup" style="display:none">
        <input type="radio" name="horse-1a" id="horse-1" value="on"  onChange="clearBid();">
            <label for="horse-1"><img src="images/caballo.png"/> 1</label>
        <input type="radio" name="horse-1a" id="horse-2" value="off" checked="checked" onChange="clearBid();">
            <label for="horse-2"><img src="images/caballo.png" style="display:inline"/><img src="images/caballo.png" style="display:inline"/><img src="images/caballo.png" style="display:inline"/> 3</label>
      </fieldset>
     </td>
  </tr>
  <tr valign="top">
     <td align="center">
        <fieldset data-role="controlgroup" >
            <input type="checkbox" name="checkbox-1a" id="checkbox-1a" onChange="onChange(this);">
            	<label id="dice1" for="checkbox-1a"><img src="images/caballo.png"/> 1</label>
            <input type="checkbox" name="checkbox-2a" id="checkbox-2a" onChange="onChange(this);">
            	<label for="checkbox-2a"><img src="images/caballo.png"  /> 2</label>
            <input type="checkbox" name="checkbox-3a" id="checkbox-3a" onChange="onChange(this);">
            	<label for="checkbox-3a"><img src="images/caballo.png"/> 3</label>
            <input type="checkbox" name="checkbox-4a" id="checkbox-4a" onChange="onChange(this);">
            	<label for="checkbox-4a"><img src="images/caballo.png"/> 4</label>
            <input type="checkbox" name="checkbox-5a" id="checkbox-5a" onChange="onChange(this);">
            	<label for="checkbox-5a"><img src="images/caballo.png"/> 5</label>
            <input type="checkbox" name="checkbox-6a" id="checkbox-6a" onChange="onChange(this);">
            	<label for="checkbox-6a"><img src="images/caballo.png"/> 6</label>
        </fieldset>
         <div style="margin:8px 0;padding:8px;border:1px solid rgba(255,255,255,.25);border-radius:8px;background:rgba(0,0,0,.25)">
          <div style="font-size:12px;margin-bottom:6px;color:#fff">Modo de juego</div>
          <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
            <input type="radio" name="horse_mode" id="horse-mode-real" value="real" checked="checked">
            <label for="horse-mode-real">Real</label>
            <input type="radio" name="horse_mode" id="horse-mode-demo" value="demo">
            <label for="horse-mode-demo">Demo</label>
          </fieldset>
          <div style="font-size:11px;color:#ccc;margin-top:4px">Demo y Real comparten el mismo evento, pero usan contabilidad separada.</div>
        </div>
        <a data-icon="check" data-role="button" role="button" style="width:60px;height:20px" onClick="Apostar();"><?=$LANG["bed"]?></a>
    </td>
    <td >
    <fieldset data-role="controlgroup" style="width:120px;">
      	<input type="radio" name="radio-choice" id="radio-choice-3" value="10" onChange="onChange(this);" />
     	<label for="radio-choice-3" >$10</label>
        
     	<input type="radio" name="radio-choice" id="radio-choice-4" value="25" onChange="onChange(this);" />
     	<label for="radio-choice-4" >$25</label>

     	<input type="radio" name="radio-choice" id="radio-choice-5" value="50" onChange="onChange(this);"/>
     	<label for="radio-choice-5" >$50</label>

     	<input type="radio" name="radio-choice" id="radio-choice-6" value="100" onChange="onChange(this);"/>
     	<label for="radio-choice-6" >$100</label>

     	<input type="radio" name="radio-choice" id="radio-choice-7" value="500" onChange="onChange(this);"/>
     	<label for="radio-choice-7" >$500</label>

     	<input type="radio" name="radio-choice" id="radio-choice-8" value="0" onChange="onChange(this);"/>     	
     	<label for="radio-choice-8" ><?=$LANG["other"]?></label>
        
        <input type="number" name="radio-choice" id="radio-choice-9" value="1000" step="1000" onChange="onChange(this);" 
        style="font-size:24px;display:none"/>
</fieldset>
    </td>
  </tr>
  <tr valign="top" >
    <td colspan="2" align="center">
    <div id="contador" style="font-size:small;color:#FFF;font-size:large"></div>
    </td>
    </tr>
</table> 
</div> 