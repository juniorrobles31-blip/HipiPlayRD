<?php
if (!defined('ROOT')){
	define('ROOT','./include/');
}

require_once(ROOT."class/common.php");

class WITHDRAW extends DISPLAY {

	private  $db 		= null;
	private  $sql 		= null;
	private  $code;
	private  $trans;

	public function __construct() {
		parent::__construct();//Load common
		require_once( ROOT.'class/dbconfig.php');
		$this->db = new DB();
		//To return codes
		require_once(ROOT."class/codes.php");
		$this->code = new CODE();
		if (defined('API-JDD')){$this->type = "code";}else{$this->type="info";}


	}

	public function WithdrawAmount($user,$idMethod,$amount){
		$TimeOfServer = date("Y-m-d H:i:s",server_time());

		if(empty($user)||empty($idMethod)||empty($amount)){
			 throw new Exception($this->code->ERROR_EMPTY[$this->type]);
		}

		if(!is_numeric($idMethod)||!is_numeric($amount)){
			 throw new Exception($this->code->ERROR_ONLY_NUMBER[$this->type]);
		}
		$data = parent::_searchIdUser($user);
		$idUser =$data->id_user;

		require_once(ROOT."class/transaction.php");
		$this->trans = new TRANSACTION();
		$balance =  $this->trans->getBalance($idUser);
		if(isset($amount) && $balance<$amount){
			throw new Exception($this->code->ERROR_BALANCE[$this->type]);
		}

		switch($idMethod){
			//-------------------------------------
			case 22://Retiro en establecimiento
			//-------------------------------------
			$dsCodeUnique = genCodeUnique();
			$id = $this->db->insert('tbl_withdraw', array("`id_user`" => $idUser, "`id_method`" => $idMethod, "`cd_withdraw`" => $dsCodeUnique, "`amount`" => $amount, "`date`" => $TimeOfServer),array('%i','%i','%s','%s','%s'));
			if(empty($id)){throw new Exception($this->code->ERROR_DB[$this->type]);}
			break;
			//-------------------------------------
			default:  //Si no encuentra el tipo de retiro
			//-------------------------------------
			 throw new Exception($this->code->ERROR_NOT_FRM_PAY[$this->type]);
			break;
		}
		return $dsCodeUnique;
	}
	//------------------------------------------------
	// FuncTion para realizar el retiro en establecimiento (Busca el monto que debe de pagar)
	//------------------------------------------------
	public function SearchWithdraw($user,$code){
			if(empty($user)||empty($code)){
			 throw new Exception($this->code->ERROR_EMPTY[$this->type]);
			}
			//search id_user
			$data = parent::_searchIdUser($user);
			$idUser =$data->id_user;

			require_once(ROOT."class/transaction.php");
			$this->trans = new TRANSACTION();
			$balance =  $this->trans->getBalance($idUser);
			if(isset($amount) && $balance<$amount){
				throw new Exception($this->code->ERROR_BALANCE[$this->type]);
			}

			$data = $this->db->select('SELECT `id_withdraw`,`status`,`amount` FROM `tbl_withdraw` WHERE `id_user`=? AND `cd_withdraw`=?;',array($idUser,$code),array('%i','%s'));
			if($data['count']!=1){throw new Exception($this->code->ERROR_WITHDRAWAL_NOT_FOUND[$this->type]);}
			switch($data[0]->status){
				case 'B':
					throw new Exception($this->code->ERROR_WITHDRAWAL_BLOCKED[$this->type]);
				break;
				case 'P':
					throw new Exception($this->code->ERROR_WITHDRAWAL_PAYED[$this->type]);
				break;
			}
			return $data[0]->amount;
	}
	//------------------------------------------------
	// Funcion para realizar el retiro en establecimiento
	//------------------------------------------------
	function PayWithdraw($idUserReg,$user,$code){
			if(empty($idUserReg)||empty($idUserTrans)||empty($user)||empty($code)){
			 	 throw new Exception($this->code->ERROR_EMPTY[$this->type]);
			}
			////-----------------------------------------------------
			////  CONTROL DE ACCESO A ESTE MODULO
			////-----------------------------------------------------
			require_once(ROOT."class/cURL.php");
			$data = array();
			$data['method'] = "login";
			$data['apiuser']= $user;
			$data['apipass']= $pass; //var_dump($data);
			$data['user']=$user;

			$curl = new cURL('https://www.zuzuvama.com/api/signin.php',$data,'POST');
			//$curl = new cURL('http://localhost/zuzuvama/api/signin.php',$data,'POST');
			$curl->debug=false;
			$json = $curl->execute();

			if ($json["STATUS"] != "OK"){
			//die(json_encode($json));
				throw new Exception($this->code->ERROR_USER_NOT_FOUND[$this->type]);
			}
			$role = $json["role"];
			if($role<>'POS'){
				throw new Exception($this->code->ERROR_NOT_ACCESS_FUNCTIONS[$this->type]);
			}
			//search id_user
			$data 	= parent::_searchIdUser($user);
			$idUser = $data->id_user;

			require_once(ROOT."class/transaction.php");
			$this->trans = new TRANSACTION();
			$idTpTrans = isset($idTpTrans) ? $idTpTrans : 0;
			$this->trans->doWithdraw($idUserReg,$idTpTrans,$idUser,$code);

			return true;
	}

}
?>
