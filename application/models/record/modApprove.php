<?php
class record_modApprove extends Extra_Db {
	
	/** Nguoi tao: PHUONGTT
		* Ngay tao: 17/11/2010
		* Y nghia: Update phe duyet ho so
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSApproveRecordUpdate($arrParameter){
		$psSql = "Exec eCS_ApproveRecordUpdate  ";	
		$psSql .= "'" . $arrParameter['C_RECORD_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECORD_TRANSITION_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_LEADER_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_LEADER_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_LEADER_ROLE'] . "'";
		$psSql .= ",'" . $arrParameter['C_HANDLE_ID'] . "'"; 
        $psSql .= ",'" . $arrParameter['C_HANDLE_NAME'] . "'";  
		$psSql .= ",'" . $arrParameter['C_CURRENT_STATUS'] . "'";
		$psSql .= ",'" . $arrParameter['C_DETAIL_STATUS'] . "'";			
		$psSql .= ",'" . $arrParameter['C_WORKER_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKER_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_APPROVE_CONTENT'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['NEW_FILE_ID_LIST'] . "'";
      
        
        
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
		* Ngay tao: 27/10/2010
		* Y nghia: Lay chi tiet mot TTHC
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSRecordTypeGetSingle($sRecordTypePk){
		$arrResult = null;
		$sql = "Exec eCS_RecordTypeGetSingle ";
		$sql .= "'" . $sRecordTypePk . "'";
		//echo $sql; exit;
		try{
			$arrResult = $this->adodbExecSqlString($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}

    /**
     * **************************************************************************************************************************************************************
     * **************************************************************************************************************************************************************
     * @param $sRecordIdList
     * @param $iHasDeleteAllPermission
     * @return null
     * Xu ly ho so phuong xa
     */
    public function eCSWardProcessUpdate($arrParameter){
        $arrTempResult = array();
        $psSql = "Exec [dbo].[eCS_WardProcessUpdate] ";
        $psSql .= "'" . $arrParameter['PK_RECORD_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_SUBMIT_ORDER_CONTENT'] . "'";
        $psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
        $psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
        $psSql .= ",'" . $arrParameter['C_ROLES'] . "'";
        $psSql .= ",'" . $arrParameter['C_STATUS'] . "'";
        $psSql .= ",'" . $arrParameter['C_DETAIL_STATUS'] . "'";
        $psSql .= ",'" . $arrParameter['NEW_FILE_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_USER_ID'] . "'";
        $psSql .= ",'" . $arrParameter['C_USER_NAME'] . "'";
        $psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
        //echo htmlspecialchars($psSql); exit;
        try {
            $arrTempResult = $this->adodbExecSqlString($psSql) ;
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrTempResult;
    }
}