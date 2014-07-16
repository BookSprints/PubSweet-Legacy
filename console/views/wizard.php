<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>EPUB Management Console</title>
    <link href="<?php echo base_url();?>public/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONTS -->
    <style type="text/css">
        @font-face {
            font-family:"cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Regular.otf) format("opentype");
        }
        @font-face {
            font-family:"cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Bold.otf) format("opentype");
            font-weight: bold;
        }
        @font-face {
            font-family:"cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Italic.otf) format("opentype");
            font-style: italic;
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Regular.otf) format('opentype');
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Bold.otf) format('opentype');
            font-weight: bold;
        }
        @font-face {
            font-family:"libre-baskerville";
            src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Italic.otf) format('opentype');
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
            <a class="brand" href="<?php echo base_url(); ?>">PubSweet</a>
            <ul class="nav">
                <?php if (isset($book)): ?>
                    <li>
                        <a href="<?php echo base_url() . 'book/tocmanager/' . $book['id'] ?>"><?php echo $book['title']; ?></a>
                    </li>
                <?php endif; ?>
                <li>
                <li><a href="<?php echo base_url() . 'taskmanager/' . $book['id'] . '/'; ?>" target="_blank">Task Manager</a></li>
                <li class="active"><a href="<?php echo base_url() . 'console/' . $book['id'] . '/' ?>">Console</a>
                </li>

            </ul>
            <ul class="nav pull-right">
                <li>
                    <a id="logout"
                       href="<?php echo base_url(); ?>auth/logout">
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
            <!--<fieldset>
                <legend>Please enter the Book Name</legend>
                <div class="control-group"><label class="control-label" for="bookname">Book</label>

                    <div class="controls">
                        <input type="text" name="bookname" id="bookname" required="required">
                    </div>
                </div>-->
            <fieldset id="metadata" class="bookable">
                <legend>Metadata</legend>
                    <input type="hidden" name="book_id" value="<?php echo $book['id'];?>" id="book_id"/>
                    <input type="hidden" name="bookname"
                           value="<?php echo str_replace(' ', '_', strtolower($book['title']));?>" id="bookname"/>
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
                </fieldset>
<!--            </fieldset>-->
            <fieldset>
                <div class="span6">
                <legend>Custom CSS</legend>
                <!--<div class="control-group">
                    <div class="controls">
                    <label class="checkbox" for="prettify-epub">
                        <input type="checkbox" name="prettify-epub" id="prettify-epub"/> Inject Google Prettify into Epub</label>
                    </div>
                </div>-->
                <div class="control-group"><label class="control-label" for="css">Custom CSS</label>

                    <div class="controls"><textarea name="css" id="css" rows="15"></textarea></div>
                </div>
                </div>
            <!--</fieldset>
            <fieldset>-->
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
                <div class="control-group">
                    <div class="controls">
                        <label class="checkbox" for="download">
                            <input type="checkbox" name="download" id="download">Create EPUB</label>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <label class="checkbox" for="create-bookjs">
                        <input type="checkbox" name="create-bookjs" id="create-bookjs"
                           data-toggle="collapse" data-target="#bookjs-option">Create Book PDF</label>
                        <div id="bookjs-option" class="collapse">
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
                            <div class="control-group ">
                                <div class="controls">
                                    <label class="checkbox" for="hyphen"><input type="checkbox" name="hyphen" id="hyphen"/>Hyphenate</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <!--<fieldset>
                <legend>Make changes</legend>
                <div class="control-group">
                    <div class="controls">
                        <label class="checkbox" for="fix">
                            <input type="checkbox" name="fix" id="fix">Fix internal links</label>
                        <label class="checkbox" for="download">
                            <input type="checkbox" name="download" id="download">Download EPUB</label>
                        <label class="checkbox" for="create-bookjs">
                            <input type="checkbox" name="create-bookjs" id="create-bookjs"
                                   data-toggle="collapse" data-target="#bookjs-option">Create BookJS</label>
                        <div id="bookjs-option" class="collapse offset1">
                            <label class="checkbox" for="prettify"><input type="checkbox" name="prettify"
                                                                          id="prettify"/>Inject Google Prettify</label>
                            <label class="checkbox" for="editablecss">
                                <input type="checkbox" name="editable" id="editablecss">Make editable CSS</label>
                            <label class="checkbox" for="hyphen"><input type="checkbox" name="hyphen" id="hyphen"/>Hyphenate</label>
                            <fieldset id="bookjs-config">
                                <legend>BookJS Config</legend>
                                <div class="control-group"><label class="control-label" for="page-height">Page Height</label>

                                    <div class="controls"><input type="text" value="9.68" id="page-height" name="pageHeight"></div>
                                </div>
                                <div class="control-group"><label class="control-label" for="page-width">Page Width</label>

                                    <div class="controls"><input type="text" value="7.44" id="page-width" name="pageWidth"></div>
                                </div>
                                <div class="control-group"><label class="control-label" for="length-unit">Length Unit</label>

                                    <div class="controls"><input type="text" value="in" id="length-unit" name="lengthUnit"></div>
                                </div>
                            </fieldset>
                        </div>

                    </div>
                </div>
            </fieldset>-->

            <!--<div class="form-actions"><input class="btn" type="submit" value="Submit"><input class="btn" type="reset"
                                                                                       value="Reset"></div>-->
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
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-2.0.2.min.js"></script>
<script src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/handlebars.js"></script>
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
            self.activate(this.find('fieldset:first-child'));
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
            baseUrl: 'http://pubsweet.local/',
//            baseUrl: 'http://pubsweet-new.booksprints.net/',
            init:function () {
                driver.handleCoverPreview();
                $('#management-form').bootzard({'done':function () {
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
                    /*{element:$('#fetching'), method:management.downloadEpub, enabled:true},
                    {element:$('#fixing'), method:management.fixLinks, enabled:$('#fix').is(':checked')},
                    {element:$('#cssing'), method:management.uploadCSS, enabled:(!!$('#css').val())||$('#prettify-epub').is(':checked')},

                    {element:$('#fixinImages'), method:management.fixImages, enabled:true}*/
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
                    document.location = driver.baseUrl+'render/epub/'+$('#book_id').val();
                }

            },
            createPreviewURL: function($link){
                if($('#create-bookjs').is(':checked')){
                    $link.attr('href', this.baseUrl+
                            'console/console/preview/'+driver.bookname+'/'+
                                    +($('#editablecss').is(':checked')?true:false)+'/'+
                                    +($('#hyphen').is(':checked')?true:false)+'/'+
                                    +($('#prettify').is(':checked')?true:false)
                                    +'/?a=1'//only a placeholder
                                    +($('#pageWidth').val()!=7.44?'&w='+$('#pageWidth').val():'')
                                    +($('#pageHeight').val()!=9.68?'&h='+$('#pageHeight').val():'')
                                    +($('#lengthUnit').val()!='in'?'&u='+$('#lengthUnit').val():'')
                                    +($('#language').val()!='en-US'?'&l='+$('#language').val():''))
                            .removeClass('hide');

                }
            },
            createLiveCSSURL: function($link){
                if($('#create-bookjs').is(':checked')){
                    $link.attr('href', this.baseUrl+
                            'console/console/livecss/'+driver.bookname+'/'+
                                    +($('#editablecss').is(':checked')?true:false)+'/'+
                                    +($('#hyphen').is(':checked')?true:false)+'/'+
                                    +($('#prettify').is(':checked')?true:false))
                            .removeClass('hide');

                }
            },
            /*downloadEpub:function (callback) {
                $.get('manager/getFromObjavi/'+driver.bookname, function (data) {
                    if (data.ok) {
                        driver.url = data.link;
                        var bookHistory = JSON.parse(localStorage.getItem('bookHistory'));
                        if(Object.prototype.toString.call( bookHistory ) === '[object Array]'){
                            if(bookHistory.indexOf(driver.bookname)==-1){
                                bookHistory.push(driver.bookname);
                                localStorage.setItem('bookHistory', JSON.stringify(bookHistory));
                            }else{
                                //do nothing, it already exists
                            }
                        }else{
                            localStorage.setItem('bookHistory', JSON.stringify([driver.bookname]));
                        }
                    }
                    if (!!callback) {
                        callback(data.ok);
                    }
                }, 'json').error(function(){
                            callback(false);
                        });
            },*/
            generateXHTMLFiles: function(callback){
                $.post(driver.baseUrl+'render/epub/'+$('#book_id').val(),
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
                $.post(driver.baseUrl+'console/manager/addMetadata/',
                    $('#metadata').serialize()+'&book='+driver.bookname,
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
                $.post(driver.baseUrl+'console/manager/injectCSS/',
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
                    url: driver.baseUrl+"console/manager/injectCover/",
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