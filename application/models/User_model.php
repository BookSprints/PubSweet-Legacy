<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 26/08/13
 * Time: 0:40
 * To change this template use File | Settings | File Templates.
 */

class user_model extends  CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all(){
        $this->db->select('id, username,picture,names,banned');
        $this->db->from('users');
        $this->db->order_by('username');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_user_by_id($user_id)
    {
        $this->db->select('id, username , picture, names');
        $query= $this->db->get_where('users', array('id'=>$user_id));
        return $query->row_array();
    }

    public function user_delete($id,$data){
         $this->db->where('id', $id);
         $this->db->update(
          'users',$data);
    }

    public function update_user()
        {
            $this->load->library('DX_Auth');
            $this->db->where('id', $this->input->post('user_id'));
            $this->db->update(
                'users',
                $data = array(
                    'password' =>  crypt($this->dx_auth->_encode($this->input->post('password')))
                    )

            );
    }
    public function user_active($id,$data){
             $this->db->where('id', $id);
             $this->db->update(
              'users',$data);
        }

    public function set_role($user_id, $role_id)
    {
        $this->db->query(
            sprintf("INSERT INTO users_roles(user_id, role_id)
                VALUES(%u, %u) ON DUPLICATE KEY UPDATE user_id=user_id", $user_id, $role_id)
        );
    }

    public function getRoles($user_id)
    {
        $this->db->select('role_id');
        $this->db->from('users_roles');
        $this->db->where(array('user_id'=>$user_id));
        $query = $this->db->get();
        $result = $query->result_array();
        $roles = array();
        foreach ($result as $item) {
            $roles[] = intval($item['role_id']);
        }
        return $roles;

    }

    public function isFacilitator($user_id)
    {
        $roles = $this->getRoles($user_id);
        return array_search(3, $roles)!==false;
    }
}