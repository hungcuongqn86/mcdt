<?php
/**
 * @see
 *
 */

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 09/04/2015
 * Noi dung: Tao lop G_Export
 */
class G_Export
{
    protected static $_instance = null;

    public function __construct()
    {

    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Creater :TRUONGDV
     * Date : 09/11/2012
     * Idea : Doc template EXCEL và xuất ra file EXCEL
     */
    public function createreportexcel($arrParameter)
    {
        //Lay params
        $sPathXmlFile = $arrParameter['sPathXmlFile'];
        $sParrentTagName = $arrParameter['sParrentTagName'];
        $TagName = $arrParameter['TagName'];
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile);


        $objReader = G_Components_PhpExcel::createReader('Excel5');
        $objPHPExcel = $objReader->load($arrParameter['sPathTemplateFile'] . $arrParameter['sTemplateFile'] . '.xls');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        //Lay tieu chi tim kiem
        $arrFilter = $ojbConfigXml->table_struct_of_filter_form->filter_row->toArray();
        $arrFilterValue = $arrParameter['arrFilterValue'];
        $arrTagFilter = $this->getDataFilter($arrFilter, $arrFilterValue);
        //Lay chuoi sql và replace
        $sql = $ojbConfigXml->report_sql->sql;
        $data = $this->getAllData($sql, $arrTagFilter);
        //var_dump($data);die();
        //Header
        $arrHeader = $ojbConfigXml->report_header->col->toArray();
        if (is_array($arrHeader)) {
            foreach ($arrHeader as $key => $arrElement) {
                $field_name = $arrElement['field_name'];
                $row = $arrElement['row'];
                $field_column = $arrElement['field_column'];
                $replace = $arrElement['replace'];
                $type = $arrElement['type'];
                switch ($type) {
                    case 'function':
                        $params = $arrElement['param'];
                        $arrParams = explode(',', $params);
                        $phpFunction = $arrElement['phpFunction'];
                        $pClassname = $arrElement['classname'];
                        $i = 0;
                        $arrParamFunc = array();
                        foreach ($arrParams as $key => $param) {
                            $arrParamFunc[] = (isset($arrFilterValue[$param]) ? $arrFilterValue[$param] : $param);
                            $i++;
                        }
                        $objClass = new $pClassname;
                        $value = $objClass->$phpFunction($arrParamFunc);
                        break;

                    default:
                        $value = (isset($arrFilterValue[$field_column]) ? $arrFilterValue[$field_column] : '');
                        break;
                }
                $rowvalue = $objWorksheet->getCell($field_name . $row)->getValue();
                $rowvalue = str_replace('#' . $replace . '#', $value, $rowvalue);
                //echo $rowvalue; die();
                $objWorksheet->setCellValue($field_name . $row, $rowvalue);
            }
        }
        $level1groupid = $ojbConfigXml->report_sql->level1groupid;
        $level1groupname = $ojbConfigXml->report_sql->level1groupname;
        $merge_col_from = $ojbConfigXml->report_sql->merge_col_from;
        $merge_col_to = $ojbConfigXml->report_sql->merge_col_to;
        $baseRow = $ojbConfigXml->report_body->baseRow;
        $calculate_total = $ojbConfigXml->report_sql->calculate_total;
        // var_dump($ojbConfigXml); die();
        // var_dump($level1groupid);
        // $sql = "Select count(distinct ".$level1groupid.") from "
        if (isset($ojbConfigXml->report_body)) {
            $TagElements = $ojbConfigXml->report_body->col_list->col->toArray();
            // var_dump($TagElements);
            // var_dump(sizeof($TagElements)); die();
            // var_dump($data);
            $total_group = 0;
            isset($data[0]['TOTAL_GROUP']) ? $total_group = $data[0]['TOTAL_GROUP'] : $total_group = 0;

            if ($calculate_total) {
                $objWorksheet->insertNewRowBefore($baseRow, sizeof($data) + $total_group + 1);
            } else
                $objWorksheet->insertNewRowBefore($baseRow, sizeof($data) + $total_group);
            // echo (sizeof($data) + $total_group);

            $total = $baseRow + sizeof($data);
            if (is_array($data)) {
                $count = 0;
                $temp = 0;
                $default = '';
                $defaultName = '';
                $row = $baseRow;
                // var_dump($data);
                foreach ($data as $r => $dataRow) {
                    //$row = $baseRow + $r;
                    if ($level1groupid != '') {
                        if ($dataRow[$level1groupid] != $default) {
                            $count = 0;
                            $default = $dataRow[$level1groupid];
                            $defaultName = $dataRow[$level1groupname];
                            $objWorksheet->mergeCells($merge_col_from . $row . ':' . $merge_col_to . $row);
                            $objWorksheet->getStyle($merge_col_from . $row)->getFont()->applyFromArray(G_Extensions_Ext::getInstance()->_setFontTitleColGroupExcell());
                            $objWorksheet->setCellValue($this->previousChar($merge_col_from) . $row, PHPExcel_Calculation_MathTrig::ROMAN($temp + 1));
                            $objWorksheet->setCellValue($merge_col_from . $row, $defaultName);
                            $objWorksheet->getRowDimension($row)->setRowHeight(-1);
                            $objWorksheet->getStyle($merge_col_from . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            // $objWorksheet->getStyle($this->previousChar($merge_col_from) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $row++;
                            $temp++;
                        }
                    }
                    $count++;
                    //Duyet cac phan tu cua
                    $numrow = sizeof($data);
                    foreach ($TagElements as $elements => $arrElement) {
                        $field_name = $arrElement['field_name'];
                        $field_column = $arrElement['field_column'];
                        $data_format = $arrElement['data_format'];
                        if ($data_format == 'identity') {
                            $objWorksheet->setCellValue($field_name . $row, $count);
                        } else {
                            $objWorksheet->setCellValue($field_name . $row, G_Convert::getInstance()->_restoreBadChar($dataRow[$field_column]));
                            // $objWorksheet->setCellValue($field_name . ($numrow+), $dataRow[$field_column]);
                        }
                    }
                    //Autofit
                    $objWorksheet->getRowDimension($row)->setRowHeight(-1);
                    $row++;
                }

                if ($calculate_total) {
                    foreach ($TagElements as $elements => $arrElement) {
                        $field_name = $arrElement['field_name'];
                        $field_column = $arrElement['field_column'];
                        $data_format = $arrElement['data_format'];
                        if ($field_name != 'A') {
                            $objWorksheet->setCellValue($field_name . $total, '=SUM(' . $field_name . $baseRow . ':' . $field_name . ($total - 1) . ')');
                        } else {
                            $objWorksheet->setCellValue($field_name . $total, 'Tổng');
                            $objWorksheet->getStyle($field_name . $total)->getFont()->applyFromArray(G_Extensions_Ext::getInstance()->_setFontTitleColExcell());
                        }
                    }
                }
            }
        }
        $objWorksheet->removeRow($baseRow - 1, 1);
        /* $objWorksheet->setCellValue('G'.($row+3),'Hà Nội, ngày '.date('d').' tháng '.date('m').' năm '.date('Y'));
          $objWorksheet->setCellValue('B'.($row+8),G_Lib::_getItemAttrById($_SESSION['arr_all_staff'],$_SESSION['staff_id'],'name')); */

        /* $objPageSetup = new PHPExcel_Worksheet_PageSetup();
          $objPageSetup->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
          $objPageSetup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE); */
        //$objPageSetup->setPrintArea("E5:H7");
        // $objPageSetup->setFitToWidth(1);
        // $objPHPExcel->getActiveSheet()->setPageSetup($objPageSetup);
        // $fileName = $arrParameter['reportname'];
        $fileName = $arrParameter['reportname'] . ' ' . date('dmy') . date('His');
        if ($arrParameter['type'] == 'excel') {
            //Xuat file
            $objWriter = G_Components_PhpExcel::createWriter($objPHPExcel, 'Excel5');
            $fileName = $fileName . ".xls";
        } else {
            //$objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
            $objWriter = G_Components_PhpExcel::createWriter($objPHPExcel, 'PDF');
            $objWriter->setPreCalculateFormulas(false);
            $fileName = $fileName . ".pdf";
        }
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $objWriter->save($reportFile);
        return $fileName;
    }

    public function previousChar($character)
    {
        $myascii = ord($character);
        return chr($myascii - 1);
    }

    /**
     * Creater :TRUONGDV
     * Date : 09/11/2012
     * Idea : Doc template WORD và xuất ra file WORD
     */
    public function createreportword($arrParameter)
    {
        //Lay params
        $sPathXmlFile = $arrParameter['sPathXmlFile'];
        $sParrentTagName = $arrParameter['sParrentTagName'];
        $TagName = $arrParameter['TagName'];
        $dirTemplate = $arrParameter['sPathTemplateFile'] . $arrParameter['sTemplateFile'] . '.docx';
        //Goi class xu ly
        Zend_Loader::loadClass('Zend_Config_Xml');
        Zend_Loader::loadClass('G_Components_PhpDocx');
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile);
        $phpdocx = new G_Components_PhpDocx($dirTemplate);
        //Lay tieu chi tim kiem
        $arrFilter = $ojbConfigXml->table_struct_of_filter_form->filter_row->toArray();
        $arrFilterValue = $arrParameter['arrFilterValue'];
        $arrTagFilter = $this->getDataFilter($arrFilter, $arrFilterValue);
        //Lay chuoi sql và replace
        $sql = $ojbConfigXml->report_sql->sql;
        $data = $this->getAllData($sql, $arrTagFilter);
        $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
        //Header
        $arrHeader = $ojbConfigXml->report_header->col->toArray();
        if (is_array($arrHeader)) {
            foreach ($arrHeader as $key => $arrElement) {
                $field_name = $arrElement['field_name'];
                $row = $arrElement['row'];
                $field_column = $arrElement['field_column'];
                $replace = $arrElement['replace'];
                $type = $arrElement['type'];
                switch ($type) {
                    case 'function':
                        $params = $arrElement['param'];
                        $arrParams = explode(',', $params);
                        $phpFunction = $arrElement['phpFunction'];
                        $pClassname = $arrElement['classname'];
                        $i = 0;
                        $arrParamFunc = array();
                        foreach ($arrParams as $key => $param) {
                            if (isset($arrFilterValue[$param])) {
                                $arrParamFunc[] = (isset($arrFilterValue[$param]) ? $arrFilterValue[$param] : '');
                            } else {
                                $arrParamFunc[] = $param;
                            }
                            $i++;
                        }
                        $objClass = new $pClassname;
                        $value = $objClass->$phpFunction($arrParamFunc);
                        break;

                    default:
                        $value = (isset($arrFilterValue[$field_column]) ? $arrFilterValue[$field_column] : '');
                        break;
                }
                $phpdocx->assign('#' . $replace . '#', $value);
            }
        }
        $arrDocx = array();
        $level1groupid = $ojbConfigXml->report_sql->level1groupid;
        $level1groupname = $ojbConfigXml->report_sql->level1groupname;
        $merge_col_from = $ojbConfigXml->report_sql->merge_col_from;
        if (isset($ojbConfigXml->report_body)) {
            $TagElements = $ojbConfigXml->report_body->col_list->col->toArray();
            $arrBlock = array();
            $count = 0;
            $stt = 0;
            $default = '';
            $defaultName = '';
            if (is_array($data)) {
                foreach ($data as $r => $dataRow) {
                    $arrTemp = array();
                    if ($level1groupid != '') {
                        if ($dataRow[$level1groupid] != $default) {
                            $count = 0;
                            $default = $dataRow[$level1groupid];
                            $defaultName = $dataRow[$level1groupname];
                            array_push($arrBlock, array('content' => array(array('#' . $merge_col_from . '#' => $defaultName)), 'group' => array('blockname' => $r + 1)));
                        }
                    }
                    $count++;
                    //Duyet cac phan tu cua
                    foreach ($TagElements as $elements => $arrElement) {
                        $replace = $arrElement['replace'];
                        $field_column = $arrElement['field_column'];
                        $data_format = $arrElement['data_format'];
                        if ($data_format == 'identity') {
                            $arrTemp['#' . $replace . '#'] = $count;
                        } else {
                            $arrTemp['#' . $replace . '#'] = $dataRow[$field_column];
                        }
                    }
                    array_push($arrDocx, $arrTemp);
                }
                $phpdocx->assignBlock("blockname", $arrDocx);
                //Group
                if ($level1groupid != '') {
                    foreach ($arrBlock as $key => $value) {
                        $phpdocx->assignNestedBlock("blockgroup", $value['content'], $value['group']);
                    }
                }
            }
        }
        //var_dump($arrDocx); die();
        $fileName = "report" . date('dmy') . date('His') . ".doc";
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $phpdocx->save($reportFile);
        return $fileName;
    }


    /**
     * Creater :TRUONGDV
     * Date : 31/05/2013
     * Idea : Doc template WORD và xuất ra file WORD
     */
    public function exportfilewordlist($arrParameter)
    {
        //Lay params
        $sPathXmlFile = $arrParameter['sPathXmlFile'];
        $sParrentTagName = $arrParameter['sParrentTagName'];
        $TagName = $arrParameter['TagName'];
        $dirTemplate = $arrParameter['sPathTemplateFile'] . $arrParameter['sTemplateFile'] . '.docx';
        $data = $arrParameter['data'];
        $dataFilter = $arrParameter['dataFilter'];
        //Goi class xu ly
        Zend_Loader::loadClass('Zend_Config_Xml');
        Zend_Loader::loadClass('G_Components_PhpDocx');
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile);
        $phpdocx = new G_Components_PhpDocx($dirTemplate);
        //Lay tieu chi tim kiem
        /* $arrFilter = $ojbConfigXml->table_struct_of_filter_form->filter_row->toArray();
          $arrFilterValue = $arrParameter['arrFilterValue'];
          $arrTagFilter = $this->getDataFilter($arrFilter,$arrFilterValue); */
        //Lay chuoi sql và replace
        /* $sql = $ojbConfigXml->report_sql->sql;
          $data = $this->getAllData($sql,$arrTagFilter); */
        $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
        //Header
        $arrHeader = $ojbConfigXml->report_header->col->toArray();
        if (is_array($arrHeader)) {
            foreach ($arrHeader as $key => $arrElement) {
                $field_name = $arrElement['field_name'];
                $row = $arrElement['row'];
                $field_column = $arrElement['field_column'];
                $replace = $arrElement['replace'];
                $type = $arrElement['type'];
                switch ($type) {
                    case 'function':
                        $params = $arrElement['param'];
                        $arrParams = explode(',', $params);
                        $phpFunction = $arrElement['phpFunction'];
                        $pClassname = $arrElement['classname'];
                        $i = 0;
                        $arrParamFunc = array();
                        foreach ($arrParams as $key => $param) {
                            $param = trim($param);
                            if (isset($dataFilter[$param])) {
                                $arrParamFunc[] = $dataFilter[$param];
                            } else {
                                $arrParamFunc[] = $param;
                            }
                            $i++;
                        }
                        $objClass = new $pClassname;
                        $value = $objClass->$phpFunction($arrParamFunc);
                        break;

                    default:
                        $value = (isset($dataFilter[$field_column]) ? $dataFilter[$field_column] : '');
                        break;
                }
                $phpdocx->assign('#' . $replace . '#', $value);
            }
        }
        $arrDocx = array();
        $level1groupid = $ojbConfigXml->report_sql->level1groupid;
        $level1groupname = $ojbConfigXml->report_sql->level1groupname;
        $merge_col_from = $ojbConfigXml->report_sql->merge_col_from;
        if (isset($ojbConfigXml->report_body)) {
            $TagElements = $ojbConfigXml->report_body->col_list->col->toArray();
            $arrBlock = array();
            $count = 0;
            $stt = 0;
            $default = '';
            $defaultName = '';
            if (is_array($data)) {
                foreach ($data as $r => $dataRow) {
                    $arrTemp = array();
                    if ($level1groupid != '') {
                        if ($dataRow[$level1groupid] != $default) {
                            $count = 0;
                            $default = $dataRow[$level1groupid];
                            $defaultName = $dataRow[$level1groupname];
                            array_push($arrBlock, array('content' => array(array('#' . $merge_col_from . '#' => $defaultName)), 'group' => array('blockname' => $r + 1)));
                        }
                    }
                    $count++;
                    //Duyet cac phan tu cua
                    foreach ($TagElements as $elements => $arrElement) {
                        $field_name = $arrElement['field_name'];
                        $field_column = $arrElement['field_column'];
                        $data_format = $arrElement['data_format'];
                        //
                        $type = (isset($arrElement['type']) ? $arrElement['type'] : '');
                        switch ($type) {
                            case 'function':
                                $params = $arrElement['param'];
                                $arrParams = explode(',', $params);
                                $phpFunction = $arrElement['phpFunction'];
                                $pClassname = $arrElement['classname'];
                                $arrParamFunc = array();
                                foreach ($arrParams as $key => $param) {
                                    if (isset($dataRow[$param])) {
                                        $arrParamFunc[] = $dataRow[$param];
                                    } else {
                                        $arrParamFunc[] = $param;
                                    }
                                }
                                $objClass = new $pClassname;
                                $value = $objClass->$phpFunction($arrParamFunc);
                                break;
                            default:
                                $value = (isset($dataRow[$field_column]) ? $dataRow[$field_column] : '');
                                break;
                        }

                        if ($data_format == 'identity') {
                            $arrTemp['#' . $field_name . '#'] = $count;
                        } else {
                            $arrTemp['#' . $field_name . '#'] = $value;
                        }
                    }
                    array_push($arrDocx, $arrTemp);
                }
                $phpdocx->assignBlock("blockname", $arrDocx);
                //Group
                if ($level1groupid != '') {
                    foreach ($arrBlock as $key => $value) {
                        $phpdocx->assignNestedBlock("blockgroup", $value['content'], $value['group']);
                    }
                }
            }
        }
        //var_dump($arrDocx); die();
        $fileName = "nv01" . date('dmy') . date('His') . ".doc";
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $phpdocx->save($reportFile);
        return $fileName;
    }

    public function createreportwordmultiblock($arrParameter)
    {
        //Lay params
        $sPathXmlFile = $arrParameter['sPathXmlFile'];
        $sParrentTagName = $arrParameter['sParrentTagName'];
        $TagName = $arrParameter['TagName'];
        $dirTemplate = $arrParameter['sPathTemplateFile'] . $arrParameter['sTemplateFile'] . '.docx';
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile);
        $phpdocx = new G_Components_PhpDocx($dirTemplate);
        $data = $arrParameter['data'];
        $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
        //Header
        //var_dump($data);
        $datanull = array();
        $dataheader = (isset($data[0])) ? $data[0] : $datanull;
        $arrHeader = $ojbConfigXml->report_header->col->toArray();
        if (is_array($arrHeader)) {
            foreach ($arrHeader as $key => $arrElement) {
                $field_name = $arrElement['field_name'];
                //  $row = $arrElement['row'];
                $field_column = $arrElement['field_column'];
                $replace = $arrElement['replace'];
                $type = $arrElement['type'];
                $block = (isset($arrElement['block']) ? $arrElement['block'] : '');
                switch ($type) {
                    case 'function':
                        $params = $arrElement['param'];
                        $arrParams = explode(',', $params);
                        $phpFunction = $arrElement['phpFunction'];
                        $pClassname = $arrElement['classname'];
                        $i = 0;
                        $arrParamFunc = array();
                        foreach ($arrParams as $key1 => $param) {
                            if (isset($dataheader[$param])) {
                                $arrParamFunc[] = $dataheader[$param];
                            } else {
                                $arrParamFunc[] = $param;
                            }
                            $i++;
                        }
                        $objClass = new $pClassname;
                        $value = $objClass->$phpFunction($arrParamFunc);
                        //var_dump($value);
                        break;

                    default:
                        $value = (isset($dataheader[$field_column]) ? $dataheader[$field_column] : '');
                        //var_dump($data['0']['C_MEMBER']);
                        break;
                }
                //var_dump($data);
                //var_dump($data['C_MEMBER']);
                if ($block == '') {
                    $phpdocx->assign('#' . $replace . '#', $value);
                } else {
                    // chekc
                    $phpdocx->assignBlock($block, $value);
                }
            }
        }
        //var_dump($arrHeader);
        //var_dump($data['C_MEMBER']); die();
        $arrDocx = array();
        if (isset($ojbConfigXml->report_body)) {
            $TagElements = $ojbConfigXml->report_body->col_list->col->toArray();
            $arrBlock = array();
            $count = 0;
            $stt = 0;
            $default = '';
            $defaultName = '';
            if (is_array($data)) {
                foreach ($data as $r => $dataRow) {
                    $arrTemp = array();
                    $count++;
                    foreach ($TagElements as $elements => $arrElement) {
                        $replace = $arrElement['replace'];
                        $field_column = $arrElement['field_column'];
                        $data_format = (isset($arrElement['data_format']) ? $arrElement['data_format'] : '');
                        if ($data_format == 'identity') {
                            $arrTemp['#' . $replace . '#'] = $count;
                        } else {
                            $arrTemp['#' . $replace . '#'] = $dataRow[$field_column];
                        }
                    }
                    array_push($arrDocx, $arrTemp);
                }
                $phpdocx->assignBlock("blockname", $arrDocx);
            }
        }
        if (isset($arrParameter['petitionType']) && $arrParameter['petitionType'] !== '') {
            $fileName = mb_strtolower($arrParameter['petitionType'], 'UTF-8') . ".doc";
        } else {
            $fileName = "report" . ".doc";
        }
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $phpdocx->save($reportFile);
        return $fileName;
    }

    /**
     * @param $arrParameter
     * @return array
     */
    public function commonexportfileword($arrParameter)
    {
        $objDb = new G_Db();
        $arrCodeTemp = explode(',',$arrParameter['scodetemplist']);
        $arrRecordId = explode(',',$arrParameter['pkrecordlist']);
        $count_codetemp = sizeof($arrCodeTemp);
        $count_record = sizeof($arrRecordId);
        $fileName = 'phieu_in_'.date('Y').'_'.date('m').'_'.date('d').'_'.date('H').'_'.date('i').'_'.date('s').'.doc';
        $dirTemplate = $arrParameter['sPathTemplateFile'] .$arrCodeTemp[0]. '.docx';
        $sPathXmlFile = $arrParameter['sPathXmlFile'].$arrCodeTemp[0].'.xml';
        $ojbConfigXml = new G_Xml($sPathXmlFile);
        $phpdocx = new G_Components_PhpDocx($dirTemplate);
        for ($i = 0; $i < $count_codetemp; $i++) {
            $sPathXmlFile = $arrParameter['sPathXmlFile'] . $arrCodeTemp[$i] . '.xml';
            $dirTemplate = $arrParameter['sPathTemplateFile'] . $arrCodeTemp[$i] . '.docx';
            if (file_exists($dirTemplate)) {
                if ($i) {
                    $ojbConfigXml->__loadxml($sPathXmlFile);
                }
                $arrHeader = $ojbConfigXml->report_header->col->toArray();
                for ($j = 0; $j < $count_record; $j++) {
                    $sql = $ojbConfigXml->sql_block->sql_str;
                    $data = $objDb->_querySql(array('pk' => $arrRecordId[$j]), $sql, false, 0);
                    foreach ($arrHeader as $key => $arrElement) {
                        $field_column = (isset($arrElement['field_column']) ? $arrElement['field_column'] : '');
                        $replace = (isset($arrElement['replace']) ? $arrElement['replace'] : '');
                        $type = $arrElement['type'];
                        $block = (isset($arrElement['block']) ? $arrElement['block'] : '');
                        switch ($type) {
                            case 'function':
                                $params = $arrElement['param'];
                                $arrParams = explode(',', $params);
                                $phpFunction = $arrElement['phpFunction'];
                                $pClassname = $arrElement['classname'];
                                $l = 0;
                                $arrParamFunc = array();
                                foreach ($arrParams as $key1 => $param) {
                                    $param = trim($param);
                                    if (isset($data[$param])) {
                                        $arrParamFunc[] = $data[$param];
                                    } else {
                                        $arrParamFunc[] = $param;
                                    }
                                    $l++;
                                }
                                $objClass = new $pClassname;
                                $value = $objClass->$phpFunction($arrParamFunc);
                                break;

                            default:
                                $value = (isset($data[$field_column]) ? $data[$field_column] : '');
                                break;
                        }
                        if ($block != '') {
                            $phpdocx->assignBlock($block, $value);
                        } else {
                            $value = $this->breakLineString((string)$value);
                            $phpdocx->assign('#' . $replace . '#', $value);
                        }
                    }
                    #group
                    $arrDocx = array();
                    $level1groupid = $ojbConfigXml->sql_block->level1groupid;
                    $level1groupname = $ojbConfigXml->sql_block->level1groupname;
                    $merge_col_from = $ojbConfigXml->sql_block->merge_col_from;
                    if (isset($ojbConfigXml->report_body)) {
                        $TagElements = $ojbConfigXml->report_body->col_list->col->toArray();
                        $sql = $ojbConfigXml->sql_block->sql_body_str;
                        $data = $objDb->_querySql(array('pk' => $arrRecordId[$j]), $sql, true, 0);
                        $arrBlock = array();
                        $count = 0;
                        $default = '';
                        if (is_array($data)) {
                            foreach ($data as $r => $dataRow) {
                                if ($level1groupid != '') {
                                    if ($dataRow[$level1groupid] != $default) {
                                        //echo '--'.$r;
                                        $count = 0;
                                        $default = $dataRow[$level1groupid];
                                        $defaultName = $dataRow[$level1groupname];
                                        array_push($arrBlock, array('content' => array(array('#' . $merge_col_from . '#' => $defaultName)), 'group' => array('blockname' => $r + 1)));
                                    }
                                }
                                $arrTemp = array();
                                $count++;
                                foreach ($TagElements as $elements => $arrElement) {
                                    $replace = $arrElement['replace'];
                                    $field_column = $arrElement['field_column'];
                                    $data_format = (isset($arrElement['data_format']) ? $arrElement['data_format'] : '');
                                    if ($data_format == 'identity') {
                                        $arrTemp['#' . $replace . '#'] = $count;
                                    } else {
                                        $arrTemp['#' . $replace . '#'] = $dataRow[$field_column];
                                    }
                                }
                                array_push($arrDocx, $arrTemp);
                            }
                            $phpdocx->assignBlock("blockname", $arrDocx);
                            //Group
                            if ($level1groupid != '') {
                                //var_dump($arrBlock);exit;
                                foreach ($arrBlock as $key => $value) {
                                    $phpdocx->assignNestedBlock("blockgroup", $value['content'], $value['group']);
                                }
                            }
                        }
                    }
                    //exit;
                    $phpdocx->addtemp($dirTemplate);
                }
            }
        }
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $phpdocx->merconten($reportFile);
        return $fileName;
    }
    /**
     * Creater :TRUONGDV
     * Date : 31/05/2013
     * Idea : Doc template WORD và xuất ra file WORD
     */
    public function exportfileword($arrParameter)
    {
        $count_codetemp = sizeof($arrParameter);
        $fileName = array();
        for ($i = 0; $i < $count_codetemp; $i++) {
            $sPathXmlFile = $arrParameter[$i]['sPathXmlFile'];
            $dirTemplate = $arrParameter[$i]['sPathTemplateFile'] . $arrParameter[$i]['sTemplateFile'] . '.docx';
            // var_dump($dirTemplate);die();
            $data = $arrParameter[$i]['data'];
            $optionprint = (isset($arrParameter[$i]['optionprint']) ? $arrParameter[$i]['optionprint'] : '');
            //Tao doi tuong
            $ojbConfigXml = new G_Xml($sPathXmlFile);
            $phpdocx = new G_Components_PhpDocx($dirTemplate);
            $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
            $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
            //Header
            $arrHeader = $ojbConfigXml->report_header->col->toArray();
            // var_dump($arrHeader);
            if (is_array($arrHeader)) {
                foreach ($arrHeader as $key => $arrElement) {
                    $field_column = (isset($arrElement['field_column']) ? $arrElement['field_column'] : '');
                    $replace = (isset($arrElement['replace']) ? $arrElement['replace'] : '');

                    $type = $arrElement['type'];
                    $block = (isset($arrElement['block']) ? $arrElement['block'] : '');
                    switch ($type) {
                        case 'function':
                            $params = $arrElement['param'];
                            $arrParams = explode(',', $params);
                            $phpFunction = $arrElement['phpFunction'];
                            $pClassname = $arrElement['classname'];
                            $l = 0;
                            $arrParamFunc = array();
                            foreach ($arrParams as $key1 => $param) {
                                $param = trim($param);
                                if (isset($data[$param])) {
                                    $arrParamFunc[] = $data[$param];
                                } else {
                                    $arrParamFunc[] = $param;
                                }
                                $l++;
                            }
                            $objClass = new $pClassname;
                            $value = $objClass->$phpFunction($arrParamFunc);
                            // var_dump($value);
                            break;

                        default:
                            $value = (isset($data[$field_column]) ? $data[$field_column] : '');
                            break;
                    }
                    if ($block != '') {
                        $phpdocx->assignBlock($block, $value);
                    } else {
                        $value = $this->breakLineString((string)$value);
                        $phpdocx->assign('#' . $replace . '#', $value);
                        // var_dump($replace);var_dump($value);
                    }

                }
            }
            // var_dump($phpdocx);die();
            // if($i==0 || $i==1)
            //     echo $i.':'.($arrParameter[$i]['petitionType']).'cde';
            if (isset($arrParameter[$i]['petitionType']) && $arrParameter[$i]['petitionType'] !== '') {
                $fileName[$i] = mb_strtolower($arrParameter[$i]['petitionType'], 'UTF-8') . ".doc";
            } else {
                $fileName[$i] = "report" . ".doc";
            }

            // Duong dan file report
            $path = $_SERVER['SCRIPT_FILENAME'];
            $path = substr($path, 0, -9);
            $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName[$i];
            $phpdocx->save($reportFile);
            if ($optionprint == 'COM') {
                G_Export::getInstance()->convertDocByCom($reportFile);
            }
        }
        return $fileName;
    }

    public static function breakLine($arrInput)
    {
        $pContent = (string)$arrInput[0];
        $replace = (string)$arrInput[1];
        $arrLine = explode(chr(10), $pContent);
        $arrTemp = array();
        $arrDocx = array();
        if (!empty($arrLine)) {
            foreach ($arrLine as $key => $content) {
                $arrTemp['#' . $replace . '#'] = $content;
                array_push($arrDocx, $arrTemp);
            }
        }
        // var_dump($arrDocx);
        return $arrDocx;

    }

    public function breakLineString($pContent = '')
    {
        $ilen = strlen($pContent);
        if ($ilen > 0) {
            for ($index = 0; $index < $ilen; $index++) {
                if (ord(substr($pContent, $index, 1)) == 10) {//=10 la ma xuong dau dong
                    $pContent = str_replace(chr(10), "<w:br/>", $pContent);
                    // $pContent = trim($pContent,' ');
                }
            }
        }
        return $pContent;
    }

    public function breakLineString2($pContent = '')
    {
        $ilen = strlen($pContent);
        if ($ilen > 0) {
            for ($index = 0; $index < $ilen; $index++) {
                if (ord(substr($pContent, $index, 1)) == 10) {//=10 la ma xuong dau dong
                    $pContent = str_replace(chr(10), '!~!', $pContent);
                }
            }
        }
        $arrConten = explode('!~!', $pContent);
        $pContent = '';
        foreach ($arrConten as $key => $content) {
            $pContent .= '<w:br/><w:t>' . trim($content) . '</w:t>';
        }
        return $pContent;
    }

    /**
     * @param $sDocPath
     */
    public function convertDocByCom($sDocPath){
        $word = new COM("word.application") or die("Unable to instanciate Word!");
        $word->Visible = 1;
        $word->Documents->Open($sDocPath);
        $sDocPath = realpath($sDocPath);
        $word->Documents[1]->SaveAs($sDocPath, 1);
        $word->Quit();
        $word = null;
    }
    private function getDataFilter($arrFilter, $arrFilterValue) {
        $arrTagFilter = array(); //Luu Tag=>value cua tieu thuc loc
        if (!isset($arrFilter[0])) {
            $arrTemp = array();
            array_push($arrTemp, $arrFilter);
            $arrFilter = $arrTemp;
        }

        foreach ($arrFilter as $key => $value) {
            $arrTemF = explode(',', $value['tag_list']);
            foreach ($arrTemF as $keyF => $valueF) {
                $arrFil = array('C_KEY' => $valueF, 'C_VALUE' => $arrFilterValue[$valueF]);
                array_push($arrTagFilter, $arrFil);
            }
        }
        return $arrTagFilter;
    }
    private function getAllData($sql, $arrTagFilter, $option = '0') {
        $arrResult = array();
        $db = new G_Db();
        if (is_array($arrTagFilter)) {
            $userIdentity = Zend_Auth::getInstance()->getIdentity();
            $sOwnerCode = (isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='' ? $userIdentity->sDistrictWardProcess : $userIdentity->sOwnerCode);
            foreach ($arrTagFilter as $key => $value) {
                $sql = str_replace('#' . $value['C_KEY'] . '#', $value['C_VALUE'], $sql);
            }
            $sql = str_replace("#OWNER_CODE#", $sOwnerCode, $sql);
            // echo $sql; die();
            if ($option == '1') {
                $arrResult = $db->adodbExecSqlString($sql);
            } else {
                $arrResult = $db->adodbQueryDataInNameMode($sql);
            }
            //var_dump($arrResult); die();
        }
        return $arrResult;
    }
}