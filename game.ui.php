<?php 
	require_once("./include/class/time.php");
	$time = new ROLL();
?>
<div id="search_popup" data-role="popup" data-position-to="window" data-theme="a" class="ui-corner-all">
	<table width="100%" border="0" cellspacing="4" cellpadding="0">
  <tr>
  <td valign="middle" nowrap>
    	<input id="game_mode" value="<?=$GAME_MODE;?>" type="hidden"/>
	</td>
    <td valign="middle">
    	<input type="number" name="txt_search" id="txt_search" value="" placeholder="<?=$LANG['search']?>" required>
    </td>
    <td valign="top" nowrap>
    	<a data-role="button" data-icon="search"  style="display:inline-block" onClick="search_result($('input#game_mode').val(), $('input#txt_search').val(), 0, 1, 'result');"><?=$LANG['search']?></a> 
    <a data-role="button" data-icon="delete" data-iconpos="notext"  style="display:inline-block;margin-bottom: 14px;" data-rel="back">&nbsp;</a> 
    </td>    
  </tr>
  <tr>
    <td colspan="3">
    <div id="result" style="height:380px; overflow-y:scroll" ><?=$LANG['result']?></div>
    </td>
    </tr>
	</table>
</div>

<div id="help_popup" data-role="popup" data-position-to="window" class="ui-corner-all">
	<?php include("include/gui/info.inc.php");?>
</div>

<table id="play_button" width="0%" border="0" align="right" cellpadding="2" cellspacing="0">
	<tr>
    <td align="center">
    <?php if (isset($_SESSION["id"])){?>
    <a href="#bid" data-role="button" data-inline="true" style="margin:0px; width:80px; display:inline-block" data-icon="bars" data-rel="popup" data-transition="pop" onClick="$('div#contador').html('');"><?=$LANG['play']?></a>
    <?php } ?>
    </td>
    <td align="center">
     <a href="#search_popup" data-role="button" style="margin:0px; display:inline-block" data-icon="search" data-rel="popup" data-transition="pop">&nbsp;</a>
    </td>
    <td align="center">
     <a href="#help_popup" data-role="button" style="margin:0px; display:inline-block" data-icon="info" data-rel="popup" data-transition="pop">&nbsp;</a>
    </td>
  </tr>
</table>
<div id="timer"></div>
<div id="play"></div>