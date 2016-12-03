<?php
class G_User extends  Zend_Auth
{
    public function __construct()
    {
        $this->_storage = new Zend_Auth_Storage_Session('G_MCDT');
    }
}
?>