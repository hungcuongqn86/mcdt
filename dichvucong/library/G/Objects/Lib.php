<?php

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 01/04/2015
 * Noi dung: Tao lop G_Objects_Lib luu cac ham dung chung
 */
class G_Objects_Lib
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function _createCookie($sName, $sValue = "")
    {
        //Dat gia tri expires
        $dExpires = time() + 60 * 60 * 24;
        //Dat gia tri vao Cookie
        setcookie($sName, $sValue, $dExpires, "/", "");
    }


    public static function _getCookie($sNname)
    {
        if (isset($_COOKIE[$sNname]) && strlen($_COOKIE[$sNname]) > 0) {
            return urldecode($_COOKIE[$sNname]);
        } else {
            return "";
        }

    }

    /**
     * ******************************************************************************************************************
     * @Idea   :Lay id goc cua don vi (phong ban) cha
     * @return : Id cua thu muc goc
     *********************************************************************************************************************
     */
    public static function _getRootUnitId($p_arr = array())
    {
        $v_root_id = "";

        //Lay thong tin cac phong ban
        $arr_all_unit = $p_arr;
        $arr_all_unit = str_replace('\n', '', $arr_all_unit);
        //var_dump($arr_all_unit);
        foreach ($arr_all_unit as $v_unit) {
            if (is_null($v_unit['parent_id']) || trim($v_unit['parent_id']) == '' || trim($v_unit['parent_id']) == 'NULL') {
                $v_root_id = $v_unit['id']; //Don vi goc
                break;
            }
            unset($v_unit);
        }
        return $v_root_id;
    }

    /**
     * Creater : TRUONGDV
     * Date :
     * Idea : copy file $pToFile vao thu muc chi dinh$pFromFile
     *
     * @param $pFromFile : Duong dan luu file
     * @param $pToFile : Ten file chua noi dung
     */
    public static function _copyFile($pFromFile, $pToFile)
    {
        //Goi ham copy file cua he thong PHP
        move_uploaded_file($pFromFile, $pToFile);
    }

    /**
     * Creater : TRUONGDV
     * Date : 09/01/2009
     * Idea: Doc file
     * @param $spFilePath : Duong dan file can doc
     * @return Noi dung file
     */
    public static function _readFile($spFilePath)
    {
        $spRet = "";
        $handle = fopen($spFilePath, "r");
        if ($handle) {
            while (!feof($handle)) {
                $spRet .= fread($handle, 10000);
            }
        }
        return $spRet;
    }

    /**
     * Creater : TRUONGDV
     * Date : 09/01/2009
     * Idea: Ghi file
     * @param $spFilePath : Duong dan file can ghi
     * @param $spContent : Noi dung can ghi vao file
     */
    public static function _writeFile($spFilePath, $spContent)
    {
        if (file_exists($spFilePath)) {
            chmod($spFilePath, 0777);
        }
        $handle = fopen($spFilePath, "w+");
        if ($handle) {
            fwrite($handle, $spContent);
            fclose($handle);
        }
        //echo $handle;exit;
    }

    /**
     * Creater : TRUONGDV
     * Date : 21/05/2009
     * Idea : Tao phuong thuc xoa file trong thu muc $sPathDir
     *
     * @param $sFileNameList : Danh sach file can xoa
     * @param $sDelimitor : Ky tu phan tach giua cac phan tu
     * @param $sPathDir : Duong dan luu file can xoa
     */
    public function _deleteFile($sFileNameList, $sDelimitor, $sPathDir)
    {
        $arrFileName = explode($sDelimitor, $sFileNameList); //Mang luu ten file
        $iCount = sizeof($arrFileName);
        for ($index = 0; $index < $iCount; $index++) {
            $sFullPathFileName = $sPathDir . $arrFileName[$index];
            if (file_exists($sFullPathFileName)) {
                if (is_writable($sFullPathFileName)) {
                    unlink($sFullPathFileName);
                } else {
                    echo "<font color='red'> <b>File $arrFileName[$index]: can xoa khong cho phep truy cap!</b></font> <br /> ";
                }
            } else {
                echo "<font color='red'> <b>File $arrFileName[$index]: can xoa khong ton tai trong :" . $sPathDir . "</b></font> <br /> ";
            }
        }
    }

    /**
     * @Idea: Lay mot phan tu cua danh sach tai mot vi tri cho truoc
     *
     * @param $pList : Mang luu danh sach phan tu
     * @param $pIndex : Chi so phan tu can lay
     * @param $pDelimitor : Ky tu phan cach giua cac phan tu
     * @return Gia tri phan tu lay duoc
     */
    public static function _listGetAt($pList, $pIndex, $pDelimitor)
    {
        $retValue = "";
        if (strlen($pList) == 0) {
            return $retValue;
        }
        $arrElement = explode($pDelimitor, $pList);
        $retValue = $arrElement[$pIndex];
        return $retValue;
    }

    /**
     * @Idea : Lay tong so phan tu cua mot danh sach
     *
     * @param $pString : Xau ky tu
     * @param $pDelimitor : Ky tu phan tach cac phan tu
     * @return So phan tu trong xau phan tach nhau boi $pDelimitor
     */
    //*************************************************************************
    public static function _listGetLen($pString, $pDelimitor)
    {
        $retValue = 0;
        if (strlen($pString) <> 0) {
            $array = explode($pDelimitor, $pString);
            $retValue = sizeof($array);
        }
        return $retValue;
    }

    /**
     * Creater : TRUONGDV
     * Date : 21/06/2009
     * Idea : Phuong thuc kiem tra co ton tai mot phan tu trong tap gia tri khong? (true => ton tai; false => khong ton tai)
     *
     * @param $p_list : Tap phan tu
     * @param $p_element : Phan tu can kiem tra
     * @param $p_delimitor : Ky tu phan tach giua cac phan tu trong $p_list
     * @return Tra lai gia tri true/false
     */
    public static function _listHaveElement($p_list, $p_element, $p_delimitor)
    {
        if ($p_list == "" Or $p_element == "") {
            return false;
        }
        $ret_value = false;
        if (strlen($p_list) > 0) {
            $array = explode($p_delimitor, $p_list);
            $ret_value = in_array($p_element, $array);
        }
        return $ret_value;
    }

    public static function _deleteFileUpload($url)
    {
        return unlink($url);
    }

    /**
     * Creater: TRUONGDV
     * Date: 09/04/2015
     * IDea: Lay mot ngay bat ky trong tuan cua nam(Trang 1; Trang 2;...)
     *
     * @param $year (int) : Xac dinh nam
     * @param $numberOfWeek (int) : Tuan thu may trong nam
     * @param $orderDate : Lay ngay thu may trong tuan
     * @return Ngay trong tuan
     */
    public static function _getAnyDateOnWeekOfYear($year, $numberOfWeek, $orderDate)
    {
        //Xac dinh ngay 1/1 la thu may
        $timestamp = mktime(0, 0, 0, 1, 1, $year);
        $fisrtDayOfYear = date('w', $timestamp);
        if ($fisrtDayOfYear == 0) {
            $fisrtDayOfYear = 7;
        }
        //Xac dinh ngay dau cua tuan thu nhat la ngay nao, ngay nay co the thuoc nam truoc.
        if ($fisrtDayOfYear <= 4) {
            $day = 2 - $fisrtDayOfYear;
        } else {
            $day = 9 - $fisrtDayOfYear;
        }
        for ($i = 1; $i <= 53; $i++) {
            if ($i == $numberOfWeek) {
                $dateOfWeek = date("d/m/Y", mktime(0, 0, 0, 1, $day + $orderDate, $year));
                break;
            }
            $day = $day + 7;
            //Kiem tra tuan sau da sang tuan thu nhat cua nam sau chua.
            if (date("W", mktime(0, 0, 0, 1, $day, $year)) == 1) {
                break;
            }
        }
        return $dateOfWeek;
    }

    // Lay gia tri default hien thi menu left
    public function getDefaultDisplayMenuLeft()
    {
        $sHideDisplayMeneLeft = "";
        $sGetValueInCookie = $this->_getCookie("showHideMenu");
        if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)) {
            $this->_createCookie("showHideMenu", 1);
            $sHideDisplayMeneLeft = 1;
        } else {//Da ton tai Cookie
            if ($sGetValueInCookie != 0) {
                $sHideDisplayMeneLeft = 1;
            }
        }
        return $sHideDisplayMeneLeft;
    }

    // Convert gia tri từ 1 mảng
    public function getItemAttrByIndex($arrIn, $valueIn, $indexCompare, $indexOut)
    {
        foreach ($arrIn as $key => $value) {
            if ($value[$indexCompare] === $valueIn)
                return $value[$indexOut];
        }
        return '';
    }

    public function getListItemAttrByIndex($arrIn, $arrValue, $indexCompare, $indexOut)
    {
        $result = array();
        foreach ($arrIn as $key => $value) {
            if (in_array($value[$indexCompare], $arrValue)) {
                array_push($result, $value[$indexOut]);
            }
        }
        return $result;
    }

    public static function _getValuesByIds($data, $Pks, $value, $key_value = 'id')
    {
        $arrPk = explode(',', $Pks);
        $arrPk = preg_replace('/\{/', '', $arrPk);
        $arrPk = preg_replace('/\}/', '', $arrPk);
        $iTotal = sizeof($data);
        $count = sizeof($arrPk);
        $sOutput = '';
        for ($i = 0; $i < $count; $i++)
            for ($j = 0; $j < $iTotal; $j++)
                if ($arrPk[$i] == $data[$j][$key_value]) {
                    if ($value == 'namePosition')
                        $sOutput .= ',' . $data[$j]['position_code'] . ' - ' . $data[$j]['name'];
                    else
                        $sOutput .= ',' . $data[$j][$value];
                }
        $sOutput = substr($sOutput, 1, strlen($sOutput));
        return $sOutput;
    }

    public function convertDataByList($arrIn, $listIn, $indexCompare, $indexOut, $delimitor = ',')
    {
        if (empty($arrIn))
            return $listIn;
        $arrList = explode($delimitor, $listIn);
        $listOut = '';
        foreach ($arrList as $key => $value) {
            $listOut .= $this->getItemAttrByIndex($arrIn, $value, $indexCompare, $indexOut) . $delimitor;
        }
        $listOut = substr($listOut, 0, strlen($listOut) - strlen($delimitor));
        return $listOut;
    }

    public function create_zip($files = array(), $destination = '', $overwrite = false)
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
                $basename = basename($file);
                $zip->addFile($file, $basename);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            //close the zip -- done!
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

    public function checkKeyValueExist($data, $key, $value)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ($v[$key] === $value)
                    return true;
            }
        }
        return false;
    }

    /**
     * @param $uploaddir
     * @param string $valueName
     * @return mixed|string
     */
    public function uploadAjaxTemp($uploaddir, $valueName = 'uploadfile')
    {
        $uploaddir = '..' . $uploaddir;
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
        $time = time();
        $filename = basename($_FILES[$valueName]['name']);
        $arrTemp = explode('.', $filename);
        $ext = end($arrTemp);
        $filename = substr($filename, 0, strlen($filename) - strlen($ext));
        $filename .= strtolower($ext);
        $file = $uploaddir . $time . '!~!' . $filename;
        $file = G_Convert::getInstance()->_convertVNtoEN($file);

        if (is_file($_FILES[$valueName]['name']) || $_FILES[$valueName]['name'] != '') {
            if (move_uploaded_file($_FILES[$valueName]['tmp_name'], $file)) {
                return str_replace('..', '', $file);
            } else {
                return "error";
            }
        } else {
            return "error";
        }
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
        $sPath = str_replace("/", "\\", $pathLink);
        if (!file_exists($sPath . $folderYear)) {
            mkdir($sPath . $folderYear, 0777);
            $sPath = $sPath . $folderYear;
            if (!file_exists($sPath . chr(92) . $folderMonth)) {
                mkdir($sPath . chr(92) . $folderMonth, 0777);
            }
        } else {
            $sPath = $sPath . $folderYear;
            if (!file_exists($sPath . chr(92) . $folderMonth)) {
                mkdir($sPath . chr(92) . $folderMonth, 0777);
            }
        }
        //Tao ngay trong nam->thang
        if (!file_exists($sPath . chr(92) . $folderMonth . chr(92) . $sCurrentDay)) {
            mkdir($sPath . chr(92) . $folderMonth . chr(92) . $sCurrentDay, 0777);
        }
        //
        $strReturn = $pathLink . $folderYear . '/' . $folderMonth . '/' . $sCurrentDay . '/';
        return $strReturn;
    }

    /**
     * @param $listAttach
     * @param $listUrlAttach
     * @param $sDir
     * @param string $sDelimitor
     * @param string $sPkRecord
     * @param string $sTableObject
     * @return mixed
     * @throws Zend_Exception
     */
    public function _uploadFileAttachList($listAttach, $listUrlAttach, $sDir, $sDelimitor = ',', $sPkRecord = '', $sTableObject = '')
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $path = self::_createFolder($sDir, date('Y'), date('m'), date('d'));
        $sFileNameList = "";
        $arrAttach = explode($sDelimitor, $listAttach);
        $arrUrlAttach = explode($sDelimitor, $listUrlAttach);
        $count = sizeof($arrAttach);
        if ($count == 0) {
            return $sFileNameList;
        }
        $dest = '';
        for ($i = 0; $i < $count; $i++) {

            $urlTemp = '..' . $arrUrlAttach[$i];

            if ($sysConst->fileserverStatus) {
                $arr = explode('/', $urlTemp);
                $ilen = count($arr);
                if ($ilen > 1) {
                    $filename = $arr[$ilen - 1];
                    $fodel = $arr[$ilen - 2] . "!~!";
                } else {
                    $filename = $arr[0];
                    $fodel = "!~!";
                }
                $sFullFileName = $fodel . $filename;
            } else {
                $arr = explode('!~!', $urlTemp);
                $filename = end($arr);
                $random = mt_rand(1, 1000000);
                $fodel = date("Y") . '_' . date("m") . '_' . date("d") . "_" . date("H") . date("i") . date("u") . $random . "!~!";
                $sFullFileName = $fodel . $filename;
                $dest = $path . G_Convert::getInstance()->_convertVNtoEN($sFullFileName);
            }
            //Di chuyen sang thu muc luu tru
            if (file_exists($urlTemp)) {
                if ($sysConst->fileserverStatus) {
                    $urlTemp = str_replace("/", chr(92), $_SERVER['DOCUMENT_ROOT'] . $arrUrlAttach[$i]);
                    $fileObj = new G_FileServer();
                    $fileId = $fileObj->_upload($urlTemp, $sPkRecord, $arrAttach[$i], $sTableObject);
                    $arr = explode('!~!', $filename);
                    $filename = end($arr);
                    $sFullFileName = $fileId . "!~!" . $filename;
                } else {
                    copy($urlTemp, $dest);
                }
                //Xoa file o thu muc tam
                unlink($urlTemp);
            }
            $sFileNameList .= $sFullFileName . $sDelimitor;
        }
        $sFileNameList = substr($sFileNameList, 0, strlen($sFileNameList) - strlen($sDelimitor));
        // tra lai gia tri
        return G_Convert::getInstance()->_convertVNtoEN($sFileNameList);
    }


    public function _uploadFile($listAttach, $listUrlAttach, $locationList, $sDir, $sDelimitor = ',', $sPkRecord = '', $sTableObject = '')
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $path = self::_createFolder($sDir, date('Y'), date('m'), date('d'));
        $dirTemp = G_Global::getInstance()->dirTempUpload;
        $sFileNameList = "";
        $arrAttach = explode($sDelimitor, $listAttach);
        $arrUrlAttach = explode($sDelimitor, $listUrlAttach);
        $arrLocation = explode($sDelimitor, $locationList);
        $count = sizeof($arrAttach);
        if ($count == 0) {
            return $sFileNameList;
        }
        $dest = '';
        for ($i = 0; $i < $count; $i++) {
            $urlTemp = $dirTemp . $arrUrlAttach[$i];
            if ($arrLocation[$i] == 1) {
                $sFullFileName = $arrUrlAttach[$i];
            } else {
                if ($sysConst->fileserverStatus) {
                    $arr = explode('/', $urlTemp);
                    $ilen = count($arr);
                    if ($ilen > 1) {
                        $filename = $arr[$ilen - 1];
                        $fodel = $arr[$ilen - 2] . "!~!";
                    } else {
                        $filename = $arr[0];
                        $fodel = "!~!";
                    }
                    $sFullFileName = $fodel . $filename;
                } else {
                    $arr = explode('!~!', $urlTemp);
                    $filename = end($arr);
                    $random = mt_rand(1, 1000000);
                    $fodel = date("Y") . '_' . date("m") . '_' . date("d") . "_" . date("H") . date("i") . date("u") . $random . "!~!";
                    $sFullFileName = $fodel . $filename;
                    $dest = $path . G_Convert::getInstance()->_convertVNtoEN($sFullFileName);
                }
            }

            //Di chuyen sang thu muc luu tru
            if (file_exists($urlTemp)) {
                if ($sysConst->fileserverStatus) {
                    $urlTemp = str_replace("/", chr(92), $_SERVER['DOCUMENT_ROOT'] . $arrUrlAttach[$i]);
                    $fileObj = new G_FileServer();
                    $fileId = $fileObj->_upload($urlTemp, $sPkRecord, $arrAttach[$i], $sTableObject);
                    $arr = explode('!~!', $filename);
                    $filename = end($arr);
                    $sFullFileName = $fileId . "!~!" . $filename;
                } else {
                    copy($urlTemp, $dest);
                }
                //Xoa file o thu muc tam
                unlink($urlTemp);
            }
            $sFileNameList .= $sFullFileName . $sDelimitor;
        }
        $sFileNameList = substr($sFileNameList, 0, strlen($sFileNameList) - strlen($sDelimitor));
        // tra lai gia tri
        return G_Convert::getInstance()->_convertVNtoEN($sFileNameList);
    }

    /**
     * Idea : Tim kiem gia tri trong mot mang
     *
     * @param $arrRes : Mang gia tri
     * @param $ColumnIdRes : Ma gia tri
     * @param $ColumnTexRes : Ten gia tri
     * @param $TextRes : Gia tri tim kiem
     * @param $hndRes : Hidden luu gia tri
     * @param $editable : 1 : duoc phep them moi doi tuong, 0: khong duoc phep them moi doi tuong
     * @param $option : (Neu $option = 1 chi chon mot doi tuong ; $option = 0 thi duoc chon nhieu)
     * @param $sColumName : Cot du lieu can bo sung them vao text hien thi tren doi tuong Auto Complete Text (vi du: truyen vao gia tri position_code hien thi Ma chuc vu - Ten can b. CT - Nguyen Van A)
     * @param $disOpt : 1: hien thi sCode, 2: hien thi sName (chi dung trong truong hop $editable = 0)
     * @param $fs ,$fc,$fd : Tuong ung la cac ham fSelect, fClear, fDelete
     * @return Xau html
     * $defineFun : Ham se thuc thi khi NSD chon mot gia tri trong autocomplete textbox
     */
    public function doc_search_ajax($arrRes, $ColumnIdRes, $ColumnTexRes, $TextRes, $hndRes, $single = 1, $sColumName = "", $editable = 0, $disOpt = 0, $fs = "", $fc = "", $fd = "")
    {
        $sitePath = G_Global::getInstance()->sitePath;
        $sHtmlRes = '';
        $sHtmlRes = $sHtmlRes . ' <script type="text/javascript">  '; //
        $sHtmlNameId = '';
        $sHtmlNameId = $sHtmlNameId . '  var NameID' . $hndRes . ' = new Array('; //
        $sHtmlNameText = '';
        $sHtmlNameText = $sHtmlNameText . ' var NameText' . $hndRes . ' = new Array(';
        $sHtmlNameStr = '';
        $sHtmlNameStr .= ' var NameStr' . $hndRes . ' = new Array(';
        // Ghi Ma va ten ra mot mang
        foreach ($arrRes as $arrTemp) {
            $sTemp = "";
            if ($sColumName != "") {
                $sPositionCode = $arrTemp[$sColumName];
                if ($sPositionCode != "") {
                    $sTemp = $sPositionCode . " - ";
                }
            }
            $sHtmlNameId = $sHtmlNameId . '"' . $arrTemp[$ColumnIdRes] . '",';
            $sHtmlNameText = $sHtmlNameText . '"' . $sTemp . str_replace('"', '\"', $arrTemp[$ColumnTexRes]) . '",';
            if ($disOpt == 2)
                $sHtmlNameStr .= '"' . $arrTemp[$ColumnTexRes] . '",';
        }
        $sHtmlNameId = rtrim($sHtmlNameId, ',') . '); ';
        $sHtmlNameText = rtrim($sHtmlNameText, ',') . '); ';
        $sHtmlNameStr = rtrim($sHtmlNameStr, ',') . '); ';
        $sHtmlRes = $sHtmlRes . $sHtmlNameId . $sHtmlNameText . $sHtmlNameStr . ' ';
        $sHtmlRes = $sHtmlRes . ' obj' . $hndRes . ' = new actb(document.getElementById(\'' . $TextRes . '\'),document.getElementById(\'' . $hndRes . '\'),NameStr' . $hndRes . ',NameText' . $hndRes . ',NameID' . $hndRes . ',\'FillProduct(' . $disOpt . ',\',\'' . $single . '\',\'' . $editable . '\',\'' . $sitePath . '\',\'' . $fs . '\',\'' . $fc . '\',\'' . $fd . '\');';
        $sHtmlRes = $sHtmlRes . '</script>';
        return $sHtmlRes;
    }

    /**
     * @param $pathname
     * @return string
     */
    public function _mkdir($pathname)
    {
        $arrPath = explode('/', $pathname);
        $sPath = '';
        foreach ($arrPath as $key => $value) {
            $sPath .= $value . '/';
            if (!file_exists($sPath)) {
                mkdir($sPath, 0777);
            }
        }
        return $sPath;
    }

    public static function deleteListFileAttach($listFile, $delimitor)
    {
        $arrDeleteFileList = explode($delimitor, $listFile);
        $dirsavefile = realpath('io/attach-file');
        foreach ($arrDeleteFileList as $key => $value) {
            $fileIds = explode('_', $value);
            $file = $dirsavefile . DIRECTORY_SEPARATOR . $fileIds[0] . DIRECTORY_SEPARATOR . $fileIds[1] . DIRECTORY_SEPARATOR . $fileIds[2] . DIRECTORY_SEPARATOR . $value;
            if (file_exists($file))
                unlink($file);
        }
    }

    /*
     * Xu ly file
     */
    public function fileUpdate($arrInput) {
        $sFileAttachList = (isset($arrInput['sFileAttachList']) ? $arrInput['sFileAttachList'] : '');
        $sDocTypeList = (isset($arrInput['sDocTypeList']) ? $arrInput['sDocTypeList'] : '');
        $sDelimitor = (isset($arrInput['sDelimitor']) ? $arrInput['sDelimitor'] : '');
        $hdn_file_deleted_list = (isset($arrInput['sUnFileAttachList']) ? $arrInput['sUnFileAttachList'] : '');
        $locationList = (isset($arrInput['locationList']) ? $arrInput['locationList'] : '');
        $valueKey = (isset($arrInput['key']) ? $arrInput['key'] : '');
        // Dinh kem file
        $sFileNameAttachList = '';
        if ($sFileAttachList != '' && $sDocTypeList != '') {
            // Upload file dinh kem
            $sDir = G_Global::getInstance()->dirSaveFile;
            $sFileNameAttachList = $this->_uploadFile($sDocTypeList, $sFileAttachList, $locationList, $sDir, $sDelimitor, $valueKey);
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

        return array(
                'fileList' => $sFileNameAttachList,
                'doctypeList' => $sDocTypeList,
                'sDelimitor' => $sDelimitor,
                'fkDoc' => $valueKey,
            );
    }


    public function  sendEmail($message)
    {
        $sysConst = Zend_Registry::get('__sysConst__');
        $config = array(
            'auth' => $sysConst->email_auth,
            'ssl' => $sysConst->email_ssl,
            'port' => $sysConst->email_port,
            'username' => $sysConst->email_username,
            'password' => $sysConst->email_password
        );
        // var_dump($config); die;
        $transport = new Zend_Mail_Transport_Smtp($sysConst->email_mailserver, $config);
        $mail = new Zend_Mail('UTF-8');
        $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        $mail->setFrom($sysConst->email_username, $sysConst->email_username);
        $mail->addTo($message['to'], $message['to']);
        $mail->setSubject($message['subject']);
        $mail->setBodyHtml($message['body']);
        try {
            $sent = true;
            $mail->send($transport);
        } catch (Exception $e) {
            echo $e->getMessage(); die;
            $sent = false;
        }
        return $sent;
    }
}

?>