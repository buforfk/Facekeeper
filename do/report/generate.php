<?php
class report_generate extends Event
{
    public function defaultAction()
    {
        # keyword 對應
        $results = $this->db->query("SELECT * FROM `result_keyword` ORDER BY `pid` DESC,`keyword_length` DESC LIMIT 0, 50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('keyword_result' , $results);


        # 次數統計
        $results = $this->db->query("SELECT * FROM `result_counting` ORDER BY `pid` DESC,`count` DESC LIMIT 0,50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('counting_result' , $results);

        # Facebook
        $results = $this->db->query("SELECT * FROM `result_pool` WHERE `source` = 1 ORDER BY `pid` DESC, `keyword_length` DESC LIMIT 0,50")->fetchAll(PDO::FETCH_ASSOC);
        $this->view->assign('facebook_results' , $results); 
        
        # PTT 
        $results = $this->db->query("SELECT * FROM `ptt_pool` ORDER BY `pid` DESC,`keyword_length` DESC LIMIT 0, 50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('ptt_result' , $results);

        # 輸出
        $this->view->output('report/generate.html');
    }
}
