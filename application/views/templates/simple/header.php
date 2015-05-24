<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 2/11/14
 * Time: 12:30 PM
 */
?>
<!DOCTYPE html>
<html dir="<?php echo $this->session->userdata('lang_dir');?>" lang="<?php echo $this->session->userdata('lang_iso_code');?>">
<head>
    <meta charset="utf-8">
    <title>PUBSWEET</title>
    <style type="text/css">

        @font-face {
            font-family: "myriad";
            src: url(<?php echo base_url(); ?>public/css/myriad/MyriadPro-Regular.otf) format('opentype');
        }

        @font-face {
            font-family: "myriad";
            src: url(<?php echo base_url(); ?>public/css/myriad/MyriadPro-Bold.otf) format('opentype');
            font-weight: bold;
        }

        @font-face {
            font-family: "myriad";
            src: url(<?php echo base_url(); ?>public/css/myriad/MyriadPro-It.otf) format('opentype');
            font-style: italic;
        }
        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Regular.otf) format("opentype");
        }

        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Bold.otf) format("opentype");
            font-weight: bold;
        }

        @font-face {
            font-family: "cabin";
            src: url(<?php echo base_url(); ?>public/css/cabin/Cabin-Italic.otf) format("opentype");
            font-style: italic;
        }
        .container{
            margin-top: 60px;
        }
        .section{
            color: #666;
        }
    </style>
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/bootstrap.min.css"/>
    <?php if(!empty($draft) && $draft):?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/annotator.min.css"/>
    <?php endif;?>
</head>
<body data-book-id="<?php echo $id;?>">
    <div class="container">
        <?php echo $content;?>
    </div>