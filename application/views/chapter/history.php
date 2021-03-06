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
                <th>Compare to</th>
                <th>Date</th>
                <th></th>
            </tr>
            <?php foreach ($history as $item): ?>

            <tr data-id="<?php echo $item['id'];?>">
                <td><?php echo $item['username'];?></td>
                <td><a href="chapter/historyEntry/<?php echo $item['id'];?>" class="view-content">Click to view content</a></td>
                <td><a class="compare" href="chapter/compare/<?php echo $item['id']; ?>/previous">Previous entry</a>&nbsp;|&nbsp;
                    <a class="compare" href="chapter/compare/<?php echo $item['id']; ?>/current">Current version</a></td>
                <td><?php echo $item['created'];?></td>

                <td><a class="rollback" href="chapter/rollback/<?php echo $item['id'];?>">Rollback</a></td>
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
<div class="modal hide" id="compare-modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Compare</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" class="btn">Close</a>
    </div>
</div>