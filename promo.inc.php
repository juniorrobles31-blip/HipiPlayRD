<h3><?=$LANG["sponsor"]?></h3>
	
<div class="help" data-role="collapsibleset" style="text-align:justify" data-mini="true">

	<div data-role="collapsible"  data-collapsed="false">
		<h3 ><?=$LANG["sponsor.what"]?></h3>
		<?=$LANG["sponsor.what.detail"]?>
	</div>
    
    <div data-role="collapsible" >
    	<h3 ><?=$LANG["sponsor.profit"]?></h3>
		<?=$LANG["sponsor.profit.detail"]?>
	</div>
    
    <div data-role="collapsible">
    	<h3 ><?=$LANG["sponsor.how"]?></h3>
		<?=$LANG["sponsor.how.detail"]?>
    </div>
    
     <div data-role="collapsible">
		<h3 ><?=$LANG["sponsor.code.where"]?></h3>
         <?php 
			if (isset($_SESSION["id"])){
				$protocol = explode("/",$_SERVER['SERVER_PROTOCOL']);
				$url = $protocol[0]."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=regist&promo=".$_SESSION["id"];
			}else{
				$url = "";	
			}
		?>
        <p><?=$LANG["sponsor.code.detail"]?></p>
        <p>
        Enlace [<strong style="color:#FF0000">
          <?php if (isset($_SESSION["id"])){echo '<a target="_blank" href="mailto: ?subject=Registrate en Juegos del Dinero&body=Ingresa a la siguiente web '.$url.'">'.$url.'</a>';}else{echo $LANG["login"];}?>
          </strong>] <br>
          <?=$LANG["sponsor.code"]?> [<strong style="color:#FF0000">
          <?php if (isset($_SESSION["id"])){echo $_SESSION["id"];}else{echo $LANG["login"];}?>
          </strong>]        
        </p>
      </div>    
</div>