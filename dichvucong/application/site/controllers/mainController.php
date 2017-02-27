<?php

/**
 * @author: Truongdv
 * @see: 24/11/2015
 * @todo: Gui ho so qua mang
 */
class mainController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        $objGen = new G_Gen();
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'main'
            ));
            $response = $this->getResponse();
            $this->view->headTitle(Zend_Registry::get('__sysConst__')->title); 
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'site/record.js', ',', 'js');
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('fe_menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('fe_footer.phtml', './application/layout/scripts/'));

        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'site/record.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }

    }

    public function indexAction()
    {
        $dbconnect = new G_Db();
        $arrRecordType = $dbconnect->_querySql(array('iViewOnNet' => -1, 'iRegisterOnNet' => 1), 'dbo.eCS_RecordGetAllByNet', 1, 0);
        $this->view->arrRecordType = $arrRecordType;
        $this->view->iCurrentPage = 1;
        $this->view->iNumberRecordPerPage = 15;
    }

    /**
     * @author: Truongdv
     * @see: 27/11/2015
     * @todo: Gioi thieu
     */
    public function aboutAction()
    {
        $this->view->slugclass = 'gioithieu';
        $this->view->headTitle()->setSeparator(' - ')->prepend('Giới thiệu'); 

    }

    /**
     * @author: Truongdv
     * @see: 27/11/2015
     * @todo: Huong dan
     */
    public function guideAction()
    {
        $this->view->slugclass = 'huongdan';
        $this->view->headTitle()->setSeparator(' - ')->prepend('Hướng dẫn'); 

    }
}

?>