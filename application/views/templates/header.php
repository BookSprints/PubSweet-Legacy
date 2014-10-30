<!DOCTYPE html>
<html dir="<?php echo $this->session->userdata('lang_dir');?>" lang="<?php echo $this->session->userdata('lang_iso_code');?>">
<head>
<meta charset="utf-8">
<base href="<?php echo base_url();?>">
<title>PUBSWEET</title>
<!--    <link href='http://fonts.googleapis.com/css?family=Abril+Fatface' rel='stylesheet' type='text/css'>-->
<!--<link href='http://fonts.googleapis.com/css?family=Playfair+Display' rel='stylesheet' type='text/css'>-->
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

    /*@font-face {
        font-family: "libre-baskerville";
        src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Regular.otf) format('opentype');
    }

    @font-face {
        font-family: "libre-baskerville";
        src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Bold.otf) format('opentype');
        font-weight: bold;
    }

    @font-face {
        font-family: "libre-baskerville";
        src: url(<?php echo base_url(); ?>public/css/libre-baskerville/LibreBaskerville-Italic.otf) format('opentype');
        font-style: italic;
    }*/
</style>
<!--<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/bootstrap.min.css"/>
<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/chosen.css"/>
<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/jquery-ui-1.10.3.custom.min.css">
<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/validationEngine.jquery.css">
<link rel="stylesheet" href="<?php /*echo base_url(); */?>public/css/bootstrap-editable.css"/>-->


<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/pubsweet-libs.min.css"/>

<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/pubsweet.min.css"/>
<!--<link rel="stylesheet" href="--><?php //echo base_url(); ?><!--public/css/pubsweet.css"/>-->

</head>
<body>
<div id="info"></div>