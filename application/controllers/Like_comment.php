<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 10-03-13
 * Time: 11:15 AM
 * To change this template use File | Settings | File Templates.
 */

class Like_Comment extends CI_Controller {
    function __construct(){
        parent:: __construct();
        $this->load->library('session');
        $this->load->model('like_comments_model','likes');
    }

    function add_like(){
        $comment_id = $this->input->post('comment_id');
        $user_id = $this->session->userdata('DX_user_id');
        $book_id = $this->input->post('book_id');
        $data = array(
            'comment_id'=>$comment_id,
            'user_id'=>$user_id
        );
        if($this->likes->save_like($data))
            echo json_encode(array('ok'=>1));
    }

    function remove_like(){
        $comment_id = $this->input->post('comment_id');
        $user_id = $this->session->userdata('DX_user_id');
        $data = array(
            'comment_id'=>$comment_id,
            'user_id'=>$user_id
        );
        $this->likes->remove_like($data);
        echo json_encode(array('ok'=>1));
    }

}