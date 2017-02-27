<?php

class admin_ListController extends Zend_Controller_Action
{
    public function init()
    {
        $objGen = new G_Gen();
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        Zend_Loader::loadClass('admin_modList');
        if (!$this->_request->isXmlHttpRequest()) {
            $objConfig = new G_Global();

            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            //Load ca thanh phan cau vao trang layout (index.phtml)
            $response = $this->getResponse();
            //Lay so dong tren man hinh danh sach
            $this->view->NumberRowOnPage = 15;
            //Duong dan file JS xu ly modul     
            $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
            // Load tat ca cac file Js va Css
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'admin/list.js', ',', 'js');
            // echo $this->view->LoadAllFileJsCss; die;
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'admin/list.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }
    }

    public function indexAction()
    {
        //Tao cac doi tuong
        $objList = new admin_modList();
        $ojbXmlLib = new G_Xml();
        $objCache = new G_Cache();
        //Tieu de man hinh danh sach
        $this->view->bodyTitle = 'DANH SÁCH ĐỐI TƯỢNG DANH MỤC';
        // Lay cac tham param de truyen vao phuong thuc getAllListType : dung cho search
        $iStatus = '';
        $sListTypeName = '';
        $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        //Lay thong tin loai danh muc de hien thi selectbox "Loai danh muc"
        $arrListType = $objList->getAllListType($iStatus, $sListTypeName, $sOwnerCode, 1, 10000000);
        //Tao mang mot chieu hien thi selecbox "Loai danh muc"
        $this->view->arrAllListType = G_Convert::_createOneDimensionArray($arrListType, 'PkListType', 'sName');
        $psFilterXmlString = '<?xml version="1.0"?><root><data_list></data_list></root>';
        $iListType = $arrListType[0]['PkListType'];
        //Lay thong tin danh muc doi tuong
        $iCurrentPage = 1;
        $iNumberRecordPerPage = 15;
        $arrResult = $objList->getAllList($arrListType, $iListType, $iCurrentPage, $iNumberRecordPerPage, $psFilterXmlString);

        $this->view->filexml = $arrResult['xmlFileName'];
        $psXmlFileName = G_Global::getInstance()->dirXml.$arrResult['xmlFileName'];
        $this->view->generateFilterForm = $ojbXmlLib->_xmlGenerateFormfield($psXmlFileName, 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row', 'list_of_object/filter_formfield_list', 'sXmlData', null, false, false);
        $this->view->arrListItem = $objCache->getAllObjectbyListCode('DM_SO_HS_TREN_TRANG');
        $this->view->iCurrentPage = $iCurrentPage;
        $this->view->iNumberRecordPerPage = $iNumberRecordPerPage;
    }

    public function recordAction()
    {
        $objList = new admin_modList();
        //Lay danh sach cac THE mo ta tieu tri loc 
        $psFilterXmlTagList = $this->_request->getParam('hdn_filter_xml_tag_list', "");
        $this->view->filterXmlTagList = $psFilterXmlTagList;
        //Lay danh sach cac gia tri tuong ung mo ta tieu tri loc
        $psFilterXmlValueList = $this->_request->getParam('hdn_filter_xml_value_list', "");
        // Lay cac tham param de truyen vao phuong thuc getAllListType : dung cho search
        $iStatus = '';
        $sListTypeName = '';
        $sOwnerCode = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
        //Lay thong tin loai danh muc de hien thi selectbox "Loai danh muc"
        $arrListType = $objList->getAllListType($iStatus, $sListTypeName, $sOwnerCode, 1, 2000);
        //var_dump($arrListType); die();
        $psFilterXmlString = $this->_xmlGenerateXmlDataString($psFilterXmlTagList, $psFilterXmlValueList);
        $iListType = $this->_request->getParam('listtype_type');
        //echo htmlspecialchars($psFilterXmlString);
        //Lay thong tin quy dinh so row / page
        $piCurrentPage = $this->_request->getParam('hdn_current_page');
        $piNumRowOnPage = $this->_request->getParam('hdn_record_number_page');
        //Lay thong tin danh muc doi tuong
        $arrResult = $objList->getAllList($arrListType, $iListType, $piCurrentPage, $piNumRowOnPage, $psFilterXmlString);
        //Mang luu thong tin doi tuong danh muc
        $arrAllList = $arrResult['arrList'];
        //Lay ten file XML
        $filexml = $arrResult['xmlFileName'];
        echo Zend_Json::encode(array('arrAllList' => $arrAllList, 'filexml' => $filexml));
        $this->_helper->getHelper('viewRenderer')->setNoRender();
    }

    public function addAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index');
        }
        // Tieu de cua Form cap  nhat
        $this->view->bodyTitle = 'CẬP NHẬT THÔNG TIN ĐỐI TƯỢNG DANH MỤC';
        //Tao doi tuong XML
        $ojbXmlLib = new G_Xml();
        // Tao doi tuong cho lop tren		
        $objList = new admin_modList();
        $objConst = new G_Const();
        $arrConst = $objConst->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        // Thuc hien lay du lieu tu form 		
        if ($this->_request->isPost()) {
            //Lay Id loai danh muc
            $iListType = $this->_request->getParam('hdn_id_listtype', 0);
            $this->view->iIdListType = $iListType;
            $arrListTypeResult = $objList->getSingleListType($iListType);

            $chkAutoGenerateObjectStatus = $arrListTypeResult[0]['bAutoGenerateObjectStatus'];
            $sAutoGenerateObjectCode = "";
            if ($chkAutoGenerateObjectStatus) {
                $sAutoGenerateObjectCode = $arrListTypeResult[0]['bAutoGenerateObjectCode'];
            }
            $this->view->chkAutoGenerateObjectStatus = $chkAutoGenerateObjectStatus;
            $this->view->sAutoGenerateObjectCode = $sAutoGenerateObjectCode;
            //Lay ten file XML
            $psFileName = $this->_request->getParam('hdn_xml_file', '');
            $psFileName = G_Global::getInstance()->dirXml . $psFileName;
            if (!is_file($psFileName)) {
                $psFileName = G_Global::getInstance()->dirXml . "list/quan_tri_doi_tuong_danh_muc.xml";
            }
            $psXmlStr = '<?xml version="1.0" encoding="UTF-8"?><root><data_list></data_list></root>';

            $arrGetSingleList = array('sOwnerCodeList' => Zend_Auth::getInstance()->getIdentity()->sOwnerCode, 'iOrder' => $objList->getMaxOrder($iListType));
            $this->view->generateFormHtml = $ojbXmlLib->_xmlGenerateFormfield($psFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row', 'update_object/update_formfield_list', $psXmlStr, $arrGetSingleList, false, false);
        }
    }

    public function saveAction()
    {
        $objConvert = new G_Convert();
        $objList = new admin_modList();
        $objFilter = new Zend_Filter();

        // Thuc hien lay du lieu tu form 		
        if ($this->_request->isPost()) {
            $psFilterXmlTagList = $this->_request->getParam('hdn_filter_xml_tag_list', "");
            $this->view->filterXmlTagList = $psFilterXmlTagList;
            $psFilterXmlValueList = $this->_request->getParam('hdn_filter_xml_value_list', "");
            $this->view->filterXmlValueList = $psFilterXmlValueList;
            //Lay Id loai danh muc
            $iListType = $this->_request->getParam('hdn_id_listtype', 0);
            $iListId = $this->_request->getParam('hdn_list_id', 0);
            if ($iListId == '')
                $iListId = 0;
            $_SESSION['listtypeId'] = $iListType; //Phuc vu menh de where lay so thu tu tiep theo cua doi tuong can them moi
            //Lay ten file XML
            $psFileName = $this->_request->getParam('hdn_xml_file', '');
            //echo $psFileName;
            //Neu khong ton tai file XML thi doc file XML mac dinh
            if ($psFileName == "" || !is_file($psFileName)) {
                $psFileName = G_Global::getInstance()->dirXml . "list/quan_tri_doi_tuong_danh_muc.xml";
            }
            //Lay danh sash THE va GIA TRI tuong ung mo ta chuoi XML, cau truc bien hdn_XmlTagValueList luu TagList|{*^*}|ValueList		
            $psXmlTagValueList = $this->_request->getParam('hdn_XmlTagValueList', '');
            //Tao xau XML luu CSDL
            if ($psXmlTagValueList != "") {
                $arrXmlTagValue = explode("|{*^*}|", $psXmlTagValueList);
                if ($arrXmlTagValue[0] != "" && $arrXmlTagValue[1] != "") {
                    //Danh sach THE
                    $psXmlTagList = $arrXmlTagValue[0];
                    //Danh sach GIA TRI
                    $psXmlValueList = $arrXmlTagValue[1];
                    //Tao xau XML luu CSDL
                    $psXmlStringInDb = $this->_xmlGenerateXmlDataString($psXmlTagList, $psXmlValueList);
                }
            }
            //Trang thai cua doi tuong danh muc (HOAT_DONG : hoat dong; NGUNG_HOAT_DONG ; Ngung hoat dong)
            $sStatus = 'NGUNG_HOAT_DONG';
            if ($objFilter->filter($this->_request->getParam('list_status', ''))) {
                $sStatus = 'HOAT_DONG';
            }
            //Neu NSD khong chon thuoc don vi nao thi mac dinh lay ma don vi NSD hien thoi
            $sOwnerCodeList = trim($objFilter->filter($this->_request->getParam('owner_code_list', '')));
            // echo $sOwnerCodeList; die();
            if (is_null($sOwnerCodeList)) {
                $sOwnerCodeList = Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
            }
            //Mang luu tham so update in database
            $arrParameter = array(
                'PkListType' => $iListType,
                'PkList' => $iListId,
                'sCode' => trim($objConvert->_replaceBadChar($objFilter->filter($this->_request->getParam('list_code_update', '')))),
                'sName' => trim($objConvert->_replaceBadChar($objFilter->filter($this->_request->getParam('list_name_update', '')))),
                'iOrder' => intval($objFilter->filter($this->_request->getParam('list_order', ''))),
                'sOwnerCodeList' => $sOwnerCodeList,
                'sStatus' => $sStatus,
                'DELETED_EXIST_FILE_ID_LIST' => '',
                'NEW_FILE_ID_LIST' => '',
                'GET_XML_FILE_NAME' => $psFileName
            );
            if ($objFilter->filter($this->_request->getParam('list_code_update', '')) != "") {
                $arrResult = $objList->updateList($iListType, $arrParameter, $psXmlStringInDb);
                // Neu add khong thanh cong
                if ($arrResult != null || $arrResult != '') {
                    echo "<script type='text/javascript'>";
                    echo "alert('$arrResult');\n";
                    echo "</script>";
                } else {
                    //Write Xml output file
                    $arrTempList = $objList->createXMLDb($iListType);
                    $listTypeCode = $objList->GetListTypeCode($iListType);
                    $sFilePath = G_Global::getInstance()->dirCache . Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
                    if (!is_dir($sFilePath)) {
                        G_Lib::getInstance()->_mkdir($sFilePath);
                    }
                    // Thuc hien Tao file xml
                    $this->createXML($sFilePath, $arrTempList, $listTypeCode);
                }
            }
        }
        $this->_helper->getHelper('viewRenderer')->setNoRender();
    }

    public function editAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index');
        }
        $this->view->bodyTitle = 'CẬP NHẬT THÔNG TIN ĐỐI TƯỢNG DANH MỤC';
        $ojbXmlLib = new G_Xml();
        $objList = new admin_modList();
        $objConst = new G_Const();

        $psFilterXmlTagList = $this->_request->getParam('hdn_filter_xml_tag_list', "");
        $this->view->filterXmlTagList = $psFilterXmlTagList;
        $psFilterXmlValueList = $this->_request->getParam('hdn_filter_xml_value_list', "");
        $this->view->filterXmlValueList = $psFilterXmlValueList;
        $arrConst = $objConst->_setProjectPublicConst();
        $this->view->arrConst = $arrConst;
        // Thuc hien lay du lieu tu form 		
        if ($this->_request->isPost()) {
            //Lay Id loai danh muc
            $iListType = $this->_request->getParam('hdn_id_listtype', 0);
            $this->view->iIdListType = $iListType;
            $arrListTypeResult = $objList->getSingleListType($iListType);
            $chkAutoGenerateObjectStatus = $arrListTypeResult[0]['bAutoGenerateObjectStatus'];
            $sAutoGenerateObjectCode = "";
            if ($chkAutoGenerateObjectStatus) {
                $sAutoGenerateObjectCode = $arrListTypeResult[0]['bAutoGenerateObjectCode'];
            }
            $this->view->chkAutoGenerateObjectStatus = $chkAutoGenerateObjectStatus;
            $this->view->sAutoGenerateObjectCode = $sAutoGenerateObjectCode;
            //Lay Id loai danh muc
            $iListId = $this->_request->getParam('hdn_list_id', 0);
            $this->view->iIdList = $iListId;
            //Lay ten file XML
            $psFileName = $this->_request->getParam('hdn_xml_file', '');
            $psFileName = G_Global::getInstance()->dirXml . $psFileName;
            if (!is_file($psFileName)) {
                $psFileName = G_Global::getInstance()->dirXml . "list/quan_tri_doi_tuong_danh_muc.xml";
            }
            //Lay thong tin danh muc doi tuong
            if ($iListId != 0) {
                $arrGetSingleList = $objList->getSingleList($iListId);
            } else {
                $arrGetSingleList = array();
            }
            //
            $psXmlStr = 'sXmlData';
            //Tao xau html mo ta form field cap nhat thong tin va gui ra VIEW hien thi ket qua
            $arrGet1singleInput['0'] = $arrGetSingleList;
            $this->view->generateFormHtml = $ojbXmlLib->_xmlGenerateFormfield($psFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row', 'update_object/update_formfield_list', $psXmlStr, $arrGetSingleList, false, false);
        }
    }

    public function deleteAction()
    {
        $objList = new admin_modList();
        if ($this->_request->isPost()) {
            $iListTypeId = (int)$this->_request->getParam('hdn_id_listtype', 0);
            $this->view->iIdListType = $iListTypeId;
            $iListIdList = $this->_request->getParam('hdn_object_id_list', "");
            if ($iListIdList != "") {
                $psRetError = $objList->deleteList($iListIdList);
                if ($psRetError) {
                    echo 'Lỗi xóa đối tượng danh mục!';
                } else {
                    $arrTempList = $objList->createXMLDb($iListTypeId);
                    $listTypeCode = $objList->GetListTypeCode($iListTypeId);
                    $sFilePath = G_Global::getInstance()->dirCache . Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
                    $this->createXML($sFilePath, $arrTempList, $listTypeCode);
                    echo 'OK';
                }
            }
        }
        $this->_helper->getHelper('viewRenderer')->setNoRender();
    }

    private function createXML($pFilePath, $arrList, $listTypeCode)
    {
        $objCache = new G_Cache();
        $arrUpdate = array();
        $backend = array('cache_dir' => $pFilePath);
        if (!empty($arrList)) {
            foreach ($arrList as $value) {
                $arrTemp = array(
                    'sStatus' => $value['sStatus'],
                    'sCode' => $value['sCode'],
                    'sName' => $value['sName'],
                    'sOwnerCode' => $value['sOwnerCode'],
                );
                if ($value['sXmlData'] != '') {
                    $xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . $value['sXmlData'];
                    $objXmlData = new Zend_Config_Xml($xmlData, 'data_list');
                    $arrXmlData = $objXmlData->toArray();
                    $arrKey = array_keys($arrXmlData);
                    foreach ($arrKey As $valueKey) {
                        $arrTemp[$valueKey] = $arrXmlData[$valueKey];
                    }
                }
                array_push($arrUpdate, $arrTemp);
            }
            $objCache->save_cache($arrUpdate, $listTypeCode, $options = array('backend' => $backend));
        } else {
            $objCache->clean_cache($listTypeCode, $options = array('backend' => $backend));
        }
    }

    public function importAction()
    {
        // Tieu de cua Form cap  nhat
        $this->view->bodyTitle = 'IMPORT DỮ LIỆU';
        // Tao doi tuong cho lop tren		
        $objList = new admin_modList();
        // Tao doi tuong Zend_Filter
        $objFilter = new Zend_Filter();
        // Thuc hien lay du lieu tu form 		
        if ($this->_request->isPost()) {
            // Lay toan bo tham so truyen tu form			
            $arrInput = $this->_request->getParams();
            $psFilterXmlTagList = $this->_request->getParam('hdn_filter_xml_tag_list', "");
            $this->view->filterXmlTagList = $psFilterXmlTagList;
            $psFilterXmlValueList = $this->_request->getParam('hdn_filter_xml_value_list', "");
            $this->view->filterXmlValueList = $psFilterXmlValueList;
            //Lay thong tin trang hien thoi
            $piCurrentPage = $this->_request->getParam('hdn_current_page', 0);
            $this->view->currentPage = $piCurrentPage;
            //Lay thong tin quy dinh so row / page
            $piNumRowOnPage = $this->_request->getParam('hdn_record_number_page', 0);
            $this->view->numRowOnPage = $piNumRowOnPage;
            //Lay Id loai danh muc
            $iListType = $this->_request->getParam('hdn_id_listtype', 0);
            $this->view->iIdListType = $iListType;
            $_SESSION['listtypeId'] = $iListType;
            //Lay thong tin loai danh muc de hien thi selectbox "Loai danh muc"			
            $arrListType = $objList->getAllListType("", "", Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
            $sListTypeName = "";
            for ($index = 0; $index < sizeof($arrListType); $index++) {
                if ($arrListType[$index]['PkListType'] == $iListType) {
                    $sListTypeName = $arrListType[$index]['sName'];
                    break;
                }
            }
            $this->view->sListTypeName = $sListTypeName;
            //Lay ten file XML
            $psFileName = $this->_request->getParam('hdn_xml_file', '');
            //echo $psFileName;
            //Neu khong ton tai file XML thi doc file XML mac dinh
            if ($psFileName == "" || !is_file($psFileName)) {
                $psFileName = G_Global::getInstance()->dirXml . "list/quan_tri_doi_tuong_danh_muc.xml";
            }
            //Lay danh sash THE va GIA TRI tuong ung mo ta chuoi XML, cau truc bien hdn_XmlTagValueList luu TagList|{*^*}|ValueList		
            $psXmlTagValueList = $this->_request->getParam('hdn_XmlTagValueList', '');
            //Tao xau XML luu CSDL
            $psXmlStringInDb = '';
            if ($psXmlTagValueList != "") {
                $arrXmlTagValue = explode("|{*^*}|", $psXmlTagValueList);
                if ($arrXmlTagValue[0] != "" && $arrXmlTagValue[1] != "") {
                    //Danh sach THE
                    $psXmlTagList = $arrXmlTagValue[0];
                    //Danh sach GIA TRI
                    $psXmlValueList = $arrXmlTagValue[1];
                    //Tao xau XML luu CSDL					
                    $psXmlStringInDb = $this->_xmlGenerateXmlDataString($psXmlTagList, $psXmlValueList);
                }
            }
            // Thuc hien tao mot mang de day vao view
            $this->view->arrInput = $arrInput;
            $arrParameter = array();
            if ($objFilter->filter($arrInput['sCode']) != "") {
                $arrResult = $objList->updateList($iListType, $arrParameter, $psXmlStringInDb);
                // Neu add khong thanh cong			
                if ($arrResult != null || $arrResult != '') {
                    echo "<script type='text/javascript'>";
                    echo "alert('$arrResult');\n";
                    echo "</script>";
                } else {
                    //Luu gia tri												
                    $arrParaSet = array("hdn_id_listtype" => $iListType, "hdn_xml_file" => $psFileName, "sel_page" => $piCurrentPage, "cbo_nuber_record_page" => $piNumRowOnPage, "hdn_filter_xml_tag_list" => $psFilterXmlTagList, "hdn_filter_xml_value_list" => $psFilterXmlValueList);
                    //var_dump($arrParaSet); exit;
                    $_SESSION['seArrParameter'] = $arrParaSet;
                    $this->_request->setParams($arrParaSet);
                    //Write Xml output file
                    $arrTempList = $objList->createXMLDb($iListType);
                    $listTypeCode = $objList->GetListTypeCode($iListType);
                    $sFilePath = G_Global::getInstance()->dirCache . Zend_Auth::getInstance()->getIdentity()->sOwnerCode;
                    // Thuc hien Tao file xml
                    $this->createXML($sFilePath, $arrTempList, $listTypeCode);
                    //Tro ve trang index						
                    $this->_redirect('listxml/list/index/');
                }
            }
        }
    }

    /**
     * @param $psXmlTagList
     * @param $psValueList
     * @return string
     */
    private function _xmlGenerateXmlDataString($psXmlTagList, $psValueList)
    {
        //Tao doi tuong config
        $objLib = new G_Lib();
        $objConst = new G_Const();
        $arrConst = $objConst->_setProjectPublicConst();
        $objConvert = new G_Convert();
        $strXML = '<?xml version="1.0"?><root><data_list>';
        for ($i = 0; $i < $objLib->_listGetLen($psXmlTagList, $arrConst['_CONST_SUB_LIST_DELIMITOR']); $i++) {
            $strXML = $strXML . "<" . $objLib->_listGetAt($psXmlTagList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR']) . ">";
            $strXML = $strXML . trim($objConvert->_restoreBadChar($objLib->_listGetAt($psValueList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR'])));
            $strXML = $strXML . "</" . $objLib->_listGetAt($psXmlTagList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR']) . ">";
        }
        $strXML = $strXML . "</data_list></root>";
        return $strXML;
    }
}