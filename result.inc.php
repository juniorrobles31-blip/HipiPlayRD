<?php
//require_once 'include/lib/sessionout.php';

?>
<form id="search_form" name="search_form" method="post" action="?page=result" >


<table width="320px" border="0">
  <tr>
    <td colspan="2"><img src="images/bn-result-es.gif" alt="" width="230" height="46" /></td>
  </tr>
  <tr>
    <td >Número Jugada<span class="red">*</span>:</td>
    <td><span id="sprytextfield">
      <input type="text" <?php echo "name=txt_search  id=txt_search" ?> class="tb1" 
        <?php if(isset($_POST['txt_search']))
		{ 
		echo "value=".$_POST['txt_search'];                 
		}?>
         />
      <span class="textfieldRequiredMsg">Requerido</span><span class="textfieldInvalidFormatMsg">Solo nm_one</span></span></td>
  </tr>
  <tr>
    <td colspan="2"><input type="image" src="images/btn-search-es.gif"  name="Buscar" width="83" height="18"  /></td>
  </tr>
</table>
</form>
<!--<div id="SRCH_TABLE">-->
 <?php
	
$ResultCountBuscar = 0;
	
if(isset($_POST['txt_search']))
{
$search = $_POST["txt_search"];

	if($stmtq = $mysqli->prepare('SELECT id_won, color, DATE_FORMAT( `time` , "%d-%b-%Y / %r" ) AS time FROM tb_won_dice.1 WHERE id_won REGEXP ?')) 
	{
		if($stmtq->bind_param('i', $search))
		{
			if($stmtq->execute())
			{
				if($stmtq->store_result())
				{
					$num_rows = $stmtq->num_rows;
				}
			}
		} 
		$stmtq->close();
	}
	
	if($num_rows==0 or $search=="")
	{
	$ResultTableRowsBuscar ='<h3>No hay resultados, intente con otra busqueda</h3>';
	}
	else
	{
	$ResultTableRowsBuscar = 
	'<tr style="font-weight:bold;">
	<td align="center"><h2>Número Jugada</h2></td>
	<td align="center"><h2>Color</h2></td>
	<td align="center"><h2>Día</h2></td>
	<td align="center"><h2>Hora</h2></td>
	</tr>
	';
		if($stmtre = $mysqli->prepare("SELECT id_won, color, DATE_FORMAT( time , '%e-%M-%Y' ) AS `day`, DATE_FORMAT( `time` , '%h:%i %p' ) AS time  FROM tb_won_dice.1 WHERE id_won = ?")) 
		{
			if($stmtre->bind_param('i', $search))
			{
				if($stmtre->execute())
				{
						if($stmtre->bind_result($id_won, $color,$day, $time))
						{  
							while($stmtre->fetch())
							{
								if($color==0){
								$color="Black.png";	
								}elseif($color==1){
								$color="Red.png";	
								}else{
								$color="el resultado fue alterado";
								}
								$day = str_replace($eng,$esp,$day);	
								$ResultCountBuscar++;
								$ResultTableRowsBuscar .=  
									"<tr>\n" .
									'<td align="center">' . htmlentities($id_won, ENT_QUOTES) . "</td>\n" . 
									'<td align="center"><img src=images/' . htmlentities($color, ENT_QUOTES) . " width=30 height=30 /></td>\n" . 
									'<td align="center">' . htmlentities($day, ENT_QUOTES) . "</td>\n" . 
									'<td align="center">' . htmlentities($time, ENT_QUOTES) . "</td>\n" . 							
									"</tr>\n";
							}
						}
					
				}
			} 
			$stmtre->close();
		}
	}
	
}	
else
{ /*TODAS LAS JUGADAS*/


if($stmtnum = $mysqli->prepare('SELECT count(*) as `total` FROM tb_won_dice.1')){
		if($stmtnum->execute()){
			if($stmtnum->bind_result($total)){
					while($stmtnum->fetch()){
					 $numrows = $total;
					}
				}
		}
		$stmtnum->close();
	}
	
	//Obtain the required page number		
	if (isset($_GET['pageno'])){
		$pageno = $_GET['pageno'];
	} 
	else {
		$pageno = 1;
	} 
	
	//Calculate number of $lastpage
	$rows_per_page = 10;
	$lastpage      = ceil($numrows/$rows_per_page);
	
	//Ensure that $pageno is within range
	$pageno = (int)$pageno;
	if ($pageno > $lastpage){
	$pageno = $lastpage;
	} 
	if ($pageno < 1){
	$pageno = 1;
	} 
	//Construct LIMIT clause
	$limit = ($pageno - 1) * $rows_per_page;
	$offset = $rows_per_page;
		
$ResultTableRowsBuscar = 
	'<tr style="font-weight:bold;">
	<td align="center"><h2>Número Jugada</h2></td>
	<td align="center"><h2>Color</h2></td>
	<td align="center"><h2>Día</h2></td>
	<td align="center"><h2>Hora</h2></td>
	</tr>
	';

				
		if($stmt = $mysqli->prepare("SELECT id_won, color,  DATE_FORMAT( time , '%e-%M-%Y' ) AS `day`, DATE_FORMAT( `time` , '%h:%i %p' ) AS time FROM tb_won_dice.1 ORDER BY id_won DESC LIMIT ?,?")){	
			if($stmt->bind_param('ii', $limit, $offset)){
				if($stmt->execute()){ 
					if($stmt->bind_result($id_won, $color, $day, $time)){  						while($stmt->fetch())	{
								if($color==0){
								$color="Black.png";	
								}elseif($color==1){
								$color="Red.png";	
								}else{
								$color="el resultado fue alterado";
								}

$day = str_replace($eng,$esp,$day);	
							$ResultCountBuscar++;
							$ResultTableRowsBuscar .=  
								"<tr class=highlighter >\n" .
								'<td align="center">' . htmlentities($id_won, ENT_QUOTES) . "</td>\n" . 
									'<td align="center"><img src=images/' . htmlentities($color, ENT_QUOTES) . " width=30 height=30 /></td>\n" . 
									'<td align="center">' . htmlentities($day, ENT_QUOTES) . "</td>\n" . 
									'<td align="center">' . htmlentities($time, ENT_QUOTES) . "</td>\n" .   	  								
								"</tr>\n";
						}
					}
				}
			}
			$stmt->close();
		}
}

$mysqli->close();
	
	
	echo '<table  class="result" width="100%">', 
		$ResultTableRowsBuscar, 
		'</table>';
		
if(!isset($_POST['txt_search'])){

	 /*previous pages				*/
	if ($pageno == 1)	{
		echo " FIRST PREV ";
	} 
	else{
		echo '<a href="?page='.$PageName.'&pageno=1" class="green">FIRST</a> ';
		$prevpage = $pageno-1;
		echo ' <a href="?page='.$PageName.'&pageno='.$prevpage.'" class="green">PREV</a>';
	} 

	echo " ( Page $pageno of $lastpage ) ";

	 /*following pages*/
	if ($pageno == $lastpage) 
	{
		echo " NEXT LAST ";
	} 
	else 
	{
		$nextpage = $pageno+1;
		echo '<a href="?page='.$PageName.'&pageno='.$nextpage.'" class="green">NEXT</a> ';
		echo '<a href="?page='.$PageName.'&pageno='.$lastpage.'" class="green">LAST</a>';
	} 
	   							
}
				
    ?>
<script type="text/javascript">
var sprytextfield = new Spry.Widget.ValidationTextField("sprytextfield", "integer", {validateOn:["change"]});
</script>
   <!-- </div>-->

