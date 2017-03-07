<?php
/**
 * Class Xu ly thong thong tin loai BAO CAO
 */
class Listxml_reportController extends  Zend_Controller_Action {
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
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('Listxml_modListReport');
		
		//Lay so dong treng man hinh danh sach
		$this->view->NumberRowOnPage 	= $this->_ConstPublic['NumberRowOnPage'];		
		
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 			= $this->_ConstPublic['delimitor'];
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
				
		// Goi lop public		
		$objPublicLibrary = new Extra_Util();
		
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = $objPublicLibrary->_getAllFileJavaScriptCss('public/js/ListType','','','','js') . $objPublicLibrary->_getAllFileJavaScriptCss('public/js/ListType','','','','css').Extra_Util::_getAllFileJavaScriptCss('','js','jquery-1.5.1.js',',','js');
				
		/* Ket thuc 		*/
		//Dinh nghia current modul code
		$this->view->currentModulCode = "LIST";		
		$this->view->currentModulCodeForLeft = "REPORT";
		
		//Lay cac hang so su dung trong JS public		
		$objConfig = new Extra_Init();
		$this->view->arrConst = $objConfig->_setProjectPublicConst();	
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();			

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
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	 //Hien thi footer
	}	
	/**
	 * Creater : CUONGNH
	 * Date :26/10/2012
	 *
	 */
	public function indexAction(){
		$this->view->bodyTitle = 'DANH SÁCH BÁO CÁO CHUNG';			
		// Tao doi tuong cho lop tren		
		$objListReport = new Listxml_modListReport();	
		// Lay cac tham param de truyen vao phuong thuc getAllListType : dung cho search
		$iStatus = '';
		$sListTypeName =$this->_request->getParam('sFullTextSearch','');
		$this->view->listTypeName = $sListTypeName;	
		$sOwnerCode = $_SESSION['OWNER_CODE'];		
		// Thuc hien phuong thuc getAllListType 
		$this->view->arrRecordType = $objListReport->getAllListReport($iStatus,$sListTypeName,$sOwnerCode);
		// Tao bien chua so phan tu cua mang ket qua 
		$this->view->iCountElement = count($this->view->arrResult);	
	}
	/**
	 * @see : Thuc hien Them moi LOAI BAO CAO
	 *
	 */
	public function addAction(){
		// Tieu de cua Form cap  nhat
		$this->view->bodyTitle = 'CẬP NHẬT MỘT LOẠI BÁO CÁO';
		//Lay danh sach loai TTHC
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype	  = new listxml_modRecordtype();
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll( $_SESSION['OWNER_CODE'],'','');
		//var_dump($arrRecordType);
		//$arrRecordType = $_SESSION['arr_all_record_type'];			
		$this->view->arrRecordType = $arrRecordType;
		// goi load div 
		$this->view->divDialog = $this->showDialog();
		//Lay So thu tu lon nhat
		$objListReport = new Listxml_modListReport();	
		$arrMaxOrder = $objListReport->eCSListReportMaxOrder($_SESSION['OWNER_CODE']);
		//var_dump($arrMaxOrder);
		if($arrMaxOrder[0]['C_ORDER'] == "" or $arrMaxOrder[0]['C_ORDER'] <= 0){
			$this->view->iOrder = 1;
		}else{	
			$this->view->iOrder = $arrMaxOrder[0]['C_ORDER'] + 1;
		}
		// Lay toan bo tham so truyen tu form			
		$arrInput = $this->_request->getParams();
		// Thuc hien lay du lieu tu form 		
		if($arrInput["C_NAME"]<>''){	
			$objEfyLibrary = new Extra_Util();
			$sReportName = trim($objEfyLibrary->_restoreXmlBadChar($arrInput['C_NAME']));			
			$iReportOrder = $arrInput['C_ORDER'];			
			$sReportXml = $arrInput['txt_xml_file_name'];
			$sRecordTypeList = $arrInput['C_RECORD_TYPE_LIST'];	
			//Trang thai cua doi tuong danh muc (HOAT_DONG : hoat dong; NGUNG_HOAT_DONG ; Ngung hoat dong)
			$sStatus = 'NGUNG_HOAT_DONG';
			if ($arrInput['C_STATUS']){
				$sStatus = 'HOAT_DONG';
			}
			$this->view->arrResult = $objListReport->UpdateListReport('',$sReportName,$sReportXml,$iReportOrder,$sStatus,$sRecordTypeList,$_SESSION['OWNER_CODE']);			
		}		
	}		
	/**
	 * @see : Thuc hien viec sua mot laoi bao cao
	 */
	public function editAction(){
		// Tieu de cua Form cap  nhat
		$this->view->bodyTitle = 'CẬP NHẬT MỘT LOẠI BÁO CÁO';
		//Lay danh sach loai TTHC
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype = new listxml_modRecordtype();			
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll( $_SESSION['OWNER_CODE'],'','');
		//var_dump($arrRecordType);
		$this->view->arrRecordType = $arrRecordType;
		// goi load div 
		$this->view->divDialog = $this->showDialog();
		//  Lay id cua ban ghi
		$sListReportId = $this->_request->getParam('hdn_object_id');
		$this->view->sListReportId = $sListReportId;
		// Tao doi tuong 
		$objListReport = new Listxml_modListReport();		
	   	$objEfyLibrary = new Extra_Util();
	   	// Lay toan bo tham so truyen tu form			
		$arrInput = $this->_request->getParams();
		// Thuc hien lay du lieu tu form 		
		if($arrInput["C_NAME"]<>''){	
			$objEfyLibrary = new Extra_Util();
			$sReportName = trim($objEfyLibrary->_restoreXmlBadChar($arrInput['C_NAME']));			
			$iReportOrder = $arrInput['C_ORDER'];			
			$sReportXml = $arrInput['txt_xml_file_name'];
			$sRecordTypeList = $arrInput['C_RECORD_TYPE_LIST'];	
			//Trang thai cua doi tuong danh muc (HOAT_DONG : hoat dong; NGUNG_HOAT_DONG ; Ngung hoat dong)
			$sStatus = 'NGUNG_HOAT_DONG';
			if ($arrInput['C_STATUS']){
				$sStatus = 'HOAT_DONG';
			}
			$this->view->arrResult = $objListReport->UpdateListReport($sListReportId,$sReportName,$sReportXml,$iReportOrder,$sStatus,$sRecordTypeList,$_SESSION['OWNER_CODE']);			
		}	
		// Lay thong tin trong csdl
		$arrResult = $objListReport->getSingleListReport($sListReportId);	
		// Thuc hien bind du lieu vao view
	   	$this->view->arrInput = $arrResult;
	   // Xu ly nut Tinh trang
	   if($arrResult[0]['C_STATUS']=='HOAT_DONG'){
	   		$this->view->bStatus = true;	   	
	   }else {
			$this->view->bStatus = false;	   	
	   }				
	}	/**
	 * @see : Thuc hien viec sua mot laoi bao cao
	 */
	public function collistAction(){
		// Tieu de cua Form cap  nhat
		$this->view->bodyTitle = 'CÁC TRƯỜNG DỮ LIỆU';
		//Lay danh sach loai TTHC
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype = new listxml_modRecordtype();			
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll( $_SESSION['OWNER_CODE'],'','');		
		//  Lay id cua ban ghi
		$sListReportId = $this->_request->getParam('hdn_object_id');
		$this->view->sListReportId = $sListReportId;
		// Tao doi tuong 
		$objListReport = new Listxml_modListReport();		
	   	$objEfyLibrary = new Extra_Util();
	   	// Lay danh sach loai dinh dang
	   	$arrDataType = $objListReport->getListInfoByCode('DM_XML_DATA_TYPE','','');
	   	$this->view->arrDataType = $arrDataType;
	   	// Lay danh sach hinh thuc thong ke
	   	$arrCalculate = $objListReport->getListInfoByCode('DM_LOAI_THONG_KE','','');
	   	$this->view->arrCalculate = $arrCalculate;
	   	// Lay danh sach can chinh
	   	$arrAlign = $objListReport->getListInfoByCode('DM_CAN_CHINH','','');
	   	$this->view->arrAlign = $arrAlign;	   	
	   	// Lay danh sach loai nguon du lieu
	   	$arrDataSou = $objListReport->getListInfoByCode('DM_NGUON_DL','','');
	   	$this->view->arrDataSou = $arrDataSou;	 
	   	// Lay danh sach ham xu ly
	   	$arrDataFun = $objListReport->getListInfoByCode('DM_HAM_XL','','');	   	 
	   	$this->view->arrDataFun = $arrDataFun;	 
	   	// Lay toan bo tham so truyen tu form			
		$arrInput = $this->_request->getParams();
		//ID cot du lieu
		$sListReportColId = $this->_request->getParam('hdn_report_col_id');
		$this->view->sListReportColId = $sListReportColId;	   
		if($arrInput["hdn_update"]=='1'){	
			$objEfyLibrary = new Extra_Util();
			//Tieu de
			$sColTitle = trim($objEfyLibrary->_restoreXmlBadChar($arrInput['C_TITLE']));		
			//Dinh dang
			$sColDataType = $arrInput['C_DATA_TYPE'];
			$sColWidth = $arrInput['C_WIDTH'];
			$sColAlign = $arrInput['C_ALIGN_TYPE'];
			$sColDataSou = $arrInput['C_DATA_SOU'];
			$sColDataSouName = $arrInput['C_DATA_SOU_NAME'];			
			$sColFuncName = $arrInput['C_FUN_NAME'];		
			$sColCalculate = $arrInput['C_CALCULATE'];
			$sColCondition = $arrInput['C_CONDITION'];
			$iReportOrder = $arrInput['C_ORDER'];			
			$sRecordTypeList = $arrInput['C_RECORD_TYPE_LIST'];	
			//Trang thai cua doi tuong danh muc (HOAT_DONG : hoat dong; NGUNG_HOAT_DONG ; Ngung hoat dong)
			$sStatus = 'NGUNG_HOAT_DONG';
			if ($arrInput['C_STATUS']){
				$sStatus = 'HOAT_DONG';
			}
			//echo '$sListReportId'.$sListReportId;exit;
			$this->view->arrResult = $objListReport->UpdateListReportCol($sListReportColId,$sListReportId,$sColTitle,$sColDataType,$sColWidth,$sColAlign,$sColDataSou,$sColDataSouName,$sColFuncName,$sColCalculate,$sColCondition,$iReportOrder,$sStatus,$sRecordTypeList);
			$this->_redirect('listxml/report/collist/?hdn_object_id='.$sListReportId);			
		}	
		// Lay thong tin trong csdl
		$arrResult = $objListReport->getSingleListReport($sListReportId);	
		//Danh sach TTHC ap dung
		$sRecordTypeList = 'EFY'.$arrResult[0]['C_RECORDTYPE_CODE_LIST'];
		$arrRecordTypeSm = array();
		$iDem = 0;
		for($index = 0;$index < sizeof($arrRecordType) ;$index++){
			if(stripos($sRecordTypeList,$arrRecordType[$index]['C_CODE'])>0){
				$arrRecordTypeSm[$iDem]['C_CODE'] = $arrRecordType[$index]['C_CODE'];
				$arrRecordTypeSm[$iDem]['C_NAME'] = $arrRecordType[$index]['C_NAME'];
				$iDem =$iDem + 1;
			}
		}
		$this->view->arrRecordType = $arrRecordTypeSm;
		// Thuc hien bind du lieu vao view
	   	$this->view->arrInput = $arrResult;
	    //Lay thong tin cot hien hanh
		//$sListReportColId = $this->_request->getParam('hdn_report_col_id');
		//$this->view->sListReportColId = $sListReportColId;	   
		$arrReportColInfo = $objListReport->getSingleListReportCol($sListReportColId);	
		$this->view->arrReportColInfo = $arrReportColInfo;
		//var_dump($arrMaxOrder);
		if($arrReportColInfo[0]['C_ORDER'] == "" or $arrReportColInfo[0]['C_ORDER'] <= 0){
			$this->view->iOrder = 1;
		}else{	
			$this->view->iOrder = $arrReportColInfo[0]['C_ORDER'];
		}
		//var_dump($arrReportColInfo);
	    // Xu ly nut Tinh trang
	    if($arrReportColInfo[0]['C_STATUS']=='HOAT_DONG'){
	   		$this->view->bStatus = true;	   	
	    }else {
			$this->view->bStatus = false;	   	
	    }		
	    //Lay danh sach cot
	    $arrListCol = $objListReport->getAllListColReport($sListReportId,'','');
	    $this->view->arrListCol = $arrListCol;		
	}
	/**
	 * @see : Thuc hien viec xoa
	 */
	public function deleteAction(){
		//Request hidden luu id da duoc chon
		$sListTypeIdList = $this->_request->getParam('hdn_object_id_list');
		// Tao doi tuong 
		$objListReport = new Listxml_modListReport();	
		// thuc hien cau lenh xoa		
		$arrResult = $objListReport->deleteListReport($sListTypeIdList);
		// Kiem tra 
		if($arrResult != null || $arrResult != '' ){											
			echo "<script type='text/javascript'>";
			echo "alert('$arrResult');";	
			echo "</script>";
		}else {
			//Tra ve trang index
			$this->_redirect('listxml/report/index/');
		}	
	}	
	/**
	 * @see : Thuc hien viec xoa
	 */
	public function deletecolAction(){
		//Request hidden luu id da duoc chon
		$sListTypeIdList = $this->_request->getParam('hdn_object_id_list');
		$sListReportId = $this->_request->getParam('hdn_object_id');		
		// Tao doi tuong 
		$objListReport = new Listxml_modListReport();	
		// thuc hien cau lenh xoa		
		$arrResult = $objListReport->deleteListReportCol($sListTypeIdList);
		// Kiem tra 
		if($arrResult != null || $arrResult != '' ){											
			echo "<script type='text/javascript'>";
			echo "alert('$arrResult');";	
			echo "</script>";
		}else {
			//Tra ve trang index
			$this->_redirect('listxml/report/collist/?hdn_object_id='.$sListReportId);
		}	
	}	
/**
 * @see : Thuc hien lay file doc tat ca cac file xml tu o cung cua server
 * @creator: Thainv
 * @createdate: 
 * 
 * 
 * 	*/
	private function showDialog(){
		$dir = "./xml/listreport/";				
		$objConfig = new Extra_Init();
		$sResHtml = $sResHtml. "<div style='overflow:auto;height:95%; width:98%; padding: 6px 2px 2px 2px;'>";	
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	// kt la file xml thi hien thi		        	
		        	$filetype = substr($file,strlen($file)-4,4);			        	
		        	$filetype = strtolower($filetype);
		        	if($filetype == ".xml"){		            	
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