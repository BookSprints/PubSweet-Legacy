/**
 * Created by jgutix on 4/1/14.
 */

/*global window */
/*global io, console, driver */
(function ($, H) {
    "use strict";

    window.broadcast = {
        server: use.nodejs+'pubsweet',
        socket: null,
        custom: [],
        init: function () {
            var self = this;
            if (!(window.io === undefined)) {
                self.socket = io.connect(self.server);
                self.socket.on('connect', function () {
                    var i;
                    self.socket.on('users-editing-book', window.driver.usersConnected);
                    self.socket.on('new-user-editing-book', window.driver.newUserConnected);
                    self.socket.on('remove-user-editing', window.driver.removeUserConnected);

                    self.socket.on('new-section', window.driver.book.drawSection);
                    self.socket.on('move-section', window.driver.book.moveSection);
                    self.socket.on('delete-section', window.driver.book.deleteSection);
                    self.socket.on('new-chapter', window.driver.book.drawChapter);
                    self.socket.on('move-chapter', window.driver.book.moveChapter);
                    self.socket.on('delete-chapter', window.driver.book.deleteChapter);
                    self.socket.on('add-chapter-status', window.driver.book.addStatus);
                    self.socket.on('delete-chapter-status', window.driver.book.deleteChapterStatus);
                    self.socket.on('update-status-chapter', window.driver.book.updateStatus);
                    self.socket.on('updateTitleChapter', window.driver.book.updateTitleChapter);
                    self.socket.on('updateTitleSection', window.driver.book.updateTitleSection);

                    self.socket.on('new-term', window.driver.dictionary.newTerm);
                    self.socket.on('editing-terms', window.driver.dictionary.editingTerms);
                    self.socket.on('new-term-editing', window.driver.dictionary.newTermEditing);
                    self.socket.on('remove-term-editing', window.driver.dictionary.removeTermEditing);//aqui
                    self.socket.on('updating-term', window.driver.dictionary.updatingTerm);
                    self.socket.on('delete-term', window.driver.dictionary.deleteTerm);

                    self.socket.on('add-like', window.driver.discussion.addLike);
                    self.socket.on('remove-like', window.driver.discussion.removeLike);
                    self.socket.on('new-message', window.driver.discussion.drawMessage);

                    self.socket.on('new-review', window.driver.chapter.drawReview);
                    self.socket.on('plus-approve', window.driver.chapter.plusApprove);

                    //execute custom functions
                    for (i = 0; i < self.custom.length; i++) {
                        self.custom[i](self);
                    }
                });
            }

        },
        emit: function (event, data) {
            if (this.socket !== undefined && this.socket !== null) {
                this.socket.emit(event, data);
            } else {
                console.warn(event + ' not sent. Broadcast connection is not stablished');
            }
        },
        customOnConnect: function (callback) {
            this.custom.push(callback);
        }

    };

    window.driver = {
        indexFile: 'index.php',
        defaultURL: 'dashboard/profile',
        async: [],//array of functions to be executed asynchronously
        addAsync: function (index, func) {
            if (driver.async[index] === undefined) {
                driver.async[index] = [];
            }
            driver.async[index].push(func);
        },
        execAsync: function (index) {
            if (!!driver.async[index]) {
                $.each(driver.async[index], function (index, func) {
                    func();
                });
            }

        },
        init: function () {
            this.info = $('#info');
            this.infoTemplate = H.compile($("#info-template").html());

            var fullURL = window.location.href.replace($('base').attr('href'), ''),
                url = fullURL.length > 0 ? fullURL : this.defaultURL;
            this.route(url.split('/'));
            this.UrlPosition = url;
            broadcast.customOnConnect(function () {
                $.getJSON('user/getUsersInfo', null, function (response) {
//                        var book_id = driver.parameters[0];//getting first parameter
                    var data = {
                        id: response.id,
                        userName: response.username,
                        imgProfile: response.picture !== null ? response.picture:'http://placehold.it/140x140',
                        url: url
                    };
                    broadcast.emit('editing-books', data);
                });
            });
        },
        /**
         * identify URI segments and execute the appropriate method
         */
        route: function (segments) {
            //var website = segmentos[2];
            var controllerIndex = segments[3] === this.indexFile ? 1 : 0,
                controller = segments[controllerIndex],
                action = segments[controllerIndex + 1] === undefined || segments[controllerIndex + 1] === '' ? 'index' : segments[controllerIndex + 1];
            if (controller === undefined || controller.split('.').length > 1 || this[controller] === undefined) {
                return;
            }

            this.initParams(segments, controllerIndex + 2);

            this.common();
            //a method exists
            if (this[controller].init !== undefined) {
                this[controller].init();//method for execute common options per controller
            }
            if (this[controller][action] !== undefined) {
                this[controller][action]();
            }
        },
        /**
         * Parameters will be all segments beyond the controller/action segments. These will be accessible through driver.parameters
         * @param segments
         * @param newIndex
         */
        initParams: function (segments, newIndex) {
            this.parameters = [];
            if (segments[newIndex] !== undefined) {
                var i, stop = segments.length;
                for (i = newIndex; i < stop; i++) {
                    this.parameters.push(segments[i]);
                }

            }
        },
        /**
         * Common functionality executed in everypage
         */
        common: function () {
            $('.modal').on('shown', function () {
                $(this).find('[autofocus]').focus();
            });
        },
        usersConnected: function (data) {
            var cont = 0, item;
            for (item in data) {
                if (driver.UrlPosition === data[item].url && sessionStorage.user_id !== data[item].id){
                    if(cont === 0){
                        $('.usersConnected').empty();
                    }

                    $('.usersConnected').append(driver.book.userConnected(data[item]));
                    cont++;
                }
            }
            if(cont > 0){
                $('.logged').text(cont);
            }

        },
        newUserConnected: function (data) {
            if (driver.UrlPosition === data.url && sessionStorage.user_id !== data.id){
                var cont = $('.logged').text();
                if(isNaN(cont) || cont === ""){
                    $('.logged').text(1);
                    $('.usersConnected').empty().html(driver.book.userConnected(data));
                }
                else{
                    $('.logged').text(parseInt(cont)+1);
                    $('.usersConnected').append(driver.book.userConnected(data));
                }
            }
        },
        removeUserConnected: function (data) {
            if(data !== null){
                if (driver.UrlPosition === data.url){
                    var userDisconnect = $('.usersConnected').children();
                    if (userDisconnect.size() > 0) {
                        userDisconnect.find('.userConnected[data-id="' + data.id + '"]').remove();
                        var cont = $('.logged').text();
                        cont = cont === "" ? 0:cont;
                        if (parseInt(cont)-1 <= 0 || isNaN(cont)){
                            $('.usersConnected').html("<li> no one else online now</li>");
                            $('.logged').empty();
                        }
                        else
                            $('.logged').text(parseInt(cont)-1);
                    }
                }
            }
        },
        validateForm: function (idForm) {
            $(idForm).validationEngine({
                scroll: false,
                autoHidePrompt: 'true',
                autoHideDelay: '2000'
            });
        },
        admin: {
            editors: function () {
                var info = H.compile($("#info-template").html());
                $('.editor_type').on('change', function () {
                    var status = $(this).is(':checked');
                    var data = "id=" + $(this).attr('data-id') + "&" +
                        "checked=" + status;
                    $.post('admin/editor_status', data)
                        .fail(function () {
                            $('.result').html(info({type: 'error', text: '<span><strong>Sorry</strong> we had a problem</span>'}));
                        })
                        .done(function () {
                            $('.result').html(info({type: 'success', text: 'Successfully updated'}))
                            $(".alert").fadeOut(5000);
                        });
                });
            },
            users: function(){
                $('body').on('click', '.delete-user',function(){
                    var $this= $(this);
                    if(confirm('Are you sure?')){
                        $.post('admin/userDelete', {user_id: $this.data('id')}, function(){
                            $this.parents('#user').addClass('banned');
                            $this.text('Unban');
                            $this.removeClass('delete-user');
                            $this.addClass('enable-user');
                        },'json');
                    }
                    return false;
                });
                $('body').on('click', '.enable-user',function(){
                    var $this= $(this);
                    if(confirm('Are you sure?')){
                        $.post('admin/user_enabled', {user_id: $this.data('id')}, function(){
                            $this.parents('#user').removeClass('banned');
                            $this.text('Ban');
                            $this.removeClass('enable-user');
                            $this.addClass('delete-user');
                        },'json');
                    }
                    return false;
                });
                $('body').on('click','.resetpass',function(){
                    var $this = $(this);
                    $('.modal #reset-user').find('#user_id').val($this.data('id'));
                    $('#reset-password-modal').modal('show');
                    return false;
                });
                var $userUpdate = $('#reset-user'),
                    $userId = $userUpdate.find("#user_id");
                $userUpdate.on('submit',function(){
                    var $this = $(this),
                        password = $this.find("#newpass").val(),
                        confirm =  $this.find('#confirm_newpassword').val();
                    if(password==confirm){
                        $this.find(":submit").button('loading');
                        $.post($this.attr('action'), $this.serialize(), function (data){
                            if(data.ok){
                                $('.modal').modal('hide');
                                $this.get(0).reset();
                                $this.find(":submit").button('reset');
                            }
                        },'json');
                    }else{
                        $('.modal .info').text("Password doesn't match").addClass('text-error').show()
                    }
                    return false;
                })



            },
            stats: function(){
                var margin = {top: 20, right: 20, bottom: 50, left: 50},
                    width = 900 - margin.left - margin.right,
                    height = 300 - margin.top - margin.bottom;

                var parseDate = d3.time.format("%m-%d-%Y").parse;

                var x = d3.time.scale()
                    .range([0, width]);

                var y = d3.scale.linear()
                    .range([height, 0]);

                var xAxis = d3.svg.axis()
                    .scale(x)
                    .orient("bottom");

                var yAxis = d3.svg.axis()
                    .scale(y)
                    .orient("left");

                var line = d3.svg.line()
                    .x(function(d) { return x(d.date); })
                    .y(function(d) { return y(+d.count); });

                var svg = d3.select("#graph").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                d3.json("admin/login_stats/"+$('#graph').data('last-days'), function(error, data) {
                    data.forEach(function(d) {
                        d.date = parseDate(d.date);
                        d.count = +d.count;
                    });

                    x.domain(d3.extent(data, function(d) { return d.date; }));
                    y.domain(d3.extent(data, function(d) { return d.count; }));

                    svg.append("g")
                        .attr("class", "x axis")
                        .attr("transform", "translate(0," + height + ")")
                        .call(xAxis);

                    svg.append("g")
                        .attr("class", "y axis")
                        .call(yAxis)
                        .append("text")
                        .attr("transform", "rotate(-90)")
                        .attr("y", 6)
                        .attr("dy", ".71em")
                        .style("text-anchor", "end")
                        .text("Logins");

                    svg.append("path")
                        .datum(data)
                        .attr("class", "line")
                        .attr("d", line);
                });
            },
            books: function () {
                var $modalBookOwner = $('#modal-book-owner'),
                    $formOwners = $modalBookOwner.find('#form-book-owner'),
                    $owners = $formOwners.find('#owners'),
                    $bookInput = $formOwners.find('#book-input'),
                    $errorHandler = $modalBookOwner.find('.help-inline.error'),
                    $currentOwner = null,
                    $modalBookname = $('#modal-book-name'),
                    $formBookname = $modalBookname.find('#form-book-name'),
                    $bookNameInput = $formBookname.find('#book-name-id'),
                    $errorHandlerBookname = $modalBookname.find('.help-inline.error'),
                    $currentBookname = null;

                $('.update-owner').on('click', function () {
                    $currentOwner = $(this);
                    $errorHandler.hide();
                    $owners.val($currentOwner.data('value'));
                    $bookInput.val($currentOwner.parents('tr').data('book-id'));
                    $modalBookOwner.modal('show');
                    return false;
                });
                $('.change-book-name').on('click', function () {
                    $currentBookname = $(this);
                    $errorHandlerBookname.hide();
                    $bookNameInput.val($currentBookname.parents('tr').data('book-id'));
                    $('[name=bookname]').val($currentBookname.siblings('.current-bookname').text());
                    $modalBookname.modal('show');
                    return false;

                });
                $formOwners.on('submit', function(){
                    var $this = $(this),
                        newOwnerId = $owners.val(),
                        $submit = $this.find(":submit");
                    $submit.button('loading');
                    $.post($formOwners.attr('action'), $formOwners.serialize(), function(resp){
                        if(resp.ok){
                            $currentOwner.data('value', newOwnerId)
                                .siblings('.owner-name').text($('option[value='+newOwnerId+']').text());
                            $submit.button('reset');
                            $modalBookOwner.modal('hide');
                        }else{
                            $submit.button('reset');
                            $errorHandler.text('Unexpected error').show();
                        }

                    }, 'json').error(function(resp){
                        $submit.button('reset');
                        $errorHandler.text('Unexpected error').show();
                    });
                   return false;
                });

                $formBookname.on('submit', function(){
                    var $submit = $formBookname.find(':submit');
                    $submit.button('loading');
                    $.post($formBookname.attr('action'), $formBookname.serialize(), function(resp){
                        if(resp.ok){
                            $currentBookname.siblings('.current-bookname').text($('[name=bookname]').val());
                            $submit.button('reset');
                            $modalBookname.modal('hide');
                        }else{
                            $submit.button('reset');
                            $errorHandler.text('Unexpected error').show();
                        }

                    }, 'json').error(function(resp){
                        $submit.button('reset');
                        $errorHandler.text('Unexpected error').show();
                    });
                   return false;
                });
            }
        },
        dashboard: {
            profile: function () {
                driver.validateForm('#create-book');
                $('#create-book').on('submit', function () {
                    var $this = $(this);
                    if($this.validationEngine('validate')){
                        $this.find(":submit").button('loading');
                        $.post($this.attr('action'), $this.serialize(), function (data) {
                            if (data.ok) {

                                window.location.href = 'book/tocmanager/' + data.id;
                            } else {
                                $this.find(":submit").button('reset');
                            }
                        }, 'json');
                    }

                    return false;
                });
                $(function () {
                    var text = null;
                    $('#name').dblclick(function () {
                        text = $(this).text();
                        $(this).empty().html('<input name="name" id="name" type="text" value="' + text + '">').find('input').focus();
                    }).keypress(function (e) {
                            if (e.keyCode === 13) {//press enter
                                text = $('input', this).val();
                                $(this).html(text);
                                var data = 'name=' + text;
                                $.post('register/profile_update', data);
                            }
                            if (e.keyCode === 27) {//press scape
                                $(this).html(text);
                            }
                        });
                });
                driver.dashboard.handleImage();
                $('#profile-img').on('click', function () {
                    $('#upload').trigger('click');
                });

                var $copyForm = $('#copy-form');
                $('.copy-link').on('click', function(){
                    $copyForm.get(0).reset();
                    $copyForm.attr('action', $(this).data('href'));
                    $('#copy-modal').modal('show');
                    return false;
                });
                $copyForm.on('submit', function () {
                    var $this = $(this);
                    $this.find(":submit").button('loading');
                    $.post($this.attr('action'), $this.serialize(), function(resp){
                        if (resp.ok) {
//                            window.location.href = driver.urlBase + 'book/tocmanager/' + resp.id;
                            window.location.href = window.location.href;
                        } else {
                            $this.find(":submit").button('reset');
                        }

                    },'json');
                    return false;
                });
            },
            previewImg: function (evt) {
                var file = evt.target.files[0]; // FileList object

                //enabled only for images
                if (!file.type.match('image.*')) {
                    return;
                }

                var reader = new FileReader();

                reader.onload = (function (theFile) {
                    return function (e) {
                        var image = new Image();
                        image.src = e.target.result;

                        var temp_canvas = document.createElement('canvas');
                        var temp_ctx = temp_canvas.getContext('2d');
                        temp_canvas.width = 200;
                        temp_canvas.height = 200;
                        temp_ctx.clearRect(0, 0, 200, 200); // clear canvas
                        temp_ctx.drawImage($("#profile-img").get(0), 0, 0, 200, 200);
                        $("#profile-img").attr({
                            'src': temp_canvas.toDataUr
                        });
                        driver.dashboard.uploadPicture(e.target.result);
                    };
                })(file);

                reader.readAsDataURL(file);
                return false;
            },
            handleImage: function () {
                // variables
                var canvas, ctx;
                var image;
                var image2;
                var iMouseX, iMouseY = 1;
                var theSelection;

                // define Selection constructor
                function Selection(x, y, w, h) {
                    this.x = x; // initial positions
                    this.y = y;
                    this.w = w; // and size
                    this.h = h;

                    this.px = x; // extra variables to dragging calculations
                    this.py = y;

                    this.csize = 6; // resize cubes size
                    this.csizeh = 10; // resize cubes size (on hover)

                    this.bHow = [false, false, false, false]; // hover statuses
                    this.iCSize = [this.csize, this.csize, this.csize, this.csize]; // resize cubes sizes
                    this.bDrag = [false, false, false, false]; // drag statuses
                    this.bDragAll = false; // drag whole selection
                }

                // define Selection draw method
                Selection.prototype.draw = function () {

                    ctx.strokeStyle = '#000';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(this.x, this.y, this.w, this.h);

                    // draw part of original image
                    if (this.w > 0 && this.h > 0) {
                        ctx.drawImage(image2, this.x, this.y, this.w, this.h, this.x, this.y, this.w, this.h);
                    }

                    // draw resize cubes
                    ctx.fillStyle = '#fff';
                    ctx.fillRect(this.x - this.iCSize[0], this.y - this.iCSize[0], this.iCSize[0] * 2, this.iCSize[0] * 2);
                    ctx.fillRect(this.x + this.w - this.iCSize[1], this.y - this.iCSize[1], this.iCSize[1] * 2, this.iCSize[1] * 2);
                    ctx.fillRect(this.x + this.w - this.iCSize[2], this.y + this.h - this.iCSize[2], this.iCSize[2] * 2, this.iCSize[2] * 2);
                    ctx.fillRect(this.x - this.iCSize[3], this.y + this.h - this.iCSize[3], this.iCSize[3] * 2, this.iCSize[3] * 2);
                }

                function drawScene() { // main drawScene function
                    if (!!image.src) {
                        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // clear canvas

                        // draw source image
                        ctx.drawImage(image, 0, 0, ctx.canvas.width, ctx.canvas.height);
                        image2.src = ctx.canvas.toDataURL();
                        // and make it darker
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                        ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);

                        // draw selection
                        theSelection.draw();
                    }

                }


                // loading source image
                image = new Image();
                image.onload = function () {
                }
                //image.src = 'images/image.jpg';
                image2 = new Image();
                image2.onload = function () {
                }

                // creating canvas and context objects
                canvas = document.getElementById('panel');
                ctx = canvas.getContext('2d');

                // create initial selection
                theSelection = new Selection(100, 100, 150, 150);

                $('#panel').mousemove(function (e) { // binding mouse move event
                    var canvasOffset = $(canvas).offset();
                    iMouseX = Math.floor(e.pageX - canvasOffset.left);
                    iMouseY = Math.floor(e.pageY - canvasOffset.top);

                    // in case of drag of whole selector
                    if (theSelection.bDragAll) {
                        theSelection.x = iMouseX - theSelection.px;
                        theSelection.y = iMouseY - theSelection.py;
                    }

                    for (var i = 0; i < 4; i++) {
                        theSelection.bHow[i] = false;
                        theSelection.iCSize[i] = theSelection.csize;
                    }

                    // hovering over resize cubes
                    if (iMouseX > theSelection.x - theSelection.csizeh && iMouseX < theSelection.x + theSelection.csizeh &&
                        iMouseY > theSelection.y - theSelection.csizeh && iMouseY < theSelection.y + theSelection.csizeh) {

                        //theSelection.bHow[0] = true;
                        theSelection.iCSize[0] = theSelection.csizeh;
                    }
                    if (iMouseX > theSelection.x + theSelection.w - theSelection.csizeh && iMouseX < theSelection.x + theSelection.w + theSelection.csizeh &&
                        iMouseY > theSelection.y - theSelection.csizeh && iMouseY < theSelection.y + theSelection.csizeh) {

                        //theSelection.bHow[1] = true;
                        theSelection.iCSize[1] = theSelection.csizeh;
                    }
                    if (iMouseX > theSelection.x + theSelection.w - theSelection.csizeh && iMouseX < theSelection.x + theSelection.w + theSelection.csizeh &&
                        iMouseY > theSelection.y + theSelection.h - theSelection.csizeh && iMouseY < theSelection.y + theSelection.h + theSelection.csizeh) {

                        //theSelection.bHow[2] = true;
                        theSelection.iCSize[2] = theSelection.csizeh;
                    }
                    if (iMouseX > theSelection.x - theSelection.csizeh && iMouseX < theSelection.x + theSelection.csizeh &&
                        iMouseY > theSelection.y + theSelection.h - theSelection.csizeh && iMouseY < theSelection.y + theSelection.h + theSelection.csizeh) {

                        //theSelection.bHow[3] = true;
                        theSelection.iCSize[3] = theSelection.csizeh;
                    }

                    // in case of dragging of resize cubes
                    var iFW, iFH;
                    if (theSelection.bDrag[0]) {
                        var iFX = iMouseX - theSelection.px;
                        var iFY = iMouseY - theSelection.py;
                        iFW = theSelection.w + theSelection.x - iFX;
                        iFH = theSelection.h + theSelection.y - iFY;
                    }
                    if (theSelection.bDrag[1]) {
                        var iFX = theSelection.x;
                        var iFY = iMouseY - theSelection.py;
                        iFW = iMouseX - theSelection.px - iFX;
                        iFH = theSelection.h + theSelection.y - iFY;
                    }
                    if (theSelection.bDrag[2]) {
                        var iFX = theSelection.x;
                        var iFY = theSelection.y;
                        iFW = iMouseX - theSelection.px - iFX;
                        iFH = iMouseY - theSelection.py - iFY;
                    }
                    if (theSelection.bDrag[3]) {
                        var iFX = iMouseX - theSelection.px;
                        var iFY = theSelection.y;
                        iFW = theSelection.w + theSelection.x - iFX;
                        iFH = iMouseY - theSelection.py - iFY;
                    }

                    if (iFW > theSelection.csizeh * 2 && iFH > theSelection.csizeh * 2) {
                        theSelection.w = iFW;
                        theSelection.h = iFH;

                        theSelection.x = iFX;
                        theSelection.y = iFY;
                    }

                    drawScene();
                });

                $('#panel').mousedown(function (e) { // binding mousedown event
                    var canvasOffset = $(canvas).offset();
                    iMouseX = Math.floor(e.pageX - canvasOffset.left);
                    iMouseY = Math.floor(e.pageY - canvasOffset.top);

                    theSelection.px = iMouseX - theSelection.x;
                    theSelection.py = iMouseY - theSelection.y;

                    if (theSelection.bHow[0]) {
                        theSelection.px = iMouseX - theSelection.x;
                        theSelection.py = iMouseY - theSelection.y;
                    }
                    if (theSelection.bHow[1]) {
                        theSelection.px = iMouseX - theSelection.x - theSelection.w;
                        theSelection.py = iMouseY - theSelection.y;
                    }
                    if (theSelection.bHow[2]) {
                        theSelection.px = iMouseX - theSelection.x - theSelection.w;
                        theSelection.py = iMouseY - theSelection.y - theSelection.h;
                    }
                    if (theSelection.bHow[3]) {
                        theSelection.px = iMouseX - theSelection.x;
                        theSelection.py = iMouseY - theSelection.y - theSelection.h;
                    }


                    if (iMouseX > theSelection.x + theSelection.csizeh && iMouseX < theSelection.x + theSelection.w - theSelection.csizeh &&
                        iMouseY > theSelection.y + theSelection.csizeh && iMouseY < theSelection.y + theSelection.h - theSelection.csizeh) {

                        theSelection.bDragAll = true;
                    }

                    for (var i = 0; i < 4; i++) {
                        if (theSelection.bHow[i]) {
                            theSelection.bDrag[i] = true;
                        }
                    }
                });

                $('#panel').mouseup(function (e) { // binding mouseup event
                    theSelection.bDragAll = false;

                    for (var i = 0; i < 4; i++) {
                        theSelection.bDrag[i] = false;
                    }
                    theSelection.px = 0;
                    theSelection.py = 0;
                });

                drawScene();

                $('.crop').on('click', function () {
                    var temp_ctx, temp_canvas;
                    temp_canvas = document.createElement('canvas');
                    temp_ctx = temp_canvas.getContext('2d');
                    temp_canvas.width = theSelection.w;
                    temp_canvas.height = theSelection.h;
                    temp_ctx.drawImage(image2, theSelection.x, theSelection.y, theSelection.w, theSelection.h,
                        0, 0, theSelection.w, theSelection.h);
                    var vData = temp_canvas.toDataURL();
                    $("#profile-img").attr('src', vData);
                    driver.dashboard.uploadPicture(vData);
                    $('.modal').modal('hide');
                })

                $('.change').on('click',function(){
                    $('#upload').trigger('click');
                })

                $('#upload').on('change', function (evt) {
                    var file = evt.target.files[0]; // FileList object

                    //enabled only for images
                    if (!file.type.match('image.*')) {
                        return;
                    }

                    var reader = new FileReader();

                    reader.onload = (function (theFile) {
                        return function (e) {
                            image.src = e.target.result;
                            drawScene();
                            $('#preview').modal();
                        };
                    })(file);

                    reader.readAsDataURL(file);
                    return false;
                });
            },
            uploadPicture: function (picture) {
                $.post('register/set_picture', {picture: picture}, function (data) {
                    console.log(data)
                });
            }
        },

        book: {
            init: function(){
                driver.book.id = driver.parameters[0];
            },
            chapterSortConfig: {
                helper: 'clone',
                connectWith: '.chapters',
                placeholder: 'placeholder',
                opacity:0.8,
                update: function () {
                    var cont = 0;
                    $('.chapter').each(function (i) {
                        var li = $(this);
                        var parent = li.parent().data('section-id');
                        if (parent) {
                            driver.book.data[0][cont] = parent;//section
                            driver.book.data[1][cont] = li.attr('data-id');//id
                            driver.book.data[2][cont] = (i + 1);//order
                            cont++;
                        }
                    });
//                        driver.book.reorder();
                },
                stop: function () {
                    var data = {
                        section: driver.book.data[0].toString(),
                        id: driver.book.data[1].toString(),
                        order: driver.book.data[2].toString()
                    };
                    $.post("chapter/update",
                        data, function (response) {
//                            $('#result').html(response);
                            broadcast.emit('move-chapter', data);
                        });
                }
            },
            userConnected: H.compile($('#connected-template').html()),
            sectionsTemplate: H.compile($('#section-template').html()),
            chapterTemplate: H.compile($('#chapter-template').html()),
            statusTooltipConf:{
                delay: { show: 500, hide: 100 },
                title:function(){
                    var user = $(this).data('user_id');
                    if(user !== ""){
                        user = $(this).data('user');
                    }
                    else
                        user="nobody";
                    var text = $(this).data('title')+' assigned to '+ user;
                    return text;
                },
                placement:'top'
            },
            reorder: function () {
                var parent = 0;
                $(".sections").children().each(function (i) {
                    var li = $(this);
                    var children = 0;
                    parent++;
                    li.children('.section-name').find('span').html((i + 1) + '.&nbsp;&nbsp;');
                    li.children().children('.chapter').each(function (j) {
                        var $this = $(this);
                        children++;
                        $this.find('.chapter').html(parent.toString() + '.' + (children) + '.&nbsp;&nbsp;');
                    });
                });
            },
            /**
             * Controller
             */
            tocmanager: function () {
                var $sections = $(".sections");
                driver.book.handleBroadcasting();
                driver.book.handleCoAuthors(driver.book.id);
                driver.book.handleToCPersistence($sections);

                driver.book.data = [
                    [""],
                    [""],
                    [""]
                ];
                $sections.filter('.contributor').sortable({
                    placeholder: 'placeholder',
                    opacity: 0.8,
                    update: function () {
                        $('.collapse').css("position",'relative');
                        var data = "";
                        var newPositions = [];
                        $sections.children().each(function (i) {
                            var li = $(this);
                            var id = li.data("id");
                            data += " " + id + '=' + (i + 1) + '&';
                            newPositions.push(id);
//                                driver.book.reorder();
                        });

                        $.post("section/update", data, function (response) {
//                            $('#result').html(response);
                            broadcast.emit('move-section', newPositions);
                        });
                    }
                });
                $sections.disableSelection();
                $(".contributor .chapters").sortable(driver.book.chapterSortConfig);
                $(".chapters").disableSelection();
                var $list = $('.lists');
                if($sections.hasClass('contributor')){
                    //Rename Chapter
                    $list.editable({
                        highlight:false,
                        selector: '.chapter .title',
                        pk: 1,
                        type: 'text',
                        mode: 'inline',
                        toggle: 'dblclick',
                        url: 'chapter/changeName',
                        params: function (params) {
                            var data = {};
                            data['title'] = params.value;
                            data['id'] = $(this).parent().data('id');
                            return data;
                        },
                        success:function(response){
                            var data = JSON.parse(response);
                            $(this).attr('title', data.title);
                            broadcast.emit('updateTitleChapter',data);
                        }
                    });
                    //renaming sections
                    $list.editable({
                        selector: '.section-name .name',
                        pk: 1,
                        type: 'text',
                        mode: 'inline',
                        toggle: 'dblclick',
                        url: 'section/changeName',
                        params: function (params) {
                            var data = {};
                            data['title'] = params.value;
                            data['id'] = $(this).parent().parent().data('id');
                            return data;
                        },
                        success:function(response){
                            var data = JSON.parse(response);
                            broadcast.emit('updateTitleSection',data);
                        }
                    });
                }

                $('body').on('click', '.chapter-contents', function () {
                    $('#chapter-contents-modal').find('.modal-body')
                        .load($(this).attr('href'));
                    $('#chapter-contents-modal').modal('show');
                    return false;
                })
                    .on('click','.chapter-status a', function () {
                        var $this = $(this),
                            $chapter = $this.parents('.chapter'),
                            name = $chapter.find('.title').html(),
                            chapter_id = $chapter.data('id');
                        $('#chapter-message-title').text(name);
                        $('#chapter_id').val(chapter_id);
                        $('#selected-user').val($this.data('user_id'));
                        $.post('status/chapterStatusList',{'chapter_id':chapter_id},function(response){
                            var addStatus = H.compile($('#status-item-template').html());
                            $('#StatusList').empty();
                            for(var item in response){
                                var data ={
                                    id: response[item].id,
                                    title: response[item].title,
                                    status: response[item].status === "1"?'checked':'',
                                    user_id: response[item].user_id
                                };
                                $('#StatusList').append(addStatus(data));
                            }
                        },'json');
                        $.getJSON('user/get_all_users',null)
                            .done(function(response){
                                $('.select-user').empty();
                                var addUser = H.compile($('#status-user-template').html());
                                for(var item in response){
                                    var user = response[item].names!=null ? response[item].names:response[item].username;
                                    var data = {
                                        id:response[item].id,
                                        user:user
                                    };
                                    $('.chapter-message-user[data-user="'+response[item].id+'"]').children('.name').find('strong').text(response[item].names);

                                    $('.status-item').find('.select-user').append(addUser(data));
                                }
                                $('.chapter-message-user[data-user="0"]').children('.name').find('strong').text('nobody');
                            });
                        $('#chapter-message-modal').modal('show');
                        return false;
                    });
                /*don't show outline around sections when is upon a chapter*/
                $('.chapter,.accordion-body')
                    .on('mouseover',function(){
                        $('.section').attr('style','border:solid 1px #FFFFFF; !important;');
                    })
                    .on('mouseout',function(){
                        $('.section').attr('style','');
                    });
                /*End condition, outline around*/
                $('.chapter-status').on('mouseover','.status',function(){
                    var status = $(this);
                    var id = status.data('id');
                    var user = status.data('user_id');
                    if(user !== "" && status.data('user')=="" ){
                        var url = 'user/getUsersInfo/'+user.toString();
                        $.post(url,null,function(response){
                            user = response.names;
                            $('.chapter-status').find('.status[data-id="'+id+'"]').data('user',user);
                        },'json');
                    }
                });

                $('.chapter-status .status').tooltip(driver.book.statusTooltipConf);

                $('body').on('click','.status-item .status_name',function(){
                    var value =$(this).text();
                    $(this).hide();
                    $(this).parent().find('.edit_status_name').attr('type','text').attr('value',value);
                })
                    .on('click','.select-user .status-user',function(){
                        $(this).parents('.dropdown').find('.chapter-message-user').find('strong').text($(this).text());
                        $(this).parents('.dropdown').find('.selected-user').val($(this).data('id'));
                        $('.dropdown').removeClass('open');
                        return false;
                    });

                $('#chapter-status').on('submit',function(){
                    var data =new Array(
                        new Array(),
                        new Array(),
                        new Array(),
                        new Array()
                    );
                    $('.status-item').each(function(i){
                        data[0][i] = $(this).data('id');//status id
                        data[1][i] = $(this).find('.edit_status_name').val();//title
                        data[2][i] = $(this).find('.selected-user').val();//user
                        data[3][i] = $(this).find('.status_complete').is(':checked')? '1':'0';//isComplete
                    });
                    var info ={
                        id:data[0].toString(),
                        title:data[1].toString(),
                        user_id:data[2].toString(),
                        status:data[3].toString()
                    }
                    $.post('status/update',info,function(response){
                        if(response.ok){
                            $('#chapter-message-modal').modal('hide');
                            driver.book.updateStatus(info);
                            broadcast.emit('update-status-chapter',info);
                        }
                    },'json');
                    return false;
                });

                $('#add-chapter-status').on('click',function(){
                    var book = driver.book.id;
                    if(book.indexOf('#')>=0){//TODO JARBIT: What is this for?
                        book = book.substr(0,book.length-1);
                    }
                    var data = {
                        book_id:book,
                        chapter_id:$('#chapter_id').val(),
                        user_id:0,
                        status_id:''
                    };
                    $.post('status/save',data,function(response){
                        data.status_id = response.id;
                        driver.book.addStatus(data);
                        broadcast.emit('add-chapter-status',data);
                    },'json');
                    return false;
                });

                $('body').on('click','.status-delete',function(){
                    if(confirm('are you sure?')){
                        var status_id =$(this).parents('.status-item').data('id');
                        $.post('status/delete',{id:status_id},function(response){
                            if(response.ok)
                            {
                                driver.book.deleteChapterStatus(response);
                                broadcast.emit('delete-chapter-status',response);
                            }
                        },'json');
                    }
                    return false;
                })
                    .on('click','.delete-chapter', function(){
                        var $this = $(this);
                        if(confirm('Are you sure?')){
                            $.post('chapter/delete_chapter',
                                {chapter_id: $this.data('id')}, function(response){
                                if(response.ok){
                                    driver.book.deleteChapter(response);
                                    broadcast.emit('delete-chapter',response);
                                }

                            },'json');
                        }
                        return false;
                    });

                if($sections.hasClass('contributor')){
                    $('.btn-chapter').on('click',function(){
                        if($sections.children().length>0){
                            $('#create-chapter-modal').modal('show');
                        }
                        else
                        {
                            var info = H.compile($("#info-template").html());
                            $('#result').html(info({type: 'warn', text: 'You have to create a sections'}));
                        }
                    });
                }else{
                    $('.btn-chapter').attr('disabled','disabled');
                    $('.btn-section').attr('disabled','disabled');
                    $('.btn-users').attr('disabled','disabled').removeClass('btn-info');
                }


                driver.validateForm('#create-chapter');//validationEngine
                $('#create-chapter').on('submit', function (e) {

                    var $this = $(this);
                    if($this.validationEngine('validate')){
                        var title = $this.find('#title').val(),
                            $section = $('.section').last(),
                            section_item = $section.attr('data-order'),
                            editor_id = $('#select-editor').find('option:selected').val();
                        $this.find(":submit").button('loading');
                        $.post($this.attr('action'), $this.serialize(), function (data) {
                            if (data.ok) {
                                var url = "", type = "";
                                switch (editor_id) {
                                    case ('1'):
                                        type = 'Lexicon';
                                        url = "dictionary/creator/";
                                        break;
                                    case ('2'):
                                        type = "WYSI";
                                        url = "editor/normal/";
                                        break;
                                }
                                var data = {'id': data.id, title: title,
                                    section: section_item, 'order': data.order,
                                    book_id:driver.book.id, 'url': url, 'type': type};
                                broadcast.emit('new-chapter', data);
                                driver.book.drawChapter(data);
                                $('.modal').modal('hide');
                                $(".chapters").sortable('refresh');
                                $this.get(0).reset();
                                $('.alert').hide();
                            }else{
                                $('.alert').text('An error has occurred');
                            }

                        }, 'json')
                        .always(function(resp){
                            $this.find(":submit").button('reset');
                        })
                        .error(function(resp){
                            $('.alert').text('An error has occurred').show();
                        });
                    }

                    return false;
                });

                driver.validateForm('#create-section');//validationEngine

                $("#create-section").on('submit', function (e) {
                    var $this = $(this);
                    if($this.validationEngine('validate')){
                        var data = $this.serialize(),
                            title = $this.find('#title').val();
                        $this.find(":submit").button('loading');
                        $.post($this.attr('action'), data, function (response) {
                            //Implement the section's load
                            if (response.ok) {
                                var data = {'id': response.id, title: title, order: response.order, book_id: driver.book.id};
                                broadcast.emit('new-section', data);
                                driver.book.drawSection(data)
                                $('.modal').modal('hide');
                                $this.get(0).reset();
                            }
                            $this.find(":submit").button('reset');
                        }, 'json');
                    }

                    return false;
                });
                $('body').on('click','.delete-section', function(){
                    var $this = $(this);
                    if(confirm('Are you sure?')){
                        $.post('section/delete_section', {section_id: $this.data('id')}, function(response){
                            if(response.ok)
                            {
                                driver.book.deleteSection(response);
                                broadcast.emit('delete-section',response);
                            }
                        },'json');
                    }
                    return false;
                });


                /* INVITE */

                $('#invited-email').on('submit', function(){
                    var $this =$(this);
                    $this.find(':submit').button('loading');
                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            alert('Well done');
                            $this.get(0).reset();
                        }
                        $this.find(":submit").button('reset');
                    }, 'json');
                    return false;
                });

                /* LOCK/UNLOCK CHAPTER */
                $('body').on('click', '.lock', function(){
                    var $this = $(this), lock = !!$this.data('lock');
                    $.post($this.attr('href'), function(resp){
                        if(resp.ok){
                            lock = !lock;
                            $this.data('lock', lock);
                            var $parent = $this.parents('.options'),
                                $edit = $parent.find('.edit'),
                                $delete = $parent.find('.delete-chapter');
                            if(lock){
                                $this.text('Unlock');
                                $edit.hide();
                                $delete.hide();

                            }else{
                                $this.text('Lock');
                                $edit.show();
                                $delete.show();
                            }
                        }
                    }, 'json');
                    return false;
                })
            },
            handleBroadcasting: function(){

                broadcast.customOnConnect(function (io) {
                    var url = use.nodejs+'pubsweetbackend/editing-sections?callback=?';

                    $.getJSON(url, function(resp){
                        $.each(resp, function(i, item){
                            if(item==null){
                                return;
                            }
                            var $edit = $('li.chapter[data-id="'+item.chapter_id+'"]').find('.edit'),
                                editorName = item.user.names=='' ? item.user.username : item.user.names,
                                $newEdit = $('<span></span>', {"class":'edit chapter-disabled', text: editorName+' is editing',
                                    'title': editorName+' is editing'});

                            $edit.replaceWith($newEdit);
                        });
                    });

                    io.socket.on('lock-wysi', function(data){
                        var $edit = $('li.chapter[data-id="'+data.chapter_id+'"]').find('.edit'),
                            editorName = data.user.names==''?data.user.username:data.user.names,
                            $newEdit = $('<span></span>', {"class":'edit chapter-disabled', text: editorName + ' is editing',
                                'title': editorName + ' is editing'});

                        $edit.replaceWith($newEdit);
                    });
                    io.socket.on('unlock-wysi', driver.book.unlockChapter);
                    $('body').on('click', '.chapter-disabled', function(){
                        if(confirm('Do you want to unlock this chapter?')){
                            var $this = $(this),
                                data = {chapter_id: $this.parents('.chapter').data('id')};
                            io.socket.emit('unlock-wysi', data);
                            driver.book.unlockChapter(data);
                        }
                    })
                });

            },
            unlockChapter: function(data){
                var $edit = $('li.chapter[data-id="'+data.chapter_id+'"]').find('.edit'),
                    $newEdit = $('<a></a>', {"class":'edit', text: 'Edit',
                        'href': "editor/normal/"+data.chapter_id});
                $edit.tooltip('destroy');
                $edit.replaceWith($newEdit);
            },
            handleToCPersistence: function($sections){
                var user_id;
                $(function(){
//                    $.getJSON('user/getUsersInfo',function (response) {
//                        user_id = response.id;
//                        sessionStorage.user_id = user_id;
//
//                        if(user_id !== undefined)
//                        {
//                            if(localStorage.sections !== undefined){
//                                driver.storedSections = JSON.parse(localStorage.sections);
//                                driver.book.hiddenSections = driver.storedSections[user_id]==undefined?undefined:driver.storedSections[user_id][driver.book.id];
//                                if(driver.book.hiddenSections==undefined){
//                                    driver.book.hiddenSections = [];
//                                }
//
//                                for(var i = 0; i < driver.book.hiddenSections.length; i++){
////                                    if(sections.hide[i].user_id === user_id){
//                                    var section_id = driver.book.hiddenSections[i];
//                                    var item = $('#section-chapters-'+section_id);
//                                    $('.accordion-toggle[data-target="#section-chapters-'+section_id+'"]').addClass('collapsed');
//                                    item.attr("style","height:0px");
//                                    item.removeClass('in');
////                                    }
//                                }
//
//                            }else{
//                                var object = {};
//                                object[driver.book.id] = [];
//                                driver.storedSections = {};
//                                driver.storedSections[user_id] = object;
//                                driver.book.hiddenSections = [];
//                            }
//                        }
//
//                    });
                    $sections.show();
                    $('.collapse').on('show',function(){
                        var section_id =$(this).find('.chapters').data('section-id');
                        if(driver.book.hiddenSections!=undefined){
                            var index = driver.book.hiddenSections.indexOf(section_id);
                            delete driver.book.hiddenSections[index];
                            driver.book.saveTocPersistence(user_id, driver.book.id, driver.book.hiddenSections);
                        }


//                        var user_id = sessionStorage.user_id;
//                        var sections = JSON.parse(localStorage.sections);
//                        var item = {id:section_id,user_id:user_id};
//                        for(var i in sections.hide){
//                            if(JSON.stringify(sections.hide[i])==JSON.stringify(item)){
//                                sections.hide.splice(i,1);
//                                localStorage.sections = JSON.stringify(sections);
//                            }
//                        }
                    })
                        .on('hide',function(){
                            var section_id =$(this).find('.chapters').data('section-id');
                            if(driver.book.hiddenSections!=undefined){
                                driver.book.hiddenSections.push(section_id);
                                driver.book.saveTocPersistence(user_id, driver.book.id, driver.book.hiddenSections);
                            }

//                            var user_id = sessionStorage.user_id;
//                            if(localStorage.sections !== undefined)
//                            {
//                                var sections = JSON.parse(localStorage.sections);
//                                sections.hide.push({id:section_id,user_id:user_id});
//                                localStorage.sections = JSON.stringify(sections);
//                            }
//                            else
//                            {
//                                var sections = {hide:[{id:section_id,user_id:user_id}]};
//                                localStorage.sections = JSON.stringify(sections);
//                            }
                        });

                });
            },
            saveTocPersistence: function(user, book_id, sections){
                var uniques = [];
                $.each(sections, function(i, el){
                    if($.inArray(el, uniques) === -1) uniques.push(el);
                });
                if(driver.storedSections[user]==undefined){
                    driver.storedSections[user] = {}
                }
                driver.storedSections[user][book_id] = uniques;
                localStorage.sections = JSON.stringify(driver.storedSections);
            },
            /**
             * handle everything related to CoAuthors
             * @param bookid
             */
            handleCoAuthors: function(bookid){
                var newCoAuthor = H.compile($('#new-coauthor-template').html());
                $('#new-coauthor').on('submit', function(){
                    var $this =$(this),
                        $option =$this.find('#user').find('option:selected');

                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            $('#coauthors').append(newCoAuthor({user: $option.text(),
                                contributor: $('#contributor').is(':checked'),
                                reviewer: $('#reviewer').is(':checked')
                            }));
                            $option.remove();
                        }
                    }, 'json');
                    return false;
                });

                $('body').on('click','.remove', function(){
                    var $this = $(this);
                    if(confirm('Are you sure?')){
                        $.post($this.attr('href'), {id: $this.data('user-id'), book_id: bookid}, function(data){
                            if(data.ok){
                                var $row=$this.parents('.deletable');

                                $('#user').append($('<option></option>',
                                    {value: $this.data('user-id'),
                                        text: $row.find('td').first().text()}));
                                $row.remove();
                            }
                        },'json');
                    }
                    return false;
                })
                    .on('click', '.btn-update', function(){
                        var $this = $(this);
                        $.post('book/updateCoAuthor/',
                            {
                                user: $this.data('user-id'),
                                book: bookid,
                                field: $this.data('field'),
                                value: +(!$this.hasClass('active'))
                            }, function(data){
                                if(data.ok){
                                    $this.toggleClass('active');
                                }
                            })
                    });



            },
            updateTitleSection:function(data){
                $('.section[data-id="'+data.id+'"]').find('.name').text(data.title);
            },
            updateTitleChapter:function(data){
                $('.chapter[data-id="'+data.id+'"]').find('.title').text(data.title).attr('title', title);
            },
            drawSection: function (data) {
                if(driver.book.id==data.book_id){
                    $('.sections').append(driver.book.sectionsTemplate(data));
                    $(".sections").sortable('refresh');
                    $(".chapters").sortable(driver.book.chapterSortConfig);
                }
            },
            drawChapter: function (data) {
                if(driver.book.id==data.book_id){
                    $('.section').last().find('.chapters').append(driver.book.chapterTemplate(data));
                    //                $('.chapter .title').editable(driver.book.editableChapterConfig);
                    $('.chapter-status .status').tooltip(driver.book.statusTooltipConf);
                }

            },
            moveSection: function (data) {
                $.each(data, function () {
                    $('.section[data-id="' + this + '"]').appendTo(".sections");
                });
                $(".sections").sortable("refresh");
            },
            deleteSection: function(data){
                $('.section[data-id="'+data.id+'"]').remove();
            },
            moveChapter: function (data) {
                var section = data.section.split(',');
                var chapter = data.id.split(',');
                var order = data.order.toString().split(',');
                $.each(chapter, function (i) {
                    $('.chapter[data-id="' + chapter[i] + '"]').appendTo('.chapters[data-section-id="' + section[i] + '"]');
                });
                $(".sections").sortable("refresh");
                $(".chapters").sortable(driver.book.chapterSortConfig);
            },
            deleteChapter: function(data){
                $('.chapter[data-id="'+data.id+'"]').remove();
            },
            addStatus:function(data){
                var addChapterStatus = H.compile($('#status-chapter-template').html());//element in the drag & drop
                var addStatus = H.compile($('#status-item-template').html()); //modal
                $('.chapter[data-id="'+data.chapter_id+'"]').find('.chapter-status a').append(addChapterStatus(data));
                var data2 ={
                    id:data.status_id,
                    title:'create content',
                    user_id:0
                }
                $('#StatusList').append(addStatus(data2));
                $('.status-item[data-id="'+data2.id+'"] .chapter-message-user').children('.name').find('strong').text('nobody');
                $.getJSON('user/get_all_users',null,function(response){
                    $('.select-user').empty();
                    var addUser = H.compile($('#status-user-template').html());
                    for(var item in response){
                        var data = {id:response[item].id,user:response[item].names};
                        $('.status-item').children().find('.select-user').append(addUser(data));
                    }
                });
                $('.chapter-status .status').tooltip(driver.book.statusTooltipConf);
            },
            deleteChapterStatus:function(data){
                $('.chapter-status').find('.status[data-id="'+data.id+'"]').remove();
                $('.status-item[data-id="'+data.id+'"]').remove();
            },
            updateStatus:function(data){
//                var info ={
//                    id:data[0].toString(),
//                    title:data[1].toString(),
//                    user_id:data[2].toString(),
//                    status:data[3].toString()
//                };
                var id =  data.id.split(',');
                var status = data.status.split(',');
                var title = data.title.split(',');
                var user = data.user_id.split(',');

                $.each(id,function(i){
                    var statusItem = $('.chapter-status').find('.status[data-id="'+id[i]+'"]');
                    if(status[i] =="1"){
                        statusItem.text('Ø');
                        statusItem.attr('data-status',1);
                    }
                    else{
                        statusItem.text('O');
                        statusItem.attr('data-status',0);
                    }
                    if(title[i] !== "")
                        statusItem.data('title',title[i]);
                    if(user[i] !== ''){
                        $.getJSON('user/getUsersInfo/'+user[i],null,function(response){
                            statusItem.data('user', response.names);
                            statusItem.data('user_id', response.id);
                        });
                    }
                });
            },
            stats: function() {
                var margin = {top: 20, right: 20, bottom: 30, left: 40},
                    width = 960 - margin.left - margin.right,
                    height = 500 - margin.top - margin.bottom;

                var x = d3.scale.ordinal()
                    .rangeRoundBands([0, width], .1);

                var y = d3.scale.linear()
                    .range([height, 0]);

                var xAxis = d3.svg.axis()
                    .scale(x)
                    .orient("bottom");

                var yAxis = d3.svg.axis()
                    .scale(y)
                    .orient("left")
                    .ticks(10);

                var svg = d3.select("#barchart")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                d3.json("review/data/" + driver.parameters[0], function (error, data) {

                    x.domain(data.map(function (d) {
                        return d.names;
                    }));
                    y.domain([0, d3.max(data, function (d) {
                        return d.allComments;
                    })]);

                    svg.append("g")
                        .attr("class", "y axis")
                        .call(yAxis)
                        .append("text")
                        .attr("transform", "rotate(-90)")
                        .attr("y", 6)
                        .attr("dy", ".71em")
                        .style("text-anchor", "end")
                        .text("Quantity");

                    svg.selectAll(".bar")
                        .data(data)
                        .enter().append("rect")
                        .attr("class", "bar")
                        .attr("x", function (d) {
                            return x(d.names);
                        })
                        .attr("width", x.rangeBand())
                        .attr("y", function (d) {
                            return y(d.allComments);
                        })
                        .attr("height", function (d) {
                            return height - y(d.allComments);
                        });

                    //Adding after bar so text will be above the bars
                    svg.append("g")
                        .attr("class", "x axis")
                        .attr("transform", "translate(0," + height + ")")
                        .call(xAxis)
                        .selectAll("text")
                        .style("text-anchor", "end")
                        .attr("dx", "-.8em")
                        .attr("dy", ".15em")
                        .attr("transform", function (d) {
                            return "translate(10, -5)"
                        });

                    createPieChart(data);
                });

                function createPieChart(data) {
                    var w = 960,                        //width
                        h = 500,                            //height
                        margin = 40,                    //
                        r = (h / 2) - margin,                            //radius
                        color = d3.scale.category20c();     //builtin range of colors

//                    data = [{"label":"one", "value":20},
//                        {"label":"two", "value":50},
//                        {"label":"three", "value":30}];

                    var vis = d3.select("#piechart")
                        .data([data])                   //associate our data with the document
                        .attr("width", w)           //set the width and height of our visualization (these will be attributes of the <svg> tag
                        .attr("height", h)
                        .append("svg:g")                //make a group to hold our pie chart
                        .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")")    //move the center of the pie chart from 0, 0 to radius, radius

                    var arc = d3.svg.arc()              //this will create <path> elements for us using arc data
                        .outerRadius(r);

                    var pie = d3.layout.pie()           //this will create arc data for us given a list of values
                        .value(function (d) {
                            return d.allComments;
                        });    //we must tell it out to access the value of each element in our data array

                    var arcs = vis.selectAll("g.slice")     //this selects all <g> elements with class slice (there aren't any yet)
                        .data(pie)                          //associate the generated pie data (an array of arcs, each having startAngle, endAngle and value properties)
                        .enter()                            //this will create <g> elements for every "extra" data element that should be associated with a selection. The result is creating a <g> for every object in the data array
                        .append("svg:g")                //create a group to hold each slice (we will have a <path> and a <text> element associated with each slice)
                        .attr("class", "slice");    //allow us to style things in the slices (like text)

                    arcs.append("svg:path")
                        .attr("fill", function (d, i) {
                            return color(i);
                        }) //set the color for each slice to be chosen from the color function defined above
                        .attr("d", arc);                                    //this creates the actual SVG path using the associated data (pie) with the arc drawing function

                    arcs.append("svg:text")                                     //add a label to each slice
                        .attr("transform", function (d) {                    //set the label's origin to the center of the arc
                            //we have to make sure to set these before calling arc.centroid
                            d.innerRadius = r + 100;
                            d.outerRadius = r + 200;
                            return "translate(" + arc.centroid(d) + ")";        //this gives us a pair of coordinates like [50, 50]
                        })
                        .attr("text-anchor", "middle")                          //center the text on it's origin
                        .text(function (d, i) {
                            return data[i].names;
                        });        //get the label from our original data array

                }

                function type(d) {
//                    d.frequency = +d.frequency;
                    return d;
                }

                driver.book.wordCount();
                driver.book.bubbleUsersWordCount();
                driver.book.wordHistory();

            },
            wordCount: function () {
                var width = 940,
                    height = 660,
                    radius = 300,
                    color = d3.scale.category20c();

                var svg = d3.select("#word-count").append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(" + width / 2 + "," + height * .52 + ")");

                var partition = d3.layout.partition()
                    .sort(null)
                    .size([2 * Math.PI, radius * radius])
                    .value(function(d) { return d.words; });

                var arc = d3.svg.arc()
                    .startAngle(function(d) { return d.x; })
                    .endAngle(function(d) { return d.x + d.dx; })
                    .innerRadius(function(d) { return Math.sqrt(d.y/2); })
                    .outerRadius(function(d) { return Math.sqrt(d.y + d.dy); });

                d3.json("stats/bookWordCount/" + driver.parameters[0], function(error, root) {
                    var path = svg.datum(root).selectAll("path")
                        .data(partition.nodes)
                        .enter().append("path")
                        .attr("display", function(d) { return d.depth ? null : "none"; }) // hide inner ring
                        .attr("d", arc)
                        .style("stroke", "#fff")
                        .style("fill", function(d) { return color((d.children ? d : d.parent).name); })
                        .style("fill-rule", "evenodd")
                        .each(stash);

                    d3.selectAll("input").on("change", function change() {
                        var self = this,
                            value = function(d) { return d[self.value]; };

                        path
                            .data(partition.value(value).nodes)
                            .transition()
                            .duration(1500)
                            .attrTween("d", arcTween);
                    });

                    var text = svg.datum(root).selectAll("text").data(partition.nodes);
                    var textEnter = text.enter().append("text")
                        .style("fill-opacity", 1)
                        .attr("text-anchor", function(d) {
                            return (d.x + d.dx / 2) > Math.PI ? "end" : "start";
                        })
                        .attr("dy", ".2em")
                        .attr("transform", function(d) {
                            var multiline = (d.name || "").length > 16,
                                angle = (d.x + d.dx / 2) * 180 / Math.PI - 90,
                                rotate = angle + (multiline ? -.5 : 0);
                            return "rotate(" + rotate + ")translate(" + (Math.sqrt(d.y/2) ) + ")rotate(" + (angle > 90 ? -180 : 0) + ")";
                        })
                        .on("mouseover", function(d){
                            d3.select(this).selectAll('tspan').text(function(d){
                                return "("+ d.value + ") " + d.name;
                            });
                        }).
                        on('mouseout', function(d){
                            d3.select(this).selectAll('tspan').text(function(d){
                                return d.name!=undefined ? d.name.substr(0, 15)
                                + (d.name.length > 15 ? '...' : '') : '';
                            });
                        });
                    textEnter.append("tspan")
                        .attr("x", 0)
                        .text(function(d) { return d.name!=undefined ? d.name.substr(0, 15)
                        + (d.name.length > 15 ? '...' : '') : ''});

                    driver.book.bubbleWordCount(root);
                });

                // Stash the old values for transition.
                function stash(d) {
                    d.x0 = d.x;
                    d.dx0 = d.dx;
                }

                // Interpolate the arcs in data space.
                function arcTween(a) {
                    var i = d3.interpolate({x: a.x0, dx: a.dx0}, a);
                    return function(t) {
                        var b = i(t);
                        a.x0 = b.x;
                        a.dx0 = b.dx;
                        return arc(b);
                    };
                }

                d3.select(self.frameElement).style("height", height + "px");
            },
            bubbleWordCount : function (root) {
                var diameter = 940,
                    format = d3.format(",d"),
                    color = d3.scale.category20c();

                var bubble = d3.layout.pack()
                    .sort(null)
                    .size([diameter, diameter])
                    .padding(1.5);

                var svg = d3.select("#bubble-words").append("svg")
                    .attr("width", diameter)
                    .attr("height", diameter)
                    .attr("class", "bubble");

                //d3.json("stats/bookWordCount/" + driver.parameters[0], function(error, root) {
                    var node = svg.selectAll(".node")
                        .data(bubble.nodes(classes(root))
                            .filter(function(d) { return !d.children; }))
                        .enter().append("g")
                        .attr("class", "node")
                        .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

                    node.append("title")
                        .text(function(d) { return d.packageName + ": " + format(d.value); });

                    node.append("circle")
                        .attr("r", function(d) { return d.r; })
                        .style("fill", function(d) { return color(d.packageName); });

                    node.append("text")
                        .attr("dy", ".3em")
                        .style("text-anchor", "middle")
                        .text(function(d) { return d.className.substring(0, d.r / 3); });
                //});

                // Returns a flattened hierarchy containing all leaf nodes under the root.
                function classes(root) {
                    var classes = [];

                    function recurse(name, node) {
                        if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
                        else classes.push({packageName: name, className: node.name, value: node.words});
                    }

                    recurse(null, root);
                    return {children: classes};
                }

                d3.select(self.frameElement).style("height", diameter + "px");
            },
            bubbleUsersWordCount : function (root) {
                var diameter = 940,
                    format = d3.format(",d"),
                    color = d3.scale.category20c();

                var bubble = d3.layout.pack()
                    .sort(null)
                    .size([diameter, diameter])
                    .padding(1.5);

                var svg = d3.select("#users-words").append("svg")
                    .attr("width", diameter)
                    .attr("height", diameter)
                    .attr("class", "bubble");

                d3.json("stats/usersWordCount/" + driver.parameters[0], function(error, root) {
                    var node = svg.selectAll(".node")
                        .data(bubble.nodes(classes(root))
                            .filter(function(d) { return !d.children; }))
                        .enter().append("g")
                        .attr("class", "node")
                        .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

                    node.append("title")
                        .text(function(d) { return d.packageName + ": " + format(d.value); });

                    node.append("circle")
                        .attr("r", function(d) { return d.r; })
                        .style("fill", function(d) { return color(d.packageName); });

                    node.append("text")
                        .attr("dy", ".3em")
                        .style("text-anchor", "middle")
                        .text(function(d) { return d.className.substring(0, d.r / 3); });
                });

                // Returns a flattened hierarchy containing all leaf nodes under the root.
                function classes(root) {
                    var classes = [];

                    function recurse(name, node) {
                        if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
                        else classes.push({packageName: node.names, className: node.names, value: node.added});
                    }

                    recurse(null, root);
                    return {children: classes};
                }

                d3.select(self.frameElement).style("height", diameter + "px");
            },
            wordHistory: function () {
                var margin = {top: 20, right: 20, bottom: 100, left: 80},
                    width = 940 - margin.left - margin.right,
                    height = 500 - margin.top - margin.bottom;

                var parseDate = d3.time.format("%d-%b-%y").parse;

                var x = d3.time.scale()
                    .range([0, width]);

                var y = d3.scale.linear()
                    .range([height, 0]);

                var xAxis = d3.svg.axis()
                    .scale(x)
                    .orient("bottom");

                var yAxis = d3.svg.axis()
                    .scale(y)
                    .orient("left");

                var area = d3.svg.area()
                    .x(function(d) { return x(d.date); })
                    .y0(height)
                    .y1(function(d) { return y(d.words); });

                var svg = d3.select("#words-history").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                d3.csv("stats/wordHistory/" + driver.parameters[0], function(error, data) {
                    data.forEach(function(d) {
                        var date = d.date.split(" ")[0].split("-"),
                            time = d.date.split(" ")[1].split(":");

                        d.date = new Date(+date[0], +date[1]-1, +date[2], +time[0], +time[1], +time[2]);
                        d.words = parseInt(d.words);
                    });

                    x.domain(d3.extent(data, function(d) { return d.date; }));
                    y.domain([0, d3.max(data, function(d) { return +d.words; })]);

                    svg.append("path")
                        .datum(data)
                        .attr("class", "area")
                        .attr("d", area);

                    svg.append("g")
                        .attr("class", "x axis")
                        .attr("transform", "translate(0," + height + ")")
                        .call(xAxis);

                    svg.append("g")
                        .attr("class", "y axis")
                        .call(yAxis)
                        .append("text")
                        .attr("transform", "rotate(-90)")
                        .attr("y", 6)
                        .attr("dy", ".71em")
                        .style("text-anchor", "end")
                        .text("Words ($)");
                });
            },
            /**
             * controller / action
             */
            full: function(){
                $('.undo').on('click', function(){
                    var $this = $(this);
                    $.post($this.attr('href'), function(resp){
                       if(resp.ok){
                           $this.parents('.deleted').removeClass('deleted');
                           $this.remove();
                       }
                    }, 'json');
                    return false;
                })
            },
            /**
             * controller / action
             */
            findReplace: function(){
                var $currentChapter = null,
                    $contents = $('#contents'),
                    $alert = $('.alert.alert-info').clone();
                $('#down').on('click', function(){
                    $contents.highlight('next', $('#find').val(), function(node){
                        var $node = $(node);
                        $currentChapter = $node.parents('.chapter');
                        $(window).scrollTop($node.offset().top - 250);
                    });
                    return false;
                });
                $('#up').on('click', function(){
                    $contents.highlight('previous', $('#find').val(), function(node){
                        var $node = $(node);
                        $currentChapter = $node.parents('.chapter');
                        $(window).scrollTop($node.offset().top - 250);
                    });
                    return false;
                });
                $('#single-replace').on('click', function(){
                    var $highlight = $currentChapter.find('.highlight'),
                        $temp = $highlight.clone(),
                        textNode = document.createTextNode($('#replace').val());
                    $highlight.replaceWith(textNode);
                    $.post('chapter/replace/'+$currentChapter.data('id'),
                        {content: $currentChapter.html()},
                    function(resp){
                        if(resp.ok){
                            $contents.highlight('reset');
                            $currentChapter.html(resp.content);
                        }else{
                            $(textNode).replaceWith($temp);
                            $alert.find('p').text('Internal error: Unable to save').end()
                                .removeClass('alert-info')
                                .addClass('alert-error');
                            if(!!$('.alert.alert-info').length){
                                $('.alert.alert-info').replaceWith($alert.clone());
                            }else{
                                $('form').after($alert.clone());
                            }
                        }
                    }, 'json');
                    return false;
                });

            }

        },
        register: {
            login: function () {
                driver.validateForm('#login');//validationEngine
            },
            user: function () {
                driver.validateForm('#register-user');//validationEngine
            }
        },
        auth: {
            login: function () {
                driver.validateForm('#login');//validationEngine
            },
            logout:function(){
                sessionStorage.clear();
            }
        },
        dictionary: {
            termTemplate: H.compile($('#term-template').html()),
            listTerm: $('#list-term'),
            "$termDetail" : $('.text-editor'),
            "$language" : $('#language'),
            defaultEditorOptions: {
                "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": false, //Button to insert a link. Default true
                "image": true, //Button to insert an image. Default true,
                "color": false //Button to change color of font
            },
            /**
             * Controller
             */
            creator: function () {
                broadcast.customOnConnect(function () {
                    broadcast.emit('editing-terms');
                });
                /** Instead of
                 * $(document).on('ready', function(){});
                 *
                 * better use
                 *
                 * $(function(){}):
                 *
                 * it's shorter
                 */
                $(window).on("beforeunload", function() {
                    var term_editing =false,
                        definition= false;
                    if( $('#term-original-form').attr('data-edited')== 'true')
                        term_editing = true;
                    $.each($('.definitions').children(),function(i){
                        if ($(this).attr('data-edited') === 'true'){
                            definition = true;
                        }
                    });
                    if((term_editing === true) || (definition === true)) {
                        return "All of your unsaved changes will be lost if you continue. Keep on?";

                    }
                });
                $(function(){
                    $.getJSON('user/getUsersInfo', function(response){
                        sessionStorage.username = response.username;
                    });
                    localStorage.removeItem('term');
                });
                driver.dictionary.setBackDrop();

                var editor =$('#term-content') ;
                driver.addAsync('ckeditor', function(){
                    if (typeof CKEDITOR !== "undefined") {
                        var config = jQuery.extend({}, driver.basicConfig);
                        config.contentsLangDirection = 'rtl';
                        config.on ={'change': function(event){
                            $('.'+event.editor.id).parents('form').attr('data-edited','true');
                        }
                        };
                        config.extraPlugins+=',customflagpicker';
                        config.toolbar.push({ name: 'customflagpicker', items: [ 'FlagPicker']});
                        $('#term-content').ckeditor(config);
                    }
                });
                var $termDetail = $('.text-editor'),
                    $definitions = $termDetail.find('.definitions');

                var definitionConfig = jQuery.extend({}, driver.basicConfig),
                    definitionConfig2 = jQuery.extend({}, driver.basicConfig);
                driver.addAsync('ckeditor',function(){
                    $('#editor-136').ckeditor(definitionConfig);
                    $('#editor-152').ckeditor(definitionConfig2);
                });

                var $language = $('#language');
                $('body').on('submit', '.definition-form', function () {
                    var $this = $(this);
                    $this.find(":submit").button('loading');
                    $.post($this.attr("action"), $this.serialize(), function (response) {
                        if (response.ok) {
                            $this.find('.definition_id').val(response.id);
                            driver.info.html(
                                $(driver.infoTemplate({type: 'success',
                                    text: 'Definition successfully updated'})).hide().fadeIn(500));
                            setTimeout(function () {
                                $('.alert').fadeOut(1500);
                            }, 2000);
                        }
                        $this.find(":submit").button('reset');
                    }, 'json');
                    $this.attr('data-edited',false);
                    return false;
                })
                    //clicking a term
                    .on('click', '.item-list', driver.dictionary.loadTerm)
                    .on('click', '.accordion-toggle', function () {
                        if ($(this).hasClass('collapsed')) {
                            console.log('collapsed');
                        }
                    });

                $('body').on('change','.item-editor',function(){
                    var $form = $(this).parents('form');
                    $form.attr('data-edited','true');
                });

                var $termOriginalForm = $("#term-original-form"),
                    $termId = $termOriginalForm.find("#term-id");
                $termOriginalForm.on("submit", function (e) {
                    var $this = $(this),
                        term = $this.find(".item-editor").val(),
                        id = $termId.val();
                    $this.find(":submit").button('loading');

                    var language = $language.find('option:selected').val(),
                        term_update = {
                            id:$termId.val(),
                            term:$('.item-editor').val(),
                            meaning:$('#term-content').val(),
                            language:language
                        }
                    $.post($this.attr("action"), $this.serialize(), function (response) {
                        if (response.ok) {
                            localStorage.term = JSON.stringify(term_update);
                            var $item = $('.item-list[data-id="' + id + '"]').text(term);
                            var found = false;
                            $('.item-list').each(function (i, item) {
                                if ($(item).text() > term) {
                                    $(item).parent('li').before($item.parent('li'));
                                    found = true;
                                    return false;
                                }
                            });
                            if (!found) {//if no term is after the term, just add it at the end
                                driver.dictionary.listTerm.append($item.parent('li'));
                            }
                            var data = {id: id, term: term};
                            broadcast.emit('updating-term', data);
//                                broadcast.emit('remove-term-editing',data);
//                                driver.dictionary.removeTermEditing(data);
                            driver.info.html($(driver.infoTemplate({type: 'success',
                                text: 'Successfully updated'})).hide().fadeIn(500));
                            setTimeout(function () {
                                $('.alert').fadeOut(1500);
                            }, 2000);
                            $this.attr('data-edited',false);

                        }
                        $this.find(":submit").button('reset');
                    }, 'json');
                    return false;
                });

                //Move item to other chapter
                var $termChapter = $('#item-chapter');
                $termChapter.on('change', function (){
                    var $this = $(this),
                        itemId = $this.val(),
                        term_id =  $termId.val();
                    $.post('dictionary/update_chapter/',{
                        chapter_id: itemId,
                        term_id: term_id
                    }, function (response) {
                        $('[data-id="'+term_id+'"]').parent().remove();
                        $termOriginalForm.get(0).reset();
                        $('.text-editor').hide()
                        $('#change-chapter').hide();
                        $('.move-item').show();

                    });
                });
                $('.move-item').on('click',function(){
                    $('.move-item').hide();
                    $('#change-chapter').show();
                    return false;

                });
                $termDetail.hide();
                driver.dictionary.handleTermCreation($termOriginalForm, $termDetail, $termId, $definitions);

                driver.dictionary.listTerm.on('click', '.delete-term', function () {
                    $('#confirm-delete-term').modal();
                    var result = $(this).parent().children('.item-list');
                    $('#term_id').val(result.data('id'));
                    $('#term').html(result.html());
                });

                $('#form-term-delete').on('submit', function (e) {
                    var $this = $(this);
                    $.post($this.attr('action'), $this.serialize(), function (response) {
                        if (response.ok) {
                            var data = {id: response.id};
                            broadcast.emit('delete-term',data);
                            driver.dictionary.deleteTerm(data);
                            $('.modal').modal('hide');
                            $this.get(0).reset();
                        }
                    }, 'json');
                    return false;
                });
                $('#cancel').on('click', function () {
                    $termDetail.hide();
                    var data = {id: $termId.val()};
                    broadcast.emit('remove-term-editing', data);
                    driver.dictionary.removeTermEditing(data);
                });

                $('.save-all').on('click', function(){
                    $('.definitions').find('.definition-form').each(function(i, item){ $(item).find(':submit').click();})
                    $('#term-original-form').find(':submit').click();
                });
            },
            loadTerm: function(){
                $('.move-item').show();
                $('#change-chapter').hide();
                var $this = $(this),
                    $termId = $("#term-id"),
                    $definitions = driver.dictionary.$termDetail.find('.definitions');
                driver.info.html($(driver.infoTemplate({type: 'info',
                    text: 'Loading...'})));

                if (driver.dictionary.$termDetail.is(":visible")){
                    var language = driver.dictionary.$language.find('option:selected').val(),
                        term_editing =false;
                    if( $('#term-original-form').attr('data-edited')== 'true')
                        term_editing = true;

                    if($this.parent().data('locked') !== true){
                        var definition= false;
                        $.each($('.definitions').children(),function(i){
                            if ($(this).attr('data-edited') === 'true'){
                                definition = true;
                            }
                        });

                        if((term_editing === true) || (definition === true)){
                            if(!confirm('All of your unsaved changes will be lost if you continue. Keep on?')) {
                                return false;
                            }
                            else {
                                var data = {id: $termId.val()};
                                driver.dictionary.removeTermEditing(data);
                                view();
                            }
                        }
                        else {
                            var data = {id: $termId.val()};
                            driver.dictionary.removeTermEditing(data);
                            view();
                        }
                    }
                }
                else {
                    var data = {id: $termId.val()};
                    driver.dictionary.removeTermEditing(data);
                    view();
                }
                function view(){
                    $('#term-original-form').attr('data-edited','false');

                    // Assigns the input name in the editor
                    $(".item-editor").val($this.text());
                    //Assigns the id to input hidden
                    $termId.val($this.data("id"));
                    $.getJSON('term/get/' + $this.data("id"), function (data) {
                        driver.dictionary.handleFileUpload(data);

                        if(data.term.meaning === null)
                            data.term.meaning = '';
                        CKEDITOR.instances['term-content'].setData(data.term.meaning);
                        $definitions.find('form').each(function(i, item){
                            this.reset();
                            $(this).find('input[name="id"]').val('');

                        });
                        $definitions.find('form input[name="term_id"]').val(data.term.id);
                        var emptyEnglish = true, emptyFrench = true;
                        $.each(data.definitions, function () {
                            if(this.language_id==136 || this.language_id==152 ){
                                var $form = $definitions.find('.definition-form-'+this.language_id);
                                $form.find('input[name="term"]').val(this.term);
                                var $definitionId = $form.find('input[name="id"]');
                                if($definitionId.length<1){
                                    $form.append($('<input/>', {'type':'hidden', name:'id', value: this.id}));
                                }else{
                                    $definitionId.val(this.id);
                                }
                                if(this.language_id==136){
                                    CKEDITOR.instances["editor-136"].setData(this.definition);
                                    CKEDITOR.instances["editor-136"].updateElement();
                                    emptyEnglish = false;
                                }else if(this.language_id==152){
                                    CKEDITOR.instances["editor-152"].setData(this.definition);
                                    CKEDITOR.instances["editor-152"].updateElement();
                                    emptyFrench = false;
                                }

                            }
                        });
                        if(emptyEnglish){
                            CKEDITOR.instances["editor-136"].setData('');
                        }
                        if(emptyFrench){
                            CKEDITOR.instances["editor-152"].setData('');
                        }
                        driver.dictionary.$termDetail.show();
                        $('iframe').each(function(i, item){
                            if($(this).css('width')=='0px'){
                                $(this).css('width','100%')
                            }
                        });
                        $('.alert').fadeOut(500);
                    });
                    var data = {'id': $this.data('id'), 'editor': sessionStorage.username};
                    broadcast.emit('new-term-editing', data);
                    driver.dictionary.newTermEditing(data);

                }
                return false;
            },
            handleFileUpload: function(data){
                var imageHolder = H.compile($('#image-handler').html());
                $('#image-holder').html(imageHolder(data));
                $('#fileupload').fileupload({
                    dataType: 'json',
                    done: function (e, data) {
                        $('#result').html($('<img/>', {src: data.result.file}));
                        $('#delete-image').show();
                        $('#accordion2 .accordion-toggle').text('View/Edit Image');
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .bar').css(
                            'width',
                            progress + '%'
                        );
                    },
                    formData: function(){
                        return [{name: 'term_id', value: $('#term-id').val()}]
                    }
                });
                $('#delete-image').on('click', function(){
                    if(confirm('Are you sure?')){

                        $.post('dictionary/delete_image',{id: $(this).data('id')}, function(){
                            $('#result').html($('<img/>', {src: 'http://placehold.it/200'}))
                        } );
                    }
                });

            },

            /**
             * Insert the item in the list
             */
            insertTerm: function(data){
                var found = false;
                $('.item-list').each(function (i, item) {
                    if ($(item).text() > data.term) { //if ordered alphabetically the term.
                        $(item).parent('li').before(driver.dictionary.termTemplate(data));
                        found = true;
                        return false;
                    }
                });
                if (!found) {//if no term is after the term, just add it at the end
                    driver.dictionary.listTerm.append(driver.dictionary.termTemplate(data));

                }
            },
            editingTerms: function (data) {
                for (var item in data) {
                    driver.dictionary.newTermEditing(data[item]);
                }
            },
            newTermEditing: function (data) {
                $('[data-editor='+data.editor+']').each(function(i, item){
                    driver.dictionary.removeTermEditing({'id': $(item).data('id')});
                });
                var term = driver.dictionary.listTerm.find('[data-id="' + data.id + '"]');
                term.attr('data-editor', data.editor);
                term.parent().attr('data-locked', true);
                $('.editor[data-id="' + data.id + '"]').html('&nbsp;' + data.editor + '&nbsp;is editing');
                $('.item-list[data-id="' + data.id + '"]').addClass('items-terms-disable');
            },
            removeTermEditing: function (data) {
                if (data !== null) {
                    $('[data-id="' + data.id + '"]').parent().attr('data-locked', false);
                    $('.editor[data-id="' + data.id + '"]').empty();
                    $('.item-list[data-id="' + data.id + '"]').removeClass('items-terms-disable');
                }

            },
            newTerm: function (data) {
                if(driver.parameters[0]==data.chapter_id){
                    driver.dictionary.insertTerm(data)
                }

            },
            updatingTerm: function (data) {
                driver.dictionary.listTerm.find('.item-list[data-id="' + data.id + '"]').text(data.term);
            },
            deleteTerm: function (data) {
                driver.dictionary.listTerm.find('.item-list[data-id="' + data.id + '"]').parent().remove();
            },
            /**
             * Listener of the new term creation
             * */
            handleTermCreation: function($originalForm, $termDetail, $termId, $definitions){
                driver.validateForm('#create-term');//call ValidationEngine

                $("#create-term").on("submit", function (e) {
                    $('.move-item').show();
                    $('#change-chapter').hide();
                    var $this = $(this),
                        term = $this.find("#term-name").val();
                    $this.find(":submit").button('loading');
                    $.post($this.attr("action"), $this.serialize(), function (response) {
                        if (response.ok) {
                            $('.definition-form').each(function(i, item){ item.reset();});

                            for(var item in CKEDITOR.instances){
                                CKEDITOR.instances[item].setData('');
                            }

                            var found = false;
                            var data = {'id': response.id, term: term,chapter_id:driver.parameters[0]};
                            broadcast.emit('new-term', data);
                            $originalForm.get(0).reset();
//                            $definitions.empty();
                            $termDetail.show();
                            $originalForm.find(".item-editor").val(term);
                            $termId.val(response.id);
                            $definitions.find('input[name="term_id"]').val(response.id);
                            $definitions.find('input[name="id"]').val("");

                            driver.dictionary.insertTerm(data);
                            $('#term-content').val('');
                            $this.find(":submit").button('reset');
                            $this.get(0).reset();
                        }
                    }, 'json');
                    return false;
                });
            },
            setBackDrop: function(){

                var $listItem = $('#list-item'),
                    $backDrop = $('<div></div>',{html:'&nbsp;'});
                $('body').append($backDrop);
                $backDrop.css({
                    position: 'absolute',
                    top: $listItem.position().top,
                    left: $listItem.position().left,
                    width: $listItem.css('width'),
                    height: $listItem.css('height'),
                    "background-color": 'black',
                    opacity: 0.5
                });
                setTimeout(function () {
                    $backDrop.remove();
                    $('#syncing').fadeOut(500);
                    $('#create-load').fadeOut(500);
                }, 5000);

            }
        },
        editor: {
            normal: function () {

                broadcast.customOnConnect(function (io) {
                    $.getJSON('user/getUsersInfo',function(response){
                        sessionStorage.user_id = response.id;
                        io.socket.emit('lock-wysi', {chapter_id: driver.parameters[0],
                            'user': response});
                    });

                    $(window).on("beforeunload", function() {
                        io.socket.emit('unlock-wysi', {chapter_id: driver.parameters[0]});
                        return "All unsaved content will be lost if you leave";
                    });
                });

                $('#form-save-content').on('submit', function() {
                    var $this = $(this);
                    $this.find(':submit').button('loading');
                    $('.cke_button__save').find('.cke_button__save_icon')
                        .attr('style','background-image:url('+'public/img/loading.gif)!important; ' +
                            'background-position:0 0!important;');
                    $.post($this.attr('action'), $this.serialize(), function (data) {
                        if (data.ok) {
                            driver.info.html(
                                $(driver.infoTemplate({type: 'success',
                                    text: 'Successfully saved'})).hide().fadeIn(500));
                            setTimeout(function () {
                                $('.alert').fadeOut(1500);
                            }, 2000);
                        }
                    }, 'json')
                        .error(function(){
                            alert('can´t be saved');
                        })
                        .always(function(){
                            $('.cke_button__save').find('.cke_button__save_icon')
                                .removeAttr('style');
                        });
                    return false;
                });
                driver.addAsync('ckeditor', function () {
                    if (typeof CKEDITOR !== "undefined") {
                        $.ajax({
                            type: "GET",
                            url: 'flags/all',
                            async: false,
                            data: {},
                            dataType: 'JSON',
                            success: function(resp)
                            {
                                window.myflags=resp.data;
                            }

                        });

                        var config = {
                            startupFocus : true,
                            toolbarCanCollapse: true,
                            extraAllowedContent: '* [id]',
                            toolbar: [
                                { name: 'document', groups: [ 'mode', 'document', 'doctools' ],
                                    items: [ 'Source', '-', 'Save','CustomautosaveOptions'/*, 'NewPage', 'Preview'*/, 'Print'/*, '-', 'Templates'*/ ] },
                                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ],
                                    items: [ 'Find', 'Replace', '-', /*'SelectAll', '-',*/ 'Scayt' ] },
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ],
                                    items: [ 'Bold', 'Italic', /*'Underline', 'Strike', 'Subscript', 'Superscript',*/
                                        '-', 'RemoveFormat' ] },
                                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', /*'align', 'bidi'*/ ],
                                    items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Indent List',
                                        '-', /*'CreateDiv',*/ '-'/*,
                                         'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'*/, '-', 'BidiLtr', 'BidiRtl' ] },
                                { name: 'insert', items: [ 'InsertPre','Image', 'Link',/* 'Flash', */'Table', 'CreatePlaceholder','EqnEditor','SpecialChar']},
                                { name: 'styles', items: [ 'Styles', 'Format'/*, 'Blockquote', 'Font', 'FontSize' */]},
                                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                                { name: 'tools', items: [  'ShowBlocks' ]}/*,
                                 {name: 'language', groups:'Language', items:['customlanguage']}*//*,
                                 {
                                 name: 'spellcheck',
                                 items: [ 'jQuerySpellChecker' ]
                                 }*/
                            ],
                            removePlugins: 'forms,flash,floatingspace,iframe,newpage,resize,maximize,smiley,liststyleS,lite,autosave,align,bidi',//elementspath',
                            //elementspath, is for bottom bar
                            // jqueryspellchecker,
                            extraPlugins: 'imagebrowser,backup,placeholder,indentlist,eqneditor,specialchar,customautosave,insertpre,tabletools'/*,customlanguage'*/,
                            resize_enabled: false,
                            contentsCss: "public/css/custom_ckeditor.css",
                            imageBrowser_listUrl: "book/images/"+driver.parameters[0],

                            height: '75vh',
                            magicline_color: '#666',
                            on: {
                                'changeLanguage': function(event){
                                    event.editor.destroy();
                                    config.language = event.data;
                                    $('#editor').ckeditor(config);
                                }
                            },

                            coreStyles_bold	: { element : 'strong', attributes : {'class': 'Bold'} },
                            coreStyles_italic : { element : 'em', attributes : {'class': 'Italic'} },
                            coreStyles_blockquote : { element : 'blockquote', attributes : {'class': 'Blockquote'}},
                            filebrowserImageUploadUrl : 'editor/uploadImage/'+driver.parameters[0],
                            format_tags: "p;h1;h2;h3;h4;pre;blockquote",
                            autoSaveOptionUrl: 'book/saveUserConfig/'+driver.parameters[0],
                            autoSaveOptionTime: $('#editor').data('auto-save-time'),
                            flags: window.myflags,
                            baseHref: $('base').attr('href')
                        };
                        $('#editor').ckeditor(config);
                    }
                });
                var user_id =  sessionStorage.user_id;
                var zoom = localStorage["zoom-user" + user_id + "-chapter"+ driver.parameters[0]];

                $( "#slider").slider({
                    min:0,
                    max:100,
                    value:zoom !== undefined?zoom: 25,
                    slide:function( event, ui ){
                        var form =$('#form-save-content').find('.cke_wysiwyg_frame').contents();
                        form.find('.cke_editable').css('font-size',ui.value+'px');
                    },
                    change:function(event,ui){
                        var user_id =  sessionStorage.user_id;
                        localStorage["zoom-user" + user_id + "-chapter"+ driver.parameters[0]] = ui.value;
                    }
                });

                $('body').on('mouseover','.cke_top',function(){
                    if($(this).find('.cke_toolbox_main').css('display')=='none')
                    {
                        $(this).find('.cke_toolbox_main').toggle();
                        $(this).find('.cke_toolbox_collapser').removeClass('cke_toolbox_collapser_min');
                    }
                }).on('mouseleave','.cke_top',function(){
                        if($(this).attr('hide') === 'true')
                        {
                            $(this).find('.cke_toolbox_main').toggle();
                            $(this).find('.cke_toolbox_collapser').addClass('cke_toolbox_collapser_min');
                        }
                    });

                $('body').on('click','.cke_toolbox_collapser',function(){
                    if($(this).parents('.cke_top').attr('hide')== 'true'){
                        $(this).parents('.cke_top').attr('hide','false');
                        $(this).parents('.cke_top').find('.cke_toolbox_main').css({display:'inline'});
                        $(this).removeClass('cke_toolbox_collapser_min');
                    }
                    else{
                        $(this).parents('.cke_top').attr('hide','true');
                    }

                });


            },
            /* this version uses the contenteditable version of ckeditor*/
            normal2: function () {
                /* $(document.body).annotator()*/
                $('#form-save-content').on('submit',function () {
                    var $this = $(this);
                    $this.find(':submit').button('loading');
                    $('.cke_button__save').find('.cke_button__save_icon')
                        .attr('style','background-image:url('+'public/img/loading.gif)!important; ' +
                            'background-position:0 0!important;');
                    $.post($this.attr('action'), $this.serialize(), function (data) {
                        if (data.ok) {
                            alert('Saved');
                        }
                    }, 'json')
                        .error(function(){
                            alert('can´t be saved');
                        })
                        .always(function(){
                            $('.cke_button__save').find('.cke_button__save_icon')
                                .removeAttr('style');
                        });
                    return false;
                }).error(function () {
                        $this.find(':submit').button('reset');
                    });
                driver.addAsync('ckeditor', function () {
                    if (typeof CKEDITOR !== "undefined") {
                        var config = {
                            startupFocus : true,
                            toolbarCanCollapse: true,
                            toolbar: [
                                { name: 'savebutton', items: [ 'Savebutton'] },
                                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ],
                                    items: [ 'Find', 'Replace', '-', /*'SelectAll', '-',*/ 'Scayt' ] },
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ],
                                    items: [ 'Bold', 'Italic', /*'Underline', 'Strike', 'Subscript', 'Superscript',*/
                                        '-', 'RemoveFormat' ] },
                                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ],
                                    items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Indent List',
                                        '-', /*'CreateDiv',*/ '-'/*,
                                         'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'*/, '-', 'BidiLtr', 'BidiRtl' ] },
                                { name: 'insert', items: [ 'Image', /* 'Flash', */'Table', 'CreatePlaceholder']},
                                { name: 'styles', items: [ 'Styles', 'Format'/*, 'Blockquote', 'Font', 'FontSize' */]},
                                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                                { name: 'tools', items: [  'ShowBlocks' ]}/*,
                                 {name: 'language', groups:'Language', items:['customlanguage']}*//*,
                                 {
                                 name: 'spellcheck',
                                 items: [ 'jQuerySpellChecker' ]
                                 }*/

                            ],
                            removePlugins: 'forms,flash,floatingspace,iframe,newpage,resize,maximize,smiley,contextmenu,liststyle,tabletools,lite,autosave',//elementspath',
                            //elementspath, is for bottom bar
                            // jqueryspellchecker,
                            extraPlugins: 'savebutton,imagebrowser,backup,placeholder,indentlist,customlanguage',
                            resize_enabled: false,
                            contentsCss: "public/css/custom_ckeditor.css",
                            imageBrowser_listUrl: "book/images/",
                            height: '75vh',
                            magicline_color: '#666',
                            sharedSpaces:{top:'top', bottom:'bottom'},
                            on: {
                                'changeLanguage': function(event){
                                    event.editor.destroy();
                                    config.language = event.data;
                                    $('#editor').ckeditor(config);
                                }
                            },

                            coreStyles_bold	: { element : 'strong', attributes : {'class': 'Bold'} },
                            coreStyles_italic : { element : 'em', attributes : {'class': 'Italic'} },
                            filebrowserImageUploadUrl : 'editor/uploadImage',
                            format_tags: "p;h1;h2;h3;h4;pre;blockquote"
                        };
                        CKEDITOR.inline('editor', config);
//                        $('#editor').ckeditor(config);

                    }
                });
                var user_id =  sessionStorage.user_id,
                    $editor = $('#editor'),
                    zoom = localStorage["zoom-user" + user_id + "-chapter"+ driver.parameters[0]];
                $( "#slider").slider({
                    min:0,
                    max:100,
                    value:zoom !== undefined?zoom: 25,
                    slide:function( event, ui ){
                        $editor.css({'zoom': ui.value/20});
                    },
                    change:function(event,ui){
                        var user_id =  sessionStorage.user_id;
                        localStorage["zoom-user" + user_id + "-chapter"+ driver.parameters[0]] = ui.value;
                    }
                });
            }
        },
        /**
         * Basic configuration for ckeditor
         */
        basicConfig: {
            startupFocus : true,
            extraPlugins: 'customcolorbutton',
            toolbar: [
                {
                    name: 'basicstyles',
                    groups: [ 'basicstyles', 'cleanup' ],
                    items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ]
                },
                {   name: 'paragraph',
                    groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ],
                    items: [ 'JustifyLeft', 'JustifyRight' ]

                },
                { name: 'customcolors', items: [ 'CustomTextColor', 'CustomBGColor' ] }
//                { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
            ],
            removePlugins: 'lite,backup',
            resize_enabled: false,
            colorButton_forecolors : 'ff0000',
            colorButton_backcolors : 'ffff00',
            colorButton_enableMore : false
            /*on: {
             'change': function(event){
             $('.'+event.editor.id).parents('form').attr('data-edited','true');
             }
             }*/

        },
        discussion: {
            view: function(){

                $(function(){
                    $.getJSON('user/getUsersInfo',function(response){
                        sessionStorage.user_id = response.id;
                    });
                });

                $('#form-discussion').on('submit', function(){
                    var $this =$(this);

                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            driver.discussion.drawMessage(data.message);
                            broadcast.emit('new-message', data.message);
                            $this.get(0).reset();
                            $('#message-content').val('');
                            $('.modal').modal('hide');
                        }
                    }, 'json');
                    return false;
                });
                $('body').on('click', '.delete', function(){
                    var $this = $(this);
                    if(confirm('Are you sure?')){
                        $.post($this.attr('href'), {id: $this.data('id')}, function(data){
                            if(data.ok){
                                $this.parents('.deletable').remove();
                            }
                        },'json');
                    }
                    return false;
                });
                driver.addAsync('ckeditor',function(){
                    if (typeof CKEDITOR !== "undefined") {
                        <!--   $('#message-content').ckeditor(driver.basicConfig);-->
                    }
                });

                $('body').on('click','.add-like',function(){
                    var message_id = $(this).data('message_id'),
                        book_id = driver.parameters[0],
                        user_id = sessionStorage.user_id;
                    var data= {
                        message_id:message_id,
                        book_id: book_id
                    }
                    $.post('likes/add_like',data,function(response){
                        if(response.ok){
                            var data ={id:message_id,user_id:user_id};
                            driver.discussion.addLike(data);
                            broadcast.emit('add-like',data);
                        }
                    },'json');
                    return false;
                })
                    .on('click','.add-dislike',function(){
                        var message_id = $(this).data('message_id'),
                            book_id = driver.parameters[0],
                            user_id = sessionStorage.user_id;
                        var data= {
                            message_id:message_id,
                            book_id: book_id
                        }
                        $.post('likes/remove_like',data,function(response){
                            if(response.ok)
                            {
                                var data ={id:message_id,user_id:user_id};
                                driver.discussion.removeLike(data);
                                broadcast.emit('remove-like',data);
                            }
                        },'json');

                        return false;
                    });

            },
            addLike:function(data){
                var comment =  $('.deletable[data-id="'+data.id+'"]');
                if(data.user_id === sessionStorage.user_id){
                    var btn = H.compile($('#btn-dis_like').html());

                    comment.find('.btn-like').html(btn(data));
                }
                var like= comment.find('.likes span');
                like.text(parseInt(like.text())+1);
            },
            removeLike:function(data){
                var comment =  $('.deletable[data-id="'+data.id+'"]');
                if(data.user_id === sessionStorage.user_id){
                    var btn = H.compile($('#btn-like').html());
                    comment.find('.btn-like').html(btn(data));
                }
                var like= comment.find('.likes span');
                like.text(parseInt(like.text())-1);
            },
            getMessageTemplate: function(){
                if(driver.discussion.newMessage==undefined){
                    driver.discussion.newMessage = H.compile($('#new-comment').html());
                }
                return driver.discussion.newMessage
            },
            getCommentsDiv: function(){
                if(driver.discussion.$comments==undefined){
                    driver.discussion.$comments = $('#comments');
                }
                return driver.discussion.$comments
            },
            drawMessage: function(data){
                data.url = 'discussion/delete/';
                driver.discussion.getCommentsDiv().prepend(driver.discussion.getMessageTemplate()(data));
            }

        },
        chapter: {
            review: function(){

                $(function(){
                    $.getJSON('user/getUsersInfo',function(response){
                        sessionStorage.user_id = response.id;
                    });
                });
                driver.addAsync('ckeditor',function(){
                    if (typeof CKEDITOR !== "undefined") {
                        $('#review-text').ckeditor(driver.basicConfig);
                    }
                });

                var $term = $('#add-comment-form').find('[name="term_id"]');
                $('.show-form').on('click', function(){
                    var $this = $(this);
                    $term.val($this.data('term-id'))
                    $('#add-comment-modal').modal('show');

                });

                $('#add-comment-form').on('submit', function(){
                    var $this = $(this);
                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            driver.chapter.drawReview(data.review);
                            console.log(data.review);
                            var $toggler = $('[href="#comments-'+data.review.term_id+'"]');
                            if($toggler.text().trim()=='No comments'){
                                $toggler.text('Expand/Collapse all comments');
                            }
                            broadcast.emit('new-review',data.review);
                            $this.get(0).reset();
                            $('#review-text').val('');
                            $('.modal').modal('hide');
                        }
                    },'json');
                    return false;
                });

                $('.new-approve').on('click',function(){
                    var $this = $(this),
                        data = {
                            term_id:$(this).data('term-id')
                        };

                    $.post('review/new_approve',data,function(resp){
                        if(resp.ok){
                            $this.attr('disabled','disabled');
                            data.user_id = resp.user_id;
                            driver.chapter.plusApprove(data);
                            broadcast.emit('plus-approve',data);
                        }

                    },'json');
                    return false;
                });
                $('.approve-counter').on('click',function(){
                    var $this = $(this);
                    var id = $this.data('term-id');
                    var add = H.compile($('#user-approve').html());
                    $.getJSON('review/list_approve_by_term/'+id)
                        .done(function(data){
                            $('.users-approves').empty();
                            for(var user in data){
                                if (data[user].picture === null)
                                    data[user].picture= 'http://placehold.it/140x140';
                                $('.users-approves').append(add(data[user]));

                            }
                        });
                    $('#modal-approvals').modal('show');

                });
            },
            getReviewTemplate: function(){
                if(driver.chapter.newReview==undefined){
                    driver.chapter.newReview = H.compile($('#new-review').html());
                }
                return driver.chapter.newReview
            },
            drawReview: function(data){
                $('.comments[data-term-id="'+data.term_id+'"]')
                    .prepend(driver.chapter.getReviewTemplate()(data));
            },
            plusApprove: function(data){
                var $counter = $('.approve-counter[data-term-id="'+data.term_id+'"]'),
                    count = +($counter.text());
                if(sessionStorage.user_id === data.user_id){
                    $('.new-approve[data-term-id="'+data.term_id+'"]').attr('disabled','true');
                }

                $counter.text(++count);
            },
            history: function(){
                var $preview = $('#preview-entry');
                $('.view-content').on('click', function(){
                    var $this = $(this);
                    $.get($this.attr('href'), function(resp){
                        $preview.find('.modal-body').html(resp);
                        $preview.modal('show')
                    });
                    return false;
                });
                $('.rollback').on('click', function (){
                    var $this = $(this);
                    $.post($this.attr('href'), function(resp){
                        if(resp.ok){
                            driver.info.html(
                                $(driver.infoTemplate({type: 'success',
                                    text: 'Successfully rolled back'})).hide().fadeIn(500));
                            setTimeout(function () {
                                $('.alert').fadeOut(1500);
                            }, 2000);
                        }
                    }, 'json');
                    return false;
                });
                $('.compare').on('click', function (){
                    var $this = $(this);
                    var $content = $('#compare-modal').modal('show').find('.modal-body').text('fetching...');
                    $.get($this.attr('href'), function(resp){
                        $content.html(resp);
                    });
                    return false;
                });
            }
        }
    }
    /**
     * Controller
     * @type {{view: Function, addLike: Function, removeLike: Function}}
     */
    var topic={
        view: function(){
            $(function(){
                $.getJSON('user/getUsersInfo',function(response){
                    sessionStorage.user_id = response.id;
                });
            });
            var newTopic = H.compile($('#new-topic-template').html()),
                $topics = $('#topics');
            $('#new-topic-form').on('submit', function(){
                var $this =$(this),
                    title = $this.find('[name=topic]').val();

                if(title.length>0){
                    $.post($this.attr('action'), $this.serialize(), function(data){
                        if(data.ok){
                            $topics.prepend(newTopic({id: data.id,
                                title: title,
                                base: driver.urlBase}));

                            var $comments = $('#comments-'+data.id),
                                href='topic/detail/'+data.id;
                            $comments.load(href+' #comments .media', function(){
                                $comments.before($('<a></a>', {'class':'actions',/*'href': href+'/new',*/ text: 'Reply',"data-id":data.id}));
                                // $comments.before($('<a></a>', {'class':'actions','href': href, text: 'View all comments >>'}));

                            });

                            $this.get(0).reset();
                            $('.modal').modal('hide');
                        }
                    }, 'json');
                }
                return false;
            });
            $('.timeago').timeago('refresh');


            $('body').on('click', '.topic-detail', function(){
                var $this = $(this),
                    $comments = $('#comments-'+$this.data('id')),
                    target = $this.data('target');
                $this.find(".loading").attr('src',''+'public/img/loading.gif').show();
                $comments.load(target+' #comments .media', function(){
                    $comments.prepend($('<a></a>', {'class':'actions',/*'href': target+'/new',*/ text: 'Reply',"data-id":$this.data('id')}));
                    /* $comments.prepend($('<a></a>', {'class':'actions','href': target, text: 'View all comments >>'})); */
                    $(".loading").hide();
                    $comments.toggle();

                });
                return false;
            });
            $('body').on('click','.actions',function(){
                var $this = $(this);
                $('.modal #form-discussion').find('#topic-id').val($this.data('id'));
                $('#comment_modal').modal('show');
                return false;
            });

            driver.addAsync('ckeditor',function(){
                if (typeof CKEDITOR !== "undefined") {
                    $('#message-content').ckeditor(driver.basicConfig);
                    $('#message-area').ckeditor(driver.basicConfig);
                }
            });
            var newComment= H.compile($('#new-comment-template').html());
            $('#form-discussion').on('submit', function(){
                var $this =$(this);

                $.post($this.attr('action'), $this.serialize(), function(data){
                    if(data.ok){
                        var topic_id = $this.find('#topic-id').val(),
                            $comments = $('#comments-'+ topic_id);
                        $comments.prepend(newComment(data.comment));
                        $('.counter'+ topic_id).text(parseInt($('.counter'+ topic_id).text())+1);

                        $this.get(0).reset();
                        $('.modal').modal('hide');
                    }
                }, 'json');
                return false;
            });
            $('body').on('click', '.delete', function(){
                var $this = $(this);
                if(confirm('Are you sure?')){
                    $.post($this.attr('href'), {id: $this.data('id')}, function(data){
                        if(data.ok){
                            $this.parents('.comment').remove();
                        }
                    },'json');
                }
                return false;
            })
                .on('click','.add-like',function(){
                    var comment_id = $(this).data('comment_id'),
                        book_id = driver.parameters[0],
                        user_id = sessionStorage.user_id;
                    var data= {
                        comment_id:comment_id,
                        book_id:book_id
                    }
                    $.post('like_comment/add_like',data,function(response){
                        if(response.ok){
                            var data ={id:comment_id,user_id:user_id};
                            driver.topic.addLike(data);
                            broadcast.emit('add-like',data);
                        }
                    },'json');
                    return false;
                })
                .on('click','.add-dislike',function(){
                    var comment_id = $(this).data('comment_id'),
                        book_id = driver.parameters[0],
                        user_id = sessionStorage.user_id;
                    var data= {
                        comment_id:comment_id,
                        book_id: book_id
                    }
                    $.post('like_comment/remove_like',data,function(response){
                        if(response.ok){
                            var data ={id:comment_id,user_id:user_id};
                            driver.topic.removeLike(data);
                            broadcast.emit('remove-like',data);
                        }
                    },'json');
                    return false;
                });
            if(driver.parameters[1]!=undefined){
                $('.modal').modal();
            }
            $('.timeago').timeago('refresh');
        },
        addLike:function(data){
            var comment =  $('.deletable[data-id="'+data.id+'"]');
            if(data.user_id === sessionStorage.user_id){
                var btn = H.compile($('#btn-dis_like').html());
                comment.find('.btn-like').html(btn(data));
            }
            var like= comment.find('.likes span');
            like.text(parseInt(like.text())+1);
        },
        removeLike:function(data){
            var comment =  $('.deletable[data-id="'+data.id+'"]');
            if(data.user_id === sessionStorage.user_id){
                var btn = H.compile($('#btn-like').html());
                comment.find('.btn-like').html(btn(data));
            }
            var like= comment.find('.likes span');
            like.text(parseInt(like.text())-1);
        }
    }

    driver['topic'] = topic;
    driver.init();
})(window.jQuery, Handlebars);