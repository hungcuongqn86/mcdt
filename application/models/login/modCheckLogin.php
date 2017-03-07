<?php

/**
 * Class login_modCheckLogin
 */
class login_modCheckLogin extends Efy_DB_Connection {
    /**
     * @param $sUserName
     * @param $sPassWord
     * @return Mang
     */
	public function UserCheckLogin($sUserName,$sPassWord){		
		$sql = Extra_Init::_getUserDb() . ".dbo.USER_UserCheckLogin ";
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
