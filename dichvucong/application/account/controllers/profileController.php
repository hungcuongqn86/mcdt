<?php

class account_profileController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $baseUrl = $this->_request->getBaseUrl() . "/public/";
        Zend_Loader::loadClass('account_modAccount');
        $objGen = new G_Gen();
        if ($this->_request->isXmlHttpRequest()) {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'account/profile.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        } else {
            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'account/profile.js', ',', 'js');

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
        $model = new account_modAccount();
        $currentStaff = G_Account::getInstance()->getIdentity();
        $id = $currentStaff->ID;
        $model = $model->getIdentityUserByID($id);
        $this->view->model = $model;

    }

    public function saveinfoAction()
    {
        $model = new account_modAccount();
        $auth = G_Account::getInstance();
        $currentStaff = $auth->getIdentity();
        $id = $currentStaff->ID;
        $Birthday = date('Y/m/d', strtotime(str_replace('/', '-', $this->_request->getParam('Birthday'))));
        $params = array(
            "ID" => $id,
            "sFullName" => $this->_request->getParam('sFullName'),
            "sAddress" => $this->_request->getParam('Adress'),
            "sEmail" => $this->_request->getParam('Email'),
            "sMobile" => $this->_request->getParam('Mobile'),
            "iSex" => $this->_request->getParam('Sex'),
            "dBirthday" => $Birthday
        );

        $result = $model->UpdateUser($params);
        $resp = 0;
        if ($result) {
            $resp = 1;
            $storage = $auth->getStorage();
            $currentStaff->sFullName = $params['sFullName'];
            $currentStaff->sAddress = $params['sAddress'];
            $currentStaff->sEmail = $params['sEmail'];
            $currentStaff->sMobile = $params['sMobile'];
            $currentStaff->iSex = $params['iSex'];
            $currentStaff->dBirthday = $params['dBirthday'];
            $storage->write($currentStaff);
        }
        echo $resp;
        die;
    }

    public function changepassAction()
    {
        
    }

    public function savechangepassAction()
    {
        $model = new account_modAccount();
        $currentStaff = G_Account::getInstance()->getIdentity();
        $id = $currentStaff->ID;
        $passOld = base64_decode($this->_request->getParam('passOld'));
        $passNew = base64_decode($this->_request->getParam('passNew'));

        $key = G_Global::getInstance()->_key;
        $passOld = Zend_Crypt_Hmac::compute($key,'md5', $passOld);
        
        $passNew = Zend_Crypt_Hmac::compute($key,'md5', $passNew);
        $model->BeginTrans();
        $result = $model->ChangePassword($id, $passOld, $passNew);
        $rsp = 0;
        if ($result && $result['sPassword'] == $passNew) {
            $rsp = 1;
            $model->CommitTrans();
        } else {
            $model->RollbackTrans();
        }
        echo $rsp;
        die;
    }

    public function avatarAction()
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $urlupload = $sysConst->linkUser . '/avatar.php';
        $message = '';
        $hdn_action = $this->_request->getParam('hdn_action');
        if ($this->_request->isPost() && $hdn_action == 1) {
            $file_name = $this->_request->getParam('hdn-avatar', '');
            $hdn_avatar_old = $this->_request->getParam('hdn_avatar_old', '');
            $urlavatar = realpath('io/tempupload/') . '/' . $file_name;
            $rs = '';
            if (is_file($urlavatar)) {
                $rs = $this->_uploadAvatar($urlavatar, $urlupload);
                if ($rs == 200) {
                    $message = 'Cập nhật thành công!';
                    /*
                    * Cap nhat vao database
                    */
                }
            } else {
                $file_name = $hdn_avatar_old;
            }
        }
        $userIdentity = G_Account::getInstance()->getIdentity();
        $urlavatr = $sysConst->linkUser . '/avatar.php?id=' . $userIdentity->sAvatar;
        $this->view->urlavatr = $urlavatr;
        $this->view->message = $message;
    }

    public function cropAction()
    {
        $crop = new G_CropAvatar($_POST['avatar_src'], $_POST['avatar_data'], $_FILES['avatar_file']);
        $result = '';
        if ($crop->getResult()) {
            $result = $this->_request->getBaseUrl() . '/' . $crop->getResult();
        }

        $response = array(
            'state' => 200,
            'message' => $crop->getMsg(),
            'result' => $result
        );
        echo json_encode($response);
        die;
    }

    public function _uploadAvatar($file, $url)
    {
        ini_set('display_errors', 1);
        $result = '';
        $ch = curl_init($url);
        if (!$ch) {
            die("Không kết nối được đến FileServer!");
        } else {
            $auth = G_Account::getInstance()->getIdentity();
            if ($auth->sAvatar == '') {
                $filename = $auth->ID . '.png';
            } else {
                $filename = $auth->sAvatar;
            }
            $arrPostFields = array('action' => 'upload',
                'id' => $filename,
                'appcode' => 'mkntc'
            );
            $stagfile = 'FILENAME';
            $uploadfiletype = $this->mime_content_type($file);
            if (version_compare(phpversion(), '5.6.5', '<')) {
                $arrPostFields[$stagfile] = "@$file";
            } else {
                $arrPostFields[$stagfile] = new CURLFile($file, $uploadfiletype, $file);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrPostFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

            $postResult = curl_exec($ch);

            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
                $auth = G_Account::getInstance();
                $storage = $auth->getStorage();
                $rs = $auth->getIdentity();
                $rs->sAvatar = $filename;
                $storage->write($rs);
                $result = $postResult;
                $model = new account_modAccount();
                $model->updateAvatar($rs->PkStaff, $filename);
            }
            curl_close($ch);
        }
        return $result;
    }

    public function mime_content_type($filename)
    {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'pfx' => 'application/x-pkcs12',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

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
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        // $ext = strtolower(array_pop(explode('.', $filename)));
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

    public function getavatarAction() {
        ini_set("display_errors", "0");
        // error_reporting(E_ALL); 
        $userIdentity = G_Account::getInstance()->getIdentity();

        $diravatar = 'io/attach-file/avatar/' . $userIdentity->sAvatar;
        if (is_file($diravatar) == false) {
            $diravatar = 'io/attach-file/avatar/avatar_default.png';
        }
        $diravatar = 'io/attach-file/avatar/avatar_default.png';
        $data = file_get_contents($diravatar);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            ob_start();
            imagepng($im);
            printf('<img src="data:image/png;base64,%s"/>', 
                    base64_encode(ob_get_clean()));

            imagedestroy($im);

            // header('Content-Type: image/png');
            // imagepng($im);
            // imagedestroy($im);

        }
        else {
            echo 'An error occurred.';
        }

        die;


    }

}

?>