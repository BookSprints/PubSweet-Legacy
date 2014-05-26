<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 18/09/13
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */

class likes extends CI_Controller {
    function __construct(){
        parent:: __construct();
        $this->load->library('session');
        $this->load->model('likes_model','likes');
    }

    function add_like(){
        $message_id = $this->input->post('message_id');
        $user_id = $this->session->userdata('DX_user_id');
        $book_id = $this->input->post('book_id');
        $data = array(
            'messages_id'=>$message_id,
            'user_id'=>$user_id
        );
        if($this->likes->save_like($data))
            echo json_encode(array('ok'=>1));
    }

    function remove_like(){
        $message_id = $this->input->post('message_id');
        $user_id = $this->session->userdata('DX_user_id');
        $data = array(
            'messages_id'=>$message_id,
            'user_id'=>$user_id
        );
        $this->likes->remove_like($data);
        echo json_encode(array('ok'=>1));
    }

}