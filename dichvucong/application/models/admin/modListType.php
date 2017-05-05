<?php

class admin_modListType extends G_Db
{
    public function getAllListType($piStatus, $psTypeName, $psOwnerCode, $iCurrentPage, $iRecordNumberPage)
    {
        $arrResult = array();
        $sql = "Exec sp_SysListtypeGetAll ";
        $sql .= $this->qstr($piStatus);
        $sql .= "," . $this->qstr($psTypeName);
        $sql .= "," . $this->qstr($psOwnerCode);
        $sql .= "," . $this->qstr($iCurrentPage);
        $sql .= "," . $this->qstr($iRecordNumberPage);
        $arrTempResult = $this->adodbQueryDataInNameMode($sql);

        //Xu ly ket qua
        $countElement = sizeof($arrTempResult);
        if ($countElement > 0) {
            for ($index = 0; $index < $countElement; $index++) {
                // Lay ID ListType
                $arrResult[$index]['PkListType'] = $arrTempResult[$index]['PkListType'];
                // Ma cua ListType
                $arrResult[$index]['sCode'] = $arrTempResult[$index]['sCode'];

                // Ten ListType
                $arrResult[$index]['sName'] = $arrTempResult[$index]['sName'];

                // So thu tu
                $arrResult[$index]['iOrder'] = $arrTempResult[$index]['iOrder'];
                // Tinh trang
                if (trim($arrTempResult[$index]['sStatus']) == 'HOAT_DONG') {
                    $sStatus = 'Hoạt động';
                } else {
                    $sStatus = 'Ngừng hoạt động';
                }
                $arrResult[$index]['sStatus'] = $sStatus;
                $arrResult[$index]['C_TOTAL_RECORD'] = $arrTempResult[$index]['C_TOTAL_RECORD'];
            }
        }
        return $arrResult;
    }


    public function updateListType($piListTypeId, $psListTypeCode, $psListTypeName, $piListTypeOrder, $psListTypeXml, $pbListTypeStatus, $psListTypeOwnerCodeList
        , $sChkImportStatus, $sChkAutoGenerateStatus, $sTxtAutoGenerateObjectCode)
    {
        // Bien luu trang thai
        $Result = null;
        $sql = "Exec sp_SysListtypeUpdate " . $this->qstr($piListTypeId);
        $sql .= "," . $this->qstr($psListTypeCode);
        $sql .= "," . $this->qstr($psListTypeName);
        $sql .= "," . $this->qstr($piListTypeOrder);
        $sql .= "," . $this->qstr($psListTypeXml);
        $sql .= "," . $this->qstr($pbListTypeStatus);
        $sql .= "," . $this->qstr($psListTypeOwnerCodeList);
        $sql .= "," . $this->qstr($sChkImportStatus);
        $sql .= "," . $this->qstr($sChkAutoGenerateStatus);
        $sql .= "," . $this->qstr($sTxtAutoGenerateObjectCode);
        //echo $sql;exit;
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrTempResult = $this->adodbExecSqlString($sql);
            $Result = $arrTempResult['RET_ERROR'];
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $Result;
    }

    public function deleteListType($psListTypeIdList)
    {
        // Bien luu trang thai
        $Result = null;
        $sql = "Exec sp_SysListtypeDelete " . $this->qstr($psListTypeIdList);
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrTempResult = $this->adodbExecSqlString($sql);
            if (isset($arrTempResult['RET_ERROR'])) {
                $Result = $arrTempResult['RET_ERROR'];
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $Result;
    }

    /**
     * @creator:
     * @createdate: 12/01/2009
     * @see : Lay thong tin cua mot Loai danh muc
     * @param : piListTypeId: Id cua loai danh muc
     * @return : $Result - Mang chua thong tin cua danh muc
     */
    public function getSingleListType($piListTypeId)
    {
        $sql = "Exec sp_SysListtypeGetSingle " . $this->qstr($piListTypeId);
        //echo $sql . "<br>";
        try {
            $arrTempResult = $this->adodbQueryDataInNameMode($sql);
            //Xu ly ket qua
            $countElement = sizeof($arrTempResult);
            if ($countElement > 0) {
                for ($index = 0; $index < $countElement; $index++) {
                    //foreach ($arrTempResult as $key)
                    // Lay ID ListType
                    $arrResult[$index]['PkListType'] = $arrTempResult[$index]['PkListType'];
                    // Ma cua ListType
                    $arrResult[$index]['sCode'] = $arrTempResult[$index]['sCode'];
                    // Ten ListType
                    $arrResult[$index]['sName'] = $arrTempResult[$index]['sName'];
                    // So thu tu
                    $arrResult[$index]['iOrder'] = $arrTempResult[$index]['iOrder'];
                    // Tinh trang
                    $arrResult[$index]['sStatus'] = $arrTempResult[$index]['sStatus'];
                    // file xml
                    $arrResult[$index]['sXmlFileName'] = $arrTempResult[$index]['sXmlFileName'];
                    // Don vi su dung
                    $arrResult[$index]['sOwnerCodeList'] = $arrTempResult[$index]['sOwnerCodeList'];
                    $arrResult[$index]['sImportStatus'] = $arrTempResult[$index]['sImportStatus'];
                    $arrResult[$index]['bAutoGenerateObjectStatus'] = $arrTempResult[$index]['bAutoGenerateObjectStatus'];
                    $arrResult[$index]['bAutoGenerateObjectCode'] = $arrTempResult[$index]['bAutoGenerateObjectCode'];

                }
            }
        } catch (ErrorException   $e) {
            $e->getMessage();
        }
        return $arrResult;
    }

    /**
     * @author: Trinh Thanh Phuong
     * Enter description here ...
     * @param unknown_type $iListTypeId
     */
    public function GetListTypeCodeFromIdList($iListTypeIdList)
    {
        $sSql = 'Exec sp_SysGetListTypeCodeFromIdList ' . $this->qstr($iListTypeIdList);
        try {
            $arrResult = $this->adodbQueryDataInNameMode($sSql);
        } catch (Exception  $e) {
            $e->getMessage();
        }
        return $arrResult;
    }

    /**
     * @author: Trinh Thanh Phuong
     * Get listtype code from listtype id
     * @param unknown_type $iListTypeId
     */
    public function GetListTypeCode($iListTypeId)
    {
        $sSql = 'Exec sp_SysGetListTypeCodeFromId ' . $this->qstr($iListTypeId);
        try {
            $arrResult = $this->adodbExecSqlString($sSql);
        } catch (Exception  $e) {
            $e->getMessage();
        }
        return $arrResult['sCode'];
    }
}

?>