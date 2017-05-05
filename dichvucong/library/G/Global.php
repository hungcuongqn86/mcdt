<?php

class G_Global
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public $sitePath;
    public $dbUser = '[kntc-yb-user]';
    public $codeApp = 'mKNTC';
    public $layoutPath = './application/layout';
    public $dirCache;
    public $dirXml;
    public $dirImage;
    public $dirPublic;
    public $dirSaveFile;
    public $dirTemReport;
    public $dirHelp;
    public $dirTempUpload;
    public $dirTemplate;
    public $openfilePath;
    public $_key = 'g4tech';
    public $dirXmlExt;

    function __construct()
    {
        $this->sitePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->dirPublic = 'public/';
        $this->dirCache = './public/cache/';
        $this->dirHelp = 'help/';
        //$this->dirCache = $this->dirPublic . 'cache';
        $this->dirImage = $this->dirPublic . 'images/';
        $this->dirXml = $this->dirPublic . 'xml/';
        $this->dirSaveFile = dirname(dirname(dirname(__FILE__))) . '/io/attach-file/';
        $this->dirTempUpload = dirname(dirname(dirname(__FILE__))) . '/io/tempupload/';
        $this->dirTemReport = $this->dirPublic . 'templates/report/';
        $this->dirTemplate = $this->dirPublic . 'templates/';
        $this->openfilePath =  $this->sitePath ."system/file/open/";
        $this->dirXmlExt = dirname(dirname(dirname(dirname(__FILE__)))). '/g4t-mcdt-lethuy/xml/record';
    }

    public function _getCurrentHttpAndHost()
    {
        $sCurrentHttpHost = 'http://' . $_SERVER['HTTP_HOST'] . $this->sitePath;
        return $sCurrentHttpHost;
    }

    /**
     * Lay ra gia tri quyen quan tri he thong
     *
     */
    public static function _getPermisstionSystem($iOption = 0)
    {
        switch ($iOption) {
            case 1;    //Quyen quan tri toan he thong
                return "ADMIN_SYSTEM";
                break;
            case 2;    //Quyen quan tri cap mot don vi trien khai
                return "ADMIN_OWNER";
                break;
            default:
                return "";
                break;
        }
    }

    public function urlLogin()
    {
        return $this->_getCurrentHttpAndHost() . "dang-nhap";
    }

    public function urlDefault()
    {
        return $this->_getCurrentHttpAndHost() . 'nhacviec';
    }

    public static function _setFtpParameters($ownercode)
    {
        $arrParameters = array(
            '00' => array(
                'host' => '127.0.0.1'
            )
        );
        $arrParameters[$ownercode]['user'] = 'ftp_' . $ownercode;
        $arrParameters[$ownercode]['password'] = 'ftp' . $ownercode . '@123poiuytrewq';
        $arrParameters[$ownercode]['path'] = '/public/';
        $arrParameters[$ownercode]['port'] = '21';
        return $arrParameters[$ownercode];
    }

    public static function limitstartftp()
    {
        return 5;
    }

    public static function _setDefaultPassword()
    {
        return "d98f4caed5c4d64bc4c4bb85fe5bab07";
    }
    /**
     * Idea: Tao cac hang so dung chung cho viec xu ly JS
     *
     * @return Chuoi mo ta JS
     */
    public function _setJavaScriptPublicVariable(){

        $arrConst = G_Const::_setProjectPublicConst();
        $sysConst = Zend_Registry::get('__sysConst__');
        $psHtml = "<script>\n";
        $psHtml = $psHtml . "_LIST_DELIMITOR='" . $arrConst['_CONST_LIST_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_SUB_LIST_DELIMITOR='" . $arrConst['_CONST_SUB_LIST_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_DECIMAL_DELIMITOR='" . $arrConst['_CONST_DECIMAL_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_LIST_WORK_DAY_OF_WEEK='" . $arrConst['_CONST_LIST_WORK_DAY_OF_WEEK'] . "';\n";
        $psHtml = $psHtml . "_LIST_DAY_OFF_OF_YEAR='" . $arrConst['_CONST_LIST_DAY_OFF_OF_YEAR'] . "';\n";
        $psHtml = $psHtml . "_INCREASE_AND_DECREASE_DAY='" . $arrConst['_CONST_INCREASE_AND_DECREASE_DAY'] . "';\n";
        $psHtml = $psHtml . "_LIST_WORK_TIME_OF_DAY='" . $arrConst['_CONST_LIST_WORK_TIME_OF_DAY'] . "';\n";

        $psHtml = $psHtml . "_MODAL_DIALOG_MODE='" . $arrConst['_MODAL_DIALOG_MODE'] . "';\n";
        $psHtml = $psHtml . "_GET_HTTP_AND_HOST='" . $arrConst['_GET_HTTP_AND_HOST'] . "';\n";
        $psHtml = $psHtml . "_IMAGE_URL_PATH='" . $arrConst['_CONST_IMAGE_URL_PATH'] . "';\n";
        //$psHtml = $psHtml . "_APP_CODE='" . $arrConst['_APP_CODE'] . "';\n";


        $psHtml = $psHtml . "lunarHoliday='" 	. $sysConst->lunarHoliday . "';\n";
        $psHtml = $psHtml . "solarHoliday='" 	. $sysConst->solarHoliday . "';\n";
        $psHtml = $psHtml . "tetHoliday='" 		. $sysConst->tetHoliday . "';\n";
        $psHtml = $psHtml . "satTime='" 		. $sysConst->satTime . "';\n";
//        $psHtml = $psHtml . "ownerCode='" 		. $sysConst->OWNER_CODE . "';\n";
        //$psHtml = $psHtml . "updateQlgdDate='" 		. $sysConst->update_date_qlgd . "';\n";

        if($sysConst->am_start_time)
            $psHtml = $psHtml . "am_start_time=" 	. $sysConst->am_start_time . ";\n";
        if($sysConst->am_stop_time)
            $psHtml = $psHtml . "am_stop_time=" 	. $sysConst->am_stop_time . ";\n";
        if($sysConst->pm_start_time)
            $psHtml = $psHtml . "pm_start_time=" 	. $sysConst->pm_start_time . ";\n";
        if($sysConst->pm_stop_time)
            $psHtml = $psHtml . "pm_stop_time=" 	. $sysConst->pm_stop_time . ";\n";
//        if($sysConst->LOGIN_URL)
//            $psHtml = $psHtml . "LOGIN_URL='" 	. $sysConst->LOGIN_URL . "';\n";
        $psHtml = $psHtml . "taskOther='';\n";
        $psHtml = $psHtml . "_CURRENT_DAY='" 	. date('d/m/Y') . "';\n";

        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize = substr($upload_max_filesize, 0, strlen($upload_max_filesize)) * 1000;
        $psHtml .= "upload_max_filesize='" 	. $upload_max_filesize . "';\n";
        $psHtml .= "</script>\n";
        return $psHtml;
    }
}