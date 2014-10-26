<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 10/25/14
 * Time: 12:04 AM
 */
?>
<div class="container pubsweet-main" id="profile-container">
<!--<form action="admin/import" method="POST" role="form" >-->
    <?php echo form_open_multipart('admin/import')?>

    <legend>Import EPUB</legend>

	<div class="form-group">
		<label for="">Title</label>
		<input type="text" class="form-control" name="title" id="title" placeholder="Input...">
	</div>
    <div class="form-group">
		<label for="epub">File</label>
		<input type="file" class="form-control" name="epub" id="epub" placeholder="Input...">
	</div>
	<button type="submit" class="btn btn-primary">Save</button>
</form>
</div>