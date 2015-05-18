<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 02-04-13
 * Time: 03:15 PM
 */
require dirname(__FILE__) . '/../libraries/EPUB.php';
class Manager extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('console');
    }
    
    public function getFromObjavi($book)
    {        
        $result = array('ok'=>false);
//        if(isset($_POST['book'])){
        if(isset($book)){
            $link = self::getURL($book, $this->config->item('server'));
            if(!empty($link)){
                $localLink = self::getFileEpub($book, $link);
                $epub = new EPUB($localLink);
                $epub->cleanXHTML();
                if($this->config->item('automatic-apple-fix')){
                    $epub->addAppleOptions();
                }
                $epub->close();
                echo json_encode(array('ok'=>1, 'link'=>$localLink));
                return;
            }else{
                $result['error']='Objavi returns nothing';
            }
        }else{
            $result['error']='Not enough info';
        }
        echo json_encode($result);
    }

    public function fixLinks($book){
        //no epub extension
        if ($book) {
            $local = 'tmp/'.$book.'.epub';
            $epub = new EPUB($local);
            $epub->fixLocalFile();
            $result = array('ok' => 1, 'epub' => $local);
            if(!empty($epub->noFileLinks)){
                $result['orphanLinks']=$epub->noFileLinks;
                $result['xhtmlFiles'] = $epub->xhtmlFiles;
                $result['book'] = $book;
            }
            echo json_encode($result);
            return;
        }else{
            echo json_encode(array('ok' => false, 'error' => 'Missing info'));
        }
    }

    public function fixOrphans(){
        $epub = new EPUB('tmp/'.$_POST['book'].'.epub');
        $epub->fixOrphanLinks($_POST);
        echo json_encode(array('ok'=>1));
    }
    
    /*public function injectCSS(){
        if(isset($_POST['book'])){
            $epub = new EPUB('tmp/'.$_POST['book'].'.epub');
            if(isset($_POST['prettify-epub'])){
                $resultPrettify=self::insertPrettify($epub);
            }
            if(isset($_POST['css']) && !empty($_POST['css'])){
                $epub->reopen();
                $epub->uploadCSS($_POST['css']);
            }


            $epub->backToEpub('content.opf', $epub->opf(), true);
            echo json_encode(array('ok'=>1,'resultPrettify'=>$resultPrettify));
        }else{
            echo json_encode(array('ok'=>false, 'error'=>'Some parameters are missing'));
        }
    }*/

    public function injectCSS(){
        $book = $this->input->post('book');
        $css = $this->input->post('css');
        if(!empty($book)){
            $this->load->helper(array('file','inflector'));
            $this->load->model('Books_model','books');
            $bookData = $this->books->get($book);
            $path = $this->getPath($this->books->getFolderName($bookData['title']), 'css');
            $file = 'extra.css';
            if(write_file($path.$file, $this->input->post('css'), 'w+')){
                echo json_encode(array('ok'=>1));
            }else{
                echo json_encode(array('ok'=>false, 'error'=>'No writable '.$path.$file));
            }
            /*$epub = new EPUB('tmp/'.$_POST['book'].'.epub');
            if(isset($_POST['prettify-epub'])){
                $resultPrettify=self::insertPrettify($epub);
            }
            if(isset($_POST['css']) && !empty($_POST['css'])){
                $epub->reopen();
                $epub->uploadCSS($_POST['css']);
            }

            $epub->backToEpub('content.opf', $epub->opf(), true);*/

        }else{
            echo json_encode(array('ok'=>false, 'error'=>'Some parameters are missing'));
        }
    }

    /**
     * @param EPUB $epub
     * @return bool
     */
    private static function insertPrettify(&$epub)
    {
        $epub->reopen();
        if($epub->insertCSS('js/prettifier/prettify.css')){
            $epub->insertJS('js/prettifier/prettify.js');
            $epub->insertJS('js/prettifier/lang-css.js');
            $epub->insertJS('js/prettifier/lang-sql.js');
            $epub->reopen();
            $zip = zip_open($epub->getLocalFile());
            while ($zip_entry = zip_read($zip)) {
                $entryName = zip_entry_name($zip_entry);
                if (!is_dir($entryName)) {
                    $path_parts = pathinfo($entryName);
                    if (isset ($path_parts['extension']) && strtolower(trim($path_parts['extension'])) == 'xhtml') {
                        if(!$dom = str_get_html($epub->getFromName($entryName))){
                            die($entryName.' '.$epub->getError());
                        }

                        $element = $dom->find('head', 0);
                        $element->innertext =
                            $element->innertext . '<script src="prettify.js" type="text/javascript"/><script type="text/javascript">window.onload=prettyPrint;</script>';
                        foreach ($dom->find('pre') as $e) {
                            $e->class = 'prettyprint linenums';
                        }
                        foreach ($dom->find('code') as $e) {
                            $e->class = 'prettyprint linenums';
                        }

                        $epub->backToEpub($entryName, $dom);
                    }
                }
            }
            zip_close($zip);

        }
        return  false;
    }

    private function store($filename, $path)
    {
        if (empty($_FILES[$filename]) || $_FILES[$filename]["error"] == 4) {
            return; //return silently because no file was uploaded
        }
        if ($_FILES[$filename]["error"] > 0) {
            echo "Error: " . $_FILES[$filename]["error"] . "<br>";
        } else {
            /*echo "Upload: " . $_FILES["resume"]["name"] . "<br>";
            echo "Type: " . $_FILES["resume"]["type"] . "<br>";
            echo "Size: " . ($_FILES["resume"]["size"] / 1024) . " kB<br>";
            echo "Stored temporarily in: " . $_FILES["resume"]["tmp_name"];*/

            move_uploaded_file($_FILES[$filename]["tmp_name"],$path.$_FILES[$filename]["name"]);
            return $path.$_FILES[$filename]["name"];
            //echo "Stored in: " . $storePath . $filePath;
        }
    }

    private function getPath($bookFolderName, $folder)
    {
        $path = dirname(__FILE__).'/../epub/'.$bookFolderName;
        @mkdir($path);
        @mkdir($path=$path.'/'.$folder.'/');
        return $path;
    }

    public function injectCover(){
        $book = $this->input->post('book');
        if(isset($book) && isset($_FILES['cover'])){
            $this->load->model('Books_model','books');
            $bookData = $this->books->get($book);
            $this->load->helper(array('file','inflector'));

            $this->store('cover', $this->getPath($bookData['title'], 'static'));
            /*$coverJpg = self::decodeImg($_POST['cover']);
            $epub = new EPUB('tmp/'.$_POST['book'].'.epub');
            if($coverJpg===false){
                return array('error'=>'Error en la codificacion');
            }*/
//            $epub->setCover($coverJpg);
            echo json_encode(array('ok'=>1));

        }else{
            echo json_encode(array('ok'=>false, 'error'=>'Missing data'));
        }
    }

    private static function decodeImg($base64Content){
        if (!preg_match('/data:([^;]*);base64,(.*)/', $base64Content, $matches)) {
            return false;
        }

        return base64_decode(chunk_split($matches[2]));
    }

    public function addMetadata()
    {
        //TODO: move to console
        $this->load->model('metadata_model','metadata');
        $this->metadata->save($this->input->post('title'), 'title', $this->input->post('book_id'));
        $this->metadata->save($this->input->post('author'), 'creator', $this->input->post('book_id'));
        $this->metadata->save($this->input->post('publisher'), 'publisher', $this->input->post('book_id'));
        $this->metadata->save($this->input->post('rights'), 'rights', $this->input->post('book_id'));
//        $this->metadata->save('title', $this->input->post('title'), $this->input->post('book_id'));

        $token = $this->input->post('token');
        $sections = $this->input->post('sections[]');
        $chapters = $this->input->post('chapters[]');
        if(!empty($token)){
            if(!file_exists(APPPATH.'/epub/profiles/')){
                mkdir(APPPATH.'/epub/profiles/');
            }
            file_put_contents(APPPATH.'/epub/profiles/'.$token.'-content', json_encode(array('sections'=>$sections, 'chapters'=>$chapters)));
        }

        echo json_encode(array('ok'=>1));
    }

    public function fixImages($book){
        //no epub extension
        if ($book) {
            $local = 'tmp/'.$book.'.epub';
            $epub = new EPUB($local);
            $epub->fixImagesReferencesInOPF();
            echo json_encode(array('ok' => 1, 'epub' => $local));
            return;

        }else{
            echo json_encode(array('ok' => false, 'error' => 'Missing info'));
        }
    }
    
    private static function getURL($book, $server)
    {
        $objavi = "http://objavi.booktype.pro/?book=$book&server=$server&mode=epub&destination=nowhere";
        return file_get_contents($objavi);
    }
    
    private static function getFileEpub($book, $url){
    ini_set('max_execution_time', 180);
    $localLink = 'tmp/'.$book.'.epub';
    if(copy(trim($url), $localLink)){
        return $localLink;
    }else{
        echo 'error';
        return false;
    }
}
}