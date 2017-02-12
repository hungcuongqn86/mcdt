<?php

/**
 * Class record_handleController
 */
class record_handleController extends  Zend_Controller_Action {
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
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 	= "!~~!";	
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();	
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
			
		//Goi lop Listxml_modList
		Zend_Loader::loadClass('record_modHandle');
		Zend_Loader::loadClass('record_modReceive');
		//Lay cac hang so su dung trong JS public
		$objConfig = new Efy_Init_Config();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		//Tao doi tuong XML
        $objLibrary = new Efy_Library();
		// Load tat ca cac file Js va Css
		$sStyle = $objLibrary->_getAllFileJavaScriptCss('','efy-js','handle/handle.js,js-record/handle.js,xml/general_datatable.js',',','js');
		$this->view->LoadAllFileJsCss = $sStyle;
		//Dinh nghia current modul code
		$this->view->currentModulCode = "HANDLE";
        $currentModulCodeForLeft = 'HANDLE-RECORD';
        $sActionName = $this->_request->getActionName();
        switch ($sActionName) {
            case 'list':
                $currentModulCodeForLeft = 'HANDLE-ADDITIONAL';
                break;
            case 'viewlist':
                $currentModulCodeForLeft = 'HANDLE-ADDITIONAL';
                break;
            case 'approved':
                $currentModulCodeForLeft = 'HANDLE-APPROVED';
                break;
            case 'viewapproved':
                $currentModulCodeForLeft = 'HANDLE-APPROVED';
                break;
            case 'result':
                $currentModulCodeForLeft = 'HANDLE-RESULT';
                break;
            case 'backongate':
                $currentModulCodeForLeft = 'HANDLE-RESULT';
                break;
            case 'viewresult':
                $currentModulCodeForLeft = 'HANDLE-RESULT';
                break;
            case 'transition':
                $currentModulCodeForLeft = 'HANDLE-TRANSITION';
                break;
            case 'confirm':
                $currentModulCodeForLeft = 'HANDLE-TRANSITION';
                break;
        }
        $this->view->currentModulCodeForLeft = $currentModulCodeForLeft;


		//Lay tra tri trong Cookie
		$sGetValueInCookie = $objLibrary->_getCookie("showHideMenu");
		if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)){
            $objLibrary->_createCookie("showHideMenu",1);
            $objLibrary->_createCookie("ImageUrlPath",$this->_request->getBaseUrl() . "/public/images/close_left_menu.gif");
			//Mac dinh hien thi menu trai
			$this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
			//Hien thi anh dong menu trai
			$this->view->ShowHideimageUrlPath = $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif";
		}else{
			if ($sGetValueInCookie != 0){
				$this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
			}else{
				$this->view->hideDisplayMeneLeft = "";// = "" : an menu
			}
			//Lay dia chi anh trong Cookie
			$this->view->ShowHideimageUrlPath = $objLibrary->_getCookie("ImageUrlPath");
		}

		//Hien thi file template
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));
	}
    /**
     *
     */
	public function indexAction(){
		//Goi cac doi tuong
		$objInitConfig 			 = new Efy_Init_Config();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
        $ojbEfyLib				 = new Efy_Library();

		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		$iCurrentStaffId = $_SESSION['staff_id'];
		$arrRecordType = $_SESSION['arr_all_record_type'];
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;

        $sRole = '';
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; C&#7846;N GI&#7842;I QUY&#7870;T';
        $sStatusList = 'THU_LY,TRA_LAI';
        $sDetailStatusCompare = " And (PK_RECORD in (Select FK_RECORD From T_eCS_RECORD_RELATE_STAFF Where C_STATUS = ''DANG_XL'' And FK_STAFF = ''".$iCurrentStaffId."'' And C_ROLES = ''THULY_CHINH'')) ";

		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
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
		$arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$sReceiveDate = '';

		$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1; 
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../handle/index','RecordTypeId'=>$sRecordTypeId);
		$this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'THU_LY', $arrRecordType,$arrInputfilter);

		if(isset($_SESSION['seArrParameter'])){
			$Parameter 			= $_SESSION['seArrParameter'];
			$sRecordTypeId		= $Parameter['recordType'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
			unset($_SESSION['seArrParameter']);
		}
		//Day gia tri tim kiem ra view
		$this->view->sfullTextSearch = $sfullTextSearch;
		$arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
		//var_dump($arrResult);
		//Lay file XML mo ta form danh sach
		$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';	
		if(!file_exists($sXmlFileName)){
			$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';	
		}	
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../handle/viewindex');
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];	
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = $ojbEfyLib->_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}else{
			$this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
		}
	}
    /**
     * @throws Zend_Exception
     */
    public function viewindexAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Efy_Function_RecordFunctions();
        //Lay mang hang so dung chung
        $arrConst = $objInitConfig->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        //Pk cua HS
        $sRecordPk = $this->_request->getParam('hdn_object_id','');
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Lay mang cac cong viec tien do
        $this->view->arrWork = $objHandle->eCSHandleWorkGetAll($sRecordPk,'');
        //Thong tin ho so
        $arrRecord=$objSearch->eCSSearchGetSingle($sRecordPk,$sOwnerCode);
        $arFileAttach = $objSearch->DOC_GetAllDocumentFileAttach($sRecordPk, 'HO_SO', 'T_eCS_RECORD');
        $arrRecord['file'] = $arFileAttach;
        $this->view->generalhtmlinfo = $objrecordfun->generalhtmlinfo($arrConst,$arrRecord);
    }
    /**
     * @throws Zend_Exception
     */
    public function viewlistAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Efy_Function_RecordFunctions();
        //Lay mang hang so dung chung
        $arrConst = $objInitConfig->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        //Pk cua HS
        $sRecordPk = $this->_request->getParam('hdn_object_id','');
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Lay mang cac cong viec tien do
        $this->view->arrWork = $objHandle->eCSHandleWorkGetAll($sRecordPk,'');
        //Thong tin ho so
        $arrRecord=$objSearch->eCSSearchGetSingle($sRecordPk,$sOwnerCode);
        $arFileAttach = $objSearch->DOC_GetAllDocumentFileAttach($sRecordPk, 'HO_SO', 'T_eCS_RECORD');
        $arrRecord['file'] = $arFileAttach;
        $this->view->generalhtmlinfo = $objrecordfun->generalhtmlinfo($arrConst,$arrRecord);
    }
    /**
     * @throws Zend_Exception
     */
    public function viewapprovedAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Efy_Function_RecordFunctions();
        //Lay mang hang so dung chung
        $arrConst = $objInitConfig->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        //Pk cua HS
        $sRecordPk = $this->_request->getParam('hdn_object_id','');
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Lay mang cac cong viec tien do
        $this->view->arrWork = $objHandle->eCSHandleWorkGetAll($sRecordPk,'');
        //Thong tin ho so
        $arrRecord=$objSearch->eCSSearchGetSingle($sRecordPk,$sOwnerCode);
        $arFileAttach = $objSearch->DOC_GetAllDocumentFileAttach($sRecordPk, 'HO_SO', 'T_eCS_RECORD');
        $arrRecord['file'] = $arFileAttach;
        $this->view->generalhtmlinfo = $objrecordfun->generalhtmlinfo($arrConst,$arrRecord);
    }
    /**
     *
     */
    public function processAction(){
        $objHandle	  			 = new record_modHandle();
        $objrecordfun = new Efy_Function_RecordFunctions();
        // Lay id ho so
        $sRecordIdList = $this->_request->getParam('hdn_object_id_list');
        $this->view->sRecordIdList = $sRecordIdList;
        $arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($sRecordIdList);
        $this->view->general_information = $objrecordfun->general_information($arrRecordInfo);
        $sRecordTypeId = '';
        if($arrRecordInfo){
            $sRecordTypeId = $arrRecordInfo[0]['FK_RECORDTYPE'];
        }
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $_SESSION['arr_all_record_type']);
        $this->view->arrinfoRecordType = $arrinfoRecordType;

        $this->view->AttachFile = $objrecordfun->DocSentAttachFile(array(),0,10,true,50);

        $supdate = trim($this->_request->getParam('hdn_update',""));
        if($supdate) {
            $ojbEfyLib = new Efy_Library();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = $this->_request->getParam('chk_process_type');
            $sStatus = '';
            $sDetailStatus = '';
            $sHandleid = '';
            $sHandlename = '';
            $sRole = '';
            switch ($sWorkType) {
                case 'CHUYEN_CAN_BO_XL':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'THULY_CHINH';
                    break;
                case 'CHUYEN_THUE':
                    $sStatus = 'CHUYEN_TIEP';
                    $sDetailStatus = '22';
                    $sHandleid = $this->_request->getParam('chk_tax');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'THUE';
                    break;
                case 'YEU_CAU_BO_SUNG':
                    $sStatus = 'BO_SUNG';
                    $sDetailStatus = '10';
                    break;
                case 'TU_CHOI':
                    $sStatus = 'TU_CHOI';
                    $sDetailStatus = '41';
                    break;
            }
            //Ngày hẹn
            $dLimitDate = $this->_request->getParam('limit_date');
            $dLimitDate = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dLimitDate);
            //Lay thong tin file dinh kem
            $arrFileNameUpload = $ojbEfyLib->_uploadFileList(10, $this->_request->getBaseUrl() . "/public/attach-file/", 'FileName', '!#~$|*');
            $arrParameter = array(
                'PK_RECORD_LIST' => $sRecordIdList,
                'C_WORKTYPE' => $sWorkType,
                'C_SUBMIT_ORDER_CONTENT' => $idea,
                'FK_STAFF' => $sHandleid,
                'C_POSITION_NAME' => $sHandlename,
                'C_ROLES' => $sRole,
                'C_LIMIT_DATE' => $dLimitDate,
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objHandle->eCSHandleTransitionUpdate($arrParameter);
            $this->_redirect('record/handle/index');
        }
    }
    /**
     *
     */
    public function resultAction(){
		//Goi cac doi tuong
		$objInitConfig 			 = new Efy_Init_Config();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
		$objLibrary 			 = new Efy_Library();
		//ID NSD dang nhap hien thoi
		$iCurrentStaffId = $_SESSION['staff_id'];
		$arrRecordType = $_SESSION['arr_all_record_type'];
		$sRecordTypeId = $this->_request->getParam('recordType');
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('hdn_record_type_id');
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('r');
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CHỜ CHUYỂN TRẢ KẾT QUẢ CHO MỘT CỬA';
		//main or support
		$sStatus = $this->_request->getParam('status','');
		$this->view->sStatus = $sStatus;
		$sRole = 'THULY_CHINH';
		$sStatusList = 'CAP_PHEP' ;
		$sDetailStatusCompare = ' And C_DETAIL_STATUS = 21';			
        
		$this->view->sStatusList = $sStatusList;
		$this->view->sRole = $sRole;	
		//Sap xep
		$sOrderClause = $this->_request->getParam('hdn_OrderClause','');
		if($sOrderClause==''){
			$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		}		
		$this->view->sOrderClause = $sOrderClause;				
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
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
		$arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
		$this->view->sCheckResult = $arrinfoRecordType['C_RESULT_DOC_TYPE'];
		
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$sReceiveDate = '';
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1; 
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));

		$dFromDate 	= trim($this->_request->getParam('fromDate',''));		
		$dToDate 	= trim($this->_request->getParam('toDate',''));	
		$this->view->dFromDate = $dFromDate;
		$this->view->dToDate = $dToDate;
		$dFromDateTemp = $objLibrary->_ddmmyyyyToYYyymmdd($dFromDate);
		$dToDateTemp = $objLibrary->_ddmmyyyyToYYyymmdd($dToDate);	
		//Day gia tri tim kiem ra view
		$arrInputfilter = array(
								'fullTextSearch'=>$sfullTextSearch
								,'pUrl'			=>'../handle/result'
								,'RecordTypeId'	=>$sRecordTypeId
								,'fromDate'		=>$dFromDate
								,'toDate'		=>$dToDate
								);		
		$this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'THU_LY', $arrRecordType,$arrInputfilter);
		//Neu ton tai gia tri tim kiem tron session thi lay trong session				
		if(isset($_SESSION['seArrParameter'])){
			$Parameter 			= $_SESSION['seArrParameter'];
			$sRecordTypeId		= $Parameter['recordType'];
			$sfulltextsearch	= $Parameter['fullTextSearch'];
			unset($_SESSION['seArrParameter']);
		}
		//Day gia tri tim kiem ra view
		$this->view->sfullTextSearch = $sfullTextSearch;
		$this->view->sDetailStatusCompare = $sDetailStatusCompare;
		//C -> M: Truy van lay danh sach HS can giai quyet
		$arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage,$dFromDateTemp,$dToDateTemp);
		//Lay file XML mo ta form danh sach
		$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';	
		if(!file_exists($sXmlFileName)){
			$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';	
		}
		$this->view->hdn_xml_file_name = 'danh_sach_hs_can_giai_quyet.xml';
		$this->view->sRecordTypeCode = $sRecordTypeCode;
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../handle/viewresult');
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = $objLibrary->_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = $objLibrary->_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = $objLibrary->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}else{
			$this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
		}
	}
    /**
     * @throws Zend_Exception
     */
    public function viewresultAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Efy_Function_RecordFunctions();
        //Lay mang hang so dung chung
        $arrConst = $objInitConfig->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        //Pk cua HS
        $sRecordPk = $this->_request->getParam('hdn_object_id','');
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Lay mang cac cong viec tien do
        $this->view->arrWork = $objHandle->eCSHandleWorkGetAll($sRecordPk,'');
        //Thong tin ho so
        $arrRecord=$objSearch->eCSSearchGetSingle($sRecordPk,$sOwnerCode);
        $arFileAttach = $objSearch->DOC_GetAllDocumentFileAttach($sRecordPk, 'HO_SO', 'T_eCS_RECORD');
        $arrRecord['file'] = $arFileAttach;
        $this->view->generalhtmlinfo = $objrecordfun->generalhtmlinfo($arrConst,$arrRecord);
    }
    /**
     *
     */
	public function submitorderAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Efy_Function_RecordFunctions();
        // Lay id ho so
        $sRecordIdList = $this->_request->getParam('hdn_object_id_list');
        $this->view->sRecordIdList = $sRecordIdList;
        $arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($sRecordIdList);
        $this->view->general_information = $objrecordfun->general_information($arrRecordInfo);
        $sRecordTypeId = '';
        if($arrRecordInfo){
            $sRecordTypeId = $arrRecordInfo[0]['FK_RECORDTYPE'];
        }
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $_SESSION['arr_all_record_type']);
        $this->view->arrinfoRecordType = $arrinfoRecordType;

        $this->view->AttachFile = $objrecordfun->DocSentAttachFile(array(),0,10,true,50);

        $supdate = trim($this->_request->getParam('hdn_update',""));
        if($supdate){
            $ojbEfyLib = new Efy_Library();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $leaderid = $this->_request->getParam('chk_leader');
            $sleadername = $objrecordfun->getNamePositionStaffByIdList($leaderid);
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //Lay thong tin file dinh kem
            $arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
            $arrParameter = array(
                'PK_RECORD_LIST'							=>	$sRecordIdList,
                'C_WORKTYPE'                                =>  'TRINH_LD_PHONG',
                'C_SUBMIT_ORDER_CONTENT'					=>	$idea,
                'FK_STAFF'									=>	$leaderid,
                'C_POSITION_NAME'							=>	$sleadername,
                'C_ROLES'									=>	'DUYET_CAP_MOT',
                'C_STATUS'									=>	'TRINH_KY',
                'C_DETAIL_STATUS'							=>	'21',
                'NEW_FILE_ID_LIST'                          =>  $arrFileNameUpload,
                'C_USER_ID'									=>	$iUserId,
                'C_USER_NAME'								=>	$iUserName,
                'C_OWNER_CODE'								=>	$_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/handle/index');
        }
	}

    /**
     *
     */
	public function listAction(){	
		//Goi cac doi tuong
		$objInitConfig 			 = new Efy_Init_Config();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
        $ojbEfyLib               = new Efy_Library();
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();		
		//Lay mang cac TTHC
		$arrRecordType = $_SESSION['arr_all_record_type'];
		
		//CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->sRecordTypeId = $sRecordTypeId;
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

		$arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$iCurrentStaffId = $_SESSION['staff_id'];
		$sReceiveDate = '';
        $sRole = '';
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; B&#7892; SUNG';
        $sStatusList = 'BO_SUNG' ;
        $sDetailStatusCompare = 'And A.C_DETAIL_STATUS = 10';

		$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1; 
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../handle/list','RecordTypeId'=>$sRecordTypeId);
		$this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'THU_LY', $arrRecordType,$arrInputfilter);
		//Neu ton tai gia tri tim kiem tron session thi lay trong session
		if(isset($_SESSION['seArrParameter'])){
			$Parameter 			= $_SESSION['seArrParameter'];
			$sRecordTypeId		= $Parameter['recordType'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
			unset($_SESSION['seArrParameter']);
		}
		//Day gia tri tim kiem ra view
		$this->view->sfullTextSearch = $sfullTextSearch;
		//C -> M: Truy van lay danh sach HS can giai quyet
		$arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
		//Lay file XML mo ta form danh sach
		$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';
		if(!file_exists($sXmlFileName)){
			$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';
		}
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../handle/viewlist');
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = $ojbEfyLib->_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}else{
			$this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
		}
	}
    /**
     *
     */
    public function approvedAction(){
        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objRecordFunction	     = new Efy_Function_RecordFunctions();
        $objXml					 = new Efy_Publib_Xml();
        $ojbEfyLib               = new Efy_Library();
        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        //Lay mang cac TTHC
        $arrRecordType = $_SESSION['arr_all_record_type'];

        //CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
        $sRecordTypeId = $this->_request->getParam('recordType');
        $this->view->sRecordTypeId = $sRecordTypeId;
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

        $arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $iCurrentStaffId = $_SESSION['staff_id'];
        $sReceiveDate = '';
        $sRole = '';
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; B&#7892; SUNG';
        $sStatusList = 'TRINH_KY' ;
        $sDetailStatusCompare = '';

        $sOrderClause = 'order by  C_RECEIVED_DATE desc';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $iCurrentPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../handle/approved','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'THU_LY', $arrRecordType,$arrInputfilter);
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
            $sRecordTypeId		= $Parameter['recordType'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
            unset($_SESSION['seArrParameter']);
        }
        //Day gia tri tim kiem ra view
        $this->view->sfullTextSearch = $sfullTextSearch;
        //C -> M: Truy van lay danh sach HS can giai quyet
        $arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
        //Lay file XML mo ta form danh sach
        $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';
        if(!file_exists($sXmlFileName)){
            $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';
        }
        $this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../handle/viewapproved');
        $iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = $ojbEfyLib->_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
        if (count($arrResult) > 0){
            $this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
        }else{
            $this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
        }
    }
    /**
     *
     */
   	public function backongateAction(){
        $objHandle	  			 = new record_modHandle();
        $objrecordfun = new Efy_Function_RecordFunctions();
        // Lay id ho so
        $sRecordIdList = $this->_request->getParam('hdn_object_id_list');
        $this->view->sRecordIdList = $sRecordIdList;
        $arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($sRecordIdList);
        $this->view->general_information = $objrecordfun->general_information($arrRecordInfo);
        $sRecordTypeId = '';
        if($arrRecordInfo){
            $sRecordTypeId = $arrRecordInfo[0]['FK_RECORDTYPE'];
        }
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $_SESSION['arr_all_record_type']);
        $this->view->arrinfoRecordType = $arrinfoRecordType;

        $this->view->AttachFile = $objrecordfun->DocSentAttachFile(array(),0,10,true,50);

        $supdate = trim($this->_request->getParam('hdn_update',""));
        if($supdate) {
            $ojbEfyLib = new Efy_Library();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = $this->_request->getParam('chk_process_type');
            $sStatus = '';
            $sDetailStatus = '';
            switch ($sWorkType) {
                case 'CHUYEN_MOT_CUA_TRA_KET_QUA':
                    $sStatus = 'CAP_PHEP';
                    $sDetailStatus = '41';
                    break;
                case 'TU_CHOI':
                    $sStatus = 'TU_CHOI';
                    $sDetailStatus = '41';
                    break;
            }
            //Lay thong tin file dinh kem
            $arrFileNameUpload = $ojbEfyLib->_uploadFileList(10, $this->_request->getBaseUrl() . "/public/attach-file/", 'FileName', '!#~$|*');
            $arrParameter = array(
                'PK_RECORD_LIST' => $sRecordIdList,
                'C_WORKTYPE' => $sWorkType,
                'C_SUBMIT_ORDER_CONTENT' => $idea,
                'FK_STAFF' => '',
                'C_POSITION_NAME' => '',
                'C_ROLES' => '',
                'C_LIMIT_DATE' => '',
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objHandle->eCSHandleTransitionUpdate($arrParameter);
            $this->_redirect('record/handle/result');
        }
	}

    /**
     *
     */
    public function transitionAction(){
        $this->view->titleBody = "DANH SÁCH HỒ SƠ LIÊN THÔNG CHỜ NHẬN";
        $objconfig = new Efy_Init_Config();
        $objrecordfun = new Efy_Function_RecordFunctions();
        $objxml = new Efy_Publib_Xml();
        $ojbEfyLib = new Efy_Library();

        $arrRecordType = $_SESSION['arr_all_record_type'];
        $sRecordTypeId = $this->_request->getParam('recordType');
        if($sRecordTypeId == "")
            $sRecordTypeId=$_SESSION['RECORD_TYPE'];
        if($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $_SESSION['RECORD_TYPE']=$sRecordTypeId;

        $iCurrentStaffId = $_SESSION['staff_id'];
        $sReceiveDate = '';
        $sStatusList = 'LIEN_THONG_CHO_NHAN';
        $sRole = '';
        $sOrderClause = '';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $iPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iPage <= 1){
            $iPage = 1;
        }
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0)
            $iNumberRecordPerPage = 15;

        //Neu ton tai gia tri tim kiem trong session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
            $sRecordTypeId		= $Parameter['sRecordTypeId'];
            $sfullTextSearch	= $Parameter['sfullTextSearch'];
            $iPage				= $Parameter['iPage'];
            $iNumberRecordPerPage = $Parameter['iNumberRecordPerPage'];
            unset($_SESSION['seArrParameter']);
        }
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $srecordType = $arrinfoRecordType['C_NAME'];
        $this->view->srecordType = $srecordType;
        $this->view->sRecordTypeCode = $sRecordTypeCode;
        $this->view->sRecordTypeId = $sRecordTypeId;

        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_lien_thong_cho_nhan.xml';
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_lien_thong_cho_nhan.xml';
        }

        //Day gia tri tim kiem ra view
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../handle/transition','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'THU_LY', $arrRecordType, $arrInputfilter);

        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $sDetailStatusCompare = " And A.C_DETAIL_STATUS = 10" ;
        $arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../handle/viewtransition');
        $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
        $this->view->iNumberRecord = $iNumberRecord;

        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = $ojbEfyLib->_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
        if (count($arrRecord) > 0){
            $this->view->sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
        }
    }
    /**
     *
     */
    public function confirmAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Efy_Function_RecordFunctions();
        // Lay id ho so
        $sRecordIdList = $this->_request->getParam('hdn_object_id_list');
        $this->view->sRecordIdList = $sRecordIdList;
        $arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($sRecordIdList);
        $this->view->general_information = $objrecordfun->general_information($arrRecordInfo);

        $supdate = trim($this->_request->getParam('hdn_update',""));
        if($supdate) {
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = 'PHAN_HOI_HS_LIEN_THONG';
            $arrParameter = array(
                'PK_RECORD_LIST' => $sRecordIdList,
                'C_WORKTYPE' => $sWorkType,
                'C_SUBMIT_ORDER_CONTENT' => $idea,
                'FK_STAFF' => '',
                'C_POSITION_NAME' => '',
                'C_ROLES' => '',
                'FK_UNIT' => '',
                'C_UNIT_NAME' => '',
                'C_STATUS' => '',
                'C_DETAIL_STATUS' => '',
                'NEW_FILE_ID_LIST' => '',
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSReceiveTransitionRecordUpdate($arrParameter);
            $this->_redirect('record/handle/transition');
        }
    }
}?>
