<?php

/**
 * cuongnh
 * Class giao tiep voi fileServer
 */
class G_FileServer
{
    private $_sAuth;
    private $_sOwnerCode;
    private $_sOwnerCodeCenter;
    private $_sAppCode;
    private $_FileServerUrl;
    private $_sPkRecord;

    public function __construct()
    {
        if (!class_exists('Zend_Config_Ini')) {
            Zend_Loader::loadClass('Zend_Config_Ini');
        }
        if (!class_exists('Zend_Registry')) {
            Zend_Loader::loadClass('Zend_Registry');
        }
        $registry = Zend_Registry::getInstance();
        if ($registry->isRegistered('config')) {
            $config = $registry->get('config');
        } else {
            $config = new Zend_Config_Ini('./config/config.ini', 'attachfile');
            $registry->set('config', $config);
        }
        $this->_sAuth = $config->file->sAuthen;
        $this->_sOwnerCodeCenter = $config->file->sOwnerCode;
        $this->_sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        $this->_sAppCode = $config->file->sAppCode;
        $this->_FileServerUrl = trim($config->file->sFileServerUrl);
    }

    public function _upload($uploadfile, $sPkRecord, $sRecordType, $sTableObject)
    {
        if ($sPkRecord == '') {
            if (!isset($this->_sPkRecord)) {
                $this->_sPkRecord = $this->unique();
                $this->_registryPk();
            }
        } else {
            if (!isset($this->_sPkRecord)) {
                $this->_sPkRecord = $sPkRecord;
            }
        }
        $sOwnerCodeView = $this->_sOwnerCode;
        $sOwnerCodeShare = $this->_sOwnerCode;
        if ((isset($this->_sOwnerCodeCenter)) && ($this->_sOwnerCodeCenter != $this->_sOwnerCode)) {
            $sOwnerCodeView .= ',' . $this->_sOwnerCodeCenter;
            $sOwnerCodeShare .= ',' . $this->_sOwnerCodeCenter;
        }
        $postResult = '';
        $sUrl = $this->_FileServerUrl . 'upload';
        $ch = curl_init($sUrl);
        if (!$ch) {
            die("Không kết nối được đến FileServer!");
        } else {
            $arrPostFields = array('sampfile' => "@$uploadfile", 'owner_code' => $this->_sOwnerCode, 'app_code' => $this->_sAppCode, 'PkRecord' => $this->_sPkRecord, 'record_type' => $sRecordType, 'table_obj' => $sTableObject, 'owner_code_view' => $sOwnerCodeView, 'owner_code_share' => $sOwnerCodeShare);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $this->_sAuth);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $postResult = curl_exec($ch);
            //echo $postResult;exit;
            curl_close($ch);
        }
        return $postResult;
    }

    public function _updatePkFile($newpkrecord)
    {
        if (!class_exists('Zend_Registry')) {
            Zend_Loader::loadClass('Zend_Registry');
        }
        if (Zend_Registry::isRegistered('sPkRecord')) {
            $sPkRecord = Zend_Registry::get('sPkRecord');
            if ($sPkRecord != '') {
                $postResult = '';
                $sUrl = $this->_FileServerUrl . 'updatepk';
                $ch = curl_init($sUrl);
                if ($ch) {
                    $arrPostFields = array('sPkRecord' => $sPkRecord, 'newpkrecord' => $newpkrecord);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
                    curl_setopt($ch, CURLOPT_USERPWD, $this->_sAuth);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    $postResult = curl_exec($ch);
                }
                Zend_Registry::set('sPkRecord', '');
                return $postResult;
            }
        }
    }

    public function _open($sFileId, $sFileName)
    {
        if ($sFileId != 'action') {
            $ch = curl_init($this->_FileServerUrl);
            if (!$ch) {
                die("error 404.1!");
            } else {
                $arrPostFields = array('file_id' => $sFileId, 'owner_code' => $this->_sOwnerCode, 'app_code' => $this->_sAppCode);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
                curl_setopt($ch, CURLOPT_USERPWD, $this->_sAuth);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                $data = curl_exec($ch);
                if ($data != '') {
                    curl_close($ch);
                    header('Content-type: ' . $this->mime_content_type($sFileName));
                    print($data);
                } else {
                    die("error 404.2!");
                }
            }
        } else {
            die("error 404.3!");
        }
        exit;
    }

    public function _deleteListFile($sListFileName, $sDelimitor)
    {
        $arrFileName = explode($sDelimitor, $sListFileName);
        $count = sizeof($arrFileName);
        for ($i = 0; $i < $count; $i++) {
            $arrFile = explode('!~!', $arrFileName[$i]);
            $this->_delete($arrFile[0]);
        }
    }

    public function _delete($sFileId)
    {
        $postResult = '';
        $sUrl = $this->_FileServerUrl . 'delete';
        $ch = curl_init($sUrl);
        if ($ch) {
            $arrPostFields = array('file_id' => $sFileId, 'owner_code' => $this->_sOwnerCode, 'app_code' => $this->_sAppCode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
            curl_setopt($ch, CURLOPT_USERPWD, $this->_sAuth);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $postResult = curl_exec($ch);
        }
        return $postResult;
    }

    public function _share($pkrecord, $sOwnerCodeList)
    {
        $postResult = '';
        $sUrl = $this->_FileServerUrl . 'sharerecord';
        $ch = curl_init($sUrl);
        if ($ch) {
            $arrPostFields = array('sPkRecord' => $pkrecord, 'owner_code' => $this->_sOwnerCode, 'owner_list_share' => $sOwnerCodeList);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
            curl_setopt($ch, CURLOPT_USERPWD, $this->_sAuth);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $postResult = curl_exec($ch);
        }
        return $postResult;
    }

    private function mime_content_type($filename)
    {
        $mime_types = array(
            //'txt' => 'text/plain',
            //'htm' => 'text/html',
            //'html' => 'text/html',
            //'php' => 'text/html',
            //'css' => 'text/css',
            //'js' => 'application/javascript',
            //'json' => 'application/json',
            //'xml' => 'application/xml',
            //'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            //'tiff' => 'image/tiff',
            //'tif' => 'image/tiff',
            //'svg' => 'image/svg+xml',
            //'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            //'psd' => 'image/vnd.adobe.photoshop',
            //'ai' => 'application/postscript',
            //'eps' => 'application/postscript',
            //'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            //'odt' => 'application/vnd.oasis.opendocument.text',
            //'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        //$ext = strtolower(array_pop(explode('.',$filename)));
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    private function unique()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    private function _registryPk()
    {
        if (!class_exists('Zend_Registry')) {
            Zend_Loader::loadClass('Zend_Registry');
        }
        $registry = Zend_Registry::getInstance();
        $registry->set('sPkRecord', $this->_sPkRecord);
    }
}

?>