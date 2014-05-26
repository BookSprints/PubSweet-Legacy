<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-18-13
 * Time: 07:39 PM
 */
class Editors_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function all()
    {
        $this->db->select();
        $this->db->from('editor_types');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function updateState($id,$status){
        $this->db->where('id',$id);
        $this->db->update('editor_types',
            array(
                'enabled' => $status

            ));
    }
}