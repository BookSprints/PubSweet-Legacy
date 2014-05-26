<div class="container ">
    <h1 class="text-center brand">LEXICON</h1>

</div>
<div class="container" id="login-container">
    <div class="row-fluid">


        <div class="span4 offset4 bordered">
            <form class="form-vertical" id="login" action="<?php echo base_url('auth/login'); ?>" method="post">


                <div class="control-group ">
                    <label class="control-label"> Username or Email </label>

                    <div class="controls">
                        <input class="validate[required] span10 input-large" type="text"
                               autofocus="autofocus" id="inputuser" name="username"> <div class="span1">&nbsp;</div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Password</label>

                    <div class="controls">
                        <input class="validate[required] span10 input-large" type="password" id="inputPassword"
                               name="password"> <div class="span1">&nbsp;</div>
                    </div>

                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="span1">
                            &nbsp;
                        </div>
                        <a href="<?php echo base_url('register/user'); ?>" id="call-register">Register</a>

                        <div class="span1 pull-right">
                            &nbsp;
                        </div>
                        <input class="btn btn-primary btn-large pull-right" type="submit" value="Login"/>
                    </div>
                </div>

            </form>
            <br/>
            <div class="clearfix">
            <div class="pull-right"><?php echo anchor($this->dx_auth->forgot_password_uri, 'Forgotten your password?');?>
                <!--<a href="#">Forgotten your password?</a>--></div>
            </div>
            <?php if (isset($error) && $error): ?>
                <br/>
                <div class="alert alert-error">Error, please try again
                    <button class="close" type="button" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

