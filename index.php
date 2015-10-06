<?php
include 'vendor/autoload.php';

//if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
//    die('forbiden');
//}

error_reporting(E_ALL);
ini_set("display_errors", 1);


$values = include 'src/FileManager/Config/config.php';
$config = new FileManager\Config\ArrayConfig($values);
//var_dump($config->get('viewerjs_file_exts'));
$i18n = FileManager\Ressource\i18n\Translation::get('fr_FR');
//var_dump($i18n->get('Upload_file'));

$fm = new FileManager\FileManager();
$fm
        ->setConfig($config)
        ->setTranslation($i18n)
;
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
<?php
echo $fm->render();
?>