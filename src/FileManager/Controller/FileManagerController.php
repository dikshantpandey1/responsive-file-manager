<?php

namespace FileManager\Controller;

use FileManager\Config\ConfigurationInterface;
use FileManager\Tools\View;

/**
 * Description of FileManager
 *
 * @author pwilvers
 */
class FileManagerController {

    protected $view;
    protected $_request;
    protected $_config;

    /**
     *
     */
    public function __construct() {
        $this->view = $this->getView();
        $this->_request = new \FileManager\Tools\Request();
    }

    /**
     *
     * @return View
     */
    protected function getView() {
        return new View();
    }

    /**
     *
     * @param ConfigurationInterface $config
     * @param ConfigurationInterface $translation
     * @return type
     */
    public function render(ConfigurationInterface $config, ConfigurationInterface $translation) {
        $this->_config = $config;
        $this->setMainView();
        $this->view
                ->assign('config', $config)
                ->assign('translator', $translation)
                ->assign('MainForm', $this->renderViewFile('MainForm'))
                ->assign('Uploader', $this->renderViewFile('Uploader'))
                ->assign('Header', $this->renderViewFile('Header'))
                ->assign('Breadcrunb', $this->renderViewFile('Breadcrumb'))
                ->assign('Files', $this->renderViewFile('Files'))
                ->assign('Lightbox', '')
                ->assign('Loading', '')
                ->assign('Player', '')
        ;
        return $this->view->render('src/FileManager/Template/DialogTemplate.php');
    }

    /**
     *
     */
    protected function setMainView() {

        $field_id = $this->_request->get('field_id', '', array('strTag', 'replace'));
        $type = $this->_request->get('type', 0);
        $type_param = $this->_request->get('type', 0, array('strTag', 'replace'));
        $editor = $this->_request->get('editor', $type == 0 ? false : 'tinymce', array('strTag', 'replace'));
        $return_relative_url = $this->_request->get('relative_url', false, array('bool'));

        //apply
        if ($type_param == 1)
            $apply = 'apply_img';
        elseif ($type_param == 2)
            $apply = 'apply_link';
        elseif ($type_param == 0 && $field_id == '')
            $apply = 'apply_none';
        elseif ($type_param == 3)
            $apply = 'apply_video';
        else
            $apply = 'apply';

        $popup = !!$this->_request->get('popup', 0, array('strTag'));
        $crossdomain = !!$this->_request->get('crossdomain', 0, array('strTag'));

        //view type
        if (!isset($_SESSION['RF']["view_type"])) {
            $view = $default_view;
            $_SESSION['RF']["view_type"] = $view;
        }
        if (isset($_GET['view'])) {
            $view = \FileManager\Tools\Utils::fix_get_params($_GET['view']);
            $_SESSION['RF']["view_type"] = $view;
        }
        $view = $_SESSION['RF']["view_type"];

        //subdir
        if (isset($_GET['fldr']) && !empty($_GET['fldr']) && strpos($_GET['fldr'], '../') === FALSE && strpos($_GET['fldr'], './') === FALSE) {
            $subdir = urldecode(trim(strip_tags($_GET['fldr']), "/") . "/");
            $_SESSION['RF']["filter"] = '';
        } else {
            $subdir = '';
        }

        if ($subdir == "") {
            if (!empty($_COOKIE['last_position']) && strpos($_COOKIE['last_position'], '.') === FALSE)
                $subdir = trim($_COOKIE['last_position']);
        }
        //remember last position
        setcookie('last_position', $subdir, time() + (86400 * 7));

        if ($subdir == "/") {
            $subdir = "";
        }

        // If hidden folders are specified
        if (count($this->_config->get('hidden_folders'))) {
            // If hidden folder appears in the path specified in URL parameter "fldr"
            $dirs = explode('/', $subdir);
            foreach ($dirs as $dir) {
                if ($dir !== '' && in_array($dir, $this->_config->get('hidden_folders'))) {
                    // Ignore the path
                    $subdir = "";
                    break;
                }
            }
        }
        /*         * *
         * SUB-DIR CODE
         * * */

        if (!isset($_SESSION['RF']["subfolder"])) {
            $_SESSION['RF']["subfolder"] = '';
        }
        $rfm_subfolder = '';

        if (!empty($_SESSION['RF']["subfolder"]) && strpos($_SESSION['RF']["subfolder"], '../') === FALSE && strpos($_SESSION['RF']["subfolder"], './') === FALSE && strpos($_SESSION['RF']["subfolder"], "/") !== 0 && strpos($_SESSION['RF']["subfolder"], '.') === FALSE) {
            $rfm_subfolder = $_SESSION['RF']['subfolder'];
        }

        if ($rfm_subfolder != "" && $rfm_subfolder[strlen($rfm_subfolder) - 1] != "/") {
            $rfm_subfolder .= "/";
        }
        $current_path = $this->_config->get('current_path');
        $upload_dir = $this->_config->get('upload_dir');
        $thumbs_base_path = $this->_config->get('thumbs_base_path');
        if (!file_exists($current_path . $rfm_subfolder . $subdir)) {
            $subdir = '';
            if (!file_exists($current_path . $rfm_subfolder . $subdir)) {
                $rfm_subfolder = "";
            }
        }

        if (trim($rfm_subfolder) == "") {
            $cur_dir = $upload_dir . $subdir;
            $cur_path = $current_path . $subdir;
            $thumbs_path = $thumbs_base_path;
            $parent = $subdir;
        } else {
            $cur_dir = $upload_dir . $rfm_subfolder . $subdir;
            $cur_path = $current_path . $rfm_subfolder . $subdir;
            $thumbs_path = $thumbs_base_path . $rfm_subfolder;
            $parent = $rfm_subfolder . $subdir;
        }


//filter
        $filter = "";
        if (isset($_SESSION['RF']["filter"])) {
            $filter = $_SESSION['RF']["filter"];
        }

        if (isset($_GET["filter"])) {
            $filter = \FileManager\Tools\Utils::fix_get_params($_GET["filter"]);
        }

        if (!isset($_SESSION['RF']['sort_by'])) {
            $_SESSION['RF']['sort_by'] = 'name';
        }

        if (isset($_GET["sort_by"])) {
            $sort_by = $_SESSION['RF']['sort_by'] = \FileManager\Tools\Utils::fix_get_params($_GET["sort_by"]);
        } else
            $sort_by = $_SESSION['RF']['sort_by'];


        if (!isset($_SESSION['RF']['descending'])) {
            $_SESSION['RF']['descending'] = TRUE;
        }

        if (isset($_GET["descending"])) {
            $descending = $_SESSION['RF']['descending'] = \FileManager\Tools\Utils::fix_get_params($_GET["descending"]) == 1;
        } else {
            $descending = $_SESSION['RF']['descending'];
        }
//getparam
        $get_params = array(
            'editor' => $editor,
            'type' => $type_param,
            'lang' => $this->_config->get('default_language'),
            'popup' => $popup,
            'crossdomain' => $crossdomain,
            'field_id' => $field_id,
            'relative_url' => $return_relative_url,
            'akey' => (isset($_GET['akey']) && $_GET['akey'] != '' ? $_GET['akey'] : 'key')
        );
        if (isset($_GET['CKEditorFuncNum'])) {
            $get_params['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
            $get_params['CKEditor'] = (isset($_GET['CKEditor']) ? $_GET['CKEditor'] : '');
        }
        $get_params['fldr'] = '';
        $get_params = http_build_query($get_params);
        $files = $this->getFiles($current_path, $rfm_subfolder, $subdir, $sort_by, $descending);

        $n_files = count($files);
        $files_prevent_duplicate = array();
        $lazy_loading_enabled = ($this->_config->get('lazy_loading_file_number_threshold') == 0 ||
                $this->_config->get('lazy_loading_file_number_threshold') != -1 &&
                $n_files > $this->_config->get('lazy_loading_file_number_threshold')) ? true : false;

        $this->view
                ->assign('requet', $this->_request)
                ->assign('field_id', $field_id)
                ->assign('lang', $this->_config->get('default_language'))
                ->assign('type', $type)
                ->assign('type_param', $type_param)
                ->assign('editor', $editor)
                ->assign('return_relative_url', $return_relative_url)
                ->assign('apply', $apply)
                ->assign('popup', $popup)
                ->assign('crossdomain', $crossdomain)
                ->assign('files_prevent_duplicate', $files_prevent_duplicate)
                ->assign('view', $view)
                ->assign('subdir', $subdir)
                ->assign('cur_dir', $cur_dir)
                ->assign('thumbs_path', $thumbs_path)
                ->assign('rfm_subfolder', $rfm_subfolder)
                ->assign('sort_by', $sort_by)
                ->assign('descending', $descending)
                ->assign('filter', $filter)
                ->assign('base_url', \FileManager\Tools\Utils::base_url())
                ->assign('cur_path', $cur_path)
                ->assign('files', $files)
                ->assign('n_files', $n_files)
                ->assign('get_params', $get_params)
                ->assign('current_files_number', '')
                ->assign('current_folders_number', '')
                ->assign('lazy_loading_enabled', $lazy_loading_enabled)
        ;
    }

    /**
     *
     */
    protected function renderViewFile($file) {
        return $this->view->render('src/FileManager/Template/' . $file . 'Template.php');
    }

    protected function getFiles($current_path, $rfm_subfolder, $subdir, $sort_by, $descending) {

        $files = scandir($current_path . $rfm_subfolder . $subdir);
        $n_files = count($files);

//php sorting
        $sorted = array();
        $current_folder = array();
        $prev_folder = array();
        $current_files_number = 0;
        $current_folders_number = 0;
        foreach ($files as $k => $file) {
            if ($file == ".")
                $current_folder = array('file' => $file);
            elseif ($file == "..")
                $prev_folder = array('file' => $file);
            elseif (is_dir($current_path . $rfm_subfolder . $subdir . $file)) {
                $date = filemtime($current_path . $rfm_subfolder . $subdir . $file);
                if ($this->_config->get('show_folder_size')) {
                    list($size, $nfiles, $nfolders) = folder_info($current_path . $rfm_subfolder . $subdir . $file);
                    $current_folders_number++;
                } else {
                    $size = 0;
                }
                $file_ext = trans('Type_dir');
                $sorted[$k] = array(
                    'file' => $file,
                    'file_lcase' => strtolower($file),
                    'date' => $date,
                    'size' => $size,
                    'nfiles' => $nfiles,
                    'nfolders' => $nfolders,
                    'extension' => $file_ext,
                    'extension_lcase' => strtolower($file_ext));
            } else {
                $current_files_number++;
                $file_path = $current_path . $rfm_subfolder . $subdir . $file;
                $date = filemtime($file_path);
                $size = filesize($file_path);
                $file_ext = substr(strrchr($file, '.'), 1);
                $sorted[$k] = array('file' => $file, 'file_lcase' => strtolower($file), 'date' => $date, 'size' => $size, 'extension' => $file_ext, 'extension_lcase' => strtolower($file_ext));
            }
        }
        switch ($sort_by) {
            case 'date':
                usort($sorted, '\FileManager\Controller\FileManagerController::dateSort');
                break;
            case 'size':
                usort($sorted, 'sizeSort');
                break;
            case 'extension':
                usort($sorted, 'extensionSort');
                break;
            default:
                usort($sorted, '\FileManager\Controller\FileManagerController::filenameSort');
                break;
        }

        if (!$descending) {
            $sorted = array_reverse($sorted);
        }

        $files = array_merge(array($prev_folder), array($current_folder), $sorted);
        return $files;
    }

    protected static function filenameSort($x, $y) {
        return $x['file_lcase'] < $y['file_lcase'];
    }

    protected static function dateSort($x, $y) {
        return $x['date'] < $y['date'];
    }

    protected static function sizeSort($x, $y) {
        return $x['size'] < $y['size'];
    }

    protected static function extensionSort($x, $y) {
        return $x['extension_lcase'] < $y['extension_lcase'];
    }

}
