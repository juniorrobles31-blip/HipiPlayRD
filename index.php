<?php
/*****************************************************
// API.doc
// info		:	Auto.Documentacion
// version 	:	1.3
// date		:	27-11-2016
// autor	: 	Engelbert Pena
*****************************************************/
class DOC{
	//-----------------------------------------------------
	// startswith(string, search)
	//-----------------------------------------------------
	public static function startswith($haystack, $needle) {
		return substr($haystack, 0, strlen($needle)) === $needle;
	}
	public static function contains($haystack, $needle){
		return strpos($haystack, $needle) !== false;
	}
	
	//-----------------------------------------------------
	// scan_dir(string)
	//-----------------------------------------------------
	public function scan_dir($dir){
		$files  = scandir($dir);
		return $files;
	}
	
	public function scan($dir){
		$files = $this->scan_dir('.');
    	foreach ($files as $key => $value) {
			$value = str_replace(".php","",$value);
			if ($value == "." || $value == ".." 
			|| $value =="index" || $value =="_notes"){
				continue;	
			}?>
            <a href="?page=<?=$value;?>" class="link"><?=strtoupper($value);?></a>
		<?php
		}
	}
	
	public function show($filename){
		if (!$this->contains($filename,".")){
			$filename = $filename.".php";
		}
		$file = explode(".",$filename);
		switch ($file[1]){
		case "php":
		
		break;
		default:
			echo "Unsuported file type [".$file[1]."]";
			return;
		break;
		}
		
		$elements = $this->loadElements($filename);
		foreach ($elements as $key => $value) {
			if ($value == "." || $value == ".." || $value == "index"){
				continue;	
			}
			?>
			<div class="element">
				<h3><?=$value["name"];?></h3>
				<em>Info</em>:<br><div class="tab"></div><?=$value["info"];?><br>
				<em>params</em>:<?php foreach ($value["param"] as $key => $value2) { echo '<br><div class="tab"></div>'.$value2; }?><br>
				<em>return</em>:<?php foreach ($value["return"] as $key => $value2) { echo '<br><div class="tab"></div>'.$value2; }?><br>
				<?php if (count($value["code"])>0){?>
					<em>code</em>:
					<?php foreach ($value["code"] as $key => $value2) { 
						$this->code($value2);
					}?><br>
				<?php } 
				if (count($value["TODO"])>0){?>
					<em>TODO</em>:<?php foreach ($value["TODO"] as $key => $value2) { echo '<br><div class="tab"></div><span style="color:red">'.$value2.'</span>'; }?><br>
				<?php } ?>
			</div>
			<?php
		}	
	}
	
	public function code($input){
		$code = highlight_string($input,true);
		$code = str_replace('?php','?php<br>',$code);
		$code = str_replace(';;;','<br>',$code);// salto de linea doble
		$code = str_replace(';;',';<br>',$code);// salto de linea
		echo $code;
	}
	//-----------------------------------------------------
	// newElement(string)
	//-----------------------------------------------------
	private function newElement($type){
		$element = array();	
		$element["name"] = "";
		$element["type"] = $type;
		$element["info"] = "";
		$element["code"] = array();
		$element["param"] = array();
		$element["return"] = array();
		$element["TODO"] = array();
		
		return $element;
	}
	//-----------------------------------------------------
	// loadElements(string)
	//-----------------------------------------------------
	private function loadElements($file_name){
		$ELEMENTS = array();
		$lines = file($file_name);
		
		$element = NULL;
		$found   = false;
		$pline   = "";
		
		foreach ($lines as $key => $value) {
			$line = trim($value);
			//echo "<b>{$key}</b> : " . htmlspecialchars($line) . "<br />\n";
			
			// general
			/*
			if ($this->startswith($pline,"/**")){
				if ($found){
					$ELEMENTS[] = $element;
				}
				$found = true;
				$element = $this->newElement("general");
				$element["name"] = trim(substr($line, strlen("* @name "), strlen($line)));
				$pline = "";
				continue;
			}*/
			// class
			if ($this->startswith($line,"class ")){
				$name = trim(substr($line, strlen("class "), strlen($line)),"{");
				if ($found){
					$ELEMENTS[] = $element;
				}
				$found = true;
				$element = $this->newElement("class");
				$element["name"] = $name;
				$pline = "";
				continue;
			}
			// function
			if ($this->startswith($line,"public function ")){
				$input = trim(substr($line, strlen("public function "), strlen($line)),"{");
				$explode = explode('(',$input);
				$name = $explode[0];
				if ($name !== '__construct'){// ignore default contructor
					if ($found){
						$ELEMENTS[] = $element;
					}
					$found = true;
					$element = $this->newElement("function");
					$element["name"] = $name;
					$pline = "";
					continue;					
				}
			}
			// case
			if (($this->startswith($line,"case ") && $this->startswith($pline,"//==")) || $key == count($lines)-1){
				if ($found){
					$ELEMENTS[] = $element;
				}
				$found = true;
				$element = $this->newElement("case");
				$element["name"] = trim(substr($line, strlen("case "), strlen($line)),'":');
				continue;
			}
			if ($found){
				if ($this->startswith($line,"* @info ")){
					$element["info"] = trim(substr($line, strlen("* @info "), strlen($line)));
					continue;
				}
				if ($this->startswith($line,"* @return ")){
					$element["return"][] = trim(substr($line, strlen("* @return "), strlen($line)));
					continue;
				}
				if ($this->startswith($line,"* @code ")){
					$element["code"][] = trim(substr($line, strlen("* @code "), strlen($line)));
					continue;
				}
				// TODO
				//if ($this->startswith($line,"// TODO : ")){
					//$element["TODO"][] = trim(substr($line, strlen("// TODO : "), strlen($line)));
					//continue;
				//}
				if ($this->contains($line,"// TODO : ")){
					$element["TODO"][] = trim(substr($line, strpos($line,"// TODO : ")+strlen("// TODO : "), strlen($line)));
					continue;
				}
				// params from tag
				if ($this->startswith($line,"* @param ")){
					$element["param"][] = trim(substr($line, strlen("* @param "), strlen($line)));
					continue;
				}
				// params from isDefined()
				if ($this->startswith($line,"isDefined")){
					$a = trim(substr($line, strlen("isDefined"), strlen($line)),'(");');
					$b = str_replace('",true' ,"", $a);
					// not override params tag
					$override = false;
					foreach ($element["param"] as $key => $value) {
						if ($this->startswith($value,$b)){
							$override = true;	
						}
					}
					if (!$override){
						if (strlen($a) > strlen($b)){
							$b .= " *";
						}
						$element["param"][] = $b;
					}
					unset($a);
					unset($b);
					continue;
				}
			}		
			$pline = $line;
		}
		return $ELEMENTS;
	}
}

?>
<style>
	body{
		margin: 0px 0px;
		padding: 0px 0px;	
	}
	
	em{
		color:#07BFED;	
	}
	
	code, .code{
		display:block;
		padding: 8px;
		background-color:#F9F9D7;	
		font-family: monospace;
		border-left: 6px solid #07BFED;
	}
	.code{
		border-left: 6px solid #ED0707;
	}
	
	.link{
		cursor:pointer;
		height: 28px;
		display: block;
		text-decoration:none;
		padding-left: 8px;
		color:#0744ED;
	}
	.link:hover{
		background-color:#A9F2FB;
		box-shadow: 2px 2px 4px #A9F2FB;
	}
	.menu{
		position:fixed;
		width: 10%;
		min-width: 140px;
		max-width: 140px;
		height:100%;
		box-shadow: 0px 0px 16px #000;
	}
	.menu > h2{
		text-align:center;	
	}
	.content{
		position:absolute;
		left:140px;	
		width:80%;
		display:inline-block;
		margin: 4px 48px;	
	}
	.tab{
		display:inline-block;
		margin-left: 2em;
	}
	a{
		color:#000;
		text-decoration:none;
	}
</style>

<div class="menu">
	<h2><a href="index.php">API</a></h2>
	<?php
		$DOC = new DOC();
		$DOC->scan('.');
    ?>
</div>
<div class="content">
	<?php

// echo "<br>user : [". $Crypt->encrypt("perezabreu", 1)."] >> ". $Crypt->decrypt($Crypt->encrypt("perezabreu", 1), 1);
// echo "<br>pass : ". $Crypt->encrypt("123", 1)." >> ". $Crypt->decrypt($Crypt->encrypt("123", 1), 1)."<br>";
/*
$imputText = "My text to encrypt.";
 $imputKey = "encryptor key";
 $blockSize = 256;
 $aes = new AES($imputText, $imputKey, $blockSize);

 $enc = $aes->encrypt();
 $aes->setData($enc);
 $dec=$aes->decrypt();
 echo "After encryption: ".$enc."<br/>";
 echo "After decryption: ".$dec."<br/>";

 require_once("crypt.php");
 $Crypt = new CRYPT();
 
 $json = '{"say":"Hello World"}';
 $key  = rand(0, 19);
 $encrypt = $Crypt->encrypt($json, $key);
 $decrypt = $Crypt->decrypt($encrypt, $key);
 
 echo "string : ".$json.'<br>';
 echo "encrypt: ".$encrypt.'<br>';
 echo "decrypt: ".$decrypt;	
 echo "<br>m.key: ".$Crypt->get_master_key();
*/ 	
if (isset($_GET["page"])){
	echo '<h2>'.strtoupper($_GET["page"]).'</h2>';
	
	$DOC->show($_GET["page"]);			
	
}else{?>
	<div class="element">
		<h3>API</h3>
		<em>URL</em>:<br><div class="tab"></div>
		https://www.juegosdeldinero.com/api/service<br>
		
		<em>HEADER</em>:<br>
		<div class="tab"></div>User-Agent: JD.service<br>
		<div class="tab"></div>Content-Type : application/json<br>
		<div class="tab"></div>key : [int]<br>
		<div class="tab"></div>sha : [string]<br>
        <br>
        <div class="tab"></div><strong>key </strong>es el indice de la clave de encriptacion que varia entre 0 y 19 (temporalmente se podra utilizar -1 para conexion sin encryptacion)<br>
        <div class="tab"></div><strong>sha </strong>es el sha de del mensaje desencryptado para comprobar la integridad de los datos<br>
					 
		<em>POST</em>:<br>
		<div class="tab"></div>method [string] - nombre del metodo "test"<br>
		<div class="tab"></div>apiuser [string] - nombre del usuario del API <br>
		<div class="tab"></div>apipass [string] - clave del usuario del API <br>
		
        <em>Code</em>:<br>
		<?php
echo $DOC->code('{
	"apiuser":"perezabreu",
	"apipass":"123"
}');
		?>
        
		<em>return</em>:<br>
		<div class="tab"></div>{ "INFO": "TEST connection OK", "STATUS": "OK" }<br>
        
        <br>
        <form method="post">
        TEST
		<textarea class="code" id="data" name="data" style="width:100%;height:120px"><?php
		if (isset($_POST["data"])){
			
		 require_once 'AES.php';
		 $aes = new AES();

			
			$key = rand(0,19);
			echo "data: \n".
				$aes->encrypt($_POST["data"],$key).
				"\nkey: \n".
				$key.
				"\nSHA: \n".
				sha1($_POST["data"]);
		}else{
			echo '{
	"apiuser":"perezabreu",
	"apipass":"123"
}';
			}
		?></textarea><br>
        <script>
			var json_connection = '{\n"apiuser":"perezabreu",\n"apipass":"123"\n}';
			var json_recharge   = '{\n"apikey":"c786096634765842b6f66b2c76978578",\n"user":"00100012345",\n"amount":100\n}';
		</script>
        <button type="button" style="height:48px" onClick="document.getElementById('data').innerText =json_connection;">Conexion</button>
        <button type="button" style="height:48px" onClick="document.getElementById('data').innerText =json_recharge;">Recargas</button>
		<button type="submit" style="height:48px">Encryptar key-1</button>
		</form>
	</div>

	<div class="element" style="display:none">
		<h3>Nombre de la Funcion</h3>
		<em>Info</em>:<br><div class="tab"></div>
		La descripcion de la funcion<br>
		<em>params</em>:<br>
		<div class="tab"></div>param
		<div class="tab"></div> parametro opcional<br>
		<div class="tab"></div>param*
		<div class="tab"></div>parametro obligatorio<br>
		<em>return</em>:<br>
		<div class="tab"></div>json.STATUS<div class="tab"></div> OK o ERROR para determinar si la operacion se ejecuto con exito<br>
		<div class="tab"></div>json.INFO<div class="tab"></div> Descripcion de la ejecucion<br>
		<div class="tab"></div>json.state<div class="tab"></div>variable<br>
		<div class="tab"></div>json.chat[]<div class="tab"></div>array<br>
		<div class="tab"></div>json.services{}<div class="tab"></div>Objet<br>
	</div>
	<?php
} ?>
</div>