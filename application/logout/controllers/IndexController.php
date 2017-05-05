<?php

/**
 * Class Logout_IndexController
 */
class Logout_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        Zend_Session::start();
        Zend_Session::destroy();
        $sReURL = Extra_Init::_setUserLoginUrl(); ?>
        <script>
            window.location.href = '<?=$sReURL;?>';
        </script>
        <?php
        exit;
    }
}

?>