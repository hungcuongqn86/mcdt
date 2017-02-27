<?php
/**
 * Class Xu ly thong thong tin loai danh muc
 */
class Login_IndexController extends  Zend_Controller_Action {
	public function init(){
		//Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		Zend_Loader::loadClass('login_modCheckLogin');		
		//Cau hinh cho Zend_layoutasdfsdfsd
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout'],
			    'layout' => 'login'
			    ));	
		// Load tat ca cac file Js va Css
		$this->view->LoadAllFileJsCss = Efy_Publib_Library::_getAllFileJavaScriptCss('','efy-js','position.js,jquery-1.5.1.js,ajax.js',',','js');
		$this->view->showModelDialog = 1;//An menu
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
	}	
	/**
	 * Creater : HUNGVM
	 * Date : 27/09/2009
	 * Idea : Tao phuong thuc hien xu ly logout khoi he thong
	 */
	public function indexAction(){
		//Zend_Loader::loadClass('Zend_Session');
		//Zend_Session::destroy();
		$sReURL = Efy_Init_Config::_setUserLoginUrl();
		$objInitConfig 		= new Efy_Init_Config();
		$objmodLogin		= new login_modCheckLogin();
		//
		//$this->_helper->layout()->disableLayout();
		$this->view->bodyTitle = 'TH&#212;NG TIN &#272;&#258;NG NH&#7852;P';
		$arrConst =$objInitConfig->_setProjectPublicConst();
		$this->view->arrConst= $arrConst;//C-> V(arrConst)
		$hdhOption = $this->_request->getParam('hdn_option');
		$this->view->hdn_option= $hdhOption;
		

		$sCheckReme = Efy_Library::_getCookie("sCheckReme");
		if($sCheckReme){
			$sUserName = Efy_Library::_getCookie("sUserName");
			$sPassWord = Efy_Library::_getCookie("sPassWord");
		}else{
			$sCheckReme = 0;
		}
		$sUserNameTemp =$this->_request->getParam('txt_usename');
		if($sUserNameTemp!=''){
			$sUserName = $sUserNameTemp;
			$sPassWord = '';
		}
		$this->view->txt_password= $sPassWord;
		$this->view->txt_usename= $sUserName;
		$this->view->sCheckReme= $sCheckReme;



		
		$sUserName =$this->_request->getParam('txt_usename');//Lay tham so tu V truyen den C
		$this->view->txt_usename= $sUserName;
		$sPassWord =$this->_request->getParam('txt_password');
		$this->view->txt_password= $sPassWord;
		$sUrlre= $this->_request->getParam('urlre');
		$this->view->urlRe =$this->baseUrl . "/login/";
		if($hdhOption =="1"){
			$arrStaff =$objmodLogin->UserCheckLogin($sUserName,md5($sPassWord));//kt username va pass NSD neu dung tra ra mot ban ghi chua tt NSD
			if (sizeof($arrStaff)>0){
			//luu thong tin nguoi dang nhap vao session
				@session_start();
				$_SESSION['INFORMATION_STAFF_LOGIN'] = efy_library::_getInformationStaffLogin($arrStaff[0]['C_NAME'],$arrStaff[0]['C_POSITION_CODE'],$arrStaff[0]['C_UNIT_NAME']);
				//$_SESSION['staff_id'] = //$arrStaff[0]['PK_STAFF'];//str_replace('{','',str_replace('}', '',$arrStaff[0]['PK_STAFF'])); //Luu ID can bo dang nhap vao Session
				$_SESSION['staff_id'] = str_replace('{','',str_replace('}', '',$arrStaff[0]['PK_STAFF']));
				$_SESSION['OWNER_CODE'] = $arrStaff[0]['C_UNIT_OWNER_CODE'];//luu don vi trien khai		
				$_SESSION['STAFF_PERMISSTION'] = $arrStaff[0]['C_ROLE'];		
				//Lay thong tin phong ban
				if(!isset($_SESSION['arr_all_staff']) || is_null($_SESSION['arr_all_staff'])){
					//Luu tru thong tin phong ban cua toan bo cac don vi trien khai
					$_SESSION['arr_all_unit_keep'] = Efy_Init_Session::SesGetDetailInfoOfAllUnit();	
					//Luu co cau to chuc cua can bo hien tai
					$_SESSION['arr_all_unit'] = Efy_Init_Session::_getAllUnitsByCurrentStaff($_SESSION['OWNER_CODE']);	
				}
				
				//Lay thong tin can bo
				if(!isset($_SESSION['arr_all_staff']) || is_null($_SESSION['arr_all_staff'])){
					//Luu thong tin can bo cua tat ca don vi trien khai
					$_SESSION['arr_all_staff_keep'] = Efy_Init_Session::SesGetPersonalInfoOfAllStaff();	
					//Luu thong tin can bo thuoc don vi NSD hien thoi
					$_SESSION['arr_all_staff'] = Efy_Init_Session::_getAllUsersByCurrentOrg($_SESSION['OWNER_CODE']);	
				}
				//echo 'mang'.$_SESSION['arr_all_staff']['staff_id']. '   id'.$_SESSION['staff_id'];	 exit;
				//Lay quyen cua NSD
				if(!isset($_SESSION['arrStaffPermission']) || is_null($_SESSION['arrStaffPermission'])){
					$_SESSION['arrStaffPermission'] = Efy_Init_Session::StaffPermisionGetAll($_SESSION['staff_id']);	
				}				

				//Lay thong tin don vi trien khai
				if(!isset($_SESSION['SesGetAllOwner']) || is_null($_SESSION['SesGetAllOwner'])){		
					$_SESSION['SesGetAllOwner'] = Efy_Init_Session::SesGetAllOwner();		
				}
				//var_dump($_SESSION['staff_id']);exit;
				//Thanh cong thi thuc hien URL mac dinh duoc cau hinh trong file Config
				$this->_redirect(Efy_Init_Config::_setDefaultUrl());
			}else{?>
				<script>
					alert('Ten dang nhap hoac mat khau khong dung!');
				</script><?php
			}	
		}
	}
}
?>