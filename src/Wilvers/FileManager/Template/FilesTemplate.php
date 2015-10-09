<div class="row-fluid ff-container">
    <div class="span12">
        <?php if (@opendir($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir) === FALSE) { ?>
            <br/>
            <div class="alert alert-error">
                <?php echo $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir; ?><br/>
                There is an error! The upload folder there isn't. Check your config.php file.
            </div>
        <?php } else { ?>
            <h4 id="help"><?php echo $this->translator->get('Swipe_help'); ?></h4>
            <?php if (isset($this->folder_message)) { ?>
                <div class="alert alert-block"><?php echo $this->folder_message; ?></div>
            <?php } ?>
            <?php if ($this->config->get('show_sorting_bar')) { ?>
                <!-- sorter -->
                <div class="sorter-container <?php echo "list-view" . $this->view; ?>">
                    <div class="file-name"><a class="sorter sort-name <?php
                        if ($this->sort_by == "name") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="name"><?php echo $this->translator->get('Filename'); ?></a></div>
                    <div class="file-date"><a class="sorter sort-date <?php
                        if ($this->sort_by == "date") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="date"><?php echo $this->translator->get('Date'); ?></a></div>
                    <div class="file-size"><a class="sorter sort-size <?php
                        if ($this->sort_by == "size") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="size"><?php echo $this->translator->get('Size'); ?></a></div>
                    <div class='img-dimension'><?php echo $this->translator->get('Dimension'); ?></div>
                    <div class='file-extension'><a class="sorter sort-extension <?php
                        if ($this->sort_by == "extension") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="extension"><?php echo $this->translator->get('Type'); ?></a></div>
                    <div class='file-operations'><?php echo $this->translator->get('Operations'); ?></div>
                </div>
            <?php } ?>

            <input type="hidden" id="file_number" value="<?php echo $this->n_files; ?>" />
            <!--ul class="thumbnails ff-items"-->
            <ul class="grid cs-style-2 <?php echo "list-view" . $this->view; ?>" id="main-item-container">
                <?php
                $jplayer_ext = array("mp4", "flv", "webmv", "webma", "webm", "m4a", "m4v", "ogv", "oga", "mp3", "midi", "mid", "ogg", "wav");
                foreach ($this->files as $file_array) {
                    $file = $file_array['file'];
                    if ($file == '.' ||
                            (isset($file_array['extension']) && $file_array['extension'] != $this->translator->get('Type_dir')) ||
                            ($file == '..' && $this->subdir == '') ||
                            in_array($file, $this->config->get('hidden_folders')) ||
                            ($this->filter != '' && $this->n_files > $this->config->get('file_number_limit_js') && $file != ".." && stripos($file, $this->filter) === false))
                        continue;
                    $new_name = \Wilvers\FileManager\Tools\Utils::fix_filename($file, $this->config->get('transliteration'));
                    if ($file != '..' && $file != $new_name) {
                        //rename
                        \Wilvers\FileManager\Tools\Utils::rename_folder($this->config->get('current_path') . $this->subdir . $file, $new_name, $this->config->get('transliteration'));
                        $file = $new_name;
                    }
                    //add in thumbs folder if not exist
                    if (!file_exists($this->thumbs_path . $this->subdir . $file))
                        \Wilvers\FileManager\Tools\Utils::create_folder(false, $this->thumbs_path . $this->subdir . $file);
                    $class_ext = 3;
                    if ($file == '..' && trim($this->subdir) != '') {
                        $src = explode("/", $this->subdir);
                        unset($src[count($src) - 2]);
                        $src = implode("/", $src);
                        if ($src == '')
                            $src = "/";
                    }
                    elseif ($file != '..') {
                        $src = $this->subdir . $file . "/";
                    }
                    ?>
                    <li data-name="<?php echo $file ?>" class="<?php
                    if ($file == '..')
                        echo 'back';
                    else
                        echo 'dir';
                    ?>" <?php if (($this->filter != '' && stripos($this->file, $this->filter) === false)) echo ' style="display:none;"'; ?>><?php
                            $file_prevent_rename = false;
                            $file_prevent_delete = false;
                            if (isset($filePermissions[$file])) {
                                $file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
                                $file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
                            }
                            ?><figure data-name="<?php echo $file ?>" class="<?php if ($file == "..") echo "back-"; ?>directory" data-type="<?php
                        if ($file != "..") {
                            echo "dir";
                        }
                        ?>">
                                    <?php if ($file == "..") { ?>
                                <input type="hidden" class="path" value="<?php echo str_replace('.', '', dirname($this->rfm_subfolder . $this->subdir)); ?>"/>
                                <input type="hidden" class="path_thumb" value="<?php echo dirname($this->thumbs_path . $this->subdir) . "/"; ?>"/>
                            <?php } ?>
                            <a class="folder-link" href="dialog.php?<?php echo $this->get_params . rawurlencode($src) . "&" . uniqid() ?>">
                                <div class="img-precontainer">
                                    <div class="img-container directory"><span></span>
                                        <img class="directory-img"  src="<?php echo $this->config->get('public.baseurl') . 'img/' . $this->config->get('icon_theme'); ?>/folder<?php
                                        if ($file == "..") {
                                            echo "_back";
                                        }
                                        ?>.png" />
                                    </div>
                                </div>
                                <div class="img-precontainer-mini directory">
                                    <div class="img-container-mini">
                                        <span></span>
                                        <img class="directory-img"  src="<?php echo $this->config->get('public.baseurl') . 'img/' . $this->config->get('icon_theme'); ?>/folder<?php
                                        if ($file == "..") {
                                            echo "_back";
                                        }
                                        ?>.png" />
                                    </div>
                                </div>
                                <?php if ($file == "..") { ?>
                                    <div class="box no-effect">
                                        <h4><?php echo $this->translator->get('Back') ?></h4>
                                    </div>
                                </a>

                            <?php } else { ?>
                                </a>
                                <div class="box">
                                    <h4 class="<?php
                                    if ($this->config->get('ellipsis_title_after_first_row')) {
                                        echo "ellipsis";
                                    }
                                    ?>"><a class="folder-link" data-file="<?php echo $file ?>" href="dialog.php?<?php echo $this->get_params . rawurlencode($src) . "&" . uniqid() ?>"><?php echo $file; ?></a></h4>
                                </div>
                                <input type="hidden" class="name" value="<?php echo $file_array['file_lcase']; ?>"/>
                                <input type="hidden" class="date" value="<?php echo $file_array['date']; ?>"/>
                                <input type="hidden" class="size" value="<?php echo $file_array['size']; ?>"/>
                                <input type="hidden" class="extension" value="<?php echo $this->translator->get('Type_dir'); ?>"/>
                                <div class="file-date"><?php echo date($this->translator->get('Date_type'), $file_array['date']) ?></div>
                                <?php if ($this->config->get('show_folder_size')) { ?>
                                    <div class="file-size"><?php echo \Wilvers\FileManager\Tools\Utils::makeSize($file_array['size']) ?></div>
                                    <input type="hidden" class="nfiles" value="<?php echo $file_array['nfiles']; ?>"/>
                                    <input type="hidden" class="nfolders" value="<?php echo $file_array['nfolders']; ?>"/>
                                <?php } ?>
                                <div class='file-extension'><?php echo $this->translator->get('Type_dir'); ?></div>
                                <figcaption>
                                    <a href="javascript:void('')" class="tip-left edit-button rename-file-paths <?php if ($this->config->get('rename_folders') && !$file_prevent_rename) echo "rename-folder"; ?>" title="<?php echo $this->translator->get('Rename') ?>" data-path="<?php echo $this->rfm_subfolder . $this->subdir . $file; ?>"">
                                        <i class="icon-pencil <?php if (!$this->config->get('rename_folders') || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>
                                    <a href="javascript:void('')" class="tip-left erase-button <?php if ($this->config->get('delete_folders') && !$file_prevent_delete) echo "delete-folder"; ?>" title="<?php echo $this->translator->get('Erase') ?>" data-confirm="<?php echo $this->translator->get('Confirm_Folder_del'); ?>" data-path="<?php echo $this->rfm_subfolder . $this->subdir . $file; ?>" >
                                        <i class="icon-trash <?php if (!$this->config->get('delete_folders') || $file_prevent_delete) echo 'icon-white'; ?>"></i>
                                    </a>
                                </figcaption>
                            <?php } ?>
                        </figure>
                    </li>
                    <?php
                }

                $files_prevent_duplicate = array();
                foreach ($this->files as $nu => $file_array) {
                    $file = $file_array['file'];

                    if ($file == '.' ||
                            $file == '..' ||
                            is_dir($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file) ||
                            in_array($file, $this->config->get('hidden_files')) ||
                            !in_array(strtolower($file_array['extension']), $this->config->get('ext')) ||
                            ($this->filter != '' && $this->n_files > $this->config->get('file_number_limit_js') && stripos($file, $this->filter) === false))
                        continue;

                    $file_path = $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file;
                    //check if file have illegal caracter

                    $filename = substr($file, 0, '-' . (strlen($file_array['extension']) + 1));

                    if ($file != \Wilvers\FileManager\Tools\Utils::fix_filename($file, $this->config->get('transliteration'))) {
                        $file1 = \Wilvers\FileManager\Tools\Utils::fix_filename($file, $this->config->get('transliteration'));
                        $file_path1 = ($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file1);
                        if (file_exists($file_path1)) {
                            $i = 1;
                            $info = pathinfo($file1);
                            while (file_exists($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $info['filename'] . ".[" . $i . "]." . $info['extension'])) {
                                $i++;
                            }
                            $file1 = $info['filename'] . ".[" . $i . "]." . $info['extension'];
                            $file_path1 = ($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file1);
                        }

                        $filename = substr($file1, 0, '-' . (strlen($file_array['extension']) + 1));
                        \Wilvers\FileManager\Tools\Utils::rename_file($file_path, \Wilvers\FileManager\Tools\Utils::fix_filename($filename, $this->config->get('transliteration')), $this->config->get('transliteration'));
                        $file = $file1;
                        $file_array['extension'] = \Wilvers\FileManager\Tools\Utils::fix_filename($file_array['extension'], $this->config->get('transliteration'));
                        $file_path = $file_path1;
                    }

                    $is_img = false;
                    $is_video = false;
                    $is_audio = false;
                    $show_original = false;
                    $show_original_mini = false;
                    $mini_src = "";
                    $src_thumb = "";
                    $extension_lower = strtolower($file_array['extension']);
                    if (in_array($extension_lower, $this->config->get('ext_img'))) {
                        $src = $this->base_url . $this->cur_dir . rawurlencode($file);
                        $mini_src = $src_thumb = $this->thumbs_path . $this->subdir . $file;
                        //add in thumbs folder if not exist
                        if (!file_exists($src_thumb)) {
                            try {
                                if (!\Wilvers\FileManager\Tools\Utils::create_img($file_path, $src_thumb, 122, 91)) {
                                    $src_thumb = $mini_src = "";
                                } else {
                                    \Wilvers\FileManager\Tools\Utils::new_thumbnails_creation($this->config->get('current_path') . $this->rfm_subfolder . $this->subdir, $file_path, $file, $this->config->get('current_path'), '', '', '', '', '', '', '', $this->config->get('fixed_image_creation'), $this->config->get('fixed_path_from_filemanager'), $$this->config->get('fixed_image_creation_name_to_prepend'), $this->config->get('fixed_image_creation_to_append'), $this->config->get('fixed_image_creation_width'), $this->config->get('fixed_image_creation_height'), $$this->config->get('fixed_image_creation_option'));
                                }
                            } catch (Exception $e) {
                                $src_thumb = $mini_src = "";
                            }
                        }
                        $is_img = true;
                        $src_thumb = $this->config->get('src_thumb_alias') . $this->rfm_subfolder . $this->subdir . rawurlencode($file);

                        //check if is smaller than thumb
                        list($img_width, $img_height, $img_type, $attr) = @getimagesize($file_path);
                        if ($img_width < 122 && $img_height < 91) {
                            $src_thumb = $this->config->get('src_thumb_alias') . $this->rfm_subfolder . $this->subdir . $file;
                            $show_original = true;
                        }

                        if ($img_width < 45 && $img_height < 38) {
                            $mini_src = $this->config->get('src_thumb_alias') . $this->rfm_subfolder . $this->subdir . $file;
                            $show_original_mini = true;
                        }
                    }
                    $is_icon_thumb = false;
                    $is_icon_thumb_mini = false;
                    $no_thumb = false;
                    if ($src_thumb == "") {
                        $no_thumb = true;
                        if (file_exists('img/' . $icon_theme . '/' . $extension_lower . ".jpg")) {
                            $src_thumb = 'img/' . $icon_theme . '/' . $extension_lower . ".jpg";
                        } else {
                            $src_thumb = "img/" . $icon_theme . "/default.jpg";
                        }
                        $is_icon_thumb = true;
                    }
                    if ($mini_src == "") {
                        $is_icon_thumb_mini = false;
                    }

                    $class_ext = 0;
                    if (in_array($extension_lower, $this->config->get('ext_video'))) {
                        $class_ext = 4;
                        $is_video = true;
                    } elseif (in_array($extension_lower, $this->config->get('ext_img'))) {
                        $class_ext = 2;
                    } elseif (in_array($extension_lower, $this->config->get('ext_music'))) {
                        $class_ext = 5;
                        $is_audio = true;
                    } elseif (in_array($extension_lower, $this->config->get('ext_misc'))) {
                        $class_ext = 3;
                    } else {
                        $class_ext = 1;
                    }
                    if ((!($this->type == 1 && !$is_img) && !(($this->type == 3 && !$is_video) && ($this->type == 3 && !$is_audio))) && $class_ext > 0) {
                        ?>
                        <li class="ff-item-type-<?php echo $class_ext; ?> file"  data-name="<?php echo $file; ?>" <?php if (($this->filter != '' && stripos($file, $this->filter) === false)) echo ' style="display:none;"'; ?>><?php
                            $file_prevent_rename = false;
                            $file_prevent_delete = false;
                            if (isset($filePermissions[$file])) {
                                if (isset($filePermissions[$file]['prevent_duplicate']) && $filePermissions[$file]['prevent_duplicate']) {
                                    $files_prevent_duplicate[] = $file;
                                }
                                $file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
                                $file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
                            }
                            ?>		<figure data-name="<?php echo $file ?>" data-type="<?php
                            if ($is_img) {
                                echo "img";
                            } else {
                                echo "file";
                            }
                            ?>">
                                <a href="javascript:void('')" class="link" data-file="<?php echo $file; ?>" data-function="<?php echo $this->apply; ?>">
                                    <div class="img-precontainer">
                                        <?php if ($is_icon_thumb) { ?><div class="filetype"><?php echo $extension_lower ?></div><?php } ?>
                                        <div class="img-container">
                                            <span></span>
                                            <img class="<?php echo $show_original ? "original" : "" ?><?php echo $is_icon_thumb ? " icon" : "" ?><?php echo $this->lazy_loading_enabled ? " lazy-loaded" : "" ?>" <?php echo $this->lazy_loading_enabled ? "data-original" : "src"; ?>="<?php echo $src_thumb; ?>">
                                        </div>
                                    </div>
                                    <div class="img-precontainer-mini <?php if ($is_img) echo 'original-thumb' ?>">
                                        <div class="filetype <?php echo $extension_lower ?> <?php if (in_array($extension_lower, $this->config->get('editable_text_file_exts'))) echo 'edit-text-file-allowed' ?> <?php
                                        if (!$is_icon_thumb) {
                                            echo "hide";
                                        }
                                        ?>"><?php echo $extension_lower ?></div>
                                        <div class="img-container-mini">
                                            <span></span>
                                            <?php if ($mini_src != "") { ?>
                                                <img class="<?php echo $show_original_mini ? "original" : "" ?><?php echo $is_icon_thumb_mini ? " icon" : "" ?><?php echo $this->lazy_loading_enabled ? " lazy-loaded" : "" ?>" <?php echo $this->lazy_loading_enabled ? "data-original" : "src" ?>="<?php echo $mini_src; ?>">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if ($is_icon_thumb) { ?>
                                        <div class="cover"></div>
                                    <?php } ?>
                                </a>
                                <div class="box">
                                    <h4 class="<?php
                                    if ($this->config->get('ellipsis_title_after_first_row')) {
                                        echo "ellipsis";
                                    }
                                    ?>"><a href="javascript:void('')" class="link" data-file="<?php echo $file; ?>" data-function="<?php echo $this->apply; ?>">
                                            <?php echo $filename; ?></a> </h4>
                                </div>
                                <input type="hidden" class="date" value="<?php echo $file_array['date']; ?>"/>
                                <input type="hidden" class="size" value="<?php echo $file_array['size'] ?>"/>
                                <input type="hidden" class="extension" value="<?php echo $extension_lower; ?>"/>
                                <input type="hidden" class="name" value="<?php echo $file_array['file_lcase']; ?>"/>
                                <div class="file-date"><?php echo date($this->translator->get('Date_type'), $file_array['date']) ?></div>
                                <div class="file-size"><?php echo \Wilvers\FileManager\Tools\Utils::makeSize($file_array['size']) ?></div>
                                <div class='img-dimension'><?php
                                    if ($is_img) {
                                        echo $img_width . "x" . $img_height;
                                    }
                                    ?></div>
                                <div class='file-extension'><?php echo $extension_lower; ?></div>
                                <figcaption>
                                    <form action="force_download.php" method="post" class="download-form" id="form<?php echo $nu; ?>">
                                        <input type="hidden" name="path" value="<?php echo $this->rfm_subfolder . $this->subdir ?>"/>
                                        <input type="hidden" class="name_download" name="name" value="<?php echo $file ?>"/>

                                        <a title="<?php echo $this->translator->get('Download') ?>" class="tip-right" href="javascript:void('')" onclick="$('#form<?php echo $nu; ?>').submit();"><i class="icon-download"></i></a>
                                        <?php if ($is_img && $src_thumb != "" && $extension_lower != "tiff" && $extension_lower != "tif") { ?>
                                            <a class="tip-right preview" title="<?php echo $this->translator->get('Preview') ?>" data-url="<?php echo $src; ?>" data-toggle="lightbox" href="#previewLightbox"><i class=" icon-eye-open"></i></a>
                                        <?php } elseif (($is_video || $is_audio) && in_array($extension_lower, $jplayer_ext)) { ?>
                                            <a class="tip-right modalAV <?php
                                            if ($is_audio) {
                                                echo "audio";
                                            } else {
                                                echo "video";
                                            }
                                            ?>"
                                               title="<?php echo $this->translator->get('Preview') ?>" data-url="ajax_calls.php?action=media_preview&title=<?php echo $filename; ?>&file=<?php echo $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file; ?>"
                                               href="javascript:void('');" ><i class=" icon-eye-open"></i></a>
                                           <?php } elseif ($preview_text_files && in_array($extension_lower, $previewable_text_file_exts)) { ?>
                                            <a class="tip-right file-preview-btn" title="<?php echo $this->translator->get('Preview') ?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=text&title=<?php echo $filename; ?>&file=<?php echo $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file; ?>"
                                               href="javascript:void('');" ><i class=" icon-eye-open"></i></a>
                                           <?php } elseif ($googledoc_enabled && in_array($extension_lower, $googledoc_file_exts)) { ?>
                                            <a class="tip-right file-preview-btn" title="<?php echo $this->translator->get('Preview') ?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=google&title=<?php echo $filename; ?>&file=<?php echo $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file; ?>"
                                               href="docs.google.com;" ><i class=" icon-eye-open"></i></a>

                                        <?php } elseif ($viewerjs_enabled && in_array($extension_lower, $viewerjs_file_exts)) { ?>
                                            <a class="tip-right file-preview-btn" title="<?php echo $this->translator->get('Preview') ?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=viewerjs&title=<?php echo $filename; ?>&file=<?php echo $this->config->get('current_path') . $this->rfm_subfolder . $this->subdir . $file; ?>"
                                               href="docs.google.com;" ><i class=" icon-eye-open"></i></a>

                                        <?php } else { ?>
                                            <a class="preview disabled"><i class="icon-eye-open icon-white"></i></a>
                                        <?php } ?>
                                        <a href="javascript:void('')" class="tip-left edit-button rename-file-paths <?php if ($this->config->get('rename_files') && !$file_prevent_rename) echo "rename-file"; ?>" title="<?php echo $this->translator->get('Rename') ?>" data-path="<?php echo $this->rfm_subfolder . $this->subdir . $file; ?>">
                                            <i class="icon-pencil <?php if (!$this->config->get('rename_files') || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>

                                        <a href="javascript:void('')" class="tip-left erase-button <?php if ($this->config->get('delete_files') && !$file_prevent_delete) echo "delete-file"; ?>" title="<?php echo $this->translator->get('Erase') ?>" data-confirm="<?php echo $this->translator->get('Confirm_del'); ?>" data-path="<?php echo $this->rfm_subfolder . $this->subdir . $file; ?>">
                                            <i class="icon-trash <?php if (!$this->config->get('delete_files') || $file_prevent_delete) echo 'icon-white'; ?>"></i>
                                        </a>
                                    </form>
                                </figcaption>
                            </figure>
                        </li>
                        <?php
                    }
                }
                ?></div>
    </ul>
<?php } ?>
</div>
