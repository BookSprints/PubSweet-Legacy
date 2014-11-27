<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-09-13
 * Time: 02:43 PM
 */
class Book extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }

        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
    }

    public function tocmanager($id)
    {
        $this->load->model(array('sections_model','chapters_model','status_model','editors_model',
                'books_model','book_settings_model','registers_model','user_model'));
        $user = $this->registers_model->find($this->session->userdata('DX_user_id'));
        $sections = $this->sections_model->find($id);
        $chapters = $this->chapters_model->find($id);
        $status = $this->status_model->find($id);
        $editors = $this->editors_model->all();
        $bookname = $this->books_model->get($id);
        $temp = $this->user_model->get_all();
        $temp2 = $this->books_model->coauthors($id);
        $this->load->helper('form');
        $users = array();
        $coauthors = array();
        foreach ($temp as $item) {
            $users[$item['id']] = $item;
        }
        foreach ($temp2 as $item) {
            $coauthors[$item['user_id']] = $item;
        }

        $isBookOwner = $bookname['owner']==$this->session->userdata('DX_user_id');
        $isFacilitator = $this->user_model->isFacilitator($this->session->userdata('DX_user_id'));
//        var_dump($bookOwner);die();
        $contributor = (isset($coauthors[$this->session->userdata('DX_user_id')])
                && $coauthors[$this->session->userdata('DX_user_id')]['contributor']);
        $reviewer = (isset($coauthors[$this->session->userdata('DX_user_id')])
            && $coauthors[$this->session->userdata('DX_user_id')]['reviewer']);

        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book' => $bookname));
        $this->load->view('book/tocmanager',
                array('id'=>$id,
                    'user'=>$user,
                    'sections'=>$sections,
                    'chapters'=>$chapters,
                    'status'=>$status,
                    'editors'=>$editors,
                    'book'=>$bookname,
                    'settings'=>$this->book_settings_model->get($id),
                    'users'=>$users,
                    'coauthors'=>$coauthors,
                    'isBookOwner' => $isBookOwner,
                    'isFacilitator' => $isFacilitator,
                    'contributor' => $contributor,
                    'reviewer' => $reviewer,
//                    'editing_sections' => $this->getCurrentlyEditingSections()
                ));

        $this->load->view('templates/footer');

    }

    public function save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sections', 'Sections', 'required');
        $this->form_validation->set_rules('chapters', 'Chapters', 'required');
        $this->form_validation->set_rules('editors', 'Editors', 'required');
        $this->load->model('books_model');
        $id = $this->books_model->set_book($this->session->userdata('DX_user_id'));
        echo json_encode(array('ok' => 1, 'id' => $id));
    }

    public function addCoauthor()
    {
        $this->load->model('books_model','model');
        $data = array('book_id'=>$this->input->post('book_id'),
            'user_id'=>$this->input->post('user_id'),
            'contributor'=>$this->input->post('contributor')=='on'?1:0,
            'reviewer'=>$this->input->post('reviewer')=='on'?1:0);
        echo json_encode(array('ok'=>$this->model->addCoauthor($data)));
    }

    public function removeCoAuthor()
    {
        $this->load->model('books_model','model');
        $data = array('book_id'=>$this->input->post('book_id'),
            'user_id'=>$this->input->post('id'));
        echo json_encode(array('ok'=>$this->model->removeCoauthor($data)));

    }

    public function updateCoAuthor()
    {
        $this->load->model('books_model','model');
        $where = array('book_id'=>$this->input->post('book'),
            'user_id'=>$this->input->post('user'));
        $data = array($this->input->post('field')=>$this->input->post('value'));
        echo json_encode(array('ok'=>$this->model->updateCoauthor($data, $where)));

    }

    public function images($chapterId)
    {
        $this->load->model('books_model');
        $book = $this->books_model->findByChapter($chapterId);
        $folderName = url_title($book['title']);
        $this->load->helper('file');
        $files = get_filenames(BASEPATH.'../public/uploads/'.$folderName);
        $result = array();
        if(!empty($files)){
            foreach ($files as $item) {
                $result[] = array(
                    "image" => base_url()."public/uploads/".$folderName.'/'.$item,
                    "thumb" => base_url()."public/uploads/".$folderName.'/'.$item,
                    "folder" => "uploads/".$folderName
                );
            }
        }

        echo json_encode($result);
    }

    public function stats($id)
    {
        $this->load->model('books_model', 'model');
        $book = $this->model->get($id);
        $users = $this->model->allUsers($id);

        $this->load->model('reviews_model', 'reviews');
        $allComments = $this->reviews->countAfter($id);
        $last7Days = $this->reviews->countAfter($id,' DATE_SUB(NOW(), INTERVAL 7 DAY) ');
        $last24Hours = $this->reviews->countAfter($id,' DATE_SUB(NOW(), INTERVAL 24 HOUR) ');

        $allApprovals = $this->reviews->countApprovalsAfter($id);
        $last7DaysApprovals = $this->reviews->countApprovalsAfter($id,' DATE_SUB(NOW(), INTERVAL 7 DAY) ');
        $last24HoursApprovals = $this->reviews->countApprovalsAfter($id,' DATE_SUB(NOW(), INTERVAL 24 HOUR) ');

        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book' => $book));
        $this->load->view('book/stats',
            array(
                'users' => $users,
                'allComments'=>$allComments,
                'last7Days'=>$last7Days,
                'last24Hours'=>$last24Hours,

                'allApprovals'=>$allApprovals,
                'last7DaysApprovals'=>$last7DaysApprovals,
                'last24HoursApprovals'=>$last24HoursApprovals));

        $this->load->view('templates/footer');
    }

    public function settings($id)
    {
        $this->load->model('books_model', 'model');
        $book = $this->model->get($id);
        $this->load->model('book_settings_model', 'settings_model');

        $this->load->view('templates/header');
        $this->load->view('templates/navbar', array('book' => $book));
        $this->load->view('book/settings',
            array('id' => $id, 'settings'=>$this->settings_model->get($id),
                'book'=>$book));

        $this->load->view('templates/footer');
    }

    public function save_settings($id)
    {
        $this->load->model('book_settings_model', 'settings_model');
        $settings = $this->input->post('settings');
        if(!isset($settings['enable_flag'])){
            $settings['enable_flag'] = 0;
        }
        foreach($settings as $key=>$value){
            $this->settings_model->save($id, $key, $value);
        }
        redirect('book/settings/'.$id);
    }

    public function saveUserConfig($id)
    {
        $this->load->model('chapters_model');
        $chapter = $this->chapters_model->get($id);
        $setting = json_encode(array('autosave_time'=>$this->input->post('time')));
        $this->db->query(sprintf('INSERT INTO book_user_settings(book_id, user_id, settings)
            VALUES(%u, %u, \'%s\') ON DUPLICATE KEY UPDATE settings=\'%s\'',
            $chapter['book_id'], $this->session->userdata('DX_user_id'), $setting, $setting));
        echo json_encode(array('ok'=>1));
    }

}