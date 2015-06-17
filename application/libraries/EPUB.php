<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 02-04-13
 * Time: 05:18 PM
 */
require dirname(__FILE__).'/simple_html_dom.php';
class EPUB
{
    private $epub, $localFile, $opf, $toc;
    public $noFileLinks=array(), $xhtmlFiles, $images;
    private static $shortedChapters;
    private $extraLayer = '';

    public function __construct($file)
    {
        $this->zip = new ZipArchive;
        //Opens a Zip archive
        if($epub = $this->zip->open($file)){
            $this->localFile = $file;
            /*if(($opfContent = $this->zip->getFromName('content.opf')) === false){
                if(($opfContent = $this->zip->getFromName('OEBPS/content.opf')) === false){
                    throw new Exception('OPF file not found');
                }
            }

            $this->opf = simplexml_load_string($opfContent);
            $this->opf->registerXPathNamespace('opf', 'http://www.idpf.org/2007/opf');*/
            $this->opf();
        }else{
            echo 'Can not open: '.$file;
        }
    }
    public function reopen(){
        $this->zip->open($this->getLocalFile());
    }

    public function close(){
        try{
            $this->zip->close();
        }catch (Exception $e){
            return false;
        }

    }

    public function getLocalFile(){
        return $this->localFile;
    }

    public function opf(){
        if(empty($this->opf)){
            if(($opfContent = $this->zip->getFromName('content.opf')) === false){
                if(($opfContent = $this->zip->getFromName('OEBPS/content.opf')) === false){
                    throw new Exception('OPF file not found');
                }else{
                    $this->extraLayer = "OEBPS/";
                }
            }

            $this->opf = simplexml_load_string($opfContent);
            $this->opf->registerXPathNamespace('opf', 'http://www.idpf.org/2007/opf');
        }
        return $this->opf;
    }

    public function addAppleOptions(){
        $this->zip->addFromString('META-INF/com.apple.ibooks.display-options.xml',
            '<?xml version="1.0" encoding="UTF-8"?>
            <display_options>
            <platform name="*">
            <option name="specified-fonts">true</option>
            </platform>
            </display_options>');
    }
    //TODO move to EpubFixer
    public function cleanXHTML(){
        $this->xhtmlFiles = $this->getXhtmlFiles();
        $this->setShortedChapters();
        foreach ($this->xhtmlFiles as $item) {
//            $clean_html = $purifier->purify($dirty_html);
            $this->backToEpub($item, tidy_repair_string($this->zip->getFromName($item), array('char-encoding'=>'utf8','output-xhtml'=>true)));
        }

    }

    public function getToc()
    {
        if($this->toc==null){
            if(($tocContent = $this->zip->getFromName('toc.ncx')) === false){
                if(($tocContent = $this->zip->getFromName('OEBPS/toc.ncx')) === false){
                    throw new Exception('TOC file not found');
                }else{

                    $this->extraLayer = "OEBPS/";
                }
            }
            $this->toc = simplexml_load_string($tocContent);
        }

        return $this->toc;
    }

    //TODO move to EpubFixer
    /**
     * Fix anchors linking to local files
     */
    function fixLocalFile()
    {
//        $this->localFile = $file;
        $this->xhtmlFiles = $this->getXhtmlFiles();
        $this->setShortedChapters();
        foreach ($this->xhtmlFiles as $item) {
            $this->backToEpub($item, $this->fixContent($this->zip->getFromName($item), $item));
        }
//        $this->zip->close();
    }

    private function setShortedChapters()
    {
        if(!isset(self::$shortedChapters)){
            foreach ($this->xhtmlFiles as $item) {
                $chunks = explode('_', $item);
                $chunks2 = explode('.', $chunks[1]);
                self::$shortedChapters[$chunks2[0]] = $item;
            }
        }


    }

    function getXhtmlFiles()
    {
        if(!file_exists($this->localFile)){
            echo 'File not found';
            return false;
        }
        $zip = zip_open($this->localFile);
        $xhtmlFiles = array();

        if ($zip!=false) {
            while ($zip_entry = zip_read($zip)) {
                $entryName = zip_entry_name($zip_entry);
                if (!is_dir($entryName)) {
                    $path_parts = pathinfo($entryName);
//                    $ext = strtolower(trim(isset ($path_parts['extension']) ? $path_parts['extension'] : ''));
                    if (isset ($path_parts['extension']) && strtolower(trim($path_parts['extension'])) == 'xhtml') {
                        $xhtmlFiles[] = $entryName;
                    }
                }
            }
        }
        zip_close($zip);
        return $xhtmlFiles;
    }

    //TODO move to EpubFixer if necessary
    //TODO write documentation
    private function fixContent($content, $file)
    {
        // Create DOM from string
        $html = str_get_html($content);
        foreach ($html->find('a') as $element) {
            $chunks = $chunks2 = null;
            //don't start with http|ftp|https|mailto don't end with xhtml neither
            if (!empty($element->href) && !preg_match('/[http|ftp|https|mailto]:/', $element->href)
                && !preg_match('/\.xhtml/', $element->href)
            ) {
                $chunks = explode('/', $element->href);
                $chunks2 = explode('#', isset($chunks[1])?$chunks[1]:$chunks[0]);
                $chapter = $chunks2[0];

                if (isset(self::$shortedChapters[$chapter])) {
                    $element->href = self::$shortedChapters[$chapter] . (isset($chunks2[1]) ? '#' . $chunks2[1] : '');
                } else {
                    $id = count($this->noFileLinks);
                    $class = 'nofile'.$id;
                    $element->class=$class;
                    $item = array('file'=>$file, 'class'=>$class,
                        'text'=>$element->plaintext, 'href'=>$element->href);
                    $this->noFileLinks[] = $item;
                }

            }
        }

        return $html;

    }

    //TODO move to EpubWriter
    public function backToEpub($file, $content, $xml=false)
    {
        if($xml){
            if (!$this->zip->addFromString($file, $content->asXML())) {
                echo 'error backtoepub xml';
            }
        }else{
            if (!$this->zip->addFromString($file, $content)) {
                echo 'error';
            }
        }

    }

    public function fixOrphanLinks($data)
    {
        foreach($data as $key=>$item){
            if($key=='book'){
                continue;
            }
            $internalFile = str_replace('&','.',$key);
            $xhtml = $this->zip->getFromName($internalFile);
            $dom = str_get_html($xhtml);
            foreach($item as $class=>$newLink){
                $element = $dom->find('a.'.$class, 0);
                $element->href=$newLink;
            }
            $this->zip->addFromString($internalFile, $dom->innertext);
        }
        return array('ok'=>1);//TODO check if this method is still necessary
    }
    
    public function uploadCSS($content){//TODO check if this method is still necessary
        if (get_magic_quotes_gpc()) {
            $content = stripslashes($content);
        }
        $cssFile = 'objavi.css';
        if (!$this->zip->addFromString($cssFile, $content)) {
            return array('ok'=>false);
        }
        $this->fixCSSReference($cssFile);
        $this->fixCSSInnerFilesReference($cssFile);
        return array('ok'=>1);
    }

    private function fixCSSInnerFilesReference($cssFile)
    {//TODO check if this method is still necessary
        $zip = zip_open($this->localFile);
        while ($zip_entry = zip_read($zip)) {
            $entryName = zip_entry_name($zip_entry);
            if (!is_dir($entryName)) {
                $path_parts = pathinfo($entryName);
                if (isset ($path_parts['extension']) && strtolower(trim($path_parts['extension'])) == 'xhtml') {
                    if($content=$this->zip->getFromName($entryName)){
                        $html = str_get_html($this->zip->getFromName($entryName));
                        if (!$html->find('link[href="'.$cssFile.'"]', 0)) {
                            $element = $html->find('head', 0);
                            $element->innertext = $element->innertext . '<link href="' . $cssFile . '" type="text/css" rel="stylesheet"/>';
                            $this->zip->addFromString($entryName, $html);
                        }
                    }else{
                        die($this->zip->getStatusString());
                    }


                }
            }
        }
        zip_close($zip);
        return true;
    }
    //TODO move to EpubFixer
    private function fixCSSReference($cssFile){
        $result = $this->opf->xpath('//opf:manifest/opf:item[@href="'.$cssFile.'"]');
        if(empty($result)){
            $cssItem = $this->opf->manifest->addChild('item');
            $cssItem->addAttribute('href', $cssFile);
            $cssItem->addAttribute('id', $cssFile);
            $cssItem->addAttribute('media-type', 'text/css');
        }
        return true;
    }
    //TODO probably need to go to EpubWriter
    private function createCoverHtml($file)
    {
        $htmlCoverFile = 'cover.xhtml';
        $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'.
            '<html xmlns="http://www.w3.org/1999/xhtml">'.
            '<head>'.
            '<title>Cover</title>'.
            '<style type="text/css"> img { max-width: 100%; } </style>'.
            '</head>'.
            '<body>'.
            '<div id="cover-image">'.
            '<img src="'.$file.'" alt="cover"/>'.
            '</div>'.
            '</body>'.
            '</html>';
        if($this->zip->addFromString($htmlCoverFile, $content)){
            return $htmlCoverFile;
        }
        return false;
    }

    public function setCover($cover){
        $coverJpg = 'cover.jpeg';
        if (!$this->zip->addFromString($coverJpg, $cover)) {
            return false;
        }
        $html = $this->createCoverHtml($coverJpg);

        $this->setCoverReference($html, $coverJpg);
        return true;
    }

    private function setCoverReference($html, $jpg)
    {
        $internalFile = 'content.opf';//TODO change for $this->opf
        $opf = $this->zip->getFromName($internalFile);
        $xml = new SimpleXMLElement($opf);

        $meta = $xml->metadata->addChild('meta');
        $meta->addAttribute('name', 'cover');
        $meta->addAttribute('content', 'cover-image');

        $coverHtmlItem = $xml->manifest->addChild('item');
        $coverHtmlItem->addAttribute('href', $html);
        $coverHtmlItem->addAttribute('id', 'cover');
        $coverHtmlItem->addAttribute('media-type', 'application/xhtml+xml');

        $coverJpgItem = $xml->manifest->addChild('item');
        $coverJpgItem->addAttribute('href', $jpg);
        $coverJpgItem->addAttribute('id', 'cover-image');
        $coverJpgItem->addAttribute('media-type', 'image/jpeg');

        $itemref1 = $xml->spine->addChild('itemref');
        $itemref1->addAttribute('idref', 'cover');
        $itemref1->addAttribute('linear', 'no');

        if (!isset($xml->guide)) {
            $xml->addChild('guide');
        }

        $reference = $xml->guide->addChild('reference');
        $reference->addAttribute('href', $html);
        $reference->addAttribute('type', 'cover');
        $reference->addAttribute('title', 'Cover');

        $this->zip->addFromString($internalFile, $xml->asXML());
//        $this->zip->close();
    }

    /**
     * Search images in the content.opf file with blank character in its name and change it for "_"
     *
     * @return bool
     */
    public function fixImagesReferencesInOPF(){
        $internalFile = 'content.opf';
        $opf = $this->zip->getFromName($internalFile);
        $xml = new SimpleXMLElement($opf);
        $xml->registerXPathNamespace('opf', 'http://www.idpf.org/2007/opf');
        $result = $xml->xpath('//opf:manifest/opf:item[@media-type="image/png"]');
        foreach($result as $key=>$item){
            if(strpos($item['href'],' ')!==FALSE){
                $old = $item['href'];
                $new = str_replace(' ','_', $old);
                $tempdom = dom_import_simplexml($item);
                $tempdom->setAttributeNode(new DOMAttr('href', $new));
                //$result[$key]->addAttribute('href', $new);
                $this->zip->renameName($old, $new);
            }
        }

        $result = $xml->xpath('//opf:manifest/opf:item[@media-type="image/jpeg"]');
        foreach($result as $key=>$item){
            if(strpos($item['href'],' ')!==FALSE){
                $old = $item['href'];
                $new = str_replace(' ','_', $old);
                $tempdom = dom_import_simplexml($item);
                $tempdom->setAttributeNode(new DOMAttr('href', $new));
                $this->zip->renameName($old, $new);
            }
        }
        $this->zip->addFromString($internalFile, $xml->asXML());

        $this->fixImageReferencesInFiles();
        return true;
    }

    /**
     * Search image references in the xhtml files with blank character in its name and change it for "_"
     *
     * @return bool
     */
    private function fixImageReferencesInFiles()
    {
        $xhtmlFiles = $this->getXhtmlFiles();
        foreach($xhtmlFiles as $entry){
            if($entry=='cover.xhtml'){
                continue;
            }
            $xhtml = $this->zip->getFromName($entry);
            $dom = str_get_html($xhtml);
            foreach($dom->find('img') as $element){
                $uri = $element->src;
                if(strpos($uri,' ')!==FALSE || strpos($uri,'%20')!==FALSE){
                    $element->src = str_replace(' ','_', $uri);
                    $element->src = str_replace('%20','_', $uri);
                }
            }
            $this->backToEpub($entry, $dom->innertext);
        }

    }
    //TODO move to EpubWriter
    public function insertCSS($URI){
        $fileName = basename($URI);
        if(!$this->zip->addFile($URI, $fileName)){
            return false;
        }else{
            if(!$this->fixCSSReference($fileName)){
                die('css-reference');
                return false;
            }
            if(!$this->fixCSSInnerFilesReference($fileName)){
                die('css-fixCSSInnerFilesReference');
                return false;
            }
            return true;
        }

    }
    //TODO move to EpubWriter
    public function insertJS($URI){
        $fileName = basename($URI);
        $this->zip->addFile($URI, $fileName);
        $this->fixJSReference($fileName);
    }

    private function fixJSReference($jsFile){
        $result = $this->opf->xpath('//opf:manifest/opf:item[@href="'.$jsFile.'"]');
        if(empty($result)){
            $item = $this->opf->manifest->addChild('item');
            $item->addAttribute('href', $jsFile);
            $item->addAttribute('id', $jsFile);
            $item->addAttribute('media-type', 'javascript');
        }
        return true;

    }

    public function getFromName($name)
    {
        return $this->zip->getFromName(str_ireplace('//','/',$this->extraLayer.$name));
    }

    public function getError(){
        return $this->zip->getStatusString();
    }

    /**
     *
     */
    public function getCompactContent()
    {
        if(empty($this->compactContent)){
            $toc = $this->getToc();
            $this->compactContent = $this->createStructure($toc->navMap);
        }

        return $this->compactContent;
    }

    public function createStructure($navParent)
    {
        $result = array();
        foreach($navParent->navPoint as $item):
            if(($file = str_get_html($this->getFromName((string) $item->content['src'])))==false){
                continue;
            }

            if(empty($item->navPoint)){
                $result[(string)$item->navLabel->text] = $file->find('body', 0)->outertext;
            }else{
                $content = $file->find('body', 0)->outertext;
                $result[(string)$item->navLabel->text] = array('content'=>$content,
                    'children'=>$this->createStructure($item) );
            }

        endforeach;
        return $result;
    }

    public function getImages()
    {
        $result = $this->opf()->xpath('//opf:manifest/opf:item[@media-type="image/png"]');
        $result1 = $this->opf()->xpath('//opf:manifest/opf:item[@media-type="image/jpeg"]');
        return array_merge($result, $result1);
    }
}