<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 17/07/13
 * Time: 11:24
 * To change this template use File | Settings | File Templates.
 */

class Sections_model extends MY_Model {
    public function __construct()
    {
        parent::__construct();
        $this->table = 'sections';
    }

    public function find($book_id, $all = false){
        $this->db->select('id, title, order, removed');
        $this->db->order_by('order');
        $filters['book_id'] = $book_id;
        if(!$all){
            $filters['sections.removed'] = 0;
        }
        $query = $this->db->get_where('sections', $filters);
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

    /*public function delete($id){

        $this->softUpdate($id,
            $data = array(
                'removed'=> 1
            ));
    }

    public function undelete($id){

        $this->softUpdate($id,
            $data = array(
                'removed'=> 0
            ));
    }

    public function softUpdate($id, $data){

        $this->db->where('id', $id);
        $this->db->update(
            'sections', $data);
    }*/

}