<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 10/24/14
 * Time: 11:58 PM
 */

class Importer extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }
    }

    public function form()
    {
        $this->load->helper('form');
        $this->load->view('templates/header');
        $this->load->view('admin/import');
        $this->load->view('templates/footer');
    }

    /**
     * Process and save the data from a epub file
     */
    public function save()
    {
        $config['upload_path'] = APPPATH.'../public/uploads/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if($this->upload->do_upload("epub")){
            $data = $this->upload->data();
            $this->load->library('EPUB', array('file'=>$data['full_path']));

            $this->load->model('books_model', 'bookModel');
            $this->load->model('sections_model', 'sectionModel');
            $this->load->model('chapters_model', 'chapterModel');
            $bookId = $this->bookModel->set_book($this->session->userdata('DX_user_id'));

            $this->importImages();

            $content = $this->epub->getCompactContent();
            $order = 1;
            foreach($content as $key=>$item){
                if(empty($item['children'])){
                    if(empty($main)){
                        $main = $this->sectionModel->set_section(array('title'=>'Main Section', 'book_id'=>$bookId));
                    }
                    $chapterId = $this->chapterModel->set_chapter(
                        array('title'=>$key, 'section_id'=>$main, 'book_id'=>$bookId,
                              'content'=>
                                  $this->fixImagesSrc(empty($item['content']) ? $item : $item['content']),
                              'order'=>++$order, 'editor_id'=>2));
                }else{
                    $sectionId = $this->sectionModel->set_section(array('title'=>$key, 'book_id'=>$bookId));
                    foreach($item['children'] as $key2=>$item2){
                        if(empty($item2['children'])){
                            $chapterId = $this->chapterModel->set_chapter(
                                array('title'=>$key2, 'section_id'=>$sectionId, 'book_id'=>$bookId,
                                      'content'=>$this->fixImagesSrc(empty($item2['content']) ? $item2 : $item2['content']),
                                    'order'=>++$order, 'editor_id'=>2));
                        }else{
                            $sectionId = $this->sectionModel->set_section(array('title'=>$key, 'book_id'=>$bookId));
                        }

                    }
                }

            }

            redirect('book/tocmanager/'.$bookId, 'refresh');
        }else{
            echo $this->upload->display_errors();
        }
    }

    private function fixImagesSrc($content)
    {
        return str_ireplace(array('images/','graphics//'), 'public/uploads/'.$this->bookModel->getFolderName($this->input->post('title')).'/',
            $content);
    }

    private function importImages()
    {
        $this->load->helper('file');
        $images = $this->epub->getImages();
        $imagePath = $this->bookModel->getImagesPath($this->input->post('title'));
        foreach ($images as $item) {
            $img = $this->epub->getFromName($item['href']);

            if (!file_exists($imagePath)) {
                @mkdir($imagePath);
            }
            $fileParts = pathinfo($item['href']);
            write_file($imagePath . $fileParts['basename'], $img);
        }
    }
} 