<ul data-role="listview">
	<li class=""><a href="?page=home" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("home");?>"><?=$LANG["home"]?></a></li>
    <li><a href="?page=info" class="ui-btn ui-btn-icon-right ui-icon-info <?php activePage("info");?>"><?=$LANG["how.win"]?></a></li>
    <li><a href="?page=promo" class="ui-btn ui-btn-icon-right ui-icon-info <?php activePage("promo");?>"><?=$LANG["sponsor"]?></a></li>
	<li><?=$LANG["game"]?></li>
	<li><a href="?page=game" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("game");?>"><?=$LANG["all"]?></a></li>
	<li><a href="?page=puntazo" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("puntazo");?>"><?=$LANG["game.puntazo"]?></a></li>
    <!--<li><a href="?page=conoce" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("conoce");?>"><?=$LANG["game.conoce"]?></a></li>-->
	<li><a href="?page=dado_directo" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("dado_directo");?>"><?=$LANG["game.dice.1"]?></a></li>
	<li><a href="?page=super_dado" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("super_dado");?>"><?=$LANG["game.dice.2"]?></a></li>
	<li><a href="?page=dado_tripleta" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("dado_tripleta");?>"><?=$LANG["game.dice.3"]?></a></li>
	<li><a href="?page=ruleta" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("ruleta");?>"><?=$LANG["game.roulette"]?></a></li>
	<li><a href="?page=caballos" class="ui-btn ui-btn-icon-right ui-icon-carat-r <?php activePage("caballos");?>"><?=$LANG["game.horse"]?></a></li>

	<li><?=$LANG["user"]?></li>
<?php if (!isset($_SESSION["id"])) { ?>
	
    <li><a href="#panel_user" onClick="setFocus('username',500);" class="ui-btn ui-btn-icon-right ui-icon-user <?php activePage("login");?>"><?=$LANG["login"]?></a></li>
    <li><a href="?page=regist"  class="ui-btn ui-btn-icon-right ui-icon-user <?php activePage("regist");?>"><?=$LANG["signup"]?></a></li>

<?php }else{ ?>
    <li><a href="?page=profile" class="ui-btn ui-btn-icon-right ui-icon-user <?php activePage("profile");?>"><?php echo $logname;?></a></li>
    <li><a href="?page=logout" class="ui-btn ui-btn-icon-right ui-icon-power <?php activePage("logout");?>"><?=$LANG["exit"]?></a></li>

<?php } ?>
</ul>