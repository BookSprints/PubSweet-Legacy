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
        <div class="row-fluid">
            <form action="" method="POST" role="form" class="form-inline">
                <label for="">Find</label>
                <input type="text" class="form-control" name="find" id="find"
                    value="<?php echo $this->input->post('find');?>" required>
                <label for="">Replace</label>
                <input type="text" class="form-control" name="replace" id="replace"
                       value="<?php echo $this->input->post('replace');?>" required>
                <button type="submit" class="btn btn-primary">Find</button>
            </form>
            <div class="alert alert-info">
            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            	<strong>Heads up!</strong> Find/Replaces is case-sensitive. Also will not work on section's name.
            </div>

        </div>
        <div class="row-fluid">
            <h1 class="text-center"><?php echo $book['title'];?></h1>
            <?php

            foreach ($sections as $section) :
                ?>
                <h2><?php echo $section['title'];?></h2>
                <?php

                foreach ($chapters as $object) :
                    if($object['section_id']==$section['id']):
                    ?>
                    <div><?php echo $object['content']?></div>
                    <?php
                    endif;
                endforeach;//end chapters
            endforeach;//end sections ?>
        </div>
    </div>
</div>