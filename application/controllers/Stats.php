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
        $this->load->model('normal_chapter_history_model', 'history_model');

    }

    public function bookWordCount($bookId)
    {
        $this->load->model('chapters_model');
        echo json_encode(array('name'=>'Book',
                               'children'=>$this->chapters_model->wordCount($bookId)));
    }

    public function usersWordCount($bookId)
    {
        echo json_encode(array('name'=>'Book',
                               'children'=>$this->history_model->groupByUsers($bookId)));
    }

    /**
     * Output as csv file
     * @param $bookId
     */
    public function wordHistory($bookId){
        $data = $this->history_model->byBook($bookId, false);

        echo 'date,words'.PHP_EOL;
        $total = 0;
        $subTotals = array();
        foreach ($data as $entry) {
            $total += $entry['words'];
            $total -= $subTotals[$entry['chapter_id']];
            echo '"'.$entry['created'].'",'.$total.PHP_EOL;
            $subTotals[$entry['chapter_id']] = $entry['words'];
        }
    }

    /**
     * @param $bookId
     */
    public function normalizeHistory($bookId)
    {
        if(is_cli() && !empty($bookId)){
            include APPPATH.'/libraries/finediff.php';

            $this->load->model('normal_chapter_history_model','normal_chapter_history');
            $history = $this->normal_chapter_history->byBook($bookId);
            $previousEntry = null;
            $tempChapterId = null;
            foreach ($history as $item) {
                if($previousEntry == null || $tempChapterId != $item['chapter_id']){
                    $words = str_word_count(strip_tags($item['content']));
                    $this->db->update('normal_chapter_history', array(
                        'words'=>$words, 'inserted'=>$words, 'deleted'=>0
                    ), array('id'=>$item['id']));
                }else{
                    $plainContent = strip_tags($item['content']);
                    $diff = new FineDiff(strip_tags($previousEntry['content']),
                        strip_tags($item['content']));
                    if($diff->insertions_count==0 && $diff->deletions_count==0){
                        $this->db->update('normal_chapter_history', array(
                            'words'=>str_word_count($plainContent), 'inserted'=>$diff->insertions_count, 'deleted'=>$diff->deletions_count
                        ), array('id'=>$item['id']));
                    }else{
                        $this->db->delete('normal_chapter_history', array('id'=>$item['id']));
                    }

                }
                $previousEntry = $item;
                $tempChapterId = $item['chapter_id'];

            }
        }


    }

}