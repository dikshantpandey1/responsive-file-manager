<!-- breadcrumb div start -->

<div class="row-fluid">
    <?php
    $link = "dialog.php?" . $this->get_params;
    ?>
    <ul class="breadcrumb">
        <li class="pull-left"><a href="<?php echo $link ?>/"><i class="icon-home"></i></a></li>
        <li><span class="divider">/</span></li>
        <?php
        $bc = explode("/", $this->subdir);
        $tmp_path = '';
        if (!empty($bc))
            foreach ($bc as $k => $b) {
                $tmp_path.=$b . "/";
                if ($k == count($bc) - 2) {
                    ?> <li class="active"><?php echo $b ?></li><?php } elseif ($b != "") {
                    ?>
                    <li><a href="<?php echo $link . $tmp_path ?>"><?php echo $b ?></a></li><li><span class="divider"><?php echo "/"; ?></span></li>
                    <?php
                }
            }
        ?>

        <li class="pull-right"><a class="btn-small" href="javascript:void('')" id="info"><i class="icon-question-sign"></i></a></li>
        <li class="pull-right"><a class="btn-small" href="javascript:void('')" id="change_lang_btn"><i class="icon-globe"></i></a></li>
        <li class="pull-right"><a id="refresh" class="btn-small" href="dialog.php?<?php echo $this->get_params . $this->subdir . "&" . uniqid() ?>"><i class="icon-refresh"></i></a></li>

        <li class="pull-right">
            <div class="btn-group">
                <a class="btn dropdown-toggle sorting-btn" data-toggle="dropdown" href="#">
                    <i class="icon-signal"></i>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu pull-left sorting">
                    <li class="text-center"><strong><?php echo $this->translator->get('Sorting') ?></strong></li>
                    <li><a class="sorter sort-name <?php
                        if ($this->sort_by == "name") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="name"><?php echo $this->translator->get('Filename'); ?></a></li>
                    <li><a class="sorter sort-date <?php
                        if ($this->sort_by == "date") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="date"><?php echo $this->translator->get('Date'); ?></a></li>
                    <li><a class="sorter sort-size <?php
                        if ($this->sort_by == "size") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="size"><?php echo $this->translator->get('Size'); ?></a></li>
                    <li><a class="sorter sort-extension <?php
                        if ($this->sort_by == "extension") {
                            echo ($this->descending) ? "descending" : "ascending";
                        }
                        ?>" href="javascript:void('')" data-sort="extension"><?php echo $this->translator->get('Type'); ?></a></li>
                </ul>
            </div>
        </li>
        <li><small class="hidden-phone">(<span id="files_number"><?php echo $this->current_files_number . "</span> " . $this->translator->get('Files') . " - <span id='folders_number'>" . $this->current_folders_number . "</span> " . $this->translator->get('Folders'); ?>)</small></li>
    </ul>
</div>