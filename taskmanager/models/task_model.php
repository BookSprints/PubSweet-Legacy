<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 06-10-13
 * Time: 10:20 AM
 */

class Task_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function add()
    {
        $data = array(
            'title'=>$this->input->post('title'),
            'phase_id'=>$this->input->post('phase_id'),
            'designee_id'=>$this->input->post('designee_id')
        );
        $this->db->insert('tasks', $data);
        return $this->db->insert_id();
    }

    public function update($onlyDescription)
    {
        if($onlyDescription){
            $data = array(
                'description'=>$this->input->post('description')
            );

        }else{
            $data = array(
                'description'=>$this->input->post('description'),
                'dependency'=>isset($_POST['dependency']) && $_POST['dependency'] ? 1 : 0
            );
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tasks', $data);

    }

    public function complete()
    {

        $data=array('completed'=>1,
                    'description'=>$this->input->post('description'),
                    'dependency'=>$this->input->post('dependency')=='true'?1:0
        );

        $this->db->where_in('id', $this->input->post('id'));
        $this->db->update('tasks',$data);


    }

    public static function delete()
    {
        $sql = sprintf('DELETE FROM tasks WHERE id=%u', $_POST['id']);
        if (!$result = Db::conn()->query($sql)) {
            echo Db::conn()->error;
        }

        return array('ok' => $result);
    }

    public function move()
    {
        $data=array('phase_id'=>$this->input->post('phase'));
        $this->db->where_in('id', $this->input->post('ids'));
        $this->db->update('tasks',$data);
    }

    public function all()
    {
        $this->db->select(array('id','title','phase_id','designee_id',
            'description','dependency','completed'));
        $this->db->from('tasks');
        $this->db->order_by('tasks.completed','DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This function returns all tasks of the phase active.
     * @return mixed
     */
    public function actives()
    {
        $this->db->select(array('tasks.id','tasks.title','tasks.phase_id','tasks.designee_id',
            'tasks.description','tasks.dependency','tasks.completed'));
        $this->db->from('tasks');
        $this->db->join('phases', 'phases.id = tasks.phase_id AND phases.active');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id){
        if(is_array($id)){
            $this->db->where_in('id',$id);
        }else{
            $this->db->where(array('id'=>$id));
        }

        $this->db->from('tasks');
        $result = $this->db->get();
        return $result->row_array();
    }


}