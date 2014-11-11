<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/4/14
 * Time: 8:33 PM
 */
?>
<form action="admin/addFacilitator" method="post">
    <div class="control-group">
        <label class="control-label" for="inputEmail">Email</label>
        <div class="controls">
            <select name="user_id" id="user_id">
                <?php
                foreach ($users as $item) :
                    ?>
                <option value="<?php echo $item['id'];?>"><?php echo $item['username'];?></option>
                <?php
                endforeach;

                ?>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn">Cancel</button>
        </div>
    </div>
</form>