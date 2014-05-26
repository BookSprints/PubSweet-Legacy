<!DOCTYPE html>
<html>
<head>
    <title><?php echo $chaptername['title']; ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/bootstrap.min.css"/>
    <style type="text/css">
        textarea {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
        }

        body {
            background-color: rgba(169, 169, 169, 0.22) !important;
        }

        .sheet {
            background-color: white;
            padding: 1in;
            width: 7.5in;
            outline: #a9a9a9 solid 1px !important;
            min-height: 11in;
            margin: auto;
            margin-top: 60px;
            margin-bottom: 60px;
        }

        .cke_button__savebutton_icon, .cke_button__backbutton_icon {
            display: none !important;
        }

        .cke_button__savebutton_label, .cke_button__backbutton_label {
            display: inline !important;
        }

        #content {
            font-family: "myriad";
            /*font-family: "libre-baskerville";*/
        }

        .cke_top {
            background: white !important;
        }
    </style>

    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        $(function () {
            // Turn off automatic editor creation first.
            CKEDITOR.disableAutoInline = true;
            var config = {
                startupFocus : true,
                extraPlugins: 'sharedspace,savebutton,imagebrowser,backup,placeholder,indentlist,autosave,customlanguage',
                // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
                toolbar: [
                    { name: 'back', items: [ 'Backbutton' ] },
                    { name: 'savebutton', items: [ 'Savebutton'] },
                    { name: 'document', groups: [ 'mode', 'document', 'doctools' ],
                        items: [ /*'Source', '-', 'Save', 'NewPage', 'Preview',*/ 'Print'/*, '-', 'Templates'*/ ] },
                    /*{ name: 'clipboard', groups: [ 'clipboard', 'undo' ],
                     items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },*/
                    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ],
                        items: [ 'Find', 'Replace', '-', /*'SelectAll', '-',*/ 'Scayt' ] },
                    /*{ name: 'forms',
                     items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },*/
//                        '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ],
                        items: [ 'Bold', 'Italic', /*'Underline', 'Strike', 'Subscript', 'Superscript',*/
                            '-', 'RemoveFormat' ] },
                    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ],
                        items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                            '-', 'Blockquote', /*'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',*/
                            '-', 'BidiLtr', 'BidiRtl' ] },
                    /*{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },*/
                    { name: 'insert', items: [ 'Image', /* 'Flash', */'Table',
                        /*'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'*/ ] },
                    { name: 'styles', items: [ 'Styles', 'Format'/*, 'Font', 'FontSize'*/ ] },/*
                     /*{ name: 'colors', items: [ 'TextColor', 'BGColor' ] }*/
                    { name: 'tools', items: [ /*'Maximize', */'ShowBlocks' ] },
                    /*{ name: 'others', items: [ '-' ] },
                     { name: 'about', items: [ 'About' ] },*/
                    {name: 'language', groups:'Language', items:['customlanguage']}
                ],
                removePlugins: 'forms,flash,floatingspace,iframe,newpage,resize,maximize,smiley,contextmenu,liststyle,tabletools,lite',
                sharedSpaces: {
                    top: 'top',
                    bottom: 'bottom'
                },
                imageBrowser_listUrl: "../../book/images",
                on: {
                    'changeLanguage': function(event){
                        event.editor.destroy();
                        config.language = event.data;
                        CKEDITOR.inline('content', config);
                    },
                    'instanceReady':
                        function (evt) {
                            $('#content').focus();
                        }
                }
            };
            CKEDITOR.inline('content', config);
//            CKEDITOR.on('instanceReady',
//                );
        });
    </script>
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div id="top"></div>
</div>
<div id="content" class="sheet" contenteditable="true" autofocus data-chapter="<?php echo $chaptername['id']; ?>"
     data-back-url="<?php echo base_url('book/tocmanager/' . $chaptername['book_id']); ?>"
     data-action-url="<?php echo base_url('chapter/saveContent'); ?>">
    <?php echo empty($chaptername['content']) ? '<h1>' . $chaptername['title'] . '</h1>' : $chaptername['content']; ?>
</div>
<div class="navbar navbar-fixed-bottom" id="bottom"></div>
</body>
</html>