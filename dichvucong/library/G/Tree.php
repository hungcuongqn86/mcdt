<?php
/**
 * @see
 *
 */

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 08/04/2015
 * Noi dung: Tao lop G_Tree
 */
class G_Tree
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function _builtXmlTree($arrAllList, $exception_brand_id, $show_control, $opening_node_img_name, $closing_node_img_name, $leaf_node_img_name, $select_parent, $list_id_checked = "", $object_name = "")
    {
        global $_MODAL_DIALOG_MODE;

        $sPath = G_Global::getInstance()->dirImage;

        $strTop = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $strTop .= '<treeview title="Treeview" >' . "\n";
        $strTop .= "<custom-parameters>" . "\n";
        $strTop .= '<param name="shift-width" value="10"/>' . "\n";
        $strTop .= '<param name="opening_node_img_name" value="' . $sPath . $opening_node_img_name . '"/>' . "\n";
        $strTop .= '<param name="closing_node_img_name" value="' . $sPath . $closing_node_img_name . '"/>' . "\n";
        $strTop .= '<param name="leaf_node_img_name" value="' . $sPath . $leaf_node_img_name . '"/>' . "\n";
        $strTop .= '<param name="modal_dialog_mode" value="' . $_MODAL_DIALOG_MODE . '"/>' . "\n";
        $strTop .= '<param name="show_control" value="' . $show_control . '"/>' . "\n";
        $strTop .= '<param name="select_parent" value="' . $select_parent . '"/>' . "\n";
        $strTop .= "</custom-parameters>" . "\n";
        $strBottom = "</treeview>";
        $strXML = "";
        $parent_id = NULL;
        //Lay ra mang chua cac Object muc ngoai cung
        $v_count = sizeof($arrAllList);
        $v_current_index = 0;
        for ($i = 0; $i < $v_count; $i++) {
            if (strcasecmp(trim($arrAllList[$i][1]), $parent_id) == 0) {
                //if($arrAllList[$i][1]==$parent_id){
                $arr_current_list[$v_current_index][0] = $arrAllList[$i][0];//PK
                $arr_current_list[$v_current_index][1] = $arrAllList[$i][1];//FK
                $arr_current_list[$v_current_index][2] = htmlspecialchars($arrAllList[$i][2]);//sCode
                $arr_current_list[$v_current_index][3] = htmlspecialchars($arrAllList[$i][3]);//sName
                $arr_current_list[$v_current_index][4] = $arrAllList[$i][4];//sType
                $arr_current_list[$v_current_index][5] = $arrAllList[$i][5];//sLevel
                $v_current_index++;
            }
        }
        //Tao cac Node muc 2 cua treeview
        for ($i = 0; $i < $v_current_index; $i++) {
            $v_current_id = $arr_current_list[$i][0];//PK
            $v_parent_id = 0;// id cua cha (FK =0)
            $v_current_code = htmlspecialchars($arr_current_list[$i][2]);//sCode
            $v_current_name = htmlspecialchars($arr_current_list[$i][3]);    //sName
            $v_current_type = $arr_current_list[$i][4];//sType
            $v_current_level = $arr_current_list[$i][5];//sLevel
            //Kiem tra ID neu no khong la $exception_brand_id thi moi tao (tranh truong hop "vua la chau vua la cha" giua hai phan tu)
            if (strcasecmp(trim($v_current_id), $exception_brand_id) != 0) {
                //if($v_current_id!=$exception_brand_id){
                $arr_id_list = explode(",", $list_id_checked);
                $value_checked = 0;
                for ($j = 0; $j < sizeof($arr_id_list); $j++) {
                    if ($arr_id_list[$j] == $v_current_id) {
                        $value_checked = $v_current_id;
                    }
                }
                $strXML .= '<folder title="' . trim($v_current_name) . '" id="' . $v_current_id . '" value="' . $v_current_code . '" value_check="' . $value_checked . '" type="' . $v_current_type . '" parent_id="' . $v_parent_id . '" xml_tag_in_db_name="' . $object_name . '" level="' . $v_current_level . '" >' . "\n";
                //Tao ra cac Node  con cua tree view
                $strXML .= self::_builtChildNode($arrAllList, $v_current_level, $v_current_id, $exception_brand_id, $list_id_checked, $object_name);
                $strXML .= "</folder>" . "\n";
            }
        }
        return $strTop . $strXML . $strBottom;

    }

    //Xay dung cac node con cua mot doi tuong
    public function _builtChildNode($arrAllList, $current_level, $parent_id, $exception_brand_id, $list_id_checked = "", $object_name = "")
    {
        $strXML = "";
        $v_current_index = 0;
        $v_count = sizeof($arrAllList);
        for ($j = 0; $j < $v_count; $j++) {
            //Tim nhung thang con
            if ((strcasecmp(trim($arrAllList[$j][1]), $parent_id) == 0) && ($arrAllList[$j][5] >= $current_level)) {
                //if (($arrAllList[$j][1]==$parent_id) and ($arrAllList[$j][5]>=$current_level)){
                $arr_current_list[$v_current_index][0] = $arrAllList[$j][0];//PK
                $arr_current_list[$v_current_index][1] = $arrAllList[$j][1];//FK
                $arr_current_list[$v_current_index][2] = htmlspecialchars($arrAllList[$j][2]);//sCode
                $arr_current_list[$v_current_index][3] = htmlspecialchars($arrAllList[$j][3]);//sName
                $arr_current_list[$v_current_index][4] = $arrAllList[$j][4];//sType
                $arr_current_list[$v_current_index][5] = $arrAllList[$j][5];//sLevel
                $v_current_index++;
            }
        }
        //Truong hop mang $arr_current_list rong thi ket thuc de quy
        if ($v_current_index <= 0) {
            return;
        }
        for ($i = 0; $i < $v_current_index; $i++) {
            $v_current_id = $arr_current_list[$i][0];//PK
            $v_parent_id = $arr_current_list[$i][1];//FK
            $v_current_code = htmlspecialchars($arr_current_list[$i][2]);//sCode
            $v_current_name = htmlspecialchars($arr_current_list[$i][3]);//sName
            $v_current_type = $arr_current_list[$i][4];//sType
            $v_current_level = $arr_current_list[$i][5];//sLevel
            //Kiem tra ID neu no khong la $exception_brand_id thi moi tao
            if (strcasecmp(trim($v_current_id), $exception_brand_id) != 0) {
                //if($v_current_id!=$exception_brand_id){
                $arr_id_list = explode(",", $list_id_checked);
                $value_checked = 0;
                for ($j = 0; $j < sizeof($arr_id_list); $j++) {
                    if ($arr_id_list[$j] == $v_current_id) {
                        $value_checked = $v_current_id;
                    }
                }
                $strXML .= '<folder title="' . trim($v_current_name) . '" id="' . $v_current_id . '" value="' . $v_current_code . '" value_check="' . $value_checked . '" type="' . $v_current_type . '" parent_id="' . $parent_id . '" xml_tag_in_db_name="' . $object_name . '" level="' . $v_current_level . '" >' . "\n";
                if ($v_current_type == '0') {
                    $strXML .= self::_builtChildNode($arrAllList, $v_current_level, $v_current_id, $exception_brand_id, $list_id_checked, $object_name);
                }
                $strXML .= "</folder>" . "\n";
            }
        }
        return $strXML;
    }

    //Lay danh sach tat ca cac phogn ban
    public function _getArrAllUnit()
    {
        $arrOutput = array();
        $arrUnit = G_Cache::getInstance()->getAllUnit();
        $iTotal = sizeof($arrUnit);
        for ($i = 0; $i < $iTotal; $i++) {
            $v_unit = $arrUnit[$i];
            array_push($arrOutput, array($v_unit['id'], $v_unit['parent_id'], $v_unit['code'], $v_unit['name'], 0, 0));
        }
        return $arrOutput;
    }

    //Lay danh sach nguoi su dung cua mot don vi
    //*********************************************************************************************************************
    public static function _getArrChildStaff($arr_unit)
    {
        $arrOutput = array();
        $arrStaff = G_Cache::getInstance()->getAllStaff();
        $iTotal = sizeof($arrStaff);
        $v_count = sizeof($arr_unit);
        for ($j = 0; $j < $v_count; $j++) {
            for ($i = 0; $i < $iTotal; $i++) {
                $v_staff = $arrStaff[$i];
                if ($v_staff['unit_id'] == $arr_unit[$j]['0']) {
                    array_push($arrOutput, array($v_staff['id'], $v_staff['unit_id'], $v_staff['code'], $v_staff['name'], 1, 1));
                }
            }
        }
        return $arrOutput;
    }

    /**
     * @param $sFormFielName
     * @param $arrOption
     * @param $p_valuelist
     * @param bool $display
     * @param $readonly
     * @return string
     */
    public function _generateHtmlForTreeUser($formFielName, $arrOption, $p_valuelist, $display = false, $readonly)
    {
        $strHTML = '';
        $v_select_parent = (array_key_exists('v_select_parent', $arrOption) ? $arrOption['v_select_parent'] : false);
        if (trim($p_valuelist) != "" && trim($p_valuelist) != "0") {
            $strHTML = ($v_select_parent == "true" ? $this->_generateTableUnit($p_valuelist) : $this->_generateTableUser($p_valuelist, $display));
        }
        if (!($display && $readonly == "true")) {
            $strHTML = $strHTML . "<table class='list_table2'  width='100%' cellpadding='0' cellspacing='0'>";
            $strHTML = $strHTML . '<input type="hidden" id = "hdn_item_id" name="hdn_item_id" value="">';
            $arr_unit = self::_getArrAllUnit();
            $arr_staff = self::_getArrChildStaff($arr_unit);
            if ($v_select_parent == "true") {
                $arr_list = $arr_unit;
            } else {
                $arr_list = self::_attachTwoArray($arr_unit, $arr_staff, 5);
            }
            $v_current_id = 0;
            $xml_str = self::_builtXmlTree($arr_list, $v_current_id, 'true', 'home.jpg', 'home.jpg', 'user.jpg', $v_select_parent, $p_valuelist, $formFielName);

            $xml = new DOMDocument;
            $xml->loadXML($xml_str);

            $xsl = new DOMDocument;

            $xsl->load("public/xsl/treeview.xsl");

            // Configure the transformer
            $proc = new XSLTProcessor();

            $proc->importStylesheet($xsl); // attach the xsl rules

            $ret = $proc->transformToXML($xml);
            $strHTML = $strHTML . "<tr><td>" . $ret . "</td></tr>";

            $strHTML = $strHTML . "</table>";
        }
        return $strHTML;
    }

    public function _generateTableUser($p_valuelist, $display = false)
    {
        $objLib = new G_Lib();
        $objCache = new G_Cache();
        $arr_all_staff = $objCache->getAllStaff();
        $arr_all_unit = $objCache->getAllUnit();
        $strHTML = '';
        $arr_all_cooperator = explode(",", $p_valuelist);
        $v_cooperator_count = sizeof($arr_all_cooperator);
        $strHTML = $strHTML . '<table class="list-table-data" width="100%" cellpadding="0" cellspacing="0" >';
        $strHTML = $strHTML . '<col width="8%"><col width="26%"><col width="28%"><col width="38%">';
        $strHTML = $strHTML . '<tr  class="header">';
        $strHTML = $strHTML . '<td align="center" class="title" width="8%">STT</td>';
        $strHTML = $strHTML . '<td align="center" class="title" width="26%">H&#7885; t&#234;n</td>';
        $strHTML = $strHTML . '<td align="center" class="title" width="28%">Ch&#7913;c v&#7909</td>';
        $strHTML = $strHTML . '<td align="center" class="title" width="38%">Ph&#242;ng ban</td>';
        $strHTML = $strHTML . '</tr>';
        $v_current_style_name = 'round_row';
        for ($j = 0; $j < $v_cooperator_count; $j++) {
            $v_cooperator_id = $arr_all_cooperator[$j];
            $v_cooperator_name = $objLib->getItemAttrByIndex($arr_all_staff, $v_cooperator_id, 'id', 'name');
            $v_cooperator_position_name = $objLib->_getValuesByIds($arr_all_staff, $v_cooperator_id, 'id', 'position_name');
            $v_cooperator_unit_id = $objLib->_getValuesByIds($arr_all_staff, $v_cooperator_id, 'id', 'unit_id');
            $v_cooperator_unit_name = $objLib->_getValuesByIds($arr_all_unit, $v_cooperator_unit_id, 'id', 'name');
            if ($v_current_style_name == "odd_row") {
                $v_current_style_name = "round_row";
            } else {
                $v_current_style_name = "odd_row";
            }
            $strHTML = $strHTML . '<tr class="' . $v_current_style_name . '">';
            if ($display) {
                $strHTML = $strHTML . '<td align="center"><input type="radio" id="item_staff_id" name="item_staff_id" value="' . $v_cooperator_id . '" /></td>';
            } else {
                $strHTML = $strHTML . '<td align="center">' . ($j + 1) . '</td>';
            }
            $strHTML = $strHTML . '<td align="left">' . $v_cooperator_name . '&nbsp;</td>';
            $strHTML = $strHTML . '<td align="left">' . $v_cooperator_position_name . '&nbsp;</td>';
            $strHTML = $strHTML . '<td align="left">' . $v_cooperator_unit_name . '&nbsp;</td>';
            $strHTML = $strHTML . '</tr>';
        }
        $strHTML = $strHTML . '</table>';
        return $strHTML;
    }

    public function _generateTableUnit($p_valuelist)
    {
        $objLib = new G_Lib();
        $arr_all_unit = G_Cache::getInstance()->getAllUnit();
        $strHTML = '';
        $arr_all_cooperator = explode(",", $p_valuelist);
        $v_cooperator_count = sizeof($arr_all_cooperator);
        $strHTML = $strHTML . '<table class="list-table-data" width="100%" cellpadding="0" cellspacing="0" >';
        $$strHTML = $strHTML . '<colgroup><col width="8%"><col width="92%"></colgroup>';
        $strHTML = $strHTML . '<tr  class="header">';
        $strHTML = $strHTML . '<td align="center" class="title" width="8%">STT</td>';
        $strHTML = $strHTML . '<td align="center" class="title" width="92%">Ph&#242;ng ban</td>';
        $strHTML = $strHTML . '</tr>';
        $v_current_style_name = 'round_row';
        for ($j = 0; $j < $v_cooperator_count; $j++) {
            $v_cooperator_id = $arr_all_cooperator[$j];
            $v_cooperator_name = $objLib->_getItemAttrById($arr_all_unit, $v_cooperator_id, 'name');
            $v_cooperator_unit_id = $objLib->_getItemAttrById($arr_all_unit, $v_cooperator_id, 'id');
            $v_cooperator_unit_name = $objLib->_getItemAttrById($arr_all_unit, $v_cooperator_unit_id, 'name');
            if ($v_current_style_name == "odd_row") {
                $v_current_style_name = "round_row";
            } else {
                $v_current_style_name = "odd_row";
            }
            $strHTML = $strHTML . '<tr class="' . $v_current_style_name . '">';
            $strHTML = $strHTML . '<td align="center">' . ($j + 1) . '</td>';
            $strHTML = $strHTML . '<td align="left">' . $v_cooperator_name . '&nbsp;</td>';
            $strHTML = $strHTML . '</tr>';
        }
        $strHTML = $strHTML . '</table>';
        return $strHTML;
    }

    public static function _attachTwoArray($p_array1, $p_array2, $number_element){
        $v_count_arr1 = sizeof($p_array1);
        $v_count_arr2 = sizeof($p_array2);
        $j = $v_count_arr1;
        for($i = 0; $i<$v_count_arr2; $i++){
            for($h=0; $h<=$number_element; $h++){
                if (isset($p_array2[$i][$h])){
                    $p_array1[$j][$h] = $p_array2[$i][$h];
                }else{
                    $p_array1[$j][$h] = null;
                }
            }
            $j++;
        }
        return $p_array1;
    }
}