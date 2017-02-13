<?php
/**
 * @copyright :efy.com.vn - 2009 
 * Model thuc hien viec tiep nhan ho so qua mang
 * @see : Lop modReportStaff 
 * */
/**
 * @author Tientk
 * 
 *
 */
class record_modReceiveonnet extends Efy_DB_Connection {
    /**
     * @param $sRecordTypeID
     * @param $sUnit
     * @param $sFullTextSearch
     * @param $sRecordStatus
     * @param $iPage
     * @param $iNumberRecordPerPage
     * @return Mang
     */
	public function eCSNetReceiveRecordGetAll($sRecordTypeID,$sUnit,$sFullTextSearch,$sRecordStatus,$iPage,$iNumberRecordPerPage){		
		$objConn = new  Efy_DB_Connection(); 
		$sql = "Exec eCS_NetReceiveRecordGetAll ";
		$sql = $sql . "'" .  $sRecordTypeID . "'";
		$sql = $sql . ",'" . $sUnit . "'";
		$sql = $sql . ",'" . $sFullTextSearch . "'";
		$sql = $sql . ",'" . $sRecordStatus . "'";
		$sql = $sql . ",'" . $iPage . "'";
		$sql = $sql . ",'" . $iNumberRecordPerPage . "'";
			
		//echo $sql . '<br>';//exit;
		try{
			//Thuc thi lenh sql va tra ra mot mang 1 chieu
			$arrRecord = $objConn->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}		
		return $arrRecord;
	}

    /**
     * @param $sRecordTypeID
     * @param $sUnit
     * @param $sFullTextSearch
     * @param $sRecordStatus
     * @param $iPage
     * @param $iNumberRecordPerPage
     * @return Mang
     */
    public function eCSNetOrderGetAll($sRecordTypeID,$sUnit,$sFullTextSearch,$sRecordStatus,$iPage,$iNumberRecordPerPage){
        $objConn = new  Efy_DB_Connection();
        $sql = "Exec eCS_NetOrderGetAll ";
        $sql = $sql . "'" .  $sRecordTypeID . "'";
        $sql = $sql . ",'" . $sUnit . "'";
        $sql = $sql . ",'" . $sFullTextSearch . "'";
        $sql = $sql . ",'" . $sRecordStatus . "'";
        $sql = $sql . ",'" . $iPage . "'";
        $sql = $sql . ",'" . $iNumberRecordPerPage . "'";

        //echo $sql . '<br>';//exit;
        try{
            //Thuc thi lenh sql va tra ra mot mang 1 chieu
            $arrRecord = $objConn->adodbQueryDataInNameMode($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        return $arrRecord;
    }

    /**
     * @param $sRecordTypeID
     * @param $sUnit
     * @param $sFullTextSearch
     * @param $sRecordStatus
     * @param $iPage
     * @param $iNumberRecordPerPage
     * @return Mang
     */
	public function eCSNetOfficialRecordGetAll($sRecordTypeID,$sUnit,$sFullTextSearch,$sRecordStatus,$iPage,$iNumberRecordPerPage){		
		$objConn = new  Efy_DB_Connection(); 
		$sql = "Exec eCS_NetOfficialRecordGetAll ";
		$sql = $sql . "'" .  $sRecordTypeID . "'";
		$sql = $sql . ",'" . $sUnit . "'";
		$sql = $sql . ",'" . $sFullTextSearch . "'";
		$sql = $sql . ",'" . $sRecordStatus . "'";
		$sql = $sql . ",'" . $iPage . "'";
		$sql = $sql . ",'" . $iNumberRecordPerPage . "'";		
		//echo $sql . '<br>';//exit;
		try{
			//Thuc thi lenh sql va tra ra mot mang 1 chieu
			$arrResult = $objConn->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}		
		return $arrResult;
	}
	

	public function eCSNetReceiveRecordDelete($sNetReceiveRecordIDList){
		$Result = null;			
		$sql = "Exec eCS_NetReceiveRecordDelete ";		
		$sql .= "'".$sNetReceiveRecordIDList ."'";	
		//$sql .= ",'".$iHasDeleteAllPermission ."'";
		//echo $sql . '<br>'; exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($sql) ; 			
			$Result= $arrTempResult['RET_ERROR'];
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $Result;	
	}
/**
 	* Nguoi tao: Tientk
	* Ngay tao: 18/05/2011
	* Y nghia: Lay chi tiet mot ho so
	* adodbExecSqlString: lay mang 1 chieu
	* adodbQueryDataInNameMode: lay mang da chieu
 	* @param unknown_type $sRecordTypeID
*/
	public function eCSNetReceiveRecordGetSingle($sRecordTypeID){
		$arrResult = null;
		$sql = "Exec eCS_NetReceiveRecordGetSingle ";
		$sql .= "'" . $sRecordTypeID . "'";
		//echo $sql; exit;
		try{
			//Thuc thi lenh sql va tra ra mot mang 2 chieu
			$arrResult = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/**
	 * Ham lay danh sach ho so tiep nhan chinh thuc
	 * @param unknown_type $sRecordTypeId -- ma ho so
	 * @param unknown_type $sOwnerCode	-- Ma don vi su dung
	 * @param unknown_type $sfulltextsearch -- Tu, cum tu tim kiem
	 * @param unknown_type $iPage -- Trang hien thoi
	 * @param unknown_type $iNumberRecordPerPage -- So ho so tren trang
	 *
	public function eCSOfficialRecordGetAll($sRecordTypeId,$sOwnerCode, $sfulltextsearch, $iPage, $iNumberRecordPerPage){		
		$objConn = new  Efy_DB_Connection(); 
		$sql = "Exec eCS_RecordTransitionGetAll ";
		$sql = $sql . "'" . $sRecordTypeId . "'";
		$sql = $sql . ",'" . $sOwnerCode . "'";
		$sql = $sql . ",'" . $sfulltextsearch . "'";
		$sql = $sql . ",'" . $iPage . "'";
		$sql = $sql . ",'" . $iNumberRecordPerPage . "'";	
		echo $sql . '<br>';// exit;
		try{
			$arrResul = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}		
		return $arrResul;
	}*/
	/**
	 * Ngươi tao: Tientk
	 * Ngay tao: 20/05/2011
	 * Ham cap nhat thong tin mot ho so
	 * @param array $arrParameter: Mang du lieu luu thong tin ho so
	 */
	public function eCSNetReceiveRecordUpdate($arrParameter){
		$psSql = "Exec [dbo].[eCS_NetReceiveRecordUpdate] ";	
		$psSql .= "'" . $arrParameter['PK_NET_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['C_PRELIMINARY_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_ORIGINAL_APPLICATION_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECEIVING_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_STATUS'] . "'";
		$psSql .= ",'" . $arrParameter['C_MESSAGE'] . "'";
		$psSql .= ",'" . $arrParameter['FK_RECEIVER'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECEIVER_POSITION_NAME'] . "'";
		//echo htmlspecialchars($psSql); exit;
		try {
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;
	}
	/**
	 * Ngươi tao: Tientk
	 * Ngay tao: 01/06/2011
	 * Ham cap nhat thong tin ho so sang bang T_eCS_Record_Work
	 * @param array $arrParameter: Mang du lieu luu thong tin ho so
	 */
	public function eCSRecordWorkSystemUpdate($arrParameter){
		$psSql = "Exec [dbo].[eCS_RecordWorkSystemUpdate] ";	
		$psSql .= "'" . $arrParameter['PK_RECORD_WORK'] . "'";
		$psSql .= ",'" . $arrParameter['FK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
		$psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_RESULT'] . "'";
		$psSql .= ",'" . $arrParameter['C_SYSTEM_WORKTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['NewAttachFileNameList'] . "'";
		//echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;		
	}
	/**
	 * Ngươi tao: Tientk
	 * Ngay tao: 30/05/2011
	 * Ham lay thong tin user cua ca nhan to chuc dang ky qua mang
	 * @param $sUserId: id cua ca nhan to chuc 
	 *
	 */
	Public function eCSNetUserGetSingle($sUserId){
        $psSql = "Exec [dbo].[sp_AccountGetSingle] ";
        $psSql .= "'" .$sUserId . "'";
//        echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;	
	}
/**
	 * Ngươi tao: Tientk
	 * Ngay tao: 30/05/2011
	 * Ham lay thong tin email cua phong tiep nhan ho so
	 * @param $sStaffId: id cua nhan vien tiep nhan ho so
	 *
	 */
	Public function eCS_NetUnitEmailGetSingle($sStaffId){
	$psSql = "Exec [dbo].[eCS_NetUnitEmailGetSingle] ";	
	$psSql .= "'" .$sStaffId . "'";
	//echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;	
	}
}
?>
