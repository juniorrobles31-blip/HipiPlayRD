<h3 ><?=$LANG["security.notes"];?></h3>

<?php if (isset($_GET["r"])){ echo $LANG["security.warn"].'<br><br>'.$LANG["security.goto.token"]; } ?>

<div class="help" data-role="collapsibleset" style="text-align:justify" data-mini="true">
    <div data-role="collapsible"  data-collapsed="false">
        <h3 ><?=$LANG["password"];?></h3>
        <ol>
             <?=$LANG["security.tips.password"]?>
        </ol>
    </div>
    
    <div data-role="collapsible">
        <h3><?=$LANG["token"];?></h3>
        <ol>
        	<?=$LANG["security.tips.token"]?>
        </ol>
    </div>

</div>