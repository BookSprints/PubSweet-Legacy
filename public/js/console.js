/**
 * Created by jgutix on 5/19/15.
 */
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
            });
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
                {element:$('#metadating'), method:driver.uploadMetadata,
                    enabled: $('#metadata').find('input').filter(function() { return !!this.value }).length>0},
                {element:$('#epubing'), method:driver.generateXHTMLFiles, enabled:true},
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
                document.location = 'render/epub/'+$('#book_id').val()+'/'+$('#settings-token').val();
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
                    $link.attr('href', 'console/preview/'+driver.bookname+'/'
                    + token + '/'
                    +($('#editablecss').is(':checked')?1:0)+'/'
                    +($('#hyphen').is(':checked')?1:0)+'/'
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
                $link.attr('href', 'console/livecss/'+driver.bookname+'/'
                +($('#editablecss').is(':checked')?true:false)+'/'
                +($('#hyphen').is(':checked')?true:false)+'/'
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