<?php

/**
 * Class Xu ly thong thong tin loai danh muc
 */
class main_ajaxController extends Zend_Controller_Action
{
    public $baseUrl;

    public function init()
    {

    }

    /**
     * Creater : Truongdv
     * Date : 18/10/2012
     * Idea : Tao phuong thuc hien lay du lieu trong danh muc
     */
    public function indexAction()
    {
        //Tao doi tuong
        $objCache = new G_Cache();
        $typelist = $this->_request->getParam('typelist');
        if ($this->_request->getParam('status', 1) == 1) {
            $arrResult = $objCache->getAllObjectbyListCodeFull($typelist);
        } else {
            $arrResult = $objCache->getAllObjectbyListCode($typelist);
        }
        echo Zend_Json::encode($arrResult);
        die();
    }

    public function uploadfileAction()
    {
        $uploaddir = G_Global::getInstance()->dirTempUpload;
        $result = G_Lib::getInstance()->uploadAjaxTemp($uploaddir);
        echo $result;
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * Creater: Truongdv
     * Date: 30/08/2012
     * Idea: Lay phong ban theo don vi
     */
    public function getunitbypermissionAction()
    {
        $ownerCode = $this->_request->getParam('value');
        $arrUnit = $_SESSION['UNIT_PRIVATE'];
        $FkUnit = '';
        $arrR04 = array();
        foreach ($arrUnit as $key => $value) {
            if ($ownerCode == $value['sCode']) {
                $FkUnit = $value['PkUnit'];
                break;
            }
        }
        $arrDepartment = $_SESSION['DEPARTMENT_PRIVATE'];
        //Lay phong ban
        foreach ($arrDepartment as $key => $value) {
            if ($FkUnit == $value['FkUnit']) {
                array_push($arrR04, array('PkObject' => $value['PkObject']
                , 'sName' => $value['sName']
                , 'sCode' => $value['sCode']
                ));
            }
        }
        echo Zend_Json::encode($arrR04);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * Creater: Truongdv
     * Date: 04/12/2012
     * Idea: Lay phong ban theo don vi trong DB User
     */
    public function getunitfromdbuserAction()
    {
        $dbConnect = new G_Db();
        $ownerCode = $this->_request->getParam('value');
        $sql = G_Global::getInstance()->dbUser . ".dbo.sp_UserUnitGetAll ";
        $sql .= "'HOAT_DONG'";
        $sql .= ",''";
        $sql .= ",'" . $ownerCode . "'";
        //echo '<br>'.$sql . '<br>'; exit;
        try {
            $arrUnit = $dbConnect->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo Zend_Json::encode($arrUnit);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * Creater: Truongdv
     * Date: 26/10/2012
     * Idea: Xóa tài liệu kèm theo(Xóa file gốc và xóa trong db)
     */
    public function deletefileAction()
    {
        // Tao doi tuong
        $dbConnect = new G_Db();
        $sysConst = Zend_Registry::get('__sysConst__');
        $FkDoc = $this->_request->getParam('FkDoc');
        $listurl = $this->_request->getParam('list');
        $delimitor = $this->_request->getParam('delimitor');
        $arrListUrl = explode($delimitor, $listurl);
        $fileNameList = '';
        $sDelimitor = '~!@#';
        foreach ($arrListUrl as $key => $value) {
            $arr = explode('/', $value);
            if ($sysConst->fileServer) {
                $ilen = count($arr);
                if ($ilen > 1) {
                    $filename = $arr[$ilen - 1];
                    $fileid = $arr[$ilen - 2];
                    $fileObj = new G_FileServer();
                    $fileObj->_delete($fileid);
                } else {
                    $filename = $arr[0];
                    $fileid = "";
                }
                $fileNameList .= $fileid . "!~!" . $filename . $sDelimitor;
            } else {
                $fileNameList .= end($arr) . $sDelimitor;
                $file = '..' . $value;
                if (file_exists($file))
                    unlink($file);
            }
        }
        $fileNameList = substr($fileNameList, 0, strlen($fileNameList) - strlen($sDelimitor));
        //Xóa trong CSDL
        //$doctype = 'HO_SO';
        $doctype = $this->_request->getParam('doctype', 'HO_SO');
        if ($FkDoc != '') {
            $arrParam = array('FkDoc' => $FkDoc,
                'fileNameList' => $fileNameList,
                'doctype' => $doctype,
                'sDelimitor' => $sDelimitor
            );
            $dbConnect->_querySql($arrParam, 'sp_SysFileDelete', false, false);

        }
        $imageAvatar = G_Global::getInstance()->dirImage . 'avatar_default.png';
        echo $imageAvatar;
        $this->getHelper('viewRenderer')->setNoRender();
    }

    // Lay thong tin cua 1 danh sach cac ho so
    public function getinforrecordAction()
    {
        $sListID = $this->_request->getParam('listid');
        $sPath = G_Global::getInstance()->dirXml . $this->_request->getParam('pathfile');
        $arrParameter = $this->_request->getParams();
        // Tao doi tuong
        $objxml = new G_Xml();
        $arrResult = array();
        if ($sListID != '')
            $arrResult = $objxml->_xmlExportSqlGetAllByID($sPath, $sListID, 'list_of_object/list_sql', 'list_of_object/list_body/col', $arrParameter, false);
        $arrResult = $this->converttostandarddata($arrResult);

        echo Zend_Json::encode($arrResult);
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function getinforrecordsAction()
    {
        // Tao doi tuong
        $sysConst = Zend_Registry::get('__sysConst__');
        $dbConnect = new G_Db();
        $dbCenter = $sysConst->dbCenter;
        $sListID = $this->_request->getParam('listid', '');
        $type = $this->_request->getParam('type', '');
        $arrInput = array(
            'pkrecord' => $sListID,
        );
        switch ($type) {
            case 'RecordWork':
                $sql = '[dbo].sp_KntcRecordWorkGetAll';
                break;
            case 'RecordInfo':
                $sql = '[dbo].sp_KntcRecordGetInfor';
                break;
            default:
                $sql = '[dbo].sp_KntcRecordGetInfor';
                // $sql = 'DBLink.[' . $dbCenter . '].[dbo].sp_KntcGetInforGeneralRecord';
                break;
        }
        $arrResult = $dbConnect->_querySql($arrInput, $sql, true, 0);
        $arrResult = $this->converttostandarddata($arrResult);

        echo Zend_Json::encode($arrResult);
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }


    public function recordworkgetallAction()
    {
        // Tao doi tuong
        $sysConst = Zend_Registry::get('__sysConst__');
        $dbConnect = new G_Db();
        $dbCenter = $sysConst->dbCenter;
        $sListID = $this->_request->getParam('listid', '');
        $type = $this->_request->getParam('type', '');

        switch ($type) {
            case 'RecordWork':
                $sql = 'DBLink.[' . $dbCenter . '].[dbo].sp_KntcGetAllRecordWork';
                break;

            default:
                $sql = 'DBLink.[' . $dbCenter . '].[dbo].sp_KntcGetInforGeneralRecord';
                break;
        }
        $arrInput = array(
            'pkrecord' => $sListID,
        );
        $arrResult = $dbConnect->_querySql($arrInput, $sql, true, false);
        if ($arrResult) {
            $objExt = new G_Extensions_Ext();
            $objCache = new G_Cache();
            $objLib = new G_Lib();
            $sesGetAllOwner = $objCache->getSesGetAllOwner();
            $id = Zend_Auth::getInstance()->getIdentity()->PkStaff;
            for ($i = 0; $i < sizeof($arrResult); $i++) {
                $arrResult[$i]['dWorkDate'] = $objExt->convertDateFormatDDMMYYYYHHMMSS($arrResult[$i]['dWorkDate']);
                $sWorkType = $arrResult[$i]['sWorkType'];
                $arrResult[$i]['sWorkType'] = $objExt->convertListCodeToName(array('0' => $sWorkType, '1' => 'CONG_VIEC'));
                $arrResult[$i]['sResult'] = $objExt->contensumfile(array('0' => $arrResult[$i]['sResult'], '1' => $arrResult[$i]['FkRecordWork'], '2' => 'TIEN_DO', '3' => '0'));
                $arrResult[$i]['sOwnerCode'] = $objLib->_getValuesByIds($sesGetAllOwner, $arrResult[$i]['sOwnerCode'], 'name', 'code');
                $arrResult[$i]['sPetitionContent'] = $objExt->contensumfile(array('0' => $arrResult[$i]['sPetitionContent'], '1' => $arrResult[$i]['PkRecord'], '2' => 'HO_SO', '3' => '1'));
                $arrResult[$i]['FkStaff'] = str_replace(array('{', '}'), '', $arrResult[$i]['FkStaff']);
                if ($arrResult[$i]['FkStaff'] == $id && $sWorkType == 'CONG_VIEC') {
                    $arrResult[$i]['f_Task'] = '<span class="edit_icon editRecordWork" rdw="' . $arrResult[$i]['FkRecordWork'] . '" style="padding-right: 5px;">Sửa</span><span class="delete_icon deleteRecordWork"  rdw="' . $arrResult[$i]['FkRecordWork'] . '">Xóa</span>';
                } else $arrResult[$i]['f_Task'] = '';
            }
        }

        echo Zend_Json::encode($arrResult);
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function viewtempoAction()
    {
        $dirXml = G_Global::getInstance()->dirXml;
        $sPath = 'record/share/sh03/index.xml';
        $sPathInfor = 'record/share/sh01/index.xml';
        $dbCenter = Zend_Registry::get('__sysConst__')->dbCenter;
        if (is_file($dirXml . $sPath)) {
            $objFlow = new G_Flows();
            $objGen = new G_Gen();
            $pkrecord = $this->_request->getParam('pkrecord');

            $action = ($dbCenter ? 'getinforrecords' : 'getinforrecord');
            $arrInput = array(
                'action' => $action,
                'type' => 'RecordInfo',
                'listid' => $pkrecord,
                'pathfile' => $sPathInfor,
                'sCode' => 'xml101001',
                'formName' => 'frm02_0201_delete'
            );
            $this->view->htmlInfo = $objGen->getGeneralInformation($arrInput);

            $arrInput = array(
                'action' => $action,
                'type' => 'RecordWork',
                'listid' => $pkrecord,
                'pathfile' => $sPath,
                'sCode' => 'xml101003',
                'formName' => 'frm02_0201_delete',
                'idputdata' => 'wf-record-work'
            );
            $this->view->htmlRecordWork = $objGen->getGeneralInformation($arrInput);
            $this->view->pkrecord = $pkrecord;
        } else {
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }

    public function converttostandarddata($arrResult)
    {
        $objExt = new G_Extensions_Ext();
        $objCache = new G_Cache();
        $objSession = new G_Session();
        $objLib = new G_Lib();
        for ($i = 0; $i < sizeof($arrResult); $i++) {
            if (isset($arrResult[$i]['dReceivedDate']))
                $arrResult[$i]['dReceivedDate'] = $objExt->convertDateFormatDDMMYYYY($arrResult[$i]['dReceivedDate']);
            if (isset($arrResult[$i]['dAppointedDate']))
                $arrResult[$i]['dAppointedDate'] = $objExt->getAlertApointedDate($arrResult[$i]['dAppointedDate']);
            if (isset($arrResult[$i]['dWorkDate']))
                $arrResult[$i]['dWorkDate'] = $objExt->convertDateFormatDDMMYYYYHHMMSS($arrResult[$i]['dWorkDate']);
            if (isset($arrResult[$i]['sWorkType']))
                $arrResult[$i]['sWorkType'] = $objExt->convertListCodeToName(array('0' => $arrResult[$i]['sWorkType'], '1' => 'CONG_VIEC'));
            if (isset($arrResult[$i]['sResult'])) {
                $fkDoc = (isset($arrResult[$i]['FkRecordWork']) ? $arrResult[$i]['FkRecordWork'] : $arrResult[$i]['PkRecordWork']);
                $arrResult[$i]['sResult'] = $objExt->contensumfile(array('0' => $arrResult[$i]['sResult'], '1' => $fkDoc, '2' => 'TIEN_DO', '3' => '0'));
            }

            if (isset($arrResult[$i]['sOwnerCode']))
                $arrResult[$i]['sOwnerCode'] = $objLib->_getValuesByIds($objSession->SesGetAllOwner(), $arrResult[$i]['sOwnerCode'], 'name', 'code');
            if (isset($arrResult[$i]['sPetitionContent']) && isset($arrResult[$i]['PkRecord']))
                $arrResult[$i]['sPetitionContent'] = $objExt->contensumfile(array('0' => $arrResult[$i]['sPetitionContent'], '1' => $arrResult[$i]['PkRecord'], '2' => 'HO_SO', '3' => '1'));
            // if (isset($arrResult[$i]['sHandle'])){

            $arrStaff = $objCache->getAllObjectbyListCode('CAN_BO_THAM_MUU');
            // var_dump($arrStaff);
            $sHtml = '';
            $sHtml = $sHtml . "<select id='staff' class='normal_selectbox staff' name='staff'  style='width:80%' >";
            $sHtml = $sHtml . $this->generateSelectStaff($arrStaff, 'sCode', 'sCode', 'sName');
            $sHtml = $sHtml . "</select>";
            if (isset($arrResult[$i]['PkRecord']))
                $sHtml = $sHtml . "<label id='del' class='del' pkrecord='" . $arrResult[$i]['PkRecord'] . "'>";

            $arrResult[$i]['sHandle'] = $sHtml;
        }
        return $arrResult;
    }

    public function generateSelectStaff($arrStaff, $IdColumn, $ValueColumn, $NameColumn)
    {
        $objLib = new G_Lib();
        $strHTML = "";
        $i = 0;
        $count = sizeof($arrStaff);
        $arr_all_staff = G_Cache::getInstance()->getAllStaff();
        for ($i = 0; $i < $count; $i++) {
            $staffId = trim($arrStaff[$i][$IdColumn]);
            $strValue = trim($arrStaff[$i][$ValueColumn]);
            $dia_ban = $arrStaff[$i]['dia_ban'];
            $optSelected = '';
            $staffName = trim($arrStaff[$i][$NameColumn]);
            $unitId = $objLib->_getValuesByIds($arr_all_staff, $staffId, 'unit_id');
            $strHTML .= '<option id=' . '"' . $staffId . '"' . ' ' . 'name=' . '"' . $staffName . '"' . ' unit= ' . '"' . $unitId . '"' . ' ';
            $strHTML .= 'dia_ban=' . '"' . $dia_ban . '"' . '' . '  value=' . '"' . $strValue . '"' . ' ' . $optSelected . '>' . $staffName . '</option>';
        }
        return $strHTML;
    }

    // Lay thong tin tim kiem mo rong
    public function searchexpandAction()
    {
        $pathxml = $this->_request->getParam('pathxml');
        $objxml = new G_Xml($pathxml);
        $sHtmlSearchExpand = $objxml->_xmlGenerateFormfield($pathxml, 'list_of_object/table_struct_of_filter_expand_form/filter_row_list/filter_row', 'list_of_object/filter_formfield_expand_list', 'sXmlData', array(), false, false);
        echo $sHtmlSearchExpand;
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function markstarupdateAction()
    {
        // Tao doi tuong
        $dbConnect = new G_Db();
        $pkrecordlist = $this->_request->getParam('pkrecordlist');
        $staffID = Zend_Auth::getInstance()->getIdentity()->PkStaff;
        $arrResult = $dbConnect->_querySql(array('pkrecordlist' => $pkrecordlist, 'fkstaff' => $staffID), 'sp_KntcMarkRecordUpdate', false, false);
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function removestarAction()
    {
        // Tao doi tuong
        $dbConnect = new G_Db();
        $pkrecordlist = $this->_request->getParam('pkrecordlist');
        $staffID = Zend_Auth::getInstance()->getIdentity()->PkStaff;
        $arrResult = $dbConnect->_querySql(array('pkrecordlist' => $pkrecordlist, 'fkstaff' => $staffID), 'sp_KntcMarkRecordDelete', false, false);
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    //
    public function dropdowndyamicAction()
    {
        $objCache = new G_Cache();
        $listtype = $this->_request->getParam('listtype', '');
        $TagGroup = $this->_request->getParam('TagGroup', '');
        $textdefault = $this->_request->getParam('textdefault', '');
        $GroupSelectBox = $this->_request->getParam('GroupSelectBox', '');
        $valueselect = $this->_request->getParam('valueselect', '');
        $SelectBox = $objCache->getAllObjectbyListCodeFull($listtype);
        $result = array();
        foreach ($SelectBox as $key => $value) {
            if ($value[$TagGroup] == $GroupSelectBox)
                $result[] = $SelectBox[$key];
        }
        $htmls = '';
        if (sizeof($result) > 1 || sizeof($result) == 0)
            $htmls = '<option selected="selected" id="" name="" value="">' . $textdefault . '</option>';
        $htmls .= G_Gen::getInstance()->_generateSelectOption($result, 'sCode', 'sCode', 'sName', $valueselect);
        echo $htmls;
        exit;
    }

    /**
     *
     */
    public function dropdownslbbysessionAction()
    {
        $textdefault = $this->_request->getParam('textdefault', '');
        $GroupSelectBox = $this->_request->getParam('GroupSelectBox', '');
        $valueselect = $this->_request->getParam('valueselect', '');
        $secssionName = $this->_request->getParam('secssionName', '');
        $valueCompare = $this->_request->getParam('valueCompare', '');
        $result = array();
        foreach ($_SESSION[$secssionName] as $key => $value) {
            if ($value[$valueCompare] == $GroupSelectBox)
                $result[] = $_SESSION[$secssionName][$key];
        }

        $htmls = '<option selected="selected" id="" name="" value="">' . $textdefault . '</option>';
        $htmls .= G_Gen::getInstance()->_generateSelectOption($result, 'id', 'id', 'name', $valueselect);
        echo $htmls;
        $this->getHelper("viewRenderer")->setNoRender();
    }

    /*
    	Lay toan bo qua trinh thuc hien cua 1 ho so theo quy dinh
    */
    public function viewlimittimeAction()
    {
        $dbConnect = new G_Db();
        $objComponents = new G_Extensions_Ext();
        $objDate = new G_Date();
        $pkrecord = $this->_request->getParam('pkrecord');
        $arrResult = $dbConnect->_querySql(array('pkrecord' => $pkrecord), 'sp_KntcProcessTimeRecordGetAll', true, false);
        $sHtmlTr = '';
        if ($arrResult) {
            $iTotal = sizeof($arrResult);
            for ($i = 0; $i < $iTotal; $i++) {
                $arrInput = array($arrResult[$i]['iTotalDayProcess'], $arrResult[$i]['iNumberDay'], $arrResult[$i]['dDateEnd']);
                $arrResult[$i]['dDateEnd'] = $objComponents->_getAppointedProcessed($arrInput);
                $arrInput = array($arrResult[$i]['iTotalDayProcess'], $arrResult[$i]['iNumberDay']);
                $arrResult[$i]['iTotalDayProcess'] = $objDate->_convertTimeToddhhmm($arrResult[$i]['iTotalDayProcess']);

                if (date('Y-m-d', strtotime($arrResult[$i]['dDateSend'])) == '1970-01-01') {
                    $arrResult[$i]['dDateSend'] = '';
                } else {
                    $arrResult[$i]['dDateSend'] = date("d/m/Y H:i", strtotime($arrResult[$i]['dDateSend']));
                }
                $sHtmlTr .= '<tr>';
                $sHtmlTr .= '<td align="CENTER">' . ($i + 1) . '</td>';
                $sHtmlTr .= '<td align="left">' . $arrResult[$i]['sDepartmentName'] . '</td>';
                $sHtmlTr .= '<td align="left">' . $arrResult[$i]['sTaskName'] . '</td>';
                $sHtmlTr .= '<td align="CENTER">' . date("d/m/Y H:i", strtotime($arrResult[$i]['dDateStart'])) . '</td>';
                $sHtmlTr .= '<td align="CENTER">' . $arrResult[$i]['iNumberDay'] . '</td>';
                $sHtmlTr .= '<td align="CENTER">' . $arrResult[$i]['dDateEnd'] . '</td>';
                $sHtmlTr .= '<td align="CENTER">' . $arrResult[$i]['dDateSend'] . '</td>';
                $sHtmlTr .= '<td align="CENTER">' . $arrResult[$i]['iTotalDayProcess'] . '</td>';
                $sHtmlTr .= '<td align="left">' . $arrResult[$i]['sStaffName'] . '</td>';
                $sHtmlTr .= '</tr>';
            }
        }
        $this->view->sHtmlTr = $sHtmlTr;
    }

    public function viewinfopetitionAction()
    {
        $dbConnect = new G_Db();
        $sPath = G_Global::getInstance()->dirXml . 'record/receive/record/view.xml';
        $pkrecord = $this->_request->getParam('pkrecord');
        $objxml = new G_Xml($sPath);
        $StrSq = '[sp_KntcRecordGetInfor]';
        $arrGetSingle = $dbConnect->_querySql(array('pkrecord' => $pkrecord), $StrSq, false, false);
        if (isset($arrGetSingle['f_dAppointedDate']) && $arrGetSingle['f_dAppointedDate'] == '01/01/1970')
            $arrGetSingle['f_dAppointedDate'] = '';
        $frmhtml = $objxml->_xmlGenerateFormfield($sPath, 'update_object/table_struct_of_update_form/update_row_list/update_row', 'update_object/update_formfield_list', 'sXmlData', $arrGetSingle, false, 1);
        $this->view->frmhtml = $frmhtml;
    }

    // Set them quyen chuc nang cua can bo uy quyen
    public function setauthoziredAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function functionauthoziredAction()
    {
        $eventid = $this->_request->getParam('eventid');
        echo $eventid;
        die();
    }

    public function loadduplicateAction()
    {
        //$dbCenter = Zend_Registry::get('__sysConst__')->dbCenter;
        //Lay tham so tu form
        $R14 = $this->_request->getParam('registerName');
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        $sOwncode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);

        $dbConnect = new G_Db();
        $objExt = new G_Extensions_Ext();
        //echo $arrResult = $dbConnect->_querySql(array('R14' => $R14, 'sOwncode' => $sOwncode, 'currentPage' => '1', 'numberRecordPerPage' => '1000'), '[dbo].sp_KntcDuplicateRecordGetAlls', true, true);exit;
        $arrResult = $dbConnect->_querySql(array('R14' => $R14, 'sOwncode' => $sOwncode, 'currentPage' => '1', 'numberRecordPerPage' => '1000'), '[dbo].sp_KntcDuplicateRecordGetAlls', true, false);
        $sPath = G_Global::getInstance()->dirXml . "record/share/sh09/index.xml";
        $objxml = new G_Xml($sPath);
        $arrBody = $objxml->getArrayElement('list_of_object/list_body/col');
        $groups = array('column_group' => 'sOwnerCode',
            'function_group' => 'convertListCodeToName',
            'class_group' => 'G_Extensions_Ext',
            'parame_group' => 'sOwnerCode,DON_VI_TRIEN_KHAI');
        $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        //lay mang don vi  mywreceive_wrecord
        $arrAllOwner = G_Cache::getInstance()->getSesGetAllOwner();
        if(isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !=''){
            $jsgetinfo='mywreceive_wrecord.getDuplicateReplace(this);';
        }else{
            $jsgetinfo='obj_receive_record.getDuplicateReplace(this);';
        }
        for ($i = 0; $i < sizeof($arrResult); $i++) {
            $sHtml = '<label class="normal_label" onclick="viewtempo(this)" style="cursor: pointer;color:#0000CC;width:100%;padding:0;">Xem</label>';

            $sHtml .= '<br><label class="normal_label" onclick="'.$jsgetinfo.'" style="cursor: pointer;color:#0000CC;width:100%;padding:0;"> Lấy đơn</label>';
            $arrResult[$i]['sTempo'] = $sHtml;
            $arrResult[$i]['sOwnerCode'] = G_Lib::_getValuesByIds($arrAllOwner, $arrResult[$i]['sOwnerCode'], 'name', 'code');
            $arrResult[$i]['sRegistorNameView'] = $arrResult[$i]['sRegistorName'];
            if ($arrResult[$i]['iNumberDuplicateRecord']) {
                $arrResult[$i]['sRegistorName'] = $objExt->addNumberDuplicate(array($arrResult[$i]['sRegistorName'], $arrResult[$i]['iNumberDuplicateRecord']));
            }
        }
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * @throws Zend_Exception
     */
    public function loaddialogduplicateAction()
    {
        $dbCenter = Zend_Registry::get('__sysConst__')->dbCenter;
        //Lay tham so tu form
        $pkrecord = $this->_request->getParam('pkrecord');
        $auth = Zend_Auth::getInstance()->getIdentity();
        $sOwncode = (isset($auth->sDistrictWardProcess) && $auth->sDistrictWardProcess !='' ? $auth->sDistrictWardProcess : $auth->sOwnerCode);

        $dbConnect = new G_Db();
        $arrResult = $dbConnect->_querySql(array('pkrecord' => $pkrecord, 'sOwncode' => $sOwncode, 'currentPage' => '1', 'numberRecordPerPage' => '1000'), '[' . $dbCenter . '].[dbo].sp_KntcDuplicateDialogRecordGetAlls', true, false);
        //
        $sPath = G_Global::getInstance()->dirXml . "record/share/sh10/index.xml";
        $objxml = new G_Xml($sPath);
        $arrBody = $objxml->getArrayElement('list_of_object/list_body/col');
        $groups = array('column_group' => 'sOwnerCode', 'function_group' => 'convertListCodeToName', 'class_group' => 'G_Extensions_Ext', 'parame_group' => 'sOwnerCode,DON_VI_TRIEN_KHAI');
        $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        $arrAllOwner = G_Cache::getInstance()->getSesGetAllOwner();
        for ($i = 0; $i < sizeof($arrResult); $i++) {
            $sHtml = '<label class="normal_label" onclick="viewtempo(this)" style="cursor: pointer;color:#0000CC;width:38%;padding:0;">Xem</label>';
            $arrResult[$i]['sTempo'] = $sHtml;
            $arrResult[$i]['sOwnerCode'] = G_Lib::_getValuesByIds($arrAllOwner, $arrResult[$i]['sOwnerCode'], 'name', 'code');
            if ($arrResult[$i]['iNumberDuplicateRecord']) {
                $arrResult[$i]['sRegistorName'] = $arrResult[$i]['sRegistorName'] . '<lable class="duplicate" onclick="viewDuplicate(this)" style="cursor: pointer;color:#0000CC;width:38%;padding:0;">(' . $arrResult[$i]['iNumberDuplicateRecord'] . ')</lable>';
            }
        }
        echo Zend_Json::encode($arrResult);
        exit;
    }

    /**
     * Xac nhan don trung
     */
    public function mergeduplicateAction()
    {
        $pkrecord = $this->_request->getParam('pkrecord');
        $pkrecord_f = $this->_request->getParam('pkrecord_f');
        $dbConnect = new G_Db();
        $auth = Zend_Auth::getInstance()->getIdentity();
        try {
            $dbConnect->_querySql(array('pkrecord' => $pkrecord, 'pkrecord_f' => $pkrecord_f), 'sp_KntcMergeDuplicate', 0, 0);
            $dbConnect->_querySql(array('PkRecordWork' => '',
                                'pkrecord' => $pkrecord_f,
                                'RW03' => date('Y/m/d H:i:s'),
                                'RW04' => 'DON_TRUNG',
                                'RW05' => 'Xác nhận đơn trùng',
                                'RW06' => $auth->PkStaff,
                                'RW07' => $auth->sPositionCode . ' - ' . $auth->sName,
                                'RW08' => 0,
                                'RW09' => $auth->sOwnerCode,
                                'RW10' => $auth->sUnitName,
                                'doctypelist' => '',
                                'FileAttachList' => '',
                                'sDelimitorDoctype' => '',
                                'R36' => ''
                            ), 'sp_KntcRecordWorkUpdate', 0, 0);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die;        
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     *
     */
    public function printbmAction()
    {
        $type = '';
        $pkrecord = $this->_request->getParam('pkrecord');
        $typexl = $this->_request->getParam('typeprint');
        if ($typexl == 'PHIEU_XU_LY') {
            $type = 'PHIEU_XU_LY';
        }
        G_Extensions_Ext::getInstance()->printBM($pkrecord, $type);
        die();
    }

    /**
     *
     */
    public function printmeetingAction()
    {
        $dbConnect = new G_Db();
        $objExport = new G_Export();
        $codetemp = $this->_request->getParam('codetemp');
        $kieuin = $this->_request->getParam('kieuin');
        $pkperiod = $this->_request->getParam('pkperiod');
        $arrCodeTmp = G_Cache::getInstance()->getAllObjectbyListCodeFull('MAU_IN', Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $tmpxml = '';
        $petitionType = '';

        for ($i = 0; $i < sizeof($arrCodeTmp); $i++)
            if ($codetemp == $arrCodeTmp[$i]['sCode']) {
                $petitionType = $arrCodeTmp[$i]['ten_mau_in'];
                $tmpxml = $arrCodeTmp[$i]['mau_in'];
                break;
            }
        $received_date = '';
        $sql = "Select dReceivedDate From KntcPeriodicMeetingCitizens Where PkPeriodicMeetingCitizens = " . $dbConnect->qstr($pkperiod);
        try {
            $received_date = $dbConnect->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $pkrecordmeetingcitizens = $this->_request->getParam('pkrecordmeetingcitizens');
        $v_export_filename = '';
        // Lay du lieu
        $arrData = array();
        if ($codetemp == 'BMTDDK.UBH') {
            try {
                $arrData = $dbConnect->_querySql($received_date, 'sp_KntcPeriodic_Meeting');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            foreach ($arrData as $key => $value) {
                $arrData[$key]['sMember'] = G_Convert::getInstance()->_restoreBadChar($value['sMember']);
                $arrData[$key]['sRegistorAddress'] = G_Convert::getInstance()->_restoreBadChar($value['sRegistorAddress']);
                $arrData[$key]['sRegistorName'] = G_Convert::getInstance()->_restoreBadChar($value['sRegistorName']);
            }
        } else {
            try {
                $arrData = $dbConnect->_querySql(array('pkrecordmeetingcitizens' => $pkrecordmeetingcitizens), 'sp_KntcPeople_Meeting', true, 0);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        $arrParameter = array(
            'sPathXmlFile' => G_Global::getInstance()->dirXml . 'report/BMTCD/BMTDDK.UBH.xml',
            'sParrentTagName' => 'replace_list',
            'sPathTemplateFile' => G_Global::getInstance()->dirTemReport . 'BMTCD/',
            'sTemplateFile' => $codetemp,
            'reportType' => $codetemp,
            'TagName' => 'replace',
            'data' => $arrData,
            'petitionType' => $petitionType
        );
        switch ($kieuin) {
            case 'doc':
                //$arrParameter['optionprint'] ='COM';
                if ($codetemp == 'BMTDDK.UBH') {
                    $v_export_filename = $objExport->createreportwordmultiblock($arrParameter);
                } else {
                    $v_export_filename = $objExport->exportfileword(array($arrParameter));
                }
                break;
            case 'excel':
                $arrParameter['type'] = 'excel';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            case 'pdf':
                $arrParameter['sTemplateFile'] = 'mau_giay_in_pdf';
                $arrParameter['type'] = 'pdf';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            default:
                break;
        }
        $getBaseUrl = G_Global::getInstance()->sitePath;
        for ($k = 0; $k < sizeof($v_export_filename); $k++) {
            # code...
            $v_export_filename[$k] = 'io/export/' . $v_export_filename[$k];
            $v_filename = 'http://' . $_SERVER['HTTP_HOST'] . $getBaseUrl . $v_export_filename[$k];
            if ($k != (sizeof($v_export_filename) - 1)) {
                echo $v_filename . '!~!';
            } else {
                echo $v_filename;
            }
        }
        die();
    }

    /**
     *
     */
    public function commonprintAction()
    {
        $tempdir = $this->_request->getParam('tempdir');
        $codetemplist = $this->_request->getParam('codetemplist');
        $pkrecordlist = $this->_request->getParam('pkrecordlist');
        $filetype = $this->_request->getParam('filetype');
        $objExport = new G_Export();
        // Mang du lieu dau vao
        $arrParameter = array(
            'sPathXmlFile' => G_Global::getInstance()->dirXml . "report/$tempdir/",
            'sPathTemplateFile' => G_Global::getInstance()->dirTemReport . 'BMTCD/',
            'scodetemplist' => $codetemplist,
            'pkrecordlist' => $pkrecordlist
        );
        switch ($filetype) {
            case 'MSWORD':
                $v_export_filename = $objExport->commonexportfileword($arrParameter);
                break;
            case 'excel':
                $arrParameter['type'] = 'excel';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            case 'pdf':
                $arrParameter['sTemplateFile'] = 'mau_giay_in_pdf';
                $arrParameter['type'] = 'pdf';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            default:
                break;
        }
        $getBaseUrl = G_Global::getInstance()->sitePath;
        $v_filename = 'http://' . $_SERVER['HTTP_HOST'] . $getBaseUrl . '/io/export/' . $v_export_filename;
        die($v_filename);
    }

    /*
    public function printmeetingAction()
    {
        $objComponents = new G_Extensions_Ext();
        $dbConnect = new G_Db();
        $objExport = new G_Export();
        $objConvert = new G_Convert();
        $codetemp = $this->_request->getParam('codetemp');
        $kieuin = $this->_request->getParam('kieuin');
        $pkperiod = $this->_request->getParam('pkperiod');
        $received_date = '';
        $sql = "Select dReceivedDate From KntcPeriodicMeetingCitizens Where PkPeriodicMeetingCitizens = '$pkperiod'";
        try {
            $received_date = $dbConnect->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $arrIn = $received_date;
        $petitionType = '';
        $arrData = array();
        $pkrecordmeetingcitizens = $this->_request->getParam('pkrecordmeetingcitizens');
        $v_export_filename = '';
        // Lay du lieu
        switch ($codetemp) {
            case 'BMTBCD.UBH':
                try {
                    $arrData = $dbConnect->_querySql(array('pkrecordmeetingcitizens' => $pkrecordmeetingcitizens), 'sp_KntcPeople_Meeting', true, 0);
                    $petitionType = 'thong_bao_tiep_cong_dan';
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                //var_dump($arrData);die();
                break;
            case 'BMTDDK.UBH':
                try {
                    $arrData = $dbConnect->_querySql($arrIn, 'sp_KntcPeriodic_Meeting');
                    $petitionType = 'ket_luan_tiep_cong_dan_dinh_ky';
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                foreach ($arrData as $key => $value) {
                    $arrData[$key]['sMember'] = $objConvert->_restoreBadChar($value['sMember']);
                    $arrData[$key]['sRegistorAddress'] = $objConvert->_restoreBadChar($value['sRegistorAddress']);
                    $arrData[$key]['sRegistorName'] = $objConvert->_restoreBadChar($value['sRegistorName']);
                }
                break;
        }
        // Mang du lieu dau vao
        $arrParameter = array(
            'sPathXmlFile' => G_Global::getInstance()->dirXml.'report/BMTCD/' . $codetemp . '.xml',
            'sParrentTagName' => 'replace_list',
            'sPathTemplateFile' => G_Global::getInstance()->dirTemReport. $codetemp . '/',
            'sTemplateFile' => $codetemp,
            'reportType' => $codetemp,
            'TagName' => 'replace',
            'data' => $arrData,
            'petitionType' => $petitionType
        );
        // echo sizeof($arrData);die();
        // var_dump($arrData); die();
        switch ($kieuin) {
            case 'doc':
                //$arrParameter['optionprint'] = 'COM';
                if ($codetemp == 'BMTBCD.UBH') {
                    $v_export_filename = $objExport->exportfileword($arrParameter);
                } else {
                    $v_export_filename = $objComponents->createreportwordmultiblock($arrParameter);
                }
                break;
            case 'excel':
                $arrParameter['type'] = 'excel';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            case 'pdf':
                $arrParameter['sTemplateFile'] = 'mau_giay_in_pdf';
                $arrParameter['type'] = 'pdf';
                $v_export_filename = $objExport->createreportexcel($arrParameter);
                break;
            default:
                break;
        }
        $my_report_file = 'http://' . $_SERVER['HTTP_HOST'] . $this->_request->getBaseUrl() . '/io/export/' . $v_export_filename . '!~!';
        echo $my_report_file;
        die();
    }
    */
    public function remindercancelAction()
    {
        $dbConnect = new G_Db();
        $fromDate = $this->_request->getParam('fromDate');
        $toDate = $this->_request->getParam('toDate');
        $sOwncode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        $arrCancel = $dbConnect->_querySql(array('sFromDate' => $fromDate, 'sToDate' => $toDate, 'sOwncode' => $sOwncode), 'GetAllPetitionCancel', true, false);
        $htmlRss = '';
        if ($arrCancel) {
            $htmlRss .= '<div id="reminder_cancel">';
            $htmlRss .= '<table id="table-data-cancel" class="list-table-data" cellspacing="0" cellpadding="0" border="0" align="center">';
            $htmlRss .= '<col width="7%"><col width="35%"><col width="25%"><col width="25%"><col width="14%">';
            $htmlRss .= '<tr class="header">';
            $htmlRss .= '<td>STT</td>';
            $htmlRss .= '<td>Tổ chức/ cá nhân</td>';
            $htmlRss .= '<td>Ngày nhận đơn</td>';
            $htmlRss .= '<td>Ngày rút đơn</td>';
            $htmlRss .= '<td>#</td>';
            $htmlRss .= '</tr>';
            for ($j = 0; $j < sizeof($arrCancel); $j++) {
                $htmlRss .= '<tr>';
                $htmlRss .= '<td align="center">' . ($j + 1) . '<input type="hidden" value="' . $arrCancel[$j]['PkRecord'] . '" name="chk_item_id" onclick="{selectrow(this);}">' . '</td>';
                $htmlRss .= '<td align="center">' . $arrCancel[$j]['sRegistorName'] . '</td>';
                $htmlRss .= '<td align="center">' . G_Convert::_yyyymmddToDDmmyyyy($arrCancel[$j]['dReceivedDate']) . '</td>';
                $htmlRss .= '<td align="center">' . G_Convert::_yyyymmddToDDmmyyyy($arrCancel[$j]['dSignApproveDate']) . '</td>';
                $htmlRss .= '<td align="center"><label class="normal_label" style="cursor: pointer;color:#0000CC;padding-right:2%" onclick="viewtempo_cancel(this)">Xem</label></td>';
                $htmlRss .= '</tr>';
            }
            $htmlRss .= '</table>';
            $htmlRss .= '</div>';
        }
        echo $htmlRss;
        die();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function hidekqxlAction()
    {
        $dbConnect = new G_Db();
        $RMC01 = $this->_request->getParam('pkrecordmeetingcitizens');
        $sql = "Select sCurrentStatus from KntcRecordMeetingCitizens where PkRecordMeetingCitizens=" . $dbConnect->qstr($RMC01);
        $arrResult = $dbConnect->adodbQueryDataInNameMode($sql);
        echo Zend_Json::encode($arrResult);
        die();
        $this->getHelper("viewRenderer")->setNoRender();
    }

    public function pagingAction()
    {
        $arrInput = $this->_request->getParams();
        $iTotalRecord = ($arrInput['iTotalRecord']) ? $arrInput['iTotalRecord'] : '';
        $sJsChangePage = (isset($arrInput['JsChangePage']) && $arrInput['JsChangePage']) ? $arrInput['JsChangePage'] : 'gotopageModal';
        $iPage = (isset($arrInput['iPage']) ? $arrInput['iPage'] : '');
        $iRowOnPage = ($arrInput['iRowOnPage']) ? $arrInput['iRowOnPage'] : '';
        $htmlpaging = '<div id = "paging-content-modal">';
        $iNumberPage = ceil($iTotalRecord / $iRowOnPage);     //Tong so trang
        $inumberpageinsheet = 10;                             //so trang hien thi
        $iStartPage = 1;                                     //Trang dau tien
        $iEndpage = 1;                                     //Trang cuoi cung
        $iaddPage = 4;                                     //buoc tien cua phan trang
        if ($iNumberPage < $inumberpageinsheet) {
            $iStartPage = 1;
            $iEndpage = $iNumberPage;
        } else {
            if ($iPage > $iaddPage) {
                $flag = 0;
                while ($iaddPage >= 0) {
                    if ($iNumberPage - ($iPage - $iaddPage - 1) >= $inumberpageinsheet) {
                        $iStartPage = $iPage - $iaddPage;
                        $flag = 1;
                        break;
                    }
                    $iaddPage--;
                }
                if (!$flag)
                    $iStartPage = ($iNumberPage - $inumberpageinsheet) + 1;
            } else {
                $iStartPage = 1;
            }
            $iEndpage = ($iStartPage + $inumberpageinsheet) - 1;
        }
        if ($iPage > 1) {
            $htmlpaging .= '<a class = "pre" num="' . ($iPage - 1) . '">Trước</a>';
        }
        if ($iTotalRecord > $iRowOnPage)
            for ($i = $iStartPage; $i <= $iEndpage; $i++) {
                if ($i == $iPage)
                    $htmlpaging .= '<a class = "pg current" num="' . ($i) . '">' . $i . '</a>';
                else $htmlpaging .= '<a class = "pg" num="' . ($i) . '">' . $i . '</a>';
            }
        if ($iPage < $iNumberPage) {
            $htmlpaging .= '<a class = "nex" num="' . ($iPage + 1) . '">Tiếp</a>';
        }
        $htmlpaging .= '</div>';
        echo $htmlpaging;
        $this->getHelper("viewRenderer")->setNoRender();
    }

    public function pagingmodalAction()
    {
        $arrInput = $this->_request->getParams();
        $iTotalRecord = ($arrInput['iTotalRecord']) ? $arrInput['iTotalRecord'] : '';
        $sJsChangePage = (isset($arrInput['JsChangePage']) && $arrInput['JsChangePage']) ? $arrInput['JsChangePage'] : 'gotopageModal';
        $iPage = (isset($arrInput['iPage']) ? $arrInput['iPage'] : '');
        $iRowOnPage = ($arrInput['iRowOnPage']) ? $arrInput['iRowOnPage'] : '';
        $htmlpaging = '<div id = "paging-content-modal">';
        $iNumberPage = ceil($iTotalRecord / $iRowOnPage);     //Tong so trang
        $inumberpageinsheet = 10;                             //so trang hien thi
        $iStartPage = 1;                                     //Trang dau tien
        $iEndpage = 1;                                     //Trang cuoi cung
        $iaddPage = 4;                                     //buoc tien cua phan trang
        if ($iNumberPage < $inumberpageinsheet) {
            $iStartPage = 1;
            $iEndpage = $iNumberPage;
        } else {
            if ($iPage > $iaddPage) {
                $flag = 0;
                while ($iaddPage >= 0) {
                    if ($iNumberPage - ($iPage - $iaddPage - 1) >= $inumberpageinsheet) {
                        $iStartPage = $iPage - $iaddPage;
                        $flag = 1;
                        break;
                    }
                    $iaddPage--;
                }
                if (!$flag)
                    $iStartPage = ($iNumberPage - $inumberpageinsheet) + 1;
            } else {
                $iStartPage = 1;
            }
            $iEndpage = ($iStartPage + $inumberpageinsheet) - 1;
        }
        if ($iPage > 1) {
            $htmlpaging .= '<a class = "pre" onclick ="' . $sJsChangePage . '(' . ($iPage - 1) . ',this)">Trước</a>';
        }
        if ($iTotalRecord > $iRowOnPage)
            for ($i = $iStartPage; $i <= $iEndpage; $i++) {
                if ($i == $iPage)
                    $htmlpaging .= '<a class = "pg current" onclick ="' . $sJsChangePage . '(' . $i . ',this)">' . $i . '</a>';
                else $htmlpaging .= '<a class = "pg" onclick ="' . $sJsChangePage . '(' . $i . ',this)">' . $i . '</a>';
            }
        if ($iPage < $iNumberPage) {
            $htmlpaging .= '<a class = "nex" onclick ="' . $sJsChangePage . '(' . ($iPage + 1) . ',this)">Tiếp</a>';
        }
        $htmlpaging .= '</div>';
        echo $htmlpaging;
        $this->getHelper("viewRenderer")->setNoRender();
    }

    public function getduplicateAction()
    {
        $dbConnect = new G_Db();
        $pkrecord = $this->_request->getParam('pkrecord', '');
        $sql = "Select PkDuplicateRecord, FkRecord As PkRecord, sInputNumber, dReceivedDate, sRegistorName, sRegistorAddress,sPetitionContent,sOwnerCode FROM KntcDuplicateRecord WHERE FkRecord='" . $pkrecord . "'   ";
        $arrResult = $dbConnect->adodbQueryDataInNameMode($sql);
        if ($arrResult) {
            $objExt = new G_Extensions_Ext();
            foreach ($arrResult as $key => $data) {
                $arrResult[$key]['sPetitionContent'] = $objExt->contensumfile(array($data['sPetitionContent'], $data['PkDuplicateRecord'], 'DON_TRUNG', 0));
                $arrResult[$key]['dReceivedDate'] = $objExt->convertDateFormatDDMMYYYY($data['dReceivedDate']);
            }
        }
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function editworkAction()
    {
        $dbConnect = new G_Db();
        $pkrecord = $this->_request->getParam('pkrecord', '');
        $sql = "
            Select PkRecordWork
                        ,FkRecord           
                        ,convert(varchar(10),dWorkDate, 103) As dWorkDate
                        ,sWorkType
                        ,sResult
            From KntcRecordWork
            Where PkRecordWork = " . $dbConnect->qstr($pkrecord);
        $arrResult = array();
        try {
            $arrResult = $dbConnect->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $objExt = new G_Extensions_Ext();
        $objGen = new G_Gen();
        $arrFileList = $objExt->_getFileSaved($pkrecord, 'TIEN_DO', 0);
        $arrResult['FILE'] = '';
        if ($arrFileList)
            $arrResult['FILE'] = $objGen->getDefaultFile($arrFileList);
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function deleteworkAction()
    {
        $dbConnect = new G_Db();
        $pkrecord = $this->_request->getParam('pkrecord', '');
        $arrResult = $dbConnect->_querySql(array('PkRecordWork' => $pkrecord), 'sp_KntcDeleteRecordWork', false, false);
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

}

?>