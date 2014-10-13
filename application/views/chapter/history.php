<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/5/14
 * Time: 12:25 AM
 */
?>
<div class="container pubsweet-main">
    <div class="row-fluid">
        <table class="table table-bordered table-striped">
            <tr>
                <th>User</th>
                <th>Content</th>
                <th>Date</th>
                <th></th>
            </tr>
            <?php foreach ($history as $item): ?>


            <tr data-id="<?php echo $item['id'];?>">
                <td><?php echo $item['username'];?></td>
                <td><a href="#" class="view-content">Click to view content</a>
                    <div class="hide"><?php echo $item['content'];?></div></td>
                <td><?php echo $item['created'];?></td>
                <td><a class="rollback" href="/chapter/rollback/<?php echo $item['id'];?>">Rollback</a></td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
</div>


<div class="modal hide" id="preview-entry">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Preview</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" class="btn">Close</a>
    </div>
</div>