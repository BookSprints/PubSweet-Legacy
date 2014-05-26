<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 07-16-13
 * Time: 03:01 PM
 */
class Dictionary_entries_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function set_create()
    {
        $data = array(
            'term' => $this->input->post('term'),
            'chapter_id' => $this->input->post('chapter_id'),
            'language' => $this->input->post('language_id')

        );
        $this->db->insert('dictionary_entries', $data);
        return $this->db->insert_id();

    }

    public function term_list($dictionary)
    {
        $this->db->select('de.id, de.term, de.meaning, de.language, de.updated, l.code_dir, l.iso_code, i.full_image_path');
        $this->db->from('dictionary_entries de');
        $this->db->join('languages l','de.language = l.id');
        $this->db->join('images i','i.id = de.image_id', 'LEFT');
        $this->db->where(array('chapter_id' => $dictionary));
        $this->db->order_by("term", "asc");
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_term($id)
    {
        $this->db->select('de.id, term, meaning, language, full_image_path');
        $this->db->from('dictionary_entries de');
        $this->db->join('images i', 'i.id = de.image_id', 'LEFT');
        $this->db->where(array('de.id' => $id));
        $query = $this->db->get();

        // $query = $this->db->get();
        $data = $query->row_array();
        if(!empty($data['flags'])){
            $data['flags'] = explode(';',$data['flags']);
        }

        return $data;
    }

    public function update_item()
    {
        $this->db->where('id', $this->input->post('id'));
        $this->db->update(
            'dictionary_entries',
            $data = array(
                'term'    => $this->input->post('term'),
                'meaning' => $this->input->post('meaning'),
                'language'=> $this->input->post('language')

            )

        );
    }

    public function updateImageReference($termId, $imageId)
    {
        $this->db->where('id', $termId);
        $this->db->update(
            'dictionary_entries',
            $data = array(
                'image_id'    => $imageId
            )
        );
    }

    public function deleteImageReference($termId)
    {
        $this->db->where('id', $termId);
        $this->db->update(
            'dictionary_entries',
            $data = array(
                'image_id'    => null
            )
        );
    }

    public function term_delete($id){
        $this->db->where('id',$id);
        $this->db->delete('dictionary_entries');
    }

    public function update_chapterItem($id, $data){

        $this->db->where('id', $id);
        $this->db->update(
            'dictionary_entries',
            $data );
    }

}