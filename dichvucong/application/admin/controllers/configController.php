<?php

class admin_configController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        $objGen = new G_Gen();
        if (!$this->_request->isXmlHttpRequest()) {

            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            //Load cac thanh phan cau vao trang layout (index.phtml)
            $response = $this->getResponse();
            //Lay cac hang so su dung trong JS public
            $this->view->JSPublicConst = G_Global::getInstance()->_setJavaScriptPublicVariable();
            $this->view->arrConst = G_Const::getInstance()->_setProjectPublicConst();
            // Load tat ca cac file Js va Css
//            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'system/config.js', ',', 'js');

            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'system/config.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }
    }

    /**
     * Khi thực hiện cần lưu thông tin :
     * Creater : ...
     * Date : ....
     * Idea :....
     */
    public function indexAction()
    {
        //Goi cac doi tuong
        $objCache = new G_Cache();
        $dbConnect = new G_Db();
        $delmiter = '!#$g4t$#!';
        $this->view->delmiter = $delmiter;
        $configs = array();
        if ($this->_request->isPost()) {
            $name_param = $this->_request->getParam('name_param');
            $value_param = $this->_request->getParam('value_param');
            $des_param = $this->_request->getParam('des_param');
            $id_param = $this->_request->getParam('id_param');
            $orders_param = $this->_request->getParam('orders_param');

            $name_param = implode($delmiter, $name_param);
            $value_param = implode($delmiter, $value_param);
            $des_param = implode($delmiter, $des_param);
            $id_param = implode($delmiter, $id_param);
            $orders_param = implode($delmiter, $orders_param);
            $arrParams = array(
                'name_param' => $name_param,
                'value_param' => $value_param,
                'des_param' => $des_param,
                'id_param' => $id_param,
                'orders_param' => $orders_param,
                'delmiter' => $delmiter
            );
//            var_dump($arrParams); die;
            $dbConnect->_querySql($arrParams, 'configUpdate', 0, 0);
            $hdndelList = $this->_request->getParam('hdndelList');
            $dbConnect->_querySql(array('hdndelList' => $hdndelList, 'delmiter' => $delmiter), 'configDelete', 0, 0);
            $objCache->update_system_config();
        }
        if ($configs == '' || empty($configs)) {
            $configs = $dbConnect->_querySql(array(), 'configGetAll', 1, 0);
            if (empty($configs))
                $configs = array(array('ConfigID' => '', 'Name' => '', 'Value' => '', 'Description' => '', 'Orders' => 1));
        }
        $this->view->configs = $configs;
    }

}

?>
