<?php
/**
 * @copyright :efy.com.vn - 2009 
 * @see : Lop modReportStaff 
 * */
class record_modSendRecord extends Extra_Db {
	/**
	 * @author: KHOINV
	 * @since :11/05/2011
	 * @see : cap nhat thong tin dang ky tai khoan qua mang
	 * @param :
	 * 			$arrParameter: mang chua thong tin update
	 * @return : 
	 * 			
	 * 
	 * 
	 * 	*/
	public function eCSNetUserUpdate($arrParameter){
		$sql = "Exec eCS_NetUserUpdate ";	
		$sql .= "'"  . $arrParameter['PK_NET_ID'] . "'";
		$sql .= ",'" . $arrParameter['C_FULLNAME'] . "'";
		$sql .= ",'" . $arrParameter['C_USERNAME'] . "'";
		$sql .= ",'" . $arrParameter['C_PASSWORD'] . "'";
		$sql .= ",'" . $arrParameter['C_EMAIL'] . "'";
		$sql .= ",'" . $arrParameter['C_ID_CARD'] . "'";			
		$sql .= ",'" . $arrParameter['C_CREATED_DATE'] . "'";
		$sql .= ",'" . $arrParameter['C_XML_DATA'] . "'";		
//		/echo $sql;
		try {						
			$arrResult = $this->adodbExecSqlString($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;	
	}
	
	
	
	
	/**
	 * @author : KHOINV
	 * @since : 11/05/2011
	 * @see : Lay danh sach loai bao cao
	 * @param :
	 * 			$sUserName: user dang nhap
	 * 			$sPassWord: password dang nhap
	 * @return : 
	 * 			$arrResult 
	 * 			
	 * */
	public function eCSNetCheckLogin($sUserName,$sPassWord){			
		$sql = "Exec eCS_NetCheckLogin  ";
		$sql .= "'" . $sUserName . "'";
		$sql .= ",'" . $sPassWord . "'";		
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
	 * @since : 11/05/2011
	 * @see : Lay danh sach ho so da gui qua mang doi voi tung loai thu tuc
	 * @param :
	 * 			$sRecordTypeID: Ma loai ho so
	 * 			$sUserName: ten dang nhap
	 * @return : 
	 * 			$arrResult : mang 2 chieu chua dang sach thu tuc
	 * 			
	 * */
	public function eCSNetRecordGetAllByType($sRecordTypeID,$sUserName){			
		$sql = "Exec eCS_NetRecordGetAllByType  ";
		$sql .= "'" . $sRecordTypeID . "'";
		$sql .= ",'" . $sUserName . "'";		
		//echo $sql ; exit();
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
	 * @author : KHOINV
	 * @since : 11/05/2011
	 * @see : Lay thong tin chi tiet thu tuc tu ma thu tuc truyen vao
	 * @param :
	 * 			$sRecordID: Ma thu tuc
	 * 		
	 * @return : 
	 * 			$arrResult : row chua thong tin chi tiet thu tuc
	 * 			
	 * */
	public function eCSNetRecordGetSingle($sRecordID){			
		$sql = "Exec eCS_NetRecordGetSingle  ";
		$sql .= "'" . $sRecordID . "'";			
		//echo $sql ; exit();
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
	 * @author : KHOINV
	 * @since : 11/05/2011
	 * @see : Lay thong tin chi tiet thu tuc tu ma thu tuc truyen vao
	 * @param :
	 * 			$sRecordID: Ma thu tuc
	 * 		
	 * @return : 
	 * 			$arrResult : row chua thong tin chi tiet thu tuc
	 * 			
	 * */
	public function eCSNetRecordUpdate($arrParameter){			
		$sql = "Exec eCS_NetRecordUpdate  ";
		$sql .= "'"  . $arrParameter['PK_NET_RECORD'] . "'";
		$sql .= ",'" . $arrParameter['FK_RECORDTYPE'] . "'";
		$sql .= ",'" . $arrParameter['FK_NET_ID'] . "'";
		$sql .= ",'" . $arrParameter['C_CODE'] . "'";
		$sql .= ",'" . $arrParameter['C_INPUT_DATE'] . "'";
		$sql .= ",'" . $arrParameter['C_PRELIMINARY_DATE'] . "'";			
		$sql .= ",'" . $arrParameter['C_ORIGINAL_APPLICATION_DATE'] . "'";
		$sql .= ",'" . $arrParameter['C_RECEIVING_DATE'] . "'";		
		$sql .= ",'" . $arrParameter['C_XML_DATA'] . "'";			
		$sql .= ",'" . $arrParameter['C_STATUS'] . "'";
		$sql .= ",'" . $arrParameter['C_MESSAGE'] . "'";	
		$sql .= ",'" . $arrParameter['C_UNIT'] . "'";
		//echo $sql;exit;
		try {						
			$arrResult = $this->adodbExecSqlString($sql);					
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $arrResult;	
	}
	/**
	 * @author : KHOINV
	 * @since : 12/05/2011
	 * @see : xoa ho so dang ky qua mang
	 * @param :
	 * 			$sNetRecordIDList: danh sach ma thu tuc can xoa
	 * 		
	 * @return : 
	 * 			$arrResult : ma cua ban ghi vua cap nhat
	 * 			
	 * */
	public function eCS_NetRecordDelete($sNetRecordIDList){			
		$sql = "Exec eCS_NetRecordDelete  ";
		$sql .= "'" . $sNetRecordIDList . "'";			
		//echo $sql ; //exit();
		// thuc hien cap nhat du lieu vao csdl
		try {			
			$arrResult = $this->adodbExecSqlString($sql) ; 			
			$Result= $arrResult['RET_ERROR'];
		}catch (Exception $e){
			echo $e->getMessage();
		};				
		return $Result;	
		//var_dump($arrResult) . 'du lieu ket xuat tu database';
	}
	/**
	 * @author : KHOINV
	 * @since : 12/05/2011
	 * @see : tra cuu thong tin ho so qua mang
	 * @param :
	 * 			$sRecordTypeID: ma loai ho so
	 * 			$sName:			ten nguoi dang ky	
	 * 			$sCode:			ma ho so
	 * @return : 
	 * 			$arrResult : mang 2 chieu chua danh sach ho so
	 * 			
	 * */
	public function eCS_NetRecordSeach ($sRecordTypeID,$sName,$sCode,$iPage,$iNumberRecordPerPage){			
		$sql = "Exec eCS_NetRecordSeach  ";
		$sql .= "'" . $sRecordTypeID . "'";		
		$sql .= ",'" . $sName . "'";	
		$sql .= ",'" . $sCode . "'";
		$sql .= ",'" . $iPage . "'";	
		$sql .= ",'" . $iNumberRecordPerPage . "'";		
		//echo $sql ; exit();
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
	 * @author : KHOINV
	 * @since : 12/05/2011
	 * @see : lay danh sach ho so cho xem hoac dang ky qua mang
	 * @param :
	 * 			$iViewOnNet:       		xem qua mang hay khong (1:co, 0: khong, -1:khong quan tam)
	 * 			$iRegisterOnNet:		dang ky qua mang hay khong (1:co, 0: khong, -1:khong quan tam)	
	 * @return : 
	 * 			$arrResult : mang 2 chieu chua danh sach ho so
	 * 			
	 * */
	public function eCS_RecordGetAllByNet ($iViewOnNet,$iRegisterOnNet){			
		$sql = "Exec eCS_RecordGetAllByNet  ";
		$sql .= "'" . $iViewOnNet . "'";		
		$sql .= ",'" . $iRegisterOnNet . "'";		
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
	 * @author : KHOINV
	 * @since : 12/05/2011
	 * @see : lay danh sach tien do thuc hien cua ho so
	 * @param :
	 * 			$sRecordcode:     ma ho so
	 * @return : 
	 * 			$arrResult : mang 2 chieu chua tien do thuc hien cua ho so
	 * 			
	 * */
	public function eCS_NetRecordWork  ($sRecordcode){			
		$sql = "Exec eCS_NetRecordWork  ";
		$sql .= "'" . $sRecordcode . "'";			
		//echo $sql ; exit();
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
	 * @author : KHOINV
	 * @since : 11/05/2011
	 * @see : Lay danh don vi nhan ho so
	 * @param :
	 * 			$sUserName: user dang nhap
	 * @return : 
	 * 			$arrResult 
	 * 			
	 * */
	public function eCSGetListUnit($sUnit){			
		$sql = "Exec eCS_GetListUnit  ";
		$sql .= "'" . $sUnit . "'";
		//echo $sql ; exit();
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
	 * @author : KHOINV
	 * @since : 19/05/2011
	 * @see : Lay thong tin file dinh kem
	 * @param :
	 * 			$sRecordID: ma ho so
	 * 			$sKeyAttach:ma tai lieu kem theo
	 * @return : 
	 * 			$arrResult: mang 2 chieu chua thong tin file dinh kem
	 * 			
	 * */
	public function eCSFileGetSingle($sRecordID,$sKeyAttach){			
		$sql = "Exec EfyLib_libFileGetSingle  ";
		$sql .= "'" . $sRecordID . "'";
		$sql .= ",'" . $sKeyAttach . "'";
		//echo $sql ; exit();
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
	/**
	 * @author : KHOINV
	 * @since : 19/05/2011
	 * @see : xoa file trong database va tren o cung
	 * @param :
	 * 			$filename: ten file can xoa
	 * @return : 
	 * 			$arrResult: cap nhat bang T_EFYLIB_FILE
	 * 			
	 * */
	public function eCSDeleteFileUpload($filename){			
		$sql = "Exec Net_deleteFileUpload  ";
		$sql .= "'" . $filename . "'";
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
}
?>
