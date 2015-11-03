<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>Preview - <?php echo $bookTitle; ?></title>
    <base href="<?php echo base_url(); ?>">
    <!-- Required for the css regions polyfill -->
    <style id="flows">
        <?php echo $pageStyles;?>
        #flow {
            display: none;
        }

        .pagination-main-contents-container {
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: column;
            flex-direction: column;
        }

        .pagination-contents-container {
            position: absolute;
        }

        .pagination-contents {
            display: -webkit-flex;
            -webkit-flex: 1;
            display: flex;
            flex: 1;
        }

        /* There seems to be a bug in the new flexbox model code which requires the
         * height to be set to an arbitrary value (which is ignored).
         */
        .pagination-contents {
            height: 0;
        }

        .pagination-contents-column {
            -webkit-flex: 1;
            flex: 1;
        }

        body {
            counter-reset: pagination-footnote pagination-footnote-reference;
        }

        .pagination-footnote::before {
            counter-increment: pagination-footnote-reference;
            content: counter(pagination-footnote-reference);
        }

        .pagination-footnote > * > *:first-child::before {
            counter-increment: pagination-footnote;
            content: counter(pagination-footnote);
        }

        .pagination-footnote > * > * {
            display: block;
        }

        .pagination-page {

            margin-left: auto;
            margin-right: auto;
            page-break-after: always;
            position: relative;

        }

        img {
            -webkit-region-break-before: always;
            break-before: always;
            -webkit-region-break-after: always;
            break-after: always;
        }

        .pagination-pagenumber, .pagination-header {
            position: absolute;
        }

        .pagination-pagebreak {
            -webkit-region-break-after: always;
            break-after: always;
        }

        .pagination-simple {
            height: auto;
            position: relative;
        }

        .pagination-marginnote-item {
            position: absolute;
        }

        .pagination-marginnote > * {
            display: block;
        }
        <?php echo $chapterRegions;?>
    </style>
    <?php if (isset($prettify) && $prettify): ?>
        <link rel="stylesheet" href="public/js/prettifier/prettify.css"/>
    <?php endif; ?>
    <!--    <link rel="stylesheet" href="book.css">-->
    <?php if (!isset($editablecss) || !$editablecss):
        foreach ($css as $item) :
            if(!empty($item)):?>
            <style type="text/css"><?php echo $item; ?></style>
        <?php
            endif;
            endforeach;
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
    <?php if (isset($prettify) && $prettify): ?>
        <script type="text/javascript" src="public/js/prettifier/prettify.js"></script>
    <?php endif; ?>
    <script src="public/js/css-regions-polyfill.js"></script>

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
    <script type="text/javascript" src="public/js/book-polyfill.js"></script>

</head>
<body
    <?php if (isset($prettify) && $prettify): ?>
        onload="prettyPrint();"
    <?php endif; ?>
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