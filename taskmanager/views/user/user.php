<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title><?php echo $names;?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/bootstrap.min.css"/>
    <style type="text/css">
        /*body {
            max-height: 100vh;
            height: 100vh; *//*needed for chrome*//*
            overflow-y: hidden;
        }*/

        body>header {
            height: 10vh;
            background-color: #efefef;
        }
        .logo img{
            max-height: 8vh;
        }

        .content {
            padding-top: 1vh;
            padding-left: 5px;
            padding-right: 5px;
            background-color: #425768;
            min-height: 89vh;
            border: 2px solid #B5AA98;
        }
        /*.grid{
            -moz-column-count: 4;
            -moz-column-gap: 20px;
            -webkit-column-count: 4;
            -webkit-column-gap: 20px;
            column-count: 4;
            column-gap: 20px;
        }*/
        ul.grid{
            margin: 0 8%;
        }
        .grid > li {/* card */
            min-height: 30px;
            line-height: 30px;
            padding: 5px;
            margin: 3px 3px 3px 3px;
            border-radius: 3px;
            border: 3px solid #eddcc8;
            background-color: white;
            <?php echo 'background-color: '.$color.';'?>
            width: 250px;
            float: left;
        }

        li.dependency{
            border-color: gainsboro;
            border-width: 3px;
        }
        .grid li.completed{
            background-color: gainsboro !important;
        }

        .grid li.completed:after{
            content: ' Done';
            font-weight: bold;
            float: right;
        }
        .content header {
            font-family: 'FS Albert Web Regular', Verdana, sans-serif;
            font-size: 18px;
            color: #fff;
            line-height: 2em;
            text-align: center;
        }
    </style>
</head>
<body data-id="<?php echo $id;?>">
<header class="container-fluid">
    <div class="row-fluid">
        <div style="height: inherit; padding: 3vh;" class="pull-left">
        <a href="<?php echo base_url().'taskmanager/'.$bookid.'/';?>">Back to home</a>

        </div>
        <div class="logo pull-right">
            <a href="/">
                <img src="" alt="">
            </a>
        </div>
    </div>
</header>
<div class="content">
    <div class="container-fluid">
        <div class="row-fluid">
            <header><?php echo $names;?></header>
            <ul class="grid unstyled">
            <?php /*foreach ($tasks as $item) :
                if($item['designee_id']==$id']):*/?><!--
                <li><a class="task" href="#"><?php /*echo $item['title'];*/?></a></li>
            --><?php /*endif;
                endforeach;*/?>
            </ul>
        </div>
    </div>
</div>
<script id="card-modal-template" type="text/x-handlebars-template">
    <div class="modal card-modal hide">
        <form id="card" class="form-horizontal modal-form" action="#" method="post">
            <input type="hidden" name="id" value="{{id}}"/>
            <div class="modal-header">
                <h2>{{title}}</h2>
                <h3>Assigned to <em><?php echo $names;?></em></h3>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <textarea rows="5" cols="25" placeholder="Type the description" class="input-xlarge"
                                  name="description" id="description"
                                  required autofocus>{{description}}</textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <label class="checkbox pull-left">
                    <input type="checkbox" name="dependency" id="dependency" disabled="disabled"
                        {{#if dependency}}checked="checked"{{/if}}>Dependency
                </label>
                <div class="pull-right">
                    <input data-id="{{id}}" class="btn btn-primary complete" type="button" value="Complete"/>
                    <input class="btn" type="reset" value="Close" data-dismiss="modal"/>
                </div>
            </div>

        </form>

    </div>
</script>
<script id="task-template" type="text/x-handlebars-template">
    <li data-color="{{color}}" class="{{#if completed}}completed {{/if}}{{#if dependency}}dependency{{/if}}"
    {{#if color}}style="background-color: {{color}}"{{/if}} >
    <a class="task" data-id="{{id}}"> {{title}} </a>
    <!--<a class="pull-right delete" href="task/delete/" data-id="{{id}}"><i class="icon-remove-sign"></i></a>-->
    </li>
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/handlebars.js"></script>
<!--<script type="text/javascript" src="http://192.168.0.100:8080/socket.io/socket.io.js"></script>-->
<script type="text/javascript" src="http://213.108.105.1/socket.io/socket.io.js"></script>
<script type="text/javascript">
    (function($, H){
        var layout={
            body: $('body'),
            grid: $('.grid')
        }
        var controller = {
            tasks: [],
            init: function(){
                this.compile();
                this.load();
                var self = this;
                $('body').on('click', '.task', function(){
                    var task = self.findTask($(this).data('id'), 'id');
                   $(self.cardTemplate(task)).modal().on('hide', function(){
                       var $form = $(this).find('form'), description= $form.find('#description').val();
                       $.post('../task/update/', {"description": description, 'onlyDescription':true, id: task.id}, function(resp){
                           if(resp.ok){
                               task.description = description;
                               broadcast.emit('update-task-info', task)
                           }
                       }, 'json');
                   })
                       .on('click', '.complete', function(){
                           var task = self.findTask($(this).data('id'), 'id');
                           task.description = $('#description').val()
                           $.post('../task/complete/', task, function(resp){
                               if(resp.ok){
                                   task.completed = 1;
                                   self.markAsCompleted(task);
                                   broadcast.emit('complete-task', task)
                               $.post('../phase/testComplete/', {id: task.phase_id},function(resp){
                                       if(!(+resp.equal)){
                                           window.location.href = window.location.href;//TODO: change for ajax
                                       }
                                   });
                                   $('.modal').off('hide').modal('hide');
                               }
                           },'json')
                       });
                })

            },
            compile: function(){
                this.taskItem = H.compile($('#task-template').html());
                this.cardTemplate = H.compile($('#card-modal-template').html());
            },
            load: function(){
                var self = this;
                $.getJSON('../task/actives/', function(data){
                    self.tasks = data;
                    $.each(data, function(i, item){
                        if(+item.designee_id==layout.body.data('id') && (!(+item.completed))){
                            item.completed = +item.completed;
                            item.dependency = +item.dependency;
                            self.addTask(item);
                        }

                    })

                });
            },
            findTask: function(value, property){
                if(property==undefined){
                    property='title';
                }
                var finish = this.tasks.length;
                for(var i = 0; i<finish; i++){
                    if(this.tasks[i][property]==value){
                        return this.tasks[i];
                    }
                }
            },
            addTask: function(data){
                layout.grid.append(this.taskItem(data))
            },
            markAsCompleted: function(task){
                $('.task[data-id="'+task.id+'"]').parents('li').addClass('completed').hide();
            },
            replaceTask: function(task){
                var finish = this.tasks.length;
                for(var i = 0; i<finish; i++){
                    if(this.tasks[i]['id']==task.id){
                        this.tasks[i] = task;
                        break;
                    }
                }
            }
        };

        controller.init();
        var broadcast={
            server: 'http://213.108.105.1',
//            socket : io.connect('http://192.168.0.100:8080'),
            socket : null,
            init: function(){
                var self = this;
                if(!(window.io==undefined)){
                    self.socket = io.connect(self.server);
                    self.socket.on('connect', function () {
                        self.socket.on('new-task', function(data){
                            if(data.designee_id==layout.body.data('id') && data.active){
                                controller.tasks.push(data)
                                controller.addTask(data);
                            }

                        });
                        self.socket.on('update-task-info', function(task){
                            controller.replaceTask(task);
                            var $this = $('.task[data-id="'+task.id+'"]');
                            if(task.dependency){
                               $this.parents('li').addClass('dependency');
                            }else{
                               $this.parents('li').removeClass('dependency');
                            }
                        });
                        self.socket.on('complete-task', function(task){
                            controller.markAsCompleted(task);
                        });
                        self.socket.on('move-task', function(info){
                            var task = controller.findTask(info.ids[0]);
                            task = task==undefined?info.task:task;
                            if(info.active){
                                controller.addTask(task);
                            }else{
                                $('.task[data-id="'+task.id+'"]').parents('li').remove();
                            }
                        });
                        self.socket.on('move-phase', function(){
                            alert('The active phase has moved forward, this page will refresh to show your new tasks');
                            window.location.reload(true);
                        });
                    });
                }

            },
            emit: function(event, data){
                if(this.socket!=undefined){
                    this.socket.emit(event, data);
                }else{
                    console.warn(event+' not sent. Broadcast connection is not stablished');
                }
            }
        };
        broadcast.init();

    })(window.jQuery, Handlebars);
</script>
</body>
</html>