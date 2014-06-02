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
        $this->load->helper('url');
    }

    public function wizard($id)
    {
        $this->load->model('books_model', 'book');
        $data['book'] = $this->book->get($id);
        $this->load->view('wizard', $data);
    }

    public function preview($book, $editablecss, $hyphen, $prettify)
    {
        require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        if (isset($book)) {
            $dir = '../application/epub/' . $book;

            $dirFiles = scandir($dir);

            $xhtmlFiles = array();

            /*foreach($dirFiles as $file) {

                if (!is_dir($dir . "/" . $file)) {
                    $path_parts = pathinfo($dir . "/" . $file);

                    $ext = strtolower(trim(isset ($path_parts['extension']) ? $path_parts['extension'] : ''));

                    if ($ext == 'xhtml') {

                        $xhtmlFiles[] = $file;

                    }
                }
            }

            asort($xhtmlFiles);*/

//            $zip1 = new ZipArchive;
            //Opens a Zip archive
//            $epub = $zip1->open($file);
            if(file_exists($dir.'/toc.ncx')){
                $toc = file_get_contents($dir.'/toc.ncx');
                if($toc!==false){
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
                        $i=0;
                        while(isset($navPoint->navPoint[$i])){
                            $chapter = $navPoint->navPoint[$i];
                            $xhtmlFiles[] = (string) $chapter->content->attributes()->src[0];
                            ++$i;
                        }

                    }
                }
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
                        $element->src = 'data:image/' . (empty($parts['extension']) ? 'jpeg' : $parts['extension']) . ';base64,' . base64_encode($zip1->getFromName($uri));
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
            $css[] = file_exists($dir.'/objavi.css')?file_get_contents($dir.'/objavi.css'):'';
            $css[] = file_exists($dir.'/css/extra.css')?file_get_contents($dir.'/css/extra.css'):'';
        }

        $params = array('book' => $book, 'editablecss' => $editablecss,
            'hyphen' => $hyphen, 'prettify' => $prettify,
            'fullHTML' => $fullHTML, 'css' => $css);
        if(isset($xml)){
            $params['bookTitle'] = $xml->docTitle->text;
        }

        $this->load->view('preview', $params);
    }

    public function livecss($book, $editablecss, $hyphen, $prettify)
    {
        require dirname(__FILE__) . '/../libraries/simple_html_dom.php';
        if (isset($book)) {
            $dir = '../application/epub/' . $book;

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
                        $element->src = 'data:image/' . (empty($parts['extension']) ? 'jpeg' : $parts['extension']) . ';base64,' . base64_encode($zip1->getFromName($uri));
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
        $this->load->view('livecss', array('book' => $book, 'editablecss' => $editablecss,
            'hyphen' => $hyphen, 'prettify' => $prettify,
            'fullHTML' => $fullHTML, 'css' => $css, 'bookTitle' => $xml->docTitle->text,
            'url'=>$book.'/'.$editablecss.'/'.$hyphen.'/'.$prettify));
    }
}