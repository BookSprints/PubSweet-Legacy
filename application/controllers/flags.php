<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 18/09/13
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */

class Flags extends CI_Controller {
    function __construct(){
        parent:: __construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('flags_model','model');
    }

    function all($edit=false){
        $data = $this->model->all();
        if($edit===false){
            echo json_encode(array('ok'=>1, 'data'=>$data));
        }else{
            $lang = $this->session->userdata('language');
            $lang = empty($lang)?'english':$lang;
            $this->lang->load($lang, $lang);

            $this->load->helper('form');
            $this->load->view('templates/header');
            $this->load->view('templates/navbar', $data);
            $this->load->view('flags/list', array('flags'=>$data));
            $this->load->view('templates/footer');
        }

    }

    function add(){
        $config['upload_path'] = APPPATH.'../public/uploads/flags/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if($this->upload->do_upload("image")){
            $data = $this->upload->data();

            $this->model->insert(array('title'=>$this->input->post('title'),
                'image'=>$data['file_name']));
            redirect('flags/all/edit');
        }else{
            echo $this->upload->display_errors();
        }

    }

}