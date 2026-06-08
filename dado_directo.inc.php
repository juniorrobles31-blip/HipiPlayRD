<?php 
	$GAME_MODE = "dice.1";
	if (!isset($_GET["iframe"])){
		include("./include/gui/game.ui.php");
	}
?>
<script> 
	GAME_MODE        = "<?=$GAME_MODE;?>";
	GLOBAL.NEXT_PLAY = <?php echo $time->nextPlay($GAME_MODE);?>;
</script>

<div id="area1" class="area">
    <div id="cube1" class="cube">
        <div class="side front2" ></div>
        <div class="side back2"  ></div>
        <div class="side bottom2"></div>
        <div class="side top2"   ></div>
        <div class="side right2" ></div>
        <div class="side left2"  ></div>

        <div id="dice1_1" class="side front" ></div>
        <div id="dice6_1" class="side back" ></div>
        <div id="dice2_1" class="side bottom" ></div>
        <div id="dice4_1" class="side top" ></div>
        <div id="dice3_1" class="side right" ></div>
        <div id="dice5_1" class="side left" ></div>
        
        <div class="corners left">
            <div class="cornerwrapper ftl"><div class="corner"></div></div>
            <div class="cornerwrapper btr"><div class="corner"></div></div>
            <div class="cornerwrapper fbl"><div class="corner"></div></div>
            <div class="cornerwrapper bbr"><div class="corner"></div></div>
        </div>
        
        <div class="corners right">
            <div class="cornerwrapper ftl"><div class="corner"></div></div>
            <div class="cornerwrapper btr"><div class="corner"></div></div>
            <div class="cornerwrapper fbl"><div class="corner"></div></div>
            <div class="cornerwrapper bbr"><div class="corner"></div></div>
        </div>
    </div>        
</div>
<?php if (isset($_GET["iframe"])){ return; }?>
<div id="bid" data-role="popup" data-position-to="window" data-theme="a" class="ui-corner-all">
  <table width="100px" border="0" cellspacing="0" cellpadding="4">
  <tr valign="top">
      <td align="center">
        <fieldset data-role="controlgroup">
            <input type="checkbox" name="checkbox-1a" id="checkbox-1a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-1a"><img src="images/dice_1.svg" width="32" style="background:rgba(255,255,255,1);"/></label>
            <input type="checkbox" name="checkbox-2a" id="checkbox-2a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-2a"><img src="images/dice_2.svg" width="32"  style="background:rgba(255,255,255,1)" /></label>
            <input type="checkbox" name="checkbox-3a" id="checkbox-3a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-3a"><img src="images/dice_3.svg" width="32" style="background:rgba(255,255,255,1)"/></label>
            <input type="checkbox" name="checkbox-4a" id="checkbox-4a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-4a"><img src="images/dice_4.svg" width="32" style="background:rgba(255,255,255,1)"/></label>
            <input type="checkbox" name="checkbox-5a" id="checkbox-5a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-5a"><img src="images/dice_5.svg" width="32" style="background:rgba(255,255,255,1)"/></label>
            <input type="checkbox" name="checkbox-6a" id="checkbox-6a" class="custom" onChange="onChange(this);">
            	<label for="checkbox-6a"><img src="images/dice_6.svg" width="32" style="background:rgba(255,255,255,1)"/></label>
        </fieldset>
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
        
        <input type="number" name="radio-choice" id="radio-choice-9" value="1000" step="1000" onChange="onChange(this);" />
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
