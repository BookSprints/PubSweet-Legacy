<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/4/14
 * Time: 8:33 PM
 */
?>

<div class="modal hide" id="modal-book-name">
    <form action="book/updateName" method="post" id="form-book-name" class="modal-form">

                <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Change Book Name</h4>
            </div>
            <div class="modal-body">

                <input name="book_id" type="hidden" id="book-name-id"/>

                <div class="control-group">
                    <label class="control-label" for="inputEmail">Type the new name</label>

                    <div class="controls">
                        <input type="text" name="bookname" class="input-block-level"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="help-inline error hide pull-left"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        data-loading-text="Updating...">Close
                </button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
                </div>
        <!-- /.modal-dialog -->
    </form>

</div><!-- /.modal -->

<div class="modal hide" id="modal-book-owner">
    <form action="book/updateOwner" method="post" id="form-book-owner" class="modal-form">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Change owner</h4>
                </div>
                <div class="modal-body">

                    <input name="book_id" type="hidden" id="book-input"/>

                    <div class="control-group">
                        <label class="control-label" for="inputEmail">Pick the new owner</label>

                        <div class="controls">
                            <select name="owner" id="owners">
                                <?php
                                foreach ($users as $item) :
                                    ?>
                                    <option
                                        value="<?php echo $item['id'];?>"><?php echo empty($item['names']) ? $item['username'] : $item['names'];?></option>
                                <?php
                                endforeach;

                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="help-inline error hide pull-left"></span>
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                            data-loading-text="Updating...">Close
                    </button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </form>

</div><!-- /.modal -->

<div class="container pubsweet-main">
    <!--    <a class="btn btn-primary" data-toggle="modal" href="modal-id">Trigger modal</a>-->

    <div class="row-fluid">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th></th>
                <th>Books</th>
                <th>Owner</th>
                <th>Options</th>
            </tr>
            </thead>
            <?php //var_dump($users);die();?>
            <?php foreach ($books as $item): ?>

                <tr data-book-id="<?php echo $item['id']; ?>">
                    <td><?php echo $item['id']; ?></td>
                    <td><a href="#modal-book-name" class="change-book-name"><i class="icon-pencil"></i></a>&nbsp;
                        <span class="current-bookname"><?php echo $item['title']; ?></span></td>
                    <td><a href="#modal-book-owner" class="update-owner" data-value="<?php echo $item['owner']; ?>"><i
                                class="icon-pencil"></i></a>&nbsp;
                        <span
                            class="owner-name"><?php echo $users[$item['owner']][empty($users[$item['owner']]['names']) ? 'username' : 'names']; ?></span>
                    </td>
                    <td><a href="book/stats/<?php echo $item['id']; ?>">Stats</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php echo $this->pagination->create_links(); ?>
    </div>
</div>