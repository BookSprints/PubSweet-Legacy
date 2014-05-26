<package xmlns:opf="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://www.idpf.org/2007/opf" version="2.0" unique-identifier="primary_id">
  <metadata>
    <!--<dc:publisher>Unknown</dc:publisher>
    <dc:rights>GPLv2+</dc:rights>
    <dc:language>en</dc:language>
    <dc:creator>The Contributors</dc:creator>
    <dc:title><?php /*echo $book_name;*/?></dc:title>
    <dc:date opf:event="start">2013-03-24</dc:date>
    <dc:date opf:event="last-modified">2013-05-01</dc:date>
    <dc:date opf:event="published">2013-05-03</dc:date>
    <dc:identifier id="primary_id">http://backstopmedia.booktype.pro/developing-a-d3js-edge/2013.05.03-01.37</dc:identifier>-->
      <dc:language>en</dc:language>
      <dc:title><?php echo $book_name;?></dc:title>
      <dc:date opf:event="last-modified"><?php echo date('Y-m-d');?></dc:date>
      <?php if(!empty($metadata)):
          foreach ($metadata as $key => $item) :?>
      <dc:<?php echo $item['attribute'];?>><?php echo $item['value'];?></dc:<?php echo $item['attribute'];?>>
      <?php endforeach;
            endif;?>
      <dc:identifier id="primary_id">http://pubsweet.booksprints.net/<?php echo $book_name;?></dc:identifier>
      <?php if(isset($cover)&&$cover):?>
          <meta name="cover" content="cover">
      <?php endif;?>
  </metadata>
  <manifest>
      <item href="toc.ncx" media-type="application/x-dtbncx+xml" id="ncx"/>
      <?php
        if(isset($imgs)):
        foreach ($imgs as $key => $item) :?>
            <item href="static/reusable_with_axes.html.png" media-type="image/png" id="att022_reusable_with_axeshtmlpng"/>
      <?php endforeach;
            endif;
      ?>
      <?php if(isset($cover)&&$cover):?>
          <item href="static/cover.jpg" media-type="image/jpg" id="cover"/>
      <?php endif;?>

      <?php
      if(!!$css):
      foreach ($css as $key => $item) :?>
        <item href="css/<?php echo $item['name'];?>" media-type="text/css" id="<?php echo str_replace('.','-', $item['name'])?>"/>
      <?php endforeach;
            endif;
      ?>
      <?php foreach ($chapters as $key => $item) :?>
          <item href="<?php echo $item['url'];?>" media-type="application/xhtml+xml" id="<?php echo str_replace('.xhtml','', $item['url'])?>"/>
        <?php endforeach;?>
      <?php if(isset($cover)&&$cover):?>
          <item href="cover.xhtml" media-type="application/xhtml+xml" id="coverxhtml"/>
      <?php endif;?>
  </manifest>
  <spine toc="ncx">
      <?php foreach ($chapters as $key => $item) :?>
      <itemref idref="<?php echo str_replace('.xhtml','', $item['url'])?>"/>
      <?php endforeach;?>
      <?php if(isset($cover)&&$cover):?>
          <itemref idref="coverxhtml" linear="no"/>
      <?php endif;?>
  </spine>
  <guide>
      <?php if(isset($cover)&&$cover):?>
          <reference href="cover.xhtml" type="cover" title="Cover"></reference>
      <?php endif;?>
  </guide>
</package>