<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-05-13
 * Time: 12:16 PM
 * To change this template use File | Settings | File Templates.
 */

class Reviews_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save()
    {
        $data = array(
            'comment' => $this->input->post('comment'),
            'term_id' => $this->input->post('term_id'),
            'user_id' => $this->input->post('user_id'),

        );
        $this->db->insert('reviews', $data);
        return $this->db->insert_id();
    }

    public function all()
    {
        $this->db->select('reviews.id, reviews.comment, reviews.term_id, reviews.user_id, users.names,users.username');
        $this->db->from('reviews');
//        $this->db->join('chapters','chapters.book_id = books.id', 'LEFT');
        $this->db->join('users', 'users.id=reviews.user_id');
        /*if($userId!=null){
            $this->db->where(array('owner'=>$userId));
        }
        $this->db->group_by('books.id');*/
        $this->db->order_by('reviews.created','DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id)
    {
        $this->db->select('reviews.id, comment, reviews.created, reviews.term_id, users.id as user_id, users.names,users.username');
        $this->db->from('reviews');
        $this->db->join('users', 'users.id=reviews.user_id');
        $this->db->where(array('reviews.id' => $id));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->db->delete('messages',array('id'=>$id));
    }

    public function add_approve($data){
        $query = $this->db->get_where('approvals',$data);
        if($query->num_rows()<=0)
            if($this->db->insert('approvals',$data))
                return true;
    }

    public function approves(){
        $query = $this->db->get('approvals');
        return $query->result_array();
    }

    public function approvals_by_term($id){
        $this->db->select('users.names,users.picture,users.username');
        $this->db->from('approvals');
        $this->db->join('users', 'users.id=approvals.user_id');
        $this->db->where(array('term_id' => $id));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function countAfter($id, $where=null)
    {
        $this->db->select('user_id, count(reviews.id) as count');
        $this->db->from('reviews');
        $this->db->join('dictionary_entries de', 'de.id = reviews.term_id');
        $this->db->join('chapters c', 'c.id = de.chapter_id');
        $this->db->where('c.book_id', $id);
        $this->db->where('c.removed',0);
        if($where!=null){
            $this->db->where(sprintf('reviews.created > %s', $where));
        }
        $this->db->group_by('reviews.user_id');
        $query = $this->db->get();

        $result = $query->result_array();
        $data = array();
        foreach($result as $item){
            $data[$item['user_id']] = $item;
        }
        return $data;
    }

    public function countApprovalsAfter($id, $where=null)
    {
        $this->db->select('user_id, count(approvals.id) as count');
        $this->db->from('approvals');
        $this->db->join('dictionary_entries de', 'de.id = approvals.term_id');
        $this->db->join('chapters c', 'c.id = de.chapter_id');
        $this->db->where('c.book_id', $id);
        $this->db->where('c.removed', 0);
        if($where!=null){
            $this->db->where(sprintf('approvals.created > %s', $where));
        }
        $this->db->group_by('approvals.user_id');
        $query = $this->db->get();

        $result = $query->result_array();
        $data = array();
        foreach($result as $item){
            $data[$item['user_id']] = $item;
        }
        return $data;
    }
}