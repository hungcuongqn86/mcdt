<?php
/**
 * @copyright :efy.com.vn - 11/2010 
 * @see : Nguoi tao: NGHIAT
 * */
class record_modUser extends Efy_DB_Connection {		
	public function eCSNetUserUpdate($arrParameter){
		$psSql = "Exec eCS_NetUserUpdate  ";	
		$psSql .= "'"  . $arrParameter['PK_NET_ID'] . "'";
		$psSql .= ",'" . $arrParameter['C_FULLNAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_USERNAME'] . "'";
		$psSql .= ",'" . $arrParameter['C_PASSWORD'] . "'";
		$psSql .= ",'" . $arrParameter['C_EMAIL'] . "'";
		$psSql .= ",'" . $arrParameter['C_ID_CARD'] . "'";;			
		$psSql .= ",'" . $arrParameter['C_CREATED_DATE'] . "'";
		$psSql .= ",'" . $arrParameter['C_XML_DATA'] . "'";
		//Thuc thi lenh SQL		
		//echo $psSql; exit;
		try {			
			$arrResult = $this->adodbExecSqlString($psSql) ; 
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}
	public function eCSNetUpdatePass($sUsername,$sNewpass){
		$psSql = "Exec eCS_NetUpdatePass ";
		$psSql .= "'"  . $sUsername . "'";
		$psSql .= ",'"  . $sNewpass . "'";
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
