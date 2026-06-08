<?php if ($MOVIL){ ?>
<div class="header_left">
	<a href="#panel_side" data-role="button" data-icon="bars" data-iconpos="notext" accesskey="M"><?=$LANG["balance"]?></a>
    
</div>

<?php } ?>

<div class="header_right" >
	<?php if(isset($_SESSION["id"])) {?>
    <span style="font-size:small"><?=$LANG["balance"]?></span><br>
    <h2 style='display:inline;'>$<?=$_SESSION['balance'];?></h2>
	<?php } ?>
</div>

<div class="title">
   <div class="logo"></div>
</div>

  