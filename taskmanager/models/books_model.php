<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-11-13
 * Time: 12:38 AM
 */

class Books_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all($userId)
    {
        $this->db->select('books.id, books.title, chapters.id as chapter_id');
        $this->db->from('books');
        $this->db->join('chapters','chapters.book_id = books.id', 'LEFT');
        $this->db->where(array('owner'=>$userId));
        $this->db->group_by('books.id');
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        return $query->result_array();

    }

    public function set_book($userid)
    {
        $data = array(
            'title' => $this->input->post('title'),
            'owner' => $userid
        );
        $this->db->insert('books', $data);
        return $this->db->insert_id();

    }

    public function get($bookId){
        $this->db->select('id, title');
        $query= $this->db->get_where('books', array('id'=>$bookId));
        return $query->row_array();
    }

}