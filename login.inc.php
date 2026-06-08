<?php
if (isset($_POST["login"])){
	// salir si ya se inicio la sesion
	if(isset($_SESSION['id'])){
		redirigir("Location:?page=logout");
		exit;
	}
	
	// ZUzuvama.login
	require_once("include/class/cURL.php");
	
	global $zzvmUse,$zzvmPass;
	
	$data = array();
	$data['method'] = 'login';
	$data['apiuser']= $zzvmUser; 
	$data['apipass']= $zzvmPass; 
	$data['user']   = $_POST['user'];
	$data['pass']   = $_POST['pass'];
	
	$curl = new cURL('https://www.zuzuvama.com/api/signin.php',$data,'POST');
	//$curl->debug=true;
	$json = $curl->execute();
	//echo "DEBUG";
	//var_dump($json);
	if (isset($json["body"])){
		die($json["body"]);	
	}

	if ($json["STATUS"] == "OK"){
		//$user_browser = $_SERVER['HTTP_USER_AGENT'];
		$id    = $json['iduser'];
		$alias = preg_replace("/[^a-zA-Z0-9_\-]+/", "",$json['alias']);			
		$age   = age($json['bday'],false);
		
		if ($age < 18){
			redirigir("Location:?page=login&$error=".urlencode("Debes ser mayor de edad para ingresar en este portal"));
			exit;
		}
		// verificar si existe registro local
		require_once("include/class/userservice.php");
		$login   = new USERSERVICE();
		$user_id = $login->loginUser($id, $alias);
		
		if ($user_id > 0){
			redirigir("Location:index.php?page=game");
		}else{
			redirigir("Location:?page=login&$error=".urlencode('E.44: Error de logueo'));
		}
		exit;
			
	}else{
		redirigir("Location:?page=login&$error=".urlencode($json["INFO"]));
	}
	//redirigir("Location:?page=login&$error=".urlencode(json_encode($json)));
	die(json_encode($json));
	
	return;// salir del proceso
	
	
	//---------------------------
	// login fantasma
	//---------------------------
	/*
	$_SESSION['id'] = -1;
	$_SESSION['usuario'] = "MARCOS";
	$_SESSION['role'] 	 = 'USR';
	$_SESSION['start']   = time(); 
	$_SESSION['expire']  = $_SESSION['start'] + ($timeout) ; 	
	redirigir("Location:index.php?page=game");
	*/
	//---------------------------
	
	
	if (isset($CtrlPage)){
		if (isset($_POST["token"])){
			if(!isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['token'])||empty($_POST['token'])){
				redirigir("Location:?page=login&$error=$empty");
				exit;
			}
			$fdata    = zzvmWs('login.token',$_POST['user'],$_POST['token'],'','','','');
		}else{
			if(!isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['pass'])||empty($_POST['pass'])){
				redirigir("Location:?page=login&$error=$empty");
				exit;
			}
			$fdata    = zzvmWs('login',$_POST['user'],$_POST['pass'],'','','','');
		}
	
		$wsStatus = $fdata['status'];
		$wsMsg    = $fdata['msg']; 
		
		if($wsStatus == 1){	
			$wsRol   = $fdata['rol']; 
			$wsUser  = $fdata['user']; 
			$wsidUser = $fdata['iduser']; 
			if($wsRol<>'USR'){
				redirigir("Location:?page=login&$error=$noaccess");
				exit;
			}
	
			sec_session_start(); // custom php session.	
			$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.	
			$wsidUser = preg_replace("/[^0-9]+/", "", $wsidUser); // XSS protection as we might print this value
			$_SESSION['id'] = $wsidUser; 
			$wsUser = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $wsUser); // XSS protection as we might print this value
			$_SESSION['usuario'] = $wsUser;
			$_SESSION['role'] 	 = $wsRol;
			//$_SESSION[$slogin_string] = $password;
			$_SESSION['start']   = time(); // taking now logged in time
			/////* Session timeout	*/
			$_SESSION['expire']  = $_SESSION['start'] + ($timeout) ; // ending a session in 15 minutes from the starting time
			////Login successful.
			redirigir("Location:index.php?page=game");
			exit;
		
		}elseif ($wsStatus == 2){ 
			if ($wsMsg != $goodws){
				echo '<span style="color:#FF0000">'.$wsMsg.'</span>';
			}
			?>
			<form method="post" enctype="multipart/form-data" target="_self" >
            	<input type="hidden" name="login" id="login" value="true"/>
                <input type="hidden" name="user" id="user" value="<?=$fdata['iduser'];?>"/>
                <ul data-role="listview" style="padding:1em">
                  <li>
                 	<label for="token"><?=$LANG["token"]?>:</label>
                  <input type="text" name="token" id="token" value="" placeholder="<?=$LANG["token"]?>" required data-theme="a" maxlength="8">
                  <button type="submit" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-btn-icon-left ui-icon-check"><?=$LANG["verify"]?></button>
              </li>
            </ul>
          </form>
          <script>
		  	$(function() {
		   		$("#panel_user").panel("open");
			});
		  </script>
		<?php
		}else{
			redirigir("Location:?page=login&$error=$wsMsg");//redirigir("Location:?page=login&$lusername=$username&$getError=$userMsg");
			exit;	
		}
	
	} 
}else{ ?>

<form method="post" enctype="multipart/form-data" target="_self" >
	<input type="hidden" name="login" id="login" value="true"/>
  <ul data-role="listview" style="padding:1em">
	<li> <?=$LANG["session.start"]?> </li>
	<li><label for="user"><?=$LANG["user"]?>:</label>
        <input type="text" name="user" id="user" value="" placeholder="<?=$LANG["user"]?>" required data-theme="a">
		<label for="pass"><?=$LANG["password"]?>:</label>
		<input type="password" name="pass" id="pass" value="" placeholder="<?=$LANG["password"]?>" required data-theme="a">

     	<!--
      	<button type="submit" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-btn-icon-left ui-icon-check"><?=$LANG["login"]?></button>-->
      	
      	<button class="zzvm" type="submit">
		<div>
			<img src="images/zuzuvama.png" height="36px">
		</div>
		<div>
			<?=$LANG["login.with"]?><br>Zuzuvama
		</div>
	</button>
    </li>
  </ul>
</form>
<?php } ?>