<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-18-13
 * Time: 07:39 PM
 */
class Coauthors_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function canEdit($userId, $bookId)
    {
        $this->db->select();
        $this->db->from('coauthors');
        $this->db->where(array('book_id'=>$bookId,
            'user_id'=>$userId, 'contributor'=>1));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function canReview($userId, $bookId)
    {
        $this->db->select();
        $this->db->from('coauthors');
        $this->db->where(array('book_id'=>$bookId,
            'user_id'=>$userId, 'reviewer'=>1));
        $query = $this->db->get();
        return $query->row_array();
    }
}