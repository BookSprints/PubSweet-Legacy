<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/9/14
 * Time: 4:12 PM
 */

class Stats extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
    }

    public function bookWordCount()
    {
        $this->load->model('books_model');
        $this->books_model->worddCount();
    }
} 