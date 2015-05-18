<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <base href="<?php echo base_url();?>">
    <title>EPUB Management Console</title>
    <link href="public/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONTS -->
    <style type="text/css">
        @font-face {
            font-family:"cabin";
            src: url(public/css/cabin/Cabin-Regular.otf) format("opentype");
        }
        @font-face {
            font-family:"cabin";
            src: url(public/css/cabin/Cabin-Bold.otf) format("opentype");
            font-weight: bold;
        }
        @font-face {
            font-family:"cabin";
            src: url(public/css/cabin/Cabin-Italic.otf) format("opentype");
            font-style: italic;
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(public/css/libre-baskerville/LibreBaskerville-Regular.otf) format('opentype');
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(public/css/libre-baskerville/LibreBaskerville-Bold.otf) format('opentype');
            font-weight: bold;
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(public/css/libre-baskerville/LibreBaskerville-Italic.otf) format('opentype');
            font-style: italic;
        }
    </style>
    <style type="text/css">
        @-webkit-keyframes ajax-loader-rotate {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }
        @-moz-keyframes ajax-loader-rotate {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        @keyframes ajax-loader-rotate {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        .ajax-loader {
          opacity: .8;
          display: block;
          border-radius: 50%;

          font-size: 29px;
          width: .25em;
          height: .25em;

          box-shadow:
            0 -.4em       0 0 rgba(0,0,0,1),
            -.28em -.28em 0 0 rgba(0,0,0,.75),
            -.4em 0       0 0 rgba(0,0,0,.50),
            -.28em .28em  0 0 rgba(0,0,0,.25)
          ;

          -webkit-animation: .85s ajax-loader-rotate steps(8) infinite;
          -moz-animation: .85s ajax-loader-rotate steps(8) infinite;
          animation: .85s ajax-loader-rotate steps(8) infinite;
        }

        body{
            font-family:"cabin";
        }

        .book-data, .bookable input, .bookable textarea, .bookable select{
            font-family: "libre-baskerville";
        }

        #preview{
            max-height: 350px;
            max-height: 350px;
        }
        .brand{
            font-family: "libre-baskerville";
        }
        .content-options li{
            list-style: none;
            margin-left: 20px;
        }

    </style>
    <style type="text/css">
        .bootzard > .active {
            display: block;
        }

        .bootzard > fieldset {
            display: none;
        }
    </style>
</head>
<body>
<?php
/*$id = $this->session->userdata('DX_user_id');*/
$module = $this->uri->segment(2);
if (!empty($book['id'])):  ?>
    <div class="container navbar navbar-static-top">
        <div class="navbar-inner">
            <a class="brand" href="">PubSweet</a>
            <ul class="nav">
                <?php if (isset($book)): ?>
                    <li>
                        <a href="<?php echo 'book/tocmanager/' . $book['id'] ?>"><?php echo $book['title']; ?></a>
                    </li>
                <?php endif; ?>
                <li>
                <li class="active"><a href="<?php echo 'console/' . $book['id'] . '/' ?>">Console</a>
                </li>

            </ul>
            <ul class="nav pull-right">
                <li>
                    <a id="logout"
                       href="auth/logout">
                        <!--<span><?php/* echo $this->session->userdata(
                                'DX_username'
                            );*/ ?></span>-->  Logout</a>
                </li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row-fluid">
        <h1>Publish Console</h1>

        <!--form:post#management-form.form-horizontal>(fieldset>legend+(div.control-group>label.control-label+div.controls>input:text))*3-->
        <form id="management-form" class="form-horizontal bootzard" action="#" method="post">
            <input type="hidden" name="settings-token" id="settings-token" value="<?php echo substr(md5(microtime()),rand(0,26),6);?>"/>

            <fieldset id="metadata" class="bookable">
                <div class="span6">
                    <legend>Metadata</legend>
                    <input type="hidden" name="book_id" value="<?php echo $book['id'];?>" id="book_id"/>
                    <input type="hidden" name="bookname"
                           value="<?php echo $bookName;?>" id="bookname"/>
                    <div class="control-group"><label class="control-label" for="book-title">Book title</label>

                        <div class="controls"><input type="text" name="title" id="book-title"
                                                     value="<?php echo $book['title'];?>"></div>
                    </div>
                    <div class="control-group"><label class="control-label" for="author">Author</label>

                        <div class="controls"><input type="text" name="author" id="author"></div>
                    </div>
                    <div class="control-group"><label class="control-label" for="publisher">Publisher</label>

                        <div class="controls"><input type="text" name="publisher" id="publisher" value="PUBSWEET"></div>
                    </div>
                    <div class="control-group"><label class="control-label" for="published-date">Published Date</label>

                        <div class="controls"><input type="text" name="date" id="published-date"></div>
                    </div>
                    <div class="control-group"><label class="control-label" for="license">License</label>

                        <div class="controls"><input type="text" name="rights" id="license" value="GPLv2+"></div>
                    </div>
                </div>
                <div class="span6">
                    <legend>Content</legend>
                    <div>
                        <label class="radio inline" for="full">
                            <input type="radio" name="content" checked="checked"
                                   id="full-content"/>Full</label>
                        <label
                            class="radio inline" for="customized"><input type="radio" name="content"
                                                                         id="customized"/>Customized</label>
                    </div>
                    <div class="content-detail hide">
                    <?php

                    foreach ($sections as $id=>$chapters) {
                        $options = sprintf('<ul class="content-options"><label class="checkbox" for="section%s">
                            <input type="checkbox" name="sections[]" id="section%s" value="%s" checked/>%s</label>%%s</ul>',
                            $chapters[0]['section_id'], $chapters[0]['section_id'], $chapters[0]['section_id'],
                            $chapters[0]['section_title']);
                        $chaptersInput = '';
                        foreach ($chapters as $chapter) {
                            $chaptersInput .= sprintf('<li><label for="chapter%s" class="checkbox">
                                <input type="checkbox" name="chapters[]" value="%s" id="chapter%s" checked>%s</label></li>',
                                $chapter['id'], $chapter['id'], $chapter['id'], $chapter['title']);
                        }
                        echo sprintf($options, $chaptersInput);
                    }
                    ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                </fieldset>
            <fieldset>
                <div class="span6">
                <legend>Custom CSS</legend>
                <div class="control-group"><label class="control-label" for="css">Custom CSS</label>

                    <div class="controls"><textarea name="css" id="css" rows="15"></textarea></div>
                </div>
                </div>
                <div class="span6">
                <legend>Cover</legend>
                <div class="control-group">
                    <div class="controls">
                        <input type="file" accept="image/x-png, image/jpeg" name="cover" id="cover"></div>
                </div>
                <div>
                    <div class="controls"><img alt="preview" id="preview" class="img-polaroid hide"></div>
                </div>
                </div>
                <div class="clearfix"></div>
            </fieldset>
            <fieldset>
                <div class="span6">
                    <legend>EPUB options</legend>
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox" for="download">
                                <input type="checkbox" name="download" id="download">Create EPUB</label>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <legend>PDF options</legend>
                        <div class="controls">
                            <label class="checkbox" for="create-bookjs">
                                <input type="checkbox" name="create-bookjs" id="create-bookjs"
                                       data-toggle="collapse" data-target="#bookjs-option">Create Book PDF</label>

                        </div>

                        <div id="bookjs-option" class="collapse">
                            <div class="control-group ">
                                <div class="controls">
                                    <label class="checkbox" for="hyphen"><input type="checkbox" name="hyphen" id="hyphen"/>Hyphenate</label>
                                </div>
                            </div>
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" class="btn btn-primary active" id="set-basic">Basic</button>
                                <button type="button" class="btn btn-primary" id="set-advanced">Advanced</button>
                            </div>
                            <div id="basic">

                                <div class="control-group"><label class="control-label" for="language">Language</label>

                                    <div class="controls"><input type="text" value="en-US" id="language" name="language"></div>
                                </div>
                                <div class="control-group"><label class="control-label" for="pageHeight">Page Height</label>

                                    <div class="controls"><input type="text" value="9.68" id="pageHeight" name="pageHeight"></div>
                                </div>
                                <div class="control-group"><label class="control-label" for="pageWidth">Page Width</label>

                                    <div class="controls"><input type="text" value="7.44" id="pageWidth" name="pageWidth"></div>
                                </div>
                                <div class="control-group"><label class="control-label" for="lengthUnit">Length Unit</label>

                                    <div class="controls"><input type="text" value="in" id="lengthUnit" name="lengthUnit"></div>
                                </div>
                            </div>
                            <div id="advanced" class="hide">
                            <div class="control-group">
                                <div class="">
                                    <label class="" for="epub-config">Bookjs config
                                        <a data-toggle="modal" href="#modal-help" class="pull-right"><i class="icon-info-sign"></i></a></label>
                                    <textarea name="bookjs-config" id="bookjs-config" cols="60" rows="15" class="span12"><?php echo $defaultConfig;?>
                                    </textarea>
                                </div>

                                <div class="modal fade hide" id="modal-help">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                <h4 class="modal-title">OPTIONS</h4>
                                            </div>
                                            <div class="modal-body">

                                                <p>The following options are available to customize the pagination behavior. In
                                                    the descriptions below you can see the default values for these options. You
                                                    only need to specify the options if you want to deviate from the default
                                                    value.</p>

                                                <p><code>sectionStartMarker: 'h1'</code> -- This is the HTML element we look for to find where
                                                    a new section starts.</p>

                                                <p><code>sectionTitleMarker: 'h1'</code> -- Within the newly found section, we look for the
                                                    first instance of this element to determine the title of the section.</p>

                                                <p><code>chapterStartMarker: 'h2'</code> -- This is the HTML element we look for to find where
                                                    a new chapter starts.</p>

                                                <p><code>chapterTitleMarker: 'h2'</code> -- Within the newly found chapter, we look for the
                                                    first instance of this element to determine the title of the chapter.</p>

                                                <p><code>flowElement: 'document.body'</code> -- This specifies element the container element
                                                    of the content we will flow into pages. You can use any javascript selector
                                                    here, such as "document.getElementById('contents')" .</p>

                                                <p><code>alwaysEven: false</code> -- This determines whether each section and chapter should
                                                    have an even number of pages (2, 4, 6, 8, ...).</p>

                                                <p><code>columns: 1</code> -- This specifies the number of number of columns used for the
                                                    body text.</p>

                                                <p><code>enableFrontmatter: true</code> -- This resolves whether a table of contents, page\
                                                    headers and other frontmatter contents should be added upon page creation.
                                                    Note: divideContents has to be true if one wants the frontmatter to render.</p>

                                                <p><code>enableTableOfFigures: false</code> -- This creates a table of figures in the front.
                                                    Figures are expected to be in the HTML5 format:</p>

                                                <code>&lt;figure&gt;...&lt;figcaption&gt;&lt;/figcaption&gt;&lt;/figure&gt;</code>

                                                <p>If an <code>&lt;img&gt;</code> element is present in the figure, its alt-attribute text will be
                                                    used as the reference text. If no <code>&lt;img&gt;</code> element is present, or one is
                                                    present, but it has no alt-attribute, the contents of the <code>&lt;figcaption&gt;</code>
                                                    element will be usd instead. If no <code>&lt;figcaption&gt;</code> element is present, a
                                                    description text is generated of the following format:</p>

                                                <p>"Figure chapter.number"</p>

                                                <p><code>enableTableOfTables: false</code> -- This creates a table of <code>&lt;table&gt;</code>s within
                                                    <code>&lt;figure&gt;</code>s, similarly to enableTableOfFigures. If this option is enabled,
                                                    tables will not additionally be listed in the table of figures. If no
                                                    <code>&lt;figcaption&gt;</code> element is present, the description text will be in the
                                                    following format:</p>

                                                <p>"Table chapter.number"</p>

                                                <p><code>bulkPagesToAdd: 50</code> -- This is the initial number of pages of each flowable
                                                    part (section, chapter). After this number is added, adjustments are made by
                                                    adding another bulk of pages or deleting pages individually. It takes much
                                                    less time to delete pages than to add them individually, so it is a point to
                                                    overshoot the target value. For larger chapters add many pages at a time so
                                                    there is less time spent reflowing text.</p>

                                                <p><code>pagesToAddIncrementRatio: 1.4</code> -- This is the ratio of how the bulk of pages
                                                    incremented. If the initial bulkPagestoAdd is 50 and those initial 50 pages
                                                    were not enough space to fit the contents of that chapter, then next
                                                    1.4 * 50 = 70 are pages, for a total of 50+70 = 120 pages, etc. .  1.4 seems
                                                    to be the fastest in most situations.</p>

                                                <p><code>frontmatterContents: none</code> -- These are the HTML contents that are added to
                                                    the frontmatter before the table of contents. This would usually be a title
                                                    page and a copyright page, including page breaks.</p>

                                                <p><code>autoStart: true</code> -- This controls whether pagination should be executed
                                                    automatically upon page load. If it is set to false, pagination has to be
                                                    initiated manually. See below under "methods."</p>

                                                <p><code>numberPages: true</code> -- This controls whether page numbers should be used. If
                                                    page numbers are not used, the table of contents is automatically left out.</p>

                                                <p><code>divideContents: true</code> -- This controls whether the contents are divided up
                                                    according to sections and chapters before flowing. CSS Regions take a long
                                                    time when more than 20-30 pages are involved, which is why it usually makes
                                                    sense to divide the contents up. However, if the contents to be flown takes
                                                    up less space than this, there is no need to do this division. The added
                                                    benefit of not doing it is that the original DOM of the part that contains
                                                    the contents will not be modified. Only the container element that holds the
                                                    contents will be assigned another CSS class. Note: divideContents has to be
                                                    true if one wants the frontmatter to render.</p>

                                                <p><code>maxPageNumber: 10000</code> -- This controls the maximum amount of pages. If more
                                                    pages than this are added, BookJS will die. Notice that pages are added
                                                    incrementally, so you won't be able to control the exact number of pages.
                                                    You should always set this to something much larger than what you will ever
                                                    expect that you book will need.</p>

                                                <p><code>topfloatSelector: '.pagination-topfloat'</code> -- This is the CSS selector used
                                                    for finding top floats within the HTML code. Top floats are placed on the
                                                    page either of the reference or the one following it. In editing
                                                    environments, the top float should be inserted inside two additional
                                                    elements, like this:</p>

                                                <code>&lt;span class='pagination-topfloat'&gt;&lt;span&gt;&lt;span&gt;This is the top float contents&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;</code>

                                                <p><code>footnoteSelector: '.pagination-footnote'</code> -- This is the CSS selector used
                                                    for finding footnotes within the HTML code. Footnotes are automatically
                                                    moved if the page of their reference changes. In editing environments, the
                                                    footnote should be inserted inside two additional elements, like this:</p>

                                                <code>&lt;span class='pagination-footnote'&gt;&lt;span&gt;&lt;span&gt;This is a footnote&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;</code>

                                                <h3>Page style options</h3>

                                                <p>These settings provide a way to do simple styling of the page. These
                                                    settings are different from the above ones in that they can be overriden
                                                    through CSS to provide more advanced designs (see the above note on
                                                    pagination.css).</p>

                                                <p><code>outerMargin: .5</code> (inch)-- This controls the margin on the outer part of the
                                                    page.</p>

                                                <p><code>innerMargin: .8</code> (inch)-- This controls the margin on the inenr part of the
                                                    page.</p>

                                                <p><code>contentsTopMargin: .8</code> (inch)-- This controls the margin between the top of
                                                    the page and the top of the contents.</p>

                                                <p><code>headerTopMargin: .3</code> (inch) -- This controls the margin between the top of
                                                    the page and the top of the page headers.</p>

                                                <p><code>contentsBottomMargin: .8</code> (inch) -- This controls the margin between the
                                                    bottom of the page and the bottom of the contents.</p>

                                                <p><code>pagenumberBottomMargin: .3</code> (inch) -- This controls the margin between the
                                                    bottom of the page and the bottom of the page number.</p>

                                                <p><code>pageHeight: 8.3</code> (inch) -- This controls the height of the page.</p>

                                                <p><code>pageWidth: 5.8</code> (inch) -- This controls the width of the page.</p>

                                                <p><code>columnSeparatorWidth: .09</code> (inch) -- This is the space between columns.</p>

                                                <p><code>lengthUnit: 'in'</code> (inch) -- Use this to specify the unit used in all the page
                                                    style options. It can be any unit supported by CSS.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </div>
                            </div>

                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="row hide" id="advance">
        <!--<div id="downloading">Getting download link</div>-->
        <div id="epubing"><h1>Generating XHTML files</h1></div>
        <div id="metadating"><h1>Setting metadata</h1></div>
        <div id="cssing"><h1>Uploading CSS</h1></div>
                <div id="covering"><h1>Uploading Cover</h1></div>
<!--        <div id="fetching"><h1>Fetching EPUB</h1></div>-->
<!--        <div id="fixing"><h1>Fixing links</h1></div>-->
<!--        <div id="fixingImages"><h1>Fixing Images</h1></div>-->
    </div>
    <div id="result" class="row hide">
        <a href="#" id="epub" class="btn btn-block btn-primary hide" target="_blank">Download</a>
        <div class="row-fluid">
        <a id="bookjs" href="#" class="btn span6 hide" target="_blank">Preview</a>
        <a id="livecss" href="#" class="btn span6 hide" target="_blank">Experimental Designer</a>
        </div>
</div>
<!--<div class="container">
    <div class="progress progress-striped active" id="book-downloading">
      <div class="bar" style="width: 40%;"></div>
    </div>
</div>-->
<script type="text/x-handlebars-template" id="orphan-links-template">
    <div class="modal hide fade" id="orphan-modal">
        <form id="orphan-form" class="form-horizontal modal-form" action="manager/fixOrphans/" method="post">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                These links should be fixed manually
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <th>Text</th>
                        <th>Original HREF</th>
                        <th>New HREF</th>
                    </tr>

                {{#each this.orphanLinks}}
                <tr>
                    <td>{{text}}</td>
                    <td>{{href}}</td>
                    <td>{{{select this}}}</td>
                </tr>
                {{/each}}
                    <input type="hidden" name="book" value="{{book}}">
                </table>
            </div>
            <div class="modal-footer"><input class="btn" type="submit" value="Fix">
                <input class="btn" type="reset" value="Reset"></div>
        </form>
    </div>
</script>
<script type="text/x-handlebars-template" id="select-template">
{{#each this}}
<option value="{{this}}">{{this}}</option>
{{/each}}
</script>
<script type="text/javascript" src="public/js/jquery-2.0.2.min.js"></script>
<script src="public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="public/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="public/js/handlebars.js"></script>
<!--http://www.smashingmagazine.com/2010/01/15/progress-trackers-in-web-design-examples-and-best-design-practices/-->
<script type="text/javascript">
    (function ($) {
        $.fn.bootzard = function (config) {
            var self = this;
            self.activate = function (selector, previous) {
                var footer = $('<ul></ul>', {class:'pager'});
                if (!selector.is('fieldset:last-of-type')) {
                    footer.append($('<li></li>')
                            .append($('<a></a>', {text:'Next', 'class':'next', 'href':'#'}))
                    );
                } else {
                    footer.append($('<li></li>')
                            .append($('<a></a>', {text:'Do it!', 'class':'done', 'href':'#'}))
                    );
                }
                if (!selector.is('fieldset:first-of-type')) {
                    footer.prepend($('<li></li>')
                            .append($('<a></a>', {text:'Previous', 'class':'prev', 'href':'#'}))
                    );
                }

                if (previous != undefined) {
                    previous.removeClass('active');
                    previous.find('.pager').remove();
                    previous.find('.control-group.error').removeClass('error');
                }
                selector.addClass('active').append(footer);
            };
            self.activate(this.find('fieldset').first());
            self.addClass('bootzard');
            this.on('click', '.next',function () {
                var fieldSet = $(this).parents('fieldset'), result = true;
                fieldSet.find('input, textarea, select').each(function (index, item) {
                    if (!item.checkValidity()) {
                        $(item).parents('.control-group').addClass('error');
                        result = false;
                    }
                });
                if (result) {
                    self.activate(fieldSet.next('fieldset'), fieldSet);
                }
                return false;
            }).on('click', '.prev',function () {
                        var fieldSet = $(this).parents('fieldset');
                        self.activate(fieldSet.prev('fieldset'), fieldSet);
                        return false;
                    }).on('click', '.done', config.done)
        };
    })(window.jQuery);
</script>
<script type="text/javascript">
    (function ($) {
        var driver = {
            init:function () {
                driver.handleCoverPreview();
                $('#management-form').bootzard({'done': function () {
                    driver.process();
                    return false;
                }});
                localStorage.setItem('bookHistory', localStorage.getItem('bookHistory')||JSON.stringify([]));
                var $bookname =$('#bookname');
                $bookname.typeahead({'source': function(){
                    return JSON.parse(localStorage.getItem('bookHistory'));
                }, 'minLength': 3});
                if($('#load').is(':checked')){
                    $bookname.on('blur', function(){
                        $.get('getFileInfo')
                    });
                }
                var $advanced = $('#advanced'),
                    $basic = $('#basic'),
                    $bookjsconfig = $advanced.find('textarea');
                $('#set-basic').on('click', function(){
                    $basic.show();
                    $advanced.hide();
                    $bookjsconfig.attr('disabled', 'disabled');
                });
                $('#set-advanced').on('click', function(){
                    $advanced.show();
                    $basic.hide();
                    $bookjsconfig.removeAttr('disabled');

                });
                $('#customized').on('change', function(){
                    $('.content-detail').show();
                });
                $('#full-content').on('change', function(){
                    $('.content-detail').hide();
                })
                $('[name="sections[]"]').on('change', function(){
                    var $this = $(this),
                       $chapters = $this.parents('.content-options').find('[name="chapters[]"]');
                    if($this.is(':checked')){
                        $chapters.prop('checked','checked');
                    }else{
                        $chapters.removeAttr('checked');
                    }
                });
            },
            encodeImg:function (img) {
                var canvas = document.createElement("canvas");
                var MAX_WIDTH = 350;
                //var MAX_HEIGHT = 800;
                var width = img.width;
                var height = img.height;

                //if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
                /*} else {
                 if (height > MAX_HEIGHT) {
                 width *= MAX_HEIGHT / height;
                 height = MAX_HEIGHT;
                 }
                 }*/
                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);
                // Copy the image contents to the canvas
                /*var ctx = canvas.getContext("2d");
                 ctx.drawImage(img, 0, 0, );*/

                // Get the data-URL formatted image
                // Firefox supports PNG and JPEG. You could check img.src to
                // guess the original format, but be aware the using "image/jpg"
                // will re-encode the image.
                return canvas.toDataURL("image/jpeg");
            },
            handleCoverPreview:function () {
                $('#cover').on('change', function () {
                    var input = this;
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            $('#preview').attr('src', e.target.result).show();
                        };
                        reader.readAsDataURL(input.files[0]);
                        $(input).show();
                    }
                });
            },
            process:function () {
                var steps;
                /*
                 steps = [
                    element: html element, holding the text info about the procedure,
                    method: function to be executed,
                    enabled: true if the step is going to be executed |false or custom sentence returning a boolean
                 ]*/

                steps = [
//                    {element: $('#downloading'), method: management.getDownloadURL},
                    {element:$('#epubing'), method:driver.generateXHTMLFiles, enabled:true},
                    {element:$('#metadating'), method:driver.uploadMetadata,
                        enabled: $('#metadata').find('input').filter(function() { return !!this.value }).length>0},
                    {element:$('#cssing'), method:driver.uploadCSS, enabled:true},
                    {element:$('#covering'), method:driver.uploadCover, enabled:!!$('#cover').val()}

                ];
                $('#advance').show();
                driver.bookname = $('#bookname').val();
                driver.work(steps, 0);
            },
            /**
             * recursive function
             *
             * @param steps
             * @param index
             */
            work:function (steps, index) {
                var item = steps[index];
                if(!item.enabled){
                    item.element.addClass('muted').find('h1').append('<small></small>', {'class':'pull-right', text: '...skipped'});
                    if(steps[index+1]!=undefined){
                        driver.work(steps, index + 1);
                        return;
                    }else{
                        driver.end();
                        return;
                    }

                }
                item.element.addClass('alert alert-info').prepend($('<div></div>', { 'class':"ajax-loader pull-right"}));
                item.method(function (result) {
                    if(result){
                        item.element.addClass('alert-success').find('h1').append($('<span></span>', {html: '&#x2714;', 'class':'pull-right'}));//append check mark
                    }else{
                        item.element.addClass('alert-error');
                    }
                    item.element.removeClass('alert-info');
                    item.element.find('.ajax-loader').remove();
                    if(steps.length==index+1){
                        driver.end();
                    }
                    if(result && steps[index+1]!=undefined){
                        driver.work(steps, index + 1)
                    }
                });
            },
            end: function(){
                var $result = $('#result');
                driver.createPreviewURL($result.find('#bookjs'));
                driver.createLiveCSSURL($result.find('#livecss'));
                $result.find('#epub').attr('href', driver.url).removeClass('hide');
                $result.show();
                if($('#download').is(':checked')){
                    //because the xhtml files is always rendered, the generation is unuseful
                    document.location = 'render/epub/'+$('#book_id').val();
                }

            },
            /**
             * Handle the options related to the creation of the pdf preview
             * @param $link
             */
            createPreviewURL: function($link){
                if($('#create-bookjs').is(':checked')){
                    var token = $('#settings-token').val();
                    $.post('console/saveSettings', {'bookjs-config': $('#bookjs-config').val(), 'settings-token': token}, function(resp){
                        $link.attr('href', 'console/preview/'+driver.bookname+'/'+
                        + token + '/'
                        +($('#editablecss').is(':checked')?1:0)+'/'+
                        +($('#hyphen').is(':checked')?1:0)+'/'+
                        +($('#prettify').is(':checked')?1:0)
                        +'/?a=1'//only a placeholder
                        +($('#pageWidth').val()!=7.44?'&w='+$('#pageWidth').val():'')
                        +($('#pageHeight').val()!=9.68?'&h='+$('#pageHeight').val():'')
                        +($('#lengthUnit').val()!='in'?'&u='+$('#lengthUnit').val():'')
                        +($('#language').val()!='en-US'?'&l='+$('#language').val():''))
                            .removeClass('hide');
                    });

                }
            },
            createLiveCSSURL: function($link){
                if($('#create-bookjs').is(':checked')){
                    $link.attr('href', 'console/livecss/'+driver.bookname+'/'+
                                    +($('#editablecss').is(':checked')?true:false)+'/'+
                                    +($('#hyphen').is(':checked')?true:false)+'/'+
                                    +($('#prettify').is(':checked')?true:false))
                            .removeClass('hide');

                }
            },
            generateXHTMLFiles: function(callback){
                $.post('render/epub/'+$('#book_id').val()+'/'+$('#settings-token').val(),
                    {download: false},
                    function(data){
                        if(data.ok){
                            if (!!callback) {
                                callback(data.ok);
                            }
                        }
                    }, 'json').error(function(){
                        callback(false);
                    });
            },
            uploadMetadata:function (callback) {
                var content = '';
                if($('#customized').is(':checked')){
                    content = $('.content-detail').serialize();
                }
                $.post('console/manager/addMetadata/',
                    $('#metadata').serialize() + content +
                    '&book='+driver.bookname+
                    '&token='+$('#settings-token').val(),
                    function(data){
                        if(data.ok){
                            if (!!callback) {
                                callback(data.ok);
                            }
                        }
                    }, 'json').error(function(){
                            callback(false);
                        });
            },
            fixLinks:function (callback) {
                $.getJSON('manager/fixLinks/'+driver.bookname, function(data){
                    if(!!data.orphanLinks){
                        driver.fixManuallyLinks(data, callback);
                    }else{
                        if (!!callback) {
                            callback(data.ok);
                        }
                    }

                }).error(function(){
                        callback(false);
                    });
            },
            fixManuallyLinks:function (data, callback) {
                Handlebars.registerHelper('select', function(item){

                    return '<select name="'+item.file.replace('.', '&')+'['+item.class+']">' +
                            driver.xhtmlFiles+
                            '</select>'
                });
                driver.modalTemplate = Handlebars.compile($('#orphan-links-template').html());
                driver.optionsTemplate = Handlebars.compile($('#select-template').html());
//                management.silence=true;
                //TODO: improve this
                $('#orphan-modal').remove();
                driver.xhtmlFiles = driver.optionsTemplate(data.xhtmlFiles);
                $('body').append(driver.modalTemplate(data));//.modal('show');
                $('#orphan-modal').modal('show');
                $('#orphan-modal').on('hidden', function(){
                    driver.silence=false;
                });
                $('body').on('submit', '#orphan-form', function(){
                    var $this =$(this);
                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            driver.silence=false;
                            $('#orphan-modal').modal('hide');
                            if (!!callback) {
                                callback(data.ok);
                            }
                        }
                    }, 'json');
                    return false;
                });
            },
            uploadCSS:function (callback) {
                $.post('console/manager/injectCSS/',
                {css:$('#css').val(), 'prettify-epub': $('#prettify-epub').is(':checked'),
                    book:$('#book_id').val()}, function (data) {
                    if (!!callback) {
                        callback(data.ok);
                    }
                }, 'json');
            },
            uploadCover:function (callback) {
                var $cover = $('#cover');
                var fd = new FormData();
                fd.append('book', $('#book_id').val());
                fd.append("cover", $cover.get(0).files!=undefined?$cover.get(0).files[0]:null);

                $.ajax({
                    cache:false,
                    contentType:false,
                    processData:false,
                    type:"POST",
                    url: "console/manager/injectCover/",
                    dataType:'json',
                    data:fd,
                    success:function (data, textStatus, jqXHR) {
                        if (data.ok) {
                            if (!!callback) {
                                callback(data.ok);
                            }
                        }
                    },
                    error:function () {
                        console.log('We got a problem');
                    },
                    statusCode:{
                        413:function () {
                            alert("Image too big");
                        }
                    }

                });

                return false;
            },
            fixImages: function(callback){
                $.getJSON('manager/fixImages/'+driver.bookname, function(data){

                    if (!!callback) {
                        callback(data.ok);
                    }


                }).error(function(){
                        callback(false);
                    });
            }
        };
        driver.init();
    })(window.jQuery);
</script>
</body>
</html>