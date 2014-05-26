<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-30-13
 * Time: 10:50 AM
 * To change this template use File | Settings | File Templates.
 */
class Topic extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('topics_model','model');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }

    }

    public function view($id)
    {
        $this->load->model('books_model');
        $this->load->model('comments_model','comments');
        $data['book'] = $this->books_model->get($id);
        $topics['topics'] = $this->model->byBook($id);
        $cont=0;
        foreach($topics['topics'] as $theme){
            $response = $this->comments->byTopic($theme['id']);
            $topics['topics'][$cont]['comments']=$response;
            $cont++;
        }
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
        $this->load->view('templates/header');
        $this->load->view('templates/navbar', $data);
        $this->load->view('discussion/topic', array('topics'=>$topics,'book'=>$data));
        $this->load->view('templates/footer');
    }

    public function add(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('topic', 'Topic', 'required');
        if($this->form_validation->run()){
            $topic = $this->model->savetopic();
            $this->load->model('comments_model','comment');
            $comment_id = $this->comment->save($topic);
            echo json_encode(array('ok'=>1, 'id'=>$topic, 'comment'=>$this->comment->get($comment_id)));
        }

    }

    public function detail($id)
    {
        $this->load->model('comments_model','comment');
        $this->load->model('like_comments_model','likes');
        $data['topic'] = $this->model->get($id);
        $comments['comments'] = $this->comment->byTopic($id);
        $this->load->model('books_model');
        $bookname = $this->books_model-> get($data['topic']['book_id']);
        $cont=0;
        foreach($comments['comments'] as $item){
            $likes = $this->likes->likes_by_comments($item['id']);
            $comments['comments'][$cont]['likes']=$likes;
            $cont++;
        }
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book'=>$bookname));
        $this->load->view('discussion/comment', array('comments'=>$comments,'topic'=>$data));
        $this->load->view('templates/footer');
    }
   /*public function delete()
    {
        $result = $this->model->delete($this->input->post('id'));
        echo json_encode(array('ok'=>$result));
    }*/
}