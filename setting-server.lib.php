<?php
//------------------------------------------------
// DataBase
//------------------------------------------------
	
define("HOST", "db588048652.db.1and1.com"); // The host you want to connect to.
define("USER", "dbo588048652"); // The database username.
define("PASSWORD", "Omeg@1315"); // The database password. 
define("DATABASE", "db588048652"); // The database name.

$mysqli = @new mysqli(HOST, USER, PASSWORD, DATABASE);

// Works as of PHP 5.2.9 and 5.3.0.
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}


$mysqli2 = @new mysqli(HOST, USER, PASSWORD, DATABASE);

// Works as of PHP 5.2.9 and 5.3.0.
if ($mysqli2->connect_error) {
    die('Connect Error: ' . $mysqli2->connect_error);
}

?>
