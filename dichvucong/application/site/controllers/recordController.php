<?php

/**
 * @author: Truongdv
 * @see: 24/11/2015
 * @todo: Gui ho so qua mang
 */
class recordController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        $objGen = new G_Gen();
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'main'
            ));
            $response = $this->getResponse();
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->title); 
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();

            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'ui/jquery-ui.js,ui/external/jquery.ui.datepicker-vi.js,process/paging.js,chosen/chosen.jquery.js,libXml.js,site/record.js', ',', 'js')
                .$objGen->_gCssJs('', 'js', 'ui/jquery-ui.css', ',', 'css')
                .$objGen->_gCssJs('', 'css', 'chosen/chosen.css,main.css', ',', 'css');
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('fe_menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('fe_footer.phtml', './application/layout/scripts/'));

        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'site/record.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }

    }

    public function indexAction()
    {
        $this->view->headTitle()->setSeparator(' - ')->prepend('Gửi hồ sơ');
        $dbconnect = new G_Db();
        $sql = 'Select PK_RECORDTYPE,C_NAME,C_CODE from T_eCS_RECORDTYPE where C_IS_REGISTER_ON_NET=1 And C_STATUS =\'HOAT_DONG\' ';
        $arrRecordType = $dbconnect->_querySql(array(), $sql, 1, 0);
        $this->view->arrRecordType = $arrRecordType;
        $this->view->iCurrentPage = 1;
        $this->view->iNumberRecordPerPage = 10;
    }

    public function loadlistAction()
    {
        $dbconnect = new G_Db();
        $arrResult = array(array('C_TEXT_STATUS' => 'Chờ nhận '));
        // parseDataXml
        $recordTypeCode = $this->_request->getParam('recordTypeCode', '');
        $recordtype = $this->_request->getParam('recordtype', '');
        $xmlFile = $this->getDirectoryFileXml($recordTypeCode);
        $arrInput = array(
                'recordtype' => $recordtype,
                'userId' => G_Account::getInstance()->getIdentity()->ID,
                'currentPage' => $this->_request->getParam('hdn_current_page', '1'),
                'numberRecordPerPage' => $this->_request->getParam('hdn_record_number_page', '15')
            );
        $arrResult = $dbconnect->_querySql($arrInput, 'dbo.eCS_NetRecordGetAllByType', 1, 0);
        if ($arrResult) {
            $arrResult = G_Xml::getInstance()->parseDataXml($xmlFile, 'list_of_object/list_body/col', $arrResult, 'C_XML_DATA');
        }
        // echo '<pre>';
        // var_dump($arrResult); die;
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    private function getDirectoryFileXml($recordTypeCode, $type = 'listview') {
        // $dirXml = G_Global::getInstance()->dirXmlExt;
        $dirXml = G_Global::getInstance()->dirXml . 'record';
        $fileName = '';
        switch ($type) {
            case 'listview':
                $fileName = 'ho_so_da_dang_ky_qua_mang.xml';
                break;
            case 'formfield':
                $fileName = 'ho_so_da_tiep_nhan.xml';
                break;
        }
        // $recordTypeCode = 'sdf';
        $xmlFile = $dirXml . '/' . $recordTypeCode . '/' . $fileName;
        if(!file_exists($xmlFile)){
            $xmlFile = $dirXml.'/other/' . $fileName;
        }
        return $xmlFile;
    }

    /**
     * @author:
     * @see:
     * @todo: Them ho so
     */
    public function addAction()
    {
        $objXml = new G_Xml();
        $recordTypeCode = $this->_request->getParam('recordTypeCode', '');
        $recordtype = $this->_request->getParam('recordtype', '');
        $recordId = $this->_request->getParam('recordId', '');
        
        $myRecord = new Zend_Session_Namespace('Record');
        $myRecord->setExpirationSeconds(900);
        $myRecord->recordTypeCode = $recordTypeCode;
        $myRecord->recordTypeId = $recordtype;
        $myRecord->recordId = $recordId;
        $xmlFile = $this->getDirectoryFileXml($recordTypeCode, 'formfield');
        $record = array();
        if ($recordId) {
            $this->view->bodyTitle = "CHỈNH SỬA HỒ SƠ";
            $dbconnect = new  G_Db(); 
            $record = $dbconnect->_querySql(array('recordId' => $recordId), 'dbo.eCS_NetRecordGetSingle', 0, 0);
            $recordCode = $record['C_CODE'];
            $myRecord->inputDate = $record['C_INPUT_DATE'];
            $status = $record['C_STATUS'];
            $received_date = date('d/m/Y', strtotime($record['C_INPUT_DATE']));
        } else{
            $this->view->bodyTitle = "ĐĂNG KÝ HỒ SƠ MỚI";
            $recordCode = $this->generateRecordCodeNET($recordTypeCode);
            $received_date = date('d/m/Y');
            $status = 'CHO_TIEP_NHAN_SO_BO';
        }
        $myRecord->status = $status;
        $data = array(
                'RecodeCode' => $recordCode,
                'RecodeTypeName' =>  $this->_request->getParam('recordTypeName', ''),
                'C_RECEIVED_DATE' => $received_date,
                'status' => $status,
            );
        $formUpdate = $objXml->_xmlGenerateFormfield($xmlFile, 'update_object/table_struct_of_update_form/update_row_list/update_row', 'update_object/update_formfield_list', 'C_XML_DATA', $record, false, false);
        $this->view->formUpdate = $formUpdate;
        $this->view->data = $data;
    }

    private function generateRecordCodeNET($srecordtype) 
    {
        $ownercode = G_Account::getInstance()->getIdentity()->sOwnerCode;
        $dbconnect = new  G_Db(); 
        $v_inc_code_length = 5;
        $v_fix_code = 'e'.$ownercode. "." .$srecordtype. "." .date("y");
        $v_str_count = strlen($v_fix_code);
        $str_sql = " Select Max(SUBSTRING(C_CODE, ". (int)($v_str_count+2) .", ". (int)($v_inc_code_length+2) .")) MAX_CODE ";
        $str_sql = $str_sql." From T_eCS_NET_RECORD";
        $str_sql = $str_sql." Where SUBSTRING(C_CODE,1,". (int)($v_str_count) .") = ". $dbconnect->qstr($v_fix_code) ." And LEN(C_CODE)= ". (int)($v_inc_code_length+$v_str_count+1);
        try{
            $result = $dbconnect->adodbExecSqlString($str_sql);
        }catch (Exception $e){
            echo $e->getMessage();
        };      
        $v_next_code = $result['MAX_CODE'];
        if (is_null($v_next_code) || $v_next_code==""){
            $v_next_code = 1;
            $v_next_code = str_repeat("0", $v_inc_code_length-strlen($v_next_code)).$v_next_code;
        }else{
            $v_next_code = intval($v_next_code)+1;
            $v_next_code = str_repeat("0", $v_inc_code_length-strlen($v_next_code)).$v_next_code;
        }
        
        return $v_fix_code. "." .$v_next_code;
    }
    /**
     * @author:
     * @see:
     * @todo: Sua ho so
     */
    public function editAction()
    {
        

    }

    public function saveAction()
    {   
        $user = G_Account::getInstance()->getIdentity();
        $myRecord = new Zend_Session_Namespace('Record');
        if ($this->getRequest()->isPost() && $myRecord->recordTypeId) {
            $dbconnect = new G_Db();
            $strXml = '<root><data_list>';
            $sXmlTags = $this->_request->getParam('hdn_xml_tag_list','');
            $sXmlValues = $this->_request->getParam('hdn_xml_value_list','');
            $arrXmlTags = explode('!~~!', $sXmlTags);
            $arrXmlValues = explode('!~~!', $sXmlValues);
            for ($i = 0; $i < sizeof($arrXmlTags); $i++)
                $strXml .= '<' . $arrXmlTags[$i] . '>' . $arrXmlValues[$i] . '</' . $arrXmlTags[$i] . '>';
            $strXml = $strXml . "</data_list></root>";
            if (isset($myRecord->inputDate)) {
                $dInputDate = $myRecord->inputDate;
            } else {
                $dInputDate = date('Y/m/d H:i:s', strtotime(str_replace('/', '-', $this->_request->getParam('received_date'))));
            }
            $arrInput = array(  
                'PK_NET_RECORD' => $myRecord->recordId,         
                'FK_RECORDTYPE' => $myRecord->recordTypeId,
                'FK_NET_ID' => $user->ID,    
                'C_CODE' => $this->_request->getParam('C_CODE',''),
                'C_INPUT_DATE' => $dInputDate,
                'C_PRELIMINARY_DATE' =>null,
                'C_ORIGINAL_APPLICATION_DATE' =>null,
                'C_RECEIVING_DATE' => null,
                'C_XML_DATA' => $strXml,
                'C_STATUS' => 'CHO_TIEP_NHAN_SO_BO',
                'C_MESSAGE' => '',
                'C_UNIT' =>$user->sOwnerCode
            ); 
            $dbconnect->BeginTrans();
            $arrResult = $dbconnect->_querySql($arrInput, 'dbo.eCS_NetRecordUpdate', 0, 0);
            if ($arrResult) {
                $resp = array('error' => false, 'msg' => 'Gửi thành công!');
                // Thuc hien update file dinh kem
                $sFileAttachList = $this->_request->getParam('sFileAttachList', '');
                if ($sFileAttachList) {
                    $paramFile = array(
                            'sFileAttachList' => $sFileAttachList,
                            'sDocTypeList' => $this->_request->getParam('sDocTypeList', ''),
                            'sDelimitor' => $this->_request->getParam('sDelimitor', ''),
                            'sUnFileAttachList' => $this->_request->getParam('sUnFileAttachList', ''),
                            'locationList' => $this->_request->getParam('locationList', ''),
                            'key' => $arrResult['NEW_ID']
                        );
                    $files = G_Lib::getInstance()->fileUpdate($paramFile);

                    $arrInput = array(
                            'fkDoc' => $files['fkDoc'],
                            'fileList' => $files['fileList'],
                            'doctypeList' => $files['doctypeList'],
                            'tableName' => 'T_eCS_NET_RECORD',
                            'sDelimitor' => $files['sDelimitor']
                        );
                    $rs = $dbconnect->_querySql($arrInput, 'dbo.sp_SysFileUpdate', 0, 0);

                    if (empty($rs) || $rs == false) {
                        $resp = array('error' => true, 'msg' => 'Lỗi cập nhật File!');
                    }
                }
            } else {
                $resp = array('error' => true, 'msg' => 'Lỗi cập nhật CSDL!');
            }            
            
            // Cap nhat CSDL
            if ($resp['error'] == false) {
                $dbconnect->CommitTrans();
            } else {
                $dbconnect->RollbackTrans();
            }
            
        } else {
            $resp = array('error' => true, 'msg' => 'Yêu cầu không hợp lệ!');
        }

        die(json_encode($resp));
    }
    

    public function templateAction() {
        $recordTypeCode = $this->_request->getParam('code', '');
        $xmlFile = $this->getDirectoryFileXml($recordTypeCode);

        $xml = simplexml_load_file($xmlFile);
        if (isset($xml->list_of_object->list_body)) {
            $body = $xml->list_of_object->list_body->asXML();
        } else {
            $body = $xml->asXML();
        }
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'text/xml')
                    ->appendBody($body);
        $this->_helper->viewRenderer->setNoRender(true);
        return $response;
    }

    public function deleteAction() {
        $dbconnect = new G_Db();
        $recordId = implode(',', $this->_request->getParam('recordId', ''));
        $result = $dbconnect->_querySql(array('recordIdList' => $recordId), 'dbo.sp_NetRecordDelete', 0, 0);
        // echo $result; die;
        if ($result) {
            $iRow = $result['iRow'];
            $iTotal = $result['iTotal'];
            $msg = 'Xóa thành công '. $iRow . '/' . $iTotal . ' hồ sơ';
            if ($iRow < $iTotal) {
                $msg .= ' (Có '. ($iTotal - $iRow) . ' hồ sơ đã được xử lý không thể xóa!)';
            }
            $resp = array(
                    'error' => false,
                    'msg' => $msg
                );
        } else {
            $resp = array(
                    'error' => true,
                    'msg' => 'Lỗi xóa hồ sơ'
                );
        }
        die(json_encode($resp));
    }
}

?>