<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 10-08-13
 * Time: 10:18 AM
 */

class Invited extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('inviteds_model', 'model');

    }

    public function invite()
    {
        $this->load->model('books_model');
        $book = $this->books_model->get($this->input->post('book_id'));
        $this->send($this->input->post('invited'), $book['title']);

//        $this->load->library('form_validation');
//        $this->form_validation->set_rules('invited', 'Invited', 'required');
        $data = array(
            'invited' => $this->input->post('invited'),
            'book' => $this->input->post('book_id'),
            'inviter' => $this->session->userdata('DX_user_id')
        );

        $id = $this->model->save($data);
        echo json_encode(array('ok' => 1, 'id' => $id));
    }

    public function send($to, $book)
    {
        $this->load->library('Mail');

        $this->mail->send(
            $to,
            sprintf(
                '<p>You have been invited to join the book <strong>%s</strong>,</p>
                                 <p> Please click this link to <a href="%s">register and start work</a>.
                                Please click this link to <a href="%s">decline the invitation</a>.
                                </p> <p>Kind regards,</p> ',
                $book,
                base_url('register/user'),
                base_url('invited/decline/' . $to)
            ),
            'Lexicon invitation'
        );
    }

    public function decline($email)
    {
        $this->model->remove($email);
        $this->load->library('Mail');
        $this->mail->send('pubsweet-admin@booksprints.net', 'This guy has just declined',
            'Invited has declined');
        $this->load->view('invitations/decline');
    }

}