<?php
//TODO: move all the logic out of the view, implement a $ci->load->template method
$ci =&get_instance();
$ci->load->model('user_model');

$id = $this->session->userdata('DX_user_id');
$module = $this->uri->segment(1);
$view = $this->uri->segment(2);
$tabs = '_blank';
if (!empty($id)): ?>
    <div class="container navbar">

        <div class="navbar-inner">

            <!-- <a class="brand" href="<?php //echo base_url(); ?>">LEXICON</a> -->
            <ul class="nav">

                <?php if (isset($book)): ?>
                    <li>
                        <a href="<?php echo base_url('book/tocmanager/' . $book['id']); ?>">
                            <?php echo $book['title']; ?></a>
                    </li>
                    <?php if (!($view == "profile" || empty($view))): ?>
                        <li class="<?php echo ($module == 'book' && $view == 'tocmanager') ? 'active' : ''?>">

                            <a href="<?php echo base_url('book/tocmanager/' . $book['id']); ?>">
                                <?php echo $this->lang->line('contents'); ?></a>
                        </li>
                        <li class="<?php echo ($module == 'topic')? 'active' : ''?>">
                            <a href="<?php echo base_url('topic/view/' . $book['id']); ?>"
                                target="<?php echo ($module != 'topic') ? '_blank' : ''?>" >
                                <?php echo $this->lang->line('discussion'); ?></a>

                        </li>

                        <?php if ($id == $book['owner'] || $ci->user_model->isFacilitator($id)): ?>
                            <li><a href="<?php echo base_url() . 'console/' . $book['id'] . '/'; ?>" target="_blank">
                                    <?php echo $this->lang->line('console'); ?></a></li>
                            <li <?php echo $view == 'imageManager' ? ' class="active" ' : ''; ?>>
                                <a href="<?php echo base_url() . 'book/imageManager/' . $book['id']; ?>">
                                    <?php echo $this->lang->line('images'); ?></a></li>
                            <li <?php echo $view == 'full' ? ' class="active" ' : ''; ?>>
                                <a href="<?php echo base_url() . 'book/full/' . $book['id']; ?>">
                                    <?php echo $this->lang->line('full-content'); ?></a></li>

                        <?php
                        endif;//user
                    endif; ?>

                <?php endif;//book ?>
            </ul>
            <ul class="nav pull-right">
                <?php if (!empty($module) && $module != 'dashboard'): ?>
                    <li class="dropdown">
                        <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                            <i class=" icon-globe "></i>
                            <span class="logged"></span>
                            <!--<?php echo $this->lang->line('who-is-logged-in'); ?> -->

                        </a>
                        <ul class="dropdown-menu usersConnected" role="menu">
                            <li> no one else online now</li>

                        </ul>
                    </li>
                <?php endif; ?>
                <li class="dropdown">
                    <a class="dropdown-toggle dropdown" data-toggle="dropdown" href=""> <i class="icon-user "></i></a>
                    <ul class="dropdown-menu" role="menu"
                        title="<?php echo $this->session->userdata('DX_username'); ?> ">
                        <li><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
                        <li>
                            <a id="logout"
                               href="<?php echo base_url('auth/logout') . '?' . time(); ?>"> <?php echo $this->lang->line('logout'); ?> </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

    </div>
<?php endif; ?>
<div id="logo-pubsweet">
    <!-- <img src="--><?php //echo base_url();?><!--"  alt=""/>-->
</div>
