<?php

	if (!isset($_GET["sec"])){
		$_GET["sec"] = "bed";
	}
	if (!isset($logid)){
		redirigir("Location:?page=logout");
		exit;
	}
	require_once('./include/class/common.php');
	$display = new DISPLAY();
	
	
?>
<br>
<a href="?page=profile&sec=bed" data-icon="grid" class="button_menu <?php if ($_GET["sec"] == "bed"){echo 'active ';}?>"><?=$LANG["bids"]?></a>
<a href="?page=profile&sec=account" data-icon="grid" class="button_menu <?php if ($_GET["sec"] == "account"){echo 'active ';}?>"><?=$LANG["account"]?></a>
<a href="?page=profile&sec=sponsor" data-icon="grid" class="button_menu <?php if ($_GET["sec"] == "sponsor"){echo 'active ';}?>"><?=$LANG["sponsor"]?></a>

<table width="0%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
  <td valign="top" align="center" style="min-width:400px">
    <?php
switch ($_GET["sec"]){
case "bed": ?>
    <table width="100%" border="0" cellspacing="8" cellpadding="0">
      <tr>
        <td> 
		<?php 
            $idGame = 0;
            $display->ddGames($idGame); 
        ?>
          </td>
        <td><input type="search" name="txt_search" id="txt_search" style="display:inline-block;"/></td>
        <td><a data-role="button" data-icon="search" style="display:inline-block" onClick="if ( $('select#cboGame').val()==0){alert('<?=$LANG["choose.gametype"]?>');}else{search_result($('select#cboGame').val(), $('input#txt_search').val(), 1, 1, 'search_result');}">&nbsp;</a> </td>
        </tr>
      </table>
    <div id="search_result">
      <script>
	  	search_result("", "0", 1, 1, 'search_result');
	  </script>
      </div>
  <?php
break;
case "sponsor":
	$protocol = explode("/",$_SERVER['SERVER_PROTOCOL']);
	$url = strtolower($protocol[0])."://".$_SERVER['SERVER_NAME'].str_replace("index.php","",$_SERVER['SCRIPT_NAME'])."?page=regist&sponsor=".$_SESSION["id"];//
	
	$fb_link = 'https://www.facebook.com/dialog/share?'.
  'app_id=119215678262556'.
  '&display=popup'.
  '&caption='.str_replace(' ','%20',"TITLE").
  '&href='.urlencode('http://juegosdeldinero.com?page=regist&sponsor='.$_SESSION["id"]).
  '&redirect_uri='.urlencode('http://juegosdeldinero.com');
  
	//$url = str_replace('&','%26',$url);
	$fb_link = "http://www.facebook.com/sharer.php?s=100".
				"&p[title]="."TITLE".
				"&p[summary]="."SUMARY".
				"&p[url]=".urlencode("http://juegosdeldinero.com?page=regist&sponsor=".$_SESSION["id"]).
				"&p[images][0]=".urlencode('http://juegosdeldinero.com/images/btn_puntazo.png');
				
				//$fb_link = 'http://www.facebook.com/sharer.php?s=100&amp;p[url]=http://ar.zu.my&amp;p[images][0]=http://www.gravatar.com/avatar/2f8ec4a9ad7a39534f764d749e001046.png&amp;p[title]=Rock on Arzumy!&amp;p[summary]=In this blog, Arzumy will teach you how to rock!';
?>
<table width="0" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr><th colspan="2"><?=$LANG["sponsor"]?></th></tr>
</thead>
  <tbody>
   
    <tr>
      <td height="48"><?=$LANG["code"]?></td>
      <td height="48">&nbsp;<?=$_SESSION["id"];?></td>
    </tr>
    <tr>
      <td height="48"><?=$LANG["link"]?></td>
      <td height="48">&nbsp;<?=$url;?></td>
    </tr>
    <tr>
      <td height="48"><?=$LANG["share"]?></td>
      <td height="48">
      
      	<a class="icon icon_gmail" title="gmail" href="mailto: ?subject=Registrate en Juegos del Dinero&body=Ingresa a la siguiente web <?=$url;?>" target="_blank"/>
        <a class="icon icon_fb" title="Facebook" href="<?=$fb_link;?>" target="_blank"/>
        <a class="icon icon_twitter" title="Twitter" href="https://twitter.com/home?status=<?=str_replace('&','%26',$url);?>" target="_blank"/>
        <a class="icon icon_gplus" title="Google+" href="https://plus.google.com/share?url=<?=str_replace('&','%26',$url);?>" target="_blank"/>
         <a class="icon icon_likedin" title="Likedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?=str_replace('&','%26',$url);?>&title=Juegos%20del%20Dinero&summary=Juega%20Online&source=" target="_blank"/>
        
      </td>
    </tr>
    	<?php
		   if ($_SESSION['sponsor'] === NULL || $_SESSION['sponsor'] === 0){?>        
	<tr>
      <td height="48">Registrar mi promotor</td>
      <td height="48">
				 <div style="width:340px; margin: 0 auto">
					<br>
					<strong><?=$LANG["sponsor"]?></strong>
					<br>
					<br>
					<?=$LANG["sponsor.regist"]?>
					<table>
						<tr><td>
						<input name="sponsor" id="sponsor" type="text" autofocus form="sponsor_form" placeholder="<?=$LANG["sponsor"]?> ID" title="Sponsor" maxlength="4" value="">
						</td><td>
						<button onClick="sponsorSave()"><?=$LANG["send"]?></button>
						</td>
						</tr>
					</table> 
				</div>    	
			</td>
    	</tr>
   	<?php }	 ?>
   
    <tr>
      <th height="48" colspan="2"><?=$LANG["affiliates"]?></th>
      </tr>
   <tr>
   	<td colspan="2">
   	<?php $display->tblSponsor($_SESSION["id"]); ?>

   	</td>
   </tr>
  </tbody>
</table>


<?php
break;
case "account":?>
    <div style="max-width:400px; height:500px; padding:8px 8px;">
    
        <div id="account_main" style="width:100%;margin:8px;">
        <input id="account_user" type="hidden" value="<?=$_SESSION["id"];?>" >
        <table width="500px" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th><?=$LANG["name"]?></th>
            <td width="380px" height="48px"><?=$_SESSION["usuario"];?></td>
          </tr>
          <tr>
            <th><?=$LANG["balance"]?></th>
            <td width="380px" height="48px" valign="middle"><span id="balance2"><?=$_SESSION["balance"];?></span> <img class="icon" src="./images/icon_refresh.png" style="padding:4px;vertical-align: middle;" title="<?=$LANG["balance"]?>" onClick="getBalance();"/></td>
          </tr>
          <tr>
            <th><?=$LANG["code"]?></th>
            <td height="48px"><?=$_SESSION["id"];?></td>
          </tr>
          <tr>
            <th><?=$LANG["security"]?></th>
            <td>
            <img class="icon" src="./images/icon_pass.png" title="<?=$LANG["password.change"]?>" onClick="$('div#account_main').toggle(400);$('div#account_password').toggle(500);"/>
            <img class="icon" src="./images/icon_token.png" title="<?=$LANG["token"]?>" onClick="$('div#account_main').toggle(400);$('div#account_token').toggle(500);getConfig();"/>
            </td>
          </tr>
          <tr>
            <th><?=$LANG["balance"]?></th>
            <td>
            <img class="icon" src="./images/icon_retiro.png" title="<?=$LANG["money.back"]?>" onClick="$('div#account_main').toggle(400);$('div#account_retire').toggle(500);"/>
            <img class="icon" src="./images/icon_deposit.png" title="<?=$LANG["money.refill"]?>" onClick="$('div#account_main').toggle(400);$('div#account_recharge').toggle(500);"/>
            </td>
          </tr>
        </table>
        </div>
        
        <div id="account_password" style="width:100%;display:none">
        <?=$LANG["password.change"]?>
            <table width="0" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td><?=$LANG["previous"]?></td>
                  <td><input id="account_pass1" type="password" ></td>
                </tr>
                 <tr>
                  <td><?=$LANG["new"]?></td>
                  <td><input id="account_pass2" type="password" ></td>
                </tr>
                 <tr>
                  <td><?=$LANG["new"]?></td>
                  <td><input id="account_pass3" type="password" ></td>
                </tr>
              </tbody>
            </table>
            <div style="text-align:center">
              <a data-role="button" data-icon="arrow-l" onClick="$('#account_main').toggle(500);$('#account_password').toggle(500);" style="display:inline-block;width:33%"><?=$LANG["cancel"]?></a>
              <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="changePass();" style="display:inline-block;width:33%;"><?=$LANG["save"]?></a>
          </div>
       	</div>
        
        <div id="account_retire" style="width:100%;display:none">
        	<?=$LANG["money.back"]?>
          <div id="retire_2" style="display:n one">            	
       		  <!--<a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="$('#retire_3').toggle(500);$('#retire_3_banca').toggle(500);">Banca</a>
       		  <a data-role="button" data-icon="arrow-r" data-iconpos="right" >Env&iacute;o</a>
              -->
       		  <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="$('#retire_local').toggle(500);$('#retire_2').toggle(500);" >Zuzuvama</a>
                 <br>
                <a data-role="button" data-icon="arrow-l" onClick="$('#account_main').toggle(500);$('#account_retire').toggle(500);" style="display:inline-block;width:33%"><?=$LANG["cancel"]?></a>
       	  </div>
            
        	<div id="retire_local" style="display:none">
            	Digite el monto a retirar
            	<input type="number" placeholder="monto" id="retire_value">
                <br>
                <div style="text-align:center">
       		  	<a data-role="button" data-icon="arrow-l" onClick="$('#retire_local').toggle(500);$('#retire_2').toggle(500);" style="display:inline-block;width:33%;"><?=$LANG["cancel"]?></a>
                <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="retire(22,'input#retire_value');" style="display:inline-block;width:33%;">Retirar</a>
                </div>
        	</div>
            
            <div id="retire_local_2" style="display:none">            	
            	Su código de retiro es [<strong id="code"></strong>], diríjase al punto de venta más cercano.
            </div>
            
            <div id="retire_3_banca" style="display:none">
       		  <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="$('#retire_3_banca_2').toggle(500);$('#retire_3_banca').toggle(500);">Llueve</a>
       		  <a data-role="button" data-icon="arrow-r" data-iconpos="right" >So&ntilde;adora</a>
                <br>
       		  <a data-role="button" data-icon="arrow-l" onClick="$('#retire_3').toggle(500);$('#retire_3_banca').toggle(500);" style="display:inline-block;width:33%;"><?=$LANG["cancel"]?></a>
        	</div>
            
            <div id="retire_3_banca_2" style="display:none">
        		Se te ha enviado un c&oacute;digo a tu correo electr&oacute;nico para verificar tu identidad, favor ingresarlo en el siguiente recuadro.
                <input type="text" placeholder="c&oacute;digo" > 
                <br>
                <div style="text-align:center">
        		<a data-role="button" data-icon="arrow-l" onClick="$('#retire_3_banca').toggle(500);$('#retire_3_banca_2').toggle(500);" style="display:inline-block;width:33%;"><?=$LANG["cancel"]?></a>
                <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="$('#retire_3_banca_3').toggle(500);$('#retire_3_banca_2').toggle(500);" style="display:inline-block;width:33%;">Validar</a>
                </div>
        	</div>
            
            <div id="retire_3_banca_3" style="display:none">
        		Su <strong>c&oacute;digo de valor</strong> es : [<strong style="font-size:large; color:#ADFF2F">c0d1g0v4l0r</strong>]. Pase por la banca m&aacute;s cercana con su documento de indentidad.
        	</div>
        
        </div>
    
    	<div id="account_token" style="width:100%;display:none">
        TOKEN
          <table width="0" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td nowrap>Activar Token&nbsp;</td>
                  <td>&nbsp;<input type="checkbox" data-role="flipswitch" name="account_token" id="account_token" onChange="activeToken();">
            	</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><p>EL token es un codigo de 8 digitos generado con la cual se comprueba la identidad del usuario aumentando exponenciarmente la seguridad.</p>
                  <p>Una vez activada la opcion de token, debera instalar la aplicacion en su smartphone Android desde google play.</p></td>
                </tr>
                <tr>
                  <td nowrap>Enviar e-mail&nbsp;</td>
                  <td>&nbsp;<input type="checkbox" data-role="flipswitch" name="account_token_email" id="account_token_email" onChange="activeTokenEmail();">
            	</td>
                </tr>
                <tr>
                  <td nowrap>&nbsp;</td>
                  <td>Si no tienes acceso a un smartphone android, puedes activar esta opcion y el token se enviara a tu correo electronico</td>
                </tr>
            </tbody>
          </table>
            <p>&nbsp;</p>
            
            <div style="text-align:center">
              <a data-role="button" data-icon="arrow-l" onClick="$('#account_main').toggle(500);$('#account_token').toggle(500);" style="display:inline-block;width:33%"><?=$LANG["cancel"]?></a>
              <a data-role="button" data-icon="arrow-r" data-iconpos="right" onClick="window.open('https://play.google.com/store/apps/details?id=net.magnastudios.zuzuvama');" style="display:inline-block;width:33%;">Descargar</a>
          </div>
       	</div>
            
            <div id="account_recharge" style="width:100%;display:none">
        	<?=$LANG["money.refill"]?><br>
                <h4>Recargas</h4>
                <p>
                	<a data-role="button" data-icon="arrow-r" onClick="recharge(100);" style="width:33%">$100</a>
                    <a data-role="button" data-icon="arrow-r" onClick="recharge(500);" style="width:33%">$500</a>
                    <a data-role="button" data-icon="arrow-r" onClick="recharge(1000);" style="width:33%">$1000</a>
                </p>
                
                <div style="text-align:center">
                  <a data-role="button" data-icon="arrow-l" onClick="$('#account_main').toggle(500);$('#account_recharge').toggle(500);" style="display:inline-block;width:33%"><?=$LANG["cancel"]?></a>
                   
                </div>
        	</div>
    
    </div>
    <?php
break;
case "?":

break;
}
?>     
  </td>
  </tr>
</table>
