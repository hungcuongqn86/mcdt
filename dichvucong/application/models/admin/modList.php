<?php

class admin_modList extends G_Db
{
    /**
     * @param $piStatus
     * @param $psTypeName
     * @param $psOwnerCode
     * @return array
     */
    public function getAllListType($piStatus, $psTypeName, $psOwnerCode, $iCurrentPage =1, $iNumberRecordPerPage = 15)
    {
        $psSql = "Exec sp_SysListtypeGetAll " . $this->qstr($piStatus) . "," . $this->qstr($psTypeName) . "," . $this->qstr($psOwnerCode);
        $psSql .= "," . $this->qstr($iCurrentPage);
        $psSql .= "," . $this->qstr($iNumberRecordPerPage);
        $arrAllListType = $this->adodbQueryDataInNameMode($psSql);
        return $arrAllListType;
    }

    /**
     * @param $arrAllListType
     * @param $piIdListType
     * @param $piPage
     * @param $piNumRecordOnPage
     * @param $psFilterXmlString
     * @return array
     */
    public function getAllList($arrAllListType, $piIdListType, $piPage, $piNumRecordOnPage, $psFilterXmlString)
    {
        $objConfig = new G_Global();
        //Tao mang luu ket qua
        $arrResult = array();
        //Neu ton tai Id Loai danh muc
        if ($piIdListType > 0) {
            //Luu ten file XML
            $psXmlFileName = "";
            //So phan tu cua mang loai danh muc
            $countElementListType = sizeof($arrAllListType);
            $chkImportStatus = false;
            $fileXml = '';
            for ($index = 0; $index < $countElementListType; $index++) {
                //Lay file XML
                if ($arrAllListType[$index]['PkListType'] == $piIdListType) {
                    $fileXml = "list/" . $arrAllListType[$index]['sXmlFileName'];
                    $psXmlFileName = $objConfig->dirXml . $fileXml;
                    if ($arrAllListType[$index]['sImportStatus']) {
                        $chkImportStatus = true;
                    }
                    break;
                }
            }
            //Neu loai danh muc hien thoi khong xac dinh file XML thi se lay file XML mac dinh
            if (trim($psXmlFileName) == "" || !is_file($psXmlFileName)) {
                $fileXml = "list/quan_tri_doi_tuong_danh_muc.xml";
                $psXmlFileName = $objConfig->dirXml . $fileXml;
            }

            $objLibXml = G_Xml::getInstance();
            //Doc file XML
            $psSqlString = $objLibXml->_xmlGetXmlTagValue($psXmlFileName, "list_of_object", "list_sql");

            //Thay the gia tri trong xau SQL
            $psSqlString = str_replace("#listtype_type#", $piIdListType, $psSqlString);
            $psSqlString = str_replace("#page#", $piPage, $psSqlString);
            $psSqlString = str_replace("#number_record_per_page#", $piNumRecordOnPage, $psSqlString);
            $psSqlString = $this->_replaceTagXmlValueInSql($psSqlString, $psXmlFileName, 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row', $psFilterXmlString);
//            echo $psSqlString; die;
            //Thuc thi lenh SQL
            $arrAllList = $this->adodbQueryDataInNameMode($psSqlString);
            //Xu ly ket qua
            $countElement = sizeof($arrAllList);
            if ($countElement > 0) {
                for ($index = 0; $index < $countElement; $index++) {
                    // Tinh trang
                    if (trim($arrAllList[$index]['sStatus']) == 'HOAT_DONG') {
                        $sStatus = 'Hoạt động';
                    } else {
                        $sStatus = 'Ngừng hoạt động';
                    }
                    $arrAllList[$index]['sStatus'] = $sStatus;
                }
            }

            //
            $arrResult = array('arrList' => $arrAllList,
                'xmlFileName' => $fileXml,
                'chkImportStatus' => $chkImportStatus
            );
        }
        //Return result
        return $arrResult;
    }

    /**
     * @param $p_sql_replace
     * @param $p_xml_string_in_file
     * @param $p_xml_tag
     * @param $p_filter_xml_string
     * @return mixed
     */
    private function _replaceTagXmlValueInSql($p_sql_replace, $p_xml_string_in_file, $p_xml_tag, $p_filter_xml_string)
    {
        //Tao mang luu thong tin cua cac phan tu tren form
        $objConvert = new G_Convert();
        $objxml = new G_Xml($p_xml_string_in_file);;
        $arrfilter_rows = $objxml->getArrayElement($p_xml_tag);

        $p_sql_replace = $objConvert->_restoreBadChar($p_sql_replace);
        //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
        $p_sql_replace = str_replace("#OWNER_CODE#", Zend_Auth::getInstance()->getIdentity()->sOwnerCode, $p_sql_replace);
        $v_sql_replace_temp = $p_sql_replace;
        foreach ($arrfilter_rows as $rows) {
            isset($rows["tag_list"]) ? $v_tag_list = $rows["tag_list"] : $v_tag_list = '';
            $arr_tag = explode(",", $v_tag_list);
            for ($i = 0; $i < sizeof($arr_tag); $i++) {
                isset($rows[$i]["data_format"]) ? $v_data_format = $rows[$i]["data_format"] : $v_data_format = '';
                isset($rows[$i]["xml_tag_in_db"]) ? $xmlTagInDb = $rows[$i]["data_format"] : $xmlTagInDb = '';
                $value_input = '';
                if ($p_filter_xml_string != "") {
                    $value = $objxml->_xmlGetXmlTagValue($p_filter_xml_string, 'data_list', $arr_tag[$i]);
                    $value_input = $objConvert->_restoreBadChar($value);
                    if ($v_data_format == "isdate") {
                        $value_input = $objConvert->_ddmmyyyyToYYyymmdd($value_input);
                    }
                    if ($v_data_format == "isnumeric") {
                        $value_input = intval($value_input);
                    }
                    if ($v_data_format == "ismoney") {
                        $value_input = floatval($value_input);
                    }
                }
                $v_sql_replace_temp = str_replace("#" . $arr_tag[$i] . "#", $value_input, $v_sql_replace_temp);
            }
        }
        return $v_sql_replace_temp;
    }

    /**
     * @param $piIdListType
     * @param $arrParameter
     * @param $psXmlStringInDb
     * @return string
     */
    public function updateList($piIdListType, $arrParameter, $psXmlStringInDb)
    {
        $objConfig = new G_Global();
        $Result = '';
        //Neu ton tai Id danh muc
        if ($arrParameter['PkListType'] > 0) {
            //Neu loai danh muc hien thoi khong xac dinh file XML thi se lay file XML mac dinh
            if (trim($arrParameter['GET_XML_FILE_NAME']) == "" || !is_file($arrParameter['GET_XML_FILE_NAME'])) {
                $psXmlFileName = $objConfig->dirXml . "list/quan_tri_doi_tuong_danh_muc.xml";
            } else {
                $psXmlFileName = trim($arrParameter['GET_XML_FILE_NAME']);
            }
            //Tao doi tuong G_Lib
            $objLib = new G_Lib();
            $objLibXml = new G_Xml($psXmlFileName);

            //Doc file XML
            $psXmlStringInFile = $objLib->_readFile($psXmlFileName);
            $psSqlString = $objLibXml->_xmlGetXmlTagValue($psXmlStringInFile, "update_object", "update_sql");

            //Thay the gia tri trong xau SQL
            $psSqlString = str_replace("#listtype_type#", $piIdListType, $psSqlString);
            $psSqlString = str_replace("#list_id#", $arrParameter['PkList'], $psSqlString);
            $psSqlString = str_replace("#listtype_id#", $piIdListType, $psSqlString);
            $psSqlString = str_replace("#list_code#", $arrParameter['sCode'], $psSqlString);
            $psSqlString = str_replace("#list_name#", $arrParameter['sName'], $psSqlString);
            $psSqlString = str_replace("#list_order#", $arrParameter['iOrder'], $psSqlString);
            $psSqlString = str_replace("#list_status#", $arrParameter['sStatus'], $psSqlString);
            $psSqlString = str_replace("#p_owner_code_list#", $arrParameter['sOwnerCodeList'], $psSqlString);
            $psSqlString = str_replace("#xml_data#", $psXmlStringInDb, $psSqlString);

            $psSqlString = str_replace("#deleted_exist_file_id_list#", $arrParameter['DELETED_EXIST_FILE_ID_LIST'], $psSqlString);
            $psSqlString = str_replace("#new_file_id_list#", $arrParameter['NEW_FILE_ID_LIST'], $psSqlString);
            //echo htmlspecialchars($psSqlString) . '<br>';die();
            //Thuc thi lenh SQL
            try {
                $arrTempResult = $this->adodbExecSqlString($psSqlString);
                if (isset($arrTempResult['RET_ERROR']))
                    $Result = $arrTempResult['RET_ERROR'];
            } catch (Exception $e) {
                echo $e->getMessage();
            };
        }
        return $Result;
    }

    /**
     * @param $iListTypeId
     * @return array
     */
    public function createXMLDb($iListTypeId)
    {
        $sSql = "Exec sp_SysListByListtypeGetAll " . $this->qstr($iListTypeId);
        $arrResult = array();
        try {
            $arrResult = $this->adodbQueryDataInNameMode($sSql);
        } catch (Exception  $e) {
            $e->getMessage();
        }
        return $arrResult;
    }

    /**
     * @param $iListTypeId
     * @return mixed
     */
    public function GetListTypeCode($iListTypeId)
    {
        $sSql = 'Exec sp_SysGetListTypeCodeFromId ' . $this->qstr($iListTypeId);
        $arrResult = array();
        try {
            $arrResult = $this->adodbExecSqlString($sSql);
        } catch (Exception  $e) {
            $e->getMessage();
        }
        return $arrResult['sCode'];
    }

    /**
     * @param int $iListId
     * @return mixed
     */
    public function getSingleList($iListId = 0)
    {
        $psSqlString = "Exec sp_SysListGetSingle  ";
        $psSqlString = $psSqlString . $iListId;
        $arrGetSingleList = $this->adodbExecSqlString($psSqlString);
        return $arrGetSingleList;
    }

    /**
     * @param string $psObjectIdList
     * @return mixed
     */
    public function deleteList($psObjectIdList = "")
    {
        $psSqlString = "Exec sp_SysListDelete  '";
        $psSqlString = $psSqlString . $psObjectIdList . "'";
        //Thuc thi xau SQL va luu va mang (Xoa du lieu)
        $arrGetSingleList = $this->adodbExecSqlString($psSqlString);
        return $arrGetSingleList;
    }

    /**
     * @param $piListTypeId
     * @return array
     */
    public function getSingleListType($piListTypeId)
    {
        $sql = "Exec sp_SysListtypeGetSingle " . $this->qstr($piListTypeId);
        //echo $sql . "<br>";
        $arrTempResult = array();
        try {
            $arrTempResult = $this->adodbQueryDataInNameMode($sql);
        } catch (ErrorException $e) {
            $e->getMessage();
        }
        return $arrTempResult;
    }

    /**
     * @param $piListTypeId
     * @return int
     */
    public function getMaxOrder($piListTypeId)
    {
        $sql = "SELECT max(iOrder) As MAX_VALUE FROM SysList WHERE FkListType =" . $this->qstr($piListTypeId);
        $arrTempResult = array();
        $iOrder = 0;
        try {
            $arrTempResult = $this->adodbExecSqlString($sql);
        } catch (ErrorException $e) {
            $e->getMessage();
        }
        if ($arrTempResult) {
            $iOrder = $arrTempResult['MAX_VALUE'];
        }
        return $iOrder + 1;
    }
}