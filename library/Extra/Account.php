<?php

/**
 * Class Extra_Account
 */
class Extra_Account extends  Zend_Auth
{
    public function __construct()
    {
        $this->_storage = new Zend_Auth_Storage_Session('PUBLIC_SERVICES');
    }
}