<?php
/**
 * Class Xu ly thong thong tin loai danh muc
 */
class Logout_IndexController extends  Zend_Controller_Action {
	public function init(){

	}
	public function indexAction(){
		Zend_Loader::loadClass('Zend_Session');
		//Load class Extra_Init
		Zend_Loader::loadClass('Extra_Init');
		Zend_Session::start();
		Zend_Session::destroy();	
		$sReURL = Extra_Init::_setUserLoginUrl();?>
		<script>
			window.location.href = '<?=$sReURL;?>';
		</script>
		<?php
        exit;
	}
}
?>