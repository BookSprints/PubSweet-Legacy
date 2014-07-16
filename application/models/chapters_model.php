<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-11-13
 * Time: 12:38 AM
 */

class Chapters_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /** find all chapter at the  book
     * @param $book
     * @return mixed
     */
    public function find($book)
    {
        $this->db->select('c.id, c.title, section_id, s.title as section_title, c.order, editor_id, content');
        $this->db->from('chapters c');
        $this->db->join('sections s','s.id = c.section_id AND s.removed=0');
        $this->db->where(array('c.book_id'=>$book,'c.removed'=>0));
        $this->db->order_by('s.order, c.order');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function findGrouped($id)
    {
        $rows = $this->find($id);
        $result = array();
        foreach ($rows as $item) {
            $result[$item['section_id']][] = $item;
        }
        return $result;
    }

    public function set_chapter($data)
    {
        $this->db->insert('chapters', $data);
        return $this->db->insert_id();
    }
//Insert the update in database
    public function update_position($id,$section,$order)
    {
        $this->db->where('id',$id);
        $this->db->update('chapters',
            array(
                'section_id' => $section,
                'order' => $order
            ));
    }

    public function change_name($id, $data){
        $this->db->where('id',$id);
        $this->db->update('chapters',$data);
    }

    public function get($chapterId){

       $this->db->select('chapters.id, chapters.title, chapters.content, editor_id, removed,
            books.title as bookname, books.id as book_id, owner');
       $this->db->from('chapters');
       $this->db->join('books', 'books.id = chapters.book_id');
       $this->db->where( array('chapters.id'=>$chapterId));
       $query=$this->db->get();
       return $query->row_array();
   }

    public function update($data, $id){
        $this->db->where('id',$id);
        $result = $this->db->update('chapters', $data);
        $this->addToHistory($id, $this->session->userdata('DX_user_id'), $data['content']);
        return $result;
    }

    private function addToHistory($chapter_id, $user_id, $content)
    {
        $this->db->insert('normal_chapter_history', array('chapter_id'=>$chapter_id,
            'user_id'=>$user_id, 'content'=>$content));
    }

    public function selectchapter($book_id){
        $this->db->select('id, title, content, editor_id, removed');
         $this->db->from('chapters');
        $this->db->where( array('book_id'=>$book_id));
         $query = $this->db->get();
         return $query->result_array();

    }

    public function delete($id,$data){

            $this->db->where('id', $id);
            $this->db->update(
                'chapters',$data);
    }

    public function getHistory($id)
    {
        $this->db->select('nch.id, nch.content, nch.created, u.username');
        $this->db->from('normal_chapter_history nch');
        $this->db->join('users u','u.id = nch.user_id');
        $this->db->where( array('chapter_id'=>$id));
        $this->db->order_by('created DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getHistoryEntry($id)
    {
        $this->db->select('nch.*');
        $this->db->from('normal_chapter_history nch');
        $this->db->where( array('id'=>$id));
        $query = $this->db->get();
        return $query->row_array();
    }
}