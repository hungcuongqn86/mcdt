<?php
require_once 'Plugin/adodb/adodb.inc.php';

/**
 * Class Extra_Db
 */
class Extra_Db extends Zend_Db {
    /**
     * @param $adapter
     * @param array $config
     * @return the
     */
	public static function connectADO($adapter, $config = array()){		
		global $adoConn, $ADODB_CACHE_DIR, $ADODB_CACHE_TIMEOUT;
		if($adapter == "MSSQL"){//Ket noi MS SQL server
			//Tao doi tuong ADODB
			$adoConn = NewADOConnection("ado_mssql");  // create a connection
			$connStr = "Provider=SQLOLEDB; Data Source=" . $config['host'] . ";Initial Catalog='" . $config['dbname'] . "'; User ID=" . $config['username'] . "; Password=" .$config['password'];
			//call connect adodb
			$adoConn->Connect($connStr) or die("Hien tai he thong khong the ket noi vao CSDL duoc!");
		}
		$ADODB_CACHE_DIR  		= $config['pathAdoCache'];
		$ADODB_CACHE_TIMEOUT 	= $config['cachetimeout'];
		return $adoConn;
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
    	global $adoConn;
        $adoConn->BeginTrans();
    }

    public function CommitTrans()
    {
    	global $adoConn;
        $adoConn->CommitTrans();
    }

    public function RollbackTrans()
    {
    	global $adoConn;
        $adoConn->RollbackTrans();
    }

    public function StartTrans()
    {
    	global $adoConn;
        $adoConn->StartTrans();
    }

    public function CompleteTrans()
    {
    	global $adoConn;
        $adoConn->CompleteTrans();
    }

    public function qstr($string) {
    	global $adoConn;
        return $adoConn->qstr($string);
    }

    /**
     * @param $sql
     * @return mixed
     */
	public function adodbExecSqlString($sql){
		global $adoConn;
		$adoConn->SetFetchMode(ADODB_FETCH_ASSOC);
		$ArrSingleData = $adoConn->GetRow($sql); 
		return $ArrSingleData;
	}

    /**
     * @param $sql
     * @param string $optCache
     * @return mixed
     */
	public function adodbQueryDataInNumberMode($sql, $optCache = ""){
		global $adoConn;
		//Thoi gian Cache
		$adoConn->SetFetchMode(ADODB_FETCH_NUM);
		if ($optCache == ""){
			//echo "1";
			$ArrAllData = $adoConn->GetArray($sql); 
		}else{
			global $ADODB_CACHE_TIMEOUT;
			$cacheTime = $ADODB_CACHE_TIMEOUT;
			$ArrAllData = $adoConn->CacheGetAll($cacheTime,$sql); 
		}
		return $ArrAllData;
	}

    /**
     * @param $sql
     * @param string $optCache
     * @return mixed
     */
	public function adodbQueryDataInNameMode($sql, $optCache = ""){
		global $adoConn;
		//Thoi gian Cache
		$adoConn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($optCache == ""){
			$ArrAllData = $adoConn->GetArray($sql); 
		}else{
			global $ADODB_CACHE_TIMEOUT;
			$cacheTime = $ADODB_CACHE_TIMEOUT;
			$ArrAllData = $adoConn->CacheGetAll($cacheTime,$sql); 
		}
		return $ArrAllData;
	}

    /**
     * @param $arrParameter
     * @return string
     */
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
     * @param $arrParameter
     * @param $spName
     * @param bool $optionQuery
     * @param bool $optionReturn
     * @return mixed|string
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