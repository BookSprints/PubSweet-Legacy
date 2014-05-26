<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 3/17/14
 * Time: 10:21 AM
 */
?>
<div class="container pubsweet-main" id="edit-flags">

    <div class="row-fluid">

    <?php echo form_open_multipart('flags/add', array('class'=>'form-inline'));?>
        <input type="text" name="title" placeholder="Title"/>
        <input type="file" name="image" id="new-image"/>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
<table class="table table-bordered table-striped">
    <tr>
        <th>Title</th>
        <th>Flag</th>
    </tr>
<?php
foreach($flags as $item):
    ?>

        <tr>
            <td><?php echo $item['title'];?></td>
            <td><img src="<?php echo base_url().'public/uploads/flags/'.$item['image'];?>" alt="<?php echo $item['title'];?>"/></td>

        </tr>

<?php
endforeach;
?>
</table>
        </div></div>