<?php
/************************************************
class.transaction.php
version	:  1.0.1
date	:	24-8-2016
*************************************************/
if (!defined('ROOT')){ define('ROOT',"./include/"); }

require_once(ROOT."class/api.class.php");

class login extends API{
	
	  public function __construct($request, $origin) {
        parent::__construct($request);
		parent::login($origin);
    }
}
?>