<?php

namespace Wilvers\FileManager\Config;

/**
 * Description of ConfigInterface
 *
 * @author pwilvers
 */
class ArrayConfig implements ConfigurationInterface {

    /**
     * array multi dimmension whith config values
     */
    protected $_config = array();

    /**
     * delimiter for path to array key
     * path.to.my.value ==> $_config[path][to][my][value]
     */
    protected $_delimiter = '.';

    public function __construct($config = array()) {
        $this->setConfig($config);
        //$this->init($config, $this->_config);
    }

    /**
     * getter
     * @return type
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * setter
     * @param type $config
     * @return \FileManager\Config\ArrayConfig
     */
    public function setConfig($config) {
        $this->_config = $config;
        return $this;
    }

    /**
     * getter
     * @return type
     */
    public function getDelimiter() {
        return $this->_delimiter;
    }

    /**
     * setter
     * @param type $delimiter
     * @return \FileManager\Config\ArrayConfig
     */
    public function setDelimiter($delimiter) {
        $this->_delimiter = $delimiter;
        return $this;
    }

    /**
     * get value from config
     * @param type $key
     * @return boolean
     */
    public function get($key) {
        $path = explode($this->_delimiter, $key);
        $config = $this->_config;
        foreach ($path as $k => $v) {
            if (!isset($config[$v])) {
                file_put_contents(__DIR__ . '/../../../../tmp/log.txt', date('H:i:s') . ' ' . $v . ' config not Found' . PHP_EOL, FILE_APPEND);
                return false;
            } else {
                $config = $config[$v];
            }
        }
        return $config;
        //return $key;
    }

    /**
     * set value to config
     * @param type $key
     * @param type $value
     */
    public function set($key, $value) {
        $path = explode($this->_delimiter, $key);
        $config = &$this->_config;
        foreach ($path as $k => $v) {
            if (!isset($config[$v]))
                $config[$v] = "";
            $config = &$config[$v];
        }
        $config[$v] = $this->clear($value);
    }

    /**
     * initialisation from array
     * each values setted are returned by beforeSetValue function
     * @param type $param
     */
    protected function init($param, &$config) {
        //$config = &$this->_config;
        foreach ($param as $key => $value) {
            if (is_array($value)) {
                $config[$key] = ""; //!! a modifier
                $this->init($value, $config[$key]);
            } else {
                $config[$key] = $this->beforeSetValue($value);
            }
        }
        return $this;
    }

    /**
     *
     * @param type $value
     * @return type
     */
    protected function beforeSetValue($value) {
        return trim($value);
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function isEmpty($key) {
        $v = $this->get($key);
        return empty($v);
    }

}
