<?php
/**
 * @see
 *
 */

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 09/04/2015
 * Noi dung: Tao lop G_Auth
 */
class G_Auth
{
    protected static $_instance = null;

    public function __construct()
    {

    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function checkAuthFunction($module, $controller, $action)
    {
        $arrPemissionAll = $this->_getPermissionAll();
        foreach ($arrPemissionAll as $key => $arrPemission) {
            if ($arrPemission['sPackageCode'] == $module && $arrPemission['sModuleCode'] == $controller && $arrPemission['sEventCode'] == $action)
                return false;
        }
        return true;
    }

    public function _getPermissionAll()
    {
        $arrPermission = Zend_Auth::getInstance()->getIdentity()->PERMISSIONS;
        return $arrPermission;
    }

    // Kiem tra khong ton tai trong mnag quyen
    private function checknotexit($arrInput, $permission_group, $permission_type)
    {
        $iTotal = sizeof($arrInput);
        for ($i = 0; $i < $iTotal; $i++) {
            if ($arrInput[$i]['sGroupPermission'] == $permission_group && $arrInput[$i]['sPermissionType'] == $permission_type) {
                return false;
            }
        }
        return true;
    }

    // Lay cac chuc nang thuoc quan tri he thong
    public function getFunctionPermission($permission_group, $baseUrl)
    {
        $sHtml = '';
        $role = Zend_Auth::getInstance()->getIdentity()->sRole;
        $arrPermission = array();
        switch ($role) {
            case 'SUPPER_ADMIN':
                $arrPermission = array('LOAI_DANH_MUC', 'DM_DOI_TUONG', 'QL_PACKET', 'QL_GIAO_DIEN', 'QL_LUONG_XL_WF', 'PHAN_QUYEN', 'SYSTEM_CONFIG', 'SYSTEM_LOG');
                break;
            
            case 'ADMIN_SYSTEM':
                $arrPermission = array('LOAI_DANH_MUC', 'DM_DOI_TUONG', 'PHAN_QUYEN');
                break;
            
            case 'ADMIN_OWNER':
                $arrPermission = array('LOAI_DANH_MUC', 'DM_DOI_TUONG', 'PHAN_QUYEN');
                break;
        }
        
        for ($i=0; $i < sizeof($arrPermission); $i++) {
            switch ($arrPermission[$i]) {
                case 'LOAI_DANH_MUC':
                    $sHtml .= '<li id = "system_listtype_index" title="Loại danh mục">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/listtype/index/">';
                    $sHtml .= '<span class="icon_LDM"></span>';
                    $sHtml .= '<span class="text">Loại danh mục</span></a></li>';
                    break;
                case 'DM_DOI_TUONG':
                    $sHtml .= '<li id = "system_list_index" title="Danh mục đối tượng">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/list/index/">';
                    $sHtml .= '<span class="icon_DTDM"></span>';
                    $sHtml .= '<span class="text">Danh mục đối tượng</span></a></li>';
                    break;

                case 'QL_PACKET':
                    $sHtml .= '<li id = "system_module_index" title="Quản lý packet">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/module/index/">';
                    $sHtml .= '<span class="icon_PQND"></span>';
                    $sHtml .= '<span class="text">Quản lý packet</span></a></li>';
                    break;
                case 'QL_GIAO_DIEN':
                    $sHtml .= '<li id = "system_menu_index" title="Quản lý giao diện">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/menu/index/">';
                    $sHtml .= '<span class="icon_PQND"></span>';
                    $sHtml .= '<span class="text">Quản lý giao diện</span></a></li>';
                    break;
                case 'QL_LUONG_XL_WF':
                    $sHtml .= '<li id = "system_flowlist_index" title="Định nghĩa luồng xử lý (WF)">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/flowlist/index/">';
                    $sHtml .= '<span class="icon_PQND"></span>';
                    $sHtml .= '<span class="text">Định nghĩa luồng xử lý (WF)</span></a></li>';
                    break;
                case 'PHAN_QUYEN':
                    $sHtml .= '<li id = "system_setuppermission_index" title="Phân quyền chức năng">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/setuppermission/index/" >';
                    $sHtml .= '<span class="icon_PQND"></span>';
                    $sHtml .= '<span class="text">Phân quyền chức năng</span></a></li>';
                    break;
                case 'SYSTEM_CONFIG':
                    $sHtml .= '<li id = "system_config_index" title="Cấu hình">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/config/index/">';
                    $sHtml .= '<span class="icon_PQND"></span>';
                    $sHtml .= '<span class="text">Cấu hình</span></a></li>';
                    break;
                case 'SYSTEM_LOG':
                    $sHtml .= '<li id = "system_log_index" title="Log">';
                    $sHtml .= '<a href="' . $baseUrl . '/system/log/index/" >';
                    $sHtml .= '<span class="icon_BDK"></span>';
                    $sHtml .= '<span class="text">Log</span></a></li>';
                    break;
                default:
                    break;
            }
        }
        return $sHtml;
    }
}