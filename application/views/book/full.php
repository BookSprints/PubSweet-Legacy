<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/6/15
 * Time: 12:34 PM
 */
?>
<div class="container pubsweet-main" id="full-content">
    <div class="row-fluid">
        <h1>Full content</h1>

        <div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Heads up!</strong>&nbsp;Chapters under deleted sections won't be visible until the deleted section is
            restored
        </div>
        <table class="table">
            <?php

            foreach ($sections as $section) :
                ?>
                <tr class="section-row <?php echo $section['removed'] ? 'deleted' : '';?>">
                    <td colspan="4"><h2>
                            <?php echo $section['title'];?></h2></td>
                    <td>
                        <?php if( $section['removed']):?>
                            <a class="undo" href="section/undo/<?php echo $section['id'];?>">Undo</a>
                        <?php endif;?>
                    </td>
                </tr>
                <?php

            foreach ($chapters as $object) :
                if($object['section_id']==$section['id']):
                ?>

                <tr class="<?php echo $object['removed'] ? 'deleted' : '';?>">
                    <td></td>
                    <td colspan="3"><a href="editor/normal/<?php echo $object['id'];?>">
                    <?php echo $object['title'];?></a></td>
                    <td>
                        <?php if( $object['removed']):?>
                            <a class="undo" href="chapter/undo/<?php echo $object['id'];?>">Undo</a>
                        <?php endif;?>
                    </td>
                </tr>

                <?php
            endif;
            endforeach;//end chapters
            endforeach;//end sections ?>
        </table>
    </div>
</div>