<br>
<?php 
require_once("card.php");

if(!$MOVIL){ echo '<div style="max-width:800px; margin: 0 auto;">';}
	
card("caballos");
card("dado_directo");
card("super_dado");
card("dado_tripleta");
card("ruleta");
card("puntazo");

if(!$MOVIL){ echo '</div>';}
?>
