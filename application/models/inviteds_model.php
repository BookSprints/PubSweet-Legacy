<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 10-07-13
 * Time: 11:00 AM
 */
class Inviteds_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save($data)
    {
        $this->db->insert('invited_externals', $data);
        return $this->db->insert_id();
    }

    public function checkInvitations($email){
        $this->db->where(array('invited'=>$email));
        $this->db->update('invited_externals', array('accepted'=>1));
        return $this->db->affected_rows();
    }

    public function remove($email)
    {
        return $this->db->delete('invited_externals',array('invited'=>urldecode($email)));
    }
}