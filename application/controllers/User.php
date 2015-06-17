<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 26/08/13
 * Time: 0:13
 */

class User extends CI_Controller {
    function __construct(){
        parent:: __construct();
        $this->load->library('session');
        $this->load->model('user_model','users');

    }

    function get_all_users(){
        $result = $this->users->get_all();
        echo json_encode($result);
    }


    function getUsersInfo($id=''){
        if(empty($id))
        {
            $id=$this->session->userdata('DX_user_id');
        }
        $result = $this->users->get_user_by_id($id);
        echo json_encode($result);
    }

    public function image($id)
    {
        $user = $this->users->get_user_by_id($id);
        header('Content-Type: image/png');
        if(empty($user['picture'])){
            echo file_get_contents('http://placehold.it/48x48');
        }else{
            echo base64_decode(str_replace('data:image/png;base64,','',$user['picture']));
        }
    }

}