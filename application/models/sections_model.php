<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 17/07/13
 * Time: 11:24
 * To change this template use File | Settings | File Templates.
 */

class Sections_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function find($book_id){
        $this->db->select('id, title,order');
        $this->db->order_by('order');
        $query = $this->db->get_where('sections', array('book_id'=>$book_id, 'sections.removed'=>0));
        return $query->result_array();
    }

    public function set_section($data){
        $this->db->insert('sections', $data);
        return $this->db->insert_id();
    }
    public function update_position($id,$order){
        $this->db->where('id',$id);
        $this->db->update('sections',
            array(
                'order' => $order
            ));
    }

    public function change_name($id,$data){
        $this->db->where('id',$id);
        $this->db->update('sections',$data);
    }
    public function delete($id,$data){

     $this->db->where('id', $id);
     $this->db->update(
       'sections',$data);
      }

}