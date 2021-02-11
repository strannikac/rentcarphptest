<?php 

use App\App;

//date_default_timezone_set("UTC"); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'autoload.php';
require_once _ROOT_ . 'vendor/autoload.php';

new App();

?>