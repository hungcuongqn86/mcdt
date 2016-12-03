<?php

/**
 *
 */
class G_Objects_Cache
{

    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function getInstanceCache($options = array())
    {
        $frontendParam = array(
            'lifetime' => null,
            'automatic_serialization' => 'true'
        );

      //  echo G_Global::getInstance()->dirCache; die;
        $backendParam = array(
            'cache_dir' => G_Global::getInstance()->dirCache
        );
        if ($options) {
            if (isset($options['frontend'])) {
                $frontendParam = $options['frontend'];
            }
            if (isset($options['backend'])) {
                $backendParam = $options['backend'];
            }
        }
        $cache = Zend_Cache::factory('Core', 'File', $frontendParam, $backendParam);
        return $cache;
    }

    public function save_cache($data, $name, $options = array())
    {
        $cache = self::getInstanceCache($options);
        $cache->save($data, $name);
    }

    public function load_cache($name, $options = array())
    {
        $cache = self::getInstanceCache($options);
        $arrOutput = array();
        try {
            $arrOutput = $cache->load($name);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $arrOutput;
    }

    public function clean_cache($id = "", $options = array())
    {
        $cache = self::getInstanceCache($options);
        if ($id)
            $cache->remove($id);
        else
            $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }
}

?>