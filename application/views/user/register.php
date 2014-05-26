<?php if ($result == 'success'): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4>Se guardó correctamente</h4>
    </div>
<?php endif; ?>

<div class="container ">
    <h1 class="text-center brand" id="registerh1">LEXICON</h1>
</div>
<div class="container">
    <div class="row-fluid">
        <div class="span6 offset3 bordered" id="register-user">
            <form class="form-vertical" action="<?php echo base_url('auth/register'); ?>" method="post">
                <div class="control-group">
                    <label class="control-label text-center">Email</label>

                    <div class="controls">
                        <input class="validate[required,custom[email]] span10 offset1 input-large"
                               type="text" id="inputusuario" name="email" autofocus="autofocus" required="required">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label text-center">User</label>

                    <div class="controls">
                        <input class="validate[required] span10 offset1 input-large" type="text"
                               id="inputusuario" name="username">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label text-center">Password</label>

                    <div class="controls">
                        <input class="validate[required] span10 offset1 input-large" type="password"
                               id="inputPassword" name="password">

                    </div>

                </div>
                <div class="control-group">
                    <label class="control-label text-center">Confirm Password</label>

                    <div class="controls">
                        <input class="validate[required,equals[inputPassword]] span10 offset1 input-large"
                               type="password" id="repeatPassword"
                               name="confirm_password">

                    </div>

                </div>
                <div class="control-group">
                    <div class="controls">
                        <a class="btn btn-large offset1 span4" href="<?php echo base_url(); ?>">Cancel</a>
                        <div class="span2">&nbsp;</div>
                        <input class="btn btn-primary btn-large offset1 span4" type="submit" value="Register"/>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
