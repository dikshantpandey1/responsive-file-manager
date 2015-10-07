<?php

namespace Wilvers\FileManager\Ressource\i18n;

use Wilvers\FileManager\Config\ArrayConfig;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Translation
 *
 * @author pwilvers
 */
class Translation {

    public static function get($lang) {
        $config = include $lang . '.php';
        return new ArrayConfig($config);
    }

}
