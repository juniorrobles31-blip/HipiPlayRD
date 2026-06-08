<a href="?page=<?php if(isset($_SESSION["id"])){echo "game";}else{ echo "regist";} ?>">
	<!--<img src="images/home.jpg" width="100%" alt=""/>-->
    <?php if (!$MOVIL){ ?>
    <!--<div class="card-container">
      <div class="flipboard">
        <div class="front"><?=$LANG["click.here"];?></div>
        <div class="front back"><?=$LANG["signup"];?></div>
      </div>
    </div>
    -->
    <?php } ?> 
</a>

<?php include("include/gui/game.inc.php"); ?>