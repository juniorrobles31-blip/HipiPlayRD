<?php
/************************************************
class.userservice.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
//require_once('./include/class/class.system.php');
if (!defined('ROOT')){
	define('ROOT','./include/');
}

class USERSERVICE {
	private  $db;
	private  $code;
	private  $type;
	private  $tblUser;

	public function __construct() {
		require_once(ROOT."class/dbconfig.php");
		$this->db = new DB();
		$this->tblUser = "`gms_user`";
		//To return codes
		require_once(ROOT."class/codes.php");
		$this->code = new CODE();
		if (defined('API-JDD')){$this->type = "code";}else{$this->type="info";}

	}

	public function loginUserApi($user,$pass,$origin){
		if(empty($user)||empty($pass)||empty($origin)){
			throw new Exception($this->code->ERROR_EMPTY[$this->type]);
		}

		require_once(ROOT."class/cURL.php");

		$data = array();
		$data['method'] = "Apilogin";
		$data['apiuser']= $user;
		$data['apipass']= $pass; //var_dump($data);

		$curl = new cURL('https://www.zuzuvama.com/api/signin.php',$data,'POST');
		//$curl = new cURL('http://localhost/zuzuvama/api/signin.php',$data,'POST');
		$curl->debug=false;
		$json = $curl->execute();

		if ($json["STATUS"] != "OK"){
		//die(json_encode($json));
			throw new Exception($this->code->ERROR_USER_NOT_FOUND[$this->type]);
		}

		$idZZVM = $json["iduser"];
		//$idZZVM = 4;

		$data = $this->db->select('SELECT `id_user`, DATE_ADD(NOW(), INTERVAL 15 MINUTE) next_time  FROM `gms_user` WHERE `id_zzvm` = ?;', array($idZZVM),array('%i'));
		if ($data['count']!=1){
			throw new Exception($this->code->ERROR_USER_NOT_FOUND[$this->type]);
		}
		$id_user=$data[0]->id_user;
		$next_time=$data[0]->next_time;

		$data = $this->db->select('SELECT `id_user`, `expiration_date` FROM `gms_login` WHERE `id_user` = ?;', array($id_user),array('%i'));

		$token = $this->_genToken();

		if ($data['count']==0){
			$id = $this->db->insert("gms_login",array("`id_user`" => $id_user,"`token`" => $token, "`attempts`"=>"1", "`expiration_date`"=>$next_time,"`origin`" => $origin), array('%i','%s','%i','%s','%s'));

			if(empty($id)){ throw new Exception($this->code->ERROR_DB[$this->type]);}

		}else{
			/*if($data[0]->expiration_date > $data[0]->actual_date){
			throw new Exception( "Login no puede ser generado");
			}*/
			$id = $this->db->update("gms_login",array("`token`"=>$token,"`expiration_date`"=>$next_time,"`origin`" => $origin), array('%s','%s','%s'),array("`id_user`"=>$id_user), array('%i'));
		}
		return $token;
	}

	public function loginUserApiKey($apikey,$origin){
		$data = $this->db->select('SELECT `id_user`, `expiration_date`, NOW() as actual_date, DATE_ADD(NOW(), INTERVAL 15 MINUTE) next_time FROM `gms_login` WHERE `token` = ? AND `origin` = ?;', array($apikey,$origin),array('%s','%s'));
		if ($data['count']!=1){ throw new Exception($this->code->UNAUTHORIZED[$this->type]); }

		if($data[0]->expiration_date < $data[0]->actual_date){
			throw new Exception($this->code->ERROR_SESSION_EXPIRE[$this->type]);
		}
		$next_time = $data[0]->next_time;
		$id_user = $data[0]->id_user;

		$id = $this->db->update("gms_login",array("`expiration_date`"=>$next_time), array('%s'),array("`id_user`"=>$id_user), array('%s'));

		return $id_user;
	}

	private function _genToken(){
		$cstrong = true;
		return bin2hex(openssl_random_pseudo_bytes(16,$cstrong));
		//echo '<br>base64_encode '.$token = base64_encode($token);
		//echo '<br>base64_decode '.$secretKey = base64_decode($token);
	}

	public function loginUser($user,$alias) {
		if(empty($user)||empty($alias)){
			throw new Exception($this->code->ERROR_EMPTY[$this->type]);
		}
		$data = $this->db->select('SELECT `id_user`,`sponsor` FROM '. $this->tblUser .' WHERE `id_zzvm` = ?;', array($user),array('%i'));

		// si no existe lo registra
		if ($data['count']==0){
			$id = $this->db->insert($this->tblUser,array("`id_zzvm`" => $user,"`alias`" => $alias, "`entry_date`"=>"NOW()"), array('%i','%s','%s'));
			$sponsor  = NULL;
			if(empty($id)){
				throw new Exception($this->code->ERROR_DB[$this->type]);
			}
		}else{
			// devuelve el registro
			$id = $data[0]->id_user;
			$sponsor = $data[0]->sponsor;
			$this->db->update($this->tblUser,array("`alias`" => $alias), array('%s'), array("`id_user`" => $id), array('%i'));
		}
		require_once(ROOT."class/transaction.php");
		$exe = new TRANSACTION();

		//Esto hay que colocarlo donde va el try catch
		sec_session_start();
		global $timeout;
		$_SESSION['id']      = $id;
		$_SESSION['usuario'] = $alias;
		$_SESSION['sponsor'] = $sponsor;
		$_SESSION['start']   = time(); // taking now logged in time
		$_SESSION['expire']  = $_SESSION['start'] + ($timeout) ; // ending a session in 15 minutes from the starting time
		$_SESSION['balance'] = $exe->getBalance($id);

		return $id;
	}

	public function updateSponsor($id, $sponsor){
		$this->db->update($this->tblUser, array("`sponsor`" => $sponsor), array('%i'), array("`id_user`" => $id), array('%i'));
		$_SESSION['sponsor'] = $sponsor;
	}

	public function logout(){
		// Unset all session values
		$_SESSION = array();
		// get session parameters
		$params = session_get_cookie_params();
		// Delete the actual cookie.
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		// Destroy session
		session_unset();
		session_destroy();
		return true;
   }

}
?>
