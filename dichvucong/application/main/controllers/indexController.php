<?php

/**
 * Nguoi tao: Truongdv
 * Ngay tao: 24/08/2015
 * Y nghia: 
 */
class main_indexController extends Zend_Controller_Action
{
    public function init()
    {
        
    }
    public function setLayoutIndex() {
        if (!$this->_request->isXmlHttpRequest()) {
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            $response = $this->getResponse();
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            //Hien thi file template
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        }
    }
    public function retrievetimeleftAction()
    {
        if (G_Account::getInstance()->hasIdentity()) {
            $time = Zend_Registry::get('__sysConst__')->sessionTimeOut - (time() - $_SESSION['lastAccess']); // giay
            die(json_encode($time));
        } else {
            die(json_encode(0));
        }
    }

    public function keepaliveAction()
    {
        $_SESSION['lastAccess'] = time();
        die(json_encode(Zend_Registry::get('__sysConst__')->sessionTimeOut)); // giay
    }
	
	public function xmldatalistAction()
	{
		$filexml = $this->_request->getParam('f', '');
		$filexml = realpath('public/xml') . '/' . $filexml;
        $xml = simplexml_load_file($filexml);
        if (isset($xml->list_of_object->list_body)) {
            $body = $xml->list_of_object->list_body->asXML();
        } else {
            $body = $xml->asXML();
        }
		$response = $this->getResponse();
		$response->setHeader('Content-Type', 'text/xml')
					->appendBody($body);
		$this->_helper->viewRenderer->setNoRender(true);
		return $response;
	}
}