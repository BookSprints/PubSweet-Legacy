<?php


class Registers_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function find($id)
    {
        $query = $this->db->get_where('users', array('id'=>$id));
        return $query->row_array();
    }

    public function set_userdata($user, $password,$email)
    {

        $data = array(

         'user' => $user,
        'password' =>$password,
        'email' => $email,
        );
        return $this->db->insert('users', $data);
    }

    public function updatePicture($user, $picture)
    {
        return $this->db->update('users', array(
            'picture'=>$picture
        ), array('id'=>$user));
    }

    public function update_profile($user_id, $data)
    {
        $this->db->where('id', $user_id);
        return $this->db->update('users', $data);
    }
}