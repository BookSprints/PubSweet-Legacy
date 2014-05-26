<script id="image-handler" type="text/x-handlebars-template">
    <div class="accordion" id="accordion2">
        <div class="accordion-group">
        <div class="accordion-heading clearfix">
        <a class="accordion-toggle pull-right" data-toggle="collapse" href="#collapseOne">
        {{term.imagelabel}}
        </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
        <div class="accordion-inner">
        <div class="row-fluid">
        <div class="span4" id="result">

        <img src="{{#if term.full_image_path}}{{term.full_image_path}}{{else}}http://placehold.it/200{{/if}}"
             alt="image" style="max-width: 200px;"/>

        </div>
        <div class="span8">
            <input id="fileupload" type="file" name="attachment" id="attach-input"
                data-url="<?php echo base_url('dictionary/attach/'); ?>"
                accept="image/*">

            <div id="progress">
            <div class="bar" style="width: 0%;height: 18px;background: green;"></div>
        </div>

        <button id="delete-image" class="btn {{#unless term.full_image_path}}hide{{/unless}}" data-id="{{term.id}}">Delete</button>

    </div>

    </div>
    </div>

    </div>
    </div>
    </div>
    </div>
</script>
<div class="container pubsweet-main" id="edit-dictionary">

    <div class="row-fluid">
        <div class="span3" id="dictionary-creator">
            <h4 class="text-center">Lexicon</h4>

            <form action="<?php echo base_url('dictionary/save/') ; ?>" method="post"
                  id="create-term">
                <input class="validate[required]" type="hidden" name="chapter_id"
                       value="<?php echo $id; ?>"/>

                <div class="controls input-append">
                    <input class="validate[required] span7" type="text"
                           name="term" autofocus="autofocus"
                           placeholder="<?php echo $this->lang->line('enter-term');?>" required="required" id="term-name"/>
                    <input name="language_id" type="hidden" value="26"/>
                    <button class="btn btn-primary" data-loading-text="Creating..." id="btn-addentry"><i
                            class="icon-white icon-plus"></i><?php echo $this->lang->line('entry');?>
                    </button>
                </div>
                <br>
                <br>
            </form>
            <div id="syncing" style="color: white;">Syncing...</div>
            <div class="panel" id="list-item">
                <ul class="unstyled" id="list-term" dir="rtl">
                    <?php foreach ($term as $item) : ?>
                        <li class="items-terms" data-locked='false'>
                            <div class="status"></div>
                            <span class="item-list" title="<?php echo $item['term']; ?>"
                               data-id="<?php echo $item['id']; ?>"><?php echo $item['term']; ?></span>
                            <button class="delete-term close">&times;</button>
                            <span class="editor muted" data-id="<?php echo $item['id']; ?>"></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="span9">
            <!--<div><label class="radio inline" for="">
                    <input type="radio" name="primary_language" id=""/>Arabic</label>
                <label
                    class="radio inline" for=""><input type="radio" name="primary_language" id=""/>English</label>
                <label
                    class="radio inline" for=""><input type="radio" name="primary_language" id=""/>French</label>
            </div>-->


            <div class="alert" id="create-load">Please Wait<a class="close" data-dismiss="alert" href="#">&times;</a></div>
            <h3 class="text-center"><?php echo $chaptername['title']; ?></h3>

            <div class="clearfix"></div>
            <div class="text-editor hide">
                <div id="image-holder"></div>
                <form action="<?php echo base_url('dictionary/update'); ?>" dir="RTL"
                      id="term-original-form" method="post" data-edited="false">
                    <input type="hidden" name="id" id="term-id"/>

                   <div class="term-update pull-right chosen-container">
                      <a href="" class="move-item"><?php echo $this->lang->line('move-this-item-out-of-this-chapter');?> </a>
                     <span class="hide" id="change-chapter">  <label for="item-chapter">  <?php echo $this->lang->line('pick-the-new-chapter');?>
                     <select name="item-chapter" id="item-chapter" class="pick-chapter ">
                      <?php foreach($chapteritem as $moveitem):  ?>
                      <?php if($moveitem['editor_id']== 1 and $moveitem['removed']== 0):?>
                        <option value="<?php echo $moveitem['id']; ?>" <?php echo ($moveitem['id']== $chaptername['id'])?'selected="selected"':'';?> ><?php echo $moveitem['title']; ?></option>
                      <?php
                      endif;
                      endforeach ?>
                     </select>
                         </label>
                     </span>
                  </div>

                   <div class="clearfix"></div>
                    <div id="headeditor">
                        <input type="text" placeholder="<?php echo $this->lang->line('item');?>" name="term" class="item-editor">

                        <div class="pull-left">
                            <input type="hidden" name="language" value="26">
                            <span>Arabic</span>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div id="term-list">
                        <textarea name="meaning" id="term-content" dir="RTL"></textarea>
                    </div>
                    <div class="footer pull-right hide" id="editorfooter">
                        <input class="btn" id="cancel" type="reset" value="<?php echo $this->lang->line('cancel');?>">
                        <input class="btn btn-primary" data-loading-text="Saving..." type="submit" value="<?php echo $this->lang->line('save');?>">
                    </div>
                    <div class="clearfix"></div>
                </form>
                <div class="definitions">
                    <form action="<?php echo base_url('definition/save'); ?>" method="post"
                          data-edited="false" class="definition-form definition-form-136">
                        <input type="hidden" name="term_id">
                        <input type="hidden" name="id">
                       <div class="clearfix"></div>
                        <div id="headeditor">
                            <input type="text" placeholder="<?php echo $this->lang->line('item');?>" name="term">

                            <div class="pull-right">
                                <input type="hidden" name="language_id" value="136">
                                <span>English</span>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <textarea name="definition" dir="LTR" id="editor-136"></textarea>
                        </div>
                        <div class="footer pull-right hide">
                            <input class="btn" id="cancel" type="reset" value="<?php echo $this->lang->line('cancel');?>">
                            <input class="btn btn-primary" data-loading-text="Saving..." type="submit" value="<?php echo $this->lang->line('save');?>">
                        </div>
                        <div class="clearfix"></div>
                    </form>

                    <form action="<?php echo base_url('definition/save'); ?>" class="definition-form definition-form-152"
                          method="post" data-edited="false">
                        <input type="hidden" name="term_id"/>
                        <input type="hidden" name="id">
                       <div class="clearfix"></div>
                        <div id="headeditor">
                            <input type="text" placeholder="<?php echo $this->lang->line('item');?>" name="term">

                            <div class="pull-right">
                                <input type="hidden" name="language_id" value="152">
                                <span>French</span>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <textarea name="definition" dir="LTR" id="editor-152"></textarea>
                        </div>
                        <div class="footer pull-right hide">
                            <input class="btn" id="cancel" type="reset" value="<?php echo $this->lang->line('cancel');?>">
                            <input class="btn btn-primary" data-loading-text="Saving..." type="submit" value="<?php echo $this->lang->line('save');?>">
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div>
                    <button class="save-all btn btn-primary pull-right" type="button">Save all</button>
                </div>
            </div>

        </div>

    </div>


    <div class="modal hide fade" id="confirm-delete-term">
        <form action="<?php echo base_url('dictionary/termDelete');?>" id="form-term-delete" method="post"
              class="modal-form">
            <input type="hidden" name="term_id" id="term_id">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4><?php echo $this->lang->line('confirm-delete');?></h4>
            </div>
            <div class="modal-body">
                <h5><?php echo $this->lang->line('you-want-to-delete-this-term');?></h5>
                <span id="term"></span>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('confirm');?></button>
                <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></a>
            </div>
        </form>
    </div>
</div>
</div>