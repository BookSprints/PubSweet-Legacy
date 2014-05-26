<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 10-03-13
 * Time: 11:10 AM
 * To change this template use File | Settings | File Templates.
 */
class Like_Comments_model extends CI_Model{
    function __construct(){
        parent:: __construct();
        $this->load->database();
    }

    function likes_by_comments($comment_id){
        $this->db->select('*');
        $query = $this->db->get_where('like_comments', array('comment_id'=>$comment_id));
        return $query->result_array();
    }

    function save_like($data){
        $query = $this->db->get_where('like_comments',$data);
        if($query->num_rows()<=0){
            $this->db->insert('like_comments', $data);
            return true;
        }
        else
            return false;
    }

    function remove_like($data){
        $this->db->where($data);
        $this->db->delete('like_comments');
    }

}