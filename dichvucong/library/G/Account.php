<?php
/**
 * @see
 *
 */


/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 08/04/2015
 * Noi dung: Tao lop G_User xu ly thong tin User
 */
class G_Account extends  Zend_Auth
{
    public function __construct()
    {
        $this->_storage = new Zend_Auth_Storage_Session('PUBLIC_SERVICES');
    }

   /* public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    ->setStorage(new Zend_Auth_Storage_Session('PUBLIC_SERVICES'))

    public function hasIdentity() {
        return 
    }
    */
}