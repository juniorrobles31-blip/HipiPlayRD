<?php 
	if (!isset($CtrlPage)){exit;}
	require_once('./include/class/common.php');
	$display = new DISPLAY();
	$IDGAME	= 0;
	$IDVALUE=0;
	$IDFLTRFROM='';
	$IDFLTRTO='';
	$IDTRAN='';
	$IDUSER='';
	$NOPLAY='';
?>
 <!-- BASIC FORM ELELEMNTS -->

<form2 class="form-horizontal style-form" id="fltrusr_form" name="fltrusr_form" method="post" action="?page=showtransusr" enctype="multipart/form-data">
  <h3><i class="fa fa-angle-right"></i> Transacciones</h3>
  
<div class="form-group" style="float:left">
      <label class="col-sm-2 col-sm-2 control-label">Desde</label>
      <div class="col-sm-10">
          <input type="date" class="form-control"  name="from" id="from" <?php 
            if(isset($_POST['from'])){ 
            echo " value='".$_POST['from']."' ";
            }								
        ?> >
      </div>
  </div>
<div class="form-group" style="float:left">
      <label class="col-sm-2 col-sm-2 control-label">Hasta</label>
      <div class="col-sm-10">
          <input type="date" class="form-control"  name="to" id="to" 
          <?php 
            if(isset($_POST['to'])){ 
            echo " value='".$_POST['to']."' ";
            }
        
        ?> >
      </div>
  </div>

    <div class="form-group" style="float:left">
      <label class="col-sm-2 col-sm-2 control-label">Tipo de juego</label>
      <div class="col-sm-10">
        <?php  $display->ddGames($IDGAME); ?>
      </div>
    </div>
                

<div class="form-group" style="float:left">
      <label class="col-sm-2 col-sm-2 control-label">Tipo de transaccion</label>
      <div class="col-sm-10">
        <?php  $display->ddValues($IDVALUE); ?>
      </div>
    </div>	

<div class="form-group" style="float:left;width:120px">
    <label class="col-sm-2 col-sm-2 control-label">Jugada</label>
    <div class="col-sm-10">
    <input type="number" name="num_play" id="num_play" <?php if(!empty($NOPLAY)){ echo ' value= "'.$NOPLAY.'" '; } ?> >	
    </div>
</div>	

<div class="form-group" style="float:left;width:120px">
    <label class="col-sm-2 col-sm-2 control-label">Transaccion</label>
    <div class="col-sm-10">
    <input type="number" name="num_trans" id="num_trans" <?php if(!empty($IDTRAN)){ echo ' value= "'.$IDTRAN.'" '; } ?> >	
    </div>
</div>	
<div class="form-group" style="float:left">
	<br>			
	<!--<button type="submit" class="btn btn-theme">Filtrar</button>-->
    <button type="button" onClick="transFilter();">Filtrar</button>
</div>

</form2>

<div id="user_trans" style="margin:0 auto;width:auto">
 <?php 

 	$display->tblUserTrans($IDTRAN,$IDGAME,$IDFLTRFROM,$IDFLTRTO,$IDUSER,$IDVALUE,$NOPLAY); 
 ?>
</div>