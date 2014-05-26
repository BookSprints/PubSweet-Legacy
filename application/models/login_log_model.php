<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 1/10/14
 * Time: 12:43 PM
 */

class Login_log_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insert($username)
    {
        $data = array('username'=>$username);

        $this->db->insert('login_log', $data);
    }

    public function last($interval=null)
    {
        $this->db->select('username, time');
        $this->db->from('login_log l');
        if($interval!=null){
            $this->db->where(sprintf('l.time > %s', sprintf('DATE_SUB(NOW(), INTERVAL %s)',$interval)));
        }
        $this->db->order_by('time DESC');
        $query = $this->db->get();

        $result = $query->result_array();
        return $result;
    }


    public function groupByDate($data)
    {
        $result = array();
        foreach($data as $key=>$item){
            $timestamp = $item['time'];
            $dateTime = new DateTime($timestamp);
            $onlyDate = $dateTime->format('m-d-Y');
            if(isset($result[$onlyDate])){
                ++$result[$onlyDate]['count'];
                $result[$onlyDate]['detail'][] = $data[$key];
            }else{
                $result[$onlyDate]['count'] = 1;
                $result[$onlyDate]['date'] = $onlyDate;
                $result[$onlyDate]['detail'] = array();
                $result[$onlyDate]['detail'][] = $data[$key];
            }

        }
        return $result;
    }
} 