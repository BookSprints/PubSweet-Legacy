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
       $this->load->model(array('chapters_model','books_model','user_model'));
       $chaptername= $this->chapters_model->get($id);
       $bookname = $this->books_model->get($chaptername['book_id']);

       $lang = $this->session->userdata('language');
       $lang = empty($lang)?'english':$lang;
       $this->lang->load($lang, $lang);

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");

       $this->load->view('templates/header');
       $this->load->view('templates/navbar', array('book'=>$bookname));
       $this->load->view('editor/normal_editor',
        array(
            'id' => $id,
            'chaptername' => $chaptername
        ));
       $this->load->view('templates/footer');

    }

    public function full($id)
    {
       $this->load->model('chapters_model');
       $chaptername= $this->chapters_model->get($id);

       $this->load->view('editor/full', array(
                   'id' => $id,
                   'chaptername' => $chaptername
               ));
    }

    public function normal2($id)
    {
        $this->load->model(array('chapters_model','books_model'));
        $chaptername= $this->chapters_model->get($id);
        $bookname = $this->books_model->get($chaptername['book_id']);

        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);

        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book'=>$bookname));
        $this->load->view('editor/normal',
            array(
                'id' => $id,
                'chaptername' => $chaptername
            ));
        $this->load->view('templates/footer');
    }

    public function uploadImage()
    {
        $config['upload_path'] = BASEPATH.'../public/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']	= '5000';
        $config['max_width']  = '7680';
        $config['max_height']  = '4320';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('upload'))
        {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
//            $this->load->view('upload_form', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            echo 'Success';
//            $this->load->view('upload_success', $data);
        }
    }
}