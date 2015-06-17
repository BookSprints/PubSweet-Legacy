<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 1/1/15
 * Time: 12:44 PM
 */

class Book_copy_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function doCopy($id, $title)
    {
        $bookData = $this->getBook($id);
        $oldTitle = url_title($bookData['title']);
        unset($bookData['id']);
        unset($bookData['timestamp']);
        $bookData['title'] = $title;
        $this->db->trans_start();
        $newBookId = $this->insert('books', $bookData);
        $sections = $this->sections($id);
        foreach ($sections as $item) {
            $this->copySection($item, $newBookId);
        }
        $this->db->trans_complete();
        if($this->db->trans_status()){
            //copy folder where images are stored
            $oldPath = BASEPATH.'../public/uploads/'.$oldTitle;
            if(file_exists($oldPath)){
                return true;
            }else{
                return copy($oldPath, BASEPATH.'../public/uploads/'.url_title($title));
            }
        }else{
            return false;
        }
    }

    public function insert($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    private function copySection($section, $bookId)
    {
        $oldId = $section['id'];
        unset($section['id']);
        $section['book_id'] = $bookId;
        $newSectionId = $this->insert('sections', $section);
        $this->copyChapters($oldId, $newSectionId, $bookId);
    }

    public function copyChapters($oldSectionId, $newSectionId, $bookid){
        $chapters = $this->chapters($oldSectionId);
        foreach($chapters as $item){
            $oldId = $item['id'];
            unset($item['id']);
            $item['section_id'] = $newSectionId;
            $item['book_id'] = $bookid;
            $newChapterId = $this->insert('chapters', $item);
            $this->copyTerms($oldId, $newChapterId);
        }
    }

    public function copyTerms($oldChapterId, $newChapterId)
    {
        $terms = $this->terms($oldChapterId);
        foreach($terms as $item){
            $oldId = $item['id'];
            unset($item['id']);
            $item['chapter_id'] = $newChapterId;
            $newTermId = $this->insert('dictionary_entries', $item);
            $this->copyDefinitions($oldId, $newTermId);
        }
    }

    public function copyDefinitions($oldTermId, $newTermId)
    {
        $terms = $this->definitions($oldTermId);
        foreach($terms as $item){
            $oldId = $item['id'];
            unset($item['id']);
            $item['term_id'] = $newTermId;
            $newDefinitionId = $this->insert('definitions', $item);
        }
    }

    public function getBook($bookId){
        $this->db->select('*');
        $query= $this->db->get_where('books', array('id'=>$bookId));
        return $query->row_array();
    }

    public function sections($book_id)
    {
        $this->db->select('*');
        $this->db->order_by('order');
        $query = $this->db->get_where('sections', array('book_id' => $book_id, 'sections.removed' => 0));
        return $query->result_array();
    }

    public function chapters($section_id)
    {
        $this->db->select('*');
        $this->db->order_by('order');
        $query = $this->db->get_where('chapters', array('section_id' => $section_id, 'chapters.removed' => 0));
        return $query->result_array();
    }

    public function terms($chapter)
    {
        $this->db->select('de.*');
        $this->db->from('dictionary_entries de');
        $this->db->where(array('chapter_id' => $chapter));
        $query = $this->db->get();

        return $query->result_array();
    }

    public function definitions($term_id)
    {
        $this->db->select('definitions.*');
        $this->db->from('definitions');
        $this->db->where(array('term_id'=>$term_id));
        $query = $this->db->get();
        return $query->result_array();
    }
} 