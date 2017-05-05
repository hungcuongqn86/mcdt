<?php
/**
 * @copyright :efy.com.vn - 2009 
 * @see : Lop modReportStaff 
 * */
class Record_modReport extends Extra_Db {
	/**
	 * @author: Toanhv
	 * @since: 11/04/2009
	 * @see: Ham thuc hien lay tat ca cac thong tin Bao cao
	 * @param :
	 * 			$psFilterXmlString: Chuoi Xml mo ta tieu chi loc
	 * 			$psXmlFileName: Ten file XMl
	 * @return :
	 * 			Mang chua thong tin 
	 * 	*/
	public function getAllReportProject($psFilterXmlString,$psXmlFileName){
		//Tao doi tuong Extra_Util
		$objEfyLib = new Extra_Util();
		//Tao doi tuong Extra_Xml
		Zend_Loader::loadClass('Extra_Xml');
		$objEfyLibXml = new Extra_Xml();
		//Doc file XML
		$psXmlStringInFile = $objEfyLib->_readFile($psXmlFileName);
		//echo '$psXmlStringInFile ='.$psXmlStringInFile ;
		$psSqlString = $objEfyLibXml->_xmlGetXmlTagValue($psXmlStringInFile,"report_sql","sql");
		//echo '<br> psSqlString = ' . $psSqlString . '<br>';	exit;			
		// Thay the gia tri trong file xml 
		//$psSqlString = $objEfyLibXml->_replaceTagXmlValueInSql($psSqlString, $psXmlStringInFile, 'filter_row', $psFilterXmlString);			
		$psSqlString = $objEfyLibXml->_replaceTagXmlValueInSql($psSqlString, $psXmlStringInFile, 'table_struct_of_filter_form/filter_row', $psFilterXmlString,'filter_formfield_list');
		//echo '<br>$psSqlString = ' .$psSqlString .'<br>';exit;
		//echo htmlspecialchars($psXmlStringInFile) ;exit;
		//Thuc thi lenh SQL
		$arrResult = $this->adodbQueryDataInNameMode($psSqlString);	
		//echo 'psSqlString = '.$psSqlString; exit;
		//$piCount = sizeof($arrResult);				
		return $arrResult;		
	}
		
	/**
	 * @author: KHOINV
	 * @since :26/05/2011
	 * @see : Lay danh sach loai ho so
	 * @param :
	 * 			$sOwnercode: ma down vi
	 * 			$sFullTextSearch: gia tri tim kiem
	 * @return : 
	 * 			Mang chua loai ho so
	 * 
	 * 
	 * 	*/
	public function eCSRecordTypeGetAll($sOwnercode,$sFullTextSearch,$sCate){
		$sql = "Exec eCS_RecordTypeGetAll ";
		$sql.= "'" .$sOwnercode."'";
		$sql.= ",'". $sFullTextSearch."'";
		$sql.= ",'". $sCate."'";		
		//echo 'abc:'.$sql;exit;
		try {						
			$arrResult = $this->adodbQueryDataInNameMode($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;	
	}
	
	/**
	 * @author: KHOINV
	 * @since :26/05/2011
	 * @see : Lay danh sach loai ho so
	 * @param :
	 * 			$sOwnercode: ma down vi
	 * 			$styperecord: ma loai ho so (01,02,...)
	 * @return : 
	 * 			Mang chua danh sach bao cao ung voi loai ho so 
	 * 
	 * 
	 * 	*/
	public function eCSListGetAllByTypeRecord($styperecord,$sOwnercode){
		$sql = "Exec eCS_ListGetAllByTypeRecord ";
		$sql=$sql." '".	$styperecord."'";
		$sql=$sql." ,'". $sOwnercode."'";	
		//echo $sql;exit;
		try {						
			$arrResult = $this->adodbQueryDataInNameMode($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;	
	}
	
	
	/**
	 * @author : Toanhv
	 * @since : 10/04/2009
	 * @see : Lay danh sach loai bao cao
	 * @param :
	 * 			$psReportListTypeCode: Ma Loai Danh muc : DM_BAO_CAO
	 * 			
	 * @return : 
	 * 			$arrResult 
	 * 			
	 * */
	public function getAllReportByReportType($psReportListTypeCode){			
		$sql = "Exec Doc_GetAllReportByReporttype  '" . $psReportListTypeCode ."'";		
		//echo $sql ; //exit();
		// thuc hien cap nhat du lieu vao csdl
		try {			
			$arrResult = $this->adodbQueryDataInNameMode($sql) ; 			
			
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;	
		//var_dump($arrResult) . 'du lieu ket xuat tu database';
	}
	
	/**
	 * @author : Toanhv
	 * @since : 10/04/2009
	 * @see : Lay danh sach loai bao cao
	 * @param :
	 * 			$arrParam: Mang chua danh muc
	 * 			
	 * @return : 
	 * 			$sHtmlRes : chuoi mo ta mot danh sach cac checkbox
	 * 			
	 * */

	

}
?>
