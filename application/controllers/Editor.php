<?php

/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-06-13
 * Time: 03:12 PM
 */
class Editor extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library("session");

//        if (!$this->session->userdata('DX_user_id')) {
//            redirect('register/login', 'refresh');
//        }

    }

    public function normal($id)
    {
        $data['id'] = $id;
        $this->load->model(array('chapters_model', 'books_model', 'user_model', 'coauthors_model'));
        $data['chaptername'] = $this->chapters_model->get($id);
        if($data['chaptername']['locked']){
            redirect('book/tocmanager/' . $data['chaptername']['book_id'], false);
        }
        $book = $this->books_model->get($data['chaptername']['book_id']);
        $isBookOwner = $book['owner']==$this->session->userdata('DX_user_id');
        $isFacilitator = $this->user_model->isFacilitator($this->session->userdata('DX_user_id'));
        if(!empty($book) && ($this->coauthors_model->canEdit($this->session->userdata('DX_user_id'),
            $data['chaptername']['book_id']) || $isBookOwner || $isFacilitator)){

            $userConfig = $this->books_model->getUserConfig($book['id'], $this->session->userdata('DX_user_id'));
            $data['userSettings'] = isset($userConfig['settings']) ? json_decode($userConfig['settings']) : null;
            $lang = $this->session->userdata('language');
            $lang = empty($lang) ? 'english' : $lang;
            $this->lang->load($lang, $lang);

            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
            $this->output->set_header("Pragma: no-cache");

            $this->load->view('templates/header');
            $this->load->view('templates/navbar', array('book' => $book));
            $this->load->view('editor/normal_editor', $data);
            $this->load->view('templates/footer');
        }else{
            redirect('book/tocmanager/'.$data['chaptername']['book_id'], 'refresh');
        }

    }

    public function full($id)
    {
        $this->load->model('chapters_model');
        $chaptername = $this->chapters_model->get($id);

        $this->load->view(
            'editor/full',
            array(
                'id'          => $id,
                'chaptername' => $chaptername
            )
        );
    }

    public function normal2($id)
    {
        $this->load->model(array('chapters_model', 'books_model'));
        $chaptername = $this->chapters_model->get($id);
        $bookname = $this->books_model->get($chaptername['book_id']);

        $lang = $this->session->userdata('language');
        $lang = empty($lang) ? 'english' : $lang;
        $this->lang->load($lang, $lang);

        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book' => $bookname));
        $this->load->view(
            'editor/normal2',
            array(
                'id'          => $id,
                'chaptername' => $chaptername
            )
        );
        $this->load->view('templates/footer');
    }

    public function uploadImage($chapterId)
    {
        $this->load->model('books_model');
        $book = $this->books_model->findByChapter($chapterId);
        $folderName = url_title($book['title']);
        $config['upload_path'] = BASEPATH . '../public/uploads/' . $folderName . '/';
        if (!file_exists($config['upload_path'])) {
            mkdir($config['upload_path']);
        }
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '5000';
        $config['max_width'] = '7680';
        $config['max_height'] = '4320';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('upload')) {
            $error = array('error' => $this->upload->display_errors());
            echo $error;
        } else {
            $data = array('upload_data' => $this->upload->data());
            echo 'Success';
        }
        die();
    }
}