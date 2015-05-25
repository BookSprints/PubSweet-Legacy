<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/23/15
 * Time: 2:42 PM
 */

class MY_Model extends CI_Model{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /* *** SOFT DELETE **** */
    public function delete($id){

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

    private function softUpdate($id, $data){

        $this->db->where('id', $id);
        $this->db->update(
            $this->table, $data);
    }
}