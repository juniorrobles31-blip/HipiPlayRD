<?php 
	$GAME_MODE = "roulette";
	if (!isset($_GET["iframe"])){
		include("./include/gui/game.ui.php");
	}
?>
<script> 
	GAME_MODE        = "<?=$GAME_MODE;?>";
	GLOBAL.NEXT_PLAY = <?php echo $time->nextPlay($GAME_MODE);?>;
</script>

<div id="roulette">
    <img id="roulette_base" src="images/roulette_base.svg" alt="" class="roulette"/>
    <img id="roulette_top" 	src="images/roulette_top.svg"  alt="" class="roulette"/>
    <img id="ball" 		    src="images/roulette_ball.svg"  alt="" class="roulette-ball"/>
</div>

<?php if (isset($_GET["iframe"])){ return; }?>

<div id="bid" data-role="popup" data-position-to="window" data-theme="a" class="ui-corner-all">
  <table width="100px" border="0" cellspacing="0" cellpadding="4">
  <tr valign="top" >
    <td align="center">
    <fieldset data-role="controlgroup">
        <input type="radio" name="color" id="color-1" value="1" class="custom" onChange="onChange(this);">
        <label for="color-1" style="width:60px;height:32px;background:none !important;background-color:#000 !important;">&nbsp;</label>
        <input type="radio" name="color" id="color-2" value="2" class="custom" onChange="onChange(this);">
        <label for="color-2" style="width:60px;height:32px;background:none !important;background-color:#F00 !important;">&nbsp;</label>
    </fieldset>
        <a data-icon="check" data-role="button" role="button" style="width:60px;height:20px" onClick="Apostar();"><?=$LANG["bed"]?></a>     	
    </td>
    <td >
    <fieldset data-role="controlgroup" style="width:120px">
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
 
        <input type="number" name="radio-choice" id="radio-choice-9" value="1000" step="1000" onChange="onChange(this);" style="display:none"/ >
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
