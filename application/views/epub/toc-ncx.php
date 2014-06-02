<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
  <head>
    <meta content="http://pubsweet.booksprints.net/<?php echo $book_name;?>" name="dtb:uid"/>
    <meta content="1" name="dtb:depth"/>
    <meta content="0" name="dtb:totalPageCount"/>
    <meta content="0" name="dtb:maxPageNumber"/>
  </head>
  <docTitle>
    <text><?php echo $book_name;?></text>
  </docTitle>
  <navMap>
      <?php $tempSection = null;
            $counter = 0;
            $close = false;
      ?>
  <?php foreach ($chapters as $item) :?>

      <?php if(isset($item['section']) && $tempSection != $item['section']):?>
      <?php if($close):?>
          </navPoint>
      <?php endif;?>
          <?php $tempSection = $item['section'];
          $counter++;
          $close = true;
          ?>

          <navPoint id="section<?php echo $counter;?>" playOrder="<?php echo $counter;?>">
              <navLabel>
                  <text><?php echo $item['section'];?></text>
              </navLabel>
              <content src="<?php echo $item['url'];?>"/>
      <?php endif;?>
                <navPoint id="chapter<?php echo ++$counter;?>" playOrder="<?php echo $counter;?>">
                  <navLabel>
                    <text><?php echo $item['title'];?></text>
                  </navLabel>
                  <content src="<?php echo $item['url'];?>"/>
                </navPoint>
  <?php endforeach;?>
      <?php if($close):?>
          </navPoint>
      <?php endif;?>
  </navMap>
</ncx>