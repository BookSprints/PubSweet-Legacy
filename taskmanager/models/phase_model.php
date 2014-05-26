<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-10-13
 * Time: 10:20 AM
 */

class Phase_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function add()
    {
        $data = array(
            'name'=>$this->input->post('name'),
            'active'=>$this->input->post('active'),
            'book_id'=>$this->input->post('book_id'),
        );
        $this->db->insert('phases', $data);
        return $this->db->insert_id();
    }

    public function all($bookid)
    {
        $this->db->select(array('id','book_id','name','active'));
        $this->db->from('phases');
        $this->db->order_by('id');
        $this->db->where('book_id', $bookid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function desactivate($id)
    {
        $this->db->update('phases', array('active'=>0), array('id'=>$id));
    }

    public function activate($id)
    {
        $this->db->update('phases', array('active'=>1), array('id'=>$id));
    }

    public function nextPhase($id)
    {
        $this->db->select('id');
        $this->db->from('phases');
        $this->db->where(sprintf('id>%u',$id),NULL,false);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['id'];
    }

    public function delete()
    {
        $this->db->delete('phases', array('id' => $this->input->post('id')));
    }

    public function moveIndependent($id, $newId)
    {
        $this->db->update('tasks',array('phase_id'=>$newId), sprintf('dependency=0 AND completed=0 AND phase_id = %u',$id));
    }

    /**
     * Test wether this phase has uncompleted tasks
     */
    public function isActive($id)
    {
        $this->db->where(sprintf('dependency =1
                                AND  completed =0
                                AND  phase_id =%u', $id));

        $this->db->from('tasks');
        return $this->db->count_all_results()>0;
    }


//ALTER TABLE  `phases` CHANGE  `name`  `name` VARCHAR( 250 ) NOT NULL
}