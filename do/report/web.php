<?php
class report_web extends Event
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
        # 收錄資料統計
        $results = $this->db->query("SELECT * FROM `result_counting` ORDER BY `count` DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('result' , $results);

        $this->view->output('report/web.html');
    }
    
    public function key()
    {
        $youtube_obj = new WebKeyResult($this->db);
        
        $results = $youtube_obj->getAll();
        
        $this->view->assign('result',$results);
        $this->view->output('report/key.html');
    }

    public function delete()
    {
        $hash = $this->request->get('hash','');

        $this->db->exec("DELETE FROM `result_pool` WHERE `hash` = '".$hash."';");

        $this->notifyHelper->set('[內部編號：'.$hash.']的項目已自結果集內刪除');

        $this->go('report/web');
    }
}
