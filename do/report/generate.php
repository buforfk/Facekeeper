<?php
class report_generate extends Event
{
    public function defaultAction()
    {
        # keyword 對應
        $results = $this->db->query("SELECT * FROM `result_keyword` ORDER BY `keyword_length` DESC LIMIT 0, 50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('keyword_result' , $results);


        # 次數統計
        $results = $this->db->query("SELECT * FROM `result_counting` ORDER BY `count` DESC LIMIT 0,50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('counting_result' , $results);

        # 輸出
        $this->view->output('report/generate.html');
    }
}
