<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 30/08/13
 * Time: 13:02
 * To change this template use File | Settings | File Templates.
 */

class status extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('status_model','status');
    }

    function chapterStatusList(){
        $chapter_id = $this->input->post('chapter_id');
        $result = $this->status->find_by_chapter($chapter_id);
        echo json_encode($result);
    }

    function save(){
        $book_id = $this->input->post('book_id');
        $chapter_id = $this->input->post('chapter_id');
        $user_id=$this->input->post('user_id');
        $data = array(
            'book_id'=>$book_id,
            'chapter_id'=>$chapter_id,
            'user_id'=>$user_id
        );
        $id = $this->status->set($data);
        echo json_encode(array('ok' => 1,'id'=>$id));
    }

    function delete(){
        $id=$this->input->post('id');
        $this->status->delete($id);
        echo json_encode(array('ok' => 1,'id'=>$id));
    }

    public function update(){
        $id = explode(',', $this->input->post('id'));
        $status_title =explode(',', $this->input->post('title'));
        $user_id =explode(',',$this->input->post('user_id'));
        $status = explode(',',$this->input->post('status'));
        for ($cont =0 ; $cont<count($id); $cont++)
        {
            if(empty($status_title[$cont])){
                $data = array(
                    'status'=>$status[$cont],
                    'user_id'=>$user_id[$cont]
                );
                $this->status->update($data,$id[$cont]);
            }
            else{
                $data = array(
                    'title'=> empty($status_title[$cont])?'create content':$status_title[$cont] ,
                    'status'=>$status[$cont],
                    'user_id'=>$user_id[$cont]
                );
                $this->status->update($data,$id[$cont]);
            }
        }
        echo json_encode(array('ok'=>1));
    }

}