<?php
/**
 * Nguoi tao: nghiat
 * Ngay tao: 25/11/2010
 * Y nghia: Class chuyen kho bac
 */	
class record_treasuryController extends  Zend_Controller_Action {
	public $_publicPermission;
	public function init(){

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
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 	= "!~~!";	
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();	
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
			
		//Goi lop Listxml_modList
		Zend_Loader::loadClass('record_modTax');
		
		//Lay cac hang so su dung trong JS public
		$objConfig = new Extra_Init();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		//Tao doi tuong XML
		Zend_Loader::loadClass('Extra_Xml');
		
		// Load tat ca cac file Js va Css
		$sStyle = Extra_Util::_getAllFileJavaScriptCss('','js','handle/handle.js,jquery-1.5.1.js',',','js');
										//.Extra_Util::_getAllFileJavaScriptCss('','js/Autocomplete','actb_search.js,common_search.js',',','js')
										//.Extra_Util::_getAllFileJavaScriptCss('','js','js_calendar.js',',','js');
		$sStyle.= Extra_Util::_getAllFileJavaScriptCss('','js','jquery-1.5.1.js',',','js');
		$sStyle.= Extra_Util::_getAllFileJavaScriptCss('','js','ui/jquery.ui.core.js,ui/jquery.ui.widget.js,ui/jquery.ui.datepicker.js,ui/i18n/jquery.ui.datepicker-vi.js',',','js');
		$sStyle.= Extra_Util::_getAllFileJavaScriptCss('','style','simpleModal.css,themes/humanity/jquery-ui-1.8.13.custom.css,themes/base/calendar.css',',','css');
		$this->view->LoadAllFileJsCss = $sStyle;
		/* Ket thuc*/
		
		//Dinh nghia current modul code
		$this->view->currentModulCode = "TREASURY";
		$this->view->currentModulCodeForLeft = $this->_request->getParam('status','');				
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
	/**
	 * Idea : Phuong thuc hien thi danh sach
	 */
	public function indexAction(){	
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Extra_Ecs();
		$objXml					 = new Extra_Xml();
		$objTax					 = new record_modTax();	
		//main or support
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		if($sStatus == 'wait'){
			//Tieu de man hinh danh sach
			$this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; C&#7846;N GI&#7842;I QUY&#7870;T';
			$sRole = 'KHO_BAC';
			$sStatusList = 'CHUYEN_TIEP' ;
			$sDetailStatusCompare = ' And A.C_DETAIL_STATUS = 23 ';
			$sDentalAction = '../../../index/status/wait/';
		}else if($sStatus == 'resolved'){
			//Tieu de man hinh danh sach
			$this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; &#272;&#195; GI&#7842;I QUY&#7870;T';
			$sRole = 'KHO_BAC';
			$sStatusList = 'THU_LY' ;
			$sDetailStatusCompare = ' And A.C_DETAIL_STATUS = 21 ';
			$sDentalAction = '../../../index/status/resolved/';
		}
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();		
		//Lay mang cac TTHC
		//$arrRecordType = $objTax->eCSRecordTypeGetAll($sOwnerCode,'');
		$arrRecordType = $_SESSION['arr_all_record_type'];
		//CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
		$sRecordTypeId = $this->_request->getParam('recordType');
		if ($_SESSION['RECORD_TYPE']){
			if($sRecordTypeId==''){
				$sRecordTypeId = $_SESSION['RECORD_TYPE'];
			}else{
				$_SESSION['RECORD_TYPE'] = $sRecordTypeId;
			}
		}else{
			if($sRecordTypeId == ""){
				$sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
			}	
			$_SESSION['RECORD_TYPE'] = $sRecordTypeId;
		}
		$this->view->sRecordTypeId = $sRecordTypeId;
		if($sRecordTypeId == "") $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
		$arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$iCurrentStaffId = $_SESSION['staff_id'];
		$sReceiveDate = '';
		$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1; 
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>$sDentalAction,'RecordTypeId'=>$sRecordTypeId);
		$this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId,$sRole,$arrRecordType,$arrInputfilter);
		//Neu ton tai gia tri tim kiem tron session thi lay trong session
		if(isset($_SESSION['seArrParameter'])){
			$Parameter 			= $_SESSION['seArrParameter'];
			$sRecordTypeId		= $Parameter['recordType'];
			$sfulltextsearch	= $Parameter['fullTextSearch'];
			unset($_SESSION['seArrParameter']);
		}
		//Day gia tri tim kiem ra view
		$this->view->sfullTextSearch = $sfullTextSearch;
		//C -> M: Truy van lay danh sach HS can giai quyet
		$arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
		//Lay file XML mo ta form danh sach
		$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';		
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",false);
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];	
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = Extra_Util::_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = Extra_Util::_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = Extra_Util::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}else{
			$this->view->sdocpertotal = "Danh sach nay khong co ho so nao";
		}
	}
	public function editAction(){ //hieu chinh
		//Xu ly quay lai
		$psOption = $this->_request->getParam('hdh_option','');
		//Trang thai
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		//Lay Id cua TTHC
		$sRecordTypeId = $this->_request->getParam('recordType',"");
		$this->view->sRecordTypeId = $sRecordTypeId;
		if ($psOption == "QUAY_LAI"){
			//Ghi va quay lai chinh form voi noi dung rong						
			$this->_redirect('record/treasury/index/status/'.$sStatus.'/?recordType='.$sRecordTypeId);			
		}
		//Goi doi tuong
		$objRecordFunction	     = new Extra_Ecs();
		$objTax	  				 = new record_modTax();
		$objInitConfig 			 = new Extra_Init();
		$ojbEfyLib				 = new Extra_Util();
		//Lay Lich JS
		$efyLibUrlPath = $objInitConfig->_setLibUrlPath();
		$url_path_calendar = $efyLibUrlPath . 'efy-calendar/';
		$this->view->urlCalendar = $url_path_calendar;
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();	
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'C&#7852;P NH&#7852;T TH&#212;NG TIN KHO B&#7840;C';
		//Lay thong tin cua TTHC
		$arrInfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
		//Dua ten TTHC ra view
		$this->view->sRecordTypeName = $arrInfoRecordType['C_NAME'];
		//Ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		//
		$this->view->sOwnerCode = $sOwnerCode;
		//Lay Id 
		$sRecordPk = $this->_request->getParam('hdn_object_id',""); 
		$this->view->sRecordPk = $sRecordPk;
		//Lay thong tin thue can hieu chinh
		$arrRecord = $objTax->eCSTaxTreasuryGetSingle($sRecordPk);
		$this->view->arrRecord = $arrRecord;
		//Lay ID phong ban NSD dang nhap hien thoi
		//$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		//Cap nhat
		if($psOption == "GHI"){
			//ID COng viec thue
			$sTaxPk = 	$this->_request->getParam('hdn_tax_id',"");			
			//Ngay thuc hien
			$dWorkDate = 	$ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_WORK_DATE',"") );
			//Dau viec 
			$sWorkType = 	$this->_request->getParam('work',"");
			//Li do
			$sResult = 	$this->_request->getParam('C_RESULT',"");
			//ID can bo
			$iFkStaff = $_SESSION['staff_id'];
			//Ten-chuc vu
			$sPositionName = $objRecordFunction->getNamePositionStaffByIdList($iFkStaff);
			//-----
			$sRegisterName	 = trim($this->_request->getParam('C_REGISTER_NAME',""));
			$sRegisterAddress	 = trim($this->_request->getParam('C_REGISTER_ADDRESS',""));
			$sAreaLand	 = trim($this->_request->getParam('C_AREA_LAND',""));
			$sAddressLand	 = trim($this->_request->getParam('C_ADDRESS_LAND',""));
			$sPayTotal	 = trim($this->_request->getParam('C_PAID_TAX_TOTAL',""));
			$sPayAddress	 = trim($this->_request->getParam('C_TREASURY_ADDRESS',""));
			//Cap nhat CSDL
			$arrParameter = array(	
						'PK_RECORD'									=>	$sRecordPk,															
						'PK_RECORD_TAX'								=>	$sTaxPk,
						'C_WORK_DATE'								=>	$dWorkDate,															
						'C_WORK_TYPE'								=>	$sWorkType,
						'C_RESULT'									=>	$sResult,
						'FK_STAFF'									=>	$iFkStaff,
						'C_POSITION_NAME'							=>	$sPositionName,	 
						'C_OWNER_CODE'								=>	$sOwnerCode,	
						'C_REGISTER_NAME'							=>	$sRegisterName,
						'C_REGISTER_ADDRESS'						=>	$sRegisterAddress,
						'C_AREA_LAND'								=>	$sAreaLand,
						'C_ADDRESS_LAND'							=>	$sAddressLand,
						'C_PAYABLE_TAX_TOTAL'						=>	$sPayTotal,
						'C_TREASURY_ADDRESS'						=>	$sPayAddress
						);
			$arrResult = $objTax->eCSTaxTreasuryUpdate($arrParameter);	//Goi model cap nhat vao CSDL
			$this->_redirect('record/treasury/index/status/'.$sStatus.'/');
		}	
	}
	public function printAction(){	//In thong bao thue
		$objInitConfig 			 = new Extra_Init();
		//Pk cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		//Duong dan Url
		$http = $objInitConfig->_getCurrentHttpAndHost();
		//Cac tuy chon in
		$path = $_SERVER['SCRIPT_FILENAME'];
		$path = substr($path, 0, -9);
		//Ten file mo ta bao cao
		$my_report = str_replace("/", "\\", $path) . "rpt\\record\\"."tax"."\\thong_bao_nop_thue_dat.rpt";
		//Thong so ket noi den Crystal Report
		$COM_Object = "CrystalDesignRunTime.Application.9";		
		$crapp= new COM($COM_Object) or die("Unable to Create Object");
		$creport = $crapp->OpenReport($my_report, 1);	
		//Ket noi CSDL SQL theo kieu ADODB
		$connectSQL = new Zend_Config_Ini('./config/config.ini','dbmssql');
		$arrConn = $connectSQL->db->config->toArray();
		$creport->Database->Tables(1)->SetLogOnInfo($arrConn['host'], $arrConn['dbname'], $arrConn['username'], $arrConn['password']);
		$creport->EnableParameterPrompting = 0;	
		$creport->ReadRecords();
		$creport->ParameterFields(1)->SetCurrentValue($sRecordPk);
		//Ten file
		$report_file = 'tien do cong viec.pdf';
		// Duong dan file report
		$my_report_file = str_replace("/", "\\", $path) . "public\\" . $report_file;
		//export to PDF process
		$creport->ExportOptions->DiskFileName=$my_report_file; //export to file 
		$creport->ExportOptions->PDFExportAllPages=true;
		$creport->ExportOptions->DestinationType = 1; // export to file
		$creport->ExportOptions->FormatType= 31;
		$creport->Export(false);
		$my_report_file = $http .'public/' . $report_file;
		//echo $my_report_file."<br>";
		//echo 'http://'.$_SERVER['HTTP_HOST'].':8080/'.$this->_request->getBaseUrl() .'/public/' . $report_file;exit;
		$this->view->my_report_file = $my_report_file;
	}
public function worklistAction(){
		//Goi cac doi tuong
		$objInitConfig 			 	= new Extra_Init();
		$objRecordFunction	     	= new Extra_Ecs();
		$ojbEfyLib				 	= new Extra_Util();
		//Tieu de 
		$this->view->bodyTitle = 'DANH S&#193;CH C&#212;NG VI&#7878;C';
		//Trang thai
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		//CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
		//Lay ID phong ban NSD dang nhap hien thoi
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$this->view->iFkUnit = $iFkUnit;
		//Ten chuc vu NSD
		$this->view->iFkStaff = $_SESSION['staff_id'];
		//Lay ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$this->view->sOwnerCode =$sOwnerCode;
		//Ten don vi
		$this->view->sUnitName = $objRecordFunction->getNameUnitByIdUnitList($iFkUnit);
		//ID cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$arrTest = $objRecordFunction->eCSGetInfoRecordFromListId($sRecordPk,$sOwnerCode);
		$this->view->sRecordTransitionPk =$arrTest[0]['PK_RECORD_TRANSITION'];
		$this->view->sRecordPk = $sRecordPk;
		//Lay mang cac cong viec tien do
		$this->view->arrWork = $objRecordFunction->eCSHandleWorkGetAll($sRecordPk,$sOwnerCode);	
	}
	public function viewrecordAction(){
		$this->view->titleBody = "CHI TI&#7870;T H&#7890; S&#416;�";
		$objconfig = new Extra_Init();
		$objrecordfun = new Extra_Ecs();
		$objxml = new Extra_Xml();
		$ojbEfyLib = new Extra_Util();
		//Lay tham so cau hinh
		$efyLibUrlPath = $objconfig->_setLibUrlPath();
		$url_path_calendar = $efyLibUrlPath . 'efy-calendar/';
		$this->view->urlCalendar = $url_path_calendar;
		$sRecordWorkPk = $this->_request->getParam('hdn_list_id','');
		$this->view->sRecordWorkPk = $sRecordWorkPk;
		//Lay thong tin nguoi dang nhap hien thoi
		$sStaffName = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'],$_SESSION['staff_id'],'name');
		$sStaffPosition = Extra_Util ::_getItemAttrById($_SESSION['arr_all_staff'],$_SESSION['staff_id'],'position_code');
		$this->view->arrConst = $objconfig->_setProjectPublicConst();			
		//Lay thong tin cua loai ho so TTHC
		$arrRecordType = $objrecordfun->eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
		//Lay id TTHC tu trang index
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
		$this->view->iNumberProcessDate = $arrinfoRecordType['C_PROCESS_NUMBER_DATE'];
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';		
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';	
		}
		$this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];
		//Lay thong tin cua mot ho so
		$srecordId = $this->_request->getParam('hdn_object_id','');
		$this->view->srecordId = $srecordId;
		$arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($srecordId, $_SESSION['OWNER_CODE']);
		$sRecordTransitionId = $arrRecordInfo[0]['PK_RECORD_TRANSITION'];
		//Khong duoc phep chinh sua ho so lien thong
		if ($sRecordTransitionId != '')
			$this->view->sRecordTransitionId = $sRecordTransitionId;
		$arrSingleRecord = $objrecordfun->eCSRecordGetSingle($srecordId, $_SESSION['OWNER_CODE'],$sRecordTransitionId);
		$this->view->arrSingleRecord = $arrSingleRecord;
		$this->view->RecodeCode = $arrSingleRecord[0]['C_CODE'];		
		if($this->_request->getParam('rc') != '')
			$this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
		//Lay thong tin file dinh kem
		$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
		
		$arFileAttach = $objrecordfun->eCSGetAllDocumentFileAttach($srecordId, 'HO_SO', 'T_eCS_RECORD');
		
		$this->view->AttachFile = $objrecordfun->DocSentAttachFile_View($arFileAttach,sizeof($arFileAttach),10,true,50);	
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
			$arrParaSet = array("iPage"=>$iPage, "iNumberRecordPerPage"=>$iNumberRecordPerPage,"sfullTextSearch"=>$sfullTextSearch,"sRecordTypeId"=>$sRecordTypeId,"recordType"=>$sRecordTypeId);
			$_SESSION['seArrParameter'] = $arrParaSet;	
		}
		
		//Xu ly truong hop neu la ghi va them tiep thi ko lay thong tin file dinh kem
		$sOption = $this->_request->getParam('hdh_option','');
		$this->view->option = $sOption;
		if ($sOption == "QUAY_LAI")		$this->_redirect('record/receive/index/');			
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrSingleRecord,true,true);	
	}
	public function workaddAction(){
		//Pk cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$this->view->sRecordPk = $sRecordPk;
		//ID TTHC
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Trang thai
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		//Xu ly quay lai
		$psOption = $this->_request->getParam('hdh_option','');
		if ($psOption == "QUAY_LAI"){
			//Ghi va quay lai chinh form voi noi dung rong						
			$this->_redirect('record/tax/worklist/status/'.$sStatus.'/?hdn_object_id='.$sRecordPk.'&recordType='.$sRecordTypeId);
			
		}		
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Extra_Ecs();
		$ojbEfyLib				 = new Extra_Util();
		$objXml					 = new Extra_Xml();
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
		//Lay Lich JS
		$efyLibUrlPath = $objInitConfig->_setLibUrlPath();
		$url_path_calendar = $efyLibUrlPath . 'efy-calendar/';
		$this->view->urlCalendar = $url_path_calendar;
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'C&#7852;P NH&#7852;T TI&#7870;N &#272;&#7896; GI&#7842;I QUY&#7870;T C&#212;NG VI&#7878;C';
		//Lay ID phong ban NSD dang nhap hien thoi
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$this->view->iFkUnit = $iFkUnit;
		//Lay ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$this->view->sOwnerCode = $sOwnerCode;
		$arrWorkTypeByRecordType = $objRecordFunction->fGetRecordTypeList('THU_LY',$sOwnerCode,',');
		$arrWorkTypeByRecordType = explode(',',$arrWorkTypeByRecordType['']);
		//Lay danh sach cua Danh muc cong viec
		$arrWorkType = $objRecordFunction->getAllObjectbyListCode($sOwnerCode,"DM_CV",1);
		$i = 0;
		for ($index = 0 ; $index <sizeof($arrWorkType) ; $index++){//echo $arrWorkType[$index]['C_XML_DATA'];exit;
			$sTest = $objXml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrWorkType[$index]['C_XML_DATA'], "data_list", "list_giai_doan");
			//echo htmlspecialchars($sTest);
			if($sTest =="THU_LY"){
				for ($j=0;$j<sizeof($arrWorkTypeByRecordType);$j++){
					if($arrWorkType[$index]['C_CODE'] == $arrWorkTypeByRecordType[$j]){
						$arrHandleWork[$i]['C_NAME']	= $arrWorkType[$index]['C_NAME'];
						$arrHandleWork[$i]['C_CODE']	= $arrWorkType[$index]['C_CODE'];
						$i++;
						break;
					}	
				}	
			}
		} 
		//Dua cac dau muc cong viec lien quan den thu ly ra man hinh cap nhat
		$this->view->arrHandleWork = $arrHandleWork;
		// Request tu trang cap nhat
		$dWorkDate = trim($this->_request->getParam('C_WORK_DATE',""));
		// Lay dau viec
		$sWorkType = trim($this->_request->getParam('C_WORKTYPE',""));		
		//Lay thong tin file dinh kem
		$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
		$arFileAttach = array();	
		$this->view->AttachFile = $objRecordFunction->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,69);	
		//ID cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$arrTest = $objRecordFunction->eCSGetInfoRecordFromListId($sRecordPk,$sOwnerCode);
		$this->view->sRecordTransitionPk =$arrTest[0]['PK_RECORD_TRANSITION'];
		// Xu ly Cap nhat du lieu tu form
		if($dWorkDate != "" && $sWorkType != ""){
			// Request 
			$sResult = trim($this->_request->getParam('C_RESULT',""));
			//Mang luu du lieu update
			$arrParameter = array(	
								'PK_RECORD'							=>	$sRecordPk,															
								'PK_RECORD_WORK'					=>	'',
								'C_OWNER_CODE'						=>	$sOwnerCode,
								'C_WORK_DATE'						=>	$ojbEfyLib->_ddmmyyyyToYYyymmdd($dWorkDate),
								'C_WORKTYPE'						=>	$sWorkType,
								'C_RESULT'							=>	$sResult,	
								'FK_STAFF'							=>	$_SESSION['staff_id'],
								'C_POSITION_NAME'					=>	$objRecordFunction->getNamePositionStaffByIdList($_SESSION['staff_id']),
								'C_DOC_TYPE'						=>	'RECORD_WORK',
								'C_FILE'							=>	$arrFileNameUpload
								);
			$arrResult = $objRecordFunction->eCSHandleWorkUpdate($arrParameter);	//Goi model cap nhat vao CSDL		
			$this->_redirect('record/tax/worklist/status/'.$sStatus.'/?hdn_object_id='.$sRecordPk.'&recordType='.$sRecordTypeId);		
		}	
						
	}
	public function workeditAction(){
		//Pk cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$this->view->sRecordPk = $sRecordPk;
		//ID TTHC
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Trang thai
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		//Xu ly quay lai
		$psOption = $this->_request->getParam('hdh_option','');
		if ($psOption == "QUAY_LAI"){
			//Ghi va quay lai chinh form voi noi dung rong						
			$this->_redirect('record/tax/worklist/status/'.$sStatus.'/?hdn_object_id='.$sRecordPk.'&recordType='.$sRecordTypeId);
			
		}	
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Extra_Ecs();
		$ojbEfyLib				 = new Extra_Util();
		$objXml					 = new Extra_Xml();
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
		//Lay Lich JS
		$efyLibUrlPath = $objInitConfig->_setLibUrlPath();
		$url_path_calendar = $efyLibUrlPath . 'efy-calendar/';
		$this->view->urlCalendar = $url_path_calendar;
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'CẬP NHẬT TIẾN �?Ộ GIẢI QUYẾT CÔNG VIỆC';
		//Lay ID phong ban NSD dang nhap hien thoi
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$this->view->iFkUnit = $iFkUnit;
		//Lay ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$this->view->sOwnerCode = $sOwnerCode;
		$arrWorkTypeByRecordType = $objRecordFunction->fRecordTypeListByCode('WORKTYPE',$sRecordTypeId,$sOwnerCode,',',',');
		$arrWorkTypeByRecordType = explode(',',$arrWorkTypeByRecordType['']);
		//Lay danh sach cua Danh muc cong viec
		$arrWorkType = $objRecordFunction->getAllObjectbyListCode($sOwnerCode,"DM_CV",1);
		$i = 0;
		for ($index = 0 ; $index <sizeof($arrWorkType) ; $index++){
			$sTest = $objXml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrWorkType[$index]['C_XML_DATA'], "data_list", "list_giai_doan");
			if($sTest =="THU_LY"){
				for ($j=0;$j<sizeof($arrWorkTypeByRecordType);$j++){
					if($arrWorkType[$index]['C_CODE'] == $arrWorkTypeByRecordType[$j]){
						$arrHandleWork[$i]['C_NAME']	= $arrWorkType[$index]['C_NAME'];
						$arrHandleWork[$i]['C_CODE']	= $arrWorkType[$index]['C_CODE'];
						$i++;
						break;
					}	
				}	
			}
		} 
		//Lay Pk cua cong viec can hieu chinh
		$sRecordWorkPk = $this->_request->getParam('hdn_record_id','');
		$this->view->sRecordWorkPk = $sRecordWorkPk;
		//Thong tin cua cong viec
		$this->view->arrSingle = $objRecordFunction->eCSHandleWorkGetSingle($sRecordWorkPk);
		//Dua cac dau muc cong viec lien quan den thu ly ra man hinh cap nhat
		$this->view->arrHandleWork = $arrHandleWork;
		// Request tu trang cap nhat
		$dWorkDate = trim($this->_request->getParam('C_WORK_DATE',""));
		// Lay dau viec
		$sWorkType = trim($this->_request->getParam('C_WORKTYPE',""));			
		//Lay thong tin file dinh kem
		$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
		//Lay file da dinh kem tu truoc
		$arrFileAttach = $objRecordFunction->eCSGetAllDocumentFileAttach($sRecordWorkPk, 'RECORD_WORK', 'T_eCS_RECORD_WORK');
		$this->view->AttachFile = $objRecordFunction->DocSentAttachFile($arrFileAttach,sizeof($arrFileAttach),10,true,69);	
		//ID cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$arrTest = $objRecordFunction->eCSGetInfoRecordFromListId($sRecordPk,$sOwnerCode);
		$this->view->sRecordTransitionPk =$arrTest[0]['PK_RECORD_TRANSITION'];
		// Xu ly Cap nhat du lieu tu form
		if($dWorkDate != ""){
			// Request 
			$sResult = trim($this->_request->getParam('C_RESULT',""));
			//Mang luu du lieu update
			$arrParameter = array(	
								'PK_RECORD'							=>	$sRecordPk,															
								'PK_RECORD_WORK'					=>	$sRecordWorkPk,
								'C_OWNER_CODE'						=>	$sOwnerCode,
								'C_WORK_DATE'						=>	$ojbEfyLib->_ddmmyyyyToYYyymmdd($dWorkDate),
								'C_WORKTYPE'						=>	$sWorkType,
								'C_RESULT'							=>	$sResult,	
								'FK_STAFF'							=>	$_SESSION['staff_id'],
								'C_POSITION_NAME'					=>	$objRecordFunction->getNamePositionStaffByIdList($_SESSION['staff_id']),
								'C_DOC_TYPE'						=>	'RECORD_WORK',
								'C_FILE'							=>	$arrFileNameUpload
								);
			$arrResult = $objRecordFunction->eCSHandleWorkUpdate($arrParameter);	//Goi model cap nhat vao CSDL		
			$this->_redirect('record/tax/worklist/status/'.$sStatus.'/?hdn_object_id='.$sRecordPk.'&recordType='.$sRecordTypeId);			
		}						
	}
	public function workdeleteAction(){
		$objRecordFunction	     = new Extra_Ecs();
		//Trang thai
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		//Pk HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		//ID TTHC
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Lay Id doi tuong can xoa
		$sRecordWorkIdList = $this->_request->getParam('hdn_object_id_list',"");	
		//echo $sRecordWorkIdList;exit;
		//Goi phuong thuc xoa doi tuong
		$arrFile=Extra_Ecs::eCSFileGetSingle($sRecordWorkIdList,'RECORD_WORK');
		$objRecordFunction->eCSHandleWorkDelete($sRecordWorkIdList);
		//xoa file tren o cung
		$fileNameList =$arrFile[0]['C_FILE_NAME'];
		$objconfig = new Extra_Init();
		if($fileNameList != '' && $fileNameList != null){	
			$scriptUrl = $_SERVER['SCRIPT_FILENAME'];
			$scriptFileName = explode("/", $scriptUrl);
			$sWebsitePart = $objconfig ->_setWebSitePath();
			$sWebsitePart = substr($sWebsitePart,1,-1);
			$linkFile = "";
			for($i= 0;$i< sizeof($scriptFileName);$i++){
				if($scriptFileName[$i] == $sWebsitePart){
					$k = $i;
					break;
				}
			}
			for($j = 0; $j <=$k; $j++ ){
				$linkFile .= $scriptFileName[$j]."\\";
			}
			$linkFile .= "public\\attach-file\\";
			//xoa file tren o cung	
			$fileId = explode("!~!", $fileNameList);
			$fileId = explode("_" ,$fileId[0]);
			$unlink = $linkFile . $fileId[0] . "\\" . $fileId[1] .  "\\" . $fileId[2] . "\\" . $fileNameList;
			//echo $unlink;exit;
			unlink($unlink);//echo //exit;	
		}
		$this->_redirect('record/tax/worklist/status/'.$sStatus.'/?hdn_object_id='.$sRecordPk.'&recordType='.$sRecordTypeId);	
	}
	public function workprintAction(){
		$objInitConfig 			 = new Extra_Init();
		//Pk cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		//echo '$sRecordPk:'.$sRecordPk;exit;
		//Duong dan Url
		$http = $objInitConfig->_getCurrentHttpAndHost();
		//Cac tuy chon in
		$path = $_SERVER['SCRIPT_FILENAME'];
		$path = substr($path, 0, -9);
		//Ten file mo ta bao cao
		$my_report = str_replace("/", "\\", $path) . "rpt\\record\\"."handle"."\\tien_do_cong_viec.rpt";
		//Thong so ket noi den Crystal Report
		$COM_Object = "CrystalDesignRunTime.Application.9";		
		$crapp= new COM($COM_Object) or die("Unable to Create Object");
		$creport = $crapp->OpenReport($my_report, 1);	
		//Ket noi CSDL SQL theo kieu ADODB
		$connectSQL = new Zend_Config_Ini('./config/config.ini','dbmssql');
		$arrConn = $connectSQL->db->config->toArray();
		$creport->Database->Tables(1)->SetLogOnInfo($arrConn['host'], $arrConn['dbname'], $arrConn['username'], $arrConn['password']);
		$creport->EnableParameterPrompting = 0;	
		$creport->ReadRecords();
		$creport->ParameterFields(1)->SetCurrentValue($sRecordPk);
		$creport->ParameterFields(2)->SetCurrentValue('');
		//Ten file
		$report_file = 'tien do cong viec.pdf';
		// Duong dan file report
		$my_report_file = str_replace("/", "\\", $path) . "public\\" . $report_file;
		//export to PDF process
		$creport->ExportOptions->DiskFileName=$my_report_file; //export to file 
		$creport->ExportOptions->PDFExportAllPages=true;
		$creport->ExportOptions->DestinationType = 1; // export to file
		$creport->ExportOptions->FormatType= 31;
		$creport->Export(false);

		$my_report_file = $http .'public/' . $report_file;
		//echo 'http://'.$_SERVER['HTTP_HOST'].':8080/'.$this->_request->getBaseUrl() .'/public/' . $report_file;exit;
		$this->view->my_report_file = $my_report_file;

	}
}?>
