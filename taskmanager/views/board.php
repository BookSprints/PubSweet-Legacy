<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Manager</title>
    <link href="<?php echo base_url(); ?>public/css/bootstrap.min.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="style.css"/>-->
    <style type="text/css">
            /* LAYOUT*/
        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Regular.otf) format("opentype");
        }

        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Bold.otf) format("opentype");
            font-weight: bold;
        }

        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Italic.otf) format("opentype");
            font-style: italic;
        }

        body {
            max-height: 100vh;
            height: 100vh; /*needed for chrome*/
            overflow-y: hidden;
        }

        body>header {
            height: 50px;
            background-color: #efefef;
            clear: both;
        }

        .logo img {
            max-height: 8vh;
        }

        .content {
            padding-top: 1vh;
            padding-left: 5px;
            padding-right: 5px;
            height: 89vh;
            background-color: #425768;
            border: 2px solid #B5AA98;
        }

        .board-wrapper {
            max-width: 80vw;
            width: 80vw;
            min-width: 80vw;
            overflow-x: auto;
            overflow-y: hidden;
            display: inline-block;
            position: absolute;
            left: 0;
        }

        .board {
            max-height: 89vh;
            display: inline-block;
        }

        .user-wrapper {
            display: inline;
            width: 20vw;
            min-width: 20vw;
        }

            /* END LAYOUT */

        .panel {
            border: 2px solid #B5AA98;
            border-radius: 4px 4px 4px 4px;
            position: relative;
            height: 80vh;
            background-color: gainsboro;
            width: 100%;
        }

        .panel.active {
            background-color: white;
            -webkit-transition: background-color 800ms linear;
            -moz-transition: background-color 800ms linear;
            -o-transition: background-color 800ms linear;
            -ms-transition: background-color 800ms linear;
            transition: background-color 800ms linear;
        }

        .user-wrapper .panel {
            background-color: #eddcc8; /* color from plosone*/
        }

        .panel .list {
            height: 70vh;
            max-height: 70vh;
            overflow-y: auto;
            overflow-x: hidden;
            /*display: table-row;*/
        }

        .list > li {
            /* card */
            min-height: 30px;
            background-color: white;
            line-height: 30px;
            padding: 5px;
            margin: 3px 3px 3px 3px;
            border-radius: 3px;
            border: 1px solid #bbb;
        }

        li.dependency {
            border-width: 3px;
        }

        li.completed {
            background-color: gainsboro !important;
            padding-right: 4em;
            position: relative;
        }

        li.completed:after {
            /*content: ' Done';*/
            content: ' \2714';
            font-weight: bold;
            right: 3px;
            position: absolute;
            top: 0.5em;
            color: green;
        }

        .panel footer {
            padding: 5px;
            bottom: 0;
            position: absolute;
            width: -webkit-calc(100% -10px);
            width: -moz-calc(100% -10px);
        }

        .span3 header {
            font-family: 'cabin', Verdana, sans-serif;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            line-height: 2em;
        }

        .board li {
            cursor: move;
        }

        #add-task-form .modal-body {
            overflow-y: inherit;
        }

        .card-modal h1 {
            font-family: 'cabin', Verdana, sans-serif;
        }

        .card-modal .form-horizontal .controls {
            margin-left: 50px;
        }

            /* buttons */
        .btn-primary {
            font-family: Arial, "sans serif";
            background-color: #7280af;
            background-image: linear-gradient(to bottom, #7280af, #4D5677);
            border-color: #7280af #7280af #7280af;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        }

        .btn-primary:hover, .btn-primary:focus {
            background: #3c63af;
            color: #fff;
            text-decoration: none;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }

            /* end buttons */

            /* for drag an drop with jqueryui*/
        .drop-hover {
            outline: gray dashed 3px;
            outline-offset: -4px;
        }

        .active .drop-hover {
            outline-color: gainsboro;
        }

        #list {
            height: 100px;
            margin-right: 0;
        }
        .navbar{
            margin-bottom: 0;
        }
        .navbar-inner {
            border: none;
            background-image: none;
            background-color: #efefef;
            border: none;
            webkit-box-shadow: none;
            box-shadow: none;
        }
        .navbar .nav>li>a{
            padding: 15px;
        }
        .brand {
            /*font-family: 'libre-baskerville', serif;*/
            font-family: 'myriad', serif;
        }

            /*END D&D*/
    </style>
</head>
<body>
<header class="container-fluid">

    <div class="row-fluid">
        <div class="pull-left">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="nav"><a class="brand" href="<?php echo base_url(); ?>">LEXICON</a></div>
                <form class="navbar-form pull-left">
                <button class="btn btn-primary" data-target="#add-phase-modal" data-toggle="modal" type="button">Add Phase
                </button>
                <a class="btn btn-danger" href="manager/ixpoloa">Reset</a>
                </form>
            </div></div>
        </div>

        <div class="pull-right">
            <?php
            $id = $this->session->userdata('DX_user_id');
            if (!empty($book['id'])):  ?>
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav">

                            <?php if (isset($book)): ?>
                                <li>
                                    <a href="<?php echo base_url(
                                        ) . 'book/tocmanager/' . $book['id'] ?>"><?php echo $book['title']; ?></a>
                                </li>
                            <?php endif; ?>
                            <li class="active"><a href="<?php echo base_url() . 'taskmanager/' . $book['id'] . '/'; ?>">Task
                                    Manager</a></li>
                            <li><a href="<?php echo base_url() . 'console/' . $book['id'] . '/' ?>" target="_blank">Console</a>
                            </li>
                            <li>
                                <a id="logout"
                                   href="<?php echo base_url(); ?>auth/logout">
                                    Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</header>
<div class="content">
    <div class="board-wrapper">
        <div class="board"></div>
    </div>
    <div class="user-wrapper">
        <div class="span3 pull-right">
            <header class="text-center">People</header>
            <div class="panel">

                <ul class="unstyled list" id="usernames">

                </ul>

                <footer>
                    <button class="btn pull-right btn-primary" data-target="#add-people-modal" data-toggle="modal">Add
                    </button>
                    <div class="clearfix"></div>
                </footer>
            </div>
        </div>
    </div>
</div>
<div class="modal hide" id="add-phase-modal">
    <form id="add-phase-form" class="form-horizontal modal-form"
          action="<?php echo base_url() . 'taskmanager/phase/add/'; ?>" method="post">
        <input type="hidden" name="book_id" id="book" value="<?php echo $book['id']; ?>"/>

        <div class="modal-body">
            <input class="input-xlarge" type="text" name="phase" id="phase-input"
                   placeholder="Phase name" required autofocus>
        </div>
        <div class="modal-footer">
            <input class="btn" type="reset" value="Close" data-dismiss="modal"/>
            <input class="btn btn-primary" type="submit" value="OK"/>
        </div>
    </form>
</div>
<div class="modal hide" id="add-task-modal">
    <form id="add-task-form" class="form-horizontal modal-form"
          action="<?php echo base_url() . 'taskmanager/task/add/'; ?>" method="post">
        <div class="modal-body">
            <div class="control-group"><input class="input-xlarge" type="text" name="task" id="task-name"
                                              placeholder="Task name" required autofocus></div>
            <div class="control-group">
                <input class="input-xlarge" type="text" name="assigne" id="assigne"
                       placeholder="Assigned to" autocomplete="off">
            </div>

        </div>
        <div class="modal-footer">
            <input class="btn" type="reset" value="Close" data-dismiss="modal"/>
            <input class="btn btn-primary" type="submit" value="OK"/>
        </div>
    </form>
</div>
<div class="modal hide" id="add-people-modal">
    <form id="add-people-form" class="modal-form" action="<?php echo base_url() . 'taskmanager/user/add/'; ?>"
          method="post" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="row-fluid">
                <div class="span8">
                    <input class="input-xlarge" type="text" name="people" id="people-input"
                           placeholder="Add name" required autofocus>

                    <div>
                        <input class="input-xlarge " type="text" id="inputemail" name="email" placeholder="Email">

                        <div class="controls">
                            <input type="file" id="files" name="files"/>
                        </div>
                    </div>

                </div>
                <div class="span4">
                    <img src="" id="list" style="width:200px; height:200px"/>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input class="btn" type="reset" value="Close" data-dismiss="modal"/>
            <input class="btn btn-primary" type="submit" value="OK"/>
        </div>

    </form>

</div>
<!-- TODO: precompile these templates-->
<script id="phase-template" type="text/x-handlebars-template">
    <div class="span3">
        <header class="text-center">{{name}}</header>
        <div class="panel {{#if active}}active{{/if}}">
            <ul class="unstyled list" data-id="{{id}}">
            </ul>
            <footer>
                <div class="pull-right">
                    <a class="btn btn-danger delete" href="phase/delete/" data-id="{{id}}"><i
                            class="icon-remove-sign"></i></a>
                    <button class="btn add-task btn-primary"
                            data-target="#add-task-modal" data-toggle="modal">Add
                    </button>
                </div>
                <div class="clearfix"></div>
            </footer>
        </div>
    </div>
</script>
<script id="user-template" type="text/x-handlebars-template">
    <li data-id="{{id}}"
    {{#if color}}style="background-color: {{color}};"{{/if}}>
    <img src="{{#if picture}}{{picture}}{{else}}http://placehold.it/20x20{{/if}}"
         width="20" height="20"
         alt="img"/> <a href="{{{url}}}">{{#if names}}{{names}}{{else}}{{username}}{{/if}}</a>
    </li>
</script>
<script id="task-template" type="text/x-handlebars-template">
    <li class="{{#if completed}}completed {{/if}}{{#if dependency}}dependency{{/if}}">
        <a class="task" data-id="{{id}}"> {{title}} </a>
        <!--<a class="pull-right delete" href="task/delete/" data-id="{{id}}"><i class="icon-remove-sign"></i></a>-->
    </li>
</script>
<script id="card-modal-template" type="text/x-handlebars-template">
    <div class="modal card-modal hide">
        <form id="card" class="form-horizontal modal-form" action="#" method="post">
            <input type="hidden" name="id" value="{{id}}"/>

            <div class="modal-header">
                <h2>{{title}}</h2>

                <h3>Assigned to <em>{{user.names}}</em></h3>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <textarea rows="5" cols="25" placeholder="Text Field..." class="input-xlarge"
                                  name="description" id="description"
                                  required autofocus>{{description}}</textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <label class="checkbox pull-left">
                    <input type="checkbox" name="dependency" id="dependency" {{#if
                    dependency}}checked="checked"{{/if}}>Dependency
                </label>

                <div class="pull-right">
                    <input data-id="{{id}}" class="btn btn-primary complete" type="button" value="Complete"/>
                    <input class="btn" type="reset" value="Close" data-dismiss="modal"/>
                </div>
            </div>

        </form>
    </div>
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-2.0.2.min.js"></script>
<script src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/handlebars.js"></script>
<!--<script type="text/javascript" src="http://192.168.0.100:8080/socket.io/socket.io.js"></script>-->
<script type="text/javascript" src="http://213.108.105.1/socket.io/socket.io.js"></script>
<!-- TODO: minify-->
<script type="text/javascript">

function addimage(evt) {
    var file = evt.target.files[0]; // FileList object

    // Obtenemos la imagen del campo "file".
//                  for (var i = 0, f; f = files[i]; i++) {
    //Solo admitimos imágenes.
    if (!file.type.match('image.*')) {
        return;
    }

    var reader = new FileReader();

    reader.onload = (function (theFile) {
        return function (e) {
            // Insertamos la imagen
            $("#list").attr({
                'src': e.target.result
            });

            /*$("#list").append($('<img></img>',{'class':'thumb',src: e.target.result, title: theFile.name}));*/
        };
    })(file);

    reader.readAsDataURL(file);
//                  }
}

document.getElementById('files').addEventListener('change', addimage, false);

//TODO: FUTURE: convert to cofeescript
(function ($, H) {
    "use strict";
    var layout = {
        board: $('.board'),
        people: $('#usernames')
    };
    var phaseUI = {
        find: function (id) {
            return $('.list[data-id="' + id + '"]');
        },
        findParent: function (id) {
            return this.find(id).parents('.panel')
        }
    }
    var tasksUI = {
        findLastCompleted: function (phaseId) {
            return phaseUI.find(phaseId).find('li.completed').last();
        }
    }
    //TODO: create classes for UI e.g. PhaseUI
    var manager = {
//        baseUrl: 'http://localhost/pubsweet/',
        baseUrl: 'http://pubsweet.booksprints.net/',
        draggableOptions: {revert: 'invalid', tolerance: 'fit',
            connectToSortable: '.board .list'},
//            droppableOptions: {hoverClass: "drop-hover", scope: 'tasks'},
        sortableOptions: {connectWith: '.board .list', revert: true,
            receive: function (event, ui) {
//                        console.log(ui);
                if (ui.sender != this) {
                    var id = ui.item.find('.task').data('id'),
                        phase = $(this).data('id'),
                        info = {ids: [id],
                            phase: phase,
                            active: phaseUI.findParent(phase).hasClass('active')
                        }
                    $.post(manager.baseUrl+'taskmanager/task/move', info, function (data) {
                        if (!data.ok) {
                            $(ui.sender).append($(ui.item));
                        } else {
                            info.task = manager.findTask(id, 'id');
                            broadcast.emit('move-task', info)
                        }
                    }, 'json');
                }
            }
        },
        users: [],
        usedColors: [],
        init: function () {
            var self = this;
            this.bookId = +(window.location.href.replace(this.baseUrl + 'taskmanager/', '')
                .replace('/', ''));
            this.compile();
            this.load();
            $('#assigne').typeahead(
                {   minLength: 2,
                    source: function (query, process) {
                        var names = [];
                        $.each(self.users, function (i, item) {
                            names.push(item.names);
                        });
                        process(names);
                    },
                    updater: function (item) {
//                                $('#assigne').val(JSON.parse(item.name);
//                                console.log(item);
                        return item;
                    }

                });
            $('#add-phase-form').on('submit', function () {
                var $this = $(this),
                    book_id = $this.find('#book').val(),
                    data = {name: $this.find('#phase-input').val(), book_id: book_id, active: !!$('.panel.active').length ? 0 : 1};
                $.post($this.attr('action'), data, function (resp) {
                    if (resp.ok) {
                        data.id = resp.id;
                        self.addNewPhase(data);
                        broadcast.emit('new-phase', data);
                        self.updateDroppables();
                        $('.modal').modal('hide');
                        $this.get(0).reset();
                    }
                }, 'json');

                return false;
            });
            $('body').on('click', '.add-task',function () {
                $(this).parents('.panel').addClass('waiting');
            }).on('click', '.task', function () {
                    var $this = $(this), task = self.findTask($this.data('id'), 'id');
                    $(self.cardTemplate(task)).modal().on('hide', function () {
                        var $form = $(this).find('form');
                        $.post(manager.baseUrl+'taskmanager/task/update/', $form.serialize(), function (resp) {
                            if (resp.ok) {
                                task.description = $form.find('#description').val();
                                task.dependency = $form.find('#dependency').is(':checked');
                                if (task.dependency) {
                                    $this.parents('li').addClass('dependency');
                                } else {
                                    $this.parents('li').removeClass('dependency');
                                }
                                broadcast.emit('update-task-info', task)
                            }
                        }, 'json');
                    });
                })
                .on('click', '.complete', function () {
                    var task = self.findTask($(this).data('id'), 'id');
                    task.description = $('.modal.in #description').val();
                    task.dependency = $('.modal.in #dependency').is(':checked');
                    $.post(manager.baseUrl+'taskmanager/task/complete/', task, function (resp) {
                        if (resp.ok) {
                            $('.modal').off('hide').modal('hide');
                            broadcast.emit('complete-task', task)
                            self.completeTask(task);
                        }
                    }, 'json');
                })
                .on('click', '.delete', function () {
                    var $this = $(this);
                    if (confirm("Sure?")) {
                        $.post($this.attr('href'), {id: $this.data('id')}, function (data) {
                            if (data.ok) {
                                $this.parents('.span3').remove();
                                broadcast.emit('remove-phase', $this.data('id'));


                            }
                        }, 'json')
                    }
                    return false;
                });
            $('#add-task-modal').on('hidden', function () {
                $('.panel.waiting').removeClass('waiting')
            });
            $('#add-task-form').on('submit', function () {
                var $this = $(this),
                    $panel = $('.panel.waiting'),
                    $phase = $panel.find('.list'),
                    assigne = $this.find('#assigne').val(),
                    user = self.findUser(assigne),
                    taskData = {
                        title: $this.find('#task-name').val(),
                        'phase_id': $phase.data('id'),
                        /*'designee_id': user==undefined?0:user.id*/
                        dependency: 1,//default
                        completed: 0,//default
                        description: '',//default
                        active: $panel.hasClass('active')
                    },
                    successAdd = function (resp) {
                        if (resp.ok) {
                            taskData.id = resp.id;
                            self.tasks.push(taskData)
                            self.addNewTask(taskData, $phase);
                            broadcast.emit('new-task', taskData);
                            self.updateDraggables();
                            $('.modal').modal('hide');
                            $this.get(0).reset()
                        }
                    }
                if (assigne.trim().length > 0 && user == undefined) {
                    var data = {names: assigne,
                        color: manager.chooseColor()};
                    $.post(manager.baseUrl+'taskmanager/user/add/', data,function (resp) {
                        if (resp.ok) {
                            data.id = resp.id;
                            self.users.push(data);
                            self.addNewUser(data);
                            broadcast.emit('new-user', data);
                            user = data;
                            taskData.user = user;
                            taskData.designee_id = data.id
                            $.post($this.attr('action'), taskData, successAdd, 'json');
                        }
                    }, 'json').fail(function () {
                            alert('fail to add new user');
                        });
                } else {
                    taskData.user = user;
                    taskData.designee_id = user == undefined ? 0 : user.id;
                    $.post($this.attr('action'), taskData, successAdd, 'json');
                }

                return false;
            });
            $('#add-people-form').on('submit', function () {
                var $this = $(this);
                var data = {names: $this.find('#people-input').val(),
                    email: $this.find('#inputemail').val(),
                    color: manager.chooseColor(),
                    picture: $this.find('#list').attr('src')};
                $.post($this.attr('action'), data,function (resp) {
                    if (resp.ok) {
                        data.id = resp.id;
                        self.users.push(data);
                        self.addNewUser(data);
                        broadcast.emit('new-user', data);
                        $('.modal').modal('hide');
                        $this.get(0).reset()
                    }
                }, 'json').fail(function () {
                        alert('happening');
                    });
                return false;
            });
            $('.modal').on('shown', function () {
                $(this).find('[autofocus]').focus();
            });
        },
        chooseColor: function () {

            var colors = ['azure', 'burlywood', 'navajowhite', 'honeydew', 'yellow', 'coral', 'linen', 'skyblue',
                'mistyrose', 'rosybrown', 'darkkhaki', 'palegreen', 'mediumaquamarine', 'peachpuff', 'powderblue',
                'lightsalmon', 'silver', 'thistle', 'chartreuse', 'aquamarine'];
            var num = 0;
            if (manager.usedColors.length >= 19) {
                return '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);//random color
            } else {
                var extraStop = 0;//extra condition when most colors are used, could be in an infinite loop
                do {
                    num = Math.floor(Math.random() * (colors.length - 1));
                    ++extraStop;
                    if (extraStop > 10) {
                        return '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);
                    }
                } while (manager.usedColors.indexOf(colors[num]) > -1)
                return colors[num];
            }

        },
        compile: function () {
            this.newPanel = H.compile($('#phase-template').html());
            this.newUser = H.compile($('#user-template').html());
            this.newTask = H.compile($('#task-template').html());
            this.cardTemplate = H.compile($('#card-modal-template').html());
        },
        load: function () {
            var self = this;
            $.getJSON(manager.baseUrl + 'taskmanager/manager/all/' + manager.bookId,function (data) {
                if (data.ok) {
                    self.users = data.users;
                    for (var i = 0; i < data.users.length; i++) {
                        self.addNewUser(data.users[i]);
                    }
                    for (var j = 0; j < data.phases.length; j++) {
                        self.addNewPhase(data.phases[j]);
                    }
                    self.tasks = data.tasks;
                    for (var k = 0; k < data.tasks.length; k++) {
                        data.tasks[k].dependency = +data.tasks[k].dependency;
                        data.tasks[k].completed = +data.tasks[k].completed;
                        var user = self.findUser(data.tasks[k].designee_id, 'id');
                        if (user != undefined) {
                            if (!data.tasks[k].completed) {
                                data.tasks[k].color = user.color;
                            }
                            data.tasks[k].user = user;
                        }

                        self.addNewTask(data.tasks[k], phaseUI.find(data.tasks[k]['phase_id']));
                    }
                    self.updateDroppables();
                    self.updateDraggables();
                } else {
                    alert('error');
                }
            }).fail(function () {
                    alert('failing');
                });

        },
        updateDroppables: function () {
            var self = this;
//                $('.ui-droppable').droppable('disable');
//                $('.ui-sortable').sortable('disable');
            layout.board.find('.list')/*.droppable(self.droppableOptions)*/
                .sortable(self.sortableOptions);
            self.updateDraggables();
        },
        updateDraggables: function () {
//                $('.ui-draggable').draggable('disable');
            layout.board.find('.board .list li').filter(':not(".ui-draggable")').draggable(manager.draggableOptions);
        },
        addNewPhase: function (data) {
            data.active = !!parseInt(data.active);//conversion to boolean, handlebarsjs doesn't consider 0 as false
            layout.board.css('width', (parseInt(layout.board.width()) + 250) + 'px')
                .append(this.newPanel(data));
        },
        phaseTestActive: function (id) {
            var $phase = phaseUI.find(id),
                $panel = $phase.parents('.panel.active'),
                self = this;
            if ($panel.length > 0) {
                if ($phase.find('li.dependency').filter(':not(.completed)').length < 1) {
                    $.post(manager.baseUrl+'taskmanager/phase/desactivate/', {'id': id}, function (data) {
                        if (data.ok) {
                            $panel.removeClass('active');
                            if (data.newId != undefined) {
                                var nextPhase = phaseUI.find(data.newId);
                                nextPhase.parents('.panel').addClass('active');
                                self.moveNotDependencies($phase, nextPhase);
                                broadcast.emit('move-phase');
                            }
                        }
                    }, 'json');
                }
            }
        },
        addNewTask: function (data, $phase) {
            $phase.append(this.newTask(data));
        },
        /**
         * Finds task object with corresponding value and property

         * @param value
         * @param property
         * @returns {*}
         */
        findTask: function (value, property) {
            if (property == undefined) {
                property = 'title';
            }
            var finish = this.tasks.length;
            for (var i = 0; i < finish; i++) {
                if (this.tasks[i][property] == value) {
                    return this.tasks[i];
                }
            }
        },
        replaceTask: function (task) {
            var finish = this.tasks.length;
            for (var i = 0; i < finish; i++) {
                if (this.tasks[i]['id'] == task.id) {
                    this.tasks[i] = task;
                    break;
                }
            }
        },
        moveNotDependencies: function ($currentPhase, $nextPhase) {
            var $items = $currentPhase.find('li:not(".dependency")').filter(':not(".completed")'),
                taskIds = [];
            $.each($items, function (i, item) {
                taskIds.push($(item).find('.task').data('id'));
            });
            $.post(manager.baseUrl+'taskmanager/task/move/', {ids: taskIds, phase: $nextPhase.data('id')}, function (data) {
                if (data.ok) {
                    $nextPhase.append($items);
                }
            }, 'json');

        },
        completeTask: function (task) {
            task.completed = 1;
            var $last = tasksUI.findLastCompleted(task.phase_id),
                $thisTask = $('.task[data-id="' + task.id + '"]').parents('li').addClass('completed');
            if ($last != undefined) {
                $thisTask.insertBefore(phaseUI.find(task.phase_id).find('li').first());
            } else if ($last != $thisTask) {
                $thisTask.insertAfter($last);
            }

            this.phaseTestActive(task.phase_id);
        },
        addNewUser: function (data) {
            manager.usedColors.push(data.color);
            data.url = manager.baseUrl + 'taskmanager/u/' + manager.bookId + '/' + encodeURIComponent(data.username);
            layout.people.append(this.newUser(data))
        },
        findUser: function (value, property) {
            if (property == undefined) {
                property = 'names';
            }
            var finish = this.users.length;
            for (var i = 0; i < finish; i++) {
                if (this.users[i][property] == value) {
                    return this.users[i];
                }
            }
        }
    }
    manager.init();
    var broadcast = {
//            server: 'http://192.168.0.100:8080',
        server: 'http://213.108.105.1',
        socket: null,
//            socket : io.connect('http://213.108.105.1'),
        init: function () {
            var self = this;
            if (!(window.io == undefined)) {
                self.socket = io.connect(self.server);
                self.socket.on('connect', function () {
                    self.socket.on('new-task', function (data) {
                        manager.tasks.push(data.task)
                        manager.addNewTask(data, phaseUI.find(data.phase_id));
                    });
                    self.socket.on('new-phase', function (data) {
                        manager.addNewPhase(data);
                    });
                    self.socket.on('new-user', function (data) {
                        manager.users.push(data);
                        manager.addNewUser(data);
                    });
                    self.socket.on('remove-phase', function (id) {

                        phaseUI.find(id).parents('.span3').remove();
                    });
                    self.socket.on('update-task-info', function (task) {
                        manager.replaceTask(task);
                        var $this = $('.task[data-id="' + task.id + '"]');
                        if (task.dependency) {
                            $this.parents('li').addClass('dependency');
                        } else {
                            $this.parents('li').removeClass('dependency');
                        }
                    });
                    self.socket.on('complete-task', function (task) {
                        manager.completeTask(task);
                    });
                    self.socket.on('move-task', function (info) {
                        phaseUI.find(info.phase)
                            .append($('.task[data-id="' + info.ids[0] + '"]').parent('li'));
                    })
                });
            }

        },
        emit: function (event, data) {
            if (this.socket != undefined) {
                this.socket.emit(event, data);
            } else {
                console.warn(event + ' not sent. Broadcast connection is not stablished');
            }
        }
    };
    broadcast.init();
})(window.jQuery, Handlebars);
</script>
</body>

</html>