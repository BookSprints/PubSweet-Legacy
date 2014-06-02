<!DOCTYPE HTML>
<html lang="en-US">
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
            'alwaysEven': true,
            'enableFrontmatter': true,
            'bulkPagesToAdd': 50,
            'pagesToAddIncrementRatio': 1.4,
            'pageHeight': 11,
            'pageWidth': 8.5,
            'lengthUnit: ': 'in',
            'oddAndEvenMargins': false,
            'frontmatterContents': '<h1><?php echo $bookTitle;?></h1>'
                + '<div class="pagination-pagebreak"></div>',
            'autoStart': true,
            'polyfill': true

        };
        <?php if(isset($_GET['pageHeight']) && !empty($_GET['pageHeight'])):?>
        paginationConfig.pageHeight = <?php echo $_GET['pageHeight'];?>;
        <?php endif;?>
        <?php if(isset($_GET['pageWidth']) && !empty($_GET['pageHeight'])):?>
        paginationConfig.pageWidth = <?php echo $_GET['pageWidth'];?>;
        <?php endif;?>
        <?php if(isset($_GET['lengthUnit']) && !empty($_GET['pageHeight'])):?>
        paginationConfig.lengthUnit = '<?php echo $_GET['lengthUnit'];?>';
        <?php endif;?>
    </script>

    <script type="text/javascript" src="<?php echo base_url();?>public/js/book.js"></script>
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

</body>
</html>