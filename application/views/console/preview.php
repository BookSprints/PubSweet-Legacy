<!DOCTYPE HTML>
<html lang="<?php echo empty($_GET['l'])? 'en-US' : sprintf('%s',$_GET['l']) ;?>">
<head>
    <meta charset="UTF-8">
    <title>Preview - <?php echo $bookTitle;?></title>
    <!--    <link rel="stylesheet" href="https://raw.github.com/sourcefabric/BookJS/0.25.0/book.css">-->
    <?php if(isset($prettify) && $prettify):?>
        <link rel="stylesheet" href="<?php echo base_url();?>public/js/prettifier/prettify.css"/>
    <?php endif;?>
<!--    <link rel="stylesheet" href="book.css">-->
    <?php if (!isset($editablecss) || !$editablecss):
            foreach ($css as $item) :?>
                <style type="text/css"><?php echo $item; ?></style>
            <?php endforeach;
        else: ?>
        <style>
            body style {
                display: block;
                background: #e1e1e1;
                color: black;
                font: 10px courier;
                padding: 5px;
                white-space: pre;
                width: 180px;
                height: 100%;
                position: fixed;
                top: 5px;
                bottom: 5px;
                overflow-y: auto;
                border: 1px solid green;
            }
        </style>
    <?php endif; ?>
    <?php if(isset($prettify) && $prettify):?>
        <script type="text/javascript" src="<?php echo base_url();?>public/js/prettifier/prettify.js"></script>
    <?php endif;?>
<!--    <script type="text/javascript" src="--><?php //echo base_url();?><!--public/js/cssregions.min.js"></script>-->
    <script type="text/javascript">
        window.paginationConfig = {
            'sectionStartMarker': 'div.section',
            'sectionTitleMarker': 'h1.sectiontitle',
            'chapterStartMarker': 'div.chapter',
            'chapterTitleMarker': 'h1.chaptertitle',
            'flowElement': "document.getElementById('flow')",
            'alwaysEven': false,
            'enableFrontmatter': true,
            'bulkPagesToAdd': 50,
            'pagesToAddIncrementRatio': 1.4,
            'pageHeight': 9.68,
            'pageWidth': 7.44,
            'lengthUnit: ': 'in',
            'oddAndEvenMargins': false,
            'frontmatterContents': '<h1><?php echo $bookTitle;?></h1>'
                + '<div class="pagination-pagebreak"></div>',
            'autoStart': true,
            'polyfill': true

        };
        <?php if(isset($_GET['h']) && !empty($_GET['h'])):?>
        paginationConfig.pageHeight = <?php echo sprintf('%f',$_GET['h']);?>;
        <?php endif;?>
        <?php if(isset($_GET['w']) && !empty($_GET['w'])):?>
        paginationConfig.pageWidth = <?php echo sprintf('%f',$_GET['w']);?>;
        <?php endif;?>
        <?php if(isset($_GET['u']) && !empty($_GET['u'])):?>
        paginationConfig.lengthUnit = '<?php echo sprintf('%s',$_GET['u']);?>';
        <?php endif;?>
    </script>

    <script type="text/javascript" src="<?php echo base_url();?>public/js/book.js"></script>
    <style>
        /*@media print {
            .pagination-page{
                margin-right: 0;
                margin-left: 0;
                width: 100%;
                border: none;
                margin-bottom: 0;
            }
        }*/
    </style>
</head>
<body
    <?php if(isset($prettify) && $prettify):?>
    onload="prettyPrint();"
    <?php endif;?>
    >
<?php if (isset($editablecss) && !!$editablecss):
    foreach ($css as $item) :?>
    <style contenteditable type="text/css"><?php echo $item; ?></style>
    <?php endforeach;
endif; ?>
<div id="flow">
    <?php echo $fullHTML; ?>
</div>
<?php if (isset($hyphen) && $hyphen): ?>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="<?php echo base_url();?>public/js/hyphenator/Hyphenator.js" type="text/javascript"></script>
    <script type="text/javascript">
        var hyphenatorSettings = {
            selectorfunction: function () {

                return $('p').filter(function(){
                    return $(this).css('text-align')=='justify';
                }).get();
//            return $('[style="text-align: justify;"]').get();
            }
        };
        Hyphenator.config(hyphenatorSettings);
        Hyphenator.run();
    </script>
<?php endif;?>
</body>
</html>