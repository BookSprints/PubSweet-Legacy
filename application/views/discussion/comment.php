<div class="container pubsweet-main">
    <div class="row-fluid">
        <div class="offset1 span10">
            <h3 class="text-center"><?php echo  $topic['topic']['topic'] ;?></h3>
              <div class="unstyled media-list" id="comments">
                <?php foreach ($comments['comments'] as $item) : ?>

                    <div class="deletable media comment" data-id="<?php echo $item['id'] ?>">

                        <a class="pull-left" href="">
                            <img class="media-object" src="<?php echo base_url('user/image/'.$item['id']); ?>" alt="" width="64px"
                                 height="64px"/>
                        </a>

                        <div class="media-body">
                            <div class="pull-right btn-like">
                                <?php
                                $user_has_like = false;
                                foreach ($item['likes'] as $like) {
                                    if ($this->session->userdata('DX_user_id') == $like['user_id']) {
                                        $user_has_like = true;
                                    }
                                }
                                if (!$user_has_like):
                                    ?>
                                    <button class="btn add-like" data-comment_id="<?php echo $item['id'] ?>">
                                        <i class="icon-thumbs-up"></i>
                                        <span><?php echo $this->lang->line('like'); ?></span>
                                    </button>
                                <?php else:
                                    ?>
                                    <button class="btn add-dislike" data-comment_id="<?php echo $item['id'] ?>">
                                        <i class="icon-thumbs-down"></i>
                                        <span><?php echo $this->lang->line('dislike'); ?></span>
                                    </button>
                                <?php
                                endif;
                                ?>
                            </div>

                            <span class="user media-heading"><strong><?php echo empty( $item['names']) ? $item['username'] : $item['names']; ?></strong></span>
                            &nbsp; &nbsp;
                            <p><?php echo $item['comment']; ?></p>

                            <div class="actions pull-right">
                        <span class="date">
                            <time class="timeago" datetime="2012-07-18T07:51:50Z" title=""><?php echo $item['created']; ?></time>
                        </span>

                                <?php if ($item['user_id'] == $this->session->userdata('DX_user_id')): ?>
                                    &nbsp;-&nbsp;
                                    <a href="<?php echo base_url('comment/delete/'); ?>" class="delete "
                                       data-id="<?php echo $item['id']; ?>">
                                        <?php echo $this->lang->line('delete'); ?>
                                    </a>
                                <?php endif; ?>

                                &nbsp;
                        <span class="likes">
                            <i class="icon-thumbs-up"></i>
                            <span>
                                <?php echo count($item['likes']) ?>
                            </span>
                        </span>
                            </div>

                            <br>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="offset2 span8">
            <a href="" class=" btn-addComment pull-right" data-target="#comment_modal" data-toggle="modal">

                <span><?php echo $this->lang->line('reply'); ?></span>
            </a>
        </div>
    </div>
</div>

<div class="modal hide fade" id="comment_modal">
    <form class="form-vertical modal-form" action="<?php echo base_url('comment/add/'); ?>" method="post"
          id="form-discussion">
        <div class="modal-header">
            <input type="hidden" name="topic_id" value="<?php echo $topic["topic"]['id'] ?>"/>
            <!-- <input type="hidden" name="book_id" value="<?php echo $book["book"]['id']?>"/> -->
            <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id'); ?>"/>

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add New Comment</h3>
        </div>
        <div class="modal-body" id="chapter-modal-body">
            <div class="control-group">
                <div class="controls">
                    <textarea class="" name="comment" id="message-content" cols="20" rows="5"
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

