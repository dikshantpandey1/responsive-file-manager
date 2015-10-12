<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'vendor/autoload.php';

//if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
//    $r = new \Wilvers\FileManager\Tools\Response('forbiden', 403);
//    $r->send();
//    exit;
//}

$values = include 'src/wilvers/FileManager/Config/config.php';
$config = new Wilvers\FileManager\Config\ArrayConfig($values);
$i18n = Wilvers\FileManager\Ressource\i18n\Translation::get('fr_FR');

$fm = new Wilvers\FileManager\FileManager();
$fm
        ->setConfig($config)
        ->setTranslation($i18n)
;

$fm->execute();
