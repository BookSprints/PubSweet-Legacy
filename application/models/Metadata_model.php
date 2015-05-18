<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-20-13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

class Metadata_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save($value, $attribute, $book)
    {
        if(empty($value)){
            return;
        }
        $this->db->query(sprintf('INSERT INTO book_metadata(value, attribute, book_id)
            VALUES("%s", "%s", %u) ON DUPLICATE KEY UPDATE value="%s"',
            $value, $attribute, $book, $value));
    }

    public function get($bookid)
    {
        $result = $this->db->get_where('book_metadata', array('book_id'=>$bookid));
        return $result->result_array();
    }
}