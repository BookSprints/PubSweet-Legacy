<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-02-13
 * Time: 10:28 AM
 */
class Definitions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insert()
    {
        $data = array(

            'term' => $this->input->post('term'),
            'definition' => $this->input->post('definition'),
            'term_id' => $this->input->post('term_id'),
            'language_id' => $this->input->post('language_id')
        );
        $this->db->insert('definitions', $data);
        return $this->db->insert_id();

    }

    public function update()
    {
        $this->db->where('id', $this->input->post('id'));
        $this->db->update(
            'definitions',
            $data = array(

                'term' => $this->input->post('term'),
                'definition' => $this->input->post('definition'),
                'language_id' => $this->input->post('language_id'),

            )

        );
    }

    /**
     * @param $term_id
     * @return mixed
     * get  languages

     */
    public function definitions($term_id)
    {
        $this->db->select('definitions.id, term_id, language_id, term , definition, languages.code_dir');
        $this->db->from('definitions');
        $this->db->join('languages','languages.id = definitions.language_id');
        $this->db->where('term_id', $term_id);
        $query = $this->db->get();
        return $query->result_array();


    }

    public function getAllByChapters($chapter_id)
    {
        $this->db->select('definitions.id, definitions.term_id, definitions.language_id, definitions.term,
            definitions.definition, definitions.updated, chapters.id as chapter_id, l.iso_code');
        $this->db->from('definitions');
        $this->db->join('dictionary_entries','dictionary_entries.id=definitions.term_id');
        $this->db->join('chapters','chapters.id=dictionary_entries.chapter_id');
        $this->db->join('languages l','l.id=definitions.language_id');
        $this->db->where('definitions.term <>', "");
        $this->db->where('chapters.id', $chapter_id);
        $this->db->order_by('chapters.id');
        $query = $this->db->get();
        return $query->result_array();


    }

}