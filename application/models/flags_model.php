<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juancarlosg
 * Date: 9/3/13
 * Time: 11:19 AM
 * To change this template use File | Settings | File Templates.
 */

class Flags_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all()
    {
        $this->db->select('id, title, image');
        $this->db->from('flags');
        $query = $this->db->get();
        $data = $query->result_array();
        $result = array();
        foreach ($data as $row) {
            $result[$row['id']] = $row;
        }
        return $result;
    }

    public function get()
    {
        $this->db->select('id, title, image');
        $this->db->from('flags');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insert($data)
    {
        $this->db->insert('flags', $data);
        return $this->db->insert_id();
    }
}