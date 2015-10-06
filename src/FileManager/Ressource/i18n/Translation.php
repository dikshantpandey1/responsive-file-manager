<?php

namespace FileManager\Ressource\i18n;

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
        return new \FileManager\Config\ArrayConfig($config);
    }

}
