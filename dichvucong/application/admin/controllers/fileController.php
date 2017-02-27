<?php

/**
 * cuongnh
 * Class giao tiep voi fileServer
 */
class admin_fileController extends Zend_Controller_Action
{
    public function openAction()
    {
        $arrInput = $this->_request->getParams();
        foreach ($arrInput as $sFileId => $sFileName) {
        }
        $fileObj = new G_FileServer();
        $fileObj->_open($sFileId, $sFileName);
    }
}

?>