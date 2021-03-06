<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-11-13
 * Time: 12:38 AM
 */

class Books_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function all($userId=null, $extended=false)
    {
        $select = 'books.id, books.title, books.owner, chapters.id as chapter_id';
        if($extended===true){
            $select .= ',content';
        }
        $this->db->select($select);
        $this->db->from('books');
        $this->db->join('chapters','chapters.book_id = books.id', 'LEFT');
        if($userId!=null){
            $this->db->where(array('owner'=>$userId));
        }
        $this->db->where(array('status'=>1));
        $this->db->group_by('books.id');
        $this->db->order_by('books.id','DESC');
        $this->db->order_by('books.timestamp','DESC');
        $query = $this->db->get();
        return $query->result_array();

    }

    public function set_book($userid)
    {
        $data = array(
            'title' => $this->input->post('title'),
            'owner' => $userid
        );
        $this->db->insert('books', $data);
        return $this->db->insert_id();

    }

    /*
     * TODO: change method name, it could collide with generic get method
     */
    /**
     * @param $bookId
     * @param $allowHidden, will allow to get status=0 book
     * @return mixed
     */
    public function get($bookId, $allowHidden=false){
        $this->db->reset_query();
        $this->db->select('id, title, owner');
        $this->db->from('books');
        $this->db->where('id', $bookId);
        if(!$allowHidden){
            $this->db->where('status', 1);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getUserConfig($bookId, $userId){
        $this->db->select('book_id, user_id, settings');
        $query= $this->db->get_where('book_user_settings', array('book_id'=>$bookId, 'user_id'=>$userId));
        return $query->row_array();
    }

    public function addCoauthor($data)
    {
       return $this->db->insert('coauthors', $data);
    }

    public function removeCoauthor($data)
    {
        $this->db->where($data);
        return $this->db->delete('coauthors');
    }

    public function updateCoauthor($data, $where)
    {
        $this->db->where($where);
        return $this->db->update('coauthors', $data);
    }

    /**
     * @param $id - BOOK ID
     * @return mixed
     */
    public function coauthors($id)
    {
        $this->db->select('book_id, user_id, contributor, reviewer');
        $this->db->from('coauthors');
        $this->db->where(array('book_id'=>$id));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function invited_books($id)
    {
        $this->db->select('book_id, user_id');
        $this->db->from('coauthors');
        $this->db->where(array('user_id'=>$id));
        $query = $this->db->get();
        $first = $query->result_array();

        $this->db->select('book as book_id, users.id as user_id');
        $this->db->from('invited_externals');
        $this->db->join('users','invited_externals.invited=users.email');
        $this->db->where(array('users.id'=>$id));
        $this->db->group_by('users.id, book_id');
        $query = $this->db->get();
        $second = $query->result_array();

        if(empty($first)){
            return $second;
        }else if(empty($second)){
            return $first;
        }else if(empty($first) && empty($second)){
            return null;
        }
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            return array_unique(array_merge($first, $second), SORT_REGULAR);
        }else{
            return array_unique(array_merge($first, $second));
        }
    }

    /**
     * @param $id - book id
     */
    public function allUsers($id)
    {
        $this->db->select('u.id as user_id, u.names, u.username');
        $this->db->from('books b');
        $this->db->join('coauthors c','b.id = c.book_id');
        $this->db->join('users u', 'u.id = c.user_id OR b.owner = u.id');
        $this->db->where(array('b.id'=>$id));
        $query = $this->db->get();
        $result = $query->result_array();
        $data = array();
        foreach($result as $item){
            $data[$item['user_id']] = $item;
        }
        return $data;
    }

    public function findByChapter($chapterId)
    {
        $this->db->select('books.id, books.title, books.owner');
        $this->db->from('books');
        $this->db->join('chapters c', 'c.book_id = books.id');
        $this->db->where(array('c.id'=>$chapterId));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function updateOwner()
    {
        $this->db->where('id', $this->input->post('book_id'));
        return $this->db->update('books', array('owner'=>$this->input->post('owner')));

    }

    public function updateName()
    {
        $book = $this->get($this->input->post('book_id'));
        $newTitle = $this->input->post('bookname');

        $this->db->where('id', $this->input->post('book_id'));
        if($this->db->update('books', array('title'=>$newTitle))){
            $oldPath = BASEPATH.'../public/uploads/'.url_title($book['title']);
            if(file_exists($oldPath)){
                return rename($oldPath, BASEPATH.'../public/uploads/'.url_title($newTitle));
            }
            //TODO: update images path
            // update chapters set content = replace(content, 'VMS-Troubleshooting-and-Operations', 'vMS-Troubleshooting-and-Operations');

            return true;
        }else{
            return false;
        }

    }

    public function getFolderName($bookTitle)
    {
        return url_title($bookTitle);
        /*$this->load->helper('file');
        $files = get_filenames(BASEPATH.'../public/uploads/'.$folderName);*/
    }

    public function getImagesPath($bookTitle)
    {
        $folderName = url_title($bookTitle);
        return BASEPATH.'../public/uploads/'.$folderName.'/';
    }

    public function replace($find, $replace, $bookId)
    {
        $this->db->reset_query();
        $this->db->where('id', $this->input->post('book_id'));
        return $this->db->query('UPDATE chapters SET content = replace(content, ?, ?)
                WHERE book_id = ?', array($find, $replace, $bookId));
    }
}

//update books set status = 0 where id in (132, 108, 106, 98, 97)

//delete39,40,92,93,94

//delete nc FROM `normal_chapter_history` nc left join chapters c on c.id = nc.chapter_id left join books b on b.id = c.book_id where c.id is null or b.id is null or c.removed or b.status = 0;
