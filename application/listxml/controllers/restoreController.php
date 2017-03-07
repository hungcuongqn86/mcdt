<?php

/**
 * Creater : NGHIAT
 * Date : 18/10/2010
 * Idea : Class Xu ly thong thong doi tuong danh muc
 */
class Listxml_restoreController extends  Zend_Controller_Action {
	public function init(){
		//Extra_Ecs::CheckLogin();
		//Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		//Cau hinh cho Zend_layout
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout'],
			    'layout' => 'index'			    
			    ));	
		//Load ca thanh phan cau vao trang layout (index.phtml)
		$response = $this->getResponse();
		
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();
		
		//Lay so dong tren man hinh danh sach
		$this->view->NumberRowOnPage 	= $this->_ConstPublic['NumberRowOnPage'];		
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
		
		//Duong dan file JS xu ly modul
		$this->view->baseJavaUrl = "js/jsList.js";
		
		//Goi lop Listxml_modList
		Zend_Loader::loadClass('Listxml_modlist');
		
		//Lay cac hang so su dung trong JS public
		Zend_Loader::loadClass('Extra_Init');
		$objConfig = new Extra_Init();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		//Tao doi tuong XML
		Zend_Loader::loadClass('Extra_Xml');
		
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss =Extra_Util::_getAllFileJavaScriptCss('public/js/ListType','','','','js')
										.Extra_Util::_getAllFileJavaScriptCss('public/js/ListType','','','','css')
										.Extra_Util::_getAllFileJavaScriptCss('','js','jsList.js,jquery-1.5.1.js,jquery.simplemodal.js',',','js')
										.Extra_Util::_getAllFileJavaScriptCss('','style','simpleModal.css',',','css');;
		/* Ket thuc*/
		
		//Dinh nghia current modul code
		$this->view->currentModulCode = "LIST";
		$this->view->currentModulCodeForLeft = "RESTORE";		
		
		//Lay tra tri trong Cookie
		$sGetValueInCookie = Extra_Util::_getCookie("showHideMenu");
		
		//Neu chua ton tai thi khoi tao
		if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)){
			Extra_Util::_createCookie("showHideMenu",1);
			Extra_Util::_createCookie("ImageUrlPath",$this->_request->getBaseUrl() . "/public/images/close_left_menu.gif");
			//Mac dinh hien thi menu trai
			$this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
			//Hien thi anh dong menu trai
			$this->view->ShowHideimageUrlPath = $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif";
		}else{//Da ton tai Cookie
			/*
				Lay gia tri trong Cookie, neu gia tri trong Cookie = 1 thi hien thi menu, truong hop = 0 thi an menu di
			*/
			if ($sGetValueInCookie != 0){
				$this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
			}else{
				$this->view->hideDisplayMeneLeft = "";// = "" : an menu
			}
			//Lay dia chi anh trong Cookie
			$this->view->ShowHideimageUrlPath = Extra_Util::_getCookie("ImageUrlPath");
		}
			
		//Hien thi file template
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    	//Hien thi header 
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    		//Hien thi header 		    
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  
	}
	public function restoreAction(){
		// Tieu de cua Form cap  nhat
		$this->view->bodyTitle = 'PHỤC HỒI DỮ LIỆU';
		$RecordFunctions 	= new Extra_Ecs();
		//$objBackup 			= new Listxml_modBackup();
		$objConfig			= new Extra_Init();
		$ojbXmlLib 			= new Extra_Xml();
		// goi load div 
		$this->view->divDialog = $this->showDialog();
		$connectSQL = new Zend_Config_Ini('./config/config.ini','dbmssql');
		$arrConfig = $connectSQL->db->config->toArray();					
		$sDatabase = $arrConfig['dbname'];
		$this->view->sDatabaseName = $sDatabase;
		$arrResult = $RecordFunctions->getAllObjectbyListCode($_SESSION['OWNER_CODE'],'DM_TS_HT');
		//var_dump($arrResult);
		$this->view->urlbackup=$objConfig->_setWebSitePath().'listxml/restore/restoredatabase/';
		$sPath = $ojbXmlLib->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrResult[0]['C_XML_DATA'],'data_list','path_backup');		
		$this->view->sPathbackup = $sPath;			
	   //  Thuc hien hieu chinh danh muc
	   $isUpdate = $this->_request->getParam('hdn_update','');
	   $sFileName = $this->_request->getParam('txt_fileName','');  
				
	}
	public function restoredatabaseAction(){
		global $adoConn;		
		$adoConn->Disconnect();		
		//Goi cac doi tuong	
		$objrestore			= new Listxml_modlist();
		$sFileName = $this->_request->getParam('fileName','');
		$sDatabaseName = $this->_request->getParam('database','');
		if(is_file($sFileName)){
			$sDatabaseName = $this->_request->getParam('database','');
			$connectSQL = new Zend_Config_Ini('./config/config.ini','dbmssql');
			$config = $connectSQL->db->config->toArray();
			//Tao doi tuong ADODB
			$adoConn = NewADOConnection("ado_mssql");  // create a connection
			$connStr = "Provider=SQLOLEDB; Data Source=" . $config['host'] . ";Initial Catalog='master'; User ID=" . $config['username'] . "; Password=" .$config['password'];
			//call connect adodb
			$adoConn->Connect($connStr) or die("Hien tai he thong khong the ket noi vao CSDL duoc!");
			$adoConn->SetFetchMode(ADODB_FETCH_ASSOC);
			//
			$sql = "Exec [sp_RestoreDatabase] ";
			$sql .= "'" . $sFileName . "'";
			$sql .= ",'" . $sDatabaseName . "'";
			$ArrSingleData = $adoConn->GetRow( $sql);
			if(sizeof($ArrSingleData)==0){
				echo 'Phục hồi dữ liệu thành công';
			}else{
				echo 'Gặp sự cố khi phục hồi dữ liệu';
			}		
		}else{
			echo 'File sao lưu dữ liệu '.$sFileName.' không tồn tại';
		}
		exit;
	}
/**
 * @see : Thuc hien lay file doc tat ca cac file xml tu o cung cua server
 * @creator: Thainv
 * @createdate: 
 * 
 * 
 * 	*/
	
	private function showDialog(){
		$RecordFunctions 	= new Extra_Ecs();
		$ojbXmlLib 			= new Extra_Xml();
		$arrResult = $RecordFunctions->getAllObjectbyListCode($_SESSION['OWNER_CODE'],'DM_TS_HT');
		$dir = $ojbXmlLib->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrResult[0]['C_XML_DATA'],'data_list','path_backup');
		//$dir = "./xml/list/";				
		$objConfig = new Extra_Init();
		
		$sResHtml = $sResHtml. "<div style='overflow:auto;height:95%; width:98%; padding: 6px 2px 2px 2px;'>";	
		
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	// kt la file xml thi hien thi		        	
		        	$filetype = substr($file,strlen($file)-4,4);			        	
		        	$filetype = strtolower($filetype);
		        	if($filetype == ".bak"){		            	
		            	$sResHtml = $sResHtml . "<p  class='normal_label' style='width:95%'  align='left' >";		            	
		            	$sResHtml = $sResHtml . " 	<img src ='".$objConfig->_setImageUrlPath() ."file_icon.gif' width='12' />" ;	
						$sResHtml = $sResHtml . "		<a href='#' onClick =\"getFileNameFromDiv('".$file. "')\">" . $file . "</a>";
		            	$sResHtml = $sResHtml . "</p>";
		        	}	
		        }
		        closedir($dh);
		    }
		}
		$sResHtml = $sResHtml.'</div>';	
		
		return  $sResHtml;
	}	
}
?>