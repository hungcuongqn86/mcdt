<?php

/**
 * Class Extra_Ecs
 */
class Extra_Ecs
{
    /**
     * @param $arrRecordInfo
     * @return string
     */
    public function general_information($arrRecordInfo)
    {
        $strHTML = '<table cellspacing="0" cellpadding="0" border="0" align="center" id="table-data" class="list-table-data">
            <colgroup><col width="15%"><col width="15%"><col width="25%"><col width="30%"><col width="15%"></colgroup>
            <tbody>
            <tr class="header">
                <td align="center" class="">Mã hồ sơ</td>
                <td align="center" class="">Ngày nhận</td>
                <td align="center" class="">Tên tổ chức/ Cá nhân</td>
                <td align="center" class="">Địa chỉ</td>
                <td align="center" class="">Ngày hẹn</td>
            </tr>';
        foreach ($arrRecordInfo as $item) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $item['C_RECEIVED_RECORD_XML_DATA'];
            $objXmlData = new Zend_Config_Xml($xml, 'data_list');
            $arrInfo = $objXmlData->toArray();
            $arrInfoName = $arrInfo['ben_chuyen_nhuong'];
            $arrInfoName = json_decode($arrInfoName);
            //echo $arrInfoName[1]-> ben_chuyen_nhuong_obj_name_1;
            //var_dump($arrInfoName);
            if ($item['C_CATE'] = 'DAT_DAI_TNMT') {
                $strHTML .= '<tr>
                <td align="CENTER">' . $item['C_CODE'] . '</td>
                <td align="CENTER">' . $item['C_INPUT_DATE'] . '</td>
                <td align="LEFT" class="data">' . $arrInfoName[0]->ben_chuyen_nhuong_obj_name_0 . ',' . $arrInfoName[1]->ben_chuyen_nhuong_obj_name_1 . '</td>
                <td align="LEFT" class="data">' . $arrInfoName[0]->ben_chuyen_nhuong_obj_address_0 . '</td>
                <td align="CENTER" class="data">' . $item['C_APPOINTED_DATE'] . '</td>
            </tr>';
            } else {
                $strHTML .= '<tr>
                <td align="CENTER">' . $item['C_CODE'] . '</td>
                <td align="CENTER">' . $item['C_INPUT_DATE'] . '</td>
                <td align="LEFT" class="data">' . $arrInfo['registor_address'] . '</td>
                <td align="LEFT" class="data">' . $arrInfo['registor_address'] . '</td>
                <td align="CENTER" class="data">' . $item['C_APPOINTED_DATE'] . '</td>
            </tr>';
            }

        }
        $strHTML .= '</tbody></table>';
        return $strHTML;
    }

    /**
     * @param $arrConst
     * @param $arrRecord
     * @return string
     */
    public function generalhtmlinfo($arrConst, $arrRecord)
    {
        $objXml = new Extra_Xml();
        $ojbEfyLib = new Extra_Util();
        $objConfig = new Extra_Init();
        $spkrecord = $arrRecord['PK_RECORD'];
        $arrFile = $arrRecord['file'];
        $ResHtmlString = "<div class = 'large_title' style='padding-left:0px; text-align: left; float: left;'>" . $arrConst['_THONG_TIN_HS'] . "</div>";
        $ResHtmlString .= "<table class='table_detail_doc' border='1' width='98%'>";
        $ResHtmlString .= "<col width='30%'><col width='70%'>";
        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "    <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_TEN_TTHC'] . "</td>";
        $ResHtmlString .= "    <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_RECORDTYPE_NAME'] . "</td>";
        $ResHtmlString .= "</tr>";
        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "    <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_MA_HO_SO'] . "</td>";
        $ResHtmlString .= "    <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_CODE'] . "</td>";
        $ResHtmlString .= "</tr>";
        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "    <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_NGAY_TIEP_NHAN'] . "</td>";
        $ResHtmlString .= "    <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_RECEIVED_DATE'] . "</td>";
        $ResHtmlString .= "</tr>";
        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "    <td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_NGAY_HEN'] . "</td>";
        $ResHtmlString .= "    <td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_APPOINTED_DATE'] . "</td>";
        $ResHtmlString .= "</tr>";

        if ($arrRecord['C_POSITION_NAME']) {
            $ResHtmlString .= "<tr class='normal_label'>";
            $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_CB_THU_LY'] . "</td>";
            $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_POSITION_NAME'] . "</td>";
            $ResHtmlString .= "</tr>";
        }

        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_TRANG_THAI_XU_LY'] . "</td>";
        $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord['C_CURRENT_STATUS'] . "</td>";
        $ResHtmlString .= "</tr>";

        $arrRecord = $objXml->_convertXmlStringToArray($arrRecord['C_RECEIVED_RECORD_XML_DATA'], 'data_list');
        if ($arrRecord) {
            $JsonarrRecord = $arrRecord[0]['ben_chuyen_nhuong'];
            $sName = '';
            $sAddress = '';
            if ($JsonarrRecord) {
                $arrInfoName = json_decode(html_entity_decode($JsonarrRecord), true);
                foreach ($arrInfoName as $key => $item) {
                    $sName .= '<br>' . $item['ben_chuyen_nhuong_obj_name_' . $key];
                    $sAddress .= '<br>' . $item['ben_chuyen_nhuong_obj_address_0'];
                }
            } else {
                $sName = $arrRecord[0]['ten_nguoi_nop_hs'];
                $sAddress = $arrRecord[0]['dia_chi_nguoi_nop_hs'];
            }
        }
        $sListCode = $arrRecord[0]['tai_lieu_kt'];
        $arr_value = explode(",", $sListCode);
        $sValueList = '';
        for ($j = 0; $j < sizeof($arr_value); $j++) {
            $sValueObj = $arr_value[$j];
            $arrValueObj = explode("|#|", $sValueObj);
            $sValueList = $sValueList . $arrValueObj[0] . ',';
        }
        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_TEN_TO_CHUC_CA_NHAN'] . "</td>";
        $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sName . "</td>";
        $ResHtmlString .= "</tr>";

        $ResHtmlString .= "<tr class='normal_label'>";
        $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_DIA_CHI'] . "</td>";
        $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sAddress . "</td>";
        $ResHtmlString .= "</tr>";

        if ($arrRecord[0]['dien_thoai_nguoi_nop_hs']) {
            $ResHtmlString .= "<tr class='normal_label'>";
            $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_DIEN_THOAI'] . "</td>";
            $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord[0]['dien_thoai_nguoi_nop_hs'] . "</td>";
            $ResHtmlString .= "</tr>";
        }

        /*if ($arrRecord[0]['email_nk']) {
            $ResHtmlString .= "<tr class='normal_label'>";
            $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Email</td>";
            $ResHtmlString .= "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrRecord[0]['email_nk'] . "</td>";
            $ResHtmlString .= "</tr>";
        }*/
        $sOwnerCode = $sOwnerCode = $_SESSION['OWNER_CODE'];
        $arrTrainingLevel = self::getAllListObjectCode($sOwnerCode, $sValueList);
        $arrDoc = explode(',', $arrRecord[0]['tai_lieu_kt']);
        $sDoc = "<table class='table_detail_doc' border='1' style=' width:100%'>";
        $iDoc = sizeof($arrDoc);
        if (($iDoc > 0) && ($arrDoc['0'] != '')) {
            for ($index = 0; $index < $iDoc; $index++) {
                if ($arrTrainingLevel[$index]['C_NAME'] <> '') {
                    $arr_single_file = self::eCSFileGetSingle($spkrecord, $arrTrainingLevel[$index]['C_CODE']);
                    $sActionUrl = '';
                    $file_name = '';
                    $sfilestring = '';
                    if ($arr_single_file) {
                        $v_file = trim($arr_single_file[0]['C_FILE_NAME']);//echo $v_file;exit;
                        if ($v_file != '') {
                            $arrFilename = explode('!~!', $v_file);
                            $file_name = $arrFilename[1];
                            $file_id = explode("_", $arrFilename[0]);
                            //Get URL
                            $sActionUrl = $objConfig->_setAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sActionUrl)) {
                                $sActionUrl = $objConfig->_setDvcAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                            }
                        }
                        $sfilestring = "<a href='$sActionUrl' > - $file_name </a>";
                    }
                    $sDoc = $sDoc . "<tr class='normal_label'>";
                    $sDoc = $sDoc . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . ($index + 1) . '. ' . $arrTrainingLevel[$index]['C_NAME'] . $sfilestring . "</td>";
                    $sDoc = $sDoc . "</tr>";
                }
            }
        }
        //Hien thi thong tin Tai lieu khac
        if (trim($arrRecord[0]['tl_khac']) != "") {
            $sDoc = $sDoc . "<tr class='normal_label'>";
            $sDoc = $sDoc . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $ojbEfyLib->_breakLine(trim($arrRecord[0]['tl_khac'])) . "</td>";
            $sDoc = $sDoc . "</tr>";

        }
        $sDoc = $sDoc . "</table>";
        if (($iDoc > 0) && ($arrDoc['0'] != '')) {
            $ResHtmlString .= "<tr class='normal_label'>";
            $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>Tài liệu kèm theo</td>";
            $ResHtmlString .= "	<td class='normal_label' style = 'padding:1px;'>" . $sDoc . "</td>";
            $ResHtmlString .= "</tr>";
        }
        //File
        if ($arrFile) {
            $sfilestring = '';
            foreach ($arrFile as $key => $file) {
                $v_file = trim($file['C_FILE_NAME']);
                if ($v_file != '') {
                    $arrFilename = explode('!~!', $v_file);
                    $file_name = $arrFilename[1];
                    $file_id = explode("_", $arrFilename[0]);
                    //Get URL
                    $sActionUrl = $objConfig->_setAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sActionUrl)) {
                        $sActionUrl = $objConfig->_setDvcAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $v_file;
                    }
                    $sfilestring .= "<a style='line-height: 25px;' href='$sActionUrl' >$file_name </a><br>";
                }
            }
            $ResHtmlString .= "<tr class='normal_label'>";
            $ResHtmlString .= "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>File đính kèm</td>";
            $ResHtmlString .= "	<td class='normal_label' style = 'padding:10px;'>" . $sfilestring . "</td>";
            $ResHtmlString .= "</tr>";
        }
        $ResHtmlString .= "</table>";
        return $ResHtmlString;
    }


    public function DocSentAttachFile($arrFileList, $piCountFile, $piMaxNumberAttachFile = 10, $psHaveUpLoadFields = true, $size)
    {

        $psGotoUrlForDeleteFile = "javascript:delete_row(document.getElementsByName(\"tr_line_new\"),document.getElementsByName(\"chk_file_attach_new_id\"),document.getElementById(\"hdn_deleted_new_file_id_list\"));";
        $psGotoUrlForAddFile = "javascript:add_row(document.getElementsByName(\"tr_line_new\")," . $piMaxNumberAttachFile . ");";

        $strHTML = "<table width='75%' cellpadding='0' cellspacing='0'><col width = '6%'><col width = '94%'>";

        //Tao doi tuong thong tin config
        $objConfig = new Extra_Init();

        //ID File dinh kem
        if (($piCountFile > 0) && ($arrFileList != '')) {
            // Goi thu tuc xu ly khi xoa cac file da co
            $psGotoUrlForDeleteFile = $psGotoUrlForDeleteFile . "delete_row_exist(document.getElementsByName(\"tr_line_exist\"),document.getElementsByName(\"chk_file_attach_exist_id\"),\"" . $_SERVER['REQUEST_URI'] . "\");";
            for ($index = 0; $index < $piCountFile; $index++) {
                $sFileId = $arrFileList[$index]['PK_FILE'];
                $sFileName = $arrFileList[$index]['C_FILE_NAME'];
                // Tach ten file ra
                if (strpos($sFileName, "!~!") == 0) {
                    $file_name = $sFileName;
                } else {
                    $arrFilename = explode('!~!', $sFileName);
                    $file_name = $arrFilename[1];
                    $file_id = explode("_", $arrFilename[0]);
                }
                //Get URL
                $sActionUrl = $objConfig->_setAttachFileUrlPath() . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $sFileName;

                //
                $strHTML = $strHTML . "<tr id='tr_line_exist' name = 'tr_line_exist'><td colspan='2' class='normal_link'>";
                if ($psHaveUpLoadFields) {
                    $strHTML = $strHTML . "<input type='checkbox' name='chk_file_attach_exist_id' id = '' value='$sFileName'>";
                }
                $strHTML = $strHTML . '<a onclick="open_new_tab(\'' . $sActionUrl . '\')">' . $file_name . '</a></td></tr>';
            }
        }
        //Them moi
        if ($psHaveUpLoadFields) {
            //Vong lap hien thi cac file dinh kem se them vao van ban
            for ($index = 0; $index < $piMaxNumberAttachFile; $index++) {
                if ($index < 1) {
                    $v_str_show = "block";
                } else {
                    $v_str_show = "none";
                }
                $strHTML = $strHTML . "<tr name = 'tr_line_new' id='tr_line_new' style='display:$v_str_show'><td><input type='checkbox' name='chk_file_attach_new_id' id = 'chk_file_attach_new_id' value=$index></td>";
                $strHTML = $strHTML . "<td><input class='textbox' type='file' name='FileName$index' id = 'FileName$index' optional='true' size = '" . $size . "'></td></tr>";
            }
            $strHTML = $strHTML . "<tr align='center'><td colspan='2' align='center'><a onclick='$psGotoUrlForAddFile' class='small_link'>Th&#234;m file</a>&nbsp;|&nbsp;";
            $strHTML = $strHTML . "	<a onclick='$psGotoUrlForDeleteFile' class='small_link'>X&#243;a file</a></td></tr>";
        }
        $strHTML = $strHTML . "</table>";
        //echo htmlspecialchars($strHTML);//exit;
        return $strHTML;
    }

    /**
     * @return string
     */
    public function curPageURL()
    {
        $pageURL = 'http://';
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * @param $df_url
     * @return string|Zend_Session_Namespace
     */
    public function CheckLogin($df_url)
    {
        //Tao bien Zend_Session_Namespace
        $SesCheckLogin = '';
        if (!isset($_SESSION['varCheckLogin'])) {
            //Tao Zend_Session_Namespace
            Zend_Loader::loadClass('Zend_Session_Namespace');
            $SesCheckLogin = new Zend_Session_Namespace('varCheckLogin');
        }
        $objConfig = new Extra_Init();
        $substr_count = substr_count($df_url, $objConfig->_setSeachrecordresultUrlPath());

        if (($substr_count == 0) && ($df_url != $objConfig->_setUserLoginUrl()) && ((!isset($_SESSION['staff_id']) || is_null($_SESSION['staff_id']) || $_SESSION['staff_id'] == ''))) {
            //Kiem tra thong tin NSD
            ?>
            <script type="text/javascript">
                UrlRes = '<?php echo $objConfig->_setUserLoginUrl() ?>';
                window.location = UrlRes;
            </script><?php
        }
        if ((!isset($_SESSION['SesGetAllOwnerNotCurrentOwner'])) && (isset($_SESSION['SesGetAllOwner']))) {
            $arrOwner = array();
            $icount = sizeof($_SESSION['SesGetAllOwner']);
            $arrResult = $_SESSION['SesGetAllOwner'];
            for ($i = 0; $i < $icount; $i++) {
                if ($_SESSION['SesGetAllOwner'][$i]['code'] != $_SESSION['OWNER_CODE']) {
                    $arr1Owner = array("name" => $arrResult[$i]['name'], "code" => $arrResult[$i]['code'], "order" => $arrResult[$i]['order'], "address" => $arrResult[$i]['address'], "email" => $arrResult[$i]['email'], "phone" => $arrResult[$i]['phone']);
                    array_push($arrOwner, $arr1Owner);
                }
            }
            $_SESSION['SesGetAllOwnerNotCurrentOwner'] = $arrOwner;
        }
        //Tao SESSION luu thong tin tat ca cac TTHC cua nguoi dang nhap hien thoi
        if ((!isset($_SESSION['arr_all_record_type']) || is_null($_SESSION['arr_all_record_type'])) && (isset($_SESSION['staff_id']))) {
            $_SESSION['arr_all_record_type'] = Extra_Ecs::eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
        }
        //var_dump($_SESSION['arr_all_record_type']);//exit;
        return $SesCheckLogin;
    }

    /**
     * @param string $pGroupUser
     * @param string $sSessionName
     * @return mixed
     */
    public function docGetAllUnitLeader($pGroupUser = "", $sSessionName = "arr_all_staff")
    {
        $i = 0;
        foreach ($_SESSION[$sSessionName] as $staff) {
            if (Extra_Util::_listHaveElement($pGroupUser, $staff['position_group_code'], ",")) {
                $arrUnitLeader[$i] = $staff;
                $i++;
            }
        }
        return $arrUnitLeader;
    }

    /**
     * @param $arrRes
     * @param $ColumnIdRes
     * @param $ColumnTexRes
     * @param $TextRes
     * @param $hndRes
     * @param int $single
     * @param string $sColumName
     * @param int $editable
     * @return string
     */
    function doc_search_ajax($arrRes, $ColumnIdRes, $ColumnTexRes, $TextRes, $hndRes, $single = 1, $sColumName = "", $editable = 0)
    {
        // doc_search_ajax($arrLeader,"id","name","C_LEADER_LIST_3","hdn_leader_list_3",0,"position_code",0);
        $sWebsitePart = Extra_Init::_setWebSitePath();
        $sHtmlRes = '';
        $sHtmlRes = $sHtmlRes . ' <script type="text/javascript">  ';//
        $sHtmlNameId = '';
        $sHtmlNameId = $sHtmlNameId . '  var NameID' . $hndRes . ' = new Array(';//
        $sHtmlNameText = '';
        $sHtmlNameText = $sHtmlNameText . ' var NameText' . $hndRes . ' = new Array(';
        //Ghi Ma va ten ra mot mang
        foreach ($arrRes as $arrTemp) {
            $sTemp = "";
            if ($sColumName != "") {
                $sPositionCode = $arrTemp[$sColumName];
                if ($sPositionCode != "") {
                    $sTemp = $sPositionCode . " - ";
                }
            }
            $sHtmlNameId = $sHtmlNameId . '"' . $arrTemp[$ColumnIdRes] . '",';
            $sHtmlNameText = $sHtmlNameText . '"' . $sTemp . $arrTemp[$ColumnTexRes] . '",';
        }
        $sHtmlNameId = rtrim($sHtmlNameId, ',') . '); ';
        $sHtmlNameText = rtrim($sHtmlNameText, ',') . '); ';
        $sHtmlRes = $sHtmlRes . $sHtmlNameId . $sHtmlNameText . ' ';
        $sHtmlRes = $sHtmlRes . ' obj' . $hndRes . '= new actb(document.getElementById(\'' . $TextRes . '\'),NameText' . $hndRes . ',NameText' . $hndRes . ',\'FillProduct' . $hndRes . '(\',\'' . $single . '\',\'' . $editable . '\',\'' . $sWebsitePart . '\');';
        $sHtmlRes = $sHtmlRes . ' function FillProduct' . $hndRes . '(v_id){}';
        $sHtmlRes = $sHtmlRes . '</script>';
        return $sHtmlRes;
    }

    /**
     * @param $nameStrColor
     * @param $nameStrInput
     * @return string
     */
    public function searchStringColor($nameStrColor, $nameStrInput)
    {
        $i = 0;
        $j = 0;
        $arrSubject = "";
        $arrSubject = explode(" ", $nameStrInput);
        $arrSearch = explode(" ", $nameStrColor);
        for ($i = 0; $i < sizeof($arrSearch); $i++) {
            $nameStrOutput = "";
            for ($j = 0; $j < sizeof($arrSubject); $j++) {
                if (sizeof($arrSearch) > 1) {
                    $str = $arrSearch[$i];
                } else {
                    $str = $nameStrColor;
                }
                if (Extra_Util::Lower2Upper(trim($arrSubject[$j])) == Extra_Util::Lower2Upper(trim($str))) {
                    $strText = "<label style = 'background-color:#99FF99'>" . $arrSubject[$j] . "</label>";
                    $arrSubject[$j] = $strText;
                }
                $nameStrOutput .= $arrSubject[$j] . " ";
            }
        }
        return $nameStrOutput;
    }

    /*
	 * Nguoi sua: KHOINV
	 * MUC DICH: doi mau nhung tu tim kiem chua het 1 tu
	*/
    public function searchStringColor2($nameStrColor, $nameStrInput)
    {
        $i = 0;
        $j = 0;
        $nameStrOutput = "";
        $nameStrOutput2 = "";
        //$arrSubject = explode(" ",$nameStrInput);
        if (!is_null($nameStrColor) & $nameStrColor != '') {
            //mang chua in hoa
            $arrSubject = explode($nameStrColor, $nameStrInput);
            //chuyen ve chu hoa de tim kiem
            $sSeach = Extra_Util::Lower2Upper($nameStrColor);
            $sStr = Extra_Util::Lower2Upper($nameStrInput);
            $arrSearch = explode($sSeach, $sStr);
            for ($i = 0; $i < sizeof($arrSearch); $i++) {
                //neu chuoi tim kiem co trong mang can tim kiem
                if (sizeof($arrSearch) > 1) {
                    //lay chuoi can doi mau
                    $nameStrOutput .= substr($nameStrInput, strlen($nameStrOutput2), strlen($arrSearch[$i]));
                    $nameStrOutput2 .= substr($nameStrInput, strlen($nameStrOutput2), strlen($arrSearch[$i]));
                    if (strlen($nameStrInput) > strlen($nameStrOutput)) {
                        $sSeachColor = substr($nameStrInput, strlen($nameStrOutput), strlen($nameStrColor));
                        $strText = "<label style = 'background-color:#99FF99'>" . $sSeachColor . "</label>";
                        //$arrSubject[$j] = $strText;
                        $nameStrOutput .= $strText;
                        $nameStrOutput2 .= $sSeachColor;
                    }
                } else {
                    $nameStrOutput = $nameStrInput;
                }
            }
        } else {
            $nameStrOutput = $nameStrInput;
        }
        return $nameStrOutput;
    }

    /**
     * @param $nameStrColor
     * @param $nameStrInput
     * @return mixed
     */
    public function searchCharColor($nameStrColor, $nameStrInput)
    {
        $strText = "<label style = 'background-color:#99FF99'>" . $nameStrColor . "</label>";
        $nameStrOutput = str_replace(Extra_Util::Lower2Upper(trim($nameStrColor)), Extra_Util::Lower2Upper(trim($strText)), trim($nameStrInput));
        return $nameStrOutput;
    }

    /**
     * @param $sStaffIdList
     * @param string $delimitor
     * @return string
     */
    public function getNamePositionStaffByIdList($sStaffIdList, $delimitor = '!#~$|*')
    {
        $arrStaffId = explode(',', $sStaffIdList);
        $sNamePositionStaffList = "";
        for ($i = 0; $i < sizeof($arrStaffId); $i++) {
            $sName = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'], $arrStaffId[$i], 'name');
            $sPosition = Extra_Util::_getItemAttrById($_SESSION['arr_all_staff'], $arrStaffId[$i], 'position_code');
            $sNamePositionStaffList = $sNamePositionStaffList . $delimitor . $sPosition . ' - ' . $sName;
        }
        $sNamePositionStaffList = substr($sNamePositionStaffList, strlen($delimitor));
        return $sNamePositionStaffList;
    }

    /**
     * @param $sUnitIdList
     * @param string $sSession
     * @return string
     */
    public function getNameUnitByIdUnitList($sUnitIdList, $sSession = 'arr_all_unit')
    {
        $arrUnitId = explode(',', $sUnitIdList);
        $sNameUnitList = "";
        for ($i = 0; $i < sizeof($arrUnitId); $i++) {
            $sNameUnit = Extra_Util::_getItemAttrById($_SESSION[$sSession], $arrUnitId[$i], 'name');
            $sNameUnitList = $sNameUnitList . '!#~$|*' . $sNameUnit;
        }
        $sNameUnitList = substr($sNameUnitList, 6);
        return $sNameUnitList;
    }

    /**
     * @param string $sStaffNameList
     * @param string $delimitor
     * @return string
     */
    public function convertStaffNameToStaffId($sStaffNameList = "", $delimitor = ",")
    {
        $sStaffIdList = "";
        if (trim($sStaffNameList) != "") {
            //chuyen doi mang danh sach ten can bo ra mang mot chieu
            $arr_staff_name = explode(";", $sStaffNameList);
            for ($index = 0; $index < sizeof($arr_staff_name); $index++) {
                foreach ($_SESSION['arr_all_staff_keep'] as $staff) {
                    $sStaffPositionName = $staff['position_code'] . " - " . $staff['name'];
                    if (trim($sStaffPositionName) == trim($arr_staff_name[$index])) {
                        $sStaffIdList .= $staff['id'] . $delimitor;
                    }
                }
            }
            $sStaffIdList = substr($sStaffIdList, 0, strlen($sStaffIdList) - 1);
        }
        return $sStaffIdList;
    }

    /**
     * @param string $sUnitNameList
     * @return string
     */
    public function convertUnitNameListToUnitIdList($sUnitNameList = "")
    {
        $sUnitIdList = "";
        if (trim($sUnitNameList) != "") {
            //chuyen doi mang danh sach ten can bo ra mang mot chieu
            $arrUnitName = explode(";", $sUnitNameList);
            //var_dump($arrUnitName); exit;
            for ($index = 0; $index < sizeof($arrUnitName); $index++) {
                foreach ($_SESSION['arr_all_unit_keep'] as $unit) {
                    if (trim($unit['name']) == trim($arrUnitName[$index])) {
                        $sUnitIdList .= $unit['id'] . ";";
                    }
                }
            }
            $sUnitIdList = substr($sUnitIdList, 0, -1);
        }
        return $sUnitIdList;
    }

    function _get_item_attr_by_id($p_array, $p_id, $p_attr_name)
    {
        foreach ($p_array as $staff) {
            if (strcasecmp($staff['id'], $p_id) == 0) {
                return $staff[$p_attr_name];
            }
        }
        return "";
    }

    /**
     * @param $v_staff_id_list
     * @param string $v_option
     * @return string
     */
    function doc_get_all_unit_permission_form_staffIdList($v_staff_id_list, $v_option = 'unit')
    {
        $arr_staff_id = explode(',', $v_staff_id_list);
        $v_return_string = "";
        if ($v_option == 'unit') {
            for ($i = 0; $i < sizeof($arr_staff_id); $i++) {
                $v_return_string = $v_return_string . ',' . Extra_Ecs::_get_item_attr_by_id($_SESSION['arr_all_staff_keep'], $arr_staff_id[$i], 'unit_id');
            }
            $v_return_string = substr($v_return_string, 1);
        }
        return $v_return_string;
    }

    /**
     * @param $sOwnerCode
     * @param $sCode
     * @param string $optCache
     * @return array|mixed
     */
    public function getAllListObjectCode($sOwnerCode, $sCode, $optCache = "")
    {
        // Tao doi tuong xu ly du lieu
        $arrObject = array();
        $objConn = new  Extra_Db();
        $sql = "EfyLib_GetAllName_TLKT ";
        $sql = $sql . " '" . $sOwnerCode . "'";
        $sql = $sql . " ,'" . $sCode . "'";
        //echo $sql . '<br>';exit;
        try {
            $arrObject = $objConn->adodbQueryDataInNameMode($sql, $optCache);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrObject;
    }

    /**
     * @param $sOwnerCode
     * @param $sCode
     * @param string $optCache
     * @return mixed
     */
    public function getAllObjectbyListCode($sOwnerCode, $sCode, $optCache = "")
    {
        // Tao doi tuong xu ly du lieu
        $objConn = new  Extra_Db();
        $sql = "EfyLib_ListGetAllbyListtypeCode ";
        $sql = $sql . " '" . $sOwnerCode . "'";
        $sql = $sql . " ,'" . $sCode . "'";
        //echo $sql . '<br>';exit;
        try {
            $arrObject = $objConn->adodbQueryDataInNameMode($sql, 0);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrObject;
    }

    /**
     * Nguoi tao: KHOINV
     * Ngay tao: 22/04/2011
     * Y nghia:Lay danh sach ten tai lieu kem theo tu danh sach ma truyen vao
     * $sOwnerCode: Ma don vi
     * $sCode: Ma danh muc
     * $sList: danh sach ma tai lieu kt
     */
    public function getNameFromCode($sOwnerCode, $sCode, $sList)
    {
        // Tao doi tuong xu ly du lieu
        $objConn = new  Extra_Db();
        $sql = "EfyLib_NameFromCode ";
        $sql = $sql . " '" . $sOwnerCode . "'";
        $sql = $sql . " ,'" . $sCode . "'";
        $sql = $sql . " ,'" . $sList . "'";
        //echo $sql . '<br>';exit;
        try {
            $arrObject = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrObject;
    }

    /**
     * Creater: Phuongtt
     * Date:    29/10/2010
     * Des: Lay thong tin TTHC
     * @param unknown_type $sStaffId Id nguoi dang nhap hien thoi
     * @param unknown_type $sOwnerCode Ma don vi su dung
     * @param unknown_type $sClauseString Menh de dieu kien SQL
     */
    public function eCSRecordTypeGetAllByStaff($sStaffId, $sOwnerCode, $sClauseString = '')
    {
        $objConn = new  Extra_Db();
        $sql = "Exec eCS_RecordTypeGetAllByStaff ";
        $sql = $sql . "'" . $sStaffId . "'";
        $sql = $sql . ",'" . $sOwnerCode . "'";
        $sql = $sql . ",'" . $sClauseString . "'";
        //echo htmlspecialchars($sql);
        //echo $sql;exit;
        try {
            $arrRecordType = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrRecordType;
    }

    /**
     * Creater: Phuongtt
     * Date: 29/10/2010
     * Des: Kiem qua quyen tiep nhan cua nguoi su dung
     * @param unknown_type $sStaffId Id nguoi dang nhap hien thoi
     * @param unknown_type $arrRecordType Mang nay la ket qua cua ham eCSRecordTypeGetAllByStaff
     */
    public function eCSPermisstionReceiverForRecordType($sStaffId, $arrRecordType)
    {
        $isyes = 0;
        foreach ($arrRecordType as $recordType) {
            $strtemp = ',' . $recordType['C_RECEIVER_ID_LIST'] . ',';
            //echo stripos($strtemp, $sStaffId);
            if ($strtemp == $sStaffId || stripos($strtemp, $sStaffId)) {
                $isyes = 1;
                break;
            }
        }
        if ($isyes == 0) return false;
        else return true;
    }

    /**
     * Creater: Phuongtt
     * Date: 29/10/2010
     * Des: Kiem qua quyen thu ly cua nguoi su dung
     * @param unknown_type $sStaffId Id nguoi dang nhap hien thoi
     * @param unknown_type $arrRecordType Mang nay la ket qua cua ham eCSRecordTypeGetAllByStaff
     */
    public function eCSPermisstionHandlerForRecordType($sStaffId, $arrRecordType)
    {
        $isyes = 0;
        $sStaffId = ',' . $sStaffId . ',';
        foreach ($arrRecordType as $recordType) {
            $strtemp = ',' . $recordType['C_HANDLER_ID_LIST'] . ',';
            if (stripos($strtemp, $sStaffId) !== false) {
                $isyes = 1;
                break;
            }
        }
        if ($isyes == 0) return false;
        else return true;
    }

    /**
     * Creater: Phuongtt
     * Date: 29/10/2010
     * Des: Kiem qua quyen phe duyet cua nguoi su dung
     * @param unknown_type $sStaffId Id nguoi dang nhap hien thoi
     * @param unknown_type $arrRecordType Mang nay la ket qua cua ham eCSRecordTypeGetAllByStaff
     */
    public function eCSPermisstionApproveForRecordType($sStaffId, $sApproveLevel, $arrRecordType, $sRecordTypeId = "")
    {
        $isyes = 0;
        for ($i = 0; $i < sizeof($arrRecordType); $i++) {
            $arrLeaderId = explode(',', $arrRecordType[$i]['C_APPROVE_LEADER_ID_LIST']);
            $arrrolecode = explode(',', $arrRecordType[$i]['C_ROLES_CODE_LIST']);
            //
            if ($sRecordTypeId == "") {
                if (in_array($sStaffId, $arrLeaderId)) {
                    if ($sApproveLevel == '') {
                        return true;
                        break;
                    } else {
                        $sApproveLevelTemp = '';
                        for ($j = 0; $j < sizeof($arrLeaderId); $j++) {
                            if (($arrLeaderId[$j] == $sStaffId) && ($arrrolecode[$j] == $sApproveLevel)) {
                                $sApproveLevelTemp = $sApproveLevel;
                            }
                        }
                        if ($sApproveLevelTemp != '') {
                            return $sApproveLevelTemp;
                            break;
                        }
                    }
                }
            } else {
                if ($arrRecordType[$i]['PK_RECORDTYPE'] == $sRecordTypeId) {
                    for ($j = 0; $j < sizeof($arrLeaderId); $j++) {
                        if (($arrLeaderId[$j] == $sStaffId) && ($arrrolecode[$j] == $sApproveLevel)) {
                            return $sApproveLevel;
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $sRecordTypeId
     * @param $sRecordType
     * @param $iCurrentStaffId
     * @param $sReceiveDate
     * @param $sStatusList
     * @param $sDetailStatusCompare
     * @param $sRole
     * @param $sOrderClause
     * @param $sOwnerCode
     * @param $sfulltextsearch
     * @param $iPage
     * @param $iNumberRecordPerPage
     * @return array|Mang
     */
    public function eCSRecordGetAll($sRecordTypeId, $sRecordType, $iCurrentStaffId, $sReceiveDate, $sStatusList, $sDetailStatusCompare, $sRole, $sOrderClause, $sOwnerCode, $sfulltextsearch, $iPage, $iNumberRecordPerPage)
    {
        $objConn = new  Extra_Db();
        $arrResul = array();
        $sql = "Exec eCS_RecordGetAll ";
        $sql = $sql . "'" . $sRecordTypeId . "'";
        $sql = $sql . ",'" . $sRecordType . "'";
        $sql = $sql . ",'" . $iCurrentStaffId . "'";
        $sql = $sql . ",'" . $sReceiveDate . "'";
        $sql = $sql . ",'" . $sStatusList . "'";
        $sql = $sql . ",'" . $sDetailStatusCompare . "'";
        $sql = $sql . ",'" . $sRole . "'";
        $sql = $sql . ",'" . $sOrderClause . "'";
        $sql = $sql . ",'" . $sOwnerCode . "'";
        $sql = $sql . ",'" . $sfulltextsearch . "'";
        $sql = $sql . ",'" . $iPage . "'";
        $sql = $sql . ",'" . $iNumberRecordPerPage . "'";
        //echo $sql . '<br>'; //exit;
        try {
            $arrResul = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResul;
    }

    /**
     * @param $sRecordId
     * @param $sOwnerCode
     * @param null $sRecordTransitionId
     * @return Mang
     */
    public function eCSRecordGetSingle($sRecordId, $sOwnerCode, $sRecordTransitionId = null)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec [dbo].[eCS_RecordGetSingle] ";
        $sql .= "'" . $sRecordId . "'";
        $sql .= ",'" . $sOwnerCode . "'";
        $sql .= ",'" . $sRecordTransitionId . "'";
        //echo $sql . '<br>'; exit;
        try {
            $arrSendReceived = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrSendReceived;
    }

    /**
     * @param $sRecordId
     * @param $sOwnerCode
     * @param $stagList
     * @param null $sRecordTransitionId
     * @return Mang
     */
    public function eCSRecordGetSingleForPrint($sRecordId, $sOwnerCode, $stagList, $sRecordTransitionId = null)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec [dbo].[eCS_RecordGetSingleforPrint] ";
        $sql .= "'" . $sRecordId . "'";
        $sql .= ",'" . $sOwnerCode . "'";
        $sql .= ",'" . $stagList . "'";
        $sql .= ",'" . $sRecordTransitionId . "'";
        //echo $sql . '<br>'; exit;
        try {
            $arrSendReceived = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrSendReceived;
    }

    /**
     * @param $staffId
     * @param $roles
     * @param $arrRecordType
     * @param $arrInput
     * @return string
     */
    public function genEcsFilterFrom($staffId, $roles, $arrRecordType, $arrInput)
    {
        $shtml = '<div id = "filter">';
        $sselect = '<select id = "recordType" name = "recordType" style="width:90%;" onChange="actionUrl(\'' . $arrInput['pUrl'] . '\');" >';
        $staffIdTemp = $staffId;
        $arrRecordTypeTemp = array();
        if ($roles == 'TIEP_NHAN') {
            foreach ($arrRecordType as $recordType) {
                $strtemp = ',' . $recordType['C_RECEIVER_ID_LIST'] . ',';

                if (stripos($strtemp, $staffIdTemp) && (!isset($arrRecordTypeTemp[$recordType['PK_RECORDTYPE']]) || !$arrRecordTypeTemp[$recordType['PK_RECORDTYPE']])) {
                    $arrRecordTypeTemp[$recordType['PK_RECORDTYPE']] = 1;
                    if (stripos('@{' . $recordType['PK_RECORDTYPE'] . '}', $arrInput['RecordTypeId'])) $sCheck = "selected='selected'";
                    else $sCheck = "";
                    $sselect .= '<option value = "' . $recordType['PK_RECORDTYPE'] . '" ' . $sCheck . '  >' . $recordType['C_NAME'] . '</option>';
                }
            }
        } else if ($roles == 'THU_LY') {//var_dump($arrRecordType);
            foreach ($arrRecordType as $recordType) {
                $strtemp = ',' . $recordType['C_HANDLER_ID_LIST'] . ',';
                if (stripos($strtemp, $staffIdTemp) !== false && $arrRecordTypeTemp[$recordType['PK_RECORDTYPE']] != 1) {
                    $arrRecordTypeTemp[$recordType['PK_RECORDTYPE']] = 1;
                    if ($recordType['PK_RECORDTYPE'] == $arrInput['RecordTypeId'] || $recordType['PK_RECORDTYPE'] == '{' . $arrInput['RecordTypeId'] . '}') $sCheck = "selected='selected'";
                    else $sCheck = "";
                    $sselect .= '<option ' . $sCheck . ' value = "' . $recordType['PK_RECORDTYPE'] . '">' . $recordType['C_NAME'] . '</option>';
                }
            }
        } else if ($roles == 'PHE_DUYET') {
            for ($i = 0; $i < sizeof($arrRecordType); $i++) {
                $arrLeaderId = explode(',', $arrRecordType[$i]['C_APPROVE_LEADER_ID_LIST']);
                $arrrolecode = explode(',', $arrRecordType[$i]['C_ROLES_CODE_LIST']);
                for ($j = 0; $j < sizeof($arrLeaderId); $j++) {
                    if (($staffId == $arrLeaderId[$j] && ($arrrolecode[$j] == 'DUYET_CAP_MOT' || $arrrolecode[$j] == 'DUYET_CAP_HAI') || $arrrolecode[$j] == 'DUYET_CAP_BA')) {
                        if (stripos('@' . $arrRecordType[$i]['PK_RECORDTYPE'], $arrInput['RecordTypeId'])) $sCheck = "selected='selected'";
                        else $sCheck = "";
                        $sselect .= '<option ' . $sCheck . ' value = "' . $arrRecordType[$i]['PK_RECORDTYPE'] . '">' . $arrRecordType[$i]['C_NAME'] . '</option>';
                        break;
                    }
                }
            }
        } else if ($roles == 'THUE' || $roles == 'KHO_BAC') {
            for ($i = 0; $i < sizeof($arrRecordType); $i++) {
                if (stripos('@{' . $arrRecordType[$i]['PK_RECORDTYPE'] . '}', $arrInput['RecordTypeId'])) $sCheck = "selected='selected'";
                else $sCheck = "";
                $sselect .= '<option ' . $sCheck . ' value = "' . $arrRecordType[$i]['PK_RECORDTYPE'] . '">' . $arrRecordType[$i]['C_NAME'] . '</option>';
            }
        }
        $sselect .= '</select>';
        $shtml .= '<div>' . $sselect . '</div>';
        $shtml .= '<div style= "margin-top:2px;"><input type = "textbox" value = "' . $arrInput['fullTextSearch'] . '" name = "txtfullTextSearch" id = "txtfullTextSearch" style="width:80%;" /><input style="margin-left:2%;margin-bottom:0;" type="button" value="Tìm kiếm" class="add_large_button"  onclick="actionUrl(\'' . $arrInput['pUrl'] . '\');" /></div>';
        $shtml .= '</div>';
        return $shtml;
    }

    /**
     * @param $arrRecordType
     * @param $stitle
     * @param $sValue
     * @return string
     */
    public function genSelectBox($arrRecordType, $stitle, $sValue)
    {

        $sselect = '<select id = "recordType" name = "recordType" style="width:99%;" >';
        $sselect = $sselect . " <option value=''>" . $stitle . "</option>";
        if (sizeof($arrRecordType) > 0) {
            for ($i = 0; $i < sizeof($arrRecordType); $i++) {
                if ($arrRecordType[$i]['PK_RECORDTYPE'] == $sValue) {
                    $sCheck = "selected='selected'";
                } else $sCheck = "";
                $sselect .= '<option value = "' . $arrRecordType[$i]['PK_RECORDTYPE'] . '" ' . $sCheck . '  >' . $arrRecordType[$i]['C_NAME'] . '</option>';
            }
        }
        $sselect .= '</select>';
        $shtml = $sselect;
        return $shtml;
    }

    /**
     * @param $srecordtype
     * @return string
     */
    function generateRecordCode($srecordtype)
    {
        $objConn = new  Extra_Db();
        $v_inc_code_length = 5;//Do dai cua ma theo tung loai, tung nam cua ho so
        $v_fix_code = $_SESSION['OWNER_CODE'] . "." . $srecordtype . "." . date("y");
        $v_str_count = strlen($v_fix_code);
        $v_str_sql = " Select Max(SUBSTRING(C_CODE, $v_str_count+2, $v_inc_code_length)) MAX_CODE ";
        $v_str_sql = $v_str_sql . " From T_eCS_RECORD";
        $v_str_sql = $v_str_sql . " Where SUBSTRING(C_CODE,1,$v_str_count) = '$v_fix_code' And LEN(C_CODE)=($v_inc_code_length+$v_str_count+1)";
        try {
            $arr_all_data = $objConn->adodbExecSqlString($v_str_sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        $v_next_code = $arr_all_data['MAX_CODE'];
        if (is_null($v_next_code) || $v_next_code == "") {
            $v_next_code = 1;
            $v_next_code = str_repeat("0", $v_inc_code_length - strlen($v_next_code)) . $v_next_code;
        } else {
            $v_next_code = intval($v_next_code) + 1;
            $v_next_code = str_repeat("0", $v_inc_code_length - strlen($v_next_code)) . $v_next_code;
        }
        $v_str_ret = $v_fix_code . "." . $v_next_code;
        return $v_str_ret;
    }

    /**
     * @param $RecordTypeId
     * @param $arrRecordType
     * @return array
     */
    function getinforRecordType($RecordTypeId, $arrRecordType)
    {
        $arrResult = array();
        foreach ($arrRecordType as $recordType) {
            if ($RecordTypeId != '') {
                if (stripos('@' . $recordType['PK_RECORDTYPE'], $RecordTypeId)) {
                    $arrResult = $recordType;
                    break;
                }
            }
        }
        return $arrResult;
    }

    /**
     * @param $sRecordPk
     * @param $iFkUnit
     * @param $sOwnerCode
     * @param string $sRecordTransitionPk
     * @return string
     */
    public function eCSRecordBasicGetSingle($sRecordPk, $iFkUnit, $sOwnerCode, $sRecordTransitionPk = '')
    {
        // Tao doi tuong xu ly du lieu
        $objConn = new  Extra_Db();
        //Tao duoi tuong trong lop dung chung
        $objLib = new Extra_Util();
        $objRecordFunction = new Extra_Ecs();
        //Lay cac gia tri const
        $ojbEfyInitConfig = new Extra_Init();
        $arrConst = $ojbEfyInitConfig->_setProjectPublicConst();
        try {
            // Chuoi SQL
            $sql = "Exec eCS_RecordBasicGetSingle ";
            $sql .= "'" . $sRecordPk . "'";
            $sql .= ",'" . $iFkUnit . "'";
            $sql .= ",'" . $sOwnerCode . "'";
            $sql .= ",'" . $sRecordTransitionPk . "'";
            //echo $sql;
            // Truy van CSDL
            $arrTemp = $objConn->adodbExecSqlString($sql);
            //var_dump($arrTemp);
            $sHandleStaff = $arrTemp['C_HANDLE_POSITION_NAME'];
            if (trim($sHandleStaff) != '') $sHandleStaff = $sHandleStaff . ', ';
            // In ra ket qua
            $ResHtmlString = "<div class = 'large_title' style='padding-left: 1px; text-align: left; float: left;'>" . $arrConst['_THONG_TIN_HS'] . "</div>";
            $ResHtmlString = $ResHtmlString . "<table class='table_detail_doc' border='1' width='98%'>";
            $ResHtmlString = $ResHtmlString . "<col width='25%'><col width='75%'>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_TEN_TTHC'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrTemp['C_NAME'] . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_MA_HO_SO'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrTemp['C_CODE'] . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . 'T&#234;n ng&#432;&#7901;i khai' . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrTemp['registor_name'] . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . '&#272;&#7883;a ch&#7881; ng&#432;&#7901;i khai' . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrTemp['registor_address'] . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_NGAY_TIEP_NHAN'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . Extra_Util::_yyyymmddToDDmmyyyyhhmm($arrTemp['C_RECEIVED_DATE']) . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_NGAY_HEN'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . Extra_Util::_yyyymmddToDDmmyyyyhhmm($arrTemp['C_APPOINTED_DATE']) . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_NOI_THU_LY'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $sHandleStaff . $objRecordFunction->getNameUnitByIdUnitList($iFkUnit) . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "<tr class='normal_label'>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'HEIGHT: 18pt;'align='left'>" . $arrConst['_TRANG_THAI_XU_LY'] . "</td>";
            $ResHtmlString = $ResHtmlString . "	<td class='normal_label' style = 'padding-left:10px;HEIGHT: 18pt;'>" . $arrTemp['C_CURRENT_STATUS'] . "</td>";
            $ResHtmlString = $ResHtmlString . "</tr>";
            $ResHtmlString = $ResHtmlString . "</table>";
            // Tra lai gia tri
            return $ResHtmlString;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * @param $sStaffIdList
     * @param $arrAllStaff
     * @return string
     */
    function GetUnitIdListFromStaffIdList($sStaffIdList, $arrAllStaff)
    {
        $arrStaffId = explode(',', $sStaffIdList);
        $sUnitIdList = '';
        foreach ($arrStaffId as $sStaffId) {
            foreach ($arrAllStaff As $staff)
                //echo stripos( $staff['id'],$sStaffId );exit;
                if (stripos('@' . $staff['id'], $sStaffId)) {
                    $sUnitIdList .= $staff['unit_id'] . ',';
                    break;
                }
        }
        return substr($sUnitIdList, 0, -1);
    }

    /**
     * @param $sStaffId
     * @param $arrRecordType
     * @param $sRecordTypeId
     * @return string
     */
    function eCSGetPermisstionApproveForRecordType2($sStaffId, $arrRecordType, $sRecordTypeId)
    {
        //$sRecordTypeId = '{'.$sRecordTypeId.'}';
        //echo 'die roi'.$sRecordTypeId;
        for ($j = 0; $j < sizeof($arrRecordType); $j++) {
            //echo "loi roi..........".$arrRecordType[$j]['PK_RECORDTYPE'];
            if ($arrRecordType[$j]['PK_RECORDTYPE'] == $sRecordTypeId) {
                $arrTemp = $arrRecordType[$j];
                //var_dump($arrTemp);
                $arrApproveLeaderIdList = explode(',', $arrTemp['C_APPROVE_LEADER_ID_LIST']);
                //var_dump($arrApproveLeaderIdList);echo "<br>";
                $arrrolecode = explode(',', $arrTemp['C_ROLES_CODE_LIST']);
                //var_dump($arrrolecode);
                for ($i = 0; $i < sizeof($arrApproveLeaderIdList); $i++) {
                    if ($sStaffId == $arrApproveLeaderIdList[$i]) {
                        return $arrrolecode[$i];
                    }
                }
            }
        }
        return '';
    }

    /**
     * @param $sRecordIdList
     * @return mixed|null
     */
    public function eCSGetInfoRecordFromListId($sRecordIdList)
    {
        $objConn = new  Extra_Db();
        $arrResult = null;
        $sql = "Exec eCS_GetInfoRecordFromListId ";
        $sql .= "'" . $sRecordIdList . "'";
        //echo $sql; exit;
        try {
            $arrResult = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResult;
    }

    /**
     * @param $arrValue
     * @param $sColId
     * @param $sColName
     * @param $svalue
     * @return mixed
     */
    public function getValueInArray($arrValue, $sColId, $sColName, $svalue)
    {
        foreach ($arrValue as $valueSet) {
            if ($valueSet[$sColId] == $svalue)
                return $valueSet[$sColName];
        }
    }

    /**
     * @param $sName
     * @param $sCode
     * @param $arrList
     * @return bool
     */
    public function testElementInArray($sName, $sCode, $arrList)
    {
        foreach ($arrList as $arrTemp) {
            if ($sCode != '') {
                if ($arrTemp[$sCode] == $sName) {
                    return true;
                }
            } else {
                if ($arrTemp == $sName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $sPathXmlFile
     * @param $sParrentTagName
     * @param $TagName
     * @param $sPathTemplateFile
     * @param string $sXmlData
     * @param array $arrRecord
     * @param $sPathTemplateFile
     * @param $sTemplateFile
     * @param $sOwnerCode
     * @return mixed|string
     * @throws Zend_Exception
     */
    public function ecs_PrintReceipt($sPathXmlFile, $sParrentTagName, $TagName, $sPathTemplateFile, $sXmlData = '', $arrRecord = array(), $sPathTemplateFile, $sTemplateFile, $sOwnerCode)
    {
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile, $sParrentTagName);
        //Tao doi tuong xu ly XML
        $objXml = new Extra_Xml();
        $objQLDTFun = new Extra_Ecs();
        $objLib = new Extra_Util();
        $sXmlData = '<?xml version="1.0" encoding="UTF-8"?>' . $sXmlData;
        $sContentFile = '';
        $userIdentity = Extra_Account::getInstance()->getIdentity();
        $ownerName = $userIdentity->OWNER_NAME;
        if (isset($ojbConfigXml->$TagName)) {
            $TagElements = $ojbConfigXml->$TagName->toArray();                                //Chuyen doi cac phan tu trong .xml cua the $TagName ra mang
            if (is_array($TagElements)) {
                //Noi dung file dinh kem
                if (file_exists($sPathTemplateFile . $sTemplateFile)) {
                    $sContentFile = file_get_contents($sPathTemplateFile . $sTemplateFile, FILE_BINARY);
                } else {
                    $sContentFile = file_get_contents('./templates/other/' . $sTemplateFile, FILE_BINARY);
                }

                //Duyet cac phan tu cua
                foreach ($TagElements as $elements => $arrElement) {
                    $sFromXmlData = $arrElement["from_xml_data"];
                    isset($arrElement["data_format"]) ? $sDataFormat = $arrElement["data_format"] : $sDataFormat = '';
                    $sFindString = $arrElement["find_string"];
                    $sFieldName = $arrElement["field_name"];
                    isset($arrElement["xml_tag_in_db"]) ? $sXmlTagInDb = $arrElement["xml_tag_in_db"] : $sXmlTagInDb = '';
                    $sValue = '';
                    if ($sFromXmlData == 'true') {
                        if ($sXmlData != '') {
                            $sValue = trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", $sXmlTagInDb));
                        }
                    } else {//Lay du lieu tu cot
                        isset($arrRecord[$sFieldName]) ? $sValue = $arrRecord[$sFieldName] : $sValue = '';
                    }
                    //
                    if ($sDataFormat == "breakline") {
                        $sValue = $objLib->_breakLine($sValue);
                    }
                    //Kieu date
                    if ($sFromXmlData != 'true' && $sDataFormat == 'isdate') {
                        $sValue = $objLib->_yyyymmddToDDmmyyyy($sValue);
                    }
                    if ($sXmlTagInDb == 'tai_lieu_kt') {
                        $sVal = "";
                        $j = 1;
                        if ($sValue != '') {
                            $arrList = $objQLDTFun->getNameFromCode($sOwnerCode, $arrRecord['C_KEY'] . "_TLKT", $sValue);
                            for ($i = 0; $i < sizeof($arrList); $i++) {
                                $sVal = $sVal . $j . '. ' . $arrList[$i]['C_NAME'] . '<br>';
                                $j++;
                            }
                            $sValue = $sVal;
                            //Lay file dinh kem khac co lien quan
                            $sValue = $sValue . $objLib->_breakLine(trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", 'tl_khac')));
                        }
                    }
                    //Kieu In giay phep dang ngay dd thang mm nam yyyy
                    if ($sDataFormat == 'isdatelicense') {
                        if ($sValue <> '') {
                            $sValue = $objLib->_ddmmyyyyToYYyymmdd($sValue);
                            $sValue = "ng&#224;y " . date("d", strtotime($sValue)) . " th&#225;ng " . date("m", strtotime($sValue)) . " n&#259;m " . date("Y", strtotime($sValue));
                        } else {
                            $sValue = "ng&#224;y...th&#225;ng...n&#259;m";
                        }
                    }
                    //Kieu In giay phep dang ngay dd thang mm nam yyyy
                    if ($sDataFormat == 'iscate') {
                        if ($sValue <> '') {
                            $sValue = $this->getAllListObjectCode($_SESSION['OWNER_NAME'], $sValue);
                            $sValue = $sValue[0]['C_NAME'];
                        }
                    }
                    //Thay the ten don vi su dung

                    $sContentFile = str_replace("#UNIT_NAME#", $ownerName, $sContentFile);
                    //Thay the gia tri
                    $sContentFile = str_replace($sFindString, $sValue, $sContentFile);
                }
            }
        }
        //Thay the that ngu ngay thang nam
        $v_report_date = "Ng&#224;y " . date("d") . " th&#225;ng " . date("m") . " n&#259;m " . date("Y");
        $sContentFile = str_replace("#CURRENT_DATE#", $v_report_date, $sContentFile);
        return $sContentFile;
    }

    /**
     * @param $sPathXmlFile
     * @param $sParrentTagName
     * @param $TagName
     * @param $sPathTemplateFile
     * @param string $sXmlData
     * @param array $arrRecord
     * @param $sPathTemplateFile
     * @param $sTemplateFile
     * @param $sOwnerCode
     * @return string
     */
    public function ecs_printTempDoc($sPathXmlFile, $sParrentTagName, $TagName, $sPathTemplateFile, $sXmlData = '', $arrRecord = array(), $sPathTemplateFile, $sTemplateFile, $sOwnerCode)
    {
        $objConfig = new Extra_Init();
        //Tao doi tuong
        $ojbConfigXml = new Zend_Config_Xml($sPathXmlFile, $sParrentTagName);
        $objXml = new Extra_Xml();
        $objQLDTFun = new Extra_Ecs();
        $objLib = new Extra_Util();
        $dirTemplate = $sPathTemplateFile . $sTemplateFile;
        $phpdocx = new Extra_Word($dirTemplate);
        $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
        $sXmlData = '<?xml version="1.0" encoding="UTF-8"?>' . $sXmlData;

        if (isset($ojbConfigXml->$TagName)) {
            $TagElements = $ojbConfigXml->$TagName->toArray();
            if (is_array($TagElements)) {
                //Duyet cac phan tu cua
                foreach ($TagElements as $elements => $arrElement) {
                    //Bien xac dinh co phai lay du lieu tu xau XML luu trong DB khong?
                    if (isset($arrElement["from_xml_data"])) {
                        $sFromXmlData = $arrElement["from_xml_data"];
                    } else {
                        $sFromXmlData = '';
                    }
                    //Dinh dang kieu du lieu
                    if (isset($arrElement["data_format"])) {
                        $sDataFormat = $arrElement["data_format"];
                    } else {
                        $sDataFormat = '';
                    }
                    //Tim au can thay the
                    if (isset($arrElement["find_string"])) {
                        $sFindString = $arrElement["find_string"];
                    } else {
                        $sFindString = '';
                    }
                    //Ten cot luu thong tin lay du lieu thay the
                    if (isset($arrElement["field_name"])) {
                        $sFieldName = $arrElement["field_name"];
                    } else {
                        $sFieldName = '';
                    }
                    //Ten the luu du lieu trong xau XML thay the file temp
                    if (isset($arrElement["xml_tag_in_db"])) {
                        $sXmlTagInDb = $arrElement["xml_tag_in_db"];
                    } else {
                        $sXmlTagInDb = '';
                    }
                    //--------------Bat dau thay the du lieu---------------------------------
                    if ($sFromXmlData == 'true' && $sXmlData != '') {
                        $sValue = trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", $sXmlTagInDb));
                    } else {//Lay du lieu tu cot
                        $sValue = (isset($arrRecord[0][$sFieldName]) ? trim($arrRecord[0][$sFieldName]) : '');
                    }
                    // ky tu xuong dong
                    /*                    if ($sDataFormat == "breakline"){
                                            $sValue = $objLib->_breakLine($sValue);
                                        }*/
                    //Kieu getdate
                    /*                    if ($sDataFormat == "getdate"){
                                            $sValue = "ngày ".date("d")." tháng ".date("m")." năm ".date("Y");
                                        }*/
                    //Kieu selectbox
                    if ($sFromXmlData == 'true' && $sDataFormat == 'selectbox') {
                        $list_name = '';
                        if (isset($arrElement['list_name'])) {
                            $list_name = $arrElement['list_name'];
                        }
                        if (!empty($list_name) && $list_name != '') {
                            $arrList = $objQLDTFun->getNameFromCode($sOwnerCode, $list_name, $sValue);
                            if (isset($arrList['0']['C_NAME'])) {
                                $sValue = $arrList['0']['C_NAME'];
                            }
                        }
                    }
                    /*                    //Ngay kieu chu
                                        if ($sDataFormat == "isdateText" && $sValue !=''){
                                            $arr = explode('/', $sValue);
                                            $sValue = 'ngày '.$arr['0'].' tháng '.$arr['1'].' năm '.$arr['2'];
                                        }
                                        //Ngay kieu chu hoa
                                        if ($sDataFormat == "isdateTextH" && $sValue !=''){
                                            $arr = explode('/', $sValue);
                                            $sValue = 'Ngày '.$arr['0'].' tháng '.$arr['1'].' năm '.$arr['2'];
                                        }*/
                    //Kieu In giay phep dang ngay dd thang mm nam yyyy
                    /*                    if ($sFromXmlData == 'true'&& $sDataFormat == 'isdatelicense'){
                                            if($sValue<>''){
                                                $sValue = $objLib->_ddmmyyyyToYYyymmdd($sValue);
                                                $sValue = "ngày ".date("d", strtotime($sValue))." tháng ".date("m", strtotime($sValue))." năm ".date("Y", strtotime($sValue));
                                            }
                                        }*/
                    if ($sDataFormat == "formfieldata") {
                        $sBlockName = $arrElement["sblockname"];
                        $sfind_string_list = $arrElement["find_string_list"];
                        $arrfind_string = explode(',', $sfind_string_list);
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $arrDocx = array();
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        if ($arrValue) {
                            foreach ($arrValue as $index => $value) {
                                $arrTemp = array();
                                for ($i = 0; $i < sizeof($arrfind_string); $i++) {
                                    $key = '#' . $arrfind_string[$i] . '#';
                                    $objkey = $sXmlTagInDb . '_' . $arrfield[$i] . '_' . $index;
                                    //echo $objkey;
                                    if (property_exists($value, $objkey)) {
                                        $arrTemp[$key] = $value->$objkey;
                                    } else {
                                        $arrTemp[$key] = '';
                                    }
                                }
                                array_push($arrDocx, $arrTemp);
                            }
                        }
                        //var_dump($arrDocx);exit;
                        $phpdocx->assignBlock($sBlockName, $arrDocx);
                    }

                    if ($sDataFormat == "formfieldatatolist") {
                        $sfind_string = $arrElement["find_string_list"];
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        $str_value = '';
                        if ($arrValue) {
                            foreach ($arrValue as $index => $value) {
                                $itemstr = '';
                                foreach ($arrfield as $field) {
                                    $taxkey = $sXmlTagInDb . '_' . $field . '_' . $index;
                                    $stempval = $value->$taxkey;
                                    if ($stempval != '') {
                                        $itemstr .= ' ' . $stempval;
                                    }
                                }
                                if ($str_value == '') {
                                    $str_value .= $itemstr;
                                } else {
                                    $str_value .= ', ' . $itemstr;
                                }
                            }
                        }
                        //var_dump($arrDocx);exit;
                        $phpdocx->assign($sfind_string, $str_value);
                    }

                    if ($sDataFormat == "formfieldatatax") {
                        $sfind_string_list = $arrElement["find_string_list"];
                        $arrfind_string = explode(',', $sfind_string_list);
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $tax_tag = $arrElement["tax_tag"];
                        $tax_prefix = $arrElement["tax_prefix"];

                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        $objItem = array();
                        $rowindex = 0;
                        if ($arrValue) {
                            $objItem = $arrValue[0];
                            foreach ($arrValue as $index => $value) {
                                $taxkey = $sXmlTagInDb . '_' . $tax_tag . '_' . $index;
                                if (property_exists($value, $taxkey)) {
                                    if ($value->$taxkey != '') {
                                        $objItem = $value;
                                        $rowindex = $index;
                                        break;
                                    }
                                }
                            }
                        }

                        for ($i = 0; $i < sizeof($arrfind_string); $i++) {
                            $key = '#' . $arrfind_string[$i] . '#';
                            $objkey = $sXmlTagInDb . '_' . $arrfield[$i] . '_' . $rowindex;
                            if (property_exists($objItem, $objkey)) {
                                //echo $key, $objItem->$objkey;
                                $phpdocx->assign($key, $objItem->$objkey);
                            } else {
                                $phpdocx->assign($key, '');
                            }
                        }

                        $taxkey = $sXmlTagInDb . '_' . $tax_tag . '_' . $rowindex;
                        if (property_exists($objItem, $taxkey)) {
                            $taxcode = $objItem->$taxkey;
                            if ($taxcode != '') {
                                for ($i = 0; $i < strlen($taxcode); $i++) {
                                    $phpdocx->assign('#' . $tax_prefix . $i . '#', $taxcode[$i]);
                                }
                            } else {
                                for ($i = 0; $i < 30; $i++) {
                                    $phpdocx->assign('#' . $tax_prefix . $i . '#', '');
                                }
                            }
                        } else {
                            for ($i = 0; $i < 30; $i++) {
                                $phpdocx->assign('#' . $tax_prefix . $i . '#', '');
                            }
                        }
                    }

                    //Kieu du lieu muc dich su dung dat
                    if ($sDataFormat == "datatable") {
                        $sBlockName = $arrElement["sblockname"];
                        $sfind_string_list = $arrElement["find_string_list"];
                        $arrfind_string = explode(',', $sfind_string_list);
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $arrDocx = array();
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        if ($arrValue) {
                            foreach ($arrValue as $key => $value) {
                                $arrTemp = array();
                                for ($i = 0; $i < sizeof($arrfind_string); $i++) {
                                    $key = '#' . $arrfind_string[$i] . '#';
                                    $arrTemp[$key] = $value->$arrfield[$i];
                                }
                                array_push($arrDocx, $arrTemp);
                            }
                        }
                        $phpdocx->assignBlock($sBlockName, $arrDocx);
                    }

                    if ($sDataFormat == "tabletostring") {
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $snote_list = $arrElement["note_list"];
                        $arrnote = explode('#', $snote_list);
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        $sValue = '';
                        if ($arrValue) {
                            foreach ($arrValue as $key => $value) {
                                foreach ($arrfield as $index => $field) {
                                    $stempval = $value->$field;
                                    if ($stempval != '') {
                                        $sValue .= $stempval . $arrnote[$index];
                                    }
                                }
                                $sValue .= ', ';
                            }
                        }
                        if (($sValue != '') && (strlen($sValue) > 2)) {
                            $sValue = substr($sValue, 0, -2);
                        }
                    }

                    if ($sDataFormat == "tabletostringlist") {
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $snote_list = $arrElement["note_list"];
                        $arrnote = explode('#', $snote_list);
                        $sValue = htmlspecialchars_decode($sValue);
                        $arrValue = json_decode($sValue);
                        $sValue = '';
                        if ($arrValue) {
                            foreach ($arrValue as $key => $value) {
                                foreach ($arrfield as $index => $field) {
                                    $stempval = $value->$field;
                                    if ($stempval != '') {
                                        $sValue .= $stempval . $arrnote[$index];
                                    }
                                }
                                $sValue .= ' ';
                                $sValue = strstr($sValue, ' ');
                            }
                        }
                        if (($sValue != '') && (strlen($sValue) > 2)) {
                            $sValue = substr($sValue, 0, -2);
                        }
                    }

                    if ($sDataFormat == "list_info") {
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $slable_list = $arrElement["lable_list"];
                        $arrlable = explode('#', $slable_list);
                        $sValue = '';
                        foreach ($arrfield as $key => $field) {
                            $stempval = trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", $field));
                            if ($stempval != '') {
                                $sValue .= $arrlable[$key] . ': ' . trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", $field)) . ', ';
                            }
                        }
                        if (($sValue != '') && (strlen($sValue) > 2)) {
                            $sValue = substr($sValue, 0, -2);
                        }
                    }
                    if ($sDataFormat == "if_else") {
                        $sfield_list = $arrElement["field_list"];
                        $arrfield = explode(',', $sfield_list);
                        $sValue = '';
                        foreach ($arrfield as $key => $field) {
                            $stempval = trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", $field));
                            if ($stempval != '') {
                                $sValue .= $stempval;
                                break;
                            }
                        }
                    }

                    if ($sXmlTagInDb == 'tai_lieu_kt') {
                        $sVal = "";
                        $j = 1;
                        if ($sValue != '') {
                            $arrList = $objQLDTFun->getNameFromCode($sOwnerCode, $arrRecord['C_KEY'] . "_TLKT", $sValue);
                            for ($i = 0; $i < sizeof($arrList); $i++) {
                                $sVal = $sVal . $arrList[$i]['C_NAME'] . '; ';
                                $j++;
                            }
                            $sValue = $sVal;
                            //Lay file dinh kem khac co lien quan
                            $sValue = $sValue . trim($objXml->_xmlGetXmlTagValue($sXmlData, "data_list", 'tl_khac'));
                        }
                    }

                    //Kieu fix du lieu
                    /*                    if ($sDataFormat == "FixValue"){
                                            $sValue 		= $arrElement["data_value"];
                                        }*/
                    //Thay the gia tri
                    /*                    $phpdocx->assign("#ngay#",date('d'));
                                        $phpdocx->assign("#thang#",date('m'));
                                        $phpdocx->assign("#nam#",date('Y'));*/
                    if ($sFindString != '') {
                        $phpdocx->assign($sFindString, $sValue);
                    }
                }
            }
        }
        $arr = explode('.', $sTemplateFile);
        $sFileExport = $arr[0];
        $v_export_filename = $sFileExport . '_' . date('dmy') . date('His') . '.doc';
        $dir_export_file = $_SERVER['DOCUMENT_ROOT'] . $objConfig->_setWebSitePath() . 'public/export/' . $v_export_filename;
        $phpdocx->save($dir_export_file);
        return $v_export_filename;
    }

    /**
     * @param $psXmlStringInFile
     * @param $arrReportCol
     * @param $arrResult
     * @param $v_colume_name_of_xml_string
     * @param $sFilterXmlString
     * @param $v_exporttype
     * @return string
     */
    public function _exportreport($psXmlStringInFile, $arrReportCol, $arrResult, $v_colume_name_of_xml_string, $sFilterXmlString, $v_exporttype)
    {
        $sHTML_string = '';
        $objXmlLib = new Extra_Xml();
        $objEfyLib = new Extra_Util();
        $objConfig = new Extra_Init();
        //Duyet mang du lieu và xuat ma html
        $v_count_row = sizeof($arrResult);
        $v_count_col = sizeof($arrReportCol);
        //var_dump($arrReportCol);exit;
        $iInc = 0;
        $iIngroup = 0;
        $sHTMLData = '#row_conten#';
        //Lay mang tong so
        $arrSumCol = array();
        //Kiem tra xem co nhom du lieu khong
        $sGroupCode = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_sql", "group_by");
        if ($sGroupCode <> '') {
            $sGroupName = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_sql", "group_name");
            //Danh chi muc cho nhom
            $sgroup_identity = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_sql", "group_identity");
            //Dat lai stt khi chuyen nhom
            $sreset_identity = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_sql", "reset_identity");
            $sGroupValue = '';
        }
        //So cot khong thuc hien tinh toan
        $iCountColTitleSum = 0;
        $sRowSumHtml = '';
        for ($row_index = 0; $row_index < $v_count_row; $row_index++) {
            $v_received_record_xml_data = '<?xml version="1.0" encoding="UTF-8"?>' . $arrResult[$row_index][$v_colume_name_of_xml_string];
            $sHTML_string_row = '';
            if ($sGroupCode) {
                if ($sGroupValue != $arrResult[$row_index][$sGroupCode]) {
                    $iIngroup++;
                    if ($sreset_identity) {
                        $iInc = 0;
                    }
                    //echo $this->romanic_number($iIngroup);exit;
                    $sHTML_string_row = '<tr class="data"><td class="data" align="center"><b>' . $this->romanic_number($iIngroup) . '</b></td><td class="data" align="left" colspan="' . ($v_count_col - 1) . '"><b><i>' . $arrResult[$row_index][$sGroupName] . '</i></b></td></tr>';
                    $sGroupValue = $arrResult[$row_index][$sGroupCode];
                }
            }
            $sHTML_string_row = $sHTML_string_row . '<tr class="data" >#row_conten#';
            for ($col_index = 0; $col_index < $v_count_col; $col_index++) {
                $sType = $arrReportCol[$col_index]["C_FORMAT"];
                $sAlign = $arrReportCol[$col_index]["C_ALIGN"];
                $sCaculate = $arrReportCol[$col_index]["C_CALCULATE"];
                if (($row_index == 0) && ($sRowSumHtml == '')) {
                    if ($sCaculate != '') {
                        $sRowSumHtml = '<tr class="data" ><td class="data" align="center" colspan="' . $iCountColTitleSum . '"><b>Tổng</b></td>';
                    } else {
                        $iCountColTitleSum = $iCountColTitleSum + 1;
                    }
                }
                if ($sType <> 'identity') {
                    $xmlData = $arrReportCol[$col_index]["C_DATA_SOURCE"];
                    $sColumnName = $arrReportCol[$col_index]["C_COL_TAB_NAME"];
                    $sFunction = $arrReportCol[$col_index]["C_FUN_NAME"];
                    if ($xmlData == 'xml_data') {
                        $sValue = $objXmlLib->_xmlGetXmlTagValue($v_received_record_xml_data, "data_list", $sColumnName);
                    } else {
                        $sValue = $arrResult[$row_index][$sColumnName];
                    }
                    $sHTMLRowData = '<td class="data" style="padding:0cm 2px 0cm 2px;font-size:11.0pt" align="' . $sAlign . '">' . self::_generatValue($sValue, $sType, $sFunction) . '&nbsp;</td>#row_conten#';
                    $v_poss = strrpos($sHTML_string_row, '#row_conten#');
                    $sHTML_string_row = substr_replace($sHTML_string_row, $sHTMLRowData, $v_poss, 12);
                    if ($sCaculate == 'sum') {
                        $arrSumCol[$col_index] = $arrSumCol[$col_index] + $sValue;
                    }
                } else {
                    $iInc++;
                    $sHTMLRowData = '<td class="data" style="padding:0cm 2px 0cm 2px;" align="' . $sAlign . '">' . $iInc . '&nbsp;</td>#row_conten#';
                    $v_poss = strrpos($sHTML_string_row, '#row_conten#');
                    $sHTML_string_row = substr_replace($sHTML_string_row, $sHTMLRowData, $v_poss, 12);
                }
            }
            $v_poss = strrpos($sHTML_string_row, '#row_conten#');
            $sHTML_string_row = substr_replace($sHTML_string_row, '</tr>#row_conten#', $v_poss, 12);
            $v_poss = strrpos($sHTMLData, '#row_conten#');
            $sHTMLData = substr_replace($sHTMLData, $sHTML_string_row, $v_poss, 12);
        }
        //Hoan thien row caculate
        if ($sRowSumHtml != '') {
            for ($col_index = $iCountColTitleSum; $col_index < $v_count_col; $col_index++) {
                if ($arrSumCol[$col_index] != '') {
                    $sType = $arrReportCol[$col_index]["C_FORMAT"];
                    $sFunction = $arrReportCol[$col_index]["C_FUN_NAME"];
                    $sRowSumHtml = $sRowSumHtml . '<td class="data" align="center"><b>' . self::_generatValue($arrSumCol[$col_index], $sType, $sFunction) . '</b></td>';
                } else {
                    $sRowSumHtml = $sRowSumHtml . '<td class="data">&nbsp;</td>';
                }
            }
            $sRowSumHtml = $sRowSumHtml . '</tr>';
            $v_poss = strrpos($sHTMLData, '#row_conten#');
            $sHTMLData = substr_replace($sHTMLData, $sRowSumHtml, $v_poss, 12);
        } else {
            $v_poss = strrpos($sHTMLData, '#row_conten#');
            $sHTMLData = substr_replace($sHTMLData, '', $v_poss, 12);
        }
        //Bao cao co su dung temp hay ko
        $v_report_temp = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_table_format", "report_label_file");
        if ($v_report_temp <> '') {//Neu bao cao su dung temp
            $sHTML_string = $objEfyLib->_readFile($v_report_temp);
            //Replace tieu chi loc
            $sFilter_list = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_header", "filter_list");
            if ($sFilter_list != '') {
                $tab_value = '';
                $value = '';
                $objConfigXml = new Zend_Config_Xml($psXmlStringInFile);
                $arrInforFilter = $objConfigXml->filter_formfield_list->toArray();
                //var_dump($arrInforFilter);
                $arrFilter = explode(',', $sFilter_list);
                for ($indexf = 0; $indexf < sizeof($arrFilter); $indexf++) {
                    $tab_value = $arrFilter[$indexf];
                    $type = $arrInforFilter[$tab_value]['type'];
                    //Neu kieu select
                    if ($type == 'selectbox') {
                        $input_data = $arrInforFilter[$tab_value]['input_data'];
                        //echo $input_data;
                        if ($input_data == 'session') {
                            $session_name = $arrInforFilter[$tab_value]['session_name'];
                            $arrListItem = $_SESSION[$session_name];
                            $Column_value = $arrInforFilter[$tab_value]['session_id_index'];
                            $Column_name = $arrInforFilter[$tab_value]['session_name_index'];
                            //echo $Column_value;exit;
                            for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                    $value = $arrListItem[$i][$Column_name];
                                    break;
                                }
                            }
                        } else if ($input_data == 'QuarterMonth') {
                            $value = $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                            if ($value != '') {
                                if ($value[0] == 'Q') {
                                    $value = 'QUÝ ' . $this->romanic_number($value[1]);
                                } else {
                                    $value = 'THÁNG ' . $value[0];
                                }
                            }
                        } else {
                            //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                            $sSelectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $arrInforFilter[$tab_value]['selectbox_option_sql']);
                            // thuc hien co che cache o day
                            $arrListItem = Extra_Db::adodbQueryDataInNumberMode($sSelectBoxOptionSql);
                            $Column_value = $arrInforFilter[$tab_value]['selectbox_option_id_column'];
                            $Column_name = $arrInforFilter[$tab_value]['selectbox_option_name_column'];
                            for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                //echo $arrListItem[$i][$Column_value];
                                if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                    $value = $arrListItem[$i][$Column_name];
                                    if ($tab_value == 'month_tab') {
                                        $month = $arrListItem[$i][$Column_value];
                                    }
                                    if ($tab_value == 'year_tab') {
                                        $nex_year = $arrListItem[$i][$Column_value];
                                    }
                                    break;
                                }
                            }
                        }
                        //exit;
                    } else {
                        $value = $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                    }
                    $sHTML_string = str_replace('#' . $tab_value . '#', $value, $sHTML_string);
                }
            }
            //Replace một số hằng số
            //Dung rieng cho bao cao o song cong
            //$nex_month = $month + 1;
            //$pre_month = $month - 1;
            //-------------------------
            //echo $month;exit;
            //if($month == 12){
            //$nex_month = 1;
            //$nex_year = $nex_year + 1;
            //}
            //if($month == 1){
            //$pre_month = 12;
            //}
            $userIdentity = Extra_Account::getInstance()->getIdentity();
            $ownerName = str_replace('UBND', '', $userIdentity->OWNER_NAME);

            $sHTML_string = str_replace('#UNIT_FULL_NAME#', $ownerName, $sHTML_string);
            $v_report_date = $userIdentity->OWNER_NAME . ", ng&#224;y " . date("d") . " th&#225;ng " . date("m") . " n&#259;m " . date("Y");
            $sHTML_string = str_replace('#STR_STATUS#', $v_report_date, $sHTML_string);
            //$sHTML_string = str_replace('#C_TONG#', $arrResult[0]['C_TONG'], $sHTML_string);
            //$sHTML_string = str_replace('#C_TRA_DUNG_HEN#', $arrResult[0]['C_TRA_DUNG_HEN'], $sHTML_string);
            //$sHTML_string = str_replace('#C_TRA_CHAM#', $arrResult[0]['C_TRA_CHAM'], $sHTML_string);
            //$sHTML_string = str_replace('#C_DANG_GIAI_QUYET#', $arrResult[0]['C_DANG_GIAI_QUYET'], $sHTML_string);
            //$sHTML_string = str_replace('#nex_month#', $nex_month, $sHTML_string);
            //$sHTML_string = str_replace('#pre_month#', $pre_month, $sHTML_string);
            $sHTML_string = str_replace('#YEAR#', date("Y"), $sHTML_string);
            $sHTML_string = str_replace('#DATACONTEN#', $sHTMLData, $sHTML_string);
            //exit;
        } else {//Truong hop khong dung temp
            //Tieu de cac cot
            $sHtmlTempWidth = '';
            $sHtmlTempLabel = '';
            for ($col_index = 0; $col_index < $v_count_col; $col_index++) {
                $sLabel = $arrReportCol[$col_index]["C_TITLE"];
                $iWidth = $arrReportCol[$col_index]["C_WIDTH"];
                $sHtmlTempWidth = $sHtmlTempWidth . '<col width="' . $iWidth . '%">';
                $sHtmlTempLabel = $sHtmlTempLabel . '<td class="header" align="' . $sAlign . '">' . $sLabel . '</td>';
            }
            $sThead = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_header", "thead");
            if ($sThead) {
                $sHTML_string = $sHTML_string . '<table class="report_table" style="width:99%;" cellpadding="0" cellspacing="0">' . $sHtmlTempWidth . '<thead><tr>' . $sHtmlTempLabel . '</tr></thead>';
            } else {
                $sHTML_string = $sHTML_string . '<table class="report_table" style="width:99%;" cellpadding="0" cellspacing="0">' . $sHtmlTempWidth . '<tr>' . $sHtmlTempLabel . '</tr>';
            }
            //Chen du lieu
            $sHTML_string = $sHTML_string . '#DATACONTEN#</table>';
            //Phan footer
            $sFooterTabCount = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_footer", "footer_colspan");
            $arrFooterTabCount = explode('|', $sFooterTabCount);
            $arrFooterTab = explode(',', $arrFooterTabCount[0]);
            $arrFooterCol = explode(',', $arrFooterTabCount[1]);
            $sHTML_string = $sHTML_string . '<table width="99%" border="0" cellspacing="0" cellpadding="0">';
            $sHTML_string = $sHTML_string . '<tr><td colspan="' . $v_count_col . '">&nbsp;</td></tr><tr>';
            for ($index = 0; $index < sizeof($arrFooterTab); $index++) {
                $value = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_footer", $arrFooterTab[$index]);
                $sHTML_string = $sHTML_string . '<td align="center" colspan="' . $arrFooterCol[$index] . '" class="signer">' . $value . '&nbsp;</td>';
            }
            $sHTML_string = $sHTML_string . '</tr></table>';
            //Phan Header
            //Tieu de bao cao
            $sLarge_title = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_header", "large_title");
            $sHTMLHeaderstring = '<table width="99%" class="report_title_table" cellpadding="0" cellspacing="0">';
            $sHTMLHeaderstring = $sHTMLHeaderstring . '<tr><td align="center" class="sub_title" colspan="' . $v_count_col . '">' . $sLarge_title . '</td></tr>';
            //Hien thi tieu chi loc
            $sFilter_list = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_header", "filter_list");
            if ($sFilter_list <> '') {
                $arrFilterTemp = explode('|#|', $sFilter_list);
                $objConfigXml = new Zend_Config_Xml($psXmlStringInFile);
                $arrInforFilter = $objConfigXml->filter_formfield_list->toArray();
                for ($index = 0; $index < sizeof($arrFilterTemp); $index++) {
                    $tab_value = '';
                    $value = '';
                    $arrFilter = explode(',', $arrFilterTemp[$index]);
                    $dilm = '';
                    for ($indexf = 0; $indexf < sizeof($arrFilter); $indexf++) {
                        $tab_value = $arrFilter[$indexf];
                        $type = $arrInforFilter[$tab_value]['type'];
                        if ($type == 'selectbox') {
                            $input_data = $arrInforFilter[$tab_value]['input_data'];
                            if ($input_data == 'session') {
                                $session_name = $arrInforFilter[$tab_value]['session_name'];
                                $arrListItem = $_SESSION[$session_name];
                                $Column_value = $arrInforFilter[$tab_value]['session_id_index'];
                                $Column_name = $arrInforFilter[$tab_value]['session_name_index'];
                                //echo $Column_value;exit;
                                for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                    if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                        $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $arrListItem[$i][$Column_name];
                                        $dilm = ', ';
                                        break;
                                    }
                                }
                            } else {
                                //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                                $sSelectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $arrInforFilter[$tab_value]['selectbox_option_sql']);
                                // thuc hien co che cache o day
                                $arrListItem = Extra_Db::adodbQueryDataInNumberMode($sSelectBoxOptionSql);
                                $Column_value = $arrInforFilter[$tab_value]['selectbox_option_id_column'];
                                $Column_name = $arrInforFilter[$tab_value]['selectbox_option_name_column'];
                                for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                    //echo $arrListItem[$i][$Column_value];
                                    if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                        $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $arrListItem[$i][$Column_name];
                                        $dilm = ', ';
                                        break;
                                    }
                                }
                            }
                            //exit;
                        } else {
                            if ($tab_value == 'record_type') {
                                $value = 'TTHC: ';
                            }
                            $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                            $dilm = ', ';
                        }
                    }
                    $sHTMLHeaderstring = $sHTMLHeaderstring . '<tr><td align="center" class="sub_title" colspan="' . $v_count_col . '">' . $value . '</td></tr>';
                    //var_dump($arrFilter);
                }
            }
            $sHTMLHeaderstring = $sHTMLHeaderstring . '<tr><td colspan="' . $v_count_col . '">&nbsp;</td></tr></table>';
            $sHTML_string = $sHTMLHeaderstring . $sHTML_string;
            // Phan dau cua chuoi html
            $sOrientation = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "pageSetup", "orientation");
            $sHtmlContent = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
								   xmlns:w="urn:schemas-microsoft-com:office:word"
								   xmlns:v="urn:schemas-microsoft-com:vml">
								<head>							
								<meta http-equiv="Content-Type" content="application/msword; charset=UTF-8">
								<meta name=ProgId content=Word.Document>
								<!--[if gte mso 9]>
								  <xml>
								    <w:WordDocument>
								      <w:View>Print</w:View>
								      <w:DoNotOptimizeForBrowser/>
								    </w:WordDocument>
								  </xml>
								<![endif]-->
								<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
								<META HTTP-EQUIV="Expires" CONTENT="-1">
								<style>
										@page{margin:.4in .4in .4in .4in;mso-header-margin:1in;mso-footer-margin:1in;mso-page-orientation:' . $sOrientation . ';}
								</style>';
            $sStyleFileName = "public/style/report_style.css";
            $sStyleContent = $objEfyLib->_readFile($sStyleFileName);
            $sHtmlContent = $sHtmlContent . '<style type=text/css>' . $sStyleContent . '</style>';
            $sHTML_string = $sHtmlContent . '</head><body><div class=Section1>' . $sHTML_string . '</div></body>';
            $sHTML_string = str_replace('#DATACONTEN#', $sHTMLData, $sHTML_string);
        }
        switch ($v_exporttype) {
            case 1;
                $sExportFileName = "IO_report.htm";
                break;
            case 2;
                $sExportFileName = "IO_report.doc";
                break;
            default:
                $sExportFileName = "IO_report.htm";
                break;
        }
        // Tao ra file
        $objEfyLib->_writeFile('public/export/' . $sExportFileName, $sHTML_string);
        $sExportFileName = $objConfig->_getCurrentHttpAndHost() . 'public/export/' . $sExportFileName;
        return $sExportFileName;
    }

    /**
     * @param $integer
     * @param bool $upcase
     * @return string
     */
    public function romanic_number($integer, $upcase = true)
    {
        $table = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $return = '';
        while ($integer > 0) {
            foreach ($table as $rom => $arb) {
                if ($integer >= $arb) {
                    $integer -= $arb;
                    $return .= $rom;
                    break;
                }
            }
        }
        return $return;
    }

    /**
     * cuongnh
     * Ket xuat bao cao dinh dang excel
     * $psXmlStringInFile                : chuoi xml quy dinh cac thuoc tinh chung
     * $arrReportCol                    : danh sach cac cot cua bao cao
     * $arrResult                        : danh sach ban ghi
     * $v_colume_name_of_xml_string        : Ten truong xml lay du lieu
     */
    public function _exportreportexcel($psXmlStringInFile, $arrReportCol, $arrResult, $v_colume_name_of_xml_string, $sFilterXmlString)
    {
        //Goi class xu ly
        Zend_Loader::loadClass('Zend_Config_Xml');
        Zend_Loader::loadClass('Extra_Excel');
        //Tao doi tuong
        $objXmlLib = new Extra_Xml();
        $objConfig = new Extra_Init();
        //Tao doi tuong xml
        $objConfigXml = new Zend_Config_Xml($psXmlStringInFile);
        //Kiem tra xem co nhom du lieu khong
        $sGroupCode = $objConfigXml->report_sql->group_by;
        if ($sGroupCode <> '') {
            $sGroupName = $objConfigXml->report_sql->group_name;
            //Danh chi muc cho nhom
            $sgroup_identity = $objConfigXml->report_sql->group_identity;
            //Dat lai stt khi chuyen nhom
            $sreset_identity = $objConfigXml->report_sql->reset_identity;
            $sGroupValue = '';
        }
        //echo 'as'. $sGroupName,$sgroup_identity,$sreset_identity;exit;
        //Bao cao co su dung temp hay ko
        $v_report_temp = $objConfigXml->report_table_format->report_label_excel_file;
        if ($v_report_temp) {
            $objReader = Extra_Excel::createReader('Excel5');
            $objPHPExcel = $objReader->load($v_report_temp);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //e.g., 6
            $highestRow = $objWorksheet->getHighestRow();
            //Hien thi tieu chi loc
            $sFilter_list = $objConfigXml->report_header->filter_list;
            if ($sFilter_list <> '') {
                $arrFilterTemp = explode('|#|', $sFilter_list);
                $arrInforFilter = $objConfigXml->filter_formfield_list->toArray();
                for ($indexft = 0; $indexft < sizeof($arrFilterTemp); $indexft++) {
                    $value = '';
                    $arrFilter = explode(',', $arrFilterTemp[$indexft]);
                    for ($index = 0; $index < sizeof($arrFilter); $index++) {
                        $tab_value = $arrFilter[$index];
                        $type = $arrInforFilter[$tab_value]['type'];
                        if ($type == 'selectbox') {
                            $input_data = $arrInforFilter[$tab_value]['input_data'];
                            if ($input_data == 'session') {
                                $session_name = $arrInforFilter[$tab_value]['session_name'];
                                $arrListItem = $_SESSION[$session_name];
                                $Column_value = $arrInforFilter[$tab_value]['session_id_index'];
                                $Column_name = $arrInforFilter[$tab_value]['session_name_index'];
                                //echo $Column_value;exit;
                                for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                    if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                        $value = $arrListItem[$i][$Column_name];
                                        break;
                                    }
                                }
                            } else if ($input_data == 'QuarterMonth') {
                                $value = $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                                if ($value != '') {
                                    if ($value[0] == 'Q') {
                                        $value = 'QUÝ ' . $this->romanic_number($value[1]);
                                    } else {
                                        $value = 'THÁNG ' . $value[0];
                                    }
                                }
                            } else {
                                //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                                $sSelectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $arrInforFilter[$tab_value]['selectbox_option_sql']);
                                // thuc hien co che cache o day
                                $arrListItem = Extra_Db::adodbQueryDataInNumberMode($sSelectBoxOptionSql);
                                $Column_value = $arrInforFilter[$tab_value]['selectbox_option_id_column'];
                                $Column_name = $arrInforFilter[$tab_value]['selectbox_option_name_column'];
                                for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                    //echo $arrListItem[$i][$Column_value];
                                    if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                        $value = $arrListItem[$i][$Column_name];
                                        if ($tab_value == 'month_tab') {
                                            $month = $arrListItem[$i][$Column_value];
                                        }
                                        if ($tab_value == 'year_tab') {
                                            $nex_year = $arrListItem[$i][$Column_value];
                                        }
                                        break;
                                    }
                                }
                            }
                            //exit;
                        } else {
                            $value = $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                        }
                        //replace du lieu
                        for ($i = 0; $i < $highestColumnIndex; $i++) {
                            $column = PHPExcel_Cell::stringFromColumnIndex($i);
                            for ($j = 1; $j <= $highestRow; $j++) {
                                $rowvalue = $objWorksheet->getCell($column . $j)->getValue();
                                $rowvalue = str_replace('#' . $tab_value . '#', $value, $rowvalue);
                                $objWorksheet->setCellValue($column . $j, $rowvalue);
                            }
                        }
                    }
                }
            }
            //replace du lieu khac
            //Dung rieng cho bao cao o song cong
            //$nex_month = $month + 1;
            //$pre_month = $month - 1;
            //-------------------------
            //echo $month;exit;
            //if($month == 12){
            //	$nex_month = 1;
            //	$nex_year = $nex_year + 1;
            //}
            //if($month == 1){
            //	$pre_month = 12;
            //}
            //Replace một số hằng số
            $userIdentity = Extra_Account::getInstance()->getIdentity();
            $ownerName = str_replace('UBND', '', $userIdentity->OWNER_NAME);
            $v_report_date = $ownerName . " ngày " . date("d") . " tháng " . date("m") . " năm " . date("Y");
            for ($i = 0; $i < $highestColumnIndex; $i++) {
                $column = PHPExcel_Cell::stringFromColumnIndex($i);
                for ($j = 1; $j <= $highestRow; $j++) {
                    $rowvalue = $objWorksheet->getCell($column . $j)->getValue();
                    $rowvalue = str_replace('#UNIT_FULL_NAME#', $ownerName, $rowvalue);
                    $rowvalue = str_replace('#STR_STATUS#', $v_report_date, $rowvalue);
                    //$rowvalue = str_replace('#C_TONG#', $arrResult[0]['C_TONG'], $rowvalue);
                    //$rowvalue = str_replace('#C_TRA_DUNG_HEN#', $arrResult[0]['C_TRA_DUNG_HEN'], $rowvalue);
                    //$rowvalue = str_replace('#C_TRA_CHAM#', $arrResult[0]['C_TRA_CHAM'], $rowvalue);
                    //$rowvalue = str_replace('#C_DANG_GIAI_QUYET#', $arrResult[0]['C_DANG_GIAI_QUYET'], $rowvalue);
                    //$rowvalue = str_replace('#nex_month#', $nex_month, $rowvalue);
                    //$rowvalue = str_replace('#pre_month#', $pre_month, $rowvalue);
                    $rowvalue = str_replace('#YEAR#', date("Y"), $rowvalue);
                    $objWorksheet->setCellValue($column . $j, $rowvalue);
                }
            }
            //Lay row bat dau chen du lieu
            $rowfirt = $objConfigXml->report_sql->firt_row;
            if ($rowfirt == '') {
                $rowfirt = '1';
            }
            $v_count_col = sizeof($arrReportCol);
            $v_count_row = sizeof($arrResult);
            //Them so row
            $objWorksheet->insertNewRowBefore($rowfirt + 1, $v_count_row);
            //Chen du lieu bao cao
            $isiifirt = 65;
            $isiitemp = 0;
            if ($v_count_col > 0) {
                $isiiend = $isiifirt + $v_count_col - 1;
                $iIngroup = 0;
                $iInc = 0;
                //Day data vao tung cot
                $sCaculate_title = 64;
                for ($col_index = 0; $col_index < $v_count_col; $col_index++) {
                    $sCaculate_value = 0;
                    $isiitemp = $isiifirt + $col_index;
                    $v_align = $arrReportCol[$col_index]["C_ALIGN"];
                    $v_type = $arrReportCol[$col_index]["C_FORMAT"];
                    $sCaculate = $arrReportCol[$col_index]["C_CALCULATE"];
                    $xmlData = $arrReportCol[$col_index]["C_DATA_SOURCE"];
                    $columnName = $arrReportCol[$col_index]["C_COL_TAB_NAME"];
                    $sFunction = $arrReportCol[$col_index]["C_FUN_NAME"];
                    for ($row_index = 0; $row_index < $v_count_row; $row_index++) {
                        if ($arrResult[$row_index]['CHECK_GROUP']) {
                            $iIngroup = $arrResult[$row_index]['CHECK_GROUP'];
                        }
                        $row_item = $rowfirt + $row_index + $iIngroup;
                        if (($sGroupCode) && ($isiitemp == $isiifirt)) {
                            //echo 'ok';exit;
                            if ($sGroupValue != $arrResult[$row_index][$sGroupCode]) {
                                $iIngroup++;
                                //echo $arrResult[$row_index][$sGroupName],'<br>';
                                $objWorksheet->insertNewRowBefore($row_item + 1, 1);
                                if ($sgroup_identity) {
                                    $objWorksheet->setCellValue('A' . $row_item, self::romanic_number($iIngroup));
                                    $objWorksheet->getStyle('A' . $row_item)->getAlignment()->setHorizontal('center');
                                    $objWorksheet->getStyle('A' . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                                }
                                $objWorksheet->mergeCells('B' . $row_item . ':' . chr($isiiend) . $row_item);
                                $objWorksheet->setCellValue('B' . $row_item, $arrResult[$row_index][$sGroupName]);
                                $objWorksheet->getStyle('B' . $row_item)->getAlignment()->setHorizontal('left');
                                $objWorksheet->getStyle('B' . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                                $sGroupValue = $arrResult[$row_index][$sGroupCode];
                                $arrResult[$row_index]['CHECK_GROUP'] = $iIngroup;
                                if ($sreset_identity) {
                                    $iInc = 0;
                                }
                                $row_item++;
                            }
                        }
                        if ($v_type == 'identity') {
                            $iInc++;
                            $value = $iInc;
                        } else {
                            if ($xmlData == 'xml_data') {
                                $v_received_record_xml_data = '<?xml version="1.0" encoding="UTF-8"?>' . $arrResult[$row_index][$v_colume_name_of_xml_string];
                                $value = $objXmlLib->_xmlGetXmlTagValue($v_received_record_xml_data, "data_list", $columnName);
                            } else {
                                $value = $arrResult[$row_index][$columnName];
                            }
                        }
                        if ($sCaculate) {
                            $sCaculate_value = $sCaculate_value + $value;
                        }
                        $objWorksheet->setCellValue(chr($isiitemp) . $row_item, self::_generatValue($value, $v_type, $sFunction));
                        $objWorksheet->getStyle(chr($isiitemp) . $row_item)->getAlignment()->setHorizontal($v_align);
                    }
                    if ($sCaculate_value > 0) {
                        if ($sCaculate_title >= 65) {
                            $objWorksheet->mergeCells('A' . ($row_item + 1) . ':' . chr($sCaculate_title) . ($row_item + 1));
                            $objWorksheet->getStyle('A' . ($row_item + 1))->getAlignment()->setHorizontal('center');
                            $objWorksheet->setCellValue('A' . ($row_item + 1), 'Tổng cộng');
                            $objWorksheet->getStyle('A' . ($row_item + 1))->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                            $sCaculate_title = 0;
                        }
                        $objWorksheet->setCellValue(chr($isiitemp) . ($row_item + 1), self::_generatValue($sCaculate_value, $v_type, $sFunction));
                        $objWorksheet->getStyle(chr($isiitemp) . ($row_item + 1))->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                    } else {
                        $sCaculate_title = $sCaculate_title + 1;
                    }
                }
                //Set style cho data

                $objWorksheet->getStyle('A' . $rowfirt . ':' . chr($isiiend) . ($rowfirt + $v_count_row + $iIngroup))->applyFromArray($objConfig->_setStyleDataExcell());

            }
        } else {
            //Tao lap doi tuong xu ly dl excell
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            //cot dau tien
            $isiifirt = 65;
            $isiitemp = 0;
            $v_count_col = sizeof($arrReportCol);
            $v_count_row = sizeof($arrResult);
            $rowfirt = $objConfigXml->report_sql->firt_row;
            if ($rowfirt == '') {
                $rowfirt = '1';
            }
            if ($v_count_col > 0) {
                $isiiend = $isiifirt + $v_count_col - 1;
                $iIngroup = 0;
                $iInc = 0;
                $sCaculate_title = 64;
                for ($col_index = 0; $col_index < $v_count_col; $col_index++) {
                    $sCaculate_value = 0;
                    $isiitemp = $isiifirt + $col_index;
                    $v_label = $arrReportCol[$col_index]["C_TITLE"];
                    $v_width = $arrReportCol[$col_index]["C_WIDTH"];
                    $sCaculate = $arrReportCol[$col_index]["C_CALCULATE"];
                    $sFunction = $arrReportCol[$col_index]["C_FUN_NAME"];
                    $objWorksheet->getColumndimension(chr($isiitemp))->setWidth($v_width);
                    //$objWorksheet->getColumndimension(chr($isiitemp))->setAutoSize(true);
                    $objWorksheet->setCellValue(chr($isiitemp) . $rowfirt, $v_label);
                    $objWorksheet->getStyle(chr($isiitemp) . $rowfirt)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                    $objWorksheet->getStyle(chr($isiitemp) . $rowfirt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    //Day data vao tung cot
                    $v_align = $arrReportCol[$col_index]["C_ALIGN"];
                    $v_type = $arrReportCol[$col_index]["C_FORMAT"];
                    for ($row_index = 0; $row_index < $v_count_row; $row_index++) {
                        if ($arrResult[$row_index]['CHECK_GROUP']) {
                            $iIngroup = $arrResult[$row_index]['CHECK_GROUP'];
                        }
                        $row_item = $rowfirt + $row_index + $iIngroup + 1;
                        //Nhom du lieu
                        if (($sGroupCode) && ($isiitemp == $isiifirt)) {
                            //echo 'ok';exit;
                            if ($sGroupValue != $arrResult[$row_index][$sGroupCode]) {
                                $iIngroup++;
                                //echo $arrResult[$row_index][$sGroupName],'<br>';
                                $objWorksheet->insertNewRowBefore($row_item + 1, 1);
                                if ($sgroup_identity) {
                                    $objWorksheet->setCellValue('A' . $row_item, self::romanic_number($iIngroup));
                                    $objWorksheet->getStyle('A' . $row_item)->getAlignment()->setHorizontal('center');
                                    $objWorksheet->getStyle('A' . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                                }
                                $objWorksheet->mergeCells('B' . $row_item . ':' . chr($isiiend) . $row_item);
                                $objWorksheet->setCellValue('B' . $row_item, $arrResult[$row_index][$sGroupName]);
                                $objWorksheet->getStyle('B' . $row_item)->getAlignment()->setHorizontal('left');
                                $objWorksheet->getStyle('B' . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                                $sGroupValue = $arrResult[$row_index][$sGroupCode];
                                $arrResult[$row_index]['CHECK_GROUP'] = $iIngroup;
                                if ($sreset_identity) {
                                    $iInc = 0;
                                }
                                $row_item++;
                            }
                        }
                        if ($v_type == 'identity') {
                            $iInc++;
                            $value = $iInc;
                        } else {
                            $xmlData = $arrReportCol[$col_index]["C_DATA_SOURCE"];
                            $columnName = $arrReportCol[$col_index]["C_COL_TAB_NAME"];
                            $sFunction = $arrReportCol[$col_index]["C_FUN_NAME"];
                            if ($xmlData == 'xml_data') {
                                $v_received_record_xml_data = '<?xml version="1.0" encoding="UTF-8"?>' . $arrResult[$row_index][$v_colume_name_of_xml_string];
                                $value = $objXmlLib->_xmlGetXmlTagValue($v_received_record_xml_data, "data_list", $columnName);
                            } else {
                                $value = $arrResult[$row_index][$columnName];
                            }
                        }
                        if ($sCaculate) {
                            $sCaculate_value = $sCaculate_value + $value;
                        }
                        $objWorksheet->setCellValue(chr($isiitemp) . $row_item, self::_generatValue($value, $v_type, $sFunction));
                        $objWorksheet->getStyle(chr($isiitemp) . $row_item)->getAlignment()->setHorizontal($v_align);
                    }
                    if ($sCaculate_value > 0) {
                        if ($sCaculate_title >= 65) {
                            $objWorksheet->mergeCells('A' . ($row_item + 1) . ':' . chr($sCaculate_title) . ($row_item + 1));
                            $objWorksheet->getStyle('A' . ($row_item + 1))->getAlignment()->setHorizontal('center');
                            $objWorksheet->setCellValue('A' . ($row_item + 1), 'Tổng cộng');
                            $objWorksheet->getStyle('A' . ($row_item + 1))->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                            $sCaculate_title = -1110;
                        }
                        $objWorksheet->setCellValue(chr($isiitemp) . ($row_item + 1), self::_generatValue($sCaculate_value, $v_type, $sFunction));
                        $objWorksheet->getStyle(chr($isiitemp) . ($row_item + 1))->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                    } else {
                        $sCaculate_title = $sCaculate_title + 1;
                    }
                }
                //Set style cho data
                $objWorksheet->getStyle('A' . $rowfirt . ':' . chr($isiiend) . ($rowfirt + $v_count_row + $iIngroup + 1))->applyFromArray($objConfig->_setStyleDataExcell());
                //Phan Header
                //Tieu de bao cao
                $sLarge_title = $objConfigXml->report_header->large_title;
                $objWorksheet->mergeCells('A1:' . chr($isiiend) . '1');
                $objWorksheet->setCellValue('A1', $sLarge_title);
                $objWorksheet->getStyle('A1')->getFont()->applyFromArray($objConfig->_setFontTitleExcell());
                $objWorksheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //Hien thi tieu chi loc
                $sFilter_list = $objConfigXml->report_header->filter_list;
                if ($sFilter_list <> '') {
                    $arrFilterTemp = explode('|#|', $sFilter_list);
                    $arrInforFilter = $objConfigXml->filter_formfield_list->toArray();
                    for ($indexft = 0; $indexft < sizeof($arrFilterTemp); $indexft++) {
                        $value = '';
                        $row_item = 2 + $indexft;
                        $arrFilter = explode(',', $arrFilterTemp[$indexft]);
                        $dilm = '';
                        for ($index = 0; $index < sizeof($arrFilter); $index++) {
                            $tab_value = $arrFilter[$index];
                            $type = $arrInforFilter[$tab_value]['type'];
                            if ($type == 'selectbox') {
                                $input_data = $arrInforFilter[$tab_value]['input_data'];
                                if ($input_data == 'session') {
                                    $session_name = $arrInforFilter[$tab_value]['session_name'];
                                    $arrListItem = $_SESSION[$session_name];
                                    $Column_value = $arrInforFilter[$tab_value]['session_id_index'];
                                    $Column_name = $arrInforFilter[$tab_value]['session_name_index'];
                                    //echo $Column_value;exit;
                                    for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                        if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                            $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $arrListItem[$i][$Column_name];
                                            $dilm = ', ';
                                            break;
                                        }
                                    }
                                } else {
                                    //thay the ma don vi cua nguoi dang nhap hien thoi vao chuoi SQL
                                    $sSelectBoxOptionSql = str_replace("#OWNER_CODE#", $_SESSION['OWNER_CODE'], $arrInforFilter[$tab_value]['selectbox_option_sql']);
                                    // thuc hien co che cache o day
                                    $arrListItem = Extra_Db::adodbQueryDataInNumberMode($sSelectBoxOptionSql);
                                    $Column_value = $arrInforFilter[$tab_value]['selectbox_option_id_column'];
                                    $Column_name = $arrInforFilter[$tab_value]['selectbox_option_name_column'];
                                    for ($i = 0; $i < sizeof($arrListItem); $i++) {
                                        //echo $arrListItem[$i][$Column_value];
                                        if ($arrListItem[$i][$Column_value] == $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value)) {
                                            $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $arrListItem[$i][$Column_name];
                                            $dilm = ', ';
                                            break;
                                        }
                                    }
                                }
                                //exit;
                            } else {
                                $value = $value . $dilm . $arrInforFilter[$tab_value]['label'] . $objXmlLib->_xmlGetXmlTagValue($sFilterXmlString, "data_list", $tab_value);
                                $dilm = ', ';
                            }
                        }
                        $objWorksheet->mergeCells('A' . $row_item . ':' . chr($isiiend) . $row_item);
                        $objWorksheet->setCellValue('A' . $row_item, $value);
                        $objWorksheet->getStyle('A' . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleExcell());
                        $objWorksheet->getStyle('A' . $row_item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
                //Phan footer
                $sFooterTabCount = $objConfigXml->report_footer->footer_colspan;
                $arrFooterTabCount = explode('|', $sFooterTabCount);
                $arrFooterTab = explode(',', $arrFooterTabCount[0]);
                $arrFooterCol = explode(',', $arrFooterTabCount[1]);
                $row_item = $rowfirt + $v_count_row + $iIngroup + 2;
                $iFromCol = 65;
                for ($index = 0; $index < sizeof($arrFooterTab); $index++) {
                    $value = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "report_footer", $arrFooterTab[$index]);
                    $iToCol = $iFromCol + $arrFooterCol[$index] - 1;
                    $objWorksheet->mergeCells(chr($iFromCol) . $row_item . ':' . chr($iToCol) . $row_item);
                    $objWorksheet->setCellValue(chr($iFromCol) . $row_item, $value);
                    $objWorksheet->getStyle(chr($iFromCol) . $row_item)->getFont()->applyFromArray($objConfig->_setFontTitleColExcell());
                    $objWorksheet->getStyle(chr($iFromCol) . $row_item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorksheet->mergeCells(chr($iFromCol) . ($row_item + 1) . ':' . chr($iToCol) . ($row_item + 6));
                    $iFromCol = $iToCol + 1;
                }
            } else {
                echo "B&#225;o c&#225;o ch&#432;a &#273;&#432;&#7907;c khai b&#225;o ho&#224;n ch&#7881;nh!";
                exit;
            }
            // pageSetup
            $objWorksheet->getPageSetup()->setPrintArea('A1:' . chr($isiiend) . ($row_item + 7));
            $sOrientation = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "pageSetup", "orientation");
            $objWorksheet->getPageSetup()->setOrientation($sOrientation);
            $sPaperSize = $objXmlLib->_xmlGetXmlTagValue($psXmlStringInFile, "pageSetup", "paperSize");
            $objWorksheet->getPageSetup()->setPaperSize($sPaperSize);
            $objWorksheet->getPageSetup()->setFitToPage(true);
            $objWorksheet->getPageSetup()->setFitToWidth(1);
            $objWorksheet->getPageSetup()->setFitToHeight(0);
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //Duong dan file export
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "public\\export\\IO_report.xls";
        $objWriter->save($reportFile);
        $sExportFileName = $objConfig->_getCurrentHttpAndHost() . 'public/export/IO_report.xls';
        return $sExportFileName;
    }

    /**
     * Cuongnh
     * Định dạng lại giá trị một số kiểu dữ liệu khi kết xuất báo cáo
     * $pValue: giá trị cần định dạng
     * $pType: loại dữ liệu
     */
    public function _generatValue($pValue, $pType = '', $sFunction = '')
    {
        $objEfyLib = new Extra_Util();
        $value_return = '';
        switch ($pType) {
            case "date";
                $value_return = $objEfyLib->_yyyymmddToDDmmyyyy($pValue);
                break;
            case "time";
                $value_return = $objEfyLib->_yyyymmddToHHmm($pValue);
                break;
            case "money";
                $value_return = number_format($pValue, 0, '.', ',');;
                break;
            default:
                $value_return = $pValue;
        }
        if ($sFunction <> '') {
            $value_return = self::$sFunction($value_return);
        }
        return $value_return;
    }

    /**
     * @param $pathLink
     * @param $folderYear
     * @param $folderMonth
     * @param string $sCurrentDay
     * @return string
     */
    public function _createFolder($pathLink, $folderYear, $folderMonth, $sCurrentDay = "")
    {
        $sPath = $pathLink;
        if (!file_exists($sPath . $folderYear)) {
            mkdir($sPath . $folderYear, 0777, true);
            $sPath = $sPath . $folderYear;
            if (!file_exists($sPath . chr(92) . $folderMonth)) {
                mkdir($sPath . chr(92) . $folderMonth, 0777, true);
            }
        } else {
            $sPath = $sPath . $folderYear;
        }
        //Tao ngay trong nam->thang
        //echo $sPath . chr(92) . $folderMonth . chr(92) . $sCurrentDay;exit;
        if (!file_exists($sPath . chr(92) . $folderMonth . chr(92) . $sCurrentDay)) {
            mkdir($sPath . chr(92) . $folderMonth . chr(92) . $sCurrentDay, 0777);
        }
        $strReturn = $sPath . '/' . $folderMonth . '/' . $sCurrentDay . '/';
        //echo $strReturn;exit;
        return $strReturn;
    }

    /**
     * @param $sListAttach
     * @param $sDir
     * @param string $sVarName
     * @param string $sDelimitor
     * @return string|tra
     */
    public function _uploadFileAttachList($sListAttach, $sDir, $sVarName = "FileName", $sDelimitor = ",")
    {
        $path = self::_createFolder($sDir, date('Y'), date('m'), date('d'));
        $sFileNameList = "";
        $arrAttach = explode(',', $sListAttach);
        $i = sizeof($arrAttach);
        if ($i == 0) {
            return $sFileNameList;
        }

        for ($index = 0; $index < $i; $index++) {
            $random = self::_get_randon_number();
            $sAttachFileName = $sVarName . $index;
            $fodel = date("Y") . '_' . date("m") . '_' . date("d") . "_" . date("H") . date("i") . date("u") . $random . "!~!";
            $sFullFileName = $fodel . self::_replaceBadChar($_FILES[$sAttachFileName]['name']);
            // Neu la file
            if ($arrAttach[$index] != "" && (is_file($_FILES[$sAttachFileName]['name']) || $_FILES[$sAttachFileName]['name'] != '')) {
                //echo 'full file name:'. $sFullFileName; exit;
                move_uploaded_file($_FILES[$sAttachFileName]['tmp_name'], $path . self::_convertVNtoEN($sFullFileName));
                $sFileNameList .= $arrAttach[$index] . ':' . $sFullFileName . $sDelimitor;
            }
        }
        // xu ly chuoi
        $sFileNameList = substr($sFileNameList, 0, strlen($sFileNameList) - strlen($sDelimitor));
        // tra lai gia tri
        return self::_convertVNtoEN($sFileNameList);
    }

    /**
     * @return int
     */
    function _get_randon_number()
    {
        $ret_value = mt_rand(1, 1000000);
        return $ret_value;
    }

    /**
     * Thay the cac ki tu dac biet trong mot xau boi ki tu khac
     *
     * @param $p_string : Chuoi can thay the
     * @return : Chuoi da thay the ky tu
     */
    public function _replaceBadChar($spString)
    {
        $psRetValue = stripslashes($spString);
        $psRetValue = str_replace('<', '&lt;', $psRetValue);
        $psRetValue = str_replace('>', '&gt;', $psRetValue);
        $psRetValue = str_replace('"', '&#34;', $psRetValue);
        $psRetValue = str_replace("'", '&#39;', $psRetValue);
        return $psRetValue;
    }

    /**
     * Creater: KHOINV
     *
     * @param $strText : chuoi ky tu can chuyen font tu VN sang EN
     * @return tra ve chuoi khong dau
     */
    function _convertVNtoEN($strText)
    {
        $vnChars = array("á", "à", "ả", "ã", "ạ", "ă", "ắ", "ằ", "ẳ", "ẵ", "ặ", "â", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ê", "ế", "�?", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "�?", "õ", "�?", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "ơ", "ớ", "�?", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "đ", "�?", "﻿À", "Ả", "Ã", "Ạ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Â", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ê", "Ế", "Ề", "Ể", "Ễ", "Ệ", "�?", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ô", "�?", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "�?", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "�?");
        $enChars = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D");
        for ($i = 0; $i < sizeof($vnChars); $i++) {
            $strText = str_replace($vnChars[$i], $enChars[$i], $strText);
        }
        return $strText;
    }

    /**
     * @param $sRecordID
     * @param $sKeyAttach
     * @return mixed
     */
    public function eCSFileGetSingle($sRecordID, $sKeyAttach)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec EfyLib_libFileGetSingle  ";
        $sql .= "'" . $sRecordID . "'";
        $sql .= ",'" . $sKeyAttach . "'";
        //echo $sql ; exit();
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrResult = $objConn->adodbQueryDataInNameMode($sql);

        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
        //var_dump($arrResult) . 'du lieu ket xuat tu database';
    }

    /**
     * @param $sUnitID
     * @return mixed
     */
    public function eCS_GetUnitName($sUnitID)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec eCS_GetUnitName  ";
        $sql .= "'" . $sUnitID . "'";
        //echo $sql ; exit();
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrResult = $objConn->adodbExecSqlString($sql);

        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
        //var_dump($arrResult) . 'du lieu ket xuat tu database';
    }

    /**
     * @param $sStaffId
     * @param $sOwnerCode
     * @param string $sClauseString
     * @return mixed
     */
    public function eCSNetRecordTypeGetAllByStaff($sStaffId, $sOwnerCode, $sClauseString = '')
    {
        $objConn = new  Extra_Db();
        $sql = "Exec eCS_RecordTypeGetAllByStaff ";
        $sql = $sql . "'" . $sStaffId . "'";
        $sql = $sql . ",'" . $sOwnerCode . "'";
        $sql = $sql . ",'" . $sClauseString . "'";
        echo htmlspecialchars($sql);
        try {
            $arrNetRecordType = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrNetRecordType;
    }

    /**
     * Creater: Tientk
     * Date: 25/5/2011
     * Des: Ham lay thong tin co ban cua mot loai thu tuc hanh chinh
     * @param $RecordTypeId Ma Loai ho so
     * @param unknown_type $arrRecord Mang nay la ket qua cua ham eCSNetRecordTypeGetAll
     */
    function getinforNetRecordType($NetRecordTypeId, $arrNetRecordType)
    {
        $arrResult = array();
        foreach ($arrNetRecordType as $netRecordType) {
            if ($netRecordType['PK_NET_RECORD'] == $NetRecordTypeId) {
                $arrResult = $netRecordType;
                break;
            }
        }
        return $arrResult;
    }
    //***************************************************************************************
    //'Muc dich : Sinh ra doan ma HTML the hien cac option cua mot SelectBox
    //'			dua tren mot arr
    //'Tham so  :
    //			arr_list		: mang du lieu
    //			ValueColumn		: Ten cot lay gia tri gan cho moi option

    //			DisplayColumn	: Ten cot lay de hien thi cho moi option
    //			SelectedValue	: Gia tri duoc lua chon )
    //****************************************************************************************
    function _generate_select_option($arr_list, $IdColumn, $ValueColumn, $NameColumn, $SelectedValue)
    {
        $strHTML = "";
        $i = 0;
        $count = sizeof($arr_list);
        for ($row_index = 0; $row_index < $count; $row_index++) {
            $strID = trim($arr_list[$row_index][$IdColumn]);
            $strValue = trim($arr_list[$row_index][$ValueColumn]);
            $gt = $SelectedValue;
            if ($strID != $SelectedValue) {
                $optSelected = "";
            } else {
                $optSelected = "selected";
            }
            $DspColumn = trim($arr_list[$row_index][$NameColumn]);
            $strHTML .= '<option id=' . '"' . $strID . '"' . ' ' . 'name=' . '"' . $DspColumn . '"' . ' ';
            $strHTML .= 'value=' . '"' . $strValue . '"' . ' ' . $optSelected . '>' . $DspColumn . '</option>';
            $i++;
        }
        return $strHTML;
    }

    function _isbreakcontent($v_value)
    {
        $v_len = strlen($v_value);
        if ($v_len > 0) {
            for ($index = 0; $index < $v_len; $index++) {
                if (ord(substr($v_value, $index, 1)) == 10) {//=10 la ma xuong dau dong
                    $v_value = str_replace(chr(10), "<br>", $v_value);
                }
            }
        }
        return $v_value;
    }

    function get_text_record_net_status($v_value)
    {
        global $arr_status_of_record_net;
        for ($i = 0; $i < sizeof($arr_status_of_record_net); $i++) {
            if ($arr_status_of_record_net[$i]['0'] == $v_value) {
                return $arr_status_of_record_net[$i]['1'];
            }
        }
        return "";
    }

    public function eCSDeleteFileUpload($filename)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec Net_deleteFileUpload  ";
        $sql .= "'" . $filename . "'";
        //echo $sql ; exit();
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrResult = $objConn->adodbExecSqlString($sql);

        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
        //var_dump($arrResult) . 'du lieu ket xuat tu database';
    }

    /**
     * Creater: KHOINV
     * Date: 09/07/2011
     * Des: Kiem qua quyen tiep nhan cua nguoi su dung
     * @param unknown_type $sStaffId Id nguoi dang nhap hien thoi
     * @param unknown_type $sRoll quyen can kiem tra
     */
    public function eCSPermisstionForRecordType($sStaffId, $sRoll)
    {
        $objConn = new  Extra_Db();
        $sql = "Exec EfyLib_GetStaffPermission ";
        $sql = $sql . "'" . $sStaffId . "'";
        $sql = $sql . ",'" . $sRoll . "'";
        //echo $sql;exit;
        try {
            $arrNetRecordType = $objConn->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        if ($arrNetRecordType['Roll'] == 0) return false;
        else return true;
    }

    public function eCSHandleWorkGetAll($sRecordPk, $sOwnerCode)
    {
        $objConn = new  Extra_Db();
        $psSql = "Exec eCS_HandleWorkGetAll ";
        $psSql .= "'" . $sRecordPk . "'";
        $psSql .= ",'" . $sOwnerCode . "'";
        //echo  "<br>eCSHandleWorkGetAll:". $psSql . "<br>";
        //exit;
        try {
            $arrResult = $objConn->adodbQueryDataInNameMode($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function fGetRecordTypeList($sWorkType, $sOwnerCode, $sDelimiter1 = ',')
    {
        $objConn = new  Extra_Db();
        $psSql = "Select dbo.f_GetRecordTypeList(";
        $psSql .= "'" . $sWorkType . "'";
        $psSql .= ",'" . $sOwnerCode . "'";
        $psSql .= ",'" . $sDelimiter1 . "')";
        //echo  "<br>". $psSql . "<br>";
        //exit;
        try {
            $arrResult = $objConn->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function eCSHandleWorkUpdate($arrParameter)
    {
        $objConn = new  Extra_Db();
        $psSql = "Exec eCS_HandleWorkUpdate  ";
        $psSql .= "'" . $arrParameter['PK_RECORD'] . "'";
        $psSql .= ",'" . $arrParameter['PK_RECORD_WORK'] . "'";
        $psSql .= ",'" . $arrParameter['C_OWNER_CODE'] . "'";
        $psSql .= ",'" . $arrParameter['C_WORK_DATE'] . "'";
        $psSql .= ",'" . $arrParameter['C_WORKTYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_RESULT'] . "'";
        $psSql .= ",'" . $arrParameter['FK_STAFF'] . "'";
        $psSql .= ",'" . $arrParameter['C_POSITION_NAME'] . "'";
        $psSql .= ",'" . $arrParameter['C_DOC_TYPE'] . "'";
        $psSql .= ",'" . $arrParameter['C_FILE'] . "'";
        //Thuc thi lenh SQL
        //echo $psSql; exit;
        try {
            $arrResult = $objConn->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function eCSHandleWorkGetSingle($sRecordWorkPk)
    {
        $objConn = new  Extra_Db();
        $psSql = "Exec eCS_HandleWorkGetSingle ";
        $psSql .= "'" . $sRecordWorkPk . "'";
        //echo  "<br>". $sql . "<br>";
        //exit;
        try {
            $arrResult = $objConn->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    public function eCSHandleWorkDelete($sRecordWorkIdList)
    {
        $objConn = new  Extra_Db();
        // Bien luu trang thai
        $sql = "Exec eCS_HandleWorkDelete '" . $sRecordWorkIdList . "'";
        //echo $sql;exit;
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrTempResult = $objConn->adodbExecSqlString($sql);
            $Result = $arrTempResult['RET_ERROR'];
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $Result;
    }

    public function fRecordTypeListByCode($sWorkType, $sRecordTypePk, $sOwnerCode, $sDelimiter1 = ',', $sDelimiter2 = '!&@!')
    {
        $objConn = new  Extra_Db();
        $psSql = "Select dbo.f_RecordTypeListByCode(";
        $psSql .= "'" . $sWorkType . "'";
        $psSql .= ",'" . $sRecordTypePk . "'";
        $psSql .= ",'" . $sOwnerCode . "'";
        $psSql .= ",'" . $sDelimiter1 . "'";
        $psSql .= ",'" . $sDelimiter2 . "')";
        //echo  "<br>". $psSql . "<br>";
        //exit;
        try {
            $arrResult = $objConn->adodbExecSqlString($psSql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

// Ham goi WEBSERVICE gui hs Submit form lan dau cho RTA
    public function SubmitInstance($type, $status, $minor_code, $apply_type, $creator_name, $creator_address, $creator_phone, $applicant_name, $applicant_age, $applicant_ethinic, $applicant_sex, $applicant_phone, $procedure_type, $procedure_details, $case_apply_date, $appointment_returned_date, $sent_processed_date, $received_process_date, $description)
    {
        switch ($type) {
            case "0101_DM_LHS_01";
                $type = '0';
                break;
            case "0101_DM_LHS_02";
                $type = '1';
                break;
            case "0101_DM_LHS_03";
                $type = '2';
                break;
            case "0101_DM_LHS_04";
                $type = '3';
                break;
        }
        switch ($status) {
            case "10";
                $status = '0';
                break;
            case "51";
                $status = '2';
        }
        switch ($apply_type) {
            case "0101_DM_C01";
                $apply_type = '0';
                break;
            case "0101_DM_C02";
                $apply_type = '1';
                break;
            case "0101_DM_C03";
                $apply_type = '3';
                break;
            case "0101_DM_C04";
                $apply_type = '2';
                break;
        }
        switch ($applicant_sex) {
            case "NAM";
                $applicant_sex = '0';
                break;
            case "NU";
                $applicant_sex = '1';
        }
        $arrdata = array(
            "type" => $type,
            "status" => $status,
            "minor_code" => $minor_code,
            "apply_type" => $apply_type,
            "creator_name" => $creator_name,
            "creator_address" => $creator_address,
            "creator_phone" => $applicant_phone,
            "applicant_name" => $applicant_name,
            "applicant_age" => $applicant_age,
            "applicant_ethinic" => $applicant_ethinic,
            "applicant_sex" => $applicant_sex,
            "applicant_phone" => $applicant_phone,
            "procedure_type" => $procedure_type,
            "procedure_details" => $procedure_details,
            "case_apply_date" => $case_apply_date,
            "appointment_returned_date" => $appointment_returned_date,
            "sent_processed_date" => Null,
            "received_process_date" => Null,
            "description" => $description
        );
        //Var_dump($arrdata);exit;
        $arr = array(
            "name" => "mscoreqb_ws_test",
            "token" => "g0lE06Ten3Dj3Gj7STb0sOQ7p617h6Tf",
            "data" => $arrdata
        );
        $content = json_encode($arr);
        //var_dump($content);exit;
        $url = "http://mscoreqb.rta.vn/mscore/submitInstance";
        $curl = curl_init($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($json_response, true);
        //var_dump($response);exit;
        return $response;
    }

// Ham goi WEBSERVICE  update ket qua hs RTA khi can bo tiep nhan tra KQ cho dan
    public function UpdateInstance($uuid, $status, $sent_processed_date, $received_process_date, $case_return_actual_date)
    {
        switch ($status) {
            case "10";
                $status = '0';
                break;
            case "51";
                $status = '2';
        }

        $arrdata = array(
            "uuid" => $uuid,
            "status" => $status,
            "sent_processed_date" => $sent_processed_date,
            "received_process_date" => $received_process_date,
            "case_return_actual_date" => $case_return_actual_date
        );
        //Var_dump($arrdata);exit;
        $arr = array(
            "name" => "mscoreqb_ws_test",
            "token" => "g0lE06Ten3Dj3Gj7STb0sOQ7p617h6Tf",
            "data" => $arrdata
        );
        $content = json_encode($arr);
        $url = "http://mscoreqb.rta.vn/mscore/updateInstance";
        $curl = curl_init($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($json_response, true);
        //var_dump($response);exit;
        return $response;
    }

// Ham goi WEBSERVICE  update ket qua hs RTA khi can bo tiep nhan sua lai thong tin hs
    public function UpdateInstanceedit($Newid, $apply_type, $creator_name, $creator_address, $creator_phone, $applicant_name, $applicant_age, $applicant_ethinic, $applicant_sex, $applicant_phone, $description)
    {
        switch ($apply_type) {
            case "0101_DM_C01";
                $apply_type = '0';
                break;
            case "0101_DM_C02";
                $apply_type = '1';
                break;
            case "0101_DM_C03";
                $apply_type = '3';
                break;
            case "0101_DM_C04";
                $apply_type = '2';
                break;
        }
        switch ($applicant_sex) {
            case "NAM";
                $applicant_sex = '0';
                break;
            case "NU";
                $applicant_sex = '1';
        }
        $arrdata = array(
            "uuid" => $Newid,
            "apply_type" => $apply_type,
            "creator_name" => $creator_name,
            "creator_address" => $creator_address,
            "creator_phone" => $applicant_phone,
            "applicant_name" => $applicant_name,
            "applicant_age" => $applicant_age,
            "applicant_ethinic" => $applicant_ethinic,
            "applicant_sex" => $applicant_sex,
            "applicant_phone" => $applicant_phone,
            "description" => $description
        );
        //Var_dump($arrdata);exit;
        $arr = array(
            "name" => "mscoreqb_ws_test",
            "token" => "g0lE06Ten3Dj3Gj7STb0sOQ7p617h6Tf",
            "data" => $arrdata
        );
        $content = json_encode($arr);
        $url = "http://mscoreqb.rta.vn/mscore/updateInstance";
        $curl = curl_init($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($json_response, true);
        //var_dump($response);exit;
        return $response;
    }

// Ham goi WEBSERVICE  update ket qua hs RTA khi can bo tiep nhan bo sung thong tin hs thieu
    public function _UpdateInstanceedit($Newid, $creator_name, $creator_address, $applicant_name, $applicant_ethinic, $new_appointment_returned_date)
    {
        $arrdata = array(
            "uuid" => $Newid,
            "creator_name" => $creator_name,
            "creator_address" => $creator_address,
            "applicant_name" => $applicant_name,
            "applicant_ethinic" => $applicant_ethinic,
            "new_appointment_returned_date" => $new_appointment_returned_date
        );
        //Var_dump($arrdata);exit;
        $arr = array(
            "name" => "mscoreqb_ws_test",
            "token" => "g0lE06Ten3Dj3Gj7STb0sOQ7p617h6Tf",
            "data" => $arrdata
        );
        $content = json_encode($arr);
        $url = "http://mscoreqb.rta.vn/mscore/updateInstance";
        $curl = curl_init($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($json_response, true);
        //var_dump($response);exit;
        return $response;
    }
}

// Su dung ADODB de thuc thi cau lenh Query du lieu tu CSDL
// va tra lai array voi cac phan tu duoc truy nhap qua TEN
function _adodb_query_data_in_name_mode($p_sql)
{
    global $ado_conn;
    if (_is_sqlserver()) {
        $ado_conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $arr_all_data = $ado_conn->GetAll($p_sql);
    }
    return $arr_all_data;
}

function _is_sqlserver()
{
    global $_EFY_DB_TYPE;
    return ($_EFY_DB_TYPE == "SQL SEVER");
}

//Lay ten hinh thuc dang ky kinh doanh
function _getNameByCode0101($p_id)
{
    $p_array = $this->getAllObjectbyListCode('', '0101_DM_HT');
    foreach ($p_array as $array) {
        if ($array['C_CODE'] == $p_id) {
            return $array['C_NAME'];
            break;
        }
    }
    return "";
}

?>