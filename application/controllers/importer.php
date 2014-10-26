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

    public function save()
    {
        $config['upload_path'] = APPPATH.'../public/uploads/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if($this->upload->do_upload("epub")){
            $data = $this->upload->data();
            $epub = $this->load->library('EPUB', $data['full_path']);

            $this->load->model('books_model', 'bookModel');
            $this->load->model('sections_model', 'sectionModel');
            $this->load->model('chapters_model', 'chapterModel');
            $bookId = $this->bookModel->set_book($this->session->userdata('DX_user_id'));
            $sectionId = $this->sectionModel->set_section(array('title'=>'Main Section', 'book_id'=>$bookId));
            $content = $epub->getCompactContent();
            $order = 1;
            foreach($content as $key=>$item){
                $chapterId = $this->chapterModel->set_chapter(
                    array('title'=>$key, 'section_id'=>$sectionId, 'book_id'=>$bookId,
                          'content'=>$item, 'order'=>++$order, 'editor_id'=>2));
            }

            redirect('book/tocmanager/'+$bookId);
        }else{
            echo $this->upload->display_errors();
        }
    }
} 