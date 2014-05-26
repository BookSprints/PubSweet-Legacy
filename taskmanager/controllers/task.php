<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-27-13
 * Time: 11:45 PM
 * To change this template use File | Settings | File Templates.
 */

class Task extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Task_model', 'model');
    }
    public function add()
    {
        $id = $this->model->add();
        if($this->input->post('active')==='true'){
            $this->load->helper('url');
            $this->emailActivation(array($id));
        }

        echo json_encode(array('ok'=>1, 'id'=>$id));
    }

    public function update()
    {
        $this->model->update(isset($_POST['onlyDescriptin'])&&$_POST['onlyDescriptin']);
        echo json_encode(array('ok'=>1));
    }
    public function move()
    {
        $this->model->move();
        echo json_encode(array('ok'=>1));
        if($this->input->post('active')==='true'){
            $this->load->helper('url');
            $this->emailActivation($this->input->post('ids'));
        }

    }

    public function complete()
    {
        $this->model->complete();
        $this->emailCompletion($this->input->post('id'));
        echo json_encode(array('ok'=>1));
    }

    public function actives(){
        echo json_encode($this->model->actives());
    }

    private function emailCompletion($id){

        $this->load->model('user_model', 'user_model');
        $recipients = $this->user_model->findByTaskId($id);
        $task = $this->model->get($id);
        $this->load->library('Mail');
        foreach ($recipients as $item) {
            $this->mail->send($item['email'],
                sprintf('<p> Hey %s</p> <p>Your task <strong>' .$task['title'].'</strong> is complete! Good work!</p> <p>Many thanks,</p> <p>Your Task Manager</p>', $item['name']),
                'Tasks Complete');
        }

    }
    private function emailActivation($ids){

        $this->load->model('user_model', 'user_model');
        $recipients = $this->user_model->findByTaskId($ids);
        $task = $this->model->get($ids);
        $this->load->library('Mail');
        foreach ($recipients as $item) {
            $this->mail->send($item['email'], sprintf('<p> Hi %s,</p> <p>There is a new task for you. It is labeled <strong>'.$task['title'].'</strong> and you can visit
                    your Task Manager home page to view: <a href="%suser/%s">Your user board.</a></p> <p>Kind regards,</p> <p>Your Task Manager</p> ',
                    $item['names'], base_url(),urlencode($item['names'])),'Tasks Activated');
        }


    }
}