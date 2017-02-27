<?php
	/**
	 * Nguoi tao: Phuongtt
	 * ngay tao: 27/12/2010
	 */
	// Dinh nghia duong dan den thu vien cua Zend
	set_include_path('../../library/'
			. PATH_SEPARATOR . '../../application/models/'
			. PATH_SEPARATOR . '../../config/');			
	// Goi class Zend_Load
	include "../../library/Zend/Loader.php";	
	Zend_Loader::loadClass('Zend_Db');	
	Zend_Loader::loadClass('Efy_DB_Connection');
	Zend_Loader::loadClass('Efy_Init_Config');
	Zend_Loader::loadClass('Zend_Config_Ini');
	Zend_Loader::loadClass('Zend_Config_Xml');
	Zend_Loader::loadClass('Zend_Registry');
	Zend_Loader::loadClass('Efy_Function_RecordFunctions');
	Zend_Loader::loadClass('Efy_Library');
	Zend_Loader::loadClass('Efy_Xml');
	$conn = new Efy_DB_Connection();
	$sConfig = new Efy_Init_Config();
	//Ket noi CSDL SQL theo kieu ADODB
	$connectSQL = new Zend_Config_Ini('../../config/config.ini','dbmssql');
	$registry = Zend_Registry::getInstance();
	$registry->set('connectSQL', $connectSQL);
	$connAdo = Efy_Db_Connection::connectADO($connectSQL->db->adapter,$connectSQL->db->config->toArray());
	//Khoi tao session
	session_start();
	//Lay ten ham thuc hien
	$sFunctionName = $_REQUEST['FuntionName'];
	if($sFunctionName != '' && $sFunctionName != null){	
		$sFunctionName .= '();';
		eval($sFunctionName);
	}
	/**
	 * cuongnh
	 * Lay danh sach ho so co sap xep ho so khi click vao tieu de danh sach...
	 */
	function getAllRecord(){
		isset($_REQUEST['sRole'])?$sRole = $_REQUEST['sRole']:$sRole = '';
		isset($_REQUEST['hdn_sDetailStatusCompare'])?$sDetailStatusCompare = $_REQUEST['hdn_sDetailStatusCompare']:$sDetailStatusCompare = '';
		isset($_REQUEST['hdn_sStatusList'])?$sStatusList = $_REQUEST['hdn_sStatusList']:$sStatusList = '';
		isset($_REQUEST['hdn_sRecordType'])?$sRecordType = $_REQUEST['hdn_sRecordType']:$sRecordType = '';
		isset($_REQUEST['toDate'])?$dToDate = $_REQUEST['toDate']:$dToDate = '';
		isset($_REQUEST['fromDate'])?$dFromDate = $_REQUEST['fromDate']:$dFromDate = '';
		isset($_REQUEST['recordType'])?$sRecordTypeId = $_REQUEST['recordType']:$sRecordTypeId = '';
		isset($_REQUEST['txtfullTextSearch'])?$sfullTextSearch = $_REQUEST['txtfullTextSearch']:$sfullTextSearch = '';
		isset($_REQUEST['hdn_current_page'])?$iPage = $_REQUEST['hdn_current_page']:$iPage = '1';
		isset($_REQUEST['iNumberRecordPerPage'])?$iNumberRecordPerPage = $_REQUEST['iNumberRecordPerPage']:$iNumberRecordPerPage = '15';	
		isset($_REQUEST['iCurrentStaffId'])?$iCurrentStaffId = $_REQUEST['iCurrentStaffId']:$iCurrentStaffId = '';
		isset($_REQUEST['sOwnerCode'])?$sOwnerCode = $_REQUEST['sOwnerCode']:$sOwnerCode = '';		
		isset($_REQUEST['dataordername'])?$dataordername = $_REQUEST['dataordername']:$dataordername = '';		
		isset($_REQUEST['datatype'])?$datatype = $_REQUEST['datatype']:$datatype = '';	
		isset($_REQUEST['hdn_recordTypeCode'])?$sRecordTypeCode = $_REQUEST['hdn_recordTypeCode']:$sRecordTypeCode = '';
		isset($_REQUEST['hdn_order'])?$shdn_order = $_REQUEST['hdn_order']:$shdn_order = '1';		
		$objconfig = new Efy_Init_Config();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$ojbEfyLib = new Efy_Library();
		$objxml = new Efy_Xml();
		if ($iPage <= 1)
			$iPage = 1;
		if ($iNumberRecordPerPage == 0)
			$iNumberRecordPerPage = 15;	
		$dFromDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dFromDate);
		$dToDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dToDate);		
		$sOrderClause = '';
		if($datatype=='column_name'){
			$sOrderClause = ' Order by '.$dataordername;
		}else{
			$sOrderClause = " Order by dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA,''".$dataordername."'')";
		}
		if($shdn_order=='2'){
			$sOrderClause = $sOrderClause . ' desc';
		}
		$arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,'',$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage,$dFromDateTemp,$dToDateTemp);
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/ho_so_da_tiep_nhan.xml';	
		}	
		$sGenlist = "<input type='hidden' id='hdn_OrderClause' name='hdn_OrderClause' value='".$sOrderClause."' >";
		$sGenlist = $sGenlist . $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,'../viewrecord/',$dataordername,$shdn_order);
		$iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
		if (count($arrRecord) > 0){
			$sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,'') ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,'');
		}
		$sGenlist = $sGenlist.'<table width="100%" cellpadding="0" cellspacing="0" border="0">';
		$sGenlist = $sGenlist.'<tr><td style="color:red;width:30%;padding-left:1%;" class="small_label">';
		$sGenlist = $sGenlist.'<small class="small_starmark">'.$sdocpertotal.'</small>';
		$sGenlist = $sGenlist.'</td><td align="center" style="width="50%">';
		$sGenlist = $sGenlist.'<table width="10%">';
		$sGenlist = $sGenlist.$generateStringNumberPage;
		$sGenlist = $sGenlist.'</table></td>';
		$sGenlist = $sGenlist.'<td width="30%" align="right" style="font-size:13px; padding-right:1%; font:tahoma" class="normal_label">';
		$sGenlist = $sGenlist.$generateHtmlSelectBoxPage;
		$sGenlist = $sGenlist.'</td></tr></table>';
		echo $sGenlist;	
	}
	/**
	 * cuongnh
	 * Lay danh sach ho so
	 */
	function getAllRecordAjax(){
		isset($_REQUEST['sRole'])?$sRole = $_REQUEST['sRole']:$sRole = '';
		isset($_REQUEST['hdn_sDetailStatusCompare'])?$sDetailStatusCompare = $_REQUEST['hdn_sDetailStatusCompare']:$sDetailStatusCompare = '';
		isset($_REQUEST['hdn_sStatusList'])?$sStatusList = $_REQUEST['hdn_sStatusList']:$sStatusList = '';
		isset($_REQUEST['toDate'])?$dToDate = $_REQUEST['toDate']:$dToDate = '';
		isset($_REQUEST['fromDate'])?$dFromDate = $_REQUEST['fromDate']:$dFromDate = '';
		isset($_REQUEST['recordType'])?$sRecordTypeId = $_REQUEST['recordType']:$sRecordTypeId = '';
		isset($_REQUEST['txtfullTextSearch'])?$sfullTextSearch = $_REQUEST['txtfullTextSearch']:$sfullTextSearch = '';
		isset($_REQUEST['hdn_current_page'])?$iPage = $_REQUEST['hdn_current_page']:$iPage = '1';
		isset($_REQUEST['iNumberRecordPerPage'])?$iNumberRecordPerPage = $_REQUEST['iNumberRecordPerPage']:$iNumberRecordPerPage = '15';	
		isset($_REQUEST['iCurrentStaffId'])?$iCurrentStaffId = $_REQUEST['iCurrentStaffId']:$iCurrentStaffId = '';
		isset($_REQUEST['sOwnerCode'])?$sOwnerCode = $_REQUEST['sOwnerCode']:$sOwnerCode = '';		
		isset($_REQUEST['hdn_xml_file_name'])?$sxmlFileName = $_REQUEST['hdn_xml_file_name']:$sxmlFileName = '';	
		isset($_REQUEST['hdn_OrderClause'])?$sOrderClause = $_REQUEST['hdn_OrderClause']:$sOrderClause = '';	
		$arrRecordType = $_SESSION['arr_all_record_type'];
		$objconfig = new Efy_Init_Config();
		$objrecordfun = new Efy_Function_RecordFunctions();
		$ojbEfyLib = new Efy_Library();
		$objxml = new Efy_Xml();
		if ($iPage <= 1)
			$iPage = 1;
		if ($iNumberRecordPerPage == 0)
			$iNumberRecordPerPage = 15;	
		$dFromDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dFromDate);
		$dToDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dToDate);
		//Lay cac tham so truyen vao sp
		//1 - Neu la ho so cho duyet
		if($sStatusList=='TRINH_KY'){
			$sApproveLevel = $objrecordfun->eCSGetPermisstionApproveForRecordType2($iCurrentStaffId, $arrRecordType, $sRecordTypeId);
			//echo $sApproveLevel;
			switch ($sApproveLevel) {
				case 'DUYET_CAP_MOT':
					$sDetailStatusCompare = " And A.C_DETAIL_STATUS = 21";
				break;
				case 'DUYET_CAP_HAI':
					$sDetailStatusCompare = " And A.C_DETAIL_STATUS = 31";
				break;
				case 'DUYET_CAP_BA':
					$sDetailStatusCompare = " And A.C_DETAIL_STATUS = 41";
				break;
				case '':
					$sApproveLevel = 'THULY_CHINH';
					//Kiem tra NSD dang nhap hien thoi la thu ly cap nao
					$sHandleType = $objrecordfun->eCSPermisstionHandlerTypeForRecordType($iCurrentStaffId, $arrRecordType, $sRecordTypeId);
					//Kiem tra xem la ho lien thong hay ko lien thong
					if($sHandleType == 'THULY_CAP_MOT') $sDetailStatusCompare = 'And (A.C_DETAIL_STATUS = 21 or A.C_DETAIL_STATUS = 41)';
					if($sHandleType == 'THULY_CAP_HAI') $sDetailStatusCompare = 'And (A.C_DETAIL_STATUS = 31 or A.C_DETAIL_STATUS = 41)';
				break;				
				default:
					$sDetailStatusCompare = "";
				break;
			}			
			$sRole = $sApproveLevel;			
		}
		if(($sStatusList=='CHO_PHAN_CONG')||($sStatusList == 'DA_PHAN_CONG')){
			//Lay phong ban
			$iFkUnit = $objrecordfun->doc_get_all_unit_permission_form_staffIdList($iCurrentStaffId);			
			//Kiem tra xem la ho lien thong hay ko lien thong
			if($objrecordfun->eCSPermisstionApproveForRecordType($iCurrentStaffId, 'DUYET_CAP_MOT', $arrRecordType,$sRecordTypeId) == 'DUYET_CAP_MOT'){
				$sDetailStatusCompare = " Inner Join T_eCS_RECORD_RELATE_UNIT Z On PK_RECORD = Z.FK_RECORD And Z.FK_UNIT=''".$iFkUnit."'' ";
			}	
			if($objrecordfun->eCSPermisstionApproveForRecordType($iCurrentStaffId, 'DUYET_CAP_HAI', $arrRecordType,$sRecordTypeId) == 'DUYET_CAP_HAI'){
				$sDetailStatusCompare = " Inner Join T_eCS_RECORD_RELATE_UNIT Z On PK_RECORD = Z.FK_RECORD And Z.FK_UNIT=''".$iFkUnit."''  And (A.C_DETAIL_STATUS = 31)";
			}	
			if ($sStatusList == 'DA_PHAN_CONG') $sStatusList = "THU_LY";
		}
		$arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
		$sRecordTypeCode = $arrinfoRecordType['C_CODE'];
		$sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];
		//Lay mang danh sach ho so
		if(($sStatusList == 'CHO_TIEP_NHAN_SO_BO,DA_BO_XUNG_CHO_NHAN_SO_BO')||($sStatusList == 'CHO_BO_SUNG_QUA_MANG')){
			Zend_Loader::loadClass('record_modReceiveonnet');
			$objReceiveonnet = new record_modReceiveonnet();
			$sXml_data = "C_XML_DATA";
			$sColId = "PK_NET_RECORD";
			if($sStatusList == 'CHO_BO_SUNG_QUA_MANG'){
				$sStatusList = 'CHO_BO_SUNG';
			}
			$arrRecord = $objReceiveonnet->eCSNetReceiveRecordGetAll($sRecordTypeId,$sOwnerCode,$sfullTextSearch,$sStatusList,$iPage,$iNumberRecordPerPage);
		}elseif($sStatusList == 'DA_TIEP_NHAN_QUA_MANG'){
			Zend_Loader::loadClass('record_modReceiveonnet');
			$objReceiveonnet = new record_modReceiveonnet();
			$sXml_data = "C_XML_DATA";
			$sColId = "PK_NET_RECORD";
			$arrRecord = $objReceiveonnet->eCSNetOfficialRecordGetAll($sRecordTypeId,$sOwnerCode,$sfullTextSearch,'DA_TIEP_NHAN',$iPage,$iNumberRecordPerPage);
		}else{
			$sXml_data = "C_RECEIVED_RECORD_XML_DATA";
			$sColId = "PK_RECORD";
			$arrRecord = $objrecordfun->eCSRecordGetAll($sRecordTypeId,$sRecordType,$iCurrentStaffId,'',$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage,$dFromDateTemp,$dToDateTemp);
		}
		$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$sRecordTypeCode.'/'.$sxmlFileName;
		//echo $sxmlFileName;exit;
		if(!file_exists($sxmlFileName)){
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/ho_so_da_tiep_nhan.xml';	
		}	
		//echo $sxmlFileName;//exit;
		$sGenlist = "<input type='hidden' id='hdn_OrderClause' name='hdn_OrderClause' value='".$sOrderClause."' >";
		$sGenlist = $sGenlist . $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord,$sXml_data,$sColId,$sfullTextSearch,false,false,'../viewrecord/');
		$iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
		if (count($arrRecord) > 0){
			$sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,'') ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,'');
		}
		$sGenlist = $sGenlist.'<table width="100%" cellpadding="0" cellspacing="0" border="0">';
		$sGenlist = $sGenlist.'<tr><td style="color:red;width:30%;padding-left:1%;" class="small_label">';
		$sGenlist = $sGenlist.'<small class="small_starmark">'.$sdocpertotal.'</small>';
		$sGenlist = $sGenlist.'</td><td align="center" style="width="50%">';
		$sGenlist = $sGenlist.'<table width="10%">';
		$sGenlist = $sGenlist.$generateStringNumberPage;
		$sGenlist = $sGenlist.'</table></td>';
		$sGenlist = $sGenlist.'<td width="30%" align="right" style="font-size:13px; padding-right:1%; font:tahoma" class="normal_label">';
		$sGenlist = $sGenlist.$generateHtmlSelectBoxPage;
		$sGenlist = $sGenlist.'</td></tr></table>';
		echo $sGenlist;	
	}	
	/**
	 * cuongnh
	 * Lay danh sach ho so
	 */
	function getAllRecordSeachAjax(){
		isset($_REQUEST['hdn_recordTypeCode'])?$sRecordTypeCode = $_REQUEST['hdn_recordTypeCode']:$sRecordTypeCode = '';
		isset($_REQUEST['sOwnerCode'])?$sOwnerCode = $_REQUEST['sOwnerCode']:$sOwnerCode = '';
		isset($_REQUEST['iPage'])?$iPage = $_REQUEST['iPage']:$iPage = '1';
		isset($_REQUEST['iNumberRecordPerPage'])?$iNumberRecordPerPage = $_REQUEST['iNumberRecordPerPage']:$iNumberRecordPerPage = '15';	
		isset($_REQUEST['sSearchCheck'])?$sSearchCheck = $_REQUEST['sSearchCheck']:$sSearchCheck = '';
		isset($_REQUEST['sStatus'])?$sStatus = $_REQUEST['sStatus']:$sStatus = '';
		isset($_REQUEST['sfullTextSearch'])?$sfullTextSearch = $_REQUEST['sfullTextSearch']:$sfullTextSearch = '';
		isset($_REQUEST['dToDate'])?$dToDate = $_REQUEST['dToDate']:$dToDate = '';
		isset($_REQUEST['dFromDate'])?$dFromDate = $_REQUEST['dFromDate']:$dFromDate = '';
		isset($_REQUEST['sRecordTypeId'])?$sRecordTypeId = $_REQUEST['sRecordTypeId']:$sRecordTypeId = '';
		isset($_REQUEST['hdn_xml_value_list'])?$hdn_xml_value_list = $_REQUEST['hdn_xml_value_list']:$hdn_xml_value_list = '';
		isset($_REQUEST['hdn_xml_tag_list'])?$hdn_xml_tag_list = $_REQUEST['hdn_xml_tag_list']:$hdn_xml_tag_list = '';
		
		$objconfig = new Efy_Init_Config();
		$ojbEfyLib = new Efy_Library();
		$objxml = new Efy_Xml();
		if ($iPage <= 1)
			$iPage = 1;
		if ($iNumberRecordPerPage == 0)
			$iNumberRecordPerPage = 15;	
		$dFromDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dFromDate);
		$dToDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dToDate);
		//Lay file XML mo ta form danh sach ho so tim kiem
		$sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';
		if(!file_exists($sSearchXmlFileName)){
			$sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/tim_kiem_nang_cao.xml';	
		}		
		$sXmlOperatorList = '';
		$sXmlTrueFalseList = '';
		$sDelimetor = '!~~!';
		if($sSearchCheck){
			//Lay the chua noi dung toan tu
			$sXmlOperatorList = $objxml->_xmlGetXmlTagValueFromFile($sSearchXmlFileName,'list_of_object','filter_formfield_list',$hdn_xml_tag_list,"compare_operator",$sDelimetor);	
			$sXmlTrueFalseList = $objxml->_xmlGetXmlTagValueFromFile($sSearchXmlFileName,'list_of_object','filter_formfield_list',$hdn_xml_tag_list,"xml_data",$sDelimetor);	
		}
		Zend_Loader::loadClass('record_modSearch');
		$objSearch = new Record_modSearch();
		if($sStatus == 'TRA_KET_QUA')  $sStatus = 'CAP_PHEP';
		$arrRecord = $objSearch->eCSSearchGetAll($sRecordTypeId,'KHONG_LIEN_THONG',$dFromDateTemp,$dToDateTemp,$sfullTextSearch,$sStatus,$sOwnerCode,$hdn_xml_tag_list,$hdn_xml_value_list,$sXmlOperatorList,$sXmlTrueFalseList,$sDelimetor,$iPage,$iNumberRecordPerPage);
		//
		$sXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$sRecordTypeCode.'/danh_sach_ho_so_tim_kiem.xml';
		if(!file_exists($sXmlFileName)){
			$sXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/danh_sach_ho_so_tim_kiem.xml';	
		}		
		$sGenlist = $objxml->_xmlGenerateList($sXmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,$sAction = '../viewdetailsrecord/');
		$iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
		if (count($arrRecord) > 0){
			$sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
			//Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
			$generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,'') ;
			//Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
			$generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,'');
		}
		$sGenlist = $sGenlist.'<table width="100%" cellpadding="0" cellspacing="0" border="0">';
		$sGenlist = $sGenlist.'<tr><td style="color:red;width:30%;padding-left:1%;" class="small_label">';
		$sGenlist = $sGenlist.'<small class="small_starmark">'.$sdocpertotal.'</small>';
		$sGenlist = $sGenlist.'</td><td align="center" style="width="50%">';
		$sGenlist = $sGenlist.'<table width="10%">';
		$sGenlist = $sGenlist.$generateStringNumberPage;
		$sGenlist = $sGenlist.'</table></td>';
		$sGenlist = $sGenlist.'<td width="30%" align="right" style="font-size:13px; padding-right:1%; font:tahoma" class="normal_label">';
		$sGenlist = $sGenlist.$generateHtmlSelectBoxPage;
		$sGenlist = $sGenlist.'</td></tr></table>';
		echo $sGenlist;	
	}		
	/**
	 * cuongnh
	 * Lay danh sach ho so
	 */
	function getfilterOnclick(){
		isset($_REQUEST['hdn_recordTypeCode'])?$sRecordTypeCode = $_REQUEST['hdn_recordTypeCode']:$sRecordTypeCode = '';
		$sFilterXmlString = '';
		if(trim($sFilterXmlString) == '') $sFilterXmlString = '<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>';
		//Lay file XML mo ta form cac tieu thuc loc phuc vu tim kiem nang cao
		$objconfig = new Efy_Init_Config();
		$sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';	
		if(!file_exists($sSearchXmlFileName)){
			$sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/tim_kiem_nang_cao.xml';	
		}		
		$objxml = new Efy_Xml();
		//Tao form hien thi tieu tri loc	
		$sfilter = $objxml->_xmlGenerateFormfield($sSearchXmlFileName, 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row','list_of_object/filter_formfield_list',$sFilterXmlString,null,true,false);
		echo $sfilter;	
	}		
	/**
	 * cuongnh
	 * Lay danh sach ho so trung lap ...
	 */
	function getIdenticalRecord(){
		global $conn;
		$sGenlist = '';
		isset($_REQUEST['recordtype'])?$recordtype = $_REQUEST['recordtype']:$recordtype = '';
		isset($_REQUEST['tablist'])?$tablist = $_REQUEST['tablist']:$tablist = '';
		isset($_REQUEST['tabvalue'])?$tabvalue = $_REQUEST['tabvalue']:$tabvalue = '';
		isset($_REQUEST['operator'])?$operator = $_REQUEST['operator']:$operator = '';
		isset($_REQUEST['valuedf'])?$valuedf = $_REQUEST['valuedf']:$valuedf = '';
		isset($_REQUEST['relations'])?$relations = $_REQUEST['relations']:$relations = '';
		//echo $recordtype.'-'.$tablist.'-'.$tabvalue.'-'.$operator;
		$arrtablist = explode(",",$tablist);
		$arrvaluelist = explode(",",$tabvalue);
		$arroperatorlist = explode(",",$operator);
		$arrvaluedf = explode(",",$valuedf);
		$sql = "Select convert(varchar(50),PK_RECORD) as PK_RECORD,C_CODE,C_RECEIVED_DATE,C_RECEIVED_RECORD_XML_DATA,C_LICENSE_XML_DATA from T_eCS_RECORD Where 1=1";
		$sql = $sql." And FK_RECORDTYPE in (select PK_RECORDTYPE from T_eCS_RECORDTYPE where C_CODE ='".$recordtype."')";
		$counttab = sizeof($arrtablist);
		if($counttab>0){
			$sqltablist = $relations;
			for($tabIndex = 0;$tabIndex< $counttab;$tabIndex++){
				if($arrvaluelist[$tabIndex]<>''){
					if($arroperatorlist[$tabIndex]=='='){
						$sqltab = "convert(nvarchar(1000),C_DATA_TEMP.query('/root/data_list/".$arrtablist[$tabIndex]."/text()'))= '".strtoupper($arrvaluelist[$tabIndex])."'";
					}else{
						$sqltab = "convert(nvarchar(1000),C_DATA_TEMP.query('/root/data_list/".$arrtablist[$tabIndex]."/text()')) like dbo.Lower2Upper('%".$arrvaluelist[$tabIndex]."%')";
					}
				}else{
					$sqltab = $arrvaluedf[$tabIndex];
				}
				$sqltablist = str_replace($arrtablist[$tabIndex],$sqltab,$sqltablist);
			}
			$sql = $sql." And (".$sqltablist.")";
		}
		//echo $sql;
		try {			
			$arrResult = $conn->adodbQueryDataInNameMode($sql); 
		}catch (Exception $e){
			return $e->getMessage();
		}
		if(sizeof($arrResult)>0){
			$objconfig = new Efy_Init_Config();
			$objxml = new Efy_Xml();
			$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/'.$recordtype.'/ho_so_trung_lap.xml';
			if(!file_exists($sxmlFileName)){
				$sxmlFileName = $objconfig->_setXmlFileUrlPath(2).'record/other/ho_so_trung_lap.xml';	
			}	
			$sGenlist = $sGenlist . $objxml->_xmlGenerateIdenticalRecordList($sxmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",'','../viewrecord/');
		}	
		echo $sGenlist;	
	}
	/**
	 * cuongnh
	 * Lay loai bao cao theo tung TTHC
	 */
	function getReportTypeList(){
		global $conn;
		$sGenlist = '';
		isset($_REQUEST['recordtype'])?$recordtype = $_REQUEST['recordtype']:$recordtype = '';
		isset($_REQUEST['ownercode'])?$ownercode = $_REQUEST['ownercode']:$ownercode = '';
		// Tao doi tuong cho lop tren
		Zend_Loader::loadClass('Listxml_modListReport');
		$objReport = new Listxml_modListReport() ;
		$arrreport=$objReport->getAllListReport('HOAT_DONG','',$ownercode,$recordtype);	
		$sGenlist =	'<table style="border:1px solid #DDDDDD;" width="100%" cellpadding="0" cellspacing="0"><col width="5%"><col width="95%">';
		for ($i=0;$i<sizeof($arrreport);$i++) {
			$v_report_list_id = $arrreport[$i]['PK_LIST_RECORD'];
			$v_report_name = $arrreport[$i]['C_NAME'];														
			$sGenlist = $sGenlist . '<tr style="border:1px solid #CCCC99;height: 25px">';
			$sGenlist = $sGenlist . '<td style="font-size:13px;padding: 0 3pt 3pt 3pt;font-family:Arial;">';
			$sGenlist = $sGenlist . '<input type="radio" name="sel_reporttype" readonly="true" value="'.$v_report_list_id.'" onClick="changeReportType(\''.$v_report_list_id.'\',\''.$arrreport[$i]['C_XML_FILE_NAME'].'\');" message="Phải chọn báo cáo"></td>';
			$sGenlist = $sGenlist . '<td colspan="10" style="font-size:13px;padding: 0 3pt 0 3pt;font-family:Arial;color:#000000;">';
			$sGenlist = $sGenlist . '<font onclick = "set_checked_onlabel(document.getElementsByName(\'sel_reporttype\'),\''.$v_report_list_id.'\',\'radio\',\''.$arrreport[$i]['C_XML_FILE_NAME'].'\')" class="normal_label; ">'.$v_report_name.'</font>';
			$sGenlist = $sGenlist .	'</td></tr>';
		}
		$sGenlist = $sGenlist .	'</table>';	
		echo $sGenlist;	
	}
	/**
	 * cuongnh
	 * Lay tieu chi loc theo loai bao cao
	 */
	function getReportFillterList(){
		global $conn;
		$ojbEfyLib = new Efy_Library();
		$objconfig = new Efy_Init_Config();
		$objxml = new Efy_Xml();
		$sGenlist = '';
		isset($_REQUEST['recordtype'])?$recordtype = $_REQUEST['recordtype']:$recordtype = '';
		isset($_REQUEST['reporttype'])?$sReportID = $_REQUEST['reporttype']:$sReportID = '';
		isset($_REQUEST['xmlname'])?$sxmlname = $_REQUEST['xmlname']:$sxmlname = '';
		isset($_REQUEST['ownercode'])?$ownercode = $_REQUEST['ownercode']:$ownercode = '';
		//Goi lop Listxml_modListType
		Zend_Loader::loadClass('listxml_modRecordtype');
		$objRecordtype	  = new listxml_modRecordtype();
		$arrRecordType = $objRecordtype->eCSRecordTypeGetAll($ownercode,'','','');
		//var_dump($arrRecordType);exit;
		///Lay sesion can bo thu ly cua TTHC
		for ($i=0;$i<sizeof($arrRecordType);$i++){
			if($recordtype==$arrRecordType[$i]['C_CODE']){
				$sHandleList = $arrRecordType[$i]['C_HANDLER_ID_LIST'];
				break;
			}
		}		
		if($sHandleList<>''){
			$arrHandleList = array();
			$arrHandleIdList = explode(',',$sHandleList);
			for ($index = 0; $index<sizeof($arrHandleIdList); $index++){
				$arrHandleList[$index]['id'] = $arrHandleIdList[$index];
				$arrHandleList[$index]['name'] =  $ojbEfyLib->_getItemAttrById($_SESSION['arr_all_staff'],$arrHandleIdList[$index],'name');
			}
			$_SESSION['arrHandleList'] = $arrHandleList;
		}
		if($sxmlname!=''){
			$v_xml_file = $objconfig->_setXmlFileUrlPath(2) . "listreport/". $sxmlname;
			//echo $v_xml_file;exit;
			$sGenlist = $sGenlist . $objxml->_xmlGenerateFormfield($v_xml_file, 'table_struct_of_filter_form/filter_row','filter_formfield_list','<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>', array(),true,true);
			$sGenlist = $sGenlist . '<script language="JavaScript">'. $objxml->_xmlGenerateCalendar($v_xml_file,  'table_struct_of_filter_form/filter_row','filter_formfield_list','<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>');
			$sGenlist = $sGenlist . '</script>';
		}		
		echo $sGenlist;	
	}
?>