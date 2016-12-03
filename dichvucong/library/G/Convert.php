<?php
/**
 * @see
 *
 */

/**
 * Nguoi tao: TRUONGDV
 * Ngay tao: 08/04/2015
 * Noi dung: Tao lop G_Convert
 */
class G_Convert
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function _replaceBadChar($spString)
    {
        $psRetValue = stripslashes($spString);
        $psRetValue = str_replace('&', '&amp;', $psRetValue);
        $psRetValue = str_replace('<', '&lt;', $psRetValue);
        $psRetValue = str_replace('>', '&gt;', $psRetValue);
        $psRetValue = str_replace('"', '&#34;', $psRetValue);
        $psRetValue = str_replace("'", '&#39;', $psRetValue);
        return $psRetValue;
    }

    public static function _restoreBadChar($pshtml)
    {
        $pshtml = str_replace('&amp;', '&', $pshtml);
        $pshtml = str_replace('&quot;', '"', $pshtml);
        $pshtml = str_replace('&#39;', "'", $pshtml);
        $pshtml = str_replace('&lt;', '<', $pshtml);
        $pshtml = str_replace('&gt;', '>', $pshtml);
        $pshtml = str_replace('&#34;', '"', $pshtml);
        //$pshtml = htmlspecialchars($pshtml);
        return $pshtml;
    }

    /**
     * Creater : TRUONGDV
     * Date: 08/04/2015
     * Idea: Tao mang mot chieu, mang co cau truc cac phan tu theo kieu
     * array(pt1=>pt2, pt3=>pt4)
     *
     * @param $pArraySource : Mang chua du lieu can lay thong tin de tao mang mot chieu
     * @param $psColumName01 : Ten cot (VD: PK_)
     * @param $psColumName02 : : Ten cot (VD: sName)
     * @return Mang mot chieu
     */
    public static function _createOneDimensionArray($pArraySource, $psColumName01, $psColumName02)
    {
        $arrResultArray = array();
        if (is_array($pArraySource) && sizeof($pArraySource) > 0) {
            //Duyet cac phan tu cua mang
            for ($index = 0; $index < sizeof($pArraySource); $index++) {
                // Lay gia tri cua $psColumName01
                $psColumName01Value = $pArraySource[$index][$psColumName01];
                // Lay gia tri cua $psColumName02
                $psColumName02Value = $pArraySource[$index][$psColumName02];
                //Chuyen cac phan tu vao mang
                $arrResultArray[$psColumName01Value] = $psColumName02Value;
            }
        }
        return $arrResultArray;
    }


    public static function _yyyymmddToDDmmyyyy($psYyymmdd)
    {
        if (is_null($psYyymmdd) || trim($psYyymmdd) == "" || $psYyymmdd == 1900)
            return "";
        return date("d/m/Y", strtotime($psYyymmdd));
    }

    public static function _yyyymmddToDDmmyyyyhhmm($psYyyymmdd)
    {
        if (is_null($psYyyymmdd) || trim($psYyyymmdd) == "")
            return "";
        return date("d/m/Y H:i", strtotime($psYyyymmdd));
    }

    /**
     * Idea: chuyen doi thoi gian dang yyyymmdd thanh chuoi dang dd/mm/yyyy hh:mm:ss
     *
     * @param $psYyyymmdd :la thoi gian dang chuoi
     * @return chuoi dang dd/mm/yyyy hh:mm:ss (De hien thi tren man hinh)
     */
    public static function _yyyymmddToDDmmyyyyhhmmss($psYyyymmdd)
    {
        if (is_null($psYyyymmdd) || trim($psYyyymmdd) == "")
            return "";
        return date("d/m/Y H:i:s", strtotime($psYyyymmdd));
    }

    /**
     * Idea: chuyen doi thoi gian dang yyyymmdd thanh chuoi dang yyyy-mm-dd hh:mm:ss
     *
     * @param $psYyyymmdd : la thoi gian dang chuoi
     * @return chuoi dang dd/mm/yyyy hh:mm:ss (De hien thi tren man hinh)
     */
    public static function _yyyymmddToYYyymmddhhmmss($psYyyymmdd)
    {
        if (is_null($psYyyymmdd) || trim($psYyyymmdd) == "")
            return "";
        return date("Y/m/d H:i:s", strtotime($psYyyymmdd));
    }

    /**
     * Idea: chuyen doi thoi gian dang yyyymmdd thanh chuoi dang hh:mm
     *
     * @param $psYyyymmdd : la thoi gian dang chuoi
     * @return chuoi hh:mm(De hien thi tren man hinh)
     */
    public static function _yyyymmddToHHmm($psYyyymmdd)
    {
        if (is_null($psYyyymmdd) || trim($psYyyymmdd) == "")
            return "";
        return date("H:i", strtotime($psYyyymmdd));
    }

    /**
     * Idea: chuyen doi thoi gian dang chuoi yyyymmdd thanh chuoi dang hh:mm:ss
     *
     * @param $psYyyymmdd : la thoi gian dang yyyymmdd
     * @return chuoi hh:mm:ss (De hien thi tren man hinh)
     */
    public static function _yyyymmddToHHmmss($psYyyymmdd)
    {
        if (is_null($psYyyymmdd) || trim($psYyyymmdd) == "")
            return "";
        return date("H:i:s", strtotime($psYyyymmdd));
    }

    /**
     * @Idea : chuyen doi ngay dang mm/dd/yyyy thanh ngay dang yyyy/mm/dd
     * Edit : TRUONGDV
     * @param $psDdmmyyyy : la chuoi dang dd/mm/yyyy( chuoi chu khong phai date object )
     * $iSearch = 1 : SU dung de phuc vu tim kiem tu ngay
     *            = 2    : SU dung de phuc vu tim kiem den ngay
     *            = '' or (!= 1 && != 2 ) : Su dung de update kieu dd/mm/yyyy h:i:s vao CSDL
     * @return chuoi dang yyyy/mm/dd(De dua vao csdl)
     */
    public static function _ddmmyyyyToYYyymmdd($psDdmmyyyy, $iSearch = '')
    {
        $psdate = NULL;
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $psDdmmyyyy)) {
            return $psDdmmyyyy;
        }
        $psdateArr = "";
        $psdel = "";
        if (strlen($psDdmmyyyy) == 0 or is_null($psDdmmyyyy)) {
            $psdate = "";
            return $psdate;
        }
        if (strpos($psDdmmyyyy, "-") <= 0 and strpos($psDdmmyyyy, "/") <= 0) {
            $psdate = "";
            return $psdate;
        }
        if (strpos($psDdmmyyyy, "-") > 0) {
            $psdel = "-";
        }
        if (strpos($psDdmmyyyy, "/") > 0) {
            $psdel = "/";
        }
        $arr = explode(" ", $psDdmmyyyy);
        if ($arr[0] <> "") {
            $psdateArr = explode($psdel, $arr[0]);
            if (sizeof($psdateArr) <> 3) {
                $psdate = NULL;
                return $psdate;
            } else {
                if ($iSearch == 2)
                    $psdate = $psdateArr[2] . "/" . $psdateArr[1] . "/" . $psdateArr[0] . ' ' . gmdate("23:59:59");
                else if ($iSearch == 1)
                    $psdate = $psdateArr[2] . "/" . $psdateArr[1] . "/" . $psdateArr[0];
                else
                    $psdate = $psdateArr[2] . "/" . $psdateArr[1] . "/" . $psdateArr[0] . ' ' . gmdate("H:i:s", time() + 3600 * (7 + date("0")));
                return $psdate;
            }
        }
        return $psdate;
    }

    /**
     * Creater: TRUONGDV
     *
     * @param $strText : chuoi ky tu can chuyen font tu VN sang EN
     * @return tra ve chuoi khong dau
     */
    public function _convertVNtoEN($strText)
    {
        $vnChars = array("á", "à", "ả", "ã", "ạ", "ă", "ắ", "ằ", "ẳ", "ẵ", "ặ", "â", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ê", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "ơ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "đ", "Á", "﻿À", "Ả", "Ã", "Ạ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Â", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ê", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ô", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "Đ");
        $enChars = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D");
        for ($i = 0; $i < sizeof($vnChars); $i++) {
            $strText = str_replace($vnChars[$i], $enChars[$i], $strText);
        }
        return $strText;
    }

    public static function _convertToMoney($sValue, $dili = ',')
    {
        $sValue = trim($sValue);
        if ($sValue == 0) return '';
        $count = strlen((string)$sValue);
        $begin = 0;
        if ($sValue * 1 < 0)
            $begin = 1;
        if ($count > 3) {
            $k = -1;
            $strValue = "";
            for ($i = $count - 1; $i >= $begin; $i--) {
                $k++;
                if ($k % 3 == 0 && $k > 0) {
                    $strValue = $strValue . $dili;
                }
                $strValue = $strValue . $sValue[$i];
            }
            if ($begin == 1)
                return '-' . strrev($strValue);
            else
                return strrev($strValue);
        }
        return $sValue;
    }

    public function _BsVndText($amount)
    {
        $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                        $unread[$k] = 1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1)
                continue;

            if (($i % 3 == 0) && ($i > 0))
                $textnumber = $Text[$so] . ' ' . $TextLuythua[$i / 3] . " " . $textnumber;

            if ($i % 3 == 2)
                $textnumber = $Text[$so] . ' ' . 'trăm ' . $textnumber;

            if ($i % 3 == 1)
                $textnumber = $Text[$so] . ' ' . 'mươi ' . $textnumber;
        }
        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);
        return ucfirst($textnumber . " đồng chẵn");
    }

    /**
     * Creater: TRUONGDV
     *    08/04/2015
     * @param $sText : Chuyen doi chuoi tu TCVN3 sang Unicode
     * @return tra ve font Unicode
     */
    function _convertTCVN3ToUnicode($inputText)
    {
        $uniChars = array("á", "à", "ả", "ã", "ạ", "ă", "ắ", "ằ", "ẳ", "ẵ", "ặ", "â", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ê", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "ơ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "đ", "Á", "﻿À", "Ả", "Ã", "Ạ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Â", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ê", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ô", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "Đ");
        $tcvnChars = array("¸", "µ", "¶", "·", "¹", "¨", "¾", "»", "¼", "½", "Æ", "©", "Ê", "Ç", "È", "É", "Ë", "Ð", "Ì", "Î", "Ï", "Ñ", "ª", "Õ", "Ò", "Ó", "Ô", "Ö", "Ý", "×", "Ø", "Ü", "Þ", "ã", "ß", "á", "â", "ä", "«", "è", "å", "æ", "ç", "é", "¬", "í", "ê", "ë", "ì", "î", "ó", "ï", "ñ", "ò", "ô", "­", "ø", "õ", "ö", "÷", "ù", "ý", "ú", "û", "ü", "þ", "®", "¸", "#µ", "¶", "·", "¹", "¡", "¾", "»", "¼", "½", "Æ", "¢", "Ê", "Ç", "È", "É", "Ë", "Ð", "Ì", "Î", "Ï", "Ñ", "£", "Õ", "Ò", "Ó", "Ô", "Ö", "Ý", "×", "Ø", "Ü", "Þ", "ã", "ß", "á", "â", "ä", "¤", "è", "å", "æ", "ç", "é", "¥", "í", "ê", "ë", "ì", "î", "ó", "ï", "ñ", "ò", "ô", "¦", "ø", "õ", "ö", "÷", "ù", "ý", "ú", "û", "ü", "þ", "§");
        for ($i = 0; $i < sizeof($tcvnChars); $i++) {
            $outputText = str_replace($tcvnChars[$i], $uniChars[$i], $inputText);
        }
        return $outputText;
    }

    /**
     * Creater: TRUONGDV
     *    08/04/2015
     * @param $sText : Chuyen doi chuoi tu Unicode  sang TCVN3
     * @return tra ve font TCVN3
     */
    function _convertUnicodeToTCVN3($inputText)
    {
        $uniChars = array("á", "à", "ả", "ã", "ạ", "ă", "ắ", "ằ", "ẳ", "ẵ", "ặ", "â", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ê", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "ơ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "đ", "Á", "﻿À", "Ả", "Ã", "Ạ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Â", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ê", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ô", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "Đ");
        $tcvnChars = array("¸", "µ", "¶", "·", "¹", "¨", "¾", "»", "¼", "½", "Æ", "©", "Ê", "Ç", "È", "É", "Ë", "Ð", "Ì", "Î", "Ï", "Ñ", "ª", "Õ", "Ò", "Ó", "Ô", "Ö", "Ý", "×", "Ø", "Ü", "Þ", "ã", "ß", "á", "â", "ä", "«", "è", "å", "æ", "ç", "é", "¬", "í", "ê", "ë", "ì", "î", "ó", "ï", "ñ", "ò", "ô", "­", "ø", "õ", "ö", "÷", "ù", "ý", "ú", "û", "ü", "þ", "®", "¸", "#µ", "¶", "·", "¹", "¡", "¾", "»", "¼", "½", "Æ", "¢", "Ê", "Ç", "È", "É", "Ë", "Ð", "Ì", "Î", "Ï", "Ñ", "£", "Õ", "Ò", "Ó", "Ô", "Ö", "Ý", "×", "Ø", "Ü", "Þ", "ã", "ß", "á", "â", "ä", "¤", "è", "å", "æ", "ç", "é", "¥", "í", "ê", "ë", "ì", "î", "ó", "ï", "ñ", "ò", "ô", "¦", "ø", "õ", "ö", "÷", "ù", "ý", "ú", "û", "ü", "þ", "§");
        for ($i = 0; $i < sizeof($uniChars); $i++) {
            $outputText = str_replace($uniChars[$i], $tcvnChars[$i], $inputText);
        }
        return $outputText;
    }

    public static function Lower2Upper($strText)
    {
        $strLC = "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|đ|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|í|ì|ỉ|ĩ|ị|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|ý|ỳ|ỷ|ỹ|ỵ'";
        $strUC = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|Đ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Í|Ì|Ỉ|Ĩ|Ị|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự|Ý|Ỳ|Ỷ|Ỹ|Ỵ'";
        //$strLC = "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|Ã¡|Ã |áº£|Ã£|áº¡|Äƒ|áº¯|áº·|áº±|áº³|áºµ|Ã¢|áº¥|áº§|áº©|áº«|áº­|Ä‘|Ã©|Ã¨|áº»|áº½|áº¹|Ãª|áº¿|á»|á»ƒ|á»…|á»‡|Ã­|Ã¬|á»‰|Ä©|á»‹|Ã³|Ã²|á»|Ãµ|á»|Ã´|á»‘|á»“|á»•|á»—|á»™|Æ¡|á»›|á»|á»Ÿ|á»¡|á»£|Ãº|Ã¹|á»§|Å©|á»¥|Æ°|á»©|á»«|á»­|á»¯|á»±|Ã½|á»³|á»·|á»¹|á»µ'";
        //$strUC = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|Ã|Ã€|áº¢|Ãƒ|áº |Ä‚|áº®|áº¶|áº°|áº²|áº´|Ã‚|áº¤|áº¦|áº¨|áºª|áº¬|Ä|Ã‰|Ãˆ|áºº|áº¼|áº¸|ÃŠ|áº¾|á»€|á»‚|á»„|á»†|Ã|ÃŒ|á»ˆ|Ä¨|á»Š|Ã“|Ã’|á»Ž|Ã•|á»Œ|Ã”|á»|á»’|á»”|á»–|á»˜|Æ |á»š|á»œ|á»ž|á» |á»¢|Ãš|Ã™|á»¦|Å¨|á»¤|Æ¯|á»¨|á»ª|á»¬|á»®|á»°|Ã|á»²|á»¶|á»¸|á»´'";
        $arrLC = explode('|', $strLC);
        $arrUC = explode('|', $strUC);
        for ($i = 0; $i < sizeof($arrLC); $i++) {
            $strText = str_replace($arrLC[$i], $arrUC[$i], $strText);
        }
        return $strText;
    }

    public static function _breakLine($pContent = '')
    {
        $ilen = strlen($pContent);
        if ($ilen > 0) {
            for ($index = 0; $index < $ilen; $index++) {
                if (ord(substr($pContent, $index, 1)) == 10) {//=10 la ma xuong dau dong
                    $pContent = str_replace(chr(10), "<br>", $pContent);
                }
            }
        }
        return $pContent;
    }

    /**
     * @param $arrBody
     * @param $arrResult
     * @param string $groups
     * @return array
     */
    public function convertArrayData($arrBody, $arrResult, $groups = null)
    {
        if (array_key_exists('column_group', $groups)) $column_group = $groups['column_group'];
        if (array_key_exists('function_group', $groups)) $function_group = $groups['function_group'];
        if (array_key_exists('class_group', $groups)) $class_group = $groups['class_group'];
        if (array_key_exists('parame_group', $groups)) $parame_group = $groups['parame_group'];
        $arrTemp = array();
        $arrOut = array();
        $arrElement = array();
        $iTotal = sizeof($arrResult);
        for ($i = 0; $i < $iTotal; $i++) {
            $arrTemp = $arrResult[$i];
            $arrElement = array();
            foreach ($arrBody as $key => $aBody) {
                $sColumnName = (isset($aBody['column_name']) ? $aBody['column_name'] : '');
                $sAlias = (isset($aBody['alias_name']) ? $aBody['alias_name'] : $sColumnName);
                $phpfunction = (isset($aBody['phpfunction']) ? $aBody['phpfunction'] : '');
                $params = (isset($aBody['param']) ? $aBody['param'] : '');
                $arrParamFunc = array();
                $value = (isset($arrTemp[$sAlias]) ? $arrTemp[$sAlias] : '');
                $value = $this->_restoreBadChar($value);
                if ($params != '') {
                    $arrParams = explode(',', $params);
                    foreach ($arrParams as $key => $param) {
                        $param = trim($param);
                        if ($this->checkIndexArray($arrBody, $param)) {
                            $paramin = (isset($arrTemp[$param]) ? $arrTemp[$param] : '');
                        } else {
                            $paramin = $param;
                        }
                        // $arrParamFunc[] = (isset($arrTemp[$param]) ? $arrTemp[$param] : $param);
                        $arrParamFunc[] = $paramin;
                    }
                }
                $classname = (isset($aBody['classname']) ? $aBody['classname'] : '');
                if ($phpfunction != '' && $classname != '') {
                    $objClass = new $classname;
                    $value = $objClass->$phpfunction($arrParamFunc);
                    $index = 'f_' . $sAlias;
                    $arrTemp[$index] = $value;
                }
                // $arrElement[$sColumnName] = $value;
            }
            // group
            if ($column_group != '' && $function_group != '' && $class_group != '') {
                $arrParamFunc = array();
                $value = (isset($arrTemp[$column_group]) ? $arrTemp[$column_group] : '');
                if ($parame_group != '') {
                    $arrParams = explode(',', $parame_group);
                    foreach ($arrParams as $key => $param) {
                        $param = trim($param);
                        $arrParamFunc[] = (isset($arrTemp[$param]) ? $arrTemp[$param] : $param);
                    }
                }
                $objClassGroup = new $class_group;
                $value = $objClassGroup->$function_group($arrParamFunc);
                $index = 'f_' . $column_group;
                $arrTemp[$index] = $value;
            }
            array_push($arrOut, $arrTemp);
        }
        return $arrOut;
    }
    /**
     * @param $arrBody
     * @param $param
     * @return bool
     */
    private function checkIndexArray($arrBody, $param)
    {
        foreach ($arrBody as $key => $value) {
            $sColumnName = (isset($value['column_name']) ? $value['column_name'] : '');
            $sAlias = (isset($value['alias_name']) ? $value['alias_name'] : $sColumnName);
            if ($param == $sAlias) {
                return true;
            }
        }
        return false;
    }
}