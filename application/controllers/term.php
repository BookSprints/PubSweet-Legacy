<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-27-13
 * Time: 09:30 PM
 */

class Term extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library("session");
        $lang = $this->session->userdata('language');
        $lang = empty($lang) ? 'english' : $lang;
        $this->lang->load($lang, $lang);
    }

    public function get($id)
    {
        $this->load->model('Dictionary_entries_model','model');
        $this->load->model('Definitions_model','definition_model');

        $term = $this->model->get_term($id);

        if(empty($term['full_image_path'])){
            $term['imagelabel'] = $this->lang->line('add-image');
        }else{
            $term['imagelabel'] = $this->lang->line('view-edit-image');
        }
        $term['full_image_path'] = empty($term['full_image_path'])?'':base_url().$term['full_image_path'];
        echo json_encode(array('term'=>$term,
            'definitions'=>$this->definition_model->definitions($id)));
    }
}