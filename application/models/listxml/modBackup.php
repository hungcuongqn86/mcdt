<?php
class Listxml_modBackup extends Extra_Db {
		
	/** Nguoi tao: NGHIAT
		* Ngay tao: 27/10/2010
		* Y nghia: Lay chi tiet mot TTHC
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSBackupHand($spath,$sDatabaseName,$sFileName){
		$arrResult = null;
		$sql = "Exec [sp_AutoBackupDb] ";
		$sql .= "'" . $spath . "'";
		$sql .= ",'" . $sDatabaseName . "'";
		$sql .= ",'" . $sFileName . "'";
		//echo $sql; exit;
		try{
			$arrResult = $this->adodbExecSqlString($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
} 	