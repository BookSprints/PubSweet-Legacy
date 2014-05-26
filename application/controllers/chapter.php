<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-09-13
 * Time: 02:43 PM
 */
class Chapter extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('chapters_model','model');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }
    }

    public function save()
    {
        $this->load->model(array('sections_model','status_model'));
        $book_id = $this->input->post('book_id');
        $section = end($this->sections_model->find($book_id));
        $chapters = $this->model->find($book_id);
        $editor_id =$this->input->post('editor_id');

        $order = 0;
        foreach ($chapters as $item) {
            if($item['section_id'] == $section['id']){
                $order++;
            }
        }
        $order++;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Title', 'required');
        if($this->form_validation->run() && $section['id'] != 0 )
        {
            $data = array(
                'title' => $this->input->post('title'),
                'book_id' => $book_id,
                'section_id' => $section['id'],
                'order' => $order,
                'editor_id'=>$editor_id
            );
            $id = $this->model->set_chapter($data);
            $status=array(
                'book_id' => $book_id,
                'chapter_id'=>$id,
                'user_id'=>0
            );
            $status_id = $this->status_model->set($status);
            echo json_encode(array('ok'=>1, 'id'=>$id,'order'=>$order,'status'=>$status_id));
        }
    }

    //update the order's chapters
    public function update(){
        $id = explode(',',$this->input->post('id'));
        $section =explode(',',$this->input->post('section'));
        $order =explode(',',$this->input->post('order'));
        for ($cont =0 ; $cont<count($id); $cont++)
        {
            $this->model->update_position($id[$cont],$section[$cont],$order[$cont]);
        }

    }

    public function changeName(){
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $data = array('title'=>$title);
        $this->model->change_name($id,$data);
        echo json_encode(array('id'=>$id,'title'=>$title));
    }

    public function saveContent()
    {
        echo json_encode(array('ok'=>
            $this->model->update(array('content'=>$this->input->post('content')), $this->input->post('id'))));
    }

    public function review($id)
    {
        $data['id'] = $id;
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);

        $this->load->model('Books_model','books_model');
        $this->load->model('Chapters_model','chapters_model');
        $chapter= $this->chapters_model->get($id);
        $this->load->model('Dictionary_entries_model','dictionary');
        $data['entries'] = $this->dictionary->term_list($id);
        $this->load->model('Definitions_model','definitions');
        $result = $this->definitions->getAllByChapters($id);
        $definitions = array();
        foreach ($result as $item) {
            $definitions[$item['term_id']][] = $item;
        }
        $data['definitions'] = $definitions;

        $this->load->model('Reviews_model','reviews');
        $result = $this->reviews->all();
        $reviews = array();
        foreach ($result as $item) {
            $reviews[$item['term_id']][] = $item;
        }
        $data['reviews'] = $reviews;

        $result = $this->reviews->approves();

        foreach($result as $res){
            $data['approves'][$res['term_id']][] = $res;
            if($res['user_id']==$this->session->userdata('DX_user_id')){
                $data['voted'][$res['term_id']] = true;
            }
        }
//        print_r($data['approves']);die();
        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book' => $this->books_model->get($chapter['book_id'])));
        $this->load->view('chapter/review', $data);
        $this->load->view('templates/footer');
    }
    public function delete_chapter(){
        $id = $this->input->post('chapter_id');
        $data = array(
            'removed'=> 1
        );
        $this->model-> delete($id,$data);
        echo json_encode(array('ok'=>1));
    }

    public function history($id)
    {
        $this->load->model(array('books_model','chapters_model'));
        $chapter = $this->chapters_model->get($id);
        if($chapter['editor_id']==2){
            $history = $this->model->getHistory($id);
            $this->load->view('templates/header');
            $this->load->view('templates/navbar', array('book' => $this->books_model->get($chapter['book_id'])));
            $this->load->view('chapter/history', array('history'=>$history));
            $this->load->view('templates/footer');
        }else{
            redirect('/book/tocmanager/'.$chapter['book_id']);
        }

    }

    public function rollback($id)
    {
        $history=$this->model->getHistoryEntry($id);
        if(empty($history)){
            return;
        }
        echo json_encode(
            array('ok'=>$this->model->update(
                    array('content'=>$history['content']), $history['chapter_id'])));
    }

}