<?php

class account_modAccount extends G_Db
{
    

    public function check_login($sUserName, $sPassWord)
    {

        $sql = "dbo.sp_AccountAuthentication ";
        $sql .= $this->qstr($sUserName);
        $sql .= "," . $this->qstr(Zend_Crypt_Hmac::compute(G_Global::getInstance()->_key,'md5', $sPassWord));
        $result = array();
        try {
            $result = $this->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $auth = G_Account::getInstance();
        if ($result) {
            $result = self::__map($result);
            if ($result['sStatus'] == 1) {
                $storage = $auth->getStorage();
                $result = json_decode(json_encode($result), FALSE);
                $result->sOwnerCode = Zend_Registry::get('__sysConst__')->ownerCode;
                $result->sOwnerCode = 'HLT';
                $storage->write($result);
                $res = array('logined' => TRUE, 'msg' => 'Đăng nhập thành công');
            } else {
                $res = array('logined' => FALSE, 'msg' => 'Tài khoản của bạn không HOẠT ĐỘNG, bạn có thể liên hệ QUẢN TRỊ HỆ THỐNG để kích hoạt lại');
            }
        } else {
            $res = array('logined' => FALSE, 'msg' => 'Tên đăng nhập hoặc mật khẩu không đúng');
        }
        return $res;
    }
 
    public static function __map($data) {
        if ($data) {
            $arrKey = self::dataKey();
            if (is_array($data[0])) {
                for ($i=0; $i < sizeof($data); $i++) { 
                    $data[$i] = self::convert($data[$i], $arrKey);
                }
            } else {
                $data = self::convert($data, $arrKey);
            }
            
        }

        return $data;

    }

    private static function convert($data, $arrKey) {
        $arrOutput = array();
        foreach ($data as $key => $value) {
            if (isset($arrKey[$key])) {
                $arrOutput[$arrKey[$key]] = $value;
            } else {
                $arrOutput[$key] = $value;
            }
        }
        return $arrOutput;
    }
    public static function dataKey() {
        return array(
                'PK_NET_ID' => 'ID',
                'C_FULLNAME' => 'sFullName',
                'C_FIRSTNAME' => 'sFirstName',
                'C_LASTNAME' => 'sLastName',
                'C_PASSWORD' => 'sPassword',
                'C_EMAIL' => 'sEmail',
                'C_ID_CARD' => 'sIdCard',
                'C_MOBILE' => 'sMobile',
                'C_ROLES' => 'sRole',
                'C_STATUS' => 'sStatus',
                'C_AUTH'  =>'sAuth',
                'C_ADDRESS' => 'sAddress',
                'C_SEX' => 'iSex',
                'C_BIRTHDAY'  =>'dBirthday',
                'C_CREATED_DATE' => 'dRegisterDate'
            );
    }
    public function logout()
    {
        Zend_Session::destroy();
    }

    public function getIdentityUserByID($id)
    {
        $psSql = "dbo.sp_AccountGetSingle ";
        $psSql .= $this->qstr($id);
        try {
            $arrResult = $this->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return self::__map($arrResult);
    }

    public function UpdateUser($params)
    {
        $psSql = "dbo.sp_AccountChangeProfile  ";
        $psSql .= $this->qstr($params['ID']);
        $psSql .= "," . $this->qstr($params['sFullName']);
        $psSql .= "," . $this->qstr($params['sAddress']);
        $psSql .= "," . $this->qstr($params['sEmail']);
        $psSql .= "," . $this->qstr($params['sMobile']);
        $psSql .= "," . $this->qstr($params['iSex']);
        $psSql .= "," . $this->qstr($params['dBirthday']);
        //echo $psSql; exit;
        try {
            $arrResult = $this->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function ChangePassword($id, $oldPass, $newPass)
    {
        $sql = "dbo.sp_AccountChangePassword ";
        $sql .= $this->qstr($id);
        $sql .= "," . $this->qstr($oldPass);
        $sql .= "," . $this->qstr($newPass);
        //echo $sql . '<br>';// exit;
        try {
            $arrResul = $this->adodbExecSqlString($sql);
            $arrResul = self::__map($arrResul);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResul;
    }

    public function updateAvatar($id, $avatar)
    {
        $sql = "Update " . ".dbo.UserStaff
                SET sAvatar = " . $this->qstr($avatar) . "
                WHERE PkStaff = " . $this->qstr($id);
        try {
            $arrResul = $this->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResul;
    }


    public function forgotpassword($user, $email)
    {
        if ($user != "" && $email != "") {            
            $result = $this->_querySql(array('user' => $user, 'email' => $email), 'dbo.sp_AccountForgotPassword', false, 0);
            if ($result && $result['RESULT'] > 0) {
                return $result;                
                return $this->sendEmail($result);
            } else return FALSE;
        } else {
            return FALSE;
        }
    }

    public function authcode($user, $auth_code)
    {
        if ($user != '' && $auth_code != '') {
            $sql = "dbo.sp_AccountAuthCode ";
            $sql .= $this->qstr($user);
            $sql .= "," . $this->qstr($auth_code);
            $result = array();
            try {
                $result = $this->adodbExecSqlString($sql);
                $result = self::__map($result);
            } catch (Exception $e) {
                echo $e->getMessage();
            };
            if ($result && $result['sAuth']) {                
                return TRUE;
            } else return FALSE;
        } else {
            return FALSE;
        }
    }

    public function register($param)
    {
        $result = $this->_querySql($param, 'dbo.sp_AccountRegister', false, 0);
        if ($result) {
            if ($result['msg']) {
                return $resp = array('error' => true, 'msg' => $result['msg']);
            }
            $resp = array('error' => false, 'id' => $result['NEW_ID']);
        } else {
            $resp = array('error' => true, 'msg' => 'Lỗi cập nhật CSDL');
        }
        return $resp;
    }

    public function getAll($param) {
        $result = $this->_querySql($param, 'dbo.sp_AccountGetAll', 1, 0);
        $result = self::__map($result);
        return $result;
    }

    public function approve($param)
    {
        $result = $this->_querySql($param, 'dbo.sp_AccountApprove', 0, 0);
        // echo $result; die;
        if ($result && $result['iRow']) {
            $resp = array('error' => false, 'id' => $result['NEW_ID'], 'sAuth' => $result['sAuth']);
        } else {
            $resp = array('error' => true, 'msg' => 'Lỗi cập nhật CSDL');
        }
        return $resp;
    }

    public function save($param)
    {
        $result = $this->_querySql($param, 'dbo.sp_AccountUpdate', 0, 0);
        // echo $result; die;
        if ($result && $result['iRow']) {
            $resp = array('error' => false, 'id' => $result['NEW_ID']);
        } else {
            $resp = array('error' => true, 'msg' => 'Lỗi cập nhật CSDL');
        }
        return $resp;
    }
    
}

?>

