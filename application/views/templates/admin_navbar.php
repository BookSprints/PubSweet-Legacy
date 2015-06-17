<?php
$this->load->model('user_model');

$id = $this->session->userdata('DX_user_id');
$module = $this->uri->segment(1);
$view = $this->uri->segment(2);
$tabs='_blank';
if (!empty($id)):  ?>
    <div class="container navbar">

        <div class="navbar-inner">

           <!-- <a class="brand" href="<?php //echo base_url(); ?>">LEXICON</a> -->
            <ul class="nav">

                    <li>
                        <a href="<?php echo base_url('admin/books/');?>">Books</a>
                    </li>
                <li><a href="<?php echo base_url('admin/users/');?>">Users</a></li>
                <li><a href="<?php echo base_url('admin/facilitators/');?>">Facilitators</a></li>
                <li><a href="<?php echo base_url('admin/stats/');?>">Stats</a></li>

            </ul>
            <ul class="nav pull-right">
                <?php if(!empty($module) && $module!='dashboard'):?>
                <li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                        <i class=" icon-globe "></i>
                        <span class="logged"></span>
                        <!--<?php echo $this->lang->line('who-is-logged-in');?> -->

                    </a>
                    <ul class="dropdown-menu usersConnected" role="menu" >
                        <li> no one else online now</li>

                     </ul>
                </li>
                <?php endif;?>
                <li class="dropdown">

                      <a class="dropdown-toggle dropdown" data-toggle="dropdown" href=""> <i class="icon-user "></i>

                      </a>
                   <ul class="dropdown-menu" role="menu" title="<?php echo $this->session->userdata('DX_username'); ?> ">
                          <li ><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard');?></a> </li>
                         <li>
                             <a id="logout" href="<?php echo base_url('auth/logout'); ?>"> <?php echo $this->lang->line('logout');?> </a>
                         </li>
                   </ul>
             </li>

            </ul>
        </div>

    </div>
<?php endif; ?>
<div  id="logo-pubsweet">
<!-- <img src="--><?php //echo base_url('');?><!--"  alt=""/>-->
</div>
