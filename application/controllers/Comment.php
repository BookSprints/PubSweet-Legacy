<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-30-13
 * Time: 09:29 AM
 * To change this template use File | Settings | File Templates.
 */
class Comment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('comments_model','model');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }

    }

    public function add(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment', 'Comment', 'required');
        $this->form_validation->run();
        $id = $this->model->save();
        $comment = $this->model->get($id);
        echo json_encode(array('ok'=>1, 'comment'=>$comment));
    }

    public function delete()
    {
        $result = $this->model->delete($this->input->post('id'));
        echo json_encode(array('ok'=>$result));
    }
}