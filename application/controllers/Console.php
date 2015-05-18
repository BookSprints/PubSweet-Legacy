<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-02-13
 * Time: 10:26 PM
 */

class Console extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('DX_Auth');
        $this->load->helper('url');
        if (!$_SESSION['DX_user_id']){
            redirect('register/login', 'refresh');
        }
    }

    public function wizard($id)
    {
        $this->load->model('books_model', 'book');
        $this->load->model('chapters_model', 'chapters');
        $data['book'] = $this->book->get($id);
        $data['sections'] = $this->chapters->findGrouped($id);
        $data['defaultConfig'] =
            "window.paginationConfig = {
                    'sectionStartMarker': 'div.section',
                    'sectionTitleMarker': 'h1.sectiontitle',
                    'chapterStartMarker': 'div.chapter',
                    'chapterTitleMarker': 'h1.chaptertitle',
                    'flowElement': \"document.getElementById('flow')\",
                    'alwaysEven': false,
                    'enableFrontmatter': true,
                    'bulkPagesToAdd': 50,
                    'pagesToAddIncrementRatio': 1.4,
                    'pageHeight': 9.68,
                    'pageWidth': 7.44,
                    'lengthUnit: ': 'in',
                    'oddAndEvenMargins': false,
                    'frontmatterContents': '".$data['book']['title']."</h1>'
                        + '<div class=\"pagination-pagebreak\"></div>',
                    'autoStart': true,

                };";
        $data['bookName'] = $this->book->getFolderName($data['book']['title']);
        $this->load->view('console/wizard', $data);
    }

    public function saveSettings()
    {
        $token = $this->input->post('settings-token');
        $bookJSConfig = $this->input->post('bookjs-config');
        if(!empty($token)){
            if(!file_exists(APPPATH.'/epub/profiles/')){
                mkdir(APPPATH.'/epub/profiles/');
            }
            file_put_contents(APPPATH.'/epub/profiles/'.$token, $bookJSConfig);
        }
    }

    public function preview($book, $identifier, $editablecss, $hyphen, $prettify=false)
    {
        require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        if (isset($book)) {
            $dir = dirname(__FILE__).'/../epub/' . $book.'/';

            if(file_exists($dir.'toc.ncx')){
                $toc = file_get_contents($dir.'toc.ncx');
                if($toc!==false){
                    $xml = new SimpleXMLElement($toc);
                    $sections = array();
                    $fullHTML = '';

                    foreach ($xml->navMap->navPoint as $navPoint) {
                        $sectionId = $navPoint->content->attributes()->src[0];
                        $sectionPage = '<div class="section">';
                        $sectionPage .= '<h1 class="sectiontitle">' . $navPoint->navLabel->text . '</h1>';
                        $i = 0;
                        while (isset($navPoint->navPoint[$i])) {
                            $sectionPage .= '<h2 class="sectionchaptertitle">' . ($navPoint->navPoint[$i]->navLabel->text) . '</h2>';
                            ++$i;
                        }
                        $sectionPage .= '</div>';
                        $sections[$sectionId . ''] = $sectionPage;
                        $fullHTML .= $sectionPage;
                        $i=0;

                        while(isset($navPoint->navPoint[$i])){
                            $chapter = $navPoint->navPoint[$i];
                            $entry = (string) $chapter->content->attributes()->src[0];

                                if ($entry == 'cover.xhtml') {
                                    continue;
                                }
                                $xhtml = file_get_contents($dir.$entry);
                                $globalDom = str_get_html($xhtml);
                                $dom = $globalDom->find('body', 0);


                            if (isset($_GET['prettify']) && $_GET['prettify']) {
                                    foreach ($dom->find('pre, code') as $element) {
                                        $element->class = 'prettyprint linenums';
                                        $element->outertext = '<div class="no-page-break">' . $element->outertext . '</div>';
                                    }
                                }

                                foreach ($dom->find('img') as $element) {
                                        $uri = $element->src;
                                    if(file_exists($dir.$uri)){

                                        if (!empty($uri) && $uri != '#' && !preg_match('/[http|ftp|https|mailto|data]:/', $uri)) {
                                            $parts = pathinfo($uri);
                                            $element->src = 'data:image/' . (empty($parts['extension']) ? 'jpeg' : $parts['extension']) . ';base64,' .
                                                base64_encode(file_get_contents($dir.$uri));
                                        }
                                        $parent = $element->parent();
                                        if($parent->tag=='p'){
                                            $parent->setAttribute('class', $parent->getAttribute('class').' has-image');
                                        }
                                    }


                                }
                                foreach ($dom->find('h1') as $element) {
                                    $element->class = 'chaptertitle';
                                }

                                foreach ($dom->find('h2,h3') as $element) {
                                    $next = $element->next_sibling();
                                    if (!empty($next)) {
                                        $element->outertext = '<div class="no-page-break">' . $element->outertext . $next->outertext . '</div>';
                                        $next->outertext = '';
                                    }
                                }

                                foreach ($dom->find('table#bluebox') as $element) {
                                    $element->outertext = '<div>' . $element->outertext . '</div>';
                                }

                                foreach($dom->find('a') as $element){

                                    if(strpos($element->href, '.xhtml')!==false){

                                        $parts = explode('.xhtml', $element->href);

                                        $element->href = $parts[1];

                                    }

                                }

                                $fullHTML .= (isset($sections[$entry]) ? $sections[$entry] : '')
                                    . '<div class="chapter">' . $dom->innertext . '</div>';

                            ++$i;
                        }

                    }
                }
            }

        }

        $params = array('book' => $book, 'editablecss' =>$editablecss,
            'hyphen' => $hyphen, 'prettify' => $prettify,
            'fullHTML' => $fullHTML, 'css' => $this->loadCss($dir),
            'customConfig'=>$this->loadConfig($identifier, $xml->docTitle->text));
        if(isset($xml)){
            $params['bookTitle'] = $xml->docTitle->text;
        }

        $this->load->view('console/preview', $params);
    }

    /**
     * Load extra css files
     * @param $dir
     * @return array
     */
    private function loadCss($dir)
    {
        $css = [];
        if(file_exists($dir.'objavi.css')){
            $css[] = file_get_contents($dir.'objavi.css');
        }
        if(file_exists($dir.'css/extra.css')){
            $css[] = file_get_contents($dir.'css/extra.css');
        }

        return $css;
    }

    private function loadConfig($identifier, $bookTitle)
    {
        if(file_exists(APPPATH.'/epub/profiles/'.$identifier)){
            return file_get_contents(APPPATH.'/epub/profiles/'.$identifier);
        }else{
            return "window.paginationConfig = {
                'sectionStartMarker': 'div.section',
                'sectionTitleMarker': 'h1.sectiontitle',
                'chapterStartMarker': 'div.chapter',
                'chapterTitleMarker': 'h1.chaptertitle',
                'flowElement': \"document.getElementById('flow')\",
                'alwaysEven': false,
                'enableFrontmatter': true,
                'bulkPagesToAdd': 50,
                'pagesToAddIncrementRatio': 1.4,
                'pageHeight': 9.68,
                'pageWidth': 7.44,
                'lengthUnit': 'in',
                'oddAndEvenMargins': false,
                'frontmatterContents': '<h1>".$bookTitle."</h1>'
                    + '<div class=\"pagination-pagebreak\"></div>',
                'autoStart': true,
                'polyfill': true;

            };";
        }
    }

    public function livecss($book, $editablecss, $hyphen, $prettify)
    {
        require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        if (isset($book)) {
            $dir = dirname(__FILE__) . '/../epub/' . $book;

            $dirFiles = scandir($dir);

            $xhtmlFiles = array();

//            if ($zip) {

            foreach($dirFiles as $file) {
//                    $file = file_get_contents( $dir . "/" . $file);
                if (!is_dir($dir . "/" . $file)) {
                    $path_parts = pathinfo($dir . "/" . $file);

                    $ext = strtolower(trim(isset ($path_parts['extension']) ? $path_parts['extension'] : ''));

                    if ($ext == 'xhtml') {

                        $xhtmlFiles[] = $file;

                    }
                }
//                }
            }
//            zip_close($zip);
            asort($xhtmlFiles);

//            $zip1 = new ZipArchive;
            //Opens a Zip archive
//            $epub = $zip1->open($file);
            $toc = file_get_contents($dir.'/toc.ncx');
            $xml = new SimpleXMLElement($toc);
            $sections = array();
            foreach ($xml->navMap->navPoint as $navPoint) {
                $sectionId = $navPoint->content->attributes()->src[0];
                $sectionPage = '<div class="section">';
                $sectionPage .= '<h1 class="sectiontitle">' . $navPoint->navLabel->text . '</h1>';
                $i = 0;
                while (isset($navPoint->navPoint[$i])) {
                    $sectionPage .= '<h2 class="sectionchaptertitle">' . ($navPoint->navPoint[$i]->navLabel->text) . '</h2>';
                    ++$i;
                }
                $sectionPage .= '</div>';
                $sections[$sectionId . ''] = $sectionPage;
            }
            $fullHTML = '';
            foreach ($xhtmlFiles as $entry) {
                if ($entry == 'cover.xhtml') {
                    continue;
                }
                $xhtml = file_get_contents($dir.'/'.$entry);
                $dom = str_get_html($xhtml);

                if (isset($_GET['prettify']) && $_GET['prettify']) {
                    foreach ($dom->find('pre, code') as $element) {
                        $element->class = 'prettyprint linenums';
                        $element->outertext = '<div class="no-page-break">' . $element->outertext . '</div>';
                    }
                }

                foreach ($dom->find('img') as $element) {
                    $uri = $element->src;
                    if (!empty($uri) && $uri != '#' && !preg_match('/[http|ftp|https|mailto|data]:/', $uri)) {
                        $parts = pathinfo($uri);
                        $element->src = 'data:image/' . (empty($parts['extension']) ? 'jpeg' : $parts['extension']) . ';base64,'
                            . base64_encode(file_get_contents($dir.$uri));
                    }

                }
                foreach ($dom->find('h1') as $element) {
                    $element->class = 'chaptertitle';
                }

                foreach ($dom->find('h2,h3') as $element) {
                    $next = $element->next_sibling();
                    if (!empty($next)) {
                        $element->outertext = '<div class="no-page-break">' . $element->outertext . $next->outertext . '</div>';
                        $next->outertext = '';
                    }
                }

                foreach ($dom->find('table#bluebox') as $element) {
                    $element->outertext = '<div>' . $element->outertext . '</div>';
                }

                $body = $dom->find('body', 0);
                $fullHTML .= (isset($sections[$entry]) ? $sections[$entry] : '')
                    . '<div class="chapter">' . $body->innertext . '</div>';
            }

            /** CSS */
            $css = file_exists($dir.'/objavi.css')?file_get_contents($dir.'/objavi.css'):'';
        }
        $this->load->view('console/livecss', array('book' => $book, 'editablecss' => $editablecss,
            'hyphen' => $hyphen, 'prettify' => $prettify,
            'fullHTML' => $fullHTML, 'css' => $css, 'bookTitle' => $xml->docTitle->text,
            'url'=>$book.'/'.$editablecss.'/'.$hyphen.'/'.$prettify));
    }
}

/*var as = document.getElementsByTagName('a');
for(var i=0;i<as.length;i++){
    if(as[i].href.indexOf('xhtml')!==-1){
        var parts = as[i].href.split('xhtml');
as[i].href=parts[1];
    }
}*/