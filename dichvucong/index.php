<?php
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Jakarta');
$dir = dirname(dirname(__FILE__)).'/library/';
set_include_path($dir . PATH_SEPARATOR . './library/'
    . PATH_SEPARATOR . './application/models' . PATH_SEPARATOR . './application/');
require_once $dir . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('G_');
//Goi class Controller
Zend_Loader::loadClass('G_App');
$app = new G_App();
$app->run();
?>