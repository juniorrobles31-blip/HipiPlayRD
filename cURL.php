<?php
//-----------------------------------------------------
// cURL
// info		:	Conexion remota
// version 	:	1.1
// date		:	24-11-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
if (class_exists('cURL')) {
	return;
}
class cURL {
	public $url;
	public $method;
	public $data;
	public $useragent;
	public $username;
	public $password;
	public $response;
	private $headers;
	public $debug;
	
	public function __construct ($url = NULL, $data = NULL, $method = 'GET'){
		$this->url				= $url;
		$this->method			= $method;
		$this->data				= $data;
		$this->useragent        = NULL;
		$this->username			= NULL;
		$this->password			= NULL;
		$this->response         = NULL;
		$this->headers			= array('Accept: application/json');
		//array('Content-Type: application/json','Accept: application/json');
		//$this->data	 = array('first_name' => 'John');
		$this->debug 			= false;
	}
	
	private function headerValue($name){
		for($i=0; $i < count($this->headers); $i++){
			$e = explode(":",$this->headers[$i]);
			if ($e[0] == $name){
				return 	trim($e[1]);
			}
		}
		return NULL;
	}
	
	private function headerIndex($name){
		for($i=0; $i < count($this->headers); $i++){
			$e = explode(":",$this->headers[$i]);
			if ($e[0] == $name){
				return 	$i;
			}
		}
		return NULL;
	}
	
	public function addHeader($key,$value){
		$i = $this->headerIndex($key);
		if ($i == NULL){
			$this->headers[] = $key +":"+$value;
		}else{
			$this->headers[$i] = $key +":"+$value;
		}
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
		
		if ($this->useragent!== NULL ){
			curl_setopt($curl, CURLOPT_USERAGENT,$this->useragent);
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
		
		if ($this->startsWith($result,"{")){
			$this->response = json_decode($result,true);
		}elseif ($this->startsWith($result,"[")){
			// remove the start "["
			$r = substr($result,1,strlen($result)-1);
			// remove the finish "]"
			$r = substr($r,0,strlen($r)-1);
			
			$this->response = json_decode($r,true);
		}elseif ($this->startsWith($result,"<")){
			$this->response["body"] = $result;
		}else{
			$this->response["body"] = "PLAIN DATA: ". $result;
		}
		
		$this->response['HTTP']   = $httpCode;
		
		if (!isset($this->response['STATUS'])){
			$this->response['STATUS'] = "ERROR";
		}		
		return $this->response;
	}	
	
	private function startsWith($haystack, $needle) {
		return substr($haystack, 0, strlen($needle)) === $needle;
	}
}
?>