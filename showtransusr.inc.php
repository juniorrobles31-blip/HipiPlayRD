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

	if(isset($_POST['from'])){ $IDFLTRFROM= $_POST['from']; }
	if(isset($_POST['to'])){ $IDFLTRTO= $_POST['to']; }
	if(isset($_POST['cboGame'])){ $IDGAME= $_POST['cboGame']; }
	if(isset($_POST['cboType'])){ $IDVALUE= $_POST['cboType']; }
	if(isset($_POST['num_play'])){ $NOPLAY= $_POST['num_play']; }
	if(isset($_POST['num_trans'])){ $IDTRAN= $_POST['num_trans']; }
	if(isset($_REQUEST[KEY])){ $IDUSER= $_REQUEST[KEY]; }
	if(isset($_GET['game'])){ $IDGAME= $_GET['game']; }
	if(isset($_GET['play'])){ $NOPLAY= $_GET['play']; }
	if(empty($IDFLTRFROM)){ $IDFLTRFROM =TODAY;}
	if(empty($IDFLTRTO)){$IDFLTRTO = date('Y-m-d', strtotime(TODAY. ' + 1 day'));}



?>
<!-- BASIC FORM ELELEMNTS -->
<div class="row mt">
    <div class="col-lg-12">
      <div class="form-panel">
          <form class="form-horizontal style-form"  id="fltrusr_form" name="fltrusr_form" method="post" action="?page=showtransusr">
 					<input type="hidden" name=<?=KEY?> value="<?=$IDUSER?>">
						  <h3><i class="fa fa-angle-right"></i> Transacciones</h3>
            <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Desde</label>
                  <div class="col-sm-10">
                      <input type="date" class="form-control"  name="from" id="from"
											<?php
                        if(!empty($IDFLTRFROM)){
                        echo " value='".$IDFLTRFROM."' ";
                        }
                    ?> >
                  </div>
              </div>
            <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Hasta</label>
                  <div class="col-sm-10">
                      <input type="date" class="form-control"  name="to" id="to"
											<?php
                        if(!empty($IDFLTRTO)){
                        echo " value='".$IDFLTRTO."' ";
                        }
                    ?> >
                  </div>
              </div>


            <div class="form-group">
              <label class="col-sm-2 col-sm-2 control-label">Tipo de Juego</label>
              <div class="col-sm-10">
                  <?php $display->ddGames($IDGAME); ?>
              </div>
            </div>

               <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label"> Tipo de transaccion</label>
                  <div class="col-lg-10">
                       <?php $display->ddValues($IDVALUE); ?>
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">No. Jugada</label>
                  <div class="col-lg-10">
                      <input type="number" class="form-control" name="num_play" id="num_play" <?php if(!empty($NOPLAY)){ echo ' value= "'.$NOPLAY.'" '; } ?> >
                  </div>
              </div>
			   	<div class="form-group">
			      <label class="col-sm-2 col-sm-2 control-label">No. Transaccion</label>
			      <div class="col-lg-10">
			           <input type="number"  class="form-control" name="num_trans" id="num_trans" <?php if(!empty($IDTRAN)){ echo ' value= "'.$IDTRAN.'" '; } ?> >
			      </div>
        	</div>

          <button type="submit" class="btn btn-theme">Filtrar</button>
          </form>
      </div>
    </div><!-- col-lg-12-->
</div><!-- /row -->



<div id="user_trans" style="margin:0 auto;width:auto">
 <?php

	/* if(isset($_POST['srchusr'])){
		$USER = $_POST['srchusr'];
	}*/

	//$display->formSearchUser($_GET['page'],$IDUSER,'Busqueda de usuario');

 	$display->tblUserTrans($IDTRAN,$IDGAME,$IDFLTRFROM,$IDFLTRTO,$IDUSER,$IDVALUE,$NOPLAY);
 ?>
</div>
