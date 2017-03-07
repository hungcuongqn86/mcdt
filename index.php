<?php
@ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
// Dinh nghia duong dan den thu vien cua Zend
set_include_path('./library/'
    . PATH_SEPARATOR . './application/models' . PATH_SEPARATOR . './application/');
// Goi class Zend_Load
require_once './library/Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('Extra_');
$autoloader->registerNamespace('G_');

//Goi class Controller
Zend_Loader::loadClass('Extra_Db');
Zend_Loader::loadClass('Efy_Library');
Zend_Loader::loadClass('Extra_Xml');
Zend_Loader::loadClass('Extra_Session');
Zend_Loader::loadClass('Extra_Init');
Zend_Loader::loadClass('Efy_Function_RecordFunctions');

//Khai bao bien toan cuc
$conDirApp = new Zend_Config_Ini('./config/config.ini', 'dirApp');
$registry = Zend_Registry::getInstance();
$registry->set('conDirApp', $conDirApp);

//Dinh nghia hang so dung chung
$ConstPublic = new Zend_Config_Ini('./config/config.ini', 'ConstPublic');
$registry = Zend_Registry::getInstance();
$registry->set('ConstPublic', $ConstPublic);

//Ket noi CSDL SQL theo kieu ADODB
$connectSQL = new Zend_Config_Ini('./config/config.ini', 'dbmssql');
$registry = Zend_Registry::getInstance();
$registry->set('connectSQL', $connectSQL);
$connAdo = Extra_Db::connectADO($connectSQL->db->adapter, $connectSQL->db->config->toArray());
//Lay url hien hanh
$df_url = Efy_Function_RecordFunctions::curPageURL();
// Goi ham kiem tra user login
Efy_Function_RecordFunctions::CheckLogin($df_url);

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->addControllerDirectory('./application/controllers');
$frontController->addControllerDirectory('./application/listxml/controllers', 'listxml');
$frontController->addControllerDirectory('./application/record/controllers', 'record');
$frontController->addControllerDirectory('./application/logout/controllers', 'logout');
$frontController->addControllerDirectory('./application/login/controllers', 'login');
$frontController->throwExceptions(true);
$frontController->setDefaultModule('public');
$frontController->dispatch();