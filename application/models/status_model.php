<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 30/08/13
 * Time: 9:42
 * To change this template use File | Settings | File Templates.
 */

class status_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function find($book)
    {
        $this->db->select('id,title,status,chapter_id, user_id');
        $query = $this->db->get_where('status', array('book_id'=>$book));
        return $query->result_array();
    }

    public function find_by_chapter($chapter_id){
        $this->db->select('id, title,status,chapter_id,user_id');
        $query = $this->db->get_where('status', array('chapter_id'=>$chapter_id));
        return $query->result_array();
    }

    public function set($data)
    {
        $this->db->insert('status', $data);
        return $this->db->insert_id();
    }

    public function update($data, $id){
        $this->db->where('id',$id);
        return $this->db->update('status', $data);
    }

    public function delete($id){
        $this->db->where('id',$id);
        $this->db->delete('status');
    }



}