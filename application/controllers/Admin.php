<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 29/07/13
 * Time: 11:17
 * To change this template use File | Settings | File Templates.
 */

class Admin extends CI_Controller{
    function __construct(){
        parent:: __construct();
        $this->load->helper('url');
        $this->load->library('session');
        if (!$this->session->userdata('DX_user_id')) {
            redirect('register/login', 'refresh');
        }
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);

        $this->load->model(array('editors_model','user_model'));

    }

    function editors(){
        $editor = $this->editors_model->all();
        $this->load->view('templates/header');
        $this->load->view('admin/editor_types',array('editor'=>$editor));
        $this->load->view('templates/footer');

    }

    function editor_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('checked');
        $status = ($status == 'true')?1:0;
        $this->editors_model->updateState($id,$status);
    }
    public function users(){
        $lang = $this->session->userdata('language');
        $lang = empty($lang)?'english':$lang;
        $this->lang->load($lang, $lang);
        $this->load->model('user_model');
        $user=$this->user_model->get_all();
        $this->load->helper('form');
        $this->load->view('templates/header');
        $this->load->view('templates/admin_navbar');
        $this->load->view('user/admin',array('user'=>$user));
        $this->load->view('templates/footer');
    }
    public function userDelete()
        {
        $id = $this->input->post('user_id');
        $data = array(
          'banned'=> 1
        );
        $this->user_model->user_delete($id,$data);
        echo json_encode(array('ok'=>1));
        }
    public function update_user()
       {
           $this->user_model->update_user();
           echo json_encode(array('ok' => 1));
       }
    public function user_enabled()
           {
           $id = $this->input->post('user_id');
           $data = array(
             'banned'=> 0
           );
           $this->user_model-> user_active($id,$data);
           echo json_encode(array('ok'=>1));
           }

    public function stats($day = 7)
    {
        $this->load->model('login_log_model','log_model');
        $data['last'] = $this->log_model->last($day.' DAY');

        $data['days'] = $day;

        $this->load->view('templates/header');
        $this->load->view('templates/admin_navbar');
        $this->load->view('admin/stats', $data);
        $this->load->view('templates/footer');

//        http://bl.ocks.org/mbostock/3885211
        //mostrar las relaciones entre escritores y capítulos
//        http://mbostock.github.io/d3/talk/20111116/bundle.html
        //mostrar cantidad de palabras
        //http://bl.ocks.org/mbostock/4063423
    }

    public function login_stats($day = 7)
    {
        $this->load->model('login_log_model','log_model');
        $data = $this->log_model->groupByDate($this->log_model->last($day.' DAY'));
        echo json_encode(array_values($data));
    }

    public function facilitators(){
        $this->load->model('User_model');
        $this->load->model('facilitators_model','facilitator');

        $this->load->view('templates/header');
        $this->load->view('templates/admin_navbar');

        $this->load->view('admin/facilitators', array('users'=>$this->user_model->get_all(),
                                                      'facilitators'=>$this->facilitator->all()));
        $this->load->view('templates/footer');

    }

    public function addFacilitator()
    {
        $this->load->model('User_model','user');
        $this->user->set_role($this->input->post('user_id'), 3);
        redirect('admin/facilitators', 'refresh');
    }

    public function books()
    {
        $this->load->library(array('pagination','arrayhelper'));

        /*$config['base_url'] = 'http://example.com/index.php/test/page/';
        $config['total_rows'] = 200;
        $config['per_page'] = 20;

        $this->pagination->initialize($config);*/

        $this->load->model(array('books_model','user_model'));
        $data['books'] = $this->books_model->all();
        $data['users'] = $this->arrayhelper->indexedArray($this->user_model->get_all(), 'id');
        $this->load->view('templates/header');
        $this->load->view('templates/admin_navbar');
        $this->load->view('admin/books', $data);
        $this->load->view('templates/footer');

    }

}