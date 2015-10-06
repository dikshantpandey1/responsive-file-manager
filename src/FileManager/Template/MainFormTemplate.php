
<!-- vars -->

<input type="hidden" id="popup" value="<?php echo $this->popup; ?>" />
<input type="hidden" id="crossdomain" value="<?php echo $this->crossdomain; ?>" />
<input type="hidden" id="editor" value="<?php echo $this->editor; ?>" />
<input type="hidden" id="view" value="<?php echo $this->view; ?>" />
<input type="hidden" id="subdir" value="<?php echo $this->subdir; ?>" />
<input type="hidden" id="field_id" value="<?php echo $this->field_id; ?>" />
<input type="hidden" id="type_param" value="<?php echo $this->type_param; ?>" />
<input type="hidden" id="cur_dir" value="<?php echo $this->cur_dir; ?>" />
<input type="hidden" id="cur_dir_thumb" value="<?php echo $this->thumbs_path . $this->subdir; ?>" />
<input type="hidden" id="fldr_value" value="<?php echo $this->subdir; ?>"/>
<input type="hidden" id="sub_folder" value="<?php echo $this->rfm_subfolder; ?>"/>
<input type="hidden" id="return_relative_url" value="<?php echo $this->return_relative_url == true ? 1 : 0; ?>"/>
<input type="hidden" id="sort_by" value="<?php echo $this->sort_by; ?>" />
<input type="hidden" id="descending" value="<?php echo $this->descending ? 1 : 0; ?>" />
<input type="hidden" id="current_url" value="<?php echo str_replace(array('&filter=' . $this->filter, '&sort_by=' . $this->sort_by, '&descending=' . intval($this->descending)), array(''), $this->config->get('base_url') . $_SERVER['REQUEST_URI']); ?>" />
<input type="hidden" id="clipboard" value="<?php echo ((isset($_SESSION['RF']['clipboard']['path']) && trim($_SESSION['RF']['clipboard']['path']) != null) ? 1 : 0); ?>" />
<input type="hidden" id="base_url_true" value="<?php echo $this->base_url; ?>"/>

<!-- config -->

<input type="hidden" id="base_url" value="<?php echo $this->config->get('base_url') ?>"/>
<input type="hidden" id="lazy_loading_file_number_threshold" value="<?php echo $this->config->get('lazy_loading_file_number_threshold') ?>"/>
<input type="hidden" id="file_number_limit_js" value="<?php echo $this->config->get('file_number_limit_js'); ?>" />
<input type="hidden" id="copy_cut_max_size" value="<?php echo $this->config->get('copy_cut_max_size'); ?>" />
<input type="hidden" id="copy_cut_max_count" value="<?php echo $this->config->get('copy_cut_max_count'); ?>" />
<input type="hidden" id="transliteration" value="<?php echo $this->config->get('transliteration') ? "true" : "false"; ?>" />
<input type="hidden" id="convert_spaces" value="<?php echo $this->config->get('convert_spaces') ? "true" : "false"; ?>" />
<input type="hidden" id="replace_with" value="<?php echo $this->config->get('convert_spaces') ? $this->config->get('replace_with') : ""; ?>" />

<?php
$duplicate_files = 0;
if ($this->config->get('duplicate_files'))
    $duplicate_files = 1;
$copy_cut_files = 0;
if ($this->config->get('copy_cut_files'))
    $copy_cut_files = 1;
$copy_cut_dirs = 0;
if ($this->config->get('copy_cut_dirs'))
    $copy_cut_dirs = 1;
$chmod_files = 0;
if ($this->config->get('chmod_files'))
    $chmod_files = 1;
$chmod_dirs = 0;
if ($this->config->get('chmod_dirs'))
    $chmod_dirs = 1;
$edit_text_files = 0;
if ($this->config->get('edit_text_files'))
    $edit_text_files = 1;
?>
<input type="hidden" id="duplicate" value="<?php echo $duplicate_files; ?>" />

<input type="hidden" id="copy_cut_files_allowed" value="<?php echo $copy_cut_files; ?>" />
<input type="hidden" id="copy_cut_dirs_allowed" value="<?php echo $copy_cut_dirs; ?>" />
<input type="hidden" id="chmod_files_allowed" value="<?php echo $chmod_files; ?>" />
<input type="hidden" id="chmod_dirs_allowed" value="<?php echo $chmod_dirs; ?>" />
<input type="hidden" id="edit_text_files_allowed" value="<?php echo $edit_text_files; ?>" />

<!-- translator -->
<input type="hidden" id="lang_lang_change" value="<?php echo $this->translator->get('Lang_Change'); ?>" />
<input type="hidden" id="lang_edit_file" value="<?php echo $this->translator->get('Edit_File'); ?>" />
<input type="hidden" id="lang_new_file" value="<?php echo $this->translator->get('New_File'); ?>" />
<input type="hidden" id="lang_filename" value="<?php echo $this->translator->get('Filename'); ?>" />
<input type="hidden" id="lang_file_info" value="<?php echo strtoupper($this->translator->get('File_info')); ?>" />
<input type="hidden" id="lang_edit_image" value="<?php echo $this->translator->get('Edit_image'); ?>" />
<input type="hidden" id="lang_select" value="<?php echo $this->translator->get('Select'); ?>" />
<input type="hidden" id="lang_extract" value="<?php echo $this->translator->get('Extract'); ?>" />
<input type="hidden" id="lang_copy" value="<?php echo $this->translator->get('Copy'); ?>" />
<input type="hidden" id="lang_cut" value="<?php echo $this->translator->get('Cut'); ?>" />
<input type="hidden" id="lang_paste" value="<?php echo $this->translator->get('Paste'); ?>" />
<input type="hidden" id="lang_paste_here" value="<?php echo $this->translator->get('Paste_Here'); ?>" />
<input type="hidden" id="lang_paste_confirm" value="<?php echo $this->translator->get('Paste_Confirm'); ?>" />
<input type="hidden" id="lang_files" value="<?php echo $this->translator->get('Files'); ?>" />
<input type="hidden" id="lang_folders" value="<?php echo $this->translator->get('Folders'); ?>" />
<input type="hidden" id="lang_files_on_clipboard" value="<?php echo $this->translator->get('Files_ON_Clipboard'); ?>" />
<input type="hidden" id="lang_clear_clipboard_confirm" value="<?php echo $this->translator->get('Clear_Clipboard_Confirm'); ?>" />
<input type="hidden" id="lang_file_permission" value="<?php echo $this->translator->get('File_Permission'); ?>" />
<input type="hidden" id="insert_folder_name" value="<?php echo $this->translator->get('Insert_Folder_Name'); ?>" />
<input type="hidden" id="new_folder" value="<?php echo $this->translator->get('New_Folder'); ?>" />
<input type="hidden" id="ok" value="<?php echo $this->translator->get('OK'); ?>" />
<input type="hidden" id="cancel" value="<?php echo $this->translator->get('Cancel'); ?>" />
<input type="hidden" id="rename" value="<?php echo $this->translator->get('Rename'); ?>" />
<input type="hidden" id="lang_duplicate" value="<?php echo $this->translator->get('Duplicate'); ?>" />
<input type="hidden" id="lang_show_url" value="<?php echo $this->translator->get('Show_url'); ?>" />
