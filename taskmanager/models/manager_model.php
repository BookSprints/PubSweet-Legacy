<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 4/07/13
 * Time: 11:14
 * To change this template use File | Settings | File Templates.
 */
class Manager_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
public function delete()
   {
       $tables=array('phases','tasks','users',);
       $this->db->where('1=1', null,false);
       $this->db->delete($tables);
   }
}