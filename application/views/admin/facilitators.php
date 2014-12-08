<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/4/14
 * Time: 8:33 PM
 */
?>
<div class="container pubsweet-main">
    <form action="admin/addFacilitator" method="post">
        <div class="control-group">
            <label class="control-label" for="inputEmail">Username</label>
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
                <button type="submit" class="btn btn-primary">Add</button>
                <a href="dashboard/profile">Back to dashboard</a>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Username</th>
                    <th>Name</th>
                </tr>
            </thead>
            <?php foreach ($facilitators as $item):?>

            <tr>
                <td></td>
                <td><?php echo $item['username'];?></td>
                <td><?php echo $item['names'];?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>