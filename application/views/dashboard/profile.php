<div id="formprofile">
    <div class="container pubsweet-main" id="profile-container">

        <div class="row-fluid">
        <div class="span3"><div class=" text-center" id="image_profile">
                <div id="name"><?php
                    if(empty( $user['names'])){
                        echo $this->lang->line('type-name');
                    }else{
                        echo $user['names'];//$user_name;
                    }
                    ?></div>
                <div>
                    <img id="profile-img" alt="profile-img"
                         src="<?php echo empty($user['picture'])?'http://placehold.it/200x200':$user['picture'];?>"
                         width="200" height="200" class="img-polaroid"/>
                    <input type="file" name="img" id="upload" class="hide"/>
                </div>
                <div><a href="<?php echo $this->dx_auth->change_password_uri;?>">
                        <?php echo $this->lang->line('change-pass');?>
                </a></div>

            </div>
            <div class="controls pull-right">
                <div></div>
                <input type="button" class="btn btn-primary validate[required] profile-button"
                       value="<?php echo $this->lang->line('create-book');?>"
                       data-target="#create-book-modal" data-toggle="modal"  id="btncreatebook">
                <a href="importer/form" class="btn btn-primary profile-button">Import from EPUB</a>
                <br/>
                <br/>

                <form action="dashboard/updateLanguage/" method="post" id="form-language" class="form-vertical">
                    <div class="control-group"><label class="control-label" for="">PUBSWEET'S LANGUAGE:</label>
                        <div class="controls"><select name="language" id="select-languages" onchange="$('#form-language').get(0).submit();" class="span12">
                                <?php
                                $selectedLanguage = $this->session->userdata('lang_iso_code');
                                if(empty($selectedLanguage)){
                                    $selectedLanguage = 'en';
                                }

                                foreach ($languages as $key => $item) :?>
                                    <option value="<?php echo $key;?>"
                                        <?php echo $key==$selectedLanguage?'selected="selected"':'';?>>
                                        <?php echo $item;?></option>
                                <?php endforeach;?>
                            </select></div>
                    </div>

                </form>
            </div>
        </div>
        <div class="span9"><div id="mybook">
                <h3 class="text-center"><?php echo $this->lang->line('my-books');?></h3>
                <table class="table table-striped table-bordered">

                    <tbody>
                    <?php if(isset($my_books)):

                        foreach ($my_books as $item) : ?>
                            <tr>
                                <td class="bookname"><?php echo $item['title']; ?></td>
                                <td class="edit"><a href="<?php echo 'book/tocmanager/'.$item['id']; ?>">
                                        <?php echo $this->lang->line('edit');?></a></td>
                                <td>
                                    <span class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            Options<b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="book/stats/<?php echo $item['id']; ?>">
                                                    <?php echo $this->lang->line('stats');?></a>
                                            </li>
                                            <li>
                                                <a href="book/settings/<?php echo $item['id']; ?>">
                                                    <?php echo $this->lang->line('settings');?></a>
                                            </li>
                                            <li>
                                                <a href="book/replace/<?php echo $item['id']; ?>">
                                                    <?php echo $this->lang->line('find-replace');?></a>
                                            </li>
                                            <li>
                                            <a class="copy-link" href="#copy-modal" data-href="book/copy/<?php echo $item['id']; ?>"
                                               data-toggle="modal">
                                                <?php echo $this->lang->line('copy');?></a>
                                            </li>
                                        </ul>
                                    </span>

                                    </td>
                                <td>
                                    <?php if(empty($item['chapter_id'])):?>
                                        <?php echo $this->lang->line('no-chapters');?>
                                    <?php else:?>
                                        <a href="render/epub/<?php echo $item['id']; ?>">EPUB</a>
                                        &#183;
                                        <span class="dropdown">
                                            <a class="dropdown-toggle" data-toggle="dropdown"
                                               href="#">
                                                HTML<b class="caret"></b></a>
                                            <ul class="dropdown-menu pull-right">
                                                <li>
                                                    <a href="render/html/<?php echo $item['id']; ?>">Preview</a>
                                                </li>
                                                <li>
                                                    <a href="render/structure/<?php echo $item['id']; ?>">Structure</a>
                                                </li>
                                                <li>
                                                    <a href="render/html/<?php echo $item['id']; ?>/draft">Review</a>
                                                </li>
                                            </ul>
                                        </span>

                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php
                        unset($all_books[$item['id']]);
                        endforeach;
                    endif;

                    if(isset($invited_books)):
                        foreach ($invited_books as $item) :
                            if(!isset($all_books[$item['book_id']])){
                                continue;
                            }?>
                            <tr>
                                <td class="bookname"><?php echo $all_books[$item['book_id']]['title']; ?></td>
                                <td class="edit"><a href="book/tocmanager/<?php echo $all_books[$item['book_id']]['id']; ?>">
                                        <?php echo $this->lang->line('edit');?></a></td>
                                <td>
                                    <span class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            Options<b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="book/stats/<?php echo $all_books[$item['book_id']]['id']; ?>">
                                                    <?php echo $this->lang->line('stats');?></a>
                                            </li>
                                            <li>
                                                <a href="book/settings/<?php echo $all_books[$item['book_id']]['id']; ?>">
                                                    <?php echo $this->lang->line('settings');?></a>
                                            </li>
                                        </ul>
                                    </span>
                                </td>
                                <td>
                                    <?php if(empty($all_books[$item['book_id']]['chapter_id'])):?>
                                        <?php echo $this->lang->line('no-chapters');?>
                                    <?php else:?>
                                    <a href="render/epub/<?php echo $all_books[$item['book_id']]['id']; ?>">EPUB</a>
                                    &#183;
                                    <span class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            HTML<b class="caret"></b></a>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="render/html/<?php echo $all_books[$item['book_id']]['id'];?>">Preview</a>
                                            </li>
                                            <li>
                                                <a href="render/structure/<?php echo $all_books[$item['book_id']]['id'];?>">Structure</a>
                                            </li>
                                            <li>
                                                <a href="render/html/<?php echo $all_books[$item['book_id']]['id'];?>/draft">Review</a>
                                            </li>
                                        </ul>
                                    </span>
                                </td>
                                <?php endif;?>
                            </tr>
                        <?php endforeach;

                    endif;
                    ?>
                    </tbody>
                </table>
            </div></div>
        </div>
        <?php if($this->session->flashdata('register')):?>
            <div class="alert alert-info">
            <h2 class="text-center"><?php echo $this->lang->line('you-have-successfully-registered');?>.</h2>
            <h1 class="text-center"><?php echo $this->lang->line('welcome-to');?> <span class="brand">PUBSWEET</span></h1>
            </div>
        <?php endif;?>
    </div>


</div>

<div class="modal hide fade" id="create-book-modal">
    <form id="create-book" action="book/save/" method="post" class="modal-form">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php echo $this->lang->line('new-book');?></h3>
        </div>
        <div class="modal-body">
            <h3><?php echo $this->lang->line('book-name');?></h3>
            <input class="validate[required]" type="text" name="title" autofocus="autofocus" id="book"/>

        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></a>
            <input class="btn btn-primary book-save" data-loading-text="Saving..." type="submit"
                   value="<?php echo $this->lang->line('create');?>"/>
        </div>
    </form>
</div>

<div class="modal hide" id="preview">
    <div class="modal-body">
        <canvas id="panel" width="380" height="380"></canvas>
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn"><?php echo $this->lang->line('cancel');?></button>
        <button type="button" class="btn change"> <?php echo $this->lang->line('pick-another');?></button>
        <button type="button" class="btn crop"><?php echo $this->lang->line('save');?></button>
    </div>
</div>

<div id="copy-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="copy-modal" aria-hidden="true">
    <form id="copy-form" action="/" method="post" class="modal-form">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Copy book</h3>
        </div>
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label" for="inputInfo"><?php echo $this->lang->line('new-book-name');?></label>
                <div class="controls">
                    <input class="validate[required]" type="text" name="title" autofocus="autofocus"/>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" type="reset">Close</button>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </div>
    </form>
</div>