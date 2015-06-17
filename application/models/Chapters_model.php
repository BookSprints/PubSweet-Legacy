<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-11-13
 * Time: 12:38 AM
 */

class Chapters_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'chapters';
    }

    /** find all chapter at the  book
     * @param $book
     * @return mixed
     */
    public function find($book, $all = false)
    {
        $this->db->select('c.id, c.title, section_id, s.title as section_title, c.order, editor_id, content,
                            c.locked, c.removed');
        $this->db->from('chapters c');
        $this->db->join('sections s','s.id = c.section_id '.($all ? '' : 'AND s.removed=0'));
        $filters = array('c.book_id'=>$book);
        if(!$all){
            $filters['c.removed'] = 0;
        }
        $this->db->where($filters);
        $this->db->order_by('s.order, c.order');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *
     * @param $id
     * @param $onlyContent
     * @return array - grouped by section id
     */
    public function findGrouped($id, $onlyContent=null)
    {
        $rows = $this->find($id);
        $result = array();
        foreach ($rows as $item) {
            if(empty($onlyContent) || in_array($item['section_id'], $onlyContent->sections)){
                if(empty($onlyContent) || in_array($item['id'], $onlyContent->chapters)){
                    $result[$item['section_id']][] = $item;

                }

            }

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

       $this->db->select('chapters.id, chapters.title, chapters.content, chapters.locked, editor_id, removed,
            books.title as bookname, books.id as book_id, owner');
       $this->db->from('chapters');
       $this->db->join('books', 'books.id = chapters.book_id');
       $this->db->where( array('chapters.id'=>$chapterId));
       $query=$this->db->get();
       return $query->row_array();
   }

    public function update($data, $id){
        $oldChapter = $this->get($id);
        $this->db->where('id',$id);
        $result = $this->db->update('chapters', $data);
        if($result && $oldChapter['content'] != $data['content']){
            $this->addToHistory($id, $this->session->userdata('DX_user_id'),
                $data['content'], strip_tags($oldChapter['content']));
        }

        return true;
    }

    private function addToHistory($chapter_id, $user_id, $newContent, $oldContent)
    {
        $onlyText = strip_tags($newContent);
//        include APPPATH.'/libraries/finediff.php';
//        $diff = new FineDiff($oldContent, $newContent);

        $this->db->insert('normal_chapter_history', array('chapter_id'=>$chapter_id,
            'user_id'=>$user_id, 'content'=>$newContent, 'words'=>str_word_count($onlyText),
            /*'inserted'=>$diff->insertions_count, 'deleted'=>$diff->deletions_count*/));

    }

    public function selectchapter($book_id){
        $this->db->select('id, title, content, editor_id, removed');
         $this->db->from('chapters');
        $this->db->where( array('book_id'=>$book_id));
         $query = $this->db->get();
         return $query->result_array();

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

    public function toggleLock($chapterId)
    {
        $sql = "UPDATE chapters SET locked = 1 - locked WHERE id = ?";

        return $this->db->query($sql, array($chapterId));
    }

    /**
     * Prepares the data to be used by a graphic in stats section
     *
     * @param $bookId
     * @return mixed Word count of chapters' content grouped by section
     */
    public function wordCount($bookId)
    {
        $all = $this->find($bookId);

        $temp = array();
        $sectionsTemp = array();
        foreach($all as $chapter){

            $temp[$chapter['section_id']][] = array('name'=>$chapter['title'],
                'words'=>str_word_count(strip_tags($chapter['content']))
            );
            $sectionsTemp[$chapter['section_id']] = $chapter['section_title'];
        }
        $result = array();
        foreach($sectionsTemp as $key=>$section){
            $result[] = array('name'=>$section,
                'children'=>$temp[$key]);
        }
        return $result;
    }

    public function getBySection($sectionId)
    {
        $this->db->select('c.id, c.title, section_id, s.title as section_title, c.order, editor_id, content');
        $this->db->from('chapters c');
        $this->db->join('sections s','s.id = c.section_id AND s.removed=0');
        $this->db->where(array('c.section_id'=>$sectionId,'c.removed'=>0));
        $this->db->order_by('s.order, c.order');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function replace($id, $search, $replace)
    {
        return $this->db->query('UPDATE chapters SET content = replace(content, ?, ?)
                WHERE id = ?', array($search, $replace, $id));
    }

}