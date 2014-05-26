<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-02-13
 * Time: 10:23 AM
 */
class Mail extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $config['protocol'] = 'mail';
        /*$config['protocol'] = 'smtp';
        $config['smtp_host'] =	'mail.booksprints.net';
        $config['smtp_user'] =	'taskmanager@booksprints.net';
        $config['smtp_pass'] =	'p0tat0';
        $config['smtp_port'] =	25;*/
//        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);
    }

    public function send($recipient, $text, $subject)
    {
        $this->email->from('taskmanager@booksprints.net', 'Booksprints Bot');

//            echo $item['email'];
        $this->email->to($recipient);
        $this->email->subject($subject);
        $this->email->message($this->template($text));

        $this->email->send();
        //echo $this->email->print_debugger();

    }

    public function template($text)
    {
        return '<!doctype html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <title>Booksprints Mail</title>
        </head>
        <body>
        '.$text.'
        </body>
        </html>';
    }
}