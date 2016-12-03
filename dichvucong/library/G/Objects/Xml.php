<?php
require_once 'Zend/Config/Xml.php';

/**
 * Class G_Objects_Xml
 */
class G_Objects_Xml extends Zend_Config_Xml
{
    /**
     * @var null
     */
    protected static $_instance = null;

    /**
     * @return G_Objects_Xml|null
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('<?xml version="1.0" encoding="UTF-8"?><root></root>');
        }
        return self::$_instance;
    }

    /**
     * @var null
     */
    protected $_xml = null;

    /**
     * @param string $xml
     * @param null $section
     * @param bool $options
     * @throws Zend_Config_Exception
     */
    public function __construct($xml = '<?xml version="1.0" encoding="UTF-8"?><root></root>', $section = null, $options = false)
    {

        return parent::__construct($xml, $section, $options);
    }

    /**
     * @param $xml
     * @param null $section
     * @param bool $options
     * @return Zend_Config_Xml
     * @throws Zend_Config_Exception
     */
    public function __loadxml($xml, $section = null, $options = false)
    {
        if ($this->_xml == $xml) {
            return self::getInstance();
        }
        $this->_xml = $xml;
        return self::__construct($xml, $section, $options);
    }

    /**
     * @param $sXml
     * @param $sParentTag
     * @param $sTag
     * @return mixed|string
     */
    public function _xmlGetXmlTagValue($sXml, $sParentTag, $sTag)
    {
        //Neu ton tai xau XML
        if ($sXml != "") {
            self::__loadxml($sXml, $sParentTag);
            return G_Convert::_restoreBadChar($this->$sTag);
        } else {
            return "";
        }
    }

    /**
     * @param $arrTags
     * @return array
     */
    public function getArrayElement($sTags)
    {
        /*  if ($sTags == 'update_object/table_struct_of_update_form/update_row_list/update_row') {
              return $this->update_object->table_struct_of_update_form->update_row_list->update_row->toArray();
          }
          if ($sTags == 'list_of_object/table_struct_of_filter_form/filter_row_list/filter_row') {
              return $this->list_of_object->table_struct_of_filter_form->filter_row_list->filter_row->toArray();
          }
          if ($sTags == 'list_of_object/list_body_hidden/col') {
              return $this->list_of_object->list_body_hidden->col->toArray();
          }
          if ($sTags == 'list_of_object/switch_data/item') {
              return $this->list_of_object->switch_data->item->toArray();
          }*/

        $arrValue = $this->toArray();
        $arrTags = explode("/", $sTags);
        for ($i = 0; $i < sizeof($arrTags); $i++) {
            if (isset($arrValue[$arrTags[$i]])) {
                $arrValue = $arrValue[$arrTags[$i]];
            } else {
                return array();
            }
        }
        return $arrValue;
    }

    /**
     * @param $spXmlFileName
     * @param $pathXmlTagStruct
     * @param $pathXmlTag
     * @param $column_xml
     * @param $p_arr_item_value
     * @param bool $p_view_mode
     * @param bool $readonly
     * @return string
     */
    public function _xmlGenerateFormfield($spXmlFileName, $pathXmlTagStruct, $pathXmlTag, $column_xml, $p_arr_item_value, $p_view_mode = false, $readonly = false)
    {
        //Lay mang du lieu
        $arrData = array();
        if (isset($p_arr_item_value[$column_xml])) {
            $p_xml_string = '<?xml version="1.0" encoding="UTF-8"?>' . $p_arr_item_value[$column_xml];
            self::__loadxml($p_xml_string, 'data_list');
            $arrData = $this->toArray();
        }
        //Load xml
        if ($spXmlFileName != '') {
            self::__loadxml($spXmlFileName);
        } else {
            return '';
        }
        //$pathXmlTagStruct ='list_of_object';
        //Tao mang luu cau truc cua form
        $arrTable_truct_rows = $this->getArrayElement($pathXmlTagStruct);
        if (empty($arrTable_truct_rows)) return '';

        //Kiem tra $arrTable_truct_rows co phai la mang 1 chieu hay ko
        if (!isset($arrTable_truct_rows[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrTable_truct_rows);
            $arrTable_truct_rows = $arrTemp;
        }

        //Lay cac tham so
        $v_first_col_width = $this->common_para_list->common_para->first_col_width;
        $v_js_file_name = $this->common_para_list->common_para->js_file_name;
        $v_js_function = $this->common_para_list->common_para->js_function;

        //Start
        $spHtmlStr = '';

        $sContentXmlTop = '<div id = "Top_contentXml">';
        $sContentXmlTopLeft = '<div id = "Topleft_contentXml">';
        $sContentXmlTopRight = '<div id = "Topright_contentXml">';
        $sContentXmlBottom = '<div id="Bottom_contentXml">';

        foreach ($arrTable_truct_rows as $row) {
            isset($row["row_id"]) ? $sRowId = $row["row_id"] : $sRowId = '';
            isset($row["v_style"]) ? $sStyleRow = $row["v_style"] : $sStyleRow = '';
            isset($row["view_position"]) ? $sViewPosition = $row["view_position"] : $sViewPosition = '';
            $strdiv = '<div>';
            if ($sRowId != '')
                $strdiv = '<div id = "id_' . $sRowId . '" class="normal_label" style="' . $sStyleRow . '">';
            if ($sViewPosition == 'left') {
                $sContentXmlTopLeft .= $strdiv . $this->_generalHtmlRow($row, $pathXmlTag, $p_arr_item_value, $arrData, $p_view_mode, $readonly) . '</div>';
            } else if ($sViewPosition == 'right') {
                $sContentXmlTopRight .= $strdiv . $this->_generalHtmlRow($row, $pathXmlTag, $p_arr_item_value, $arrData, $p_view_mode, $readonly) . '</div>';
            } else {
                $sContentXmlBottom .= $strdiv . $this->_generalHtmlRow($row, $pathXmlTag, $p_arr_item_value, $arrData, $p_view_mode, $readonly) . '</div>';
            }
        }
        if ($v_js_file_name != '' && !(is_null($v_js_file_name))) {
            $spHtmlStr .= G_Gen::getInstance()->_gCssJs('', 'js/record', $v_js_file_name, ',', 'js');
        }
        if ($v_js_function != '' && !(is_null($v_js_function))) {
            $spHtmlStr .= '<script>try{' . $v_js_function . '}catch(e){;}</script>';
        }
        $sContentXmlTopLeft .= '</div>';
        $sContentXmlTopRight .= '</div>';
        $sContentXmlTop .= $sContentXmlTopLeft . $sContentXmlTopRight . '</div>';
        $sContentXmlBottom .= '</div>';
        if ($sContentXmlTop != '<div id = "Top_contentXml"><div id = "Topleft_contentXml"></div><div id = "Topright_contentXml"></div></div>') {
            $spHtmlStr .= $sContentXmlTop;
            $spHtmlStr .= '<script type="text/javascript">$(function(){$(\'#Top_contentXml\').equalHeights();});</script>';
        }
        if ($sContentXmlBottom != '<div id="Bottom_contentXml"></div>')
            $spHtmlStr .= $sContentXmlBottom;
        $spHtmlStr .= '<style> #Bottom_contentXml div label{width:' . $v_first_col_width . ';} #Top_contentXml div label{width:' . ($v_first_col_width * 2) . '%;}</style>';
        return $spHtmlStr;
    }

    /**
     * @param $row
     * @param $pathXmlTag
     * @param $p_arr_item_value
     * @param $arrData
     * @param bool $p_view_mode
     * @param bool $readonly
     * @return string
     */
    private function _generalHtmlRow($row, $pathXmlTag, $p_arr_item_value, $arrData, $p_view_mode = false, $readonly = false)
    {
        //Start
        $sRowHtmlStr = '';
        if (isset($row["tag_list"])) {
            $v_tag_list = $row["tag_list"];
        } else {
            return '';
        }
        $arr_tag = explode(",", $v_tag_list);
        $iCountTag = sizeof($arr_tag);
        //Tao mang luu thong tin cua cac phan tu tren form
        $arrTable_rows = $this->getArrayElement($pathXmlTag);
        if (empty($arrTable_rows)) return '';
        for ($i = 0; $i < $iCountTag; $i++) {
            $arrOption = $arrTable_rows[$arr_tag[$i]];
            $sRowHtmlStr .= $this->_generateHtmlInput($arr_tag[$i], $arrOption, $p_arr_item_value, $arrData, $i, $p_view_mode, $readonly);
        }
        return $sRowHtmlStr;
    }

    /**
     * @return array
     */
    private function _getElementHaveTopLable()
    {
        $arr = array('textarea', 'multiradiobutton', 'slidegallery', 'video', 'multiplecheckbox', 'multiplecheckbox_fileattach', 'multipleradio', 'multipletextbox');
        return $arr;
    }

    /**
     * @param $sFormFielName
     * @param $sType
     * @param $arrOption
     * @param $iIndexTag
     * @return string
     */
    private function _generateHtmlLable($sFormFielName, $sType, $arrOption, $iIndexTag)
    {
        //Do rong (width) cua lable
        isset($arrOption["width_label"]) ? $widthLabel = $arrOption["width_label"] : $widthLabel = '';
        //Noi dung lable
        isset($arrOption["label"]) ? $sLabel = $arrOption["label"] : $sLabel = '&nbsp;';
        //Neu bat buoc nhap thi them * vao lable
        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        if ($optOptional == 'false') {
            $sLabel .= "<small class='normal_starmark'>*</small>";
        }
        //Class css lable
        $sClassLabel = 'normal_label';
        $arrElementHaveTopLable = $this->_getElementHaveTopLable();
        if (in_array($sType, $arrElementHaveTopLable)) {
            $sClassLabel = 'normal_label_top';
        }
        if (in_array($sType, array('checkbox', 'radio'))) {
            $sClassLabel .= ' normal_label_checkbox';
        }
        //Neu co nhieu hon 1 phan tu nam tren 1 hang
        $sStyle = $widthLabel;
        if ($iIndexTag > 0)
            $sStyle = 'float:none;' . $widthLabel;
        $psRetHtml = '<label class="' . $sClassLabel . '" style = "' . $sStyle . '"  for="' . $sFormFielName . '">' . $sLabel . '</label>';
        return $psRetHtml;
    }

    /**
     * @param $arrOption
     * @param $p_arr_item_value
     * @param $arrData
     * @return bool|mixed|string
     */
    private function _getValue($arrOption, $p_arr_item_value, $arrData)
    {
        //Lay value
        $sValue = '';
        isset($arrOption["data_format"]) ? $spDataFormat = $arrOption["data_format"] : $spDataFormat = '';
        isset($arrOption["xml_data"]) ? $sXmlData = $arrOption["xml_data"] : $sXmlData = '';
        if ($sXmlData == 'true') {
            isset($arrOption["xml_tag_in_db"]) ? $sXmlTagInDb = $arrOption["xml_tag_in_db"] : $sXmlTagInDb = '';
            if ($sXmlTagInDb && isset($arrData[$sXmlTagInDb]))
                $sValue = $arrData[$sXmlTagInDb];
        } else {
            isset($arrOption["column_name"]) ? $sColumnName = $arrOption["column_name"] : $sColumnName = '';
            isset($p_arr_item_value[$sColumnName]) ? $sValue = $p_arr_item_value[$sColumnName] : $sValue = '';
            if ($spDataFormat == "isdate") {
                $sValue = G_Convert::_yyyymmddToDDmmyyyy(G_Convert::_replaceBadChar($sValue));
            } else {
                $sValue = G_Convert::_replaceBadChar($sValue);
            }
        }
        return $sValue;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $p_arr_item_value
     * @param $arrData
     * @param $iIndexTag
     * @param bool $p_view_mode
     * @param bool $readonly
     * @return string
     */
    private function _generateHtmlInput($sFormFielName, $arrOption, $p_arr_item_value, $arrData, $iIndexTag, $p_view_mode = false, $readonly = false)
    {
        $spRetHtml = '';
        //Loai doi tuong
        isset($arrOption["type"]) ? $sType = strtolower($arrOption["type"]) : $sType = '';
        //Lay value
        $sValue = $this->_getValue($arrOption, $p_arr_item_value, $arrData);
        //Neu co line
        isset($arrOption["have_line_before"]) ? $v_have_line_before = $arrOption["have_line_before"] : $v_have_line_before = '';
        if ($v_have_line_before == 'true') {
            $spRetHtml .= "<table width='100%'  border='0' cellspacing='0' cellpadding='0'><tr><td><hr width='100%' color='#E1E1E1' size=0px'></td></tr></table>";
        }
        //LABLE
        $v_str_label = $this->_generateHtmlLable($sFormFielName, $sType, $arrOption, $iIndexTag);
        switch ($sType) {
            case "label";
                $spRetHtml .= $v_str_label;
                break;
            case "attachfile":
                $arrFileAttach = array();
                isset($arrOption["doctype"]) ? $doctype = $arrOption["doctype"] : $doctype = '';
                //Lay khoa chinh
                $v_primary_key = '';
                $primaryKey = $this->list_sql->primary_key;
                if ($primaryKey != '')
                    $v_primary_key = $p_arr_item_value[$primaryKey];
                if ($v_primary_key != '')
                    $arrFileAttach = self::libFileGetSingle($v_primary_key, $doctype);
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlNormalAttachfile($sFormFielName, $v_primary_key, $arrOption, $arrFileAttach, $doctype, $readonly);
                break;
            case "textbox";
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlTextbox($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly);
                break;
            case "text";
                $spRetHtml .= $v_str_label . '<span class="data">' . $sValue . '&nbsp;</span>';
                break;
            case "checkbox";
                $spRetHtml .= G_Gen::_generateHtmlCheckbox($sFormFielName, $arrOption, $sValue, $readonly) . $v_str_label;
                break;
            case "radio";
                $spRetHtml .= G_Gen::_generateHtmlRadio($sFormFielName, $arrOption, $sValue, $readonly) . $v_str_label;
                break;
            case "textarea";
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlTextarea($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly);
                break;
            case "selectbox";
                $arrListItem = $this->_getListItem($arrOption);
                isset($arrOption["selectbox_option_id_column"]) ? $selectBoxIdColumn = $arrOption["selectbox_option_id_column"] : $selectBoxIdColumn = '';
                isset($arrOption["selectbox_option_name_column"]) ? $selectBoxNameColumn = $arrOption["selectbox_option_name_column"] : $selectBoxNameColumn = '';
                isset($arrOption["selectbox_option_value_column"]) ? $selectBoxValueColumn = $arrOption["selectbox_option_value_column"] : $selectBoxValueColumn = '';
                isset($arrOption["the_first_of_id_value"]) ? $theFirstOfIdValue = $arrOption["the_first_of_id_value"] : $theFirstOfIdValue = '';
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlSelectbox($sFormFielName, $arrOption, $arrListItem, $selectBoxIdColumn, $selectBoxNameColumn, $sValue, $theFirstOfIdValue, $readonly);
                break;
            case "multiplecheckbox";
                $arrListItem = $this->_getListItem($arrOption);
                $arrOption["readonly"] = $readonly;
                $arrOption["disabled"] = $p_view_mode;
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlForMultipleCheckbox($sFormFielName, $arrListItem, $arrOption, $sValue);
                break;
            //kieu mulriplecheckbox co file dinh kem
            case "multiplecheckbox_fileattach";
                $arrListItem = $this->_getListItem($arrOption);
                isset($arrOption["checkbox_multiple_id_column"]) ? $checkBoxMultipleIdColumn = $arrOption["checkbox_multiple_id_column"] : $checkBoxMultipleIdColumn = '';
                isset($arrOption["checkbox_multiple_name_column"]) ? $checkBoxMultipleNameColumn = $arrOption["checkbox_multiple_name_column"] : $checkBoxMultipleNameColumn = '';
                $spRetHtml .= $v_str_label . '<div id="multiplecheckbox_'.$sFormFielName.'" style="width:'.$arrOption['width'].'">' . G_Gen::_generateHtmlForMultipleCheckbox_fileAttach($sFormFielName, $arrListItem, $arrOption, $sValue). '</div>';
                break;
            case "treeuser";
                $spRetHtml .= $v_str_label . G_Tree::getInstance()->_generateHtmlForTreeUser($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly);
                break;
            case "textboxorder";
                $spRetHtml .= $v_str_label . G_Gen::_generateHtmlTextboxOrder($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly);
                break;
            case "checkboxstatus";
                $spRetHtml .= G_Gen::_generateHtmlCheckbox($sFormFielName, $arrOption, $sValue, $readonly) . $v_str_label;
                break;
            default:
                $spRetHtml .= $v_str_label;
        }
        return $spRetHtml;
    }

    public static function libFileGetSingle($sRecordID, $sKeyAttach)
    {
        $objConn = new G_Db();
        $sql = "Exec sp_SysFileGetSingleList  ";
        $sql .= "'" . $sRecordID . "'";
        $sql .= ",'" . $sKeyAttach . "'";
        try {
            $arrResult = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    /**
     * @param $arrOption
     * @return array
     */
    private function _getListItem($arrOption)
    {
        isset($arrOption["input_data"]) ? $inputData = $arrOption["input_data"] : $inputData = '';
        $arrListItem = array();
        if ($inputData == "session") {
            isset($arrOption["session_name"]) ? $sessionName = $arrOption["session_name"] : $sessionName = '';
            $j = 0;
            $arrList = G_Session::getInstance()->_getSession($sessionName);
            if ($arrList) {
                foreach ($arrList as $arr_item) {
                    $arrListItem[$j] = $arr_item;
                    $j++;
                }
            }
        } elseif (strtolower($inputData) == "g4tcategory") {
            isset($arrOption["public_list_code"]) ? $publicListCode = $arrOption["public_list_code"] : $publicListCode = '';
            if ($publicListCode != '') {
                $arrListItem = G_Cache::getInstance()->getAllObjectbyListCodeFull($publicListCode, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
            }
        } else {
            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            isset($arrOption["selectbox_option_sql"]) ? $selectBoxOptionSql = str_replace("#OWNER_CODE#", $sOwnerCode, $arrOption["selectbox_option_sql"]) : $selectBoxOptionSql = '';
            $arrListItem = G_Db::getInstance()->adodbQueryDataInNameMode($selectBoxOptionSql);
        }
        return $arrListItem;
    }

    /**
     * Lay thong tin chuoi sql
     * @param $psXmlFile
     * @param $psXmlTag
     * @param $arrDefault
     * @return array
     * @throws Zend_Exception
     */
    function _xmlExportSql($psXmlFile, $psXmlTag, $arrDefault)
    {
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $arrSql_struct = $this->getArrayElement($psXmlTag);
        if (empty($arrSql_struct))
            return $arrDefault;
        // class_group
        $arrSql_struct = array_merge($arrDefault, $arrSql_struct);
        return $arrSql_struct;
    }

    // Lay du lieu tim kiem
    private function getsqlsearchdetail($pathXmlTagStruct, $psXmlTagField, $arrValue = array())
    {
        if ($pathXmlTagStruct == '' || $psXmlTagField == '') {
            return array();
        }
        $arrStructField = $this->getArrayElement($pathXmlTagStruct);
        if (empty($arrStructField))
            return array();
        if (!isset($arrStructField[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrStructField);
            $arrStructField = $arrTemp;
        }
        $arrField = $this->getArrayElement($psXmlTagField);
        if (empty($arrField))
            return array();

        $arrColumn = array();
        foreach ($arrStructField as $key1 => $value1) {
            $arrFieldList = explode(',', $value1['tag_list']);
            foreach ($arrFieldList as $key2 => $value2) {
                array_push($arrColumn, $value2);
            }
        }
        $sXmlColumnList = array();
        $sXmlTagList = array();
        $sXmlValueList = array();
        $sXmlOperatorList = array();
        $sXmlTableList = array();
        $table_name_list = array();
        foreach ($arrColumn as $key => $column) {
            $arrItem = $arrField[$column];
            isset($arrItem["column_name"]) ? $sColumnName = $arrItem["column_name"] : $sColumnName = '';
            array_push($sXmlColumnList, $sColumnName);
            isset($arrItem["data_format"]) ? $data_format = $arrItem["data_format"] : $data_format = '';
            isset($arrValue[$column]) ? $v_value = $arrValue[$column] : $v_value = '';
            if ($data_format == 'isdate') {
                $v_value = G_Convert::_ddmmyyyyToYYyymmdd($v_value);
            }
            isset($arrItem["compare_operator"]) ? $operator = G_Convert::_restoreBadChar($arrItem["compare_operator"]) : $operator = '';
            array_push($sXmlOperatorList, $operator);
            if ($operator == 'like' && $sColumnName == 'C_DATA_TEMP') {
                $v_value = G_Convert::Lower2Upper($v_value);
            }
            isset($arrItem["xml_tag_in_db"]) ? $tagName = $arrItem["xml_tag_in_db"] : $tagName = '';
            if ($arrItem['xml_data'] == 'false')
                $tagName = '';
            array_push($sXmlTagList, $tagName);
            isset($arrItem["table_name"]) ? $table_name = $arrItem["table_name"] : $table_name = '';
            array_push($sXmlTableList, $table_name);
            if (!in_array($table_name, $table_name_list)) {
                array_push($table_name_list, $table_name);
            }
            array_push($sXmlValueList, $v_value);
        }
        return array(
            'sXmlColumnList' => implode(',', $sXmlColumnList),
            'sXmlValueList' => implode(',', $sXmlValueList),
            'sXmlOperatorList' => implode(',', $sXmlOperatorList),
            'sXmlTagList' => implode(',', $sXmlTagList),
            'sXmlTableList' => implode(',', $sXmlTableList),
            'table_name_list' => implode(',', $table_name_list),
        );
    }

    /**
     * Lay dieu kien khac cho chuoi query lay du lieu
     * @param $arrSwitchData
     * @param $v_value
     * @return string
     */
    private function getclauseorther($arrSwitchData, $v_value)
    {
        foreach ($arrSwitchData as $key => $arrData) {
            if ($arrData['v_value'] == $v_value) {
                return $arrData['v_clause_list'];
            }
        }
        return '';
    }

    /**
     * @param $psXmlFile
     * @param $psXmlStructField
     * @param $psXmlTagField
     * @param $psXmlSql
     * @param $psXmlBody
     * @param array $arrValue
     * @param string $sXmlStuctFiledExpand
     * @param string $sXmlTagFiledExpand
     * @param bool $odebug
     * @return string
     */
    public function _xmlExportSqlQuery($psXmlFile, $psXmlStructField, $psXmlTagField, $psXmlSql, $psXmlBody, $arrValue = array(), $sXmlStuctFiledExpand = '', $sXmlTagFiledExpand = '', $odebug = false)
    {
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        // Tao mang lu thong tin lay du lieu
        $arrBody = $this->getArrayElement($psXmlBody);
        if (empty($arrBody))
            return '';
        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);
        if (empty($arrSql))
            return '';
        $dbConnect = new G_Db();
        //Tao mang luu cac phan tu tren form
        $arrBodyHidden = $this->getArrayElement('list_of_object/list_body_hidden/col');
        if (!empty($arrBodyHidden)) {
            if (!isset($arrBodyHidden[0])) {
                $arrTemp = array();
                array_push($arrTemp, $arrBodyHidden);
                $arrBodyHidden = $arrTemp;
            }
            foreach ($arrBodyHidden as $key => $value) {
                array_push($arrBody, $value);
            }
        }

        // Tao mang luu thong tin phan loai du lieu
        $arrSwitchData = $this->getArrayElement('list_of_object/switch_data/item');
        $multiclause = false;
        if (!empty($arrSwitchData))
            $multiclause = true;
        if (!isset($arrSwitchData[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrSwitchData);
            $arrSwitchData = $arrTemp;
        }

        $type = (isset($arrSql['type']) ? $arrSql['type'] : '');
        $primaryKey = (isset($arrSql['primary_key']) ? $arrSql['primary_key'] : '');
        $input_table = (isset($arrSql['input_table']) ? $arrSql['input_table'] : '');
        $group_column = (isset($arrSql['group_column']) ? $arrSql['group_column'] : '');
        $function_group = (isset($arrSql['function_group']) ? $arrSql['function_group'] : '');
        $class_group = (isset($arrSql['class_group']) ? $arrSql['class_group'] : '');
        $parame_group = (isset($arrSql['parame_group']) ? $arrSql['parame_group'] : '');

        $groups['column_group'] = $group_column;
        $groups['function_group'] = $function_group;
        $groups['class_group'] = $class_group;
        $groups['parame_group'] = $parame_group;

        $according = (isset($arrSql['according']) ? $arrSql['according'] : '');
        $col_sort = '';
        $col_sort_type = '';


        $default_table_order = (isset($arrSql['default_table_order']) ? $arrSql['default_table_order'] : '');
        $default_column_order = (isset($arrSql['default_column_order']) ? $arrSql['default_column_order'] : 'DESC');
        if ($default_table_order != '' && $default_column_order != '') {
            $col_sort = $default_table_order . '.' . $default_column_order;
            $col_sort_type = $according;
        }

        $sGroupColumn = '';
        if ($input_table != '' && $group_column != '')
            $sGroupColumn = $input_table . '.' . $group_column;

        if ($type == 'SP') {
            //
            $sp_name = $arrSql['sp_name'];
            $list_parame = $arrSql['list_parame'];
            // Check lay thong tin phan loai du lieu
            if ($multiclause) {
                $namemodeswitch = (isset($arrValue['namemodeswitch']) ? $arrValue['namemodeswitch'] : '');
                if ($namemodeswitch != '') {
                    $list_parame = $this->getclauseorther($arrSwitchData, $namemodeswitch);
                }
            }
            $sXmlColumnList = '';
            $sXmlTagList = '';
            $sXmlValueList = '';
            $sXmlOperatorList = '';
            $sXmlTableList = '';
            $table_name_list = '';
            // Tim kiem
            $arrSearchDetail = $this->getsqlsearchdetail($psXmlStructField, $psXmlTagField, $arrValue);
            if (!empty($arrSearchDetail)) {
                $char = ($sXmlColumnList != '' ? ',' : '');
                $sXmlColumnList .= $char . $arrSearchDetail['sXmlColumnList'];
                $sXmlValueList .= $char . $arrSearchDetail['sXmlValueList'];
                $sXmlOperatorList .= $char . $arrSearchDetail['sXmlOperatorList'];
                $sXmlTagList .= $char . $arrSearchDetail['sXmlTagList'];
                $sXmlTableList .= $char . $arrSearchDetail['sXmlTableList'];
                $table_name_list .= $char . $arrSearchDetail['table_name_list'];
            }
            // Tim kiem mo rong
            $arrSearchDetail = $this->getsqlsearchdetail($sXmlStuctFiledExpand, $sXmlTagFiledExpand, $arrValue);
            // var_dump($arrSearchDetail); die;
            if (!empty($arrSearchDetail)) {
                $char = ($sXmlColumnList != '' ? ',' : '');
                $sXmlColumnList .= $char . $arrSearchDetail['sXmlColumnList'];
                // $sXmlValueList .= $char . $arrSearchDetail['sXmlValueList'];
                $sXmlValueList .= $char . mb_strtoupper($arrSearchDetail['sXmlValueList'], 'UTF-8');
                $sXmlOperatorList .= $char . $arrSearchDetail['sXmlOperatorList'];
                $sXmlTagList .= $char . $arrSearchDetail['sXmlTagList'];
                $sXmlTableList .= $char . $arrSearchDetail['sXmlTableList'];
                $table_name_list .= $char . $arrSearchDetail['table_name_list'];
            }
            $stringGetData = $this->stringGetData($arrBody, $arrValue);
            //
            $sOrderColumn = '';
            $sSort = '';
            if (isset($arrValue['hdn_columnName']) && isset($arrValue['hdn_tablename'])) {
                $sOrderColumn = $arrValue['hdn_tablename'] . '.' . $arrValue['hdn_columnName'];
            }
            if (isset($arrValue['hdn_orderby'])) {
                $sSort = $arrValue['hdn_orderby'];
            }
            if ($sOrderColumn == '') {
                $sOrderColumn = $col_sort;
                $sSort = $col_sort_type;
            }
            $iCurrentPage = '1';
            $iNumberRecordPerPage = '15';
            if (isset($arrValue['hdn_current_page']))
                $iCurrentPage = $arrValue['hdn_current_page'];
            if (isset($arrValue['hdn_record_number_page']))
                $iNumberRecordPerPage = $arrValue['hdn_record_number_page'];
            $iStar = '';
            if (isset($arrValue['hdn_star']))
                $iStar = $arrValue['hdn_star'];
            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            $sSql = "Exec  ";
            $sSql .= $sp_name;
            $sSql .= " " . $dbConnect->qstr($sXmlColumnList);
            $sSql .= "," . $dbConnect->qstr($sXmlValueList);
            $sSql .= "," . $dbConnect->qstr($sXmlOperatorList);
            $sSql .= "," . $dbConnect->qstr($sXmlTagList);
            $sSql .= "," . $dbConnect->qstr($sXmlTableList);
            $sSql .= "," . $dbConnect->qstr($table_name_list);
            $sSql .= "," . $stringGetData;
            $sSql .= "," . $dbConnect->qstr($primaryKey);
            $list_parame = G_Convert::_restoreBadChar($list_parame);
            // $list_parame = str_replace("'", "''", $list_parame);
            $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
            $list_parame = str_replace("#staff_id#", $userIdentity->PkStaff, $list_parame);
            $department_id = $userIdentity->FkUnit;
            $list_parame = str_replace("#department_id#", $department_id, $list_parame);
            $sSql .= "," . $dbConnect->qstr($list_parame);
            $sSql .= "," . $dbConnect->qstr($sGroupColumn);
            $sSql .= "," . $dbConnect->qstr($sOrderColumn);
            $sSql .= "," . $dbConnect->qstr($sSort);
            $sSql .= "," . $dbConnect->qstr($iCurrentPage);  // Trang hien tai
            $sSql .= "," . $dbConnect->qstr($iNumberRecordPerPage); // Tong so ho so/ trang
            $sSql .= "," . $dbConnect->qstr($userIdentity->PkStaff);
            $sSql .= "," . $dbConnect->qstr($iStar);
        } else {
            // SQL
            $sSql = $arrSql['string_sql'];
            $arrStructField = $this->getArrayElement($psXmlStructField);
            if (empty($arrStructField))
                return array();
            if (!isset($arrStructField[0])) {
                $arrTemp = array();
                array_push($arrTemp, $arrStructField);
                $arrStructField = $arrTemp;
            }
            $arrColumn = array();
            foreach ($arrStructField as $key1 => $value1) {
                $arrFieldList = explode(',', $value1['tag_list']);
                foreach ($arrFieldList as $key2 => $value2) {
                    array_push($arrColumn, $value2);
                }
            }
            foreach ($arrColumn as $key => $value) {
                $replace = '';
                if (isset($arrValue[$value]))
                    $replace = $arrValue[$value];
                $sSql = str_replace('#' . $value . '#', $replace, $sSql);
            }
        }
        // return $sSql;
        if ($odebug) {
            return $sSql;
        }
        // echo $sSql; die();
        $arrResult = $dbConnect->adodbQueryDataInNameMode($sSql);
        if ($arrResult)
            $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        return $arrResult;
    }

    // Xuat ra xau SQL GetALlByListID
    public function _xmlExportSqlGetAllByID($psXmlFile, $sListID, $psXmlSql, $psXmlBody, $arrValue = array(), $odebug = false)
    {
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        // Tao mang lu thong tin lay du lieu
        $arrBody = $this->getArrayElement($psXmlBody);
        if (empty($arrBody)) return '';
        $arrBodyHidden = $this->getArrayElement('list_of_object/list_body_hidden/col');
        if (!empty($arrBodyHidden)) {
            if (!isset($arrBodyHidden[0])) {
                $arrTemp = array();
                array_push($arrTemp, $arrBodyHidden);
                $arrBodyHidden = $arrTemp;
            }
            foreach ($arrBodyHidden as $key => $value) {
                array_push($arrBody, $value);
            }
        }
        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);
        if (empty($arrSql)) return '';

        $type = (isset($arrSql['type']) ? $arrSql['type'] : '');
        $primaryKey = (isset($arrSql['primary_key']) ? $arrSql['primary_key'] : '');
        $input_table = (isset($arrSql['input_table']) ? $arrSql['input_table'] : '');
        $group_column = (isset($arrSql['group_column']) ? $arrSql['group_column'] : '');
        $function_group = (isset($arrSql['function_group']) ? $arrSql['function_group'] : '');
        $class_group = (isset($arrSql['class_group']) ? $arrSql['class_group'] : '');
        $parame_group = (isset($arrSql['parame_group']) ? $arrSql['parame_group'] : '');

        $groups['column_group'] = $group_column;
        $groups['function_group'] = $function_group;
        $groups['class_group'] = $class_group;
        $groups['parame_group'] = $parame_group;

        $default_table_order = (isset($arrSql['default_table_order']) ? $arrSql['default_table_order'] : '');
        $default_column_order = (isset($arrSql['default_column_order']) ? $arrSql['default_column_order'] : 'DESC');
        $according = (isset($arrSql['according']) ? $arrSql['according'] : '');
        $col_sort = '';
        $col_sort_type = '';
        if ($default_table_order != '' && $default_column_order != '') {
            $col_sort = $default_table_order . '.' . $default_column_order;
            $col_sort_type = $according;
        }

        $sGroupColumn = '';
        if ($input_table != '' && $group_column != '')
            $sGroupColumn = $input_table . '.' . $group_column;
        $dbConnect = new G_Db();
        if ($type == 'SP') {
            //
            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sp_name = $arrSql['sp_name'];
            $list_parame = $arrSql['list_parame'];
            $stringGetData = $this->stringGetData($arrBody, $arrValue);
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            $sSql = $sp_name . ' ';
            $sSql .= $dbConnect->qstr($sListID);
            $sSql .= "," . $stringGetData;
            $sSql .= "," . $dbConnect->qstr($primaryKey);
            $list_parame = G_Convert::_restoreBadChar($list_parame);
            // $list_parame = str_replace("'", "''", $list_parame);
            $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
            $list_parame = str_replace("#staff_id#", $userIdentity->PkStaff, $list_parame);
            $list_parame = str_replace("#department_id#", $userIdentity->FkUnit, $list_parame);
            $sSql .= "," . $dbConnect->qstr($list_parame);
            $sSql .= "," . $dbConnect->qstr($sGroupColumn);
            $sSql .= "," . $dbConnect->qstr($col_sort);
            $sSql .= "," . $dbConnect->qstr($col_sort_type);
            $sSql .= "," . $dbConnect->qstr($userIdentity->PkStaff);
        } else {
            // SQL
            $sSql = $arrSql['string_sql'];
        }
        if ($odebug)
            return $sSql;
        $arrResult = $dbConnect->adodbQueryDataInNameMode($sSql);
        $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        return $arrResult;
    }

    // Chuoi SQL Update
    public function getParamxml($psXmlFile, $psXmlStructField, $psXmlTagField, $psXmlSql, $arrValue = array(), $opUpdateDataTemp = 0, $systemWorkType = 0)
    {
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $arrStructField = $this->getArrayElement($psXmlStructField);
        if (empty($arrStructField)) return '';
        if (!isset($arrStructField[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrStructField);
            $arrStructField = $arrTemp;
        }

        //Tao mang luu cac phan tu tren form
        $arrField = $this->getArrayElement($psXmlTagField);
        if (empty($arrField)) return '';

        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);
        if (empty($arrSql)) return '';

        $arrColumn = array();
        foreach ($arrStructField as $key1 => $value1) {
            $arrFieldList = explode(',', $value1['tag_list']);
            foreach ($arrFieldList as $key2 => $value2) {
                array_push($arrColumn, $value2);
            }
        }
        $type = $arrSql['type'];
        $primaryKey = $arrSql['primary_key'];
        $valueKey = (isset($arrValue[$primaryKey]) ? $arrValue[$primaryKey] : '');
        // Khai bao cac bien
        $sDelimiter = '~!@#';
        if ($type == 'SP') {
            $sp_name = $arrSql['sp_name'];
            $list_parame = $arrSql['list_parame'];
            $sXmlColumnList = '';
            $sXmlValueList = '';
            $sDataSearch = '';
            $arrFieldXml = array();
            $sFileNameAttachList = '';
            foreach ($arrColumn as $key => $column) {
                $v_type = strtolower($arrField[$column]['type']);
                if ($v_type != 'label' && $v_type != 'attachfile' && $v_type != 'slidegallery' && $v_type != 'video') {
                    $sColumnName = '';
                    if (isset($arrField[$column]['column_name']))
                        $sColumnName = $arrField[$column]['column_name'];
                    // ColumnName khac rong
                    if ($sColumnName != '') {
                        $xml_data = (isset($arrField[$column]['xml_data']) ? $arrField[$column]['xml_data'] : 'false');
                        $data_format = (isset($arrField[$column]['data_format']) ? $arrField[$column]['data_format'] : '');
                        $v_value = '';
                        if (isset($arrValue[$column]))
                            $v_value = $arrValue[$column];
                        $v_value = G_Convert::_replaceBadChar($v_value);
                        // Search
                        $data_temp = (isset($arrField[$column]['data_temp']) ? 'true' : 'false');
                        if ($data_temp == 'true') {
                            $sDataSearch .= ' ' . G_Convert::Lower2Upper($v_value) . ' ';
                        }
                        // Value
                        if ($data_format == 'isdate') {
                            $v_value = $arrValue[$column] = G_Convert::_ddmmyyyyToYYyymmdd($arrValue[$column]);
                        }
                        if (strtolower($xml_data) === 'true') {
                            // Chuoi xml
                            $arrTemp = array();
                            $arrTemp[$sColumnName] = $arrField[$column];
                            $arrTemp[$sColumnName]['tag_id'] = $column;
                            array_push($arrFieldXml, $arrTemp);
                        } else {
                            // Chuoi column name
                            $sXmlColumnList .= $sColumnName . $sDelimiter;
                            $sXmlValueList .= $v_value . $sDelimiter;
                        }
                    }
                }
            }
            $sFileAttachList = (isset($arrValue['sFileAttachList']) ? $arrValue['sFileAttachList'] : '');
            $sDocTypeList = (isset($arrValue['sDocTypeList']) ? $arrValue['sDocTypeList'] : '');
            $sDelimitor = (isset($arrValue['sDelimitor']) ? $arrValue['sDelimitor'] : '');
            $hdn_file_deleted_list = (isset($arrValue['sUnFileAttachList']) ? $arrValue['sUnFileAttachList'] : '');
            $locationList = (isset($arrValue['locationList']) ? $arrValue['locationList'] : '');
            // Dinh kem file
            $sFileNameAttachList = '';
            if ($sFileAttachList != '' && $sDocTypeList != '') {
                // Upload file dinh kem
                $sDir = G_Global::getInstance()->dirSaveFile;
//                $sFileNameAttachList = G_Lib::getInstance()->_uploadFileAttachList($sDocTypeList, $sFileAttachList, $sDir, $sDelimitor, $valueKey);
                $sFileNameAttachList = G_Lib::getInstance()->_uploadFile($sDocTypeList, $sFileAttachList, $locationList, $sDir, $sDelimitor, $valueKey);
            }
            // Xoa file cu
            if ($hdn_file_deleted_list != '' && $sDelimitor != '' && $sFileNameAttachList != '') {
                $arrDeleteFileList = explode($sDelimitor, $hdn_file_deleted_list);
                $dirsavefile = realpath('io/attach-file');
                foreach ($arrDeleteFileList as $key => $value) {
                    $fileIds = explode('_', $value);
                    $href = $dirsavefile . DIRECTORY_SEPARATOR . $fileIds[0] . DIRECTORY_SEPARATOR . $fileIds[1] . DIRECTORY_SEPARATOR . $fileIds[2] . DIRECTORY_SEPARATOR . $value;
                    if (file_exists($href))
                        unlink($href);
                }
            }
            // Lay chuoi column XML
            $arrXML = $this->getStringInputXML($arrFieldXml, $arrValue, $sDelimiter);
            $sXmlColumnList .= $arrXML['sColumnXmlList'];
            $sXmlValueList .= $arrXML['sValueXmlList'];
            $sXmlColumnList = substr($sXmlColumnList, 0, strlen($sXmlColumnList) - strlen($sDelimiter));
            $sXmlValueList = substr($sXmlValueList, 0, strlen($sXmlValueList) - strlen($sDelimiter));
            $sCurrentStatusList = (isset($arrValue['hdn_current_status_list']) ? $arrValue['hdn_current_status_list'] : '');
            $sDetailtStatusList = (isset($arrValue['hdn_detailt_status_list']) ? $arrValue['hdn_detailt_status_list'] : '');
            // Tham so dau vao tien do cong viec
            $sWorkType = (isset($arrValue['hdn_work_type']) ? $arrValue['hdn_work_type'] : '');
            $auth = Zend_Auth::getInstance()->getIdentity();
            $sStaffID = $auth->PkStaff;
            $sStaffName = $auth->sName;
            $sPresident = $auth->sPositionCode;
            $sPositionName = $sPresident . ' - ' . $sStaffName;
            $sOwnerCode = (isset($auth->sDistrictWardProcess) && $auth->sDistrictWardProcess !='' ? $auth->sDistrictWardProcess : $auth->sOwnerCode);
            // Tham so khac
            if ($list_parame != '') {
                $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
                $list_parame = str_replace("#staff_id#", $sStaffID, $list_parame);
                $department_id = $auth->FkUnit;
                $list_parame = str_replace("#position_name#", $sPositionName, $list_parame);
                $list_parame = str_replace("#department_id#", $department_id, $list_parame);
                $list_parame = self::_replaceListParam($list_parame, $arrValue);
                $arrElement = explode(',', $list_parame);
                foreach ($arrElement as $key => $element) {
                    $arrUpdate = explode('=', $element);
                    $sXmlColumnList .= $sDelimiter . trim(str_replace(chr(10), '', $arrUpdate[0]));
                    $sXmlValueList .= $sDelimiter . $arrUpdate[1];
                }
            }
            // Eventable
            $uEventTableID = (isset($arrValue['hdn_event_table_id']) ? $arrValue['hdn_event_table_id'] : '');

            $dbConnect = new G_Db();

            // Cong viec tu dong
            $sSql = $sp_name .' '. $dbConnect->qstr($sXmlColumnList) . "," . $dbConnect->qstr($sXmlValueList) . "," . $dbConnect->qstr($primaryKey) . "," . $dbConnect->qstr($valueKey) . "," . $dbConnect->qstr($sDelimiter);
            $sSql .= "," . $dbConnect->qstr($sCurrentStatusList);
            $sSql .= "," . $dbConnect->qstr($sDetailtStatusList);
            $sSql .= "," . $dbConnect->qstr($sDataSearch);
            $sSql .= "," . $dbConnect->qstr($sWorkType);
            $sSql .= "," . $dbConnect->qstr($sStaffID);
            $sSql .= "," . $dbConnect->qstr($sPositionName);
            $sSql .= "," . $dbConnect->qstr($auth->sUnitName);
            $sSql .= "," . $dbConnect->qstr($systemWorkType);
            $sSql .= "," . $dbConnect->qstr($sOwnerCode);
//            $sSql .= ",'" . $auth->C_DISTRICT_WARD_PROCESS . "'";
            $sSql .= "," . $dbConnect->qstr($opUpdateDataTemp);
            $sSql .= "," . $dbConnect->qstr($sFileNameAttachList);
            $sSql .= "," . $dbConnect->qstr($sDocTypeList);
            $sSql .= "," . $dbConnect->qstr($sDelimitor);
            $sSql .= "," . $dbConnect->qstr($uEventTableID);
        } else {
            // SQL
            $sSql = $arrSql['string_sql'];
            foreach ($arrColumn as $key => $value) {
                $replace = '';
                if (isset($arrValue[$value]))
                    $replace = $arrValue[$value];
                $sSql = str_replace('#' . $value . '#', $replace, $sSql);
            }
        }
        // echo $sSql; die();
        return $sSql;
    }

    private function _ksort($arrFieldXml)
    {
        $total = sizeof($arrFieldXml);
        for ($i = 0; $i < $total - 1; $i++) {
            $columnCurent = key($arrFieldXml[$i]);
            for ($j = $i + 1; $j < $total; $j++) {
                $next = 0;
                $columnNext = key($arrFieldXml[$j]);
                if ($columnCurent === $columnNext) {
                    $next++;
                    $arrTemp = $arrFieldXml[$i + $next];
                    $arrFieldXml[$i + $next] = $arrFieldXml[$j];
                    $arrFieldXml[$j] = $arrTemp;
                }
            }
        }
        return $arrFieldXml;
    }

    // Tach du lieu thanh chuoi column và gia trị xml
    private function getStringInputXML($arrFieldXml, $arrPost, $sDelimiter)
    {
        $arrFieldXml = $this->_ksort($arrFieldXml);
        $sColumnName = '';
        $sColumnXmlList = '';
        $sValueXmlList = '';
        $sValueXml = '';
        foreach ($arrFieldXml as $keys => $FieldXml) {
            $column = key($FieldXml);
            if ($sColumnName != $column) {
                $sColumnName = $column;
                $sColumnXmlList .= $sColumnName . $sDelimiter;
                $sValueXml = '<root><data_list>';
            }
            $tagxml = $FieldXml[$column]['xml_tag_in_db'];
            // $columnName
            $tag_id = $FieldXml[$column]['tag_id'];
            $valueTagXml = $arrPost[$tag_id];
            $sValueXml .= '<' . $tagxml . '>' . $valueTagXml . '</' . $tagxml . '>';
            $nextkey = $keys + 1;
            // $nextkey = key($arrFieldXml);
            if (!isset($arrFieldXml[$nextkey]) || key($arrFieldXml[$nextkey]) != $column) {
                $sValueXml .= '</data_list></root>';
                $sValueXmlList .= $sValueXml . $sDelimiter;
            }
            next($arrFieldXml);
        }
        return array(
            'sColumnXmlList' => $sColumnXmlList,
            'sValueXmlList' => $sValueXmlList,
        );
    }

    // Chuoi SQL lay du lieu man hinh danh sach
    public function stringGetData($arrBody, $arrRequest = array())
    {
        $sOutColumnList = array();
        $sAliasList = array();
        $sOutTagList = array();
        $sOutTableList = array();
        $sOutTable = array();
        $sSqlFunctionList = array();
        $sSqlParamList = array();
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        $dbConnect = new G_Db();
        $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
        foreach ($arrBody as $key => $col) {
            $column = (isset($col['column_name']) ? $col['column_name'] : '');
            array_push($sOutColumnList, $column);
            $sAlias = (isset($col['alias_name']) ? $col['alias_name'] : $column);
            array_push($sAliasList, $sAlias);
            $tableName = (isset($col['table_name']) ? $col['table_name'] : '');
            if (!in_array($tableName, $sOutTable)) {
                array_push($sOutTable, $tableName);
            }
            array_push($sOutTableList, $tableName);
            $tagName = (isset($col['xml_tag_in_db']) ? $col['xml_tag_in_db'] : '');
            if ($col['xml_data'] == 'false')
                $tagName = '';
            array_push($sOutTagList, $tagName);

            // function sql
            $sqlfunction = (isset($col['sqlfunction']) ? $col['sqlfunction'] : '');
            $sqlparame = (isset($col['sqlparame']) ? $col['sqlparame'] : '');
            $sqlparame = str_replace("#OWNER_CODE#", $sOwnerCode, $sqlparame);
            $sqlparame = str_replace("#staff_id#", $userIdentity->PkStaff, $sqlparame);
            $sTastId = (isset($arrRequest['FkTaskWF']) ? $arrRequest['FkTaskWF'] : '');
            $sqlparame = str_replace("#FkTaskWF#", $sTastId, $sqlparame);
            $sqlparame = str_replace("'", "''", $sqlparame);
            $sqlparame = str_replace(",", "#", $sqlparame);
            array_push($sSqlFunctionList, $sqlfunction);
            array_push($sSqlParamList, $sqlparame);
        }

        $stringResult = $dbConnect->qstr(implode(',', $sOutColumnList));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sAliasList));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sOutTableList));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sOutTagList));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sOutTable));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sSqlFunctionList));
        $stringResult .= "," . $dbConnect->qstr(implode(',', $sSqlParamList));
        return $stringResult;
    }

    // Xuat html button tren man hinh danh sach
    public function _xmlGenerateButton($psXmlFile, $psXmlTag, $oPermission = false)
    {
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $sHtmlString = '';
        $arrPermission = G_Auth::getInstance()->_getPermissionAll();
        $objCache = new G_Cache();
        //Tao mang luu cau truc cua form
        $arrTable_truct = $this->getArrayElement($psXmlTag);
        if (empty($arrTable_truct))
            return '';
        if (!isset($arrTable_truct[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrTable_truct);
            $arrTable_truct = $arrTemp;
        }

        foreach ($arrTable_truct as $key => $value) {
            $label = $value['label'];
            $type = $value['type'];
            $type = strtoupper($type);
            $id = $value['id'];
            $link_code = (isset($value['link_code']) ? $value['link_code'] : '');
            $link_name = (isset($value['link_name']) ? $value['link_name'] : '');
            $link_url = (isset($value['link_url']) ? $value['link_url'] : '');
            $typebutton = (isset($value['typebutton']) ? $value['typebutton'] : '');
            $listcode = (isset($value['listcode']) ? $value['listcode'] : '');
            if (!$oPermission || G_Lib::_getValuesByIds($arrPermission, $link_code, 'sEventCode', 'sEventCode')) {
                $js_function = G_Convert::_replaceBadChar($value['js_function']);
                if ($type == 'GROUP') {
                    $arrCode = explode(';', $link_code);
                    $arrName = explode(';', $link_name);
                    if ($typebutton == 'in') {
                        $sHtmlString .= '<a tabindex="0" href="#" class="fg-button fg-button-icon-right link-button elmLink printbutton"';
                        $sHtmlString .= " value='$label' v_display='$type' id='$id' link_name='$id' onclick='$js_function'>";
                        $sHtmlString .= '<span class="ui-icon ui-icon-print"></span>' . $label . '</a>';
                        $sHtmlString .= '<div id="' . $id . '_printlist" style="display:none;"><ul>';
                        $arrCode = explode(',', $listcode);
                        $count = sizeof($arrCode);
                        $arrTemplate = $objCache->getAllObjectbyListCodeFull('MAU_IN', Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
                        for ($i = 0; $i < $count; $i++) {
                            $name = G_Lib::_getValuesByIds($arrTemplate, $arrCode[$i], 'sName', 'sCode');
                            $sHtmlString .= '<li style="line-height: 25px;border-bottom: 1px dashed #d9d9d9;"><label style="width: 100%;" for="' . $arrCode[$i] . '"><input onclick="selectprinttemp(this);" id="' . $arrCode[$i] . '" class="checkbox" type="checkbox" value="' . $arrCode[$i] . '" name="print_checkbox"><span onclick="selectprinttemp(this);" style="padding: 0 0 0 10px;cursor: pointer;">' . $name . '</span></label>';
                            //$sHtmlString .= '<a class="view_print">Xem mẫu</a>';
                            $sHtmlString .= '</li>';
                        }
                        $sHtmlString .= '</ul></div>';
                    } else {
                        $sHtmlString .= '<a tabindex="0" href="#" class="fg-button fg-button-icon-right link-button elmLink menudropdown"';
                        $sHtmlString .= " value='$label' v_display='$type' id='$id' frm_field='$link_code' link_name='$link_name' link_url='$link_url'>";
                        $sHtmlString .= '<span class="ui-icon ui-icon-triangle-1-s"></span>' . $label . '</a>';
                        $sHtmlString .= '<div id="search-engines" class="hidden"><ul>';
                        $count = sizeof($arrCode);
                        $arrLink = explode(';', $link_url);
                        for ($i = 0; $i < $count; $i++) {
                            $idlinkcode = $arrCode[$i];
                            $sHtmlString .= '<li><a id="' . $idlinkcode . '" href="#" url="' . $arrLink[$i] . '" onclick="' . $js_function . '">' . $arrName[$i] . '</a></li>';
                        }
                        $sHtmlString .= '</ul></div>';
                    }
                } else {
                    $sHtmlString .= '<input class="link-button elmLink" type="button" ';
                    $sHtmlString .= " value='$label' codetemp='$listcode' v_display='$type' id='$id' frm_field='$link_code' link_name='$link_name' onclick='$js_function' link_url='$link_url'>";
                }
            }
        }
        return $sHtmlString;
    }

    // Cau truc menu
    public function _xmlGenerateMenu($psXmlFile, $psXmlTag, $psXmlMenuItem)
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        if (isset($this->infor_other)) {
            $arrType = $this->infor_other->toArray();
            $style_menu = $arrType['menu_type'];
        } else {
            $style_menu = 'TOP';
        }

        $sHtmlHeader = '';
        // Tao doi tuong trong thu vien dung trung
        $objLib = New G_Lib();

        //Tao mang luu cau truc cua form
        $arrMenu_struct = $this->getArrayElement($psXmlTag);
        if (!isset($arrMenu_struct[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrMenu_struct);
            $arrMenu_struct = $arrTemp;
        }
        //Tao mang luu cau truc cua form
        $arrMenu_items = $this->getArrayElement($psXmlMenuItem);
        //
        $sHtmlLeft = '';
        $jsHeader = '';
        $jsLeft = '';
        $sHtmlSubmenuTop = '';
        $jsSubmenuTop = '';
        foreach ($arrMenu_struct as $key => $arrMenuStruct) {
            $menu_name = $arrMenuStruct['menu_name'];
            $tag_list = $arrMenuStruct['tag_list'];
            $menu_code = $arrMenuStruct['menu_code'];
            $menu_url = $arrMenuStruct['menu_url'];
            $arrTagList = explode(',', $tag_list);
            $countTag = sizeof($arrTagList);
            $submenu_code = '';
            $sHtmlHeader .= '<!--' . $menu_name . '-->' . chr(10);
            $sHtmlHeader .= '<?php $urlRe = $this->baseUrl . "../M' . $menu_url . '";?>' . chr(10);
            $sHtmlHeader .= '<li id="' . $menu_code . '" href="<?=$urlRe;?>"  onclick="selectLeft(\'<?=$urlRe;?>\',\'' . $menu_code . '\',\'' . $submenu_code . '\')" >' . $menu_name . '</li>' . chr(10);

            $jsHeader .= 'case "' . $menu_code . '":' . chr(10);
            $jsHeader .= chr(9) . 'document.getElementById("' . $menu_code . '").className="selected";' . chr(10);
            $jsHeader .= chr(9) . 'break;' . chr(10);
            $sHtmlLeft .= '<div style="margin:3px 3px 0 3px">' . chr(10);
            $sHtmlLeft .= chr(9) . '<ul id = "left_' . $menu_code . '">' . chr(10);
            $jsLeft .= 'if (CurrentModulCode == "' . $menu_code . '"){' . chr(10);
            $jsLeft .= 'document.getElementById("left_' . $menu_code . '").style.display = "block";' . chr(10);
            $jsLeft .= 'switch(currentModulCodeForLeft){' . chr(10);
            // SubmenuTop
            $sHtmlSubmenuTop .= '<ul id = "sub_top_' . $menu_code . '" class = "sub_top_menu">' . chr(10);
            $jsSubmenuTop .= 'case "' . $menu_code . '":' . chr(10);
            $jsSubmenuTop .= '$("ul#sub_top_' . $menu_code . '").show();';
            $jsSubmenuTop .= 'switch(currentModulCodeForLeft){' . chr(10);

            for ($i = 0; $i < $countTag; $i++) {
                $tagSubMenu = $arrTagList[$i];
                $submenu_name = $arrMenu_items[$tagSubMenu]['submenu_name'];
                $submenu_code = $arrMenu_items[$tagSubMenu]['submenu_code'];
                $submenu_url = $arrMenu_items[$tagSubMenu]['submenu_url'];
                $subMenu = '';
                $jsSubmenu = '';
                $subMenu .= chr(9) . chr(9) . '<li id = "' . $submenu_code . '">' . chr(10);
                $subMenu .= chr(9) . chr(9) . chr(9) . '<a href="<?php echo $this->baseUrl; ?>../M' . $submenu_url . '" onclick="{selectLeft(this,\'' . $menu_code . '\',\'' . $submenu_code . '\');return false;}" >' . $submenu_name . '</a>';
                $subMenu .= chr(9) . chr(9) . '</li>' . chr(10);
                $sHtmlLeft .= $subMenu;
                // JS
                $jsSubmenu .= chr(9) . 'case "' . $submenu_code . '":' . chr(10);
                $jsSubmenu .= chr(9) . chr(9) . 'document.getElementById("' . $submenu_code . '").className = "visited";' . chr(10);
                $jsSubmenu .= chr(9) . chr(9) . 'break;' . chr(10);
                //
                $jsLeft .= $jsSubmenu;
                // SubmenuTop
                $sHtmlSubmenuTop .= $subMenu;
                $jsSubmenuTop .= $jsSubmenu;
            }
            $sHtmlLeft .= chr(9) . '</ul>' . chr(10) . '</div>' . chr(10);
            $jsLeft .= chr(9) . '}' . chr(10) . '}' . chr(10);
            //
            $sHtmlSubmenuTop .= '</ul>';
            $jsSubmenuTop .= chr(9) . '}' . chr(10) . chr(9) . ' break;' . chr(10);
        }
        // Doc template file header
        $dirTemplateHeader = realpath('public/templates/system/') . '/' . 'header.phtml';
        $contentHeader = $objLib->_readFile($dirTemplateHeader);
        //
        $contentHeader = str_replace('#START_SYSTEM_MANAGER#', '', $contentHeader);
        $contentHeader = str_replace('#END_SYSTEM_MANAGER#', '', $contentHeader);

        $contentHeader = str_replace('#LI_HEADER#', $sHtmlHeader, $contentHeader);
        $contentHeader = str_replace('#JS_HEADER#', $jsHeader, $contentHeader);
        // Xuat file header
        $sPathFile = 'application\views\scripts//';
        $objLib->_mkdir($sPathFile);
        $sPathFile .= 'header.phtml';
        $objLib->_writeFile($sPathFile, $contentHeader);
        if ($style_menu == 'LEFT') {
            // Doc template file left
            $dirTemplateLeft = realpath('public/templates/system/') . '/' . 'left.phtml';
            $contentLeft = $objLib->_readFile($dirTemplateLeft);
            //
            $contentLeft = str_replace('#START_SYSTEM_MANAGER#', '', $contentLeft);
            $contentLeft = str_replace('#END_SYSTEM_MANAGER#', '', $contentLeft);

            $contentLeft = str_replace('#DIVLEFT#', $sHtmlLeft, $contentLeft);
            $contentLeft = str_replace('#JS_LEFT#', $jsLeft, $contentLeft);
            // Left menu
            $sPathFile = 'application\views\scripts//';
            $objLib->_mkdir($sPathFile);
            $sPathFile .= 'left.phtml';
            $objLib->_writeFile($sPathFile, $contentLeft);
            // File layout
            $sPathLayout = realpath('public/templates/system/') . '/' . 'menu_left.phtml';
        } else {
            // Doc template file top menu
            $dirTemplateTop = realpath('public/templates/system/') . '/' . 'menutop.phtml';
            $contentTop = $objLib->_readFile($dirTemplateTop);
            //
            $contentTop = str_replace('#START_SYSTEM_MANAGER#', '', $contentTop);
            $contentTop = str_replace('#END_SYSTEM_MANAGER#', '', $contentTop);

            $contentTop = str_replace('#DIVSUBMENU#', $sHtmlSubmenuTop, $contentTop);
            $contentTop = str_replace('#JS_SUBMENU#', $jsSubmenuTop, $contentTop);
            // Top menu
            $sPathFile = 'application\views\scripts//';
            $objLib->_mkdir($sPathFile);
            $sPathFile .= 'left.phtml';
            $objLib->_writeFile($sPathFile, $contentTop);
            // File layout
            $sPathLayout = realpath('public/templates/system/') . '/' . 'menu_top.phtml';
        }
        // Xuat file layout
        $contentLayout = $objLib->_readFile($sPathLayout);
        $sPathLayoutexp = 'application\layout\index.phtml';
        $objLib->_writeFile($sPathLayoutexp, $contentLayout);
        return true;
    }

    // Lay current menu view
    public function _xmlGetModuleCurrent($moduleid, $controllerid)
    {
        return array('currentModulCode' => $moduleid,
                                  'currentModulCodeForLeft' => $moduleid . '_' . $controllerid . '_index');
        $sPath = G_Global::getInstance()->dirXml . 'system/menu/struct_menu.xml';
        $arrMenu = $this->parseMenu($sPath, 'menu_struct/menu_row', 'menu_list');
        foreach ($arrMenu as $key => $menu) {
            foreach ($menu['submenu'] as $key => $subMenu) {
                $items = explode('/', $subMenu['submenu_url']);
                $module =  $items[0];
                if ($module == $moduleid && $subMenu['submenu_code'] == $controllerid) {
                    return array('currentModulCode' => $menu['menu_code'],
                                  'currentModulCodeForLeft' => $moduleid . '_' . $subMenu['submenu_code']);
                }
            }
        }
        return array('currentModulCode' =>'', 'currentModulCodeForLeft' => '');
    }

    // SQL GetSingle
    public function getSqlGetSingle($psXmlFile, $psXmlStructField, $psXmlTagField, $psXmlSql, $valueKey, $sp_name = 'sp_KntcRecordGetSingle', $odebug = false)
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }

        //Tao mang luu cau truc cua form
        $arrStructField = $this->getArrayElement($psXmlStructField);
        if (!isset($arrStructField[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrStructField);
            $arrStructField = $arrTemp;
        }

        //Tao mang luu cac phan tu tren form
        $arrField = $this->getArrayElement($psXmlTagField);
        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);

        $arrColumn = array();
        foreach ($arrStructField as $key1 => $value1) {
            $arrFieldList = explode(',', $value1['tag_list']);
            foreach ($arrFieldList as $key2 => $value2) {
                array_push($arrColumn, $value2);
            }
        }
        $primaryKey = $arrSql['primary_key'];
        $sXmlColumnList = '';
        $sXmlTagList = '';
        $sXmlTableList = '';
        foreach ($arrColumn as $key => $column) {
            $sColumnName = '';
            $v_type = strtolower($arrField[$column]['type']);
            if ($v_type != 'label' && $v_type != 'attachfile' && $v_type != 'slidegallery' && $v_type != 'video') {
                if (isset($arrField[$column]['column_name']))
                    $sColumnName = $arrField[$column]['column_name'];

                if ($sColumnName != '') {
                    $sXmlColumnList .= $sColumnName . ',';
                    $tagName = '';
                    if (isset($arrField[$column]['xml_tag_in_db']))
                        $tagName = $arrField[$column]['xml_tag_in_db'];
                    if ($arrField[$column]['xml_data'] == 'false') {
                        $tagName = '';
                        // echo '1'.'<br>';
                    }
                    $sXmlTagList .= $tagName . ',';
                    $table_name = '';
                    if (isset($arrField[$column]['table_name']))
                        $table_name = $arrField[$column]['table_name'];
                    $sXmlTableList .= $table_name . ',';
                }
            }
        }
        $sXmlColumnList = substr($sXmlColumnList, 0, strlen($sXmlColumnList) - 1);
        $sXmlTagList = substr($sXmlTagList, 0, strlen($sXmlTagList) - 1);
        $sXmlTableList = substr($sXmlTableList, 0, strlen($sXmlTableList) - 1);
        $sSql = $sp_name . " '" . $sXmlColumnList . "','" . $sXmlTagList . "','" . $sXmlTableList . "','" . $primaryKey . "','" . $valueKey . "'";
        if ($odebug) {
            return $sSql;
        }
        $arrGetSingle = array();
        try {
            $arrGetSingle = G_Db::getInstance()->adodbExecSqlString($sSql);

        } catch (Exception $e) {
            ;
        }
        foreach ($arrGetSingle as $key => $value) {
            $arrGetSingle[$key] = G_Convert::_restoreBadChar($value);
        }
        return $arrGetSingle;
    }

    /**
     * @param $psXmlFile
     * @param $psXmlStructField
     * @param $psXmlTagField
     * @param $psXmlSql
     * @param $psXmlBody
     * @param array $arrValue
     * @param string $sXmlStuctFiledExpand
     * @param string $sXmlTagFiledExpand
     * @param bool $odebug
     * @param array $arrParam
     * @param string $delimetorFile
     * @param string $replaceParamFn
     * @return array|string
     */
    public function _xmlExportSqlQueryHidden($psXmlFile, $psXmlStructField, $psXmlTagField, $psXmlSql, $psXmlBody, $arrValue = array(), $sXmlStuctFiledExpand = '', $sXmlTagFiledExpand = '', $odebug = false, $arrParam = array(), $delimetorFile = ',', $replaceParamFn = '_replaceListParam')
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        //Tao mang luu cac phan tu tren form
        $arrStructField = $this->getArrayElement($psXmlStructField);
        if (!empty($arrStructField) && !isset($arrStructField[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrStructField);
            $arrStructField = $arrTemp;
        }
        //Tao mang luu cac phan tu tren form
        $arrField = $this->getArrayElement($psXmlTagField);
        // Tao mang lu thong tin lay du lieu
        $arrBody = $this->getArrayElement($psXmlBody);
        if (empty($arrBody))
            return '';
        $dbConnect = new G_Db();
        $arrBodyHidden = $this->getArrayElement('list_of_object/list_body_hidden/col');
        if (!empty($arrBodyHidden)) {
            if (!isset($arrBodyHidden[0])) {
                $arrTemp = array();
                array_push($arrTemp, $arrBodyHidden);
                $arrBodyHidden = $arrTemp;
            }
            foreach ($arrBodyHidden as $key => $value) {
                array_push($arrBody, $value);
            }
        }
        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);
        if (empty($arrSql))
            return '';

        $arrColumn = array();
        if (!empty($arrStructField)) {
            foreach ($arrStructField as $key1 => $value1) {
                $arrFieldList = explode(',', $value1['tag_list']);
                foreach ($arrFieldList as $key2 => $value2) {
                    array_push($arrColumn, $value2);
                }
            }
        }
        $type = (isset($arrSql['type']) ? $arrSql['type'] : '');
        $primaryKey = (isset($arrSql['primary_key']) ? $arrSql['primary_key'] : '');
        $input_table = (isset($arrSql['input_table']) ? $arrSql['input_table'] : '');
        $group_column = (isset($arrSql['group_column']) ? $arrSql['group_column'] : '');
        $function_group = (isset($arrSql['function_group']) ? $arrSql['function_group'] : '');
        $class_group = (isset($arrSql['class_group']) ? $arrSql['class_group'] : '');
        $parame_group = (isset($arrSql['parame_group']) ? $arrSql['parame_group'] : '');

        $groups['column_group'] = $group_column;
        $groups['function_group'] = $function_group;
        $groups['class_group'] = $class_group;
        $groups['parame_group'] = $parame_group;

        $default_table_order = (isset($arrSql['default_table_order']) ? $arrSql['default_table_order'] : '');
        $default_column_order = (isset($arrSql['default_column_order']) ? $arrSql['default_column_order'] : 'DESC');
        $according = (isset($arrSql['according']) ? $arrSql['according'] : '');
        $col_sort = '';
        $col_sort_type = '';
        if ($default_table_order != '' && $default_column_order != '') {
            $col_sort = $default_table_order . '.' . $default_column_order;
            $col_sort_type = $according;
        }

        $sGroupColumn = '';
        if ($input_table != '' && $group_column != '')
            $sGroupColumn = $input_table . '.' . $group_column;
        if ($type == 'SP') {
            $sp_name = $arrSql['sp_name'];
            $list_parame = $arrSql['list_parame'];
            $sXmlColumnList = array();
            $sXmlTagList = array();
            $sXmlValueList = array();
            $sXmlOperatorList = array();
            $sXmlTableList = array();
            $table_name_list = array();
            if (!empty($arrColumn) && !empty($arrField)) {
                foreach ($arrColumn as $key => $column) {
                    $sColumnName = (isset($arrField[$column]['column_name']) ? $arrField[$column]['column_name'] : '');
                    array_push($sXmlColumnList, $sColumnName);
                    $v_value = (isset($arrValue[$column]) ? $arrValue[$column] : '');
                    $operator = (isset($arrField[$column]['compare_operator']) ? G_Convert::_restoreBadChar($arrField[$column]['compare_operator']) : '');
                    array_push($sXmlOperatorList, $operator);
                    $data_format = (isset($arrField[$column]['data_format']) ? $arrField[$column]['data_format'] : '');
                    if ($data_format == 'isdate') {
                        // $iSearch
                        switch ($operator) {
                            case '>':
                            case '>=':
                                $iSearch = 1;
                                break;
                            case '<':
                            case '<=':
                                $iSearch = 2;
                                break;
                            default:
                                $iSearch = '';
                                break;
                        }
                        $v_value = G_Convert::_ddmmyyyyToYYyymmdd($v_value, $iSearch);
                    }
                    // Du lieu fulltextsearch
                    if ($operator == 'like' && $sColumnName == 'C_DATA_TEMP') {
                        $v_value = G_Convert::Lower2Upper($v_value);
                    }
                    $tagName = (isset($arrField[$column]['xml_tag_in_db']) ? $arrField[$column]['xml_tag_in_db'] : '');
                    if ($arrField[$column]['xml_data'] == 'false')
                        $tagName = '';
                    array_push($sXmlTagList, $tagName);
                    $table_name = (isset($arrField[$column]['table_name']) ? $arrField[$column]['table_name'] : '');
                    // Table
                    array_push($sXmlTableList, $table_name);
                    if (!in_array($table_name, $table_name_list) && $v_value != '') {
                        array_push($table_name_list, $table_name);
                    }
                    // Value
                    array_push($sXmlValueList, $v_value);
                }
            }

            $sXmlColumnList = implode($delimetorFile, $sXmlColumnList);
            $sXmlValueList = implode(',', $sXmlValueList);
            $sXmlOperatorList = implode(',', $sXmlOperatorList);
            $sXmlTagList = implode(',', $sXmlTagList);
            $sXmlTableList = implode(',', $sXmlTableList);
            $table_name_list = implode(',', $table_name_list);
            // Tim kiem mo rong
            $arrSearchDetail = $this->getsqlsearchdetail($sXmlStuctFiledExpand, $sXmlTagFiledExpand, $arrValue);
            if (!empty($arrSearchDetail)) {
                $char = ($sXmlColumnList != '' ? ',' : '');
                $sXmlColumnList .= $char . $arrSearchDetail['sXmlColumnList'];
                $sXmlValueList .= $char . $arrSearchDetail['sXmlValueList'];
                $sXmlOperatorList .= $char . $arrSearchDetail['sXmlOperatorList'];
                $sXmlTagList .= $char . $arrSearchDetail['sXmlTagList'];
                $sXmlTableList .= $char . $arrSearchDetail['sXmlTableList'];
                $table_name_list .= $char . $arrSearchDetail['table_name_list'];
            }
            $stringGetData = $this->stringGetData($arrBody, $arrValue);
            //
            $sOrderColumn = '';
            $sSort = '';
            if (isset($arrValue['hdn_columnName']) && isset($arrValue['hdn_tablename'])) {
                $sOrderColumn = $arrValue['hdn_tablename'] . '.' . $arrValue['hdn_columnName'];
            }
            if (isset($arrValue['hdn_orderby'])) {
                $sSort = $arrValue['hdn_orderby'];
            }
            if ($sOrderColumn == '') {
                $sOrderColumn = $col_sort;
                $sSort = $col_sort_type;
            }
            $iCurrentPage = '1';
            $iNumberRecordPerPage = '15';
            if (isset($arrValue['hdn_current_page']))
                $iCurrentPage = $arrValue['hdn_current_page'];
            if (isset($arrValue['hdn_record_number_page']))
                $iNumberRecordPerPage = $arrValue['hdn_record_number_page'];
            $iStar = '';
            if (isset($arrValue['hdn_star']))
                $iStar = $arrValue['hdn_star'];
            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            $sSql = "Exec  ";
            $sSql .= $sp_name;
            $sSql .= " " . $dbConnect->qstr($sXmlColumnList);
            $sSql .= "," . $dbConnect->qstr($sXmlValueList);
            $sSql .= "," . $dbConnect->qstr($sXmlOperatorList);
            $sSql .= "," . $dbConnect->qstr($sXmlTagList);
            $sSql .= "," . $dbConnect->qstr($sXmlTableList);
            $sSql .= "," . $dbConnect->qstr($table_name_list);
            $sSql .= "," . $stringGetData;
            $sSql .= "," . $dbConnect->qstr($primaryKey);
            // $list_parame = str_replace("'", "''", $list_parame);
            $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
            $list_parame = str_replace("#staff_id#", $userIdentity->PkStaff, $list_parame);
            $list_parame = str_replace("#department_id#", $userIdentity->FkUnit, $list_parame);
            eval("\$list_parame = self::" . $replaceParamFn . "(\$list_parame,\$arrValue);");

            $sSql .= "," . $dbConnect->qstr($list_parame);
            $sSql .= "," . $dbConnect->qstr($sGroupColumn);
            $sSql .= "," . $dbConnect->qstr($sOrderColumn );
            $sSql .= "," . $dbConnect->qstr($sSort);
            $sSql .= "," . $dbConnect->qstr($iCurrentPage ); // Trang hien tai
            $sSql .= "," . $dbConnect->qstr($iNumberRecordPerPage ); // Tong so ho so/ trang
            $sSql .= "," . $dbConnect->qstr($userIdentity->PkStaff);
            $sSql .= "," . $dbConnect->qstr($iStar);
        } else {
            // SQL
            $sSql = $arrSql['string_sql'];
            foreach ($arrColumn as $key => $value) {
                $replace = '';
                if (isset($arrValue[$value]))
                    $replace = $arrValue[$value];
                $sSql = str_replace('#' . $value . '#', $replace, $sSql);
            }
        }
        foreach ($arrParam as $key => $value) {
            $sSql .= "," . $dbConnect->qstr($value);
        }
        // return $sSql;
        if ($odebug) {
            return $sSql;
        }
        // echo $sSql; die();
        $arrResult = $dbConnect->adodbQueryDataInNameMode($sSql);
        $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        return $arrResult;
    }

    // Xuat html switchdata ngoai fontend
    public function _generalSwitchData($psXmlFile, $psXmlTag, $v_select = '', $uncheck = false)
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $sHtmlString = '<ul id="tab-menu" class="gr_01">';
        //Tao mang luu cau truc cua form
        $arrTable_truct = $this->getArrayElement($psXmlTag);
        if (empty($arrTable_truct))
            return '';
        if (!isset($arrTable_truct[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrTable_truct);
            $arrTable_truct = $arrTemp;
        }
        foreach ($arrTable_truct as $key => $value) {
            $v_label = $value['v_label'];
            $v_id = $value['v_id'];
            $v_value = $value['v_value'];
            if (isset($value['v_title']) && $value['v_title'] != 'undefined')
                $v_title = $value['v_title'];
            else
                $v_title = '';
            if (isset($value['v_titleName']) && $value['v_titleName'] != 'undefined')
                $v_titleName = $value['v_titleName'];
            else
                $v_titleName = '';
            if (isset($value['dspAttr']) && $value['dspAttr'] != 'undefined')
                $dspAttr = $value['dspAttr'];
            else
                $dspAttr = '';
            $checkdefault = $value['checkdefault'];
            if ($v_select != '' && $v_value == $v_select) {
                $checked = 'checked';
                $lbcheck = 'style="color:red;cursor:pointer"';
                $active = 'active';
            } else {
                $checked = ($checkdefault == 'true' && $v_select == '' ? 'checked' : '');
                $lbcheck = ($checkdefault == 'true' && $v_select == '' ? 'style="color:red;cursor:pointer"' : 'style="cursor:pointer"');
                $active = ($checkdefault == 'true' && $v_select == '' ? 'active' : '');
            }
            if ($dspAttr == 'label') {
                $event = 'onclick="togglelabel(this)"';
                $sHtmlString .= '<li ' . $active . ' v_value="' . $v_value . '" title="' . $v_title . '" class="normal_radio ' . $active . '" >
                    <span class="icon_tab icon_Sub_thuocthamquyen_white"></span>
                    <span class="text">' . $v_label . '</span></li>';
//                $sHtmlString .= '<label ' . $active . ' value="' . $v_value . '" title="' . $v_title . '" class="normal_radio" ' . $lbcheck . ' ' . $event . ' >' . $v_label . '</label>';
            } else {
                $event = 'onclick="toggleradio(this)"';
                if ($uncheck) {
                    $event = ' for="' . $v_id . '"';
                }
                $sHtmlString .= '<input ' . $checked . ' class="normal_textbox" type="radio" id="' . $v_id . '" name="namemodeswitch" titleName="' . $v_titleName . '" value="' . $v_value . '"/>';
                $sHtmlString .= '<label title="' . $v_title . '" class="normal_radio" style="cursor:pointer;" ' . $event . ' >' . $v_label . '</label>';
            }
        }
        $sHtmlString .= '</ul>';
        return $sHtmlString;
    }

    /**
     * @param $listParam
     * @param $arrRequest
     * @return string
     */
    public function _replaceListParam($listParam, $arrRequest)
    {
        $arrParams = explode(',', $listParam);
        $newListParams = '';
        for ($i = 0; $i < count($arrParams); $i++) {
            $arrParam = explode('#', $arrParams[$i]);
            if (count($arrParam) > 1) {
                if (strstr($arrParam[1], '_ses_') != '') {
                    $newListParams .= str_replace("#" . $arrParam[1] . "#", $_SESSION[str_replace('_ses_', '', $arrParam[1])], $arrParams[$i]) . ',';
                } elseif (strstr($arrParam[1], '_req_') != '') {
                    if (isset($arrRequest[str_replace('_req_', '', $arrParam[1])]) && ($arrRequest[str_replace('_req_', '', $arrParam[1])] != 'NULL' && $arrRequest[str_replace('_req_', '', $arrParam[1])] != 'Null' && $arrRequest[str_replace('_req_', '', $arrParam[1])] != 'null')) {
                        $newListParams .= str_replace("#" . $arrParam[1] . "#", $arrRequest[str_replace('_req_', '', $arrParam[1])], $arrParams[$i]) . ',';
                    }
                } else {
                    $newListParams .= $arrParams[$i] . ',';
                }
            } else {
                $newListParams .= $arrParams[$i] . ',';
            }
        }
        if ($newListParams != '') $newListParams = substr($newListParams, 0, strlen($newListParams) - 1);
        return $newListParams;
    }

    /**
     * @param $psXmlFile
     * @param $psXmlStructField
     * @param $psXmlTagField
     * @param $psXmlSql
     * @param $psXmlBody
     * @param array $arrValue
     * @param string $sXmlStuctFiledExpand
     * @param string $sXmlTagFiledExpand
     * @param bool $odebug
     * @param array $arrParam
     * @param string $delimetorFile
     * @return array|string
     */
    public function _xmlExportSqlQueryHiddenNoDb($psXmlFile, $psXmlStructField, $psXmlTagField, $psXmlSql, $psXmlBody, $arrValue = array(), $sXmlStuctFiledExpand = '', $sXmlTagFiledExpand = '', $odebug = false, $arrParam = array(), $delimetorFile = ',')
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $arrStructField = $this->getArrayElement($psXmlStructField);
        if (!empty($arrStructField) && !isset($arrStructField[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrStructField);
            $arrStructField = $arrTemp;
        }
        //Tao mang luu cac phan tu tren form
        $arrField = $this->getArrayElement($psXmlTagField);
        // Tao mang lu thong tin lay du lieu
        $arrBody = $this->getArrayElement($psXmlBody);
        if (empty($arrBody))
            return '';
        $dbConnect = new G_Db();
        $arrBodyHidden = $this->getArrayElement('list_of_object/list_body_hidden/col');
        if (!empty($arrBodyHidden)) {
            if (!isset($arrBodyHidden[0])) {
                $arrTemp = array();
                array_push($arrTemp, $arrBodyHidden);
                $arrBodyHidden = $arrTemp;
            }
            foreach ($arrBodyHidden as $key => $value) {
                array_push($arrBody, $value);
            }
        }
        //Tao mang luu thong tin cua chuoi sql
        $arrSql = $this->getArrayElement($psXmlSql);
        if (empty($arrSql))
            return '';

        $arrColumn = array();
        if (!empty($arrStructField)) {
            foreach ($arrStructField as $key1 => $value1) {
                $arrFieldList = explode(',', $value1['tag_list']);
                foreach ($arrFieldList as $key2 => $value2) {
                    array_push($arrColumn, $value2);
                }
            }
        }
        $type = (isset($arrSql['type']) ? $arrSql['type'] : '');
        $primaryKey = (isset($arrSql['primary_key']) ? $arrSql['primary_key'] : '');
        $input_table = (isset($arrSql['input_table']) ? $arrSql['input_table'] : '');
        $group_column = (isset($arrSql['group_column']) ? $arrSql['group_column'] : '');
        $function_group = (isset($arrSql['function_group']) ? $arrSql['function_group'] : '');
        $class_group = (isset($arrSql['class_group']) ? $arrSql['class_group'] : '');
        $parame_group = (isset($arrSql['parame_group']) ? $arrSql['parame_group'] : '');

        $default_table_order = (isset($arrSql['default_table_order']) ? $arrSql['default_table_order'] : '');
        $default_column_order = (isset($arrSql['default_column_order']) ? $arrSql['default_column_order'] : 'DESC');
        $according = (isset($arrSql['according']) ? $arrSql['according'] : '');
        $col_sort = '';
        $col_sort_type = '';
        if ($default_table_order != '' && $default_column_order != '') {
            $col_sort = $default_table_order . '.' . $default_column_order;
            $col_sort_type = $according;
        }

        $groups['column_group'] = $group_column;
        $groups['function_group'] = $function_group;
        $groups['class_group'] = $class_group;
        $groups['parame_group'] = $parame_group;

        $sGroupColumn = '';
        if ($input_table != '' && $group_column != '')
            $sGroupColumn = $input_table . '.' . $group_column;
        if ($type == 'SP') {
            $sp_name = $arrSql['sp_name'];
            $list_parame = $arrSql['list_parame'];
            $sXmlColumnList = array();
            $sXmlTagList = array();
            $sXmlValueList = array();
            $sXmlOperatorList = array();
            $sXmlTableList = array();
            $table_name_list = array();
            if (!empty($arrColumn) && !empty($arrField)) {
                foreach ($arrColumn as $key => $column) {
                    $sColumnName = (isset($arrField[$column]['column_name']) ? $arrField[$column]['column_name'] : '');
                    array_push($sXmlColumnList, $sColumnName);
                    $v_value = (isset($arrValue[$column]) ? $arrValue[$column] : '');
                    $operator = (isset($arrField[$column]['compare_operator']) ? G_Convert::_restoreBadChar($arrField[$column]['compare_operator']) : '');
                    array_push($sXmlOperatorList, $operator);
                    $data_format = (isset($arrField[$column]['data_format']) ? $arrField[$column]['data_format'] : '');
                    if ($data_format == 'isdate') {
                        // $iSearch
                        switch ($operator) {
                            case '>':
                            case '>=':
                                $iSearch = 1;
                                break;
                            case '<':
                            case '<=':
                                $iSearch = 2;
                                break;
                            default:
                                $iSearch = '';
                                break;
                        }
                        $v_value = G_Convert::_ddmmyyyyToYYyymmdd($v_value, $iSearch);
                    }
                    // Du lieu fulltextsearch
                    if ($operator == 'like' && $sColumnName == 'C_DATA_TEMP') {
                        $v_value = G_Convert::Lower2Upper($v_value);
                    }
                    $tagName = (isset($arrField[$column]['xml_tag_in_db']) ? $arrField[$column]['xml_tag_in_db'] : '');
                    if ($arrField[$column]['xml_data'] == 'false')
                        $tagName = '';
                    array_push($sXmlTagList, $tagName);
                    $table_name = (isset($arrField[$column]['table_name']) ? $arrField[$column]['table_name'] : '');
                    // Table
                    array_push($sXmlTableList, $table_name);
                    if (!in_array($table_name, $table_name_list) && $v_value != '') {
                        array_push($table_name_list, $table_name);
                    }
                    // Value
                    array_push($sXmlValueList, $v_value);
                }
            }

            $sXmlColumnList = implode($delimetorFile, $sXmlColumnList);
            $sXmlValueList = implode(',', $sXmlValueList);
            $sXmlOperatorList = implode(',', $sXmlOperatorList);
            $sXmlTagList = implode(',', $sXmlTagList);
            $sXmlTableList = implode(',', $sXmlTableList);
            $table_name_list = implode(',', $table_name_list);
            // Tim kiem mo rong
            $arrSearchDetail = $this->getsqlsearchdetail($sXmlStuctFiledExpand, $sXmlTagFiledExpand, $arrValue);
            if (!empty($arrSearchDetail)) {
                $char = ($sXmlColumnList != '' ? ',' : '');
                $sXmlColumnList .= $char . $arrSearchDetail['sXmlColumnList'];
                $sXmlValueList .= $char . $arrSearchDetail['sXmlValueList'];
                $sXmlOperatorList .= $char . $arrSearchDetail['sXmlOperatorList'];
                $sXmlTagList .= $char . $arrSearchDetail['sXmlTagList'];
                $sXmlTableList .= $char . $arrSearchDetail['sXmlTableList'];
                $table_name_list .= $char . $arrSearchDetail['table_name_list'];
            }
            $stringGetData = $this->stringGetData($arrBody, $arrValue);
            //
            $sOrderColumn = '';
            $sSort = '';
            if (isset($arrValue['hdn_columnName']) && isset($arrValue['hdn_tablename'])) {
                $sOrderColumn = $arrValue['hdn_tablename'] . '.' . $arrValue['hdn_columnName'];
            }
            if (isset($arrValue['hdn_orderby'])) {
                $sSort = $arrValue['hdn_orderby'];
            }
            if ($sOrderColumn == '') {
                $sOrderColumn = $col_sort;
                $sSort = $col_sort_type;
            }
            $iCurrentPage = '1';
            $iNumberRecordPerPage = '15';
            if (isset($arrValue['hdn_current_page']))
                $iCurrentPage = $arrValue['hdn_current_page'];
            if (isset($arrValue['hdn_record_number_page']))
                $iNumberRecordPerPage = $arrValue['hdn_record_number_page'];
            $iStar = '';
            if (isset($arrValue['hdn_star']))
                $iStar = $arrValue['hdn_star'];

            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            // $list_parame = str_replace("'", "''", $list_parame);
            $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
            $list_parame = str_replace("#staff_id#", $userIdentity->PkStaff, $list_parame);
            $list_parame = str_replace("#department_id#", $userIdentity->FkUnit, $list_parame);
            $list_parame = self::_replaceListParamForOtherSystem($list_parame, $arrValue);
            $sSql = "Exec  ";
            $sSql .= $sp_name;
            $sSql .= " " . $dbConnect->qstr($sXmlColumnList);
            $sSql .= "," . $dbConnect->qstr($sXmlValueList);
            $sSql .= "," . $dbConnect->qstr($sXmlOperatorList);
            $sSql .= "," . $dbConnect->qstr($sXmlTagList);
            $sSql .= "," . $dbConnect->qstr($sXmlTableList);
            $sSql .= "," . $dbConnect->qstr($table_name_list);
            $sSql .= "," . $stringGetData;
            $sSql .= "," . $dbConnect->qstr($primaryKey);
            $sSql .= "," . $dbConnect->qstr($list_parame);
            $sSql .= "," . $dbConnect->qstr($sGroupColumn);
            $sSql .= "," . $dbConnect->qstr($sOrderColumn);
            $sSql .= "," . $dbConnect->qstr($sSort);
            $sSql .= "," . $dbConnect->qstr($iCurrentPage);  // Trang hien tai
            $sSql .= "," . $dbConnect->qstr($iNumberRecordPerPage); // Tong so ho so/ trang
            $sSql .= "," . $dbConnect->qstr($userIdentity->PkStaff);
            $sSql .= "," . $dbConnect->qstr($iStar);
        } else {
            // SQL
            $sSql = $arrSql['string_sql'];
            foreach ($arrColumn as $key => $value) {
                $replace = '';
                if (isset($arrValue[$value]))
                    $replace = $arrValue[$value];
                $sSql = str_replace('#' . $value . '#', $replace, $sSql);
            }
        }
        foreach ($arrParam as $key => $value) {
            $sSql .= "," . $dbConnect->qstr($value);
        }
        // return $sSql;
        if ($odebug) {
            return $sSql;
        }
        // echo $sSql; die();
        $arrResult = G_Db::getInstance()->adodbQueryDataInNameMode($sSql);
        $arrResult = G_Convert::getInstance()->convertArrayData($arrBody, $arrResult, $groups);
        // var_dump($arrResult); die();
        return $arrResult;
    }

    /**
     * @param $listParam
     * @param $arrRequest
     * @return string
     */
    public function _replaceListParamForOtherSystem($listParam, $arrRequest)
    {
        $role = Zend_Auth::getInstance()->getIdentity()->sRole;
        $arrParams = explode('!@!', $listParam);
        $newListParams = '';
        $objSession = new G_Session();
        for ($i = 0; $i < count($arrParams); $i++) {
            preg_match_all('/@@@\S*@@@/', $arrParams[$i], $arrcheckPer);
            $checkPer = '';
            if ($arrcheckPer[0] != '' && !empty($arrcheckPer[0])) {
                $checkPer = str_replace('@@@', '', str_replace('@@@', '', $arrcheckPer[0][0]));
            }
            $arrParams[$i] = preg_replace('/@@@\S*@@@/', '', $arrParams[$i]);
            preg_match_all('/#\S*#/', $arrParams[$i], $arrReq);
            if ($arrReq[0] != '' && !empty($arrReq[0])) {
                $dataType = '';
                $value = '';
                preg_match_all('/@\S*@/', $arrReq[0][0], $arrReq1);
                if ($arrReq1[0] != '' && !empty($arrReq1[0])) {
                    $dataType = str_replace('@', '', str_replace('@', '', $arrReq1[0][0]));
                }
                if (strstr($arrReq[0][0], '_ses_') != '') {
                    $nameParam = str_replace('_ses_', '', preg_replace('/@\S*@/', '', str_replace('#', '', $arrReq[0][0])));
                    $value = $objSession->_getSession($nameParam);
                } elseif (strstr($arrReq[0][0], '_req_') != '') {
                    $nameParam = str_replace('_req_', '', preg_replace('/@\S*@/', '', str_replace('#', '', $arrReq[0][0])));
                    if (isset($arrRequest[$nameParam]))
                        $value = $arrRequest[$nameParam];
                }
                if ($value != '' && $value != 'NULL' && $value != 'null' && $value != 'Null') {
                    switch ($dataType) {
                        case 'date':
                            $value = G_Convert::_ddmmyyyyToYYyymmdd($value);
                            break;
                        case 'upper':
                            $value = G_Convert::Lower2Upper($value);
                            break;
                        default:
                            # code...
                            break;
                    }
                    if ($checkPer == 1) {
                        if (!in_array($role, array('ADMIN_SYSTEM', 'ADMIN_OWNER'))) {
                            $newListParams .= ' ' . str_replace($arrReq[0][0], $value, $arrParams[$i]);
                        }
                    } else {
                        $newListParams .= ' ' . str_replace($arrReq[0][0], $value, $arrParams[$i]);
                    }
                }
            } else {
                if ($checkPer == 1) {
                    if (!in_array($role, array('ADMIN_SYSTEM', 'ADMIN_OWNER'))) {
                        $newListParams .= ' ' . $arrParams[$i];
                    }
                } else {
                    $newListParams .= ' ' . $arrParams[$i];
                }
            }
        }
        return $newListParams;
    }

    // Lấy mệnh đề điều kiện khác trong sql
    public function getparameother($psXmlFile, $arrValue)
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        // Tao mang luu thong tin phan loai du lieu
        $arrSwitchData = $this->getArrayElement('list_of_object/switch_data/item');
        $multiclause = false;
        if (!empty($arrSwitchData))
            $multiclause = true;
        if (!isset($arrSwitchData[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrSwitchData);
            $arrSwitchData = $arrTemp;
        }
        $list_parame = '';
        // Check lay thong tin phan loai du lieu
        if ($multiclause) {
            $namemodeswitch = (isset($arrValue['namemodeswitch']) ? $arrValue['namemodeswitch'] : '');
            if ($namemodeswitch != '') {
                $list_parame = $this->getclauseorther($arrSwitchData, $namemodeswitch);
            }
        }
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
        $list_parame = G_Convert::_restoreBadChar($list_parame);
        // $list_parame = str_replace("'", "''", $list_parame);
        $list_parame = str_replace("#OWNER_CODE#", $sOwnerCode, $list_parame);
        $list_parame = str_replace("#staff_id#", $userIdentity->PkStaff, $list_parame);
        $list_parame = str_replace("#department_id#", $userIdentity->FkUnit, $list_parame);
        return $list_parame;
    }

    /**
     * @param $sCode
     * @param string $sOwnerCode
     * @return array|string
     */
    public function getAllObjectbyListCodeFull($sCode, $sOwnerCode = '')
    {
        try {
            $dirXml = G_Global::getInstance()->dirXml;
            $arrOutput = array();
            $arrXmlData = array();
            $sFilePath = $dirXml . "list/output/$sCode.xml";
            if ($sOwnerCode != "") {
                $sFilePath = $dirXml . "list/output/" . $sOwnerCode . "/$sCode.xml";
            }
            if (!file_exists($sFilePath)) {
                return $arrOutput;
            }
            self::__loadxml($sFilePath, 'data_list');
            if (isset($this->item)) {
                $arrXmlData = $this->item->toArray();
            }
            if (isset($arrXmlData[0]) && is_array($arrXmlData[0])) {
                $arrayKey = array_keys($arrXmlData[0]);
                foreach ($arrXmlData as $value) {
                    if ($sOwnerCode != '') {
                        $arrOwnerCode = explode(',', $value['sOwnerCode']);
                        if (in_array($sOwnerCode, $arrOwnerCode)) {
                            $arrTemp = array();
                            foreach ($arrayKey as $value1) {
                                $arrTemp[$value1] = $value[$value1];
                            }
                            array_push($arrOutput, $arrTemp);
                        }
                    } else {
                        $arrTemp = array();
                        foreach ($arrayKey as $value1) {
                            $arrTemp[$value1] = $value[$value1];
                        }
                        array_push($arrOutput, $arrTemp);
                    }
                }
            } else {
                $arrayKey = array_keys($arrXmlData);
                $arrTemp = array();
                foreach ($arrayKey as $value1) {
                    // $key = strtoupper($value1);
                    $arrTemp[$value1] = $arrXmlData[$value1];
                }
                array_push($arrOutput, $arrTemp);
            }
            return $arrOutput;
        } catch (Exception $e) {
            echo $e->getMessage();
            return '';
        }
    }


    // Cau truc menu
    public function parseMenu($psXmlFile, $psXmlTag, $psXmlMenuItem)
    {
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        //Tao mang luu cau truc cua form
        $arrMenu_struct = $this->getArrayElement($psXmlTag);
        if (!isset($arrMenu_struct[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrMenu_struct);
            $arrMenu_struct = $arrTemp;
        }
        //Tao mang luu cau truc cua form
        $arrMenu_items = $this->getArrayElement($psXmlMenuItem);
        $arrMenu = array();
        foreach ($arrMenu_struct as $key => $arrMenuStruct) {
            $tag_list = $arrMenuStruct['tag_list'];
            $arrTagList = explode(',', $tag_list);
            $countTag = sizeof($arrTagList);
            $arrTemp = array(
                'menu_name' => $arrMenuStruct['menu_name'],
                'menu_code' => $arrMenuStruct['menu_code'],
                'menu_url' => $arrMenuStruct['menu_url']
            );
            $arrSub = array();
            for ($i = 0; $i < $countTag; $i++) {
                $tagSubMenu = $arrTagList[$i];
                array_push($arrSub, array(
                    'submenu_name' => $arrMenu_items[$tagSubMenu]['submenu_name'],
                    'submenu_code' => $arrMenu_items[$tagSubMenu]['submenu_code'],
                    'submenu_url' => $arrMenu_items[$tagSubMenu]['submenu_url'],
                ));
            }
            $arrTemp['submenu'] = $arrSub;
            array_push($arrMenu, $arrTemp);
        }
        return $arrMenu;
    }


    public function parseDataXml($psXmlFile, $psXmlTag, $arrResult, $psColumeNameOfXmlString = 'C_XML_DATA'){ 
        //Load xml
        if ($psXmlFile != '') {
            self::__loadxml($psXmlFile);
        } else {
            return '';
        }
        $arrBody = $this->getArrayElement($psXmlTag);
        
        $arrTemp = array();
        $arrOutput = array();
        $arrElement = array();
        $iTotal = sizeof($arrResult);
        $objConvert = new G_Convert();
        for ($i = 0; $i < $iTotal; $i++) {
            $arrTemp = $arrResult[$i];
            $arrElement = array();
            foreach ($arrBody as $key => $col) {
                $sColumnName = (isset($col['column_name']) ? $col['column_name'] : '');
                $sAlias = (isset($col['alias_name']) ? $col['alias_name'] : $sColumnName);
                // echo $col['xml_data'].'<br>';
                if($col['xml_data'] == false) {
                  $value = (isset($arrTemp[$sAlias]) ? $arrTemp[$sAlias] : '');
                }else{
                  $strxml = $arrTemp[$psColumeNameOfXmlString];
                  $sAlias = $col["xml_tag_in_db"];
                  $value = '';
                  if($strxml !='') {
                    $strxml = '<?xml version="1.0" encoding="UTF-8"?>' . $strxml;
                    $value = $this->_xmlGetXmlTagValue($strxml, 'data_list', $sAlias);
                  }
                  $arrTemp[$sAlias] = $value;
                }
                $phpfunction = (isset($col['phpfunction']) ? $col['phpfunction'] : '');
                $params = (isset($col['param']) ? $col['param'] : '');
                $arrParamFunc = array();
                $value = $objConvert->_restoreBadChar($value);
                if ($params != '') {
                    $arrParams = explode(',', $params);
                    foreach ($arrParams as $key => $param) {
                        $param = trim($param);
                        if ($objConvert->checkIndexArray($arrBody, $param)) {
                            $paramin = (isset($arrTemp[$param]) ? $arrTemp[$param] : '');
                        } else {
                            $paramin = $param;
                        }
                        // $arrParamFunc[] = (isset($arrTemp[$param]) ? $arrTemp[$param] : $param);
                        $arrParamFunc[] = $paramin;
                    }
                }
                $classname = (isset($col['classname']) ? $col['classname'] : '');
                if ($phpfunction != '' && $classname != '') {
                    $objClass = new $classname;
                    $value = $objClass->$phpfunction($arrParamFunc);
                    $index = 'f_' . $sAlias;
                    $arrTemp[$index] = $value;
                }
                // $arrElement[$sColumnName] = $value;
            }
           
            unset($arrTemp[$psColumeNameOfXmlString]);
            unset($arrTemp['C_DATA_TEMP']);
            array_push($arrOutput, $arrTemp);
        }

        return $arrOutput;
        
      }
}