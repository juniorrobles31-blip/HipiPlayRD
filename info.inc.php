<div style="overflow-y: scroll; height: 500px;padding:6px">
<?php
if (isset($GAME_MODE)){?>

	<h3><?=$LANG["game.rules"]?></h3>
	<?=$LANG["game.rules.detail"]?>

<?php }else{ ?>

<div class="help" data-role="collapsibleset" style="text-align:justify" data-mini="true">
	<div data-role="collapsible" data-collapsed="false">
		<h3 ><?=$LANG["about"]?></h3>
	    <p><?=$LANG["about.detail"]?></p>
  </div>
    
	<div data-role="collapsible">
    	<h3><?=$LANG["how.win"]?></h3>
		<?=$LANG["how.win.detail"]?></div>
    
	<div data-role="collapsible">
		<h3><?=$LANG["how.bet"]?></h3>
		<?=$LANG["how.bet.detail"]?></div>

    <div data-role="collapsible">
		<h3><?=$LANG["how.refilling"]?></h3>
		<?=$LANG["how.refilling.detail"]?>
	</div>

    <div data-role="collapsible">
		<h3><?=$LANG["how.withdrawal"]?></h3>
			<?=$LANG["how.withdrawal.detail"]?><!--<img src="images/pagos.jpg" width="600" height="312" alt="pagos" />--> </div>
    
	<div data-role="collapsible">
		<h3><?=$LANG["game.rules"]?></h3>
      	<?=$LANG["game.rules.detail"]?>
	</div>

</div>
<?php } ?>
</div>

