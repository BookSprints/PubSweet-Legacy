<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 3/20/14
 * Time: 3:24 PM
 */
class Annotations extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('annotations_model','model');
    }

    public function create($id)
    {
        $nojson = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        $nojson->id = $this->model->create($id, $GLOBALS['HTTP_RAW_POST_DATA']);
        echo json_encode($nojson);
    }

    public function read($id)
    {
        $data = $this->model->all($id);
        $annotation = array();
        foreach($data as $item){
            $object = json_decode($item['annotation']);
            $object->id = $item['id'];
            $annotation[] = $object;
        }

        echo json_encode($annotation);
    }

    public function update($id)
    {
        $data = "";
        $putData = fopen("php://input", "r");
        while ($chunk = fread($putData, 1024)){
            $data .= $chunk;
        }

        $this->model->update($id, $data);
        echo $data;
    }

    public function destroy($id)
    {
        echo json_encode(array('ok'=>$this->model->delete($id)));
    }
}