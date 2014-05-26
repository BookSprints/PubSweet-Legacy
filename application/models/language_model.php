<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juancarlosg
 * Date: 9/3/13
 * Time: 11:19 AM
 * To change this template use File | Settings | File Templates.
 */

class Language_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all()
    {
        $this->db->select('id, code_language, english_name, code_dir, iso_code');
        $this->db->from('languages');
        $query = $this->db->get();
        $data = $query->result_array();
        $result = array();
        foreach ($data as $row) {
            $result[$row['id']] = $row;
        }

    }

    public function get($iso)
    {
        $this->db->select('id, code_language, english_name, code_dir, iso_code');
        $this->db->from('languages');
        $this->db->where(array('iso_code'=>$iso));
        $query = $this->db->get();
        return $query->row_array();
    }
}