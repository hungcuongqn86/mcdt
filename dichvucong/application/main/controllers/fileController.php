<?php

class main_fileController extends Zend_Controller_Action
{
    public function init()
    {
       
    }

    public function indexAction()
    {

    }

    public function uploadAction()
    {
        $upload_dir = realpath('io/tempupload') . '/';
        $options = array(
//            'upload_dir' => G_Global::getInstance()->dirTempUpload,
            'script_url' => $this->_request->getBaseUrl() . '/main/file/upload/',
            'download_url' => $this->_request->getBaseUrl() . '/main/file/gettemp?id=',
            'upload_dir' => $upload_dir,
            'access_control_allow_methods' => array('GET', 'POST'),
            'delete_type' => 'POST',
            'accept_file_types' => '/\.(gif|jpe?g|png|pdf|doc|docx|xls|xlsx|rar|zip|7z|xml)$/i',
            'create_folder_by_date' => false

        );

        $upload_handler = new G_Components_UploadHandler($options);
        die;
    }

    public function deleteAction()
    {
        $upload_dir = realpath('io/tempupload') . '/';
        $options = array(
//            'upload_dir' => G_Global::getInstance()->dirTempUpload,
            'script_url' => $this->_request->getBaseUrl() . '/main/file/upload/',
            'upload_dir' => $upload_dir,
            'access_control_allow_methods' => array('GET', 'POST'),
            'delete_type' => 'POST',
            'accept_file_types' => '/\.(gif|jpe?g|png|pdf|doc|docx|xls|xlsx|rar|zip|7z|xml)$/i',
            'create_folder_by_date' => false

        );

        $upload_handler = new G_Components_UploadHandler($options);
        die;
    }

    public function downloadAction()
    {
        $param = G_Global::getInstance()->_parameIntegrate();
        $href = $param['htkk'] . '/main/download/getcontent';
        $fileId = $this->_request->getParam('id');
        $file_name = array_pop(explode('!~~!', $fileId));
        $arrPostFields = array('id' => $fileId);
        $ch = curl_init($href);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $contents = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
            echo "\n<br />";
            $contents = '';
        } else {
            curl_close($ch);
        }
        if (!is_string($contents) || !strlen($contents)) {
            echo "Failed to get contents.";
            $contents = '';
        }

        // Directly return the Response
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $file_name . '"')
            ->appendBody($contents);
        $this->_helper->viewRenderer->setNoRender(true);
        return $response;
    }

    public function gettempAction()
    {
        ini_set('display_error', 0);
        $fileId = $this->_request->getParam('id');
        $path = G_Global::getInstance()->dirTempUpload . $fileId;
        $file_content = file_get_contents($path);
        $file_name = @array_pop(explode('!~!', $fileId));

        // Directly return the Response
        $response = $this->getResponse();
        $response->setHeader('Content-Type', $this->mime_content_type($file_name))
            ->setHeader('Content-Disposition', 'attachment;filename="' . $file_name . '"')
            ->setBody($file_content);
        $this->_helper->viewRenderer->setNoRender(true);
        return $response;
    }

    public function templateAction()
    {
        $multiple = $this->_request->getParam('multiple', '');
        if ($multiple) {
            $this->renderScript('file/template.phtml');
        } else {
            $this->renderScript('file/upload_checkbox.phtml');
        }
    }

    public function openAction()
    {
        $fileId = $this->_request->getParam('id');
        $fileIds = explode('_', $fileId);
        $href = realpath('io/attach-file')
            . DIRECTORY_SEPARATOR . $fileIds[0]
            . DIRECTORY_SEPARATOR . $fileIds[1]
            . DIRECTORY_SEPARATOR . $fileIds[2]
            . DIRECTORY_SEPARATOR . $fileId;

        $file_content = file_get_contents($href);
        $file_name = @array_pop(explode('!~!', $fileId));

        header('Content-type: ' . $this->mime_content_type($file_name));
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        print($file_content);
        die;
    }

    private function mime_content_type($filename)
    {
        $mime_types = array(
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',

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

            // ms office
            'doc' => 'application/msword',
            'docx' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint'
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
}

?>