<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 09-05-13
 * Time: 11:06 AM
 */
class Review extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('reviews_model','model');
    }

    public function view($id)
    {
        $this->load->model('books_model');
        $this->load->model('likes_model','likes');
        $data['book'] = $this->books_model->get($id);
        $messages['messages'] = $this->model->byBook($id);
        $likes = $this->likes->get_likes($id);
        $dislikes = $this->likes->get_dislikes($id);
        $cont = 0;
        foreach($messages['messages'] as $message){
            $cont_likes = 0;
            $cont_dislikes = 0;
            foreach($likes as $like){
                if($message['id'] == $like['messages_id']){
                   $cont_likes++;
                }
            }
            foreach($dislikes as $like){
                if($message['id'] == $like['messages_id']){
                    $cont_dislikes++;
                }
            }
            array_push($messages['messages'][$cont], array("likes"=>$cont_likes,"dislikes"=>$cont_dislikes));
            $cont++;
        }
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
        $this->load->view('templates/header');
        $this->load->view('templates/navbar', $data);
        $this->load->view('discussion/message', $messages);
        $this->load->view('templates/footer');
    }

    public function add(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment', 'Message', 'required');
        if($this->form_validation->run()){
            $id = $this->model->save();
            $review = $this->model->get($id);
            echo json_encode(array('ok'=>1, 'review'=>$review));
        }else{
            echo json_encode(array('ok'=>0, 'error'=>'Error de validación'));
        }


    }

    public function delete()
    {
        $result = $this->model->delete($this->input->post('id'));
        echo json_encode(array('ok'=>$result));
    }

    public function new_approve(){
        $term_id = $this->input->post('term_id');
        $user_id = $this->session->userdata('DX_user_id');
        $data = array(
            'user_id'=>$user_id,
            'term_id'=>$term_id
        );
        if($this->model->add_approve($data))
            echo json_encode(array('ok'=>1,'user_id'=>$user_id));
    }

    public function list_approve_by_term($id){
        $result = $this->model->approvals_by_term($id);
        echo json_encode($result);


    }

    public function data($book)
    {
        $this->load->model('books_model','book');
        $users = $this->book->allUsers($book);

        $this->load->model('reviews_model', 'reviews');
        $allComments = $this->reviews->countAfter($book);
        $last7Days = $this->reviews->countAfter($book,' DATE_SUB(NOW(), INTERVAL 7 DAY) ');
        $last24Hours = $this->reviews->countAfter($book,' DATE_SUB(NOW(), INTERVAL 24 HOUR) ');

        $allApprovals = $this->reviews->countApprovalsAfter($book);
        $last7DaysApprovals = $this->reviews->countApprovalsAfter($book,' DATE_SUB(NOW(), INTERVAL 7 DAY) ');
        $last24HoursApprovals = $this->reviews->countApprovalsAfter($book,' DATE_SUB(NOW(), INTERVAL 24 HOUR) ');

        $result = array();
        foreach ($users as $item):
            $result[] = array(
                'id'=> $item['user_id'],
                'names'=> empty($item['names'])?$item['username']:$item['names'],
                'allComments'=> isset($allComments[$item['user_id']])?$allComments[$item['user_id']]['count']:0,
                'last7Days'=> isset($last7Days[$item['user_id']])?$last7Days[$item['user_id']]['count']:0,
                'last24Hours'=> isset($last24Hours[$item['user_id']])?$last24Hours[$item['user_id']]['count']:0,
                'allApprovals'=> isset($allApprovals[$item['user_id']])?$allApprovals[$item['user_id']]['count']:0,
                'last7DaysApprovals'=> isset($last7DaysApprovals[$item['user_id']])?$last7DaysApprovals[$item['user_id']]['count']:0,
                'last24HoursApprovals'=> isset($last24HoursApprovals[$item['user_id']])?$last24HoursApprovals[$item['user_id']]['count']:0,
            );
        endforeach;

        echo json_encode($result);
    }
}