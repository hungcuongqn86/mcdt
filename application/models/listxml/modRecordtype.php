<?php
class listxml_modRecordtype extends Efy_DB_Connection {
    /**
     * @param $sOwnerCode
     * @param $sFullTextSearch
     * @param $sCate
     * @param $sStatus
     * @return Mang
     */
	public function eCSRecordTypeGetAll($sOwnerCode,$sFullTextSearch,$sCate,$sStatus){
		$sql = "eCS_RecordTypeGetAll ";
		$sql = $sql . " '" . $sOwnerCode . "'";
		$sql = $sql . ",'" . $sFullTextSearch . "'";
		$sql = $sql . ",'" . $sCate . "'";
        $sql = $sql . ",'" . $sStatus . "'";
		//$arrRecordType
		//echo  "<br>". $sql . "<br>";
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrResult;		
	}

    /**
     * @param $sOwnerCode
     * @return unknown
     */
	public function eCSRecordTypeMaxOrder($sOwnerCode){
		$sql = "eCS_RecordTypeMaxOrder ";
		$sql = $sql . " '" . $sOwnerCode . "'";	
		//echo  "<br>". $sql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrResult;		
	}

    /**
     * @param $arrParameter
     * @return unknown
     */
	public function eCSRecordTypeUpdate($arrParameter){
		$psSql = "Exec eCS_RecordTypeUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORDTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_CATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_ORDER'] . "'";
		$psSql .= ",'" . $arrParameter['C_STATUS'] . "'";			
		$psSql .= ",'" . $arrParameter['C_RECORD_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_PROCESS_NUMBER_DATE'] . "'";
        $psSql .= ",'" . $arrParameter['C_WARDS_PROCESS_NUMBER_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_RESULT_DOC_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_COST_NEW'] . "'";
		$psSql .= ",'" . $arrParameter['C_COST_CHANGE'] . "'";
		$psSql .= ",'" . $arrParameter['C_BEGIN_RECORD_NUMBER'] . "'";
		$psSql .= ",'" . $arrParameter['C_BEGIN_LICENSE_NUMBER'] . "'";
		$psSql .= ",'" . $arrParameter['C_IS_VIEW_ON_NET'] . "'";
		$psSql .= ",'" . $arrParameter['C_IS_REGISTER_ON_NET'] . "'";
		$psSql .= ",'" . $arrParameter['C_AUTO_RESET'] . "'";
		$psSql .= ",'" . $arrParameter['C_MOVE_TO_RESULT'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_TYPE_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		//echo $psSql; exit;
		//Thuc thi lenh SQL		
		try {	
			$arrResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;		
	}

	/*
	 *
	 */
    public function eCSRecordTypeWardConfigUpdate($arrParameter){
        $psSql = "Exec eCS_RecordTypeWardConfigUpdate  ";
        $psSql .= "'"  . $arrParameter['PK_RECORDTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['STAFF_ID'] . "'";
        $psSql .= ",'" . $arrParameter['C_WARDS_CODE_LIST'] . "'";
        //echo $psSql; exit;
        //Thuc thi lenh SQL
        try {
            $arrResult = $this->adodbExecSqlString($psSql) ;
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function eCSRecordTypeGetWardConfig($sRecordTypePk,$sStaffId){
        $arrResult = null;
        $sql = "Exec eCS_RecordTypeGetWardConfig ";
        $sql .= "'" . $sRecordTypePk . "'";
        $sql .= ",'" . $sStaffId . "'";
        //echo $sql; exit;
        try{
            $arrResult = $this->adodbExecSqlString($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrResult;
    }

    /**
     * @param $arrParameter
     * @return unknown
     */
    public function eCSRecordTypeConfigUpdate($arrParameter){
        $psSql = "Exec eCS_RecordTypeConfigUpdate  ";
        $psSql .= "'"  . $arrParameter['PK_RECORDTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_APPROVE_LEVEL'] . "'";
        $psSql .= ",'" . $arrParameter['C_DEPARTMENT_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_DEPARTMENT_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_RECEIVE_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_RECEIVE_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_HANDLE_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_HANDLE_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_TAX_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_TAX_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_TREASURY_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_TREASURY_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_LEADER_ID_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_LEADER_NAME_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_LEADER_ROLE_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
        //echo $psSql; exit;
        //Thuc thi lenh SQL
        try {
            $arrResult = $this->adodbExecSqlString($psSql) ;
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrResult;
    }

    /**
     * @param $sRecordTypePk
     * @return null|unknown
     */
	public function eCSRecordTypeGetSingle($sRecordTypePk,$sOwnerCode){
		$arrResult = null;
		$sql = "Exec eCS_RecordTypeGetSingle ";
		$sql .= "'" . $sRecordTypePk . "'";
        $sql .= ",'" . $sOwnerCode . "'";
		//echo $sql; exit;
		try{
			$arrResult = $this->adodbExecSqlString($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}

    /**
     * @param $sRecordTypeIdList
     * @return mixed
     */
	public function eCSRecordTypeDelete($sRecordTypeIdList){
		// Bien luu trang thai
		$sql = "Exec eCS_RecordTypeDelete '" . $sRecordTypeIdList . "'";	
		//echo $sql;exit;
		// thuc hien cap nhat du lieu vao csdl
		try {			
			$arrTempResult = $this->adodbExecSqlString($sql) ; 			
			$Result= $arrTempResult['RET_ERROR'];
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $Result;	
	}
}
?>