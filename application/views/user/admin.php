<div class="container pubsweet-main" >
    <div class="row-fluid">
        <div id="all-user" >
            <h3 class="text-center"> USER ADMIN</h3>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Names</th>
                    <th>Username</th>
                    <th colspan="2"></th>
                </tr>
                </thead>
             <tbody>
             <?php
                foreach ($user as $admin): ?>

             <tr id="user" class="<?php if($admin['banned']=='1') echo 'banned'; ?>">
                 <td class="name" data-id="<?php echo $admin['id']; ?>"><?php echo $admin['names']; ?></td>
                 <td class="users" id="restuser"><?php echo $admin['username']; ?></td>
                   <td class="user"> <a class="<?php if($admin['banned']=='1'){echo 'enable-user';}else  { echo "delete-user";} ?>" data-id="<?php echo $admin['id']; ?>">
                    <?php if($admin['banned']=='1'){ echo 'Unban';} else  { echo $this->lang->line('ban');} ?>  </a></td>
                 <td class="delete-pass"><a href="" data-id="<?php echo $admin['id']; ?>"  class="resetpass"><?php echo $this->lang->line('reset-password');?></a></td>
             </tr>
            <?php

            endforeach; ?>
             </tbody>

            </table>
        </div>
   </div>
<div>
</div>

<div class="modal hide " id="reset-password-modal">
    <form id="reset-user" action="<?php echo base_url('admin/update_user/'); ?>" method="post"
          class="modal-form">
        <div class="modal-header">
            <input type="hidden" id="user_id" name="user_id"/>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>


        </div>
        <div class="modal-body">
           <div class="control-group">
                <label  class="control-label" for="newpass"> Password</label>
                   <div class="controls">
                     <input name="password" type="password" id="newpass">
                  </div>
           </div>
            <div class="control-group">
                   <label  class="control-label" for="confirm-newpass">Confirm Password</label>
                <div class="controls">
                   <input name="confirm_password" type="password" id="confirm_newpassword">
               </div>
           </div>
            <br>
            <br>

            <div class="clearfix"></div>

        </div>
        <br>

        <div class="modal-footer">
            <span class="pull-left info hide"></span>
            <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></a>
            <button type="submit" class="btn btn-primary" data-loading-text="Reseting..." id="chapter-message">
                <?php echo $this->lang->line('reset-password');?>
            </button>
        </div>
    </form>
</div>