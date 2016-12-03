<?php

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 17/11/2008
 * Noi dung:
 */
class G_Session extends Zend_Session_Namespace
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    //********************************************************************************
    //Ten phuong thuc		:getPersonalInfoOfAllStaff()
    //Chuc nang	: Lay thong tin ca nhan cua tat ca can bo (staff)
    //********************************************************************************/
    public static function SesGetPersonalInfoOfAllStaff()
    {
        $p_arr_items = array();
        $arrResul = array();
        $sql = "Exec " . G_Global::getInstance()->dbUser . ".dbo.sp_UserGetPersonalInfoOfAllStaff ";
        //echo $sql . '<br>';
        try {
            $arrResul = G_Db::getInstance()->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        if (sizeof($arrResul) > 0) {
            foreach ($arrResul As $Resul) {
                $arrStaff = array("id" => str_replace('{', '', str_replace('}', '', $Resul['PkStaff']))
                , "name" => $Resul['sName']
                , "unit_id" => str_replace('{', '', str_replace('}', '', $Resul['FkUnit']))
                , "position_code" => $Resul['sPositionCode']
                , "position_name" => $Resul['sPositionName']
                , "position_group_code" => $Resul['sPositionGroupCode']
                , "address" => $Resul['sAddress']
                , "email" => $Resul['sEmail']
                , "tel" => $Resul['sTel']
                , "tel_mobile" => $Resul['sTelMobile']
                , "order" => $Resul['iOrder']
                , "ownerCode" => $Resul['sOwnerCode']
                , "idcq" => $Resul['sIDCQ']
                , "position_id" => str_replace("}", "", str_replace("{", "", $Resul['FkPosition']))
                );
                array_push($p_arr_items, $arrStaff);
            }
        }
        return $p_arr_items;
    }

    //********************************************************************************
    //Ten ham		:SesGetDetailInfoOfAllUnit()
    //Chuc nang	: Lay thong tin chi tiet cua tat ca phong ban (unit)
    //********************************************************************************/
    public static function SesGetDetailInfoOfAllUnit()
    {

        $p_arr_items = array();
        $arrResul = array();
        $sql = "Exec " . G_Global::getInstance()->dbUser . ".dbo.sp_UserGetDetailInfoOfAllUnit ";
        try {
            $arrResul = G_Db::getInstance()->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        if (sizeof($arrResul) > 0) {
            foreach ($arrResul As $Resul) {
                $arrStaff = array("id" => str_replace('{', '', str_replace('}', '', $Resul['PkUnit']))
                , "parent_id" => str_replace('{', '', str_replace('}', '', $Resul['FkUnit']))
                , "name" => $Resul['sName']
                , "code" => $Resul['sCode']
                , "address" => $Resul['sAddress']
                , "email" => $Resul['sEmail']
                , "tel" => $Resul['sTel']
                , "order" => $Resul['iOrder']
                , "ownerCode" => $Resul['sOwnerCode']
                , "sDistrictWardProcess" => $Resul['sDistrictWardProcess']
                );
                array_push($p_arr_items, $arrStaff);
            }
        }
        return $p_arr_items;
    }

    /**
     * Creater :TRUONGDV
     * Date : 24/09/2009
     * Idea : Tao phuong thuc lay tat ca quyen cua NSD hien thoi
     * @param $sStaffIdList : Id can bo hien thoi
     * @param $sDelimitor : Ky tu phan tach
     * @return Mang danh sach quyen
     */
    public static function SesGetAllPermissionForSession($sStaffIdList, $sDelimitor = "!~~!")
    {
        $ObjRecordFunctions = new G_Extensions_RecordFunctions();
        $dbConnect = new G_Db();
        //Lay mang quyen tiep nhan
        $arrAllReceiveStaff = $ObjRecordFunctions->getAllObjectbyListCodeFull(Zend_Auth::getInstance()->getIdentity()->sOwnerCode, 'TIEP_NHAN');
        $count = sizeof($arrAllReceiveStaff);
        $arrPermission = array();
        $arrPermission['RECEIVE'] = null;
        for ($i = 0; $i < $count; $i++) {
            if ($arrAllReceiveStaff[$i]['FkStaff'] == $sStaffIdList) {
                $arrPermission['RECEIVE'] = 1;
                break;
            }
        }
        //Lay mang phan quyen thu ly theo don vi
        $arrPermissionByUnit = $ObjRecordFunctions->getAllObjectbyListCodeFull(Zend_Auth::getInstance()->getIdentity()->sOwnerCode, 'THU_LY');
        $count = sizeof($arrPermissionByUnit);
        $arrPermission['HANDLE'] = null;
        for ($i = 0; $i < $count; $i++) {
            if ($arrPermissionByUnit[$i]['FkStaff'] == $sStaffIdList) {
                $arrPermission['HANDLE'] = 1;
                break;
            }
        }

        $sql = "sp_SysStaffPermissionGetAll ";
        $sql .= $dbConnect->qstr($sStaffIdList);
        $sql .= "," . $dbConnect->qstr($sDelimitor);
        try {
            $arrResult = $dbConnect->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        //Chuyen doi xau mo ta quyen -> mang mot chieu
        $arrElement = explode("!~~!", $arrResult[0]['sPermissionList']);
        for ($index = 0; $index < sizeof($arrElement); $index++) {
            $arrTemp = explode("!*~*!", $arrElement[$index]);
            $arrPermission[trim($arrTemp[0])] = null;
            if (trim($arrTemp[0]) != "") {
                $arrPermission[trim($arrTemp[0])] = 1;
            }
        }
        return $arrPermission;
    }

    /**
     * Creater : TRUONGDV
     * Date : 25/09/2009
     * Idea : Tao phuong thuc lay quyen cua NSD hien thoi
     *
     */
    public static function StaffPermisionGetAll($iLoginStaffId)
    {
        $arrPermission = self::SesGetAllPermissionForSession($iLoginStaffId);
        if (is_array($arrPermission) || $arrPermission == "") {
            return $arrPermission;
        }
        return '';
    }

    /**
     * Creater : TRUONGDV
     * Date : 25/09/2009
     * hieu chinh: TRUONGDV - 18/08/2010
     * Idea : Tao phuong thuc lay thong tin don vi su dung
     *
     * @param unknown_type $UnitId
     */
    public static function SesGetAllOwner($option = '')
    {
        $arrOwner = array();
        $arrResult = array();
        $sql = "Exec " . G_Global::getInstance()->dbUser . ".dbo.sp_UserOwnerGetAll '', 'DM_DON_VI_TRIEN_KHAI'";
        try {
            $arrResult = G_Db::getInstance()->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        for ($index = 0; $index < sizeof($arrResult); $index++) {
            $arr1Owner = array("name" => $arrResult[$index]['sName']
            , "code" => $arrResult[$index]['sCode']
            , "order" => $arrResult[$index]['iOrder']
            , "address" => $arrResult[$index]['sAddress']
            , "email" => $arrResult[$index]['sEmail']
            , "phone" => $arrResult[$index]['sTelephone']
            , "tham_gia_he_thong" => $arrResult[$index]['tham_gia_he_thong']
            , "nhom_don_vi" => $arrResult[$index]['nhom_don_vi']);
            array_push($arrOwner, $arr1Owner);
        }
        return $arrOwner;
    }

    /**
     * Creter : TRUONGDV
     * Date : 30/06/2011
     * Idea : Tra lai danh sach phong ban cua NSD hien thoi
     * @param $sOwnerCode : ID don vi NSD hien thoi
     */
    public static function _getAllUnitsByCurrentStaff($sOwnerCode = '')
    {
        $arrChildUnitRoot = array();
        if ($sOwnerCode != "") {
            $i = 0;
            $arrAllUnit = G_Cache::getInstance()->getAllUnitKeep();
            foreach ($arrAllUnit as $objUnit) {//Lay phong ban cap 0
                if($objUnit['sDistrictWardProcess'] == "") {
                    if (is_null($objUnit['parent_id']) || $objUnit['parent_id'] == "" || $objUnit['parent_id'] == "NULL") {
                        $arrChildUnitRoot[$i]['id'] = $objUnit['id'];
                        $arrChildUnitRoot[$i]['parent_id'] = NULL;
                        $arrChildUnitRoot[$i]['name'] = $objUnit['name'];
                        $arrChildUnitRoot[$i]['code'] = $objUnit['code'];
                        $arrChildUnitRoot[$i]['address'] = $objUnit['address'];
                        $arrChildUnitRoot[$i]['email'] = $objUnit['email'];
                        $arrChildUnitRoot[$i]['tel'] = $objUnit['tel'];
                        $arrChildUnitRoot[$i]['order'] = $objUnit['order'];
                        $arrChildUnitRoot[$i]['ownerCode'] = $objUnit['ownerCode'];
                        $i++;
                    } else {// Lay cac phong van con cua phong ban cap 1
                        if ($objUnit['ownerCode'] == $sOwnerCode) {
                            $arrChildUnitRoot[$i]['id'] = $objUnit['id'];
                            $arrChildUnitRoot[$i]['parent_id'] = $objUnit['parent_id'];
                            $arrChildUnitRoot[$i]['name'] = $objUnit['name'];
                            $arrChildUnitRoot[$i]['code'] = $objUnit['code'];
                            $arrChildUnitRoot[$i]['address'] = $objUnit['address'];
                            $arrChildUnitRoot[$i]['email'] = $objUnit['email'];
                            $arrChildUnitRoot[$i]['tel'] = $objUnit['tel'];
                            $arrChildUnitRoot[$i]['order'] = $objUnit['order'];
                            $arrChildUnitRoot[$i]['ownerCode'] = $objUnit['ownerCode'];
                            $i++;
                        }
                    }
                }
            }
        }
        return $arrChildUnitRoot;
    }

    /**
     * Creter : CUONGNH
     * Date : 06/11/2015
     * Idea : Tra lai danh sach phong ban cua NSD hien thoi
     * @param $sOwnerCode : ID don vi NSD hien thoi
     */
    public static function _getAllWardsByCurrentStaff($sOwnerCode = '')
    {
        $arrChildWardsRoot = array();
        if ($sOwnerCode != "") {
            $i = 0;
            $arrAllUnit = G_Cache::getInstance()->getAllUnitKeep();
            foreach ($arrAllUnit as $objUnit) {//Lay phong ban cap 0
                if($objUnit['sDistrictWardProcess'] != "") {
                    if ($objUnit['parent_id'] != "") {
                        if ($objUnit['ownerCode'] == $sOwnerCode) {
                            $arrChildWardsRoot[$i]['id'] = $objUnit['id'];
                            $arrChildWardsRoot[$i]['parent_id'] = $objUnit['parent_id'];
                            $arrChildWardsRoot[$i]['name'] = $objUnit['name'];
                            $arrChildWardsRoot[$i]['code'] = $objUnit['code'];
                            $arrChildWardsRoot[$i]['address'] = $objUnit['address'];
                            $arrChildWardsRoot[$i]['email'] = $objUnit['email'];
                            $arrChildWardsRoot[$i]['tel'] = $objUnit['tel'];
                            $arrChildWardsRoot[$i]['order'] = $objUnit['order'];
                            $arrChildWardsRoot[$i]['ownerCode'] = $objUnit['ownerCode'];
                            $arrChildWardsRoot[$i]['sDistrictWardProcess'] = $objUnit['sDistrictWardProcess'];
                            $i++;
                        }
                    }
                }
            }
        }
        return $arrChildWardsRoot;
    }

    /**
     * Creater : TRUONGDV
     * Date : 24/06/2010
     * Idea : Lay toan bo NSD cua mot don vi
     * @param $sOwnerCode : Ma don vi trien khai
     */
    public static function _getAllUsersByCurrentOrg($sOwnerCode = '')
    {
        $arrchildStaff = array();
        $i = 0;
        $arrAllStaff = G_Cache::getInstance()->getAllStaffKeep();
        foreach ($arrAllStaff as $objStaff) {
            if ($objStaff['ownerCode'] == $sOwnerCode) {
                $arrchildStaff[$i]['id'] = $objStaff['id'];
                $arrchildStaff[$i]['name'] = $objStaff['name'];
                $arrchildStaff[$i]['unit_id'] = $objStaff['unit_id'];
                $arrchildStaff[$i]['position_code'] = $objStaff['position_code'];
                $arrchildStaff[$i]['position_name'] = $objStaff['position_name'];
                $arrchildStaff[$i]['position_group_code'] = $objStaff['position_group_code'];
                $arrchildStaff[$i]['address'] = $objStaff['address'];
                $arrchildStaff[$i]['email'] = $objStaff['email'];
                $arrchildStaff[$i]['tel'] = $objStaff['tel'];
                $arrchildStaff[$i]['order'] = $objStaff['order'];
                $arrchildStaff[$i]['ownerCode'] = $objStaff['ownerCode'];
                $arrchildStaff[$i]['idcq'] = $objStaff['idcq'];
                $arrchildStaff[$i]['position_id'] = $objStaff['position_id'];
                $i++;
            }
        }
        return $arrchildStaff;
    }

    /**
     * Creater:TRUONGDV
     * Date: 19/11/2012
     * Idea: Tạo phương thức lấy thông tin phường/xã mà NSD đăng nhập hiện thời có quyền
     */
    public static function SesGetWardsByCurrentStaff($sStaffTemp = "")
    {
        $sStaff = $sStaffTemp;
        if ($sStaffTemp == "") {
            $sStaff = $_SESSION['staff_id'];
        }
        $xmlPath = G_Global::getInstance()->dirXml;
        //Lấy danh mục Quyền của tất cả NSD
        $sPermissionFilePath = $xmlPath . "list/output/" . Zend_Auth::getInstance()->getIdentity()->sOwnerCode . "/DM_QUYEN_NGUOI_SU_DUNG.xml";
        if (!is_file($sPermissionFilePath)) {
            $sPermissionFilePath = $xmlPath . "list/output/DM_QUYEN_NGUOI_SU_DUNG.xml";
        }
        $objPermissionXmlData = new G_Xml($sPermissionFilePath, 'data_list');
        $arrPermissionXmlData = array();
        if (isset($objPermissionXmlData->item)) {
            $arrPermissionXmlData = $objPermissionXmlData->item->toArray();
        }
        //Lấy Danh mục Phường/Xã
        $sWardsFilePath = $xmlPath . "list/output/" . Zend_Auth::getInstance()->getIdentity()->sOwnerCode . "/DM_PHUONG.xml";
        $objWardsXmlData = $objPermissionXmlData->__loadxml($sWardsFilePath, 'data_list');
        $arrWardsXmlData = array();
        if (isset($objWardsXmlData->item)) {
            $arrWardsXmlData = $objWardsXmlData->item->toArray();
        }
        $sWardsList = "";
        $arrWards = array();
        foreach ($arrPermissionXmlData as $value) {
            if ($value['staff_id'] == $sStaff) {
                if (isset($value['nhom_tlhs_theo_dia_ban'])) {
                    $sWardsList = $value['nhom_tlhs_theo_dia_ban'];
                }
                break;
            }
        }
        //Duyệt danh sách các phường/xã mà NSD hiện thời có quyền nhập
        if ($sWardsList != "") {
            foreach ($arrWardsXmlData as $value) {
                if (G_Lib::_listHaveElement($sWardsList, $value['sCode'], ',')) {
                    $arrWards[] = $value;
                }
            }
        }
        return $arrWards;
    }

    public function _getInformationStaffLogin($sStaffName, $sPositionName, $sUnitName, $baseUrl, $arrInput = array())
    {
        $strHtml = "";
        if ($sStaffName != "") {
            $glibs = new G_Lib();
            $iTotal = sizeof($arrInput);
            $arr_all_staff = G_Cache::getInstance()->getAllStaff();
            if ($iTotal > 0)
                $strHtml .= '<p class="information_singup"><span id="authozired">Ủy quyền</span></p>';
            $default_FkStaff = '';
            for ($i = 0; $i < $iTotal; $i++) {
                if ($default_FkStaff != $arrInput[$i]['FkStaff']) {
                    $default_FkStaff = $arrInput[$i]['FkStaff'];
                    $userName = $glibs->_getValuesByIds($arr_all_staff, $arrInput[$i]['FkStaff'], 'namePosition');
                    $strHtml .= '<h1 class="displayuser user">' . $userName . '</h1>';
                    $strHtml .= '<ul style="height:auto;">';
                }
                $arrUrl = explode('/', $arrInput[$i]['sUrl']);
                $urlIndex = 'M' . $arrUrl[0] . '/' . $arrUrl[1];
                // $value['PkEventTable']
                $strHtml .= '<li class="displayfunction">';
                $strHtml .= '<input type="radio" name="function_authozired" id="function_authozired' . $i . '" url="' . $urlIndex . '" value="' . $arrInput[$i]['FkStaff'] . '" style="vertical-align: top;display:none;"/>';
                $strHtml .= '<label for="function_authozired' . $i . '">' . $arrInput[$i]['sFunctionName'] . '</label></li>';
                if (($i + 1 == $iTotal) || $arrInput[$i + 1]['FkStaff'] != $default_FkStaff) {
                    $strHtml .= '</ul>';
                }

            }
            if ($iTotal > 0)
                $strHtml .= '<h1 class="displayuser refresh" id="reset_auth" uid="" url="' . G_Global::getInstance()->urlDefault() . '" name="' . $sPositionName . '">Reset</h1>';
            $strHtml .= '<p class="information_singup" id="changepass" style="padding-top:5px;"> ' . $sUnitName . '</p>';
            $urlRe = $baseUrl . "../logout/index";
            $strHtml .= '<p class="information_singup" onclick="href(\'' . $urlRe . '\')" style="cursor:pointer;">Thoát</p>';
        }
        return $strHtml;
    }


    public function _getInforAuthozired($arrInput)
    {

        $sHtml = '';
        $default_FkStaff = '';
        $iTotal = sizeof($arrInput);
        $objLib = new G_Lib();
        $arr_all_staff = G_Cache::getInstance()->getAllStaff();
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        for ($i = 0; $i < $iTotal; $i++) {
            if ($default_FkStaff != $arrInput[$i]['FkStaff']) {
                $default_FkStaff = $arrInput[$i]['FkStaff'];
                $userName = $objLib->_getValuesByIds($arr_all_staff, $arrInput[$i]['FkStaff'], 'namePosition');
                $sHtml .= '<h1 class="displayuser user">' . $userName . '</h1>';
                $sHtml .= '<ul style="height:auto;">';
            }
            $arrUrl = explode('/', $arrInput[$i]['sUrl']);
            $urlIndex = 'M' . $arrUrl[0] . '/' . $arrUrl[1];
            // $value['PkEventTable']
            $sHtml .= '<li class="displayfunction">';
            $sHtml .= '<input type="radio" name="function_authozired" id="function_authozired' . $i . '" url="' . $urlIndex . '" value="' . $arrInput[$i]['FkStaff'] . '" style="vertical-align: top;"/>';
            $sHtml .= '<label for="function_authozired' . $i . '">' . $arrInput[$i]['sFunctionName'] . '</label></li>';
            if (($i + 1 == $iTotal) || $arrInput[$i + 1]['FkStaff'] != $default_FkStaff) {
                $sHtml .= '</ul>';
            }

        }
        $positionName = $userIdentity->sPositionCode . ' - ' . $userIdentity->sName;
        $sHtml .= '<h1 class="displayuser refresh" id="reset_auth" uid="" url="' . G_Global::getInstance()->urlDefault() . '" name="' . $positionName . '">Reset</h1>';
        return $sHtml;
    }

    public function  _getSession($key)
    {
        switch ($key) {
            case 'arr_all_staff_keep':
                $output = G_Cache::getInstance()->getAllStaffKeep();
                break;
            case 'arr_all_staff':
                $output = G_Cache::getInstance()->getAllStaff();
                break;
            case 'arr_all_unit_keep':
                $output = G_Cache::getInstance()->getAllUnitKeep();
                break;
            case 'arr_all_unit':
                $output = G_Cache::getInstance()->getAllUnit();
                break;
            case 'SesGetAllOwner':
                $output = G_Cache::getInstance()->getSesGetAllOwner();
                break;
            default:
                if (isset($_SESSION[$key]))
                    $output = $_SESSION[$key];
                else $output = '';
                break;
        }
        return $output;
    }

    public static function SesGetDistrictOwner($val_xml, $option = '')
    {
        $arrResult = array();
        $sql = "Exec " . G_Global::getInstance()->dbUser . ".dbo.sp_SysListGetAllbyListtypeCode ";
        $sql = $sql . "'" . "'";
        $sql = $sql . ",'" . 'DM_PHUONG_XA' . "'";
        $sql = $sql . ",'" . $option . "'";
        $sql = $sql . ",'" . 'quan_huyen' . "'";
        $sql = $sql . ",'" . $val_xml . "'";
        // echo 'sql:' . $sql.'<br>';//die();
        try {
            $arrResult = G_Db::getInstance()->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResult;
    }
}