<?php
require_once 'Plugin/prax.php';

/**
 * Class Extra_Xml
 */
class Extra_Xml extends RAX
{
    public $sLabel;
    public $efyListWebSitePath;
    public $spType;
    public $spDataFormat;
    public $spMessage;
    public $optOptional;
    public $xmlData;
    public $columnName;
    public $xmlTagInDb;
    public $readonlyInEditMode;
    public $disabledInEditMode;
    public $note;
    public $width;
    public $height;
    public $row;
    public $rowId;
    public $max;
    public $min;
    public $maxlength;
    public $tooltip;
    public $count;
    public $selectBoxOptionSql;
    public $selectBoxIdColumn;
    public $selectBoxNameColumn;
    public $functionValue;
    public $theFirstOfIdValue;
    public $checkBoxMultipleSql;
    public $checkBoxMultipleIdColumn;
    public $checkBoxMultipleNameColumn;
    public $direct;
    public $textBoxMultipleSql;
    public $textBoxMultipleIdColumn;
    public $textBoxMultipleNameColumn;
    public $firstWidth;
    public $fileAttachSql;
    public $fileAttachMax;
    public $descriptionName;
    public $hideUpdateFile;
    public $tableName;
    public $orderColumn;
    public $whereClause;
    public $directory;
    public $fileType;
    public $jsFunctionList;
    public $jsActionList;
    public $value;
    public $xmlStringInFile;
    public $inputData;
    public $sessionName;
    public $sessionIdIndex;
    public $sessionNameIndex;
    public $sessionValueIndex;
    public $url;
    public $radioValue;
    public $phpFunction;
    public $content;
    public $className;
    public $haveTitleValue;
    public $defaultValue;
    public $hrf;
    public $viewMode;
    public $publicListCode;
    public $dspDiv;
    public $cacheOption;
    public $sDataFormatStr;
    public $optOptionalLabel;
    public $formFielName;
    public $counterFileAttack;
    public $v_align;
    public $value_id;
    public $v_inc;
    public $v_label;
    public $groupBy;
    public $xmlDataCompare;
    public $xmlTagInDb_list;
    public $onclickFunction;
    public $widthLabel;
    public $sFullTextSearch;
    protected $spTableDataXmlFileName;

    /**
     * @param $spXmlString
     * @param $spXmlParentTag
     * @param $spXmlTag
     * @return string|Xau
     */
    public function _xmlGetXmlTagValue($spXmlString, $spXmlParentTag, $spXmlTag)
    {
        //Neu ton tai xau XML
        if ($spXmlString != "") {
            $objXmlData = new Zend_Config_Xml($spXmlString, $spXmlParentTag);
            return Extra_Util::_restoreXmlBadChar($objXmlData->$spXmlTag);
        } else {
            return "";
        }
    }

    /**
     * @param $spXmlFileName
     * @param $pathXmlTagStruct
     * @param $pathXmlTag
     * @param $p_xml_string_in_db
     * @param $p_arr_item_value
     * @param bool $p_input_file_name
     * @param bool $p_view_mode
     * @return string
     */
    public function _xmlGenerateFormfield($spXmlFileName, $pathXmlTagStruct, $pathXmlTag, $p_xml_string_in_db, $p_arr_item_value, $p_input_file_name = true, $p_view_mode = false)
    {
        global $i;
        $ojbEfyInitConfig = new Extra_Init();
        $p_xml_string = '';
        if (sizeof($p_arr_item_value) > 0) {
            if(isset($p_arr_item_value[0]['PK_NET_RECORD'])){
                $_SESSION['NET_RECORDID'] = $p_arr_item_value[0]['PK_NET_RECORD'];
            }
            $_SESSION['RECORDID'] = $p_arr_item_value[0]['PK_RECORD'];
            if ($_SESSION['RECORDID'] == '') {
                $_SESSION['RECORDID'] = $p_arr_item_value[0]['FK_RECORD'];
            }
            $p_xml_string = $p_arr_item_value[0][$p_xml_string_in_db];
        }

        $this->efyListWebSitePath = $ojbEfyInitConfig->_getCurrentHttpAndHost();
        $this->viewMode = $p_view_mode;
        $objConfigXml = new Zend_Config_Xml($spXmlFileName);


        $v_first_col_width = $objConfigXml->common_para_list->common_para->first_col_width;
        $v_js_file_name = $objConfigXml->common_para_list->common_para->js_file_name;
        $v_js_function = $objConfigXml->common_para_list->common_para->js_function;
        //var_dump($p_arr_item_value);exit;

        //echo $p_xml_string;exit;
        if ($p_xml_string <> '') {
            $p_xml_string = '<?xml version="1.0" encoding="UTF-8"?>' . $p_xml_string;
            $objXmlData = new Zend_Config_Xml($p_xml_string, 'data_list');
        } else {
            $objXmlData = new Zend_Config_Xml($p_xml_string_in_db, 'data_list');
        }
        //Tao mang luu cau truc cua form
        $arrTagsStruct = explode("/", $pathXmlTagStruct);
        $arrTable_truct_rows = array();
        $strcode = '$arrTable_truct_rows = $objConfigXml->' . $arrTagsStruct[0];
        for ($i = 1; $i < sizeof($arrTagsStruct); $i++)
            $strcode .= '->' . $arrTagsStruct[$i];
        eval($strcode . '->toArray();');
        //Tao mang luu thong tin cua cac phan tu tren form
        $arrTags = explode("/", $pathXmlTag);
        $arrTable_rows = array();
        $strcode = '$arrTable_rows = $objConfigXml->' . $arrTags[0];
        for ($i = 1; $i < sizeof($arrTags); $i++)
            $strcode .= '->' . $arrTags[$i];
        eval($strcode . '->toArray();');

        $sContentXmlBottom = '<div id="Bottom_contentXml">';
        //Kiem tra $arrTable_truct_rows co phai la mang 1 chieu hay ko
        if (!is_array($arrTable_truct_rows[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrTable_truct_rows);
            $arrTable_truct_rows = $arrTemp;
        }
        foreach ($arrTable_truct_rows as $row) {
            $v_have_line_before = $row["have_line_before"];
            $this->havelinebefore = $v_have_line_before;
            $v_tag_list = $row["tag_list"];
            $this->rowId = $row["row_id"];
            $arr_tag = explode(",", $v_tag_list);
            $this->count = 0;
            $this->xmlTagInDb_list = '';
            $strdiv = '<div>';
            if ($this->rowId != '')
                $strdiv = '<div id = "id_' . $this->rowId . '" class="normal_label">';
            $sContentXmlBottom .= $strdiv;

            for ($i = 0; $i < sizeof($arr_tag); $i++) {
                $this->spType = $arrTable_rows[$arr_tag[$i]]["type"];
                isset($arrTable_rows[$arr_tag[$i]]["label"]) ? $this->sLabel = $arrTable_rows[$arr_tag[$i]]["label"] : $this->sLabel = '';

                isset($arrTable_rows[$arr_tag[$i]]["width_label"]) ? $this->widthLabel = $arrTable_rows[$arr_tag[$i]]["width_label"] : $this->widthLabel = '';
                isset($arrTable_rows[$arr_tag[$i]]["data_format"]) ? $this->spDataFormat = $arrTable_rows[$arr_tag[$i]]["data_format"] : $this->spDataFormat = '';
                isset($arrTable_rows[$arr_tag[$i]]["input_data"]) ? $this->inputData = $arrTable_rows[$arr_tag[$i]]["input_data"] : $this->inputData = '';
                isset($arrTable_rows[$arr_tag[$i]]["url"]) ? $this->url = $arrTable_rows[$arr_tag[$i]]["url"] : $this->url = '';
                isset($arrTable_rows[$arr_tag[$i]]["width"]) ? $this->width = $arrTable_rows[$arr_tag[$i]]["width"] : $this->width = '';
                isset($arrTable_rows[$arr_tag[$i]]["php_function"]) ? $this->phpFunction = $arrTable_rows[$arr_tag[$i]]["php_function"] : $this->phpFunction = '';
                isset($arrTable_rows[$arr_tag[$i]]["row"]) ? $this->row = $arrTable_rows[$arr_tag[$i]]["row"] : $this->row = '';
                isset($arrTable_rows[$arr_tag[$i]]["max"]) ? $this->max = $arrTable_rows[$arr_tag[$i]]["max"] : $this->max = '';
                isset($arrTable_rows[$arr_tag[$i]]["min"]) ? $this->min = $arrTable_rows[$arr_tag[$i]]["min"] : $this->min = '';
                isset($arrTable_rows[$arr_tag[$i]]["note"]) ? $this->note = $arrTable_rows[$arr_tag[$i]]["note"] : $this->note = '';
                isset($arrTable_rows[$arr_tag[$i]]["message"]) ? $this->spMessage = $arrTable_rows[$arr_tag[$i]]["message"] : $this->spMessage = '';
                isset($arrTable_rows[$arr_tag[$i]]["optional"]) ? $this->optOptional = $arrTable_rows[$arr_tag[$i]]["optional"] : $this->optOptional = '';
                isset($arrTable_rows[$arr_tag[$i]]["max_length"]) ? $this->maxlength = $arrTable_rows[$arr_tag[$i]]["max_length"] : $this->maxlength = '';
                isset($arrTable_rows[$arr_tag[$i]]["xml_data"]) ? $this->xmlData = $arrTable_rows[$arr_tag[$i]]["xml_data"] : $this->xmlData = '';
                isset($arrTable_rows[$arr_tag[$i]]["column_name"]) ? $this->columnName = $arrTable_rows[$arr_tag[$i]]["column_name"] : $this->columnName = '';
                isset($arrTable_rows[$arr_tag[$i]]["xml_tag_in_db"]) ? $this->xmlTagInDb = $arrTable_rows[$arr_tag[$i]]["xml_tag_in_db"] : $this->xmlTagInDb = '';
                isset($arrTable_rows[$arr_tag[$i]]["js_function_list"]) ? $this->jsFunctionList = $arrTable_rows[$arr_tag[$i]]["js_function_list"] : $this->jsFunctionList = '';
                isset($arrTable_rows[$arr_tag[$i]]["js_action_list"]) ? $this->jsActionList = $arrTable_rows[$arr_tag[$i]]["js_action_list"] : $this->jsActionList = '';
                isset($arrTable_rows[$arr_tag[$i]]["defaultValue"]) ? $this->defaultValue = $arrTable_rows[$arr_tag[$i]]["defaultValue"] : $this->defaultValue = '';
                isset($arrTable_rows[$arr_tag[$i]]["readonly_in_edit_mode"]) ? $this->readonlyInEditMode = $arrTable_rows[$arr_tag[$i]]["readonly_in_edit_mode"] : $this->readonlyInEditMode = '';
                isset($arrTable_rows[$arr_tag[$i]]["disabled_in_edit_mode"]) ? $this->disabledInEditMode = $arrTable_rows[$arr_tag[$i]]["disabled_in_edit_mode"] : $this->disabledInEditMode = '';
                isset($arrTable_rows[$arr_tag[$i]]["sessionName"]) ? $this->sessionName = $arrTable_rows[$arr_tag[$i]]["sessionName"] : $this->sessionName = '';
                //lay du lieu tu session
                isset($arrTable_rows[$arr_tag[$i]]["session_id_index"]) ? $this->sessionIdIndex = $arrTable_rows[$arr_tag[$i]]["session_id_index"] : $this->sessionIdIndex = '';
                isset($arrTable_rows[$arr_tag[$i]]["session_name_index"]) ? $this->sessionNameIndex = $arrTable_rows[$arr_tag[$i]]["session_name_index"] : $this->sessionNameIndex = '';
                isset($arrTable_rows[$arr_tag[$i]]["session_value_index"]) ? $this->sessionValueIndex = $arrTable_rows[$arr_tag[$i]]["session_value_index"] : $this->sessionValueIndex = '';
                isset($arrTable_rows[$arr_tag[$i]]["class_name"]) ? $this->className = $arrTable_rows[$arr_tag[$i]]["class_name"] : $this->className = '';
                isset($arrTable_rows[$arr_tag[$i]]["have_title_value"]) ? $this->haveTitleValue = $arrTable_rows[$arr_tag[$i]]["have_title_value"] : $this->haveTitleValue = '';
                isset($arrTable_rows[$arr_tag[$i]]["value"]) ? $this->radioValue = $arrTable_rows[$arr_tag[$i]]["value"] : $this->radioValue = '';
                isset($arrTable_rows[$arr_tag[$i]]["align"]) ? $this->v_align = $arrTable_rows[$arr_tag[$i]]["align"] : $this->v_align = '';
                isset($arrTable_rows[$arr_tag[$i]]["hrf"]) ? $this->hrf = $arrTable_rows[$arr_tag[$i]]["hrf"] : $this->hrf = '';
                isset($arrTable_rows[$arr_tag[$i]]["public_list_code"]) ? $this->publicListCode = $arrTable_rows[$arr_tag[$i]]["public_list_code"] : $this->publicListCode = '';
                isset($arrTable_rows[$arr_tag[$i]]["cache_option"]) ? $this->cacheOption = $arrTable_rows[$arr_tag[$i]]["cache_option"] : $this->cacheOption = '';
                isset($arrTable_rows[$arr_tag[$i]]["file_name_xml"]) ? $this->spTableDataXmlFileName = $arrTable_rows[$arr_tag[$i]]["file_name_xml"] : $this->spTableDataXmlFileName = '';

                //Hien thi DIV
                if (($p_xml_string_in_db != '' || $p_xml_string != "") && $this->xmlData == "true") {
                    $tag = $this->xmlTagInDb;
                    $this->value = $objXmlData->$tag;
                } else {
                    if ($this->spDataFormat == "isdate") {
                        $this->value = Extra_Util::_yyyymmddToDDmmyyyy(Extra_Util::_replaceBadChar($p_arr_item_value[$this->columnName]));
                    } else {
                        if(isset($p_arr_item_value[$this->columnName])){
                            $this->value = Extra_Util::_replaceBadChar($p_arr_item_value[$this->columnName]);
                        }
                        if (trim($this->value) == "") {
                            if(isset($p_arr_item_value[0][$this->columnName])){
                                $this->value = Extra_Util::_replaceBadChar($p_arr_item_value[0][$this->columnName]);
                            }
                        }
                    }
                }
                //Dat gia gi mac dinh cho doi tuong
                if (trim($this->defaultValue) != "" && (is_null($p_arr_item_value) || sizeof($p_arr_item_value) == 0 || $p_arr_item_value["chk_save_and_add_new"] == "true")) {
                    $v_arr_function_valiable = explode('(', $this->defaultValue);
                    if (function_exists($v_arr_function_valiable[0])) {
                        $v_valiable = $v_arr_function_valiable[1];
                        $v_valiable = str_replace(')', '', $v_valiable);
                        $v_valiable = str_replace('(', '', $v_valiable);
                        $v_arr_valiable = explode(',', $v_valiable);
                        $v_call_user_function_str = "'" . trim($v_arr_function_valiable[0]) . "'";
                        for ($i = 0; $i < sizeof($v_arr_valiable); $i++) {
                            $v_call_user_function_str = $v_call_user_function_str . "," . $v_arr_valiable[$i];
                        }
                        $v_call_user_function_str = "call_user_func(" . $v_call_user_function_str . ")";
                        eval("\$this->value = " . $v_call_user_function_str . ";");
                    } else {
                        $this->value = $this->defaultValue;
                    }
                }
                if ($this->spType == "selectbox") {
                    $this->selectBoxOptionSql = $arrTable_rows[$arr_tag[$i]]["selectbox_option_sql"];
                    $this->selectBoxIdColumn = $arrTable_rows[$arr_tag[$i]]["selectbox_option_id_column"];
                    $this->selectBoxNameColumn = $arrTable_rows[$arr_tag[$i]]["selectbox_option_name_column"];
                    isset($arrTable_rows[$arr_tag[$i]]["the_first_of_id_value"]) ? $this->theFirstOfIdValue = $arrTable_rows[$arr_tag[$i]]["the_first_of_id_value"] : $this->theFirstOfIdValue = '';
                }

                if ($this->spType == "button") {
                    $this->onclickFunction = $arrTable_rows[$arr_tag[$i]]["onclick_function"];
                }

                //Kieu hien thi multil checkbox hoac radio
                if ($this->spType == "multiplecheckbox" || $this->spType == "multipleradio") {
                    $this->checkBoxMultipleSql = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_sql"];
                    $this->checkBoxMultipleIdColumn = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_id_column"];
                    $this->checkBoxMultipleNameColumn = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_name_column"];
                    $this->direct = $arrTable_rows[$arr_tag[$i]]["direct"];
                    $this->firstWidth = $arrTable_rows[$arr_tag[$i]]["first_width"];
                    $this->dspDiv = $arrTable_rows[$arr_tag[$i]]["dsp_div"];
                }
                //Kieu hien thi multil checkbox co file dinh kem
                if ($this->spType == "multiplecheckbox_fileAttach") {
                    $this->checkBoxMultipleSql = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_sql"];
                    $this->checkBoxMultipleIdColumn = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_id_column"];
                    $this->checkBoxMultipleNameColumn = $arrTable_rows[$arr_tag[$i]]["checkbox_multiple_name_column"];
                    isset($arrTable_rows[$arr_tag[$i]]["direct"]) ? $this->direct = $arrTable_rows[$arr_tag[$i]]["direct"] : $this->direct = '';
                    isset($arrTable_rows[$arr_tag[$i]]["first_width"]) ? $this->firstWidth = $arrTable_rows[$arr_tag[$i]]["first_width"] : $this->firstWidth = '';
                    isset($arrTable_rows[$arr_tag[$i]]["dsp_div"]) ? $this->dspDiv = $arrTable_rows[$arr_tag[$i]]["dsp_div"] : $this->dspDiv = '';
                }

                if ($this->spType == "textboxorder") {
                    $this->tableName = $arrTable_rows[$arr_tag[$i]]["table_name"];
                    $this->orderColumn = $arrTable_rows[$arr_tag[$i]]["order_column"];
                    $this->whereClause = $arrTable_rows[$arr_tag[$i]]["where_clause"];
                }

                if ((is_null($this->value) || $this->value == '') && $this->spType != "channel") {
                    $this->readonlyInEditMode = "false";
                    $this->disabledInEditMode = "false";
                }
                $sContentXmlBottom .= Extra_Xml::_generateHtmlInput();
            }

            $sContentXmlBottom .= '</div>';

            if ($this->v_align != '' && !(is_null($this->v_align))) {
                $this->v_align = "align='" . $this->v_align . "'";
            } else {
                $this->v_align = '';
            }
        }
        $spHtmlStr = '';
        if ($v_js_file_name != '' && !(is_null($v_js_file_name))) {
            $spHtmlStr .= Extra_Util::_getAllFileJavaScriptCss('', 'js/js-record', $v_js_file_name, ',', 'js');
        }
        if ($v_js_function != '' && !(is_null($v_js_function))) {
            $spHtmlStr .= '<script>try{' . $v_js_function . '}catch(e){;}</script>';
        }

        $sContentXmlBottom .= '</div>';
        if ($sContentXmlBottom != '<div id="Bottom_contentXml"></div>')
            $spHtmlStr .= $sContentXmlBottom;
        $spHtmlStr .= '<style> #Bottom_contentXml div label{width:' . $v_first_col_width . ';} #Top_contentXml div label{width:' . ($v_first_col_width * 2) . '%;}</style>';
        return $spHtmlStr;
    }

    /**
     * @idea:Tao chuoi html ung voi doi tuong de generate form fields
     */
    private function _generateHtmlInput()
    {
        global $i;
        //Sinh ra cac thuoc tinh dung cho viec kiem hop du lieu tren form
        $this->sDataFormatStr = Extra_Xml::_generateVerifyProperty($this->spDataFormat);
        $this->optOptionalLabel = "";
        $spRetHtml = '';
        if ($this->havelinebefore == "true") {
            //echo $v_row_id;
            $spRetHtml = $spRetHtml . "<table width='100%'  border='0' cellspacing='0' cellpadding='0'><tr>";
            $spRetHtml = $spRetHtml . "<td><hr width='100%' color='#E2CA81' size=0px'></td>";
            $spRetHtml = $spRetHtml . "</tr></table>";
        }
        if ($this->optOptional == "false") {
            $this->optOptionalLabel = "<small class='normal_starmark'>*</small>";
        }
        $styleLabel = '';
        if ($this->widthLabel != '')
            $styleLabel = "width:" . $this->widthLabel;
        if ($this->sLabel == '')
            $this->sLabel = "&nbsp;";
        if ($i > 0)
            //Neu co nhieu hon 1 phan tu nam tren 1 hang
            $v_str_label = '<label class="normal_label" style="float:none;' . $styleLabel . '">' . $this->sLabel . $this->optOptionalLabel . '</label>';
        else
            $v_str_label = '<label class="normal_label" style = "' . $styleLabel . '">' . $this->sLabel . $this->optOptionalLabel . '</label>';
        $v_checked = "";

        if ($this->xmlData == 'true') {
            $this->formFielName = $this->xmlTagInDb;
        } else {
            $this->formFielName = $this->columnName;
        }
        switch ($this->spType) {
            case "table";
                $spRetHtml = $spRetHtml . $this->sLabel . $this->optOptionalLabel;
                $spRetHtml = $spRetHtml . _generate_html_for_table();
                break;
            case "label";
                if ($this->className != "") {
                    $spRetHtml = $spRetHtml . "<span class='" . $this->className . "'>" . $this->sLabel . $this->optOptionalLabel . "</span>";
                } else {
                    $spRetHtml = $spRetHtml . $this->sLabel . $this->optOptionalLabel;
                }
                break;
            case "link";
                $this->hrf = str_replace('"', '&quot;', $this->hrf);
                $spRetHtml = $spRetHtml . '<a class="normal_link" href="' . $this->hrf . '">' . $this->sLabel . '</a>';
                break;
            case 'small_title':
                $spRetHtml .= $v_str_label . '<label class="small_title" valign="bottom">' . $this->value . '</label>';
                break;
            case "relaterecord";
                $spRetHtml = $v_str_label;
                $spRetHtml = $spRetHtml . "<input type='textbox' name='$this->formFielName' class='normal_textbox' value='$this->value' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . ' ' . $this->sDataFormatStr . '" xml_tag_in_db="' . $this->xmlTagInDb . '" xml_data="' . $this->xmlData . '" column_name="' . $this->columnName . '" message="' . $this->spMessage . '" onKeyDown="change_focus(document.forms[0],this,event)">';
                $spRetHtml = $spRetHtml . "<input type='hidden' name='hdn_relate_record_code' value=''>";
                if ($this->value == "") {
                    $spRetHtml = $spRetHtml . "<input type='button' name='btn_submit' style='width:auto' title='$this->tooltip' value='L&#7845;y th&#244;ng tin t&#7915; h&#7891; s&#417; li&#234;n quan' class='small_button' onClick=''>";
                } else {
                    $arr_single_record_by_code = _adodb_query_data_in_name_mode("Onegate_RecordGetSingleByCode '$this->value'");
                    $v_record_id = $arr_single_record_by_code[0]['PK_RECORD'];
                    $v_recordtype = $arr_single_record_by_code[0]['FK_RECORDTYPE'];
                    if ($v_record_id > 0) {
                        $spRetHtml = $spRetHtml . "<a href=''>N&#7897;i dung c&#7911;a h&#7891; s&#417; li&#234;n quan</a>";
                    }
                }
                break;

            case "fileclient";
                $v_file_attack_name = "txt_xml_file_name" . $this->counterFileAttack;
                $spRetHtml = $v_str_label;
                //neu co file hien ten file
                if ($this->value != '') {
                    $spRetHtml = $spRetHtml . '<label class="normal_label" >' . $this->value . '</label><br>';
                    $spRetHtml = $spRetHtml . '<label class="normal_label" >&nbsp;</label>';
                }
                $spRetHtml = $spRetHtml . "<div name='$this->formFielName' style='display:none'><input id = '$this->formFielName' type='text' name='$this->formFielName'class='normal_textbox' title='$this->tooltip' value='$this->value'  style='width:$this->width' style='border=0' readonly  $this->sDataFormatStr xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . "></div>";
                $spRetHtml = $spRetHtml . "<input type='file' name='$v_file_attack_name' value='$this->value' class='normal_textbox' title='$this->tooltip' size='$this->width' onKeyDown='change_focus(document.forms[0],this,event)' OnChange=\"GetFileName(this,document.getElementById('" . $this->formFielName . "'))\">";
                $spRetHtml = $spRetHtml . "";
                $spRetHtml = $spRetHtml . $this->note;
                $this->counterFileAttack = $this->counterFileAttack + 1;
                break;

            case "textbox";
                $spRetHtml = $spRetHtml . $v_str_label;
                if ($this->viewMode && $this->readonlyInEditMode == "true") {
                    $spRetHtml = $spRetHtml . $this->value;
                } else {
                    if ($this->spDataFormat == "isdate") {
                        $spRetHtml = $spRetHtml . '<input type="text" id="' . $this->formFielName . '"  name="' . $this->formFielName . '" class="normal_textbox datepicker2c" value="' . $this->value . '" title="' . $this->tooltip . '" style="width:' . $this->width . '" ' . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . ' ' . $this->sDataFormatStr . '" xml_tag_in_db="' . $this->xmlTagInDb . '" xml_data="' . $this->xmlData . '" column_name="' . $this->columnName . '" message="' . $this->spMessage . '" >';
                    } else {
                        $spRetHtml = $spRetHtml . '<input type="text" id="' . $this->formFielName . '" name="' . $this->formFielName . '" class="normal_textbox" value="' . $this->value . '" title="' . $this->tooltip . '" style="width:' . $this->width . '" ' . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . ' ' . $this->sDataFormatStr . '" xml_tag_in_db="' . $this->xmlTagInDb . '" xml_data="' . $this->xmlData . '" column_name="' . $this->columnName . '" message="' . $this->spMessage . '" maxlength="' . $this->maxlength . '" ';
                        if (rtrim($this->max) != '' && !is_null($this->max)) {
                            $spRetHtml = $spRetHtml . ' max="' . $this->max . '"';
                        }
                        if (rtrim($this->min) != '' && !is_null($this->min)) {
                            $spRetHtml = $spRetHtml . ' min="' . $this->min . '"';
                        }
                        $spRetHtml = $spRetHtml . ' onKeyDown="change_focus(document.forms[0],this,event)">';
                    }
                    $spRetHtml = $spRetHtml . $this->note;
                }
                break;

            case "text";
                $spRetHtml = $spRetHtml . $v_str_label;
                $spRetHtml = $spRetHtml . '<span class="data">' . $this->value . '&nbsp;</span>';
                break;

            case "identity";
                $this->value = $this->count + 1;
                if ($this->value < 10) {
                    $spRetHtml = $spRetHtml . '<span class="data">0' . $this->value . '</span>';
                } else {
                    $spRetHtml = $spRetHtml . '<span class="data">' . $this->value . '</span>';
                }
                break;

            case "checkbox";

                if ($this->value == "true" || $this->value == 1) {
                    $v_checked = " checked ";
                } else {
                    $v_checked = " ";
                }
                if ($this->sLabel != '' || $this->optOptionalLabel != '') {
                    $spRetHtml = "";
                } else {
                    $spRetHtml = "";
                }
                $spRetHtml = $spRetHtml . '<label>&nbsp;</label>';
                $spRetHtml = $spRetHtml . "<input type='checkbox' id = '" . $this->formFielName . "' name='$this->formFielName' class='normal_checkbox' title='$this->tooltip' $v_checked value='1' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)'>";
                $spRetHtml = $spRetHtml . "<font class='normal_label'>" . $this->sLabel . $this->optOptionalLabel . "</font>";
                break;
            case "radio";
                if ($this->radioValue == $this->value || $this->value == "true") {
                    $v_checked = " checked ";
                } else {
                    $v_checked = " ";
                }
                $spRetHtml = "<input type='radio' id = '" . $this->formFielName . "' name='$this->rowId' class='normal_checkbox' $v_checked value='$this->radioValue' title='$this->tooltip' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)'>";
                $spRetHtml = $spRetHtml . "" . $this->sLabel . $this->optOptionalLabel . "";
                break;

            case "textarea";
                $spRetHtml = $spRetHtml . $v_str_label;
                if ($this->viewMode && $this->readonlyInEditMode == "true") {
                    $spRetHtml = $spRetHtml . $this->value;
                } else {
                    $spRetHtml = $spRetHtml . '<textarea class="normal_textarea" id = "' . $this->formFielName . '" name="' . $this->formFielName . '" rows="' . $this->row . '" title="' . $this->tooltip . '" style="width:' . $this->width . '" ' . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . ' xml_tag_in_db="' . $this->xmlTagInDb . '" xml_data="' . $this->xmlData . '" column_name="' . $this->columnName . '" message="' . $this->spMessage . '">' . $this->value . '</textarea>';
                }
                break;

            case "selectbox";
                $spRetHtml = $spRetHtml . $v_str_label;
                if ($this->viewMode && $this->readonlyInEditMode == "true") {
                    if ($this->inputData == "session") {
                        $j = 0;
                        $arrListItem = array();
                        if (isset($_SESSION[$this->sessionName])) {
                            foreach ($_SESSION[$this->sessionName] as $arr_item) {
                                $arrListItem[$j] = $arr_item;
                                $j++;
                            }
                        }
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->sessionIdIndex];
                        }
                        $spRetHtml = $spRetHtml . Extra_Xml::_getValueFromArray($arrListItem, $this->sessionIdIndex, $this->sessionNameIndex, $this->value);
                    } elseif ($this->inputData == "efylist") {//Lay du lieu tu file XML
                        $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "xml/list/output/" . $this->publicListCode . ".xml");
                        //
                        $arrListItem = Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item");
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->selectBoxIdColumn];
                        }
                        $spRetHtml = $spRetHtml . Extra_Xml::_getValueFromArray($arrListItem, $this->selectBoxIdColumn, $this->selectBoxNameColumn, $this->value);
                    } else {
                        //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                        $this->selectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $this->selectBoxOptionSql);
                        //echo $this->selectBoxOptionSql;
                        // thuc hien co che cache o day
                        $arrListItem = Extra_Db::adodbQueryDataInNumberMode($this->selectBoxOptionSql, $this->cacheOption);
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->selectBoxIdColumn];
                        }
                        $spRetHtml = $spRetHtml . Extra_Xml::_getValueFromArray($arrListItem, $this->selectBoxIdColumn, $this->selectBoxNameColumn, $this->value);
                    }
                } else {
                    if ($this->inputData == "session") {
                        $j = 0;
                        $arrListItem = array();
                        if (isset($_SESSION[$this->sessionName])) {
                            foreach ($_SESSION[$this->sessionName] as $arr_item) {
                                $arrListItem[$j] = $arr_item;
                                $j++;
                            }
                        }
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->sessionIdIndex];
                        }
                        //$spRetHtml = $spRetHtml . "<select id='$this->formFielName' class='normal_selectbox' name='$this->formFielName' title='$this->tooltip' style='width:$this->width' ".Extra_Xml::_generatePropertyType("optional",$optOptional).Extra_Xml::_generatePropertyType("readonly",$this->readonlyInEditMode).Extra_Xml::_generatePropertyType("disabled",$this->disabledInEditMode).Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList)." xml_tag_in_db='$this->xmlTagInDb' xml_data='$v_xml_data' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)' >";
                        $spRetHtml = $spRetHtml . "<select id='$this->rowId' class='normal_selectbox' name='$this->formFielName' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)' >";
                        if ($this->theFirstOfIdValue == "true") {
                            $spRetHtml = $spRetHtml . Extra_Util::_generateSelectOption($arrListItem, $this->sessionIdIndex, $this->sessionValueIndex, $this->sessionNameIndex, $this->value);
                        } else {
                            $spRetHtml = $spRetHtml . "<option id='' value='' name=''>--- Ch&#7885;n $this->sLabel  ---</option>" . Extra_Util::_generateSelectOption($arrListItem, $this->sessionIdIndex, $this->sessionValueIndex, $this->sessionNameIndex, $this->value);
                        }
                        $spRetHtml = $spRetHtml . "</select>";

                    } elseif ($this->inputData == "efylist") {
                        $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "xml/list/output/" . $this->publicListCode . ".xml");
                        $arrListItem = Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item");
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->selectBoxIdColumn];
                        }
                        $spRetHtml = $spRetHtml . "<select id='$this->formFielName' class='normal_selectbox' name='$this->formFielName' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)' >";
                        if (is_null($this->haveTitleValue) || ($this->haveTitleValue == "") || ($this->haveTitleValue == "true")) {
                            if ($this->theFirstOfIdValue != "true") {
                                $spRetHtml = $spRetHtml . "<option id='' value='' name=''>--- Ch&#7885;n $this->sLabel ---</option>";
                            }
                        }

                        $spRetHtml = $spRetHtml . Extra_Util::_generateSelectOption($arrListItem, $this->selectBoxIdColumn, $this->selectBoxIdColumn, $this->selectBoxNameColumn, $this->value);
                        $spRetHtml = $spRetHtml . "</select>";
                    } else {
                        //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                        $this->selectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $this->selectBoxOptionSql);
                        //echo $this->selectBoxOptionSql;
                        // thuc hien cach
                        $arrListItem = Extra_Db::adodbQueryDataInNumberMode($this->selectBoxOptionSql, $this->cacheOption);
                        if ($this->theFirstOfIdValue == "true" && $this->value == "") {
                            $this->value = $arrListItem[0][$this->selectBoxIdColumn];
                        }
                        $spRetHtml = $spRetHtml . "<select id='$this->formFielName' class='normal_selectbox' name='$this->formFielName' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)' >";
                        if (is_null($this->haveTitleValue) || ($this->haveTitleValue == "") || ($this->haveTitleValue == "true")) {
                            if ($this->theFirstOfIdValue != "true") {
                                $spRetHtml = $spRetHtml . "<option id='' value='' name=''>--- Ch&#7885;n $this->sLabel ---</option>";
                            }
                        }
                        $spRetHtml = $spRetHtml . Extra_Util::_generateSelectOption($arrListItem, $this->selectBoxIdColumn, $this->selectBoxIdColumn, $this->selectBoxNameColumn, $this->value);
                        $spRetHtml = $spRetHtml . "</select>";
                    }
                }
                break;
            case "multiplecheckbox";
                $spRetHtml = $v_str_label;
                if ($this->inputData == "session") {
                    $spRetHtml = $spRetHtml . "<div style='display:none'><input type='textbox' id='$this->formFielName' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . "xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'></div>";
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckboxFromSession($this->sessionName, $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                } elseif ($this->inputData == "efylist") {
                    $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "xml/list/output/" . $this->publicListCode . ".xml");
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckbox(Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item"), $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                } else {
                    //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                    $this->checkBoxMultipleSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $this->checkBoxMultipleSql);
                    $spRetHtml = $spRetHtml . "<div style='display:none'><input type='textbox' id='$this->formFielName' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . "xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'></div>";
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckbox(Extra_Db::adodbQueryDataInNumberMode($this->checkBoxMultipleSql, $this->cacheOption), $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                }
                break;
            //kieu mulriplecheckbox co file dinh kem
            case "multiplecheckbox_fileAttach";
                $spRetHtml = $v_str_label;
                if ($this->inputData == "session") {
                    $spRetHtml = $spRetHtml . "<div style='display:none'><input type='textbox' id='$this->formFielName' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . "xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'></div>";
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckboxFromSession($this->sessionName, $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                } elseif ($this->inputData == "efylist") {
                    $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "xml/list/output/" . $this->publicListCode . ".xml");
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckbox_fileAttach(Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item"), $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                } else {
                    //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                    $this->checkBoxMultipleSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $this->checkBoxMultipleSql);
                    $spRetHtml = $spRetHtml . "<div style='display:none'><input type='textbox' id='$this->formFielName' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . "xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'></div>";
                    $spRetHtml = $spRetHtml . Extra_Xml::_generateHtmlForMultipleCheckbox_fileAttach(Extra_Db::adodbQueryDataInNumberMode($this->checkBoxMultipleSql, $this->cacheOption), $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value);
                }
                break;
            case "multipleradio";
                if ($this->inputData == "efylist") {
                    $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "xml/list/output/" . $this->publicListCode . ".xml");
                    $arrListItem = Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item");
                } else {
                    $arrListItem = Extra_Db::adodbQueryDataInNumberMode($this->checkBoxMultipleSql, $this->cacheOption);
                }

                if ($this->direct == 'true') {
                    $spRetHtml = $this->sLabel . $this->optOptionalLabel;
                    $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' value='$this->value' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . " xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                    $spRetHtml = $spRetHtml . _generate_html_for_multiple_radio($arrListItem, $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value, $this->direct);
                } else {
                    if ($this->sLabel != "" && isset($this->sLabel)) {
                        $spRetHtml = $this->sLabel . $this->optOptionalLabel;
                        if ($this->inputData == "session") {
                            $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . " xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                            $spRetHtml = $spRetHtml . _generate_html_for_multiple_radio_from_session($this->sessionName, $this->sessionIdIndex, $this->sessionNameIndex, $this->value);
                        } else {
                            $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' value='$this->value' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . " xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                            $spRetHtml = $spRetHtml . _generate_html_for_multiple_radio($arrListItem, $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value, $this->direct);
                        }
                    } else {
                        if ($this->inputData == "session") {
                            $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' value='' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . " xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                            $spRetHtml = $spRetHtml . _generate_html_for_multiple_radio_from_session($this->sessionName, $this->sessionIdIndex, $this->sessionNameIndex, $this->value);
                        } else {
                            $spRetHtml = $this->sLabel . $this->optOptionalLabel;
                            $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' value='$this->value' hide='true' readonly " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . " xml_data='true' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                            $spRetHtml = $spRetHtml . _generate_html_for_multiple_radio($arrListItem, $this->checkBoxMultipleIdColumn, $this->checkBoxMultipleNameColumn, $this->value, $this->direct);
                        }
                    }
                }
                break;

            case "textboxorder";
                $spRetHtml = $spRetHtml . $v_str_label;
                if (is_null($this->value) || $this->value == "") {
                    $this->value = Extra_Util::_getNextValue("T_EFYLIB_LIST", "C_ORDER", "FK_LISTTYPE = " . $_SESSION['listtypeId']);
                    if (!is_null($this->tableName) && $this->tableName != "") {
                        $this->value = Extra_Util::_getNextValue($this->tableName, $this->orderColumn, $this->whereClause);
                    }
                }
                $spRetHtml = $spRetHtml . "<input type='text' name='$this->formFielName' class='normal_textbox' value='$this->value' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " $this->sDataFormatStr xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' min='$this->min' max='$this->max' maxlength='$this->maxlength' onKeyDown='change_focus(document.forms[0],this,event)'>";
                break;

            case "checkboxstatus";
                if ($this->value == "true" || $this->value == 1 || $this->value == "HOAT_DONG" || $this->value == "") {
                    $v_checked = " checked ";
                }
                $spRetHtml = $spRetHtml . '<label>&nbsp;</label>';
                $spRetHtml = $spRetHtml . "<input type='checkbox' id='$this->formFielName' name='$this->formFielName' class='normal_checkbox' title='$this->tooltip' $v_checked value='1' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName'  message='$this->spMessage' onKeyDown='change_focus(document.forms[0],this,event)'>";
                $spRetHtml = $spRetHtml . "<font style='font-family:arial;font-size:13px;font-weight:normal;line-height:13px;'>" . $this->sLabel . $this->optOptionalLabel . "</font>";
                break;

            case "button";
                if (is_null($this->className) || ($this->className == "")) {
                    $this->className = "small_button";
                }
                $spRetHtml = $spRetHtml . "&nbsp;&nbsp;<input type='button' name='$this->formFielName' class='$this->className' value='$this->sLabel' title='$this->tooltip' style='width:$this->width' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " $this->sDataFormatStr xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$this->spMessage' onClick='$this->onclickFunction'>";
                $spRetHtml = $spRetHtml . $this->note;
                break;

            case "hidden";
                $spRetHtml = $spRetHtml . "<input type='text' style='width:0;visibility:hidden' name='$this->formFielName' value='$this->value' hide='true' xml_data='$this->xmlData' " . Extra_Xml::_generatePropertyType("optional", $this->optOptional) . "' xml_tag_in_db='$this->xmlTagInDb' message='$this->spMessage'>";
                break;

            case "tabledata";
                $arrList = json_decode($this->value, true);
                $row = count($arrList);
                $spRetHtml = $v_str_label;
                $spRetHtml .= '<div style="display:none"><input type="textbox" value=\'' . $this->value . '\' id="input_' . $this->formFielName . '" name="' . $this->formFielName . '"  optional="false" xml_data="true" xml_tag_in_db="' . $this->formFielName . '"><input id="NAME_XML_TAB_TABLE_DATA" value= "' . $this->formFielName . '"/></div>';
                $spRetHtml .= '<div style="overflow:auto;width:' . $this->width . '" id="div_' . $this->formFielName . '" >';
                $spRetHtml .= self::_generateHtmlForDataTable($this->spTableDataXmlFileName, $this->formFielName, $arrList);
                $spRetHtml .= '</div>';
                $spRetHtml .= '<script>
									//jQuery(document).ready(function($){
                                        var Js_GeneralDataTable_' . $this->formFielName . ' = new Js_GeneralDataTable();
										Js_GeneralDataTable_' . $this->formFielName . '.general({
											"type":"data_table"
											,"input_id":"' . $this->formFielName . '"
											,"list_value":\'' . json_encode($arrList) . '\'
                                            ,"rowid":\'' . $row . '\'
										});
									//})
								</script>';
                break;
            case "formfieldata";
                $arrList = json_decode($this->value, true);
                $row = count($arrList);
                $spRetHtml .= '<div style="display:none"><input type="textbox" value=\'' . $this->value . '\' id="input_' . $this->formFielName . '" name="' . $this->formFielName . '"  optional="false" xml_data="true" xml_tag_in_db="' . $this->formFielName . '"><input id="NAME_XML_TAB_FORM_FIEL_DATA" value= "' . $this->formFielName . '"/></div>';
                $spRetHtml .= '<div style="overflow:auto;" id="div_' . $this->formFielName . '" >';
                $spRetHtml .= '<div id="pane_' . $this->formFielName . '" class="normal_label"><label class="normal_label" style="float: none;"></label><img onclick="Js_GeneralFormFiel_' . $this->formFielName . '.addObj()" title="Thêm" src="' . $this->efyListWebSitePath . 'public/images/add.png"></div>';
                $spRetHtml .= '</div>';
                $htmlFormFiel = self::_generateHtmlForFormFiel($this->spTableDataXmlFileName, $this->formFielName);
                $spRetHtml .= '<script>var Js_GeneralFormFiel_' . $this->formFielName . ' = new Js_GeneralFormFiel();
										Js_GeneralFormFiel_' . $this->formFielName . '.general({
											"type":"form_fiel"
											,"html_add": "' . htmlspecialchars($htmlFormFiel) . '"
											,"input_id":"' . $this->formFielName . '"
											,"list_value":\'' . json_encode($arrList) . '\'
                                            ,"rowid":\'' . $row . '\'
										});</script>';
                break;
            default:
                $spRetHtml = '<label style="width:100%">' . $this->sLabel . $this->optOptionalLabel . '</label>';
        }
        return $spRetHtml;
    }

    /**
     * @param $psXmlFile
     * @param $sformFielName
     * @param $arrList
     * @return string
     */
    Public function _generateHtmlForDataTable($psXmlFile, $sformFielName, $arrList)
    {
        $objconfig = new Extra_Init();
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(2) . 'record/table/' . $psXmlFile . '.xml';
        $psHtmlString = '';
        if (!file_exists($sxmlFileName)) {
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/table/' . $psXmlFile . '.xml';
        }
        if (file_exists($sxmlFileName)) {
            //Goi class lay tham so cau hinh he thong
            $objConfiXml = new Zend_Config_Xml($sxmlFileName);
            $psHtmlString = '<table width="100%" id="father_' . $sformFielName . '" class="list_table2" cellpadding="0" cellspacing="0" border="0" align="center">';
            $arrTable_Struct = $objConfiXml->list_of_object->list_body->col->toArray();
            //Tao header cho bang
            $psHtmlTempWidth = '';
            $psHtmlTempLabel = '';
            foreach ($arrTable_Struct as $col) {
                $slabel = $col["label"];
                $swidth = $col["width"];
                $psHtmlTempWidth .= '<col width="' . $swidth . '">';
                $psHtmlTempLabel .= '<td style="background-image: none; line-height: 18px; font-size: 12px;" class="title" >' . $slabel . '</td>';
            }
            $psHtmlString .= $psHtmlTempWidth;
            $psHtmlString .= '<tr class="header">' . $psHtmlTempLabel . '</tr>';
            $i = 1;
            // phan noi dung
            $count = count($arrList);
            if ($count > 0) {
                foreach ($arrList as $val) {
                    $psHtmlString .= '<tr class="round_row">';
                    $phtmlcontenstrr = '';
                    foreach ($arrTable_Struct as $col) {
                        $stype = $col["type"];
                        if ($stype == 'identity') {
                            $phtmlcontenstrr .= '<td align="center"></td>';
                        } else if ($stype == 'text') {

                            $id = $col["id"];
                            $value = $val[$id];
                            $phtmlcontenstrr .= '<td align="center"><input id="' . $id . '" type="textdata1" onchange="Js_GeneralDataTable_' . $sformFielName . '.insert_value_to_div()" value="' . $value . '" style="width:96%;" optional="true" class="normal_textbox"/></td>';
                        } else if ($stype == 'task') {
                            $phtmlcontenstrr .= '<td align="center" id="td_' . $i . '" class="normal_label"><span class="delete_datatable_row" onclick="Js_GeneralDataTable_' . $sformFielName . '.delete_row(' . $i . ');"></span></td></tr>';
                        }
                    }
                    $i++;
                    $psHtmlString .= $phtmlcontenstrr;
                    $psHtmlString .= '</tr>';
                }
            }
            // phan them moi ho so
            $pTablerow = '';
            foreach ($arrTable_Struct as $col) {
                $stype = $col["type"];

                if ($stype == 'identity') {
                    $pTablerow .= '<td align="center"></td>';
                } else if ($stype == 'text') {
                    $id = $col["id"];
                    $pTablerow .= '<td align="center"><input id="' . $id . '" type="textdata1" style="width:96%;" optional="true" class="normal_textbox"/></td>';
                } else if ($stype == 'task') {
                    $pTablerow .= '<td align="center"><img onclick="Js_GeneralDataTable_' . $sformFielName . '.update_data_list()"; src="' . $this->efyListWebSitePath . 'public/images/add.png"></td>';
                }
            }
            $psHtmlString .= '<tr class="round_row">' . $pTablerow . '</tr>';
            $psHtmlString .= '</table>';
        }
        return $psHtmlString;
    }

    /**
     * @param $psXmlFile
     * @param $sformFielName
     * @return string
     */
    Public function _generateHtmlForFormFiel($psXmlFile, $sformFielName)
    {
        $objconfig = new Extra_Init();
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(2) . 'record/table/' . $psXmlFile . '.xml';
        $psHtmlString = '';
        if (!file_exists($sxmlFileName)) {
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/table/' . $psXmlFile . '.xml';
        }

        if (file_exists($sxmlFileName)) {
            Zend_Loader::loadClass('Zend_Config_Xml');
            $objConfigXml = new Zend_Config_Xml($sxmlFileName);
            //$psHtmlString = '<div id="Bottom_contentXml">';
            $arrTable_truct_rows = $objConfigXml->update_object->table_struct_of_update_form->update_row_list->update_row->toArray();
            $arrTable_rows = $objConfigXml->update_object->update_formfield_list->toArray();
            foreach ($arrTable_truct_rows as $row) {
                isset($row["row_id"]) ? $rowId = $row["row_id"] : $rowId = $sformFielName . '_rowid';
                $v_tag_list = $row["tag_list"];
                $arr_tag = explode(",", $v_tag_list);
                $psHtmlString .= '<div id = "id_' . $rowId . '" class="normal_label">';
                for ($i = 0; $i < sizeof($arr_tag); $i++) {
                    isset($arrTable_rows[$arr_tag[$i]]) ? $arrProp = $arrTable_rows[$arr_tag[$i]] : $arrProp = array();
                    $psHtmlString .= self::_generateHtmlInputForFormFiel($arrProp, $sformFielName);
                }
                $psHtmlString .= '</div>';
            }
            //$psHtmlString .= '</div>';
        }
        return $psHtmlString;
    }

    /**
     * @param $arrProp
     * @param $sformFielName
     * @param $arrList
     * @return string
     */
    private function _generateHtmlInputForFormFiel($arrProp, $sformFielName)
    {
        /*echo '<pre>';
        var_dump($arrProp);*/

        isset($arrProp["label"]) ? $label = $arrProp["label"] : $label = '';
        isset($arrProp["width_label"]) ? $width_label = $arrProp["width_label"] : $width_label = '';
        isset($arrProp["type"]) ? $type = $arrProp["type"] : $type = '';
        isset($arrProp["data_format"]) ? $data_format = $arrProp["data_format"] : $data_format = '';
        isset($arrProp["input_data"]) ? $input_data = $arrProp["input_data"] : $input_data = '';
        isset($arrProp["width"]) ? $width = $arrProp["width"] : $width = '';
        isset($arrProp["php_function"]) ? $php_function = $arrProp["php_function"] : $php_function = '';
        isset($arrProp["note"]) ? $note = $arrProp["note"] : $note = '';
        isset($arrProp["xmlData"]) ? $xmlData = $arrProp["xmlData"] : $xmlData = '';
        isset($arrProp["column_name"]) ? $column_name = $arrProp["column_name"] : $column_name = '';
        isset($arrProp["xml_tag_in_db"]) ? $xml_tag_in_db = $arrProp["xml_tag_in_db"] : $xml_tag_in_db = '';
        isset($arrProp["js_function_list"]) ? $js_function_list = $arrProp["js_function_list"] : $js_function_list = '';
        isset($arrProp["js_action_list"]) ? $js_action_list = $arrProp["js_action_list"] : $js_action_list = '';
        isset($arrProp["default_value"]) ? $default_value = $arrProp["default_value"] : $default_value = '';
        isset($arrProp["session_name"]) ? $session_name = $arrProp["session_name"] : $session_name = '';

        $spRetHtml = '';

        $styleLabel = '';
        if ($width_label != '')
            $styleLabel = "width:" . $width_label;
        if ($label == '')
            $label = "&nbsp;";
        $v_str_label = '<label class="normal_label" style = "float: none;' . $styleLabel . '">' . $label . '</label>';

        $value = '';
        $inputid = $sformFielName . '_' . $xml_tag_in_db . '_#row';

        switch ($type) {
            case "textbox";
                $spRetHtml .= $v_str_label;
                $spRetHtml .= '<input type="text" id="' . $inputid . '"  name="' . $inputid . '" class="normal_textbox formfielgen ' . $sformFielName . '_formfielgen_#row" value="' . $value . '" style="width:' . $width . '" ' . self::_generateEventAndFunction($js_function_list, $js_action_list) . '" >';
                $spRetHtml .= $note;
                break;
            default:
                $spRetHtml = $styleLabel;
        }
        return $spRetHtml;
    }

    /**
     * @param $spDataFormat
     * @return string
     */
    private function _generateVerifyProperty($spDataFormat)
    {
        switch ($spDataFormat) {
            case "isemail";
                $psRetHtml = " isemail=true ";
                break;
            case "isdate";
                $psRetHtml = " isdate=true ";
                break;
            case "isnumeric";
                $psRetHtml = " isnumeric=true message = 'KHONG DUNG DINH DANG SO NGUYEN' ";
                break;
            case "isdouble";
                $psRetHtml = " isdouble=true ";
                break;
            case "ismoney";
                $psRetHtml = " isnumeric=true onKeyUp='format_money(this,event)' ";
                break;
            case "ismoney_float";
                $psRetHtml = " isfloat=true onKeyUp='format_money(this)' ";
                break;
            default:
                $psRetHtml = "";
        }
        return $psRetHtml;
    }

    /**
     * @param $paArrList
     * @param $iIdColumn
     * @param $psNameColumn
     * @param $psSelectedValue
     * @return string
     */
    private function _getValueFromArray($paArrList, $iIdColumn, $psNameColumn, $psSelectedValue)
    {
        $pValue = "";
        $count = sizeof($paArrList);
        for ($rowIndex = 0; $rowIndex < $count; $rowIndex++) {
            $strID = trim($paArrList[$rowIndex][$iIdColumn]);
            $DspColumn = trim($paArrList[$rowIndex][$psNameColumn]);
            if ($strID == $psSelectedValue) {
                $pValue = $DspColumn;
            }
        }
        return $pValue;
    }

    /**
     * @param $psXmlStringInFile
     * @param $psItemTag
     * @return array
     */
    public function _convertXmlStringToArray($psXmlStringInFile, $psItemTag)
    {
        $paArrListItem = array();
        $i = 0;
        $objStructRax = new RAX();
        $objStructRec = new RAX();
        $objStructRax->open($psXmlStringInFile);
        $objStructRax->record_delim = $psItemTag;
        $objStructRax->parse();
        $objStructRec = $objStructRax->readRecord();
        while ($objStructRec) {
            $pSstructRow = $objStructRec->getRow();
            $paArrListItem[$i] = $pSstructRow;
            $i++;
            $objStructRec = $objStructRax->readRecord();
        }
        return $paArrListItem;
    }

    /**
     * @param $pType
     * @param $pValue
     * @return string
     */
    private function _generatePropertyType($pType, $pValue)
    {
        switch ($pType) {
            case "optional";
                if ($pValue == "false") {
                    $psRetHtml = "";
                } else {
                    $psRetHtml = " optional = true ";
                }
                break;
            case "readonly";
                if ($pValue == "false") {
                    $psRetHtml = "";
                } else {
                    $psRetHtml = " readonly = true ";
                }
                break;
            case "disabled";
                if ($pValue == "false") {
                    $psRetHtml = " ";
                } else {
                    $psRetHtml = " disabled = true ";
                }
                break;
            default:
                $psRetHtml = "";
        }
        return $psRetHtml;
    }

    /**
     * @param $psJsFunctionList
     * @param $psJsActionList
     * @return string
     */
    public function _generateEventAndFunction($psJsFunctionList, $psJsActionList)
    {
        $arrJsFunctionList = explode(";", $psJsFunctionList);
        $arrJsActionList = explode(";", $psJsActionList);
        $pCountFunction = sizeof($arrJsFunctionList);
        $pCountAction = sizeof($arrJsActionList);
        $this->count = $pCountFunction > $pCountAction ? $pCountAction : $pCountFunction;
        $v_temp = "";
        for ($i = 0; $i < $this->count; $i++) {
            $v_temp = $v_temp . " $arrJsActionList[$i]='$arrJsFunctionList[$i]' ";
        }
        return $v_temp;
    }

    /**
     * @param $p_session_name
     * @param $p_session_id_index
     * @param $session_name_index
     * @param $p_valuelist
     * @param string $p_height
     * @return string
     */
    public function _generateHtmlForMultipleCheckboxFromSession($p_session_name, $p_session_id_index, $session_name_index, $p_valuelist, $p_height = 'auto')
    {
        $arrValue = explode(",", $p_valuelist);
        $count_value = sizeof($arrValue);
        $v_tr_name = '"tr_' . $this->formFielName . '"';
        $v_radio_name = '"rad_' . $this->formFielName . '"';
        if ($this->dspDiv == 1) {// = 1 thi hien di DIV
            $strHTML = "<DIV title='$this->tooltip' STYLE='overflow: auto; height:$p_height;padding-left:0px;margin:0px;'>";
            $strHTML = $strHTML . "<table id = 'table_$this->formFielName' class='list_table2'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='48%'><col width='2%'><col width='48%'>";
        } else {
            $strHTML = "<table id = 'table_$this->formFielName' class='list_table2'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='98%'>";
        }
        $i = 0;
        $v_item_url_onclick = "_change_item_checked(this,\"table_$this->formFielName\")";
        session_start();
        //var_dump($_SESSION);exit;
        foreach ($_SESSION[$p_session_name] as $arrList) {
            $v_item_id = $arrList["$p_session_id_index"];
            $v_item_name = $arrList[$session_name_index];
            if ($this->dspDiv != 1) {
                if ($v_current_style_name == "odd_row") {
                    $v_current_style_name = "round_row";
                } else {
                    $v_current_style_name = "odd_row";
                }
            } else {
                if ($i % 2 == 0) {
                    if ($v_current_style_name == "odd_row") {
                        $v_current_style_name = "round_row";
                    } else {
                        $v_current_style_name = "odd_row";
                    }
                }
            }
            $v_item_checked = "";
            for ($j = 0; $j < $count_value; $j++)
                if ($arrValue[$j] == $v_item_id) {
                    $v_item_checked = "checked";
                    break;
                }
            if ($i % 2 == 0 && $this->dspDiv == 1) {
                $strHTML = $strHTML . "<tr id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name'>";
            }
            if ($this->dspDiv != 1) {
                $strHTML = $strHTML . "<tr id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name'>";
            }
            if ($this->viewMode && $this->readonlyInEditMode == "true") {
                ;
            } else {
                $strHTML = $strHTML . "<td><input type='checkbox' nameUnit = '$v_item_name' id='$this->formFielName' name='chk_multiple_checkbox' value='$v_item_id' xml_tag_in_db_name ='$this->formFielName' $v_item_checked " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='$v_item_url_onclick' onKeyDown='change_focus(document.forms[0],this,event)'></td>";
            }
            if ($this->dspDiv == 1) {
                $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td>";
            } else {
                $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td></tr></div>";
            }
            if ($i % 2 != 1 && $i == sizeof($_SESSION[$p_session_name]) - 1 && $this->dspDiv == 1) {
                $strHTML = $strHTML . "<td colspan = \"2\"> </td>";
            }
            if ($i % 2 == 1 && $this->dspDiv == 1) {
                $strHTML = $strHTML . "</tr>";
            }
            $i++;
        }
        if ($p_valuelist != "" && $p_valuelist <> 0) {   //Kiem tra xem Hieu chinh hay la them moi
            $v_checked_show_row_all = "";
            $v_checked_show_row_selected = "checked";
        } else {
            $v_checked_show_row_all = "checked";
            $v_checked_show_row_selected = "";
        }
        if ($this->sLabel == "") {
            $this->sLabel = "&#273;&#7889;i t&#432;&#7907;ng";
        } else {
            $this->sLabel = Extra_Xml::_firstStringToLower($this->sLabel);
        }
        $strHTML = $strHTML . "</table>";
        // = 1 thi hien thi DIV
        if ($this->dspDiv == 1) {
            $strHTML = $strHTML . "</DIV>";
        }
        if ($this->viewMode && $this->readonlyInEditMode == "true") {
            ;
        } else {
            $strHTML = $strHTML . "<table width='100%' cellpadding='0' cellspacing='0'><colgroup width = '100%' span = '2'><col width='2%'><col width='98%'></colgroup>";
            $strHTML = $strHTML . "<tr><td class='small_radiobutton' colspan='10' align='right'>";
            if ($this->dspDiv != 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='1' hide='true' $v_checked_show_row_all " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_all($v_radio_name,$v_tr_name)' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_show_row_all(\"table_$this->formFielName\");'>Hi&#7875;n th&#7883; t&#7845;t c&#7843; $this->sLabel</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='2' hide='true' $v_checked_show_row_selected " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_selected($v_radio_name,$v_tr_name)' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_show_row_selected(\"table_$this->formFielName\");'>Ch&#7881; hi&#7875;n th&#7883; c&#225;c $this->sLabel &#273;&#432;&#7907;c ch&#7885;n</font>";
            }
            if ($this->dspDiv == 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='1' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,0);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[0],0);'>Ch&#7885;n t&#7845;t c&#7843;</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='2' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,1);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[1],1);'>B&#7887; ch&#7885;n t&#7845;t c&#7843;</font>";
            }
            $strHTML = $strHTML . "</td></tr>";
            $strHTML = $strHTML . "</table>";
        }
        $strHTML = '<div style = "width:' . $this->width . ';">' . $strHTML . '</div>';
        return $strHTML;
    }

    /**
     * @param $pStr
     * @return string
     */
    public function _firstStringToLower($pStr)
    {
        $psTemp = substr($pStr, 1, strlen($pStr));
        $psTemp = strtolower(substr($pStr, 0, 1)) . $psTemp;
        return $psTemp;
    }

    /**
     * @param $psXmlFile
     * @param $psXmlTag
     * @param $pArrAllItem
     * @param $psColumeNameOfXmlString
     * @param $NamOfColId
     * @param $sFullTextSearch
     * @param bool $pHaveMove
     * @param bool $pOnclick
     * @param string $sAction
     * @return string
     */
    public function _xmlGenerateList($psXmlFile, $psXmlTag, $pArrAllItem, $psColumeNameOfXmlString, $NamOfColId, $sFullTextSearch, $pHaveMove = false, $pOnclick = false, $sAction = '')
    {
        global $v_current_style_name, $v_id_column;
        global $v_onclick_up, $v_onclick_down, $v_have_move;
        global $p_arr_item;
        global $objectId;
        $v_current_style_name = "round_row";
        $v_have_move = $pHaveMove;
        //Goi class lay tham so cau hinh he thong
        //Zend_Loader::loadClass('Extra_Init');
        $ojbEfyInitConfig = new Extra_Init();
        $objConfiXml = new Zend_Config_Xml($psXmlFile);
        $arrXml = $objConfiXml->toArray();
        //var_dump($arrXml);
        $this->sFullTextSearch = $sFullTextSearch;
        $this->sAction = $sAction;
        //Doc file XML
        $this->xmlStringInFile = Extra_Util::_readFile($psXmlFile);
        //Dem so phan tu cua mang
        $this->count = sizeof($pArrAllItem);
        //Bang chua cac thanh phan cua form
        $psHtmlString = '';
        $psHtmlString = $psHtmlString . '<table class="list_table2" width="99%" cellpadding="0" cellspacing="0" border="0" align="center" id="table1">';
        $arrTable_Struct = $arrXml['list_of_object']['list_body']['col'];
        //Tao header cho bang
        $psHtmlTempWidth = '';
        $psHtmlTempLabel = '';
        foreach ($arrTable_Struct as $col) {
            $this->v_label = $col["label"];
            $this->width = $col["width"];
            $psHtmlTempWidth = $psHtmlTempWidth . '<col width="' . $this->width . '">';
            $psHtmlTempLabel = $psHtmlTempLabel . "<td class='title' align='center'  >" . $this->v_label . '</td>';
        }
        $psHtmlString = $psHtmlString . $psHtmlTempWidth;
        $psHtmlString = $psHtmlString . '<tr class="header">';
        $psHtmlString = $psHtmlString . $psHtmlTempLabel;
        $psHtmlString = $psHtmlString . '<tr>';
        //Day du lieu vao bang
        for ($iRow = 0; $iRow < sizeof($pArrAllItem); $iRow++) {
            $objectId = $pArrAllItem[$iRow][$NamOfColId];
            if ($v_current_style_name == "odd_row") {
                $v_current_style_name = "round_row";
            } else {
                $v_current_style_name = "odd_row";
            }
            $psHtmlString = $psHtmlString . '<tr class="' . $v_current_style_name . ' ">';
            foreach ($arrTable_Struct as $col) {
                $v_type = $col["type"];
                $this->v_align = $col["align"];
                $this->xmlData = $col["xml_data"];
                isset($col["input_data"]) ? $this->inputData = $col["input_data"] : $this->inputData = '';
                isset($col["column_name"]) ? $this->columnName = $col["column_name"] : $this->columnName = '';
                isset($col["xml_tag_in_db"]) ? $this->xmlTagInDb = $col["xml_tag_in_db"] : $this->xmlTagInDb = '';
                isset($col["php_function"]) ? $this->phpFunction = $col["php_function"] : $this->phpFunction = '';
                isset($col["id_column"]) ? $v_id_column = $col["id_column"] : $v_id_column = '';
                isset($col["selectbox_option_sql"]) ? $this->selectBoxOptionSql = $col["selectbox_option_sql"] : $this->selectBoxOptionSql = '';
                isset($col["readonly_in_edit_mode"]) ? $this->readonlyInEditMode = $col["readonly_in_edit_mode"] : $this->readonlyInEditMode = '';
                isset($col["disabled_in_edit_mode"]) ? $this->disabledInEditMode = $col["disabled_in_edit_mode"] : $this->disabledInEditMode = '';
                isset($col["public_list_code"]) ? $this->publicListCode = $col["public_list_code"] : $this->publicListCode = '';
                if ($this->xmlData == 'false') {
                    $this->value = $pArrAllItem[$iRow][$this->columnName];
                    if ($v_type == 'json') {
                        $arrList = json_decode($this->value, true);
                        $sValue = '';
                        $sAttr = $col["attr"];
                        isset($col["ItemIndex"]) ? $sIndex = $col["ItemIndex"] : $sIndex = '';
                        if ($sIndex != '') {
                            foreach ($arrList as $key => $item) {
                                if ($sIndex == $key) {
                                    $sAttrItem = $sAttr . '_' . $key;
                                    $sValue .= $item[$sAttrItem];
                                }
                            }
                        } else {
                            foreach ($arrList as $key => $item) {
                                $sAttrItem = $sAttr . '_' . $key;
                                if (!$key) {
                                    $sValue .= $item[$sAttrItem];
                                } else {
                                    $sValue .= '<br>' . $item[$sAttrItem];
                                }
                            }
                        }
                        $this->value = $sValue;
                    }
                    $p_arr_item = $pArrAllItem[$iRow];
                    if ($v_id_column == "true") {
                        $this->value_id = $pArrAllItem[$iRow][$this->columnName];
                        if (!$pOnclick) {
                            $this->url = "item_onclick('" . $this->value_id . "')";
                        } else {
                            $this->url = "";
                        }
                        $v_onclick_up = "btn_move_updown('" . $this->value_id . "','UP')";
                        $v_onclick_down = "btn_move_updown('" . $this->value_id . "','DOWN')";
                    }
                    $psHtmlString = $psHtmlString . $this->_generateHtmlForColumn($v_type);
                } else {
                    $strxml = $pArrAllItem[$iRow][$psColumeNameOfXmlString];
                    if ($strxml != '') {
                        $strxml = '<?xml version="1.0" encoding="UTF-8"?>' . $strxml;
                        $this->value = $this->_xmlGetXmlTagValue($strxml, 'data_list', $this->xmlTagInDb);
                        if ($v_type == 'json') {
                            $arrList = json_decode(html_entity_decode($this->value), true);
                            $sValue = '';
                            $sAttr = $col["attr"];
                            isset($col["ItemIndex"]) ? $sIndex = $col["ItemIndex"] : $sIndex = '';
                            if ($sIndex != '') {
                                if($arrList){
                                    foreach ($arrList as $key => $item) {
                                        if ($sIndex == $key) {
                                            $sAttrItem = $sAttr . '_' . $key;
                                            $sValue .= $item[$sAttrItem];
                                        }
                                    }
                                }
                            } else {
                                if($arrList){
                                    foreach ($arrList as $key => $item) {
                                        $sAttrItem = $sAttr . '_' . $key;
                                        if (!$key) {
                                            $sValue .= $item[$sAttrItem];
                                        } else {
                                            $sValue .= '<br>' . $item[$sAttrItem];
                                        }
                                    }
                                }
                            }
                            $this->value = $sValue;
                        }
                    } else {
                        $this->value = '';
                    }
                    $psHtmlString = $psHtmlString . $this->_generateHtmlForColumn($v_type);
                }
            }
            $psHtmlString = $psHtmlString . '</tr>';
        }
        if (!$pOnclick) {
            $psHtmlString = $psHtmlString . Extra_Util::_addEmptyRow(sizeof($pArrAllItem), 15, $v_current_style_name, sizeof($arrTable_Struct));
        } else {
            $psHtmlString = $psHtmlString . Extra_Util::_addEmptyRow(sizeof($pArrAllItem), 15, $v_current_style_name, sizeof($arrTable_Struct));

        }
        $psHtmlString = $psHtmlString . '</table>';
        return $psHtmlString;
    }

    /**
     * @param $psXmlFile
     * @return string
     */
    public function genEcsPrintGenerate($psXmlFile)
    {
        $objConfiXml = new Zend_Config_Xml($psXmlFile);
        $psHtmlString = "<div id='cssmenu' style='display: inline-block;'><ul><li class='has-sub' style='border-top: none;'><a href='#'><span>IN</span></a><ul>";
        if (isset($objConfiXml->list_print->item_print)) {
            $arrXml = $objConfiXml->list_print->item_print->toArray();
            if (!isset($arrXml[0]['label'])) {
                $temp = array($arrXml);
                $arrXml = $temp;
            }
        } else {
            return '';
        }
        foreach ($arrXml as $col) {
            $psHtmlString .= "<li><a class='print_receive' tempcode='" . $col['tempcode'] . "' href='#'><span>" . $col['label'] . "</span></a></li>";
        }
        $psHtmlString .= "</ul></li></ul></div>";
        return $psHtmlString;
    }

    /**
     * @param $pType
     * @return string
     */
    private function _generateHtmlForColumn($pType)
    {
        global $pClassname, $objectId;
        $sAction = "item_onclick('" . $objectId . "','" . $this->sAction . "')";
        //Tao doi tuong trong class Extra_Util
        $objEfyLib = new Extra_Util();
        switch ($pType) {
            case "checkbox";
                $psRetHtml = '<td align="' . $this->v_align . '"><input type="checkbox" onclick="selectrow(this)" name="chk_item_id" id="chk_item_id" ' . ' value="' . $this->value . '" /></td>';
                break;
            case "selectbox";
                if ($this->inputData == "efylist") {
                    $v_xml_data_in_url = Extra_Util::_readFile($this->efyListWebSitePath . "listxml/output/" . $this->publicListCode . ".xml");
                    $arr_list_item = Extra_Xml::_convertXmlStringToArray($v_xml_data_in_url, "item");
                } else {
                    //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                    $this->selectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $this->selectBoxOptionSql);
                    $arr_list_item = Extra_Db::adodbQueryDataInNumberMode($this->selectBoxOptionSql, $this->cacheOption);
                }
                $psRetHtml = "<td align='.$this->v_align.'><select class='normal_selectbox' name='sel_item' title='$this->tooltip' style='width:100%' " . $this->_generatePropertyType("optional", $v_optional) . $this->_generatePropertyType("readonly", $this->readonlyInEditMode) . $this->_generatePropertyType("disabled", $this->disabledInEditMode) . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . " xml_tag_in_db='$this->xmlTagInDb' xml_data='$this->xmlData' column_name='$this->columnName' message='$v_message' onKeyDown='change_focus(document.forms[0],this,event)'>";
                $psRetHtml = $psRetHtml . "<option id='' value=''>--- Ch&#7885;n $this->v_label ---</option>" . Extra_Util::_generateSelectOption($arr_list_item, $this->selectBoxIdColumn, $this->selectBoxIdColumn, $this->selectBoxNameColumn, $this->value);
                $psRetHtml = $psRetHtml . "</select></td>";
                break;

            case "textbox";
                if ($this->phpFunction != "" && !is_null($this->phpFunction)) {
                    $this->value = call_user_func($this->phpFunction, $this->value);
                }
                $psRetHtml = '<td align="' . $this->v_align . '"><input type="text" name="txt_item_id" value="' . $this->value . '" style="width:100%" ' . $this->_generatePropertyType("readonly", $this->readonlyInEditMode) . $this->_generatePropertyType("disabled", $this->disabledInEditMode) . ' maxlength="' . $this->maxlength . '"' . Extra_Xml::_generateEventAndFunction($this->jsFunctionList, $this->jsActionList) . '>';
                $psRetHtml = $psRetHtml . '</td>';
                break;

            case "function";
                $objClass = new $pClassname;
                $psRetHtml = '<td class="data"   align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . $objClass->$this->phpFunction($this->value) . '&nbsp;</td>';
                break;

            case "date";
                $sDate = Extra_Ecs::searchCharColor($this->sFullTextSearch, $objEfyLib->_yyyymmddToDDmmyyyy($this->value));
                $psRetHtml = '<td class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . '&nbsp;' . $sDate . '&nbsp;</td>';
                break;

            case "time";
                $psRetHtml = '<td class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . '&nbsp;' . Extra_Ecs::searchCharColor($this->sFullTextSearch, $objEfyLib->_yyyymmddToHHmm($this->value)) . '&nbsp;</td>';
                break;

            case "text";
                if ($this->xmlTagInDb == 'ho_ten_nk') {
                    $psRetHtml = '<td class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . Extra_Ecs::searchStringColor2($this->sFullTextSearch, $this->value) . '&nbsp;</td>';
                } else
                    $psRetHtml = '<td style="padding-left:5px" class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . Extra_Ecs::searchStringColor($this->sFullTextSearch, $this->value) . '&nbsp;</td>';
                break;

            case "json";
                $psRetHtml = '<td style="padding-left:5px" class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . Extra_Ecs::searchStringColor($this->sFullTextSearch, $this->value) . '&nbsp;</td>';
                break;

            case "char";
                $psRetHtml = '<td class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . '&nbsp;' . Extra_Ecs::searchCharColor($this->sFullTextSearch, $this->value) . '&nbsp;</td>';
                break;

            case "identity";
                $psRetHtml = '<td class="data" align="' . $this->v_align . '" onclick="set_hidden(this,document.getElementsByName(\'chk_item_id\'),document.getElementById(\'hdn_list_id\'),\'' . $objectId . '\')" ondblclick="' . $sAction . '">' . $this->v_inc . '&nbsp;</td>';
                break;

            default:
                $psRetHtml = $this->value;
        }
        return $psRetHtml;
    }

    /**
     * @param $p_sql_replace
     * @param $p_xml_string_in_file
     * @param $p_xml_tag
     * @param $p_filter_xml_string
     * @param string $p_path_filter_form
     * @return mixed
     */

    function _replaceTagXmlValueInSql($p_sql_replace, $p_xml_string_in_file, $p_xml_tag, $p_filter_xml_string, $p_path_filter_form = '')
    {
        //Tao mang luu thong tin cua cac phan tu tren form
        $objConfigXml = new Zend_Config_Xml($p_xml_string_in_file);
        if ($p_xml_tag) {
            $arrTags = explode("/", $p_xml_tag);
            $strcode = '$arrfilter_rows = $objConfigXml->' . $arrTags[0];
            for ($i = 1; $i < sizeof($arrTags); $i++)
                $strcode .= '->' . $arrTags[$i];
            eval($strcode . '->toArray();');
        }
        if ($p_path_filter_form) {
            $arrTags = explode("/", $p_path_filter_form);
            $strcode = '$arrFilter = $objConfigXml->' . $arrTags[0];
            for ($i = 1; $i < sizeof($arrTags); $i++)
                $strcode .= '->' . $arrTags[$i];
            eval($strcode . '->toArray();');
        }
        $p_sql_replace = Extra_Util::_restoreXmlBadChar($p_sql_replace);
        //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
        $p_sql_replace = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $p_sql_replace);
        //
        $v_sql_replace_temp = $p_sql_replace;
        //neu co nhieu hon 1 dong tim kiem
        if (sizeof($arrfilter_rows) > 1) {
            foreach ($arrfilter_rows as $rows) {
                $v_tag_list = $rows["tag_list"];
                $arr_tag = explode(",", $v_tag_list);
                for ($i = 0; $i < sizeof($arr_tag); $i++) {
                    $v_data_format = $arrFilter[$arr_tag[$i]]["data_format"];
                    $this->xmlTagInDb = $arrFilter[$arr_tag[$i]]["xml_tag_in_db"];
                    if ($p_filter_xml_string != "") {
                        $value = $this->_xmlGetXmlTagValue($p_filter_xml_string, 'data_list', $arr_tag[$i]);
                        $value_input = Extra_Util::_replaceXmlBadChar($value);
                        if ($v_data_format == "isdate") {
                            $value_input = Extra_Util::_ddmmyyyyToYYyymmdd($value_input);
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
        } else {
            $v_tag_list = $arrfilter_rows["tag_list"];
            $arr_tag = explode(",", $v_tag_list);
            for ($i = 0; $i < sizeof($arr_tag); $i++) {
                $v_data_format = $arrFilter[$arr_tag[$i]]["data_format"];
                $this->xmlTagInDb = $arrFilter[$arr_tag[$i]]["xml_tag_in_db"];
                if ($p_filter_xml_string != "") {
                    $value = $this->_xmlGetXmlTagValue($p_filter_xml_string, 'data_list', $arr_tag[$i]);
                    $value_input = Extra_Util::_replaceXmlBadChar($value);
                    if ($v_data_format == "isdate") {
                        $value_input = Extra_Util::_ddmmyyyyToYYyymmdd($value_input);
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
     * @param $psXmlTagList
     * @param $psValueList
     * @return string
     */
    public function _xmlGenerateXmlDataString($psXmlTagList, $psValueList)
    {
        //Tao doi tuong Extra_Util
        $objLib = new Extra_Util();
        //Tao doi tuong config
        $objConfig = new Extra_Init();
        $arrConst = $objConfig->_setProjectPublicConst();

        $strXML = '<?xml version="1.0"?><root><data_list>';
        for ($i = 0; $i < $objLib->_listGetLen($psXmlTagList, $arrConst['_CONST_SUB_LIST_DELIMITOR']); $i++) {
            $strXML = $strXML . "<" . $objLib->_listGetAt($psXmlTagList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR']) . ">";
            $strXML = $strXML . trim($objLib->_restoreXmlBadChar($objLib->_listGetAt($psValueList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR'])));
            $strXML = $strXML . "</" . $objLib->_listGetAt($psXmlTagList, $i, $arrConst['_CONST_SUB_LIST_DELIMITOR']) . ">";
        }
        $strXML = $strXML . "</data_list></root>";
        return $strXML;
    }

    /**
     * @param $arrList
     * @param $IdColumn
     * @param $NameColumn
     * @param $Valuelist
     * @param array $arrPara
     * @param string $height
     * @return string
     */
    public function _generateHtmlForMultipleCheckbox($arrList, $IdColumn, $NameColumn, $Valuelist, $arrPara = array(), $height = 'auto')
    {
        global $v_current_style_name;
        $arr_value = explode(",", $Valuelist);
        $count_item = sizeof($arrList);
        $count_value = sizeof($arr_value);
        $v_tr_name = '"tr_' . $this->formFielName . '"';
        $v_radio_name = '"rad_' . $this->formFielName . '"';
        if (is_array($arrPara) && count($arrPara) > 0) {
            $this->dspDiv = $arrPara['dspDiv'];
            $this->readonlyInEditMode = 'false';
            $this->disabledInEditMode = 'false';
        }
        $strHTML = '';
        if ($this->dspDiv == 1) {// = 1 thi hien di DIV
            $strHTML = $strHTML . "<DIV title='$this->tooltip' STYLE='overflow: auto;padding-left:0px;margin:0px; width:100%;height:$height;'>";
            $strHTML = $strHTML . "<table id = 'table_$this->formFielName' class='list_table2'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='48%'><col width='2%'><col width='48%'>";
        } else {
            $strHTML = $strHTML . "<table id = 'table_$this->formFielName' class='list_table2 list_table5'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='98%'>";
        }
        if ($count_item > 0) {
            $i = 0;
            $v_item_url_onclick = "_change_item_checked(this,\"table_$this->formFielName\")";
            while ($i < $count_item) {
                $v_item_id = $arrList[$i][$IdColumn];
                $v_item_name = $arrList[$i][$NameColumn];
                if ($this->dspDiv != 1) {
                    if ($v_current_style_name == "odd_row") {
                        $v_current_style_name = "round_row";
                    } else {
                        $v_current_style_name = "odd_row";
                    }
                } else {
                    if ($i % 2 == 0) {
                        if ($v_current_style_name == "odd_row") {
                            $v_current_style_name = "round_row";
                        } else {
                            $v_current_style_name = "odd_row";
                        }
                    }
                }
                $v_item_checked = "";
                for ($j = 0; $j < $count_value; $j++) {
                    if ($arr_value[$j] == $v_item_id) {
                        $v_item_checked = "checked";
                        break;
                    }
                }
                if ($i % 2 == 0 && $this->dspDiv == 1)
                    $strHTML = $strHTML . "<tr  style = 'width:100%;' id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name '>";
                if ($this->dspDiv != 1)
                    $strHTML = $strHTML . "<tr   style = 'width:100%;' id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name '>";
                if ($this->viewMode && $this->readonlyInEditMode == "true") {
                    ;
                } else {
                    $strHTML = $strHTML . "<td><input id='$this->formFielName' type='checkbox' name='chk_multiple_checkbox' value='$v_item_id' xml_tag_in_db_name ='$this->formFielName' $v_item_checked " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='$v_item_url_onclick' onKeyDown='change_focus(document.forms[0],this,event)'></td>";
                }
                if ($this->dspDiv == 1) {
                    $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td>";
                } else {
                    $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td></tr>";
                }
                if ($i % 2 != 1 && $i == $count_item - 1 && $this->dspDiv == 1) {
                    $strHTML = $strHTML . "<td colspan = \"2\"> </td>";
                }
                if ($i % 2 == 1 && $this->dspDiv == 1) {
                    $strHTML = $strHTML . "</tr>";
                }
                $i++;
            }
        }
        if ($Valuelist != "") {   //Kiem tra xem Hieu chinh hay la them moi
            $v_checked_show_row_all = "";
            $v_checked_show_row_selected = "checked";
        } else {
            $v_checked_show_row_all = "checked";
            $v_checked_show_row_selected = "";
        }
        if ($this->v_label == "") {
            $this->v_label = "&#273;&#7889;i t&#432;&#7907;ng";
        } else {
            $this->v_label = self::_firstStringToLower($this->v_label);
        }
        $strHTML = $strHTML . "</table>";
        if ($this->dspDiv == 1)
            $strHTML = $strHTML . "</DIV>";
        if ($this->viewMode && $this->readonlyInEditMode == "true") {
            ;
        } else {
            $strHTML = $strHTML . "<table width='100%' cellpadding='0' cellspacing='0'><colgroup width = '100%' span = '2'><col width='2%'><col width='98%'></colgroup>";
            $strHTML = $strHTML . "<tr><td class='small_radiobutton' colspan='10' align='right'>";
            if ($this->dspDiv != 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='1' hide='true' $v_checked_show_row_all " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_all(\"table_$this->formFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_show_row_all(\"table_$this->formFielName\");'>Hi&#7875;n th&#7883; t&#7845;t c&#7843; $this->sLabel</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='2' hide='true' $v_checked_show_row_selected " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_selected(\"table_$this->formFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_show_row_selected(\"table_$this->formFielName\");'>Ch&#7881; hi&#7875;n th&#7883; c&#225;c $this->sLabel &#273;&#432;&#7907;c ch&#7885;n</font>";
            }
            if ($this->dspDiv == 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='1' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,0);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[0],0);'>Ch&#7885;n t&#7845;t c&#7843;</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='2' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,1);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[1],1);'>B&#7887; ch&#7885;n t&#7845;t c&#7843;</font>";

            }
            $strHTML = $strHTML . "</td></tr>";
            $strHTML = $strHTML . "</table>";
        }
        $strHTML = '<div style = "width:' . $this->width . ';">' . $strHTML . '</div>';
        return $strHTML;
    }

    /**
     * @param $arrList
     * @param $IdColumn
     * @param $NameColumn
     * @param $Valuelist
     * @param array $arrPara
     * @param string $height
     * @return string
     */
    public function _generateHtmlForMultipleCheckbox_fileAttach($arrList, $IdColumn, $NameColumn, $Valuelist, $arrPara = array(), $height = 'auto')
    {
        global $v_current_style_name;
        $arr_value = explode(",", $Valuelist);
        $count_item = sizeof($arrList);
        $count_value = sizeof($arr_value);
        $v_tr_name = '"tr_' . $this->formFielName . '"';
        $v_radio_name = '"rad_' . $this->formFielName . '"';
        if (is_array($arrPara) && count($arrPara) > 0) {
            $this->dspDiv = $arrPara['dspDiv'];
            $this->readonlyInEditMode = 'false';
            $this->disabledInEditMode = 'false';
        }
        $strHTML = '';
        if ($this->dspDiv == 1) {// = 1 thi hien di DIV
            $strHTML = $strHTML . "<DIV title='$this->tooltip' STYLE='overflow: auto;padding-left:0px;margin:0px; width:100%;height:$height;'>";
            $strHTML = $strHTML . "<table id = 'table_$this->formFielName' class='list_table2'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='48%'><col width='2%'><col width='48%'>";
        } else {
            $strHTML = $strHTML . "<table id = 'table_$this->formFielName' class='list_table2 list_table5'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='60%'><col width='38%'>";
        }
        if ($count_item > 0) {
            $i = 0;
            $v_item_url_onclick = "_change_item_checked(this,\"table_$this->formFielName\")";
            // duong dan den noi chua file attach
            $urlFileAttach = new Extra_Init();
            //ma ho so
            isset($_SESSION['NET_RECORDID']) ? $v_record_id = $_SESSION['NET_RECORDID'] : $v_record_id = '';
            if (is_null($v_record_id) || $v_record_id == '') {
                isset($_SESSION['RECORDID']) ? $v_record_id = $_SESSION['RECORDID'] : $v_record_id = '';
            }
            //var_dump($arrList);exit;
            $modSendRecord = new Extra_Ecs();
            while ($i < $count_item) {
                $v_item_id = $arrList[$i][$IdColumn];
                $v_item_name = $arrList[$i][$NameColumn];
                //lay file dinh kem da co
                $arr_single_data = $modSendRecord->eCSFileGetSingle($v_record_id, $v_item_id);//var_dump($arr_single_data);exit;
                $v_file = '';
                if($arr_single_data){
                    $v_file = trim($arr_single_data[0]['C_FILE_NAME']);//echo $v_file;exit;
                }
                if ($v_file != '') {
                    $arrFilename = explode('!~!', $v_file);
                    $file_name = $arrFilename[1];
                    $file_id = explode("_", $arrFilename[0]);
                    //Get URL
                    $sActionUrl = $urlFileAttach->_setAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sActionUrl)) {
                        $sActionUrl = $urlFileAttach->_setDvcAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                    }
                }
                if ($this->dspDiv != 1) {
                    if ($v_current_style_name == "odd_row") {
                        $v_current_style_name = "round_row";
                    } else {
                        $v_current_style_name = "odd_row";
                    }
                } else {
                    if ($i % 2 == 0) {
                        if ($v_current_style_name == "odd_row") {
                            $v_current_style_name = "round_row";
                        } else {
                            $v_current_style_name = "odd_row";
                        }
                    }
                }
                $v_item_checked = "";
                for ($j = 0; $j < $count_value; $j++) {
                    if ($arr_value[$j] == $v_item_id) {
                        $v_item_checked = "checked";
                        break;
                    }
                }
                if ($i % 2 == 0 && $this->dspDiv == 1)
                    $strHTML = $strHTML . "<tr  style = 'width:100%;' id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name '>";
                if ($this->dspDiv != 1)
                    $strHTML = $strHTML . "<tr   style = 'width:100%;' id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name '>";
                if ($this->viewMode && $this->readonlyInEditMode == "true") {
                    ;
                } else {
                    $strHTML = $strHTML . "<td><input type='hidden' id='hdn_attach_filename$i' value='$v_file'><input id='$this->formFielName' type='checkbox' name='chk_multiple_checkbox' value='$v_item_id' xml_tag_in_db_name ='$this->formFielName' $v_item_checked " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='$v_item_url_onclick' onKeyDown='change_focus(document.forms[0],this,event)'></td>";
                }
                if ($this->dspDiv == 1) {
                    $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td>";
                } else {
                    if ($v_file != '') {
                        $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\"><div id='div_ajax$i'>$v_item_name<a href='$sActionUrl' > ($file_name)  </a></div></td>";
                    } else {
                        $strHTML = $strHTML . "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$this->formFielName')\">$v_item_name</td>";
                    }
                    $v_delete_file = "javascript:delete_file(\"dk_file_attach$i\");";
                    $v_delete_file_upload = "delete_file_upload(\"hdn_attach_filename$i\",$i,\"$v_item_name\");";
                    $strHTML = $strHTML . "<td style='width:38%' style='display:'><input type=file optional=true size='31%' class=small_textbox name=dk_file_attach$i onChange='_change_item_checked_row($this->formFielName,dk_file_attach$i,$i,\"table_$this->formFielName\")'><a href='$v_delete_file $v_delete_file_upload' >Xóa</a> </td></tr>";
                }
                if ($i % 2 != 1 && $i == $count_item - 1 && $this->dspDiv == 1) {
                    $strHTML = $strHTML . "<td colspan = \"2\"> </td>";
                }
                if ($i % 2 == 1 && $this->dspDiv == 1) {
                    $strHTML = $strHTML . "</tr>";
                }
                $i++;
            }
        }
        if ($Valuelist != "") {   //Kiem tra xem Hieu chinh hay la them moi
            $v_checked_show_row_all = "";
            $v_checked_show_row_selected = "checked";
        } else {
            $v_checked_show_row_all = "checked";
            $v_checked_show_row_selected = "";
        }
        if ($this->v_label == "") {
            $this->v_label = "&#273;&#7889;i t&#432;&#7907;ng";
        } else {
            $this->v_label = self::_firstStringToLower($this->v_label);
        }
        $strHTML = $strHTML . "</table>";
        if ($this->dspDiv == 1)
            $strHTML = $strHTML . "</DIV>";
        if ($this->viewMode && $this->readonlyInEditMode == "true") {
            ;
        } else {
            $strHTML = $strHTML . "<table width='100%' cellpadding='0' cellspacing='0'><colgroup width = '100%' span = '2'><col width='2%'><col width='98%'></colgroup>";
            $strHTML = $strHTML . "<tr><td class='small_radiobutton' colspan='10' align='right'>";
            if ($this->dspDiv != 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='1' hide='true' $v_checked_show_row_all " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_all(\"table_$this->formFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_show_row_all(\"table_$this->formFielName\");'>Hi&#7875;n th&#7883; t&#7845;t c&#7843; $this->sLabel</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' value='2' hide='true' $v_checked_show_row_selected " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_show_row_selected(\"table_$this->formFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_show_row_selected(\"table_$this->formFielName\");'>Ch&#7881; hi&#7875;n th&#7883; c&#225;c $this->sLabel &#273;&#432;&#7907;c ch&#7885;n</font>";
            }
            if ($this->dspDiv == 1) {
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='1' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,0);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[0].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[0],0);'>Ch&#7885;n t&#7845;t c&#7843;</font>";
                $strHTML = $strHTML . "<input type='radio' name='rad_$this->formFielName' optional='true' value='2' hide='true' " . Extra_Xml::_generatePropertyType("readonly", $this->readonlyInEditMode) . Extra_Xml::_generatePropertyType("disabled", $this->disabledInEditMode) . " onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",this,1);' onKeyDown='change_focus(document.forms[0],this,event)'>
				<font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$this->formFielName\")[1].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$this->formFielName\",document.getElementsByName(\"rad_$this->formFielName\")[1],1);'>B&#7887; ch&#7885;n t&#7845;t c&#7843;</font>";

            }
            $strHTML = $strHTML . "</td></tr>";
            $strHTML = $strHTML . "</table>";
        }
        $strHTML = '<div style = "width:' . $this->width . ';">' . $strHTML . '</div>';
        return $strHTML;
    }

    /**
     * @param $p_valuelist
     * @return string
     */
    function _generateHtmlForTreeUser($p_valuelist)
    {
        Zend_Loader::loadClass('Extra_Util');
        $arr_all_cooperator = explode(",", $p_valuelist);
        $v_cooperator_count = sizeof($arr_all_cooperator);
        if (trim($p_valuelist) != "" && trim($p_valuelist) != "0") {
            $strHTML = '<table class="list_table2" width="100%" cellpadding="0" cellspacing="0">';
            $strHTML = $strHTML . '<col width="10%"><col width="25%"><col width="25%"><col width="30%"><col width="10%">';
            $strHTML = $strHTML . '<tr  class="header">';
            $strHTML = $strHTML . '<td align="center" class="title">STT</td>';
            $strHTML = $strHTML . '<td align="center" class="title">H&#7885; t&#234;n</td>';
            $strHTML = $strHTML . '<td align="center" class="title">Ch&#7913;c v&#7909</td>';
            $strHTML = $strHTML . '<td align="center" class="title">Ph&#242;ng ban</td>';
            $strHTML = $strHTML . '<td align="center" class="title">Phường xã</td>';
            $strHTML = $strHTML . '</tr>';
            $v_current_style_name = '';
            for ($j = 0; $j < $v_cooperator_count; $j++) {
                $v_cooperator_id = $arr_all_cooperator[$j];
                $v_cooperator_name = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'], $v_cooperator_id, 'name');
                $v_cooperator_position_name = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'], $v_cooperator_id, 'position_name');
                $v_cooperator_unit_id = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'], $v_cooperator_id, 'unit_id');
                $v_cooperator_unit_name = Extra_Util::_getItemAttrById($_SESSION['arr_all_unit'], $v_cooperator_unit_id, 'name');
                $v_cooperator_ward_name = '';
                if ($v_current_style_name == "odd_row") {
                    $v_current_style_name = "round_row";
                } else {
                    $v_current_style_name = "odd_row";
                }
                $strHTML = $strHTML . '<tr class="' . $v_current_style_name . '">';
                $strHTML = $strHTML . '<td align="center">' . ($j + 1) . '</td>';
                $strHTML = $strHTML . '<td align="left">&nbsp;' . $v_cooperator_name . '&nbsp;</td>';
                $strHTML = $strHTML . '<td align="left">&nbsp;' . $v_cooperator_position_name . '&nbsp;</td>';
                $strHTML = $strHTML . '<td align="left">&nbsp;' . $v_cooperator_unit_name . '&nbsp;</td>';
                $strHTML = $strHTML . '<td align="center"><a class="conficWard" staff="' . $v_cooperator_id . '" >Chọn</a>&nbsp;' . $v_cooperator_ward_name . '&nbsp;</td>';
                $strHTML = $strHTML . '</tr>';
            }
            $strHTML = $strHTML . '</table>';
            //$strHTML = $strHTML .'</DIV>';
        }
        if (!($this->viewMode && $this->readonlyInEditMode == "true")) {
            //$strHTML = $strHTML .'<DIV STYLE="overflow: auto; height:100pt; padding-left:0px;margin:0px">';
            $strHTML = $strHTML . "<table class='list_table2'  width='100%' cellpadding='0' cellspacing='0'>";
            $strHTML = $strHTML . '<input type="hidden" id = "hdn_item_id" name="hdn_item_id" value="">';
            $v_item_unit_id = Extra_Util::_getRootUnitId();
            $arr_unit = Extra_Util::_getArrAllUnit();
            $arr_staff = Extra_Util::_getArrChildStaff($arr_unit);
            $arr_list = Extra_Util::_attachTwoArray($arr_unit, $arr_staff, 5);
            //var_dump($arr_list);
            $v_current_id = 0;

            $xml_str = Extra_Util::_builtXmlTree($arr_list, $v_current_id, 'true', 'home.jpg', 'home.jpg', 'user.jpg', 'false', $p_valuelist, $this->formFielName);

            $xml = new DOMDocument;
            $xml->loadXML($xml_str);

            $xsl = new DOMDocument;

            $xsl->load("public/treeview.xsl");

            // Configure the transformer
            $proc = new XSLTProcessor();

            $proc->importStylesheet($xsl);// attach the xsl rules

            $ret = $proc->transformToXML($xml);
            $strHTML = $strHTML . "<tr><td>" . $ret . "</td></tr>";

            $strHTML = $strHTML . "</table>";
            //$strHTML = $strHTML . "</DIV>";
        }
        return $strHTML;
    }

    /**
     * @param $spXmlFile
     * @param $sListObject1
     * @param $sListObject2
     * @param $sXmlParentTagList
     * @param $sXmlTag
     * @param $sDelimitor
     * @return string
     */
    public function _xmlGetXmlTagValueFromFile($spXmlFile, $sListObject1, $sListObject2, $sXmlParentTagList, $sXmlTag, $sDelimitor)
    {
        //Neu ton tai xau XML
        if ($spXmlFile != "") {
            $objXmlData = new Zend_Config_Xml($spXmlFile);
            $arrParentTagList = explode($sDelimitor, $sXmlParentTagList);
            $sStr = '';
            foreach ($arrParentTagList as $arrTemp) {
                $sValue = $objXmlData->$sListObject1->$sListObject2->$arrTemp->$sXmlTag;
                $sStr = $sStr . $sValue . $sDelimitor;
            }
            $sStr = substr($sStr, 0, -strlen($sDelimitor));
            return $sStr;
        } else {
            return "";
        }
    }
}
