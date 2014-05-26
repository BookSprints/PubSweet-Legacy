<div class="container-fluid">
<h1 class="text-center"><?php echo $chapter['title']; ?></h1>

<?php foreach ($entries as $item): ?>
    <div class="row-fluid">
        <?php ob_start();?>
        <div class="original span4" dir="<?php echo $item['code_dir']; ?>" lang="<?php echo $item['iso_code'];?>"
            datetime="<?php echo $item['updated'];?>">

            <p>
                <span><strong><?php echo $item['term']; ?>:</strong></span>
            </p>
            <?php if(!empty($item['full_image_path'])):?>
                <img src="<?php echo base_url() . $item['full_image_path'];?>" alt=""/>
            <?php endif;?>
            <p>
                <?php echo $item['meaning']; ?>
            </p>
        </div>
        <?php
            $originalContent = ob_get_contents();
            ob_end_clean();?>

        <?php if(strtolower($item['code_dir'])=='ltr'){
                echo $originalContent;
            }?>
        <?php if (isset($definitions[$item['id']])): ?>
            <?php if (isset($definitions[$item['id']][136])): ?>
            <div class="translation span4" lang="<?php echo $definitions[$item['id']][136]['iso_code']; ?>"
                datetime="<?php echo $definitions[$item['id']][136]['updated']; ?>">
                <span><strong><?php echo $definitions[$item['id']][136]['term']; ?>:</strong></span>
                <?php echo $definitions[$item['id']][136]['definition']; ?>
                </div>
            <?php else:?>
            <div class="span4"></div>
            <?php endif;?>

            <?php if (isset($definitions[$item['id']][152])): ?>
            <div class="translation span4" lang="<?php echo $definitions[$item['id']][152]['iso_code']; ?>"
                 datetime="<?php echo $definitions[$item['id']][152]['updated']; ?>">


                <span><strong><?php echo $definitions[$item['id']][152]['term']; ?>:</strong></span>
                <?php echo $definitions[$item['id']][152]['definition']; ?>
            </div>
            <?php else:?>
                <div class="span4"></div>
            <?php endif; ?>

        <?php endif; ?>
        <?php if(strtolower($item['code_dir'])=='rtl'):
            echo $originalContent;
        endif;?>
    </div>
    <hr/>
<?php endforeach; ?>
</div>