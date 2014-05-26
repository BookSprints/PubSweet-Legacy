<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-27-13
 * Time: 09:33 PM
 * To change this template use File | Settings | File Templates.
 */

class User extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('User_model', 'model');
    }
    public function view($bookid, $name)
    {
        if(method_exists($this, $name)){
            call_user_func(array($this,$name));
        }else{
            $data = $this->model->get(urldecode($name));
            $data['bookid'] = $bookid;
            $this->load->view('user/user.php', $data);
        }

    }
    public function add()
    {
        $id = $this->model->add();
        echo json_encode(array('ok'=>1, 'id'=>$id));
    }
}