<?php
class Listxml_modListReport extends Extra_Db {
	public function getAllListReport($piStatus,$psTypeName,$psOwnerCode,$psRecordTypeCode=''){							
		$sql = "Exec EfyLib_ListReportGetAll '" . $piStatus . "','" . $psTypeName . "','" . $psOwnerCode . "','".$psRecordTypeCode."'";			
		//echo $sql . '<br>';
		$arrTempResult = $this->adodbQueryDataInNameMode($sql);						
		return $arrTempResult;
	}
	public function UpdateListReport($piListReportId,$sReportName,$sReportXml,$iReportOrder,$sStatus,$sRecordTypeList,$sOwnerCode){
		$Result = null;		
		$sql = "Exec EfyLib_ListReportUpdate '" . $piListReportId . "','".$sReportName."'";
		$sql = $sql . ",'".$sReportXml."',".$iReportOrder;
		$sql = $sql . ",'".$sStatus."','".$sRecordTypeList."'";	
		$sql = $sql . ",'".$sOwnerCode."'";
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
	public function UpdateListReportCol($sListReportColId,$sListReportId,$sColTitle,$sColDataType,$sColWidth,$sColAlign,$sColDataSou,$sColDataSouName,$sColFuncName,$sColCalculate,$sColCondition,$iReportOrder,$sStatus,$sRecordTypeList){
		$Result = null;		
		$sql = "Exec EfyLib_ListReportColUpdate '" . $sListReportColId . "','".$sListReportId."'";
		$sql = $sql . ",'".$sColTitle."'";
		$sql = $sql . ",'".$sColDataType."','".$sColWidth."'";	
		$sql = $sql . ",'".$sColAlign."'";
		$sql = $sql . ",'".$sColDataSou."'";
		$sql = $sql . ",'".$sColDataSouName."'";
		$sql = $sql . ",'".$sColFuncName."'";
		$sql = $sql . ",'".$sColCalculate."'";
		$sql = $sql . ",'".$sColCondition."'";
		$sql = $sql . ",'".$iReportOrder."'";
		$sql = $sql . ",'".$sStatus."'";
		$sql = $sql . ",'".$sRecordTypeList."'";
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
	public function getSingleListReport($psListReportId){
		$sql = "Exec EfyLib_ListReportGetSingle '" . $psListReportId . "'";
		//echo $sql . "<br>";
		try {
			$arrTempResult = $this->adodbQueryDataInNameMode($sql);	
		}catch (ErrorException   $e){
			$e->getMessage();
		}		
		return $arrTempResult;
	}	
	public function eCSListReportMaxOrder($sOwnerCode){
		$sql = "Exec EfyLib_ListReportMaxOrder '".$sOwnerCode."'";
		//echo $sql . "<br>";
		try {
			$arrTempResult = $this->adodbQueryDataInNameMode($sql);	
		}catch (ErrorException   $e){
			$e->getMessage();
		}		
		return $arrTempResult;
	}	
	public  function deleteListReport($psListReportId){
		// Bien luu trang thai
		$Result = null;		
		$sql = "Exec EfyLib_ListReportDelete '" . $psListReportId . "'";	
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
	public  function deleteListReportCol($psListReportId){
		// Bien luu trang thai
		$Result = null;		
		$sql = "Exec EfyLib_ListReportColDelete '" . $psListReportId . "'";	
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
	public function getListInfoByCode($code,$sXml,$sValueNotGet){
		$sql = "EfyLib_ListGetAllbyCode ";
		$sql = $sql . "'" . $code . "'";
		$sql = $sql . ",'" . $sXml . "'";
		$sql = $sql . ",'" . $sValueNotGet . "'";
		//echo '$sql'.$sql;
		try {
			$arrSel = $this->adodbQueryDataInNameMode($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return $arrSel;
	}
	public function getAllListColReport($psListReportId,$listtype_code,$status){
		$sql = "Exec EfyLib_ListReportColGetAll '" . $psListReportId . "'";
		$sql = $sql . ",'" . $listtype_code . "'";
		$sql = $sql . ",'" . $status . "'";
		//echo $sql . "<br>";
        //exit;
		try {
			$arrTempResult = $this->adodbQueryDataInNameMode($sql);	
		}catch (ErrorException   $e){
			$e->getMessage();
		}		
		return $arrTempResult;
	}	
	public function getSingleListReportCol($psListReportId){
		$sql = "Exec EfyLib_ListReportColGetSingle '" . $psListReportId . "'";
		//echo $sql . "<br>";
		try {
			$arrTempResult = $this->adodbQueryDataInNameMode($sql);	
		}catch (ErrorException   $e){
			$e->getMessage();
		}		
		return $arrTempResult;
	}
	public function getAllRecord($psSqlString){
		$arrResult = $this->adodbQueryDataInNameMode($psSqlString);					
		return $arrResult;		
	}
}
?>