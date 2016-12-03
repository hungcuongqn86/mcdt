<?php
/**
 * @copyright :efy.com.vn - 11/2010 
 * @see : Nguoi tao: NGHIAT
 * */
class login_modCheckLogin extends Efy_DB_Connection {		

	/**
	 * Creater : Tientk
	 * Date : 14/06/2011
	 * Idea : Tao phuong thuc Kiem tra, xac thuc NSD khi dang nhap
	 * @param $sUserName				: Ten dang nhap
	 * @param $sPassWord				: Mat khau
	 */
	public function UserCheckLogin($sUserName,$sPassWord){		
		$sql = Efy_Init_Config::_setDbLinkUser() . ".dbo.USER_UserCheckLogin ";
		$sql = $sql . "'" . $sUserName . "'";
		$sql = $sql . ",'" . $sPassWord . "'";
		// echo '<br>'.$sql . '<br>';exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}

		if ($arrResult) {
			$auth = G_User::getInstance();
			$storage = $auth->getStorage();
			$storage->write(json_decode(json_encode($arrResult[0]), FALSE));
		}
		return $arrResult;
	}
}
?>
