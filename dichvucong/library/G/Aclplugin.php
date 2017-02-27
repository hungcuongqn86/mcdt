<?php

class G_Aclplugin extends Zend_Controller_Plugin_Abstract
{
    protected $_config;
    protected $_userInfo;

    public function __construct()
    {
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

        $auth = G_Account::getInstance();
        $module = strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());
        $resouce = $module . ':' . $controller;

        if ($auth->hasIdentity()) {
            $infoUserData = $auth->getIdentity();
            $this->_config = Zend_Registry::get('__sysConst__');
            $this->_userInfo = $infoUserData;
            $this->checkOrigin();
            
            if (in_array($resouce . ':' . $action, $this->requestAuto()) == false) {
                $_SESSION['lastAccess'] = time();
            }

            $allowed = false;
            switch ($infoUserData->sRole) {
                case 'ADMIN':
                    $allowed = true;
                    break;
                default:
                    $allowed = $this->isUserAllowed($module, $controller, $action);
                    break;
            }

            if (!$allowed) {
                $request->setModuleName('account')->setControllerName('index')->setActionName('denyacess');
            }
        } else {
            if ($this->checkAccess($module, $controller, $action)) {
                $request->setModuleName('account')->setControllerName('index')->setActionName('index');
            }
        }
    }

    private function checkOrigin()
    {
        return;
        $sysConst = $this->_config;
        $infoUserData = $this->_userInfo;
        if ($sysConst->ownerCode != $infoUserData->sOwnerCode) {
            $uri = str_replace($sysConst->_prefixCode . $sysConst->ownerCode, $sysConst->_prefixCode . $infoUserData->sOwnerCode, $this->getRequest()->getRequestUri());
            $url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $uri;
            die(header('Location: ' . $url));
        }
    }

    private function checkAccess($module, $controller, $action)
    {
        if ($module == 'admin')   return true;
        if ($module.':'.$controller == 'site:record') return true;
        return false;
    }

    private function requestAuto()
    {
        return array('main:index:retrievetimeleft', 'main:reminder:getnotify');
    }

    public function isUserAllowed($module, $controller, $action)
    {
        if (in_array($module, array('admin')))
            return false;
        return true;
    }


    public static function isModule($permissions, $module)
    {
        foreach ($permissions as $key => $value) {
            if ($value[0] == $module) {
                return true;
            }
        }
        return false;
    }

    public static function isController($permissions, $module, $controller)
    {
        foreach ($permissions as $key => $value) {
            if ($value[0] == $module && (isset($value[1]) == '' || $value[1] == $controller)) {
                return true;
            }
        }
        return false;
    }

    public static function isAdmin($sRole = '')
    {
        if ($sRole == '') $sRole = Zend_Auth::getInstance()->getIdentity()->sRole;
        return in_array($sRole, array('ADMIN_SYSTEM', 'ADMIN_OWNER', 'SUPPER_ADMIN'));
    }

    public static function isAdminSystem($sRole = '')
    {
        if ($sRole == '') $sRole = Zend_Auth::getInstance()->getIdentity()->sRole;
        return in_array($sRole, array('ADMIN_SYSTEM', 'SUPPER_ADMIN'));
    }

    public static function isAdministrator($sRole = '')
    {
        if ($sRole == '') $sRole = Zend_Auth::getInstance()->getIdentity()->sRole;
        return in_array($sRole, array('SUPPER_ADMIN'));
    }

}