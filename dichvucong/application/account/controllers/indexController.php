<?php

/**
 * creater:
 * date:
 * Class: Thông tin đăng nhập/Đăng xuất
 */
class account_indexController extends Zend_Controller_Action
{
    public function init()
    {
        Zend_Loader::loadClass('account_modAccount');
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'main'
            ));
            // $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
            $this->view->slugclass="login";
            $response = $this->getResponse();
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->title);
            $response->insert('menu', $this->view->renderLayout('fe_menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('fe_footer.phtml', './application/layout/scripts/'));
        }
    }

    public function setLayoutIndex()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            //Hien thi file template
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        }
    }

    public function indexAction()
    {
        $auth = G_Account::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect($this->_request->getHeader('referer'));
        }

        $user = base64_decode($this->_request->getParam('newsletterEmail'));
        $pass = base64_decode($this->_request->getParam('newsletterpass'));
        $remember_me = $this->_request->getParam('dwfrm_login_rememberme', '');

        if ($this->getRequest()->isPost() && $user && $pass) {
            $muser = new account_modAccount;
            $login = $muser->check_login($user, $pass);
            if ($login['logined']) {
                if ($remember_me)
                    Zend_Session::rememberMe();
                $auth = G_Account::getInstance()->getIdentity();
                $this->writeHistory($auth->ID, $auth->sFullName);
                
                $lastRequest = new Zend_Session_Namespace('lastRequest');
                $lastRequest = $lastRequest->lastRequestUri;
                if ($auth->sRole == 'ADMIN') {
                    $lastRequest = 'admin/account/index';
                }
                Zend_Session::namespaceUnset('lastRequest');
                $this->_redirect($lastRequest);
            } else {
                $this->view->message = $login['msg'];
            }
        }
        $this->view->data = array(
                'email' => $user,
                'password' => $pass,
                'remember_me' => $remember_me
            );
        $this->view->user = $user;
    }

    public function loginAction()
    {
        if ($this->_request->isXmlHttpRequest() == false) {
            $this->_redirect($this->_request->getHeader('referer'));
        }
        $resp = array('logined' => false, 'msg' => '');

        $mysession = new Zend_Session_Namespace('authtoken');
        $hash = $mysession->authtoken;
        if($hash != $this->_request->getParam('authtoken'))
            die(json_encode(array('logined' => false, 'msg' => 'Token không hợp lệ')));

        $auth = G_Account::getInstance();
        if ($auth->hasIdentity()) {
            $resp = array('logined' => true, 'msg' => '');
            die(json_encode($resp));
        }
        $user = base64_decode($this->_request->getParam('newsletterEmail'));
        $pass = base64_decode($this->_request->getParam('newsletterpass'));
        $remember_me = $this->_request->getParam('dwfrm_login_rememberme', '');
        if ($this->getRequest()->isPost() && $user && $pass) {
            $muser = new account_modAccount;
            $resp = $muser->check_login($user, $pass);
            if ($resp['logined']) {
                if ($remember_me)
                    Zend_Session::rememberMe();
                $auth = G_Account::getInstance()->getIdentity();
                $this->writeHistory($auth->ID, $auth->sFullName);
            }
        }
        die(json_encode($resp));
    }

    public function writeHistory($id, $name)
    {
        $server = Zend_Controller_Front::getInstance()->getRequest()->getServer();
        $dbConnect = new G_Db();
        $sql = "INSERT INTO SysAccountHistory(AccountID, sPositionName, Ip, sAgent, domain, sType, dTime) ";
        $sql .= " VALUES (" . $dbConnect->qstr($id);
        $sql .= ", " . $dbConnect->qstr($name);
        $sql .= ", " . $dbConnect->qstr($server['REMOTE_ADDR']);
        $sql .= ", " . $dbConnect->qstr($server['HTTP_USER_AGENT']);
        $sql .= ", " . $dbConnect->qstr($server['HTTP_HOST']);
        $sql .= ", " . $dbConnect->qstr('LOGIN');
        $sql .= ", " . $dbConnect->qstr(date('Y/m/d H:i:s')) . ")";
        $dbConnect->_querySql(array(), $sql, 0, 0);
    }

    public function logoutAction()
    {
        Zend_Session::destroy();
        if (strpos($this->_request->getHeader('referer'), 'dang-xuat'))
            $this->_redirect('');
        else
            $this->_redirect($this->_request->getHeader('referer'));

    }

    public function preDispatch()
    {
        if ($this->_request->getActionName() == 'index') {
            $session = new Zend_Session_Namespace('lastRequest');
            if (isset($session->lastRequestUri) == false) {
                $session->lastRequestUri = $this->_request->getHeader('referer');
            }
        }
    }

    public function forgotpasswordAction()
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $this->view->appName = $sysConst->appName;
        $this->view->ownerFullName = $sysConst->ownerFullName;
        $user = $this->_request->getParam('username');
        $email = $this->_request->getParam('emailname');
        $message = '';
        $sCode = '';
        if ($this->getRequest()->isPost()) {
            $muser = new account_modAccount;
            $params = array('user' => $user, 'email' => $email);
            $rules = array(array('user,email', 'required', 'message' => ' * {attribute} không được bỏ trống'),
                array('email', 'email', 'message' => ' * {attribute} không hợp lệ')
            );
            $valid = $this->validateParam($params, $rules);
            if ($valid->isValid) {
                $result = $muser->forgotpassword($user, $email);
                if ($result == FALSE) {
                    $message = "Tên đăng nhập hoặc địa chỉ email không chính xác!";
                } else {
                    $sCode = 1;
                    if ($this->sendEmail($result))
                        $message = "Vui lòng check email để xác nhận thay đổi mật khẩu!";
                    else $message = "Lỗi gửi email <br> Vui lòng liên hệ quản trị hệ thống để cấp lại mật khẩu";
                }
            }
        }
        $this->view->user = $user;
        $this->view->sCode = $sCode;
        $this->view->message = $message;
    }

    public function confirmchangeAction()
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $this->view->appName = $sysConst->appName;
        $this->view->ownerFullName = $sysConst->ownerFullName;
        $user = $this->_request->getParam('username');
        $auth_code = $this->_request->getParam('auth_code', '');
        $password = $this->_request->getParam('password', '');
        $message = '';
        $muser = new account_modAccount;
        if ($this->getRequest()->isPost()) {
            $params = array('user' => $user, 'auth_code' => $auth_code, 'password' => $password);
            $rules = array(array('user,auth_code,password', 'required', 'message' => ' * {attribute} không được bỏ trống'));
            $valid = $this->validateParam($params, $rules);
            if ($valid->isValid) {
                $result = $muser->_querySql(array('user' => $user, 'auth_code' => $auth_code, 'password' => md5($password)), G_Global::getInstance()->dbUser . '.dbo.sp_UserPasswordUpdate', 0, 0);
                if ($result && $result['RESULT'] > 0) {
                    $message = "Thay đổi mật khẩu thành công!";
                    $sCode = 3;
                } else {
                    $message = "Lỗi!";
                }
            }
        } else {
            $params = array('user' => $user, 'auth_code' => $auth_code);
            $rules = array(array('user,auth_code', 'required', 'message' => ' * {attribute} không được bỏ trống'));
            $valid = $this->validateParam($params, $rules);
            if ($valid->isValid) {
                if ($muser->authcode($user, $auth_code) == FALSE) {
                    $sCode = 1;
                    $message = "Yêu cầu không hợp lệ!";
                } else {
                    $sCode = 2;
                }
            }
        }
        $this->view->username = $user;
        $this->view->sCode = $sCode;
        $this->view->message = $message;
    }

    private function validateParam($params, $rules)
    {
        $result = new stdClass();
        foreach ($rules as $key => $value) {
            $rule = (isset($value['1']) ? $value['1'] : '');
            $listKey = $value['0'];
            $arrKey = explode(',', $listKey);
            $message = (isset($value['message']) ? $value['message'] : '');
            switch ($rule) {
                case 'required':
                    $validator = new Zend_Validate_NotEmpty();
                    break;

                case 'email':
                    $validator = new Zend_Validate_EmailAddress();
                    break;
                case 'safe':
                default:
                    $validator = '';
                    break;
            }
            if ($validator != '') {
                foreach ($arrKey as $k => $v) {
                    if (!$validator->isValid($params[trim($v)])) {
                        $result->isValid = false;
                        $result->message = $message;
                        return $result;
                    }
                }
            }
        }
        $result->isValid = true;
        $result->message = '';
        return $result;
    }

    public function successAction()
    {

    }

    public function denyacessAction()
    {
        $this->setLayoutIndex();
        $this->view->error = 'Bạn không có quyền truy cập chức năng này';
    }

    public function  sendEmail($data)
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $config = array(
            'auth' => $sysConst->email_auth,
            'ssl' => $sysConst->email_ssl,
            'port' => $sysConst->email_port,
            'username' => $sysConst->email_username,
            'password' => $sysConst->email_password
        );
        $this->view->appName = $sysConst->appName;
        $this->view->ownerFullName = $sysConst->ownerFullName;

        $transport = new Zend_Mail_Transport_Smtp($sysConst->email_mailserver, $config);
        $mail = new Zend_Mail('UTF-8');
        $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        $mail->setFrom($sysConst->email_username, $sysConst->email_username);
        $mail->addTo($data['sEmail'], $data['sEmail']);
        $mail->setSubject('Thay đổi mật khẩu');
        $body = '<p><b>Kính chào quý khách: ' . $data['sName'] . '</b><br>';
        $body .= '<br>Đây là email thông báo từ hệ thống ' . $sysConst->ownerFullName . '<br>';
        $url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $url .= G_Global::getInstance()->sitePath . 'account/index/confirmchange?';
        $url .= http_build_query(array('username' => $data['sUserName'], 'auth_code' => $data['sAuth']));

        $body .= '<br>Hãy click vào <a href="' . $url . '" target="_blank"><strong>Đây</strong></a> hoặc link bên dưới để lấy lại mật khẩu của bạn<br>';
        $body .= '<br>&nbsp;&nbsp;Link: <b>' . $url . '</b> <br>';
        $mail->setBodyHtml($body);
        try {
            $sent = true;
            $mail->send($transport);
        } catch (Exception $e) {
            $sent = false;
        }
        return $sent;
    }

    public function registerAction() 
    {
        if ($this->_request->isXmlHttpRequest() == false) {
            $this->_redirect($this->_request->getHeader('referer'));
        }
        $resp = array('error' => true, 'msg' => '');
        if($this->_request->isPost()) {
            $data = $this->_request->getParams();
            $validate = $this->validateForm($data);
            if($validate['valid']){
                $model = new account_modAccount();
                $sPassword = base64_decode($data['login_passwordconfirm']);
                
                $sPassword = Zend_Crypt_Hmac::compute(G_Global::getInstance()->_key,'md5', $sPassword);
                $param = array(
                        'firstname' => $data['customer_firstname'],
                        'lastname' => $data['customer_lastname'],
                        'sAddress' => $data['customer_address'],
                        'sEmail' => $data['customer_email'],
                        'sMobile' => $data['customer_mobile'],
                        'iSex' => $data['customer_gender'],
                        'dBirthday' => $data['birthday_year'] . '-' . $data['birthday_month'] . '-' . $data['birthday_day'],
                        'sPassWord' => $sPassword,
                        'sIdCard' => $data['customer_idcard'],
                    );
                $resp = $model->register($param);
                if($resp['error'] == false) {
                    // redirect success
                    // echo 'Thành Công!'; die;
                    $resp['msg'] = 'Đăng ký thành công!';
                }             
            } else {
                $resp = array('error' => true, 'msg' => $validate['msg']);

            }
        } 
        
        die(json_encode($resp));
    }

    public function frmregisterAction() 
    {
		$resp = array('error' => '', 'msg' => '');
        if($this->_request->isPost()) {
            $data = $this->_request->getParams();
            $validate = $this->validateForm($data);
            if($validate['valid']){
                $model = new account_modAccount();
                $sPassword = base64_decode($data['login_passwordconfirm']);
                
                $sPassword = Zend_Crypt_Hmac::compute(G_Global::getInstance()->_key,'md5', $sPassword);
                $param = array(
                        'firstname' => $data['customer_firstname'],
                        'lastname' => $data['customer_lastname'],
                        'sAddress' => $data['customer_address'],
                        'sEmail' => $data['customer_email'],
                        'sMobile' => $data['customer_mobile'],
                        'iSex' => $data['customer_gender'],
                        'dBirthday' => $data['birthday_year'] . '-' . $data['birthday_month'] . '-' . $data['birthday_day'],
                        'sPassWord' => $sPassword,
                        'sIdCard' => $data['customer_idcard'],
                );
                $resp = $model->register($param);
                if($resp['error'] == false) {
                    // redirect success
                    // echo 'Thành Công!'; die;
                    $resp['msg'] = 'Đăng ký thành công!';
                }             
            } else {
                $resp = array('error' => true, 'msg' => $validate['msg']);

            }
			
        }
        
		$this->view->resp = $resp;
        $this->view->captchaUrl = $this->_request->getBaseUrl(). '/io/export';
        $this->view->captchId = $this->generateCaptcha();
    }

    private function validateForm($data) {
        $mysession = new Zend_Session_Namespace('authtoken');
        $hash = $mysession->authtoken;
        if($hash != $data['authtoken'])
            return array('valid' => false, 'msg' => 'Token không hợp lệ');

        if($this->validateCaptcha($data['captcha']) == false)
            return array('valid' => false, 'msg' => 'Mã xác nhận không chính xác');
        return array('valid' => true);
    }

    public function generatecaptchaAction()
    {
        $captchaId = $this->generateCaptcha();
        echo $captchaId;
        exit;
    }

    public function generateCaptcha() 
    {
        $captcha = new Zend_Captcha_Image();
        $captcha->setTimeout("800");
        $captcha->setWordLen("5");
        $captcha->setHeight("70");
        $captcha->setWidth("150");
        $captcha->setExpiration("800");
        $captcha->setGcFreq("10");
        $captcha->setFontSize("25");
        $captcha->setDotNoiseLevel(5);

        $captcha->setLineNoiseLevel(0);
        $captcha->setFont(G_Global::getInstance()->dirPublic.'css/font/arial.ttf');        
        $captcha->setImgDir(realpath('io/export/'));
        $captcha->generate();
        return $captcha->getId();
    }

    public function validateCaptcha($captcha) {
        $captchaId = trim($captcha["id"]);
        $captchaInput = trim($captcha["input"]);
        $captchaSession = new Zend_Session_Namespace("Zend_Form_Captcha_" . $captchaId);
        $captchaIterator = $captchaSession->getIterator();
        $captchaWord = $captchaIterator["word"];
        if($captchaWord) {
            if( $captchaInput != $captchaWord ){
                return false;
            }else{
                return true;
            }
        } else {
            return false;
        }
    }

    public function activeAction() {
        $this->view->headTitle()->setSeparator(' - ')->prepend('Kích hoạt tài khoản'); 
        $user = $this->_request->getParam('username');
        $auth_code = $this->_request->getParam('auth_code', '');
        $dbConnect = new account_modAccount;
        $params = array('user' => $user, 'auth_code' => $auth_code);
        $rules = array(
                    array('user,auth_code', 'required', 'message' => ' * {attribute} không được bỏ trống'),
                    array('user', 'email', 'message' => ' * {attribute} không hợp lệ')
                );
        $valid = $this->validateParam($params, $rules);
        if ($valid->isValid) {
            $arrResult = $dbConnect->_querySql($params, 'sp_AccountActiveCode', 0, 0);
            if ($arrResult && $arrResult['iRow']) {
                $url = $this->_request->getBaseUrl() . '/dang-nhap';
                $resp = array('error' => false, 'msg' => 'Kích hoạt thành công<br>Click vào <strong><a href="' . $url . '" >đây</a></strong> để đăng nhập');
            } else {
                $resp = array('error' => true, 'msg' => 'Yêu cầu không hợp lệ!');
            }
        } else {
            $resp = array('error' => true, 'msg' => $valid->message);
        }

        $this->view->resp = $resp;
    }
}

?>