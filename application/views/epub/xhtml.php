<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $title;?></title>
    <?php if(isset($css) && $css && count($css)>0):
        foreach ($css as $item) :?>
            <link rel="stylesheet" href="css/<?php echo $item['name'];?>"/>
    <?php endforeach;
        endif;?>
</head>
<body><div id="title"></div><?php echo $content;?></body></html>