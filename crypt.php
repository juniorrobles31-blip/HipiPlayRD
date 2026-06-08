<?php
//-----------------------------------------------------
// CRYPT
// info		:	Encriptacion
// version 	:	1.0
// date		:	24-11-2016
// autor	: 	Engelbert Pena
//-----------------------------------------------------
if (class_exists('CRYPT')) {
	return;
}
class CRYPT{
	/**
	* @info Clase para encriptar/desencriptar los datos.
	* @code <?php require_once("crypt.php");;; $crypt = new CRYPT();;; $json = '{"say":"Hello World"}';; $key  = rand(0, 19);;; $encrypt = $crypt->encrypt($json, $key);; $decrypt = $crypt->decrypt($encrypt, $key);;; echo "string : ".$json.'<br>';; echo "encrypt: ".$encrypt.'<br>';; echo "decrypt: ".$decrypt;; ?>	
	* @code json.str: {"say":"Hello World"};;encrypt : 8Eh9ILwBiWYIw/9IW8tI3UA7HuvpkH2aOSx0jDN2An8=;;decrypt : {"say":"Hello World"}
	*/
	private $method = 'aes-256-cbc';//"aes256";
	private $pass   = array('9j8$9RNC', 'K@Jg4(9F', 'oHI%QCS8', 'O9Y*3nF', '*&^FVz0O', 
						    '&c9HDPZ-', '=_cuYF:p', '**cHQ)^7', '&&CFQlc', '*TNC88n3',
						    '&R$)@CN1', '&TCNA0rq', '($Rvhq77', '*48Jg1,', '*88!ncjK',
						    '*vo1HF1p', '&viKYAAC', 'al*&vHAM', ')*CVNq1', '|vKAMR92'
						   );
	private $master_key;
	
	public function __construct(){
		$this->master_key = $this->get_master_key();
	}
	
	public function get_master_key(){
		/**
		* @info Genera la clave maestra de encryptacion (initial vector)
		* @return [string] - clave
		*/
		//return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));	
		return date("dm.Y.MD");// TODO : sincronizar el timezone del servidor con los 3erParty
		// D	Una representación textual de un día, tres letras	Mon hasta Sun
		// M	Una representación textual corta de un mes, tres letras	Jan hasta Dec
		// Y	Una representación numérica completa de un año, 4 dígitos	Ejemplos: 1999 o 2003
		// m	Representación numérica de una mes, con ceros iniciales	01 hasta 12	
		// d	Día del mes, 2 dígitos con ceros iniciales	01 a 31	
		$mkey = array(date("Y"),date("M"),date("m"),date("d"),date("D")); 
		return "@k".$mkey[0].$mkey[1].$mkey[2].($mkey[3]+1).$mkey[4];
	}

	public function encrypt($input, $key){
		/**
		* @info Encriptacion de datos
		* @param input [string] - json en formato string
		* @param key [int] - el indice de la clave de encryptacion (0 al 19)
		* @return [string] - datos encriptados
		*/
		if ($key == -1){ return $input; }// TODO : remover bypass para ignorar la encriptacion para produccion
		$data = openssl_encrypt ((string)$input, $this->method, ($this->pass[(int)$key]), OPENSSL_RAW_DATA, $this->master_key);
		return base64_encode($data);
	}

	public function decrypt($input, $key){
		/**
		* @info Desencriptacion de datos
		* @param input [string] - datos encriptados
		* @param key [int] - el indice de la clave de encryptacion (0 al 19)
		* @return [string] - datos desencriptados
		*/
		if ($key == -1){ return $input; }// TODO : remover bypass para ignorar la encriptacion para produccion
		$data = openssl_decrypt(base64_decode($input), $this->method, ($this->pass[(int)$key]), OPENSSL_RAW_DATA, $this->master_key);
		return	$data;
	}
}
?>