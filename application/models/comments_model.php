<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-30-13
 * Time: 09:27 AM
 * To change this template use File | Settings | File Templates.
 */
class Comments_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save($topic=null)
    {
        $data = array(

            'comment' => $this->input->post('comment'),
            /*'book_id' => $this->input->post('book_id'),*/
            'user_id' => $this->session->userdata('DX_user_id'),
            'topic_id' => $topic==null?$this->input->post('topic_id'):$topic

        );
        $this->db->insert('comments', $data);
        return $this->db->insert_id();
    }

    public function byTopic($topic_id)
    {
        $this->db->select('comments.id, comment, comments.created, users.names,users.username, users.picture, users.id as user_id');
        $this->db->from('comments');
        $this->db->join('users', 'users.id=comments.user_id');
        $this->db->where(array('topic_id' => $topic_id));
        $this->db->order_by('comments.created DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id)
    {
        $this->db->select('comments.id, comment, comments.created, users.id as user_id, users.names,users.username');
        $this->db->from('comments');
        $this->db->join('users', 'users.id=comments.user_id');
        $this->db->where(array('comments.id' => $id));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->db->delete('comments',array('id'=>$id));
    }
}