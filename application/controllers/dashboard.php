<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-09-13
 * Time: 11:38 AM
 */
class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('DX_Auth');
        $this->load->helper('url');
        if (!$this->session->userdata('DX_user_id')){
            redirect('register/login', 'refresh');
        }

    }

    public function profile()
    {

        if($this->dx_auth->is_logged_in()){
            $lang = $this->session->userdata('language');
            $lang = empty($lang)?'english':$lang;
            $this->lang->load($lang, $lang);

            $this->load->model('registers_model');
            $this->load->model('books_model');
            $userid = $this->session->userdata('DX_user_id');
            $data['user'] = $this->registers_model->find($userid);
            $temp = $this->books_model->all();
            $data['all_books'] = array();
            $data['my_books'] = array();
            foreach ($temp as $item) {
                $data['all_books'][$item['id']] = $item;
                if($item['owner']==$userid){
                    $data['my_books'][] = $item;
                }

            }
            $data['languages'] = array('ar'=>'Arabic',
                'en'=>'English',
                'fr'=>'French',
                'sp'=>'Spanish');

            $data['invited_books'] = $this->books_model->invited_books($userid);
            $this->load->helper('form');
            $this->load->view('templates/header');
            $this->load->view('templates/navbar');
            $this->load->view('dashboard/profile', $data);
            $this->load->view('templates/footer');
        }else{
            redirect(base_url('register/login'));
        }
    }

    public function updateLanguage()
    {
        $this->load->model('language_model','language');
        $lang = $this->language->get($this->input->post('language'));
        $this->session->set_userdata('language', strtolower($lang['english_name']));
        $this->session->set_userdata('lang_iso_code', $lang['iso_code']);
        $this->session->set_userdata('lang_dir', $lang['code_dir']);

        redirect(base_url());
    }

}
