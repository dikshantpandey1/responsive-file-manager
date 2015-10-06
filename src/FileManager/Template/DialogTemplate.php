<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="robots" content="noindex,nofollow">
        <title>Responsive FileManager</title>
        <link rel="shortcut icon" href="img/ico/favicon.ico">
        <link href="src/FileManager/Public/css/style.css" rel="stylesheet" type="text/css" />
        <link href="src/FileManager/Public/js/jPlayer/skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css">
<!--[if lt IE 8]><style>
.img-container span, .img-container-mini span {
    display: inline-block;
    height: 100%;
}
</style><![endif]-->
        <script src="src/FileManager/Public/js/plugins.js"></script>
        <script src="src/FileManager/Public/js/jPlayer/jquery.jplayer/jquery.jplayer.js"></script>
        <script src="src/FileManager/Public/js/modernizr.custom.js"></script>
        <?php
        if ($this->config->get('aviary_active')) {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
                ?>
                <script src="https://dme0ih8comzn4.cloudfront.net/imaging/v2/editor.js"></script>
            <?php } else { ?>
                <script src="http://feather.aviary.com/imaging/v2/editor.js"></script>
                <?php
            }
        }
        ?>

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
        <![endif]-->

        <script>
                    var ext_img = new Array('<?php echo implode("','", $this->config->get('ext_img')) ?>');
                    var allowed_ext = new Array('<?php echo implode("','", $this->config->get('ext')) ?>');
                    var image_editor =<?php echo $this->config->get('aviary_active') ? "true" : "false"; ?>;
                    //dropzone config
                    Dropzone.options.rfmDropzone = {
                    dictInvalidFileType: "<?php echo $this->translator->get('Error_extension'); ?>",
                            dictFileTooBig: "<?php echo $this->translator->get('Error_Upload'); ?>",
                            dictResponseError: "SERVER ERROR",
                            paramName: "file", // The name that will be used to transfer the file
                            maxFilesize: <?php echo $this->config->get('MaxSizeUpload'); ?>, // MB
                            url: "upload.php",
<?php if ($this->apply != "apply_none") { ?>
                        init: function() {
                        this.on("success", function(file, res) {
                        file.previewElement.addEventListener("click", function() {
    <?php echo $this->apply; ?>(res, '<?php echo $this->field_id; ?>');
                        });
                        });
                        },
<?php } ?>
                    accept: function(file, done) {
                    var extension = file.name.split('.').pop();
                            extension = extension.toLowerCase();
                            if ($.inArray(extension, allowed_ext) > - 1) {
                    done();
                    }
                    else {
                    done("<?php echo $this->translator->get('Error_extension'); ?>");
                    }
                    }
                    };
                    if (image_editor) {
            var featherEditor = new Aviary.Feather({
<?php
foreach ($this->config->get('aviary_defaults_config') as $aopt_key => $aopt_val) {
    echo $aopt_key . ": " . json_encode($aopt_val) . ",";
}
?>
            onReady: function() {
            hide_animation();
            },
                    onSave: function(imageID, newURL) {
                    show_animation();
                            var img = document.getElementById(imageID);
                            img.src = newURL;
                            $.ajax({
                            type: "POST",
                                    url: "ajax_calls.php?action=save_img",
                                    data: { url: newURL, path:$('#sub_folder').val() + $('#fldr_value').val(), name:$('#aviary_img').attr('data-name') }
                            }).done(function(msg) {
                    featherEditor.close();
                            d = new Date();
                            $("figure[data-name='" + $('#aviary_img').attr('data-name') + "']").find('img').each(function(){
                    $(this).attr('src', $(this).attr('src') + "?" + d.getTime());
                    });
                            $("figure[data-name='" + $('#aviary_img').attr('data-name') + "']").find('figcaption a.preview').each(function(){
                    $(this).attr('data-url', $(this).data('url') + "?" + d.getTime());
                    });
                            hide_animation();
                    });
                            return false;
                    },
                    onError: function(errorObj) {
                    bootbox.alert(errorObj.message);
                            hide_animation();
                    }

            });
            }
        </script>
        <script src="src/FileManager/Public/js/include.js"></script>
    </head>
    <body>

        <?php
        echo $this->MainForm;
        echo $this->Uploader;
        ?>
        <div class="container-fluid">
            <?php
            echo $this->Header;
            echo $this->Breadcrunb;
            echo $this->Files;
            ?>

        </div>
        <script>
                    var files_prevent_duplicate = new Array();
<?php foreach ($this->files_prevent_duplicate as $key => $value): ?>
                files_prevent_duplicate[<?php echo $key; ?>] = '<?php echo $value; ?>';
<?php endforeach; ?>
        </script>
        <?php
        echo $this->Lightbox;
        echo $this->Loading;
        echo $this->Player;
        ?>

        <img id='aviary_img' src='' class="hide"/>

        <?php if ($this->config->get('lazy_loading_enabled')) { ?>
            <script>
                $(function(){
                $(".lazy-loaded").lazyload({
                event: 'scrollstop'
                });
                });
            </script>
        <?php } ?>

        <div>
            <?php
            foreach (array_reverse(file("tmp/log.txt")) as $key => $value) {
                echo $value . '<br/>';
            }
            file_put_contents('tmp/log.txt', '');
            ?>
        </div>
    </body>
</html>