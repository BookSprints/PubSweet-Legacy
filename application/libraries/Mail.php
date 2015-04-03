<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-02-13
 * Time: 10:23 AM
 */
class Mail{

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('email');

        /* SERVER CONFIGURATION */
        $config['protocol'] = 'smtp';
        /*$config['smtp_host'] =	'mail.booksprints.net';
        $config['smtp_user'] =	'lexicon@booksprints.net';
        $config['smtp_pass'] =	'pubsweet';
        $config['smtp_port'] =	25;*/

        $config['smtp_host'] =	'smtp-mail.outlook.com';
        $config['smtp_user'] =	'jgutierrezb@outlook.com';
        $config['smtp_pass'] =	'revolucion79';
        $config['smtp_port'] =	587;

        /* LOCAL CONFIGURATION */
//        $config['protocol'] = 'mail';
//        $config['mailpath'] = '/usr/sbin/sendmail';

        $config['charset'] = 'utf8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;

        $this->ci->email->initialize($config);
    }

    public function send($recipient, $text, $subject)
    {
        $this->ci->email->from('lexicon@booksprints.net', 'Lexicon Bot');

//            echo $item['email'];
        $this->ci->email->to($recipient);
        $this->ci->email->subject($subject);
        $this->ci->email->message($this->template($text));
        if(!$this->ci->email->send()){
            die('Email was not sent');
        }
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