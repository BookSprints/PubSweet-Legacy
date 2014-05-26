<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-27-13
 * Time: 09:32 PM
 * To change this template use File | Settings | File Templates.
 */

class Manager extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }
    public function view($id)
    {
        $this->load->helper('url');
        $this->load->model('books_model', 'book');
        $book = $this->book->get($id);
        $this->load->view("board", array('book'=>$book));

    }

    public function all($bookid){
        $this->load->model('user_model', 'user');
        $this->load->model('phase_model', 'phase');
        $this->load->model('task_model', 'task');


        echo json_encode(array(
            'ok' => 1,
            'users' => $this->user->all(),
            'phases' => $this->phase->all($bookid),
            'tasks' => $this->task->all(),
        ));
    }
    public function ixpoloa()

       {
           $this->load->helper('url');
           $this->load->model('manager_model');
           $this->load->model('models/manager_model');
           $ixpoloa= $this->manager_model->delete();
           redirect(base_url());
       }
}