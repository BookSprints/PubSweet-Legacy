<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-02-13
 * Time: 10:29 AM
 * To change this template use File | Settings | File Templates.
 */
class Definition extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * function update language
     */
    public  function save(){
        $this->load->model('definitions_model');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('term', 'Term', 'required');
        $id = $this->input->post('id');

        if( empty($id)){

         $id= $this->definitions_model->insert();
        }else {
            $this->definitions_model->update();
        }
        echo json_encode(array('ok' => 1, 'id'=>$id));

    }

}