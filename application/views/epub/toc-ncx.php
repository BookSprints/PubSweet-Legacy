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
  <?php foreach ($chapters as $key => $item) :?>
      <navPoint id="chapter<?php echo $key;?>" playOrder="<?php echo $key;?>">
          <navLabel>
            <text><?php echo $item['title'];?></text>
          </navLabel>
          <content src="<?php echo $item['url'];?>"/>
        </navPoint>
  <?php endforeach;?>
  </navMap>
</ncx>