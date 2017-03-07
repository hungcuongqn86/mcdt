<?php
/**
 * Creater : NGHIAT
 * Date : 18/10/2010
 * Idea : Class Xu ly thong thong doi tuong danh muc
 */
class listxml_recordtypeController extends  Zend_Controller_Action {
		
	//Phuong thuc init()
	public function init(){
		//Efy_Function_RecordFunctions::CheckLogin();
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
		$this->view->delimitor 			= "!~~!";	
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();	
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
			
		//Goi lop Listxml_modList
		Zend_Loader::loadClass('listxml_modRecordtype');
		
		//Lay cac hang so su dung trong JS public
		$objConfig = new Extra_Init();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		
		//Tao doi tuong XML
		Zend_Loader::loadClass('Efy_Publib_Xml');		
		
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = Efy_Publib_Library::_getAllFileJavaScriptCss('','js','recordtype/recordtype.js,jquery-1.5.1.js,jQuery.equalHeights.js',',','js').Efy_Publib_Library::_getAllFileJavaScriptCss('','js/Autocomplete','actb_search.js,common_search.js',',','js');
		/* Ket thuc*/
		//Dinh nghia current modul code
		$this->view->currentModulCode = "LIST";
		$this->view->currentModulCodeForLeft = "RECORDTYPE";		
		
		//Lay tra tri trong Cookie
		$sGetValueInCookie = Efy_Library::_getCookie("showHideMenu");
		
		//Neu chua ton tai thi khoi tao
		if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)){
			Efy_Library::_createCookie("showHideMenu",1);
			Efy_Library::_createCookie("ImageUrlPath",$this->_request->getBaseUrl() . "/public/images/close_left_menu.gif");
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
			$this->view->ShowHideimageUrlPath = Efy_Library::_getCookie("ImageUrlPath");
		}
			
		//Hien thi file template
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    	//Hien thi header 
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    		//Hien thi header 		    
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  
	}
/**
 * Creater: nghiat
 * Date: 25/10/2010
 * Idea: Thuc hien phuong thuc Action hien thi danh sach TTHC
 */
	public function indexAction(){
		//Goi cac doi tuong
		$ojbEfyInitConfig = new Extra_Init();
		$objRecordtype	  = new listxml_modRecordtype();
		$objFunction	  = new Efy_Function_RecordFunctions();		
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'DANH S&#193;CH TH&#7910; T&#7908;C H&#192;NH CH&#205;NH';
        //Lay ma don vi NSD
        $sOwnerCode	= $_SESSION['OWNER_CODE'];
        $this->view->sOwnerCode = $sOwnerCode;
        $this->view->sOwnerCodeRoot = $ojbEfyInitConfig->_getOwnerCode();
		//Lay mang hang so dung chung
		$this->view->arrConst = $ojbEfyInitConfig->_setProjectPublicConst();
		$this->view->arrCate = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_LINH_VUC",1); //1:Luu cache
		//Request cum tu tim kiem
		$sFullTextSearch = trim($this->_request->getParam('sFullTextSearch',''));
		$sCate = trim($this->_request->getParam('hdn_cate','')); 
		$_SESSION['hdn_cate'] = $sCate;
		$this->view->sFullTextSearch = $sFullTextSearch;
        //Trang thai
        $arrStatus = array();
        array_push($arrStatus, array('code'=>'HOAT_DONG','name'=>'Hoạt động'));
        array_push($arrStatus, array('code'=>'KHONG_HOAT_DONG','name'=>'Không hoạt động'));
        $this->view->arrStatus = $arrStatus;

        $sStatus = $this->_request->getParam('sStatus','');
        if($sStatus==''){
            $sStatus = 'HOAT_DONG';
        }
        $this->view->sStatus = $sStatus;
		//Controller -> Model: Lay du lieu
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll($sOwnerCode,$sFullTextSearch,$sCate,$sStatus);
		//Controller ->View: Tra ket qua ra man hinh hien thi
		$this->view->arrRecordType = $arrRecordType;
	}

    /**
     *
     */
    public function addAction(){
        //Goi cac doi tuong
        $ojbEfyInitConfig = new Extra_Init();
        $objFunction	  = new Efy_Function_RecordFunctions();
        $objRecordtype	  = new listxml_modRecordtype();
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'C&#7852;P NH&#7852;T TH&#212;NG TIN THỦ TỤC H&#192;NH CH&#205;NH';
        //Lay mang hang so dung chung
        $this->view->arrConst = $ojbEfyInitConfig->_setProjectPublicConst();
        //Lay ma don vi NSD
        $sOwnerCode	= $_SESSION['OWNER_CODE'];
        //Lay DM Linh vuc TTHC
        $this->view->arrCate = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_LINH_VUC",1); //1:Luu cache
        //Lay danh sach cua Danh muc cong viec
        $arrWorkType = $objFunction->getAllObjectbyListCode($sOwnerCode,"DM_CV",1);
        //var_dump($arrWorkType);
        $this->view->arrWorkType = $arrWorkType;
        //Lay Danh sach hinh thuc VB TKQ
        $this->view->arrDocResult = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_HINH_THUC_VB_TKQ",1);
        //Lay So thu tu lon nhat cua TTHC
        $arrMaxOrder = $objRecordtype->eCSRecordTypeMaxOrder($sOwnerCode);

        if($arrMaxOrder['C_ORDER'] == "" or $arrMaxOrder['C_ORDER'] <= 0){
            $this->view->iOrder = 1;
        }else{
            $this->view->iOrder = $arrMaxOrder['C_ORDER'] + 1;
        }
        //Cap nhat TTHC vao CSDL
        $sCode = trim($this->_request->getParam('C_CODE',""));
        // Xu ly Cap nhat du lieu tu form
        if($sCode != ""){
            // luu T_eCS_RECORDTYPE
            $sName = trim($this->_request->getParam('C_NAME',""));
            $sCate = trim($this->_request->getParam('C_CATE',""));
            $iOrder = trim($this->_request->getParam('C_ORDER',""));
            $sStatus = trim($this->_request->getParam('C_STATUS',""));
            if($sStatus == "on")  $sStatus = "HOAT_DONG";
            else 				  $sStatus = "KHONG_HOAT_DONG";
            $sRecordType = trim($this->_request->getParam('C_RECORD_TYPE',""));
            $iProcessNumberDate = trim($this->_request->getParam('C_PROCESS_NUMBER_DATE',""));
            $sResultDocType = trim($this->_request->getParam('C_RESULT_DOC_TYPE',""));
            $sCostNew = trim($this->_request->getParam('C_COST_NEW',""));
            $sCostChange = trim($this->_request->getParam('C_COST_CHANGE',""));
            $iBeginRecordNumber  = trim($this->_request->getParam('C_BEGIN_RECORD_NUMBER',""));
            $iBeginLicenseNumber = trim($this->_request->getParam('C_BEGIN_LICENSE_NUMBER',""));
            $bIsViewOnNet	= trim($this->_request->getParam('C_IS_VIEW_ON_NET',""));
            if($bIsViewOnNet == "on")  $bIsViewOnNet = 1;
            else 				  $bIsViewOnNet = 0;
            $bIsRegisterOnNet = trim($this->_request->getParam('C_IS_REGISTER_ON_NET',""));
            if($bIsRegisterOnNet == "on")   $bIsRegisterOnNet = 1;
            else 				  			$bIsRegisterOnNet = 0;
            $bAutoReset = trim($this->_request->getParam('C_AUTO_RESET',""));
            if($bAutoReset == "on") 		$bAutoReset = 1;
            else 				  			$bAutoReset = 0;
            $bMoveToResult = trim($this->_request->getParam('C_MOVE_TO_RESULT',""));
            if($bMoveToResult == "on")  	$bMoveToResult = 1;
            else 				  			$bMoveToResult = 0;
            //Luu T_eCS_RECORDTYPE_WORKTYPE
            $sWorkTypeList = trim($this->_request->getParam('C_WORKTYPE_LIST',""));
            //C_OWNER_CODE_LIST
            $sOwnerCodeList = trim($this->_request->getParam('C_OWNER_CODE_LIST',""));
            //Mang luu du lieu update
            $arrParameter = array(
                'PK_RECORDTYPE'					=>  '',
                'C_CODE'						=>	$sCode,
                'C_NAME'						=>	$sName,
                'C_CATE'						=>	$sCate,
                'C_ORDER'						=>	$iOrder,
                'C_STATUS'						=>	$sStatus,
                'C_RECORD_TYPE'					=>	$sRecordType,
                'C_PROCESS_NUMBER_DATE'			=>	$iProcessNumberDate,
                'C_RESULT_DOC_TYPE'				=>	$sResultDocType,
                'C_COST_NEW'					=>	$sCostNew,
                'C_COST_CHANGE'					=>	$sCostChange,
                'C_BEGIN_RECORD_NUMBER'			=>	$iBeginRecordNumber,
                'C_BEGIN_LICENSE_NUMBER'		=>	$iBeginLicenseNumber,
                'C_IS_VIEW_ON_NET'				=>	$bIsViewOnNet,
                'C_IS_REGISTER_ON_NET'			=>	$bIsRegisterOnNet,
                'C_AUTO_RESET'					=>	$bAutoReset,
                'C_MOVE_TO_RESULT'				=>	$bMoveToResult,
                'C_WORK_TYPE_LIST'	     		=>	$sWorkTypeList,
                'C_OWNER_CODE'					=> 	$sOwnerCodeList
            );
            $arrResult = $objRecordtype->eCSRecordTypeUpdate($arrParameter);	//Goi model cap nhat vao CSDL
            $this->view->arrResult = $arrParameter;
            $sRetError = $arrResult['RET_ERROR'];
            if ($sRetError != ''){
                //Ma TTHC nay da ton tai
                echo "<script type='text/javascript'>";
                echo "alert('$sRetError');\n";
                echo "</script>";
            }else{
                $this->_redirect('listxml/recordtype/index/');
            }
        } // Dong qua trinh xu ly cap nhat du lieu tu form
    }
    /**
     *
     */
	public function configAction(){
        //Goi cac doi tuong
        $ojbEfyInitConfig = new Extra_Init();
        $objFunction	  = new Efy_Function_RecordFunctions();
        $objRecordtype	  = new listxml_modRecordtype();
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'CẤU HÌNH TH&#7910; T&#7908;C H&#192;NH CH&#205;NH';
        //Lay mang hang so dung chung
        $this->view->arrConst = $ojbEfyInitConfig->_setProjectPublicConst();
        //Lay ma don vi NSD
        $sOwnerCode	= $_SESSION['OWNER_CODE'];
        $this->view->sOwnerCode = $sOwnerCode;
        $this->view->sOwnerCodeRoot = $ojbEfyInitConfig->_getOwnerCode();
        //Lay DM Linh vuc TTHC
        $this->view->arrCate = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_LINH_VUC",1);
        //Lay So cap duyet
        $this->view->arrApproveLevel = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_CAP_DUYET",1);
        //Lay Autocomplete Cac phong ban
        $this->view->arr_autocomplete_department = $objFunction->doc_search_ajax($_SESSION['arr_all_unit'],"id","name","C_DEPARTMENT_LIST","hdn_department_list",0,"",0);
        //Lay Pk cua TTHC can hieu chinh
        $sRecordTypePk = $this->_request->getParam('hdn_object_id','');
        //Day ra view Pk
        $this->view->sRecordTypePk = $sRecordTypePk;

        $arrResult = $objRecordtype->eCSRecordTypeGetSingle($sRecordTypePk,$sOwnerCode);
        $this->view->arrResult = $arrResult ;

        // AutoComplete
        $arrPostionGroup=$ojbEfyInitConfig->_setLeaderPostionGroup();
        $arrLeader = $objFunction->docGetAllUnitLeader($arrPostionGroup['_CONST_POSITION_GROUP'],"arr_all_staff");
        $this->view->arr_autocomplete_leader_1 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_1","hdn_leader_list_1",0,"position_code",0);
        $this->view->arr_autocomplete_leader_2 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_2","hdn_leader_list_2",0,"position_code",0);
        $this->view->arr_autocomplete_leader_3 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_3","hdn_leader_list_3",0,"position_code",0);
        //-------------------------------------------------------------------------------------------------------------
        //Cap nhat TTHC vao CSDL
        $supdate = trim($this->_request->getParam('hdn_update',""));
        // Xu ly Cap nhat du lieu tu form
        if($supdate){
            $sApproveLevel = trim($this->_request->getParam('C_APPROVE_LEVEL',""));

            // Luu T_eCS_RECORDTYPE_HANDLE_DEPARTMENT
            $sDepartmentNameList = trim($this->_request->getParam('C_DEPARTMENT_LIST',""));
            $sDepartmentIdList   = $objFunction->convertUnitNameListToUnitIdList($sDepartmentNameList);
            if($sDepartmentNameList != "") $sDepartmentNameList = substr($sDepartmentNameList,0,-1);

            // Luu T_eCS_RECORDTYPE_RELATE_STAFF
            //C_ROLES ="TIEP_NHAN"
            $sReceiveFkList =  trim($this->_request->getParam('C_RECEIVE_ID_LIST',""));
            $sReceivePositionNameList = $objFunction->getNamePositionStaffByIdList($sReceiveFkList,";");
            //C_ROLES ="THU_LY"
            $sHandleFkList =  trim($this->_request->getParam('C_HANDLE_ID_LIST',""));
            $sHandlePositionNameList = trim($objFunction->getNamePositionStaffByIdList($sHandleFkList,";"));
            //C_ROLES ="THUE"
            $sTaxFkList =  trim($this->_request->getParam('C_TAX',""));
            $sTaxPositionNameList = trim($objFunction->getNamePositionStaffByIdList($sTaxFkList,";"));
            //C_ROLES ="KHO_BAC"
            $sTreasuryFkList =  trim($this->_request->getParam('C_TREASURY',""));
            $sTreasuryPositionNameList = trim($objFunction->getNamePositionStaffByIdList($sTreasuryFkList,";"));
            // Lanh dao duyet
            $sLeaderRolesList = "";
            $sLeaderPositionNameList_1 =  trim($this->_request->getParam('C_LEADER_LIST_1',""));
            $icountChar = substr_count($sLeaderPositionNameList_1, ';'); // substr_count dem so lan xuat hien cua mot ki tu
            for($index = 0; $index < $icountChar;$index++){
                $sLeaderRolesList = $sLeaderRolesList."DUYET_CAP_MOT" .";" ;
            }
            $sLeaderPositionNameList_2 =  trim($this->_request->getParam('C_LEADER_LIST_2',""));
            $icountChar = substr_count($sLeaderPositionNameList_2, ';');
            for($index = 0; $index < $icountChar;$index++){
                $sLeaderRolesList = $sLeaderRolesList."DUYET_CAP_HAI" .";" ;
            }
            $sLeaderPositionNameList_3 =  trim($this->_request->getParam('C_LEADER_LIST_3',""));
            $icountChar = substr_count($sLeaderPositionNameList_3, ';');
            for($index = 0; $index < $icountChar;$index++){
                $sLeaderRolesList = $sLeaderRolesList."DUYET_CAP_BA" .";" ;
            }
            $sLeaderPositionNameList = trim($sLeaderPositionNameList_1.$sLeaderPositionNameList_2.$sLeaderPositionNameList_3);
            if($sLeaderPositionNameList != "") $sLeaderPositionNameList = substr($sLeaderPositionNameList,0,-1);
            if($sLeaderRolesList != "") $sLeaderRolesList = substr($sLeaderRolesList,0,-1);
            $sLeaderFkList = $objFunction->convertStaffNameToStaffId($sLeaderPositionNameList,";");

            //Mang luu du lieu update
            $arrParameter = array(
                'PK_RECORDTYPE'					=>	$sRecordTypePk,
                'C_APPROVE_LEVEL'     			=>	$sApproveLevel,
                'C_DEPARTMENT_ID_LIST'			=>	$sDepartmentIdList,
                'C_DEPARTMENT_NAME_LIST'     	=>	$sDepartmentNameList,
                'C_RECEIVE_ID_LIST'				=>	$sReceiveFkList,
                'C_RECEIVE_NAME_LIST'     		=>	$sReceivePositionNameList,
                'C_HANDLE_ID_LIST'				=>	$sHandleFkList,
                'C_HANDLE_NAME_LIST'     		=>	$sHandlePositionNameList,
                'C_TAX_ID_LIST'					=>	$sTaxFkList,
                'C_TAX_NAME_LIST'     			=>	$sTaxPositionNameList,
                'C_TREASURY_ID_LIST'			=>	$sTreasuryFkList,
                'C_TREASURY_NAME_LIST'     		=>	$sTreasuryPositionNameList,
                'C_LEADER_ID_LIST'				=>	$sLeaderFkList,
                'C_LEADER_NAME_LIST'     		=>	$sLeaderPositionNameList,
                'C_LEADER_ROLE_LIST'     		=>	$sLeaderRolesList,
                'C_OWNER_CODE'					=> 	$_SESSION['OWNER_CODE']
            ) ;
            //var_dump($arrParameter); exit;
            $objRecordtype->eCSRecordTypeConfigUpdate($arrParameter);	//Goi model cap nhat vao CSDL
            $this->_redirect('listxml/recordtype/index/');
        }
	}
	
/**
 * Creater: nghiat
 * Date: 25/10/2010
 * Idea: Thuc hien phuong thuc Ajax lay Treeview don vi doi voi loai hs lien thong
 */
	public function ajaxtrasitionAction(){
		$this->view->sRecordType = $this->_request->getParam('C_RECORD_TYPE',"");	
	}

/**
 * Creater: nghiat
 * Date: 25/10/2010
 * Idea: Thuc hien phuong thuc Ajax lay Treeview chua bang danh sach CB tiep nhan + thu ly
 */
	public function ajaxstaffAction(){
		$this->view->sSelectReceive 	= $this->_request->getParam('select_receive',"");		
		$this->view->sSelectHandle 		= $this->_request->getParam('select_handle',"");
		$this->view->sSelectTax 		= $this->_request->getParam('select_tax',"");
		$this->view->sSelectTreasury 	= $this->_request->getParam('select_treasury',"");
		//$this->view->sReceiveFkList = $this->_request->getParam('ReceiveFkList',""); 
		//$this->view->sHandleFkList = $this->_request->getParam('HandleFkList',""); 
	}
/**
 * Creater: nghiat
 * Date: 25/10/2010
 * Idea: Thuc hien phuong thuc Ajax lay Treeview don vi doi voi loai hs lien thong
 */
	public function ajaxapproveAction(){
		$objFunction	  = new Efy_Function_RecordFunctions();	
		$sApproveLevel = $this->_request->getParam('C_APPROVE_LEVEL',"");
		$this->view->sApproveLevel = $sApproveLevel;
		
		$sApproveIdList = $this->_request->getParam('C_APP_TEMP',"");
		$this->view->sApproveIdList = $sApproveIdList;
		//echo $sApproveIdList;
		$arrPostionGroup=Extra_Init::_setLeaderPostionGroup();
		$arrLeader = $objFunction->docGetAllUnitLeader($arrPostionGroup['_CONST_POSITION_GROUP'],"arr_all_staff");
		//var_export($arrLeader);
		//Lay Autocomplete Lanh dao
		if($sApproveLevel == "DUYET_CAP_MOT" or $sApproveLevel == "DUYET_CAP_HAI" or $sApproveLevel == "DUYET_CAP_BA")
			$this->view->arr_autocomplete_leader_1 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_1","hdn_leader_list_1",0,"position_code",0);
		if($sApproveLevel == "DUYET_CAP_HAI" or $sApproveLevel == "DUYET_CAP_BA")
			$this->view->arr_autocomplete_leader_2 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_2","hdn_leader_list_2",0,"position_code",0);
		if($sApproveLevel == "DUYET_CAP_BA")
			$this->view->arr_autocomplete_leader_3 = $objFunction->doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_3","hdn_leader_list_3",0,"position_code",0);
	}

    /**
     *
     */
    public function getconfigwardAction(){
        $arrInput = $this->_request->getParams();
        $objRecordtype	  = new listxml_modRecordtype();
        $arrResult = $objRecordtype->eCSRecordTypeGetWardConfig($arrInput['recordtype'],$arrInput['staffconfigid']);
        //var_dump($arrResult);exit;
        //Khai bao su dung ham XML
        global $dspDiv, $readonlyInEditMode, $disabledInEditMode, $formFielName;
        $this->dspDiv = 1;
        $this->readonlyInEditMode = true; $this->disabledInEditMode = true;
        $this->formFielName	= "C_WARD_CODE_LIST";
        $spRetHtml = "<div style='display:none'><input type='textbox' id='$this->formFielName' name='$this->formFielName' value='' hide='true' readonly optional = true xml_data=false xml_tag_in_db='' message=''></div>";
        echo $spRetHtml . Efy_Xml::_generateHtmlForMultipleCheckboxFromSession('SesGetAllOwner', 'code','name',$arrResult['C_WARD_CODE'],'auto');
        exit;
    }

    /**
     *
     */
    public function saveconfigwardAction(){
        $arrInput = $this->_request->getParams();
        $arrParameter = array(
            'PK_RECORDTYPE'					=>	$arrInput['recordtype'],
            'STAFF_ID'						=>	$arrInput['staffconfigid'],
            'C_WARDS_CODE_LIST'	            =>	$arrInput['wardcodelist']
        );

        $objRecordtype	  = new listxml_modRecordtype();
        $objRecordtype->eCSRecordTypeWardConfigUpdate($arrParameter);	//Goi model cap nhat vao CSDL
        exit;
    }

    /**
     *
     */
	public function editAction(){
		//Goi cac doi tuong
		$ojbEfyInitConfig = new Extra_Init();
		$objFunction	  = new Efy_Function_RecordFunctions();	
		$objRecordtype	  = new listxml_modRecordtype();
		//Tieu de man hinh danh sach
		$this->view->bodyTitle = 'C&#7852;P NH&#7852;T TH&#7910; T&#7908;C H&#192;NH CH&#205;NH';
		//Lay mang hang so dung chung
		$this->view->arrConst = $ojbEfyInitConfig->_setProjectPublicConst();
		//Lay ma don vi NSD
		$sOwnerCode	= $_SESSION['OWNER_CODE'];
		//Lay DM Linh vuc TTHC
		$this->view->arrCate = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_LINH_VUC",1); //1:Luu cache
		//Lay danh sach cua Danh muc cong viec
		$arrWorkType = $objFunction->getAllObjectbyListCode($sOwnerCode,"DM_CV",1);
		$this->view->arrWorkType = $arrWorkType;
		//Lay Danh sach hinh thuc VB TKQ
		$this->view->arrDocResult = $objFunction->getAllObjectbyListCode($sOwnerCode,"DANH_MUC_HINH_THUC_VB_TKQ",1);
		//Lay Pk cua TTHC can hieu chinh
		$sRecordTypePk = $this->_request->getParam('hdn_object_id','');
		//Day ra view Pk
		$this->view->sRecordTypePk = $sRecordTypePk;
		$arrResult = $objRecordtype->eCSRecordTypeGetSingle($sRecordTypePk,$sOwnerCode);
		$this->view->arrResult = $arrResult ;
		//Cap nhat TTHC vao CSDL
		$sName = trim($this->_request->getParam('C_NAME',""));
		// Xu ly Cap nhat du lieu tu form
		if($sName != ""){
			// luu T_eCS_RECORDTYPE
			$sCode = trim($this->_request->getParam('C_CODE',""));
			$sCate = trim($this->_request->getParam('C_CATE',""));
			$iOrder = trim($this->_request->getParam('C_ORDER',""));
			$sStatus = trim($this->_request->getParam('C_STATUS',""));	
            if($sStatus == "on")  $sStatus = "HOAT_DONG";
				else 				  $sStatus = "KHONG_HOAT_DONG";	
			$sRecordType = trim($this->_request->getParam('C_RECORD_TYPE',""));
			$iProcessNumberDate = trim($this->_request->getParam('C_PROCESS_NUMBER_DATE',""));
            $iWardProcessNumberDate = trim($this->_request->getParam('C_WARDS_PROCESS_NUMBER_DATE',""));
			$sResultDocType = trim($this->_request->getParam('C_RESULT_DOC_TYPE',""));
			$sCostNew = trim($this->_request->getParam('C_COST_NEW',""));
			$sCostChange = trim($this->_request->getParam('C_COST_CHANGE',""));
			$iBeginRecordNumber  = trim($this->_request->getParam('C_BEGIN_RECORD_NUMBER',""));
			$iBeginLicenseNumber = trim($this->_request->getParam('C_BEGIN_LICENSE_NUMBER',""));
			$bIsViewOnNet	= trim($this->_request->getParam('C_IS_VIEW_ON_NET',""));
				if($bIsViewOnNet == "on")  $bIsViewOnNet = 1;
				else 				  $bIsViewOnNet = 0;	
			$bIsRegisterOnNet = trim($this->_request->getParam('C_IS_REGISTER_ON_NET',""));
				if($bIsRegisterOnNet == "on")   $bIsRegisterOnNet = 1;
				else 				  			$bIsRegisterOnNet = 0;	
			$bAutoReset = trim($this->_request->getParam('C_AUTO_RESET',""));
				if($bAutoReset == "on") 		$bAutoReset = 1;
				else 				  			$bAutoReset = 0;	
			$bMoveToResult = trim($this->_request->getParam('C_MOVE_TO_RESULT',""));
				if($bMoveToResult == "on")  	$bMoveToResult = 1;
				else 				  			$bMoveToResult = 0;
			//Luu T_eCS_RECORDTYPE_WORKTYPE
			$sWorkTypeList = trim($this->_request->getParam('C_WORKTYPE_LIST',""));
            //C_OWNER_CODE_LIST
            $sOwnerCodeList = trim($this->_request->getParam('C_OWNER_CODE_LIST',""));
			//Mang luu du lieu update
			$arrParameter = array(	
								'PK_RECORDTYPE'					=>	$sRecordTypePk,							// T_eCS_RECORDTYPE								
								'C_CODE'						=>	$sCode,
								'C_NAME'						=>	$sName,
								'C_CATE'						=>	$sCate,
								'C_ORDER'						=>	$iOrder,
								'C_STATUS'						=>	$sStatus,	
								'C_RECORD_TYPE'					=>	$sRecordType,
								'C_PROCESS_NUMBER_DATE'			=>	$iProcessNumberDate,
                                'C_WARDS_PROCESS_NUMBER_DATE'	=>	$iWardProcessNumberDate,
								'C_RESULT_DOC_TYPE'				=>	$sResultDocType,
								'C_COST_NEW'					=>	$sCostNew,
								'C_COST_CHANGE'					=>	$sCostChange,
								'C_BEGIN_RECORD_NUMBER'			=>	$iBeginRecordNumber,
								'C_BEGIN_LICENSE_NUMBER'		=>	$iBeginLicenseNumber,
								'C_IS_VIEW_ON_NET'				=>	$bIsViewOnNet,
								'C_IS_REGISTER_ON_NET'			=>	$bIsRegisterOnNet,
								'C_AUTO_RESET'					=>	$bAutoReset,
								'C_MOVE_TO_RESULT'				=>	$bMoveToResult,
                                'C_WORK_TYPE_LIST'	     		=>	$sWorkTypeList,
								'C_OWNER_CODE'					=> 	$sOwnerCodeList
			);
			//var_dump($arrParameter); exit;
			$objRecordtype->eCSRecordTypeUpdate($arrParameter);	//Goi model cap nhat vao CSDL
			$this->_redirect('listxml/recordtype/index/');
		}
	}
/**
 * Creater: nghiat
 * Date: 25/10/2010
 * Idea: Thuc hien phuong thuc Action Xoa list TTHC
 */		
	public function deleteAction(){	
		$objRecordtype	  = new listxml_modRecordtype();	
		//Lay Id doi tuong can xoa
		$sRecordTypeIdList = $this->_request->getParam('hdn_object_id_list',"");	
		//echo $sRecordTypeIdList; exit;
		//Goi phuong thuc xoa doi tuong
		$objRecordtype->eCSRecordTypeDelete($sRecordTypeIdList);
		$this->_redirect('listxml/recordtype/index/');	
	}	
}	
?>