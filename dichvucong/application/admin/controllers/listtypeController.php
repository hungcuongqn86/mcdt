<?php

/**
 * Class Xu ly thong thong tin loai danh muc
 */
class admin_ListTypeController extends Zend_Controller_Action
{
    public function init()
    {
        Zend_Loader::loadClass('admin_modListType');
        $objGen = new G_Gen();
        if (!$this->_request->isXmlHttpRequest()) {

            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();
            $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
            // Load tat ca cac file Js va Css
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'admin/listtype.js', ',', 'js');
            //Lay cac hang so su dung trong JS public
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];

            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'admin/listtype.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }
    }

    /**
     * Creater : TRUONGDV
     * Date :
     * Idea : Tao phuong thuc hien thi danh sach loai danh muc
     *
     */
    // Xu ly thong tin de day ra file index.phtml
    public function indexAction()
    {
        //Tao cac doi tuong
        $objCache = new G_Cache();
        $this->view->bodyTitle = 'DANH SÁCH CÁC LOẠI DANH MỤC';
        $filexml = 'list/man_hinh_danh_sach.xml';
        //Render view
        $this->view->filexml = $filexml;
        $this->view->arrListItem = $objCache->getAllObjectbyListCode('DM_SO_HS_TREN_TRANG', Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $this->view->iCurrentPage = 1;
        $this->view->iNumberRecordPerPage = 15;

        $dir = G_Global::getInstance()->dirXml . 'list';
        $arrFile = scandir($dir, 1);
        $files = array();
        for ($i = 0; $i < sizeof($arrFile); $i++) {
            if (!in_array($arrFile[$i], array(".", "..")))
                array_push($files, $arrFile[$i]);
        }

        $this->view->files = $files;
        //var_dump($files); die;
    }

    public function recordAction()
    {
        //Tao doi tuong
        $objListType = new admin_modListType();
        //Lay tieu chi tim kiem
        $sListTypeName = $this->_request->getParam('txtSearch', '');
        $iStatus = '';
        $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        $hdn_current_page = $this->_request->getParam('hdn_current_page');
        $hdn_record_number_page = $this->_request->getParam('hdn_record_number_page');
        //Mảng lưu danh sách
        $arrResult = $objListType->getAllListType($iStatus, $sListTypeName, $sOwnerCode, $hdn_current_page, $hdn_record_number_page);

        echo Zend_Json::encode($arrResult);
        $this->_helper->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * @see : Thuc hien Them moi LOAI DANH Muc
     *
     */
    public function addAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index');
        }
        // Tieu de cua Form cap  nhat
        $this->view->bodyTitle = 'CẬP NHẬT MỘT LOẠI DANH MỤC';
        //Gan gia tri mac dinh cho cac Input
        $this->view->parError = '';
        $objConst = new G_Const();
        // goi load div
        $this->view->divDialog = $this->showDialog();
        $arrConst = $objConst->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        // Thuc hien lay du lieu tu form
        if ($this->_request->isPost()) {
            // Lay toan bo tham so truyen tu form
            $arrInput = $this->_request->getParams();

            // Tao doi tuong Zend_Filter
            $filter = new Zend_Filter();

            // Thuc hien tao mot mang de day vao view
            $arrInput['sCode'] = $this->_request->getParam('sCode');
            $arrInput['sName'] = $this->_request->getParam('sName');
            $arrInput['sImportStatus'] = $this->_request->getParam('sImportStatus');
            $arrInput['bAutoGenerateObjectStatus'] = $this->_request->getParam('bAutoGenerateObjectStatus');

            $arrInput['CHK_IMPORT_STATUS'] = $this->_request->getParam('CHK_IMPORT_STATUS');
            $arrInput['CHK_AUTO_GENERATE_OBJECT_CODE'] = $this->_request->getParam('CHK_AUTO_GENERATE_OBJECT_CODE');
            $arrInput['TXT_AUTO_GENERATE_OBJECT_CODE'] = $this->_request->getParam('TXT_AUTO_GENERATE_OBJECT_CODE');
            $arrInput['sXmlFileName'] = $this->_request->getParam('sXmlFileName');
            $this->view->arrInput = $arrInput;
            // Lay so thu tu lon nhat gan vao
            $this->view->arrInput['iOrder'] = $filter->filter($this->_request->getParam('hdn_order'));
        }

    }

    public function saveAction()
    {
        // Tao doi tuong modeListType
        $objListType = new admin_modListType();
        $objConvert = new G_Convert();
        //Lay cac tham so cua form
        $sListTypeCode = trim($objConvert->_restoreBadChar($this->_request->getParam('sCode')));
        $sListTypeName = trim($objConvert->_restoreBadChar($this->_request->getParam('sName')));
        $sChkImportStatus = trim($objConvert->_restoreBadChar($this->_request->getParam('CHK_IMPORT_STATUS')));
        $sChkAutoGenerateStatus = trim($objConvert->_restoreBadChar($this->_request->getParam('CHK_AUTO_GENERATE_OBJECT_CODE')));
        $sTxtAutoGenerateObjectCode = trim($objConvert->_restoreBadChar($this->_request->getParam('TXT_AUTO_GENERATE_OBJECT_CODE')));
        if (!$sChkAutoGenerateStatus) {
            $sTxtAutoGenerateObjectCode = "";
        }
        $iListTypeOrder = trim($this->_request->getParam('iOrder'));

        // Ten file up load
        // Lay ten file khi chon tu server
        if (isset($_FILES['sXmlFileName']['name'])) {
            $sListTypeXml = $_FILES['sXmlFileName']['name'];
        } else {
            $sListTypeXml = $this->_request->getParam('txt_xml_file_name');
        }

        //Id loai danh muc
        //PkListType
        $iListTypeId = (int)$this->_request->getParam('hdn_listtype_id');

        //Trang thai cua doi tuong danh muc (HOAT_DONG : hoat dong; NGUNG_HOAT_DONG ; Ngung hoat dong)
        $sStatus = 'NGUNG_HOAT_DONG';
        if ($this->_request->getParam('sStatus')) {
            $sStatus = 'HOAT_DONG';
        }

        // Lay don vi su dung
        $arrListTypeOwnerCodeList = $this->_request->getParam('chk_onwer_code_list');
        $sListTypeOwnerCodeList = '';
        for ($i = 0; $i < sizeof($arrListTypeOwnerCodeList) - 1; $i++)
            $sListTypeOwnerCodeList .= $arrListTypeOwnerCodeList[$i] . ',';
        $sListTypeOwnerCodeList .= $arrListTypeOwnerCodeList[sizeof($arrListTypeOwnerCodeList) - 1];
        $isSaveAndAddNew = $this->_request->getParam('C_SAVE_AND_ADD_NEW');
        // xu ly viec copy file len o cung
        if ($_FILES['sXmlFileName']['name'] != "") {
            move_uploaded_file($_FILES['sXmlFileName']['tmp_name'], "../xml/list/" . $_FILES['sXmlFileName']['name']);
            $sListTypeXml = $_FILES['sXmlFileName']['name'];
        }

        // Thuc hien cap nhat vao csdl
        $arrResult = $objListType->updateListType($iListTypeId, $sListTypeCode, $sListTypeName, $iListTypeOrder, $sListTypeXml, $sStatus, $sListTypeOwnerCodeList, $sChkImportStatus, $sChkAutoGenerateStatus, $sTxtAutoGenerateObjectCode);
        $this->_helper->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * @see : Thuc hien viec sua Mot hoac Nhieu Loai Danh muc
     */
    public function editAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index');
        }
        // Tieu de cua Form cap  nhat
        $this->view->bodyTitle = 'CẬP NHẬT MỘT LOẠI DANH MỤC';
        $arrConst = G_Const::_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        //Gan gia tri mac dinh cho cac Input Error
        $this->view->parError = '';

        $filter = new Zend_Filter();
        $this->view->divDialog = $this->showDialog();

        $iListTypeId = (int)$this->_request->getParam('PkListType');
        $this->view->iListTypeId = $iListTypeId;
        // Tao doi tuong
        $objListType = new  admin_modListType();
        $listTypeCode = $objListType->GetListTypeCode($iListTypeId);
        $objConvert = new G_Convert();

        $arrResult = $objListType->getSingleListType($iListTypeId);
        $arrTempResult = array(
            'PkListType' => $arrResult[0]['PkListType'],
            'sCode' => trim($objConvert->_replaceBadChar($arrResult[0]['sCode'])),
            'sName' => trim($objConvert->_replaceBadChar($arrResult[0]['sName'])),
            'iOrder' => $arrResult[0]['iOrder'],
            'sXmlFileName' => $arrResult[0]['sXmlFileName'],
            'sStatus' => $arrResult[0]['sStatus'],
            'sOwnerCodeList' => $arrResult[0]['sOwnerCodeList'],
            'sImportStatus' => $arrResult[0]['sImportStatus'],
            'bAutoGenerateObjectStatus' => $arrResult[0]['bAutoGenerateObjectStatus'],
            'bAutoGenerateObjectCode' => $arrResult[0]['bAutoGenerateObjectCode']
        );
        // Thuc hien bind du lieu vao view
        $this->view->arrInput = $arrTempResult;

        // Xu ly nut Tinh trang
        if ($arrTempResult['sStatus'] == 1) {
            $this->view->bStatus = true;
        } else {
            $this->view->bStatus = false;
        }
    }

    /**
     * @see : Thuc hien viec xoa Mot hoac Nhieu Loai Danh muc
     */
    public function deleteAction()
    {
        //Request hidden luu id da duoc chon
        $sListTypeIdList = $this->_request->getParam('hdn_object_id_list');
        $objListType = new  admin_modListType();
        $arrResult = $objListType->deleteListType($sListTypeIdList);
        // Kiem tra
        if ($arrResult != null || $arrResult != '') {
            echo $arrResult;
        } else {
            $objCache = new G_Cache();
            $options = array('backend' => array('cache_dir' => G_Global::getInstance()->dirCache . Zend_Auth::getInstance()->getIdentity()->sOwnerCode));
            //Tra ve trang index
            $arrListTypeCode = $objListType->GetListTypeCodeFromIdList($sListTypeIdList);
            foreach ($arrListTypeCode as $value) {
                $objCache->clean_cache($value, $options);
            }
            echo 'OK';
        }
        die();
    }

    private function showDialog()
    {

        $dir = "./xml/list/";
        $objConfig = new G_Global();
        $sResHtml = '';
        $sResHtml = $sResHtml . "<div style='overflow:auto;height:95%; width:98%; padding: 6px 2px 2px 2px;'>";

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    // kt la file xml thi hien thi
                    $filetype = substr($file, strlen($file) - 4, 4);
                    $filetype = strtolower($filetype);
                    if ($filetype == ".xml") {
                        $sResHtml = $sResHtml . "<p  class='normal_label' style='width:95%'  align='left' >";
                        $sResHtml = $sResHtml . " 	<img src ='" . $objConfig->_setImageUrlPath() . "file_icon.gif' width='12' />";
                        $sResHtml = $sResHtml . "		<a href='#' onClick =\"getFileNameFromDiv('" . $file . "')\">" . $file . "</a>";
                        $sResHtml = $sResHtml . "</p>";
                    }
                }
                closedir($dh);
            }
        }
        $sResHtml = $sResHtml . '</div>';

        return $sResHtml;
    }

    public function xmlAction()
    {
        Zend_Loader::loadClass('admin_modList');
        $objList = new admin_modList();
        $arrTempList = $objList->createXMLDb('');
        $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        // duong dan
        $sFilePath = G_Global::getInstance()->dirXml . 'list/output/' . $sOwnerCode . "/";
        if (!file_exists($sFilePath)) {
            mkdir($sFilePath, 0777);
        }
        // Thuc hien Tao file xml
        $this->createXML($sFilePath, $arrTempList);
        die();
    }

    private function createXML($pFilePath, $arrList)
    {
        $strXmlItem = '';
        $listTypeCode = $arrList[0]['LISTTYPE_CODE'];
        $strXml = '<?xml version="1.0" encoding="UTF-8"?><root><data_list>';
        $count = sizeof($arrList);
        for ($i = 0; $i < $count; $i++) {
            if ($listTypeCode != $arrList[$i]['LISTTYPE_CODE']) {
                $strXml .= '</data_list></root>';
                G_Lib::_writeFile($pFilePath . $listTypeCode . '.xml', $strXml);
                $listTypeCode = $arrList[$i]['LISTTYPE_CODE'];
                $strXml = '<?xml version="1.0" encoding="UTF-8"?><root><data_list>';
            }
            $strXmlItem = '<item><sStatus>' . $arrList[$i]['sStatus'] . '</sStatus><sCode>' . $arrList[$i]['sCode'] . '</sCode><sName>' . $arrList[$i]['sName'] . '</sName><sOwnerCode>' . $arrList[$i]['C_OWNERCODE'] . '</sOwnerCode>';
            if ($arrList[$i]['sXmlData'] != '') {
                $xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . $arrList[$i]['sXmlData'];
                $objXmlData = new Zend_Config_Xml($xmlData, 'data_list');
                $arrXmlData = $objXmlData->toArray();
                $arrKey = array_keys($arrXmlData);
                foreach ($arrKey As $value) {
                    $strXmlItem .= '<' . $value . '>' . $arrXmlData[$value] . '</' . $value . '>';
                }
            }
            $strXmlItem .= '</item>';
            $strXml .= $strXmlItem;
        }
        $strXml .= '</data_list></root>';
        G_Lib::_writeFile($pFilePath . $listTypeCode . '.xml', $strXml);
    }

    public function exportcachefileAction()
    {
        Zend_Loader::loadClass('admin_modList');
        $objList = new admin_modList();
        $arrTempList = $objList->createXMLDb('');
        // Thuc hien Tao file cache
        $this->createCache($arrTempList);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    private function createCache($arrList)
    {
        $cache = new G_Cache();
        $listTypeCode = $arrList[0]['LISTTYPE_CODE'];
        $count = sizeof($arrList);
        $arrTemp = array();
        $ss = 0;
        $iTem = 0;
        $dirCache = G_Global::getInstance()->dirCache;
        $objLib = new G_Lib();
        for ($i = 0; $i < $count; $i++) {
            if ($listTypeCode != $arrList[$i]['LISTTYPE_CODE']) {
                $arrTemp = array();
                $iTem = 0;
                $listTypeCode = $arrList[$i]['LISTTYPE_CODE'];
            }
            //
            if ($arrList[$i]['sStatus'] == 'HOAT_DONG') {
                $arrTemp[$iTem]['sCode'] = $arrList[$i]['sCode'];
                $arrTemp[$iTem]['sName'] = $arrList[$i]['sName'];
                $arrTemp[$iTem]['sStatus'] = $arrList[$i]['sStatus'];
                $arrTemp[$iTem]['sOwnerCode'] = $arrList[$i]['C_OWNERCODE'];
                if ($arrList[$i]['sXmlData'] != '') {
                    $xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . $arrList[$i]['sXmlData'];
                    $objXmlData = new Zend_Config_Xml($xmlData, 'data_list');
                    $arrXmlData = $objXmlData->toArray();
                    $arrKey = array_keys($arrXmlData);
                    foreach ($arrKey As $value) {
                        if (trim($arrXmlData[$value]) != '') {
                            $sColumnName = '' . $value . '';
                            $arrTemp[$iTem][$sColumnName] = $arrXmlData[$value];
                        }
                    }
                }
                $iTem++;
            }
            if ($count === $i + 1 || $listTypeCode != $arrList[$i + 1]['LISTTYPE_CODE']) {
                $ownercodelist = $arrList[$i]['C_OWNERCODE'];
                $arrOwnerCode = explode(',', $ownercodelist);
                foreach ($arrOwnerCode as $ownercode) {
                    $pathFile = $dirCache . $ownercode;
                    if (!is_dir($pathFile)) {
                        $objLib->_mkdir($pathFile);
                    }
                    $options = array('backend' => array('cache_dir' => $pathFile));
                    $cache->save_cache($arrTemp, $listTypeCode, $options);
                }
            }
        }
    }
}

?>