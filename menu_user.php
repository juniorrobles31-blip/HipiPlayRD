<ul data-role="listview">
   <?php if (!isset($_SESSION["id"])) { 
        require("include/gui/login.inc.php");
		
		if (!isset($wsStatus)){ 
    ?>	
    <!-- <li><a href="#" class="ui-btn ui-btn-icon-right ui-icon-info" onClick="RU.show()"><?=$LANG["login"]?></a></li> -->
    <li><a href="?page=forgot" class="ui-btn ui-btn-icon-right ui-icon-info <?php activePage("forgot");?>"><?=$LANG["forgot"]?></a></li>
   <li ><a href="?page=regist" class="ui-btn ui-btn-icon-right ui-icon-info <?php activePage("regist");?>"><?=$LANG["signup"]?></a></li>

<?php }
 }else{ ?>

    <li><a href="?page=profile&sec=account" class="ui-btn ui-btn-icon-right ui-icon-user <?php activePage("profile");?>"><?php echo $_SESSION['usuario'];?></a></li>
    <li><a href="?page=profile&sec=bed" class="ui-btn ui-btn-icon-right ui-icon-shop <?php activePage("profile");?>"><?=$LANG["bid.history"]?></a></li>     
	<li><a href="?page=profile&sec=account" class="ui-btn ui-btn-icon-right ui-icon-shop <?php activePage("profile");?>"><?=$LANG["money.back"]?></a></li>
    <li><a href="?page=logout" class="ui-btn ui-btn-icon-right ui-icon-power"><?=$LANG["exit"]?></a></li>
    <li><?=$LANG["bids"]?> </li>
    <div id="current_bids"></div>
<?php } ?>
</ul>