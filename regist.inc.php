<?php
if (isset($_SESSION["id"])){
	//return;
}

if (isset($_GET['sponsor'])){
	$_SESSION['sponsor'] = $_GET['sponsor'];
}

if (!isset($_SESSION['sponsor'])){
	$_SESSION['sponsor'] = 0;
}
?>
<script>
//alert(<?=$_SESSION['sponsor']?>);
var ZZVM = {
	callback : function(json){		
		$.ajax({
			type 	 : "POST",
			dataType : "json",
			url 	 : "system.php",
			async	 : true,
			data : {
				service : "zzvm.callback",
				id      : json.id,
				alias   : json.alias,
				sponsor : <?=$_SESSION['sponsor']?>
			},
			success : function (json) {		
				if (json.STATUS === "OK") {
					 window.location="?page=profile&sec=account";
				}else{
					console.log(json.INFO);
				}
			},
			error : function (xhr, status) {
				//var json = JSON.parse(xhr);
				$("body").html(xhr.responseText);
			}
		});
	}
};
</script>
	<br>
	<img src="images/zuzuvama.png" height="36px">
	<br>
	Zuzuvama.connect

<div id="eula"  style="max-width:100%;margin:0 auto" >
	<h3><?=$LANG["eula"]?></h3>
	<?=$LANG["eula.info"]?>
    <br>
    <br>
    <button class="zzvm" style="width:200px;margin:0 auto" onClick="$('div#eula').hide(500);$('div#regist').show(500);">Acepto</button>
</div>

<div id="regist" style="width:640px;min-height:640px;display:none;margin:0 auto">
    <iframe frameborder="0" src="https://zuzuvama.com/?page=signup&iframe=true" width="640" height="640" style="min-height:640px">
    
    </iframe>
</div>

<?php
return;
?>
<div class="login">
<div id="progressBar" style="max-height:80px"></div>

<div id="eula" style="text-align:left; display:n one">
  <h3 style="text-align:center"><?=$LANG["eula"]?></h3>
  <br>
  <ul >
  <li><?=$LANG["eula.info"]?></li>
  </ul>
  
  <p >
    <input type="checkbox" name="checkbox" id="checkbox" onClick="validateStep(0)">
    <label for="checkbox" ><?=$LANG["signup"]?></label>
  </p>

</div>


<div id="registro" style="display:none; padding:8px 8px;text-align:left;">

	<form id="reg_form" name="reg_form" method="post" enctype="multipart/form-data" onSubmit="validateStep(1); return false;">

    <label for="user_name" ><?=$LANG["user"]?>:</label>
    <input type="text" name="user_name" id="user_name" value="" placeholder="<?=$LANG["user"]?>" required data-theme="a">
    
    <label for="email" ><?=$LANG["email"]?>:</label>
    <input type="email" name="email" id="email" value="" placeholder="<?=$LANG["email"]?>" required data-theme="a">

    <label for="password"><?=$LANG["password"]?>:</label>
    <input type="password" name="password1" id="password1" value="" placeholder="<?=$LANG["password"]?>" required data-theme="a">
    <input type="password" name="password2" id="password2" value="" placeholder="<?=$LANG["verify"]." ".$LANG["password"]?>" required data-theme="a">
     
     <label for="promo"><?=$LANG["sponsor"]?>:</label>
     <input type="number" name="promo" id="promo" value="<?php if(isset($_SESSION['promo'])){echo $_SESSION['promo'];} ?>" placeholder="<?=$LANG["sponsor.code"]?>" data-theme="a" <?php if(isset($_SESSION['promo'])){echo 'readonly';} ?>>

	<button type="submit" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-btn-icon-left ui-icon-check"><?=$LANG["next"]?></button>
    </form>
</div>

<div id="registro2" style="display:none; padding:8px 8px;text-align:left;">

	<form id="reg_form2" name="reg_form2" method="post" enctype="multipart/form-data" onSubmit="validateStep(2);return false;">
         <label for="username"><?=$LANG["name"]?>:</label>
        <input type="text" name="new_username" id="new_username" value="" placeholder="<?=$LANG["name"]?>" required data-theme="a">
        <label for="userlastname"><?=$LANG["name.last"]?>:</label>
        <input type="text" name="userlastname" id="userlastname" value="" placeholder="<?=$LANG["name.last"]?>" required data-theme="a">

		<label for="bday"><?=$LANG["bday"]?>:</label>
        <input type="date" name="bday" id="bday" value="" required data-theme="a">

    <select id="cboPais" name="cboPais" required>
        <option value=""><?=$LANG["country"]?></option>
        <?php
        if($stmt = $mysqli->prepare("SELECT  `id_country`, `ds_country` FROM `gms_country` ORDER BY `id_country` ASC ;")) { 
            if($stmt->execute()){ 
                if($stmt->bind_result($id, $desc)){  
                    while($stmt->fetch()){
                        $Selected= '';
                        if(isset($IDCOUNTRY)){if($id==$IDCOUNTRY){$Selected = 'selected';}}										
                        echo '<option value="'.$id.'" '.$Selected.'>'.$desc.'</option>';
                    }
                }
            }$stmt->close();
        }
        ?>
</select>    
    <select id="cboMnda" name="cboMnda" required disabled>
      <option value=""><?=$LANG["money"]?></option>
            <?php
            $IDCURRENCY = 1;// TODO: 
		
			if($stmt = $mysqli->prepare("SELECT `id_currency`, `ds_currency` FROM `gms_currency` WHERE `cd_currency` = 'DOP' ORDER BY `ds_currency` ASC  ")) { 
				if($stmt->execute()){ 
					if($stmt->bind_result($id, $desc)){  
						while($stmt->fetch()){
							$Selected= '';
							if(isset($IDCURRENCY)){if($id==$IDCURRENCY){$Selected = 'selected';}}										
							echo '<option value="'.$id.'" '.$Selected.'>'.$desc.'</option>';
						}
					}
				}$stmt->close();
			}
            ?>
    </select>
    <label for="user"><?=$LANG["phone"]?>:</label>
        <input type="number" name="phone" id="phone" value="" placeholder="<?=$LANG["phone"]?>" required data-theme="a" maxlength="11">

	<button id="btn_regist" type="submit" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-btn-icon-left ui-icon-check"><?=$LANG["signup"]?></button>
    </form>
</div>

<div id="welcome" style="display:none">
	<h3 style="text-align:center"><?=$LANG["welcome"]?></h3>
    <br>
  <p><?=$LANG["account.regist"]?></p>
  <p><?=$LANG["account.regist.info"]?><br>
   <table id="code_form" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td width="50%"><input id="code" name="code" type="text" maxlength="8"></td>
      <td><button onClick="regist_validate();"><?=$LANG["verify"]?></button></td>
    </tr>
  </tbody>
</table>
 <br></p>
    <p id="resend_form">
    <?=$LANG["account.regist.resend"]?><button onClick="regist_resend();" style="width:160px; display:inline-block"><?=$LANG["send"]?></button>
    </p>
</div>
</div>
<script src="./js/raphael-min.js"></script>
<script src="./js/jquery.progressStep.js"></script>
