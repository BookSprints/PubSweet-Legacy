<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 2/4/15
 * Time: 12:36 AM
 */

class Normal_chapter_history_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function byBook($bookId, $groupByChapter = true)
    {
        $this->db->select('n.*, c.title');
        $this->db->from('normal_chapter_history n');
        $this->db->join('chapters c', 'c.id=n.chapter_id');
        $this->db->join('books', 'books.id=c.book_id');
        $this->db->where(array('book_id' => $bookId, 'c.removed'=>0));
        if($groupByChapter==true){
            $this->db->order_by('n.chapter_id ASC');

        }
        $this->db->order_by('n.created ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function groupByUsers($bookId)
    {
        $this->db->select('u.id, u.username, u.names, sum(n.inserted) as added, sum(n.deleted) as deleted');
        $this->db->from('normal_chapter_history n');
        $this->db->join('chapters c', 'c.id=n.chapter_id');
        $this->db->join('books', 'books.id=c.book_id');
        $this->db->join('users u', 'u.id=n.user_id');
        $this->db->where(array('book_id' => $bookId, 'c.removed'=>0));
        $this->db->group_by('n.user_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @param $id
     * @param $option
     */
    public function compare($id, $option)
    {

        $original = $this->get($id);

        $this->db->select('n.*');
        $this->db->from('normal_chapter_history n');
        if($option=='previous'){
            $this->db->where('created < ', $original['created']);
        }
        $this->db->where('chapter_id', $original['chapter_id']);

        $this->db->order_by('created','DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $compare = $query->row_array();
        return array('original'=>$original, 'compare'=>$compare);
    }

    public function get($id)
    {
        $query= $this->db->get_where('normal_chapter_history', array('id' => $id));
        return $query->row_array();
    }
}