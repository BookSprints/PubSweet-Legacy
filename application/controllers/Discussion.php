<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-05-13
 * Time: 11:06 AM
 */
class Discussion extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('messages_model','model');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }

    }

    public function view($id)
    {
        $this->load->model('books_model');
        $this->load->model('likes_model','likes');
        $data['book'] = $this->books_model->get($id);
        $messages['messages'] = $this->model->byBook($id);
        $cont=0;
        foreach($messages['messages'] as $item){
            $likes = $this->likes->likes_by_messages($item['id']);
            $messages['messages'][$cont]['likes']=$likes;
            $cont++;
        }
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
        $this->load->view('templates/header');
        $this->load->view('templates/navbar', $data);
        $this->load->view('discussion/message', array('messages'=>$messages,'book'=>$data));
        $this->load->view('templates/footer');
    }

    public function add(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('message', 'Message', 'required');
        $this->form_validation->run();
        $id = $this->model->save();
        $message = $this->model->get($id);
        echo json_encode(array('ok'=>1, 'message'=>$message));
    }

    public function delete()
    {
        $result = $this->model->delete($this->input->post('id'));
        echo json_encode(array('ok'=>$result));
    }
}