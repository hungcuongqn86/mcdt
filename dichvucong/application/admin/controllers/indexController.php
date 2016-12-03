<?php

/**
 * Nguoi tao: Truongdv
 * Ngay tao: 25/11/2015
 * Y nghia: Quan tri tai khoan
 */
class admin_indexController extends Zend_Controller_Action
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
       
    }
}

?>