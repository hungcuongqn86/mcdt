<?php
require_once 'G/Objects/Cache.php';

/**
 *
 */
class G_Cache extends G_Objects_Cache
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getAllUnitKeep()
    {
        $arrData = self::load_cache('arr_all_unit_keep');
        if ($arrData == false) {
            $arrData = G_Session::SesGetDetailInfoOfAllUnit();
            self::save_cache($arrData, 'arr_all_unit_keep');
        }
        return $arrData;
    }

    public function getAllUnit()
    {
        $arrData = self::load_cache('arr_all_unit');
        if ($arrData == false) {
            $arrData = G_Session::_getAllUnitsByCurrentStaff(Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
            self::save_cache($arrData, 'arr_all_unit');
        }
        return $arrData;
    }

    public function getAllStaffKeep()
    {
        $arrData = self::load_cache('arr_all_staff_keep');
        if ($arrData == false) {
            $arrData = G_Session::SesGetPersonalInfoOfAllStaff();
            self::save_cache($arrData, 'arr_all_staff_keep');
        }
        return $arrData;
    }

    public function getAllStaff()
    {
        $arrData = self::load_cache('arr_all_staff');
        if ($arrData == false) {
            $arrData = G_Session::_getAllUsersByCurrentOrg(Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
            self::save_cache($arrData, 'arr_all_staff');
        }
        return $arrData;
    }

    public function getTreeUser()
    {
        $arrData = self::load_cache('arr_tree_user');
        if ($arrData == false) {
            $arrData = G_Session::SesTreeUserGetAll();
            self::save_cache($arrData, 'arr_tree_user');
        }
        return $arrData;
    }

    public function getSesGetAllOwner()
    {
        $arrData = self::load_cache('SesGetAllOwner');
        if ($arrData == false) {
            $arrData = G_Session::SesGetAllOwner();
            self::save_cache($arrData, 'SesGetAllOwner');
        }
        return $arrData;
    }

    public function getSesGetAllWards()
    {
        $arrData = self::load_cache('SesGetAllWards');
        if ($arrData == false) {
            $arrData = G_Session::_getAllWardsByCurrentStaff(Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
            self::save_cache($arrData, 'SesGetAllWards');
        }
        return $arrData;
    }

    public function get_system_config()
    {
        $result = $this->load_cache('SYSTEM_CONFIG');
        if (!$result) {
            $result = $this->update_system_config();
        }
        return $result;

    }
    public function getSystemConst()
    {
        /*$arrData = self::load_cache('DM_THAM_SO_HE_THONG');
        if ($arrData == false) {
            $arrData = G_Objects_Xml::getInstance()->getAllObjectbyListCodeFull('DM_THAM_SO_HE_THONG');
            $arrData = $arrData[0];
//            self::save_cache($arrData, 'DM_THAM_SO_HE_THONG');
        }*/
        $arrData = $this->load_cache('SYSTEM_CONFIG');
        if (!$arrData) {
            $arrData = $this->update_system_config();
        }
        $arrData = json_decode(json_encode($arrData), FALSE);
        return $arrData;
    }

    public function _module()
    {
        $arrData = self::load_cache('__MODULE__');
        if ($arrData == false) {
            $arrData = G_Db::getInstance()->_querySql(array(), 'sp_SysModuleGetAll', true, false);
            self::save_cache($arrData, '__MODULE__');
        }
        $arrData = json_decode(json_encode($arrData), FALSE);
        return $arrData;
    }

    /**
     * Nguoi tao: Truongdv
     * Ngay tao: 06/05/2013
     * Y nghia:Lay Mang danh muc doi tuong cua mot danh muc
     * $sOwnerCode: Mã đơn vị sử dụng
     * $sCode: Mã loại Danh mục
     * Output: Mang cac doi tuong cua loai danh muc ung voi ma truyen vao
     */
    public static function getAllObjectbyListCode($sCode, $sOwnerCode = '')
    {
        if ($sCode == '') {
            return array();
        }
        $pFilePath = G_Global::getInstance()->dirCache;

        if ($sOwnerCode == '')
            $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        $pFilePath .= '/' . $sOwnerCode;
        if (!is_dir($pFilePath)) {
            return array();
        }
        $cache = new G_Cache();
        $backend = array('cache_dir' => $pFilePath);
        $data = $cache->load_cache($sCode, $options = array('backend' => $backend));
        $arrOutput = array();
        if ($data) {
            foreach ($data as $key => $value) {
                array_push($arrOutput, array('sCode' => $value['sCode'], 'sName' => $value['sName']));
            }
        }
        return $arrOutput;
    }

    /**
     * Nguoi tao: PHUONGTT
     * Ngay tao: 13/10/2011
     * Y nghia:Lay Mang tat ca thong tin danh muc doi tuong cua mot danh muc
     * Input: Ma danh muc
     * Output: Mang cac doi tuong cua loai danh muc ung voi ma truyen vao
     */
    public static function getAllObjectbyListCodeFull($sCode, $sOwnerCode = '')
    {
        if ($sCode == '') {
            return array();
        }
        $pFilePath = G_Global::getInstance()->dirCache;
        if ($sOwnerCode == '')
            $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        $pFilePath .= '/' . $sOwnerCode;
        if (!is_dir($pFilePath)) {
            return array();
        }
        $cache = new G_Cache();
        $backend = array('cache_dir' => $pFilePath);
        return $cache->load_cache($sCode, $options = array('backend' => $backend));
    }

    public function getAllDepartmentCurrentOwner()
    {
        $k = 0;
        $sRootId = "";
        $arr_all_unit = self::getAllUnit();
        $arrDepartment = array();
        $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        foreach ($arr_all_unit as $sUnitId) {
            if (is_null($sUnitId['parent_id'])) {
                $sRootId = $sUnitId['id'];
            }
            if ($sUnitId['ownerCode'] == $sOwnerCode && $sUnitId['parent_id'] != "" && $sRootId != $sUnitId['parent_id']) {
                $arrDepartment[$k] = $sUnitId;
                $k++;
            }
        }
        return $arrDepartment;
    }

    public function update_system_config()
    {
        $dbConnect = new G_Db();
        $result = $dbConnect->_querySql(array(), 'configGetAll', 1, 0);
        $resultCache = array();
        if ($result != '' && !empty($result)) {
            foreach ($result as $key => $value) {
                $resultCache[$value['Name']] = $value['Value'];
            }
        }
        $this->save_cache($resultCache, 'SYSTEM_CONFIG');
        return $resultCache;
    }

    public function save_system_config($key, $value)
    {
        $dbConnect = new G_Db();
        $result = $dbConnect->_querySql(array(), 'configGetAll', 1, 0);
        $id = '';
        $des = '';
        $orders = '';
        foreach ($result as $key1 => $value1) {
            if ($value1['Name'] == $key) {
                $id = $value1['ConfigID'];
                $des = $value1['Description'];
                $orders = $value1['Orders'];
                break;
            }
        }
        $param = array(
            'name_param' => $key, 'value_param' => $value, 'des_param' => $des, 'id_param' => $id, 'orders_param' => $orders, 'delmiter' => '#@!G4T#@!'
        );
        $dbConnect->_querySql($param, 'configUpdate', 0, 0);
        $configs = $this->update_system_config();
        return $configs;
    }
}

?>