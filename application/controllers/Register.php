<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-09-13
 * Time: 11:29 AM
 * To change this template use File | Settings | File Templates.
 */

class Register extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('registers_model');
        $this->load->library('Session');
        $this->load->library('DX_Auth');
    }

    public function login($result='')
    {
        $data = array('error'=>$result=='error');
        $this->load->helper('form');
        $this->load->view('templates/header');
        $this->load->view('user/login', $data);
        $this->load->view('templates/footer');

    }

    public function user($result = false)
    {
        $this->load->helper('form');
        $this->load->view('templates/header');
        $this->load->view('user/register', array('result' => $result));
        $this->load->view('templates/footer');

    }
    public function profile_update(){
        $user_id = $this->session->userdata('DX_user_id');
        $username = $this->session->userdata('DX_username');
        $name = $this->input->post('name');
        $data = array('username'=>$username,'names'=>$name);
        $this->session->set_userdata('DX_username',$username);
        $this->registers_model->update_profile($user_id,$data);
    }

    public function set_picture()
    {
        $this->load->library('session');
        $result = $this->registers_model->updatePicture($this->session->userdata('DX_user_id'), $this->input->post('picture'));
        echo json_encode(array('ok'=>$result));
    }
}

