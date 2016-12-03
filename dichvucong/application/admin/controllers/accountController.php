<?php

/**
 * Nguoi tao: Truongdv
 * Ngay tao: 25/11/2015
 * Y nghia: Quan tri tai khoan
 */
class admin_accountController extends Zend_Controller_Action
{
    public function init()
    {
        $objGen = new G_Gen();
        Zend_Loader::loadClass('account_modAccount');
        //Lay duong dan thu muc goc (path directory root)
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        if (!$this->_request->isXmlHttpRequest()) {

            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->appName);             
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();
            $this->view->arrConst = G_Const::getInstance()->_setProjectPublicConst();
            // Load tat ca cac file Js va Css
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'admin/account.js', ',', 'js');
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'admin/account.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }
    }

    /**
     * @author:Truongdv
     * @see: 30/01/2013
     * @todo:
     * Enter description here ...
     */
    public function indexAction()
    {
        $this->view->headTitle()->setSeparator(' - ')->prepend('Quản trị tài khoản'); 
        $this->view->bodyTitle = 'DANH SÁCH TÀI KHOẢN CHỜ DUYỆT';
        $this->view->iCurrentPage = 1;
        $this->view->iNumberRecordPerPage = 15;
    }

    public function loadlistAction()
    {
        $dbConnect = new account_modAccount();
        $fromDate = $this->_request->getParam('fromDate', '');
        $toDate = $this->_request->getParam('toDate', '');
        if ($fromDate != '')
            $fromDate = G_Convert::_ddmmyyyyToYYyymmdd($fromDate);
        if ($toDate != '')
            $toDate = G_Convert::_ddmmyyyyToYYyymmdd($toDate);
        $params = array(
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'status' => trim($this->_request->getParam('status', '0')),
            'fulltextsearch' => trim($this->_request->getParam('fulltextsearch', '')),
            'currentPage' => $this->_request->getParam('hdn_current_page', '1'),
            'numberRecordPerPage' => $this->_request->getParam('hdn_record_number_page', '15')
        );
        $arrResult = $dbConnect->getAll($params);
        if($arrResult)
            for ($i=0; $i < sizeof($arrResult); $i++) { 
                $arrResult[$i]['dRegisterDate'] = date('d/m/Y H:i', strtotime($arrResult[$i]['dRegisterDate']));
                $arrResult[$i]['sFullName'] = $arrResult[$i]['sFirstName'] . ' ' . $arrResult[$i]['sLastName'];
            }
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function deleteAction()
    {
        $listId = $this->_request->getParam('listId', '');
        $arrResult = array('RESULT' => 0);
        if ($listId) {
            $dbConnect = new G_Db();
            $arrResult = $dbConnect->_querySql(array('listId' => $listId), 'sp_AccountDelete', 0, 0);
        }
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function editAction() {
        $model = new account_modAccount();
        $arrSingle = $model->getIdentityUserByID($this->_request->getParam('id', ''));
        $arrSingle['dBirthday'] = date('d/m/Y', strtotime($arrSingle['dBirthday']));        
        $this->view->data = $arrSingle;
    }


    public function approveAction() {
        $model = new account_modAccount();
        $arrSingle = $model->getIdentityUserByID($this->_request->getParam('id', ''));
        $arrSingle['dBirthday'] = date('d/m/Y', strtotime($arrSingle['dBirthday']));        
        $this->view->data = $arrSingle;
    }

    public function saveapproveAction() {
        $model = new account_modAccount();
        $arrInput = $this->_request->getParams();
        $param = array(
                        'pk' => $arrInput['ID'],
                        'sFirstName' => $arrInput['sFirstName'],
                        'sLastName' => $arrInput['sLastName'],
                        'sAddress' => $arrInput['sAddress'],
                        'sEmail' => $arrInput['sEmail'],
                        'sMobile' => $arrInput['sMobile'],
                        'iSex' => $arrInput['iSex'],
                        'dBirthday' => date('Y/m/d', strtotime(str_replace('/', '-', $arrInput['dBirthday']))),
                        'sIdCard' => $arrInput['sIdCard'],
                        'sStatus' => 3,
                    );
        /*
        * status: 
        * 0 cho duyet
        * 1 dang hoat dong
        * 2 ngung hoat dong
        * 3 cho kich hoat
        */
        $result = $model->approve($param);
        if ($result['error'] == false) {
            $objLib = new G_Lib();
            $sFullName = $arrInput['sFirstName'] . ' ' . $arrInput['sLastName'];
            $body = '<p><b>Kính chào quý khách: ' . $sFullName . '</b><br>';
            $body .= '<br>Đây là email thông báo từ hệ thống ' . $sysConst->title . '<br>';
            $url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
            $url .= G_Global::getInstance()->sitePath . 'account/index/active?';
            
            $url .= http_build_query(array('username' => $arrInput['sEmail'], 'auth_code' => $result['sAuth']));

            $body .= '<br>Hãy click vào <a href="' . $url . '" target="_blank"><strong>Đây</strong></a> hoặc link bên dưới để kích hoạt sử dụng tài khoản<br>';
            $body .= '<br>&nbsp;&nbsp;Link: <b>' . $url . '</b> <br>';
            
            $message = array('to' => $arrInput['sEmail'],
                            'subject' => 'Thông báo kích hoạt tài khoản',
                            'body' => $body,
                        );
            // Gui email
            $res = $objLib->sendEmail($message);
            if ($res) {
                $result['msg'] = 'Duyệt thành công, đã gửi email';
            } else {
                $result['msg'] = 'Duyệt thành công, chưa gửi được email';
            }
        }
        echo json_encode($result);
        die;
    }

    public function saveAction() {
        $model = new account_modAccount();
        $arrInput = $this->_request->getParams();
        $param = array(
                        'pk' => $arrInput['ID'],
                        'sFirstName' => $arrInput['sFirstName'],
                        'sLastName' => $arrInput['sLastName'],
                        'sAddress' => $arrInput['sAddress'],
                        'sEmail' => $arrInput['sEmail'],
                        'sMobile' => $arrInput['sMobile'],
                        'iSex' => $arrInput['iSex'],
                        'dBirthday' => date('Y/m/d', strtotime(str_replace('/', '-', $arrInput['dBirthday']))),
                        'sIdCard' => $arrInput['sIdCard'],
                        'sStatus' => $arrInput['sStatus'],
                    );

        $result = $model->save($param);
        if ($result['error'] == false) {
            $result['msg'] = 'Cập nhật thành công';
        }
        echo json_encode($result);
        die;
    }

    public function sendmailAction() {

        $resp = array('error' => true, 'msg' => 'Lỗi gửi mail!');
        if ($this->getRequest()->isPost()) {
            $model = new account_modAccount();
            $arrSingle = $model->getIdentityUserByID($this->_request->getParam('id', 'B83843F0-D9C9-498C-9EE0-40DFE4F60CC7'));
            $objLib = new G_Lib();
            $body = 'Đặng Văn Trường';
            $arrSingle['sEmail'] = 'truongdv@g4tech.vn';
            $message = array('to' => $arrSingle['sEmail'],
                            'subject' => 'Thông báo kích hoạt tài khoản',
                            'body' => $body,
                        );
            // Gui email
            $result = $objLib->sendEmail($message);
            if ($result) {
                $resp = array('error' => false, 'msg' => 'Gửi thành công!');
            }
        }
        die(json_encode($resp));
    }
}

?>