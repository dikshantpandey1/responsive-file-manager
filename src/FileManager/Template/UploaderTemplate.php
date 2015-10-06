<div class="uploader">
    <div class="text-center">
        <button class="btn btn-inverse close-uploader"><i class="icon-backward icon-white"></i> <?php echo $this->translator->get('Return_Files_List') ?></button>
    </div>
    <div class="space10"></div><div class="space10"></div>
    <div class="tabbable upload-tabbable"> <!-- Only required for left/right tabs -->
        <?php if ($this->config->get('java_upload')) { ?>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><?php echo $this->translator->get('Upload_base'); ?></a></li>
                <li><a href="#tab2" id="uploader-btn" data-toggle="tab"><?php echo $this->translator->get('Upload_java'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab1">
                <?php } ?>
                <form action="dialog.php" method="post" enctype="multipart/form-data" id="rfmDropzone" class="dropzone">
                    <input type="hidden" name="path" value="<?php echo $this->cur_path ?>"/>
                    <input type="hidden" name="path_thumb" value="<?php echo $this->thumbs_path . $this->subdir ?>"/>
                    <div class="fallback">
                        <h3><?php echo $this->translator->get('Upload_file') ?>:</h3><br/>
                        <input name="file" type="file" />
                        <input type="hidden" name="fldr" value="<?php echo $this->subdir; ?>"/>
                        <input type="hidden" name="view" value="<?php echo $this->view; ?>"/>
                        <input type="hidden" name="type" value="<?php echo $this->type_param; ?>"/>
                        <input type="hidden" name="field_id" value="<?php echo $this->field_id; ?>"/>
                        <input type="hidden" name="relative_url" value="<?php echo $this->return_relative_url; ?>"/>
                        <input type="hidden" name="popup" value="<?php echo $this->popup; ?>"/>
                        <input type="hidden" name="lang" value="<?php echo $this->lang; ?>"/>
                        <input type="hidden" name="filter" value="<?php echo $this->filter; ?>"/>
                        <input type="submit" name="submit" value="<?php echo $this->translator->get('OK') ?>" />
                </form>
            </div>
            <div class="upload-help"><?php echo $this->translator->get('Upload_base_help'); ?></div>
            <?php if ($this->config->get('java_upload')) { ?>
            </div>
            <div class="tab-pane" id="tab2">
                <div id="iframe-container"></div>
                <div class="upload-help"><?php echo $this->translator->get('Upload_java_help'); ?></div>
            <?php } ?>
        </div>
    </div>
</div>

</div>