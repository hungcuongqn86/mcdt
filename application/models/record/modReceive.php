<?php
/**
 * 
 * Model thuc hien xu ly du lieu ho so tiep nhan truc tiep taidon vi
 * @author Trinh Thanh Phuong
 *
 */
class record_modReceive extends Extra_Db {
	/**
	 * 
	 * Ham cap nhat thong tin mot ho so
	 * @param array $arrParameter: Mang du lieu luu thong tin ho so
	 */
	public function eCSRecordUpdate($arrParameter){
		$psSql = "Exec [dbo].[eCS_RecordUpdate] ";	
		$psSql .= "'" . $arrParameter['PK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['C_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['FK_RECORDTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['FK_RECEIVER'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECEIVER_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECEIVED_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_APPOINTED_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_CURRENT_STATUS'] . "'";			
		$psSql .= ",'" . $arrParameter['C_DETAIL_STATUS'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECEIVED_RECORD_XML_DATA'] . "'";
		$psSql .= ",'" . $arrParameter['C_LICENSE_XML_DATA'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['NEW_FILE_ID_LIST'] . "'";
		//echo $psSql; exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;		
	}
	public function eCSRecordUpdateLicense($p_recordId,$p_license_xml_data){
		$psSql = "Exec [dbo].[eCS_RecordUpdateLicense] ";	
		$psSql .= "'" . $p_recordId . "'";		
		$psSql .= ",'" . $p_license_xml_data . "'";
		//echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;		
	}
	public function eCSRecordDelete($sRecordIdList,$iHasDeleteAllPermission){
		$Result = null;			
		$sql = "Exec eCS_RecordDelete ";		
		$sql .= "'".$sRecordIdList ."'";	
		$sql .= ",'".$iHasDeleteAllPermission ."'";
		//echo $sql . '<br>'; exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($sql) ; 			
			$Result= $arrTempResult['RET_ERROR'];
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $Result;	
	}

	public function DOC_GetAllDocumentFileAttach($sDocumentId, $pFileTyle, $pTableObject){
		$sql = "Exec Doc_GetAllDocumentFileAttach '" . $sDocumentId . "'";
		$sql .= ",'".$pFileTyle ."'";		
		$sql .= ",'".$pTableObject ."'"; 
		//echo $sql . '<br>';
		try {						
			$arrResult = $this->adodbQueryDataInNameMode($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;			
	}
	/**
	 * DS: Ham cap nhat thong tin chuyen thu ly
	 * @param unknown_type $arrParameter
	 */
	public function eCSMoveHandleRecordUpdate($arrParameter){
		$psSql = "Exec [dbo].[eCS_MoveHandleRecordUpdate] ";	
		$psSql .= "'" . $arrParameter['C_RECORD_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECORD_TRANSITION_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_OBJECT_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_OBJECT_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_OBJECT_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKER_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORER_POSITION_NAME'] . "'";			
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_TYPE'] . "'";
		//echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;		
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
	/** Nguoi tao: NGHIAT
		DS: Ham cap nhat thong tin chuyen thu ly
		* @param $sRecordIdList Dang sach Id ho so
		* $sOwnerCode Ma don vi su dung
	*/
	public function eCSGetInfoRecordFromListId($sRecordIdList){
		$arrResult = null;
		$sql = "Exec eCS_GetInfoRecordFromListId ";
		$sql .= "'" . $sRecordIdList . "'";
		//echo $sql; exit;
		try{
			$arrResul = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}		
		return $arrResul;
	}
	/**
	 * @author : CHIENBN
	 * @since : 12/11/2015
	 * 	Cap nhap ma id hs moi RTA gui ve 	
	 * */
	public function eCSUpdateNewId($Newid,$minor_code){			
		$sql = "Exec eCS_UpdateNewId  ";
		$sql .= "'" . $Newid . "'";
		$sql .= ",'" . $minor_code . "'";
		//echo $sql ; exit();
		// thuc hien cap nhat du lieu vao csdl
		try {			
			$arrResult = $this->adodbExecSqlString($sql) ; 			
			
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;	
		//var_dump($arrResult) . 'du lieu ket xuat tu database';
	}	
	/**
	 * @author : KHOINV
	 * @since : 19/05/2011
	 * @see : cap nhat file dinh kem vao database
	 * @param :
	 * 			$sRecordID: ma ho so
	 * 			$sListfile: danh sach file dinh kem (gom ca ma tai lieu kem theo)
	 * 			$sTablename: bang lien quan
	 * @return : 
	 * 			$arrResult: cap nhat bang T_EFYLIB_FILE
	 * 			
	 * */
	public function eCSUpdateFileAttach($sRecordID,$sListfile,$sTablename){			
		$sql = "Exec Net_UpdateFileAttach  ";
		$sql .= "'" . $sRecordID . "'";
		$sql .= ",'" . $sListfile . "'";
		$sql .= ",'" . $sTablename . "'";
		//echo $sql ; exit();
		// thuc hien cap nhat du lieu vao csdl
		try {			
			$arrResult = $this->adodbExecSqlString($sql) ; 			
			
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;	
		//var_dump($arrResult) . 'du lieu ket xuat tu database';
	}
	public function getSingleRecord($psRecord){
		$sql = "Exec ecs_RecordGetSingleByCode '" . $psRecord . "'";	
		//echo 	$sql . '<br>';exit;
		try {						
			$arrResult = $this->adodbExecSqlString($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};			
		return $arrResult;	
	}
	public function eCSRecordGetSingle($psRecord,$pOwnercode){
		$sql = "Exec eCS_RecordGetSingle '" . $psRecord . "'";	
		$sql .= ",'" . $pOwnercode . "'";	
		//echo 	$sql . '<br>';exit;
		try {						
			$arrResult = $this->adodbExecSqlString($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};			
		return $arrResult;	
	}
    /*
    cuongnh
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
	public function eCSHandleWorkMultiUpdate($arrParameter){
		$psSql = "Exec eCS_HandleWorkMultiUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD'] . "'";
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
	public function eCSSearchGetSingle($sRecordId,$sOwnerCode){
		$psSql = "Exec eCS_SearchGetSingle ";
		$psSql .= "'"  . $sRecordId . "'";
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
	/**
	 * DS: Ham cap nhat thong tin chuyen TKQ
	 * @param unknown_type $arrParameter
	 */
	public function eCSMoveRecordWaitResultUpdate($arrParameter){
		$psSql = "Exec [dbo].[eCS_MoveRecordWaitResultUpdate] ";	
		$psSql .= "'" . $arrParameter['C_RECORD_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_RECORD_TRANSITION_ID_LIST'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKER_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORER_POSITION_NAME'] . "'";			
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_NOTES'] . "'";
		//echo htmlspecialchars($psSql); exit;
		try {			
			$arrTempResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrTempResult;		
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

    public function eCSReceiveTransitionRecordUpdate($arrParameter){
        $arrTempResult = array();
        $psSql = "Exec [dbo].[eCS_ReceiveTransitionRecordUpdate] ";
        $psSql .= "'" . $arrParameter['PK_RECORD_LIST'] . "'";
        $psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_SUBMIT_ORDER_CONTENT'] . "'";
        $psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
        $psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
        $psSql .= ",'" . $arrParameter['C_ROLES'] . "'";
        $psSql .= ",'" . $arrParameter['FK_UNIT'] . "'";
        $psSql .= ",'" . $arrParameter['C_UNIT_NAME'] . "'";
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
?>
