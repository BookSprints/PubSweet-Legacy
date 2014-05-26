<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-10-13
 * Time: 10:20 AM
 */

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function get($name)
    {
        $this->db->like('username', $name);
        $result = $this->db->get_where('users');
        return $result->row_array();
    }

    public function add()
    {
        $this->db->insert('users',
            array('names'=>$this->input->post('names'),
                  'email'=>$this->input->post('email'),
//                   'color'=>$this->input->post('color'),
                   'picture'=>$this->input->post('picture'),
        ));

        return $this->db->insert_id();
    }

    public function all()
    {
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /**
     * This function find the task associate with a user.
     * @param $id
     * @return mixed
     */
    public function findByTaskId($id)
    {
        $this->db->select('users.id, names, email');
        $this->db->from('users');
        $this->db->join('tasks','tasks.designee_id = users.id');
        $this->db->where_in('tasks.id', $id);
        $result = $this->db->get();
        return $result->result_array();
    }

    public function findByPhaseId($id)
    {
        $this->db->select('users.id, names, email, tasks.title');
        $this->db->from('users');
        $this->db->join('tasks','tasks.designee_id = users.id');
        $this->db->where('tasks.completed', 0);
        $this->db->where_in('tasks.phase_id', $id);

        $result = $this->db->get();
        return $result->result_array();
    }

}