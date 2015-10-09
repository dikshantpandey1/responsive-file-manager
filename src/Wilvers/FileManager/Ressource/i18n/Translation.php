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

    public static function getLanguages() {
        return array(
            'az_AZ' => 'Azərbaycan dili',
            'bg_BG' => 'български език',
            'ca' => 'Català, valencià',
            'cs' => 'čeština, český jazyk',
            'da' => 'Dansk',
            'de' => 'Deutsch',
            'el_GR' => 'ελληνικά',
            'en_EN' => 'English',
            'es' => 'Español',
            'fa' => 'فارسی',
            'fr_FR' => 'Français',
            'he' => 'עברית',
            'hr' => 'Hrvatski jezik',
            'hu_HU' => 'Magyar',
            'id' => 'Bahasa Indonesia',
            'he_IL' => 'Hebrew (Israel)',
            'it' => 'Italiano',
            'ja' => '日本',
            'lt' => 'Lietuvių kalba',
            'mn_MN' => 'монгол',
            'nb_NO' => 'Norsk bokmål',
            'nl' => 'Nederlands, Vlaams',
            'pl' => 'Język polski, polszczyzna',
            'pt_BR' => 'Português(Brazil)',
            'pt_PT' => 'Português',
            'ru' => 'Pусский язык',
            'sk' => 'Slovenčina',
            'sl' => 'Slovenski jezik',
            'sv_SE' => 'Svenska',
            'tr_TR' => 'Türkçe',
            'uk_UA' => 'Yкраїнська мова',
            'vi' => 'Tiếng Việt',
            'zh_CN' => '中文 (Zhōngwén), 汉语, 漢語',
                // source: http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
        );
    }

}
