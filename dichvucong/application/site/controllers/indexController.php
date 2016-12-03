<?php

/**
 * @author: Truongdv
 * @see: 24/11/2015
 * @todo: Gui ho so qua mang
 */
class indexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        $objGen = new G_Gen();
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'site'
            ));
            $response = $this->getResponse();
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->appName);             
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->title); 
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();
            // $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'app/receive/record.js', ',', 'js');
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $myNamespace = new Zend_Session_Namespace('authtoken');
            $myNamespace->setExpirationSeconds(900);
            $myNamespace->authtoken = $hash = md5(uniqid(rand(),1));
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('fe_footer.phtml', './application/layout/scripts/'));
            $response->insert('sign_in', $this->view->renderLayout('login.phtml', './application/account/views/scripts/index/'));
            $response->insert('sign_up', $this->view->renderLayout('register.phtml', './application/account/views/scripts/index/'));
        } else {
            $result = array();
            $this->view->arrJsCss = Zend_Json::encode($result);
        }

    }

    public function indexAction()
    {


    }
}

?>