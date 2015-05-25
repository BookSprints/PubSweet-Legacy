<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juancarlosg
 * Date: 9/3/13
 * Time: 11:17 AM
 * To change this template use File | Settings | File Templates.
 */

class Language extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        $this->load->model('language_model','model');
        echo json_encode($this->model->all());
    }
}