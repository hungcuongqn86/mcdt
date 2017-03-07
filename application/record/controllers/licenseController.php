<?php
class record_licenseController extends  Zend_Controller_Action {
	public function init(){
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
		//Lay cac hang so su dung trong JS public
		Zend_Loader::loadClass('Extra_Init');
		$objConfig = new Extra_Init();
		$this->view->UrlAjax = $objConfig->_setUrlAjax();	
		$this->view->arrConst = $objConfig->_setProjectPublicConst();
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
		Zend_Loader::loadClass('record_modReceive');
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = '';
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
		
		// Ham lay thong tin nguoi dang nhap hien thi tai Lefmenu
		$this->view->InforStaff = Extra_Util::_InforStaff();
		//Dinh nghia current modul code
		$this->view->currentModulCode = "HANDLE";
		$this->view->currentModulCodeForLeft = 'main';
		$sActionName = $this->_request->getActionName();
		//echo $sActionName;
		if ($sActionName == 'transition' || $sActionName == 'receive'){
			$this->view->currentModulCodeForLeft = 'TRANSITION-RECORD';
		}elseif ($sActionName == 'result' || $sActionName == 'updateresult' || $sActionName == 'mailresult' || $sActionName == 'smsresult' || $sActionName == 'updatetransition'){
			$this->view->currentModulCodeForLeft = 'result';
		}elseif  ($sActionName == 'transitioned'  ){
			$this->view->currentModulCodeForLeft = 'transitioned';
		}elseif  ($sActionName == 'additional' || $sActionName == 'updateadditional'){
			$this->view->currentModulCodeForLeft = 'additional';
		}elseif  ($sActionName == 'processing'){
			$this->view->currentModulCodeForLeft = 'processing';
		}elseif  ($sActionName == 'viewrecord'){
			$this->view->currentModulCodeForLeft = '';
		}
		$psshowModalDialog = $this->_request->getParam('showModelDialog',0);
		$this->view->showModelDialog = $psshowModalDialog;
		if ($psshowModalDialog != 1){
			//Hien thi file template
			$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    //Hien thi header 
			$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    //Hien thi header 		    
	        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	 //Hien thi footer
		}
  	}	
	
	public function indexAction(){
		$this->view->titleBody = "";
		$objconfig = new Extra_Init();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Extra_Xml();
		$ojbEfyLib = new Extra_Util();
		$objReceive = new record_modReceive();

		//Lay thong tin cua loai ho so TTHC
		$arrRecordType = $objrecordfun->eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
		$sRecordTypeId = $this->_request->getParam('recordType');
		//tren hidden
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('hdn_record_type_id');
		//tren Url
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('r');
		$this->view->RecordTypeId = $sRecordTypeId;
		$arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/mau_giay_phep.xml';
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/mau_giay_phep.xml';	
		}
		//lay tieu de tu file xml
		$v_form_title=Extra_Xml::_xmlGetXmlTagValue($sxmlFileName,"common_para_list","form_title");
		$this->view->v_form_title=$v_form_title;
		//Lay thong tin cua mot ho so
		$srecordId = $this->_request->getParam('hdn_object_id','');
		$this->view->srecordId = $srecordId;

		//Luu cac dieu kien tim kiem len session
		if(!isset($_SESSION['seArrParameter'])){
			$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
			$sRecordTypeId 		= $this->_request->getParam('recordType');
			$iPage				= $this->_request->getParam('hdn_current_page',0);	
			$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
			if ($iPage <= 1){
				$iPage = 1;
			}
			if ($iNumberRecordPerPage == 0)
				$iNumberRecordPerPage = 15;
			$arrParaSet = array("iPage"=>$iPage, "iNumberRecordPerPage"=>$iNumberRecordPerPage,"sfullTextSearch"=>$sfullTextSearch,"sRecordTypeId"=>$sRecordTypeId);
			$_SESSION['seArrParameter'] = $arrParaSet;	
		}
		$sOption = $this->_request->getParam('hdh_option','');
		$this->view->option = $sOption;
		if ($sOption == "QUAY_LAI")
			$this->_redirect('record/receive/index/');
		//Kiem tra neu form da duoc submit
		if($this->_request->getParam('hdn_is_update','') == '1'){
			//Tao xau xml luu vao database
			$strXml = '<root><data_list>';
			$sXmlTags = $this->_request->getParam('hdn_xml_tag_list','');
			$sXmlValues = $this->_request->getParam('hdn_xml_value_list','');
			$arrXmlTags = explode('!~~!', $sXmlTags);
			$arrXmlValues = explode('!~~!', $sXmlValues);
			for ($i = 0; $i < sizeof($arrXmlTags); $i++)
				$strXml .= '<' . $arrXmlTags[$i] . '>' . $arrXmlValues[$i] . '</' . $arrXmlTags[$i] . '>';
			$strXml = $strXml . "</data_list></root>";

			$arrResult = $objReceive->eCSRecordUpdateLicense($srecordId, $strXml);		
		}	
		$arrSingleRecord = $objrecordfun->eCSRecordGetSingle($srecordId, $_SESSION['OWNER_CODE'],'');
		//var_dump($arrSingleRecord);
		$v_license=$arrSingleRecord[0]['C_LICENSE_XML_DATA'];
		if(is_null($v_license)){
			$v_license='';
		}
		$this->view->license=$v_license;

		$XML_DATA='C_RECEIVED_RECORD_XML_DATA';
		if($arrSingleRecord[0]['C_LICENSE_XML_DATA']!='' && !is_null($arrSingleRecord[0]['C_LICENSE_XML_DATA']) ){	
			$XML_DATA='C_LICENSE_XML_DATA';
		}
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield ($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', $XML_DATA, $arrSingleRecord,true,true);
	}
	/**
	 * Creater : KHOINV
	 * Date : 11/064/2011
	 * Idea : Tao phuong thuc hien in giay phep
	 */
	public function printreceiptAction(){		
		//Tao doi tuong Efy_lib
		$ojbEfyLib = new Extra_Util();
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		// Tao doi tuong cho lop xu ly du lieu lien quan modul	
		$objReceive = new record_modReceive();						
		//Tao doi tuong trong thu hien cac ham ma modul dung chung
		$objQLDTFun = new Efy_Function_RecordFunctions();		
		//Tao doi tuong config
		$objConfig = new Extra_Init();
		// Lay toan bo tham so truyen tu form			
		$arrInput = $this->_request->getParams();
		//$barcodelink = $objConfig->_setWebSitePath();
		//Lay thong tin chi tiet ho so			
		$psRecordID = $this->_request->getParam('hdn_object_id','');
		$psFileName = $this->_request->getParam('fileName','');
		$v_exporttype = $this->_request->getParam('hdn_exporttype',"2");
		//echo $psRecordID;
		if (trim($psRecordID) != ""){
			$arrGetSingleRecord = $objReceive->eCSRecordGetSingle($psRecordID,$sOwnerCode);
			$psXmlStr 			= 	$arrGetSingleRecord['C_LICENSE_XML_DATA'];
			if($psXmlStr==''){
				$psXmlStr 			= 	$arrGetSingleRecord['C_RECEIVED_RECORD_XML_DATA'];
			}	
		}else{
			$arrGetSingleRecord = array();
			$psXmlStr = "";
		}
		$sReceiptContentFile = $objQLDTFun->ecs_PrintReceipt('./xml/record/' . $arrGetSingleRecord['C_KEY'].'/' . $psFileName.'.xml', 'replace_list', 'replace', '', $psXmlStr, $arrGetSingleRecord,'./templates/' . $arrGetSingleRecord['C_KEY'] . '/',$psFileName.'.tpl',$sOwnerCode);				
		switch($v_exporttype) {
			case 1;
				$sExportFileName = "Maugiaycn.htm";
				//$sHtmlContent = $sHtmlConvertExcel;
				break;
			case 2;
				//$sHtmlContent = str_replace('text/html','application/msword',$sHtmlConvertWord);
				$sExportFileName = "Maugiaycn.doc";
				break;
			default:
				$sExportFileName = "Maugiaycn.doc";
				//$sHtmlContent = $sHtmlConvertExcel;
				break;
		}
		$ojbEfyLib->_writeFile('public/' . $sExportFileName,$sReceiptContentFile);

		$this->view->sExportFileName = $objConfig->_getCurrentHttpAndHost().'public/'. $sExportFileName;
	
	}
}?>