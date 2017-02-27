<?php
/**
 * Class Xu ly thong thong tin loai danh muc
 */
class Logout_IndexController extends  Zend_Controller_Action {
	public function init(){

	}
	public function indexAction(){
		Zend_Loader::loadClass('Zend_Session');
		//Load class Efy_Init_Config
		Zend_Loader::loadClass('Efy_Init_Config');
		Zend_Session::start();
		Zend_Session::destroy();	
		$sReURL = Efy_Init_Config::_setUserLoginUrl();?>
		<script>
			window.location.href = '<?=$sReURL;?>';
		</script>
		<?php
        exit;
	}
}
?>