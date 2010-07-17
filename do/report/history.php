<?php
class report_history extends Event
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
        $results = $this->db->query("SELECT * FROM `reports` ORDER BY `id` DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('report' , $results);

        $this->view->output('report/history.html');
    }
    
    public function delete()
    {
        #todo: delete real file
        $id = $this->request->get('id','');
        
        $this->db->exec("DELETE FROM `reports` WHERE `id` = '".$id."';");

        $this->notifyHelper->set('所選的項目已自結果集內刪除');

        $this->go('report/history');
    }
}
