<?php

/**
 * @see adodb.inc.php
 * Call Adodb library
 */
require_once 'G/Components/adodb/adodb.inc.php';

class G_Connection
{

    public static function factory($adapter, $config = array())
    {
        global $ADODB_CACHE_DIR, $ADODB_CACHE_TIMEOUT;
        if ($adapter == "MSSQL") {
            $adoConn = NewADOConnection("ado_mssql");  // create a connection
            $connStr = "Provider=SQLOLEDB; Data Source=" . $config['host'] . ";Initial Catalog='" . $config['dbname'] . "'; User ID=" . $config['username'] . "; Password=" . $config['password'];
            //call connect adodb
            $adoConn->Connect($connStr) or die("Hien tai he thong khong the ket noi vao CSDL duoc!");
        }
        $ADODB_CACHE_DIR = $config['pathAdoCache'];
        $ADODB_CACHE_TIMEOUT = $config['cachetimeout'];
        return $adoConn;
    }

}
