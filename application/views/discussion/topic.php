<div class="container pubsweet-main">
    <div class="row-fluid">
        <div class="offset1 span10">
           <h3>
               <button class="btn btn-addComment pull-right" data-target="#topic_modal" data-toggle="modal">
                      <i class="icon-plus"></i>
                      <span><?php echo $this->lang->line('topic');?></span>
                  </button>
               <?php echo $this->lang->line('discussion'); ?>
           </h3>
        </div>
    </div>
    <div class="media-list" id="topics">
        <?php foreach ($topics['topics'] as $theme) :?>
            <div class="deletable media">
                <div class="media-body">
                    <div class="title-topic">
                        <div class="actions pull-right">
                            <span class="responses">
                           <span class="counter<?php echo $theme['id']; ?>">
                               <?php echo count($theme['comments']) ?></span> Reponses
                           </span>

                            <br>
                            <span class="date">

                                <time class="timeago" datetime="<?php echo  $theme['created'] ;?>" title="">about 8 hours ago</time>
                                </span>


                        </div>

                        <a data-target="<?php echo base_url('topic/detail/'.$theme['id']);?>" class="topic-detail "
                           data-id="<?php echo $theme['id'];?>" data-toggle="collapse" href="#comments-<?php echo $theme['id'];?>">
                            <?php echo $theme['topic'];?> <img  class="loading" src="" /></a>

                    </div>
                    <div id="comments-<?php echo $theme['id'];?>" class="comments hide">
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>

</div>

<div class="modal hide fade" id="topic_modal">
    <form class="form-vertical modal-form" action="<?php echo base_url('topic/add'); ?>" method="post"
          id="new-topic-form">
        <div class="modal-header">
            <input type="hidden" name="book_id" value="<?php echo $book["book"]['id']?>"/>
            <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id');?>"/>

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php echo $this->lang->line('add-new-topic');?></h3>
        </div>
        <div class="modal-body" id="chapter-modal-body">
            <div class="control-group">
                <label class="control-label" for="topic-content">Topic</label>
                <div class="controls"><input type="text" name="topic" id="topic-content"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="topic-content">Comment</label>
                <div class="controls">
                    <textarea  name="comment" id="message-content" autofocus="autofocus"
                               required="required"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></a>
            <button type="submit" class="btn btn-primary">
                <?php echo $this->lang->line('post-topic');?>
            </button>
        </div>
    </form>
</div>


<div class="modal hide fade" id="comment_modal">
    <form class="form-vertical modal-form" action="<?php echo base_url('comment/add/'); ?>" method="post"
          id="form-discussion">
        <div class="modal-header">
            <input type="hidden" name="topic_id" id="topic-id" value="<?php //echo $topic["topic"]['id'] ?>"/>
            <!-- <input type="hidden" name="book_id" value="<?php echo $book["book"]['id']?>"/> -->
            <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id'); ?>"/>

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add New Comment</h3>
        </div>
        <div class="modal-body" id="chapter-modal-body">
            <div class="control-group">
                <div class="controls">
                    <textarea class="" name="comment" id="message-area" cols="20" rows="5"
                              autofocus="autofocus" required="required"
                              placeholder="<?php echo $this->lang->line('say-something'); ?>">
                    </textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></a>
            <button type="submit" class="btn btn-primary">
                <?php echo $this->lang->line('post-reply'); ?>
            </button>
        </div>
    </form>
</div>



