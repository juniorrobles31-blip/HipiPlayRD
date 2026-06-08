<?php
	require_once('include/lib/challenge.php');
	$CHALLENGE_FIELD_PARAM_NAME = "verificationCode";
	
	$ERROR = "";
	
	// procesar login
	if(isset($_POST['user'])){
		$ERROR = require_once("chglogin.inc.php");
		if ($ERROR == ""){
			return;	
		}
	}
	$newToken = generateFormToken('login_form'); 
?>

<div aria-hidden="true" aria-labelledby="modalLoginLabel" role="dialog" tabindex="-1" id="modalLogin" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          <?=$LANG["login"]?>
        </h4>
      </div>
      <form class="form-login" action="?" method="post" >
        <div class="modal-body">
          <div class="login-wrap" id="login_body">
            <?php 
			if ($ERROR !== ""){ 
          		echo '<div class="alert alert-danger"><b>** '.$ERROR.'</b></div>
				<script type="text/javascript">
					$(window).load(function(){
						$(\'#modalLogin\').modal(\'show\');
					});
				</script>
				';				
			} 
		?>
            <br>
            <input type="text" class="form-control"  name="user" id="user"  placeholder="Usuario" required autofocus
			<?php if(isset($_GET['user'])){ echo " value='".$_GET['user']."' "; } ?> >
            <br>
            <input type="password" class="form-control" name="password" id="password" placeholder="Clave" required>
            <br>
            <img src="getimage.php" style="display:inline-block"/>
            <input type="text" class="form-control" placeholder="Codigo imagen" style="display:inline-block; width:190px"
					name="<?php echo($CHALLENGE_FIELD_PARAM_NAME) ?>" 
					id="<?php echo($CHALLENGE_FIELD_PARAM_NAME) ?>" 
					maxlength="<?php echo($CHALLENGE_STRING_LENGTH) ?>" 
					size="<?php echo($CHALLENGE_STRING_LENGTH) ?>" 
					 required>
            <label class="checkbox"> <span class="pull-right"> <a data-toggle="modal" href="#" onClick="$('div#login_body').hide(500);$('div#login_recovery').show(500);"> Olvido su clave?</a> </span> </label>
            <button class="btn btn-theme btn-block" href="index.html" type="submit"><i class="fa fa-lock"></i> Entrar</button>
            <input type="hidden" name="token" value="<?php echo $newToken; ?>">
          </div>
          <div class="login-wrap" id="login_recovery" style="display:none">
            <p>Debajo coloque su Usuario o email para reiniciar la clave</p>
            <input type="text" name="email" placeholder="Usuario o Email" autocomplete="off" class="form-control placeholder-no-fix">
            <button class="btn btn-default" type="button" onClick="$('div#login_body').show(500);$('div#login_recovery').hide(500);">Cancelar</button>
            <button class="btn btn-theme" type="button">Enviar</button>
          </div>
        </div>
        <div class="modal-footer">
          <div style="text-align:center"> Aun no tienes tu cuenta?<br/>
            <a class="" href="?page=signup"> Crear cuenta </a> </div>
        </div>
      </form>
    </div>
  </div>
</div>