<script type="text/x-handlebars-template" id="term-template">
    <li class="items-terms" data-locked='false'>
        <div class="status"></div>
        <span class="item-list" data-id="{{id}}">{{term}}</span>
        <button class="delete-term close">&times;</button>
        <span class="editor muted" data-id="{{id}}"></span>
    </li>
</script>
<script type="text/x-handlebars-template" id="info-template">
    <div class="alert alert-{{type}}">{{text}}<a class="close" data-dismiss="alert" href="#">&times;</a></div>
</script>

<script type="text/x-handlebars-template" id="section-template">
    <li class="section" data-id="{{id}}" data-order="{{order}}">
        <h3 class="section-name"><span class="name editable editable-click">{{title}}</span>
            <span class="pull-right" >
               <a href="<?php echo base_url('sections/delete_section/'); ?>"
                  class="delete-section" data-id="{{id}}">&times;</a>
            </span>
            <a data-toggle="collapse" data-target="#section-chapters-{{id}}"
               class="accordion-toggle pull-left">&nbsp;</a></h3>

        <div id="section-chapters-{{id}}" class="accordion-body collapse in">
            <ul class="unstyled chapters" data-section-id="{{id}}">
            </ul>
        </div>
    </li>
</script>
<script type="text/x-handlebars-template" id="chapter-template">
    <li class="chapter" data-id="{{id}}">
        <span class="title editable editable-click">{{title}}</span>

        <span class="options pull-right">
            <span class="chapter-status">
                <a href="#" data-user_id="0" data-user="nobody" data-title="{{title}}" data-id="{{status}}" title="">
                    O
                </a>
            </span>
            <span class='chapter-type'>
                <a href="<?php echo base_url() . 'render/chapter/{{id}}'; ?>"
                   class="chapter-contents">{{type}}</a></span>&nbsp;&nbsp;
            <a href="<?php echo base_url(); ?>{{url}}{{id}}">Edit</a>
            <a href="<?php echo base_url();?>chapter/delete_chapter/"  class="delete-chapter" data-id="{{id}}">Delete</a>
            <a href="<?php echo base_url();?>chapter/review/{{id}}">Review</a>
        </span>
    </li>
</script>

<script type="text/x-handlebars-template" id="connected-template">
<!--    <div class="newUserConnected" id="{{id}}" data-original-title="{{userName}}">-->
<!--        <img src="{{imgProfile}}" alt="{{userName}}" width="20" height="20"/>-->
<!--    </div>-->
    <li class="userConnected" role="presentation" data-id={{id}}>
        <div class="status-user" role="menuitem" tabindex="-1" >
            <img src="{{imgProfile}}" alt="{{userName}}" width="20" height="20"/>
            <span>{{userName}}</span>
        </div>
    </li>
</script>

<script type="text/x-handlebars-template" id="status-user-template">
    <li role="presentation">
        <a class="status-user" role="menuitem" tabindex="-1" href="#" data-id={{id}}>
            {{user}}
        </a>
    </li>
</script>

<script type="text/x-handlebars-template" id="status-chapter-template">
    <span class="status" data-user_id="0" data-user="nobody" data-title="create content" data-id="{{status_id}}">
            O
    </span>
</script>

<script type="text/x-handlebars-template" id="status-item-template">
    <li class="status-item" data-id="{{id}}">
        <strong class="status_name">{{title}}</strong>
        <input name="status_title" class="validate[required] edit_status_name" type="hidden" value="{{title}}"/>
        <span>assigned to</span>
    <span class="dropdown">
        <input name="user_id" class="selected-user" type="hidden" value="{{user_id}}"/>
        <a id="dLabel{{id}}" data-user="{{user_id}}" class="chapter-message-user dropdown-toggle" href="#" role="button"  data-toggle="dropdown">
            <span class="name"><strong></strong></span> <b class="caret"></b>
        </a>
        <ul id="menu" class="select-user dropdown-menu" role="menu" aria-labelledby="dLabel{{id}}">

        </ul>
    </span>
        <div class="pull-right">
            <a href="#" class="status-delete" title="delete">&times; </a>
            <input class="status_complete" type="checkbox" name="status" {{status}}>
        </div>
    </li>
</script>
<script type="text/x-handlebars-template" id="new-coauthor-template">
<tr class="deletable">
    <td>{{user}}</td>
    <td><div class="btn-group" data-toggle="buttons-checkbox">
            <button type="button" class="btn btn-mini {{#if contributor}}active{{/if}}" data-user-id="">
                <?php echo $this->lang->line('contributor');?></button>
            <button type="button" class="btn btn-mini {{#if reviewer}}active{{/if}}" data-user-id="">
                <?php echo $this->lang->line('reviewer');?></button>
        </div></td>
    <td><a class="remove" href="<?php echo base_url('book/removeCoAuthor/');?>" data-user-id="{{userid}}">Remove</a></td>
</tr>
</script>

<script id="new-comment" type="text/x-handlebars-template">
<div class="deletable media" data-id="{{id}}">

    <a class="pull-left" href="">
              <img class="media-object" src="<?php echo base_url() . 'user/image/{{user_id}}'; ?>" alt="" width="64px" height="64px"/>
         </a>
    <div class="media-body">
        <div class="pull-right btn-like">
            <button data-message_id="{{id}}" class="btn add-like">
                <i class="icon-thumbs-up"></i>
                <span><?php echo $this->lang->line('like');?></span>
            </button>
        </div>
        <span class="user media-heading"><strong>{{names}}:  </strong></span>
        <p>{{{message}}}</p>
        <div class="actions pull-right"><span class="date">{{created}}</span>&nbsp;-&nbsp;
            <a href="{{url}}" class="delete" data-id="{{id}}">Delete</a>
            &nbsp;
            <span class="likes">
                <i class="icon-thumbs-up"></i>
                <span>
                    0
                </span>
            </span>
        </div>
    </div>
</div>
</script>
<script id="new-topic-template" type="text/x-handlebars-template">
<div class="deletable media" data-id="{{id}}">
    <div class="media-body">
        <div class="title-topic">
            <div class="actions pull-right">

                <span class="date">{{#if created}}{{created}}{{else}}NOW{{/if}}</span>
            </div>
            <a href="{{base}}topic/detail/{{id}}" class="topic-detail">{{{title}}}<img  class="loading" src="" /></a>
        </div>
        <div id="comments-{{id}}" class="comments hide"></div>
    </div>
</div>
</script>
<script id="new-comment-template" type="text/x-handlebars-template">
<div class="deletable media comment" data-id="{{id}}">

    <a class="pull-left" href="">
              <img class="media-object" src="<?php echo base_url() . 'user/image/{{user_id}}'; ?>" alt="" width="64px" height="64px"/>
         </a>
    <div class="media-body">
        <div class="pull-right btn-like">
            <button data-message_id="{{id}}" class="btn add-like">
                <i class="icon-thumbs-up"></i>
                <span><?php echo $this->lang->line('like');?></span>
            </button>
        </div>
        <span class="user media-heading"><strong> {{#if names}}{{names}}{{else}}{{username}}{{/if}}:  </strong></span>
        <p>{{{comment}}}</p>
        <div class="actions pull-right"><span class="date">{{created}}</span>&nbsp;-&nbsp;
            <a href="<?php echo base_url('comment/delete/') ;?>" class="delete" data-id="{{id}}">Delete</a>
            &nbsp;
            <span class="likes">
                <i class="icon-thumbs-up"></i>
                <span>
                    0
                </span>
            </span>
        </div>
    </div>
</div>
</script>
<script id="new-review" type="text/x-handlebars-template">
    <div class="deletable media">
        <span class="pull-left" >
            <img class="media-object" src="<?php echo base_url() . 'user/image/{{user_id}}'; ?>" alt="" width="48px" height="48px"/>
        </span>
        <div class="media-body">

            <span class="user media-heading"><strong>{{#if names}}{{names}}{{else}}{{username}}{{/if}}:   </strong></span>
            <p>{{{comment}}}</p>
            <div class="actions"><!--<span class="date">{{created}}</span>--></div>

        </div>
    </div>
</script>

<script id="btn-like" type="text/x-handlebars-template">
    <button class="btn add-like" data-message_id="{{id}}">
        <i class="icon-thumbs-up"></i>
        <span><?php echo $this->lang->line('like');?></span>
    </button>
</script>

<script id="btn-dis_like" type="text/x-handlebars-template">
    <button class="btn add-dislike" data-message_id="{{id}}">
        <i class="icon-thumbs-down"></i>
        <span><?php echo $this->lang->line('dislike');?></span>
    </button>
</script>

<script id="user-approve" type="text/x-handlebars-template">
    <li class="user-approve">
        <div class="media">
            <span class="approve-user-photo pull-left">
                <img class="media-object" src="{{picture}}" alt="{{username}}" width="60" height="60"/>
            </span>
            <div class="media-body">
                <span class="approve-name media-heading media-heading">{{names}}</span>
            </div>
        </div>
    </li>
</script>

<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/bootstrap-editable.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/jquery.validationEngine-en.js"></script>-->


<!-- File Uploader -->
<!--<script src="<?php /*echo base_url(); */?>public/js/jquery.ui.widget.js"></script>
<script src="<?php /*echo base_url(); */?>public/js/jquery.iframe-transport.js"></script>
<script src="<?php /*echo base_url(); */?>public/js/jquery.fileupload.js"></script>-->

<!-- next script are not jquery dependent-->
<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/d3.v3.min.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/handlebars.js"></script>
<script type="text/javascript" src="<?php /*echo base_url(); */?>public/js/timeago.js"></script>-->

<script type="text/javascript" src="<?php echo base_url();?>public/js/pubsweet-libs.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/pubsweet.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--public/js/pubsweet.min.js"></script>-->

<script type="text/javascript" src="http://booksprints.net:8080/socket.io/socket.io.js" async onload="broadcast.init()"></script>
<!--<script type="text/javascript" src="http://pubsweet.local:8080/socket.io/socket.io.js" async onload="broadcast.init()"></script>-->
<script src="<?php echo base_url(); ?>public/ckeditor/ckeditor.js" async onload="driver.execAsync('ckeditor');"></script>
</body>
</html>