<?php
class record_seachrecordresultController extends Zend_Controller_Action{
	public function init(){
		//Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		//Cau hinh cho Zend_layout
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout']."/searchrecordresult/",
			    'layout' => 'index'			    
			    ));	
		//Load ca thanh phan cau vao trang layout (index.phtml)
		$response = $this->getResponse();
		//Lay cac hang so su dung trong JS public
		Zend_Loader::loadClass('Efy_Init_Config');
		$objConfig = new Efy_Init_Config();
		$this->view->UrlAjax = $objConfig->_setUrlAjax();	
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();
		//Lay so dong tren man hinh danh sach
		$this->view->NumberRowOnPage 	= $this->_ConstPublic['NumberRowOnPage'];		
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 			= $this->_ConstPublic['delimitor'];
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";		
		//Goi lop modRecord
		Zend_Loader::loadClass('record_modSeachRecordResult');
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = Efy_Publib_Library::_getAllFileJavaScriptCss('','efy-js','Record.js',',','js') . Efy_Publib_Library::_getAllFileJavaScriptCss('','efy-js','ajax.js',',','js') . Efy_Publib_Library::_getAllFileJavaScriptCss('','efy-js','jquery-1.5.1.js,jQuery.equalHeights.js',',','js'). Efy_Publib_Library::_getAllFileJavaScriptCss('','efy-js/LibSearch','actb_search.js,common_search.js',',','js');
		//Dinh nghia current modul code
		$this->view->currentModulCode = "RECORD";
		$pcurrentModulCodeForLeft = $this->_request->getParam('htn_leftModule',"");
		if($pcurrentModulCodeForLeft != '')
				$this->view->currentModulCodeForLeft = $pcurrentModulCodeForLeft;
		else 	$this->view->currentModulCodeForLeft = 'DOCUMENT-RECORD-DOC';
		$psshowModalDialog = $this->_request->getParam('showModalDialog',"");
		$this->view->showModelDialog = $psshowModalDialog;
		if ($psshowModalDialog != 1){
			//Hien thi file template
			$response->insert('header', $this->view->renderLayout('headerSeachRecordResult.phtml','./application/views/scripts/'));    //Hien thi header 
	        $response->insert('footer', $this->view->renderLayout('footerOnNet.phtml','./application/views/scripts/'));  	 //Hien thi footer
		}
  	}	
	public function indexAction(){
		$obj_reach = new record_modSeachRecordResult();
		$objInitConfig  = new Efy_Init_Config();
		$sRecordPk = $this->_request->getParam('hdn_id_record','');
		$this->view->arrResult = $obj_reach->SeachRecordResult($sRecordPk);
		$this->view->arrWork = $obj_reach->eCS_SeachRecordWork($sRecordPk);
		$http = $objInitConfig->_getCurrentHttpAndHost();
		$http = $http .'public/images/';
		$this->view->httpPatch = $http;
	}
}