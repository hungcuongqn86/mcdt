<?php
/**

/**
* Nguoi tao: 	
* Ngay tao: 20/11/2009
* Y nghia:Lay thong tin nhac viec
*/

class Record_modReminder extends Extra_Db {
	
	/** Nguoi tao: NGHIAT
	* Ngay tao: 05/08/2010
	* Y nghia: Lay ra danh sach nhac viec
	*/
	public function docReminderGetAll($iUserId,$iDepartmentId,$iOwnerId,$sPermissionList,$sRoleLeader,$iPosition){
		$sql = "Doc_DocReminderGetAll ";
		$sql = $sql . " '" . $iUserId . "'";
		$sql = $sql . ",'" . $iDepartmentId . "'";		
		$sql = $sql . ",'" . $iOwnerId . "'";	
		$sql = $sql . ",'" . $sPermissionList . "'";	
		$sql = $sql . ",'" . $sRoleLeader . "'";	
		$sql = $sql . ",'" . $iPosition . "'";	
		//echo  "<br>". $sql . "<br>"; 
		//exit;
		try{
			$arrReminder = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrReminder;		
	}
	public function eCSPROMPTTHE($iStaffID,$iUnitID,$sOwnerCode){
		$sql = "eCS_GetAllReminder ";
		$sql = $sql . " '" . $iStaffID . "'";
		$sql = $sql . ",'" . $iUnitID . "'";		
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