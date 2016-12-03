<?php

class G_App
{
    protected static $_instance = null;
    protected static $_root = null;
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        self::$_root = dirname(dirname(dirname(__FILE__)));
        Zend_Session::start();
    }

    public function checkOrigin()
    {
        $objConfig = new G_Global();
        $absolute_url = $this->full_url($_SERVER);
    }

    function url_origin($s, $use_forwarded_host = false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    function full_url($s, $use_forwarded_host = false)
    {
        return $this->url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
    }

    public function logoutApplication()
    {
        Zend_Session::destroy();
        Zend_Session::regenerateId();
        header('Location: ' . G_Global::getInstance()->urlLogin());
        exit();
    }

    /**
     * Run the application
     *
     * @return void
     */
    public function run()
    {
        //Khai bao bien toan cuc
        $registry = Zend_Registry::getInstance();
        $cache = new G_Cache();

        
        // Doc config
        $constConfig = new Zend_Config_Ini(self::$_root . '/config/config.ini');
        //Ket noi CSDL SQL theo kieu ADODB
        $connectSQL = $constConfig->dbmssql;
        $configSQL = $connectSQL->db->config->toArray();
        $configSQL['pathAdoCache'] = self::$_root . '/public/cache/AdoCache';
        if (is_dir($configSQL['pathAdoCache']) == false) {
            mkdir($configSQL['pathAdoCache']);
        }
        $dbConnect = G_Connection::factory($connectSQL->db->adapter, $configSQL);
        $registry->set('dbAdapter', $dbConnect);
        // Lay tham so he thong
        $systemConst = $cache->getSystemConst();

        if (empty($systemConst)) {
            $systemConst = $this->getConfigDefault();
        }
        
        $registry->set('__sysConst__', $systemConst);
        $this->checkOrigin();
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->addControllerDirectory('./application/controllers');
        $arrRouter = $constConfig->module;
        foreach ($arrRouter as $key => $value) {
            $frontController->addControllerDirectory(trim($value), trim($key));
        }
        $arrModule = $cache->_module();
        if ($arrModule && !empty($arrModule))
            foreach ($arrModule as $key => $module) {
                $frontController->addControllerDirectory('./application/' . $module->MD02 . '/controllers', $module->MD02);
            }
            
        $frontController->registerPlugin(new G_Aclplugin());
        $frontController->throwExceptions(true);
        $config = new Zend_Config_Ini(self::$_root . '/config/router.ini', 'setting');
        $router = new Zend_Controller_Router_Rewrite();
        $router = $router->addConfig($config, 'routes');
        $frontController->setRouter($router);
        $frontController->setDefaultModule('site');
        $frontController->dispatch();
    }

    private function getConfigDefault() {
        $config = new stdClass();
        $config->ownerFullName = 'UBND Huyện Lệ Thủy - Quảng Bình';
        $config->appName = 'Hệ thống dịch vụ công trực tuyến';
        $config->title = 'Hệ thống dịch vụ công trực tuyến';
        $config->ownerAddress = 'HLT';
        $config->sessionTimeOut = 3600;
        $config->ownerCode = 'HLT';
        // Confgig support
        $config->license = 'Công ty cổ phần tin học G4tech Việt Nam';
        $config->supportMobile = '(04) 6329 6622';
        $config->supportEmail = 'support@g4tech.vn';
        $config->supportWebsite = 'http://g4tech.vn/';
        
        // Config mail server
        $config->email_mailserver = 'smtp.gmail.com';
        $config->email_auth = 'login';
        $config->email_ssl = 'ssl';
        $config->email_port = '465';
        $config->email_username = 'dichvuconglethuy@gmail.com';
        $config->email_password = 'lethuy@123';
        return $config;
    }
}
