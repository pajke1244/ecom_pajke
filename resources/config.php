<?php 
//ukljucujemo zbog koriscenja header("location")... da ne bi iskakala greska!!!
ob_start();

// za sesije!!!
session_start();

// session_destroy();

defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);
//define path for templates
defined("TEMPLATE_FRONT") ? null : define("TEMPLATE_FRONT", __DIR__ . DS . "templates/front");

defined("TEMPLATE_BACK") ? null : define("TEMPLATE_BACK", __DIR__ . DS . "templates/back");

defined("UPLOAD_DIR") ? null : define("UPLOAD_DIR", __DIR__ . DS . "uploads");
//define path for database
defined("DB_HOST") ? null : define("DB_HOST","localhost");
defined("DB_USER") ? null : define("DB_USER","root");
defined("DB_PASS") ? null : define("DB_PASS","");
defined("DB_NAME") ? null : define("DB_NAME","ecom_db");

//variables for db

$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// ukljucujem functions.php
require_once("functions.php");
// require_once("cart.php");

//rout
// echo __DIR__;
// echo "<br>";
// echo TEMPLATE_FRONT;
// echo "<br>";
// echo TEMPLATE_BACK;

?>   