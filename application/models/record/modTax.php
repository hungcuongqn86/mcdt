<?php
/**
 * @copyright :efy.com.vn - 11/2010 
 * @see : Nguoi tao: NGHIAT
 * */
class record_modTax extends Efy_DB_Connection {	
	public function eCSRecordTypeGetAll($sOwnerCode,$sFullTextSearch){
		$sql = "eCS_RecordTypeGetAll ";
		$sql = $sql . " '" . $sOwnerCode . "'";
		$sql = $sql . ",'" . $sFullTextSearch . "'";		
		//echo  "<br>". $sql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrResult;		
	}
	public function eCSTaxTreasuryUpdate($arrParameter){
		$psSql = "Exec eCS_TaxTreasuryUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_RECORD'] . "'";
		$psSql .= ",'" . $arrParameter['PK_RECORD_TAX'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_WORK_TYPE'] . "'";
		$psSql .= ",'" . $arrParameter['C_RESULT'] . "'";
		$psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";;			
		$psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
		$psSql .= ",'" . $arrParameter['C_REGISTER_NAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_REGISTER_ADDRESS'] . "'";
		$psSql .= ",'" . $arrParameter['C_AREA_LAND'] . "'";
		$psSql .= ",'" . $arrParameter['C_ADDRESS_LAND'] . "'";
		$psSql .= ",'" . $arrParameter['C_PAYABLE_TAX_TOTAL'] . "'";
		$psSql .= ",'" . $arrParameter['C_TREASURY_ADDRESS'] . "'";
		//Thuc thi lenh SQL		
		//echo $psSql; exit;
		try {			
			$arrResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	public function eCSTaxTreasuryGetSingle($sRecordPk){
		$psSql = "Exec eCS_TaxTreasuryGetSingle ";
		$psSql .= "'"  . $sRecordPk . "'";
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
?>
