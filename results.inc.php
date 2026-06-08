<?php
	if (!isset($CtrlPage)){exit;}
	require_once('./include/class/common.php');
	$display = new DISPLAY();

	$IDGAME	= 0;
	$IDFLTRFROM='';
	$IDFLTRTO='';
	$NOPLAY='';

	if(isset($_POST['from'])){ $IDFLTRFROM = $_POST['from']; }
	if(isset($_POST['to'])){ $IDFLTRTO = $_POST['to']; }
	if(isset($_POST['cboGame'])){ $IDGAME = $_POST['cboGame']; }
	if(isset($_POST['num_play'])){ $NOPLAY = $_POST['num_play']; }
	if(empty($IDFLTRFROM)){ $IDFLTRFROM =TODAY;}
	if(empty($IDFLTRTO)){$IDFLTRTO = date('Y-m-d', strtotime(TODAY. ' + 1 day'));}

?>
<!-- BASIC FORM ELELEMNTS -->
<div class="row mt">
    <div class="col-lg-12">
      <div class="form-panel">
          <form class="form-horizontal style-form"  id="fltrusr_form" name="fltrusr_form" method="post" action="?page=results">
						  <h3><i class="fa fa-angle-right"></i> Resultados de los juegos</h3>
            <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Desde</label>
                  <div class="col-sm-10">
                      <input type="date" class="form-control"  name="from" id="from" <?php
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
                  <label class="col-sm-2 col-sm-2 control-label">No. Jugada</label>
                  <div class="col-lg-10">
                      <input type="number" class="form-control" name="num_play" id="num_play" <?php if(!empty($NOPLAY)){ echo ' value= "'.$NOPLAY.'" '; } ?> >
                  </div>
              </div>
          <button type="submit" class="btn btn-theme">Filtrar</button>
          </form>
      </div>
    </div><!-- col-lg-12-->
</div><!-- /row -->



<div id="user_trans" style="margin:0 auto;width:auto">
 <?php 	$display->tblGamesResults($IDGAME,$IDFLTRFROM,$IDFLTRTO,$NOPLAY); ?>
</div>
