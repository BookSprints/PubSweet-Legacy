<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-27-13
 * Time: 11:45 PM
 * To change this template use File | Settings | File Templates.
 */

class Phase extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Phase_model', 'phase');
    }
    public function add()
    {
        $id = $this->phase->add();
        echo json_encode(array('ok'=>1, 'id'=>$id));
    }

    public function delete()
    {
        $this->phase->delete();
        echo json_encode(array('ok'=>1));
    }

    public function desactivate()
    {
        $id = $this->input->post('id');
        $this->phase->desactivate($id);
        $newId = $this->phase->nextPhase($id);
        $this->phase->activate($newId);
        $this->emailActivation($newId);
        //
        echo json_encode(array('ok'=>1, 'newId'=>$newId));
    }

    private function emailActivation($id){

        $this->load->model('user_model', 'user_model');
        $recipients = $this->user_model->findByPhaseId($id);

        $this->load->library('email');
        $this->load->library('Mail');
        $config['protocol'] = 'mail';
//        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);
        $this->email->from('taskmanager@booksprints.net', 'Booksprints Bot');
        $this->email->subject('Task activated');


        foreach ($recipients as $item) {
        $this->email->to($item['email']);
            $this->email->message($this->mail->template(sprinf('<p> Hi %s,</p> <p>There is a new task for you. It is labeled <strong>'.$item['title'].'</strong> and you can visit
                    your Task Manager home page to view:</p> <p>Kind regards,</p> <p>Your Task Manager</p> ',$item['name'])));

            $this->email->send();
        }

    }

    public function testComplete()
    {
        $id = $this->input->post('id');
        if(!$this->phase->isActive($id)){
            $this->phase->desactivate($id);
            $newId = $this->nextPhase($id);
            if($newId){
                $this->activate($newId);
                $this->phase->moveIndependent($id, $newId);
            }
            return array('ok'=>1, 'equal'=>0);
        }

        echo json_encode(array('ok'=>1, 'equal'=>1));
    }

}