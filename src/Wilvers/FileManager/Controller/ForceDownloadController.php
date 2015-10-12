<?php

namespace Wilvers\FileManager\Controller;

use Wilvers\FileManager\Config\ConfigurationInterface;
use Wilvers\FileManager\Tools\View;
use Wilvers\FileManager\Tools\Request;
use Wilvers\FileManager\Tools\Utils;
use Wilvers\FileManager\Controller\FileManagerController;
use Wilvers\FileManager\Tools\ImageLibmageLib;
use Wilvers\FileManager\Ressource\i18n\Translation;

/**
 * Description of FileManager
 *
 * @author pwilvers
 */
class ForceDownloadController extends FileManagerController {

    /**
     *
     * @param ConfigurationInterface $config
     * @param ConfigurationInterface $translation
     * @return type
     */
    public function execute(ConfigurationInterface $config, ConfigurationInterface $translation) {

        if (
                strpos($_POST['path'], '/') === 0 || strpos($_POST['path'], '../') !== false || strpos($_POST['path'], './') === 0
        ) {
            response('wrong path', 400)->send();
            exit;
        }


        if (strpos($_POST['name'], '/') !== false) {
            response('wrong path', 400)->send();
            exit;
        }

        $path = $current_path . $_POST['path'];
        $name = $_POST['name'];

        $info = pathinfo($name);

        if (!in_array(fix_strtolower($info['extension']), $ext)) {
            response('wrong extension', 400)->send();
            exit;
        }

        if (!file_exists($path . $name)) {
            response('File not found', 404)->send();
            exit;
        }

        $img_size = (string) (filesize($path . $name)); // Get the image size as string

        $mime_type = get_file_mime_type($path . $name); // Get the correct MIME type depending on the file.

        response(file_get_contents($path . $name), 200, array(
            'Pragma' => 'private',
            'Cache-control' => 'private, must-revalidate',
            'Content-Type' => $mime_type,
            'Content-Length' => $img_size,
            'Content-Disposition' => 'attachment; filename="' . ($name) . '"'
        ))->send();

        exit;
    }

}
