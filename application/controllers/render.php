<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-27-13
 * Time: 09:30 PM
 *
 * http://www.hxa.name/articles/content/epub-guide_hxa7241_2007.html
 */

class Render extends CI_Controller{
    private $path = 'application/epub/';
    private $book = NULL;
    private $bookname = NULL;
    private $cssFiles = NULL;
    private $images = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('DX_Auth');
        $this->load->helper('url');
        if (!$this->session->userdata('DX_user_id')){
            redirect('register/login', 'refresh');
        }
    }

    /**
     * @param $id Book ID
     */
    public function epub($id)
    {
        $this->bookId = $id;
        $this->load->helper(array('file','inflector'));
        $this->load->model('Books_model','books');
        $this->load->model('Chapters_model','chapters');
        $this->book = $this->books->get($id);
        $this->bookname = underscore($this->book['title']);
        $chapters = $this->chapters->find($id);
        $path = $this->path.$this->bookname.'/';
        @mkdir($path);

        $this->cssFiles = $this->getCSSFiles();

        $toc = array();
        foreach ($chapters as $item) {
            if($item['editor_id']==1){
                $xhtml = $this->renderLexiconChapter($item);
            }else{
                $xhtml = $this->renderNormalChapter($item);
            }

            $chapterFileName = underscore(url_title($item['title'], '_', true)).'.xhtml';
            if(!write_file ($path.$chapterFileName, $xhtml, 'w+')){
                echo 'Error creating '. $item['title'];
            }
            $toc[] = array('title'=>$item['title'],
                'url'=>$chapterFileName, 'section'=>$item['section_title']);
        }
        $cover = false;
        if(file_exists($this->path.$this->bookname.'/static/cover.jpg')){
            $cover = true;
            $this->createCoverHtml();
        }

        $this->createEpubTOC(array('chapters'=>$toc,
            'book_name'=>$this->book['title'], 'cover'=>$cover), $path);
        $this->createEpubContentOPF(
            array('chapters'=>$toc,
                'book_name'=>$this->book['title'],
                'css'=>$this->getCSSFiles(),
                'metadata'=>$this->getMetadata(),
                'images'=>$this->getImages()), $path);
        $this->createMimetype($path);
        $this->createContainerXML(null, $path);

        if($this->input->post('download')!=="false"){
            $this->export($path, $this->bookname);
        }else{
            echo json_encode(array('ok'=>1));
        }

    }

    /**
     * @param $path
     * @param $bookname
     */
    private function export($path, $bookname)
    {
        $this->load->library('zip');
        $this->zip->read_dir($path, FALSE, $path);
        //$this->zip->archive('application/epub/'.$bookname.'.epub');
        $this->zip->download($bookname.'.epub');

    }

    private function createEpubTOC($data)
    {
        $toc = $this->load->view('epub/toc-ncx', $data, true);
        if(!write_file ($this->path.$this->bookname.'/toc.ncx', $toc, 'w+')){
            echo 'Error creating toc.ncx';
        }
    }

    private function getCSSFiles()
    {
        if($this->cssFiles==null){
            $files = get_dir_file_info($this->path.$this->bookname.'/css', FALSE);
            $this->cssFiles = $files;
        }
        return $this->cssFiles;
    }

    private function getMetadata()
    {
        $this->load->model("Metadata_model", 'metadata');
        return $this->metadata->get($this->bookId);
    }

    private function createEpubContentOPF($data)
    {
        $content = $this->load->view('epub/content-opf', $data, true);
        if(!write_file ($this->path.$this->bookname.'/content.opf', $content, 'w+')){
            echo 'Error creating content.opf';
        }
    }

    private function createMimetype()
    {
        if(!write_file ($this->path.$this->bookname.'/mimetype', 'application/epub+zip', 'w+')){
            echo 'Error creating mimetype';
        }
    }

    private function createContainerXML($data)
    {
        $content = $this->load->view('epub/container-xml', $data, true);
        @mkdir($this->path.$this->bookname.'/META-INF');
        if(!write_file ($this->path.$this->bookname.'/META-INF/container.xml', $content, 'w+')){
            echo 'Error creating META-INF/container.xml';
        }
    }

    private function getLexiconContent($chapter)
    {
        $data['chapter'] = $chapter;
        $data['datetime'] = false;
        $this->load->model('Dictionary_entries_model','dictionary');
        $data['entries'] = $this->dictionary->term_list($chapter['id']);
        $this->load->model('Definitions_model','definitions');
        $result = $this->definitions->getAllByChapters($chapter['id']);
        $definitions = array();
        foreach ($result as $item) {
            $definitions[$item['term_id']][$item['language_id']] = $item;
        }
        $data['definitions'] = $definitions;
//        echo '<pre>';print_r($data['definitions']);echo '</pre>';
        return $this->load->view('epub/dictionary-html', $data, true);
    }

    private function renderLexiconChapter($chapter)
    {
        $content = $this->getLexiconContent($chapter);
        if(isset($this->html) && $this->html){
            return $content;
        }else{
            $content = $this->fixImageLinks($content);
            return $this->getXhtml(array('title'=>$chapter['title'], 'content'=>$content));
        }

    }

    private function getXhtml($data){
        $data['css']=$this->cssFiles;
        return $this->load->view('epub/xhtml', $data, true);
    }

    /**
     * takes HTML contents and wrap it with valid xhtml structure
     * @param $item HTML
     * @return mixed
     */
    private function renderNormalChapter($item)
    {
        if(isset($this->html) && $this->html){
            return empty($item['content'])?'<h1>'.$item['title'].'</h1>':$item['content'];
        }elseif(isset($this->structure) && $this->structure){
            return $this->getStructure($item);
        }else{
            $content = $this->fixImageLinks($item['content']);
            return $this->getXhtml(array('title'=>$item['title'], 'content'=>$content));
        }
    }

    public function fixImageLinks($content)
    {
        if(!function_exists('str_get_html')){
            require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        }

        $dom = str_get_html($content);
        if(empty($dom)){
            return '';
        }

        foreach($dom->find('img') as $element){

            if(strpos($element->src, base_url())!==false){
                if(strpos($element->src, base_url().'public/uploads/'.url_title($this->book['title']).'/')!==false){
                    $element->src = str_replace(base_url().'public/uploads/'.url_title($this->book['title'].'/'), 'graphics/', $element->src);
                }else if(strpos($element->src, base_url().'public/uploads/')!==false){
                    $element->src = str_replace(base_url().'public/uploads/','graphics/', $element->src);
                }

                $css = $this->BreakCSS('image{'.$element->style.'}');
                $this->images[str_replace('graphics/','', $element->src)] = array(
                    'src'=>$element->src,
                    'height'=>$css['image']['height'],
                    'width'=>$css['image']['width']
                );

            }

        }
        return $dom->innertext;
    }

    function BreakCSS($css)
    {

        $results = array();

        preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
        foreach($matches[0] AS $i=>$original)
            foreach(explode(';', $matches[2][$i]) AS $attr)
                if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
                {
                    list($name, $value) = explode(':', $attr);
                    $results[$matches[1][$i]][trim($name)] = trim($value);
                }
        return $results;
    }


    public function getStructure($item)
    {
        if(!function_exists('str_get_html')){
            require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        }

        $dom = str_get_html($item['content']);
        if(empty($dom)){
            return '';
        }
        $headings = $dom->find('h1,h2,h3,h4,h5');
        $result = '';
        foreach ($headings as $item) {
            $result .= $item->outertext();
        }

        $par = $dom->find('p');
        $result .= empty($par[0])?'':$par[0]->outertext();
        return $result;
    }

    public function chapter($id)
    {
        $this->load->model('Chapters_model','chapters');
        $item = $this->chapters->get($id);
        switch($item['editor_id']){
            case 1:
                echo $this->renderLexiconChapter($item);
                break;
            case 2:
                echo $this->renderNormalChapter($item);
                break;
            default:
                echo 'Something is wrong';
                break;
        }
    }

    private function createCoverHtml()
    {

        $content = '<div id="cover-image">'.
                '<img src="static/cover.jpg" alt="cover"/>'.
                '</div>';
        $this->getXhtml(array('title'=>'Cover', 'content'=>$content));
        return false;
    }

    private function getImages(){
//        $this->load->helper('directory');
        $uploadsFolder = BASEPATH.'../public/uploads/';
        if(!file_exists($this->path.$this->bookname.'/graphics')){
            mkdir($this->path.$this->bookname.'/graphics');
        }
//        directory_copy(BASEPATH.'../public/uploads/'.$folderName, $this->path.$this->bookname.'/graphics');
//        $this->load->helper('file');
//        $files = get_filenames($this->path.$this->bookname.'/graphics');
//        return $files;
        $config['image_library'] = 'gd2';
        $config['maintain_ratio'] = TRUE;
        foreach ($this->images as $key=>$image) {
            if(file_exists($uploadsFolder.url_title($this->book['title']).'/'.$key)){
                copy($uploadsFolder.url_title($this->book['title']).'/'.$key, $this->path.$this->bookname.'/graphics/'.$key);
            }else if(file_exists($uploadsFolder.$key)){
                copy($uploadsFolder.$key, $this->path.$this->bookname.'/graphics/'.$key);
            }

            if(extension_loaded($config['image_library'])){
                $config['source_image'] = $this->path.$this->bookname.'/graphics/'.$key;
    //            $config['create_thumb'] = TRUE;

                $config['width'] = str_replace('px','',$image['width']);
                $config['height'] = str_replace('px','',$image['height']);

                $this->load->library('image_lib', $config);

                if ( ! $this->image_lib->resize())
                {
                    echo $this->image_lib->display_errors();
                }
            }

        }
        return $this->images;
    }

    /**
     * Will render the book's content as plain html
     * @param $id
     */
    public function html($id, $draft = false)
    {
        $this->html = true;
        $this->load->model('Chapters_model', 'chapters');
        $chapters = $this->chapters->find($id);

        ob_start();
        $currentSection = null;
        foreach ($chapters as $item) {
            if($currentSection != $item['section_id']){
                echo '<h1 class="section">'.$item['section_title'].'</h1>';
            }
            $this->chapter($item['id']);
            $currentSection = $item['section_id'];
        }
        $originalContent = ob_get_contents();
        ob_end_clean();
        $this->load->view('templates/simple/header', array('id'=>$id, 'draft'=>$draft, 'content'=>$originalContent));
        $this->load->view('templates/simple/footer', array('draft'=>$draft));
    }

    /**
     * Will render the book's chapter name and first paragraph
     * @param $id
     */
    public function structure($id, $draft = false)
    {
        $this->structure = true;
        $this->load->model('Chapters_model', 'chapters');
        $chapters = $this->chapters->find($id);

        ob_start();
        $currentSection = null;
        foreach ($chapters as $item) {
            if($currentSection != $item['section_id']){
                echo '<h1 class="section">'.$item['section_title'].'</h1>';
            }
            $this->chapter($item['id']);
            $currentSection = $item['section_id'];
        }
        $originalContent = ob_get_contents();
        ob_end_clean();
        $this->load->view('templates/simple/header', array('id'=>$id, 'draft'=>$draft, 'content'=>$originalContent));
        $this->load->view('templates/simple/footer', array('draft'=>$draft));
    }
}

if(!function_exists('directory_copy'))
{
    function directory_copy($srcdir, $dstdir)
    {
        //preparing the paths
        $srcdir=rtrim($srcdir,'/');
        $dstdir=rtrim($dstdir,'/');

        //creating the destination directory
        if(!is_dir($dstdir))mkdir($dstdir, 0777, true);

        //Mapping the directory
        $dir_map=directory_map($srcdir);

        foreach($dir_map as $object_key=>$object_value)
        {
            if(is_numeric($object_key))
                copy($srcdir.'/'.$object_value,$dstdir.'/'.$object_value);//This is a File not a directory
            else
                directory_copy($srcdir.'/'.$object_key,$dstdir.'/'.$object_key);//this is a directory
        }
    }
}