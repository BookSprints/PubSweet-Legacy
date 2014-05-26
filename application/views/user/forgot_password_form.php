<div class="container ">
    <h1 class="text-center brand">PubSweet</h1>

</div>
<div class="container">
    <div class="row-fluid">


        <div class="span4 offset4 bordered">
<?php

$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'maxlength'	=> 80,
	'size'	=> 30,
	'value' => set_value('login')
);

?>

<fieldset><legend accesskey="D" tabindex="1">Forgotten Password</legend>
<?php echo form_open($this->uri->uri_string()); ?>

<?php echo $this->dx_auth->get_auth_error(); ?>

<dl>
	<dt><?php echo form_label('Enter your Username or Email Address', $login['id']);?></dt>
	<dd>
		<?php echo form_input($login, null, 'class="input-large"'); ?>
		<?php echo form_error($login['name']); ?>
		<?php echo form_submit('reset', 'Reset Now','class="btn btn-primary"'); ?>
	</dd>
</dl>

<?php echo form_close()?>
</fieldset>
            <?php echo anchor(base_url(), 'Back to login');?>
            </div>
    </div>
</div>