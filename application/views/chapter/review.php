<div class="container pubsweet-main" id="review">
<?php
if(empty($entries)):?>
<div class="hero-unit text-center info warning"><h1>This chapter is empty</h1></div>
<?php endif;

foreach ($entries as $item):

    $english = $french = $arabic = array('term'=>'','definition'=>'');

    if($item['language']==136){
        $english = array('term'=>$item['term'],
            'definition'=>$item['meaning']);
    }else{
        if(isset($definitions[$item['id']])):
            foreach ($definitions[$item['id']] as $def){
                if($def['language_id']==136):
                    $english = array('term'=>$def['term'],
                        'definition'=>$def['definition']);
                endif;
            }
        endif;//if isset
    }

    if($item['language']==152){
        $french = array('term'=>$item['term'],
            'definition'=>$item['meaning']);
    }else{
        if(isset($definitions[$item['id']])):
            foreach ($definitions[$item['id']] as $def):
                if($def['language_id']==152):
                    $french = array('term'=>$def['term'],
                        'definition'=>$def['definition']);
                endif;
            endforeach;
        endif;//if isset
    }

    if($item['language']==26){
        $arabic = array('term'=>$item['term'],
            'definition'=>$item['meaning']);
    }else{
        if(isset($definitions[$item['id']])):
            foreach ($definitions[$item['id']] as $def):
                if($def['language_id']==26):
                $arabic = array('term'=>$def['term'],
                        'definition'=>$def['definition']);
                endif;
            endforeach;
        endif;//if isset
    }

?>

<div class="row-fluid">

    <div class="span3">
        <div><strong><?php echo $french['term'];?></strong></div>
        <div class="text french"><?php echo $french['definition'];?>
        </div>
    </div>
    <div class="span3">

        <div><strong><?php echo $english['term'];?></strong></div>
        <div class="text english"><?php echo $english['definition'];?>
        </div>
    </div>
    <div class="span3" dir="rtl">
        <div><strong><?php echo $arabic['term'];?></strong></div>
       <div class="text arabic"> <?php echo $arabic['definition'];?> </div>
    </div>
    <div class="span3">
        <div>
            <div class="btn-group">
                <button class="btn approve-counter" data-term-id="<?php echo $item['id'];?>" >
                    <?php echo isset($approves[$item['id']])?count($approves[$item['id']]):0;?>
                </button>
            <button class="btn new-approve" data-term-id="<?php echo $item['id'];?>"
                <?php echo (isset($voted[$item['id']])&&$voted[$item['id']])?'disabled':'';?>>Approve</button>
            </div>
<!--            <button class="btn" data-term-id="--><?php //echo $item['id'];?><!--">Like</button>-->
            <button role="button" class="btn show-form" data-term-id="<?php echo $item['id']?>">+ <?php echo $this->lang->line('comment');?></button>
        </div>
        <div>

            <small class="pull-right">

                <a href="#comments-<?php echo $item['id']?>"
                                         data-toggle="collapse">
                    <?php if(isset($reviews[$item['id']])):?>
                        Expand/Collapse all <?php if(isset($reviews[$item['id']])) echo count($reviews[$item['id']]);?> comments
                    <?php else:?>
                        <span class="no-comments">No comments</span>
                    <?php endif;?>
                    </a>

            </small></div>
        <br>
        <div class="comments collapse <?php if(!isset($reviews[$item['id']])) echo 'in';?>" data-term-id="<?php echo $item['id']?>" id="comments-<?php echo $item['id']?>">
        <?php if(isset($reviews[$item['id']])):
            foreach ($reviews[$item['id']] as $rev):?>
            <div class="deletable media">

                <span class="pull-left" >
                    <img class="media-object" src="<?php echo base_url('user/image/'.$rev['user_id']);?>"
                         alt="" width="48px" height="48px"/>
                </span>
                <div class="media-body">

                    <span class="user media-heading"><strong>
                            <?php echo empty( $rev['names']) ? $rev['username'] : $rev['names']; ?></strong></span> &nbsp; &nbsp;
                    <p><?php echo $rev['comment'];?></p>


                    <div class="actions"><!--<span class="date"><?php /*echo $item['created'];*/?></span>&nbsp;
                        -&nbsp;<a href="<?php /*echo base_url('discussion/delete/');*/?>" class="delete "
                                  data-id="<?php /*echo $item['id'];*/?>"><?php /*echo $this->lang->line('delete');*/?></a>--></div>
                    <br>
                </div>
            </div>
        <?php
            endforeach;
            endif;?>
        </div>
    </div>
</div>
    <br/><br/>
<?php endforeach;?>
</div>
<div class="modal hide fade" id="add-comment-modal">
    <form id="add-comment-form" action="<?php echo base_url('review/add'); ?>" method="post"
          class="modal-form">
        <input type="hidden" name="term_id">
        <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id'); ?>">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php echo $this->lang->line('add-review'); ?></h3>
        </div>
        <div class="modal-body">
            <div class="control-group">
                <div class="controls" id="sections">
                    <textarea name="comment" id="review-text" cols="30" rows="5" autofocus></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="sectionResult"></div>

            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></a>
            <button type="submit" class="btn btn-primary" data-loading-text="Creating..." id="section_create">
                <?php echo $this->lang->line('send'); ?>
            </button>
        </div>
    </form>
</div>

<div class="modal hide fade" id="modal-approvals">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo $this->lang->line('approvals'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="list-approves">
            <ul class="unstyled users-approves">

            </ul>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
    </div>
</div>