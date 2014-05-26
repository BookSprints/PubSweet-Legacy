<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 29/07/13
 * Time: 11:23
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="container pubsweet-main">
    <form action="<?php echo base_url('book/save_settings/'.$id);?>" class="form-horizontal" method="post">
        <fieldset>
            <legend>Settings for <i><?php echo $book['title'];?></i></legend>
            <div class="control-group">
                <label class="checkbox" for="enable_flag">
                    <input type="checkbox" name="settings[enable_flag]" id="enable_flag"
                        <?php echo isset($settings['enable_flag']) && $settings['enable_flag']?
                        'checked="checked"':'';?>
                        value="1"/>Enable flags</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </fieldset>
    </form>
</div>