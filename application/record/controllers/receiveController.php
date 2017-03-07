<?php

/**
 * Class record_receiveController
 */
class record_receiveController extends  Zend_Controller_Action {
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
		$objConfig = new Extra_Init();
		$this->view->UrlAjax = $objConfig->_setUrlAjax();	
		$this->view->arrConst = $objConfig->_setProjectPublicConst();
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor = $this->_ConstPublic['delimitor'];
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";			
		//Goi lop modRecord
		Zend_Loader::loadClass('record_modReceive');
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
		// Load tat ca cac file Js va Css
        $objLibrary = new Efy_Library();
		$sStyle= $objLibrary->_getAllFileJavaScriptCss('','js','js-record/receive.js,xml/general_datatable.js,xml/general_formfiel.js',',','js');
        $sStyle.= $objLibrary->_getAllFileJavaScriptCss('','style','printmenu/printmenu.css',',','css');
		$this->view->LoadAllFileJsCss = $sStyle;

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
		
		//Dinh nghia current modul code
		$this->view->currentModulCode = "RECORD";
        $sActionName = $this->_request->getActionName();
        $currentModulCodeForLeft = 'RECEIVED-RECORD';
        switch ($sActionName) {
            case 'transition':
                $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                break;
            case 'forward':
                $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                break;
            case 'confirm':
                $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                break;
            case 'viewtransition':
                $currentModulCodeForLeft = 'RECEIVED-TRANSITION';
                break;
            case 'additional':
                $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                break;
            case 'updateadditional':
                $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                break;
            case 'viewadditional':
                $currentModulCodeForLeft = 'RECEIVED-ADDITIONAL';
                break;
            case 'movehandle':
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
            case 'updatetransition':
                $currentModulCodeForLeft = 'RECEIVED-RESULT';
                break;
        }
        $this->view->currentModulCodeForLeft = $currentModulCodeForLeft;

		$psshowModalDialog = $this->_request->getParam('showModelDialog',0);
		$this->view->showModelDialog = $psshowModalDialog;
		if ($psshowModalDialog != 1){
			//Hien thi file template
			$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    //Hien thi header 
			$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    //Hien thi header 		    
	        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	 //Hien thi footer
		}
  	}

    /**
     *
     */
	public function indexAction(){
		$this->view->titleBody = "DANH S&#193;CH H&#7890; S&#416; &#272;&#195; TI&#7870;P NH&#7852;N";
		$objconfig = new Extra_Init();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Efy_Publib_Xml();
        $ojbEfyLib = new Efy_Library();

		$arrRecordType = $_SESSION['arr_all_record_type'];
		//var_dump($arrRecordType);
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
		//echo $sxmlFileName;
		//Day gia tri tim kiem ra view
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../receive/index','RecordTypeId'=>$sRecordTypeId);
		//var_dump($arrInputfilter);
		$this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);
        //Lay cac mau in
        $this->view->str_print = $objxml->genEcsPrintGenerate($sxmlFileName);
		// Goi ham search de hien thi ra Complete Textbox
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		$sDetailStatusCompare = " And A.C_DETAIL_STATUS = 10" ;
		$arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
		$this->view->arrRecord = $arrRecord;
		$this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../receive/viewindex');
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
  	public function addAction(){
		$this->view->titleBody = "TI&#7870;P NH&#7852;N H&#7890; S&#416; M&#7898;I";
		$objconfig = new Extra_Init();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Efy_Publib_Xml();
		$ojbEfyLib = new Efy_Library();
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
		$this->view->iNumberProcessDate = $arrinfoRecordType['C_PROCESS_NUMBER_DATE'];
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
			//var_dump($arrResult);exit;
			//Goi mang thuc hien gui du lieu cho RTA  
			$type = $arrResult['LOAI_HO_SO'];
			$status = $arrResult['C_DETAIL_STATUS'];
			$minor_code = $arrResult['C_CODE'];
			$apply_type = $arrResult['HINH_THUC_NOP_HS'];
			$creator_name = $arrResult['CHU_HS'];
			$creator_address = $arrResult['DC_CHU_HS'];
			$creator_phone = $arrResult['SDT_CHU_HS'];			
			$applicant_name = $arrResult['TEN_NUGOI_NOP'];
			$applicant_age = $arrResult['TUOI_NGUOI_NOP'];
			$applicant_ethinic = $arrResult['DT_NGUOI_NOP'];
			$applicant_sex = $arrResult['SEX_NGUOI_NOP'];
			$applicant_phone = $arrResult['PHONE_NGUOI_NOP'];
			$procedure_type = $arrResult['LV_TTHC'];
			$procedure_details = $arrResult['TEN_TTHC'];
			$case_apply_date = $arrResult['C_RECEIVED_DATE'];
			$appointment_returned_date = $arrResult['C_APPOINTED_DATE'];
			$description = $arrResult['GHI_CHU'];			
			//echo $type;exit;
			$arrdata = $objrecordfun->SubmitInstance($type,$status,$minor_code,$apply_type,$creator_name,$creator_address,$creator_phone,$applicant_name,$applicant_age,$applicant_ethinic,$applicant_sex,$applicant_phone,$procedure_type,$procedure_details,$case_apply_date,$appointment_returned_date,'','',$description);
			//var_dump($arrdata);exit;
			$Newid = $arrdata['msg'];
			$objReceive->eCSUpdateNewId($Newid,$minor_code);
			//var_dump($arrdata);exit;
			
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
				$this->_redirect('record/receive/add?r='.$sRecordTypeId);
			if ($sOption == "GHI_QUAYLAI")
				$this->_redirect('record/receive/index');
		}				
	}

    /**
     *
     */
	public function editAction(){
		$this->view->titleBody = "TH&#212;NG TIN H&#7890; S&#416;";
		$objconfig = new Extra_Init();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Efy_Publib_Xml();
		$ojbEfyLib = new Efy_Library();
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
		$arrRecordInfo = $objReceive->eCSGetInfoRecordFromListId($srecordId, $_SESSION['OWNER_CODE']);
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
			$this->_redirect('record/receive/index');
		$sRecordIdTemp = $srecordId;
		if($sOption == "GHI_THEMTIEP")
			$sRecordIdTemp = "";
		if($sOption != "GHI_TAM"){
			$arFileAttach = $objReceive->DOC_GetAllDocumentFileAttach($sRecordIdTemp, 'HO_SO', 'T_eCS_RECORD');
			$this->view->AttachFile = $objrecordfun->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,50);	
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

			//Goi du lieu chuyen cho RTA
			$Newid = $arrResult['C_NEWID_RTA'];
			$apply_type = $arrResult['HINH_THUC_NOP_HS'];
			$creator_name = $arrResult['CHU_HS'];
			$creator_address = $arrResult['DC_CHU_HS'];
			$creator_phone = $arrResult['SDT_CHU_HS'];			
			$applicant_name = $arrResult['TEN_NUGOI_NOP'];
			$applicant_age = $arrResult['TUOI_NGUOI_NOP'];
			$applicant_ethinic = $arrResult['DT_NGUOI_NOP'];
			$applicant_sex = $arrResult['SEX_NGUOI_NOP'];
			$applicant_phone = $arrResult['PHONE_NGUOI_NOP'];
			$description = $arrResult['GHI_CHU'];		
			If($Newid <> '' and $Newid != Null ){
			// Gọi ham update thong tin ho so cho RTA khi sua 1 ho so 
				$arrdata = $objrecordfun->UpdateInstanceedit($Newid,$apply_type,$creator_name,$creator_address,$creator_phone,$applicant_name,$applicant_age,$applicant_ethinic,$applicant_sex,$applicant_phone,$description);				
			}			
			
			//thuc hien update file dinh kem
			$v_list_attach_value = "";
			if(isset($_REQUEST['hdn_list_attach_value'])){
				$v_list_attach_value = $_REQUEST['hdn_list_attach_value'];
			}
			$sUrlFileAttach=$objconfig->_setAttachFileUrlPath();
			$arrFileNameAttach = $objrecordfun->_uploadFileAttachList($v_list_attach_value,$sUrlFileAttach,'dk_file_attach',',');
			$sRecordID=$arrResult['NEW_ID'];			
			$updateFile=$objReceive->eCSUpdateFileAttach($sRecordID,$arrFileNameAttach,'T_eCS_RECORD');
			//Xu ly cac truong hop NSD luu du lieu
			if ($sOption == "GHI_THEMTIEP"){
				$this->view->option = $sOption;
				//Lay ID VB vua moi insert vao DB
				$this->view->srecordId = $arrResult['NEW_ID'];
				//Lay thong tin van ban vua them moi va hien thi ra view 
				$arrSingleRecord = $objrecordfun->eCSRecordGetSingle($arrResult['NEW_ID'], $_SESSION['OWNER_CODE']);
				$this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
				$this->view->arrSingleRecord = $arrSingleRecord;
			}
			if ($sOption == "GHI_QUAYLAI")		
				$this->_redirect('record/receive/index');
		}
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrSingleRecord,true,true);
	}
    /**
     *
     */
	public function deleteAction(){
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objReceive = new record_modReceive();
		$objconfig = new Extra_Init();
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
			$this->_redirect('record/receive/index');
	}
    /**
     *
     */
    public function switchhandleAction(){
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
            $sDepartmentid = '';
            $sDepartmentname = '';
            $sHandleid = '';
            $sHandlename = '';
            switch ($sWorkType) {
                case 'CHUYEN_PHONG_BAN_XL':
                    $sStatus = 'CHO_PHAN_CONG';
                    $sDetailStatus = '21';
                    $sDepartmentid = $this->_request->getParam('chk_unit');
                    $sDepartmentname = $objrecordfun->getNameUnitByIdUnitList($sDepartmentid);
                    break;
                case 'CHUYEN_CAN_BO_XL':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
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
                'FK_STAFF' => $sHandleid,
                'C_POSITION_NAME' => $sHandlename,
                'C_ROLES' => 'THULY_CHINH',
                'FK_UNIT' => $sDepartmentid,
                'C_UNIT_NAME' => $sDepartmentname,
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSReceiveTransitionRecordUpdate($arrParameter);
            $this->_redirect('record/receive/index');
        }
    }

    /**
     *
     */
    public function movehandleAction(){
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
            $sDepartmentid = '';
            $sDepartmentname = '';
            $sHandleid = '';
            $sHandlename = '';
            switch ($sWorkType) {
                case 'CHUYEN_PHONG_BAN_XL':
                    $sStatus = 'CHO_PHAN_CONG';
                    $sDetailStatus = '21';
                    $sDepartmentid = $this->_request->getParam('chk_unit');
                    $sDepartmentname = $objrecordfun->getNameUnitByIdUnitList($sDepartmentid);
                    break;
                case 'CHUYEN_CAN_BO_XL':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    break;
                case 'YEU_CAU_BO_SUNG':
                    $sStatus = 'BO_SUNG';
                    $sDetailStatus = '120';
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
                'FK_STAFF' => $sHandleid,
                'C_POSITION_NAME' => $sHandlename,
                'C_ROLES' => 'THULY_CHINH',
                'FK_UNIT' => $sDepartmentid,
                'C_UNIT_NAME' => $sDepartmentname,
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSReceiveTransitionRecordUpdate($arrParameter);
            $this->_redirect('record/receive/additional');
        }
    }
    /**
     *
     */
	public function transitionAction(){
        $this->view->titleBody = "DANH SÁCH HỒ SƠ LIÊN THÔNG CHỜ NHẬN";
        $objconfig = new Extra_Init();
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
        $arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../receive/transition','RecordTypeId'=>$sRecordTypeId);
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);

        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
        $sDetailStatusCompare = " And A.C_DETAIL_STATUS = 10" ;

        Zend_Loader::loadClass('listxml_modRecordtype');
        $objRecordtype	  = new listxml_modRecordtype();
        $arrWardConfig = $objRecordtype->eCSRecordTypeGetWardConfig($sRecordTypeId,$iCurrentStaffId);
        $sDetailStatusCompare .= " And charindex(A.C_WARD_OWNER_CODE,''".$arrWardConfig['C_WARD_CODE']."'') > 0" ;

        $arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,$sReceiveDate,$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../receive/viewtransition');
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
    public function forwardAction(){
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
            $sDepartmentid = '';
            $sDepartmentname = '';
            $sHandleid = '';
            $sHandlename = '';
            switch ($sWorkType) {
                case 'CHUYEN_PHONG_BAN_XL':
                    $sStatus = 'CHO_PHAN_CONG';
                    $sDetailStatus = '21';
                    $sDepartmentid = $this->_request->getParam('chk_unit');
                    $sDepartmentname = $objrecordfun->getNameUnitByIdUnitList($sDepartmentid);
                    break;
                case 'CHUYEN_CAN_BO_XL':
                    $sStatus = 'THU_LY';
                    $sDetailStatus = '21';
                    $sHandleid = $this->_request->getParam('chk_handle');
                    $sHandlename = $objrecordfun->getNamePositionStaffByIdList($sHandleid);
                    break;
                case 'YEU_CAU_BO_SUNG':
                    $sStatus = 'BO_SUNG';
                    $sDetailStatus = '120';
                    break;
                case 'TU_CHOI':
                    $sStatus = 'TU_CHOI';
                    $sDetailStatus = '120';
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
                'C_ROLES' => 'THULY_CHINH',
                'FK_UNIT' => $sDepartmentid,
                'C_UNIT_NAME' => $sDepartmentname,
                'C_STATUS' => $sStatus,
                'C_DETAIL_STATUS' => $sDetailStatus,
                'NEW_FILE_ID_LIST' => $arrFileNameUpload,
                'C_USER_ID' => $iUserId,
                'C_USER_NAME' => $iUserName,
                'C_OWNER_CODE' => $_SESSION['OWNER_CODE']
            );
            //var_dump($arrParameter);exit;
            $objReceive->eCSReceiveTransitionRecordUpdate($arrParameter);
            $this->_redirect('record/receive/transition');
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
            $sWorkType = 'XAC_NHAN_DU_THONG_TIN';
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
            $this->_redirect('record/receive/transition');
        }
    }

    /**
     * @throws Zend_Exception
     */
	public function resultAction(){	
		Zend_Loader::loadClass('record_modHandle');
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
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
		//$sRole = 'TIEP_NHAN';
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
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../receive/result','RecordTypeId'=>$sRecordTypeId);
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
		//echo $sRecordTypeId.'<br>'.$sRecordType;
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
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../receive/viewresult');
		$iTotalRecord = $arrResult[0]['C_TOTAL_RECORD'];	
		//Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
		$this->view->SelectDeselectAll = Efy_Publib_Library::_selectDeselectAll($iNumberRecordPerPage, $iTotalRecord);
		if (count($arrResult) > 0){
			$this->view->sdocpertotal = "Danh s&#225;ch c&#243;: ".sizeof($arrResult).'/'.$iTotalRecord." h&#7891; s&#417;";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$this->view->generateStringNumberPage = Efy_Publib_Library::_generateStringNumberPage($iTotalRecord, $iCurrentPage, $iNumberRecordPerPage,$pUrl) ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$this->view->generateHtmlSelectBoxPage = Efy_Publib_Library::_generateChangeRecordNumberPage($iNumberRecordPerPage,$this->view->getStatusLeftMenu);
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
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$ojbEfyLib				 = new Efy_Library();
		$objxml 				 = new Efy_Publib_Xml();
		$objResult	  			 = new record_modResult();
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'CẬP NHẬT THÔNG TIN TRẢ KẾT QUẢ';
		//Lay ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$this->view->sOwnerCode = $sOwnerCode;
		//-----
		$sRecordTypeId = $this->_request->getParam('recordType');
		$this->view->recordType = $sRecordTypeId;
		//Pk cua HS
		$sRecordPk = $this->_request->getParam('hdn_object_id','');
		$this->view->sRecordPk = $sRecordPk;
		//Lay thong tin lien quan den TTHC 
		$arrInfo = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
        $this->view->arrInfo = $arrInfo;
		$psOption = $this->_request->getParam('hdh_option','');
		if ($psOption == "QUAY_LAI"){
			//Quay lai 					
			$this->_redirect('record/receive/result');
		}
		$arrInfoList = $objResult->eCSResultInfoList($sRecordPk);
        $sownersentcode = $arrInfoList[0]['C_WARD_OWNER_CODE'];
        $sownersentname = '';
        if($sownersentcode!=''){
            foreach($_SESSION['SesGetAllOwner'] as $key => $owner){
                if($owner['code']==$sownersentcode){
                    $sownersentname = $owner['name'];
                    break;
                }
            }
        }
        $this->view->sownersentname = $sownersentname;
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
            $arrInput = $this->_request->getParams();
            $worktype = $arrInput['chk_process_type'];
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
								'C_WORKTYPE'			        =>	$worktype,
								'C_REGISTOR_NAME'				=>	$sRegistorName,
								'C_COST'						=>	$sCost,
								'C_REASON'						=>	$sReason,
								'FK_STAFF'						=>	$iFkStaff,	
								'C_POSITION_NAME'				=>	$sPositionName,
								'C_CONTENT'						=>	$sContent,
								'C_STATUS'						=>	'TRA_KET_QUA',
								'C_OWNER_CODE'					=>	$sOwnerCode
								);
			$arrResult = $objResult->eCSResultInfoUpdate($arrParameter);	//Goi model cap nhat vao CSDL		
			//Goi mang thuc hien gui du lieu cho RTA  
			$uuid = $arrResult['C_NEWID_RTA'];
			$status = $arrResult['C_DETAIL_STATUS'];
			$sent_processed_date = $arrResult['NGAY_CHUYEN_PHONG'];
			$received_process_date = $arrResult['C_HAVE_TO_RESULT_DATE'];
			$case_return_actual_date = $arrResult['C_RESULT_DATE'];			
			//echo $type;exit;
			// Gọi ham update thong tin ho so cho RTA khi da co ket qua 
			$arrdata = $objRecordFunction->UpdateInstance($uuid,$status,$sent_processed_date,$received_process_date,$case_return_actual_date);
			//var_dump($arrdata);exit;			
			$this->_redirect('record/receive/result');
		}						
	}

    /**
     * @throws Zend_Exception
     */
	public function mailresultAction(){
		//Load Class
		Zend_Loader::loadClass('record_modResult');
		Zend_Loader::loadClass('record_modHandle');
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$ojbEfyLib				 = new Efy_Library();
		$objxml 				 = new Efy_Publib_Xml();
		$objResult	  			 = new record_modResult();
		$objHandle	  			 = new record_modHandle();
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
		//Lay thong tin lien quan den TTHC 
		$arrInfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
		//Dua ten TTHC ra view
		$this->view->sRecordTypeName = $arrInfoRecordType['C_NAME'];
		//Lay ID phong ban NSD dang nhap hien thoi
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$arrRecord = $objHandle->eCSHandleRecordBasicGetAll($sRecordPkList,$iFkUnit,$sOwnerCode);
		$this->view->arrRecord = $arrRecord;
		$psOption = $this->_request->getParam('hdh_option','');
		if ($psOption == "QUAY_LAI"){
			//Quay lai 					
			$this->_redirect('record/receive/result');
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
			Zend_Loader::loadClass('Efy_Mail_Phpmailer');
			Zend_Loader::loadClass('Efy_Mail_Smtp');
			//Lay dia chỉ mail
			$arrMailInfo = $objInitConfig->configMail();
			$from = $arrMailInfo['mail_name'];
			$pass = $arrMailInfo['mail_password'];
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
			$this->_redirect('record/receive/result');
		}
	}
    /**
     * @throws Zend_Exception
     */
	public function updatetransitionAction(){ //Cap nhat chuyen tiep HS
		Zend_Loader::loadClass('record_modHandle');
		//Xu ly quay lai
		$psOption = $this->_request->getParam('hdh_option','');
		//Lay Id cua TTHC
		$sRecordTypeId = $this->_request->getParam('recordType',"");
		//Ma don vi
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$this->view->sRecordTypeId = $sRecordTypeId;
		if ($psOption == "QUAY_LAI"){
			//Ghi va quay lai chinh form voi noi dung rong						
			$this->_redirect('record/receive/result/');			
		}
		//Goi doi tuong
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objHandle	  			 = new record_modHandle();
		$objInitConfig 			 = new Extra_Init();
		$ojbEfyLib				 = new Efy_Library();
		//Lay Lich JS
		$efyLibUrlPath = $objInitConfig->_setLibUrlPath();
		$url_path_calendar = $efyLibUrlPath . 'efy-calendar/';
		$this->view->urlCalendar = $url_path_calendar;
		//Lay ma cac don vi lien thong theo TTHC
		$arrTransitionTemp = $objHandle->fRecordTypeListByCode('TRANSITION',$sRecordTypeId,$sOwnerCode,',','!&@!');
		$arrTransitionTemp = explode('!&@!',$arrTransitionTemp['']);
		$arrTransitionTemp = explode(',',$arrTransitionTemp[0]);
		//Lay Mang chua cac don vi lien thong theo TTHC
		$arrTransitionUnit = array();
		for ($j=0;$j<sizeof($arrTransitionTemp);$j++){
			for($i=0;$i<sizeof($_SESSION['SesGetAllOwner']);$i++){
				if($_SESSION['SesGetAllOwner'][$i]['code'] == $arrTransitionTemp[$j]){
					$arrTemp = array("code"=>$_SESSION['SesGetAllOwner'][$i]['code'],"name"=>$_SESSION['SesGetAllOwner'][$i]['name']);
					array_push($arrTransitionUnit,$arrTemp);
					break;
				}
			}
		}
		//Lay Autocomplete Cac don vi lien thong
		$this->view->arr_autocomplete_lien_thong_24 = $objRecordFunction->doc_search_ajax($arrTransitionUnit,"code","name","lien_thong_24","hdn_lien_thong_24",0,"",1);
		//Lay mang hang so dung chung
		$this->view->arrConst = $objInitConfig->_setProjectPublicConst();	
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'CẬP NHẬT THÔNG TIN CHUYỂN TIẾP HỒ SƠ';
		//Lay thong tin cua TTHC
		$arrInfoRecordType = $objRecordFunction->getinforRecordType($sRecordTypeId,$_SESSION['arr_all_record_type']);
		//Dua ten TTHC ra view
		$this->view->sRecordTypeName = $arrInfoRecordType['C_NAME'];
		//Lay cac cong viec co trong TTHC
		$arrWorkTypeByRecordType = $objHandle->fRecordTypeListByCode('WORKTYPE',$sRecordTypeId,$sOwnerCode);
		$arrWorkTypeByRecordType = explode(',',$arrWorkTypeByRecordType['']);
		$this->view->arrWorkTypeByRecordType = $arrWorkTypeByRecordType;
		//Lay Id 
		$sRecordPkList = $this->_request->getParam('hdn_object_id_list',"");
		if(trim($sRecordPkList) =='') {
			$sRecordPkList = $this->_request->getParam('hdn_object_id',""); 
		}
		$this->view->sRecordPk = $sRecordPkList;
		//Lay ID phong ban NSD dang nhap hien thoi
		$iFkUnit = $objRecordFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
		$arrRecord = $objHandle->eCSHandleRecordBasicGetAll($sRecordPkList,$iFkUnit,$sOwnerCode);
		$this->view->arrRecord = $arrRecord;
		//File dinh kem
		$arFileAttach = array();	
		$this->view->AttachFile = $objRecordFunction->DocSentAttachFile($arFileAttach,sizeof($arFileAttach),10,true,69);
		//Cap nhat
		if($psOption == "GHI"){
			//Lay ten chuc vu NSD dang nhap hien thoi
			$iUserId = $_SESSION['staff_id'];
			$iUserName = $objRecordFunction->getNamePositionStaffByIdList($iUserId);
			//ID hs chuyen tiep
			$sRecordPkList = substr($this->_request->getParam('hdn_record_id_list',""),0,-1);
			$sTransitionRecordPkList = substr($this->_request->getParam('hdn_record_trasition_id_list',""),0,-1);
			//Ngay chuyen
			$dTransitionDate = $this->_request->getParam('C_TRANSITION_DATE',"");
			//Noi dung
			$sContent = $this->_request->getParam('C_CONTENT',"");
			//Lay thong tin file dinh kem
			$arrFileNameUpload = $ojbEfyLib->_uploadFileList(10,$this->_request->getBaseUrl() . "/public/attach-file/",'FileName','!#~$|*');
			//Dau viec chon
			$iWork = 25;
			$sPositionName = '';
			$iFkStaff = '';
			$sUnitNameList =  trim($this->_request->getParam('lien_thong_24',""));
			$arr = explode(';',$sUnitNameList);
            $sUnitCodeList = '';
			for($i=0;$i<sizeof($arr);$i++){
				for($j=0;$j<sizeof($_SESSION['SesGetAllOwnerNotCurrentOwner']);$j++){
					if($arr[$i] == $_SESSION['SesGetAllOwnerNotCurrentOwner'][$j]['name']){
						$sUnitCodeList = $sUnitCodeList .$_SESSION['SesGetAllOwnerNotCurrentOwner'][$j]['code'].',' 	;
						break;
					}
				}
			}
			$sUnitCodeList = substr($sUnitCodeList,0,-1);
			$dAppoitedDate = 	$ojbEfyLib->_ddmmyyyyToYYyymmdd($this->_request->getParam('date_24',"") );
			//Cap nhat CSDL
			$arrParameter = array(	
						'PK_RECORD_LIST'							=>	$sRecordPkList,															
						'PK_TRASITION_RECORD_LIST'					=>	$sTransitionRecordPkList,
						'C_TRANSITION_DATE'							=>	$ojbEfyLib->_ddmmyyyyToYYyymmdd($dTransitionDate),
						'C_WORK'									=>	$iWork,
						'C_CONTENT'									=>	$sContent,	  //----
						'C_FILE'									=>	$arrFileNameUpload,
						'FK_STAFF'									=>	$iFkStaff,
						'C_POSITION_NAME'							=>	$sPositionName,
						'C_OWNER_CODE_LIST'							=>	$sUnitCodeList,
						'C_APPOINTED_DATE'							=>	$dAppoitedDate,
						'C_USER_ID'									=>	$iUserId,
						'C_USER_NAME'								=>	$iUserName
						);	
			$arrResult = $objHandle->eCSHandleTransitionUpdate($arrParameter);	//Goi model cap nhat vao CSDL
			$this->_redirect('record/receive/result/');
		}	
	}
    /**
     * @throws Zend_Exception
     */
    public function viewindexAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
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
	public function viewtransitionAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
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
    public function viewadditionalAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
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
    public function viewprocessingAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
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
	public function additionalAction(){	
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
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
		$this->view->sRecordTypeId = $sRecordTypeId;
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'DANH S&#193;CH H&#7890; S&#416; CH&#7900; B&#7892; SUNG';
		//$sRole = 'TIEP_NHAN';
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
		$sDetailStatusCompare = ' And [C_DETAIL_STATUS] <> 120' ;
		$sOrderClause = 'order by  C_RECEIVED_DATE desc';
		$sOwnerCode = $_SESSION['OWNER_CODE'];
		$iCurrentPage		= $this->_request->getParam('hdn_current_page',0);		
		if ($iCurrentPage <= 1) $iCurrentPage = 1;
		$iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page',0);
		if ($iNumberRecordPerPage == 0) $iNumberRecordPerPage = 15;
		$pUrl = $_SERVER['REQUEST_URI'];
		$sfullTextSearch 	= trim($this->_request->getParam('txtfullTextSearch',''));
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../receive/additional','RecordTypeId'=>$sRecordTypeId);
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
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../receive/viewadditional');
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
     *
     */
	public function updateadditionalAction(){
		$this->view->titleBody = "C&#7852;P NH&#7852;T B&#7892; SUNG H&#7890; S&#416;";
		$objconfig = new Extra_Init();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$objxml = new Efy_Publib_Xml();
		$ojbEfyLib = new Efy_Library();
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
			$this->_redirect('record/receive/additional');
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
				'C_CURRENT_STATUS' => 'BO_SUNG',
				'C_DETAIL_STATUS' => '10',
				'C_RECEIVED_RECORD_XML_DATA' => $strXml,
				'C_LICENSE_XML_DATA' => '',
				'C_OWNER_CODE' =>$_SESSION['OWNER_CODE'],																	
				'NEW_FILE_ID_LIST'=>$arrFileNameUpload,
			);
			//Cap nhat du lieu
			$arrResult = $objReceive->eCSRecordUpdate($arrParameter);
			
			//Goi du lieu chuyen cho RTA
			$Newid = $arrResult['C_NEWID_RTA'];
			$creator_name = $arrResult['CHU_HS'];
			$creator_address = $arrResult['DC_CHU_HS'];		
			$applicant_name = $arrResult['TEN_NUGOI_NOP'];
			$applicant_ethinic = $arrResult['DT_NGUOI_NOP'];
			$new_appointment_returned_date	= 	$arrResult['C_APPOINTED_DATE'];
			If($Newid <> '' and $Newid != Null ){
			    // Gọi ham update thong tin ho so cho RTA khi bo sung thong tin hs
				$objrecordfun->_UpdateInstanceedit($Newid,$creator_name,$creator_address,$applicant_name,$applicant_ethinic,$new_appointment_returned_date);
			}
			//Xu ly cac truong hop NSD luu du lieu
			if ($sOption == "GHI")		
				$this->_redirect('record/receive/additional');
		}
		$this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrSingleRecord,true,true);	
	}

    /**
     *
     */
	public function processingAction(){	
		//Goi cac doi tuong
		$objInitConfig 			 = new Extra_Init();
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		$objXml					 = new Efy_Publib_Xml();
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
		$sStatusList = 'CHO_PHAN_CONG,THU_LY,BO_SUNG,TRINH_KY,TRA_LAI,TU_CHOI,CHUYEN_TIEP' ;
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
		$arrInputfilter = array('fullTextSearch'=>$sfullTextSearch,'pUrl'=>'../receive/processing','RecordTypeId'=>$sRecordTypeId);
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
		$this->view->genlist = $objXml->_xmlGenerateList($sXmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../receive/viewprocessing');
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
     *
     */
    public function printreceiptAction(){
        $ojbEfyLib = new Efy_Library();
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $objReceive = new record_modReceive();
        $objQLDTFun = new Efy_Function_RecordFunctions();
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
        Zend_Loader::loadClass('Efy_phpDocx');
        $objConfig = new Extra_Init();
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile,$sParrentTagName);
        $objXml = new Efy_Xml();
        $objQLDTFun = new Efy_Function_RecordFunctions();
        $objLib = new Efy_Library();
        $dirTemplate = $sPathTemplateFile.$sTemplateFile;
        $phpdocx = new Efy_phpDocx($dirTemplate);
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
     * @throws Zend_Exception
     */
    public function viewresultAction(){
        Zend_Loader::loadClass('record_modReceive');
        Zend_Loader::loadClass('record_modHandle');

        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
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