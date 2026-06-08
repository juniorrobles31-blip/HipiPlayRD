<?php
/************************************************
class.system.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
if (!defined('SYSTEM')){
	define('SYSTEM',1);

	require_once('./include/class/class.dbconfig.php');

//-----------------------------------------------------
// toJson
//-----------------------------------------------------
function toJson($array){
	return (str_replace('\\','',json_encode($array,JSON_NUMERIC_CHECK)));	
}
function json_die($json){
	die(toJson($json));
}
//-----------------------------------------------------
// startsWith(string, search)
//-----------------------------------------------------
function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}
//-----------------------------------------------------
// endswith(string, search)
//-----------------------------------------------------
function endsWith($haystack, $needle){
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
//-----------------------------------------------------
// isDefined($key, $method="GET", $empty=false)
//-----------------------------------------------------
function isDefined($key, $method="GET", $empty=false){
	if ($key == 'user' && isset($_SESSION["id"])){
		//$_GET[$key] = $_SESSION["id"];
		return true;
	}
	if ($method == "GET"){
		if (isset($_GET[$key])){
			$_GET[$key] = str_replace('"',"",$_GET[$key]);// eliminar comillas
			$_GET[$key] = str_replace("'","",$_GET[$key]);// eliminar comillas
			$_GET[$key] = str_replace('\\',"-",$_GET[$key]);// remplazar barras
			$_GET[$key] = str_replace('/',"-",$_GET[$key]);// remplazar barras
			
			if(($empty==true) && empty($_GET[$key])){
				die(toJson(array("STATUS"=>"ERROR", "INFO"=> __LINE__ .": KEY [$key] is empty")));
				return false;
			}		
			return true;	
		}else{
			die(toJson(array("STATUS"=>"ERROR", "INFO"=> __LINE__ .": KEY [$key] not defined")));
			return false;	
		}
	}elseif ($method == "POST"){
		if (isset($_POST[$key])){
			//$_POST[$key] = str_replace('"',"",$_POST[$key]);// eliminar comillas
			//$_POST[$key] = str_replace("'","",$_POST[$key]);// eliminar comillas
			//$_POST[$key] = str_replace('\\',"-",$_POST[$key]);// remplazar barras
			//$_POST[$key] = str_replace('/',"-",$_POST[$key]);// remplazar barras
			
			if(($empty==true) && empty($_POST[$key])){
				die(toJson(array("STATUS"=>"ERROR", "INFO"=> __LINE__ .": KEY [$key] is empty")));
				return false;
			}		
			return true;	
		}else{
			die(toJson(array("STATUS"=>"ERROR", "INFO"=> __LINE__ .": KEY [$key] not defined")));
			return false;	
		}
	}
}
//-----------------------------------------------------
// get($post, $method="POST", $default = NULL)
//-----------------------------------------------------
function get($post, $method="POST", $default = NULL){
	switch($method){
	case "GET":
		if (isset($_GET[$post])){
			return $_GET[$post];
		}
	break;
	case "POST":
		if (isset($_POST[$post])){
			return $_POST[$post];
		}
	break;
	}
	return $default;
}
//-----------------------------------------------------
// cURL
//-----------------------------------------------------
class cURL {
	public $url;
	public $method;
	public $data;
	public $username;
	public $password;
	public $response;
	public $headers;
	public $debug;
	
	public function __construct ($url = NULL, $data = NULL, $method = 'GET'){
		$this->url				= $url;
		$this->method			= $method;
		$this->data				= $data;
		$this->username			= NULL;
		$this->password			= NULL;
		$this->response         = NULL;
		$this->headers			= array('Accept: application/json');
		//array('Content-Type: application/json','Accept: application/json');
		//$this->data	 = array('first_name' => 'John');
		$this->debug 			= false;
	}
	
	public function headerValue($name){
		for($i=0; $i < count($this->headers); $i++){
			$e = explode(":",$this->headers[$i]);
			if ($e[0] == $name){
				return 	trim($e[1]);
			}
		}
		return NULL;
	}
	
	public function headerIndex($name){
		for($i=0; $i < count($this->headers); $i++){
			$e = explode(":",$this->headers[$i]);
			if ($e[0] == $name){
				return 	$i;
			}
		}
		return NULL;
	}
	
	public function execute(){
		$curl = curl_init();
		$url  = $this->url;
		switch($this->method){
		case "GET":
			if ($this->data == NULL){
				$data = "";	
			}else{
				$data = http_build_query($this->data, '', '&');
			}
			$url .= "?".$data;
		break;
		case "POST":
			if (!is_array($this->data)){
				throw new InvalidArgumentException('Invalid data input for postBody. Array expected');
			}
			if ($this->headerValue("Content-Type") == "application/json"){
				$data = toJson($this->data);
			}else{
				$data = http_build_query($this->data, '', '&');
			}
			
			curl_setopt($curl, CURLOPT_POST,true);
			curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
		break;
		case "PUT":
			if (!is_array($this->data)){
				throw new InvalidArgumentException('Invalid data input for postBody. Array expected');
			}
			
			if ($this->headerValue("Content-Type") == "application/json"){
				$data = toJson($this->data);
			}else{
				$data = http_build_query($this->data, '', '&');	
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
			
			$h = $this->headerIndex("Content-Length");
			if ($h == NULL){
				$this->headers[]   = "Content-Length:".strlen($data);	
			}else{
				$this->headers[$h] =  "Content-Length:".strlen($data);	
			}
		break;
		case "DELETE":
			if ($this->headerValue("Content-Type") == "application/json"){
				$data = toJson($this->data);
			}else{
				$data = http_build_query($this->data, '', '&');	
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
		break;
		default:
			throw new InvalidArgumentException('Invalid REST method ['.$this->method.']');				
		break;
		}		
		
		if ($this->username !== NULL && $this->password !== NULL){
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT,30);		
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_HEADER, false);
		
		if ($this->debug){
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			
			echo "<br>URL<br>";
			var_dump($url);
			echo "<br>METHOD<br>";
			var_dump($this->method);
			echo "<br>DATA<br>";
			var_dump($data);
			echo "<br>HEADER<br>";
			var_dump($this->headers);
		}
		
		// set URL and other appropriate options	
		$result   = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if ($this->debug){
			echo "<br>RESULT<br>";
			var_dump($result);
			echo "<br>CURL INFO<br>";
			var_dump(curl_getinfo($curl));
			echo "<br>=============";
		}
		
		curl_close($curl);
		
		if (isset($file)){
			fclose($file);	
		}
		
		$this->response = array();
		if ($result === false){
			$this->response['STATUS'] = "ERROR";
			$this->response['INFO']   = "ERROR de conexion http:[".$httpCode."]";
			$this->response['HTTP'] = $httpCode;
			return $this->response;
		}
		//echo "TYPE: ".gettype ($result) .'<br>'. $result;
		
		if (startsWith($result,"{")){
			$this->response = json_decode($result,true);
		}elseif (startsWith($result,"[")){
			// remove the start "["
			$r = substr($result,1,strlen($result)-1);
			// remove the finish "]"
			$r = substr($r,0,strlen($r)-1);
			$this->response = json_decode($r,true);
		}elseif (startsWith($result,"<")){
			$this->response["body"] = $result;
		}else{
			$this->response["body"] = "PLAIN DATA<br>". $result;
		}
		$this->response['STATUS'] = "OK";
		$this->response['INFO']   = "Result OK";
		$this->response['HTTP']   = $httpCode;
		
		return $this->response;
	}	
}
}
?>