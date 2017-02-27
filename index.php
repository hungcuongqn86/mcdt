<?php
    //@ini_set('display_errors', 1);
	date_default_timezone_set('Europe/London');
	// Dinh nghia duong dan den thu vien cua Zend
	set_include_path('./library/'
			. PATH_SEPARATOR . './application/models'. PATH_SEPARATOR . './application/');
	// Goi class Zend_Load
	require_once './library/Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->registerNamespace('Zend_');
	$autoloader->registerNamespace('G_');

	//Goi class Controller
	Zend_Loader::loadClass('Efy_Db_Connection');	
	Zend_Loader::loadClass('Efy_Library');		
	Zend_Loader::loadClass('Efy_Xml');
	Zend_Loader::loadClass('Efy_Init_Session');	
	Zend_Loader::loadClass('Efy_Init_Config');	
	Zend_Loader::loadClass('Efy_Function_RecordFunctions'); //Goi lop tao cac phuong thuc dung chung
	Zend_Loader::loadClass('Efy_Publib_Browser'); 
	//Zend_Loader::loadClass('Efy_Publib_Logout'); 
	
	//Khai bao bien toan cuc 
	$conDirApp = new Zend_Config_Ini('./config/config.ini','dirApp');
	$registry = Zend_Registry::getInstance();
	$registry->set('conDirApp', $conDirApp);	
	
	//Dinh nghia hang so dung chung 
	$ConstPublic = new Zend_Config_Ini('./config/config.ini','ConstPublic');
	$registry = Zend_Registry::getInstance();
	$registry->set('ConstPublic', $ConstPublic);	
	
	//Ket noi CSDL SQL theo kieu ADODB
	$connectSQL = new Zend_Config_Ini('./config/config.ini','dbmssql');
	$registry = Zend_Registry::getInstance();
	$registry->set('connectSQL', $connectSQL);
	$connAdo = Efy_Db_Connection::connectADO($connectSQL->db->adapter,$connectSQL->db->config->toArray());
	//Khoi tao bien session
	//Efy_Init_Session::getValueSession($ConstPublic->toArray());
	//Lay url hien hanh
    $df_url = Efy_Function_RecordFunctions::curPageURL(); 
    //echo $df_url;            
    // Goi ham kiem tra user login
	Efy_Function_RecordFunctions::CheckLogin($df_url);
	// setup controller
	$frontController = Zend_Controller_Front::getInstance();	
	$frontController->addControllerDirectory('./application/controllers');
	$frontController->addControllerDirectory('./application/listxml/controllers', 'listxml');
	$frontController->addControllerDirectory('./application/record/controllers', 'record');
	$frontController->addControllerDirectory('./application/logout/controllers','logout');
	$frontController->addControllerDirectory('./application/login/controllers','login');
	
	$frontController->throwExceptions(true);
	$frontController->setDefaultModule('public');
	try  { 
		$frontController->dispatch();
	}  catch  (Exception  $e)  { 
		echo 'Kh&#244;ng t&#236;m th&#7845;y trang b&#7841;n y&#234;u c&#7847;u! C&#243; th&#7875; &#273;&#432;&#7901;ng d&#7851;n kh&#244;ng ch&#237;nh x&#225;c!';
	}  
