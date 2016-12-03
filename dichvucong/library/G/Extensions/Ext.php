<?php

/**
 * @author :TRUONGDV
 * @since : 13/06/2013
 * @see : Lop chua cac phuong thuc dung chung cho toan bo cac mau in, bao cao,dinh nghia cac ham convert du lieu
 */
class G_Extensions_Ext
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
     * Creater :TRUONGDV
     * Date : 09/11/2012
     * Idea : Lấy tên phòng ban
     */
    public function getUnitNameByPk($arrInput)
    {
        $PkUnit = $arrInput['0'];
        $db = new G_Db();
        $sql = "Exec CBCC_getUnitNameByPK";
        $sql .= "'" . $PkUnit . "'";
        $arrResult = $db->adodbExecSqlString($sql);
        if (isset($arrResult['sName']))
            return $arrResult['sName'];

        return '';
    }

    /**
     * Creater :TRUONGDV
     * Date : 09/11/2012
     * Idea : Convert list code sang list name
     */
    public function convertListCodeToName($arrInput)
    {
        $objCache = new G_Cache();
        $codeList = (string)$arrInput['0'];
        $listType = (string)$arrInput['1'];
        $array = $objCache->getAllObjectbyListCode($listType, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $arrListCode = explode(',', $codeList);
        $listName = '';
        if (is_array($arrListCode)) {
            foreach ($arrListCode as $key => $value) {
                $listName .= $this->convertCodeToNameByArray($value, $array) . ', ';
            }
            $listName = substr($listName, 0, strlen($listName) - 2);
        }
        return $listName;
    }

    /**
     * Idea : Convert ngay trong db sang dinh dang dd/mm/yyyy
     */
    public function convertDateFormatDDMMYYYY($arrInput)
    {
        if(is_array($arrInput) == true)
            return date('d/m/Y', strtotime($arrInput['0']));
        else
            return date('d/m/Y', strtotime($arrInput));
    }

    public function convertDateFormatDDMMYYYYHHMMSS($arrInput)
    {
        if (is_array($arrInput))
            return date('d/m/Y H:i:s', strtotime($arrInput['0']));
        else
            return date('d/m/Y H:i:s', strtotime($arrInput));
    }

    public function convertStaffIdToDepartmentName($arrInput)
    {
        $unitId = G_Lib::_getValuesByIds($_SESSION['arr_all_staff'], $arrInput['0'], 'unit_id');
        $unitName = G_Lib::_getValuesByIds($_SESSION['arr_all_unit'], $unitId, 'name');
        return $unitName;

    }

    public function convertStaffIdToStaffName($arrInput)
    {
        // echo $arrInput;die();
        $unitName = G_Lib::_getValuesByIds($_SESSION['arr_all_staff'], $arrInput['0'], 'namePosition');
        return $unitName;

    }

    // Hàm tính tổng số cột
    public function sumcolumn($arrInput)
    {
        $value_column = 0;
        if (is_array($arrInput)) {
            foreach ($arrInput as $key => $value) {
                $value_column += (int)$value;
            }
        }
        return $value_column;
    }

    // Convert sCode=>sName từ 1 mảng dữ liêu
    public function convertCodeToNameByArray($code, $array)
    {
        $countR = sizeof($array);
        for ($i = 0; $i < $countR; $i++) {
            if ($array[$i]['sCode'] == $code) {
                return $array[$i]['sName'];
            }
        }
        return '';
    }
    /**
     * @param $sRecordID
     * @param $sKeyAttach
     * @param int $fromcenter
     * @return array
     * @throws Zend_Exception
     */
    public function _getFileSaved($sRecordID, $sKeyAttach, $fromcenter = 0)
    {
        $arrResult = array();
        $dbConnect = new G_Db();
        if ($fromcenter) {
            $sysConst = Zend_Registry::get('__sysConst__');
            $dbCenter = $sysConst->dbCenter;
            $sql = 'Exec [' . $dbCenter . '].[dbo].sp_SysFileGetSingleList  ';
        } else {
            $sql = "Exec sp_SysFileGetSingleList  ";
        }
        $sql .= "'" . $sRecordID . "'";
        $sql .= ",'" . $sKeyAttach . "'";
        try {
            $arrResult = $dbConnect->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
    }

    /**
     * @param $arrInput
     * @return string
     * @throws Zend_Exception
     */
    public function contensumfile($arrInput)
    {
        //Tao doi tuong thong tin config
        $globals = new G_Global();
        $sysConst = Zend_Registry::get('__sysConst__');
        $content = $arrInput[0];
        $pkrecord = $arrInput[1];
        $doctype = (isset($arrInput[2]) ? (string)$arrInput[2] : 'HO_SO');
        $fromcenter = (isset($arrInput[3]) ? (string)$arrInput[3] : 0);
        $arrFileList = $this->_getFileSaved($pkrecord, $doctype, $fromcenter);

        $strHTML = '';
        // $attachfile = '<img class="iconatt" src="' . $globals->sitePath . $globals->dirImage . 'icon_attach.gif" />';
        $strHTML .= '<div class="listAttach" style="margin-left:6px;"><ul>';
        if ($arrFileList) {
            foreach ($arrFileList as $key => $value) {
                if ($sysConst->fileserverStatus) {
                    $file = $value['sFileName'];
                    $arrFilename = explode('!~!', $file);
                    $sActionUrl = $globals->openfilePath . $arrFilename[0] . '/' . $arrFilename[1];
                    $file_name = $arrFilename[1];
                } else {
                    $file = $value['sFileName'];
                    $arrFilename = explode('!~!', $file);
                    $file_name = array_pop($arrFilename);
                    $sActionUrl = $globals->sitePath.'mofile?id='.$file;
                }
                $strHTML .= '<li>';
                $strHTML .= '<a alt="" class="'. G_Gen::getClassIcon($file_name).'" onclick="window.open(\'' . $sActionUrl . '\');">' . $file_name . '</a>';
                $strHTML .= '</li>';
            }
        }
        $strHTML .= '</ul>';
        $strHTML .= '</div>';
        $strHTML = $content . $strHTML;
        return $strHTML;
    }

    // Ham lay thong tin file dinh kem theo loai don
    public function _convertListFile($arrInput)
    {
        $listfile = $arrInput[0];
        $typelist = (string)$arrInput[1];
        $replace = (string)$arrInput[2];
        $arrList = G_Cache::getInstance()->getAllObjectbyListCode($typelist, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $arrFile = explode(',', $listfile);
        $arrTemp = array();
        $arrDocx = array();
        if (!empty($arrFile)) {
            foreach ($arrFile as $key => $codefile) {
                $arrTemp['#' . $replace . '#'] = $this->convertCodeToNameByArray($codefile, $arrList);
                array_push($arrDocx, $arrTemp);
            }
        }
        return $arrDocx;
    }

    /**
     * Creater :QUYENNX
     * Date : 04/06/2013
     * Idea : Lấy nam hien tai
     */
    public function currentYear()
    {
        return date("Y");
    }

    // ham lay gia tri chuc vu 
    public function _getcontentposition($arrInput)
    {
        $typelist = (string)$arrInput[0];
        $value = (string)$arrInput[1];
        $replace = (string)$arrInput[2];
        $name = '';
        $arrList = G_Cache::getInstance()->getAllObjectbyListCode($typelist, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        if ($arrList)
            foreach ($arrList as $key => $arr) {
                if ($arr['sCode'] === $value) {
                    $name = $arr['note_list'];
                    break;
                }
            }
        $arrDocx = array();
        if ($name != '') {
            $arrName = explode('#', $name);
            foreach ($arrName as $key => $arr) {
                array_push($arrDocx, array('#' . $replace . '#' => $arr));
            }
        }
        // var_dump($arrName);die();
        return $arrDocx;
    }

    // ham lay ho ten nguoi ky trong danh muc
    public function _getNamepostion($arrInput)
    {
        $typelist = (string)$arrInput[0];
        $value = (string)$arrInput[1];
        $replace = (string)$arrInput[2];
        $name = '';
        $arrList = G_Cache::getInstance()->getAllObjectbyListCode($typelist, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        if ($arrList) {
            foreach ($arrList as $key => $arr) {
                if ($arr['sCode'] === $value) {
                    $name = $arr['sName'];
                    break;
                }
            }
        }
        return $name;
    }

    //ham lay ten dang nhap hien thoi 
    public function _getcurrentNameLogin()
    {
        return Zend_Auth::getInstance()->getIdentity()->sName;
    }

    public function convertSessionOwnerCodeToName($arrInput)
    {
        $code = (string)$arrInput['0'];
        $listType = (string)$arrInput['1'];
        $code = $_SESSION[$code];
        $array = G_Cache::getInstance()->getAllObjectbyListCode($listType, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $name = $this->convertCodeToNameByArray($code, $array);
        $name = G_Convert::Lower2Upper($name);
        return $name;
    }

    /*
     * Nguoi tao: tamtd
     * Ngay tao: 13/06/2013
     * Y nghia:Lay sName tuong ung tu sCode trong bang SysList
     */
    public function _convertCodeToName($sarrCode)
    {
        $sCode = $sarrCode['0'];
        // Tao doi tuong xu ly du lieu
        $objConn = new G_Db();
        $sql = "sp_SysConvertCodeToName ";
        $sql = $sql . " '" . $sCode . "'";
        //echo $sql . '<br>';exit;
        $arrResult = $objConn->adodbExecSqlString($sql);
        if (isset($arrResult['sName']))
            return $arrResult['sName'];

        return '';
    }

    public function contensumfilebyid($arrInput)
    {
        //Tao doi tuong thong tin config
        $objConfig = new G_Global();
        $content = $arrInput[0];
        $arrRes = explode('#CONTENT#', $content);
        $content = $arrRes[0];
        $strHTML = '';
        $attachfile = '<img class="iconatt" src="' . $objConfig->dirImage . '/icon_attach.gif" />';
        $strHTML .= '<div class="listAttach" style="margin-left:6px;"><ul>';
        if ($arrRes[1] != '') {
            $arrfile = explode(',', $arrRes[1]);
            // var_dump($arrfile);die();
            for ($i = 0; $i < sizeof($arrfile); $i++) {
                $arrFilename = explode('!~!', $arrfile[$i]);
                $file_name = $arrFilename[1];
                $file_id = explode("_", $arrFilename[0]);
                //Get URL
                $sActionUrl = $objConfig->dirSaveFile . $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2] . "/" . $arrfile[$i];
                $strHTML .= '<li>';
                $strHTML .= $attachfile . '<a alt="" href="' . $sActionUrl . '">' . $file_name . '</a>';
                $strHTML .= '</li>';
            }
        }
        $strHTML .= '</ul>';
        $strHTML .= '</div>';
        $strHTML = $content . $strHTML;
        return $strHTML;
    }

    public static function libFileGetSingle($sRecordID, $sKeyAttach)
    {
        $objConn = new G_Db();
        $sql = "Exec sp_SysFileGetSingleList  ";
        $sql .= "'" . $sRecordID . "'";
        $sql .= ",'" . $sKeyAttach . "'";
        // echo $sql ; exit();
        // thuc hien cap nhat du lieu vao csdl
        try {
            $arrResult = $objConn->adodbQueryDataInNameMode($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        return $arrResult;
        //var_dump($arrResult) . 'du lieu ket xuat tu database';
    }

    public function viewtempo($arrInput)
    {
        $label = (string)$arrInput[0];
        $functionjs = (string)$arrInput[1];
        // 
        $sHtml = '<label class="normal_label" onclick="' . $functionjs . '" style="cursor: pointer;color:#0000CC;padding-right:2%">' . $label . '</label>';
        // 
        return $sHtml;
    }

    // viewtempoByhsvv
    public function viewtempoByhsvv($arrInput)
    {
        $FkRecord = (string)$arrInput[0];
        $label = (string)$arrInput[1];
        $functionjs = (string)$arrInput[2];
        $functionjs = $functionjs . '(this,\'' . $FkRecord . '\')';
        // 
        $sHtml = '<label class="normal_label" onclick="' . $functionjs . '" style="cursor: pointer;">' . $label . '</label>';
        // 
        return $sHtml;
    }

    public function checkDataIsNull($arrInput)
    {
        $data = $arrInput['0'];
        $string = $arrInput['1'];
        if ($data != '') {
            $pos = strpos($string, $data);
            if ($pos === false)
                return $data;
            else return '';
        } else return '';

    }

    public function _getLimitTranfer($arrInput)
    {
        $dDateEnd = (string)$arrInput[0];
        if ($dDateEnd == '') {
            return '';
        }
        // return '';
        $dDateStart = date('Y/m/d H:i:s');
        $dDateEnd = date('Y/m/d H:i:s', strtotime($dDateEnd));
        $iTotalTimeRestProcess = G_Date::getInstance()->_diffdate($dDateStart, $dDateEnd);
        $sHtml = '';
        $sHtml .= G_Convert::getInstance()->_yyyymmddToDDmmyyyyhhmm($dDateEnd);
        $sHtml .= '<br>';
        if ($iTotalTimeRestProcess > 0) {
            // Con thoi gian xu ly
            $sHtml .= '<font style="color:blue;">(Còn lại' . $this->_convertTimeToddhhmm($iTotalTimeRestProcess) . ')</font>';
        } else {
            $iTotalTimeRestProcess = -1.0 * $iTotalTimeRestProcess;
            $sHtml .= '<font style="color:red;">(Quá hạn' . $this->_convertTimeToddhhmm($iTotalTimeRestProcess) . ')</font>';
        }
        return $sHtml;
    }

    /*
        Don vi phut
    */
    public function getAlertApointedDate($arrInput)
    {
        $objDate = new G_Date();
        $objConvert = new G_Convert();
        if (is_array($arrInput)) {
            $arrDate = explode('#', $arrInput['0']);
            $appointedDate = $arrDate['0'];
        } else {
            $arrDate = explode('#', $arrInput);
            $appointedDate = $arrDate['0'];
        }
        $signApproveDate = (isset($arrDate['1']) ? $arrDate['1'] : '');
        $currentDate = date('Y-m-d H:i:s');
        $htmlResult = '';
        if (($appointedDate != '') && ($appointedDate != '1900-01-01 00:00:00')) {
            if ($signApproveDate == '') {
                $totalminute = $objDate->_diffdate($currentDate, $appointedDate);
                $day = $this->_convertTimeToddhhmm($totalminute);
                $appointedDate = $objConvert->_yyyymmddToDDmmyyyy($appointedDate);
                if ($totalminute > 0)
                    $htmlResult = $appointedDate . '<br><font style="color:blue;">(Còn lại ' . $day . ').';
                else if ($totalminute < 0)
                    $htmlResult = $appointedDate . '<br><font style="color:red;">(Đã quá hạn ' . $day . ').';
                else
                    $htmlResult = $appointedDate . '<br><font style="color:blue;">(Hôm nay là hạn xử lý).';
            } else {
                $totalminute = $objDate->_diffdate($signApproveDate, $appointedDate);
                $day = $this->_convertTimeToddhhmm($totalminute);
                $appointedDate = $objConvert->_yyyymmddToDDmmyyyy($appointedDate);
                if ($totalminute > 0)
                    $htmlResult = $appointedDate . '<br><font style="color:blue;">(Xử lý trước hạn ' . $day . ').';
                else if ($totalminute < 0)
                    $htmlResult = $appointedDate . '<br><font style="color:red;">(Xử lý quá hạn ' . $day . ').';
                else
                    $htmlResult = $appointedDate . '<br><font style="color:blue;">(Đã xử lý).';
            }
        }
        return $htmlResult;
    }

    public function _getAppointedProcessed($arrInput)
    {
        $objConvert = new G_Convert();
        $objDate = new G_Date();
        $sTime = (int)$arrInput[0];
        $iNumberDay = (int)$arrInput[1];
        $dDateEnd = (string)$arrInput[2];
        if ($sTime == 0) {
            // Chua xu ly
            return $this->_getLimitTranfer(array($dDateEnd));
        }
        
        $sysConst = Zend_Registry::get('__sysConst__');
        $am_start_time = $sysConst->am_start_time;
        $am_stop_time = $sysConst->am_stop_time;
        $pm_start_time = $sysConst->pm_start_time;
        $pm_stop_time = $sysConst->pm_stop_time;
        // Thoi gian tinh theo don vi phut
        $work_time = ($pm_stop_time - $pm_start_time + $am_stop_time - $am_start_time) * 60;

        $sHtml = '';
        $sHtml .= $objConvert->_yyyymmddToDDmmyyyyhhmm($dDateEnd);
        $sHtml .= '<br>';
        $sTime = $iNumberDay * $work_time - $sTime;
        if ($sTime == 0) {
            $sHtml .= '<font style="color:blue;">Đúng tiến độ</font>';
        } elseif ($sTime > 0) {
            // Con thoi gian xu ly
            $sHtml .= '<font style="color:blue;">(Xử lý trước hạn' . $objDate->_convertTimeToddhhmm($sTime) . ')</font>';
        } else {
            $sTime = -1.0 * $sTime;
            $sHtml .= '<font style="color:red;">(Xử lý quá hạn' . $objDate->_convertTimeToddhhmm($sTime) . ')</font>';
        }
        return $sHtml;
    }

    public function _getTotalTimeProcessed($arrInput)
    {
        $objDate = new G_Date();
        $sTime = (int)$arrInput[0];
        $iNumberDay = (int)$arrInput[1];
        if ($sTime == 0) {
            return '';
        }
        $sysConst = Zend_Registry::get('__sysConst__');
        $am_start_time = $sysConst->am_start_time;
        $am_stop_time = $sysConst->am_stop_time;
        $pm_start_time = $sysConst->pm_start_time;
        $pm_stop_time = $sysConst->pm_stop_time;
        // Thoi gian tinh theo don vi phut
        $work_time = ($pm_stop_time - $pm_start_time + $am_stop_time - $am_start_time) * 60;
        $dDay = round($sTime / $work_time, 2);
        $sHtml = '';
        $sHtml .= $dDay;
        $sHtml .= '<br>';
        $sTime = $iNumberDay * $work_time - $sTime;
        if ($sTime == 0) {
            $sHtml .= '<font style="color:blue;">Đúng tiến độ</font>';
        } elseif ($sTime > 0) {
            // Con thoi gian xu ly
            $sHtml .= '<font style="color:blue;">(Còn lại' . $objDate->_convertTimeToddhhmm($sTime) . ')</font>';
        } else {
            $sTime = -1.0 * $sTime;
            $sHtml .= '<font style="color:red;">(Quá hạn' . $objDate->_convertTimeToddhhmm($sTime) . ')</font>';
        }
        return $sHtml;
    }

    // Luu thong tin xu ly giai doan voi truong hop ban giao nhieu ho so
    public function saveTimeLimitList($arrRequest)
    {
        $dbConnect = new G_Db();
        $objLib = new G_Lib();
        $objFlow = new G_Flows();
        $objDate = new G_Date();
        $sTaskID = (isset($arrRequest['hdn_FkTaskWF_current']) ? $arrRequest['hdn_FkTaskWF_current'] : '');
        $pkrecordlist = (isset($arrRequest['PkRecord']) ? $arrRequest['PkRecord'] : '');
        // Kiem tra la buoc ban giao hay phan cong
        $arrAllCheckAndGetInfor = $dbConnect->_querySql(array('uTaskID' => $sTaskID, 'uFkRecord' => $pkrecordlist), 'sp_KntcListCheckAndGetInforByTask', true, false);
        $iCount = (isset($arrAllCheckAndGetInfor[0]['C_COUNT']) ? $arrAllCheckAndGetInfor[0]['C_COUNT'] : 0);
        if ($iCount > 0) {
            $dDateStart = date("Y-m-d H:i:s");
            $objCache = new G_Cache();
            $currentStaffID = Zend_Auth::getInstance()->getIdentity()->PkStaff;
            $arr_all_staff = $objCache->getAllStaff();
            $arr_all_unit = $objCache->getAllUnit();
            foreach ($arrAllCheckAndGetInfor as $key => $arrCheckAndGetInfor) {
                $pkrecord = $arrCheckAndGetInfor['FkRecord'];
                // Update vao
                if (!is_null($arrCheckAndGetInfor['FkDepartmentId'])) {
                    $arrInforLimit = $dbConnect->_querySql(array('uTaskID' => $arrCheckAndGetInfor['PkTaskByDepartment'], 'pkrecord' => $pkrecord), 'sp_SysGetTimeLimitByTask', false, false);
                    $iNumberDay = $objFlow->getTimeLimitByPetition($arrInforLimit['sPetitionType'], $arrInforLimit['sNature'], $arrInforLimit['iNumberDay']);
                    $uFkDepartmentNext = $arrCheckAndGetInfor['FkDepartmentId'];
                    $sNextStaffID = $arrCheckAndGetInfor['FkStaffOfTDepartment'];
                    $sNextStaffName = $objLib->_getValuesByIds($arr_all_staff, $sNextStaffID, 'namePosition');
                    $sDepartmentName = $objLib->_getValuesByIds($arr_all_unit, $uFkDepartmentNext, 'name');
                    $uFkTask = $arrInforLimit['FkTaskWF'];
                }
                if (!is_null($arrCheckAndGetInfor['FkSolveDepartment'])) {
                    $arrInforLimit = $dbConnect->_querySql(array('uTaskID' => $arrCheckAndGetInfor['PkTaskByStaff'], 'pkrecord' => $pkrecord), 'sp_SysGetTimeLimitByTask', false, false);
                    $iNumberDay = $objFlow->getTimeLimitByPetition($arrInforLimit['sPetitionType'], $arrInforLimit['sNature'], $arrInforLimit['iNumberDay']);
                    $uFkDepartmentNext = $arrCheckAndGetInfor['FkSolveDepartment'];
                    $sNextStaffID = $arrCheckAndGetInfor['FkStaffOfTStaff'];
                    $sNextStaffName = $objLib->_getValuesByIds($arr_all_staff, $sNextStaffID, 'namePosition');
                    $sDepartmentName = $objLib->_getValuesByIds($arr_all_unit, $uFkDepartmentNext, 'name');
                    $uFkTask = $arrInforLimit['FkTaskWF'];
                }
                //Update cho can bo xu ly tiep theo
                $arrInput = array(
                    'uFkTask' => $uFkTask,
                    'uFkRecord' => $pkrecord,
                    'uFkStaff' => $sNextStaffID,
                    'uFkDepartment' => $uFkDepartmentNext,
                    'sStaffName' => $sNextStaffName, //Ten - Chuc vu can bo thuc hien tiep theo
                    'sDepartmentName' => $sDepartmentName,
                    'iNumberDay' => $iNumberDay,
                    'dDateStart' => $dDateStart,
                    'dDateEnd' => $objDate->get_appointed_date($dDateStart, $iNumberDay),
                    'dDateSend' => '',
                    'iTotalDayProcess' => '',
                    'sNote' => '',
                );
                // Update thoi gian xu ly cho buoc tiep theo
                $dbConnect->_querySql($arrInput, 'sp_KntcProcessTimeRecordUpdate', false, false);
                $arrGetSingle = $dbConnect->_querySql(array(
                    'uFkTask' => $sTaskID,
                    'uFkRecord' => $pkrecord,
                    'uFkStaff' => $currentStaffID,
                ), 'sp_KntcProcessTimeRecordGetSingle', false, false);
                if ($arrGetSingle) {
                    $objFlow->updateTimeSendRecord($arrGetSingle);
                }
            }
        }
    }

    public function _setOwnerDepartmentName()
    {
        return 'VĂN PHÒNG';
    }

    //Author: TungLX
    public function _setPetitionType($arrInput)
    {
        switch ($arrInput[0]) {
            case 'KHIEU_NAI_L1':
                return 'KHIẾU NẠI';
                break;
            case 'KHIEU_NAI_L1':
                return 'KHIẾU NẠI';
                break;
            case 'TO_CAO_L1':
                return 'TỐ CÁO';
                break;
            case 'TO_CAO_L1':
                return 'TỐ CÁO';
                break;
            default:
                return 'KIẾN NGHỊ PHẢN ÁNH';
                break;
        }
    }

    /*
        edit: ngay 17/08/2013
        Convert từ code sang name;
     */

    public function convertCodeToNameFromDonvi($arrInput)
    {
        $code = (string)$arrInput['0'];
        $listType = (string)$arrInput['1'];
        $optionconvert = (isset($arrInput['2']) ? (string)$arrInput['2'] : '');
        $array = G_Cache::getInstance()->getAllObjectbyListCode($listType, Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        $name = $this->convertCodeToLowerNameByArray($code, $array);
        if ($optionconvert == 1) {
            for ($i = 0; $i < sizeof($array); $i++) {
                if ($array[$i]['sCode'] == $code) {
                    $name = $array[$i]['ten_hoa'];
                }
            }
        } else if ($optionconvert == 2) {
            $name = mb_strtolower($name, 'UTF-8');
        } else if ($optionconvert == 3) {
            $name = mb_strtolower($name, 'UTF-8');
            $name = 'căn cứ Luật ' . $name . ', ';
            if ($code == 'KIEN_NGHI_PHAN_ANH')
                $name = '';
        } else if ($optionconvert == 4) {
            $name = mb_strtolower($name, 'UTF-8');
            $name = 'Căn cứ Luật ' . $name . ', ';
            if ($code == 'KIEN_NGHI_PHAN_ANH')
                $name = '';
        }
        return $name;
    }

    public function saveProcessForOwnerCode($sPath, $arrValue)
    {
        // Tao doi tuong
        $objxml = new G_Xml();
        $dbConnect = new G_Db();
        $objLib = new G_Lib();
        $objFlow = new G_Flows();
        $pkrecord = $arrValue['PkRecord'];
        $sqlgetsend = "Select dbo.fn_KntcGetSendOwnerCode('$pkrecord') As sSendOwnerCode";
        try {
            $arrResult = $dbConnect->adodbExecSqlString($sqlgetsend);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        $sSendOwnerCode = $arrResult['sSendOwnerCode'];
        $dbPrefix = Zend_Registry::get('__sysConst__')->_prefixDb;
        $dbLink = 'DBLink.[' . $dbPrefix . '-' . $sSendOwnerCode . ']';
        // Cau lenh sql update ho so cho don vi chuyen
        $sql = $objxml->getParamxml($sPath, "update_object/table_struct_of_update_form/update_row_list/update_row", "update_object/update_formfield_list", 'list_sql', $arrValue);
        $sql = $dbLink . ".dbo." . $sql;
        try {
            $arrResult = $dbConnect->adodbExecSqlString($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        };
        // Upload file dinh kem
        if ($arrResult) {
            $arrInput = array(
                'pkrecordlist' => $pkrecord
            , 'sDelimetor' => ','
            , 'sDocTypeList' => (isset($arrValue['sDocTypeList']) ? $arrValue['sDocTypeList'] : '')
            , 'sDelimitorDoctype' => (isset($arrValue['sDelimitor']) ? $arrValue['sDelimitor'] : '')
            );
            $arrResult = $dbConnect->_querySql($arrInput, 'sp_SysGetFileByRecordAndDocType', true, false);

            $this->putFileByOwnerCode($arrResult, $sSendOwnerCode);
        }
        // Update cho can bo xu ly tiep theo
        $sIdea = (isset($arrValue['wf_idea_content']) ? $arrValue['wf_idea_content'] : '');
        // Update thoi gian xu ly
        $arrResult = $dbConnect->_querySql(array('sModuleCode' => $arrValue['sModuleCode']
            , 'sControllerCode' => $arrValue['sControllerCode']
            , 'sActionCode' => $arrValue['sActionCode']
            )
            , $dbLink . '.dbo.sp_KntcGetTaskAndStaff', false, false);
        // echo  $arrResult; die();
        $uFkTask = $arrResult['FkTask'];
        $uFkStaff = $arrResult['sStaffDefault'];
        $arrInforLimit = $dbConnect->_querySql(array('uFkTask' => $uFkTask, 'pkRecord' => $pkrecord), $dbLink . '.dbo.sp_SysGetTimeLimitByTaskID', false, false);
        $iNumberDay = $objFlow->getTimeLimitByPetition($arrInforLimit['sPetitionType'], $arrInforLimit['sNature'], $arrInforLimit['iNumberDay']);
        $dDateStart = date("Y-m-d H:i:s");
        $objCache = new G_Cache();
        $arr_all_staff = $objCache->getAllStaff();
        $arr_all_unit = $objCache->getAllUnit();
        $uFkDepartment = $objLib->_getValuesByIds($arr_all_staff, $uFkStaff, 'unit_id');
        // Cap nhat thoi gian xu ly
        $arrInput = array(
            'uFkTask' => $arrInforLimit['FkTaskWF'],
            'uFkRecord' => $pkrecord,
            'uFkStaff' => $uFkStaff,
            'uFkDepartment' => $uFkDepartment,
            'sStaffName' => $objLib->_getValuesByIds($arr_all_staff, $uFkStaff, 'name'), //Ten - Chuc vu can bo thuc hien
            'sDepartmentName' => $objLib->_getValuesByIds($arr_all_unit, $uFkDepartment, 'name'),
            'iNumberDay' => $iNumberDay,
            'dDateStart' => $dDateStart,
            'dDateEnd' => G_Date::getInstance()->get_appointed_date($dDateStart, $iNumberDay),
            'dDateSend' => '',
            'iTotalDayProcess' => '',
            'sNote' => '',
        );
        $dbConnect->_querySql($arrInput, $dbLink . '.dbo.sp_KntcProcessTimeRecordUpdate', false, false);
        //die();
        $arrUpdate = array(
            'PkNextSolveStaff' => ''
        , 'pkrecord' => $pkrecord
        , 'uFkTask' => $uFkTask
        , 'uFkStaff' => $uFkStaff
        , 'StaffName' => ''
        , 'sRole' => 'XL_CHINH'
        , 'sCurrentStatusProcess' => $arrValue['sCurrentStatus']
        , 'iDetailStatusProcess' => $arrValue['sDetailtStatus']
        , 'dCurrentDate' => date('Y-m-d H:i:s')
        , 'sFkStaff' => Zend_Auth::getInstance()->getIdentity()->PkStaff
        , 'sIdea' => $sIdea
        , 'sDelimetor' => ','
        );

        $arrResult = $dbConnect->_querySql($arrUpdate, $dbLink . '.dbo.sp_KntcStaffNextStepUpdate', false, false);
        return true;
    }

    public function putFileByOwnerCode($arrFileList, $sSendOwnerCode)
    {
        Zend_Loader::loadClass('Zend_Ftp');
        $objConfig = new G_Global();
        $config = G_Global::_setFtpParameters($sSendOwnerCode);
        $ftp = new Zend_Ftp($config['host'], $config['user'], $config['password'], $config['port']);
        foreach ($arrFileList as $key => $value) {
            $file = $value['sFileName'];
            $arrFilename = explode('!~!', $file);
            $file_name = $arrFilename[1];
            $file_id = explode("_", $arrFilename[0]);
            //Get URL
            $sDirFile = $file_id[0] . "/" . $file_id[1] . "/" . $file_id[2];
            $sDirFull = $sDirFile . "/" . $file;
            $sPath = '..' . $objConfig->dirSaveFile . $sDirFull;
            $ftp->getCurrentDirectory()->makeDirectory($sDirFile);
            $ftp->getCurrentDirectory()->put($sPath, FTP_BINARY, $sDirFull);
        }
    }

    public function _convertTimeToddhhmm($sTime)
    {
        $sHtml = '';
        $sysConst = Zend_Registry::get('__sysConst__');
        $am_start_time = $sysConst->am_start_time;
        $am_stop_time = $sysConst->am_stop_time;
        $pm_start_time = $sysConst->pm_start_time;
        $pm_stop_time = $sysConst->pm_stop_time;
        // Thoi gian tinh theo don vi phut
        if ($sTime == 0) return '';
        $sTime = ($sTime < 0 ? $sTime * -1 : $sTime);
        $work_time = ($pm_stop_time - $pm_start_time + $am_stop_time - $am_start_time) * 60;
        $sDay = floor($sTime / $work_time);
        $sRestTime = $sTime - $sDay * $work_time;
        $sHours = floor($sRestTime / 60);
        $sMinutes = $sRestTime - $sHours * 60;
        if ($sDay > 0) {
            $sHtml .= ' ' . $sDay . ' ngày';
        }
        if ($sHours != 0 || $sMinutes != 0) {
            $sHtml .= ' ' . $sHours . ':' . $sMinutes . ':00';
        }
        return $sHtml;
    }

    public function viewduplicaterecord($arrInput)
    {
        // var_dump($arrInput);
        $label = (string)$arrInput[0];
        $functionjs = (string)$arrInput[1];
        $sHtml = '';
        // 
        if ($label > 0)
            $sHtml = '<label class="normal_label" onclick="' . $functionjs . '" style="cursor: pointer;padding-right:2%">' . $label . '</label>';
        // 
        return $sHtml;
    }

    /**
     * @author :TUNGLX
     * @since : 29/08/2013
     * @see : Hàm nối 2 chuỗi cấp gửi và nơi gửi
     */
    public function f_getResourcesTo($arrInput)
    {
        $c_group_to = $this->convertListCodeToName(array($arrInput['0'], $arrInput['1']));
        $c_resources_to = $this->convertListCodeToName(array($arrInput['2'], $arrInput['3']));
        $string = $c_group_to . '  ' . $c_resources_to;
        return trim($string);
    }

    /**
     * @author :TRUONGDV
     * @since : 04/09/2013
     * @see : Lay noi dung van bang tra loi
     */
    public function f_GetContentDocReply($arrInput)
    {
        $symbolNumber = (string)$arrInput[0]; //C_SYMBOL_NUMBER_REPLY
        $pulishedDate = (string)$arrInput[1]; //C_PUBLISHED_DATE
        $resultReply = (string)$arrInput[2]; //C_RESULT_REPLY
        $approvedLeader = (string)$arrInput[3]; //C_APPROVED_LEADER
        $FkRecord = (string)$arrInput[4]; //FkRecord
        $doctype = (string)$arrInput[5];//VB_TRA_LOI
        $string = '';
        if ($symbolNumber != '') {
            $string .= 'Số/Ký hiệu: ' . $symbolNumber;
        }
        if ($approvedLeader != '') {
            $string .= '<br><font style="font-weight:bold">Người ký: ' . $approvedLeader . '</font>';
            $string .= '<br>Ngày ký: ' . date('d/m/Y', strtotime($pulishedDate));
            $string .= '<br>Trích yếu: ' . $resultReply;
        } elseif ($resultReply != '' && $resultReply != '#wf_idea_content#') {
            $string .= '<br>Trích yếu: ' . $resultReply;
        }
        $string = $this->contensumfile(array($string, $FkRecord, $doctype));
        return $string;
    }

    public function viewTempoFromMeeting($arrInput)
    {
        $pkrecord = (string)$arrInput[0];
        $functionjs = (string)$arrInput[1];
        $functionjs = $functionjs . '(\'' . $pkrecord . '\')';
        $sHtml = '';
        // 
        $sHtml = '<label class="normal_label" onclick="' . $functionjs . '" style="color:blue;cursor: pointer;">Xem</label>';
        // 
        return $sHtml;
    }

    public function convertPeriodic($arrInput)
    {
        $month = (string)$arrInput[0];
        $year = (string)$arrInput[1];
        return $month . '/' . $year;
    }

    // Ham lay thong tin file dinh kem theo loai don
    public function insertthanhphanthamgia($arrInput)
    {
        $contens = (string)$arrInput[0];
        $replace = (string)$arrInput[1];

        $arrStaff = explode(chr(10), $contens);
        $arrTemp = array();
        $arrDocx = array();
        if (!empty($arrStaff)) {
            foreach ($arrStaff as $key => $staffname) {
                $arrTemp['#' . $replace . '#'] = $staffname;
                array_push($arrDocx, $arrTemp);
            }
        }
        return $arrDocx;
    }

    public function _getyearmeeting($arrInput)
    {
        return date("Y", strtotime($arrInput[0]));
    }

    public function getReceivedDate($arrInput)
    {
        $time = G_Convert::_ddmmyyyyToYYyymmdd($arrInput[0]);
        $time = strtotime($time);
        $day = date("d", $time);
        $month = date("m", $time);
        $year = date("o", $time);
        if ($arrInput[0] !== '01/01/1900')
            return 'Ngày ' . $day . " tháng " . $month . " năm " . $year;
        else
            return '';
    }

    public function lower_petition_type($arrInput)
    {
        return mb_strtolower($arrInput['0']);
    }
    //tao file, sua file, nen file, upload file, remove,cop,splip file

    //lay thong tin trong 1 mang
    //sap xep mang
    //tim kiem nhi phan

    //datime: mac dinh min, max, ktra dinh dang ngay
    // chuyen doi ngay tháng cac dinh dang ngay thang
    // xu ly tinh toan doi voi ngay thang
    function _createZip($files = array(), $destination = '', $overwrite = false)
    {
        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        //vars
        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            foreach ($files as $file) {
                //make sure the file exists
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if (count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach ($valid_files as $file) {
                $zip->addFile($file, $file);
            }
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

    public function _setFontTitleColExcell()
    {
        $arrInfor = array("name" => "Arial",
            "bold" => true,
            "size" => 6.5
        );
        return $arrInfor;
    }

    public function _setFontTitleColGroupExcell()
    {
        $arrInfor = array("name" => "Times New Roman",
            "bold" => true,
            "size" => 11
        );
        return $arrInfor;
    }

    // Lay tên thuong tu mang du lieu
    public function convertCodeToLowerNameByArray($code, $array)
    {
        $countR = sizeof($array);
        for ($i = 0; $i < $countR; $i++) {
            if ($array[$i]['sCode'] == $code) {
                return $array[$i]['ten_thuong'];
            }
        }
        return '';
    }

    //Xử lý Print mẫu biểu hình thức xử lý
    public function printClassify($pkrecord, $petitionType)
    {
        $dbConnect = new G_Db();
        $v_export_filename = '';
        //Lấy các hình thức xử lý đơn
        $arrSolveType = G_Cache::getInstance()->getAllObjectbyListCode('HINH_THUC_XL_DON');
        //Duyệt lấy mẫu biểu liên quan
        $codetemp = '';
        for ($index = 0; $index < sizeof($arrSolveType); $index++) {
            if (trim($arrSolveType[$index]['sCode']) == $petitionType) {
                $codetemp = trim($arrSolveType[$index]['mau_in']);
            }
        }
        //Lấy dữ liệu
        $arrIn = array(
            'pkrecord' => $pkrecord,
            'sOwnerCode' => Zend_Auth::getInstance()->getIdentity()->sOwnerCode
        );
        $arrData = $dbConnect->_querySql($arrIn, 'sp_KntcTuroialCitizen', false, false);
        // Mang du lieu dau vao
        $arrParameter = array(
            'sPathXmlFile' => './xml/report/' . $codetemp . '/' . $codetemp . '.xml',
            'sParrentTagName' => 'replace_list',
            'sPathTemplateFile' => './templates/report/' . $codetemp . '/',
            'sTemplateFile' => $codetemp,
            'reportType' => $codetemp,
            'TagName' => 'replace',
            'data' => $arrData,
            'optionprint' => 'COM'
        );
        $v_export_filename = G_Export::getInstance()->exportfileword($arrParameter);

        // $my_report_file = 'http://' . $_SERVER['HTTP_HOST'] . $this->_request->getBaseUrl() . '/io/export/' . $v_export_filename;
        return $v_export_filename;
    }

    public function printBM($pkrecord)
    {
        $dbConnect = new G_Db();
        $objExport = new G_Export();
        $getBaseUrl = G_Global::getInstance()->sitePath;
        $listcodetemp = '';
        $sql = "Select sSolveCompetentType, sPetitionType From KntcRecord Where PkRecord = '" . $pkrecord . "'";
        $competent_type = $dbConnect->adodbQueryDataInNameMode($sql);
        $arrListItem = G_Cache::getInstance()->getAllObjectbyListCodeFull('MAU_BIEU_THEO_HTXL', Zend_Auth::getInstance()->getIdentity()->sOwnerCode);
        for ($i = 0; $i < sizeof($arrListItem); $i++) {
            if ($competent_type['0']['sSolveCompetentType'] == $arrListItem[$i]['hinh_thuc_xu_ly'] && strpos($arrListItem[$i]['loai_don'], $competent_type['0']['sPetitionType']) !== false)
                $listcodetemp = $listcodetemp . '!~~!' . $arrListItem[$i]['sCode'];
        }
        if ($listcodetemp) {
            $listcodetemp_length = strlen($listcodetemp);
            $listcodetemp = substr($listcodetemp, '4', $listcodetemp_length);
            $kieuin = 'doc';
            $v_export_filename = '';
            // Lay du lieu

            $arrIn = array(
                'pkrecord' => $pkrecord,
                'sOwnerCode' => Zend_Auth::getInstance()->getIdentity()->sOwnerCode,
                'listcodetemp' => $listcodetemp,
                'sDelimitor' => '!~~!'
            );
            // var_dump($arrListItem);die();
            // echo $arrData = $dbConnect->_querySql($arrIn, 'sp_KntcTuroialCitizen', false, true); die();
            $arrData = $dbConnect->_querySql($arrIn, 'sp_KntcTuroialCitizen', true, false);
            // var_dump($arrData);die();
            $petitionType = array();
            for ($i = 0; $i < sizeof($arrData); $i++) {
                for ($j = 0; $j < sizeof($arrListItem); $j++) {
                    if ($arrData[$i]['CODE_TEMP'] == $arrListItem[$j]['sCode'])
                        $petitionType[$i] = $arrListItem[$j]['ten_hien_thi'];
                }
            }
            $arrParameter = array();
            // Mang du lieu dau vao
            for ($j = 0; $j < sizeof($arrData); $j++) {
                $arrParameter[$j] = array(
                    'sPathXmlFile' => G_Global::getInstance()->dirXml.'report/' . $arrData[$j]['CODE_TEMP'] . '/' . $arrData[$j]['CODE_TEMP'] . '.xml',
                    'sParrentTagName' => 'replace_list',
                    'sPathTemplateFile' => G_Global::getInstance()->dirTemReport. $arrData[$j]['CODE_TEMP'] . '/',
                    'sTemplateFile' => $arrData[$j]['CODE_TEMP'],
                    'reportType' => $arrData[$j]['CODE_TEMP'],
                    'TagName' => 'replace',
                    'data' => $arrData[$j],
                    'petitionType' => $petitionType[$j],
                );
            }
            switch ($kieuin) {
                case 'doc':
                    $v_export_filename = $objExport->exportfileword($arrParameter);
                    break;
                case 'excel':
                    $arrParameter['type'] = 'excel';
                    $v_export_filename = $objExport->createreportexcel($arrParameter);
                    break;
                case 'pdf':
                    $arrParameter['sTemplateFile'] = 'mau_giay_in_pdf';
                    $arrParameter['type'] = 'pdf';
                    $v_export_filename = $objExport->createreportexcel($arrParameter);
                    break;
                default:
                    break;
            }
            //$zipfilename = 'io/export/phan_loai.zip';
            for ($k = 0; $k < sizeof($v_export_filename); $k++) {
                # code...
                $v_export_filename[$k] = 'io/export/' . $v_export_filename[$k];
                $v_filename = 'http://' . $_SERVER['HTTP_HOST'] . $getBaseUrl . $v_export_filename[$k];
                if ($k != (sizeof($v_export_filename) - 1)) {
                    echo $v_filename . '!~!';
                } else {
                    echo $v_filename;
                }
            }
            //echo '!~!' . $getBaseUrl . '/' . $zipfilename;
            //$filezip = $this->create_zip($v_export_filename, $zipfilename, true);
        } else
            echo '';
    }

    public function alertStatusMeeting($arrInput)
    {
        if ($arrInput['0'] == 'TIEP_DAN_CHUYEN_XU_LY')
            return 'Đã chuyển xử lý';
        else if ($arrInput['0'] == 'KET_THUC_XU_LY')
            return 'Kết thúc xử lý';
        else
            return '';
    }

    public function printtrackletter($arrParameter)
    {
        //Lay params
        $sPathXmlFile = $arrParameter['sPathXmlFile'];
        $dirTemplate = $arrParameter['sPathTemplateFile'] . $arrParameter['sTemplateFile'] . '.docx';
        $data = $arrParameter['data'];
        //Tao doi tuong
        $ojbConfigXml = G_Xml::getInstance();
        $ojbConfigXml->__loadxml($sPathXmlFile);
        $phpdocx = new G_Components_PhpDocx($dirTemplate);
        $phpdocx->assignToHeader("#HEADER1#", "Header 1"); // basic field mapping to header
        $phpdocx->assignToFooter("#FOOTER1#", "Footer 1"); // basic field mapping to footer
        //Header
        $arrHeader = $ojbConfigXml->report_header->col->toArray();
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
                        $i = 0;
                        $arrParamFunc = array();
                        foreach ($arrParams as $key => $param) {
                            $param = trim($param);
                            if (isset($data[$param])) {
                                $arrParamFunc[] = $data[$param];
                            } else {
                                $arrParamFunc[] = $param;
                            }
                            $i++;
                        }
                        $objClass = new $pClassname;
                        $value = $objClass->$phpFunction($arrParamFunc);
                        break;

                    default:
                        $value = (isset($data[$field_column]) ? $data[$field_column] : '');
                        // var_dump($data[$field_column]);
                        break;
                }
                if ($block != '') {
                    $phpdocx->assignBlock($block, $value);
                } else {
                    $phpdocx->assign('#' . $replace . '#', $value);
                }

            }
            // var_dump($value);
        }
        $fileName = "phieu_theo_doi_xu_ly_don_thu.doc";
        // Duong dan file report
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = substr($path, 0, -9);
        $reportFile = str_replace("/", "\\", $path) . "io\\export\\" . $fileName;
        $phpdocx->save($reportFile);
        //Doc file xuat bang COM
        $optionprint = (isset($arrParameter['optionprint']) ? $arrParameter['optionprint'] : '');
        if ($optionprint == 'COM') {
            G_Export::getInstance()->convertDocByCom($reportFile);
        }
        return $fileName;
    }

    public function getAlertApointedDate2($arrInput){
        $objConvert = new G_Convert();
        if(is_array($arrInput)){
            $arrDate = explode('#', $arrInput['0']);
            // $appointedDate = $objConvert->_ddmmyyyyToYYyymmdd($arrDate['1']);
            $appointedDate = $arrDate['1'];
            // var_dump($appointedDate);
        }else{
            $arrDate = explode('#', $arrInput);
            $appointedDate = 'Hạn xử lý: ' . date('d/m/Y',strtotime($arrDate['0']));
        }
        $signApproveDate = (isset($arrDate['2'])? $arrDate['2'] : '');
        $currentDate = date('Y-m-d H:i:s');
        $htmlResult = '';
        if ($appointedDate && $appointedDate!='1900-01-01 00:00:00' ){
            $objDate = new G_Date();
            if ($signApproveDate == ''){
                $totalminute = $objDate->_diffdate($currentDate,$appointedDate);
                // var_dump($totalminute);
                $day  = $this->_convertTimeToddhhmm($totalminute);
                if($totalminute > 0)
                    $htmlResult = $arrDate['0'].'<br><font style="color:blue;">(Còn lại '.$day.').';
                else if($totalminute < 0)
                    $htmlResult = $arrDate['0'].'<br><font style="color:red;">(Đã quá hạn '.$day.').';
                else
                    $htmlResult = $arrDate['0'].'<br><font style="color:blue;">(Hôm nay là hạn xử lý).';
            }
            else {
                $signApproveDate = $objConvert->_ddmmyyyyToYYyymmdd($signApproveDate);
                $totalminute = $objDate->_diffdate($signApproveDate,$appointedDate);
                $day  = $this->_convertTimeToddhhmm($totalminute);
                if($totalminute > 0)
                    $htmlResult = $arrDate['0'].'<br><font style="color:blue;">(Xử lý trước hạn '.$day.').';
                else if($totalminute < 0)
                    $htmlResult = $arrDate['0'].'<br><font style="color:red;">(Xử lý quá hạn '.$day.').';
                else
                    $htmlResult = $arrDate['0'].'<br><font style="color:blue;">(Đã xử lý).';
            }
        }
        return $htmlResult;
    }

    /**
     * @param $arrInput
     * @return string
     */
    public function getDate($arrInput) {
        $date = date('d/m/Y');
        $arrDate = explode('/', $date);
        $string = 'ngày ' . $arrDate['0'] . ' tháng ' . $arrDate['1'] . ' năm ' . $arrDate['2'];
        $data = (string) $arrInput['0'];
        if($data==1)
            $string = 'Ngày ' . $arrDate['0'] . ' tháng ' . $arrDate['1'] . ' năm ' . $arrDate['2'];
        return $string;
    }
    public function _getUnitProcessText($arrInput) {
        $arrunit = explode(',',$arrInput[0]);
        $arrunit = '-'.implode('<w:br/>-',$arrunit);
        return $arrunit;
    }

    public function getReceivedCtDate($arrInput) {
        $date = $arrInput['0'];
        $arrDate = explode('/', $date);
        $string = 'ngày ' . $arrDate['0'] . ' tháng ' . $arrDate['1'] . ' năm ' . $arrDate['2'];
        return $string;
    }
    /**
     *
     */
    public function _setOnerReportName() {
        $sysConst = Zend_Registry::get('__sysConst__');
        return $sysConst->reportOwnerName;
    }

    public function addNumberDuplicate($arrInput)
    {
        $sRegistorName = (string)$arrInput[0];
        $iNumberDuplicateRecord = (string)$arrInput[1];
        $sHtml = $sRegistorName;
        if($sRegistorName) $sHtml .= ' ';
        if ($iNumberDuplicateRecord) {
            $sHtml .= '<lable class="duplicate" onclick="viewDuplicate(this)" style="cursor: pointer;color:#0000CC;width:38%;padding:0;">(' . $iNumberDuplicateRecord . ')</lable>';
        }
        return $sHtml;
    }

    public function addTask($arrInput)
    {
        $fkStaff = (string)$arrInput[0];
        if ($fkStaff == Zend_Auth::getInstance()->getIdentity()->PkStaff) {
            $sHtml .= '<lable class="duplicate" onclick="viewDuplicate(this)" style="cursor: pointer;color:#0000CC;width:38%;padding:0;">(' . $iNumberDuplicateRecord . ')</lable>';
        }
        return $sHtml;
    }

    public function getIdeaAssign($arrInput)
    {
        $PkRecord = $arrInput['0'];
        $db = new G_Db();
        $sql ="select sIdeaContent from KntcSolveStaff where FkRecord=".$db->qstr($PkRecord);
        $arrResult = $db->adodbExecSqlString($sql);
        if (isset($arrResult['sIdeaContent']))
            return $arrResult['sIdeaContent'];
        return '';
    }

    public function taskOption($arrInput)
    {
        $sCompetentStatus = (string)$arrInput[0];
        $sSolveCompetentType = (string)$arrInput[1];
        $sHtml = '';
        $rs = $sCompetentStatus . ':' . $sSolveCompetentType;
        switch ($rs) {
            case 'TTQ:TTQ_01':
                $sHtml .= '<span class="icon_tranfer tranfer_dep" title="Chuyển phòng"></span>';
                break;
            
            case 'KTTQ:KTTQ_03':
                $sHtml .= '<span rs="" class="icon_tranfer tranfer_ward" title="Chuyển xã"></span>';
                break;

            default:
                $sHtml .= '<span class="icon_tranfer tranfer_track" title="Chuyển theo dõi"></span>';
                break;
        }
        $sHtml .= '<span class="icon_edit edit_info" title="Đính chính thông tin"></span>';
        $sHtml .= '<span class="icon_viewer viewtempo" title="Xem tiến độ"></span>';
        
        return $sHtml;
    }

    public function taskOptionWard($arrInput)
    {
        $FkRecord = (string)$arrInput[0];
        if (!$FkRecord) {
            $sHtml .= '<span class="icon_edit edit_info" title="Đính chính thông tin"></span>';
        }
        $sHtml .= '<span class="icon_viewer viewtempo" title="Xem tiến độ"></span>';
        
        return $sHtml;
    }

    /*
    * Lay y kien can bo trinh len
    */
    public function getIdeaRelated($arrInput)
    {
        $param = array(
                'pkrecord' => $arrInput['0'],
                'fkStaff' => Zend_Auth::getInstance()->getIdentity()->PkStaff
            );
        
        $arrResult = G_Db::getInstance()->_querySql($param, 'sp_KntcRecordGetIdeaRelated', 0, 0);
        $idea = '';
        if ($arrResult) {
            $content = '<span style="font-weight:bold;">'.$arrResult['sPositionName'].':</span> <br>'.$arrResult['sResult'];
            $idea .= $this->contensumfile(array($content, $arrResult['PkRecordWork'], 'TIEN_DO'));
        }
        return $idea;
    }

    /*
    * Lay y kien da thuc hien
    */

    public function getMyIdea($arrInput)
    {
        $param = array(
                'pkrecord' => $arrInput['0'],
                'fkStaff' => Zend_Auth::getInstance()->getIdentity()->PkStaff
            );
        
        $arrResult = G_Db::getInstance()->_querySql($param, 'sp_KntcRecordGetMyIdea', 0, 0);
        $idea = '';
        if ($arrResult) {
            $content = '<span style="font-weight:bold;">'.$arrResult['sPositionName'].':</span> <br>'.$arrResult['sResult'];
            $idea .= $this->contensumfile(array($content, $arrResult['PkRecordWork'], 'TIEN_DO'));
        }
        return $idea;
    }

    public function getSessionUnitName($arrInput)
    {
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        if(isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='')
        {
            $name = G_Convert::Lower2Upper(Zend_Auth::getInstance()->getIdentity()->sUnitName);
        }
        else
        {
            $arr = array($userIdentity->sOwnerCode,'DON_VI_TRIEN_KHAI');
            $myname = G_Convert::Lower2Upper($this->convertListCodeToName($arr));
            $name   = str_replace('UBND ', '', $myname);
        }
        return $name;
    }

    public function getSessionPlace($arrInput)
    {
        $userIdentity = Zend_Auth::getInstance()->getIdentity();
        if(isset($userIdentity->sDistrictWardProcess) && $userIdentity->sDistrictWardProcess !='')
        {
            $myname = Zend_Auth::getInstance()->getIdentity()->sUnitName;
            $arr_source = array('Thị trấn', 'thị trấn', 'Xã', 'xã', 'Phường', 'phường');
            $arr_destination = array('', '', '', '', '', '');
            $name = str_replace($arr_source, $arr_destination, $myname);
        }
        else
        {
            $arr = array($userIdentity->sOwnerCode,'DON_VI_TRIEN_KHAI');
            $myname = $this->convertListCodeToName($arr);
            $arr_source = array('UBND huyện', 'UBND HUYỆN');
            $arr_destination = array('', '');
            $name   = str_replace($arr_source, $arr_destination, $myname);
        }
        return $name;
    }
    
}