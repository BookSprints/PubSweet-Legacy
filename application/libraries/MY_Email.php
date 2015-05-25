<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-02-13
 * Time: 10:23 AM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Email extends CI_Email{

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function deliver($recipient, $text, $subject)
    {
//        $this->from('pubsweet@booksprints.net', 'PubSweet Bot');
        $this->from('juan@booksprints.net', 'PubSweet Bot');

        $this->to($recipient);
        $this->subject($subject);
        $this->message($this->template($text));
        if(!$this->send()){
            die('Email was not sent');
        }
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