<?php

/**
 * Class record_wreceiveController
 */
class record_wreceiveController extends  Zend_Controller_Action {
	public function init(){
		//Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
        Zend_Loader::loadClass('Extra_Init');
        //Goi lop modRecord
        Zend_Loader::loadClass('record_modReceive');
        if (!$this->_request->isXmlHttpRequest()) {
            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => $this->_dirApp['layout'],
                'layout' => 'index'
            ));
            //Load ca thanh phan cau vao trang layout (index.phtml)
            $response = $this->getResponse();
            //Lay cac hang so su dung trong JS public
            $objConfig = new Extra_Init();
            $this->view->UrlAjax = $objConfig->_setUrlAjax();
            $this->view->arrConst = $objConfig->_setProjectPublicConst();
            //Lay duong dan thu muc goc (path directory root)
            $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";

            $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
            // Load tat ca cac file Js va Css
            $objLibrary = new Extra_Util();
            $sStyle = $objLibrary->_getAllFileJavaScriptCss('', 'js', 'js-record/wreceive.js,xml/general_datatable.js', ',', 'js');
            $sStyle.= $objLibrary->_getAllFileJavaScriptCss('','style','printmenu/printmenu.css',',','css');
            $this->view->LoadAllFileJsCss = $sStyle;
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
            } else {//Da ton tai Cookie
                if ($sGetValueInCookie != 0) {
                    $this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
                } else {
                    $this->view->hideDisplayMeneLeft = "";// = "" : an menu
                }
                //Lay dia chi anh trong Cookie
                $this->view->ShowHideimageUrlPath = $objLibrary->_getCookie("ImageUrlPath");
            }
            //Dinh nghia current modul code
            $this->view->currentModulCode = "RECORD";
            $sActionName = $this->_request->getActionName();
            $currentModulCodeForLeft = 'RECEIVED-RECORD';
            switch ($sActionName) {
                case 'transition':
                    $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                    break;
                case 'viewtransition':
                    $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                    break;
                case 'additional':
                    $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                    break;
                case 'submitorderadd':
                    $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                    break;
                case 'forwardadd':
                    $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                    break;
                case 'update':
                    $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                    break;
                case 'viewadditional':
                    $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                    break;
                case 'processing':
                    $currentModulCodeForLeft = 'RECEIVED-PROCESSING';
                    break;
                case 'viewprocessing':
                    $currentModulCodeForLeft = 'RECEIVED-PROCESSING';
                    break;
                case 'result':
                    $currentModulCodeForLeft = 'RECEIVED-RESULT';
                    break;
                case 'viewresult':
                    $currentModulCodeForLeft = 'RECEIVED-RESULT';
                    break;
                case 'updateresult':
                    $currentModulCodeForLeft = 'RECEIVED-RESULT';
                    break;
                case 'mailresult':
                    $currentModulCodeForLeft = 'RECEIVED-RESULT';
                    break;
            }
            $this->view->currentModulCodeForLeft = $currentModulCodeForLeft;

            $response->insert('header', $this->view->renderLayout('header.phtml', './application/views/scripts/'));      //Hien thi header
            $response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));          //Hien thi header
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/views/scripts/'));     //Hien thi footer
        }
  	}

    /**
     *
     */
	public function indexAction(){
		$this->view->titleBody = "DANH S&#193;CH H&#7890; S&#416; &#272;&#195; TI&#7870;P NH&#7852;N";
		$objconfig = new Extra_Init();
		$objrecordfun = new Extra_Ecs();
		$objxml = new Extra_Xml();
        $ojbEfyLib = new Extra_Util();

		$arrRecordType = $_SESSION['arr_all_record_type'];
		$sRecordTypeId = $this->_request->getParam('recordType');
		if($sRecordTypeId == "")
			$sRecordTypeId=$_SESSION['RECORD_TYPE'];
		if($sRecordTypeId == "")
			$sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
		$_SESSION['RECORD_TYPE']=$sRecordTypeId;

		$iCurrentStaffId = $_SESSION['staff_id'];
		$sReceiveDate = '';
		$sStatusList = 'MOI_TIEP_NHAN';
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

		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';	
		}

		//Day gia tri tim kiem ra view
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../wreceive/index','RecordTypeId'=>$sRecordTypeId);
		$this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);

        //Lay cac mau in
        $this->view->str_print = $objxml->genEcsPrintGenerate($sxmlFileName);

		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$sDetailStatusCompare = " And A.C_DETAIL_STATUS = 10" ;
		$arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
		$this->view->arrRecord = $arrRecord;
		$this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../wreceive/viewindex');
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
     * @throws Zend_Exception
     */
    public function viewindexAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');
        Zend_Loader::loadClass('Zend_Session_Namespace');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Extra_Ecs();
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
    public function viewadditionalAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');
        Zend_Loader::loadClass('Zend_Session_Namespace');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Extra_Ecs();
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
    public function viewprocessingAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');
        Zend_Loader::loadClass('Zend_Session_Namespace');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Extra_Ecs();
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
    public function viewtransitionAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');
        Zend_Loader::loadClass('Zend_Session_Namespace');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Extra_Ecs();
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
    public function viewresultAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');
        Zend_Loader::loadClass('Zend_Session_Namespace');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objHandle	  			 = new record_modHandle();
        $objSearch				 = new record_modReceive();
        $objrecordfun            = new Extra_Ecs();
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
  	public function addAction(){
		$this->view->titleBody = "TI&#7870;P NH&#7852;N H&#7890; S&#416; M&#7898;I";
		$objconfig = new Extra_Init();
		$objrecordfun = new Extra_Ecs();
		$objxml = new Extra_Xml();
		$ojbEfyLib = new Extra_Util();
		$objReceive = new record_modReceive();
		//Lay thong tin cua loai ho so TTHC
		$arrRecordType = $objrecordfun->eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
		//Lay id TTHC tu trang index
		$sRecordTypeId = $this->_request->getParam('recordType');
		//Tu hidden
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('hdn_record_type_id');
		//Tren Url
		if($sRecordTypeId == '')
			$sRecordTypeId =  $this->_request->getParam('r');
		$this->view->RecordTypeId = $sRecordTypeId;
		$arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
  		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';	
		}
		//echo $sxmlFileName;
		$this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
		$this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];
		$this->view->iNumberProcessDate = $arrinfoRecordType['C_WARDS_PROCESS_NUMBER_DATE'];
		//echo '$sxmlFileName:'.$sxmlFileName;
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', '<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>', array(),true,true);
		//Lay thong tin file dinh kem
		$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
		$arFileAttach = array();	
		$this->view->AttachFile = $objrecordfun->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,50);	
		$sOption = $this->_request->getParam('hdh_option','');
		$this->view->option = $sOption;
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
		//Kiem tra neu form da duoc submit
		if($this->_request->getParam('hdn_is_update','') == '1'){
			//Tao xau xml luu vao database
			$strXml = '<root><data_list>';
			$sXmlTags = $this->_request->getParam('hdn_xml_tag_list','');
			$sXmlValues = $this->_request->getParam('hdn_xml_value_list','');

			$arrXmlTags = explode('!~~!', $sXmlTags);
			$arrXmlValues = explode('!~~!', $sXmlValues);
			for ($i = 0; $i < sizeof($arrXmlTags); $i++)
				$strXml .= '<' . $arrXmlTags[$i] . '>' . $ojbEfyLib->_replaceBadChar($arrXmlValues[$i]) . '</' . $arrXmlTags[$i] . '>';
			$strXml = $strXml . "</data_list></root>";
			
			$arrParameter = array(	
				'PK_RECORD'	=> '',
				'C_CODE' => $this->_request->getParam('C_CODE',''),
				'FK_RECORDTYPE'	=> $sRecordTypeId,	
				'FK_RECEIVER' => $_SESSION['staff_id'],			
				'C_RECEIVER_POSITION_NAME' => $objrecordfun->getNamePositionStaffByIdList($_SESSION['staff_id']),
				'C_RECEIVED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_RECEIVED_DATE','')),
				'C_APPOINTED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_APPOINTED_DATE','')),
				'C_CURRENT_STATUS' => 'MOI_TIEP_NHAN',
				'C_DETAIL_STATUS' => '10',
				'C_RECEIVED_RECORD_XML_DATA' => $strXml,
				'C_LICENSE_XML_DATA' => '',
				'C_OWNER_CODE' =>$_SESSION['OWNER_CODE'],																	
				'NEW_FILE_ID_LIST'=>$arrFileNameUpload,
			);	
			$arrResult = $objReceive->eCSRecordUpdate($arrParameter);
			//thuc hien update file dinh kem
			$v_list_attach_value = "";
			if(isset($_REQUEST['hdn_list_attach_value'])){
				$v_list_attach_value = $_REQUEST['hdn_list_attach_value'];
			}
			$sUrlFileAttach=$objconfig->_setAttachFileUrlPath();
			$arrFileNameAttach = $objrecordfun->_uploadFileAttachList($v_list_attach_value,$sUrlFileAttach,'dk_file_attach',',');
			$sRecordID=$arrResult['NEW_ID'];			
			$objReceive->eCSUpdateFileAttach($sRecordID,$arrFileNameAttach,'T_eCS_RECORD');
			//Xu ly cac truong hop NSD luu du lieu
			if ($sOption == "GHI_THEMMOI")	
				$this->_redirect('record/wreceive/add?r='.$sRecordTypeId);
			if ($sOption == "GHI_QUAYLAI")				
				$this->_redirect('record/wreceive/index');
		}
	}

    /**
     *
     */
	public function editAction(){
		$this->view->titleBody = "TH&#212;NG TIN H&#7890; S&#416;";
		$objconfig = new Extra_Init();
		$objrecordfun = new Extra_Ecs();
		$objxml = new Extra_Xml();
		$ojbEfyLib = new Extra_Util();
		$objReceive = new record_modReceive();

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
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';	
		}
		$this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];
		//Lay thong tin cua mot ho so
		$srecordId = $this->_request->getParam('hdn_object_id','');
		$this->view->srecordId = $srecordId;
		$arrSingleRecord = $objrecordfun->eCSRecordGetSingle($srecordId, $_SESSION['OWNER_CODE'],'');
		$this->view->arrSingleRecord = $arrSingleRecord;
		$this->view->RecodeCode = $arrSingleRecord[0]['C_CODE'];
		if($this->_request->getParam('rc') != '')
			$this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
		//Lay thong tin file dinh kem
		$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
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

		//Xu ly truong hop neu la ghi va them tiep thi ko lay thong tin file dinh kem
		$sOption = $this->_request->getParam('hdh_option','');
		$this->view->option = $sOption;
		if ($sOption == "QUAY_LAI")
			$this->_redirect('record/wreceive/index');
        $arFileAttach = $objReceive->DOC_GetAllDocumentFileAttach($srecordId, 'HO_SO', 'T_eCS_RECORD');
        $this->view->AttachFile = $objrecordfun->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,50);

		//Kiem tra neu form da duoc submit
		if($this->_request->getParam('hdn_is_update','') == '1'){
			//Tao xau xml luu vao database
			$strXml = '<root><data_list>';
			$sXmlTags = $this->_request->getParam('hdn_xml_tag_list','');
			$sXmlValues = $this->_request->getParam('hdn_xml_value_list','');
			$arrXmlTags = explode('!~~!', $sXmlTags);
			$arrXmlValues = explode('!~~!', $sXmlValues);
			for ($i = 0; $i < sizeof($arrXmlTags); $i++)
				$strXml .= '<' . $arrXmlTags[$i] . '>' . $ojbEfyLib->_replaceBadChar($arrXmlValues[$i]) . '</' . $arrXmlTags[$i] . '>';
			$strXml = $strXml . "</data_list></root>";
			if ($sOption == "GHI_THEMTIEP")
				$srecordId = "";
			
			$arrParameter = array(	
				'PK_RECORD'	=> $srecordId,
				'C_CODE' => $this->_request->getParam('C_CODE',''),
				'FK_RECORDTYPE'	=> $sRecordTypeId,	
				'FK_RECEIVER' => $_SESSION['staff_id'],
                'C_RECEIVER_POSITION_NAME' => $objrecordfun->getNamePositionStaffByIdList($_SESSION['staff_id']),
				'C_RECEIVED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_RECEIVED_DATE','')),
				'C_APPOINTED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_APPOINTED_DATE','')),
				'C_CURRENT_STATUS' => 'MOI_TIEP_NHAN',
				'C_DETAIL_STATUS' => '10',
				'C_RECEIVED_RECORD_XML_DATA' => $strXml,
				'C_LICENSE_XML_DATA' => '',
				'C_OWNER_CODE' =>$_SESSION['OWNER_CODE'],
				'NEW_FILE_ID_LIST'=>$arrFileNameUpload,
			);	
			//Cap nhat du lieu
			$arrResult = $objReceive->eCSRecordUpdate($arrParameter);
            //var_dump($arrResult);exit;
			//thuc hien update file dinh kem
			$v_list_attach_value = "";
			if(isset($_REQUEST['hdn_list_attach_value'])){
				$v_list_attach_value = $_REQUEST['hdn_list_attach_value'];
			}
			$sUrlFileAttach=$objconfig->_setAttachFileUrlPath();
			$arrFileNameAttach = $objrecordfun->_uploadFileAttachList($v_list_attach_value,$sUrlFileAttach,'dk_file_attach',',');
			$sRecordID=$arrResult['NEW_ID'];
            //var_dump($arrFileNameAttach);exit;
			$objReceive->eCSUpdateFileAttach($sRecordID,$arrFileNameAttach,'T_eCS_RECORD');
			//Xu ly cac truong hop NSD luu du lieu
			if ($sOption == "GHI_THEMMOI")
				$this->_redirect('record/wreceive/add?r='.$sRecordTypeId);
			if ($sOption == "GHI_QUAYLAI")
				$this->_redirect('record/wreceive/index');
		}
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrSingleRecord,true,true);
	}

    /**
     *
     */
    public function updateAction(){
        $this->view->titleBody = "TH&#212;NG TIN H&#7890; S&#416;";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objxml = new Extra_Xml();
        $ojbEfyLib = new Extra_Util();
        $objReceive = new record_modReceive();

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
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';
        }
        $this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];
        //Lay thong tin cua mot ho so
        $srecordId = $this->_request->getParam('hdn_object_id','');
        $this->view->srecordId = $srecordId;
        $arrSingleRecord = $objrecordfun->eCSRecordGetSingle($srecordId, $_SESSION['OWNER_CODE'],'');
        $this->view->arrSingleRecord = $arrSingleRecord;

        $this->view->RecodeCode = $arrSingleRecord[0]['C_CODE'];
        if($this->_request->getParam('rc') != '')
            $this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
        //Lay thong tin file dinh kem
        $arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
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

        //Xu ly truong hop neu la ghi va them tiep thi ko lay thong tin file dinh kem
        $sOption = $this->_request->getParam('hdh_option','');
        $this->view->option = $sOption;
        if ($sOption == "QUAY_LAI")
            $this->_redirect('record/wreceive/additional');
        $arFileAttach = $objReceive->DOC_GetAllDocumentFileAttach($srecordId, 'HO_SO', 'T_eCS_RECORD');
        $this->view->AttachFile = $objrecordfun->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,50);

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
            $arrParameter = array(
                'PK_RECORD'	=> $srecordId,
                'C_CODE' => $this->_request->getParam('C_CODE',''),
                'FK_RECORDTYPE'	=> $sRecordTypeId,
                'FK_RECEIVER' => $_SESSION['staff_id'],
                'C_RECEIVER_POSITION_NAME' => $objrecordfun->getNamePositionStaffByIdList($_SESSION['staff_id']),
                'C_RECEIVED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_RECEIVED_DATE','')),
                'C_APPOINTED_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('C_APPOINTED_DATE','')),
                'C_CURRENT_STATUS' => 'MOI_TIEP_NHAN',
                'C_DETAIL_STATUS' => '10',
                'C_RECEIVED_RECORD_XML_DATA' => $strXml,
                'C_LICENSE_XML_DATA' => '',
                'C_OWNER_CODE' =>$_SESSION['OWNER_CODE'],
                'NEW_FILE_ID_LIST'=>$arrFileNameUpload,
            );
            //Cap nhat du lieu
            $arrResult = $objReceive->eCSRecordUpdate($arrParameter);
            //thuc hien update file dinh kem
            $v_list_attach_value = "";
            if(isset($_REQUEST['hdn_list_attach_value'])){
                $v_list_attach_value = $_REQUEST['hdn_list_attach_value'];
            }
            $sUrlFileAttach=$objconfig->_setAttachFileUrlPath();
            $arrFileNameAttach = $objrecordfun->_uploadFileAttachList($v_list_attach_value,$sUrlFileAttach,'dk_file_attach',',');
            $sRecordID=$arrResult['NEW_ID'];
            $objReceive->eCSUpdateFileAttach($sRecordID,$arrFileNameAttach,'T_eCS_RECORD');
            //Xu ly cac truong hop NSD luu du lieu
            if ($sOption == "GHI_QUAYLAI")
                $this->_redirect('record/wreceive/additional');
        }
        $this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrSingleRecord,true,true);
    }
    /**
     *
     */
	public function deleteAction(){
		$objReceive = new record_modReceive();
		$sRecordIdList = $this->_request->getParam('hdn_object_id_list','');
		$sRetError = $objReceive->eCSRecordDelete($sRecordIdList,1);
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
		if($sRetError != null || $sRetError != '' ){
			echo "<script type='text/javascript'>alert('$sRetError')</script>";
		}
		else 
			$this->_redirect('record/wreceive/index');
	}

    /**
     *
     */
    public function printreceiptAction(){
        $ojbEfyLib = new Extra_Util();
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $objReceive = new record_modReceive();
        $objQLDTFun = new Extra_Ecs();
        $objConfig = new Extra_Init();

        $barcodelink = $objConfig->_setWebSitePath();
        //Lay thong tin chi tiet ho so
        $psRecordID = $this->_request->getParam('hdn_object_id','');
        $psFileName = $this->_request->getParam('fileName','');
        if (trim($psRecordID) != ""){
            $arrGetSingleRecord = $objReceive->getSingleRecord($psRecordID);
            $psXmlStr 			= 	$arrGetSingleRecord['C_RECEIVED_RECORD_XML_DATA'];
            $arrGetSingleRecord['BARCODE_LINK'] = $barcodelink;
        }else{
            $arrGetSingleRecord = array();
            $psXmlStr = "";
        }
        //Print Receipt
        $sReceiptContentFile = $objQLDTFun->ecs_PrintReceipt('./xml/record/' . $arrGetSingleRecord['C_KEY'].'/' . $psFileName.'.xml', 'replace_list', 'replace', '', $psXmlStr, $arrGetSingleRecord,'./templates/' . $arrGetSingleRecord['C_KEY'] . '/',$psFileName.'.tpl',$sOwnerCode);
        $sExportFileName=$psFileName.'.htm';
        $ojbEfyLib->_writeFile('public/export/' . $sExportFileName,$sReceiptContentFile);
        echo $objConfig->_getCurrentHttpAndHost().'public/export/'. $sExportFileName;
        exit;
    }
    /**
     *
     */
    public function printotherreceiptAction(){
        $objReceive = new record_modReceive();
        $objConfig = new Extra_Init();
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Lay thong tin chi tiet ho so
        $psRecordID = $this->_request->getParam('hdn_object_id','');
        $psFileName = $this->_request->getParam('fileName','');
        if (trim($psRecordID) != ""){
            $arrGetSingleRecord = $objReceive->getSingleRecord($psRecordID);
            $psXmlStr 			= 	$arrGetSingleRecord['C_RECEIVED_RECORD_XML_DATA'];
        }else{
            $arrGetSingleRecord = array();
            $psXmlStr 			= 	'';
        }
        //Print Receipt
        $sReceiptContentFile = self::ecs_printTempDoc('./xml/record/' . $arrGetSingleRecord['C_KEY'].'/' . $psFileName.'.xml', 'replace_list', 'replace', '', $psXmlStr, $arrGetSingleRecord,'./templates/' . $arrGetSingleRecord['C_KEY'] . '/',$psFileName.'.docx',$sOwnerCode);
        echo $objConfig->_getCurrentHttpAndHost().'public/export/'. $sReceiptContentFile;
        exit;
    }

    /**
     * @param $arrParameter
     * @return string
     * @throws Zend_Exception
     */
    private function ecs_printTempDoc($sPathXmlFile, $sParrentTagName, $TagName, $sPathTemplateFile, $sXmlData = '', $arrRecord = array(), $sPathTemplateFile, $sTemplateFile,$sOwnerCode){
        //Goi class xu ly
        Zend_Loader::loadClass('Zend_Config_Xml');
        Zend_Loader::loadClass('Extra_Word');
        $objConfig = new Extra_Init();
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile,$sParrentTagName);
        $objXml = new Extra_Xml();
        $objQLDTFun = new Extra_Ecs();
        $objLib = new Extra_Util();
        $dirTemplate = $sPathTemplateFile.$sTemplateFile;
        $phpdocx = new Extra_Word($dirTemplate);
        $phpdocx->assignToHeader("#HEADER1#","Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#","Footer 1"); // basic field mapping to footer
        $sXmlData = '<?xml version="1.0" encoding="UTF-8"?>'.$sXmlData;

        if(isset($ojbConfigXml->$TagName)){
            $TagElements = $ojbConfigXml->$TagName->toArray();
            if(is_array($TagElements)){
                //Duyet cac phan tu cua
                foreach ($TagElements as $elements => $arrElement){
                    //Bien xac dinh co phai lay du lieu tu xau XML luu trong DB khong?
                    if(isset($arrElement["from_xml_data"])){
                        $sFromXmlData 		= $arrElement["from_xml_data"];
                    }else{
                        $sFromXmlData = '';
                    }
                    //Dinh dang kieu du lieu
                    if(isset($arrElement["data_format"])){
                        $sDataFormat 		= $arrElement["data_format"];
                    }else{
                        $sDataFormat = '';
                    }
                    //Tim au can thay the
                    if(isset($arrElement["find_string"])){
                        $sFindString 		= $arrElement["find_string"];
                    }else{
                        $sFindString = '';
                    }
                    //Ten cot luu thong tin lay du lieu thay the
                    if(isset($arrElement["field_name"])){
                        $sFieldName 		= $arrElement["field_name"];
                    }else{
                        $sFieldName = '';
                    }
                    //Ten the luu du lieu trong xau XML thay the file temp
                    if(isset($arrElement["xml_tag_in_db"])){
                        $sXmlTagInDb 		= $arrElement["xml_tag_in_db"];
                    }else{
                        $sXmlTagInDb = '';
                    }
                    //--------------Bat dau thay the du lieu---------------------------------
                    if ($sFromXmlData == 'true' && $sXmlData != ''){
                        $sValue = trim($objXml->_xmlGetXmlTagValue($sXmlData,"data_list",$sXmlTagInDb));
                    }else{//Lay du lieu tu cot
                        $sValue = (isset($arrRecord[0][$sFieldName])? trim($arrRecord[0][$sFieldName]) :'');
                    }
                    // ky tu xuong dong
                    /*                    if ($sDataFormat == "breakline"){
                                            $sValue = $objLib->_breakLine($sValue);
                                        }*/
                    //Kieu getdate
                    /*                    if ($sDataFormat == "getdate"){
                                            $sValue = "ngày ".date("d")." tháng ".date("m")." năm ".date("Y");
                                        }*/
                    //Kieu selectbox
                    if ($sFromXmlData == 'true' && $sDataFormat == 'selectbox'){
                        $list_name = '';
                        if(isset($arrElement['list_name'])){
                            $list_name = $arrElement['list_name'];
                        }
                        if(!empty($list_name) && $list_name != ''){
                            $arrList = $objQLDTFun->getNameFromCode($sOwnerCode,$list_name,$sValue);
                            if(isset($arrList['0']['C_NAME'])){
                                $sValue = $arrList['0']['C_NAME'];
                            }
                        }
                    }
                    /*                    //Ngay kieu chu
                                        if ($sDataFormat == "isdateText" && $sValue !=''){
                                            $arr = explode('/', $sValue);
                                            $sValue = 'ngày '.$arr['0'].' tháng '.$arr['1'].' năm '.$arr['2'];
                                        }
                                        //Ngay kieu chu hoa
                                        if ($sDataFormat == "isdateTextH" && $sValue !=''){
                                            $arr = explode('/', $sValue);
                                            $sValue = 'Ngày '.$arr['0'].' tháng '.$arr['1'].' năm '.$arr['2'];
                                        }*/
                    //Kieu In giay phep dang ngay dd thang mm nam yyyy
                    /*                    if ($sFromXmlData == 'true'&& $sDataFormat == 'isdatelicense'){
                                            if($sValue<>''){
                                                $sValue = $objLib->_ddmmyyyyToYYyymmdd($sValue);
                                                $sValue = "ngày ".date("d", strtotime($sValue))." tháng ".date("m", strtotime($sValue))." năm ".date("Y", strtotime($sValue));
                                            }
                                        }*/
                    //Kieu du lieu muc dich su dung dat
                    if ($sDataFormat == "datatable"){
                        $sBlockName = $arrElement["sblockname"];
                        $sfind_string_list = $arrElement["find_string_list"];
                        $arrfind_string = explode(',',$sfind_string_list);
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',',$sfield_list);
                        $arrDocx = array();
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        foreach ($arrValue as $key => $value) {
                            $arrTemp = array();
                            for($i=0;$i<sizeof($arrfind_string);$i++){
                                $key = '#'.$arrfind_string[$i].'#';
                                $arrTemp[$key] = $value->$arrfield[$i];
                            }
                            array_push($arrDocx, $arrTemp);
                        }
                        $phpdocx->assignBlock($sBlockName, $arrDocx);
                    }

                    if ($sDataFormat == "tabletostring"){
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',',$sfield_list);
                        $snote_list = $arrElement["note_list"];
                        $arrnote = explode('#',$snote_list);
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        $sValue = '';
                        foreach ($arrValue as $key => $value) {
                            foreach ($arrfield as $index => $field) {
                                $stempval = $value->$field;
                                if($stempval!=''){
                                    $sValue.=$stempval.$arrnote[$index];
                                }
                            }
                            $sValue.=', ';
                        }
                        if(($sValue!='')&&(strlen($sValue)>2)){
                            $sValue = substr($sValue,0,-2);
                        }
                    }

                    if ($sDataFormat == "list_info"){
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',',$sfield_list);
                        $slable_list = $arrElement["lable_list"];
                        $arrlable = explode('#',$slable_list);
                        $sValue = '';
                        foreach ($arrfield as $key => $field) {
                            $stempval = trim($objXml->_xmlGetXmlTagValue($sXmlData,"data_list",$field));
                            if($stempval!=''){
                                $sValue.=$arrlable[$key].': '.trim($objXml->_xmlGetXmlTagValue($sXmlData,"data_list",$field)).', ';
                            }
                        }
                        if(($sValue!='')&&(strlen($sValue)>2)){
                            $sValue = substr($sValue,0,-2);
                        }
                    }
                    if ($sDataFormat == "if_else"){
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',',$sfield_list);
                        $sValue = '';
                        foreach ($arrfield as $key => $field) {
                            $stempval = trim($objXml->_xmlGetXmlTagValue($sXmlData,"data_list",$field));
                            if($stempval!=''){
                                $sValue.= $stempval;
                                break;
                            }
                        }
                    }

                    if($sXmlTagInDb == 'tai_lieu_kt'){
                        $sVal = "";
                        $j =1;
                        if($sValue != ''){
                            $arrList=$objQLDTFun->getNameFromCode($sOwnerCode,$arrRecord['C_KEY']."_TLKT",$sValue);
                            for($i = 0; $i < sizeof($arrList); $i ++){
                                $sVal = $sVal . $arrList[$i]['C_NAME'].'; ';
                                $j ++;
                            }
                            $sValue = $sVal;
                            //Lay file dinh kem khac co lien quan
                            $sValue = $sValue . trim($objXml->_xmlGetXmlTagValue($sXmlData,"data_list",'tl_khac'));
                        }
                    }

                    //Kieu fix du lieu
                    /*                    if ($sDataFormat == "FixValue"){
                                            $sValue 		= $arrElement["data_value"];
                                        }*/
                    //Thay the gia tri
                    /*                    $phpdocx->assign("#ngay#",date('d'));
                                        $phpdocx->assign("#thang#",date('m'));
                                        $phpdocx->assign("#nam#",date('Y'));*/
                    $phpdocx->assign($sFindString,$sValue);
                }
            }
        }
        $arr = explode('.', $sTemplateFile);
        $sFileExport = 	$arr[0];
        $v_export_filename = $sFileExport.'_'.date('dmy').date('His').'.doc';
        $dir_export_file = $_SERVER['DOCUMENT_ROOT'].$objConfig->_setWebSitePath().'public/export/'.$v_export_filename;
        $phpdocx->save($dir_export_file);
        return $v_export_filename;
    }

    /**
     *
     */
    public function submitorderAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Extra_Ecs();
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
            $ojbEfyLib = new Extra_Util();
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
                'C_WORKTYPE'                                =>  'TRINH_LD_DONVI',
                'C_SUBMIT_ORDER_CONTENT'					=>	$idea,
                'FK_STAFF'									=>	$leaderid,
                'C_POSITION_NAME'							=>	$sleadername,
                'C_ROLES'									=>	'DUYET_CAP_MOT',
                'C_STATUS'									=>	'TRINH_KY',
                'C_DETAIL_STATUS'							=>	'120',
                'NEW_FILE_ID_LIST'                          =>  $arrFileNameUpload,
                'C_USER_ID'									=>	$iUserId,
                'C_USER_NAME'								=>	$iUserName,
                'C_OWNER_CODE'								=>	$_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/wreceive/index');
        }
    }
    /**
     *
     */
    public function submitorderaddAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Extra_Ecs();
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
            $ojbEfyLib = new Extra_Util();
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
                'C_WORKTYPE'                                =>  'TRINH_LD_DONVI',
                'C_SUBMIT_ORDER_CONTENT'					=>	$idea,
                'FK_STAFF'									=>	$leaderid,
                'C_POSITION_NAME'							=>	$sleadername,
                'C_ROLES'									=>	'DUYET_CAP_MOT',
                'C_STATUS'									=>	'TRINH_KY',
                'C_DETAIL_STATUS'							=>	'120',
                'NEW_FILE_ID_LIST'                          =>  $arrFileNameUpload,
                'C_USER_ID'									=>	$iUserId,
                'C_USER_NAME'								=>	$iUserName,
                'C_OWNER_CODE'								=>	$_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/wreceive/additional');
        }
    }
    /**
     *
     */
    public function forwardaddAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Extra_Ecs();
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
            $ojbEfyLib = new Extra_Util();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = $this->_request->getParam('chk_process_type');
            $sStatus = '';
            $sDetailStatus = '';
            switch ($sWorkType) {
                case 'CHUYEN_LIEN_THONG':
                    $sStatus = 'CHUYEN_QUAN_HUYEN';
                    $sDetailStatus = '10';
                    break;
                case 'CHUYEN_TRA_KQ':
                    $sStatus = 'CAP_PHEP';
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
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/wreceive/additional');
        }
    }
    /**
     *
     */
    public function forwardAction(){
        $objReceive = new record_modReceive();
        $objrecordfun = new Extra_Ecs();
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
            $ojbEfyLib = new Extra_Util();
            $sRecordIdList = $this->_request->getParam('hdn_record_id_list');
            $idea = $this->_request->getParam('idea');
            $iUserId = $_SESSION['staff_id'];
            $iUserName = $objrecordfun->getNamePositionStaffByIdList($iUserId);
            //cac truong hop xu ly
            $sWorkType = $this->_request->getParam('chk_process_type');
            $sStatus = '';
            $sDetailStatus = '';
            switch ($sWorkType) {
                case 'CHUYEN_LIEN_THONG':
                    $sStatus = 'CHUYEN_QUAN_HUYEN';
                    $sDetailStatus = '10';
                    break;
                case 'CHUYEN_TRA_KQ':
                    $sStatus = 'CAP_PHEP';
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
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSWardProcessUpdate($arrParameter);
            $this->_redirect('record/wreceive/index');
        }
    }
    // HỒ SƠ CHỜ BỔ SUNG -----------------------------------------------------------------------------------------------
    /**
     *
     */
    public function additionalAction(){
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Extra_Ecs();
        $objXml					 = new Extra_Xml();
        $ojbEfyLib               = new Extra_Util();
        //ID NSD dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        //Lay mang cac TTHC
        $arrRecordType = $_SESSION['arr_all_record_type'];
        //ID TTHC
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
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; B&#7892; SUNG';
        $sRole='';
        $sStatusList = 'BO_SUNG' ;
        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        //CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
        if($sRecordTypeId == "") $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $this->view->sRecordType = $sRecordType;
        $sReceiveDate = '';
        $sDetailStatusCompare = '' ;
        $sOrderClause = 'order by  C_RECEIVED_DATE desc';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $iCurrentPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../wreceive/additional','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId,'TIEP_NHAN', $arrRecordType,$arrInputfilter);
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
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
        $this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../wreceive/viewadditional');
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
    public function processingAction(){
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Extra_Ecs();
        $objXml					 = new Extra_Xml();
        //ID NSD dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        //Lay mang cac TTHC
        $arrRecordType = $_SESSION['arr_all_record_type'];
        //ID TTHC
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
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; &#272;ANG GI&#7842;I QUY&#7870;T';
        //$sRole = 'TIEP_NHAN';
        $sRole='';
        $sStatusList = 'TRINH_KY' ;
        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        //CAC THAM SO DE MODEL TRUY VAN LAY DS HO SO
        if($sRecordTypeId == "") $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $this->view->sRecordType = $sRecordType;
        $sReceiveDate = '';
        $sDetailStatusCompare = '' ;
        $sOrderClause = 'order by  C_RECEIVED_DATE desc';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $iCurrentPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../wreceive/processing','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId,'TIEP_NHAN', $arrRecordType,$arrInputfilter);
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
            //$sRecordTypeId		= $Parameter['recordType'];
            $sfulltextsearch	= $Parameter['fullTextSearch'];
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
        $this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../wreceive/viewprocessing');
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
            $this->view->sdocpertotal = "Danh sách này không có hồ sơ nào";
        }
    }
    /**
     *
     */
    public function transitionAction(){
        $this->view->titleBody = "DANH SÁCH HỒ SƠ LIÊN THÔNG CHUYỂN HUYỆN";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objxml = new Extra_Xml();

        $arrRecordType = $_SESSION['arr_all_record_type'];
        $sRecordTypeId = $this->_request->getParam('recordType');
        if($sRecordTypeId == "")
            $sRecordTypeId=$_SESSION['RECORD_TYPE'];
        if($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        $_SESSION['RECORD_TYPE']=$sRecordTypeId;

        $iCurrentStaffId = $_SESSION['staff_id'];
        $sReceiveDate = '';
        $sStatusList = 'CHUYEN_QUAN_HUYEN';
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

        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';
        }

        //Day gia tri tim kiem ra view
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../wreceive/transition','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);


        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $sDetailStatusCompare = " And A.C_DETAIL_STATUS = 10" ;
        $arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
        $this->view->arrRecord = $arrRecord;
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../wreceive/viewtransition');
        $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
        $this->view->iNumberRecord = $iNumberRecord;

        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = Extra_Util::_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
        if (count($arrRecord) > 0){
            $this->view->sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = Extra_Util::_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = Extra_Util::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
        }
    }
    /**
     * @throws Zend_Exception
     */
    public function resultAction(){
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Extra_Ecs();
        $objXml					 = new Extra_Xml();
        Zend_Loader::loadClass('record_modHandle');
        $objHandle	  			 = new record_modHandle();
        //ID NSD dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        //Lay mang cac TTHC
        $arrRecordType = $_SESSION['arr_all_record_type'];
        //ID TTHC
        $sRecordTypeId = $this->_request->getParam('recordType');
        if($sRecordTypeId ==''){
            $sRecordTypeId = $this->_request->getParam('hdn_record_type_id');
        }

        //var_dump($_SESSION['RECORD_TYPE']);
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
        if($sRecordTypeId == "") $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];

        //lay trang thai nhac viec
        $sReminder = $this->_request->getParam('reminder');
        //echo '$sReminder:'.$sReminder;

        //neu nhac viec la ho so bi thu choi
        if($sReminder=="TU_CHOI"){
            //Tieu de man hinh danh sach
            $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; T&#7914; CH&#7888;I';
            //dieu kien loc
            $sDetailStatusCompare = " And A.C_DETAIL_STATUS = 41 AND A.C_CURRENT_STATUS=''TU_CHOI'' " ;
            $sStatusList = 'TU_CHOI' ;
        }
        elseif ($sReminder=="CAP_PHEP"){
            //Tieu de man hinh danh sach
            $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; TR&#7842; K&#7870;T QU&#7842;';
            //dieu kien loc
            $sDetailStatusCompare = ' And A.C_DETAIL_STATUS = 41 ' ;
            $sStatusList = 'CAP_PHEP' ;
        }
        else{
            //Tieu de man hinh danh sach
            $this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; TR&#7842; K&#7870;T QU&#7842;';
            //dieu kien loc
            $sDetailStatusCompare = ' And A.C_DETAIL_STATUS = 41 ' ;
            $sStatusList = 'CAP_PHEP,TU_CHOI' ;
        }
        $sRole='';

        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();

        $arrinfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $this->view->sRecordType = $sRecordType;
        $sReceiveDate = '';
        $sOrderClause = 'order by  C_RECEIVED_DATE desc';
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $iCurrentPage		= $this->_request->getParam('hdn_current_page',0);
        if ($iCurrentPage <= 1) $iCurrentPage = 1;
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
        if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
        $pUrl = $_SERVER['REQUEST_URI'];
        $sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../wreceive/result','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objRecordFunction->genEcsFilterFrom($iCurrentStaffId,'TIEP_NHAN', $arrRecordType,$arrInputfilter);
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if(isset($_SESSION['seArrParameter'])){
            $Parameter 			= $_SESSION['seArrParameter'];
            $sfullTextSearch	= $Parameter['fullTextSearch'];
            unset($_SESSION['seArrParameter']);
        }
        //Day gia tri tim kiem ra view
        $this->view->sfullTextSearch = $sfullTextSearch;
        $this->view->sRecordTypeId = $sRecordTypeId;
        //echo '<br>2'. $sRecordTypeId;
        $arrResult = $objRecordFunction->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iCurrentPage,$iNumberRecordPerPage);
        //Lay cac cong viec co trong TTHC
        $arrWorkTypeByRecordType = $objHandle->fRecordTypeListByCode('WORKTYPE',$sRecordTypeId,$sOwnerCode);
        $arrWorkTypeByRecordType = explode(',',$arrWorkTypeByRecordType['']);
        $this->view->arrWorkTypeByRecordType = $arrWorkTypeByRecordType;
        //Lay file XML mo ta form danh sach
        $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_hs_can_giai_quyet.xml';
        if(!file_exists($sXmlFileName)){
            $sXmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_can_giai_quyet.xml';
        }
        $this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../wreceive/viewresult');
        $iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = Extra_Util::_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
        if (count($arrResult) > 0){
            $this->view->sdocpertotal = "Danh s&#225;ch c&#243;: ".sizeof($arrResult).'/'.$iTotalRecord." h&#7891; s&#417;";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = Extra_Util::_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = Extra_Util::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
        }else{
            $this->view->sdocpertotal = "Danh s&#225;ch n&#224;y kh&#244;ng c&#243; h&#7891; s&#417; n&#224;o";
        }
    }

    /**
     * @throws Zend_Exception
     */
    public function updateresultAction(){
        //Load Class
        Zend_Loader::loadClass('record_modResult');
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Extra_Ecs();
        $ojbEfyLib				 = new Extra_Util();
        $objxml 				 = new Extra_Xml();
        $objResult	  			 = new record_modResult();
        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'CẬP NHẬT THÔNG TIN TRẢ KẾT QUẢ';
        //Lay ma don vi
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $this->view->sOwnerCode = $sOwnerCode;
        $sRecordTypeId = $this->_request->getParam('recordType');
        $this->view->recordType = $sRecordTypeId;
        //Pk cua HS
        $sRecordPk = $this->_request->getParam('hdn_object_id','');
        $this->view->sRecordPk = $sRecordPk;
        //Lay thong tin lien quan den TTHC
        $arrInfo = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
        $psOption = $this->_request->getParam('hdh_option','');
        if ($psOption == "QUAY_LAI"){
            $this->_redirect('record/receive/result');
        }
        $arrInfoList = $objResult->eCSResultInfoList($sRecordPk);
        $this->view->sRegistorName = $objxml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrInfoList[0]['C_RECEIVED_RECORD_XML_DATA'],'data_list','registor_name');
        $sMoneyYes = $objxml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrInfoList[0]['C_RECEIVED_RECORD_XML_DATA'],'data_list','le_phi_tam_thu');
        $this->view->sMoneyYes = $sMoneyYes;
        //Tinh toan so tien ng dan con  phai nop
        $a = str_replace(',','',$arrInfo['C_COST_NEW']);
        $b = str_replace(',','',$sMoneyYes);
        $c = ($a - $b);
        $arrC = str_split($c);
        $j =0;
        $strC = '';
        for($i= sizeof($arrC)-1;$i>=0;$i--){
            if($j%3 == 0 && $j >0) $strC = $strC .','.$arrC[$i];
            else $strC = $strC.$arrC[$i];
            $j = $j +1;
        }
        $this->view->sMoneyNo = strrev($strC);
        // Xu ly Cap nhat du lieu tu form
        if($psOption == "GHI"){
            //Lay ID can bo dang nhap
            $iFkStaff = $_SESSION['staff_id'];
            //Ten chuc vu
            $sPositionName = $objRecordFunction->getNamePositionStaffByIdList($iFkStaff);
            // Request
            $dResultDate = trim($this->_request->getParam('C_RESULT_DATE',""));
            $sRegistorName = trim($this->_request->getParam('C_REGISTOR_NAME',""));
            $sCost = trim($this->_request->getParam('C_COST',""));
            $sReason = trim($this->_request->getParam('C_REASON',""));
            if (trim($sReason) == '')	$sContent = 'Trả kết quả';
            else $sContent = trim($sReason);
            //Mang luu du lieu update
            $arrParameter = array(
                'PK_RECORD'						=>	$sRecordPk,
                'C_WORKTYPE'			        =>	'TRA_CONG_DAN',
                'C_REGISTOR_NAME'				=>	$sRegistorName,
                'C_COST'						=>	$sCost,
                'C_REASON'						=>	$sReason,
                'FK_STAFF'						=>	$iFkStaff,
                'C_POSITION_NAME'				=>	$sPositionName,
                'C_CONTENT'						=>	$sContent,
                'C_STATUS'						=>	'TRA_KET_QUA',
                'C_OWNER_CODE'					=>	$sOwnerCode
            );
            $objResult->eCSResultInfoUpdate($arrParameter);	//Goi model cap nhat vao CSDL
            $this->_redirect('record/wreceive/result');
        }
    }

    /**
     * @throws Zend_Exception
     */
    public function mailresultAction(){
        //Load Class
        Zend_Loader::loadClass('record_modResult');
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Extra_Ecs();
        $ojbEfyLib				 = new Extra_Util();
        $objxml 				 = new Extra_Xml();
        $objResult	  			 = new record_modResult();
        //Lay mang hang so dung chung
        $this->view->arrConst = $objInitConfig->_setProjectPublicConst();
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'C&#7852;P NH&#7852;T G&#7916;I EMAIL TH&#212;NG B&#193;O';
        //Lay ma don vi
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $this->view->sOwnerCode = $sOwnerCode;
        //-----
        $sRecordTypeId = $this->_request->getParam('recordType');
        $this->view->recordType = $sRecordTypeId;
        //Pk cua HS
        $sRecordPkList = $this->_request->getParam('hdn_object_id_list',"");
        if(trim($sRecordPkList) =='') $sRecordPkList = $this->_request->getParam('hdn_object_id',"");
        $this->view->sRecordPk = $sRecordPkList;
        $arrRecordInfo = $objRecordFunction->eCSGetInfoRecordFromListId($sRecordPkList);
        $this->view->general_information = $objRecordFunction->general_information($arrRecordInfo);

        //Lay thong tin lien quan den TTHC
        $arrInfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
        $this->view->sRecordTypeName = $arrInfoRecordType['C_NAME'];

        $psOption = $this->_request->getParam('hdh_option','');
        if ($psOption == "QUAY_LAI"){
            $this->_redirect('record/wreceive/result');
        }
        $arrMailRecordList = array();
        $arrInfoList = $objResult->eCSResultInfoList($sRecordPkList);
        foreach ($arrInfoList as $arrRecordList){
            $arrTemp = array(
                "code"				=>$arrRecordList['C_CODE'],
                "registor_name"		=>$objxml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrRecordList['C_RECEIVED_RECORD_XML_DATA'],'data_list','registor_name'),
                "email_nk"			=>$objxml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrRecordList['C_RECEIVED_RECORD_XML_DATA'],'data_list','email_nk'),
                "registor_address"	=>$objxml->_xmlGetXmlTagValue('<?xml version="1.0" encoding="UTF-8"?>'.$arrRecordList['C_RECEIVED_RECORD_XML_DATA'],'data_list','registor_address'),
            );
            array_push($arrMailRecordList,$arrTemp);
        }
        $this->view->arrMailRecordList = $arrMailRecordList;
        // Xu ly Cap nhat du lieu tu form
        if($psOption == "GHI"){
            //Lay dia chỉ mail
            $arrMailInfo = $objInitConfig->configMail();
            $from = $arrMailInfo['mail_name'];
            $pass = $arrMailInfo['mail_password'];
            //var_dump($arrMailInfo);exit;
            $to_name= 'Ông/Bà';
            $from_name = $_SESSION['OWNER_NAME'];
            $subject =$_SESSION['OWNER_NAME'].': Thông báo kết quả giải quyết TTHC';
            for ($i=0;$i<sizeof($arrMailRecordList);$i++){
                $sMail = trim($this->_request->getParam('mail_'.$i,''));
                $sNote = trim($this->_request->getParam('note_'.$i,""));
                $sNote = $sNote.'<br>'.'M&#7901;i &#212;ng(b&#224;) mang gi&#7845;y h&#7865;n &#273;&#7871;n B&#7897; ph&#7853;n M&#7897;t c&#7917;a c&#7911;a '.$from_name.' &#273;&#7875; nh&#7853;n k&#7871;t qu&#7843;!'.'<br>'.'Tr&#226;n tr&#7885;ng th&#244;ng b&#225;o!';
                $sResult = $ojbEfyLib->smtpmailer($sMail,$to_name,$from,$pass,$from_name,$subject,$sNote);
                if(!$sResult) echo 'Lỗi địa chỉ mail '. $sMail;
            }
            $this->_redirect('record/wreceive/result');
        }
    }
}?>