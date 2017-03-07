<?php
class record_modSeachRecordResult extends Extra_Db{
	/** Nguoi tao: Pham Tien Dung
		* Ngay tao: 30/06/2011
		* Y nghia: Lay thong tin chi tiet cua mot ho so
		* adodbExecSqlString: lay mang 1 chieu
		
	*/
	public function SeachRecordResult($sRecordId){
		$psSql = "Exec eCS_SeachRecordResult ";
		$psSql .= "'"  . $sRecordId . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbExecSqlString($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	public function eCS_SeachRecordWork($sRecordPk){
		$psSql = "Exec eCS_SeachWorkGetAll ";
		$psSql .= "'"  . $sRecordPk . "'";
		//echo  "<br>eCSHandleWorkGetAll:". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
}