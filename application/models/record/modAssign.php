<?php
class record_modAssign extends Extra_Db {
	
	/** Nguoi tao: NGHIAT
		* Ngay tao: 04/11/2010
		* Y nghia: Update Phan cong xu ly
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSAssignHandleUpdate($arrParameter){
		$psSql = "Exec eCS_AssignHandleUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['C_ASSIGNED_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_ASSIGNED_IDEA'] . "'";
		$psSql .= ",'" . $arrParameter['C_MAIN_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_MAIN_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_SUPPORT_ID'] . "'";			
		$psSql .= ",'" . $arrParameter['C_SUPPORT_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_APPOINTED_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['FK_UNIT'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['FK_LEADER'] . "'";
		$psSql .= ",'" . $arrParameter['C_LEADER_NAME'] . "'";
		//Thuc thi lenh SQL		
		//echo $psSql; exit;
		try {			
			$arrResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;		
	}	
	/** Nguoi tao: NGHIAT
		* Ngay tao: 04/11/2010
		* Y nghia: lay danh sach can bo va thong tin lien quan den phan cong thu ly
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSAssignHandleGetAll($sRecordPk,$iFkUnit){
		$psSql = "Exec eCS_AssignHandleGetAll  ";
		$psSql .= "'"  . $sRecordPk . "'";
		$psSql .= ",'"  . $iFkUnit . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
} 	