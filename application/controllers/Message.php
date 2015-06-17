<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 4/09/13
 * Time: 23:57
 * To change this template use File | Settings | File Templates.
 */
class Message extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');


    }

    public function discussion()
    {
                $this->load->helper('form');
                $this->load->view('templates/header');
                $this->load->view('discussion/message');
                $this->load->view('templates/footer');
    }
}
