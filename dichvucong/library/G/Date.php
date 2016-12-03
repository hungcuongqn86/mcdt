<?php
require_once 'G/Objects/Date.php';

/**
 *
 */
class G_Date extends G_Objects_Date
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    protected $lunarHoliday;
    protected $solarHoliday;
    protected $tetHoliday;
    protected $satTime;
    protected $am_start_time;
    protected $am_stop_time;
    protected $pm_start_time;
    protected $pm_stop_time;
    protected $work_time;
    protected $iTimeAm;
    protected $iTimePm;
    protected $startedUsing = 2013;
    protected $timeZone;
    protected $arrSolar = array();
    protected $arrLunar = array();
    protected $arrTet = array();


    function __construct($config = array())
    {
        if ($config) {
            $this->lunarHoliday = $config['lunarHoliday'];
            $this->solarHoliday = $config['solarHoliday'];
            $this->tetHoliday = $config['tetHoliday'];
            $this->satTime = $config['satTime'];

            $this->am_start_time = $config['am_start_time'];
            $this->am_stop_time = $config['am_stop_time'];
            $this->pm_start_time = $config['pm_start_time'];
            $this->satTime = $config['satTime'];

            $this->work_time = ($this->pm_stop_time - $this->pm_start_time + $this->am_stop_time - $this->am_start_time) * 3600;
            $this->iTimeAm = ($this->am_stop_time - $this->am_start_time) * 3600;
            $this->iTimePm = ($this->pm_stop_time - $this->pm_start_time) * 3600;
            $this->timeZone = (int)date('O') / 100;

            $this->arrSolar = $this->getSolarBySolar();
            $this->arrLunar = $this->getSolarByLunarHoliday();
            $this->arrTet = $this->getTetByTetHoliday();

        } else {
            $objCache = new G_Cache();
            $sysConst = Zend_Registry::get('__sysConst__');
            $this->lunarHoliday = $sysConst->lunarHoliday;
            $this->solarHoliday = $sysConst->solarHoliday;
            $this->tetHoliday = $sysConst->tetHoliday;
            $this->satTime = $sysConst->satTime;
            $this->am_start_time = $sysConst->am_start_time;
            $this->am_stop_time = $sysConst->am_stop_time;
            $this->pm_start_time = $sysConst->pm_start_time;
            $this->pm_stop_time = $sysConst->pm_stop_time;

            $this->work_time = ($this->pm_stop_time - $this->pm_start_time + $this->am_stop_time - $this->am_start_time) * 3600;
            $this->iTimeAm = ($this->am_stop_time - $this->am_start_time) * 3600;
            $this->iTimePm = ($this->pm_stop_time - $this->pm_start_time) * 3600;
            $this->timeZone = (int)date('O') / 100;

            $arrData = $objCache->load_cache('NGAY_NGHI_LE_TET');
            if ($arrData == false) {
                $arrData = $this->getData();
                $objCache->save_cache($arrData, 'NGAY_NGHI_LE_TET');
            }
            $this->arrSolar = $arrData['solar'];
            $this->arrLunar = $arrData['lunar'];
            $this->arrTet = $arrData['tet'];

        }
    }

    public function getData()
    {
        $arrData = G_Cache::getInstance()->get_system_config();
        $self = new G_Date($arrData);
        $arrInput = array(
            'solar' => $self->getSolar(),
            'lunar' => $self->getLunar(),
            'tet' => $self->getTet()
        );
        return $arrInput;
    }

    public function getSolar()
    {
        return $this->arrSolar;
    }

    public function getLunar()
    {
        return $this->arrLunar;
    }

    public function getTet()
    {
        return $this->arrTet;
    }

    public function getSolarBySolar()
    {
        $currentYear = date('Y');
        $arrSolar = array();
        for ($year = $this->startedUsing; $year <= $currentYear; $year++) {
            // $arrSolar = array_merge($arrSolar, $this->getSolarOfYearByLunar($year));
            $arrp = explode(',', $this->solarHoliday);
            for ($i = 0; $i < sizeof($arrp); $i++) {
                $arrD = explode('/', $arrp[$i]);
                if (strlen($arrD[1]) == 1) $arrD[1] = '0' . $arrD[1];
                if (strlen($arrD[0]) == 1) $arrD[0] = '0' . $arrD[0];
                $d = $year . '/' . $arrD[1] . '/' . $arrD[0];
                array_push($arrSolar, $d);
            }
            array_push($arrSolar, $d);
        }
        return $arrSolar;
    }

    public function getSolarByLunarHoliday()
    {
        $currentYear = date('Y');
        $arrLunar = explode(',', $this->lunarHoliday);
        $arrSolar = array();
        for ($year = $this->startedUsing; $year <= $currentYear; $year++) {
            $arrSolar = array_merge($arrSolar, $this->getSolarOfYearByLunar($year, $arrLunar));
        }
        return $arrSolar;
    }

    public function getTetByTetHoliday()
    {
        $arrTet = explode(',', $this->tetHoliday);
        $currentYear = date('Y');
        $arrSolar = array();
        for ($year = $this->startedUsing; $year <= $currentYear; $year++) {
            $arrSolar = array_merge($arrSolar, $this->getSolarOfYearByLunar($year, $arrTet));
        }
        return $arrSolar;
    }

    public function getSolarOfYearByLunar($year, $arrDay)
    {
        $arrSolar = array();
        foreach ($arrDay as $key => $day) {
            $arrp = explode('/', $day);
            $d = $year . '/' . $arrp[1] . '/' . $arrp[0];
            $lunarLeap = $this->checkLunarLeap($d, $this->timeZone);
            $date = $this->convertLunar2Solar($arrp[0], $arrp[1], $year, $lunarLeap, $this->timeZone);
            if (strlen($date[1]) == 1) $date[1] = '0' . $date[1];
            if (strlen($date[0]) == 1) $date[0] = '0' . $date[0];
            array_push($arrSolar, $date[2] . '/' . $date[1] . '/' . $date[0]);

        }
        return $arrSolar;
    }

    public function get_appointed_date($begin_date, $number_dates, $minutes = 0)
    {
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $am_stop_time = $this->am_stop_time;
        $pm_start_time = $this->pm_start_time;
        $pm_stop_time = $this->pm_stop_time;
        $work_time = $this->work_time;
        $iTimeAm = $this->iTimeAm;
        $iTimePm = $this->iTimePm;
        $begin_date = $this->resetdatenormal($begin_date);
        $iTimeRestOfDay = $this->_gettimeworkofday($begin_date);
        $secondsApoited = $work_time * $number_dates + $minutes * 60;

        $iTotalRest = $secondsApoited - $iTimeRestOfDay;

        $begin_date1 = $begin_date;
        $arrBgDate = date_parse($begin_date1);
        if ($arrBgDate['hour'] < $am_start_time) {
            $begin_date1 = date('Y/m/d', strtotime($begin_date1));
            $begin_date1 = date('Y/m/d H:i:s', strtotime($begin_date1) + $am_start_time * 3600);
        }
        if ($arrBgDate['hour'] < $pm_start_time && $arrBgDate['hour'] >= $am_stop_time) {
            $begin_date1 = date('Y/m/d', strtotime($begin_date1));
            $begin_date1 = date('Y/m/d H:i:s', strtotime($begin_date1) + $pm_start_time * 3600);
        }
        if ($arrBgDate['hour'] >= $pm_stop_time) {
            $begin_date1 = date('Y/m/d', strtotime('+1 day', strtotime($begin_date1)));
            $begin_date1 = date('Y/m/d H:i:s', strtotime($begin_date1) + $am_start_time * 3600);
        }

        $iTimeRestOfDay1 = $this->_gettimeworkofday($begin_date1);
        $iTotalRest1 = $secondsApoited - $iTimeRestOfDay1;
        if ($iTotalRest1 < 0) {#Han xu ly thuoc ngay hien tai
            #Thoi gian gia dinh con lai trong buoi sang
            $z = abs($iTotalRest1) - ($pm_stop_time - $pm_start_time) * 3600;
            if ($z >= 0) {#Han xu ly thuoc buoi sang cung ngay
                $y = date('Y/m/d', strtotime($begin_date1));
                $y = date('Y/m/d H:i:s', strtotime($y) + ($am_stop_time * 3600 - $z));
                return $y;
            } else {#Han xu ly thuoc buoi chieu cung ngay
                $y = date('Y/m/d', strtotime($begin_date1));
                $y = date('Y/m/d H:i:s', strtotime($y) + ($pm_stop_time * 3600 - abs($iTotalRest1)));
                return $y;
            }
        } else {#Han xu ly thuoc nhung ngay tiep theo
            $number_dates = floor($iTotalRest / $work_time);
            $number_times = $iTotalRest % $work_time;
            $count_date = 0;
            $number_dates = (int)($number_dates);
            $dateStart = date('Y/m/d', strtotime($begin_date));
            $d = date('Y/m/d H:i:s', strtotime($dateStart) + $am_start_time * 3600);
            $timeZone = $this->timeZone;
            while ($count_date <= $number_dates) {
                $count_date++;
                // Ngay tiep theo
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
                $day = date('Y/m/d', strtotime($d));
                if (in_array($day, $this->arrTet))
                    $count_date--;

                if (((date("l", strtotime($d)) == 'Saturday' && $satTime == 0) || date("l", strtotime($d)) == 'Sunday') && !in_array($day, $this->arrTet))
                    $count_date--;
                //
                if ((date("l", strtotime($d)) == 'Saturday' && $satTime == 1) && !in_array($day, $this->arrTet)) {
                    //$count_date--;
                    if ($count_date <= $number_dates)
                        $number_times = $number_times + $iTimeAm;
                }
                if (in_array($day, $this->arrLunar) && !in_array($day, $this->arrTet))
                    $count_date--;
                if (in_array($day, $this->arrSolar) && !in_array($day, $this->arrTet))
                    $count_date--;
            }
            // echo $number_times/3600;
            if ($number_times > 0) {
                $number_dates = $number_times / $work_time;
                if ($number_dates >= 1) {
                    return $this->get_appointed_date($d, $number_dates);
                } else {
                    return $this->setTimeAppoint($d, $number_times);
                }
            } else {
                return date('Y/m/d H:i:s', strtotime($d));
            }
        }
    }

    /*
    Creater: Truongdv
    Des: Hàm tính thời điểm hẹn trả
    d : Ngày hẹn trả
    number_times : Số giay  xử lý
    */
    public function setTimeAppoint($d, $number_times)
    {
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $am_stop_time = $this->am_stop_time;
        $pm_start_time = $this->pm_start_time;
        $pm_stop_time = $this->pm_stop_time;
        $timeZone = $this->timeZone;
        $option = 0;
        //Tinh so h lam viec con lai trong ngay hen tra
        $work_time = $this->work_time;
        $iTimeAm = $this->iTimeAm;
        $iTimePm = $this->iTimePm;

        $day = date('Y/m/d', strtotime($d));

        if (date("l", strtotime($d)) == 'Sunday' && !in_array($day, $this->arrTet))
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        if (in_array($day, $this->arrLunar) && !in_array($day, $this->arrTet))
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        if (in_array($day, $this->arrSolar) && !in_array($day, $this->arrTet))
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        if (in_array($day, $this->arrTet))
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        if (date("l", strtotime($d)) == 'Saturday' && !in_array($day, $this->arrTet)) {
            if ($satTime == 0) {
                $d = date('Y/m/d', strtotime('+1 day', strtotime($d)));
                $d = date('Y/m/d', strtotime('+1 day', strtotime($d)));
            } else if ($satTime == 1)
                $option = 1;
            else {
                $option = 2;
            }
        }
        $iTimeRestPm = 0;
        if ($number_times < $iTimeAm) {
            // Buoi sang
            return date('Y/m/d H:i:s', strtotime($d) + $number_times);
        } else {
            $iTimeRestPm = $number_times - $iTimeAm;
            //
        }
        // Buoi chieu
        if ($option == 1) {//Nếu ngày hẹn trả vào T7 và T7 làm buổi sáng => thời điểm hẹn trả vào thứ 2
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
            $dateStart = date('Y/m/d', strtotime($d));
            $timeDayAm = date('Y/m/d H:i:s', strtotime($dateStart) + $am_start_time * 3600);
            return date('Y/m/d H:i:s', strtotime($timeDayAm) + $iTimeRestPm);
        } else {//Neu hom sau la thu 7 va T7 lam ca ngay hoặc là ngày bình thương
            $dateStart = date('Y/m/d', strtotime($d));
            $timeDayPm = date('Y/m/d H:i:s', strtotime($dateStart) + $pm_start_time * 3600);
            return date('Y/m/d H:i:s', strtotime($timeDayPm) + $iTimeRestPm);
        }
    }

    private function resettime($d)
    {
        $satTime = $_SESSION['satTime'];
        $am_start_time = $_SESSION['am_start_time'];
        $am_stop_time = $_SESSION['am_stop_time'];
        $pm_start_time = $_SESSION['pm_start_time'];
        $pm_stop_time = $_SESSION['pm_stop_time'];
        // T7 lam buoi sang
        $begin_time = date('H', strtotime($d));
        if ((date("l", strtotime($d)) == 'Saturday' && $satTime == 1 && ($begin_time > $pm_start_time && $begin_time < $pm_stop_time))) {
            $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        }
        $begin_date = $d;
        $arrBgDate = date_parse($begin_date);
        if ($arrBgDate['hour'] < $am_start_time) {
            $begin_date = date('Y/m/d', strtotime($begin_date));
            $begin_date = date('Y/m/d H:i:s', strtotime($begin_date) + $am_start_time * 3600);
        }
        if ($arrBgDate['hour'] < $pm_start_time && $arrBgDate['hour'] >= $am_stop_time) {
            $begin_date = date('Y/m/d', strtotime($begin_date));
            $begin_date = date('Y/m/d H:i:s', strtotime($begin_date) + $pm_start_time * 3600);
        }
        if ($arrBgDate['hour'] >= $pm_stop_time) {
            $begin_date = date('Y/m/d', strtotime('+1 day', strtotime($begin_date)));
            $begin_date = date('Y/m/d H:i:s', strtotime($begin_date) + $am_start_time * 3600);
        }
        return $begin_date;
    }

    public function resetdatenormal($d)
    {
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $d = $this->resettime($d);
        while (!$this->checknormaldate($d)) {
            $d = date('Y/m/d', strtotime('+1 day', strtotime($d)));
            $d = date('Y/m/d H:i:s', strtotime($d) + $am_start_time * 3600);
        }
        $d = $this->resettime($d);
        return $d;
    }

    private function checknormaldate($d)
    {
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $am_stop_time = $this->am_stop_time;
        $pm_start_time = $this->pm_start_time;
        $pm_stop_time = $this->pm_stop_time;
        $work_time = $this->work_time;
        $iTimeAm = $this->iTimeAm;
        $iTimePm = $this->iTimePm;
        $timeZone = $this->timeZone;
        $day = date('Y/m/d', strtotime($d));

        if (!in_array($day, $this->arrSolar) && !in_array($day, $this->arrTet) && !in_array($day, $this->arrLunar) && date("l", strtotime($d)) != 'Saturday' && date("l", strtotime($d)) != 'Sunday') {
            return true;
        }
        // Kiem tra co phia ngay nghi tet ko
        if (in_array($day, $this->arrTet)) {
            return false;
        }
        // Neu T7 la ngay nghi
        if ((date("l", strtotime($d)) == 'Saturday' && $satTime == 0)) {
            return false;
        }
        if (in_array($day, $this->arrLunar)) {
            return false;
        }
        // Ngay chu nhat nghi
        if (date("l", strtotime($d)) == 'Sunday') {
            return false;
        }

        if (in_array($day, $this->arrSolar)) {
            return false;
        }
        return true;
    }

    /*
        Lấy thời gian làm việc còn lại của 1 ngày
    */
    public function _gettimeworkofday($date)
    {
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $am_stop_time = $this->am_stop_time;
        $pm_start_time = $this->pm_start_time;
        $pm_stop_time = $this->pm_stop_time;
        $work_time = $this->work_time;
        $iTimeAm = $this->iTimeAm;
        $iTimePm = $this->iTimePm;
        $startcurrentdate = date('Y/m/d', strtotime($date));
        $begin_time = (int)date('H', strtotime($date));
        $iTimeRestOfDayBegin = 0;
        if (date("l", strtotime($date)) == 'Saturday') {
            if ($satTime == 0) { // Nghỉ làm thứ 7
                return 0;
            } elseif ($satTime == 1) {
                // Ngay T7 lam buoi sang
                $iStartCurrent = strtotime($startcurrentdate) + $am_stop_time * 3600;
                $iTimeRestOfDayBegin = $iStartCurrent - strtotime($date);
                return $iTimeRestOfDayBegin;
            }
        }
        // La ngay lam viec binh thuong lam ca ngay
        if ($begin_time >= $am_start_time && $begin_time <= $am_stop_time) {
            // Buoi sang
            $iStartCurrent = strtotime($startcurrentdate) + $am_stop_time * 3600;
            $iTimeRestOfDayBegin = $iStartCurrent - strtotime($date) + $iTimePm;
        } elseif ($begin_time >= $pm_start_time && $begin_time <= $pm_stop_time) {
            // Buoi chieu
            $iStartCurrent = strtotime($startcurrentdate) + $pm_stop_time * 3600;
            $iTimeRestOfDayBegin = $iStartCurrent - strtotime($date);
        }
        return $iTimeRestOfDayBegin;
    }

    /*
        Tinh so ngay xu ly trong khoang thoi gian $begin_date -> $finish_date
    */
    public function _diffdate($begin_date, $finish_date)
    {
        $strtotime_begin_date = (int)strtotime($begin_date);
        $strtotime_finish_date = (int)strtotime($finish_date);
        if ($strtotime_begin_date == 0 || $strtotime_finish_date == 0) {
            return 0;
        }
        if ($strtotime_begin_date > $strtotime_finish_date) {
            return -1 * $this->_diffdate($finish_date, $begin_date);
        }
        $satTime = $this->satTime;
        $am_start_time = $this->am_start_time;
        $am_stop_time = $this->am_stop_time;
        $pm_start_time = $this->pm_start_time;
        $pm_stop_time = $this->pm_stop_time;
        $work_time = $this->work_time;
        $iTimeAm = $this->iTimeAm;
        $iTimePm = $this->iTimePm;
        $timeZone = $this->timeZone;
        // Check va reset ve ngay binh thuong
        $begin_date = $this->resetdatenormal($begin_date);
        $finish_date = $this->resetdatenormal($finish_date);
        // Neu ngay bat dau va ngay ket thuc cung thuoc 1 ngay
        if (date('Y/m/d', strtotime($finish_date)) == date('Y/m/d', strtotime($begin_date))) {
            $iTotalProcess = $this->_gettimeworkofday($begin_date) - $this->_gettimeworkofday($finish_date);
            return round($iTotalProcess / 60);
        }
        // Tong thoi gian xu ly
        $iTotalProcess = 0;
        $begin_time = (int)date('H', strtotime($begin_date));
        // Thoi gian da lam cua ngay bat dau
        $iTimeRestOfDayBegin = $this->_gettimeworkofday($begin_date);
        // Thoi gian da lam cua ngay ket thuc
        $finish_time = date('H', strtotime($finish_date));
        $startcurrentdate = date('Y/m/d', strtotime($finish_date));
        $iTimeRestOfDayFinish = 0;
        if ($finish_time >= $am_start_time && $finish_time <= $am_stop_time) {
            // Buoi sang
            $iStartCurrent = strtotime($startcurrentdate) + $am_start_time * 3600;
            $iTimeRestOfDayFinish = strtotime($finish_date) - $iStartCurrent;
        } elseif ($finish_time >= $pm_start_time && $finish_time <= $pm_stop_time) {
            // Buoi chieu
            $iStartCurrent = strtotime($startcurrentdate) + $pm_start_time * 3600;
            $iTimeRestOfDayFinish = strtotime($finish_date) - $iStartCurrent + $iTimeAm;
        }
        $iTotalProcess = $iTimeRestOfDayBegin + $iTimeRestOfDayFinish;

        // Reset ve cuoi buoi cua ngay bat dau
        $dateStart = date('Y/m/d', strtotime($begin_date));
        $d = date('Y/m/d', strtotime('+1 day', strtotime($dateStart)));
        // Ngay tiep theo
        $d = $this->resetdatenormal($d);
        $iDateFinishHome = date('Y/m/d', strtotime($finish_date));

        $number_times = 0;
        $count = 0;
        // echo date('Y/m/d',strtotime($d)).'__'.$iDateFinishHome;die;
        // echo $d; die();
        while (date('Y/m/d', strtotime($d)) < $iDateFinishHome) {
            $count++;
            // checknormaldate
            $day = date('Y/m/d', strtotime($d));
            // Ngay thong thuong
            if (!in_array($day, $this->arrSolar) && !in_array($day, $this->arrTet) && !in_array($day, $this->arrLunar) && date("l", strtotime($d)) != 'Saturday' && date("l", strtotime($d)) != 'Sunday') {
                $number_times = $number_times + $work_time;
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
            }
            if (in_array($day, $this->arrTet))
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
            // Ngay T7 lam buoi sang
            if ((date("l", strtotime($d)) == 'Saturday' && $satTime == 1) && !in_array($day, $this->arrTet)) {
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
                $number_times = $number_times + $iTimePm;
            }
            // Ngay T7 lam ca ngay
            if ((date("l", strtotime($d)) == 'Saturday' && $satTime == 2) && !in_array($day, $this->arrTet)) {
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
                $number_times = $number_times + $work_time;
            }
            // Ngay T7 nghi hoac ngay CN
            if (((date("l", strtotime($d)) == 'Saturday' && $satTime == 0) || date("l", strtotime($d)) == 'Sunday') && !in_array($day, $this->arrTet))
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));

            if (in_array($day, $this->arrLunar) && !in_array($day, $this->arrTet))
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
            if (in_array($day, $this->arrSolar) && !in_array($day, $this->arrTet))
                $d = date('Y/m/d H:i:s', strtotime('+1 day', strtotime($d)));
        }
        // return $number_times/3600;
        $iTotalProcess = $iTotalProcess + $number_times;
        // $iTotalProcess = $iTotalProcess/$work_time;
        $iTotalProcess = $iTotalProcess / 60;
        $iTotalProcess = round($iTotalProcess);
        return $iTotalProcess;//Don vi phut xu ly
    }

    public function htmlAppointedDate($params)
    {
        $appointedDate = $params['C_APPOINTED_DATE'];
        $finishDate = $params['C_FINISH_DATE'];
        $pausedate = $params['C_PAUSE_DATE'];
        $currentDate = date('Y/m/d H:i:s');
        $htmlResult = '';
        if ($appointedDate) {
            if ($finishDate) {
                $minutes = $this->_diffdate($finishDate, $appointedDate);
                $day = $this->_convertTimeToddhhmm($minutes);
                $appointedDate = date('d/m/Y H:i', strtotime($appointedDate));
                if ($minutes > 0)
                    $htmlResult = $appointedDate . '<br><font class="fn-ontime">(Trước ' . $day . ').';
                else if ($minutes < 0)
                    $htmlResult = $appointedDate . '<br><font class="fn-overtime">(Chậm ' . $day . ').';
                else
                    $htmlResult = $appointedDate . '<br><font class="fn-ontime">(Đúng hẹn).';
            } else {
                if ($pausedate) {
                    $minutes = $this->_diffdate($pausedate, $appointedDate);
                    $day = $this->_convertTimeToddhhmm($minutes);
                    if ($minutes > 0)
                        $htmlResult = '<br><font class="prs-ontime">(Còn lại ' . $day . ').';
                    else if ($minutes < 0)
                        $htmlResult = '<br><font class="prs-overtime">(Quá hạn ' . $day . ').';
                    else
                        $htmlResult = '<br><font class="prs-ontime">(Đến hạn phải xử lý).';
                } else {
                    $minutes = $this->_diffdate($currentDate, $appointedDate);
                    $day = $this->_convertTimeToddhhmm($minutes);
                    $appointedDate = date('d/m/Y H:i', strtotime($appointedDate));
                    if ($minutes > 0)
                        $htmlResult = $appointedDate . '<br><font class="prs-ontime">(Còn lại ' . $day . ').';
                    else if ($minutes < 0)
                        $htmlResult = $appointedDate . '<br><font class="prs-overtime">(Quá hạn ' . $day . ').';
                    else
                        $htmlResult = $appointedDate . '<br><font class="prs-ontime">(Đến hạn phải xử lý).';
                }
            }
        }
        return $htmlResult;
    }

    /*
        Don vi phut
    */
    public function _convertTimeToddhhmm($sTime)
    {
        $sTime = abs($sTime);
        $sHtml = '';
        // Thoi gian tinh theo don vi phut
        if ($sTime == 0) return '';
        $work_time = $this->work_time / 60;
        $sDay = floor($sTime / $work_time);
        $sRestTime = $sTime - $sDay * $work_time;
        $sHours = floor($sRestTime / 60);
        $sMinutes = $sRestTime - $sHours * 60;
        if ($sDay > 0) {
            $sHtml .= ' ' . $sDay . ' ngày';
        }
        if ($sHours != 0 || $sMinutes != 0) {
            if ($sDay > 0)
                $sHtml .= ', ';
            $sHtml .= $sHours . ':' . $sMinutes;
        }
        return $sHtml;
    }

    public function getNewAppDate($params)
    {
        $appointedDate = $params['C_APPOINTED_DATE'];
        $pausedate = $params['C_PAUSE_DATE'];
        $currentDate = date('Y/m/d H:i:s');
        $currentDate = $this->resetdatenormal($currentDate);
        $timePause = $this->_diffdate($pausedate, $appointedDate);
        $newAppDate = $this->get_appointed_date($currentDate, '', $timePause);
        return $newAppDate;
    }

    public function getCurrentTimeWork()
    {
        $currentDate = date('Y/m/d H:i:s');
        $currentDate = $this->resetdatenormal($currentDate);
        return $currentDate;
    }

    // Thong bao han xu ly voi truong hop tra cuu theo ky bao cao
    public function noticeAppointed($appointedDate, $finishDate, $today)
    {
        $notice = '';
        $notice .= date('d/m/Y H:i', strtotime($appointedDate)) . '<br />';
        $appointedDate = date('Y/m/d H:i:s', strtotime($appointedDate));
        $time = $this->_diffdate($today, $appointedDate);
        if ($finishDate) {
            // Da ket thuc
            $today = date('Y/m/d H:i:s', strtotime($finishDate));
            $notice .= $this->noticeFinish($appointedDate, $finishDate);
        } else {
            // Chua xu ly
            $today = date('Y/m/d H:i:s', strtotime($today));
            $notice .= $this->noticeUnworked($appointedDate, $today);
        }
        return $notice;
    }

    private function noticeUnworked($appointedDate, $today)
    {
        $notice = '';
        $time = $this->_diffdate($today, $appointedDate);
        if ($time <= 0) {
            // Qua han
            $time = $time * -1;
            $notice .= '<font style="color:red;"><i>(Quá hạn ' . $this->_convertTimeToddhhmm($time) . ')</i></font>';

        } else {
            // Truoc han
            $notice .= '<font style="color:blue;"><i>(Còn lại ' . $this->_convertTimeToddhhmm($time) . ')</i></font>';
        }
        return $notice;
    }

    private function noticeFinish($appointedDate, $finishDate)
    {
        $notice = '';
        $time = $this->_diffdate($appointedDate, $finishDate);
        if ($time == 0) {
            $notice .= '(Đúng hạn)';
        } else if ($time > 0) {
            $notice .= '<font class="fn-overtime"><i>(Chậm ' . $this->_convertTimeToddhhmm($time) . ')</i></font>';
        } else {
            $time = $time * -1;
            $notice .= '<font class="fn-ontime"><i>(Trước ' . $this->_convertTimeToddhhmm($time) . ')</i></font>';
        }
        return $notice;
    }

    // Thong bao han xu ly
    public function getnoticeAppointed($appointedDate, $finishDate, $pausedate)
    {
        $notice = '';
        if ($pausedate) {
            $notice = '(Hồ sơ chờ bổ sung)';

        } else {
            $today = date('Y/m/d H:i:s');
            $notice = $this->noticeAppointed($appointedDate, $finishDate, $today);
        }
        return $notice;
    }

    function convertMinsToHoursMins($minute)
    {
        if ($minute <= 0) {
            return '0:0';
        }
        settype($minute, 'integer');
        $hours = floor($minute / 60);
        $minutes = ($minute % 60);
        return $hours . ':' . $minutes;
    }

    // Lay thoi gian thuc hien
    public function _getNumberProcessTime($arrInput)
    {
        $sHtml = '';
        $sTime = (float)$arrInput['sTime'];
        $iNumberMinute = (float)$arrInput['iNumberMinute'];
        $dAppoittedDate = $arrInput['dAppoittedDate'];
        $dFinishDate = $arrInput['dFinishDate'];
        // $dPauseDate = $arrInput['dPauseDate'];
        if ($dFinishDate) {
            // Da xu ly
            $iTimeProcessed = $sTime;
            $sHtml .= $this->convertMinsToHoursMins($iTimeProcessed) . '<br>';
            // Thoi gian tinh theo don vi phut
            $sTime = $iNumberMinute - $sTime;
            if ($sTime == 0) {
                $sHtml .= '<font class="fn-ontime">Đúng tiến độ</font>';
            } elseif ($sTime > 0) {
                // Con thoi gian xu ly
                $sHtml .= '<font class="fn-ontime">(Trước hạn ' . $this->_convertTimeToddhhmm($sTime) . ')</font>';
            } else {
                $sTime = -1.0 * $sTime;
                $sHtml .= '<font class="fn-overtime">(Quá hạn ' . $this->_convertTimeToddhhmm($sTime) . ')</font>';
            }
            $sHtml .= '</p>';
            return array('iTime' => $iTimeProcessed, 'sHtml' => $sHtml);
        } else {
            // Chua xu ly
            if ($dAppoittedDate == '') {
                return '';
            }
            if ($sTime) {
                // Chua ket thuc, da tinh thoi gian thuc hien
                $iTimeProcessed = $sTime;
                $iTotalTimeRestProcess = $iNumberMinute - $iTimeProcessed;

            } else {
                // Chua ket thuc, chua tinh thoi gian thuc hien
                $dDateStart = date('Y/m/d H:i:s');
                $dAppoittedDate = date('Y/m/d H:i:s', strtotime($dAppoittedDate));
                $iTotalTimeRestProcess = $this->_diffdate($dDateStart, $dAppoittedDate);
                $iTimeProcessed = $iNumberMinute - $iTotalTimeRestProcess;
            }
            $sHtml .= $this->convertMinsToHoursMins($iTimeProcessed) . '<br>';
            if ($iTotalTimeRestProcess == 0) {
                $sHtml .= '<font class="prs-ontime">(Tới hạn)</font>';
            } else if ($iTotalTimeRestProcess > 0) {
                // Con thoi gian xu ly
                $sHtml .= '<font class="prs-ontime">(Còn lại ' . $this->_convertTimeToddhhmm($iTotalTimeRestProcess) . ')</font>';
            } else {
                $iTotalTimeRestProcess = -1.0 * $iTotalTimeRestProcess;
                $sHtml .= '<font class="prs-overtime">(Quá hạn ' . $this->_convertTimeToddhhmm($iTotalTimeRestProcess) . ')</font>';
            }
            return array('iTime' => $iTimeProcessed, 'sHtml' => $sHtml);
        }
    }
}

?>