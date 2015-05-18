<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 17/07/13
 * Time: 11:20
 * To change this template use File | Settings | File Templates.
 */

class Sections extends CI_Controller{
    public function __construct(){
        parent:: __construct();
        $this->load->model('sections_model');
    }
    public function save(){
//        $date = date('d/m/Y');

        $book_id = $this->input->post('book_id');
        $title = $this->input->post('title');
        $order =$this->sections_model->find($book_id);
        $counter = count($order)+1;

        if(!empty($title)){
            $data = array(
                'title' => $title,
    //           'created' => $date,
                'book_id' => $book_id,
                'order' => $counter //the number of sections of the book
            );
            $id = $this->sections_model->set_section($data);
            echo json_encode(array('ok'=>1, 'order'=>$counter, 'id'=>$id));
        }
    }

    public function update(){
        $items =$this->input->post();
        foreach($items as $item=>$value){
            $this->sections_model->update_position($item,$value);
        }
    }

    public function changeName(){
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $data = array('title'=>$title);
        $this->sections_model->change_name($id,$data);
        echo json_encode(array('id'=>$id,'title'=>$title));
    }
    public function delete_section(){
        $id = $this->input->post('section_id');
        $data = array(
        'removed'=> 1
        );
        $this->sections_model-> delete($id,$data);
        echo json_encode(array('ok'=>1, 'id'=>$id));
    }
}