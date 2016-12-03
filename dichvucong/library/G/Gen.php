<?php
/**
 * @see
 *
 */


/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 09/01/2009
 * Noi dung: Tao lop G_Gen
 */
class G_Gen
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param $sFormFielName
     * @param $arrListItem
     * @param $arrOption
     * @param string $sValue
     * @return string
     */
    public static function _generateHtmlForMultipleCheckbox($sFormFielName, $arrListItem, $arrOption, $sValue = '')
    {
        $v_current_style_name = 'round_row';
        $arr_value = explode(",", $sValue);
        $count_item = sizeof($arrListItem);
        $count_value = sizeof($arr_value);
        $v_tr_name = '"tr_' . $sFormFielName . '"';

        isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
        if ($v_name == '') $v_name = $sFormFielName;
        isset($arrOption["checkbox_multiple_id_column"]) ? $checkBoxMultipleIdColumn = $arrOption["checkbox_multiple_id_column"] : $checkBoxMultipleIdColumn = '';
        isset($arrOption["checkbox_multiple_name_column"]) ? $checkBoxMultipleNameColumn = $arrOption["checkbox_multiple_name_column"] : $checkBoxMultipleNameColumn = '';

        isset($arrOption["width"]) ? $swidth = $arrOption["width"] : $swidth = '';
        isset($arrOption["readonly"]) ? $readonlyInEditMode = $arrOption["readonly"] : $readonlyInEditMode = '';
        isset($arrOption["disabled"]) ? $disabledInEditMode = $arrOption["disabled"] : $disabledInEditMode = '';

        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
        isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
        isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
        isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';

        $strHTML = "<div style='display:none'><input type='textbox' id='$sFormFielName' name='$v_name' value='$sValue' hide='true' readonly " . self::_generatePropertyType("optional", $optOptional) . "xml_data='$xmlData' xml_tag_in_db='$xmlTagInDb' column_name='$columnName' message='$spMessage'></div>";
        $strHTML .= "<table id = 'table_$sFormFielName' class='list-table-data'  width='100%' cellpadding='0' cellspacing='0'><col width='2%'><col width='98%'>";
        if ($count_item > 0) {
            $i = 0;
            while ($i < $count_item) {
                $v_item_id = $arrListItem[$i][$checkBoxMultipleIdColumn];
                $v_item_name = $arrListItem[$i][$checkBoxMultipleNameColumn];
                if ($v_current_style_name == "odd_row") {
                    $v_current_style_name = "round_row";
                } else {
                    $v_current_style_name = "odd_row";
                }
                $v_item_checked = "";
                for ($j = 0; $j < $count_value; $j++)
                    if ($arr_value[$j] == $v_item_id) {
                        $v_item_checked = "checked";
                        break;
                    }
                $strHTML .= "<tr style = 'width:100%;' id=$v_tr_name  value='$v_item_id' checked='$v_item_checked' class='$v_current_style_name'>";
                $strHTML .= "<td><input id='$sFormFielName' type='checkbox' class='checkvaluemark' name='chk_multiple_checkbox' value='$v_item_id' xml_tag_in_db_name ='$sFormFielName' $v_item_checked " . self::_generatePropertyType("readonly", $readonlyInEditMode) . self::_generatePropertyType("disabled", $disabledInEditMode) . " onClick='set_checked_checbox(this)' onKeyDown='change_focus(document.forms[0],this,event)'></td>";
                $strHTML .= "<td onclick = \"set_checked_multi(this)\">$v_item_name</td></tr>";
                $i++;
            }
        }
        $strHTML .= "</table>";
        $strHTML .= self::genneraltoolsupport($sFormFielName, $readonlyInEditMode, $disabledInEditMode, $sValue);
        $strHTML = '<div style = "width:' . $swidth . ';">' . $strHTML . '</div>';
        return $strHTML;
    }

    /**
     * @param $sFormFielName
     * @param $readonlyInEditMode
     * @param $disabledInEditMode
     * @param string $Valuelist
     * @return string
     */
    private static function genneraltoolsupport($sFormFielName, $readonlyInEditMode, $disabledInEditMode, $Valuelist = '')
    {
        $strHTML = '';
        if ($Valuelist != "") {
            $v_checked_show_row_all = "";
            $v_checked_show_row_selected = "checked";
        } else {
            $v_checked_show_row_all = "checked";
            $v_checked_show_row_selected = "";
        }
        $strHTML .= "<table width='100%' cellpadding='0' cellspacing='0'><colgroup width = '100%' span = '2'><col width='2%'><col width='98%'></colgroup>";
        $strHTML .= "<tr><td class='small_radiobutton' colspan='10' align='right'>";
        $strHTML .= "<input  type='radio' name='rad_$sFormFielName' value='1' hide='true' $v_checked_show_row_all " . self::_generatePropertyType("readonly", $readonlyInEditMode) . self::_generatePropertyType("disabled", $disabledInEditMode) . " onClick='_show_row_all(\"table_$sFormFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
            <font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$sFormFielName\")[0].checked = true;_show_row_all(\"table_$sFormFielName\");'>Hi&#7875;n th&#7883; t&#7845;t c&#7843;</font>";
        $strHTML .= "<input type='radio' name='rad_$sFormFielName' value='2' hide='true' $v_checked_show_row_selected " . self::_generatePropertyType("readonly", $readonlyInEditMode) . self::_generatePropertyType("disabled", $disabledInEditMode) . " onClick='jquery_show_row_selected(\"table_$sFormFielName\")' onKeyDown='change_focus(document.forms[0],this,event)'>
            <font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$sFormFielName\")[1].checked = true;jquery_show_row_selected(\"table_$sFormFielName\");'>Ch&#7881; hi&#7875;n th&#7883; c&#225;c &#273;&#432;&#7907;c ch&#7885;n</font>";
        $strHTML .= "</td></tr>";
        $strHTML .= "</table>";
        return $strHTML;
    }

    /**
     * @param $sFormFielName
     * @param $v_primary_key
     * @param $arrOption
     * @param $arrFileList
     * @param $doctype
     * @param $readonly
     * @return string
     * @throws Zend_Exception
     */
    public static function _generateHtmlNormalAttachfile($sFormFielName, $v_primary_key, $arrOption, $arrFileList, $doctype, $readonly)
    {
        isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
        if ($v_name == '') $v_name = $sFormFielName;
        isset($arrOption["width"]) ? $width = $arrOption["width"] : $width = '';
        $spRetHtml = "<div type='ATTACHFILE' style='width:$width' id='$sFormFielName' id='$v_name' doctype='$doctype' class='normal_label elementfrm fileupload' >";
        $spRetHtml .= '<input type="hidden" id="hdn_delete_file_upload" value="" />';

        $fk_doc = $v_primary_key;
        $objConfig = new G_Global();
        $spRetHtml .= '<div>';
        if($readonly) {
            $spRetHtml .= self::viewFile($arrFileList);
        } else {
            $spRetHtml .= '<span class="btn btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>Chọn tệp tin đính kèm...</span>
                                <input type="file" multiple="" method="POST" enctype="multipart/form-data" name="files[]">
                            </span>';
            
            $spRetHtml .= '<table role="presentation" style="width:100%;"><colgroup><col width="5%"><col width="90%"><col width="5%"></colgroup><tbody class="files">';
            if ($arrFileList != '') {
                $spRetHtml .= self::getDefaultFile($arrFileList);
            }
            $spRetHtml .= '</tbody></table>';
            $spRetHtml .= '<div id="ext_' . $sFormFielName . '"></div>';
            $spRetHtml .= '<script>
                    loadtemplate($(\'#ext_' . $sFormFielName . '\'));
                </script>';
        }

        $spRetHtml .= '</div>';
        return $spRetHtml;
    }

    public static function viewFile($data)
    {
        $objConfig = new G_Global();
        $htmls = '<ul>';
        $iTotal = sizeof($data);
        $sysConst = Zend_Registry::get('__sysConst__');
        for ($i = 0; $i < $iTotal; $i++) {
            $file = $data[$i]['sFileName'];
            $arrFilename = explode('!~!', $file);
            $file_name = $arrFilename[1];
            if ($sysConst->fileServer) {
                $href = $sysConst->fileServerUrlPath . $arrFilename[0] . '/' . $file_name;
            } else {
                $file_id = explode("_", $arrFilename[0]);
                $href = $objConfig->sitePath . "mofile?id=" . $file;
            }
            $htmls .= '<li><a style="cursor: pointer;" class="'. self::getClassIcon($file_name).'" onclick="{window.open(\'' . $href . '\'); return false;}">' . $file_name . '</a></li>';
        }
        $htmls .= '</ul>';
        return $htmls;
    }    
    public static function getDefaultFile($data)
    {
        $objConfig = new G_Global();
        $htmls = '';
        $iTotal = sizeof($data);
        $sysConst = Zend_Registry::get('__sysConst__');
        for ($i = 0; $i < $iTotal; $i++) {
            $htmls .= '<tr class="template-download fade">';
            $htmls .= '<td><span class="fa fa-check"></span></td>';
            $file = $data[$i]['sFileName'];
            $arrFilename = explode('!~!', $file);
            $file_name = $arrFilename[1];
            if ($sysConst->fileServer) {
                $href = $sysConst->fileServerUrlPath . $arrFilename[0] . '/' . $file_name;
            } else {
                $file_id = explode("_", $arrFilename[0]);
                $href = $objConfig->sitePath . "mofile?id=" . $file;
            }
            $htmls .= '<td>
                        <p file_name="' . $file . '" old="1" class="file_report upload_complete">
                            <a onclick="{window.open(\'' . $href . '\'); return false;}">' . $file_name . '</a>
                        </p>
                    </td>';
            $htmls .= '<td>
                    <button data-type="POST"
                            data-url="' . $objConfig->sitePath . 'main/file/upload/?file=' . $file . '&amp;_method=DELETE"
                            class="btn btn-danger delete">
                        <i class="glyphicon glyphicon-trash"></i>
                        <span>Xóa</span>
                    </button>
                </td>';
            $htmls .= '</tr>';
        }
        return $htmls;
    }

    public static function getClassIcon($file_name){
        $class ='';
        if($file_name){
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));    
            switch ($ext) {
                case 'doc':
                case 'docx':
                    $class = 'idoc';
                    break;
                case 'xls':
                case 'xlsx':
                    $class = 'iexcel';
                    break;
                case 'pdf':
                    $class = 'ipdf';
                    break;
                case 'rar':
                    $class = 'irar';
                    break;
                case 'zip':
                    $class = 'izip';
                    break;
                case '7z':
                    $class = 'i7z';
                    break;
                default:
                    $class = 'icon_img';
            }            
        }
        return $class;
    }
    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $sValue
     * @param $p_view_mode
     * @param $readonly
     * @return string
     */
    public static function _generateHtmlTextbox($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly)
    {
        $spRetHtml = '';
        if ($p_view_mode) {
            $spRetHtml .= $sValue;
        } else {
            isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
            if ($v_name == '') $v_name = $sFormFielName;
            isset($arrOption["data_format"]) ? $spDataFormat = $arrOption["data_format"] : $spDataFormat = '';
            $sDataFormatStr = self::_generateVerifyProperty($spDataFormat);
            isset($arrOption["width"]) ? $width = $arrOption["width"] : $width = '';
            isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
            isset($arrOption["disabled_in_edit_mode"]) ? $disabledInEditMode = $arrOption["disabled_in_edit_mode"] : $disabledInEditMode = '';
            isset($arrOption["js_function_list"]) ? $jsFunctionList = $arrOption["js_function_list"] : $jsFunctionList = '';
            isset($arrOption["js_action_list"]) ? $jsActionList = $arrOption["js_action_list"] : $jsActionList = '';
            isset($arrOption["store_in_child_table"]) ? $storeInChildTable = $arrOption["store_in_child_table"] : $storeInChildTable = '';
            isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
            isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
            isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
            isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';

            if ($spDataFormat == "isdate") {
                if (!$sValue) {
                    isset($arrOption["type_date_default"]) ? $type_date_default = $arrOption["type_date_default"] : $type_date_default = '';
                    isset($arrOption["v_month"]) ? $v_month = $arrOption["v_month"] : $v_month = '';
                    isset($arrOption["v_day"]) ? $v_day = $arrOption["v_day"] : $v_day = '';
                    $type_date_default = strtoupper($type_date_default);
                    switch ($type_date_default) {
                        case 'CURRENT_DATE':
                            $sValue = date('d/m/Y');
                            break;
                        case 'CURRENT_YEAR':
                            $sValue = $v_day . '/' . $v_month . '/' . date('Y');
                            break;
                        case 'FIRST_DAY':
                            $sValue = '01/' . '01' . '/' . date('Y');
                            break;
                        default:
                            break;
                    }
                }
                $spRetHtml .= '<input date="isdate" type="text" id="' . $sFormFielName . '"  name="' . $v_name . '" class="normal_textbox" value="' . $sValue . '" value_save = "' . $sValue . '" style="width:' . $width . '" ';
                $spRetHtml .= self::_generatePropertyType("optional", $optOptional) . self::_generatePropertyType("readonly", $readonly) . self::_generatePropertyType("disabled", $disabledInEditMode);
                $spRetHtml .= self::_generateEventAndFunction($jsFunctionList, $jsActionList) . ' ' . $sDataFormatStr . ' store_in_child_table="' . $storeInChildTable . '" xml_tag_in_db="' . $xmlTagInDb . '" xml_data="' . $xmlData . '" column_name="' . $columnName . '" message="' . $spMessage . '" >';
            } else {
                isset($arrOption["max_length"]) ? $maxlength = $arrOption["max_length"] : $maxlength = '';
                isset($arrOption["max"]) ? $max = $arrOption["max"] : $max = '';
                isset($arrOption["min"]) ? $min = $arrOption["min"] : $min = '';
                isset($arrOption["note"]) ? $note = $arrOption["note"] : $note = '';
                $spRetHtml .= '<input type="text" id="' . $sFormFielName . '" name="' . $sFormFielName . '" class="normal_textbox" value="' . $sValue . '" store_in_child_table="' . $storeInChildTable . '" style="width:' . $width . '" ';
                $spRetHtml .= self::_generatePropertyType("optional", $optOptional) . self::_generatePropertyType("readonly", $readonly) . self::_generatePropertyType("disabled", $disabledInEditMode);
                $spRetHtml .= self::_generateEventAndFunction($jsFunctionList, $jsActionList) . ' ' . $sDataFormatStr . ' store_in_child_table="' . $storeInChildTable . '" xml_tag_in_db="' . $xmlTagInDb . '" xml_data="' . $xmlData . '" column_name="' . $columnName . '" message="' . $spMessage . '" maxlength="' . $maxlength . '"  ';
                if (rtrim($max) != '' && !is_null($max)) {
                    $spRetHtml .= ' max="' . $max . '"';
                }
                if (rtrim($min) != '' && !is_null($min)) {
                    $spRetHtml .= ' min="' . $min . '"';
                }
                $spRetHtml .= ' onKeyDown="change_focus(document.forms[0],this,event)">';
                $spRetHtml .= $note;
            }
        }
        return $spRetHtml;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $sValue
     * @param $readonly
     * @return string
     */
    public static function _generateHtmlCheckbox($sFormFielName, $arrOption, $sValue, $readonly)
    {
        if ($sValue == "true" || $sValue == 1 || $sValue == 'HOAT_DONG') {
            $v_checked = " checked ";
        } else {
            $v_checked = " ";
        }
        isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
        if ($v_name == '') $v_name = $sFormFielName;
        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        isset($arrOption["disabled_in_edit_mode"]) ? $disabledInEditMode = $arrOption["disabled_in_edit_mode"] : $disabledInEditMode = '';
        isset($arrOption["js_function_list"]) ? $jsFunctionList = $arrOption["js_function_list"] : $jsFunctionList = '';
        isset($arrOption["js_action_list"]) ? $jsActionList = $arrOption["js_action_list"] : $jsActionList = '';
        isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
        isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
        isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
        isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';
        $spRetHtml = '<label class="normal_label slg">&nbsp;</label>';
        $spRetHtml .= "<input type='checkbox' id = '" . $sFormFielName . "' name='$v_name' class='normal_checkbox' $v_checked value='$sValue' style='margin-right: 5px;' ";
        $spRetHtml .= self::_generatePropertyType("optional", $optOptional) . self::_generatePropertyType("readonly", $readonly) . self::_generatePropertyType("disabled", $disabledInEditMode);
        $spRetHtml .= self::_generateEventAndFunction($jsFunctionList, $jsActionList) . " xml_tag_in_db='$xmlTagInDb' xml_data='$xmlData' column_name='$columnName' message='$spMessage' onKeyDown='change_focus(document.forms[0],this,event)'>";
        return $spRetHtml;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $sValue
     * @param $readonly
     * @return string
     */
    public static function _generateHtmlRadio($sFormFielName, $arrOption, $sValue, $readonly)
    {
        isset($arrOption["value"]) ? $radioValue = $arrOption["value"] : $radioValue = '';
        if ($radioValue == $sValue || $sValue == "true") {
            $v_checked = " checked ";
        } else {
            $v_checked = " ";
        }
        isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
        if ($v_name == '') $v_name = $sFormFielName;
        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        isset($arrOption["disabled_in_edit_mode"]) ? $disabledInEditMode = $arrOption["disabled_in_edit_mode"] : $disabledInEditMode = '';
        isset($arrOption["js_function_list"]) ? $jsFunctionList = $arrOption["js_function_list"] : $jsFunctionList = '';
        isset($arrOption["js_action_list"]) ? $jsActionList = $arrOption["js_action_list"] : $jsActionList = '';
        isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
        isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
        isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
        isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';
        $spRetHtml = "<input type='radio' id = '" . $sFormFielName . "' name='$v_name' class='normal_checkbox' $v_checked value='$radioValue' ";
        $spRetHtml .= self::_generatePropertyType("optional", $optOptional) . self::_generatePropertyType("readonly", $readonly) . self::_generatePropertyType("disabled", $disabledInEditMode);
        $spRetHtml .= self::_generateEventAndFunction($jsFunctionList, $jsActionList) . " xml_tag_in_db='$xmlTagInDb' xml_data='$xmlData' column_name='$columnName' message='$spMessage' onKeyDown='change_focus(document.forms[0],this,event)'>";
        return $spRetHtml;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $sValue
     * @param $p_view_mode
     * @param $readonly
     * @return string
     */
    public static function _generateHtmlTextarea($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly)
    {
        $spRetHtml = '';
        if ($p_view_mode && $readonly == "true") {
            $spRetHtml .= $sValue;
        } else {
            isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
            if ($v_name == '') $v_name = $sFormFielName;
            isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
            isset($arrOption["disabled_in_edit_mode"]) ? $disabledInEditMode = $arrOption["disabled_in_edit_mode"] : $disabledInEditMode = '';
            isset($arrOption["js_function_list"]) ? $jsFunctionList = $arrOption["js_function_list"] : $jsFunctionList = '';
            isset($arrOption["js_action_list"]) ? $jsActionList = $arrOption["js_action_list"] : $jsActionList = '';
            isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
            isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
            isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
            isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';
            isset($arrOption["width"]) ? $width = $arrOption["width"] : $width = '';
            isset($arrOption["row"]) ? $row = $arrOption["row"] : $row = '';

            $spRetHtml .= '<textarea class="normal_textarea" id = "' . $sFormFielName . '" name="' . $v_name . '" rows="' . $row . '" style="width:' . $width . '" ';
            $spRetHtml .= self::_generatePropertyType("optional", $optOptional);
            $spRetHtml .= self::_generatePropertyType("readonly", $readonly);
            $spRetHtml .= self::_generatePropertyType("disabled", $disabledInEditMode) . self::_generateEventAndFunction($jsFunctionList, $jsActionList) . ' xml_tag_in_db="' . $xmlTagInDb . '" xml_data="' . $xmlData . '" column_name="' . $columnName . '" message="' . $spMessage . '">';
            $spRetHtml .= $sValue . '</textarea>';
        }
        return $spRetHtml;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $arrListItem
     * @param $selectBoxIdColumn
     * @param $selectBoxNameColumn
     * @param $sValue
     * @param $theFirstOfIdValue
     * @param bool $readonly
     * @return string
     */
    public static function _generateHtmlSelectbox($sFormFielName, $arrOption, $arrListItem, $selectBoxIdColumn, $selectBoxNameColumn, $sValue, $theFirstOfIdValue, $readonly = false)
    {
        if ($theFirstOfIdValue == "true" && $sValue == "") {
            $sValue = $arrListItem[0][$selectBoxIdColumn];
        }
        isset($arrOption["v_name"]) ? $v_name = $arrOption["v_name"] : $v_name = '';
        if ($v_name == '') $v_name = $sFormFielName;
        isset($arrOption["width"]) ? $width = $arrOption["width"] : $width = '';
        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        isset($arrOption["disabled_in_edit_mode"]) ? $disabledInEditMode = $arrOption["disabled_in_edit_mode"] : $disabledInEditMode = '';
        isset($arrOption["js_function_list"]) ? $jsFunctionList = $arrOption["js_function_list"] : $jsFunctionList = '';
        isset($arrOption["js_action_list"]) ? $jsActionList = $arrOption["js_action_list"] : $jsActionList = '';
        isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
        isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
        isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
        isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';
        isset($arrOption["multiple"]) ? $spMultiple = 'multiple=""' : $spMultiple = '';
        isset($arrOption["display"]) ? $display = 'display=""' : $display = '';
        if ($spMultiple) {
            $v_name .= '[]';
        }
        $spRetHtml = "<div style='width:" . $width . ";'>";
        $spRetHtml .= "<select id='$sFormFielName' class='normal_selectbox' $spMultiple name='$v_name'" . self::_generatePropertyType("optional", $optOptional) . self::_generatePropertyType("readonly", $readonly);
        $spRetHtml .= self::_generatePropertyType("disabled", $disabledInEditMode) . self::_generateEventAndFunction($jsFunctionList, $jsActionList) . " xml_tag_in_db='$xmlTagInDb' xml_data='$xmlData' column_name='$columnName' message='$spMessage' onKeyDown='change_focus(document.forms[0],this,event)' >";
        if ($theFirstOfIdValue != "true") {
            isset($arrOption["label"]) ? $sLabel = $arrOption["label"] : $sLabel = '';
            $spRetHtml .= "<option id='' value='' name=''>--- Ch&#7885;n $sLabel ---</option>";
        }
        $spRetHtml .= self::_generateSelectOption($arrListItem, $selectBoxIdColumn, $selectBoxIdColumn, $selectBoxNameColumn, $sValue);
        $spRetHtml .= '</select></div>';
        if ($display == 'chosen') {
            $spRetHtml .= '<script>$("#' . $sFormFielName . '").chosen({widthdiv:100});</script>';
            # code...
        }
        return $spRetHtml;
    }

    private static function getItemFile($files, $doctype) {
        $file = array();
        for ($i=0; $i < count($files); $i++) { 
            if ($files[$i]['sDocType'] == $doctype) {
                array_push($file, $files[$i]);
            }
        }
        return $file;
    }
    /**
     * @param $sFormFielName
     * @param $arrListItem
     * @param $checkBoxMultipleIdColumn
     * @param $checkBoxMultipleNameColumn
     * @param $sValue
     */
    public static function _generateHtmlForMultipleCheckbox_fileAttach($sFormFielName, $arrListItem, $arrOption, $sValue)
    {
        isset($arrOption["checkbox_multiple_id_column"]) ? $IdColumn = $arrOption["checkbox_multiple_id_column"] : $IdColumn = '';
        isset($arrOption["checkbox_multiple_name_column"]) ? $NameColumn = $arrOption["checkbox_multiple_name_column"] : $NameColumn = '';
        isset($arrOption["optional"]) ? $optOptional = $arrOption["optional"] : $optOptional = '';
        isset($arrOption["xml_tag_in_db"]) ? $xmlTagInDb = $arrOption["xml_tag_in_db"] : $xmlTagInDb = '';
        isset($arrOption["xml_data"]) ? $xmlData = $arrOption["xml_data"] : $xmlData = '';
        isset($arrOption["column_name"]) ? $columnName = $arrOption["column_name"] : $columnName = '';
        isset($arrOption["message"]) ? $spMessage = $arrOption["message"] : $spMessage = '';

        $arr_value = explode(",", $sValue);
        $count_item = sizeof($arrListItem); 
        $count_value = sizeof($arr_value);
        $v_tr_name = '"tr_'.$sFormFielName.'"';
        $v_radio_name = '"rad_'.$sFormFielName.'"';
        $strHTML = '';
        $dspDiv =1;

        $strHTML .= '<input type="hidden" value="'.$sValue.'" v_type="multiple_checkbox" id="'.$sFormFielName.'" name="'.$sFormFielName.'" xml_data="'.$xmlData.'" xml_tag_in_db="'.$xmlTagInDb.'" column_name="'.$columnName.'" message="'.$spMessage.'" />';
        $strHTML .= '<table id="table_'.$sFormFielName.'" class="list-table-data" width="100%" cellpadding="0" cellspacing="0">';
        $strHTML .= '<col width="4%"><col width="60%"><col width="36%">';



        $myRecord = new Zend_Session_Namespace('Record');
        if ($sValue && $myRecord->recordId) {
            $files = G_Xml::libFileGetSingle($myRecord->recordId,$sValue);
        }

        for ($i=0; $i < $count_item; $i++) { 
            $v_item_id = $arrListItem[$i][$IdColumn];
            $v_item_name = $arrListItem[$i][$NameColumn];
            $v_item_checked = "";
            $v_file = array();
            if (in_array($v_item_id, $arr_value)) {
                $v_item_checked = "checked";
                $v_file = self::getItemFile($files, $v_item_id);
            }
                                   
            $strHTML .= '<tr>';
            $strHTML .= '<td align="center">';
            $strHTML .= '<input id="'.$sFormFielName.'" type="checkbox" name="chk_multiple_checkbox" value="'.$v_item_id.'" xml_tag_in_db_name ="'.$sFormFielName.'" '.$v_item_checked.' '.self::_generatePropertyType("readonly",$readonlyInEditMode).self::_generatePropertyType("disabled",$disabledInEditMode).' onClick="'.$v_item_url_onclick.'" onKeyDown="change_focus(document.forms[0],this,event)"></td>';
            
            $strHTML .= "<td onclick = \"set_checked(document.getElementsByName('chk_multiple_checkbox'),'$v_item_id','table_$sFormFielName')\">$v_item_name</td>";
            $strHTML .= '<td style="width:38%" >' . self::_generateCheckboxAttachfile($sFormFielName, $v_file, $v_item_id, false) . '</td></tr>';
            $strHTML .= "</tr>";
        }

        $strHTML .= "</table>";

        $strHTML .= "<table width='100%' cellpadding='0' cellspacing='0'><colgroup width = '100%' span = '2'><col width='2%'><col width='98%'></colgroup>";
        $strHTML .= "<tr><td class='small_radiobutton' colspan='10' align='right'>";  
        $strHTML .= "<input type='radio' name='rad_$sFormFielName' optional='true' value='1' hide='true' ".self::_generatePropertyType("readonly",$readonlyInEditMode).self::_generatePropertyType("disabled",$disabledInEditMode)." onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$sFormFielName\",this,0);' onKeyDown='change_focus(document.forms[0],this,event)'>
        <font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$sFormFielName\")[0].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$sFormFielName\",document.getElementsByName(\"rad_$sFormFielName\")[0],0);'>Ch&#7885;n t&#7845;t c&#7843;</font>";
        $strHTML .= "<input type='radio' name='rad_$sFormFielName' optional='true' value='2' hide='true' ".self::_generatePropertyType("readonly",$readonlyInEditMode).self::_generatePropertyType("disabled",$disabledInEditMode)." onClick='_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$sFormFielName\",this,1);' onKeyDown='change_focus(document.forms[0],this,event)'>
        <font style = \"cursor:pointer;\" onClick='document.getElementsByName(\"rad_$sFormFielName\")[1].checked = true;_select_all_multiple_checkbox(document.getElementsByName(\"chk_multiple_checkbox\"),\"$sFormFielName\",document.getElementsByName(\"rad_$sFormFielName\")[1],1);'>B&#7887; ch&#7885;n t&#7845;t c&#7843;</font>";
        $strHTML .= '</td></tr></table>';
        $width = '100%';

        $strHTML = '<div style = "width:' . $width . ';">' . $strHTML . '</div>';

        $strHTML .= '<div id="ext_' . $sFormFielName . '"></div>';
            $strHTML .= '<script>
                    loadItemTemplateUpload($(\'#ext_' . $sFormFielName . '\'));
                </script>';

        return $strHTML;
    }


    /**
     * @param $sFormFielName
     * @param $v_primary_key
     * @param $arrOption
     * @param $arrFileList
     * @param $doctype
     * @param $readonly
     * @return string
     * @throws Zend_Exception
     */
    public static function _generateCheckboxAttachfile($sFormFielName, $arrFileList, $doctype, $readonly)
    {
        $spRetHtml .= '<div class="modefile checkboxupload" type="ATTACHFILE_CHECKLIST" doctype="'.$doctype.'">';
        $spRetHtml .= '<input type="hidden" name="hdn_delete_file_upload" value="" />';
        if($readonly) {
            $spRetHtml .= self::viewFile($arrFileList);
        } else {
            $spRetHtml .= '<div class="left"><table role="presentation" style="width:100%;"><colgroup><col width="5%"><col width="90%"><col width="5%"></colgroup><tbody class="files">';
            if ($arrFileList != '') {
                $spRetHtml .= self::getDefaultFile($arrFileList);
            }
            $spRetHtml .= '</tbody></table></div>';
            $spRetHtml .= '<div class="right"><span class="iconattach fileinput-button">
                                <span></span>
                                <input type="file" method="POST" enctype="multipart/form-data" name="files">
                            </span></div>';
            

            
        }

        $spRetHtml .= '</div>';
        return $spRetHtml;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $sValue
     * @param $p_view_mode
     * @param $readonly
     */
    public static function _generateHtmlTextboxOrder($sFormFielName, $arrOption, $sValue, $p_view_mode, $readonly)
    {

    }

    /*
     * Nguoi tạo:
     * date: 27/10/2011
     * $search : cụm từ tìm kiếm
     * $str : chuỗi cần tô màu
     */

    public static function searchStringColor($search, $str)
    {
        if (!is_null($search) & $search != '' & $search != '') {
            $search = mb_strtoupper($search, 'UTF-8');
            $strUp = mb_strtoupper($str, 'UTF-8');
            $srOut = '';
            $searchlen = mb_strlen($search, 'UTF-8');
            mb_regex_encoding('UTF-8');
            $arrstr = mb_split($search, $strUp);
            $start = 0;
            $count = sizeof($arrstr);
            for ($i = 0; $i < $count; $i++) {
                $length = mb_strlen($arrstr[$i], 'UTF-8');
                $srOut .= mb_substr($str, $start, $length, 'UTF-8');
                $srOut .= '<span class = "lblcolor">' . mb_substr($str, ($start + $length), $searchlen, 'UTF-8') . '</span>';
                $start += $length + $searchlen;
            }
        } else
            return $str;
        return $srOut;
    }

    /**
     * @param $spDataFormat
     * @return string
     */
    private static function _generateVerifyProperty($spDataFormat)
    {
        switch ($spDataFormat) {
            case "isemail";
                $psRetHtml = ' isemail="true" ';
                break;
            case "isdate";
                $psRetHtml = ' isdate=true onKeyUp="DateOnkeyup(this,event)" ';
                break;
            case "isnumeric";
                $psRetHtml = ' isnumeric="true" ';
                break;
            case "isdouble";
                $psRetHtml = ' isdouble="true" ';
                break;
            case "ismoney";
                $psRetHtml = ' isnumeric="true" onKeyUp="format_money(this,event)" ';
                break;
            case "ismoney_float";
                $psRetHtml = ' isfloat="true" onKeyUp="format_money(this)" ';
                break;
            default:
                $psRetHtml = "";
        }
        return $psRetHtml;
    }

    /**
     * @param $pType
     * @param $pValue
     * @return string
     */
    private static function _generatePropertyType($pType, $pValue)
    {
        $pValue = mb_strtolower($pValue);
        if ($pValue == 'false') $pValue = false;
        else if ($pValue == 'true') $pValue = true;
        $pValue = (bool)$pValue;
        switch ($pType) {
            case 'optional';
                if ($pValue) {
                    $psRetHtml = ' optional = true ';
                } else {
                    $psRetHtml = ' optional = false ';
                }
                break;
            case 'readonly';
                if ($pValue) {
                    $psRetHtml = ' readonly = true ';
                } else {
                    $psRetHtml = '';
                }
                break;
            case 'disabled';
                if ($pValue) {
                    echo $pValue . '<br>';
                    $psRetHtml = ' disabled = true ';
                } else {
                    $psRetHtml = ' ';
                }
                break;
            default:
                $psRetHtml = '';
        }
        return $psRetHtml;
    }

    /**
     * @param $psJsFunctionList
     * @param $psJsActionList
     * @return string
     */
    public static function _generateEventAndFunction($psJsFunctionList, $psJsActionList)
    {
        $arrJsFunctionList = explode(";", $psJsFunctionList);
        $arrJsActionList = explode(";", $psJsActionList);
        $pCountFunction = sizeof($arrJsFunctionList);
        $pCountAction = sizeof($arrJsActionList);
        $count = $pCountFunction > $pCountAction ? $pCountAction : $pCountFunction;
        $v_temp = '';
        for ($i = 0; $i < $count; $i++) {
            $v_temp .= ' ' . $arrJsActionList[$i] . '="' . $arrJsFunctionList[$i] . '" ';
        }
        return $v_temp;
    }

    public function getstructsearchexpand()
    {
        $sSearchDetail = '';
        $sSearchDetail .= '<div id="search_expand" class="s0">';
        $sSearchDetail .= '<div class="s00">';
        $sSearchDetail .= '<div class="s1">';
        $sSearchDetail .= '<div class="s2">';
        $sSearchDetail .= '<div class="s3">';
        $sSearchDetail .= '<input type="text" value="" autocomplete="off" name="s4" class="s4" title = "Bấm F4 để tìm kiếm mở rộng">';
        $sSearchDetail .= '</div>';
        $sSearchDetail .= '<div class="s5_1"></div>';
        $sSearchDetail .= '<div class="s5"></div>';
        $sSearchDetail .= '</div>';
        $sSearchDetail .= '<div class = "s11">';
        $sSearchDetail .= '<div class = "s11Title">Tìm kiếm chi tiết</div>';
        $sSearchDetail .= '<div id = "detailSearch"><!-- Noi dung --></div>';
        $sSearchDetail .= '</div>';
        $sSearchDetail .= '</div>';
        $sSearchDetail .= '</div>';
        $sSearchDetail .= '<div id="searchIcon"></div>';
        $sSearchDetail .= '</div>';
        return $sSearchDetail;
    }

    /**
     * @return array
     */
    public function getInforStructMenu()
    {
        $sPath = G_Global::getInstance()->dirXml . 'system/menu/struct_menu.xml';
        $objConfigXml = G_Xml::getInstance();
        $objConfigXml->__loadxml($sPath);
        $arrMenu_struct = $objConfigXml->menu_struct->menu_row->toArray();
        if (!isset($arrMenu_struct[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrMenu_struct);
            $arrMenu_struct = $arrTemp;
        }
        // MenuItem
        $arrMenu_items = $objConfigXml->menu_list->toArray();
        return array('arrMenu_struct' => $arrMenu_struct,
            'arrMenu_items' => $arrMenu_items
        );
    }

    /**
     * @param $psModuleName
     * @param string $psModuleNameOrther
     * @param string $parrFileName
     * @param string $psDelimitor
     * @param $psExtension
     * @return string
     */
    public function _gCssJs($psModuleName, $psModuleNameOrther = "", $parrFileName = "", $psDelimitor = ",", $psExtension)
    {
        // chuoi ket qua tra ve
        $sResHtml = '';
        $globals = new G_Global();
        $filetype = strtolower($psExtension);
        $urlPath = $globals->sitePath . $globals->dirPublic;
        // thuc hien lay tren tung module cu the
        if ($psModuleName != "") {
            $sDir = $psModuleName . '/';
            if (is_dir($sDir)) {
                if ($dh = opendir($sDir)) {
                    while (($file = readdir($dh)) !== false) {

                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if ($filetype == "js" && $ext == "js") {
                            $sResHtml .= self::renderScriptJs($urlPath . $sDir . $file) . "\n";
                        }
                        //Thuc hien include file Css
                        if ($filetype == "css" && $ext == "css") {
                            $sResHtml .= self::renderLinkCss($urlPath . $sDir . $file) . "\n";
                        }
                    }
                    closedir($dh);
                }
            }
        }

        // Thuc hien lay file js o nhung module khac
        if ($psModuleNameOrther != "") {
            //
            $sDir = $psModuleNameOrther . '/';
            $arrTemp = explode($psDelimitor, $parrFileName);
            for ($index = 0; $index < sizeof($arrTemp); $index++) {
                //Thuc hien include file JavaScript
                $ext = strtolower(pathinfo($arrTemp[$index], PATHINFO_EXTENSION));
                if ($filetype == "js" && $ext == "js") {
                    $sResHtml .= self::renderScriptJs(trim($urlPath . $sDir . $arrTemp[$index])) . "\n";
                }
                //Thuc hien include file Css
                if ($filetype == "css" && $ext == "css") {
                    $sResHtml .= self::renderLinkCss($urlPath . $sDir . $arrTemp[$index]) . "\n";
                }
            }
        }
        return $sResHtml;
    }

    /**
     * @param $urlPath
     * @return string
     */
    private function renderScriptJs($urlPath)
    {
        $timestamp = filemtime($_SERVER['DOCUMENT_ROOT'] . $urlPath);
        $urlPath .= '?' . $timestamp;
        $script = "<script src=\"" . $urlPath . "\" type=\"text/javascript\"></script>";
        return $script;
    }

    /**
     * @param $urlPath
     * @return string
     */
    private function renderLinkCss($urlPath)
    {
        $timestamp = filemtime($_SERVER['DOCUMENT_ROOT'] . $urlPath);
        $urlPath .= '?' . $timestamp;
        $script = "<link href=\"" . $urlPath . "\" rel=\"stylesheet\" />";
        return $script;
    }

    /**
     * @param $arr_list
     * @param $IdColumn
     * @param $ValueColumn
     * @param $NameColumn
     * @param $SelectedValue
     * @return string
     */
    public static function _generateSelectOption($arr_list, $IdColumn, $ValueColumn, $NameColumn, $SelectedValue)
    {
        $strHTML = "";
        $i = 0;
        $count = sizeof($arr_list);
        for ($row_index = 0; $row_index < $count; $row_index++) {
            $strID = trim($arr_list[$row_index][$IdColumn]);
            $strValue = trim($arr_list[$row_index][$ValueColumn]);
            $optSelected = self::checkSelect($SelectedValue, $strValue);
            $DspColumn = trim($arr_list[$row_index][$NameColumn]);
            $strHTML .= '<option id=' . '"' . $strID . '"' . ' ' . 'name=' . '"' . $DspColumn . '"' . ' ';
            $strHTML .= 'value=' . '"' . $strValue . '"' . ' ' . $optSelected . '>' . $DspColumn . '</option>';
            $i++;
        }
        return $strHTML;
    }

    public static function checkSelect($SelectedValue, $strValue)
    {
        if (!is_array($SelectedValue)) {
            $arrSelected = explode(',', $SelectedValue);
            foreach ($arrSelected as $key => $value) {
                if ($strValue == $value) {
                    return "selected";
                }
            }
        }
        return "";
    }

    public static function _GenerateHeaderTable($widthCols, $TitleCols, $delimitor)
    {
        //Xu ly sinh cac cot ung voi do rong tuong ung cua Table
        $arrWidthCol = explode($delimitor, $widthCols);//Mang luu thong tin do rong cac cot
        $arrTitleCol = explode($delimitor, $TitleCols);//Mang luu thong tin luu ten tot tuong ung
        $countCol = sizeof($arrWidthCol);
        //Tao col
        $strHTML = "";
        for ($index = 0; $index < $countCol; $index++) {
            $strHTML = $strHTML . "<col width='" . $arrWidthCol[$index] . "'>";
        }
        $psHtmlTempWidth = $strHTML;
        $strHTML = $strHTML . "<tr class='header'>";
        for ($index = 0; $index < $countCol; $index++) {
            $styleCol = "";
            $strHTML = $strHTML . "<td class = 'title' style = 'text-align:center;'>" . $arrTitleCol[$index] . "</td>";
        }
        $strHTML = $strHTML . "</tr>";
        return $strHTML . "!~~!" . $psHtmlTempWidth;
    }


    public static function _addEmptyRow($iCurrentRow, $iTotalRow, $sCurrentStyle, $iTotalColumn)
    {
        //Dinh dang style
        if ($sCurrentStyle == "odd_row") {
            $sNextStyle = "round_row";
        } else {
            $sNextStyle = "odd_row";
        }

        if ($iCurrentRow >= $iTotalRow) {
            return "";
        }
        $strHTML = "";
        $style = $sCurrentStyle;
        for ($i = $iCurrentRow + 1; $i <= $iTotalRow; $i++) {
            if ($style == $sCurrentStyle) {
                $style = $sNextStyle;
            } else {
                $style = $sCurrentStyle;
            }
            $strHTML .= '<tr class=' . '"' . "$style" . '"' . '>';
            for ($j = 1; $j <= $iTotalColumn; $j++) {
                $strHTML .= "<td>&nbsp;</td>";
            }
            $strHTML .= "</tr>";
        }
        return $strHTML;
    }


    public function _gJsCssToArray($psModuleName, $psModuleNameOrther = "", $parrFileName = "", $psDelimitor = ",", $psExtension, $data = array())
    {
        $globals = new G_Global();
        $filetype = strtolower($psExtension);
        $urlPath = $globals->sitePath . $globals->dirPublic;
        // thuc hien lay tren tung module cu the
        if ($psModuleName != "") {
            $sDir = $psModuleName . '/';
            if (is_dir($sDir)) {
                if ($dh = opendir($sDir)) {
                    while (($file = readdir($dh)) !== false) {

                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if ($filetype == "js" && $ext == "js") {
                            array_push($data, $urlPath . $sDir . $file);
                        }
                        //Thuc hien include file Css
                        if ($filetype == "css" && $ext == "css") {
                            array_push($data, $urlPath . $sDir . $file);
                        }
                    }
                    closedir($dh);
                }
            }
        }

        // Thuc hien lay file js o nhung module khac
        if ($psModuleNameOrther != "") {
            //
            $sDir = $psModuleNameOrther . '/';
            $arrTemp = explode($psDelimitor, $parrFileName);
            for ($index = 0; $index < sizeof($arrTemp); $index++) {
                //Thuc hien include file JavaScript
                $file = $arrTemp[$index];
                $ext = strtolower(pathinfo($arrTemp[$index], PATHINFO_EXTENSION));
                if ($filetype == "js" && $ext == "js") {
                    array_push($data, $urlPath . $sDir . $file);
                }
                //Thuc hien include file Css
                if ($filetype == "css" && $ext == "css") {
                    array_push($data, $urlPath . $sDir . $file);
                }
            }
        }
        return $data;
    }

    public function genMenu($arrMenu, $baseUrl, $arrPermission)
    {
        $sHtml = '';
        if ($arrPermission) {
            $glib = new G_Lib();
            foreach ($arrMenu as $key => $menu) {
                $menu_name = $menu['menu_name'];
                $menu_code = $menu['menu_code'];
                $menu_url = $menu['menu_url'];
                $submenu = $menu['submenu'];
                $count = sizeof($submenu);
                $htmlSubmenu = '';
                $jsSubmenu = '';
                for ($i = 0; $i < $count; $i++) {
                    $submenu_name = $submenu[$i]['submenu_name'];
                    $submenu_code = $submenu[$i]['submenu_code'];
                    $submenu_url = $submenu[$i]['submenu_url'];
                    $arrSubmenuurl = explode('/', $submenu_url);
                    $sPacketCode =  $arrSubmenuurl[0];
                    if (self::access($arrPermission, $sPacketCode, $submenu_code)) {
                        if (sizeof($arrSubmenuurl) == 2) {
                            array_push($arrSubmenuurl, 'index');
                        }
                        $resource = implode('_', $arrSubmenuurl);
                        $urlRe = $baseUrl . '/' . $submenu_url;
                        $htmlSubmenu .= '<li id="' . $resource . '"><a href="' . $urlRe . '" title="' . $submenu_name . '">';
                        $htmlSubmenu .= '<span class="icon_BDK cl' . $submenu_code . '"></span>';
                        $htmlSubmenu .= '<span class="text">' . $submenu_name . '</span>';
                        $htmlSubmenu .= '</a></li>';
                    }
                }
                if ($htmlSubmenu) {
                    $sHtml .= '<div class="row row_01">';
                    $sHtml .= '<p class="title ' . $menu_code . '" title="' . $menu_name . '">
                                <span class="icon_categories icon_tiepnhandon"></span>
                                <span class="text">' . $menu_name . '</span>
                                <span class="icon_arrow_tiny"></span>
                                <span class="icon_arrow_tiny_hide"></span></p>';

                    $sHtml .= '<ul class="list">';
                    $sHtml .= $htmlSubmenu;
                    $sHtml .= '</ul>';
                    $sHtml .= '</div>';
                }
            }
        }
        $objAuth = new G_Auth();
        if (G_Aclplugin::isAdmin()) {
            $sHtml .= '<div class="row row_admin">';
            $sHtml .= '<p class="title" title="Quản trị hệ thống">
                                <span class="icon_categories"></span>
                                <span class="text">Quản trị hệ thống</span>
                                <span class="icon_arrow_tiny"></span>
                                <span class="icon_arrow_tiny_hide"></span></p>';
            $sHtml .= '<ul class="list">';
            $sHtml .= $objAuth->getFunctionPermission('QT_DANH_MUC', $baseUrl);
            $sHtml .= '</ul></div>';
        }
        return $sHtml;
    }

    public function access($data, $module, $controller)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ($v['sPackageCode'] === $module && $v['sModuleCode'] === $controller)
                    return true;
            }
        }
        return false;
    }

    public function getCallbackRecordWork($obj) {
        $callback = '
                var staff_id = "' . Zend_Auth::getInstance()->getIdentity()->PkStaff . '";
                var checkexitstaff = function(arrIn,key,staff_id){
                    var staff_id_sql =\'\';
                    for (var i=0; i < arrIn.length; i++) {
                        staff_id_sql = arrIn[i][\'FkStaff\'];
                        if(staff_id_sql) {
                            staff_id_sql = staff_id_sql.replace(/}/g,"");
                            staff_id_sql = staff_id_sql.replace(/{/g,"");
                        }
                        if (arrIn[i][\'PkRecordWork\'] === key && staff_id_sql === staff_id) {
                            return true;
                        }
                    }
                    return false;
                }
                $(\'div#wf-record-work\').find(\'input[name="chk_item_id"]\').each(function(){
                    if (checkexitstaff(arrResult,$(this).val(),staff_id)) {
                        $(this).attr(\'disabled\',false);
                    }else{
                        $(this).attr(\'disabled\',true).val(\'\');
                        $(this).parent().parent().find(\'td\').attr(\'onclick\',\'\');
                    }
                    $(this).click(function() {
                        if(typeof($(this).attr(\'act\')) == \'undefined\')
                            $(\'.reset\').trigger(\'click\')
                    })
                })
                
                $(\'.editRecordWork\').each(function() {
                    $(this).unbind(\'click\').click(function() {
                        $(this).closest(\'table\').find(\'input[name="chk_item_id"]\').removeAttr(\'act\')
                        $(this).parent().parent().find(\'input[name="chk_item_id"]\').attr(\'act\', \'edit\').trigger(\'click\');
                        ' . $obj . '.editRecordWork($(this).attr(\'rdw\'));
                    })
                })
                $(\'.deleteRecordWork\').each(function() {
                    $(this).unbind(\'click\').click(function() {
                        $(this).closest(\'table\').find(\'input[name="chk_item_id"]\').removeAttr(\'act\')
                        $(this).parent().parent().find(\'input[name="chk_item_id"]\').attr(\'act\', \'edit\').trigger(\'click\');
                        ' . $obj . '.deleteRecordWork($(this).attr(\'rdw\'));
                    })
                })
            ';
        return $callback;
    }

    public function getGeneralInformation($arrInput, $callback = '')
    {
        $sCode = $arrInput['sCode'];
        $action = $arrInput['action'];
        $pathfile = $arrInput['pathfile'];
        $sFormName = $arrInput['formName'];
        $idputdata = (isset($arrInput['idputdata']) ? $arrInput['idputdata'] : 'wf-general-information');

        unset($arrInput['sCode']);
        unset($arrInput['action']);
        unset($arrInput['formName']);
        unset($arrInput['idputdata']);
        $sHtml = '';
        $sHtml .= '<script>';
        $sHtml .= '
            var dirxml = \'' . $pathfile . '\';
            if(typeof(' . $sCode . ') === \'undefined\'){
                ' . $sCode . ' = new libXml({dirxml: dirxml});
            }
            var url = baseUrl + \'/main/ajax/'.$action.'/\';
            var data = JSON.parse(\''.json_encode($arrInput).'\')
            $.ajax({
                url: url,
                type: "POST",
                dataType: \'json\',
                data:data,
                success: function(arrResult) {
                    ' . $sCode . '.exportTable({
                        result: arrResult,
                        objupdate: $(\'div#' . $idputdata . '\'),
                        form: $(\'form#' . $sFormName . '\'),
                        addrow: false,
                        callback: function() {
                            ' . $callback . '
                        }
                    })
                    hideloadpage();
                },
                beforeSend: showloadpage()
            })
        ';
        $sHtml .= '</script>';
        return $sHtml;
    }
}