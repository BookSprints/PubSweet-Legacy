<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/6/15
 * Time: 12:34 PM
 */
?>
<div id="find-replace">
    <div class="container pubsweet-main">
        <div class="row-fluid" data-spy="affix" data-offset-top="100" style="background-color: white">
            <form action="book/replace/<?php echo $id;?>" method="POST" role="form" class="form-inline">
                <div class="controls controls-row">
                    <label for="" class="span1">Find</label>
                    <input type="text" class="span3" name="find" id="find"
                           value="<?php echo $this->input->post('find');?>" required>
                    <div class="btn-group">
                        <button class="btn" type="button" id="up"><i class="icon-arrow-up"></i></button>
                        <button class="btn" type="button" id="down"><i class="icon-arrow-down"></i></button>
                    </div>
                </div>
                <div class="controls controls-row">
                <label for="" class="span1">Replace</label>
                <input type="text" class="span3" name="replace" id="replace"
                       value="<?php echo $this->input->post('replace');?>" required>
                <button type="button" class="btn" id="single-replace">Replace</button>
                <button type="submit" class="btn" id="replace-all">Replace all</button>
                </div>

            </form>
            <div class="alert alert-info">
            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            	<strong>Heads up!</strong> Find/Replaces is case-sensitive. Also will not work on section's name.
            </div>

        </div>
        <div class="row-fluid" id="contents">
            <h1 class="text-center"><?php echo $book['title'];?></h1>
            <?php

            foreach ($sections as $section) :
                ?>
                <h2><?php echo $section['title'];?></h2>
                <?php

                foreach ($chapters as $object) :
                    if($object['section_id']==$section['id']):
                    ?>
                    <div class="chapter" data-id="<?php echo $object['id'];?>">
                        <?php echo $object['content']?>
                    </div>
                    <?php
                    endif;
                endforeach;//end chapters
            endforeach;//end sections ?>
        </div>
    </div>
</div>