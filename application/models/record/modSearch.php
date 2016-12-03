<?php
/**
 * @copyright :efy.com.vn - 2010 
 * @see : Lop modSearch 
 * */
class Record_modSearch extends Efy_DB_Connection {	
	/** Nguoi tao: NGHIAT
		* Ngay tao: 08/12/2010
		* Y nghia: Lay danh sach hs theo tieu chi tim kiem
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
	public function eCSSearchGetAll($sRecordTypeId,$sRecordType,$dFromReceiveDate,$dToReceiveDate,$sFulltextsearch,$sStatus,$sOwnerCode,$sXmlTagList,$sXmlValueList,$sXmlOperatorList,$sXmlTrueFalseList,$sDelimetor,$iCurrentPage,$iNumberRecordPerPage){
		$psSql = "Exec eCS_SearchGetAll ";
		$psSql .= "'"  . $sRecordTypeId . "'";
		$psSql .= ",'"  . $sRecordType . "'";
		$psSql .= ",'"  . $dFromReceiveDate . "'";
		$psSql .= ",'"  . $dToReceiveDate . "'";
		$psSql .= ",'"  . $sFulltextsearch . "'";
		$psSql .= ",'"  . $sStatus . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		$psSql .= ",'"  . $sXmlTagList . "'";
		$psSql .= ",'"  . $sXmlValueList . "'";
		$psSql .= ",'"  . $sXmlOperatorList . "'";
		$psSql .= ",'"  . $sXmlTrueFalseList . "'";
		$psSql .= ",'"  . $sDelimetor . "'";
		$psSql .= ",'"  . $iCurrentPage . "'";
		$psSql .= ",'"  . $iNumberRecordPerPage . "'";
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
		* Ngay tao: 10/12/2010
		* Y nghia: Lay thong tin chi tiet cua mot ho so
		* adodbExecSqlString: lay mang 1 chieu
		* adodbQueryDataInNameMode: lay mang da chieu
	*/
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
     * @param $dFromDate
     * @param $dToDate
     * @param $Scate
     * @param $sOwnerCode
     * @return Mang
     */
	public function eCSSearchGeneralGetAllUnit($arrParameter){
		$psSql = "Exec eCS_SearchGeneralGetAllUnit ";
		$psSql .= "'"  . $arrParameter['C_FROM_DATE'] . "'";
		$psSql .= ",'"  . $arrParameter['C_TO_DATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_CATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_OWNER_CODE'] . "'";
		//echo  "<br>". $psSql . "<br>";
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
	}

    public function eCSSearchGeneralGetAllRecordType($arrParameter){
        $psSql = "Exec eCS_SearchGeneralGetAllRecordType ";
        $psSql .= "'"  . $arrParameter['C_FROM_DATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_TO_DATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_CATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_OWNER_CODE'] . "'";
        //echo  "<br>". $psSql . "<br>";
        //exit;
        try{
            $arrResult = $this->adodbQueryDataInNameMode($psSql);
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function eCSSearchRecordGetAll($arrParameter){
        $psSql = "Exec eCS_SearchRecordGetAll ";
        $psSql .= "'"  . $arrParameter['C_TYPE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_FROM_DATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_TO_DATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_CATE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_RECORDTYPE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_OWNER_CODE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_PAGE'] . "'";
        $psSql .= ",'"  . $arrParameter['C_RECORD_PER_PAGE'] . "'";
        //echo  "<br>". $psSql . "<br>";
        //exit;
        try{
            $arrResult = $this->adodbQueryDataInNameMode($psSql);
        }catch (Exception $e){
            echo $e->getMessage();
        };
        return $arrResult;
    }


	public function eCSSearchStatusGetAll($dFromDate,$dToDate,$sOwnerCode,$sRecordTypeId,$sCurrentStatus,$sFullTextSearch,$iCurrentPage,$iNumberRecordPerPage){
		$psSql = "Exec eCS_SearchStatusGetAll ";
		$psSql .= "'"  . $dFromDate . "'";
		$psSql .= ",'"  . $dToDate . "'";
		$psSql .= ",'"  . $sOwnerCode . "'";
		$psSql .= ",'"  . $sRecordTypeId . "'";
		$psSql .= ",'"  . $sCurrentStatus . "'";
		$psSql .= ",'"  . $sFullTextSearch . "'";
		$psSql .= ",'"  . $iCurrentPage . "'";
		$psSql .= ",'"  . $iNumberRecordPerPage . "'";
		//echo  "<br>". $psSql . "<br>"; 
		//exit;
		try{
			$arrResult = $this->adodbQueryDataInNameMode($psSql);
		}catch (Exception $e){
			echo $e->getMessage();
		};
		return $arrResult;
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
}
?>
