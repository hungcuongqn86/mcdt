<?php
/**
 * 
 * Model thuc hien xu ly du lieu HS TKQ
 * @author Tran Nghia
 *
 */
class record_modResult extends Extra_Db {
	/** Nguoi tao: NGHIAT
		* Ngay tao: 03/12/2010
		* Y nghia: Update Cap nhat TKQ
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu  
	*/
	public function eCSResultInfoUpdate($arrParameter){
		$psSql = "Exec eCS_ResultInfoUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_REGISTOR_NAME'] . "'";
		$psSql .= ",'" . str_replace(',','',$arrParameter['C_COST']) . "'";
		$psSql .= ",'" . $arrParameter['C_REASON'] . "'";			
		$psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
		$psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_CONTENT'] . "'";
		$psSql .= ",'" . $arrParameter['C_STATUS'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
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
		* Ngay tao: 03/12/2010
		* Y nghia: lay danh sach thong tin cac ho so
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu 
	*/
	public function eCSResultInfoList($sRecordPkList){
		$psSql = "Exec eCS_ResultInfoList ";
		$psSql .= "'"  . $sRecordPkList . "'";
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
		* Ngay tao: 04/12/2010
		* Y nghia: update thong tin sms
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu 
	*/
	public function eCSResultSmsUpdate($sReceiverList,$sMsgList){
		$psSql = "Exec eCS_ResultSmsUpdate ";
		$psSql .= "'"  . $sReceiverList . "'";
		$psSql .= ",'"  . $sMsgList . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
}
?>
