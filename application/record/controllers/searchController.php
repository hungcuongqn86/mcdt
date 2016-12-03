<?php

/**
 * Class record_searchController
 */
class record_searchController extends  Zend_Controller_Action {
	public function init(){
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();

        //Ky tu dac biet phan tach giua cac phan tu
        $this->view->delimitor = "!~~!";
        //Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
        $this->_ConstPublic = $tempConstPublic->toArray();

        //Lay duong dan thu muc goc (path directory root)
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";

        //Goi lop Listxml_modList
        Zend_Loader::loadClass('record_modSearch');

        //Lay cac hang so su dung trong JS public
        $objConfig = new Efy_Init_Config();
        $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();

        $objLibrary = new Efy_Library();
        $sStyle = $objLibrary->_getAllFileJavaScriptCss('', 'efy-js', 'js-record/search.js', ',', 'js');
        $this->view->LoadAllFileJsCss = $sStyle;

        //Dinh nghia current modul code
        $this->view->currentModulCode = "SEARCH";
        $sActionName = $this->_request->getActionName();
        $this->view->currentModulCodeForLeft = $sActionName;
        //Lay tra tri trong Cookie
        $sGetValueInCookie = $objLibrary->_getCookie("showHideMenu");

        //Neu chua ton tai thi khoi tao
        if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)) {
            $objLibrary->_createCookie("showHideMenu", 1);
            $objLibrary->_createCookie("ImageUrlPath", $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif");
            //Mac dinh hien thi menu trai
            $this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
            //Hien thi anh dong menu trai
            $this->view->ShowHideimageUrlPath = $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif";
        } else {
            if ($sGetValueInCookie != 0) {
                $this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
            } else {
                $this->view->hideDisplayMeneLeft = "";// = "" : an menu
            }
            //Lay dia chi anh trong Cookie
            $this->view->ShowHideimageUrlPath = $objLibrary->_getCookie("ImageUrlPath");
        }

        if (!$this->_request->isXmlHttpRequest()) {
            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => $this->_dirApp['layout'],
                'layout' => 'index'
            ));
            //Load ca thanh phan cau vao trang layout (index.phtml)
            $response = $this->getResponse();
            //Hien thi file template
            $response->insert('header', $this->view->renderLayout('header.phtml', './application/views/scripts/'));        //Hien thi header
            $response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));            //Hien thi header
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/views/scripts/'));
        }
	}
	/**
	 * Idea : Phuong thuc hien thi danh sach
	 *
	 */
	public function indexAction(){
        $objRecordFunction	     = new Efy_Function_RecordFunctions();
        $objInitConfig 			 = new Efy_Init_Config();
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        $this->view->bodyTitle = 'TRA C&#7912;U H&#7890; S&#416; ';
        //Mang danh sach cac don vi
        $this->view->arrUnit = $_SESSION['SesGetAllOwner'];
        $this->view->arrPeriod = $objRecordFunction->getAllObjectbyListCode('',"DANH_MUC_TRANG_THAI");
        $dFromReceiveDate =date('01/01/Y');
        $dToReceiveDate =date('d/m/Y');
        $this->view->dFromReceiveDate = $dFromReceiveDate;
        $this->view->dToReceiveDate = $dToReceiveDate;
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $sOwnerCodeRoot = $objInitConfig->_getOwnerCode();
        if($sOwnerCode==$sOwnerCodeRoot){
            $this->view->checkward = 1;
        }else{
            $this->view->checkward = 0;
        }
        $this->view->sOwnerCode = $sOwnerCode;
	}
    /**
     *
     */
    public function loadfilterAction(){
        $objInitConfig 			 = new Efy_Init_Config();
        $arrInput                = $this->_request->getParams();
        Zend_Loader::loadClass('listxml_modRecordtype');
        $objRecordtype	        = new listxml_modRecordtype();
        $sRecordTypeId = $arrInput['recordtype'];
        $sownercode = $arrInput['ownercode'];
        $arrResult = $objRecordtype->eCSRecordTypeGetSingle($sRecordTypeId,$sownercode);
        $sRecordTypeCode = $arrResult['C_CODE'];
        //Lay file XML mo ta form cac tieu thuc loc phuc vu tim kiem nang cao
        $sSearchXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';
        if(!file_exists($sSearchXmlFileName)){
            $sSearchXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/tim_kiem_nang_cao.xml';
        }
        $sFilterXmlString = '<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>';
        $objXml	= new Efy_Publib_Xml();
        $shtml = $objXml->_xmlGenerateFormfield($sSearchXmlFileName, 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row','list_of_object/filter_formfield_list',$sFilterXmlString,null,true,false);
        echo $shtml;exit;
    }
    /**
     *
     */
    public function loadlistrecordAction(){
        $arrInput                = $this->_request->getParams();
        $objInitConfig 			 = new Efy_Init_Config();
        $ojbEfyLib				 = new Efy_Library();
        Zend_Loader::loadClass('listxml_modRecordtype');
        $objRecordtype	        = new listxml_modRecordtype();
        $sRecordTypeId = $arrInput['recordType'];
        $sownercode = $arrInput['sOwnerCode'];
        $arrResult = $objRecordtype->eCSRecordTypeGetSingle($sRecordTypeId,$sownercode);
        $sRecordTypeCode = $arrResult['C_CODE'];

        $objXml	= new Efy_Publib_Xml();
        //Lay file XML mo ta form cac tieu thuc loc phuc vu tim kiem nang cao
        $sSearchXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';
        if(!file_exists($sSearchXmlFileName)){
            $sSearchXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/tim_kiem_nang_cao.xml';
        }
        //Lay file XML mo ta form danh sach ho so tim kiem
        $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_ho_so_tim_kiem.xml';
        if(!file_exists($sXmlFileName)){
            $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_ho_so_tim_kiem.xml';
        }
        $sRecordType = $arrInput['recordType'];
        $dFromReceiveDate = $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dFromReceiveDate']);
        $dToReceiveDate = $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dToReceiveDate'],2);
        $sFulltextsearch = $arrInput['sFullTextSearch'];
        $sStatus = $arrInput['sStatus'];
        $sOwnerCode = $arrInput['sOwnerCode'];
        $sXmlTagList = $arrInput['hdn_filter_xml_tag_list'];
        $sXmlValueList = $arrInput['hdn_filter_xml_value_list'];
        $sDelimetor = '!~~!';
        //Lay the chua noi dung toan tu
        $sXmlOperatorList = $objXml->_xmlGetXmlTagValueFromFile($sSearchXmlFileName,'list_of_object','filter_formfield_list',$sXmlTagList,"compare_operator",$sDelimetor);
        $sXmlTrueFalseList = $objXml->_xmlGetXmlTagValueFromFile($sSearchXmlFileName,'list_of_object','filter_formfield_list',$sXmlTagList,"xml_data",$sDelimetor);
        $iCurrentPage		= $arrInput['hdn_current_page'];
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $arrInput['hdn_record_number_page'];
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $objSearch				 = new Record_modSearch();
        if($sStatus == 'TRA_KET_QUA')  $sStatus = 'CAP_PHEP,TU_CHOI';
        $arrResult = array();
        $arrRecord = $objSearch->eCSSearchGetAll($sRecordTypeId,$sRecordType,$dFromReceiveDate,$dToReceiveDate,$sFulltextsearch,$sStatus,$sOwnerCode,$sXmlTagList,$sXmlValueList,$sXmlOperatorList,$sXmlTrueFalseList,$sDelimetor,$iCurrentPage,$iNumberRecordPerPage);
        $genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sFulltextsearch,false,false,$sAction = '../viewrecord/status/index/');
        $arrResult[0]=sizeof($arrRecord);
        $arrResult[1]=$arrRecord[0]['C_TOTAL_RECORD'];
        $arrResult[2]=$genlist;
        echo Zend_Json::encode($arrResult);
        exit;
    }

    /**
     *
     */
    public function loadcateAction(){
        $arrInput                = $this->_request->getParams();
        $objRecordFunction	     = new Efy_Function_RecordFunctions();
        $arrCate = $objRecordFunction->getAllObjectbyListCode($arrInput['ownercode'],"DANH_MUC_LINH_VUC");
        echo Zend_Json::encode($arrCate);
        exit;
    }

    /**
     *
     */
    public function loadrecordtypeAction(){
        $arrInput                = $this->_request->getParams();
        Zend_Loader::loadClass('listxml_modRecordtype');
        $objRecordtype	        = new listxml_modRecordtype();
        $arrRecordType          = $objRecordtype->eCSRecordTypeGetAll($arrInput['ownercode'],'',$arrInput['catecode'],'HOAT_DONG');
        echo Zend_Json::encode($arrRecordType);
        exit;
    }

    /**
     *
     */
	public function generalAction(){
		$objRecordFunction	     = new Efy_Function_RecordFunctions();
        $objconfig = new Efy_Init_Config();
		//Lay Danh muc TTHC
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $sOwnerCodeRoot = $objconfig->_getOwnerCode();
        if($sOwnerCode==$sOwnerCodeRoot){
            $this->view->checkward = 1;
        }else{
            $this->view->checkward = 0;
        }
        $this->view->sOwnerCode = $sOwnerCode;
		$arrCate = $objRecordFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_LINH_VUC");
		$this->view->arrCate = $arrCate;
        //Don vi trien khai
        $arrOwner = $_SESSION['SesGetAllOwner'];
        $this->view->arrOwner = Zend_Json::encode($arrOwner);
        // Request cac tieu chi tim kiem:
        $this->view->dFromDate = date('01/01/Y');
        $this->view->dToDate = date('d/m/Y');
	}

    /**
     *
     */
    public function loaddataAction(){
        $arrInput                = $this->_request->getParams();
        $ojbEfyLib				 = new Efy_Library();
        $objSearch				 = new Record_modSearch();
        $mode = $arrInput['mode'];
        $arrResult = array();
        if($mode=='unit'){
            $arrParameter = array(
                'C_FROM_DATE'	=> $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dFromDate'],1),
                'C_TO_DATE'     => $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dToDate'],1),
                'C_CATE'        => $arrInput['CateId'],
                'C_OWNER_CODE'  => $_SESSION['OWNER_CODE']
            );
            $arrResult = $objSearch->eCSSearchGeneralGetAllUnit($arrParameter);
        }else{
            $arrParameter = array(
                'C_FROM_DATE'	=> $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dFromDate'],1),
                'C_TO_DATE'     => $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dToDate'],1),
                'C_CATE'        => $arrInput['CateId'],
                'C_OWNER_CODE'  => $arrInput['ownercode']
            );
            $arrResult = $objSearch->eCSSearchGeneralGetAllRecordType($arrParameter);
        }
        echo Zend_Json::encode($arrResult);
        exit;
    }

    /**
     *
     */
    public function loadrecordAction(){
        $arrInput                = $this->_request->getParams();
        $ojbEfyLib				 = new Efy_Library();
        $objSearch				 = new Record_modSearch();
        $iCurrentPage		     = $arrInput['hdn_current_page'];
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage    = $arrInput['hdn_record_number_page'];
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $arrParameter = array(
            'C_TYPE'             => $arrInput['type'],
            'C_FROM_DATE'	     => $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dFromDate'],1),
            'C_TO_DATE'          => $ojbEfyLib->_ddmmyyyyToYYyymmdd($arrInput['dToDate'],1),
            'C_CATE'             => $arrInput['CateId'],
            'C_RECORDTYPE'       => $arrInput['recordtype'],
            'C_OWNER_CODE'       => $arrInput['ownercode'],
            'C_PAGE'             => $iCurrentPage,
            'C_RECORD_PER_PAGE'  => $iNumberRecordPerPage
        );
        $arrResult = $objSearch->eCSSearchRecordGetAll($arrParameter);
        echo Zend_Json::encode($arrResult);
        exit;
    }

    /**
     *
     */
    public function loadviewAction(){
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
        $sRecordPk = $this->_request->getParam('recordid','');
        $sOwnerCode = $this->_request->getParam('ownercode','');
        //Lay mang cac cong viec tien do
        $this->view->arrWork = $objHandle->eCSHandleWorkGetAll($sRecordPk,'');
        //Thong tin ho so
        $arrRecord=$objSearch->eCSSearchGetSingle($sRecordPk,$sOwnerCode);
        $arFileAttach = $objSearch->DOC_GetAllDocumentFileAttach($sRecordPk, 'HO_SO', 'T_eCS_RECORD');
        $arrRecord['file'] = $arFileAttach;
        $this->view->generalhtmlinfo = $objrecordfun->generalhtmlinfo($arrConst,$arrRecord);
    }
}?>