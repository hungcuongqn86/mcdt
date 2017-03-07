<?php

/**
 * Class Record_normalreportController
 */
class Record_normalreportController extends  Zend_Controller_Action {
	public function init(){
		//Efy_Function_RecordFunctions::CheckLogin();	
		//Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		
		//Cau hinh cho Zend_layoutasdfsdfsd
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout'],
			    'layout' => 'index'			    
			    ));	
		//Load ca thanh phan cau vao trang layout (index.phtml)
		$response = $this->getResponse();
		
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();
		
		//Lay so dong tren man hinh danh sach
		$this->view->NumberRowOnPage    = $this->_ConstPublic['NumberRowOnPage'];		
		
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 	= $this->_ConstPublic['delimitor'];
		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
		//Goi lop Listxml_modProject
		Zend_Loader::loadClass('Listxml_modListReport');

		//Lay cac hang so su dung trong JS public
		Zend_Loader::loadClass('Extra_Init');
		$objConfig = new Extra_Init();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();

        //Lay tra tri trong Cookie
        $objLibrary = new Extra_Util();
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
		$this->view->currentModulCode = "REPORTS";
		$this->view->currentModulCodeForLeft = "NORMAL_REPORTS";
		$sActionName = $this->_request->getActionName();
		if ($sActionName == 'general'){
			$this->view->currentModulCodeForLeft = 'REPORTS';
		}	
		//Bien xac dinh An/Hien menu trai cua he thong;
		$this->view->hideDisplayMeneLeft = "none";
		//Hien thi file template
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    	//Hien thi header 
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    		//Hien thi header 		    
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	//Hien thi footer
	}

    /**
     * @throws Zend_Exception
     */
	public function indexAction(){		
		// Tieu de man hinh danh sach
		$this->view->bodyTitle = 'BÁO CÁO THEO TỪNG TTHC';
		$objRecordFunction	     = new Efy_Function_RecordFunctions();	
		//Lay DM Linh vuc TTHC
		$arrCate = $objRecordFunction->getAllObjectbyListCode('',"DANH_MUC_LINH_VUC");
		$this->view->arrCate = $arrCate;
        //var_dump($arrCate);
		//Mang lay cac laoi tthc
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype	  = new listxml_modRecordtype();
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll( $_SESSION['OWNER_CODE'],'','','');
		//var_dump($arrRecordType);
		echo '<script type="text/javascript">var arrRecordType=new Array();var arrValue=new Array();';
	 	$i = 0; 
	 	foreach ($arrRecordType as $value){
	 	    echo 'arrValue=new Array();arrValue[0]="'.$value['C_CODE'].'";'; 
			echo 'arrValue=new Array();arrValue[0]="'.$value['C_CODE'].'";';
			echo 'arrValue[1]="'.Extra_Util::_replaceBadChar($value['C_NAME']).'";';
			echo 'arrValue[2]="'.$value['C_CATE'].'";';
            echo 'arrValue[3]="'.$value['C_STATUS'].'";';
			echo 'arrRecordType['.$i.']=arrValue;';
	 		$i++; 
	 	}
	 	echo '</script>';			
		$this->view->optSelectRecordtype = $objRecordFunction->_generate_select_option($arrRecordType,'C_CODE','C_CODE','C_NAME','');
	}

    /**
     * @throws Zend_Exception
     */
	public function generalAction(){		
		// Tieu de man hinh danh sach
		$this->view->bodyTitle = 'BÁO CÁO CHUNG';
		//Mang lay cac laoi tthc
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype	  = new listxml_modRecordtype();
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll($_SESSION['OWNER_CODE'],'','','');
	 	foreach ($arrRecordType as $value){
			if($value['C_CODE']=='BCTH'){
				$sRecordType = $value['PK_RECORDTYPE'];
				break;
			}
	 	}		
		$this->view->RecordType = $sRecordType;		
	}

    /**
     *
     */
	public function printviewAction(){
		// Tao doi tuong cho lop tren
		$objReport = new Listxml_modListReport() ;
		//Tao doi tuong thu vien xu ly du lieu
		$objXmlLib = new Extra_Xml();
		$objEfyLib = new Extra_Util();
		$objFunction = new Efy_Function_RecordFunctions();	
		//Lay loai TTHC
		$sRecordTypeID=$this->_request->getParam('hdn_recordtype_id',"");
		//Lay danh sach cac THE mo ta tieu tri loc + dung cho nut tim kiem submit tai form
		$sFilterXmlTagList = $this->_request->getParam('hdn_filter_xml_tag_list',"");
		//Lay danh sach cac gia tri tuong ung mo ta tieu tri loc
		$sFilterXmlValueList = $this->_request->getParam('hdn_filter_xml_value_list',"");	
		//Tao chuoi loc $sFilterXmlTagList + $sFilterXmlValueList
		$sFilterXmlString = $objXmlLib->_xmlGenerateXmlDataString($sFilterXmlTagList,$sFilterXmlValueList);
		//echo htmlspecialchars($sFilterXmlString);exit;
		// Lay lai ten file xml
		$sXmlFileName = "xml/listreport/".$this->_request->getParam('hdn_file_xml',"");
		//Lay cau lenh truy xuat du lieu
		$psXmlStringInFile = $objEfyLib->_readFile($sXmlFileName);
		$psSqlString = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile,"report_sql","sql");		
		$psSqlString = $objXmlLib->_replaceTagXmlValueInSql($psSqlString, $psXmlStringInFile, 'table_struct_of_filter_form/filter_row', $sFilterXmlString,'filter_formfield_list');
		$psSqlString = str_replace('#record_type#', $sRecordTypeID, $psSqlString);	
		$psSqlString = str_replace('#owner_code#', $_SESSION['OWNER_CODE'], $psSqlString);
		//echo $psSqlString; exit;
		// Mang chua ban ghi
		$arrResult = $objReport->getAllRecord($psSqlString);
		//Lay thong tin cac truong bao cao
		$psListReportId = $this->_request->getParam('hdn_reporttype_filter',"");
		$arrReportCol = $objReport->getAllListColReport($psListReportId,$sRecordTypeID,'HOAT_DONG');
		// var_dump($arrReportCol);exit;
		// Cot chua noi dung cua bao cao
		$v_colume_name_of_xml_string = 'C_RECEIVED_RECORD_XML_DATA';
		$v_exporttype = $this->_request->getParam('hdn_exporttype',"1");
		//Ket xuat du lieu theo tưng loai dinh dang
		switch($v_exporttype) {
			case 1;
				$sExportFileName = $objFunction->_exportreport($psXmlStringInFile,$arrReportCol,$arrResult,$v_colume_name_of_xml_string,$sFilterXmlString,$v_exporttype);
				break;
			case 2;
				$sExportFileName = $objFunction->_exportreport($psXmlStringInFile,$arrReportCol,$arrResult,$v_colume_name_of_xml_string,$sFilterXmlString,$v_exporttype);
				break;
			case 3;
				$sExportFileName = $objFunction->_exportreportexcel($psXmlStringInFile,$arrReportCol,$arrResult,$v_colume_name_of_xml_string,$sFilterXmlString);
				break;
			default:
				$sExportFileName = $objFunction->_exportreport($psXmlStringInFile,$arrReportCol,$arrResult,$v_colume_name_of_xml_string,$sFilterXmlString,$v_exporttype);
				break;
		}			
		$this->view->sExportFileName = $sExportFileName;
	}
}
?>