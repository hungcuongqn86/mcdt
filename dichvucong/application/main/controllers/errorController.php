<?php

/**
 * Class Thong bao loi
 */
class main_errorController extends Zend_Controller_Action
{
    public function init()
    {
        //Load cau hinh thu muc trong file config.ini


        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        if (!$this->_request->isXmlHttpRequest()) {
            $objConfig = new G_Global();
            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();

            $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
            //Hien thi file template
            $response->insert('header', $this->view->renderLayout('header.phtml', './application/views/scripts/'));    //Hien thi header
            $response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));    //Hien thi header
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/views/scripts/'));     //Hien thi footer
            $sGetValueInCookie = G_Lib::_getCookie("showHideMenu");
        }
    }

    public function indexAction()
    {
        $this->view->error = 'Bạn không có quyền truy cập chức năng này';
        //$this->getHelper('viewRenderer')->setNoRender();
    }
}

?>