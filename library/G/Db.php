<?php
require_once 'G/Components/adodb/adodb.inc.php';

class G_Db extends Zend_Db {
    protected $adoConn;
    public function __construct(){
		if (!class_exists('Zend_Registry')) {
			Zend_Loader::loadClass('Zend_Registry');
		}
		if (!class_exists('Zend_Config_Ini')) {
			Zend_Loader::loadClass('Zend_Config_Ini');	
		}
		$registry = Zend_Registry::getInstance();
		if($registry->isRegistered('connAdo')) {
			$this->adoConn=Zend_Registry::get('connAdo');
		}else{
			$config_path = './config/config.ini';
			if (!file_exists($config_path)) {
				$config_path = '../../config/config.ini';
			}
			$config = new Zend_Config_Ini($config_path,'dbmssql');
			$registry->set('DataUserName', $config->db->config->userdbname);
			$this->adoConn = $this->connectADO($config->db->adapter,$config->db->config->toArray());			
			$registry->set('connAdo',$this->adoConn);
		}
    }
	private function connectADO($adapter, $config = array()){
		//Tao doi tuong ADODB
		$adoConn = NewADOConnection("ado_mssql");  // create a connection
		$connStr = "Provider=SQLOLEDB; Data Source=" . $config['host'] . ";Initial Catalog='" . $config['dbname'] . "'; User ID=" . $config['username'] . "; Password=" .$config['password'];
		//call connect adodb
		$adoConn->Connect($connStr) or die("Hien tai he thong khong the ket noi vao CSDL duoc!");
		return $adoConn;
	}
	public function pdoExecSP($spName,$arrParameter,$sSingle=false,$sSql=false){
		$sql = '';
		if(is_array($arrParameter)){
			foreach ($arrParameter as $key => $value) {
				if($sql !=''){
					$sql .= ",'" . $value . "'";
				}else{
					$sql .= " '" . $value . "'";
				}
			}
		}
		$sql = 'Exec [dbo].'.$spName.$sql; 
       //echo htmlspecialchars($sql); echo "</br>";
       //exit;
		if($sSql){
			return $sql;
		}else{
			if($sSingle){
				$rows = $this->adodbExecSqlString($sql);
			}else{
				$rows = $this->adodbQueryDataInNameMode($sql);              
			}
		}
		return $rows;
	}
	public function adodbExecSqlString($sql){
		$this->adoConn->SetFetchMode(ADODB_FETCH_ASSOC);
		$ArrSingleData = $this->adoConn->GetRow($sql); 
		return $ArrSingleData;
	}
	public function adodbQueryDataInNameMode($sql, $optCache = ""){
		$this->adoConn->SetFetchMode(ADODB_FETCH_ASSOC);
		$ArrAllData = $this->adoConn->GetArray($sql); 
		return $ArrAllData;
	}
	public function adodbQueryDataInNumberMode($sql, $optCache = ""){
		$this->adoConn->SetFetchMode(ADODB_FETCH_NUM);
		$ArrAllData = $this->adoConn->GetArray($sql);
		return $ArrAllData;
	}
}