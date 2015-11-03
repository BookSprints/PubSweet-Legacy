<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 2/27/14
 * Time: 10:12 PM
 */

class Image_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insert($data)
    {
        $this->db->insert('images', $data);
        return $this->db->insert_id();
    }
} 