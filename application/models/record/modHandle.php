<?php
/**
 * @copyright :efy.com.vn - 2009 
 * @see : Lop modReportStaff 
 * */
class record_modHandle extends Extra_Db {
	/** Nguoi tao: NGHIAT
		* Ngay tao: 09/11/2010
		* Y nghia: lay danh sach cac cong viec lien quan den tien do cua mot ho so
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleWorkGetAll($sRecordPk,$sOwnerCode){
		$psSql = "Exec eCS_HandleWorkGetAll ";
		$psSql .= "'"  . $sRecordPk . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		//echo  "<br>eCSHandleWorkGetAll:". $psSql . "<br>"; exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	public function eCSHandleWorkGetAll2($sRecordPk,$sOwnerCode){
		$psSql = "Exec eCS_HandleWorkGetAll2 ";
		$psSql .= "'"  . $sRecordPk . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		//echo  "<br>eCSHandleWorkGetAll:". $psSql . "<br>"; exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/** Nguoi tao: NGHIAT
		* Ngay tao: 09/11/2010
		* Y nghia: Update Cap nhat cong viec tien do
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleWorkUpdate($arrParameter){
		$psSql = "Exec eCS_HandleWorkUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['PK_RECORD_WORK'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_RESULT'] . "'";			
		$psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
		$psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_DOC_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_FILE'] . "'";
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
		* Ngay tao: 09/11/2010
		* Y nghia: lay thong tin lien quan den 1 cong viec da duoc tao lap
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleWorkGetSingle($sRecordWorkPk){
		$psSql = "Exec eCS_HandleWorkGetSingle ";
		$psSql .= "'"  . $sRecordWorkPk . "'";
		//echo  "<br>". $sql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/** Nguoi tao: NGHIAT
		* Ngay tao: 09/11/2010
		* Y nghia: Xoa cac cong viec lien quan den tien do do NSD dang nhap hien thoi dang nhap
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleWorkDelete($sRecordWorkIdList){
		// Bien luu trang thai
		$sql = "Exec eCS_HandleWorkDelete '" . $sRecordWorkIdList . "'";	
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
	/** Nguoi tao: NGHIAT
		* Ngay tao: 12/11/2010
		* Y nghia: lay thong tin co ban cua cac HS trinh ky
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleRecordBasicGetAll($sRecordPkList,$iFkUnit,$sOwnerCode){
		$psSql = "Exec eCS_HandleRecordBasicGetAll ";
		$psSql .= "'"  . $sRecordPkList . "'";
		$psSql .= ",'"  . $iFkUnit . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/** Nguoi tao: NGHIAT
		* Ngay tao: 09/11/2010
		* Y nghia: Update Cap nhat cong viec tien do
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleSubmitOrderUpdate($arrParameter){
		$psSql = "Exec eCS_HandleSubmitOrderUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['PK_TRASITION_RECORD_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_SUBMIT_ORDER_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_SUBMIT_ORDER_CONTENT'] . "'";
		$psSql .= ",'" . $arrParameter['C_FILE'] . "'";;			
		$psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
		$psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_ROLES'] . "'";
		$psSql .= ",'" . $arrParameter['C_STATUS'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_USER_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_USER_NAME'] . "'";
		//Thuc thi lenh SQL		
		//echo $psSql; exit;
		try {			
			$arrResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;		
	}

    /**
     * @param $arrParameter
     * @return unknown
     */
	public function eCSHandleTransitionUpdate($arrParameter){
        $arrTempResult = array();
        $psSql = "Exec [dbo].[eCS_HandleTransitionUpdate] ";
        $psSql .= "'" . $arrParameter['PK_RECORD_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_SUBMIT_ORDER_CONTENT'] . "'";
        $psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
        $psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
        $psSql .= ",'" . $arrParameter['C_ROLES'] . "'";
        $psSql .= ",'" . $arrParameter['C_LIMIT_DATE'] . "'";
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
	/** Nguoi tao: NGHIAT
		* Ngay tao: 09/11/2010
		* Y nghia: lay thong tin lien quan den 1 cong viec da duoc tao lap
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSHandleTransitionGetSingle($sRecordPk,$sOwnerCode,$sCurrentStatus){
		$psSql = "Exec eCS_HandleTransitionGetSingle ";
		$psSql .= "'"  . $sRecordPk . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		$psSql .= ",'"  . $sCurrentStatus . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/** Nguoi tao: NGHIAT
		* Ngay tao: 30/11/2010
		* Y nghia: lay thong tin lien quan den 1 TTHC(giai doan, dau viec...)
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function fRecordTypeListByCode($sWorkType,$sRecordTypePk,$sOwnerCode,$sDelimiter1=',',$sDelimiter2 ='!&@!'){
		$psSql = "Select dbo.f_RecordTypeListByCode(";
		$psSql .= "'"  . $sWorkType . "'";
		$psSql .= ",'"  . $sRecordTypePk . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		$psSql .= ",'"  . $sDelimiter1 . "'";
		$psSql .= ",'"  . $sDelimiter2 . "')";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	/** Nguoi tao: KHOINV
		* Ngay tao: 11/07/2011
		* Y nghia: lay thong tin lien quan den 1 TTHC(giai doan, dau viec...)
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function fGetRecordTypeList($sWorkType,$sOwnerCode,$sDelimiter1=','){
		$psSql = "Select dbo.f_GetRecordTypeList(";
		$psSql .= "'"  . $sWorkType . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		$psSql .= ",'"  . $sDelimiter1 . "')";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
}?>
