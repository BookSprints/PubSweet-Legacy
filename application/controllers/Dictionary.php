<?php

/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-10-13
 * Time: 03:21 PM
 * To change this template use File | Settings | File Templates.
 */
class Dictionary extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library("session");
        $this->load->model('dictionary_entries_model', 'model');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }


    }

    public function creator($id = 0)
    {
        $data = array();
        $data['id'] = $id;
        $this->load->model(array('chapters_model', 'books_model', 'language_model'));
        $data['chaptername'] = $this->chapters_model->get($id);
        $data['chapteritem'] = $this->chapters_model->selectchapter($data['chaptername']['book_id']);
        $data['language'] = $this->language_model->all();
        $data['term'] = $this->model->term_list($id);
        $bookname = $this->books_model->get($data['chaptername']['book_id']);
        $this->load->helper('form');
        $lang = $this->session->userdata('language');
        $lang = empty($lang) ? 'english' : $lang;
        $this->lang->load($lang, $lang);

        $this->load->view('templates/header', array('book' => $bookname));
        $this->load->view('templates/navbar');
        $this->load->view('dictionary/create', $data);
        $this->load->view('templates/footer');

    }

    public function save()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('term', 'Term', 'required');
        $id = $this->model->set_create();
        echo json_encode(array('ok' => 1, 'id' => $id));

    }

    public function update()
    {
        $this->model->update_item();
        echo json_encode(array('ok' => 1));
    }

    public function termDelete()
    {
        $id = $this->input->post('term_id');
        $this->model->term_delete($id);
        echo json_encode(array('ok' => 1, 'id' => $id));
    }

    public function update_chapter()
    {
        $id = $this->input->post('term_id');
        $chapter_id = $this->input->post('chapter_id');
        $data = array('chapter_id' => $chapter_id);
        $this->model->update_chapterItem($id, $data);
    }

    public function attach()
    {
        $config['upload_path'] = './public/uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
//        $config['max_size']	= '100';
//        $config['max_width'] = '1024';
//        $config['max_height'] = '768';

        $this->load->library('upload', $config);
        $this->upload->do_upload('attachment');
        $data = $this->upload->data();

        $imageId = $this->saveImageData($data);
        $this->updateImageReference($this->input->post('term_id'), $imageId);
        echo json_encode(array('file'=>base_url().$config['upload_path'].$data['file_name']));
    }

    private function saveImageData($data)
    {
        $this->load->model('Image_model', 'images');
        $imageInfo = array(
            'name'=>$data['raw_name'],
            'full_image_path'=>'public/uploads/'.$data['file_name']
        );
        $id = $this->images->insert($imageInfo);
        return $id;
    }

    public function updateImageReference($termId, $imageId)
    {
        $this->model->updateImageReference($termId, $imageId);
    }

    public function delete_image()
    {
        $this->model->deleteImageReference($this->input->post('id'));
    }

}