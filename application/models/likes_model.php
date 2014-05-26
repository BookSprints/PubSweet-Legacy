<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 18/09/13
 * Time: 21:23
 * To change this template use File | Settings | File Templates.
 */

class Likes_model extends CI_Model{
    function __construct(){
        parent:: __construct();
        $this->load->database();
    }

    function likes_by_messages($message_id){
        $this->db->select('*');
        $query = $this->db->get_where('likes', array('messages_id'=>$message_id));
        return $query->result_array();
    }

    function save_like($data){
        $query = $this->db->get_where('likes',$data);
        if($query->num_rows()<=0){
            $this->db->insert('likes', $data);
            return true;
        }
        else
            return false;
    }

    function remove_like($data){
        $this->db->where($data);
        $this->db->delete('likes');
    }

}