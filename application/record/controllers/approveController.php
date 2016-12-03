<?php

/**
 * Class record_approveController
 */
class record_approveController extends  Zend_Controller_Action {
	public function init(){
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		//Cau hinh cho Zend_layout
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout'],
			    'layout' => 'index'			    
			    ));
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
		Zend_Loader::loadClass('record_modApprove');
		Zend_Loader::loadClass('record_modHandle');
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
        $objLibrary = new Efy_Library();
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = $objLibrary->_getAllFileJavaScriptCss('','efy-js','/approve/approve.js,js-record/approve.js',',','js');
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
				$this->view->hideDisplayMeneLeft = 1;
			}else{
				$this->view->hideDisplayMeneLeft = "";
			}
			//Lay dia chi anh trong Cookie
			$this->view->ShowHideimageUrlPath = $objLibrary->_getCookie("ImageUrlPath");
		}
		
		//Dinh nghia current modul code
		$this->view->currentModulCode = "ASSIGN";
        $currentModulCodeForLeft = 'APPROVE';
        $sActionName = $this->_request->getActionName();
        switch ($sActionName) {
            case 'approved':
                $currentModulCodeForLeft = 'APPROVED';
                break;
            case 'viewapproved':
                $currentModulCodeForLeft = 'APPROVED';
                break;
        }
        $this->view->currentModulCodeForLeft = $currentModulCodeForLeft;
        //Hien thi file template
        $response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    //Hien thi header
        $response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    //Hien thi header
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	 //Hien thi footer
  	}	
	/**
	 * Idea : Phuong thuc hien thi danh sach
	 *
	 */
	public function indexAction(){
		$this->view->titleBody = "DANH S&#193;CH H&#7890; S&#416; CH&#7900; PH&#202; DUY&#7878;T";
		$objconfig = new Efy_Init_Config();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Efy_Publib_Xml();
        $objLibrary = new Efy_Library();

		$this->view->arrConst = $objconfig->_setProjectPublicConst();
		$arrRecordType = $_SESSION['arr_all_record_type'];
		$iCurrentStaffId = $_SESSION['staff_id'];
		$sRecordTypeId = $this->_request->getParam('recordType');
		if($sRecordTypeId == "")
			$sRecordTypeId=$_SESSION['RECORD_TYPE'];
		if($sRecordTypeId == "")
			$sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
		$_SESSION['RECORD_TYPE']=$sRecordTypeId;

		$sStatusList = 'TRINH_KY';
        $sDetailStatusCompare = " And (PK_RECORD in (Select FK_RECORD From T_eCS_RECORD_RELATE_STAFF Where C_STATUS = ''DANG_XL'' And FK_STAFF = ''".$iCurrentStaffId."'')) ";

        $sReceiveDate = '';
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
		//Neu ton tai gia tri tim kiem tron session thi lay trong session
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
		//echo '<br>'.$sRecordTypeId;
		$this->view->sRecordTypeId = $sRecordTypeId;
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';	
		}
		//Day gia tri tim kiem ra view
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../approve/index','RecordTypeId'=>$sRecordTypeId);
		$this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'PHE_DUYET', $arrRecordType, $arrInputfilter);
		// Goi ham search de hien thi ra Complete Textbox
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
		$this->view->arrRecord = $arrRecord;
		$this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../approve/viewindex');
		$iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
		$this->view->iNumberRecord = $iNumberRecord;
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = $objLibrary->_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
		if (count($arrRecord) > 0){
			$this->view->sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = $objLibrary->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = $objLibrary->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
		}
	}
    public function approvedAction(){
        $this->view->titleBody = "DANH S&#193;CH H&#7890; S&#416; ĐÃ PH&#202; DUY&#7878;T";
        $objconfig = new Efy_Init_Config();
        $objrecordfun = new Efy_Function_RecordFunctions();
        $objxml = new Efy_Publib_Xml();
        $objLibrary = new Efy_Library();

        $this->view->arrConst = $objconfig->_setProjectPublicConst();
        $arrRecordType = $_SESSION['arr_all_record_type'];
        $iCurrentStaffId = $_SESSION['staff_id'];
        $sRecordTypeId = $this->_request->getParam('recordType');
        if($sRecordTypeId == "")
            $sRecordTypeId=$_SESSION['RECORD_TYPE'];
        if($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $_SESSION['RECORD_TYPE']=$sRecordTypeId;

        $sStatusList = '';
        $sDetailStatusCompare = " And (PK_RECORD in (Select FK_RECORD From T_eCS_RECORD_RELATE_STAFF Where C_STATUS = ''KET_THUC_XL'' And FK_STAFF = ''".$iCurrentStaffId."'')) ";

        $sReceiveDate = '';
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
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
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
        //echo '<br>'.$sRecordTypeId;
        $this->view->sRecordTypeId = $sRecordTypeId;
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';
        }
        //Day gia tri tim kiem ra view
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../approve/approved','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'PHE_DUYET', $arrRecordType, $arrInputfilter);
        // Goi ham search de hien thi ra Complete Textbox
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
        $this->view->arrRecord = $arrRecord;
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../approve/viewapproved');
        $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
        $this->view->iNumberRecord = $iNumberRecord;
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = $objLibrary->_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
        if (count($arrRecord) > 0){
            $this->view->sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = $objLibrary->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = $objLibrary->_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
        }
    }
    /**
     *
     */
    public function approveAction(){
        $objconfig = new Efy_Init_Config();
        $objrecordfun = new Efy_Function_RecordFunctions();
        $objApprove = new record_modApprove();

        $this->view->arrConst = $objconfig->_setProjectPublicConst();

        $sRecordIdList = $this->_request->getParam('hdn_object_id_list');
        if(trim($sRecordIdList) == '') $sRecordIdList = $this->_request->getParam('hdn_object_id','');
        $this->view->sRecordIdList = $sRecordIdList;
        $arrRecordInfo = $objrecordfun->eCSGetInfoRecordFromListId($sRecordIdList, $_SESSION['OWNER_CODE']);
        $this->view->general_information = $objrecordfun->general_information($arrRecordInfo);

        $sRecordTypeId = '';
        if($arrRecordInfo){
            $sRecordTypeId = $arrRecordInfo[0]['FK_RECORDTYPE'];
        }
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $_SESSION['arr_all_record_type']);
        $this->view->arrinfoRecordType = $arrinfoRecordType;

        //Lay quyen phe duyet cua NSD tren mot TTHC
        $iUserId = $_SESSION['staff_id'];
        $sApproveLevel = $objrecordfun->eCSGetPermisstionApproveForRecordType2($iUserId, $_SESSION['arr_all_record_type'], $sRecordTypeId);
        $this->view->sApproveLevel = $sApproveLevel;

        $this->view->AttachFile = $objrecordfun->DocSentAttachFile(array(),0,10,true,50);

        $supdate = trim($this->_request->getParam('hdn_update',""));
        if($supdate) {
            $ojbEfyLib = new Efy_Library();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');

            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = $this->_request->getParam('chk_process_type');
            $sStatus = '';
            $sDetailStatus = '';
            $sHandleid = '';
            $sHandlename = '';
            $sRole = '';
            switch ($sWorkType) {
                case 'TRINH_LD_DONVI':
                    $sStatus = 'TRINH_KY';
                    $sDetailStatus = '41';
                    $sHandleid = $this->_request->getParam('chk_LeaderLv3');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'DUYET_CAP_BA';
                    break;
                case 'TRINH_LD_PHONG':
                    $sStatus = 'TRINH_KY';
                    $sDetailStatus = '31';
                    $sHandleid = $this->_request->getParam('chk_LeaderLv2');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'DUYET_CAP_HAI';
                    break;
                case 'LD_DONVI_CAPPHEP':
                    $sStatus = 'CAP_PHEP';
                    $sDetailStatus = '41';
                    break;
                case 'LD_PHONG_CAPPHEP':
                    $sStatus = 'CAP_PHEP';
                    $sDetailStatus = '41';
                    break;
                case 'DUYET_CHUYEN_PHONG_BAN':
                    $sStatus = 'CAP_PHEP';
                    $sDetailStatus = '21';
                    break;
                case 'LD_PHONG_TRALAI':
                    $sStatus = 'TRA_LAI';
                    $sDetailStatus = '21';
                    break;
                case 'CHUYEN_CAN_BO_XL':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    $sRole = 'THULY_CHINH';
                    break;
                case 'LD_PHONG_TUCHOI':
                    $sStatus = 'TU_CHOI';
                    $sDetailStatus = '10';
                    break;
                case 'LD_DONVI_TUCHOI':
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
                'FK_STAFF' => $sHandleid,
                'C_POSITION_NAME' => $sHandlename,
                'C_ROLES' => $sRole,
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objApprove->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/approve/index');
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
}?>