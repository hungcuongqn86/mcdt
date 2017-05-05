<?php

/**
 * @author: Truongdv
 * @see: 24/11/2015
 * @todo: Tra cuu ho so
 */
class searchController extends Zend_Controller_Action
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
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'ui/jquery-ui.js,ui/external/jquery.ui.datepicker-vi.js,process/paging.js,chosen/chosen.jquery.js,libXml.js,site/search.js', ',', 'js')
                .$objGen->_gCssJs('', 'js', 'ui/jquery-ui.css', ',', 'css')
                .$objGen->_gCssJs('', 'css', 'chosen/chosen.css', ',', 'css');

            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $this->view->slugclass ='tracuuhoso_kq';
            $response->insert('menu', $this->view->renderLayout('fe_menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('fe_footer.phtml', './application/layout/scripts/'));

        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'app/receive/classified.js', ',', 'js', $result);
            $this->view->arrJsCss = Zend_Json::encode($result);
        }

    }

    /**
     * @author: Truongdv
     * @see: 24/11/2015
     * @todo: Tim kiem hs
     */
    public function indexAction()
    {
        $this->view->headTitle()->setSeparator(' - ')->prepend('Tra cứu trạng thái hồ sơ'); 
        $customer_code = $this->_request->getParam('customer_code', '');
        if ($this->getRequest()->isPost() && $customer_code) {
            $dbconnect = new G_Db();
            $arrRecord = $dbconnect->_querySql(array('code' => $customer_code), 'dbo.sp_NetRecordSeach', 0, 0);
            if ($arrRecord) {
                $arrRecordWork = $dbconnect->_querySql(array('id' => $arrRecord['PK_RECORD'], 'owner' => ''), 'dbo.eCS_HandleWorkGetAll', 1, 0);
                $this->view->arrRecordWork = $arrRecordWork;
            } else {
                $this->view->summessage = 'Không tìm thấy hồ sơ nào có mã <strong>'. $customer_code . '</strong> vui lòng kiểm tra lại';
            }
            $this->view->arrRecord = $arrRecord;
        }
        $this->view->customer_code = $customer_code;

    }
    
}

?>