<?php

/**
 * Class record_assignController
 */
class record_assignController extends  Zend_Controller_Action {
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
		$this->view->delimitor 			= "!~~!";	
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();	
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
			
		//Goi lop Listxml_modList
		Zend_Loader::loadClass('record_modAssign');
		
		//Lay cac hang so su dung trong JS public
		$objConfig = new Efy_Init_Config();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		//Tao doi tuong XML
		Zend_Loader::loadClass('Efy_Publib_Xml');
        $objLibrary = new Efy_Library();
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = $objLibrary->_getAllFileJavaScriptCss('','efy-js','recordtype/recordtype.js,js-record/approve.js,xml/general_datatable.js',',','js');
		//Dinh nghia current modul code
		$this->view->currentModulCode = "ASSIGN";
        $currentModulCodeForLeft = 'ASSIGN-RECORD';
        $sActionName = $this->_request->getActionName();
        switch ($sActionName) {
            case 'assigned':
                $currentModulCodeForLeft = 'ASSIGNED-RECORD';
                break;
            case 'viewassigned':
                $currentModulCodeForLeft = 'ASSIGNED-RECORD';
                break;
        }
        $this->view->currentModulCodeForLeft = $currentModulCodeForLeft;

        //Lay tra tri trong Cookie
		$sGetValueInCookie = $objLibrary->_getCookie("showHideMenu");
		//Neu chua ton tai thi khoi tao
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
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    	//Hien thi header 
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    		//Hien thi header 		    
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  
	}
	/**
	 * Idea : Phuong thuc hien thi danh sach
	 */
	public function indexAction(){	
		//Goi cac doi tuong
		$objInitConfig 			 = new Efy_Init_Config();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
        $sStatus =  "CHO_PHAN_CONG";
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; PH&#194;N C&#212;NG TH&#7908; L&#221;';

		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
		$arrRecordType = $_SESSION['arr_all_record_type'];
		$sRecordTypeId = $this->_request->getParam('recordType');
		if($sRecordTypeId == "")
			$sRecordTypeId=$_SESSION['RECORD_TYPE'];
		if($sRecordTypeId == "") 
			$sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
		$_SESSION['RECORD_TYPE']=$sRecordTypeId;
		$arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$iCurrentStaffId = $_SESSION['staff_id'];
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$sReceiveDate = '';
		$sStatusList = $sStatus ;
		$sDetailStatusCompare = "And ''$iFkUnit'' in (select FK_UNIT from T_eCS_RECORD_RELATE_UNIT where FK_RECORD = A.PK_RECORD)";
		$sRole = '';
		$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1; 
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../assign/index','RecordTypeId'=>$sRecordTypeId);
		//Hien thi form tim kiem
		$this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'PHE_DUYET', $arrRecordType,$arrInputfilter);
		//Neu ton tai gia tri tim kiem tron session thi lay trong session
		if(isset($_SESSION['seArrParameter'])){
			$Parameter 			= $_SESSION['seArrParameter'];
			$sRecordTypeId		= $Parameter['recordType'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
			unset($_SESSION['seArrParameter']);
		}
		//Day gia tri tim kiem ra view
		$this->view->sfullTextSearch = $sfullTextSearch;
		//C -> M: Truy van lay danh sach HS phan cong thu ly hien thi ra man hinh
		$arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
		//Lay file XML mo ta form danh sach
		$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_phan_cong_thu_ly.xml';
		if(!file_exists($sXmlFileName)){
			$sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_phan_cong_thu_ly.xml';	
		}		
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../assign/viewindex');
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];	
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = Efy_Publib_Library::_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = Efy_Publib_Library::_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = Efy_Publib_Library::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}else{
			$this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
		}
	}
	/**
	 * Idea : Phuong thuc cap nhat phan cong thu ly
	 */
	public function addAction(){
        Zend_Loader::loadClass('record_modHandle');
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
                case 'PHAN_CONG':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'THULY_CHINH';
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
            $this->_redirect('record/assign/index');
        }
	}

    /**
     *
     */
    public function assignedAction(){
        //Goi cac doi tuong
        $objInitConfig 			 = new Efy_Init_Config();
        $objRecordFunction	     = new Efy_Function_RecordFunctions();
        $objXml					 = new Efy_Publib_Xml();
        $sStatus =  "";
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; ĐÃ PH&#194;N C&#212;NG TH&#7908; L&#221;';

        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        $arrRecordType = $_SESSION['arr_all_record_type'];
        $sRecordTypeId = $this->_request->getParam('recordType');
        if($sRecordTypeId == "")
            $sRecordTypeId=$_SESSION['RECORD_TYPE'];
        if($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $_SESSION['RECORD_TYPE']=$sRecordTypeId;
        $arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $iCurrentStaffId = $_SESSION['staff_id'];
        $sReceiveDate = '';
        $sStatusList = $sStatus ;
        $sDetailStatusCompare = "And A.PK_RECORD in (Select [FK_RECORD] from [T_eCS_RECORD_WORK] where [C_WORKTYPE] = ''PHAN_CONG'' and [FK_STAFF] = ''$iCurrentStaffId'')";
        $sRole = '';
        $sOrderClause = 'order by  C_RECEIVED_DATE desc';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $iCurrentPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../assign/assigned','RecordTypeId'=>$sRecordTypeId);
        //Hien thi form tim kiem
        $this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId, 'PHE_DUYET', $arrRecordType,$arrInputfilter);
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
            $sRecordTypeId		= $Parameter['recordType'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
            unset($_SESSION['seArrParameter']);
        }
        //Day gia tri tim kiem ra view
        $this->view->sfullTextSearch = $sfullTextSearch;
        //C -> M: Truy van lay danh sach HS phan cong thu ly hien thi ra man hinh
        $arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
        //Lay file XML mo ta form danh sach
        $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_phan_cong_thu_ly.xml';
        if(!file_exists($sXmlFileName)){
            $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_phan_cong_thu_ly.xml';
        }
        $this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../assign/viewassigned');
        $iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = Efy_Publib_Library::_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
        if (count($arrResult) > 0){
            $this->view->sdocpertotal = "Danh sách có: ".sizeof($arrResult).'/'.$iTotalRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = Efy_Publib_Library::_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = Efy_Publib_Library::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
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
    public function viewassignedAction(){
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
}?>