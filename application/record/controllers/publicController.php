<?php

/**
 * Class record_receiveController
 */
class record_publicController extends  Zend_Controller_Action {
	public function init(){

  	}
    /*
     *
     */
    public function checkpermisiontransitionAction(){
        $str_return = '';
        $conn = new G_Db();
        isset($_REQUEST['listrecord'])?$sListRecord = $_REQUEST['listrecord']:$sListRecord = '';
        isset($_REQUEST['work_type'])?$swork_type   = $_REQUEST['work_type']:$swork_type = '';
        if($sListRecord !== ''){
            $psSql = "eCS_CheckPermisionTransitionStaff";
            $arrParameter = array($sListRecord,$_SESSION['staff_id'],$swork_type,$_SESSION['OWNER_CODE']);
            $arrRecord = $conn->pdoExecSP($psSql,$arrParameter);
            $str_return = $arrRecord[0]['LIST_RECORD_ID']."|~|".$arrRecord[0]['C_CODE'];
        }
        echo $str_return;
        exit;
    }
    /**
     *
     */
    public function getallrecordAction(){
        isset($_REQUEST['sRole'])?$sRole = $_REQUEST['sRole']:$sRole = '';
        isset($_REQUEST['hdn_sDetailStatusCompare'])?$sDetailStatusCompare = $_REQUEST['hdn_sDetailStatusCompare']:$sDetailStatusCompare = '';
        isset($_REQUEST['hdn_sStatusList'])?$sStatusList = $_REQUEST['hdn_sStatusList']:$sStatusList = '';
        isset($_REQUEST['recordType'])?$sRecordTypeId = $_REQUEST['recordType']:$sRecordTypeId = '';
        isset($_REQUEST['txtfullTextSearch'])?$sfullTextSearch = $_REQUEST['txtfullTextSearch']:$sfullTextSearch = '';
        isset($_REQUEST['hdn_current_page'])?$iPage = $_REQUEST['hdn_current_page']:$iPage = 1;
        isset($_REQUEST['iNumberRecordPerPage'])?$iNumberRecordPerPage = $_REQUEST['iNumberRecordPerPage']:$iNumberRecordPerPage = 15;
        isset($_REQUEST['hdn_xml_file_name'])?$sxmlFileName = $_REQUEST['hdn_xml_file_name']:$sxmlFileName = '';
        isset($_REQUEST['hdn_OrderClause'])?$sOrderClause = $_REQUEST['hdn_OrderClause']:$sOrderClause = '';
        isset($_REQUEST['hdn_prev_period_code'])?$sprev_period_code = $_REQUEST['hdn_prev_period_code']:$sprev_period_code = '';
        isset($_REQUEST['hdn_period_code'])?$speriod_code = $_REQUEST['hdn_period_code']:$speriod_code = '';

        $sDetailStatusCompare = str_replace("\\","",$sDetailStatusCompare);
        $sOrderClause = str_replace("\\","",$sOrderClause);
        //Nguoi dang nhap hien thoi
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        //Nguoi dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        $arrRecordType = $_SESSION['arr_all_record_type'];
        $objconfig = new Extra_Init();
        $objrecordfun = new Efy_Function_RecordFunctions();
        $ojbEfyLib = new Efy_Library();
        $objxml = new Extra_Xml();

        $objConn = new  G_Db();
        $iFkUnit = $objrecordfun->getValueInArray($_SESSION['arr_all_staff'],'id','unit_id',$iCurrentStaffId);
        if($sRecordTypeId == ""){
            if($_SESSION['RECORD_TYPE'] !== ""){
                $sRecordTypeId=$_SESSION['RECORD_TYPE'];
            }else{
                $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
            }
        }
        $_SESSION['RECORD_TYPE']=$sRecordTypeId;
        if ($iPage <= 1)
            $iPage = 1;
        if ($iNumberRecordPerPage == 0)
            $iNumberRecordPerPage = 15;
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $sRecordType = $arrinfoRecordType['C_RECORD_TYPE'];

        //Lay du lieu
        if(($sStatusList == 'CHO_TIEP_NHAN_SO_BO,DA_BO_XUNG_CHO_NHAN_SO_BO')||($sStatusList == 'CHO_BO_SUNG_QUA_MANG') ||($sStatusList == 'DA_TIEP_NHAN_QUA_MANG')){
            $sSp = '[eCS_NetReceiveRecordGetAll]';
            $arrParameter = array($sRecordTypeId,$sOwnerCode,$sfullTextSearch,$sStatusList,$iPage,$iNumberRecordPerPage);
            $arrRecord =  $objConn->pdoExecSP($sSp,$arrParameter);
            $sColId = "PK_NET_RECORD";
            $sXml_data = "C_XML_DATA";
        }else if($sStatusList == 'LIEN_THONG_PHUONG_XA'){
            $arrParameter = array($sRecordTypeId,$sRole,$iCurrentStaffId,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage);
            $arrRecord = $objConn->pdoExecSP('eCS_RecordTransitionGetAll',$arrParameter);
            $sXml_data = "C_RECEIVED_RECORD_XML_DATA";
            $sColId = "PK_RECORD";
        }else{
            $sSp = '[eCS_RecordGetAll]';
            $arrParameter = array($sRecordTypeId,$sRecordType,$iCurrentStaffId,'',$sStatusList,$sDetailStatusCompare,$sRole,$sOrderClause,$sOwnerCode,$sfullTextSearch,$iPage,$iNumberRecordPerPage,'','',$iFkUnit);
            $arrRecord =  $objConn->pdoExecSP($sSp,$arrParameter);
            $sXml_data = "C_RECEIVED_RECORD_XML_DATA";
            $sColId = "PK_RECORD";
        }
        //Dung man hinh danh sach
        $sxmlFileName2 = $sxmlFileName;
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/'.$sxmlFileName;
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/'.$sxmlFileName2;
        }

        $sGenlist = $objxml->_xmlGenerateList($sxmlFileName,'col',$arrRecord,$sXml_data,$sColId,$sfullTextSearch,false,'../viewrecord/',$sprev_period_code,$speriod_code);
        $sdocpertotal = 'Danh sách này không có hồ sơ nào';
        $generateStringNumberPage ='';
        $generateHtmlSelectBoxPage = '';
        if (count($arrRecord) > 0){
            $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
            $sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,'') ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,'');
        }
        $sGenlist .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
        $sGenlist .= '<tr><td style="color:red;width:30%;padding-left:1%;" class="small_label">';
        $sGenlist .= '<small class="small_starmark">'.$sdocpertotal.'</small>';
        $sGenlist .= '</td><td align="center" style="width:30%">';
        $sGenlist .= '<table width="10%">';
        $sGenlist .= $generateStringNumberPage;
        $sGenlist .= '</table></td>';
        $sGenlist .= '<td style="width:30%;font-size:13px; padding-right:1%; font:tahoma;text-align: right;" class="normal_label">';
        $sGenlist .= $generateHtmlSelectBoxPage;
        $sGenlist .= '</td></tr></table>';
        echo $sGenlist;
        exit;
    }
    /**
     *
     */
    public function showinfoAction(){
        isset($_REQUEST['modal'])?$sModal = $_REQUEST['modal']:$sModal = 'off';
        isset($_REQUEST['pkrecord'])?$sRecordPk = $_REQUEST['pkrecord']:$sRecordPk = '';
        isset($_REQUEST['ownercode'])?$sOwnerCode = $_REQUEST['ownercode']:$sOwnerCode = $_SESSION['OWNER_CODE'];
        //Goi cac doi tuong
        $objInitConfig 			 = new Extra_Init();
        $objRecordFunction	     = new Efy_Function_RecordFunctions();
        $ojbEfyLib				 = new Efy_Library();
        $conn 					 = new G_Db();
        //Thong tin ho so
        $psSql = "eCS_SearchGetSingle";
        $arrParameter = array($sRecordPk,$sOwnerCode);
        $arrRecord = $conn->pdoExecSP($psSql,$arrParameter);
        //var_dump($arrRecord);exit;
        //Lay file dinh kem
        $sql = "Doc_GetAllDocumentFileAttach";
        $arrParameter = array($sRecordPk,'HO_SO','T_eCS_RECORD');
        $arrFile = $conn->pdoExecSP($sql,$arrParameter);
        $appoint_date = $arrRecord[0]['C_APPOINTED_DATE'];
        $tax_date = $arrRecord[0]['C_TAX_APPOINTED_DATE'];
        $psSql = "eCS_HandleWorkGetAll";
        $arrParameter = array($sRecordPk,$sOwnerCode);
        $arrWork = $conn->pdoExecSP($psSql,$arrParameter);

        $arrConst = $objInitConfig->_setProjectPublicConst();

        $ResHtmlString = '<div id="tabs" style="margin-top: 8px;">';
        $ResHtmlString .= '<ul id="tabInfo" style="display: none;"><li><a href="#tiendo">Tiến độ</a></li><li><a href="#nddon">ND Đơn</a></li><!--<li><a href="#giaidoan">Giai đoạn</a></li>--></ul>';

        $ResHtmlString .= '<div id ="tiendo">';
        $ResHtmlString .= '    <div style="margin:auto;padding:10px 5px;width:98%;">';
        $ResHtmlString .= "        <table class='table_detail_doc' border='1' width='98%'>";
        $ResHtmlString .= "            <col width='25%'><col width='75%'>";
        $ResHtmlString .= "            <tr class='normal_label'>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_TEN_TTHC']. "</td>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord[0]['C_RECORDTYPE_NAME']."</td>";
        $ResHtmlString .= "            </tr>";
        $ResHtmlString .= "            <tr class='normal_label'>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_MA_HO_SO']. "</td>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord[0]['C_CODE']."</td>";
        $ResHtmlString .= "            </tr>";
        $ResHtmlString .= "            <tr class='normal_label'>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_NGAY_TIEP_NHAN']. "</td>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord[0]['C_RECEIVED_DATE']."</td>";
        $ResHtmlString .= "            </tr>";
        if($tax_date !== NULL){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Ngày hẹn thuế</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" .$tax_date."</td>";
            $ResHtmlString .= "        </tr>";
        }
        if($appoint_date !== NULL){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_NGAY_HEN']. "</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" .$appoint_date."</td>";
            $ResHtmlString .= "        </tr>";
        }
        if($arrRecord[0]['C_ASSIGNED_UNIT_IDEA'] !== NULL and $arrRecord[0]['C_ASSIGNED_UNIT_IDEA'] !==''){
            $sConten = $arrRecord[0]['C_ASSIGNED_UNIT_IDEA'];
            if($arrRecord[0]['C_UNIT_APPOINTED_DATE'] !== NULL and $arrRecord[0]['C_UNIT_APPOINTED_DATE'] !==''){
                $sConten .= ', hạn xử lý: '.$arrRecord[0]['C_UNIT_APPOINTED_DATE'];
            }
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Ý kiến của lãnh đạo đơn vị</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sConten."</td>";
            $ResHtmlString .= "        </tr>";
        }
        //
        if($arrRecord[0]['C_UNIT_NAME'] !== NULL and $arrRecord[0]['C_UNIT_NAME'] !==''){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Phòng ban thụ lý hồ sơ</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . trim($arrRecord[0]['C_UNIT_NAME'],";")."</td>";
            $ResHtmlString .= "        </tr>";
        }
        //Y kien cho dao phong
        if($arrRecord[0]['C_DEP_IDEA'] !== NULL and $arrRecord[0]['C_DEP_IDEA'] !==''){
            $sConten = $arrRecord[0]['C_DEP_IDEA'];
            if($arrRecord[0]['C_DEP_APPOINTED_DATE'] !== NULL and $arrRecord[0]['C_DEP_APPOINTED_DATE'] !==''){
                $sConten .= ', hạn xử lý: '.$arrRecord[0]['C_DEP_APPOINTED_DATE'];
            }
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Ý kiến lãnh đạo phòng</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sConten."</td>";
            $ResHtmlString .= "        </tr>";
        }
        if($arrRecord[0]['C_POSITION_NAME'] !== NULL and $arrRecord[0]['C_POSITION_NAME'] !==''){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_CB_THU_LY']. "</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . trim($arrRecord[0]['C_POSITION_NAME'],';')."</td>";
            $ResHtmlString .= "        </tr>";
        }
        $ResHtmlString .= "            <tr class='normal_label'>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" .$arrConst['_TRANG_THAI_XU_LY']. "</td>";
        $ResHtmlString .= "	                <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" .$arrRecord[0]['C_CURRENT_STATUS']."</td>";
        $ResHtmlString .= "            </tr>";
        if($arrRecord[0]['C_NAME_CURRENT_STAFF'] !== NULL and $arrRecord[0]['C_NAME_CURRENT_STAFF'] !==''){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Vị trí hồ sơ</td>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;font-style: italic;font-weight: bold;'>" . $arrRecord[0]['C_NAME_CURRENT_STAFF']."</td>";
            $ResHtmlString .= "        </tr>";
        }
        if(sizeof($arrFile)>0){
            $ResHtmlString .= "        <tr class='normal_label'>";
            $ResHtmlString .= "	            <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>File đính kèm</td>";
            $sFile = '';
            for($k=0;$k<sizeof($arrFile);$k++){
                $strFileName = $arrFile[$k]['C_FILE_NAME'];
                if(trim($strFileName) != '') $sFile .= $ojbEfyLib->_getAllFileAttach($strFileName,"!#~$|*","!~!",$objInitConfig->_setLibUrlPath() . "attach-file/");

            }
            $ResHtmlString .= "	            <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sFile."</td>";
            $ResHtmlString .= "        </tr>";
        }
        $ResHtmlString .= "</table></div>";


        $ResHtmlString .= '<div style="margin: 5px;" align="center"><table cellpadding="0" cellspacing="0" border="0" width="99%" align="center" class="list_table2" id="table1">';
        $delimitor = '!~~!';
        //Hien thi cac cot cua bang hien thi du lieu
        $StrHeader = explode("!~~!", $ojbEfyLib->_GenerateHeaderTable("16%" . $delimitor . "22%" . $delimitor . "22%" . $delimitor . "18%". $delimitor . "22%"
            ,$arrConst['_NGAY_THUC_HIEN']. $delimitor . $arrConst['_DON_VI']  . $delimitor . $arrConst['_CAN_BO_THUC_HIEN'] . $delimitor . $arrConst['_CONG_VIEC'] . $delimitor . "Nội dung"
            ,$delimitor));
        $ResHtmlString .= $StrHeader[0];
        //Dinh nghia URL
        $sCurrentStyleName = "round_row";

        for($index = 0;$index < sizeof($arrWork) ;$index++){
            $iUnitId = $objRecordFunction->getValueInArray($_SESSION['arr_all_staff_keep'],'id','unit_id',$arrWork[$index]['FK_STAFF']);
            $sUnitName = $objRecordFunction->getValueInArray($_SESSION['arr_all_unit_keep'],'id','name',$iUnitId);
            if ($sCurrentStyleName == "odd_row"){
                $sCurrentStyleName = "round_row";
            }else{
                $sCurrentStyleName = "odd_row";
            }
            $ResHtmlString .= "<tr class='".$sCurrentStyleName."'>";
            $ResHtmlString .= '<td align="center" style="padding-left:3px;padding-right:3px;" class="normal_label" >'.$arrWork[$index]['C_WORK_DATE'].'</td>';
            $ResHtmlString .= '<td align="left" style="padding-left:3px;padding-right:3px;" class="normal_label" >'.$sUnitName.'</td>';
            $ResHtmlString .= '<td align="left" style="padding-left:3px;padding-right:3px;" class="normal_label" >'.$arrWork[$index]['C_POSITION_NAME'].'</td>';
            $ResHtmlString .= '<td align="left" style="padding-left:3px;padding-right:3px;" class="normal_label" >'.$arrWork[$index]['C_WORKTYPE_NAME'].'</td>';
            $strFileName 	= $arrWork[$index]['C_FILE_NAME'];
            $sFile = '';
            if(trim($strFileName) != '') $sFile = $ojbEfyLib->_getAllFileAttach($strFileName,"!#~$|*","!~!",$objInitConfig->_setLibUrlPath() . "attach-file/");
            $ResHtmlString .= '<td align="left" style="padding-left:3px;padding-right:3px;" class="normal_label" >'.$arrWork[$index]['C_RESULT'].' '.$sFile.'</td>';
            $ResHtmlString .= '</tr>';
        }
        $ResHtmlString .= '</table></div>';
        $ResHtmlString .= '</div>';

        $ResHtmlString .= '<div id ="nddon" style="margin: 15px 5px 5px;background-color: white;">';
        $ResHtmlString .= '<div id ="contentXml" >';
        $sRecordTypeCode = $arrRecord[0]['C_RECORDTYPE_CODE'];
        $sxmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/thong_tin_ho_so.xml';
        if(!file_exists($sxmlFileName)){
            $sxmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/ho_so_da_tiep_nhan.xml';
            if(!file_exists($sxmlFileName)){
                $sxmlFileName = $objInitConfig->_setXmlFileUrlPath(1).'record/other/ho_so_da_tiep_nhan.xml';
            }
        }
        Zend_Loader::loadClass('Extra_Xml');
        $objxml         = new Extra_Xml();
        $ResHtmlString .= $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row','update_object/update_formfield_list', 'C_RECEIVED_RECORD_XML_DATA', $arrRecord,true,true);
        $ResHtmlString .= '</div></div>';
        //$ResHtmlString .= '<div id ="giaidoan" style="margin: 15px 5px 5px;background-color: white;">';
        //$ResHtmlString .= 'Chức năng này đang được phát triển!</div>';
        if($sModal !== 'on'){
            $ResHtmlString .= '<div style="margin-left: 48%;margin-bottom:10px"><button name="quaylai" id="quaylai" type="button" value="" class="add_large_button" onClick="btn_back_index()">'.$arrConst['_QUAY_LAI'].'</button></div>';
        }
        echo $ResHtmlString;
        exit;
    }

    /**
     *
     */
    public function getidenticalAction(){
        $conn = new G_Db();
        $sGenlist = '';
        isset($_REQUEST['recordtype'])?$recordtype = $_REQUEST['recordtype']:$recordtype = '';
        isset($_REQUEST['tablist'])?$tablist = $_REQUEST['tablist']:$tablist = '';
        isset($_REQUEST['tabvalue'])?$tabvalue = $_REQUEST['tabvalue']:$tabvalue = '';
        isset($_REQUEST['operator'])?$operator = $_REQUEST['operator']:$operator = '';
        isset($_REQUEST['valuedf'])?$valuedf = $_REQUEST['valuedf']:$valuedf = '';
        isset($_REQUEST['relations'])?$relations = $_REQUEST['relations']:$relations = '';
        $arrtablist = explode(",",$tablist);
        $arrvaluelist = explode(",",$tabvalue);
        $arroperatorlist = explode(",",$operator);
        $arrvaluedf = explode(",",$valuedf);

        $sql = "Select convert(varchar(50),PK_RECORD) as PK_RECORD,C_CODE,C_RECEIVED_DATE,C_RECEIVED_RECORD_XML_DATA,C_LICENSE_XML_DATA from T_eCS_RECORD Where 1=1";
        $sql .= " And FK_RECORDTYPE in (select PK_RECORDTYPE from T_eCS_RECORDTYPE where C_CODE ='".$recordtype."') AND C_OWNER_CODE = '".$_SESSION['OWNER_CODE']."' ";
        $counttab = sizeof($arrtablist);
        if($counttab>0){
            $sqltablist = $relations;
            for($tabIndex = 0;$tabIndex< $counttab;$tabIndex++){
                if($arrvaluelist[$tabIndex]<>''){
                    if($arroperatorlist[$tabIndex]=='='){
                        $sqltab = "convert(nvarchar(1000),C_DATA_TEMP.query('/root/data_list/".$arrtablist[$tabIndex]."/text()'))= '".strtoupper($arrvaluelist[$tabIndex])."'";
                    }else{
                        $sqltab = "convert(nvarchar(1000),C_DATA_TEMP.query('/root/data_list/".$arrtablist[$tabIndex]."/text()')) like '%".strtoupper(trim($arrvaluelist[$tabIndex]))."%'";
                    }
                }else{
                    $sqltab = $arrvaluedf[$tabIndex];
                }
                $sqltablist = str_replace($arrtablist[$tabIndex],$sqltab,$sqltablist);
            }
            $sql = $sql." And (".$sqltablist.")";
        }
        try {
            $arrResult = $conn->adodbQueryDataInNameMode($sql);
        }catch (Exception $e){
            return $e->getMessage();
        }
        if(sizeof($arrResult)>0){
            $objconfig = new Extra_Init();
            $objxml = new Extra_Xml();
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$recordtype.'/ho_so_trung_lap.xml';
            if(!file_exists($sxmlFileName)){
                $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/ho_so_trung_lap.xml';
            }
            $sGenlist = $sGenlist . $objxml->_xmlGenerateIdenticalRecordList($sxmlFileName,'col',$arrResult, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",'','../viewrecord/');
        }
        echo $sGenlist;
        exit;
    }

    /**
     * @throws Zend_Exception
     */
    public function getinformationrecordAction(){
        Zend_Loader::loadClass('record_modReceive');
        $objReceive = new record_modReceive();
        $objrecordfun = new Efy_Function_RecordFunctions();
        isset($_REQUEST['pkrecord'])?$sRecordPk = $_REQUEST['pkrecord']:$sRecordPk = '';
        $arrRecordInfo = $objReceive->eCSGetInfoRecordFromListId($sRecordPk, $_SESSION['OWNER_CODE']);
        $sRecordTransitionId = $arrRecordInfo[0]['PK_RECORD_TRANSITION'];
        $arrSingleRecord = $objrecordfun->eCSRecordGetSingle($sRecordPk, $_SESSION['OWNER_CODE'],$sRecordTransitionId);
        if($arrSingleRecord[0]['C_LICENSE_XML_DATA']!=''){
            echo $arrSingleRecord[0]['C_LICENSE_XML_DATA'];
        }else{
            echo $arrSingleRecord[0]['C_RECEIVED_RECORD_XML_DATA'];
        }
        exit;
    }

    /**
     * @throws Zend_Exception
     */
    public function getallrecordseachAction(){
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
        $objconfig = new Extra_Init();
        $ojbEfyLib = new Efy_Library();
        $objxml = new Extra_Xml();
        if ($iPage <= 1)
            $iPage = 1;
        if ($iNumberRecordPerPage == 0)
            $iNumberRecordPerPage = 15;
        $dFromDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dFromDate);
        $dToDateTemp = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dToDate);
        //Lay file XML mo ta form danh sach ho so tim kiem
        $sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';
        if(!file_exists($sSearchXmlFileName)){
            $sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/tim_kiem_nang_cao.xml';
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
        $sXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/danh_sach_ho_so_tim_kiem.xml';
        if(!file_exists($sXmlFileName)){
            $sXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/danh_sach_ho_so_tim_kiem.xml';
        }
        $sGenlist = $objxml->_xmlGenerateList($sXmlFileName,'col',$arrRecord, "C_RECEIVED_RECORD_XML_DATA","PK_RECORD",$sfullTextSearch,false,false,$sAction = '../viewdetailsrecord/');
        $generateStringNumberPage = '';
        $generateHtmlSelectBoxPage = '';
        if (count($arrRecord) > 0){
            $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
            $sdocpertotal = "Danh sách có: ".sizeof($arrRecord).'/'.$iNumberRecord." hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $generateStringNumberPage = $ojbEfyLib->_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage,'','url') ;
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $generateHtmlSelectBoxPage = $ojbEfyLib->_generateChangeRecordNumberPage($iNumberRecordPerPage,'','','url');
        }else{
            $sdocpertotal = 'Danh sách không có hồ sơ nào';
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
        exit;
    }

    /**
     *
     */
    public function getfilterAction(){
        isset($_REQUEST['hdn_recordTypeCode'])?$sRecordTypeCode = $_REQUEST['hdn_recordTypeCode']:$sRecordTypeCode = '';
        $sFilterXmlString = '';
        if(trim($sFilterXmlString) == '') $sFilterXmlString = '<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>';
        //Lay file XML mo ta form cac tieu thuc loc phuc vu tim kiem nang cao
        $objconfig = new Extra_Init();
        $sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/'.$sRecordTypeCode.'/tim_kiem_nang_cao.xml';
        if(!file_exists($sSearchXmlFileName)){
            $sSearchXmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/tim_kiem_nang_cao.xml';
        }
        $objxml = new Extra_Xml();
        //Tao form hien thi tieu tri loc
        $sfilter = $objxml->_xmlGenerateFormfield($sSearchXmlFileName, 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row','list_of_object/filter_formfield_list',$sFilterXmlString,null,true,true);
        echo $sfilter;
        exit;
    }

    /**
     *
     */
    public function getgendataseachAction(){
        $conn 	             = new G_Db();
        $objRecordFunction	 = new Efy_Function_RecordFunctions();
        $ojbEfyLib			 = new Efy_Library();

        // Neu checkView = on, tim kiem tat ca don vi, off: tim kiem mot don vi
        isset($_REQUEST['sChecAllUnit'])?$sCheckAllUnit = $_REQUEST['sChecAllUnit']:$sCheckAllUnit = 'on';
        isset($_REQUEST['dFromDate'])?$dFromDate = $_REQUEST['dFromDate']:$dFromDate = date('01/01/Y');
        isset($_REQUEST['dToDate'])?$dToDate = $_REQUEST['dToDate']:$dToDate = date('d/m/Y');
        isset($_REQUEST['hdn_unit_code'])?$sOwnerCode = $_REQUEST['hdn_unit_code']:$sOwnerCode = $_SESSION['OWNER_CODE'];
        isset($_REQUEST['SelectCate'])?$sCate = $_REQUEST['SelectCate']:$sCate = '';
        $dFromDate = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dFromDate,1);
        $dToDate   = $ojbEfyLib->_ddmmyyyyToYYyymmdd($dToDate,1);

        // Lay phien ban don vi trien khai
        $sVersion  = $objRecordFunction->CheckUnitVersion();
        if($sVersion == 'QUAN_HUYEN' && $sCheckAllUnit =='on'){
            $ownercodelist = '';
            foreach($_SESSION['SesGetAllOwner'] as $key){
                if($key['tham_gia_he_thong'] == TRUE){
                    $ownercodelist = $ownercodelist.",".$key['code'];
                }
            }
            $ownercodelist = trim($ownercodelist,',');
            $arrParameter = array($dFromDate,$dToDate,$ownercodelist);
            $psSql = "eCS_SearchGeneralGetAllUnit";
        }else{
            $arrParameter = array($dFromDate,$dToDate,$sOwnerCode,$sCate);
            $psSql = "eCS_SearchGeneralGetAll";
        }
        $arrRecord = $conn->pdoExecSP($psSql,$arrParameter);
        // echo $psSql;exit;
        // var_dump($arrRecord); exit;
        // Do du lieu ra view
        $ResHtmlString = '';
        $sCurrentStyleName = "odd_row";
        $sGroupCode = '';
        $iGroupint = 0;
        $index=0;
        $iA = '';$iB = '';$iC = '';$iD = '';$iE = '';$iF = '';$iG = '';$iH = '';$iI = '';$iK = '';$iL = '';$iM = '';$iJ = '';
        if(count($arrRecord)>0){
            foreach ($arrRecord as $arrGen){
                $TlDaGqDungHan = '';
                $TlDaGqQuaHan = '';
                $TlDangGqDungHan = '';
                $TlDangGqQuaHan = '';
                // -------------- Tinh ty le % da giai quyet dung va qua han ------------------
                if($arrGen['C_TONG_DA_GIAI_QUYET'] !== '' && $arrGen['C_TONG_DA_GIAI_QUYET'] !== 0){
                    $TlDaGqDungHan = round(($arrGen['C_HS_DA_GIAI_QUYET_DH']/$arrGen['C_TONG_DA_GIAI_QUYET'])*100,2);
                    $TlDaGqQuaHan = round(($arrGen['C_HS_DA_GIAI_QUYET_QH']/$arrGen['C_TONG_DA_GIAI_QUYET'])*100,2);
                }
                if($arrGen['C_TONG_DANG_GIAI_QUYET'] !== '' && $arrGen['C_TONG_DANG_GIAI_QUYET'] !== 0){
                    $TlDangGqDungHan = round(($arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN']/$arrGen['C_TONG_DANG_GIAI_QUYET'])*100,2);
                    $TlDangGqQuaHan = round(($arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN']/$arrGen['C_TONG_DANG_GIAI_QUYET'])*100,2);
                }
                if($TlDaGqDungHan !=='' && $TlDaGqDungHan !== 0) $TlDaGqDungHan = $TlDaGqDungHan.'%';
                if($TlDaGqQuaHan !=='' && $TlDaGqQuaHan !== 0) $TlDaGqQuaHan = $TlDaGqQuaHan.'%';
                if($TlDangGqDungHan !=='' && $TlDangGqDungHan !== 0) $TlDangGqDungHan = $TlDangGqDungHan.'%';
                if($TlDangGqQuaHan !=='' && $TlDangGqQuaHan !== 0) $TlDangGqQuaHan = $TlDangGqQuaHan.'%';
                if($sCheckAllUnit == 'off' && $sGroupCode!=$arrGen['C_CATE']){
                    $index=0;
                    $iGroupint++;
                    $sCurrentStyleName = 'round_row';
                    $sGroupCode = $arrGen['C_CATE'];
                    $ResHtmlString = $ResHtmlString."<tr id='ShowHideIdTr' class=".$sCurrentStyleName.">";
                    $ResHtmlString = $ResHtmlString."<td class='normal_label' align='center'><b><i>".$objRecordFunction->romanic_number($iGroupint)."</i></b></td>";
                    $ResHtmlString = $ResHtmlString."<td class='normal_label' align='left' colspan='14' ><b><i>".$arrGen['C_CATE_NAME']."</i></b></td>";
                    $ResHtmlString = $ResHtmlString."</tr>";
                }
                $index++;
                if ($sCurrentStyleName == "round_row"){
                    $sCurrentStyleName = "odd_row";
                }else{
                    $sCurrentStyleName = "round_row";
                }
                if($sVersion == 'QUAN_HUYEN' && $sCheckAllUnit == 'on' ){
                    $name = '';
                    foreach($_SESSION['SesGetAllOwner'] as $key){
                        if($arrGen['C_OWNER_CODE'] == $key['code']){
                            $name = $key['name'];
                            $name = '<a onclick="GeneralUnit(\''.$arrGen['C_OWNER_CODE'].'\')"><b>'.$name.'<b></a>';
                        }
                    }
                    $ResHtmlString = $ResHtmlString."<tr class=".$sCurrentStyleName." id='ShowHideIdTr'>";
                    $ResHtmlString = $ResHtmlString."<td class='normal_label' align='center'>".$index."</td>";
                    $ResHtmlString = $ResHtmlString."<td class='normal_label' align='left'>".$name."</td>";
                    if($arrGen['C_TONG_SO_HS']<>''){
                        $iA = $arrGen['C_TONG_SO_HS'] + $iA;
                        $ResHtmlString = $ResHtmlString."<td class='normal_label' align='center'>".$arrGen['C_TONG_SO_HS']."</td>";
                    }else{
                        $ResHtmlString = $ResHtmlString."<td class='normal_label' align='center'></td>";
                    }
                    if($arrGen['C_HS_KY_TRUOC_CHUYEN_SANG']<>''){ $iB = $arrGen['C_HS_KY_TRUOC_CHUYEN_SANG'] + $iB;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_KY_TRUOC_CHUYEN_SANG'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_TRONG_KY']<>''){ $iC = $arrGen['C_HS_TRONG_KY'] + $iC;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_TRONG_KY'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_TONG_DA_GIAI_QUYET']<>''){ $iD = $arrGen['C_TONG_DA_GIAI_QUYET'] + $iD;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_TONG_DA_GIAI_QUYET'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DA_GIAI_QUYET_DH']<>''){ $iE = $arrGen['C_HS_DA_GIAI_QUYET_DH'] + $iE;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_DA_GIAI_QUYET_DH'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($TlDaGqDungHan <> ''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:blue;">'.$TlDaGqDungHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DA_GIAI_QUYET_QH']<>''){ $iG = $arrGen['C_HS_DA_GIAI_QUYET_QH'] + $iG;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_DA_GIAI_QUYET_QH'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($TlDaGqQuaHan<>''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:red;">'.$TlDaGqQuaHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_TONG_DANG_GIAI_QUYET']<>''){ $iI = $arrGen['C_TONG_DANG_GIAI_QUYET'] + $iI;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_TONG_DANG_GIAI_QUYET'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN']<>''){ $iJ = $arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN'] + $iJ;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($TlDangGqDungHan<>''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:blue;">'.$TlDangGqDungHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN']<>''){ $iL = $arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN'] + $iL;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center">'.$arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($TlDangGqQuaHan <> ''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:red;">'.$TlDangGqQuaHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    $ResHtmlString = $ResHtmlString.'</tr>';
                }else{
                    $ResHtmlString = $ResHtmlString.'<tr id="ShowHideIdTr" class='.$sCurrentStyleName.'>';
                    $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" >'.$index.'</td>';
                    $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="left"   >'.$arrGen['C_NAME'].'</td>';
                    if($arrGen['C_TONG_SO_HS']<>''){
                        $iA = $arrGen['C_TONG_SO_HS'] + $iA;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'TONG_SO_HS\',\'../status\');">'.$arrGen['C_TONG_SO_HS'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($arrGen['C_HS_KY_TRUOC_CHUYEN_SANG']<>''){
                        $iB = $arrGen['C_HS_KY_TRUOC_CHUYEN_SANG'] + $iB;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'KY_TRUOC_CHUYEN_SANG\',\'../status\');">'.$arrGen['C_HS_KY_TRUOC_CHUYEN_SANG'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($arrGen['C_HS_TRONG_KY']<>''){
                        $iC = $arrGen['C_HS_TRONG_KY'] + $iC;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'TRONG_KY\',\'../status\');">'.$arrGen['C_HS_TRONG_KY'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($arrGen['C_TONG_DA_GIAI_QUYET']<>''){
                        $iD = $arrGen['C_TONG_DA_GIAI_QUYET'] + $iD;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'TONG_DA_GIAI_QUYET\',\'../status\');">'.$arrGen['C_TONG_DA_GIAI_QUYET'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($arrGen['C_HS_DA_GIAI_QUYET_DH']<>''){
                        $iE = $arrGen['C_HS_DA_GIAI_QUYET_DH'] + $iE;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'DA_GIAI_QUYET_DH\',\'../status\');">'.$arrGen['C_HS_DA_GIAI_QUYET_DH'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($TlDaGqDungHan <> ''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:blue;">'.$TlDaGqDungHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DA_GIAI_QUYET_QH']<>''){
                        $iG = $arrGen['C_HS_DA_GIAI_QUYET_QH'] + $iG;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'DA_GIAI_QUYET_QH\',\'../status\');">'.$arrGen['C_HS_DA_GIAI_QUYET_QH'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($TlDaGqQuaHan<>''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:red;">'.$TlDaGqQuaHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_TONG_DANG_GIAI_QUYET']<>''){
                        $iI = $arrGen['C_TONG_DANG_GIAI_QUYET'] + $iI;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'TONG_DANG_GIAI_QUYET\',\'../status\');">'.$arrGen['C_TONG_DANG_GIAI_QUYET'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN']<>''){
                        $iJ = $arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN'] + $iJ;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'DANG_GIAI_QUYET_DH\',\'../status\');">'.$arrGen['C_HS_DANG_GIAI_QUYET_DUNG_HAN'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($TlDangGqDungHan<>''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:blue;">'.$TlDangGqDungHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    if($arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN']<>''){
                        $iL = $arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN'] + $iL;
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" onclick="btn_save_general(\''.$arrGen['PK_RECORDTYPE'].'\',\'DANG_GIAI_QUYET_QH\',\'../status\');">'.$arrGen['C_HS_DANG_GIAI_QUYET_QUA_HAN'].'</td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#ABD8FF\'" ></td>';
                    }
                    if($TlDangGqQuaHan <> ''){
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"><span style="color:red;">'.$TlDangGqQuaHan.'</span></td>';
                    }else{
                        $ResHtmlString = $ResHtmlString.'<td class="normal_label" align="center"></td>';
                    }
                    $ResHtmlString = $ResHtmlString.'</tr>';
                }
            }
            if($iD !== '' && $iD !== 0){
                $iF = round(($iE/$iD)*100, 2);
                $iH = round(($iG/$iD)*100, 2);
                if($iF !=='' && $iF !==0) {$iF = $iF."%";}else{$iF = '';}
                if($iH !=='' && $iH !==0) {$iH = $iH."%";}else{$iH = '';}
            }
            if($iI !== '' && $iI !== 0){
                $iK = round(($iJ/$iI)*100, 2);
                $iM = round(($iL/$iI)*100, 2);
                if($iK !=='' && $iK !==0) {$iK = $iK."%";}else{$iK = '';}
                if($iM !=='' && $iM !==0) {$iM = $iM."%";}else{$iM = '';}
            }
            $ResHtmlString = $ResHtmlString.'<tr id="ShowHideIdTr" class="round_row">
				<td class="normal_label" align="center" colspan="2" style="font-weight:bold;">Tổng</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iA.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iB.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iC.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iD.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iE.'</td>
				<td class="normal_label" align="center" style="color:blue;font-weight:bold;">'.$iF.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iG.'</td>
				<td class="normal_label" align="center" style="color:red;font-weight:bold;">'.$iH.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iI.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iJ.'</td>
				<td class="normal_label" align="center" style="color:blue;font-weight:bold;">'.$iK.'</td>
				<td class="normal_label" align="center" style="font-weight:bold;">'.$iL.'</td>
				<td class="normal_label" align="center" style="color:red;font-weight:bold;">'.$iM.'</td>
			</tr>';
            echo $ResHtmlString;
        }else{
            echo "<tr id='ShowHideIdTr'><td colspan='14'>Đơn vị chưa có dữ liệu</td></tr>";
        }
        exit;
    }

    /**
     * @throws Zend_Exception
     */
    public function getreporttypeAction(){
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
        echo $sGenlist;exit;
    }

    /**
     * @throws Zend_Exception
     */
    function getreportfillterAction(){
        $objconfig = new Extra_Init();
        $objxml = new Extra_Xml();
        $objlibrary = new Efy_Library();
        $sGenlist = '';
        isset($_REQUEST['recordtype'])?$recordtype = $_REQUEST['recordtype']:$recordtype = '';
        isset($_REQUEST['reporttype'])?$sReportID = $_REQUEST['reporttype']:$sReportID = '';
        isset($_REQUEST['xmlname'])?$sxmlname = $_REQUEST['xmlname']:$sxmlname = '';
        isset($_REQUEST['ownercode'])?$ownercode = $_REQUEST['ownercode']:$ownercode = $_SESSION['OWNER_CODE'];
        //Goi lop Listxml_modListType
        if($recordtype !== ''){
            Zend_Loader::loadClass('listxml_modRecordtype');
            $objRecordtype	  = new listxml_modRecordtype();
            $arrRecordType = $objRecordtype->eCSRecordTypeGetAll($ownercode,'','','');
            ///Lay sesion can bo thu ly cua TTHC
            $sHandleList = '';
            for ($i=0;$i<sizeof($arrRecordType);$i++){
                if($recordtype==$arrRecordType[$i]['C_CODE']){
                    $sHandleList .= ",".$arrRecordType[$i]['C_HANDLER_ID_LIST'];

                    break;
                }
            }
            $sHandleList = trim($sHandleList,",");
            if($sHandleList<>''){
                $arrHandleList = array();
                $arrHandleIdList = explode(',',$sHandleList);
                for ($index = 0; $index<sizeof($arrHandleIdList); $index++){
                    $arrHandleList[$index]['id'] = $arrHandleIdList[$index];
                    $arrHandleList[$index]['name'] =  $objlibrary->_getItemAttrById($_SESSION['arr_all_staff'],$arrHandleIdList[$index],'name');
                }
                $_SESSION['arrHandleList'] = $arrHandleList;
            }
        }
        if($sxmlname!=''){
            $v_xml_file = $objconfig->_setXmlFileUrlPath(1) . "listreport/". $sxmlname;
            $sGenlist = $sGenlist . $objxml->_xmlGenerateFormfield($v_xml_file, 'table_struct_of_filter_form/filter_row','filter_formfield_list','<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>', array(),true,true);
        }
        echo $sGenlist;exit;
    }

    /**
     * @throws Zend_Exception
     */
    function getrecordtypeforreportAction(){
        //Goi lop Listxml_modListType
        Zend_Loader::loadClass('listxml_modRecordtype');
        $objRecordtype	  = new listxml_modRecordtype();
        $objXML = new Extra_Xml();
        isset($_REQUEST['ownercode'])?$sOwnerCode = $_REQUEST['ownercode']:$sOwnerCode = 'ALL';
        isset($_REQUEST['code_list'])?$scodelist = $_REQUEST['code_list']:$scodelist = '';
        $arrRecordType = $objRecordtype->eCSRecordTypeGetAll( $sOwnerCode,'','');
        if(count($arrRecordType)>0){
            echo "
            	<label >Báo cáo cho các TTHC</label>
    			<div style='width:100%;'>
    				<table cellpadding='0' cellspacing='0' width='100%' border='0'>
    					<tr>
    						<td>";
            $spRetHtml = "<div style='display:none'><input type='textbox' id='C_RECORD_TYPE_LIST' name='C_RECORD_TYPE_LIST' value='' hide='true' readonly optional = true xml_data=false xml_tag_in_db='' message=''></div>";
            echo $spRetHtml . $objXML->_generateHtmlForMultipleCheckbox($arrRecordType, 'C_CODE','C_NAME',$scodelist,'C_RECORD_TYPE_LIST','70%');
            echo "
    						</td>
    					</tr>
    				</table>
    			</div>
            ";
        }
        exit;
    }

    /**
     *
     */
    public function exportcacheAction(){
        $conn = new G_Db();
        $sOwner_code = $_SESSION['OWNER_CODE'];
        //Lay mang thong tin doi tuong danh muc
        $sql = "Exec EfyLib_ListGetAll '1','10000','where 1=1 ','" . $sOwner_code . "'";
        try {
            $arrList = $conn->adodbQueryDataInNameMode($sql);
        }catch (Exception $e){
            return $e->getMessage();
        }
        $objFunction = new Efy_Function_RecordFunctions();
        $arrTLKT = array();
        $arrData = array();
        foreach($arrList as $value){
            if($value['C_TYPE'] == 'DANH_MUC'){
                $arrTemp = array("PK_LIST"=>$value['PK_LIST'],"C_ORDER"=>$value['C_ORDER'],"C_STATUS"=>$value['C_STATUS']
                ,"C_XML_DATA"=>$value['C_XML_DATA']
                ,"C_CODE"=>$value['C_CODE']
                ,"C_NAME"=>$value['C_NAME']
                ,"LISTTYPE_CODE"=>$value['LISTTYPE_CODE']
                ,"C_TYPE"=>$value['C_TYPE']
                ,"TOTAL_RECORD"=>$value['TOTAL_RECORD']
                );
                array_push($arrData,$arrTemp);
            }else if($value['C_TYPE'] == 'TLKT'){
                $arrTemp = array("PK_LIST"=>$value['PK_LIST'],"C_ORDER"=>$value['C_ORDER'],"C_STATUS"=>$value['C_STATUS']
                ,"C_XML_DATA"=>$value['C_XML_DATA']
                ,"C_CODE"=>$value['C_CODE']
                ,"C_NAME"=>$value['C_NAME']
                ,"LISTTYPE_CODE"=>$value['LISTTYPE_CODE']
                ,"C_TYPE"=>$value['C_TYPE']
                ,"TOTAL_RECORD"=>$value['TOTAL_RECORD']
                );
                array_push($arrTLKT,$arrTemp);
            }
        }
        // xuat cache voi du lieu thuoc tai lieu kem theo
        $objFunction->createCache($arrTLKT,"TLKT_CACHE");
        // xuat cache voi du lieu thuoc vao danh muc
        $return = $objFunction->createCache($arrData,$sOwner_code);
        echo $return;
        exit;
    }

    /**
     *
     */
    public function updateautolistAction(){
        isset($_REQUEST['list_code'])?$list_code = $_REQUEST['list_code']:$list_code = '';
        isset($_REQUEST['listObjCode'])?$listObjCode = $_REQUEST['listObjCode']:$listObjCode = '';
        isset($_REQUEST['listObjName'])?$listObjName = $_REQUEST['listObjName']:$listObjName = '';
        isset($_REQUEST['ordercode'])?$ordercode = $_REQUEST['ordercode']:$ordercode = '';
        isset($_REQUEST['owner_code'])?$owner_code = $_REQUEST['owner_code']:$owner_code = '';
        if(($list_code!='')&&($listObjCode!='')&&($listObjName!='')&&($ordercode!='')&&($owner_code!='')){
            $conn = new G_Db();
            // Update vao database
            $sql = "Exec EfyLib_ListAutoUpdate '".$list_code."','".$listObjCode."','".$listObjName."','".$ordercode."','".$owner_code."'";
            try {
                $conn->adodbQueryDataInNameMode($sql);
            }catch (Exception $e){
                return $e->getMessage();
            }
            //Luu vao cache
            $objFunction = new Efy_Function_RecordFunctions();
            $arrObject = $objFunction->GetAllListObjectByDatabaseListCode($list_code, $owner_code);
            $cache = $objFunction->configCacheFile('TLKT_CACHE');
            $cache->save($arrObject,$list_code);
            echo '1';
            exit;
        }else{
            echo '0';
            exit;
        }
    }
    public function deleteautoAction(){
        isset($_REQUEST['list_code'])?$list_code = $_REQUEST['list_code']:$list_code = '';
        isset($_REQUEST['listObjCode'])?$listObjCode = $_REQUEST['listObjCode']:$listObjCode = '';
        isset($_REQUEST['owner_code'])?$owner_code = $_REQUEST['owner_code']:$owner_code = '';
        if($listObjCode!=''){
            $conn = new G_Db();
            $sql = "Exec EfyLib_ListAutoDelete '".$listObjCode."'";
            //echo $sql;exit;
            try {
                $conn->adodbExecSqlString($sql);
            }catch (Exception $e){
                return $e->getMessage();
            }
            //Luu vao cache
            $objFunction = new Efy_Function_RecordFunctions();
            $arrObject = $objFunction->GetAllListObjectByDatabaseListCode($list_code, $owner_code);
            $cache = $objFunction->configCacheFile('TLKT_CACHE');
            $cache->save($arrObject,$list_code);
            echo '1';
            exit;
        }else{
            echo '0';
            exit;
        }
    }
}?>