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
class AjaxController extends FileManagerController {

    /**
     *
     * @param ConfigurationInterface $config
     * @param ConfigurationInterface $translation
     * @return type
     */
    public function execute(ConfigurationInterface $config, ConfigurationInterface $translation) {
        $this->_config = $config;
        $this->_translator = $translation;

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'view':
                    $this->view();
                    break;
                case 'filter':
                    $this->filter();
                    break;
                case 'sort':
                    $this->sort();
                    break;
                case 'image_size': // not used
                    $this->imageSize();
                    break;
                case 'save_img':
                    $this->saveImage();
                    break;
                case 'extract':
                    $this->extract();
                    break;
                case 'media_preview':
                    $this->mediaPreview();
                    break;
                case 'copy_cut':
                    $this->copyCut();
                    break;
                case 'clear_clipboard':
                    $this->clearClipboard();
                    break;
                case 'chmod':
                    $this->chmod();
                    break;
                case 'get_lang':
                    $this->getLang();
                    break;
                case 'change_lang':
                    $this->changeLang();
                    break;
                case 'get_file': // preview or edit
                    $this->getFile();

                    break;
                default: $this->sendResponse('no action passed', 400);
                    exit;
            }
        } else {
            $this->sendResponse('no action passed', 400);
            exit;
        }
    }

    protected function view() {
        if (isset($_GET['type'])) {
            $_SESSION['RF']["view_type"] = $_GET['type'];
        } else {
            $this->sendResponse('view type number missing', 400);
            exit;
        }
    }

    protected function filter() {
        if (isset($_GET['type'])) {
            if ($this->_config->get('remember_text_filter') !== false) {
                $_SESSION['RF']["filter"] = $_GET['type'];
            }
        } else {
            $this->sendResponse('view type number missing', 400);
            exit;
        }
    }

    protected function sort() {
        if (isset($_GET['sort_by'])) {
            $_SESSION['RF']["sort_by"] = $_GET['sort_by'];
        }

        if (isset($_GET['descending'])) {
            $_SESSION['RF']["descending"] = $_GET['descending'] === "TRUE";
        }
    }

    protected function imageSize() {
        $pos = strpos($_POST['path'], $this->_config->get('upload_dir'));
        if ($pos !== false) {
            $info = getimagesize(substr_replace($_POST['path'], $this->_config->get('current_path'), $pos, strlen($this->_config->get('upload_dir'))));
            $this->sendResponse($info);
            exit;
        }
    }

    protected function saveImage() {
        //@Todo create functionn thaht return array whith param for the ugly function
        $current_path = $this->_config->get('current_path');
        $thumbs_base_path = $this->_config->get('thumbs_base_path');
        $relative_image_creation = $this->_config->get('relative_image_creation');
        $relative_path_from_current_pos = $this->_config->get('relative_path_from_current_pos');
        $relative_image_creation_name_to_prepend = $this->_config->get('relative_image_creation_name_to_prepend');
        $relative_image_creation_name_to_append = $this->_config->get('relative_image_creation_name_to_append');
        $relative_image_creation_width = $this->_config->get('relative_image_creation_width');
        $relative_image_creation_height = $this->_config->get('relative_image_creation_height');
        $relative_image_creation_option = $this->_config->get('relative_image_creation_option');
        $fixed_image_creation = $this->_config->get('fixed_image_creation');
        $fixed_path_from_filemanager = $this->_config->get('fixed_path_from_filemanager');
        $fixed_image_creation_name_to_prepend = $this->_config->get('fixed_image_creation_name_to_prepend');
        $fixed_image_creation_to_append = $this->_config->get('fixed_image_creation_to_append');
        $fixed_image_creation_width = $this->_config->get('fixed_image_creation_width');
        $fixed_image_creation_height = $this->_config->get('fixed_image_creation_height');
        $fixed_image_creation_option = $this->_config->get('fixed_image_creation_option');
        $transliteration = $this->_config->get('transliteration');
        $convert_spaces = $this->_config->get('convert_spaces');
        $replace_with = $this->_config->get('replace_with');

        $info = pathinfo($_POST['name']);

        if (
                strpos($_POST['path'], '/') === 0 || strpos($_POST['path'], '../') !== false || strpos($_POST['path'], './') === 0 || (strpos($_POST['url'], 'http://s3.amazonaws.com/feather') !== 0 && strpos($_POST['url'], 'https://s3.amazonaws.com/feather') !== 0) || $_POST['name'] != Utils::fix_filename($_POST['name'], $transliteration, $convert_spaces, $replace_with) || !in_array(strtolower($info['extension']), array('jpg', 'jpeg', 'png'))
        ) {
            $this->sendResponse('wrong data', 400);
            exit;
        }
        $image_data = file_get_contents($_POST['url']);
        if ($image_data === false) {
            $this->sendResponse($this->_translator->get('Aviary_No_Save'), 400);
            exit;
        }

        $magicianObj = new ImageLibmageLib($_POST['url']);
        $magicianObj->saveImage($current_path . $_POST['path'] . $_POST['name']);
        Utils::create_img($current_path . $_POST['path'] . $_POST['name'], $thumbs_base_path . $_POST['path'] . $_POST['name'], 122, 91);
        // TODO something with this function cause its blowing my mind
        Utils::new_thumbnails_creation(
                $current_path . $_POST['path'], $current_path . $_POST['path'] . $_POST['name'], $_POST['name'], $current_path, $relative_image_creation, $relative_path_from_current_pos, $relative_image_creation_name_to_prepend, $relative_image_creation_name_to_append, $relative_image_creation_width, $relative_image_creation_height, $relative_image_creation_option, $fixed_image_creation, $fixed_path_from_filemanager, $fixed_image_creation_name_to_prepend, $fixed_image_creation_to_append, $fixed_image_creation_width, $fixed_image_creation_height, $fixed_image_creation_option
        );
    }

    protected function extract() {
        if (strpos($_POST['path'], '/') === 0 || strpos($_POST['path'], '../') !== false || strpos($_POST['path'], './') === 0) {
            $this->sendResponse('wrong path', 400);
            exit;
        }
        $current_path = $this->_config->get('current_path');
        $path = $current_path . $_POST['path'];
        $info = pathinfo($path);
        $base_folder = $current_path . fix_dirname($_POST['path']) . "/";

        switch ($info['extension']) {
            case "zip":
                $zip = new ZipArchive;
                if ($zip->open($path) === true) {
                    //make all the folders
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $OnlyFileName = $zip->getNameIndex($i);
                        $FullFileName = $zip->statIndex($i);
                        if (substr($FullFileName['name'], -1, 1) == "/") {
                            create_folder($base_folder . $FullFileName['name']);
                        }
                    }
                    //unzip into the folders
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $OnlyFileName = $zip->getNameIndex($i);
                        $FullFileName = $zip->statIndex($i);

                        if (!(substr($FullFileName['name'], -1, 1) == "/")) {
                            $fileinfo = pathinfo($OnlyFileName);
                            if (in_array(strtolower($fileinfo['extension']), $ext)) {
                                copy('zip://' . $path . '#' . $OnlyFileName, $base_folder . $FullFileName['name']);
                            }
                        }
                    }
                    $zip->close();
                } else {
                    $this->sendResponse($this->_translator->get('Zip_No_Extract'), 500);
                    exit;
                }

                break;

            case "gz":
                $p = new PharData($path);
                $p->decompress(); // creates files.tar

                break;

            case "tar":
                // unarchive from the tar
                $phar = new PharData($path);
                $phar->decompressFiles();
                $files = array();
                check_files_extensions_on_phar($phar, $files, '', $ext);
                $phar->extractTo($current_path . fix_dirname($_POST['path']) . "/", $files, true);

                break;

            default:
                $this->sendResponse($this->_translator->get('Zip_Invalid'), 400);
                exit;
        }
    }

    protected function mediaPreview() {
        $current_path = $this->_config->get('current_path');

        $preview_file = $current_path . $_GET["file"];
        $info = pathinfo($preview_file);
        ob_start();
        ?>
        <div id="jp_container_1" class="jp-video " style="margin:0 auto;">
            <div class="jp-type-single">
                <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                <div class="jp-gui">
                    <div class="jp-video-play">
                        <a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
                    </div>
                    <div class="jp-interface">
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>
                        <div class="jp-current-time"></div>
                        <div class="jp-duration"></div>
                        <div class="jp-controls-holder">
                            <ul class="jp-controls">
                                <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
                                <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
                            </ul>
                            <div class="jp-volume-bar">
                                <div class="jp-volume-bar-value"></div>
                            </div>
                            <ul class="jp-toggles">
                                <li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
                                <li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
                                <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
                                <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
                            </ul>
                        </div>
                        <div class="jp-title" style="display:none;">
                            <ul>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="jp-no-solution">
                    <span>Update Required</span>
                    To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                </div>
            </div>
        </div>
        <?php if (in_array(strtolower($info['extension']), $ext_music)): ?>

            <script type="text/javascript">
                $(document).ready(function () {

                    $("#jquery_jplayer_1").jPlayer({
                        ready: function () {
                            $(this).jPlayer("setMedia", {
                                title: "<?php $_GET['title']; ?>",
                                mp3: "<?php echo $preview_file; ?>",
                                m4a: "<?php echo $preview_file; ?>",
                                oga: "<?php echo $preview_file; ?>",
                                wav: "<?php echo $preview_file; ?>"
                            });
                        },
                        swfPath: "js",
                        solution: "html,flash",
                        supplied: "mp3, m4a, midi, mid, oga,webma, ogg, wav",
                        smoothPlayBar: true,
                        keyEnabled: false
                    });
                });
            </script>

        <?php elseif (in_array(strtolower($info['extension']), $ext_video)): ?>

            <script type="text/javascript">
                $(document).ready(function () {

                    $("#jquery_jplayer_1").jPlayer({
                        ready: function () {
                            $(this).jPlayer("setMedia", {
                                title: "<?php $_GET['title']; ?>",
                                m4v: "<?php echo $preview_file; ?>",
                                ogv: "<?php echo $preview_file; ?>"
                            });
                        },
                        swfPath: "js",
                        solution: "html,flash",
                        supplied: "mp4, m4v, ogv, flv, webmv, webm",
                        smoothPlayBar: true,
                        keyEnabled: false
                    });

                });
            </script>

            <?php
        endif;

        $content = ob_get_clean();

        $this->sendResponse($content);
        exit;
    }

    protected function copyCut() {
        $current_path = $this->_config->get('current_path');
        $copy_cut_dirs = $this->_config->get('copy_cut_dirs');
        $copy_cut_max_size = $this->_config->get('copy_cut_max_size');
        $copy_cut_max_count = $this->_config->get('copy_cut_max_count');
        $copy_cut_files = $this->_config->get('copy_cut_files');

        if ($_POST['sub_action'] != 'copy' && $_POST['sub_action'] != 'cut') {
            $this->sendResponse('wrong sub-action', 400);
            exit;
        }

        if (trim($_POST['path']) == '') {
            $this->sendResponse('no path', 400);
            exit;
        }

        $path = $current_path . $_POST['path'];

        if (is_dir($path)) {
            // can't copy/cut dirs
            if ($copy_cut_dirs === false) {
                $this->sendResponse(sprintf($this->_translator->get('Copy_Cut_Not_Allowed'), ($_POST['sub_action'] == 'copy' ? lcfirst($this->_translator->get('Copy')) : lcfirst($this->_translator->get('Cut'))), $this->_translator->get('Folders')), 403);
                exit;
            }

            // size over limit
            if ($copy_cut_max_size !== false && is_int($copy_cut_max_size)) {
                if (($copy_cut_max_size * 1024 * 1024) < foldersize($path)) {
                    $this->sendResponse(sprintf($this->_translator->get('Copy_Cut_Size_Limit'), ($_POST['sub_action'] == 'copy' ? lcfirst($this->_translator->get('Copy')) : lcfirst($this->_translator->get('Cut'))), $copy_cut_max_size), 400);
                    exit;
                }
            }

            // file count over limit
            if ($copy_cut_max_count !== false && is_int($copy_cut_max_count)) {
                if ($copy_cut_max_count < filescount($path)) {
                    $this->sendResponse(sprintf($this->_translator->get('Copy_Cut_Count_Limit'), ($_POST['sub_action'] == 'copy' ? lcfirst($this->_translator->get('Copy')) : lcfirst($this->_translator->get('Cut'))), $copy_cut_max_count), 400);
                    exit;
                }
            }
        } else {
            // can't copy/cut files
            if ($copy_cut_files === false) {
                $this->sendResponse(sprintf($this->_translator->get('Copy_Cut_Not_Allowed'), ($_POST['sub_action'] == 'copy' ? lcfirst($this->_translator->get('Copy')) : lcfirst($this->_translator->get('Cut'))), $this->_translator->get('Files')), 403);
                exit;
            }
        }

        $_SESSION['RF']['clipboard']['path'] = $_POST['path'];
        $_SESSION['RF']['clipboard_action'] = $_POST['sub_action'];
    }

    protected function clearClipboard() {
        $_SESSION['RF']['clipboard'] = null;
        $_SESSION['RF']['clipboard_action'] = null;
    }

    protected function chmod() {
        $current_path = $this->_config->get('current_path');
        $chmod_files = $this->_config->get('chmod_files');
        $chmod_dirs = $this->_config->get('chmod_dirs');
        $path = $current_path . $_POST['path'];
        if (
                (is_dir($path) && $chmod_dirs === false) || (is_file($path) && $chmod_files === false) || (is_function_callable("chmod") === false)) {
            $this->sendResponse(sprintf($this->_translator->get('File_Permission_Not_Allowed'), (is_dir($path) ? lcfirst($this->_translator->get('Folders')) : lcfirst($this->_translator->get('Files'))), 403), 400);
            exit;
        } else {
            $perm = decoct(fileperms($path) & 0777);
            $perm_user = substr($perm, 0, 1);
            $perm_group = substr($perm, 1, 1);
            $perm_all = substr($perm, 2, 1);

            $ret = '<div id="files_permission_start">
				<form id="chmod_form">
					<table class="table file-perms-table">
						<thead>
							<tr>
								<td></td>
								<td>r&nbsp;&nbsp;</td>
								<td>w&nbsp;&nbsp;</td>
								<td>x&nbsp;&nbsp;</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>' . $this->_translator->get('User') . '</td>
								<td><input id="u_4" type="checkbox" data-value="4" data-group="user" ' . (chmod_logic_helper($perm_user, 4) ? " checked" : "") . '></td>
								<td><input id="u_2" type="checkbox" data-value="2" data-group="user" ' . (chmod_logic_helper($perm_user, 2) ? " checked" : "") . '></td>
								<td><input id="u_1" type="checkbox" data-value="1" data-group="user" ' . (chmod_logic_helper($perm_user, 1) ? " checked" : "") . '></td>
							</tr>
							<tr>
								<td>' . $this->_translator->get('Group') . '</td>
								<td><input id="g_4" type="checkbox" data-value="4" data-group="group" ' . (chmod_logic_helper($perm_group, 4) ? " checked" : "") . '></td>
								<td><input id="g_2" type="checkbox" data-value="2" data-group="group" ' . (chmod_logic_helper($perm_group, 2) ? " checked" : "") . '></td>
								<td><input id="g_1" type="checkbox" data-value="1" data-group="group" ' . (chmod_logic_helper($perm_group, 1) ? " checked" : "") . '></td>
							</tr>
							<tr>
								<td>' . $this->_translator->get('All') . '</td>
								<td><input id="a_4" type="checkbox" data-value="4" data-group="all" ' . (chmod_logic_helper($perm_all, 4) ? " checked" : "") . '></td>
								<td><input id="a_2" type="checkbox" data-value="2" data-group="all" ' . (chmod_logic_helper($perm_all, 2) ? " checked" : "") . '></td>
								<td><input id="a_1" type="checkbox" data-value="1" data-group="all" ' . (chmod_logic_helper($perm_all, 1) ? " checked" : "") . '></td>
							</tr>
							<tr>
								<td></td>
								<td colspan="3"><input type="text" class="input-block-level" name="chmod_value" id="chmod_value" value="' . $perm . '" data-def-value="' . $perm . '"></td>
							</tr>
						</tbody>
					</table>';

            if (is_dir($path)) {
                $ret .= '<div class="hero-unit" style="padding:10px;">' . $this->_translator->get('File_Permission_Recursive') . '<br/><br/>
							<ul class="unstyled">
								<li><label class="radio"><input value="none" name="apply_recursive" type="radio" checked> ' . $this->_translator->get('No') . '</label></li>
								<li><label class="radio"><input value="files" name="apply_recursive" type="radio"> ' . $this->_translator->get('Files') . '</label></li>
								<li><label class="radio"><input value="folders" name="apply_recursive" type="radio"> ' . $this->_translator->get('Folders') . '</label></li>
								<li><label class="radio"><input value="both" name="apply_recursive" type="radio"> ' . $this->_translator->get('Files') . ' & ' . $this->_translator->get('Folders') . '</label></li>
							</ul>
							</div>';
            }

            $ret .= '</form></div>';

            $this->sendResponse($ret);
            exit;
        }
    }

    protected function getLang() {

        if (!file_exists('lang/languages.php')) {
            $this->sendResponse(trans('Lang_Not_Found'), 404);
            exit;
        }

        $languages = Translation::getLanguages();
        if (!isset($languages) || !is_array($languages)) {
            $this->sendResponse(trans('Lang_Not_Found'), 404);
            exit;
        }

        $curr = $_SESSION['RF']['language'];

        $ret = '<select id="new_lang_select">';
        foreach ($languages as $code => $name) {
            $ret .= '<option value="' . $code . '"' . ($code == $curr ? ' selected' : '') . '>' . $name . '</option>';
        }
        $ret .= '</select>';

        $this->sendResponse($ret);
    }

    protected function changeLang() {

        $choosen_lang = $_POST['choosen_lang'];

        if (!file_exists('lang/' . $choosen_lang . '.php')) {
            $this->sendResponse(trans('Lang_Not_Found'), 404);
            exit;
        }

        $_SESSION['RF']['language'] = $choosen_lang;
        $_SESSION['RF']['language_file'] = 'lang/' . $choosen_lang . '.php';
    }

    protected function getFile() {
        $current_path = $this->_config->get('current_path');
        $viewerjs_enabled = $this->_config->get('viewerjs_enabled');
        $viewerjs_file_exts = $this->_config->get('viewerjs_file_exts');
        $googledoc_enabled = $this->_config->get('googledoc_enabled');
        $googledoc_file_exts = $this->_config->get('googledoc_file_exts');
        $preview_text_files = $this->_config->get('preview_text_files');
        $edit_text_files = $this->_config->get('edit_text_files');
        $previewable_text_file_exts = $this->_config->get('previewable_text_file_exts');
        $editable_text_file_exts = $this->_config->get('editable_text_file_exts');
        $previewable_text_file_exts_no_prettify = $this->_config->get('previewable_text_file_exts_no_prettify');
        $base_url = $this->_config->get('base_url');
        $upload_dir = $this->_config->get('upload_dir');

        $sub_action = $_GET['sub_action'];
        $preview_mode = $_GET["preview_mode"];

        if ($sub_action != 'preview' && $sub_action != 'edit') {
            $this->sendResponse("wrong action");
            exit;
        }

        $selected_file = ($sub_action == 'preview' ? $current_path . $_GET['file'] : $current_path . $_POST['path']);
        $info = pathinfo($selected_file);

        if (!file_exists($selected_file)) {
            $this->sendResponse($this->_translator->get('File_Not_Found'), 404);
            exit;
        }

        if ($preview_mode == 'text') {
            $is_allowed = ($sub_action == 'preview' ? $preview_text_files : $edit_text_files);
            $allowed_file_exts = ($sub_action == 'preview' ? $previewable_text_file_exts : $editable_text_file_exts);
        } elseif ($preview_mode == 'viewerjs') {
            $is_allowed = $viewerjs_enabled;
            $allowed_file_exts = $viewerjs_file_exts;
        } elseif ($preview_mode == 'google') {
            $is_allowed = $googledoc_enabled;
            $allowed_file_exts = $googledoc_file_exts;
        }

        if (!isset($allowed_file_exts) || !is_array($allowed_file_exts)) {
            $allowed_file_exts = array();
        }

        if (!in_array($info['extension'], $allowed_file_exts) || !isset($is_allowed) || $is_allowed === false || !is_readable($selected_file)
        ) {
            $this->sendResponse(sprintf($this->_translator->get('File_Open_Edit_Not_Allowed'), ($sub_action == 'preview' ? strtolower($this->_translator->get('Open')) : strtolower($this->_translator->get('Edit')))), 403);
            exit;
        }

        if ($sub_action == 'preview') {
            if ($preview_mode == 'text') {
                // get and sanities
                $data = stripslashes(htmlspecialchars(file_get_contents($selected_file)));

                $ret = '';

                if (!in_array($info['extension'], $previewable_text_file_exts_no_prettify)) {
                    $ret .= '<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?lang=' . $info['extension'] . '&skin=sunburst"></script>';
                    $ret .= '<div class="text-center"><strong>' . $info['basename'] . '</strong></div><pre class="prettyprint">' . $data . '</pre>';
                } else {
                    $ret .= '<div class="text-center"><strong>' . $info['basename'] . '</strong></div><pre class="no-prettify">' . $data . '</pre>';
                }
            } elseif ($preview_mode == 'viewerjs') {
                $ret = '<iframe id="viewer" src="js/ViewerJS/../../' . $_GET["file"] . '" allowfullscreen="" webkitallowfullscreen="" class="viewer-iframe"></iframe>';
            } elseif ($preview_mode == 'google') {
                $url_file = $base_url . $upload_dir . str_replace($current_path, '', $_GET["file"]);
                $googledoc_url = urlencode($url_file);
                $googledoc_html = "<iframe src=\"http://docs.google.com/viewer?url=" . $googledoc_url . "&embedded=true\" class=\"google-iframe\"></iframe>";
                $ret = '<div class="text-center"><strong>' . $info['basename'] . '</strong></div>' . $googledoc_html . '';
            }
        } else {
            $data = stripslashes(htmlspecialchars(file_get_contents($selected_file)));
            $ret = '<textarea id="textfile_edit_area" style="width:100%;height:300px;">' . $data . '</textarea>';
        }

        $this->sendResponse($ret);
        exit;
    }

}
