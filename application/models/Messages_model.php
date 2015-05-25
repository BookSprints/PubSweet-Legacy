<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-05-13
 * Time: 12:16 PM
 * To change this template use File | Settings | File Templates.
 */

class Messages_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save()
    {
        $data = array(

            'message' => $this->input->post('message'),
            'book_id' => $this->input->post('book_id'),
            'user_id' => $this->input->post('user_id'),

        );
        $this->db->insert('messages', $data);
        return $this->db->insert_id();
    }

    public function byBook($book_id)
    {
        $this->db->select('messages.id, message, messages.created, users.names, users.picture, users.id as user_id');
        $this->db->from('messages');
        $this->db->join('users', 'users.id=messages.user_id');
        $this->db->where(array('book_id' => $book_id));
        $this->db->order_by('messages.created DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id)
    {
        $this->db->select('messages.id, message, messages.created, users.id as user_id, users.names');
        $this->db->from('messages');
        $this->db->join('users', 'users.id=messages.user_id');
        $this->db->where(array('messages.id' => $id));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->db->delete('messages',array('id'=>$id));
    }
}