<?php

namespace Wilvers\FileManager\Config;

/**
 *
 * @author pwilvers
 */
interface ConfigurationInterface {

    /**
     * return the config
     */
    function getConfig();

    /**
     * set config
     * @param type $config
     */
    function setConfig($config);

    /**
     * get value from config
     * @param type $key
     */
    function get($key);

    /**
     * set value to config
     * @param type $key
     * @param type $value
     */
    function set($key, $value);

    /**
     * check if a key exist or is empty
     * @param string $key
     * @return bool
     */
    function isEmpty($key);
}
