<div class="container pubsweet-main">
    <br/><br/>
    <div class="row-fluid">
        <div class="offset2 span8">
            <button class="btn btn-addComment pull-right" data-target="#discussion_modal" data-toggle="modal">
                <i class="icon-plus"></i>
                <span><?php echo $this->lang->line('contribute');?></span>
            </button>
        </div>
    </div>
    <div class="row-fluid">
        <div class="offset2 span8">
            <div class="unstyled media-list" id="comments">
                <?php foreach ($messages['messages'] as $item) :?>
                <div class="deletable media" data-id="<?php echo $item['id']?>">
                    <div class="pull-right btn-like">
                        <?php
                        $user_has_like = false;
                        foreach($item['likes'] as $like){
                            if($this->session->userdata('DX_user_id') == $like['user_id'])
                                $user_has_like = true;
                        }
                        if(!$user_has_like):
                        ?>
                        <button class="btn add-like" data-message_id="<?php echo $item['id']?>">
                            <i class="icon-thumbs-up"></i>
                            <span><?php echo $this->lang->line('like');?></span>
                        </button>
                        <?php
                        else:
                        ?>
                        <button class="btn add-dislike" data-message_id="<?php echo $item['id']?>">
                            <i class="icon-thumbs-down"></i>
                            <span><?php echo $this->lang->line('dislike');?></span>
                        </button>
                         <?php
                            endif;
                         ?>
                    </div>

                      <a class="pull-left" href="">
                          <img class="media-object" src="<?php echo $item['picture'];?>" alt="" width="64px" height="64px"/>
                       </a>
                    <div class="media-body">

                         <span class="user media-heading"><strong><?php echo $item['names'];?></strong></span> &nbsp; &nbsp;
                        <p><?php echo $item['message'];?></p>

                    <div class="actions pull-right">
                        <span class="date">
                            <?php echo $item['created'];?>
                        </span>

                        <?php if($item['user_id']==$this->session->userdata('DX_user_id')):?>
                        &nbsp;-&nbsp;
                        <a href="<?php echo base_url('discussion/delete/');?>" class="delete " data-id="<?php echo $item['id'];?>">
                            <?php echo $this->lang->line('delete');?>
                        </a>
                        <?php endif;?>

                        &nbsp;
                        <span class="likes">
                            <i class="icon-thumbs-up"></i>
                            <span>
                                <?php echo count($item['likes'])?>
                            </span>
                        </span>
                    </div>

                    <br>
                   </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>

</div>

<div class="modal hide fade" id="discussion_modal">
    <form class="form-vertical modal-form" action="<?php echo base_url('discussion/add/'); ?>" method="post"
          id="form-discussion">
        <div class="modal-header">
            <input type="hidden" name="book_id" value="<?php echo $book["book"]['id']?>"/>
            <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id');?>"/>

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>add new comment</h3>
        </div>
        <div class="modal-body" id="chapter-modal-body">
            <div class="control-group">
                <div class="controls">
                    <textarea name="message" id="" cols="20" rows="5" placeholder="<?php echo $this->lang->line('say-something');?>">
                    </textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></a>
            <button type="submit" class="btn btn-primary">
                <?php echo $this->lang->line('post-comment');?>
            </button>
        </div>
    </form>
</div>