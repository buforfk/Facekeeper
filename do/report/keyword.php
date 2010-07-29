<?php
class report_keyword extends Event
{
    public function startupAction()
    {
        if($this->auth->_logonManager->isLogged() == FALSE)
        {
            $this->go('user/login');
        }
        
        if(!$this->notifyHelper->isEmptyBin())
        {
            $notfiyMessage = $this->notifyHelper->get();
            $this->view->assign('NotifyMessage',$notfiyMessage);
        }
        
        $this->FaceKeeperHelper->setPageModule('report');
    }
    
    public function defaultAction()
    {
        $start = $this->request->get('page', 1 , bP_INT);

        
        $per = $this->config->get('pager.itemperpage');

         $pager_object = new bPack_Pager();

        $results_count = $this->db->query("SELECT count(*) as `count` FROM `result_keyword`;")->fetch(PDO::FETCH_ASSOC);
        $pager_object->total(ceil($results_count['count'] / $per));
        $pager_object->per($per);
        $pager_object->current($start);
        $start = ($start-1) * $per ;
        $this->view->assign('pager', $pager_object->output(new bP_Pager_Decorator_Pagi));

 
        # 收錄資料統計
        $results = $this->db->query("SELECT * FROM `result_keyword` ORDER BY `keyword_length` DESC LIMIT $start, $per")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('result' , $results);

        $this->view->output('report/key.html');
    }

    public function delete()
    {
        $hash = $this->request->get('hash','');

        $this->db->exec("DELETE FROM `result_pool` WHERE `hash` = '".$hash."';");

        $this->notifyHelper->set('[內部編號：'.$hash.']的項目已自結果集內刪除');

        $this->go('report/keyword');
    }
}
