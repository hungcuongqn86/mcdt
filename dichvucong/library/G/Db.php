<?php

/**
 *
 */
class G_Db
{
    protected static $_instance = null;
    protected $dbAdapter;

    public function __construct()
    {
        $this->dbAdapter = Zend_Registry::get('dbAdapter');
    }

    public function getAdapter()
    {
        return $this->dbAdapter;
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function BeginTrans()
    {
        $this->dbAdapter->BeginTrans();
    }

    public function CommitTrans()
    {
        $this->dbAdapter->CommitTrans();
    }

    public function RollbackTrans()
    {
        $this->dbAdapter->RollbackTrans();
    }

    public function StartTrans()
    {
        $this->dbAdapter->StartTrans();
    }

    public function CompleteTrans()
    {
        $this->dbAdapter->CompleteTrans();
    }

    public function qstr($string) {
        return $this->dbAdapter->qstr($string);
    }

    public function adodbExecSqlString($sql)
    {
        $this->dbAdapter->SetFetchMode(ADODB_FETCH_ASSOC);
        $data = $this->dbAdapter->GetRow($sql);
        return $data;
    }

    public function pdoExecSqlStringReturnNone($sql)
    {
        $arrResul = $this->dbAdapter->query($sql);
        return $arrResul;
    }

    /**
     * Creater: CUONGNH
     * date:
     * Lay tat ca thong tin trong CSDL, phan tu dang chi so bat dau tu 0,1,2,...
     * @param $sql : Xau SQL can thuc thi
     * @param $optCache : Tuy chon co cache hay khong? <> "" thi thuc hien cache
     * @return Mang luu thong tin du lieu
     */
    public function adodbQueryDataInNumberMode($sql, $optCache = "")
    {
        //Thoi gian Cache
        $this->dbAdapter->SetFetchMode(ADODB_FETCH_NUM);
        if ($optCache == "") {
            //echo "1";
            $data = $this->dbAdapter->GetArray($sql);
        } else {
            global $ADODB_CACHE_TIMEOUT;
            $cacheTime = $ADODB_CACHE_TIMEOUT;
            $data = $this->dbAdapter->CacheGetAll($cacheTime, $sql);
        }
        return $data;

    }

    /**
     * Creater: TRUONGDV
     * date:
     * Lay tat ca thong tin trong CSDL, phan tu dang ten cot
     * @param $sql : Xau SQL can thuc thi
     * @param $optCache : Tuy chon co cache hay khong? <> "" thi thuc hien cache
     *
     * @return Mang luu thong tin du lieu
     */
    public function adodbQueryDataInNameMode($sql, $optCache = "")
    {
        //Thoi gian Cache
        $this->dbAdapter->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($optCache == "") {
            $data = $this->dbAdapter->GetArray($sql);
        } else {
            global $ADODB_CACHE_TIMEOUT;
            $cacheTime = $ADODB_CACHE_TIMEOUT;
            $data = $this->dbAdapter->CacheGetAll($cacheTime, $sql);
        }
        return $data;
    }

    public function getStringSql($arrParameter)
    {
        $sql = '';
        if (is_array($arrParameter)) {
            foreach ($arrParameter as $key => $value) {
                if ($sql != '') {
                    $sql .= "," . $this->qstr($value);
                } else {
                    $sql .= $this->qstr($value);
                }
            }
        }
        return $sql;
    }

    /**
     * @author: TRUONGDV
     * @since: 25/12/2012
     * @todo : Ham thuc hien query du lieu
     * @param : $arrParameter: Mang du lieu truyen vao csdl
     * @param : $spName: Ten procedure
     * @param : $optionQuery: Loai query
     * @param : $optionReturn: kieu tra ve: true trả về chuỗi sql chưa đc query, false tra ve de lieu dc query
     */
    public function _querySql($arrParameter, $spName, $optionQuery = true, $optionReturn = false)
    {
        $arrResult = array();
        $sql = $spName . ' ' . $this->getStringSql($arrParameter);
        if ($optionReturn) {
            return $sql;
        }
        try {
            if ($optionQuery) {
                $arrResul = $this->adodbQueryDataInNameMode($sql);
            } else {
                $arrResul = $this->adodbExecSqlString($sql);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrResul;
    }
}