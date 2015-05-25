<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 4/2/14
 * Time: 11:17 AM
 */

class Book_settings_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save($book_id, $key, $value)
    {
        $this->db->query(sprintf("INSERT INTO book_settings(book_id, variable_machine_name, value)
            VALUES(%u, '%s', '%s') ON DUPLICATE KEY UPDATE value='%s'", $book_id, $key, $value, $value));
    }

    public function get($id)
    {
        $this->db->select('book_id, variable_machine_name, value');
        $query= $this->db->get_where('book_settings', array('book_id'=>$id));
        $rows = $query->result_array();
        $result = array();
        foreach($rows as $item){
            $result[$item['variable_machine_name']] = $item['value'];
        }
        return $result;
    }
} 