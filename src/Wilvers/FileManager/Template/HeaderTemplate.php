<!-- header div start -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="brand"><?php echo $this->translator->get('Toolbar'); ?></div>
            <div class="nav-collapse collapse">
                <div class="filters">
                    <div class="row-fluid">
                        <div class="span4 half">
                            <?php if ($this->config->get('upload_files')) { ?>
                                <button class="tip btn upload-btn" title="<?php echo $this->translator->get('Upload_file'); ?>"><i class="rficon-upload"></i></button>
                            <?php } ?>
                            <?php if ($this->config->get('create_text_files')) { ?>
                                <button class="tip btn create-file-btn" title="<?php echo $this->translator->get('New_File'); ?>"><i class="icon-plus"></i><i class="icon-file"></i></button>
                            <?php } ?>
                            <?php if ($this->config->get('create_folders')) { ?>
                                <button class="tip btn new-folder" title="<?php echo $this->translator->get('New_Folder') ?>"><i class="icon-plus"></i><i class="icon-folder-open"></i></button>
                            <?php } ?>
                            <?php if ($this->config->get('copy_cut_files') || $this->config->get('copy_cut_dirs')) { ?>
                                <button class="tip btn paste-here-btn" title="<?php echo $this->translator->get('Paste_Here'); ?>"><i class="rficon-clipboard-apply"></i></button>
                                <button class="tip btn clear-clipboard-btn" title="<?php echo $this->translator->get('Clear_Clipboard'); ?>"><i class="rficon-clipboard-clear"></i></button>
                            <?php } ?>
                        </div>
                        <div class="span2 half view-controller">
                            <button class="btn tip<?php if ($this->view == 0) echo " btn-inverse"; ?>" id="view0" data-value="0" title="<?php echo $this->translator->get('View_boxes'); ?>"><i class="icon-th <?php if ($this->view == 0) echo "icon-white"; ?>"></i></button>
                            <button class="btn tip<?php if ($this->view == 1) echo " btn-inverse"; ?>" id="view1" data-value="1" title="<?php echo $this->translator->get('View_list'); ?>"><i class="icon-align-justify <?php if ($this->view == 1) echo "icon-white"; ?>"></i></button>
                            <button class="btn tip<?php if ($this->view == 2) echo " btn-inverse"; ?>" id="view2" data-value="2" title="<?php echo $this->translator->get('View_columns_list'); ?>"><i class="icon-fire <?php if ($this->view == 2) echo "icon-white"; ?>"></i></button>
                        </div>
                        <div class="span6 entire types">
                            <span><?php echo $this->translator->get('Filters'); ?>:</span>
                            <?php if ($this->type != 1 && $this->type != 3) { ?>
                                <input id="select-type-1" name="radio-sort" type="radio" data-item="ff-item-type-1" checked="checked"  class="hide"  />
                                <label id="ff-item-type-1" title="<?php echo $this->translator->get('Files'); ?>" for="select-type-1" class="tip btn ff-label-type-1"><i class="icon-file"></i></label>
                                <input id="select-type-2" name="radio-sort" type="radio" data-item="ff-item-type-2" class="hide"  />
                                <label id="ff-item-type-2" title="<?php echo $this->translator->get('Images'); ?>" for="select-type-2" class="tip btn ff-label-type-2"><i class="icon-picture"></i></label>
                                <input id="select-type-3" name="radio-sort" type="radio" data-item="ff-item-type-3" class="hide"  />
                                <label id="ff-item-type-3" title="<?php echo $this->translator->get('Archives'); ?>" for="select-type-3" class="tip btn ff-label-type-3"><i class="icon-inbox"></i></label>
                                <input id="select-type-4" name="radio-sort" type="radio" data-item="ff-item-type-4" class="hide"  />
                                <label id="ff-item-type-4" title="<?php echo $this->translator->get('Videos'); ?>" for="select-type-4" class="tip btn ff-label-type-4"><i class="icon-film"></i></label>
                                <input id="select-type-5" name="radio-sort" type="radio" data-item="ff-item-type-5" class="hide"  />
                                <label id="ff-item-type-5" title="<?php echo $this->translator->get('Music'); ?>" for="select-type-5" class="tip btn ff-label-type-5"><i class="icon-music"></i></label>
                            <?php } ?>
                            <input accesskey="f" type="text" class="filter-input <?php echo (($this->type != 1 && $this->type != 3) ? '' : 'filter-input-notype'); ?>" id="filter-input" name="filter" placeholder="<?php echo strtolower($this->translator->get('Text_filter')); ?>..." value="<?php echo $this->filter; ?>"/><?php if ($this->n_files > $this->config->get('file_number_limit_js')) { ?><label id="filter" class="btn"><i class="icon-play"></i></label><?php } ?>

                            <input id="select-type-all" name="radio-sort" type="radio" data-item="ff-item-type-all" class="hide"  />
                            <label id="ff-item-type-all" title="<?php echo $this->translator->get('All'); ?>" <?php if ($this->type == 1 || $this->type == 3) { ?>style="visibility: hidden;" <?php } ?> data-item="ff-item-type-all" for="select-type-all" style="margin-rigth:0px;" class="tip btn btn-inverse ff-label-type-all"><i class="icon-remove icon-white"></i></label>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

