<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
  <head>
    <meta content="http://pubsweet-new.booksprints.net/<?php echo htmlspecialchars($book_name);?>" name="dtb:uid"/>
    <meta content="1" name="dtb:depth"/>
    <meta content="0" name="dtb:totalPageCount"/>
    <meta content="0" name="dtb:maxPageNumber"/>
  </head>
  <docTitle>
    <text><?php echo htmlspecialchars($book_name);?></text>
  </docTitle>
  <navMap>
      <?php
      $counter = 0;
      foreach ($toc as $section) :
          ++$counter;?>
      <navPoint id="section<?php echo $counter;?>" playOrder="<?php echo $counter;?>">
          <navLabel>
              <text><?php echo htmlspecialchars($section['title']);?></text>
          </navLabel>
          <content src="<?php echo $section['url'];?>"/>
          <?php
          foreach ($section['chapters'] as $chapter):
              ++$counter;?>
              <navPoint id="chapter<?php echo ++$counter;?>" playOrder="<?php echo $counter;?>">
                  <navLabel>
                      <text><?php
                          $title = htmlspecialchars($chapter['title']);

                          echo $title;?></text>
                  </navLabel>
                  <content src="<?php echo $chapter['url'];?>"/>
              </navPoint>
          <?php endforeach;?>
      </navPoint>
      <?php endforeach;?>
  </navMap>
</ncx>