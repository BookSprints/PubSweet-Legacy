<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/6/15
 * Time: 12:34 PM
 */
?>
<div>
    <div class="container pubsweet-main">
        <div class="row-fluid">
            <h1>Images per chapter</h1>
            <table class="table">
                <?php

                foreach ($sections as $section) :
                    ?>
                    <tr>
                        <td colspan="4"><h2>
                                <?php echo $section['title'];?></h2></td>
                    </tr>
                    <?php

                foreach ($chapters as $object) :
                    if($object['section_id']==$section['id']):
                    ?>

                    <tr>
                        <td></td>
                        <td colspan="3"><a href="editor/normal/<?php echo $object['id'];?>">
                        <?php echo $object['title'];?></a></td>
                    </tr>
                    <?php
                    if (!empty($object['images'])):
                        foreach ($object['images'] as $img) : ?>
                            <tr>
                                <td colspan="2"></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $img; ?></td>
                                <td></td>
                            </tr>
                        <?php endforeach; endif;?>

                <?php
                endif;
                endforeach;//end chapters
                endforeach;//end sections ?>
            </table>
        </div>
    </div>
</div>