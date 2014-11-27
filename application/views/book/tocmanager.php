<div id="tocmanager">
<div class="container pubsweet-main" id="tocmanager-container">
    <div class="row-fluid">

        <h1><?php echo $this->lang->line('contents'); ?><span class="pull-right">
                <?php if($isBookOwner || $contributor):?>
                    <?php if(isset($settings['enable_flag']) && $settings['enable_flag']):?>
                    <a class="btn flags" href="<?php echo base_url('flags/all/edit'); ?>">Flags</a>
                        <?php endif;?>
                <button class="btn btn-users btn-info" data-target="#coauthors-modal" data-toggle="modal">
                    <?php echo $this->lang->line('users'); ?>
                </button>
                <button class="btn btn-section " data-target="#create-section-modal" data-toggle="modal"
                        id="btn-sections">
                    <i class="icon-white icon-plus"></i>  <?php echo $this->lang->line('section'); ?>
                </button>
                <button class="btn btn-chapter"
                        id="btn-chapter">
                    <i class="icon-white icon-plus"></i> <?php echo $this->lang->line('chapter'); ?>
                </button>
                <?php endif;?>

            </span></h1>
        <div id="result" class="pull-right"></div>

        <div class="accordion" id="accordion2">
            <div class="accordion-group inline">
                <div class="lists accordion-heading">
                    <ul class="unstyled sections <?php echo ($isBookOwner || $contributor) ? 'contributor' : 'reviewer'; ?>">
                        <?php foreach ($sections as $section): ?>
                            <li class="section" data-id="<?php echo $section['id'] ?>"
                                data-order="<?php echo $section['order']; ?>">
                                <h3 class="section-name">
                                    <span class="name editable editable-click"><?php echo $section['title']; ?></span>
                                    <?php if($isBookOwner || $isFacilitator ):?>
                                    <span class="pull-right" >
                                        <a href="<?php echo base_url('sections/delete_section/'); ?>"  class="delete-section" data-id="<?php echo $section['id']; ?>">&times;</a>
                                    </span>
                                    <?php endif;?>
                                    <a data-toggle="collapse"
                                       data-target="#section-chapters-<?php echo $section['id'] ?>"
                                       class="accordion-toggle pull-left">&nbsp;</a>

                                </h3>

                                <div id="section-chapters-<?php echo $section['id']; ?>"
                                     class="accordion-body collapse in">
                                    <ul class="unstyled chapters"
                                        data-section-id="<?php echo $section['id'] ?>">
                                        <?php
                                        foreach ($chapters as $item):
                                            if ($item['section_id'] == $section['id']):
                                                $url = '';
                                                $type = '';
                                                switch ($item["editor_id"]) {
                                                    case 1:
                                                        $url = "dictionary/creator/";
                                                        $type = "Lexicon";
                                                        break;
                                                    case 2:
                                                        $url = "editor/normal/";
                                                        $type = "WYSI";
                                                        break;
                                                };
                                                ?>
                                                <li class="chapter" data-id="<?php echo $item['id']; ?>">
                                                    <span class="title editable editable-click"
                                                          title="<?php echo $item['title']; ?>">
                                                        <?php echo $item['title']; ?></span>
                                                    <span class="options pull-right">
                                                        <?php if ($isBookOwner || $contributor): ?>
                                                            <span class="chapter-status">
                                                            <a href="#">
                                                                <?php
                                                                foreach ($status as $status_item):
                                                                    if ($status_item['chapter_id'] == $item['id']):
                                                                        ?>
                                                                        <span class="status" data-user_id="<?php
                                                                        if (!empty($status_item['user_id']))
                                                                            echo $status_item['user_id'];
                                                                        ?>"
                                                                              data-user=""
                                                                              data-title="<?php echo $status_item['title'] ?>"
                                                                              data-id="<?php echo $status_item['id'] ?>"
                                                                              data-status="<?php echo $status_item['status'] ?>">
                                                                        <?php echo ($status_item['status']) ? "Ø" : "O"; ?>
                                                                    </span>
                                                                    <?php
                                                                    endif;
                                                                endforeach;

                                                                ?>
                                                            </a>
                                                        </span>
                                                        <?php endif; ?>
                                                        <span class='chapter-type'>
                                                            <a href="<?php echo base_url('render/chapter/' . $item['id']); ?>"
                                                               class="chapter-contents">
                                                                <?php echo $type; ?></a>
                                                        </span>&nbsp;&nbsp;
                                                        <?php
                                                        if ($isBookOwner || $contributor):?>
                                                            <a href="<?php echo base_url($url . $item['id']); ?>" class="edit"
                                                                <?php if($item['locked']) { echo ' style="display:none;" ';}?>>
                                                                <?php echo $this->lang->line('edit'); ?></a>
                                                            <a href="<?php echo base_url('chapter/delete_chapter/'); ?>"  class="delete-chapter"
                                                                <?php if($item['locked']) { echo ' style="display:none;" ';}?>

                                                            data-id="<?php echo $item['id']; ?>"><?php echo $this->lang->line('delete'); ?></a>
                                                            <a href="<?php echo base_url('chapter/history/'.$item['id']); ?>"  class="history-chapter">
                                                                <?php echo $this->lang->line('history'); ?></a>
                                                        <?php
                                                        endif;

                                                        if ($isBookOwner || $reviewer):?>
                                                            <a href="<?php echo base_url('chapter/review/' . $item['id']); ?>"><?php echo $this->lang->line('review'); ?></a>
                                                        <?php endif; ?>

                                                        <?php if ($this->session->userdata('DX_username') == 'admin' || $isBookOwner || $isFacilitator){?>
                                                            <a class="lock" href="<?php echo base_url('chapter/toggleLock/' . $item['id']); ?>" data-lock="<?php echo $item['locked'];?>">
                                                                <?php echo $this->lang->line($item['locked'] ? 'unlock' : 'lock'); ?></a>
                                                        <?php }else if($item['locked']){ echo 'Locked'; }; ?>
                                                    </span>
                                                </li>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modals">
    <div class="modal hide fade" id="create-section-modal">
        <form id="create-section" action="<?php echo base_url('sections/save'); ?>" method="post"
              class="modal-form">
            <input type="hidden" name="book_id" value="<?php echo $id; ?>">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?php echo $this->lang->line('create-new-section'); ?></h3>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls" id="sections">
                        <input class="validate[required]" type="text" name="title" autofocus="autofocus"
                               placeholder="Section name ..." id="title">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="sectionResult"></div>

                <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></a>
                <button type="submit" class="btn btn-primary" data-loading-text="Creating..." id="section_create">
                    <?php echo $this->lang->line('create'); ?>
                </button>
            </div>
        </form>
    </div>
    <div class="modal hide fade" id="create-chapter-modal">
        <form id="create-chapter" action="<?php echo base_url('chapter/save'); ?>" method="post"
              class="modal-form">
            <input type="hidden" name="book_id" value="<?php echo $id; ?>">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?php echo $this->lang->line('create-new-chapter'); ?></h3>
            </div>
            <div class="modal-body" id="chapter-modal-body">
                <div class="control-group">
                    <div class="controls">
                        <input class="validate[required]" type="text" name="title" autofocus="autofocus"
                               placeholder="chapter name ..." id="title">
                    </div>
                </div>
                <div class="pull-right">
                    <select name="editor_id" id="select-editor">
                        <?php
                        $default = 'WYSI';
                        foreach ($editors as $item):
                            if ($item['enabled']):?>
                                <option value="<?php echo $item['id']; ?>"
                                    <?php if($item['editor']==$default) echo ' selected '?>
                                    ><?php echo $item['editor'] ?></option>
                            <?php
                            endif;
                        endforeach ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">

                <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></a>
                <button type="submit" class="btn btn-primary" data-loading-text="Creating..." id="chapter-create">
                    <?php echo $this->lang->line('create'); ?>
                </button>
            </div>
        </form>
    </div>
    <div class="modal hide " id="chapter-message-modal">
        <form id="chapter-status" action="<?php echo base_url('status/save'); ?>" method="post"
              class="modal-form">
            <div class="modal-header">
                <input type="hidden" id="chapter_id"/>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?php echo $this->lang->line('status-for'); ?> <span id="chapter-message-title"></span></h3>
            </div>
            <div class="modal-body">
                <div class="controls pull-right">
                    <a class="btn btn-primary" id="add-chapter-status" href="#">
                        <i class="icon-white icon-plus"></i> <?php echo $this->lang->line('item'); ?>
                    </a>
                </div>
                <br>
                <br>

                <div class="clearfix"></div>
                <ul id="StatusList">

                </ul>
            </div>
            <br>

            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></a>
                <button type="submit" class="btn btn-primary" data-loading-text="Saving..." id="chapter-message">
                    <?php echo $this->lang->line('save-changes'); ?>
                </button>
            </div>
        </form>
    </div>

    <div class="modal hide fullpage" id="chapter-contents-modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php echo $this->lang->line('chapter-contents'); ?></h3>
        </div>
        <div class="modal-body">
            <h1><?php echo $this->lang->line('loading'); ?>....</h1>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></a>
        </div>
    </div>

    <div class="modal hide fade" id="coauthors-modal">
        <div class="modal-header">
            <h2><?php echo $this->lang->line('co-authors'); ?>
                <button class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </h2>
        </div>
        <div class="modal-body">
            <form id="invited-email" action="<?php echo base_url('invited/invite'); ?>" method="post" class="form-horizontal">
                   <input type="hidden" name="book_id" value="<?php echo $id; ?>" />
                   <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id'); ?>"/>
                <div class="control-group">
                   <label class="control-label" for=""> <?php echo $this->lang->line('invite-a-user'); ?>  </label>
                   <div class="controls">
                      <div class="input-append">
                         <input type="email" name="invited" required="required" autofocus="true" id="invited"/>
                         <button type="submit" class="btn btn-primary" data-loading-text="Inviting...">
                           <?php echo $this->lang->line('invite'); ?>
                         </button>
                     </div>
                  </div>
              </div>

            </form>
            <form id="new-coauthor" class="form-horizontal" action="<?php echo base_url('book/addCoauthor'); ?>"
                  method="post">
                <input type="hidden" name="book_id" value="<?php echo $id; ?>"/>
                <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('DX_user_id'); ?>"/>


                <div class="control-group"><label class="control-label"
                                                  for="user"><?php echo $this->lang->line('co-authors'); ?></label>

                    <div class="controls">
                        
                        <div class="input-append"><select name="user_id" id="user">
                                <?php

                                foreach ($users as $item) :
                                    if ($item['id'] != $user['id'] && empty($coauthors[$item['id']])):

                                        ?>
                                        <option
                                            value="<?php echo $item['id']; ?>"><?php echo $item['username']; ?></option>
                                    <?php endif;
                                endforeach;?>
                            </select>
                            <input class="btn" type="submit" value="<?php echo $this->lang->line('add'); ?>"/>
                        </div>
                    </div>
                </div>

                <div class="control-group">

                    <div class="controls">
                        <label class="checkbox" for="contributor"><input type="checkbox" name="contributor"
                                                                         id="contributor" checked/> Contributor - Can do
                            everything</label>
                        <label class="checkbox" for="reviewer"><input type="checkbox" name="reviewer" id="reviewer"/>
                            Reviewer - Can only review</label>
                    </div>
                </div>
            </form>
            <table id="coauthors" class="table table-bordered table-striped">
                <?php foreach ($coauthors as $item) :
                    if(!isset($users[$item['user_id']])){
                        continue;
                    }

                    ?>
                    <tr class="deletable">
                        <td><?php echo $users[$item['user_id']]['username']; ?></td>
                        <td>
                            <div class="btn-group" data-toggle="buttons-checkbox">
                                <button type="button" class="btn btn-mini btn-update <?php if ($item['contributor']) {
                                    echo 'active';
                                } ?>"
                                        data-user-id="<?php echo $item['user_id']; ?>" data-field="contributor">
                                    <?php echo $this->lang->line('contributor'); ?></button>
                                <button type="button" class="btn btn-mini btn-update <?php if ($item['reviewer']) {
                                    echo 'active';
                                } ?>"
                                        data-user-id="<?php echo $item['user_id']; ?>" data-field="reviewer">
                                    <?php echo $this->lang->line('reviewer'); ?></button>
                            </div>
                        </td>
                        <td><a class="remove" href="<?php echo base_url('book/removeCoAuthor/'); ?>"
                               data-user-id="<?php echo $item['user_id']; ?>">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="modal-footer"></div>
    </div>

<!--    <div class="modals">
        <div class="modal hide fade" id="invited-modal">
            <form id="invited-email" action="<?php //echo base_url('invited/invite'); ?>" method="post" class="modal-form">
                <input type="hidden" name="book_id" value="<?php //echo $id; ?>">
                <input type="hidden" name="user_id" value="<?php //echo $this->session->userdata('DX_user_id'); ?>"/>

                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                   <h3><?php //echo $this->lang->line('invite-a-user'); ?></h3>
               </div>
                <div class="modal-body">

                    <div class="controls-group">
                        <label for="">Email</label>
                       <div class="controls">
                       <input type="email" name="invited" required="required" autofocus="true">
                       </div>
                   </div>

               </div>
                <div class="modal-footer">

                   <a href="#" class="btn" data-dismiss="modal"><?php //echo $this->lang->line('cancel'); ?></a>
                   <button type="submit" class="btn btn-primary" data-loading-text="Inviting..." id="user-invited">
                    <?php //echo $this->lang->line('invite'); ?>
                    </button>
               </div>

            </form>
     </div>
   </div>-->


</div>

