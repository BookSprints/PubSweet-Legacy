<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 3/20/14
 * Time: 4:05 PM
 */

class Annotations_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create($id, $json)
    {
        $data = array(
            'book_id' => $id,
            'annotation' => $json
        );
        $this->db->insert('annotations', $data);
        return $this->db->insert_id();
    }

    public function all($bookid)
    {
        $this->db->select('id, book_id, annotation');
        $this->db->from('annotations');
        $this->db->where(array('book_id'=>$bookid));
        $query = $this->db->get();
        return $query->result_array();

    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('annotations',
            array(
                'annotation' => $data
            ));
    }

    public function delete($id)
    {
        return $this->db->delete('annotations', array('id'=>$id));
    }
}