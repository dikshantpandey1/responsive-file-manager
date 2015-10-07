<?php
include 'vendor/autoload.php';

//if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
//    die('forbiden');
//}

error_reporting(E_ALL);
ini_set("display_errors", 1);


$values = include 'src/wilvers/FileManager/Config/config.php';
$config = new Wilvers\FileManager\Config\ArrayConfig($values);
//var_dump($config->get('viewerjs_file_exts'));
$i18n = Wilvers\FileManager\Ressource\i18n\Translation::get('fr_FR');
//var_dump($i18n->get('Upload_file'));

$fm = new Wilvers\FileManager\FileManager();
$fm
        ->setConfig($config)
        ->setTranslation($i18n)
;

//
?>

<!--
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <div>

        </div>
        <iframe src="filemanager/filemanager/dialog.php" height="800" width="1024">
    </body>
</html>
-->
<!--[[head-replace]]--><!--[[body-bottom-replace]]-->
<?php
//$dbHead = $debugbarRenderer->renderHead();
//$dbHtml = $debugbarRenderer->render();
//$renderParams = array($dbHead, $dbHtml);
//$search = array('<!--[[head-replace]]-->', '<!--[[body-bottom-replace]]-->');
//
//$html = $fm->render($renderParams);
//echo str_replace($search, $renderParams, $fm->render($renderParams));
echo $fm->render();
