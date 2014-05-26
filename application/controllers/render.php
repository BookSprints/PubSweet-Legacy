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
    private $cssFiles = NULL;
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
        $book = $this->books->get($id);
        $bookName = underscore($book['title']);
        $this->book = $bookName;
        $chapters = $this->chapters->find($id);
        $path = $this->path.$this->book.'/';
        @mkdir($path);

        $this->cssFiles = $this->getCSSFiles();

        $toc = array();
        foreach ($chapters as $item) {
            if($item['editor_id']==1){
                $xhtml = $this->renderLexiconChapter($item);
            }else{
                $xhtml = $this->renderNormalChapter($item);
            }

            $chapterFileName = underscore($item['title']).'.xhtml';
            if(!write_file ($path.$chapterFileName, $xhtml, 'w+')){
                echo 'Error creating '. $item['title'];
            }
            $toc[] = array('title'=>$item['title'],
                'url'=>$chapterFileName);
        }
        $cover = false;
        if(file_exists($this->path.$this->book.'/static/cover.jpg')){
            $cover = true;
            $this->createCoverHtml();
        }

        $this->createEpubTOC(array('chapters'=>$toc,
            'book_name'=>$book['title'], 'cover'=>$cover), $path);
        $this->createEpubContentOPF(
            array('chapters'=>$toc,
                'book_name'=>$book['title'],
                'css'=>$this->getCSSFiles(),
                'metadata'=>$this->getMetadata()), $path);
        $this->createMimetype($path);
        $this->createContainerXML(null, $path);
        $this->export($path, $bookName);
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
        if(!write_file ($this->path.$this->book.'/toc.ncx', $toc, 'w+')){
            echo 'Error creating toc.ncx';
        }
    }

    private function getCSSFiles()
    {
        if($this->cssFiles==null){
            $files = get_dir_file_info($this->path.$this->book.'/css', FALSE);
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
        if(!write_file ($this->path.$this->book.'/content.opf', $content, 'w+')){
            echo 'Error creating content.opf';
        }
    }

    private function createMimetype()
    {
        if(!write_file ($this->path.$this->book.'/mimetype', 'application/epub+zip', 'w+')){
            echo 'Error creating mimetype';
        }
    }

    private function createContainerXML($data)
    {
        $content = $this->load->view('epub/container-xml', $data, true);
        @mkdir($this->path.$this->book.'/META-INF');
        if(!write_file ($this->path.$this->book.'/META-INF/container.xml', $content, 'w+')){
            echo 'Error creating META-INF/container.xml';
        }
    }

    private function getLexiconContent($chapter)
    {
        $data['chapter'] = $chapter;
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
            return $this->getXhtml(array('title'=>$item['title'], 'content'=>$item['content']));
        }
    }

    public function getStructure($item)
    {

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
        require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
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