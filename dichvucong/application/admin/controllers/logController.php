<?php

/**
 * Nguoi tao: Đặng Văn Trường
 * Ngay tao: 30/01/2013
 * Y nghia: Quan ly LOG
 */
class admin_logController extends Zend_Controller_Action
{
    public function init()
    {
        $objGen = new G_Gen();
        //Lay duong dan thu muc goc (path directory root)
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
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
            $this->view->LoadAllFileJsCss = $objGen->_gCssJs('', 'js', 'admin/log.js', ',', 'js');
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        } else {
            $result = array();
            $result = $objGen->_gJsCssToArray('', 'js', 'admin/log.js', ',', 'js', $result);
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
        //Tao cac doi tuong
        $objCache = new G_Cache();
        $this->view->bodyTitle = 'LỊCH SỬ ĐĂNG NHẬP';
        $this->view->arrListItem = $objCache->getAllObjectbyListCode('DM_SO_HS_TREN_TRANG');
        $this->view->iCurrentPage = 1;
        $this->view->iNumberRecordPerPage = 15;
        echo G_Lib::getInstance()->doc_search_ajax($objCache->getAllStaff(), "id", "name", "fkStaff", "fkStaffhdn", 1, "position_code", 1, 2);

    }

    public function loadlistAction()
    {
        $dbConnect = new G_Db();
        $fromDate = $this->_request->getParam('fromDate', '');
        $toDate = $this->_request->getParam('toDate', '');
        if ($fromDate != '')
            $fromDate = G_Convert::_ddmmyyyyToYYyymmdd($fromDate);
        if ($toDate != '')
            $toDate = G_Convert::_ddmmyyyyToYYyymmdd($toDate);
        $params = array(
            'domain' => trim($this->_request->getParam('domain', '')),
            'ip' => trim($this->_request->getParam('ip', '')),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'currentPage' => $this->_request->getParam('hdn_current_page', '1'),
            'numberRecordPerPage' => $this->_request->getParam('hdn_record_number_page', '15')
        );
        $arrResult = $dbConnect->_querySql($params, 'sp_SysHistoryLoginGetAll', 1, 0);
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }

    public function deleteAction()
    {
        $listId = $this->_request->getParam('listId', '');
        $arrResult = array('RESULT' => 0);
        if ($listId) {
            $dbConnect = new G_Db();
            $arrResult = $dbConnect->_querySql(array('listId' => $listId), 'sp_SysHistoryLoginDelete', 1, 0);
        }
        echo Zend_Json::encode($arrResult);
        $this->getHelper('viewRenderer')->setNoRender();
    }
}

?>