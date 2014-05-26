<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-30-13
 * Time: 10:41 AM
 */
class Topics_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function savetopic()
    {
        $data = array(

            'topic' => $this->input->post('topic'),
            'book_id' => $this->input->post('book_id'),
            'user_id' => $this->input->post('user_id'),

        );
        $this->db->insert('topics', $data);
        return $this->db->insert_id();
    }

    public function byBook($book_id)
    {
        $this->db->select('topics.id, topic, topics.created, users.names, users.picture, users.id as user_id');
        $this->db->from('topics');
        $this->db->join('users', 'users.id=topics.user_id');
        $this->db->where(array('book_id' => $book_id));
        $this->db->order_by('topics.created DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id)
    {
        $this->db->select('topics.id, topic, topics.created, topics.book_id, users.id as user_id, users.names');
        $this->db->from('topics');
        $this->db->join('users', 'users.id=topics.user_id');
        $this->db->where(array('topics.id' => $id));
        $query = $this->db->get();
        return $query->row_array();
    }
    public function byTopic($topic_id)
      {
          $this->db->select('comments.id, comment, comments.created, users.names, users.picture, users.id as user_id');
          $this->db->from('comments');
          $this->db->join('users', 'users.id=comments.user_id');
          $this->db->where(array('topic_id' => $topic_id));
          $this->db->order_by('comments.created DESC');
          $query = $this->db->get();
          return $query->row_array();
      }
   /* public function delete($id)
    {
        return $this->db->delete('comments',array('id'=>$id));
    }*/
}