<?php
/**
 * Class Facilitator_model
 */
class Facilitators_model extends  CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function all(){
        $this->db->select('u.id, u.username, u.names');
        $this->db->from('users u');
        $this->db->join('users_roles ur', 'ur.user_id = u.id');
        $this->db->where(array('ur.role_id'=>3));
        $this->db->order_by('username');
        $query = $this->db->get();
        return $query->result_array();
    }

}