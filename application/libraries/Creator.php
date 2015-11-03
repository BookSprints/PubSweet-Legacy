<?php
/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/26/15
 * Time: 1:31 AM
 */

class Creator {

    protected function createContent($id, $draft)
    {
        $originalContent = $this->getContent($id);
        $this->load->view('templates/simple/header',
            array('id' => $id, 'draft' => $draft, 'content' => $originalContent));
        $this->load->view('templates/simple/footer', array('draft' => $draft));
    }

    /**
     * @param $id
     * @return string
     */
    public function getContent($id)
    {
        $this->load->model('Chapters_model', 'chapters');
        $chapters = $this->chapters->find($id);

        ob_start();
        $currentSection = null;
        foreach ($chapters as $item) {
            if ($currentSection != $item['section_id']) {
                echo '<h1 class="section">' . $item['section_title'] . '</h1>';
            }
            $this->chapter($item['id']);
            $currentSection = $item['section_id'];
        }
        $originalContent = ob_get_contents();
        ob_end_clean();

        return $originalContent;
    }
}