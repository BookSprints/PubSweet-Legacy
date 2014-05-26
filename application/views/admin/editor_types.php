<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 29/07/13
 * Time: 11:23
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="container">
    <div class="result">

    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <td>Editor</td>
                <td>Status</td>
            </tr>
        </thead>

        <tbody>
        <?php $cont=0; foreach ($editor as $item): $cont++;?>
            <tr>
                <th><?php echo $cont;?></th>
                <td><?php echo $item['editor']?></td>
                <td><input class="editor_type" data-id="<?php echo $item['id']?>" type="checkbox" name="editor" <?php if($item['enabled']) echo "checked"?>/></td>
            </tr>
        <?php endforeach;?>

        </tbody>
    </table>
</div>