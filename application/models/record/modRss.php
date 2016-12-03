<?php
/**
 * @copyright :efy.com.vn - 11/2010 
 * @see : Nguoi tao: NGHIAT
 * */
class record_modRss extends Efy_DB_Connection {	
	public function eCSPROMPTTHE($iStaffID,$sOwnerCode){
		$sql = "eCS_PROMPT_THE ";
		$sql = $sql . " '" . $iStaffID . "'";
		$sql = $sql . ",'" . $sOwnerCode . "'";		
		//echo  "<br>". $sql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrResult;		
	}
}	
?>
