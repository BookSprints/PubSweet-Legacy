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

    public function preview($book, $identifier, $editablecss, $hyphen, $prettify=false, $polyfill = false)
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
                    $chapterRegions = '';
                    $chapterCounter = 0;
                    $chapterRegions = ' .pagination-frontmatter-layout .pagination-contents-column {
                            flow-from: pagination-frontmatter;
                        }

                        .pagination-frontmatter-contents {
                                            flow-into: pagination-frontmatter;
                        } ';
                    $chapterRegions .= $this->getPaginationRegion($chapterCounter);//TOC
                    $chapterCounter++;
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
                            $chapterRegions .= $this->getPaginationRegion($chapterCounter);
                            $chapterCounter++;
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

                        //extra page for every section
                        $chapterRegions .= $this->getPaginationRegion($chapterCounter);
                        $chapterCounter++;
                    }
                }
            }

        }
        $params = array('book' => $book, 'editablecss' =>$editablecss,
            'hyphen' => $hyphen, 'prettify' => $prettify,
            'fullHTML' => $fullHTML, 'css' => $this->loadCss($dir),
            'js'=>$this->loadExtraJs($dir),
            'customConfig'=>$this->loadConfig($identifier, $xml->docTitle->text),
            'chapterRegions'=>$chapterRegions,
            'pageStyles'=>$this->getPageStyle());

        if(isset($xml)){
            $params['bookTitle'] = $xml->docTitle->text;
        }

        if($polyfill){
            $this->load->view('console/preview-polyfill', $params);

        }else{
            $this->load->view('console/preview', $params);

        }
    }

    public function polyfill($book, $identifier, $editablecss, $hyphen, $prettify=false)
    {
        $this->preview($book, $identifier, $editablecss, $hyphen, $prettify, true);
    }

    /**
     * Parse a region definition
     * @param $i
     * @return string
     */
    private function getPaginationRegion($i)
    {
        return '.pagination-body-'.$i.'-layout .pagination-contents-column {
                    flow-from: pagination-body-'.$i.';
                }
                .pagination-body-'.$i.'-contents {
                    flow-into: pagination-body-'.$i.';
                }
                ';//let stay this space, for future concatenations
    }
    
    private function getPageStyle(/*$pageHeight, $pageWidth, $contentsWidth, $contentsBottomMargin,
        $contentsHeight, $imageMaxHeight, $imageMaxWidth, $pagenumberBottomMargin, $headerTopMargin,
        $headerTopMargin, $innerMargin, $outerMargin, $columnWidth, $contentsColumnSeparatorWidth,
        $marginNotesWidth, $marginNotesVerticalSeperator, $marginNotesSeparatorWidth*/){

        /*defaults = {
            // pagination.config starts out with default config options.
            'sectionStartMarker': 'h1',
            'sectionTitleMarker': 'h1',
            'chapterStartMarker': 'h2',
            'chapterTitleMarker': 'h2',
            'flowElement': 'document.body',
            'alwaysEven': false,
            'columns': 1,
            'enableFrontmatter': true,
            'enableTableOfFigures': false,
            'enableTableOfTables': false,
            'enableMarginNotes': false,
            'bulkPagesToAdd': 50,
            'pagesToAddIncrementRatio': 1.4,
            'frontmatterContents': '',
            'autoStart': true,
            'numberPages': true,
            'divideContents': true,
            'footnoteSelector': '.pagination-footnote',
            'topfloatSelector': '.pagination-topfloat',
            'marginnoteSelector': '.pagination-marginnote',
            'maxPageNumber': 10000,
            'columnSeparatorWidth': 0.09,
            'outerMargin': 0.5,
            'innerMargin': 0.8,
            'contentsTopMargin': 0.8,
            'headerTopMargin': 0.3,
            'contentsBottomMargin': 0.8,
            'pagenumberBottomMargin': 0.3,
            'pageHeight': 8.3,
            'pageWidth': 5.8,
            'marginNotesWidth': 1.0,
            'marginNotesSeparatorWidth': 0.09,
            'marginNotesVerticalSeparator': 0.09,
            'lengthUnit': 'in'
        };*/

        $pageHeight = 9.68;
        $pageWidth = 7.44;
        $innerMargin = 0.8;
        $outerMargin = 0.5;
        $marginNotesWidth = 1.0;
        $enableMarginNotes = false;
        $marginNotesSeparatorWidth = 0.09;
        $lengthUnit = 'in';
        $contentsBottomMargin = 0.8;
        $contentsTopMargin =  0.8;
        $pagenumberBottomMargin = 0.3;
        $headerTopMargin = 0.3;
        $columns = 1;
        $columnSeparatorWidth = 0.09;
        $marginNotesVerticalSeparator = 0.09;


        $unit = $lengthUnit;
        $pageHeight = $pageHeight.$unit;
        $pageWidth = $pageWidth.$unit;
        $marginNotesWidthNumber = $enableMarginNotes ? $marginNotesWidth : 0;
        $marginNotesSeparatorWidth = $enableMarginNotes ? $marginNotesSeparatorWidth : 0;
        $marginNotesSeparatorWidthNumber = $enableMarginNotes ? $marginNotesSeparatorWidth : 0;

        $contentsWidthNumber = $pageWidth - $innerMargin - $outerMargin -
            ($marginNotesWidthNumber + $marginNotesSeparatorWidthNumber);
        $contentsHeightNumber = $pageHeight - $contentsTopMargin - $contentsBottomMargin;
        $contentsColumnSeparatorWidthNumber = $columnSeparatorWidth;

        $contentsWidth = $contentsWidthNumber . $unit;
        $contentsBottomMargin = $contentsBottomMargin.$unit;
        $contentsHeight = $contentsHeightNumber . $unit;
        $imageMaxHeight = ($contentsHeightNumber - 0.1) . $unit;
        $imageMaxWidth = ($contentsWidthNumber - 0.1) . $unit;
        $pagenumberBottomMargin = $pagenumberBottomMargin . $unit;
        $headerTopMargin = $headerTopMargin.$unit;
        $innerMargin = $innerMargin.$unit;
        $outerMargin = $outerMargin.$unit;
        $columnWidth = $contentsWidthNumber / $columns -
            ($contentsColumnSeparatorWidthNumber * ($columns - 1))
            + $unit;
        $contentsColumnSeparatorWidth = $contentsColumnSeparatorWidthNumber.$unit;
        $marginNotesWidth = $marginNotesWidthNumber.$unit;
        $marginNotesVerticalSeparator = !!$marginNotesVerticalSeparator ? $marginNotesVerticalSeparator . $unit : 0;
        $marginNotesSeparatorWidth = $marginNotesSeparatorWidth.$unit;


        return ".pagination-page {height:" . $pageHeight . "; width:" . $pageWidth . ";" . "background-color: white;}" .
//        ".pagination-contents-item{height:" . $pageHeight ."}" .
        "\n@page {size:" . $pageWidth . " " . $pageHeight . ";}" .
        "\nbody {background-color: #efefef;}"
        // A .page.simple is employed when CSS Regions are not accessible
        . "\n.pagination-simple {padding: 1in;}"
        // To give the appearance on the screen of pages, add a space of .2in
        //. "\n@media screen{.pagination-page {border: solid 1px #000; " .

        . "\n@media screen{.pagination-page {border: solid 1px #000; " .
        "margin-bottom:.2in;}}" .
        "\n.pagination-main-contents-container {width:" . $contentsWidth . ";}" .
        "\n.pagination-contents-container {bottom:" . $contentsBottomMargin . "; height:" . $contentsHeight . "; " .
        "display: -webkit-flex; display: flex;}"
        // Images should at max size be slightly smaller than the contentsWidth.
        . "\nimg {max-height: " . $imageMaxHeight . ";max-width: " .
        $imageMaxWidth . ";}" . "\n.pagination-pagenumber {bottom:" .
        $pagenumberBottomMargin . ";}" . "\n.pagination-header {top:" .
        $headerTopMargin . ";}" .
        "\n#pagination-toc-title:before {content:'Contents';}" .
        "\n#pagination-tof-title:before {content:'Figures';}" .
        "\n#pagination-tot-title:before {content:'Tables';}" .
        "\n.pagination-page:nth-child(odd) .pagination-contents-container, " .
        ".pagination-page:nth-child(odd) .pagination-pagenumber," .
        ".pagination-page:nth-child(odd) .pagination-header {" . "right:" .
        $outerMargin . ";left:" . $innerMargin . ";}" .
        "\n.pagination-page:nth-child(even) .pagination-contents-container, " .
        ".pagination-page:nth-child(even) .pagination-pagenumber," .
        ".pagination-page:nth-child(even) .pagination-header {" . "right:" .
        $innerMargin . ";left:" . $outerMargin . ";}" .
        "\n.pagination-page:nth-child(odd) .pagination-pagenumber," .
        ".pagination-page:nth-child(odd) .pagination-header {" .
        "text-align:right;}" .
        "\n.pagination-page:nth-child(even) .pagination-pagenumber," .
        ".pagination-page:nth-child(even) .pagination-header {" .
        "text-align:left;}" .
        "\n.pagination-footnote > * > * {font-size: 0.7em; margin:.25em;}" .
        "\n.pagination-footnote > * > *::before, .pagination-footnote::before " .
        "{position: relative; top: -0.5em; font-size: 80%;}" .
        "\n.pagination-toc-entry .pagination-toc-pagenumber, " .
        ".pagination-tof-entry .pagination-tof-pagenumber, " .
        ".pagination-tot-entry .pagination-tot-pagenumber {float:right}"
        /* This seems to be a bug in Webkit. But unless we set the width of the 
         * original element that is being flown, some elements extend beyond the
         * mainContentsContainer's width.
         */

        . "\n.pagination-contents-item {width:" . $columnWidth . ";}" .
        "\n.pagination-frontmatter-contents {width:" . $contentsWidth . ";}"
        . "\n.pagination-contents-column-separator {width:" . $contentsColumnSeparatorWidth . ";}" .
        // Footnotes in non-CSS Regions browsers will render as right margin notes.
        "\n.pagination-simple .pagination-footnote > span {" .
        "position: absolute; right: 0in; width: 1in;}" .
        "\n.pagination-marginnotes, .pagination-marginnote-item {width:" . $marginNotesWidth . ";}" .
        "\n.pagination-marginnote-item {margin-bottom:" . $marginNotesVerticalSeparator . ";}" .
        "\n.pagination-marginnotes-separator {width:" . $marginNotesSeparatorWidth . ";}" .
        "\n.pagination-main-contents-container, .pagination-marginnotes, .pagination-marginnotes-separator {height:" . $contentsHeight . ";}";

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

    private function loadExtraJs($dir)
    {
        $this->load->helper('file');
        $files = get_dir_file_info($dir.'/js', FALSE);
        return $files;

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
                'polyfill': true

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
                            . base64_encode(file_get_contents($dir.'/'.$uri));
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

    /**
     * Read a file content and dumps it back
     * @param $book
     * @param $file
     */
    public function js($book, $file)
    {
        $file = dirname(__FILE__) . '/../epub/' . $book.'/js/'.$file;
        echo file_get_contents($file);
    }
}