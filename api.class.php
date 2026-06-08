<?php
/************************************************
class.transaction.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
if (!defined('ROOT')){
	define('ROOT','./include/');
}

abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
   // protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
    // protected $file = Null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */

	 /**
     * All internal varibles
     */
	 protected $idApiuser;

	 private  $code;

	private   $json = array();

    public function __construct($request) {

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json; charset=UTF-8");
		header("Cache-Control: no-cache, must-revalidate");

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

		if ($protocol !== "https://"){
			//$this->send("ErrorCode.".__LINE__.": SSL Required ");
		}

			//Security for crpyt
		/*if ($this->Crypt != NULL){
			if (!isset($_SERVER['HTTP_SHA'])){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Undefined Hacth");
			}
			$this->key = $_SERVER['HTTP_KEY'];//$_SESSION["id"];//
			$sha = $_SERVER['HTTP_SHA'];

			$data = file_get_contents('php://input');
			$dec  = $this->Crypt->decrypt($data, $this->key);
			if ($dec == NULL){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Data");
			}
			if (sha1($dec) != $sha){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Integrity");
			}
			//$this->send("ErrorCode.".__LINE__.": ".$dec);
			$_POST = json_decode($dec,true);
			unset($data);
			unset($dec);
		}else{
			$data = file_get_contents('php://input');
			if ($data == NULL){
				$this->send(CODES::Bad_Request);//"ErrorCode.".__LINE__.": Invalid Data");
			}
			$_POST = json_decode($data,true);
			unset($data);
		}*/

		//print_r($request);

        $this->args = explode('/', rtrim($request, '/'));

        $this->endpoint = array_shift($this->args);
		//  if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
		//  $this->verb = array_shift($this->args);
		//	echo "| verb: ".$this->verb;
		// }

		require_once(ROOT."class/codes.php");
		$this->code = new CODE();

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
				$this->json['code']=$this->code->NOT_ACCEPTABLE["code"];
				$this->_response($this->json,$this->json);
            }
        }

        switch($this->method) {
        //case 'DELETE':
        case 'POST':
           $this->request = $this->_cleanInputs($_POST);
           break;
        //case 'GET':
         //  $this->request = $this->_cleanInputs($_GET);
       //     break;
         //case 'PUT':
        //$this->request = $this->_cleanInputs($_GET);
        //$this->file = file_get_contents("php://input");
       //break;
        default:
			$this->json['code']=$this->code->METHOD_NOT_ALLOWED["code"];
			$this->_response($this->json,$this->json);
        	break;
        }


 }

   protected  function login($origin) {
	   	require_once(ROOT.'class/userservice.php');
		if($this->endpoint=="login"){
			if(!isset($_POST['apiuser'])||empty($_POST['apiuser'])||
			   !isset($_POST['apipass'])||empty($_POST['apipass'])){
				 $this->json['code']=$this->code->ERROR_EMPTY["code"];
				 $this->_response($this->json,404);
			}
			try{
					$exec = new USERSERVICE();

					$json['apikey'] = $exec->loginUserApi($_POST['apiuser'],$_POST['apipass'],$origin);
					$this->_response($json);
				}catch(Exception $e){
				 	$this->json['code']=$e->getMessage();
				 	$this->_response($this->json,400);
			}
		}else{
			if(!isset($_POST['apikey'])||empty($_POST['apikey']) ){
				 $this->json['code']=$this->code->ERROR_EMPTY["code"];
				 $this->_response($this->json,404);
			}
			try{
				$exec = new USERSERVICE();
				$this->idApiuser = $exec->loginUserApiKey($_POST["apikey"],$origin);
			}catch(Exception $e){
				$this->json['code']=$e->getMessage();
				$this->_response($this->json,400);
			}

		}
   }

   public function recharge(){
		if( !isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['amount'])||empty($_POST['amount'])){
			 $this->json['code']=$this->code->ERROR_EMPTY["code"];
			 $this->_response($this->json,404);
		}

		require_once(ROOT.'class/common.php');
		try{
			$exec    = new DISPLAY();
			$data    = $exec->getIdUser($_POST['user']);
			$id_user = $data->id_user;
		}catch(Exception $e){
			$this->json['code']=$e->getMessage();
			$this->_response($this->json,400);
		}

		require_once(ROOT.'class/transaction.php');
		try{
			$exec = new TRANSACTION();
			$data =$exec->recharge($this->idApiuser,$id_user,$_POST['amount']);
			$json = array();
			$json["transaction"] = $data->transaction;
			$json["balance"] 	 = $data->balance;
			$this->_response($json);
		}catch(Exception $e){
			$this->json['code']=$e->getMessage();
			$this->_response($this->json,400);
		}
   }

	 public function SearchWithdraw(){
		 if(!isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['code'])||empty($_POST['code'])){
				$this->json['code']=$this->code->ERROR_EMPTY["code"];
				$this->_response($this->json,404);
		 }
		 require_once(ROOT.'class/withdraw.php');
		 try{
			 $exec    = new WITHDRAW();
			 $data    = $exec->SearchWithdraw($_POST['user'],$_POST['code']);
		 }catch(Exception $e){
			 $this->json['code']=$e->getMessage();
			 $this->_response($this->json,400);
		 }
	 }
	public function PayWithdraw(){
		if(!isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['code'])||empty($_POST['code'])){
			 $this->json['code']=$this->code->ERROR_EMPTY["code"];
			 $this->_response($this->json,404);
		}
		require_once(ROOT.'class/withdraw.php');
		try{
			$exec    = new WITHDRAW();
			$data    = $exec->PayWithdraw($this->idApiuser,$_POST['user'],$_POST['code']);
		}catch(Exception $e){
			$this->json['code']=$e->getMessage();
			$this->_response($this->json,400);
		}
	}

	public function getEndpoint() {return $this->endpoint;}

	private function _response($data,$status=200) {
		/**
		* @info envia los datos al cliente y termina la ejecucion del script con un die
		* @param data[array / string] datos a enviar o un string cuando hay un error
		* @code <?php require_once("./security.php");; $Security = new SECURITY();; $data = new array("hello"=>"world");; $Security->send($data);;?>
		*/
       header("HTTP/1.1 " . $status);
        //return json_encode($data);
		die(json_encode($data,JSON_NUMERIC_CHECK));
    }


	 private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
}
?>
