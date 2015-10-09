<?php

namespace Wilvers\FileManager\Controller;

use Wilvers\FileManager\Config\ConfigurationInterface;
use Wilvers\FileManager\Tools\View;
use Wilvers\FileManager\Tools\Request;
use Wilvers\FileManager\Tools\Utils;
use Wilvers\FileManager\Controller\FileManagerController;

/**
 * Description of FileManager
 *
 * @author pwilvers
 */
class ExecuteController extends FileManagerController {

    /**
     *
     * @param ConfigurationInterface $config
     * @param ConfigurationInterface $translation
     * @return type
     */
    public function execute(ConfigurationInterface $config, ConfigurationInterface $translation) {

        $this->_config = $config;
        $this->_translator = $translation;

        $base = $this->_config->get('current_path');
        $current_path = $this->_config->get('current_path');
        $path = $current_path . $this->_request->get('path');
        $cycle = TRUE;
        $max_cycles = 50;
        $i = 0;
        while ($cycle && $i < $max_cycles) {
            $i++;
            if ($path == $base)
                $cycle = FALSE;

            //@Todo config by rep
            if (file_exists($path . "config.php")) {
                require_once $path . "config.php";
                $cycle = FALSE;
            }
            $path = Utils::fix_dirname($path) . "/";
            $cycle = FALSE;
        }

        $path = $current_path . $this->_request->get('path');
        $path_thumb = $this->_config->get('thumbs_base_path') . $this->_request->get('path');

        if ($this->_request->isKeySet('name')) {
            $name = Utils::fix_filename($this->_request->get('name'), $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with'));
            if (strpos($name, '../') !== FALSE) {
                $this->sendResponse('wrong name', 400);
                exit;
            }
        }

        $info = pathinfo($path);
        if (isset($info['extension']) && !($this->_request->isKeySet('action') && $this->_request->get('action') == 'delete_folder') && !in_array(strtolower($info['extension']), $this->_config->get('ext')) && $this->_request->get('action') != 'create_file') {
            $this->sendResponse('wrong extension', 400);
            exit;
        }

        if ($this->_request->isKeySet('action')) {
            switch ($this->_request->get('action')) {
                case 'delete_file':
                    $this->deleteFile($path, $current_path, $path_thumb);
                    break;
                case 'delete_folder':
                    $this->deleteFolder($path, $current_path, $path_thumb);
                    break;
                case 'create_folder':
                    $this->createFolder($path, $path_thumb, $name);
                    break;
                case 'rename_folder':
                    $this->renameFolder($path, $current_path, $path_thumb, $name);
                    break;
                case 'create_file':
                    $this->createFile($path, $name);
                    break;
                case 'rename_file':
                    $this->renameFile($path, $current_path, $path_thumb, $name);
                    break;
                case 'duplicate_file':
                    $this->duplicateFile($path, $current_path, $path_thumb);
                    break;
                case 'paste_clipboard':
                    $this->pasteClipbpard($path, $current_path, $path_thumb);
                    break;
                case 'chmod':
                    $this->chmod($path);
                    break;
                case 'save_text_file':
                    $this->saveTextFile($path);
                    break;
                default:
                    $this->sendResponse('wrong action', 400);
                    exit;
            }
        }
    }

    protected function deleteFile($path, $current_path, $path_thumb) {
        if ($this->_config->get('delete_files')) {
            unlink($path);
            if (file_exists($path_thumb))
                unlink($path_thumb);

            $info = pathinfo($path);
            if ($this->_config->get('relative_image_creation')) {
                foreach ($this->_config->get('relative_path_from_current_pos') as $k => $path) {
                    if ($path != "" && $path[strlen($path) - 1] != "/")
                        $path.="/";

                    if (file_exists($info['dirname'] . "/" . $path . $this->_config->get('relative_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('relative_image_creation_name_to_append')[$k] . "." . $info['extension'])) {
                        unlink($info['dirname'] . "/" . $path . $this->_config->get('relative_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('relative_image_creation_name_to_append')[$k] . "." . $info['extension']);
                    }
                }
            }

            if ($this->_config->get('fixed_image_creation')) {
                foreach ($this->_config->get('fixed_path_from_filemanager') as $k => $path) {
                    if ($path != "" && $path[strlen($path) - 1] != "/")
                        $path.="/";

                    $base_dir = $path . substr_replace($info['dirname'] . "/", '', 0, strlen($current_path));
                    if (file_exists($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension'])) {
                        unlink($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension']);
                    }
                }
            }
        }
    }

    protected function deleteFolder($path, $current_path, $path_thumb) {
        if ($this->_config->get('delete_folders')) {
            if (is_dir($path_thumb)) {
                Utils::deleteDir($path_thumb);
            }

            if (is_dir($path)) {
                Utils::deleteDir($path);
                if ($this->_config->get('fixed_image_creation')) {
                    foreach ($this->_config->get('fixed_path_from_filemanager') as $k => $paths) {
                        if ($paths != "" && $paths[strlen($paths) - 1] != "/")
                            $paths.="/";

                        $base_dir = $paths . substr_replace($path, '', 0, strlen($current_path));
                        if (is_dir($base_dir))
                            Utils::deleteDir($base_dir);
                    }
                }
            }
        }
    }

    protected function createFolder($path, $path_thumb, $name) {
        if ($this->_config->get('create_folders')) {
            Utils::create_folder(Utils::fix_path($path . $name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with')), Utils::fix_path($path_thumb . $name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with')));
        }
    }

    protected function renameFolder($path, $current_path, $path_thumb, $name) {
        if ($this->_config->get('rename_folders')) {
            $name = Utils::fix_filename($name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with'));
            $name = str_replace('.', '', $name);

            if (!empty($name)) {
                if (!Utils::rename_folder($path, $name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'))) {
                    $this->sendResponse($this->_translator->get('Rename_existing_folder'), 403);
                    exit;
                }

                Utils::rename_folder($path_thumb, $name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'));
                if ($this->_config->get('fixed_image_creation')) {
                    foreach ($this->_config->get('fixed_path_from_filemanager') as $k => $paths) {
                        if ($paths != "" && $paths[strlen($paths) - 1] != "/")
                            $paths.="/";

                        $base_dir = $paths . substr_replace($path, '', 0, strlen($current_path));
                        Utils::rename_folder($base_dir, $name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'));
                    }
                }
            }
            else {
                $this->sendResponse($this->_translator->get('Empty_name'), 400);
                exit;
            }
        }
    }

    protected function createFile($path, $name) {
        if ($this->_config->get('create_text_files') === FALSE) {
            $this->sendResponse(sprintf($this->_translator->get('File_Open_Edit_Not_Allowed'), strtolower($this->_translator->get('Edit'))), 403);
            exit;
        }

        $editable_text_file_exts = $this->_config->get('editable_text_file_exts');
        if ($editable_text_file_exts == false || !is_array($editable_text_file_exts)) {
            $editable_text_file_exts = array();
        }


        // check if user supplied extension
        if (strpos($name, '.') === FALSE) {
            $this->sendResponse($this->_translator->get('No_Extension') . ' ' . sprintf($this->_translator->get('Valid_Extensions'), implode(', ', $editable_text_file_exts)), 400);
            exit;
        }

        // correct name
        $old_name = $name;
        $name = Utils::fix_filename($name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with'));
        if (empty($name)) {
            $this->sendResponse($this->_translator->get('Empty_name'), 400);
            exit;
        }

        // check extension
        $parts = explode('.', $name);
        if (!in_array(end($parts), $editable_text_file_exts)) {
            $this->sendResponse($this->_translator->get('Error_extension') . ' ' . sprintf($this->_translator->get('Valid_Extensions'), implode(', ', $editable_text_file_exts)), 400);
            exit;
        }

        // file already exists
        if (file_exists($path . $name)) {
            $this->sendResponse($this->_translator->get('Rename_existing_file'), 403);
            exit;
        }

        $content = $this->_request->get('new_content');
        if (@file_put_contents($path . $name, $content) === FALSE) {
            $this->sendResponse($this->_translator->get('File_Save_Error'), 500);
            exit;
        } else {
            if (is_function_callable('chmod') !== FALSE) {
                chmod($path . $name, 0644);
            }
            $this->sendResponse($this->_translator->get('File_Save_OK'));
            exit;
        }
    }

    protected function renameFile($path, $current_path, $path_thumb, $name) {
        if ($this->_config->get('rename_files')) {
            $name = Utils::fix_filename($name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with'));
            if (!empty($name)) {
                if (!Utils::rename_file($path, $name, $this->_config->get('transliteration'))) {
                    $this->sendResponse($this->_translator->get('Rename_existing_file'), 403);
                    exit;
                }

                Utils::rename_file($path_thumb, $name, $this->_config->get('transliteration'));

                if ($this->_config->get('fixed_image_creation')) {
                    $info = pathinfo($path);

                    foreach ($this->_config->get('fixed_path_from_filemanager') as $k => $paths) {
                        if ($paths != "" && $paths[strlen($paths) - 1] != "/")
                            $paths.="/";

                        $base_dir = $paths . substr_replace($info['dirname'] . "/", '', 0, strlen($current_path));
                        if (file_exists($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension'])) {
                            Utils::rename_file($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension'], $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $name . $this->_config->get('fixed_image_creation_to_append')[$k], $this->_config->get('transliteration'));
                        }
                    }
                }
            } else {
                $this->sendResponse($this->_translator->get('Empty_name'), 400);
                exit;
            }
        }
    }

    protected function duplicateFile($path, $current_path, $path_thumb) {
        if ($this->_config->get('duplicate_files')) {
            $name = Utils::fix_filename($name, $this->_config->get('transliteration'), $this->_config->get('convert_spaces'), $this->_config->get('replace_with'));
            if (!empty($name)) {
                if (!Utils::duplicate_file($path, $name)) {
                    $this->sendResponse($this->_translator->get('Rename_existing_file'), 403);
                    exit;
                }

                Utils::duplicate_file($path_thumb, $name);

                if ($this->_config->get('fixed_image_creation')) {
                    $info = pathinfo($path);
                    foreach ($this->_config->get('fixed_path_from_filemanager') as $k => $paths) {
                        if ($paths != "" && $paths[strlen($paths) - 1] != "/")
                            $paths.= "/";

                        $base_dir = $paths . substr_replace($info['dirname'] . "/", '', 0, strlen($current_path));

                        if (file_exists($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension'])) {
                            Utils::duplicate_file($base_dir . $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $info['filename'] . $this->_config->get('fixed_image_creation_to_append')[$k] . "." . $info['extension'], $this->_config->get('fixed_image_creation_name_to_prepend')[$k] . $name . $this->_config->get('fixed_image_creation_to_append')[$k]);
                        }
                    }
                }
            } else {
                $this->sendResponse($this->_translator->get('Empty_name'), 400);
                exit;
            }
        }
    }

    protected function pasteClipbpard($path, $current_path, $path_thumb) {
        $thumbs_base_path = $this->_config->get('thumbs_base_path');

        if (!isset($_SESSION['RF']['clipboard_action'], $_SESSION['RF']['clipboard']['path']) || $_SESSION['RF']['clipboard_action'] == '' || $_SESSION['RF']['clipboard']['path'] == '') {
            $this->sendResponse();
            exit;
        }

        $action = $_SESSION['RF']['clipboard_action'];
        $data = $_SESSION['RF']['clipboard'];
        $data['path'] = $current_path . $data['path'];
        $data['path_thumb'] = $thumbs_base_path . $data['path'];
        $pinfo = pathinfo($data['path']);

        // user wants to paste to the same dir. nothing to do here...
        if ($pinfo['dirname'] == rtrim($path, '/')) {
            $this->sendResponse();
            exit;
        }

        // user wants to paste folder to it's own sub folder.. baaaah.
        if (is_dir($data['path']) && strpos($path, $data['path']) !== FALSE) {
            $this->sendResponse();
            exit;
        }

        // something terribly gone wrong
        if ($action != 'copy' && $action != 'cut') {
            $this->sendResponse('no action', 400);
            exit;
        }

        // check for writability
        if (Utils::is_really_writable($path) === FALSE || Utils::is_really_writable($path_thumb) === FALSE) {
            $this->sendResponse($this->_translator->get('Dir_No_Write') . '<br/>' . str_replace('../', '', $path) . '<br/>' . str_replace('../', '', $path_thumb), 403);
            exit;
        }

        // check if server disables copy or rename
        if (Utils::is_function_callable(($action == 'copy' ? 'copy' : 'rename')) === FALSE) {
            $this->sendResponse(sprintf($this->_translator->get('Function_Disabled'), ($action == 'copy' ? lcfirst($this->_translator->get('Copy')) : lcfirst($this->_translator->get('Cut')))), 403);
            exit;
        }

        if ($action == 'copy') {
            Utils::rcopy($data['path'], $path);
            Utils::rcopy($data['path_thumb'], $path_thumb);
        } elseif ($action == 'cut') {
            Utils::rrename($data['path'], $path);
            Utils::rrename($data['path_thumb'], $path_thumb);

            // cleanup
            if (is_dir($data['path']) === TRUE) {
                Utils::rrename_after_cleaner($data['path']);
                Utils::rrename_after_cleaner($data['path_thumb']);
            }
        }

        // cleanup
        $_SESSION['RF']['clipboard']['path'] = NULL;
        $_SESSION['RF']['clipboard_action'] = NULL;
    }

    protected function chmod($path) {
        $mode = $this->_request->get('new_mode');
        $rec_option = $this->_request->get('is_recursive');
        $valid_options = array('none', 'files', 'folders', 'both');
        $chmod_perm = (is_dir($path) ? $this->_config->get('chmod_dirs') : $this->_config->get('chmod_files'));

        // check perm
        if ($chmod_perm === FALSE) {
            $this->sendResponse(sprintf($this->_translator->get('File_Permission_Not_Allowed'), (is_dir($path) ? lcfirst($this->_translator->get('Folders')) : lcfirst($this->_translator->get('Files')))), 403);
            exit;
        }

        // check mode
        if (!preg_match("/^[0-7]{3}$/", $mode)) {
            $this->sendResponse($this->_translator->get('File_Permission_Wrong_Mode'), 400);
            exit;
        }

        // check recursive option
        if (!in_array($rec_option, $valid_options)) {
            $this->sendResponse("wrong option", 400);
            exit;
        }

        // check if server disabled chmod
        if (Utils::is_function_callable('chmod') === FALSE) {
            $this->sendResponse(sprintf($this->_translator->get('Function_Disabled'), 'chmod'), 403);
            exit;
        }

        $mode = "0" . $mode;
        $mode = octdec($mode);

        Utils::rchmod($path, $mode, $rec_option);
    }

    protected function saveTextFile($path) {
        $content = $this->_request->get('new_content');
        // $content = htmlspecialchars($content); not needed
        // $content = stripslashes($content);
        // no file
        if (!file_exists($path)) {
            $this->sendResponse($this->_translator->get('File_Not_Found'), 404);
            exit;
        }

        // not writable or edit not allowed
        if (!is_writable($path) || $this->_config->get('edit_text_files') === FALSE) {
            $this->sendResponse(sprintf($this->_translator->get('File_Open_Edit_Not_Allowed'), strtolower($this->_translator->get('Edit'))), 403);
            exit;
        }

        if (@file_put_contents($path, $content) === FALSE) {
            $this->sendResponse($this->_translator->get('File_Save_Error'), 500);
            exit;
        } else {
            $this->sendResponse($this->_translator->get('File_Save_OK'));
            exit;
        }
    }

}
